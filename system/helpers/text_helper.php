read_ushort();

							$PosLookupRecord = array();
							for ($p=0;$p<$PosCount;$p++) {
								// PosLookupRecord
								$PosLookupRecord[$p]['SequenceIndex'] = $this->read_ushort();
								$PosLookupRecord[$p]['LookupListIndex'] = $this->read_ushort();
							}

							$backtrackGlyphs = array();
							for ($b=0;$b<$BacktrackGlyphCount;$b++) {
								$this->seek($CoverageBacktrackOffset[$b]);
								$backtrackGlyphs[$b] = implode('|',$this->_getCoverage());
							}
							$inputGlyphs = array();
							for ($b=0;$b<$InputGlyphCount;$b++) {
								$this->seek($CoverageInputOffset[$b]);
								$inputGlyphs[$b] = implode('|',$this->_getCoverage());
							}
							$lookaheadGlyphs = array();
							for ($b=0;$b<$LookaheadGlyphCount;$b++) {
								$this->seek($CoverageLookaheadOffset[$b]);
								$lookaheadGlyphs[$b] = implode('|',$this->_getCoverage());
							}

							$exampleB = array();
							$exampleI = array();
							$exampleL = array();
							$html .= '<div class="context">CONTEXT: ';
							for ($ff=count($backtrackGlyphs)-1;$ff>=0;$ff--) {
								$html .= '<div>Backtrack #'.$ff.': <span class="unicode">'.$this->formatUniStr($backtrackGlyphs[$ff]).'</span></div>';
								$exampleB[] = $this->formatEntityFirst($backtrackGlyphs[$ff]);
							}
							for ($ff=0;$ff<count($inputGlyphs);$ff++) {
								$html .= '<div>Input #'.$ff.': <span class="unchanged">&nbsp;'.$this->formatEntityStr($inputGlyphs[$ff]).'&nbsp;</span></div>';
								$exampleI[] = $this->formatEntityFirst($inputGlyphs[$ff]);
							}
							for ($ff=0;$ff<count($lookaheadGlyphs);$ff++) {
								$html .= '<div>Lookahead #'.$ff.': <span class="unicode">'.$this->formatUniStr($lookaheadGlyphs[$ff]).'</span></div>';
								$exampleL[] = $this->formatEntityFirst($lookaheadGlyphs[$ff]);
							}
							$html .= '</div>'; 


							for ($p=0;$p<$PosCount;$p++) {
								$lup = $PosLookupRecord[$p]['LookupListIndex'] ;
								$seqIndex = $PosLookupRecord[$p]['SequenceIndex'] ;

								// GENERATE exampleB[n] exampleI[<seqIndex] .... exampleI[>seqIndex] exampleL[n]
								$exB = '';
								$exL = '';
								if (count($exampleB)) { $exB .= '<span class="backtrack">'.implode('&#x200d;',$exampleB).'</span>'; }

								if ($seqIndex>0) {
									$exB .= '<span class="inputother">';
									for($ip=0;$ip<$seqIndex;$ip++) {
										$exB .=  $exampleI[$ip].'&#x200d;';
									}
									$exB .= '</span>';
								}

								if (count($inputGlyphs)>($seqIndex+1)) {
									$exL .= '<span class="inputother">';
									for($ip=$seqIndex+1;$ip<count($inputGlyphs);$ip++) {
										$exL .=  '&#x200d;'.$exampleI[$ip];
									}
									$exL .= '</span>';
								}

								if (count($exampleL)) { $exL .= '<span class="lookahead">'.implode('&#x200d;',$exampleL).'</span>'; }

								$html .= '<div class="sequenceIndex">Substitution Position: '.$seqIndex.'</div>'; 

								$lul2 = array($lup=>$tag);

								// Only apply if the (first) 'Replace' glyph from the 
								// Lookup list is in the [inputGlyphs] at ['SequenceIndex']
								// Pass $inputGlyphs[$seqIndex] e.g. 00636|00645|00656
								// to level 2 and only apply if first Replace glyph is in this list
								$html .= $this->_getGPOSarray($Lookup, $lul2, $scripttag, 2, $inputGlyphs[$seqIndex], $exB, $exL);

							}
						}
					}

				}
				$html .= '</div>'; 
			}
			if ($level ==1) { $this->mpdf->WriteHTML($html); }
			else  { return $html; }
