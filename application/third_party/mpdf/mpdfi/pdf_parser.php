s->read_ushort();
					$g[] = $this->unicode_hex($this->glyphToChar($glyphID)); 
				}
			}						
			if ($CoverageFormat == 2) {
				$RangeCount= $this->read_ushort();
				for ($r=0;$r<$RangeCount;$r++) {
					$start = $this->read_ushort();
					$end = $this->read_ushort();
					$StartCoverageIndex = $this->read_ushort(); // n/a
					for ($glyphID=$start;$glyphID<=$end;$glyphID++) {
						$g[] = $this->unicode_hex($this->glyphToChar($glyphID));
					}
				}
			}
			$this->LuDataCache[$this->fontkey][$offset] = $g;
		}
		return $g;						
	}

	function _getClasses($offset) {
		if (isset($this->LuDataCache[$this->fontkey][$offset])) {
			$GlyphByClass = $this->LuDataCache[$this->fontkey][$offset];
		}
		else {
			$this->seek($offset);
			$ClassFormat = $this->read_ushort();
			$GlyphByClass = array();
			if ($ClassFormat == 1) {
				$StartGlyph = $this->read_ushort();
				$GlyphCount = $this->read_ushort();
				for ($i=0;$i<$GlyphCount;$i++) {
					$startGlyphID = $StartGlyph + $i;
					$endGlyphID = $StartGlyph + $i;
					$class = $this->read_ushort();
					// Note: Font FreeSerif , tag "blws"
					// $BacktrackClasses[0] is defined ? a mistake in the font ???
					// Let's ignore for now
					if ($class > 0) {
						for($g=$startGlyphID;$g<=$endGlyphID;$g++) {
							if ($this->glyphToChar($g)) {
								$GlyphByClass[$class][$this->glyphToChar($g)] = 1;
							}
						}
					}
				}
			}
			else if ($ClassFormat == 2) {
				$tableCount = $this->read_ushort();
				for ($i=0;$i<$tableCount;$i++) {
					$startGlyphID = $this->read_ushort();
					$endGlyphID = $this->read_ushort();
					$class = $this->read_ushort();
					// Note: Font FreeSerif , tag "blws"
					// $BacktrackClasses[0] is defined ? a mistake in the font ???
					// Let's ignore for now
					if ($class > 0) {
						for($g=$startGlyphID;$g<=$endGlyphID;$g++) {
							if ($this->glyphToChar($g)) {
								$GlyphByClass[$class][$this->glyphToChar($g)] = 1;
							}
						}
					}
				}
			}
			$this->LuDataCache[$this->fontkey][$offset] = $GlyphByClass;
		}
		return $GlyphByClass;
	}


