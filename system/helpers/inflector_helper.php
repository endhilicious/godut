Glyphs = array(); }


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


							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['SubstCount'];$b++) {
								$lup = $Lookup[$i]['Subtable'][$c]['SubstLookupRecord'][$b]['LookupListIndex'];
								$seqIndex = $Lookup[$i]['Subtable'][$c]['SubstLookupRecord'][$b]['SequenceIndex'];


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
												$exL .=  $exampleI[$ip].'&#x200d;';
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
										$html .= $this->_getGSUBarray($Lookup, $lul2, $scripttag, 2, $inputGlyphs[$seqIndex], $exB, $exL);

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
	// mPDF 5.7.1
	function _checkGSUBignore($flag, $glyph, $MarkFilteringSet) {
		$ignore = false;
		// Flag & 0x0008 = Ignore Marks
		if ((($flag & 0x0008) == 0x0008) && strpos($this->GlyphClassMarks,$glyph)) { $ignore = true; }
		if ((($flag & 0x0004) == 0x0004) && strpos($this->GlyphClassLigatures,$glyph)) { $ignore = true; }
		if ((($flag & 0x0002) == 0x0002) && strpos($this->GlyphClassBases,$glyph)) { $ignore = true; }
		// Flag & 0xFF?? = MarkAttachmentType
		if (($flag & 0xFF00) && strpos($this->MarkAttachmentType[($flag >> 8)],$glyph)) { $ignore = true; }
		// Flag & 0x0010 = UseMarkFilteringSet
		if (($flag & 0x0010) && strpos($this->MarkGlyphSets[$MarkFilteringSet],$glyph)) { $ignore = true; }
		return $ignore;
	}

	function _getGSUBignoreString($flag, $MarkFilteringSet) {
		// If ignoreFlag set, combine all ignore glyphs into -> "((?:(?: FBA1| FBA2| FBA3))*)"
		// else "()"
		// for Input - set on secondary Lookup table if in Context, and set Backtrack and Lookahead on Context Lookup
		$str = "";
		$ignoreflag = 0;

		// Flag & 0xFF?? = MarkAttachmentType
		if ($flag & 0xFF00) {
			$MarkAttachmentType = $flag >> 8;
			$ignoreflag = $flag; 
			//$str = $this->MarkAttachmentType[$MarkAttachmentType]; 
			$str = "MarkAttachmentType[".$MarkAttachmentType."] "; 
		}

		// Flag & 0x0010 = UseMarkFilteringSet
		if ($flag & 0x0010) {
			die("This font ".$this->fontkey." contains MarkGlyphSets"); 
			$str = "Mark Glyph Set: "; 
			$str .= $this->MarkGlyphSets[$MarkFilteringSet]; 
		}

		// If Ignore Marks set, supercedes any above
		// Flag & 0x0008 = Ignore Marks
		if (($flag & 0x0008) == 0x0008) { 
			$ignoreflag = 8; 
			//$str = $this->GlyphClassMarks; 
			$str = "Mark Glyphs "; 
		}

		// Flag & 0x0004 = Ignore Ligatures  
		if (($flag & 0x0004) == 0x0004) {
			$ignoreflag += 4; 
			if ($str) { $str .= "|"; }
			//$str .= $this->GlyphClassLigatures; 
			$str .= "Ligature Glyphs "; 
		}
		// Flag & 0x0002 = Ignore BaseGlyphs  
		if (($flag & 0x0002) == 0x0002) {
			$ignoreflag += 2; 
			if ($str) { $str .= "|"; }
			//$str .= $this->GlyphClassBases; 
			$str .= "Base Glyphs "; 
		}
		if ($str) { 
			return $str;
		}
		else return "";
	}

	// GSUB Patterns

/*
       BACKTRACK                        INPUT                   LOOKAHEAD
==================================  ==================  ==================================
(FEEB|FEEC)(ign) ¦(FD12|FD13)(ign) ¦(0612)¦(ign) (0613)¦(ign) (FD12|FD13)¦(ign) (FEEB|FEEC)
----------------  ----------------  -----  ------------  ---------------   ---------------
  Backtrack 1       Backtrack 2     Input 1   Input 2       Lookahead 1      Lookahead 2
--------   ---    ---------  ---    ----   ---   ----   ---   ---------   ---    -------
    \${1}  \${2}     \${3}   \${4}                      \${5+}  \${6+}    \${7+}  \${8+}

          nBacktrack = 2               nInput = 2                 nLookahead = 2

        nBsubs = 2xnBack          nIsubs = (nBsubs+)    nLsubs = (nBsubs+nIsubs+) 2xnLookahead
        "\${1}\${2} "                 (nInput*2)-1               "\${5+} \${6+}"
                                        "REPL"

¦\${1}\${2} ¦\${3}\${4} ¦REPL¦\${5+} \${6+}¦\${7+} \${8+}¦


                      INPUT nInput = 5
============================================================  
¦(0612)¦(ign) (0613)¦(ign) (0614)¦(ign) (0615)¦(ign) (0615)¦
\${1}  \${2}  \${3}  \${4} \${5} \${6}  \${7} \${8}  \${9} (All backreference numbers are + nBsubs)
-----  ------------ ------------ ------------ ------------
Input 1   Input 2      Input 3      Input 4      Input 5

A======  SequenceIndex=1 ; Lookup match nGlyphs=1
B===================  SequenceIndex=1 ; Lookup match nGlyphs=2
C===============================  SequenceIndex=1 ; Lookup match nGlyphs=3
        D=======================  SequenceIndex=2 ; Lookup match nGlyphs=2
        E=====================================  SequenceIndex=2 ; Lookup match nGlyphs=3
              