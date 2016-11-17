ectional override.
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
		  else if ($chunkOTLdata['char_data'][$i]['uni'] == 8294 || $chunkOTLdata['char_data'][$i]['uni'] == 8295 ||
			$chunkOTLdata['char_data'][$i]['uni'] == 8296) {	// LRI // RLI // FSI
			// X5a. With each RLI:
			// X5b. With each LRI:
			// X5c. With each FSI, apply rules P2 and P3 for First Strong character
			//	Set the RLI/LRI/FSI embedding level to the embedding level of the last entry on the directional status stack.
			if ($dos != -1) { $chardir = $dos; } 
			else { $chardir = $chunkOTLdata['char_data'][$i]['bidi_class']; }
			$chunkOTLdata['char_data'][$i]['level'] = $cel;
			$chunkOTLdata['char_data'][$i]['type'] = $chardir;
			$chunkOTLdata['char_data'][$i]['diid'] = $diid;

			$fsi = '';
			// X5c. With each FSI, apply rules P2 and P3 within the isolate run for First Strong character
			if ($chunkOTLdata['char_data'][$i]['uni'] == 8296) {	// FSI
				$lvl = 0;
				$nc2 = $nc;
				$i2 = $i;
				while (!($nc2==($numchunks-1) && $i2==((count($para[$nc2][18]['char_data']))-1))) { 	// while not at end of last chunk
					$i2++;
					if ($i2 >= count($para[$nc2][18]['char_data'])) { 
						$nc2++;
						$i2 = 0;
					}
					if ($lvl > 0) { continue; }
					if ($para[$nc2][18]['char_data'][$i2]['uni'] == 8294 || $para[$nc2][18]['char_data'][$i2]['uni'] == 8295 || $para[$nc2][18]['char_data'][$i2]['uni'] == 8296) {
						$lvl++;
						continue;
					}
					if ($para[$nc2][18]['char_data'][$i2]['uni'] == 8297) {
						$lvl--;
						if ($lvl < 0) { break; }
					}
					if ($para[$nc2][18]['char_data'][$i2]['bidi_class'] === UCDN::BIDI_CLASS_L || $para[$nc2][18]['char_data'][$i2]['bidi_class'] == UCDN::BIDI_CLASS_AL || $para[$nc2][18]['char_data'][$i2]['bidi_class'] === UCDN::BIDI_CLASS_R) {
						$fsi = $para[$nc2][18]['char_data'][$i2]['bidi_class'];
						break;
					}
				}
				// if fsi not found, fsi is same as paragraph embedding level
				if (!$fsi && $fsi!==0) {
					if ($pel==1) { $fsi = UCDN::BIDI_CLASS_R ; }
					else { $fsi = UCDN::BIDI_CLASS_L ; }
				}
			}

			if ($chunkOTLdata['char_data'][$i]['uni'] == 8294 || $fsi === UCDN::BIDI_CLASS_L ) {	// LRI or FSI-L
			//	Compute the least even embedding level greater than the embedding level of the last entry on the directional status stack.
				$next_level = $cel + 2 - ($cel % 2);
			}
			else if ($chunkOTLdata['char_data'][$i]['uni'] == 8295 || $fsi == UCDN::BIDI_CLASS_R || $fsi == UCDN::BIDI_CLASS_AL ) {	// RLI or FSI-R
			//	Compute the least odd embedding level greater than the embedding level of the last entry on the directional status stack.
				$next_level = $cel + ($cel % 2) + 1;
			}


			//	Increment the isolate count by one, and push an entry consisting of the new embedding level,
			//	neutral directional override status, and true directional isolate status onto the directional status stack.
			$remember[] = array('num' => $chunkOTLdata['char_data'][$i]['uni'], 'cel' => $cel, 'dos' => $dos, 'diid' => $diid);
			$cel = $next_level;
			$dos = -1;
			$diid = ++$dictr;	// Set new direction isolate ID after incrementing direction isolate counter

			$controlchars = true;
		  }
		  else if ($chunkOTLdata['char_data'][$i]['uni'] == 8297) {	// PDI
			// X6a. With each PDI, perform the following steps:
			//	Pop the last entry from the directional status stack and decrement the isolate count by one.
			while (count($remember)) {
				$last = count($remember ) - 1;
				if (($remember[$last]['num'] == 8294) || ($remember[$last]['num'] == 8295) || ($remember[$last]['num'] == 8296)) {
					$match = array_pop($remember);
					$cel = $match['cel'];
					$dos = $match['dos'];
					$diid = $match['diid'];
					break;
				}
				// End/close any open embedding states not explicitly closed during the isolate
				else if (($remember[$last]['num'] == 8235) || ($remember[$last]['num'] == 8234) || ($remember[$last]['num'] == 8238) ||
					($remember[$last]['num'] == 8237)) {
					$match = array_pop($remember);
				}
			}
			//	In all cases, set the PDI’s level to the embedding level of the last entry on the directional status stack left after the steps above.
			//	NB The level assigned to an isolate initiator is always the same as that assigned to the matching PDI.
			if ($dos != -1) { $chardir = $dos; } 
			else { $chardir = $chunkOTLdata['char_data'][$i]['bidi_class']; }
			$chunkOTLdata['char_data'][$i]['level'] = $cel;
			$chunkOTLdata['char_data'][$i]['type'] = $chardir;
			$chunkOTLdata['char_data'][$i]['diid'] = $diid;
			$controlchars = true;
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
				if ($chardir == UCDN::BIDI_CLASS_R || $chardir == UCDN::BIDI_CLASS_AL) { $strongrtl = true; }
			}
			$chunkOTLdata['char_data'][$i]['level'] = $cel;
			$chunkOTLdata['char_data'][$i]['type'] = $chardir;
			$chunkOTLdata['char_data'][$i]['diid'] = $diid;
		  }
		}
		// X8. All explicit directional embeddings and overrides are completely terminated at the end of each paragraph.
		// Paragraph separators are not included in the embedding.
		// X9. Remove all RLE, LRE, RLO, LRO, and PDF codes.
		if ($controlchars) {
			$this->removeChar($para[$nc][0], $para[$nc][18], "\xe2\x80\xaa");
			$this->removeChar($para[$nc][0], $para[$nc][18], "\xe2\x80\xab");
			$this->removeChar($para[$nc][0], $para[$nc][18], "\xe2\x80\xac");
			$this->removeChar($para[$nc][0], $para[$nc][18], "\xe2\x80\xad");
			$this->removeChar($para[$nc][0], $para[$nc][18], "\xe2\x80\xae");
			preg_replace("/\x{202a}-\x{202e}/u", '', $para[$nc][0]);
		}
	}

	// Remove any blank chunks made by removing directional codes
	$numchunks = count($para);
	for ($nc=($numchunks-1);$nc>=0;$nc--) {
		if (count($para[$nc][18]['char_data'])==0) { array_splice($para, $nc, 1); }
	}
	if ($dir != 'rtl' && !$strongrtl && !$controlchars) { return; }

	$numchunks = count($para);

	// X10. Determine the start-of-sequence (sor) and end-of-sequence (eor) types, either L or R, for each isolating run sequence. These depend on the higher of the two levels on either side of the sequence boundary:
	// For sor, compare the level of the first character in the sequence with the level of the character preceding it in the paragraph or if there is none, with the paragraph embedding level.
	// For eor, compare the level of the last character in the sequence with the level of the character following it in the paragraph or if there is none, with the paragraph embedding level.
	// If the higher level is odd, the sor or eor is R; otherwise, it is L.

	for ($ir=0; $ir<=$dictr;$ir++) {
		$prelevel = $pel;	
		$postlevel = $pel;
		$firstchar = true;
		for ($nc=0;$nc<$numchunks;$nc++) {
			$chardata =& $para[$nc][18]['char_data'];
			$numchars = count($chardata);
			for ($i=0; $i < $numchars; ++$i) {
				if (!isset($chardata[$i]['diid']) || $chardata[$i]['diid']!=$ir) { continue; }	// Ignore characters in a different isolate run
				$right = $postlevel;
				$nc2 = $nc;
				$i2 = $i;
				while (!($nc2==($numchunks-1) && $i2==((count($para[$nc2][18]['char_data']))-1))) { 	// while not at end of last chunk
					$i2++;
					if ($i2 >= count($para[$nc2][18]['char_data'])) { 
						$nc2++;
						$i2 = 0;
					}

					if (isset($para[$nc2][18]['char_data'][$i2]['diid']) && $para[$nc2][18]['char_data'][$i2]['diid']==$ir) { $right = $para[$nc2][18]['char_data'][$i2]['level']; break; }
				}

				$level = $chardata[$i]['level'];
				if ($firstchar || $level!=$prelevel) {
					$chardata[$i]['sor'] = max($prelevel, $level) % 2 ? UCDN::BIDI_CLASS_R : UCDN::BIDI_CLASS_L;
				}
				if (($nc==($numchunks-1) && $i==($numchars-1)) || $level != $right) {
					$chardata[$i]['eor'] = max($right, $level) % 2 ? UCDN::BIDI_CLASS_R : UCDN::BIDI_CLASS_L;
				}
				$prelevel = $level;
				$firstchar = false;
			}
		}
	}


	// 3.3.3 Resolving Weak Types
	// Weak types are now resolved one level run at a time. At level run boundaries where the type of the character on the other side of the boundary is required, the type assigned to sor or eor is used.
	// Nonspacing marks are now resolved based on the previous characters.

	// W1. Examine each nonspacing mark (NSM) in the level run, and change the type of the NSM to the type of the previous character. If the NSM is at the start of the level run, it will get the type of sor.
	for ($ir=0; $ir<=$dictr;$ir++) {
		$prevtype = 0;
		for ($nc=0;$nc<$numchunks;$nc++) {
			$chardata =& $para[$nc][18]['char_data'];
			$numchars = count($chardata);
			for ($i=0; $i < $numchars; ++$i) {
				if (!isset($chardata[$i]['diid']) || $chardata[$i]['diid']!=$ir) { continue; }	// Ignore characters in a different isolate run
				if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_NSM) {
					if (isset($chardata[$i]['sor'])) {
						$chardata[$i]['type'] = $chardata[$i]['sor'];
					}
					else {
						$chardata[$i]['type'] = $prevtype;
					}
				}
				$prevtype = $chardata[$i]['type'];
			}
		}
	}

	// W2. Search backward from each instance of a European number until the first strong type (R, L, AL or sor) is found. If an AL is found, change the type of the European number to Arabic number.
	for ($ir=0; $ir<=$dictr;$ir++) {
		$laststrongtype = -1;
		for ($nc=0;$nc<$numchunks;$nc++) {
			$chardata =& $para[$nc][18]['char_data'];
			$numchars = count($chardata);
			for ($i=0; $i < $numchars; ++$i) {
				if (!isset($chardata[$i]['diid']) || $chardata[$i]['diid']!=$ir) { continue; }	// Ignore characters in a different isolate run
				if (isset($chardata[$i]['sor'])) { $laststrongtype = $chardata[$i]['sor']; }
				if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_EN && $laststrongtype == UCDN::BIDI_CLASS_AL ) { 
					$chardata[$i]['type'] = UCDN::BIDI_CLASS_AN; 
				}
				if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_L || $chardata[$i]['type'] == UCDN::BIDI_CLASS_R || $chardata[$i]['type'] == UCDN::BIDI_CLASS_AL) { 
					$laststrongtype =  $chardata[$i]['type']; 
				}
			}
		}
	}


	// W3. Change all ALs to R.
	for ($nc=0;$nc<$numchunks;$nc++) {
		$chardata =& $para[$nc][18]['char_data'];
		$numchars = count($chardata);
		for ($i=0; $i < $numchars; ++$i) {
 			if (isset($chardata[$i]['type']) && $chardata[$i]['type'] == UCDN::BIDI_CLASS_AL) { $chardata[$i]['type'] = UCDN::BIDI_CLASS_R; }
		}
	}


	// W4. A single European separator between two European numbers changes to a European number. A single common separator between two numbers of the same type changes to that type.
	for ($ir=0; $ir<=$dictr;$ir++) {
		$prevtype = -1;
		$nexttype = -1;
		for ($nc=0;$nc<$numchunks;$nc++) {
			$chardata =& $para[$nc][18]['char_data'];
			$numchars = count($chardata);
			for ($i=0; $i < $numchars; ++$i) {
				if (!isset($chardata[$i]['diid']) || $chardata[$i]['diid']!=$ir) { continue; }	// Ignore characters in a different isolate run

				// Get next type
				$nexttype = -1; 
				$nc2 = $nc;
				$i2 = $i;
				while (!($nc2==($numchunks-1) && $i2==((count($para[$nc2][18]['char_data']))-1))) { 	// while not at end of last chunk
					$i2++;
					if ($i2 >= count($para[$nc2][18]['char_data'])) { 
						$nc2++;
						$i2 = 0;
					}

					if (isset($para[$nc2][18]['char_data'][$i2]['diid']) && $para[$nc2][18]['char_data'][$i2]['diid']==$ir) { $nexttype = $para[$nc2][18]['char_data'][$i2]['type']; break; }
				}

				if (!isset($chardata[$i]['sor']) && !isset($chardata[$i]['eor'])) {
					if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_ES && $prevtype == UCDN::BIDI_CLASS_EN && $nexttype == UCDN::BIDI_CLASS_EN) {
						$chardata[$i]['type'] = UCDN::BIDI_CLASS_EN;
					}
					else if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_CS && $prevtype == UCDN::BIDI_CLASS_EN && $nexttype == UCDN::BIDI_CLASS_EN) {
						$chardata[$i]['type'] = UCDN::BIDI_CLASS_EN;
					}
					else if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_CS && $prevtype == UCDN::BIDI_CLASS_AN && $nexttype == UCDN::BIDI_CLASS_AN) {
						$chardata[$i]['type'] = UCDN::BIDI_CLASS_AN;
					}
				}
				$prevtype = $chardata[$i]['type'];
			}
		}
	}

	// W5. A sequence of European terminators adjacent to European numbers changes to all European numbers.
	for ($ir=0; $ir<=$dictr;$ir++) {
		$prevtype = -1;
		$nexttype = -1;
		for ($nc=0;$nc<$numchunks;$nc++) {
			$chardata =& $para[$nc][18]['char_data'];
			$numchars = count($chardata);
			for ($i=0; $i < $numchars; ++$i) {
				if (!isset($chardata[$i]['diid']) || $chardata[$i]['diid']!=$ir) { continue; }	// Ignore characters in a different isolate run
				if (isset($chardata[$i]['sor'])) { $prevtype = $chardata[$i]['sor']; }

				if ($chardata[$i]['type'] == UCDN::BIDI_CLASS_ET) {
					if ($prevtype == UCDN::BIDI_CLASS_EN) {
						$chardata[$i]['type'] = UCDN::BIDI_CLASS_EN;
					}
					else if (!isset($chardata[$i]['eor'])) {
						$nexttype = -1;
						$nc2 = $nc;
						$i2 = $i;
						while (!($nc2==($numchunks-1) && $i2==((count($para[$nc2][18]['char_data']))-1))) { // while not at end of last chunk
							$i2++;
							if ($i2 >= count($para[$nc2][18]['char_data'])) { 
								$nc2++;
								$i2 = 0;
							}
							if ($para[$nc2][18]['char_data'][$i2]['diid']!=$ir) { continue; }
							$nexttype = $para[$nc2][18]['char_data'][$i2]['type']; 
							if (isset($para[$nc2][18]['char_data'][$i2]['sor'])) { break; }
							if ($nexttype == UCDN::BIDI_CLASS_EN) {
								$chardata[$i]['type'] = UCDN::BIDI_CLASS_EN;
								break;
							}
							else if ($nexttype != UCDN::BIDI_CLASS_ET) { break; }
						}
					}
				}
				$prevtype = $chardata[$i]['type'];
			}
		}
	}

	// W6. Otherwise, separators and terminators change to Other Neutral.
	for ($nc=0;$nc<$numchunks;$nc++) {
		$chardata =& $para[$nc][18]['char_data'];
		$numchars = count($chardata);
		for ($i=0; $i < $numchars; ++$i) {
			if (isset($chardata[$i]['type']) && (($chardata[$i]['type'] == UCDN::BIDI_CLASS_ET) || ($chardata[$i]['type'] == UCDN::BIDI_CLASS_ES) || ($chardata[$i]['type'] == UCDN::BIDI_CLASS_CS))) {
				$chardata[$i]['type'] = UCDN::BIDI_CLASS_ON;
			}
		}
	}

	//W7. Search backward from each instance of a European number until the first strong type (R, L, or sor) is found. If an L is found, then change the type of the European number to L.
	for ($ir=0; $ir<=$dictr;$ir++) {
		$laststrongtype = -1;
		for ($nc=0;$nc<$numchunks;$nc++) {
			$chardata =& $para[$nc][18]['char_data'];
			$numchars = count($chardata);
			for ($i=0; $i < $numchars; ++$i) {
				if (!isset($chardata[$i]['diid']) || $chardata[$i]['diid']!=$ir) { continue; }	// Ignore characters in a different isolate run
				if (isset($chardata[$i]['sor'])) { $laststrongtype = $chardata[$i]['sor']; }
				if (isset($chardata[$i]['type']) && $chardata[$i]['type'] == UCDN::BIDI_CLASS_EN && $laststrongtype == UCDN::BIDI_CLASS_L ) { 
					$chardata[$i]['type'] = UCDN::BIDI_CLASS_L; 
				}
				if (isset($chardata[$i]['type']) && ($chardata[$i]['type'] == UCDN::BIDI_CLASS_L || $chardata[$i]['type'] == UCDN::BIDI_CLASS_R || $chardata[$i]['type'] == UCDN::BIDI_CLASS_AL)) { 
					$laststrongtype =  $chardata[$i]['type']; 
				}
			}
		}
	}

	// N1. A sequence of neutrals takes the direction of the surrounding strong text if the text on both sides has the same direction. European and Arabic numbers act as if they were R in terms of their influence on neutrals. Start-of-level-run (sor) and end-of-level-run (eor) are u