function _getOTLscriptTag($ScriptLang, $scripttag, $scriptblock, $shaper, $useOTL, $mode) {
	// ScriptLang is the array of available script/lang tags supported by the font
	// $scriptblock is the (number/code) for the script of the actual text string based on Unicode properties (UCDN::$uni_scriptblock)
	// $scripttag is the default tag derived from $scriptblock
/*
	http://www.microsoft.com/typography/otspec/ttoreg.htm
	http://www.microsoft.com/typography/otspec/scripttags.htm

Values for useOTL

Bit	dn	hn	Value
1	1	0x0001	GSUB/GPOS - Latin scripts
2	2	0x0002	GSUB/GPOS - Cyrillic scripts
3	4	0x0004	GSUB/GPOS - Greek scripts
4	8	0x0008	GSUB/GPOS - CJK scripts (excluding Hangul-Jamo)
5	16	0x0010	(Reserved)
6	32	0x0020	(Reserved)
7	64	0x0040	(Reserved)
8	128	0x0080	GSUB/GPOS - All other scripts (including all RTL scripts, complex scripts with shapers etc)

NB If change for RTL - cf. function magic_reverse_dir in mpdf.php to update

*/


	if ($scriptblock == UCDN::SCRIPT_LATIN) {
		if (!($useOTL & 0x01)) { return array('',false); }
	}
	else if ($scriptblock == UCDN::SCRIPT_CYRILLIC) {
		if (!($useOTL & 0x02)) { return array('',false); }
	}
	else if ($scriptblock == UCDN::SCRIPT_GREEK) {
		if (!($useOTL & 0x04)) { return array('',false); }
	}
	else if ($scriptblock >= UCDN::SCRIPT_HIRAGANA && $scriptblock <= UCDN::SCRIPT_YI ) { 
		if (!($useOTL & 0x08)) { return array('',false); }
	}
	else {
		if (!($useOTL & 0x80)) { return array('',false); }
	}

	//	If availabletags includes scripttag - choose
	if (isset($ScriptLang[$scripttag])) { return array($scripttag, false); }

	// 	If INDIC (or Myanmar) and available tag not includes new version, check if includes old version & choose old version
	if ($shaper) {
		switch($scripttag) {
			CASE 'bng2': if (isset($ScriptLang['beng'])) return array('beng',true);
			CASE 'dev2': if (isset($ScriptLang['deva'])) return array('deva',true);
			CASE 'gjr2': if (isset($ScriptLang['gujr'])) return array('gujr',true);
			CASE 'gur2': if (isset($ScriptLang['guru'])) return array('guru',true);
			CASE 'knd2': if (isset($ScriptLang['knda'])) return array('knda',true);
			CASE 'mlm2': if (isset($ScriptLang['mlym'])) return array('mlym',true);
			CASE 'ory2': if (isset($ScriptLang['orya'])) return array('orya',true);
			CASE 'tml2': if (isset($ScriptLang['taml'])) return array('taml',true);
			CASE 'tel2': if (isset($ScriptLang['telu'])) return array('telu',true);
			CASE 'mym2': if (isset($ScriptLang['mymr'])) return array('mymr',true);
		}
	}

	// 	choose DFLT if present 
	if (isset($ScriptLang['DFLT'])) { return array('DFLT', false); } 
	// 	else choose dflt if present
	if (isset($ScriptLang['dflt'])) { return array('dflt', false); } 
	// 	else return no scriptTag
	if (isset($ScriptLang['latn'])) { return array('latn', false); } 
	// 	else return no scriptTag
	return array('',false);

}



// LangSys tags
function _getOTLLangTag($ietf, $available) {
	// http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
	// http://www.microsoft.com/typography/otspec/languagetags.htm
	// IETF tag = e.g. en-US, und-Arab, sr-Cyrl cf. config_lang2fonts.php
	if ($available=='') { return ''; }
	$tags = preg_split('/-/',$ietf);
	$lang = '';
	$country = '';
	$script = '';
	$lang = strtolower($tags[0]);
	if (isset($tags[1]) && $tags[1]) { 
		if (strlen($tags[1]) == 2) { $country = strtolower($tags[1]); }
	}
	if (isset($tags[2]) && $tags[2]) { $country = strtolower($tags[2]); }

	if ($lang!='' && isset(UCDN::$ot_languages[$lang])) { $langsys = UCDN::$ot_languages[$lang]; }
	else if ($lang!='' && $country !='' && isset(UCDN::$ot_languages[$lang.''.$country])) { 
		$langsys = UCDN::$ot_languages[$lang.''.$country]; 
	}
	else { $langsys = "DFLT"; }
	if (strpos($available, $langsys)===false) { 
		if (strpos($available, "DFLT")!==false) { return "DFLT"; }
		else return ''; 
	}
	return $langsys;
}