//print_r($Lookup); exit;
	}
			//=====================================================================================
			//=====================================================================================
			// GPOS FUNCTIONS
			//=====================================================================================

	function count_bits($n) {     
		for ($c=0; $n; $c++) {
			$n &= $n - 1; // clear the least significant bit set
		}
		return $c;
	}

	function _getValueRecord($ValueFormat) {	// Common ValueRecord for GPOS
		// Only returns 3 possible: $vra['XPlacement'] $vra['YPlacement'] $vra['XAdvance'] 
		$vra = array();
		// Horizontal adjustment for placement-in design units
		if (($ValueFormat & 0x0001) == 0x0001) { $vra['XPlacement'] = $this->read_short(); }
		// Vertical adjustment for placement-in design units
		if (($ValueFormat & 0x0002) == 0x0002) { $vra['YPlacement'] = $this->read_short(); }
		// Horizontal adjustment for advance-in design units (only used for horizontal writing)
		if (($ValueFormat & 0x0004) == 0x0004) { $vra['XAdvance'] = $this->read_short(); }
		// Vertical adjustment for advance-in design units (only used for vertical writing)
		if (($ValueFormat & 0x0008) == 0x0008) { $this->read_short(); }
		// Offset to Device table for horizontal placement-measured from beginning of PosTable (may be NULL)
		if (($ValueFormat & 0x0010) == 0x0010) { $this->read_ushort(); }
		// Offset to Device table for vertical placement-measured from beginning of PosTable (may be NULL)
		if (($ValueFormat & 0x0020) == 0x0020) { $this->read_ushort(); }
		// Offset to Device table for horizontal advance-measured from beginning of PosTable (may be NULL)
		if (($ValueFormat & 0x0040) == 0x0040) { $this->read_ushort(); }
		// Offset to Device table for vertical advance-measured from beginning of PosTable (may be NULL)
		if (($ValueFormat & 0x0080) == 0x0080) { $this->read_ushort(); }
		return $vra;
	}

	function _getAnchorTable($offset=0) {
		if ($offset) { $this->seek($offset); }
		$AnchorFormat = $this->read_ushort();
		$XCoordinate = $this->read_short(); 
		$YCoordinate = $this->read_short(); 
		// Format 2 specifies additional link to contour point; Format 3 additional Device table
		return array($XCoordinate, $YCoordinate);
	}

	function _getMarkRecord($offset, $MarkPos) {
		$this->seek($offset);
		$MarkCount = $this->read_ushort();
		$this->skip($MarkPos*4);
		$Class = $this->read_ushort(); 
		$MarkAnchor = $offset + $this->read_ushort(); 	// = Offset to anchor table
		list($x,$y) = $this->_getAnchorTable($MarkAnchor );
		$MarkRecord = array('Class'=>$Class, 'AnchorX'=>$x, 'AnchorY'=>$y);
		return $MarkRecord;
	}


	//////////////////////////////////////////////////////////////////////////////////
	// Recursively get composite glyph data
	function getGlyphData($originalGlyphIdx, &$maxdepth, &$depth, &$points, &$contours) {
		$depth++;
		$maxdepth = max($maxdepth, $depth);
		if (count($this->glyphdata[$originalGlyphIdx]['compGlyphs'])) {
			foreach($this->glyphdata[$originalGlyphIdx]['compGlyphs'] AS $glyphIdx) {
				$this->getGlyphData($glyphIdx, $maxdepth, $depth, $points, $contours);
			}
		}
		else if (($this->glyphdata[$originalGlyphIdx]['nContours'] > 0) && $depth > 0) {	// simple
			$contours += $this->glyphdata[$originalGlyphIdx]['nContours'];
			$points += $this->glyphdata[$originalGlyphIdx]['nPoints'];
		}
		$depth--;
	}


	//////////////////////////////////////////////////////////////////////////////////
	// Recursively get composite glyphs
	function getGlyphs($originalGlyphIdx, &$start, &$glyphSet, &$subsetglyphs) {
		$glyphPos = $this->glyphPos[$originalGlyphIdx];
		$glyphLen = $this->glyphPos[$originalGlyphIdx + 1] - $glyphPos;
		if (!$glyphLen) { 
			return;
		}
		$this->seek($start + $glyphPos);
		$numberOfContours = $this->read_short();
		if ($numberOfContours < 0) {
			$this->skip(8);
			$flags = GF_MORE;
			while ($flags & GF_MORE) {
				$flags = $this->read_ushort();
				$glyphIdx = $this->read_ushort();
				if (!isset($glyphSet[$glyphIdx])) {
					$glyphSet[$glyphIdx] = count($subsetglyphs);	// old glyphID to new glyphID
					$subsetglyphs[$glyphIdx] = true;
				}
				$savepos = ftell($this->fh);
				$this->getGlyphs($glyphIdx, $start, $glyphSet, $subsetglyphs);
				$this->seek($savepos);
				if ($flags & GF_WORDS)
					$this->skip(4);
				else
					$this->skip(2);
				if ($flags & GF_SCALE)
					$this->skip(2);
				else if ($flags & GF_XYSCALE)
					$this->skip(4);
				else if ($flags & GF_TWOBYTWO)
					$this->skip(8);
			}
		}
	}

	//////////////////////////////////////////////////////////////////////////////////

	function getHMTX($numberOfHMetrics, $numGlyphs, &$glyphToChar, $scale) {
		$start = $this->seek_table("hmtx");
		$aw = 0;
		$this->charWidths = str_pad('', 256*256*2, "\x00");
		if ($this->maxUniChar > 65536) { $this->charWidths .= str_pad('', 256*256*2, "\x00"); }	// Plane 1 SMP
		if ($this->maxUniChar > 131072) { $this->charWidths .= str_pad('', 256*256*2, "\x00"); }	// Plane 2 SMP
		$nCharWidths = 0;
		if (($numberOfHMetrics*4) < $this->maxStrLenRead) {
			$data = $this->get_chunk($start,($numberOfHMetrics*4));
			$arr = unpack("n*", $data);
		}
		else { $this->seek($start); }
		for( $glyph=0; $glyph<$numberOfHMetrics; $glyph++) {
			if (($numberOfHMetrics*4) < $this->maxStrLenRead) {
				$aw = $arr[($glyph*2)+1];
			}
			else {
				$aw = $this->read_ushort();
				$lsb = $this->read_ushort();
			}
			if (isset($glyphToChar[$glyph]) || $glyph == 0) {

				if ($aw >= (1 << 15) ) { $aw = 0; }	// 1.03 Some (arabic) fonts have -ve values for width
					// although should be unsigned value - comes out as e.g. 65108 (intended -50)
				if ($glyph == 0) {
					$this->defaultWidth = $scale*$aw;
					continue;
				}
				foreach($glyphToChar[$glyph] AS $char) {
					//$this->charWidths[$char] = intval(round($scale*$aw));
					if ($char != 0 && $char != 65535) {
 						$w = intval(round($scale*$aw));
						if ($w == 0) { $w = 65535; }
						if ($char < 196608) {
							$this->charWidths[$char*2] = chr($w >> 8);
							$this->charWidths[$char*2 + 1] = chr($w & 0xFF);
							$nCharWidths++;
						}
					}
				}
			}
		}
		$data = $this->get_chunk(($start+$numberOfHMetrics*4),($numGlyphs*2));
		$arr = unpack("n*", $data);
		$diff = $numGlyphs-$numberOfHMetrics;
		$w = intval(round($scale*$aw));
		if ($w == 0) { $w = 65535; }
		for( $pos=0; $pos<$diff; $pos++) {
			$glyph = $pos + $numberOfHMetrics;
			if (isset($glyphToChar[$glyph])) {
				foreach($glyphToChar[$glyph] AS $char) {
					if ($char != 0 && $char != 65535) {
						if ($char < 196608) { 
							$this->charWidths[$char*2] = chr($w >> 8);
							$this->charWidths[$char*2 + 1] = chr($w & 0xFF);
							$nCharWidths++;
						}
					}
				}
			}
		}
		// NB 65535 is a set width of 0
		// First bytes define number of chars in font
		$this->charWidths[0] = chr($nCharWidths >> 8);
		$this->charWidths[1] = chr($nCharWidths & 0xFF);
	}





	function getHMetric($numberOfHMetrics, $gid) {
		$start = $this->seek_table("hmtx");
		if ($gid < $numberOfHMetrics) {
			$this->seek($start+($gid*4));
			$hm = fread($this->fh,4);
		}
		else {
			$this->seek($start+(($numberOfHMetrics-1)*4));
			$hm = fread($this->fh,2);
			$this->seek($start+($numberOfHMetrics*2)+($gid*2));
			$hm .= fread($this->fh,2);
		}
		return $hm;
	}

	function getLOCA($indexToLocFormat, $numGlyphs) {
		$start = $this->seek_table('loca');
		$this->glyphPos = array();
		if ($indexToLocFormat == 0) {
			$data = $this->get_chunk($start,($numGlyphs*2)+2);
			$arr = unpack("n*", $data);
			for ($n=0; $n<=$numGlyphs; $n++) {
				$this->glyphPos[] = ($arr[$n+1] * 2);
			}
		}
		else if ($indexToLocFormat == 1) {
			$data = $this->get_chunk($start,($numGlyphs*4)+4);
			$arr = unpack("N*", $data);
			for ($n=0; $n<=$numGlyphs; $n++) {
				$this->glyphPos[] = ($arr[$n+1]);
			}
		}
		else 
			die('Unknown location table format '.$indexToLocFormat);
	}


	// CMAP Format 4
	function getCMAP4($unicode_cmap_offset, &$glyphToChar, &$charToGlyph ) {
		$this->maxUniChar = 0;	
		$this->seek($unicode_cmap_offset + 2);
		$length = $this->read_ushort();
		$limit = $unicode_cmap_offset + $length;
		$this->skip(2);

		$segCount = $this->read_ushort() / 2;
		$this->skip(6);
		$endCount = array();
		for($i=0; $i<$segCount; $i++) { $endCount[] = $this->read_ushort(); }
		$this->skip(2);
		$startCount = array();
		for($i=0; $i<$segCount; $i++) { $startCount[] = $this->read_ushort(); }
		$idDelta = array();
		for($i=0; $i<$segCount; $i++) { $idDelta[] = $this->read_short(); }		// ???? was unsigned short
		$idRangeOffset_start = $this->_pos;
		$idRangeOffset = array();
		for($i=0; $i<$segCount; $i++) { $idRangeOffset[] = $this->read_ushort(); }

		for ($n=0;$n<$segCount;$n++) {
			$endpoint = ($endCount[$n] + 1);
			for ($unichar=$startCount[$n];$unichar<$endpoint;$unichar++) {
				if ($idRangeOffset[$n] == 0)
					$glyph = ($unichar + $idDelta[$n]) & 0xFFFF;
				else {
					$offset = ($unichar - $startCount[$n]) * 2 + $idRangeOffset[$n];
					$offset = $idRangeOffset_start + 2 * $n + $offset;
					if ($offset >= $limit)
						$glyph = 0;
					else {
						$glyph = $this->get_ushort($offset);
						if ($glyph != 0)
						   $glyph = ($glyph + $idDelta[$n]) & 0xFFFF;
					}
				}
				$charToGlyph[$unichar] = $glyph;
				if ($unichar < 196608) { $this->maxUniChar = max($unichar,$this->maxUniChar); }
				$glyphToChar[$glyph][] = $unichar;
			}
		}

	}

