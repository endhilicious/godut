ure tables
									$this->seek($Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRuleOffset'][$g]);

									$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$g]['GlyphCount'] = $this->read_ushort();
									$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$g]['SubstCount'] = $this->read_ushort();
									// "Input"::[GlyphCount - 1]::Array of input GlyphIDs-start with second glyph
									for ($l=1;$l<$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$g]['GlyphCount'];$l++) { 
										$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$g]['Input'][$l] = $this->read_ushort();
									}
									// "SubstLookupRecord"::[SubstCount]::Array of SubstLookupRecords-in design order
									for ($l=0;$l<$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$g]['SubstCount'];$l++) { 
										$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$g]['SubstLookupRecord'][$l]['SequenceIndex'] = $this->read_ushort();
										$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$g]['SubstLookupRecord'][$l]['LookupListIndex'] = $this->read_ushort();
									}

								}
							}

						}
						// Format 2: Class-based Context Glyph Substitution
						else if ($SubstFormat==2) {	
							$Lookup[$i]['Subtable'][$c]['CoverageTableOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							$Lookup[$i]['Subtable'][$c]['ClassDefOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							$Lookup[$i]['Subtable'][$c]['SubClassSetCnt'] = $this->read_ushort();
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['SubClassSetCnt'];$b++) {
								$offset = $this->read_ushort();
								if ($offset==0x0000) {
									$Lookup[$i]['Subtable'][$c]['SubClassSetOffset'][] = 0;
								}
								else {
									$Lookup[$i]['Subtable'][$c]['SubClassSetOffset'][] = $Lookup[$i]['Subtable'][$c]['Offset'] + $offset;
								}
							}
						}
						else { die("GPOS Lookup Type ".$Lookup[$i]['Type'].", Format ".$SubstFormat." not supported (ttfontsuni.php)."); }
					}

					// LookupType 6: Chaining Contextual Substitution Subtable
					else if ($Lookup[$i]['Type'] == 6) {
						// Format 1: Simple Chaining Context Glyph Substitution  p255
						if ($SubstFormat==1) {	
							$Lookup[$i]['Subtable'][$c]['CoverageTableOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							$Lookup[$i]['Subtable'][$c]['ChainSubRuleSetCount'] = $this->read_ushort();
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['ChainSubRuleSetCount'];$b++) {
								$Lookup[$i]['Subtable'][$c]['ChainSubRuleSetOffset'][] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							}
						}
						// Format 2: Class-based Chaining Context Glyph Substitution  p257
						else if ($SubstFormat==2) {	
							$Lookup[$i]['Subtable'][$c]['CoverageTableOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							$Lookup[$i]['Subtable'][$c]['BacktrackClassDefOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							$Lookup[$i]['Subtable'][$c]['InputClassDefOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							$Lookup[$i]['Subtable'][$c]['LookaheadClassDefOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							$Lookup[$i]['Subtable'][$c]['ChainSubClassSetCnt'] = $this->read_ushort();
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['ChainSubClassSetCnt'];$b++) {
								$offset = $this->read_ushort();
								if ($offset==0x0000) {
									$Lookup[$i]['Subtable'][$c]['ChainSubClassSetOffset'][] = $offset;
								}
								else {
									$Lookup[$i]['Subtable'][$c]['ChainSubClassSetOffset'][] = $Lookup[$i]['Subtable'][$c]['Offset'] + $offset;
								}
							}
						}
						// Format 3: Coverage-based Chaining Context Glyph Substitution  p259
						else if ($SubstFormat==3) {	
							$Lookup[$i]['Subtable'][$c]['BacktrackGlyphCount'] = $this->read_ushort();
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['BacktrackGlyphCount'];$b++) {
								$Lookup[$i]['Subtable'][$c]['CoverageBacktrack'][] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							}
							$Lookup[$i]['Subtable'][$c]['InputGlyphCount'] = $this->read_ushort();
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['InputGlyphCount'];$b++) {
								$Lookup[$i]['Subtable'][$c]['CoverageInput'][] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							}
							$Lookup[$i]['Subtable'][$c]['LookaheadGlyphCount'] = $this->read_ushort();
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['LookaheadGlyphCount'];$b++) {
								$Lookup[$i]['Subtable'][$c]['CoverageLookahead'][] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							}
							$Lookup[$i]['Subtable'][$c]['SubstCount'] = $this->read_ushort();
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['SubstCount'];$b++) {
								$Lookup[$i]['Subtable'][$c]['SubstLookupRecord'][$b]['SequenceIndex'] = $this->read_ushort();
								$Lookup[$i]['Subtable'][$c]['SubstLookupRecord'][$b]['LookupListIndex'] = $this->read_ushort();
/*
Substitution Lookup Record 
All contextual substitution subtables specify the substitution data in a Substitution Lookup Record (SubstLookupRecord). Each record contains a SequenceIndex, which indicates the position where the substitution will occur in the glyph sequence. In addition, a LookupListIndex identifies the lookup to be applied at the glyph position specified by the SequenceIndex. 
*/

							}
						}
					}
					else { die("Lookup Type ".$Lookup[$i]['Type']." not supported."); }
				}
			}
