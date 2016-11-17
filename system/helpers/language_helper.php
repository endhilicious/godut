str .= "".$inputGlyphs[($i)].""; }
		}
		return $str;
	}

	function _makeGSUBinputMatch($inputGlyphs, $ignore) {
		// $ignore = "((?:(?: FBA1| FBA2| FBA3))*)" or "()"
		// Returns e.g. ¦(0612)¦(ignore) (0613)¦(ignore) (0614)¦
		// $inputGlyphs = array of glyphs(glyphstrings) making up Input sequence in Context
		// $lookupGlyphs = array of glyphs making up Lookup Input sequence - if applicable
		$str = "";
		for($i=1;$i<=count($inputGlyphs);$i++) {
			if ($i>1) { $str .= $ignore." "; }
			$str .= "".$inputGlyphs[($i-1)]."";
		}
		return $str;
	}

	function _makeGSUBbacktrackMatch($backtrackGlyphs, $ignore) {
		// $ignore = "((?:(?: FBA1| FBA2| FBA3))*)" or "()"
		// Returns e.g. ¦(FEEB|FEEC)(ignore) ¦(FD12|FD13)(ignore) ¦
		// $backtrackGlyphs = array of glyphstrings making up Backtrack sequence
		// 3  2  1  0
		// each item being e.g. E0AD|E0AF|F1FD
		$str = "";
		for($i=(count($backtrackGlyphs)-1);$i>=0;$i--) {
			$str .= "".$backtrackGlyphs[$i]." ".$ignore." ";
		}
		return $str;
	}

	function _makeGSUBlookaheadMatch($lookaheadGlyphs, $ignore) {
		// $ignore = "((?:(?: FBA1| FBA2| FBA3))*)" or "()"
		// Returns e.g. ¦(ignore) (FD12|FD13)¦(ignore) (FEEB|FEEC)¦
		// $lookaheadGlyphs = array of glyphstrings making up Lookahead sequence
		// 0  1  2  3
		// each item being e.g. E0AD|E0AF|F1FD
		$str = "";
		for($i=0;$i<count($lookaheadGlyphs);$i++) {
			$str .= $ignore." ".$lookaheadGlyphs[$i]."";
		}
		return $str;
	}



	function _makeGSUBinputReplacement($nInput, $REPL, $ignore, $nBsubs, $mLen, $seqIndex) {
		// Returns e.g. "REPL\${6}\${8}" or "\${1}\${2} \${3} REPL\${4}\${6}\${8} \${9}"
		// $nInput	nGlyphs in the Primary Input sequence
		// $REPL 	replacement glyphs from secondary lookup
		// $ignore = "((?:(?: FBA1| FBA2| FBA3))*)" or "()"
		// $nBsubs	Number of Backtrack substitutions (= 2x Number of Backtrack glyphs)
		// $mLen 	nGlyphs in the secondary Lookup match - if no secondary lookup, should=$nInput
		// $seqIndex	Sequence Index to apply the secondary match
		if ($ignore=="()") { $ign = false; }
		else { $ign = true; }
		$str = "";
		if ($nInput == 1) { $str = $REPL; }
		else if ($nInput>1) {
			if ($mLen==$nInput) {	// whole string replaced
				$str = $REPL; 
				if ($ign) {
					// for every nInput over 1, add another replacement backreference, to move IGNORES after replacement
					for($x=2;$x<=$nInput;$x++) {
						$str .= '\\'.($nBsubs+(2*($x-1)));
					}
				}
			}
			else {	// if only part of string replaced:
				for($x=1;$x<($seqIndex+1);$x++) {
				