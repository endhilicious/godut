level'] ) {
					if ($chardata[$j]['type'] == UCDN::BIDI_CLASS_EN) {
						$chardata[$i]['type'] = UCDN::BIDI_CLASS_EN;
						break;
					}
					else if ($chardata[$j]['type'] != UCDN::BIDI_CLASS_ET) { break; }
					++$j;
				}
			}
		}
	}

	// W6. Otherwise, separators and terminators change to Other Neutral.
	for ($i=0; $i < $numchars; ++$i) {
		if (($chardata[$i]['type'] == UCDN::BIDI_CLASS_ET) || ($chardata[$i]['type'] == UCDN::BIDI_CLASS_ES) || ($chardata[$i]['type'] == UCDN::BIDI_CLASS_CS)) {
			$chardata[$i]['type'] = UCDN::BIDI_CLASS_ON;
		}
	}

	//W7. Search backward from each instance of a European number until the first strong type (R, L, or sor) is found. If an L is found, then change the type of the European number to L.
	for ($i=0; $i < $numchars; ++$i) {
		if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_EN) {
			if ($i==0) {	// Start of Level run
				if ($chardata[$i]['sor']==UCDN::BIDI_CLASS_L) $chardata[$i]['type'] = $chardata[$i]['sor'];
			}
			else {
				for ($j=$i-1; $j >= 0; $j--) {
					if ($chardata[$j]['level'] != $chardata[$i]['level']) {	// Level run boundary 
						if ($chardata[$j+1]['sor']==UCDN::BIDI_CLASS_L) $chardata[$i]['type'] = $chardata[$j+1]['sor'];
						break;
					}
					else if ($chardata[$j]['type'] == UCDN::BIDI_CLASS_L) {
						$chardata[$i]['type'] = UCDN::BIDI_CLASS_L;
						break;
					}
					else if ($chardata[$j]['type'] == UCDN::BIDI_CLASS_R) {
						break;
					}
				}
			}
		}
	}

	// N1. A sequence of neutrals takes the direction of the surrounding strong text if the text on both sides has the same direction. European and Arabic numbers act as if they were R in terms of their influence on neutrals. Start-of-level-run (sor) and end-of-level-run (eor) are used at level run boundaries.
	for ($i=0; $i < $numchars; ++$i) {
		if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_ON || $chardata[$i]['type'] == UCDN::BIDI_CLASS_WS) {
			$left = -1;
			// LEFT
			if ($i==0) { 	// first char 
				$left = $chardata[($i)]['sor'];
			}
			else if ($chardata[($i-1)]['level'] != $chardata[($i)]['level']) { 	// run boundary
				$left = $chardata[($i)]['sor'];
			}
			else if ($chardata[($i-1)]['type'] == UCDN::BIDI_CLASS_L) {
				$left = UCDN::BIDI_CLASS_L;
			}
			else if ($chardata[($i-1)]['type'] == UCDN::BIDI_CLASS_R || $chardata[($i-1)]['type'] == UCDN::BIDI_CLASS_EN || $chardata[($i-1)]['type'] == UCDN::BIDI_CLASS_AN) {
				$left = UCDN::BIDI_CLASS_R;
			}
			// RIGHT
			$right = -1;
			$j=$i;
			// move to the right of any following neutrals OR hit a run boundary
			while(($chardata[$j]['type'] == UCDN::BIDI_CLASS_ON || $chardata[$j]['type'] == UCDN::BIDI_CLASS_WS) && $j<=($numchars-1)) { 
				if ($j==($numchars-1)) { 	// last char
					$right = $chardata[($j)]['eor'];
					break;
				}
				else if ($chardata[($j+1)]['level'] != $chardata[($j)]['level']) { 	// run boundary
					$right = $chardata[($j)]['eor'];
					break;
				}
				else if ($chardata[($j+1)]['type'] == UCDN::BIDI_CLASS_L) {
					$right = UCDN::BIDI_CLASS_L;
					break;
				}
				else if ($chardata[($j+1)]['type'] == UCDN::BIDI_CLASS_R || $chardata[($j+1)]['type'] == UCDN::BIDI_CLASS_EN || $chardata[($j+1)]['type'] == UCDN::BIDI_CLASS_AN) {
					$right = UCDN::BIDI_CLASS_R;
					break;
				}
				$j++; 
			}
			if ($left > -1 && $left==$right) {
				$chardata[$i]['orig_type'] = $chardata[$i]['type'];	// Need to store the original 'WS' for reference in L1 below
				$chardata[$i]['type'] = $left;
			}
		}
	}

	// N2. Any remaining neutrals take the embedding direction
	for ($i=0; $i < $numchars; ++$i) {
		if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_ON || $chardata[$i]['type'] == UCDN::BIDI_CLASS_WS) {
			$chardata[$i]['type'] = ($chardata[$i]['level'] % 2) ?  UCDN::BIDI_CLASS_R : UCDN::BIDI_CLASS_L;
			$chardata[$i]['orig_type'] = $chardata[$i]['type'];	// Need to store the original 'WS' for reference in L1 below
		}
	}

	// I1. For all characters with an even (left-to-right) embedding direction, those of type R go up one level and those of type AN or EN go up two levels.
	// I2. For all characters with an odd (right-to-left) embedding direction, those of type L, EN or AN go up one level.
	for ($i=0; $i < $numchars; ++$i) {
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
		$maxlevel = max($chardata[$i]['level'],$maxlevel);
	}