function _dumpproc($GPOSSUB, $lookupID, $subtable, $Type, $Format, $ptr, $currGlyph, $level) {
	echo '<div style="padding-left: '.($level*2).'em;">';
	echo $GPOSSUB .' LookupID #'.$lookupID.' Subtable#'.$subtable .' Type: '.$Type.' Format: '.$Format.'<br />';
	echo '<div style="font-family:monospace">';
	echo 'Glyph position: '.$ptr.' Current Glyph: '.$currGlyph.'<br />';

	for ($i=0;$i<count($this->OTLdata);$i++) {
		if ($i==$ptr) { echo '<b>'; }
		echo $this->OTLdata[$i]['hex'] . ' ';
		if ($i==$ptr) { echo '</b>'; }
	}
	echo '<br />';

	for ($i=0;$i<count($this->OTLdata);$i++) {
		if ($i==$ptr) { echo '<b>'; }
		echo str_pad($this->OTLdata[$i]['uni'],5) . ' ';
		if ($i==$ptr) { echo '</b>'; }
	}
	echo '<br />';

	if ($GPOSSUB == 'GPOS') {
		for ($i=0;$i<count($this->OTLdata);$i++) {
			if (!empty($this->OTLdata[$i]['GPOSinfo'])) {
				echo $this->OTLdata[$i]['hex'] . ' &#x'.$this->OTLdata[$i]['hex'].'; ';
				print_r($this->OTLdata[$i]['GPOSinfo']);
				echo ' '; 
			}
		}
	}

	echo '</div>';
	echo '</div>';
}


}

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <?php

