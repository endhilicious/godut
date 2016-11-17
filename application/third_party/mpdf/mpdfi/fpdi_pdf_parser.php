ndaries.
	for ($ir=0; $ir<=$dictr;$ir++) {
		$laststrongtype = -1;
		for ($nc=0;$nc<$numchunks;$nc++) {
			$chardata =& $para[$nc][18]['char_data'];
			$numchars = count($chardata);
			for ($i=0; $i < $numchars; ++$i) {
				if (!isset($chardata[$i]['diid']) || $chardata[$i]['diid']!=$ir) { continue; }	// Ignore characters in a different isolate run
				if (isset($chardata[$i]['sor'])) { $laststrongtype = $chardata[$i]['sor']; }
				if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_ON || $chardata[$i]['type'] == UCDN::BIDI_CLASS_WS) {
					$left = -1;
					// LEFT
					if ($laststrongtype == UCDN::BIDI_CLASS_R || $laststrongtype == UCDN::BIDI_CLASS_EN || $laststrongtype == UCDN::BIDI_CLASS_AN) { 
						$left = UCDN::BIDI_CLASS_R; 
					}
					else if ($laststrongtype == UCDN::BIDI_CLASS_L) { 
						$left = UCDN::BIDI_CLASS_L; 
					}
					// RIGHT
					$right = -1;
					// move to the right of any following neutrals OR hit a run boundary

					if (isset($chardata[$i]['eor'])) { 
						$right = $chardata[$i]['eor']; 
					}
					else {
						$nexttype = -1;
						$nc2 = $nc;
						$i2 = $i;
						while (!($nc2==($numchunks-1) && $i2==((count($para[$nc2][18]['char_data']))-1))) { // while not at end of last chunk
							$i2++;
							if ($i2 >= count($para[$nc2][18]['char_data'])) { 
								$nc2++;
								$i2 = 0;
							}
							if (!isset($para[$nc2][18]['char_data'][$i2]['diid']) || $para[$nc2][18]['char_data'][$i2]['diid']!=$ir) { continue; }
							$nexttype = $para[$nc2][18]['char_data'][$i2]['type']; 
							if ($nexttype == UCDN::BIDI_CLASS_R || $nexttype == UCDN::BIDI_CLASS_EN || $nexttype == UCDN::BIDI_CLASS_AN) { 
								$right =  UCDN::BIDI_CLASS_R; 
								break;
							}
							else if ($nexttype == UCDN::BIDI_CLASS_L) { 
								$right =  UCDN::BIDI_CLASS_L; 
								break;
							}
							else if (isset($para[$nc2][18]['char_data'][$i2]['eor'])) { 
								$right = $para[$nc2][18]['char_data'][$i2]['eor']; 
								break;
							}
						}
					}

					if ($left > -1 && $left==$right) {
						$chardata[$i]['orig_type'] = $chardata[$i]['type'];	// Need to store the original 'WS' for reference in L1 below
						$chardata[$i]['type'] = $left;
					}
				}
				else if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_L || $chardata[$i]['type'] == UCDN::BIDI_CLASS_R || $chardata[$i]['type'] == UCDN::BIDI_CLASS_EN || $chardata[$i]['type'] == UCDN::BIDI_CLASS_AN) { 
					$laststrongtype =  $chardata[$i]['type']; 
				}
			}
		}
	}

	// N2. Any remaining neutrals take the embedding direction
	for ($nc=0;$nc<$numchunks;$nc++) {
		$chardata =& $para[$nc][18]['char_data'];
		$numchars = count($chardata);
		for ($i=0; $i < $numchars; ++$i) {
			if (isset($chardata[$i]['type']) && ($chardata[$i]['type'] == UCDN::BIDI_CLASS_ON || $chardata[$i]['type'] == UCDN::BIDI_CLASS_WS)) {
				$chardata[$i]['orig_type'] = $chardata[$i]['type'];	// Need to store the original 'WS' for reference in L1 below
				$chardata[$i]['type'] = ($chardata[$i]['level'] % 2) ?  UCDN::BIDI_CLASS_R : UCDN::BIDI_CLASS_L;
			}
		}
	}

	// I1. For all characters with an even (left-to-right) embedding direction, those of type R go up one level and those of type AN or EN go up two levels.
	// I2. For all characters with an odd (right-to-left) embedding direction, those of type L, EN or AN go up one level.
	for ($nc=0;$nc<$numchunks;$nc++) {
		$chardata =& $para[$nc][18]['char_data'];
		$numchars = count($chardata);
		for ($i=0; $i < $numchars; ++$i) {
			if (isset($chardata[$i]['level'])) { 
				$odd = $chardata[$i]['level'] % 2;
				if ($odd) {
					if (($chardata[$i]['type'] == UCDN::BIDI_CLASS_L) || ($chardata[$i]['type'] == UCDN::BIDI_CLASS_AN) || ($chardata[$i]['type'] == UCDN::BIDI_CLASS_EN)) {
						$chardata[$i]['level'] += 1;
					}
				}
				else {
					if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_R) { $chardata[$i]['level'] += 1; }
					else if (($chardata[$i]['type'] == UCDN::BIDI_CLASS_AN) || ($chardata[$i]['type'] == UCDN::BIDI_CLASS_EN)) { $chardata[$i]['level'] += 2; }
				}
			}
		}
	}

	// Remove Isolate formatters
	$numchunks = count($para);
	if ($controlchars) {
		for ($nc=0;$nc<$numchunks;$nc++) {
			$this->removeChar($para[$nc][0], $para[$nc][18], "\xe2\x81\xa6");
			$this->removeChar($para[$nc][0], $para[$nc][18], "\xe2\x81\xa7");
			$this->removeChar($para[$nc][0], $para[$nc][18], "\xe2\x81\xa8");
			$this->removeChar($para[$nc][0], $para[$nc][18], "\xe2\x81\xa9");
			preg_replace("/\x{2066}-\x{2069}/u", '', $para[$nc][0]);
		}
		// Remove any blank chunks made by removing directional codes
		for ($nc=($numchunks-1);$nc>=0;$nc--) {
			if (count($para[$nc][18]['char_data'])==0) { array_splice($para, $nc, 1); }
		}
	}

}