function formatUni($char) {
	$x = preg_replace('/^[0]*/','',$char);
	$x = str_pad($x, 4, '0', STR_PAD_LEFT);
	$d = hexdec($x);
	if (($d>57343 && $d<63744) || ($d>122879 && $d<126977)) { $id = 'M'; }	// E000 - F8FF, 1E000-1F000
	else { $id = 'U'; }
	return $id .'+'.$x;
}
function formatEntity($char, $allowjoining=false) {
	$char = preg_replace('/^[0]/','',$char);
	$x = '&#x'.$char.';';
	if (strpos($this->GlyphClassMarks, $char)!==false) { 
		if (!$allowjoining) { 
			$x = '&#x25cc;'.$x; 
		}
	}
	return $x;
}
function formatUniArr($arr) {
	$s = array();
	foreach($arr AS $c) {
		$x = preg_replace('/^[0]*/','',$c);
		$d = hexdec($x);
		if (($d>57343 && $d<63744) || ($d>122879 && $d<126977)) { $id = 'M'; }	// E000 - F8FF, 1E000-1F000
		else { $id = 'U'; }
		$s[] = $id .'+'.str_pad($x, 4, '0', STR_PAD_LEFT);
	}
	return implode(', ',$s);
}
function formatEntityArr($arr) {
	$s = array();
	foreach($arr AS $c) {
		$c = preg_replace('/^[0]/','',$c);
		$x = '&#x'.$c.';';
		if (strpos($this->GlyphClassMarks, $c)!==false) { 
			$x = '&#x25cc;'.$x; 
		}
		$s[] = $x;
	}
	return implode(' ',$s);	// ZWNJ? &#x200d;
}
fun