class wmf {

var $mpdf = null;
var $gdiObjectArray;

function wmf(&$mpdf) {
	$this->mpdf = $mpdf;
}


function _getWMFimage($data) {
	$k = _MPDFK;

		$this->gdiObjectArray = array();
		$a=unpack('stest',"\1\0");
		if ($a['test']!=1)
		return array(0, 'Error parsing WMF image - Big-endian architecture not supported'); 
		// check for Aldus placeable metafile header
		$key = unpack('Lmagic', substr($data, 0, 4));
		$p = 18;  // WMF header 
		if ($key['magic'] == (int)0x9AC6CDD7) { $p +=22; } // Aldus header
		// define some state variables
		$wo=null; // window origin
		$we=null; // window extent
		$polyFillMode = 0;
		$nullPen = false;
		$nullBrush = false;
		$endRecord = false;
		$wmfdata = '';
		while ($p < strlen($data) && !$endRecord) {
			$recordInfo = unpack('Lsize/Sfunc', substr($data, $p, 6));	$p += 6;
			// size of record given in WORDs (= 2 bytes)
			$size = $recordInfo['size'];
			// func is number of GDI function
			$func = $recordInfo['func'];
			if ($size > 3) {
				$parms = substr($data, $p, 2*($size-3));	$p += 2*($size-3);
			}
			switch ($func) {
				case 0x020b:  // SetWindowOrg
					// do not allow window origin to be changed
					// after drawing has begun
					if (!$wmfdata)
						$wo = array_reverse(unpack('s2', $parms));
					break;
				case 0x020c:  // SetWindowExt
					// do not allow window extent to be changed
					// after drawing has begun
					if (!$wmfdata)
						$we = array_reverse(unpack('s2', $parms));
					break;
				case 0x02fc:  // CreateBrushIndirect
					$brush = unpack('sstyle/Cr/Cg/Cb/Ca/Shatch', $parms);
					$brush['type'] = 'B';
					$this->_AddGDIObject($brush);
					break;
				case 0x02fa:  // CreatePenIndirect
					$pen = unpack('Sstyle/swidth/sdummy/Cr/Cg/Cb/Ca', $parms);
					// convert width from twips to user unit
					$pen['width'] /= (20 * $k);
					$pen['type'] = 'P';
					$this->_AddGDIObject($pen);
					break;

				// MUST create other GDI objects even if we don't handle them
				case 0x06fe: // CreateBitmap
				case 0x02fd: // CreateBitmapIndirect
				case 0x00f8: // CreateBrush
				case 0x02fb: // CreateFontIndirect
				case 0x00f7: // CreatePalette
				case 0x01f9: // CreatePatternBrush
				case 0x06ff: // CreateRegion
				case 0x0142: // DibCreatePatternBrush
					$dummyObject = array('type'=>'D');
					$this->_AddGDIObject($dummyObject);
					break;
				case 0x0106:  // SetPolyFillMode
					$polyFillMode = unpack('smode', $parms);
					$polyFillMode = $polyFillMode['mode'];
					break;
				case 0x01f0:  // DeleteObject
					$idx = unpack('Sidx', $parms);
					$idx = $idx['idx'];
					$this->_DeleteGDIObject($idx);
					break;
				case 0x012d:  // SelectObject
					$idx = unpack('Sidx', $parms);
					$idx = $idx['idx'];
					$obj = $this->_GetGDIObject($idx);
					switch ($obj['type']) {
						case 'B':
							$nullBrush = false;
							if ($obj['style'] == 1) { $nullBrush = true; }
							else {
								$wmfdata .= $this->mpdf->SetFColor($this->mpdf->ConvertColor('rgb('.$obj['r'].','.$obj['g'].','.$obj['b'].')'), true)."\n";	
							}
							break;
						case 'P':
							$nullPen = false;
							$dashArray = array(); 
							// dash parameters are custom
							switch ($obj['style']) {
								case 0: // PS_SOLID
									break;
								case 1: // PS_DASH
									$dashArray = array(3,1);
									break;
								case 2: // PS_DOT
									$dashArray = array(0.5,0.5);
									break;
								case 3: // PS_DASHDOT
									$dashArray = array(2,1,0.5,1);
									break;
								case 4: // PS_DASHDOTDOT
									$dashArray = array(2,1,0.5,1,0.5,1);
									break;
								case 5: // PS_NULL
									$nullPen = true;
									break;
							}
							if (!$nullPen) {
								$wmfdata .= $this->mpdf->SetDColor($this->mpdf->ConvertColor('rgb('.$obj['r'].','.$obj['g'].','.$obj['b'].')'), true)."\n";
								$wmfdata .= sprintf("%.3F w\n",$obj['width']*$k);
							}
							if (!empty($dashArray)) {
								$s = '[';
								for ($i=0; $i<count($dashArray);$i++) {
									$s .= $dashArray[$i] * $k;
									if ($i != count($dashArray)-1) { $s .= ' '; }
								}
								$s .= '] 0 d';
								$wmfdata .= $s."\n";
							}
							break;
					}
					break;
				case 0x0325: // Polyline
				case 0x0324: // Polygon
					$coords = unpack('s'.($size-3), $parms);
					$numpoints = $coords[1];
					for ($i = $numpoints; $i > 0; $i--) {
						$px = $coords[2*$i];
						$py = $coords[2*$i+1];

						if ($i < $numpoints) { $wmfdata .= $this->_LineTo($px, $py); }
					   else { $wmfdata .= $this->_MoveTo($px, $py); }
					}
					if ($func == 0x0325) { $op = 's'; }
					else if ($func == 0x0324) {
						if ($nullPen) {
							if ($nullBrush) { $op = 'n'; } // no op
							else { $op = 'f'; } // fill
						}
						else {
							if ($nullBrush) { $op = 's'; } // stroke
							else { $op = 'b'; } // stroke and fill
						}
						if ($polyFillMode==1 && ($op=='b' || $op=='f')) { $op .= '*'; } // use even-odd fill rule
					}
					$wmfdata .= $op."\n";
					break;
				case 0x0538: // PolyPolygon
					$coords = unpack('s'.($size-3), $parms);
					$numpolygons = $coords[1];
					$adjustment = $numpolygons;
					for ($j = 1; $j <= $numpolygons; $j++) {
						$numpoints = $coords[$j + 1];
						for ($i = $numpoints; $i > 0; $i--) {
							$px = $coords[2*$i   + $adjustment];
							$py = $coords[2*$i+1 + $adjustment];
							if ($i == $numpoints) { $wmfdata .= $this->_MoveTo($px, $py); }
							else { $wmfdata .= $this->_LineTo($px, $py); }
						}
						$adjustment += $numpoints * 2;
					}

					if ($nullPen) {
						if ($nullBrush) { $op = 'n'; } // no op
						else { $op = 'f'; } // fill
					}
					else {
						if ($nullBrush) { $op = 's'; } // stroke
						else { $op = 'b'; } // stroke and fill
					}
					if ($polyFillMode==1 && ($op=='b' || $op=='f')) { $op .= '*'; } // use even-odd fill rule
					$wmfdata .= $op."\n";
					break;
				case 0x0000:
					$endRecord = true;
					break;
			}
		}


	return array(1,$wmfdata,$wo,$we);
}


function _MoveTo($x, $y) {
	return "$x $y m\n";
}

// a line must have been started using _MoveTo() first
function _LineTo($x, $y) {
	return "$x $y l\n";
}

function _AddGDIObject($obj) {
	// find next available slot
	$idx = 0;
	if (!empty($this->gdiObjectArray)) {
		$empty = false;
		$i = 0;
		while (!$empty) {
			$empty = !isset($this->gdiObjectArray[$i]);
			$i++;
		}
		$idx = $i-1;
	}
	$this->gdiObjectArray[$idx] = $obj;
}

function _GetGDIObject($idx) {
	return $this->gdiObjectArray[$idx];
}

function _DeleteGDIObject($idx) {
	unset($this->gdiObjectArray[$idx]);
}


}

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             		$this->errormsg = sprintf("Unable to find object ({$obj_spec[1]}, {$obj_spec[2]}) at expected location");
				return false;
    			}

    			// If we're being asked to store all the information
    			// about the object, we add the object ID and generation
    			// number for later use
				$this->actual_obj =& $result;
    			if ($encapsulate) {
    				$result = array (
    					PDF_TYPE_OBJECT,
    					'obj' => $obj_spec[1],
    					'gen' => $obj_spec[2]
    				);
    			} else {
    				$result = array();
    			}

    			// Now simply read the object data until
    			// we encounter an end-of-object marker
    			while(1) {
                    $value = $this->pdf_read_value($c);
					if ($value === false || count($result) > 4) {
						// in this case the parser coudn't find an endobj so we break here
						break;
    				}

    				if ($value[0] == PDF_TYPE_TOKEN && $value[1] === 'endobj') {
    					break;
    				}

                    $result[] = $value;
    			}

    			$c->reset($old_pos);

                if (isset($result[2][0]) && $result[2][0] == PDF_TYPE_STREAM) {
                    $result[0] = PDF_TYPE_STREAM;
                }

    			return $result;
    		}
    	} else {
    		return $obj_spec;
    	}
    }

    
    
    /**
     * Reads a token from the file
     *
     * @param object $c pdf_context
     * @return mixed
     */
    function pdf_read_token(&$c)
    {
    	// If there is a token available
    	// on the stack, pop it out and
    	// return it.

    	if (count($c->stack)) {
    		return array_pop($c->stack);
    	}

    	// Strip away any whitespace

    	do {
    		if (!$c->ensure_content()) {
    			return false;
    		}
    		$c->offset += _strspn($c->buffer, " \n\r\t", $c->offset);
    	} while ($c->offset >= $c->length - 1);

    	// Get the first character in the stream

    	$char = $c->buffer[$c->offset++];

    	switch ($char) {

    		case '['	:
    		case ']'	:
    		case '('	:
    		case ')'	:

    			// This is either an array or literal string
    			// delimiter, Return it

    			return $char;

    		case '<'	:
    		case '>'	:

    			// This could either be a hex string or
    			// dictionary delimiter. Determine the
    			// appropriate case and return the token

    			if ($c->buffer[$c->offset] == $char) {
    				if (!$c->ensure_content()) {
    				    return false;
    				}
    				$c->offset++;
    				return $char . $char;
    			} else {
    				return $char;
    			}

    		default		:

    			// This is "another" type of token (probably
    			// a dictionary entry or a numeric value)
    			// Find the end and return it.

    			if (!$c->ensure_content()) {
    				return false;
    			}

    			while(1) {

    				// Determine the length of the token

    				$pos = _strcspn($c->buffer, " []<>()\r\n\t/", $c->offset);
    				if ($c->offset + $pos <= $c->length - 1) {
    					break;
    				} else {
    					// If the script reaches this point,
    					// the token may span beyond the end
    					// of the current buffer. Therefore,
    					// we increase the size of the buffer
    					// and try again--just to be safe.

    					$c->increase_length();
    				}
    			}

    			$result = substr($c->buffer, $c->offset - 1, $pos + 1);

    			$c->offset += $pos;
    			return $result;
    	}
    }

	
}

?>