// Reorder, once divided into lines

function _bidiReorder(&$chunkorder, &$content, &$cOTLdata, $blockdir) {

	$bidiData = array();

	// First combine into one array (and get the highest level in use)
	$numchunks = count($content);
	$maxlevel = 0;
	for ($nc=0;$nc<$numchunks;$nc++) {
		$numchars = count($cOTLdata[$nc]['char_data']);
		for ($i=0; $i < $numchars; ++$i) {

			$carac = array();
			if (isset($cOTLdata[$nc]['GPOSinfo'][$i])) {$carac['GPOSinfo'] = $cOTLdata[$nc]['GPOSinfo'][$i]; }
			$carac['uni'] = $cOTLdata[$nc]['char_data'][$i]['uni'];
			if (isset($cOTLdata[$nc]['char_data'][$i]['type'])) $carac['type'] = $cOTLdata[$nc]['char_data'][$i]['type'];
			if (isset($cOTLdata[$nc]['char_data'][$i]['level'])) $carac['level'] = $cOTLdata[$nc]['char_data'][$i]['level'];
			if (isset($cOTLdata[$nc]['char_data'][$i]['orig_type'])) { $carac['orig_type'] = $cOTLdata[$nc]['char_data'][$i]['orig_type']; }
			$carac['group'] = $cOTLdata[$nc]['group']{$i};
			$carac['chunkid'] = $chunkorder[$nc];	// gives font id and/or object ID

			$maxlevel = max((isset($carac['level']) ? $carac['level'] : 0),$maxlevel);
			$bidiData[] = $carac;
		}
	}
	if ($maxlevel==0) { return; }

	$numchars = count($bidiData);

	// L1. On each line, reset the embedding level of the following characters to the paragraph embedding level:
	//	1. Segment separators (Tab) 'S',
	//	2. Paragraph separators 'B',
	//	3. Any sequence of whitespace characters 'WS' preceding a segment separator or paragraph separator, and
	//	4. Any sequence of whitespace characters 'WS' at the end of the line.
	//	The types of characters used here are the original types, not those modified by the previous phase cf N1 and N2*******
	//	Because a Paragraph Separator breaks lines, there will be at most one per line, at the end of that line.

	// Set the initial paragraph embedding level
	if ($blockdir == 'rtl') { $pel = 1; } 
	else { $pel = 0; }

	for ($i=($numchars-1); $i>0; $i--) {
		if ($bidiData[$i]['type'] == UCDN::BIDI_CLASS_WS || (isset($bidiData[$i]['orig_type']) && $bidiData[$i]['orig_type'] == UCDN::BIDI_CLASS_WS)) {
				$bidiData[$i]['level'] = $pel;
		}
		else { break; }
	}


	// L2. From the highest level found in the text to the lowest odd level on each line, including intermediate levels not actually present in the text, reverse any contiguous sequence of characters that are at that level or higher.
	for ($j=$maxlevel; $j > 0; $j--) {
		$ordarray = array();
		$revarr = array();
		$onlevel = false;
		for ($i=0; $i < $numchars; ++$i) {
			if ($bidiData[$i]['level'] >= $j) {
				$onlevel = true;
				// L4. A character is depicted by a mirrored glyph if and only if (a) the resolved directionality of that character is R, and (b) the Bidi_Mirrored property value of that character is true. 
				if (isset(UCDN::$mirror_pairs[$bidiData[$i]['uni']]) && $bidiData[$i]['type']==UCDN::BIDI_CLASS_R) {
					$bidiData[$i]['uni'] = UCDN::$mirror_pairs[$bidiData[$i]['uni']];
				}

				$revarr[] = $bidiData[$i];
			}
			else {
				if ($onlevel) {
					$revarr = array_reverse($revarr);
					$ordarray = array_merge($ordarray, $revarr);
					$revarr = Array();
					$onlevel = false;
				}
				$ordarray[] = $bidiData[$i];
			}
		}
		if ($onlevel) {
			$revarr = array_reverse($revarr);
			$ordarray = array_merge($ordarray, $revarr);
		}
		$bidiData = $ordarray;
 	}

	$content = array();
	$cOTLdata = array();
	$chunkorder = array();



	$nc = -1;	// New chunk order ID
	$chunkid = -1;

	foreach ($bidiData as $carac) {
		if ($carac['chunkid'] != $chunkid) {
			$nc++;
			$chunkorder[$nc] = $carac['chunkid'];
			$cctr = 0;
			$content[$nc] = '';
			$cOTLdata[$nc]['group'] = '';
		}
		if ($carac['uni'] != 0xFFFC) {  	// Object replacement character (65532)
			$content[$nc] .= code2utf($carac['uni']);
			$cOTLdata[$nc]['group'] .= $carac['group'];
			if (!empty($carac['GPOSinfo'])) {
				if (isset($carac['GPOSinfo'])) { $cOTLdata[$nc]['GPOSinfo'][$cctr] = $carac['GPOSinfo']; }
				$cOTLdata[$nc]['GPOSinfo'][$cctr]['wDir'] = ($carac['level'] % 2) ? 'RTL' : 'LTR';
			}
		}
		$chunkid = $carac['chunkid'];
		$cctr++;
	}

}






