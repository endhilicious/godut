tus. Reset the current level to this new level, and reset the override status to neutral.
			//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
			$next_level = $cel + 2 - ($cel % 2);
			if ( $next_level < 62 ) {
				$remember[] = array('num' => 8234, 'cel' => $cel, 'dos' => $dos);
				$cel = $next_level;
				$dos = -1;
			}
		}
		else if ($chunkOTLdata['char_data'][$i]['uni'] == 8238) { // RLO
			// X4. With each RLO, compute the least greater odd embedding level.
			//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to right-to-left.
			//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
			$next_level = $cel + ($cel % 2) + 1;
			if ($next_level < 62) {
				$remember[] = array('num' => 8238, 'cel' => $cel, 'dos' => $dos);
				$cel = $next_level;
				$dos = UCDN::BIDI_CLASS_R;
			}
		}
		else if ($chunkOTLdata['char_data'][$i]['uni'] == 8237) {	// LRO
			// X5. With each LRO, compute the least greater even embedding level.
			//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to left-to-right.
			//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
			$next_level = $cel + 2 - ($cel % 2);
			if ( $next_level < 62 ) {
				$remember[] = array('num' => 8237, 'cel' => $cel, 'dos' => $dos);
				$cel = $next_level;
				$dos = UCDN::BIDI_CLASS_L;
			}
		}
		else if ($chunkOTLdata['char_data'][$i]['uni'] == 8236) {	// PDF
			// X7. With each PDF, determine the matching embedding or override code. If there was a valid matching code, restore (pop) the last remembered (pushed) embedding level and directional override.
			if (count($remember)) {
				$last = count($remember ) - 1;
				if (($remember[$last]['num'] == 8235) || ($remember[$last]['num'] == 8234) || ($remember[$last]['num'] == 8238) || 
					($remember[$last]['num'] == 8237)) {
					$match = array_pop($remember);
					$cel = $match['cel'];
					$dos = $match['dos'];
				}
			}
		}
		else if ($chunkOTLdata['char_data'][$i]['uni'] == 10) {	// NEW LINE
			// Reset to start values
			$cel = $pel;
			$dos = -1;
			$remember = array();
		}
		else {
			// X6. For all types besides RLE, LRE, RLO, LRO, and PDF:
			//	a. Set the level of the current character to the current embedding level.
			//	b. When the directional override status is not neutral, reset the current character type to directional override status.
			if ($dos != -1) { $chardir = $dos; } 
			else {
				$chardir = $chunkOTLdata['char_data'][$i]['bidi_class'];
			}
			// stores string characters and other information
			if (isset($chunkOTLdata['GPOSinfo'][$i])) { $gpos = $chunkOTLdata['GPOSinfo'][$i]; }
			else $gpos = '';
			$chardata[] = array('char' => $chunkOTLdata['char_data'][$i]['uni'], 'level' => $cel, 'type' => $chardir, 'group' => $chunkOTLdata['group']{$i}, 'GPOSinfo' => $gpos);
		}
	}

	$numchars = count($chardata);

	// X8. All explicit directional embeddings and overrides are completely terminated at the end of each paragraph.
	// Paragraph separators are not included in the embedding.
	// X9. Remove all RLE, LRE, RLO, LRO, and PDF codes.
	// This is effectively done by only saving other codes to chardata

	// X10. Determine the start-of-sequence (sor) and end-of-sequence (eor) types, either L or R, for each isolating run sequence. These depend on the higher of the two levels on either side of the sequence boundary:
	// For sor, compare the level of the first character in the sequence with the level of the character preceding it in the paragraph or if there is none, with the paragraph embedding level.
	// For eor, compare the level of the last character in the sequence with the level of the character following it in the paragraph or if there is none, with the paragraph embedding level.
	// If the higher level is odd, the sor or eor is R; otherwise, it is L.

	$prelevel = $pel;
	$postlevel = $pel;
	$cel = $prelevel;	// current embedding level
	for ($i=0; $i < $numchars; ++$i) {
		$level = $chardata[$i]['level'];
		if ($i==0) { $left = $prelevel; }
		else { $left = $chardata[$i-1]['level']; }
		if ($i==($numchars-1)) { $right = $postlevel; }
		else { $right = $chardata[$i+1]['level']; }
		$chardata[$i]['sor'] = max($left, $level) % 2 ? UCDN::BIDI_CLASS_R : UCDN::BIDI_CLASS_L;
		$chardata[$i]['eor'] = max($right, $level) % 2 ? UCDN::BIDI_CLASS_R : UCDN::BIDI_CLASS_L;
	}



	// 3.3.3 Resolving Weak Types
	// Weak types are now resolved one level run at a time. At level run boundaries where the type of the character on the other side of the boundary is required, the type assigned to sor or eor is used.
	// Nonspacing marks are now resolved based on the previous characters.

	// W1. Examine each nonspacing mark (NSM) in the level run, and change the type of the NSM to the type of the previous character. If the NSM is at the start of the level run, it will get the type of sor.
	for ($i=0; $i < $numchars; ++$i) {
		if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_NSM) {
			if ($i==0 || $chardata[$i]['level']!=$chardata[$i-1]['level']) {
				$chardata[$i]['type'] = $chardata[$i]['sor'];
			}
			else {
				$chardata[$i]['type'] = $chardata[($i-1)]['type'];
			}
		}
	}

	// W2. Search backward from each instance of a European number until the first strong type (R, L, AL, or sor) is found. If an AL is found, change the type of the European number to Arabic number.
	$prevlevel = -1;
	$levcount = 0;
	for ($i=0; $i < $numchars; ++$i) {
		if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_EN) {
			$found = false;
			for ($j=$levcount; $j >= 0; $j--) {
				if ($chardata[$j]['type'] == UCDN::BIDI_CLASS_AL) { $chardata[$i]['type'] = UCDN::BIDI_CLASS_AN; $found = true; break; }
				else if (($chardata[$j]['type'] == UCDN::BIDI_CLASS_L) || ($chardata[$j]['type'] == UCDN::BIDI_CLASS_R)) { $found = true; break; }
			}
		}
		if ($chardata[$i]['level'] != $prevlevel) { $levcount = 0; } 
		else { ++$levcount; }
		$prevlevel = $chardata[$i]['level'];
	}

	// W3. Change all ALs to R.
	for ($i=0; $i < $numchars; ++$i) {
 		if ($chardata