// NB
//	Separate into lines at this point************
//

	// L1. On each line, reset the embedding level of the following characters to the paragraph embedding level:
	//	1. Segment separators (Tab) 'S',
	//	2. Paragraph separators 'B',
	//	3. Any sequence of whitespace characters 'WS' preceding a segment separator or paragraph separator, and
	//	4. Any sequence of whitespace characters 'WS' at the end of the line.
	//	The types of characters used here are the original types, not those modified by the previous phase cf N1 and N2*******
	//	Because a Paragraph Separator breaks lines, there will be at most one per line, at the end of that line.

	for ($i=($numchars-1); $i>0; $i--) {
		if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_WS || (isset($chardata[$i]['orig_type']) && $chardata[$i]['orig_type'] == UCDN::BIDI_CLASS_WS)) {
				$chardata[$i]['level'] = $pel;
		}
		else { break; }
	}


	// L2. From the highest level found in the text to the lowest odd level on each line, including intermediate levels not actually present in the text, reverse any contiguous sequence of characters that are at that level or higher.
	for ($j=$maxlevel; $j > 0; $j--) {
		$ordarray = array();
		$revarr = array();
		$onlevel = false;
		for ($i=0; $i < $numchars; ++$i) {
			if ($chardata[$i]['level'] >= $j) {
				$onlevel = true;

				// L4. A character is depicted by a mirrored glyph if and only if (a) the resolved directionality of that character is R, and (b) the Bidi_Mirrored property value of that character is true. 
				if (isset(UCDN::$mirror_pairs[$chardata[$i]['char']]) && $chardata[$i]['type']==UCDN::BIDI_CLASS_R) {
					$chardata[$i]['char'] = UCDN::$mirror_pairs[$chardata[$i]['char']];
				}

				$revarr[] = $chardata[$i];
			}
			else {
				if ($onlevel) {
					$revarr = array_reverse($revarr);
					$ordarray = array_merge($ordarray, $revarr);
					$revarr = Array();
					$onlevel = false;
				}
				$ordarray[] = $chardata[$i];
			}
		}
		if ($onlevel) {
			$revarr = array_reverse($revarr);
			$ordarray = array_merge($ordarray, $revarr);
		}
		$chardata = $ordarray;
 	}

	$group = '';
	$e = '';
	$GPOS = array();
	$cctr = 0;
	$rtl_content = 0x0;
	foreach ($chardata as $cd) {
		$e.=code2utf($cd['char']);
		$group .= $cd['group'];
		if ($useGPOS && is_array($cd['GPOSinfo'])) {
			$GPOS[$cctr] = $cd['GPOSinfo'];
			$GPOS[$cctr]['wDir'] = ($cd['level'] % 2) ? 'RTL' : 'LTR';
		}
		if($cd['type']==UCDN::BIDI_CLASS_L) { $rtl_content |= 1; }
		else if($cd['type']==UCDN::BIDI_CLASS_R) { $rtl_content |= 2; }
		$cctr++;
	}


	$chunkOTLdata['group'] = $group ;
	if ($useGPOS) {
		$chunkOTLdata['GPOSinfo'] = $GPOS;
	}

	return array($e,$rtl_content);
}

// **********************************************************************************************
// The following versions for BidiSort work on amalgamated chunks to process the whole paragraph
// Firstly set the level in the OTLdata - called from fn printbuffer() [_bidiPrepare]
// Secondly re-order - called from fn writeFlowingBlock and FinishFlowingBlock, when already divided into lines. [_bidiReorder]
// **********************************************************************************************

function _bidiPrepare(&$para, $dir) {

	// Set the initial paragraph embedding level
	$pel = 0;	// paragraph embedding level
	if ($dir == 'rtl') { $pel = 1; } 

	// X1. Begin by setting the current embedding level to the paragraph embedding level. Set the directional override status to neutral. 
	// Current Embedding Level
	$cel = $pel;
	// directional override status (-1 is Neutral)
	$dos = -1;
	$remember = array();
	$controlchars = false;
	$strongrtl = false;
	$diid = 0;	// direction isolate ID
	$dictr = 0;	// direction isolate counter

	// Process each character iteratively, applying rules X2 through X9. Only embedding levels f