////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
// These functions are called from mpdf after GSUB/GPOS has taken place
// At this stage the bidi-type is in string form
////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
function splitOTLdata(&$cOTLdata, $OTLcutoffpos, $OTLrestartpos='') {
	if (!$OTLrestartpos) { $OTLrestartpos = $OTLcutoffpos; }
	$newOTLdata = array('GPOSinfo' => array(), 'char_data' => array());
	$newOTLdata['group'] = substr($cOTLdata['group'],$OTLrestartpos);
	$cOTLdata['group'] = substr($cOTLdata['group'],0,$OTLcutoffpos);

	if (isset($cOTLdata['GPOSinfo']) && $cOTLdata['GPOSinfo']) {
		foreach($cOTLdata['GPOSinfo'] AS $k => $val) {
			if ($k >= $OTLrestartpos) {
				$newOTLdata['GPOSinfo'][($k - $OTLrestartpos)] = $val;
			}
			if ($k >= $OTLcutoffpos) {
				unset($cOTLdata['GPOSinfo'][$k]);
				//$cOTLdata['GPOSinfo'][$k] = array();
			}
		}
	}
	if (isset($cOTLdata['char_data'])) {
		$newOTLdata['char_data'] = array_slice($cOTLdata['char_data'], $OTLrestartpos);
		array_splice($cOTLdata['char_da