//print_r($Lookup); exit;




			//=====================================================================================
			// Process (2) Whole LookupList
			// Get Coverage tables and prepare preg_replace
			for ($i=0;$i<$LookupCount;$i++) {
				for ($c=0;$c<$Lookup[$i]['SubtableCount'] ;$c++) {
					$SubstFormat= $Lookup[$i]['Subtable'][$c]['Format'] ;

					// LookupType 1: Single Substitution Subtable 1 => 1
					if ($Lookup[$i]['Type'] == 1) {
						$this->seek($Lookup[$i]['Subtable'][$c]['CoverageTableOffset']);
						$glyphs = $this->_getCoverage(false);
						for ($g=0;$g<count($glyphs);$g++) {
							$replace = array();
							$substitute = array();
							$replace[] = unicode_hex($this->glyphToChar[$glyphs[$g]][0]);
							// Flag = Ignore
							if ($this->_checkGSUBignore($Lookup[$i]['Flag'], $replace[0], $Lookup[$i]['MarkFilteringSet'])) { continue; }
							if (isset($Lookup[$i]['Subtable'][$c]['DeltaGlyphID'])) {	// Format 1
								$substitute[] = unicode_hex($this->glyphToChar[($glyphs[$g]+$Lookup[$i]['Subtable'][$c]['DeltaGlyphID'])][0]);
							}
							else {	// Format 2
								$substitute[] = unicode_hex($this->glyphToChar[($Lookup[$i]['Subtable'][$c]['Glyphs'][$g])][0]);
							}
							$Lookup[$i]['Subtable'][$c]['subs'][] = array('Replace'=>$replace, 'substitute'=>$substitute);
						}
					}

					// LookupType 2: Multiple Substitution Subtable 1 => n
					else if ($Lookup[$i]['Type'] == 2) {
						$this->seek($Lookup[$i]['Subtable'][$c]['CoverageTableOffset']);
						$glyphs = $this->_getCoverage();
						for ($g=0;$g<count($glyphs);$g++) {
							$replace = array();
							$substitute = array();
							$replace[] = $glyphs[$g];
							// Flag = Ignore
							if ($this->_checkGSUBignore($Lookup[$i]['Flag'], $replace[0], $Lookup[$i]['MarkFilteringSet'])) { continue; }
if (!isset($Lookup[$i]['Subtable'][$c]['Sequences'][$g]['SubstituteGlyphID']) || count($Lookup[$i]['Subtable'][$c]['Sequences'][$g]['SubstituteGlyphID'])==0) { continue; }	// Illegal for GlyphCount to be 0; either error in font, or something has gone wrong - lets carry on for now!
							foreach($Lookup[$i]['Subtable'][$c]['Sequences'][$g]['SubstituteGlyphID'] AS $sub) {
								$substitute[] = unicode_hex($this->glyphToChar[$sub][0]);
							}
							$Lookup[$i]['Subtable'][$c]['subs'][] = array('Replace'=>$replace, 'substitute'=>$substitute);
						}
					}
					// LookupType 3: Alternate Forms 1 => 1 (only first alternate form is used)
					else if ($Lookup[$i]['Type'] == 3) {
						$this->seek($Lookup[$i]['Subtable'][$c]['CoverageTableOffset']);
						$glyphs = $this->_getCoverage();
						for ($g=0;$g<count($glyphs);$g++) {
							$replace = array();
							$substitute = array();
							$replace[] = $glyphs[$g];
							// Flag = Ignore
							if ($this->_checkGSUBignore($Lookup[$i]['Flag'], $replace[0], $Lookup[$i]['MarkFilteringSet'])) { continue; }

							for ($gl=0;$gl<$Lookup[$i]['Subtable'][$c]['AlternateSets'][$g]['GlyphCount'];$gl++) { 
								$gid = $Lookup[$i]['Subtable'][$c]['AlternateSets'][$g]['SubstituteGlyphID'][$gl];
								$substitute[] = unicode_hex($this->glyphToChar[$gid][0]);
							}

							//$gid = $Lookup[$i]['Subtable'][$c]['AlternateSets'][$g]['SubstituteGlyphID'][0];
							//$substitute[] = unicode_hex($this->glyphToChar[$gid][0]);

							$Lookup[$i]['Subtable'][$c]['subs'][] = array('Replace'=>$replace, 'substitute'=>$substitute);
						}
if ($i==166) {
	print_r($Lookup[$i]['Subtable']);
	exit;
}
					}
					// LookupType 4: Ligature Substitution Subtable n => 1
					else if ($Lookup[$i]['Type'] == 4) {
						$this->seek($Lookup[$i]['Subtable'][$c]['CoverageTableOffset']);
						$glyphs = $this->_getCoverage();
						$LigSetCount = $Lookup[$i]['Subtable'][$c]['LigSetCount'];
						for($s=0;$s<$LigSetCount;$s++) {
							for ($g=0;$g<$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['LigCount'];$g++) { 
								$replace = array();
								$substitute = array();
								$replace[] = $glyphs[$s];
								// Flag = Ignore
								if ($this->_checkGSUBignore($Lookup[$i]['Flag'], $replace[0], $Lookup[$i]['MarkFilteringSet'])) { continue; }
								for ($l=1;$l<$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Ligature'][$g]['CompCount'];$l++) { 
									$gid = $Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Ligature'][$g]['GlyphID'][$l];
									$rpl = unicode_hex($this->glyphToChar[$gid][0]);
									// Flag = Ignore
									if ($this->_checkGSUBignore($Lookup[$i]['Flag'], $rpl, $Lookup[$i]['MarkFilteringSet'])) { continue 2; }
									$replace[] = $rpl;
								}
								$gid = $Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Ligature'][$g]['LigGlyph'];
								$substitute[] = unicode_hex($this->glyphToChar[$gid][0]);
								$Lookup[$i]['Subtable'][$c]['subs'][] = array('Replace'=>$replace, 'substitute'=>$substitute, 'CompCount' => $Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Ligature'][$g]['CompCount']);
							}
						}
					}

					// LookupType 5: Contextual Substitution Subtable
					else if ($Lookup[$i]['Type'] == 5) {
						// Format 1: Context Substitution
						if ($SubstFormat==1) {
							$this->seek($Lookup[$i]['Subtable'][$c]['CoverageTableOffset']);
							$Lookup[$i]['Subtable'][$c]['CoverageGlyphs'] = $CoverageGlyphs = $this->_getCoverage();

							for ($s=0;$s<$Lookup[$i]['Subtable'][$c]['SubRuleSetCount'];$s++) {
								$SubRuleSet = $Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s];
								$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['FirstGlyph'] = $CoverageGlyphs[$s];
								for ($r=0;$r<$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRuleCount'];$r++) {
									$GlyphCount = $Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$r]['GlyphCount'];
									for ($g=1;$g<$GlyphCount;$g++) {
										$glyphID = $Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$r]['Input'][$g];
										$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'][$r]['InputGlyphs'][$g] = unicode_hex($this->glyphToChar[$glyphID][0]);
									}

								}
							}
						}
						// Format 2: Class-based Context Glyph Substitution
						else if ($SubstFormat==2) {	
							$this->seek($Lookup[$i]['Subtable'][$c]['CoverageTableOffset']);
							$Lookup[$i]['Subtable'][$c]['CoverageGlyphs'] = $CoverageGlyphs = $this->_getCoverage();

							$InputClasses = $this->_getClasses($Lookup[$i]['Subtable'][$c]['ClassDefOffset']);
							$Lookup[$i]['Subta