 + $this->read_ushort();
					$this->seek($Coverage);
					$glyphs = $this->_getCoverage(false,2);
					$this->GSLuCoverage[$i][$c] = $glyphs;
				}
			}

// $this->GSLuCoverage and $GSLookup

			//=====================================================================================
			//=====================================================================================
			$s = '<?php
$GSLuCoverage = '.var_export($this->GSLuCoverage , true).';
?>';

			file_put_contents(_MPDF_TTFONTDATAPATH.$this->fontkey.'.GSUBdata.php', $s);

			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
// Now repeats as original to get Substitution rules
			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
			// Get metadata and offsets for whole Lookup List table
			$this->seek($LookupList_offset );
			$LookupCount = $this->read_ushort();
			$Lookup = array();
			for ($i=0;$i<$LookupCount;$i++) {
				$Lookup[$i]['offset'] = $LookupList_offset + $this->read_ushort();
			}
			for ($i=0;$i<$LookupCount;$i++) {
				$this->seek($Lookup[$i]['offset']);
				$Lookup[$i]['Type'] = $this->read_ushort();
				$Lookup[$i]['Flag'] = $flag = $this->read_ushort();
				$Lookup[$i]['SubtableCount'] = $this->read_ushort();
				for ($c=0;$c<$Lookup[$i]['SubtableCount'] ;$c++) {
					$Lookup[$i]['Subtable'][$c]['Offset'] = $Lookup[$i]['offset'] + $this->read_ushort();

				}
				// MarkFilteringSet = Index (base 0) into GDEF mark glyph sets structure
				if (($flag & 0x0010) == 0x0010) {
					$Lookup[$i]['MarkFilteringSet'] = $this->read_ushort();
				}
				else { $Lookup[$i]['MarkFilteringSet'] = ''; }

				// Lookup Type 7: Extension
				if ($Lookup[$i]['Type'] == 7) {
					// Overwrites new offset (32-bit) for each subtable, and a new lookup Type
					for ($c=0;$c<$Lookup[$i]['SubtableCount'] ;$c++) {
						$this->seek($Lookup[$i]['Subtable'][$c]['Offset']);
						$ExtensionPosFormat = $this->read_ushort();
						$type = $this->read_ushort();
						$Lookup[$i]['Subtable'][$c]['Offset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ulong();
					}
					$Lookup[$i]['Type'] = $type;
				}

			}

//print_r($Lookup); exit;
			//=====================================================================================
			// Process (1) Whole LookupList
			for ($i=0;$i<$LookupCount;$i++) {
				for ($c=0;$c<$Lookup[$i]['SubtableCount'] ;$c++) {

					$this->seek($Lookup[$i]['Subtable'][$c]['Offset']);
					$SubstFormat= $this->read_ushort();
					$Lookup[$i]['Subtable'][$c]['Format'] = $SubstFormat;

/*
Lookup['Type'] Enumeration table for glyph substitution 
Value	Type	Description
1	Single	Replace one glyph with one glyph
2	Multiple	Replace one glyph with more than one glyph
3	Alternate	Replace one glyph with one of many glyphs
4	Ligature	Replace multiple glyphs with one glyph
5	Context	Replace one or more glyphs in context
6	Chaining Context	Replace one or more glyphs in chained context
7	Extension Substitution	Extension mechanism for other substitutions (i.e. this excludes the Extension type substitution itself)
8	Reverse chaining context single 	Applied in reverse order, replace single glyph in chaining context
*/

					// LookupType 1: Single Substitution Subtable
					if ($Lookup[$i]['Type'] == 1) {
						$Lookup[$i]['Subtable'][$c]['CoverageTableOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
						if ($SubstFormat==1) {	// Calculated output glyph indices
							$Lookup[$i]['Subtable'][$c]['DeltaGlyphID'] = $this->read_short();
						}
						else if ($SubstFormat==2) {	// Specified output glyph indices
							$GlyphCount = $this->read_ushort();
							for ($g=0;$g<$GlyphCount;$g++) { 
								$Lookup[$i]['Subtable'][$c]['Glyphs'][] = $this->read_ushort();
							}
						}
					}
					// LookupType 2: Multiple Substitution Subtable
					else if ($Lookup[$i]['Type'] == 2) {
						$Lookup[$i]['Subtable'][$c]['CoverageTableOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
						$Lookup[$i]['Subtable'][$c]['SequenceCount'] = $SequenceCount = $this->read_short();
						for($s=0;$s<$SequenceCount;$s++) {
							$Lookup[$i]['Subtable'][$c]['Sequences'][$s]['Offset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_short();
						}
						for($s=0;$s<$SequenceCount;$s++) {
							// Sequence Tables
							$this->seek($Lookup[$i]['Subtable'][$c]['Sequences'][$s]['Offset']);
							$Lookup[$i]['Subtable'][$c]['Sequences'][$s]['GlyphCount'] = $this->read_short();
							for ($g=0;$g<$Lookup[$i]['Subtable'][$c]['Sequences'][$s]['GlyphCount'];$g++) { 
								$Lookup[$i]['Subtable'][$c]['Sequences'][$s]['SubstituteGlyphID'][] = $this->read_ushort();
							}
						}
					}
					// LookupType 3: Alternate Forms
					else if ($Lookup[$i]['Type'] == 3) {
						$Lookup[$i]['Subtable'][$c]['CoverageTableOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
						$Lookup[$i]['Subtable'][$c]['AlternateSetCount'] = $AlternateSetCount = $this->read_short();
						for($s=0;$s<$AlternateSetCount;$s++) {
							$Lookup[$i]['Subtable'][$c]['AlternateSets'][$s]['Offset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_short();
						}

						for($s=0;$s<$AlternateSetCount;$s++) {
							// AlternateSet Tables
							$this->seek($Lookup[$i]['Subtable'][$c]['AlternateSets'][$s]['Offset']);
							$Lookup[$i]['Subtable'][$c]['AlternateSets'][$s]['GlyphCount'] = $this->read_short();
							for ($g=0;$g<$Lookup[$i]['Subtable'][$c]['AlternateSets'][$s]['GlyphCount'];$g++) { 
								$Lookup[$i]['Subtable'][$c]['AlternateSets'][$s]['SubstituteGlyphID'][] = $this->read_ushort();
							}
						}
					}
					// LookupType 4: Ligature Substitution Subtable
					else if ($Lookup[$i]['Type'] == 4) {
						$Lookup[$i]['Subtable'][$c]['CoverageTableOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
						$Lookup[$i]['Subtable'][$c]['LigSetCount'] = $LigSetCount = $this->read_short();
						for($s=0;$s<$LigSetCount;$s++) {
							$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Offset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_short();
						}
						for($s=0;$s<$LigSetCount;$s++) {
							// LigatureSet Tables
							$this->seek($Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Offset']);
							$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['LigCount'] = $this->read_short();
							for ($g=0;$g<$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['LigCount'];$g++) { 
								$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['LigatureOffset'][$g] = $Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Offset'] + $this->read_ushort();
							}
						}
						for($s=0;$s<$LigSetCount;$s++) {
							for ($g=0;$g<$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['LigCount'];$g++) { 
								// Ligature tables
								$this->seek($Lookup[$i]['Subtable'][$c]['LigSet'][$s]['LigatureOffset'][$g]);
								$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Ligature'][$g]['LigGlyph'] = $this->read_ushort();
								$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Ligature'][$g]['CompCount'] = $this->read_ushort();
								for ($l=1;$l<$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Ligature'][$g]['CompCount'];$l++) { 
									$Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Ligature'][$g]['GlyphID'][$l] = $this->read_ushort();
								}
							}
						}
					}

					// LookupType 5: Contextual Substitution Subtable
					else if ($Lookup[$i]['Type'] == 5) {
						// Format 1: Context Substitution
						if ($SubstFormat==1) {	
							$Lookup[$i]['Subtable'][$c]['CoverageTableOffset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_ushort();
							$Lookup[$i]['Subtable'][$c]['SubRuleSetCount'] = $SubRuleSetCount = $this->read_short();
							for($s=0;$s<$SubRuleSetCount;$s++) {
								$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['Offset'] = $Lookup[$i]['Subtable'][$c]['Offset'] + $this->read_short();
							}
							for($s=0;$s<$SubRuleSetCount;$s++) {
								// SubRuleSet Tables
								$this->seek($Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['Offset']);
								$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRuleCount'] = $this->read_short();
								for ($g=0;$g<$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRuleCount'];$g++) { 
									$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRuleOffset'][$g] = $Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['Offset'] + $this->read_ushort();
								}
							}
							for($s=0;$s<$SubRuleSetCount;$s++) {
								// SubRule Tables
								for ($g=0;$g<$Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRuleCount'];$g++) { 
									// Ligature tables
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
							$gid = $Lookup[$i]['Subtable'][$c]['AlternateSets'][$g]['SubstituteGlyphID'][0];
							if (!isset($this->glyphToChar[$gid][0])) { continue; }
							$substitute[] = unicode_hex($this->glyphToChar[$gid][0]);
							$Lookup[$i]['Subtable'][$c]['subs'][] = array('Replace'=>$replace, 'substitute'=>$substitute);
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
									if ($this->_checkGSUBignore($Lookup[$i]['Flag'], $rpl, $Lookup[$i]['MarkFilteringSet'])) {  continue 2; }
									$replace[] = $rpl;
								}
								$gid = $Lookup[$i]['Subtable'][$c]['LigSet'][$s]['Ligature'][$g]['LigGlyph'];
								if (!isset($this->glyphToChar[$gid][0])) { continue; }
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
							$Lookup[$i]['Subtable'][$c]['InputClasses'] = $InputClasses;
							for ($s=0;$s<$Lookup[$i]['Subtable'][$c]['SubClassSetCnt'];$s++) {
								if ($Lookup[$i]['Subtable'][$c]['SubClassSetOffset'][$s]>0) {
									$this->seek($Lookup[$i]['Subtable'][$c]['SubClassSetOffset'][$s]);
									$Lookup[$i]['Subtable'][$c]['SubClassSet'][$s]['SubClassRuleCnt'] = $SubClassRuleCnt = $this->read_ushort();
									$SubClassRule = array();
									for($b=0;$b<$SubClassRuleCnt;$b++) {
										$SubClassRule[$b] = $Lookup[$i]['Subtable'][$c]['SubClassSetOffset'][$s]+$this->read_ushort();
										$Lookup[$i]['Subtable'][$c]['SubClassSet'][$s]['SubClassRule'][$b] = $SubClassRule[$b];
									}
								}
							}

							for ($s=0;$s<$Lookup[$i]['Subtable'][$c]['SubClassSetCnt'];$s++) {
								if ($Lookup[$i]['Subtable'][$c]['SubClassSetOffset'][$s]>0) {
									$SubClassRuleCnt = $Lookup[$i]['Subtable'][$c]['SubClassSet'][$s]['SubClassRuleCnt'];
									for($b=0;$b<$SubClassRuleCnt;$b++) {
										$this->seek($Lookup[$i]['Subtable'][$c]['SubClassSet'][$s]['SubClassRule'][$b]);
										$Rule = array();
										$Rule['InputGlyphCount'] = $this->read_ushort();
										$Rule['SubstCount'] = $this->read_ushort();
										for ($r=1;$r<$Rule['InputGlyphCount'];$r++) {
											$Rule['Input'][$r] = $this->read_ushort();
										}
										for ($r=0;$r<$Rule['SubstCount'];$r++) {
											$Rule['SequenceIndex'][$r] = $this->read_ushort();
											$Rule['LookupListIndex'][$r] = $this->read_ushort();
										}

										$Lookup[$i]['Subtable'][$c]['SubClassSet'][$s]['SubClassRule'][$b] = $Rule;
									}
								}
							}
						}
						// Format 3: Coverage-based Context Glyph Substitution
						else if ($SubstFormat==3) {
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['InputGlyphCount'];$b++) {
								$this->seek($Lookup[$i]['Subtable'][$c]['CoverageInput'][$b]);
								$glyphs = $this->_getCoverage();
								$Lookup[$i]['Subtable'][$c]['CoverageInputGlyphs'][] = implode("|",$glyphs);
							}
							die("Lookup Type 5, SubstFormat 3 not tested. Please report this with the name of font used - ".$this->fontkey); 
						}

					}

					// LookupType 6: Chaining Contextual Substitution Subtable
					else if ($Lookup[$i]['Type'] == 6) {
						// Format 1: Simple Chaining Context Glyph Substitution  p255
						if ($SubstFormat==1) {	
							$this->seek($Lookup[$i]['Subtable'][$c]['CoverageTableOffset']);
							$Lookup[$i]['Subtable'][$c]['CoverageGlyphs'] = $CoverageGlyphs = $this->_getCoverage();

							$ChainSubRuleSetCnt = $Lookup[$i]['Subtable'][$c]['ChainSubRuleSetCount'];

							for ($s=0;$s<$ChainSubRuleSetCnt;$s++) {
								$this->seek($Lookup[$i]['Subtable'][$c]['ChainSubRuleSetOffset'][$s]);
								$ChainSubRuleCnt = $Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRuleCount'] = $this->read_ushort();
								for ($r=0;$r<$ChainSubRuleCnt;$r++) {
									$Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRuleOffset'][$r] = $Lookup[$i]['Subtable'][$c]['ChainSubRuleSetOffset'][$s] + $this->read_ushort();

								}
							}
							for ($s=0;$s<$ChainSubRuleSetCnt;$s++) {
								$ChainSubRuleCnt = $Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRuleCount'];
								for ($r=0;$r<$ChainSubRuleCnt;$r++) {
									// ChainSubRule
									$this->seek($Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRuleOffset'][$r]);

									$BacktrackGlyphCount = $Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'][$r]['BacktrackGlyphCount'] = $this->read_ushort();
									for ($g=0;$g<$BacktrackGlyphCount;$g++) {
										$glyphID = $this->read_ushort();
										$Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'][$r]['BacktrackGlyphs'][$g] = unicode_hex($this->glyphToChar[$glyphID][0]); 
									}

									$InputGlyphCount = $Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'][$r]['InputGlyphCount'] = $this->read_ushort();
									for ($g=1;$g<$InputGlyphCount;$g++) {
										$glyphID = $this->read_ushort();
										$Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'][$r]['InputGlyphs'][$g] = unicode_hex($this->glyphToChar[$glyphID][0]);
									}


									$LookaheadGlyphCount = $Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'][$r]['LookaheadGlyphCount'] = $this->read_ushort();
									for ($g=0;$g<$LookaheadGlyphCount;$g++) {
										$glyphID = $this->read_ushort();
										$Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'][$r]['LookaheadGlyphs'][$g] = unicode_hex($this->glyphToChar[$glyphID][0]);
									}

									$SubstCount = $Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'][$r]['SubstCount'] = $this->read_ushort();
									for ($lu=0;$lu<$SubstCount;$lu++) {
										$Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'][$r]['SequenceIndex'][$lu] = $this->read_ushort();
										$Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'][$r]['LookupListIndex'][$lu] = $this->read_ushort();
									}
								}
							}

						}
						// Format 2: Class-based Chaining Context Glyph Substitution  p257
						else if ($SubstFormat==2) {	
							$this->seek($Lookup[$i]['Subtable'][$c]['CoverageTableOffset']);
							$Lookup[$i]['Subtable'][$c]['CoverageGlyphs'] = $CoverageGlyphs = $this->_getCoverage();

							$BacktrackClasses = $this->_getClasses($Lookup[$i]['Subtable'][$c]['BacktrackClassDefOffset']);
							$Lookup[$i]['Subtable'][$c]['BacktrackClasses'] = $BacktrackClasses;

							$InputClasses = $this->_getClasses($Lookup[$i]['Subtable'][$c]['InputClassDefOffset']);
							$Lookup[$i]['Subtable'][$c]['InputClasses'] = $InputClasses;

							$LookaheadClasses = $this->_getClasses($Lookup[$i]['Subtable'][$c]['LookaheadClassDefOffset']);
							$Lookup[$i]['Subtable'][$c]['LookaheadClasses'] = $LookaheadClasses;

							for ($s=0;$s<$Lookup[$i]['Subtable'][$c]['ChainSubClassSetCnt'];$s++) {
								if ($Lookup[$i]['Subtable'][$c]['ChainSubClassSetOffset'][$s]>0) {
									$this->seek($Lookup[$i]['Subtable'][$c]['ChainSubClassSetOffset'][$s]);
									$Lookup[$i]['Subtable'][$c]['ChainSubClassSet'][$s]['ChainSubClassRuleCnt'] = $ChainSubClassRuleCnt = $this->read_ushort();
									$ChainSubClassRule = array();
									for($b=0;$b<$ChainSubClassRuleCnt;$b++) {
										$ChainSubClassRule[$b] = $Lookup[$i]['Subtable'][$c]['ChainSubClassSetOffset'][$s]+$this->read_ushort();
										$Lookup[$i]['Subtable'][$c]['ChainSubClassSet'][$s]['ChainSubClassRule'][$b] = $ChainSubClassRule[$b];
									}
								}
							}

							for ($s=0;$s<$Lookup[$i]['Subtable'][$c]['ChainSubClassSetCnt'];$s++) {
								if (isset($Lookup[$i]['Subtable'][$c]['ChainSubClassSet'][$s]['ChainSubClassRuleCnt'])) {
									$ChainSubClassRuleCnt = $Lookup[$i]['Subtable'][$c]['ChainSubClassSet'][$s]['ChainSubClassRuleCnt'];
								}
								else { $ChainSubClassRuleCnt = 0; }
								for($b=0;$b<$ChainSubClassRuleCnt;$b++) {
								   if ($Lookup[$i]['Subtable'][$c]['ChainSubClassSetOffset'][$s]>0) {
									$this->seek($Lookup[$i]['Subtable'][$c]['ChainSubClassSet'][$s]['ChainSubClassRule'][$b]);
									$Rule = array();
									$Rule['BacktrackGlyphCount'] = $this->read_ushort();
									for ($r=0;$r<$Rule['BacktrackGlyphCount'];$r++) {
										$Rule['Backtrack'][$r] = $this->read_ushort();
									}
									$Rule['InputGlyphCount'] = $this->read_ushort();
									for ($r=1;$r<$Rule['InputGlyphCount'];$r++) {
										$Rule['Input'][$r] = $this->read_ushort();
									}
									$Rule['LookaheadGlyphCount'] = $this->read_ushort();
									for ($r=0;$r<$Rule['LookaheadGlyphCount'];$r++) {
										$Rule['Lookahead'][$r] = $this->read_ushort();
									}
									$Rule['SubstCount'] = $this->read_ushort();
									for ($r=0;$r<$Rule['SubstCount'];$r++) {
										$Rule['SequenceIndex'][$r] = $this->read_ushort();
										$Rule['LookupListIndex'][$r] = $this->read_ushort();
									}

									$Lookup[$i]['Subtable'][$c]['ChainSubClassSet'][$s]['ChainSubClassRule'][$b] = $Rule;
								   }
								}
							}
						}
						// Format 3: Coverage-based Chaining Context Glyph Substitution  p259
						else if ($SubstFormat==3) {
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['BacktrackGlyphCount'];$b++) {
								$this->seek($Lookup[$i]['Subtable'][$c]['CoverageBacktrack'][$b]);
								$glyphs = $this->_getCoverage();
								$Lookup[$i]['Subtable'][$c]['CoverageBacktrackGlyphs'][] = implode("|",$glyphs);
							}
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['InputGlyphCount'];$b++) {
								$this->seek($Lookup[$i]['Subtable'][$c]['CoverageInput'][$b]);
								$glyphs = $this->_getCoverage();
								$Lookup[$i]['Subtable'][$c]['CoverageInputGlyphs'][] = implode("|",$glyphs);
								// Don't use above value as these are ordered numerically not as need to process
							}
							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['LookaheadGlyphCount'];$b++) {
								$this->seek($Lookup[$i]['Subtable'][$c]['CoverageLookahead'][$b]);
								$glyphs = $this->_getCoverage();
								$Lookup[$i]['Subtable'][$c]['CoverageLookaheadGlyphs'][] = implode("|",$glyphs);
							}

						}
					}
				}
			}


			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
			//=====================================================================================

			$GSUBScriptLang = array();
			$rtlpua = array();	// All glyphs added to PUA [for magic_reverse]
			foreach($gsub AS $st=>$scripts) {
				foreach($scripts AS $t=>$langsys) {
					$lul = array();	// array of LookupListIndexes
					$tags = array();	// corresponding array of feature tags e.g. 'ccmp'
//print_r($langsys ); exit;
					foreach($langsys AS $tag=>$ft) {
						foreach($ft AS $ll) { 
							$lul[$ll] = $tag; 
						}
					}
					ksort($lul);	// Order the Lookups in the order they are in the GUSB table, regardless of Feature order
					$volt = $this->_getGSUBarray($Lookup, $lul, $st);
//print_r($lul); exit;

					//=====================================================================================
					//=====================================================================================
					// Interrogate $volt 
					// isol, fin, medi, init(arab syrc) into $rtlSUB for use in ArabJoin 
					// but also identify all RTL chars in PUA for magic_reverse (arab syrc hebr thaa nko  samr)
					// identify reph, matras, vatu, half forms etc for Indic for final re-ordering
					//=====================================================================================
					//=====================================================================================
					$rtl = array();
					$rtlSUB = "array()";
					$finals = '';
					if (strpos('arab syrc hebr thaa nko  samr', $st)!==false) {	// all RTL scripts [any/all languages] ? Mandaic
//print_r($volt); exit;
						foreach($volt AS $v) {
							// isol fina fin2 fin3 medi med2 for Syriac
							// ISOLATED FORM :: FINAL :: INITIAL :: MEDIAL :: MED2 :: FIN2 :: FIN3
							if (strpos('isol fina init medi fin2 fin3 med2',$v['tag'])!==false) { 
								$key = $v['match'];
								$key = preg_replace('/[\(\)]*/','',$key);
								$sub = $v['replace'];
								if ($v['tag'] == 'isol') $kk = 0;
								else if ($v['tag'] == 'fina') $kk = 1;
								else if ($v['tag'] == 'init') $kk = 2;
								else if ($v['tag'] == 'medi') $kk = 3;
								else if ($v['tag'] == 'med2') $kk = 4;
								else if ($v['tag'] == 'fin2') $kk = 5;
								else if ($v['tag'] == 'fin3') $kk = 6;
								$rtl[$key][$kk] = $sub;
								if (isset($v['prel']) && count($v['prel'])) $rtl[$key]['prel'][$kk] = $v['prel'];
								if (isset($v['postl']) && count($v['postl'])) $rtl[$key]['postl'][$kk] = $v['postl'];
								if (isset($v['ignore']) && $v['ignore']) {
									$rtl[$key]['ignore'][$kk] = $v['ignore'];
								}
								$rtlpua[] = $sub;
							}
							// Add any other glyphs which are in PUA
							else { 
								if (isset($v['context']) && $v['context']) {
									foreach($v['rules'] AS $vs) {
										for($i=0;$i<count($vs['match']);$i++) {
											if (isset($vs['replace'][$i]) && preg_match('/^0[A-F0-9]{4}$/', $vs['match'][$i])) { 
												if (preg_match('/^0[EF][A-F0-9]{3}$/', $vs['replace'][$i])) { 
													$rtlpua[] = $vs['replace'][$i];
												}
											}
										}
									}
								}
								else {
									preg_match_all('/\((0[A-F0-9]{4})\)/', $v['match'], $m);
									for($i=0;$i<count($m[0]);$i++) { 
										$sb = explode(' ',$v['replace']);
										foreach($sb AS $sbg) { 
											if (preg_match('/(0[EF][A-F0-9]{3})/', $sbg, $mr)) { 
												$rtlpua[] = $mr[1]; 
											}
										}
									}
								}
							}
						}
//print_r($rtl); exit;
						// For kashida, need to determine all final forms except ones already identified by kashida
						// priority rules (see otl.php)
						foreach($rtl AS $base=>$variants) {
							if (isset($variants[1])) {	// i.e. final form
								if (strpos('0FE8E 0FE94 0FEA2 0FEAA 0FEAE 0FEC2 0FEDA 0FEDE 0FB93 0FECA 0FED2 0FED6 0FEEE 0FEF0 0FEF2', $variants[1])===false) {	// not already included

								// This version does not exclude RA (0631) FEAE; Ya (064A)  FEF2; Alef Maqsurah (0649) FEF0 which
								// are selected in priority if connected to a medial Bah
								//if (strpos('0FE8E 0FE94 0FEA2 0FEAA 0FEC2 0FEDA 0FEDE 0FB93 0FECA 0FED2 0FED6 0FEEE', $variants[1])===false) {	// not already included
									$finals .= $variants[1].' ';
								}
							}
						}
//echo $finals ; exit;
//print_r($rtlpua); exit;
						ksort($rtl);
						$a = var_export($rtl, true);
						$a = preg_replace('/\\\\\\\\/',"\\",$a);
						$a = preg_replace('/\'/','"',$a);
						$a = preg_replace('/\r/','',$a);
						$a = preg_replace('/> \n/','>',$a);
						$a = preg_replace('/\n  \)/',')',$a);
						$a= preg_replace('/\n    /',' ',$a);
						$a = preg_replace('/\[IGNORE(\d+)\]/','".$ignore[\\1]."',$a);
						$rtlSUB  = preg_replace('/[ ]+/',' ',$a);

					}
					//=====================================================================================
					// INDIC - Dynamic properties
					//=====================================================================================
					$rphf = array();
					$half = array();
					$pref = array();
					$blwf = array();
					$pstf = array();
					if (strpos('dev2 bng2 gur2 gjr2 ory2 tml2 tel2 knd2 mlm2 deva beng guru gujr orya taml telu knda mlym', $st)!==false) {	// all INDIC scripts [any/all languages]

						if (strpos('deva beng guru gujr orya taml telu knda mlym', $st)!==false) { $is_old_spec = true; }
						else  { $is_old_spec = false; }

						// First get 'locl' substitutions (reversed!)
						$loclsubs = array();
						foreach($volt AS $v) {
							if (strpos('locl',$v['tag'])!==false) { 
								$key = $v['match'];
								$key = preg_replace('/[\(\)]*/','',$key);
								$sub = $v['replace'];
								if ($key && strlen(trim($key))==5 && $sub) { $loclsubs[$sub] = $key; }
							}
						}
//if (count($loclsubs)) { print_r($loclsubs); exit; }

						foreach($volt AS $v) {
							// <rphf> <half> <pref> <blwf> <pstf>
							// defines consonant types:
							//     Reph <rphf>
							//     Half forms <half>
							//     Pre-base-reordering forms of Ra/Rra <pref>
							//     Below-base forms <blwf>
							//     Post-base forms <pstf>

							// applied together with <locl> feature to input sequences consisting of two characters
							// This is done for each consonant
							// for <rphf> and <half>, features are applied to Consonant + Halant combinations
							// for <pref>, <blwf> and <pstf>, features are applied to Halant + Consonant combinations
							// Old version eg 'deva' <pref>, <blwf> and <pstf>, features are applied to Consonant + Halant
							// Some malformed fonts still do Consonant + Halant for these - so match both??
							// If these two glyphs form a ligature, with no additional glyphs in context
							// this means the consonant has the corresponding form

							// Currently set to cope with both
							// See also classes/otl.php

							if (strpos('rphf half pref blwf pstf',$v['tag'])!==false) { 
								if (isset($v['context']) && $v['context'] && $v['nBacktrack']==0 && $v['nLookahead']==0) {
								  foreach($v['rules'] AS $vs) {
									if (count($vs['match'])==2 && count($vs['replace'])==1) {
										$sub = $vs['replace'][0];
										// If Halant Cons   <pref>, <blwf> and <pstf> in New version only
										if (strpos('0094D 009CD 00A4D 00ACD 00B4D 00BCD 00C4D 00CCD 00D4D',$vs['match'][0])!==false && strpos('pref blwf pstf',$v['tag'])!==false && !$is_old_spec ) {
											$key = $vs['match'][1];
											$tag = $v['tag'];
											if (isset($loclsubs[$key])) {
												$$tag[$loclsubs[$key]] = $sub;
											}
											$tmp = &$$tag;
											$tmp[hexdec($key)] = hexdec($sub);
										}
										// If Cons Halant    <rphf> and <half> always
										// and <pref>, <blwf> and <pstf> in Old version 
										else if (strpos('0094D 009CD 00A4D 00ACD 00B4D 00BCD 00C4D 00CCD 00D4D',$vs['match'][1])!==false && (strpos('rphf half',$v['tag'])!==false || (strpos('pref blwf pstf',$v['tag'])!==false && ($is_old_spec || _OTL_OLD_SPEC_COMPAT_2)))) {
											$key = $vs['match'][0];
											$tag = $v['tag'];
											if (isset($loclsubs[$key])) {
												$$tag[$loclsubs[$key]] = $sub;
											}
											$tmp = &$$tag;
											$tmp[hexdec($key)] = hexdec($sub);
										}
									}
								  }
								}
								else if (!isset($v['context'])) {
									$key = $v['match'];
									$key = preg_replace('/[\(\)]*/','',$key);
									$sub = $v['replace'];
									if ($key && strlen(trim($key))==11 && $sub) {
										// If Cons Halant    <rphf> and <half> always
										// and <pref>, <blwf> and <pstf> in Old version 
										// If Halant Cons   <pref>, <blwf> and <pstf> in New version only
										if (strpos('0094D 009CD 00A4D 00ACD 00B4D 00BCD 00C4D 00CCD 00D4D',substr($key, 0, 5))!==false && strpos('pref blwf pstf',$v['tag'])!==false && !$is_old_spec ) {
											$key = substr($key, 6, 5);
											$tag = $v['tag'];
											if (isset($loclsubs[$key])) {
												$$tag[$loclsubs[$key]] = $sub;
											}
											$tmp = &$$tag;
											$tmp[hexdec($key)] = hexdec($sub);
										}
										else if (strpos('0094D 009CD 00A4D 00ACD 00B4D 00BCD 00C4D 00CCD 00D4D',substr($key, 6, 5))!==false && (strpos('rphf half',$v['tag'])!==false || (strpos('pref blwf pstf',$v['tag'])!==false && ($is_old_spec || _OTL_OLD_SPEC_COMPAT_2)))) {
											$key = substr($key, 0, 5);
											$tag = $v['tag'];
											if (isset($loclsubs[$key])) {
												$$tag[$loclsubs[$key]] = $sub;
											}
											$tmp= &$$tag;
											$tmp[hexdec($key)] = hexdec($sub);
										}
									}
								}
							}
						}
/*
print_r($rphf ); 
print_r($half ); 
print_r($pref ); 
print_r($blwf ); 
print_r($pstf ); exit;
*/

					}
//print_r($rtlpua); exit;
					//=====================================================================================
					//=====================================================================================
					//=====================================================================================
					//=====================================================================================
					if (count($rtl) || count($rphf ) || count($half ) || count($pref ) || count($blwf ) || count($pstf ) || $finals) {
						// SAVE LOOKUPS TO FILE fontname.GSUB.scripttag.langtag.php


						$s = '<?php

$rtlSUB = '.$rtlSUB.';
$finals = \''.$finals.'\';
$rphf = '.var_export($rphf , true).';
$half = '.var_export($half , true).';
$pref = '.var_export($pref , true).';
$blwf = '.var_export($blwf , true).';
$pstf = '.var_export($pstf , true).';

 '."\n".'?>';


						file_put_contents(_MPDF_TTFONTDATAPATH.$this->fontkey.'.GSUB.'.$st.'.'.$t.'.php', $s);

					}
					//=====================================================================================
					if (!isset($GSUBScriptLang[$st])) { $GSUBScriptLang[$st] = ''; }
					$GSUBScriptLang[$st] .= $t.' ';
					//=====================================================================================

				}
			}
		//print_r($rtlpua); exit;

			// All RTL glyphs from font added to (or already in) PUA [reqd for magic_reverse]
			$rtlPUAstr = "";
			if (count($rtlpua)) {
				$rtlpua = array_unique($rtlpua);
				sort($rtlpua);
				$n = count($rtlpua);
				for($i=0;$i<$n;$i++) { 
					if (hexdec($rtlpua[$i])<hexdec('E000') || hexdec($rtlpua[$i])>hexdec('F8FF')) { 
						unset($rtlpua[$i]); 
					} 
				}
				sort($rtlpua, SORT_STRING);

				$rangeid = -1;
				$range = array();
				$prevgid = -2;
				// for each character
				foreach ($rtlpua as $gidhex) {
					$gid = hexdec($gidhex);
					if ($gid == ($prevgid + 1)) {
						$range[$rangeid]['end'] = $gidhex;
						$range[$rangeid]['count']++;
					} else {
						// new range
						$rangeid++;
						$range[$rangeid] = array();
						$range[$rangeid]['start'] = $gidhex;
						$range[$rangeid]['end'] = $gidhex;
						$range[$rangeid]['count'] = 1;
					}
					$prevgid = $gid;
				}
				foreach($range AS $rg) {
					if ($rg['count'] == 1) { $rtlPUAstr .= "\x{".$rg['start']."}"; }
					else if ($rg['count'] == 2) { $rtlPUAstr .= "\x{".$rg['start']."}\x{".$rg['end']."}"; } 
					else { $rtlPUAstr .= "\x{".$rg['start']."}-\x{".$rg['end']."}"; }

				}
			}

	//print_r($rtlPUAstr ); exit;


			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
		//print_r($rtlpua); exit;
		//print_r($GSUBScriptLang); exit;



		}
//print_r($Lookup); exit;

		return array($GSUBScriptLang, $gsub, $GSLookup, $rtlPUAstr);	// , $rtlPUAarr Not needed

	}
/////////////////////////////////////////////////////////////////////////////////////////
	// GSUB functions
	function _getGSUBarray(&$Lookup, &$lul, $scripttag) {
			// Process (3) LookupList for specific Script-LangSys
			// Generate preg_replace
			$volt = array();
			$reph = '';
			$matraE = '';
			$vatu = '';
			foreach($lul AS $i=>$tag) {
				for ($c=0;$c<$Lookup[$i]['SubtableCount'] ;$c++) {

					$SubstFormat= $Lookup[$i]['Subtable'][$c]['Format'] ;

					// LookupType 1: Single Substitution Subtable
					if ($Lookup[$i]['Type'] == 1) {
						for ($s=0;$s<count($Lookup[$i]['Subtable'][$c]['subs']);$s++) {
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['subs'][$s]['Replace'];
							$substitute = $Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute'][0];
							// Ignore has already been applied earlier on
							$repl = $this->_makeGSUBinputMatch($inputGlyphs, "()");
							$subs = $this->_makeGSUBinputReplacement(1, $substitute, "()", 0, 1, 0);
							$volt[] = array('match'=>$repl, 'replace'=>$subs, 'tag'=>$tag, 'key'=>$inputGlyphs[0], 'type' => 1);
						}
					}
					// LookupType 2: Multiple Substitution Subtable
					else if ($Lookup[$i]['Type'] == 2) {
						for ($s=0;$s<count($Lookup[$i]['Subtable'][$c]['subs']);$s++) {
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['subs'][$s]['Replace'];
							$substitute = implode(" ", $Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute']);
							// Ignore has already been applied earlier on
							$repl = $this->_makeGSUBinputMatch($inputGlyphs,"()");
							$subs = $this->_makeGSUBinputReplacement(1, $substitute, "()", 0, 1, 0);
							$volt[] = array('match'=>$repl, 'replace'=>$subs, 'tag'=>$tag, 'key'=>$inputGlyphs[0], 'type' => 2);
						}
					}
					// LookupType 3: Alternate Forms
					else if ($Lookup[$i]['Type'] == 3) {
						for ($s=0;$s<count($Lookup[$i]['Subtable'][$c]['subs']);$s++) {
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['subs'][$s]['Replace'];
							$substitute = $Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute'][0];
							// Ignore has already been applied earlier on
							$repl = $this->_makeGSUBinputMatch($inputGlyphs, "()");
							$subs = $this->_makeGSUBinputReplacement(1, $substitute, "()", 0, 1, 0);
							$volt[] = array('match'=>$repl, 'replace'=>$subs, 'tag'=>$tag, 'key'=>$inputGlyphs[0], 'type' => 3);
						}
					}
					// LookupType 4: Ligature Substitution Subtable
					else if ($Lookup[$i]['Type'] == 4) {
						for ($s=0;$s<count($Lookup[$i]['Subtable'][$c]['subs']);$s++) {
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['subs'][$s]['Replace'];
							$substitute = $Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute'][0];
							// Ignore has already been applied earlier on
							$ignore = $this->_getGSUBignoreString($Lookup[$i]['Flag'], $Lookup[$i]['MarkFilteringSet']);
							$repl = $this->_makeGSUBinputMatch($inputGlyphs, $ignore);
							$subs = $this->_makeGSUBinputReplacement(count($inputGlyphs), $substitute, $ignore, 0, count($inputGlyphs), 0);
							$volt[] = array('match'=>$repl, 'replace'=>$subs, 'tag'=>$tag, 'key'=>$inputGlyphs[0], 'type' => 4, 'CompCount' => $Lookup[$i]['Subtable'][$c]['subs'][$s]['CompCount'], 'Lig' => $substitute);
						}
					}

					// LookupType 5: Chaining Contextual Substitution Subtable
					else if ($Lookup[$i]['Type'] == 5) {
						// Format 1: Context Substitution
						if ($SubstFormat==1) {	
							$ignore = $this->_getGSUBignoreString($Lookup[$i]['Flag'], $Lookup[$i]['MarkFilteringSet']);
							for($s=0;$s<$Lookup[$i]['Subtable'][$c]['SubRuleSetCount'];$s++) {
								// SubRuleSet
$subRule = array();
								foreach($Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'] AS $rule) {
									// SubRule
									$inputGlyphs = array(); 
									if ($rule['GlyphCount']>1) { 
										$inputGlyphs = $rule['InputGlyphs']; 
									}
									$inputGlyphs[0] = $Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['FirstGlyph'];
									ksort($inputGlyphs);
									$nInput = count($inputGlyphs);

									$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, array(), 0);
									$subRule = array('context' => 1, 'tag' => $tag, 'matchback' => '', 'match' => $contextInputMatch, 'nBacktrack' => 0, 'nInput' => $nInput, 'nLookahead' => 0, 'rules' => array(), );

									for ($b=0;$b<$rule['SubstCount'];$b++) {
										$lup = $rule['SubstLookupRecord'][$b]['LookupListIndex'];
										$seqIndex = $rule['SubstLookupRecord'][$b]['SequenceIndex'];

										// $Lookup[$lup] = secondary Lookup
										for($lus=0;$lus<$Lookup[$lup]['SubtableCount'];$lus++) {
										   if (count($Lookup[$lup]['Subtable'][$lus]['subs'])) {
											foreach($Lookup[$lup]['Subtable'][$lus]['subs'] AS $luss) {

												$lookupGlyphs = $luss['Replace'];
												$mLen = count($lookupGlyphs);

												// Only apply if the (first) 'Replace' glyph from the 
												// Lookup list is in the [inputGlyphs] at ['SequenceIndex']
												// then apply the substitution
												if (strpos($inputGlyphs[$seqIndex],$lookupGlyphs[0])===false) { continue; }
												$REPL = implode(" ",$luss['substitute']);
												if (strpos("isol fina fin2 fin3 medi med2 init ",$tag)!==false && $scripttag=='arab') {
													$volt[] = array('match'=>$lookupGlyphs[0], 'replace'=>$REPL, 'tag'=>$tag, 'prel'=>$backtrackGlyphs, 'postl'=>$lookaheadGlyphs, 'ignore'=>$ignore);
												}
												else {
													$subRule['rules'][] = array('type' => $Lookup[$lup]['Type'], 'match' => $lookupGlyphs, 'replace' => $luss['substitute'], 'seqIndex' => $seqIndex, 'key' => $lookupGlyphs[0], );
												}
											}
										   }
										}
									}


									if (count($subRule['rules'])) $volt[] = $subRule;

								}
							}
						}
						// Format 2: Class-based Context Glyph Substitution
						else if ($SubstFormat==2) {	
							$ignore = $this->_getGSUBignoreString($Lookup[$i]['Flag'], $Lookup[$i]['MarkFilteringSet']);
							foreach($Lookup[$i]['Subtable'][$c]['SubClassSet'] AS $inputClass=>$cscs) {
								for($cscrule=0;$cscrule<$cscs['SubClassRuleCnt'];$cscrule++) {
									$rule = $cscs['SubClassRule'][$cscrule];

									$inputGlyphs = array();

									$inputGlyphs[0] = $Lookup[$i]['Subtable'][$c]['InputClasses'][$inputClass];
									if ($rule['InputGlyphCount']>1) {
										//  NB starts at 1 
										for ($gcl=1;$gcl<$rule['InputGlyphCount'];$gcl++) {
											$classindex = $rule['Input'][$gcl];
											if (isset($Lookup[$i]['Subtable'][$c]['InputClasses'][$classindex])) {
												$inputGlyphs[$gcl] = $Lookup[$i]['Subtable'][$c]['InputClasses'][$classindex];
											}
											// if class[0] = all glyphs excluding those specified in all other classes
											// set to blank '' for now
											else { $inputGlyphs[$gcl] = ''; }
										}
									}

									$nInput = $rule['InputGlyphCount'];

									$nIsubs = (2*$nInput)-1;

									$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, array(), 0);
									$subRule = array('context' => 1, 'tag' => $tag, 'matchback' => '', 'match' => $contextInputMatch, 'nBacktrack' => 0, 'nInput' => $nInput, 'nLookahead' => 0, 'rules' => array(), );

									for ($b=0;$b<$rule['SubstCount'];$b++) {
										$lup = $rule['LookupListIndex'][$b];
										$seqIndex = $rule['SequenceIndex'][$b];

										// $Lookup[$lup] = secondary Lookup
										for($lus=0;$lus<$Lookup[$lup]['SubtableCount'];$lus++) {
										   if (isset($Lookup[$lup]['Subtable'][$lus]['subs']) && count($Lookup[$lup]['Subtable'][$lus]['subs'])) {
											foreach($Lookup[$lup]['Subtable'][$lus]['subs'] AS $luss) {

												$lookupGlyphs = $luss['Replace'];
												$mLen = count($lookupGlyphs);

												// Only apply if the (first) 'Replace' glyph from the 
												// Lookup list is in the [inputGlyphs] at ['SequenceIndex']
												// then apply the substitution
												if (strpos($inputGlyphs[$seqIndex],$lookupGlyphs[0])===false) { continue; }

												// Returns e.g. ¦(0612)¦(ignore) (0613)¦(ignore) (0614)¦
												$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, $lookupGlyphs, $seqIndex);
												$REPL = implode(" ",$luss['substitute']);
												// Returns e.g. "REPL\${6}\${8}" or "\${1}\${2} \${3} REPL\${4}\${6}\${8} \${9}"

												if (strpos("isol fina fin2 fin3 medi med2 init ",$tag)!==false && $scripttag=='arab') {
													$volt[] = array('match'=>$lookupGlyphs[0], 'replace'=>$REPL, 'tag'=>$tag, 'prel'=>$backtrackGlyphs, 'postl'=>$lookaheadGlyphs, 'ignore'=>$ignore);
												}
												else {
													$subRule['rules'][] = array('type' => $Lookup[$lup]['Type'], 'match' => $lookupGlyphs, 'replace' => $luss['substitute'], 'seqIndex' => $seqIndex, 'key' => $lookupGlyphs[0], );
												}
											}
										   }
										}
									}
									if (count($subRule['rules'])) $volt[] = $subRule;

								}
							}



						}
						// Format 3: Coverage-based Context Glyph Substitution  p259
						else if ($SubstFormat==3) {
							// IgnoreMarks flag set on main Lookup table
							$ignore = $this->_getGSUBignoreString($Lookup[$i]['Flag'], $Lookup[$i]['MarkFilteringSet']);
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['CoverageInputGlyphs'];
							$CoverageInputGlyphs = implode('|', $inputGlyphs);
							$nInput = $Lookup[$i]['Subtable'][$c]['InputGlyphCount'];

							if ($Lookup[$i]['Subtable'][$c]['BacktrackGlyphCount']) {
								$backtrackGlyphs = $Lookup[$i]['Subtable'][$c]['CoverageBacktrackGlyphs'];
							}
							else { $backtrackGlyphs = array(); }
							// Returns e.g. ¦(FEEB|FEEC)(ignore) ¦(FD12|FD13)(ignore) ¦
							$backtrackMatch = $this->_makeGSUBbacktrackMatch($backtrackGlyphs, $ignore);

							if ($Lookup[$i]['Subtable'][$c]['LookaheadGlyphCount']) {
								$lookaheadGlyphs = $Lookup[$i]['Subtable'][$c]['CoverageLookaheadGlyphs'];
							}
							else { $lookaheadGlyphs = array(); }
							// Returns e.g. ¦(ignore) (FD12|FD13)¦(ignore) (FEEB|FEEC)¦
							$lookaheadMatch = $this->_makeGSUBlookaheadMatch($lookaheadGlyphs, $ignore);

							$nBsubs = 2*count($backtrackGlyphs);
							$nIsubs = (2*$nInput)-1;
							$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, array(), 0);
							$subRule = array('context' => 1, 'tag' => $tag, 'matchback' => $backtrackMatch, 'match' => ($contextInputMatch . $lookaheadMatch), 'nBacktrack' => count($backtrackGlyphs), 'nInput' => $nInput, 'nLookahead' => count($lookaheadGlyphs), 'rules' => array(), );

							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['SubstCount'];$b++) {
								$lup = $Lookup[$i]['Subtable'][$c]['SubstLookupRecord'][$b]['LookupListIndex'];
								$seqIndex = $Lookup[$i]['Subtable'][$c]['SubstLookupRecord'][$b]['SequenceIndex'];
								for($lus=0;$lus<$Lookup[$lup]['SubtableCount'];$lus++) {
								   if (count($Lookup[$lup]['Subtable'][$lus]['subs'])) {
									foreach($Lookup[$lup]['Subtable'][$lus]['subs'] AS $luss) {
										$lookupGlyphs = $luss['Replace'];
										$mLen = count($lookupGlyphs);

										// Only apply if the (first) 'Replace' glyph from the 
										// Lookup list is in the [inputGlyphs] at ['SequenceIndex']
										// then apply the substitution
										if (strpos($inputGlyphs[$seqIndex],$lookupGlyphs[0])===false) { continue; }

										// Returns e.g. ¦(0612)¦(ignore) (0613)¦(ignore) (0614)¦
										$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, $lookupGlyphs, $seqIndex);
										$REPL = implode(" ",$luss['substitute']);

										if (strpos("isol fina fin2 fin3 medi med2 init ",$tag)!==false && $scripttag=='arab') {
											$volt[] = array('match'=>$lookupGlyphs[0], 'replace'=>$REPL, 'tag'=>$tag, 'prel'=>$backtrackGlyphs, 'postl'=>$lookaheadGlyphs, 'ignore'=>$ignore);
										}
										else {
											$subRule['rules'][] = array('type' => $Lookup[$lup]['Type'], 'match' => $lookupGlyphs, 'replace' => $luss['substitute'], 'seqIndex' => $seqIndex, 'key' => $lookupGlyphs[0], );
										}
									}
								   }
								}
							}
							if (count($subRule['rules'])) $volt[] = $subRule;
						}

//print_r($Lookup[$i]);
//print_r($volt[(count($volt)-1)]); exit;
					}
					// LookupType 6: ing Contextual Substitution Subtable
					else if ($Lookup[$i]['Type'] == 6) {
						// Format 1: Simple Chaining Context Glyph Substitution  p255
						if ($SubstFormat==1) {	
							$ignore = $this->_getGSUBignoreString($Lookup[$i]['Flag'], $Lookup[$i]['MarkFilteringSet']);
							for($s=0;$s<$Lookup[$i]['Subtable'][$c]['ChainSubRuleSetCount'];$s++) {
								// ChainSubRuleSet
								$subRule = array();
								$firstInputGlyph = $Lookup[$i]['Subtable'][$c]['CoverageGlyphs'][$s];	// First input gyyph
								foreach($Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'] AS $rule) {
									// ChainSubRule
									$inputGlyphs = array(); 
									if ($rule['InputGlyphCount']>1) { 
										$inputGlyphs = $rule['InputGlyphs']; 
									}
									$inputGlyphs[0] = $firstInputGlyph;
									ksort($inputGlyphs);
									$nInput = count($inputGlyphs);

									if ($rule['BacktrackGlyphCount']) { $backtrackGlyphs = $rule['BacktrackGlyphs']; }
									else { $backtrackGlyphs = array(); }
									$backtrackMatch = $this->_makeGSUBbacktrackMatch($backtrackGlyphs, $ignore);

									if ($rule['LookaheadGlyphCount']) { $lookaheadGlyphs = $rule['LookaheadGlyphs']; }
									else { $lookaheadGlyphs = array(); }

									$lookaheadMatch = $this->_makeGSUBlookaheadMatch($lookaheadGlyphs, $ignore);

									$nBsubs = 2*count($backtrackGlyphs);
									$nIsubs = (2*$nInput)-1;

									$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, array(), 0);
									$subRule = array('context' => 1, 'tag' => $tag, 'matchback' => $backtrackMatch, 'match' => ($contextInputMatch . $lookaheadMatch), 'nBacktrack' => count($backtrackGlyphs), 'nInput' => $nInput, 'nLookahead' => count($lookaheadGlyphs), 'rules' => array(), );


									for ($b=0;$b<$rule['SubstCount'];$b++) {
										$lup = $rule['LookupListIndex'][$b];
										$seqIndex = $rule['SequenceIndex'][$b];

										// $Lookup[$lup] = secondary Lookup
										for($lus=0;$lus<$Lookup[$lup]['SubtableCount'];$lus++) {
										   if (count($Lookup[$lup]['Subtable'][$lus]['subs'])) {
											foreach($Lookup[$lup]['Subtable'][$lus]['subs'] AS $luss) {

												$lookupGlyphs = $luss['Replace'];
												$mLen = count($lookupGlyphs);

												// Only apply if the (first) 'Replace' glyph from the 
												// Lookup list is in the [inputGlyphs] at ['SequenceIndex']
												// then apply the substitution
												if (strpos($inputGlyphs[$seqIndex],$lookupGlyphs[0])===false) { continue; }

												// Returns e.g. ¦(0612)¦(ignore) (0613)¦(ignore) (0614)¦
												$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, $lookupGlyphs, $seqIndex);

												$REPL = implode(" ",$luss['substitute']);

												if (strpos("isol fina fin2 fin3 medi med2 init ",$tag)!==false && $scripttag=='arab') {
													$volt[] = array('match'=>$lookupGlyphs[0], 'replace'=>$REPL, 'tag'=>$tag, 'prel'=>$backtrackGlyphs, 'postl'=>$lookaheadGlyphs, 'ignore'=>$ignore);
												}
												else {
													$subRule['rules'][] = array('type' => $Lookup[$lup]['Type'], 'match' => $lookupGlyphs, 'replace' => $luss['substitute'], 'seqIndex' => $seqIndex, 'key' => $lookupGlyphs[0], );
												}
											}
										   }
										}
									}


									if (count($subRule['rules'])) $volt[] = $subRule;



								}
							}

						}
						// Format 2: Class-based Chaining Context Glyph Substitution  p257
						else if ($SubstFormat==2) {	
							$ignore = $this->_getGSUBignoreString($Lookup[$i]['Flag'], $Lookup[$i]['MarkFilteringSet']);
							foreach($Lookup[$i]['Subtable'][$c]['ChainSubClassSet'] AS $inputClass=>$cscs) {
								for($cscrule=0;$cscrule<$cscs['ChainSubClassRuleCnt'];$cscrule++) {
									$rule = $cscs['ChainSubClassRule'][$cscrule];

									// These contain classes of glyphs as strings
									// $Lookup[$i]['Subtable'][$c]['InputClasses'][(class)] e.g. 02E6|02E7|02E8
									// $Lookup[$i]['Subtable'][$c]['LookaheadClasses'][(class)]
									// $Lookup[$i]['Subtable'][$c]['BacktrackClasses'][(class)]

									// These contain arrays of classIndexes
									// [Backtrack] [Lookahead] and [Input] (Input is from the second position only)

									$inputGlyphs = array();

									if (isset($Lookup[$i]['Subtable'][$c]['InputClasses'][$inputClass])) {
										$inputGlyphs[0] = $Lookup[$i]['Subtable'][$c]['InputClasses'][$inputClass];
									}
									else { $inputGlyphs[0] = ''; }
									if ($rule['InputGlyphCount']>1) {
										//  NB starts at 1 
										for ($gcl=1;$gcl<$rule['InputGlyphCount'];$gcl++) {
											$classindex = $rule['Input'][$gcl];
											if (isset($Lookup[$i]['Subtable'][$c]['InputClasses'][$classindex])) {
												$inputGlyphs[$gcl] = $Lookup[$i]['Subtable'][$c]['InputClasses'][$classindex];
											}
											// if class[0] = all glyphs excluding those specified in all other classes
											// set to blank '' for now
											else { $inputGlyphs[$gcl] = ''; }
										}
									}

									$nInput = $rule['InputGlyphCount'];

									if ($rule['BacktrackGlyphCount']) {
										for ($gcl=0;$gcl<$rule['BacktrackGlyphCount'];$gcl++) {
											$classindex = $rule['Backtrack'][$gcl];
											if (isset($Lookup[$i]['Subtable'][$c]['BacktrackClasses'][$classindex])) {
												$backtrackGlyphs[$gcl] = $Lookup[$i]['Subtable'][$c]['BacktrackClasses'][$classindex];
											}
											// if class[0] = all glyphs excluding those specified in all other classes
											// set to blank '' for now
											else { $backtrackGlyphs[$gcl] = ''; }
										}
									}
									else { $backtrackGlyphs = array(); }
									// Returns e.g. ¦(FEEB|FEEC)(ignore) ¦(FD12|FD13)(ignore) ¦
									$backtrackMatch = $this->_makeGSUBbacktrackMatch($backtrackGlyphs, $ignore);

									if ($rule['LookaheadGlyphCount']) {
										for ($gcl=0;$gcl<$rule['LookaheadGlyphCount'];$gcl++) {
											$classindex = $rule['Lookahead'][$gcl];
											if (isset($Lookup[$i]['Subtable'][$c]['LookaheadClasses'][$classindex])) {
												$lookaheadGlyphs[$gcl] = $Lookup[$i]['Subtable'][$c]['LookaheadClasses'][$classindex];
											}
											// if class[0] = all glyphs excluding those specified in all other classes
											// set to blank '' for now
											else { $lookaheadGlyphs[$gcl] = ''; }
										}
									}
									else { $lookaheadGlyphs = array(); }
									// Returns e.g. ¦(ignore) (FD12|FD13)¦(ignore) (FEEB|FEEC)¦
									$lookaheadMatch = $this->_makeGSUBlookaheadMatch($lookaheadGlyphs, $ignore);

									$nBsubs = 2*count($backtrackGlyphs);
									$nIsubs = (2*$nInput)-1;

									$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, array(), 0);
									$subRule = array('context' => 1, 'tag' => $tag, 'matchback' => $backtrackMatch, 'match' => ($contextInputMatch . $lookaheadMatch), 'nBacktrack' => count($backtrackGlyphs), 'nInput' => $nInput, 'nLookahead' => count($lookaheadGlyphs), 'rules' => array(), );

									for ($b=0;$b<$rule['SubstCount'];$b++) {
										$lup = $rule['LookupListIndex'][$b];
										$seqIndex = $rule['SequenceIndex'][$b];

										// $Lookup[$lup] = secondary Lookup
										for($lus=0;$lus<$Lookup[$lup]['SubtableCount'];$lus++) {
										   if (count($Lookup[$lup]['Subtable'][$lus]['subs'])) {
											foreach($Lookup[$lup]['Subtable'][$lus]['subs'] AS $luss) {

												$lookupGlyphs = $luss['Replace'];
												$mLen = count($lookupGlyphs);

												// Only apply if the (first) 'Replace' glyph from the 
												// Lookup list is in the [inputGlyphs] at ['SequenceIndex']
												// then apply the substitution
												if (strpos($inputGlyphs[$seqIndex],$lookupGlyphs[0])===false) { continue; }

												// Returns e.g. ¦(0612)¦(ignore) (0613)¦(ignore) (0614)¦
												$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, $lookupGlyphs, $seqIndex);
												$REPL = implode(" ",$luss['substitute']);
												// Returns e.g. "REPL\${6}\${8}" or "\${1}\${2} \${3} REPL\${4}\${6}\${8} \${9}"

												if (strpos("isol fina fin2 fin3 medi med2 init ",$tag)!==false && $scripttag=='arab') {
													$volt[] = array('match'=>$lookupGlyphs[0], 'replace'=>$REPL, 'tag'=>$tag, 'prel'=>$backtrackGlyphs, 'postl'=>$lookaheadGlyphs, 'ignore'=>$ignore);
												}
												else {
													$subRule['rules'][] = array('type' => $Lookup[$lup]['Type'], 'match' => $lookupGlyphs, 'replace' => $luss['substitute'], 'seqIndex' => $seqIndex, 'key' => $lookupGlyphs[0], );
												}
											}
										   }
										}
									}
									if (count($subRule['rules'])) $volt[] = $subRule;

								}
							}


//print_r($Lookup[$i]['Subtable'][$c]); exit;	

						}
						// Format 3: Coverage-based Chaining Context Glyph Substitution  p259
						else if ($SubstFormat==3) {
							// IgnoreMarks flag set on main Lookup table
							$ignore = $this->_getGSUBignoreString($Lookup[$i]['Flag'], $Lookup[$i]['MarkFilteringSet']);
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['CoverageInputGlyphs'];
							$CoverageInputGlyphs = implode('|', $inputGlyphs);
							$nInput = $Lookup[$i]['Subtable'][$c]['InputGlyphCount'];

							if ($Lookup[$i]['Subtable'][$c]['BacktrackGlyphCount']) {
								$backtrackGlyphs = $Lookup[$i]['Subtable'][$c]['CoverageBacktrackGlyphs'];
							}
							else { $backtrackGlyphs = array(); }
							// Returns e.g. ¦(FEEB|FEEC)(ignore) ¦(FD12|FD13)(ignore) ¦
							$backtrackMatch = $this->_makeGSUBbacktrackMatch($backtrackGlyphs, $ignore);

							if ($Lookup[$i]['Subtable'][$c]['LookaheadGlyphCount']) {
								$lookaheadGlyphs = $Lookup[$i]['Subtable'][$c]['CoverageLookaheadGlyphs'];
							}
							else { $lookaheadGlyphs = array(); }
							// Returns e.g. ¦(ignore) (FD12|FD13)¦(ignore) (FEEB|FEEC)¦
							$lookaheadMatch = $this->_makeGSUBlookaheadMatch($lookaheadGlyphs, $ignore);

							$nBsubs = 2*count($backtrackGlyphs);
							$nIsubs = (2*$nInput)-1;
							$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, array(), 0);
							$subRule = array('context' => 1, 'tag' => $tag, 'matchback' => $backtrackMatch, 'match' => ($contextInputMatch . $lookaheadMatch), 'nBacktrack' => count($backtrackGlyphs), 'nInput' => $nInput, 'nLookahead' => count($lookaheadGlyphs), 'rules' => array(), );

							for ($b=0;$b<$Lookup[$i]['Subtable'][$c]['SubstCount'];$b++) {
								$lup = $Lookup[$i]['Subtable'][$c]['SubstLookupRecord'][$b]['LookupListIndex'];
								$seqIndex = $Lookup[$i]['Subtable'][$c]['SubstLookupRecord'][$b]['SequenceIndex'];
								for($lus=0;$lus<$Lookup[$lup]['SubtableCount'];$lus++) {
								   if (count($Lookup[$lup]['Subtable'][$lus]['subs'])) {
									foreach($Lookup[$lup]['Subtable'][$lus]['subs'] AS $luss) {
										$lookupGlyphs = $luss['Replace'];
										$mLen = count($lookupGlyphs);

										// Only apply if the (first) 'Replace' glyph from the 
										// Lookup list is in the [inputGlyphs] at ['SequenceIndex']
										// then apply the substitution
										if (strpos($inputGlyphs[$seqIndex],$lookupGlyphs[0])===false) { continue; }

										// Returns e.g. ¦(0612)¦(ignore) (0613)¦(ignore) (0614)¦
										$contextInputMatch = $this->_makeGSUBcontextInputMatch($inputGlyphs, $ignore, $lookupGlyphs, $seqIndex);
										$REPL = implode(" ",$luss['substitute']);

										if (strpos("isol fina fin2 fin3 medi med2 init ",$tag)!==false && $scripttag=='arab') {
											$volt[] = array('match'=>$lookupGlyphs[0], 'replace'=>$REPL, 'tag'=>$tag, 'prel'=>$backtrackGlyphs, 'postl'=>$lookaheadGlyphs, 'ignore'=>$ignore);
										}
										else {
											$subRule['rules'][] = array('type' => $Lookup[$lup]['Type'], 'match' => $lookupGlyphs, 'replace' => $luss['substitute'], 'seqIndex' => $seqIndex, 'key' => $lookupGlyphs[0], );
										}
									}
								   }
								}
							}
							if (count($subRule['rules'])) $volt[] = $subRule;
						}
					}
				}
			}
//print_r($Lookup); exit;
			return $volt;
	}
	//=====================================================================================
	//=====================================================================================
	// mPDF 5.7.1
	function _checkGSUBignore($flag, $glyph, $MarkFilteringSet) {
		$ignore = false;
		// Flag & 0x0008 = Ignore Marks - (unless already done with MarkAttachmentType)
		if ((($flag & 0x0008) == 0x0008 && ($flag & 0xFF00) == 0) && strpos($this->GlyphClassMarks,$glyph)) { $ignore = true; }
		if ((($flag & 0x0004) == 0x0004) && strpos($this->GlyphClassLigatures,$glyph)) { $ignore = true; }
		if ((($flag & 0x0002) == 0x0002) && strpos($this->GlyphClassBases,$glyph)) { $ignore = true; }
		// Flag & 0xFF?? = MarkAttachmentType
		if ($flag & 0xFF00) {
			// "a lookup must ignore any mark glyphs that are not in the specified mark attachment class"
			// $this->MarkAttachmentType is already adjusted for this i.e. contains all Marks except those in the MarkAttachmentClassDef table
			if (strpos($this->MarkAttachmentType[($flag >> 8)],$glyph)) { $ignore = true; }
		}
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
			// "a lookup must ignore any mark glyphs that are not in the specified mark attachment class"
			// $this->MarkAttachmentType is already adjusted for this i.e. contains all Marks except those in the MarkAttachmentClassDef table
			$MarkAttachmentType = $flag >> 8;
			$ignoreflag = $flag; 
			$str = $this->MarkAttachmentType[$MarkAttachmentType]; 
		}

		// Flag & 0x0010 = UseMarkFilteringSet
		if ($flag & 0x0010) {
			die("This font ".$this->fontkey." contains MarkGlyphSets - Not tested yet"); 
			$str = $this->MarkGlyphSets[$MarkFilteringSet]; 
		}

		// If Ignore Marks set, supercedes any above
		// Flag & 0x0008 = Ignore Marks - (unless already done with MarkAttachmentType)
		if (($flag & 0x0008) == 0x0008 && ($flag & 0xFF00) == 0) { 
			$ignoreflag = 8; 
			$str = $this->GlyphClassMarks; 
		}

		// Flag & 0x0004 = Ignore Ligatures  
		if (($flag & 0x0004) == 0x0004) {
			$ignoreflag += 4; 
			if ($str) { $str .= "|"; }
			$str .= $this->GlyphClassLigatures; 
		}
		// Flag & 0x0002 = Ignore BaseGlyphs  
		if (($flag & 0x0002) == 0x0002) {
			$ignoreflag += 2; 
			if ($str) { $str .= "|"; }
			$str .= $this->GlyphClassBases; 
		}
		if ($str) { 
			// This originally returned e.g. ((?:(?:[IGNORE8]))*) when NOT specific to a Lookup e.g. rtlSub in 
			// arabictypesetting.GSUB.arab.DFLT.php
			// This would save repeatedly saving long text strings if used multiple times
			// When writing e.g. arabictypesetting.GSUB.arab.DFLT.php to file, included as $ignore[8]
			// Would need to also write the $ignore array to that file
	//		// If UseMarkFilteringSet (specific to the Lookup) return the string
	//		if (($flag & 0x0010) && ($flag & 0x0008) != 0x0008) { 
	//			return "((?:(?:" . $str . "))*)"; 
	//		}
	//		else { return "((?:(?:" . "[IGNORE".$ignoreflag."]" . "))*)"; }
	//		// e.g. ((?:(?: 0031| 0032| 0033| 0034| 0045))*)

			// But never finished coding it to add the $ignore array to the file, and it doesn't seem to occur often enough to be worth
			// writing. So just output it as a string:
			return "((?:(?:" . $str . "))*)"; 

		}
		else return "()";
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
                                   F======================  SequenceIndex=4 ; Lookup match nGlyphs=2

All backreference numbers are + nBsubs
A - "REPL\${2} \${3}\${4} \${5}\${6} \${7}\${8} \${9}"
B - "REPL\${2}\${4} \${5}\${6} \${7}\${8} \${9}"
C - "REPL\${2}\${4}\${6} \${7}\${8} \${9}"
D - "\${1} REPL\${2}\${4}\${6} \${7}\${8} \${9}"
E - "\${1} REPL\${2}\${4}\${6}\${8} \${9}"
F - "\${1}\${2} \${3}\${4} \${5} REPL\${6}\${8}"
*/

	function _makeGSUBcontextInputMatch($inputGlyphs, $ignore, $lookupGlyphs, $seqIndex) {
		// $ignore = "((?:(?: FBA1| FBA2| FBA3))*)" or "()"
		// Returns e.g. ¦(0612)¦(ignore) (0613)¦(ignore) (0614)¦
		// $inputGlyphs = array of glyphs(glyphstrings) making up Input sequence in Context
		// $lookupGlyphs = array of glyphs (single Glyphs) making up Lookup Input sequence
		$mLen = count($lookupGlyphs);		// nGlyphs in the secondary Lookup match 
		$nInput = count($inputGlyphs);	// nGlyphs in the Primary Input sequence
		$str = "";
		for($i=0;$i<$nInput;$i++) {
			if ($i>0) { $str .= $ignore." "; }
			if ($i>=$seqIndex && $i<($seqIndex+$mLen)) { $str .= "(".$lookupGlyphs[($i-$seqIndex)].")"; }
			else { $str .= "(".$inputGlyphs[($i)].")"; }
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
			$str .= "(".$inputGlyphs[($i-1)].")";
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
			$str .= "(".$backtrackGlyphs[$i].")".$ignore." ";
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
			$str .= $ignore." (".$lookaheadGlyphs[$i].")";
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
				      if ($x==1) { $str .= '\\'.($nBsubs + 1); }
				      else { 
						if ($ign) { $str .= '\\'.($nBsubs+(2*($x-1))); }
						$str .= ' \\'.($nBsubs+1+(2*($x-1))); 
					}
				}
				if ($seqIndex>0) { $str .= " "; }
				$str .= $REPL;
				if ($ign) {
					for($x=(max(($seqIndex+1),2));$x<($seqIndex+1+$mLen);$x++) {	//  move IGNORES after replacement
					      $str .= '\\'.($nBsubs+(2*($x-1)));
					}
				}
				for($x=($seqIndex+1+$mLen);$x<=$nInput;$x++) {
				      if ($ign) { $str .= '\\'.($nBsubs+(2*($x-1))); }
				      $str .= ' \\'.($nBsubs+1+(2*($x-1)));
				}
			}
		}
		return $str;
	}



	//////////////////////////////////////////////////////////////////////////////////
	function _getCoverage($convert2hex=true, $mode=1) {
		$g = array();
		$ctr = 0;
		$CoverageFormat= $this->read_ushort();
		if ($CoverageFormat == 1) {
			$CoverageGlyphCount= $this->read_ushort();
			for ($gid=0;$gid<$CoverageGlyphCount;$gid++) {
				$glyphID = $this->read_ushort();
				$uni = $this->glyphToChar[$glyphID][0];
				if ($convert2hex) { $g[] = unicode_hex($uni); }
				else if ($mode==2) { $g[$uni] = $ctr; $ctr++; }
				else { $g[] = $glyphID; }
			}
		}						
		if ($CoverageFormat == 2) {
			$RangeCount= $this->read_ushort();
			for ($r=0;$r<$RangeCount;$r++) {
				$start = $this->read_ushort();
				$end = $this->read_ushort();
				$StartCoverageIndex = $this->read_ushort(); // n/a
				for ($glyphID=$start;$glyphID<=$end;$glyphID++) {
					$uni = $this->glyphToChar[$glyphID][0];
					if ($convert2hex) { $g[] = unicode_hex($uni); }
					else if ($mode==2) { $uni = $g[$uni] = $ctr; $ctr++; }
					else { $g[] = $glyphID; }
				}
			}
		}
		return $g;						
	}

	//////////////////////////////////////////////////////////////////////////////////
	function _getClasses($offset) {
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
				for($g=$startGlyphID;$g<=$endGlyphID;$g++) {
					if (isset($this->glyphToChar[$g][0])) {
						$GlyphByClass[$class][] = unicode_hex($this->glyphToChar[$g][0]);
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
				for($g=$startGlyphID;$g<=$endGlyphID;$g++) {
					if ($this->glyphToChar[$g][0]) {
						$GlyphByClass[$class][] = unicode_hex($this->glyphToChar[$g][0]);
					}
				}
			}
		}
		$gbc = array();
		foreach($GlyphByClass AS $class=>$garr) { $gbc[$class] = implode('|',$garr); }
		return $gbc;
	}
	//////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////
	function _getGPOStables() {
		///////////////////////////////////
		// GPOS - Glyph Positioning
		///////////////////////////////////
		if (isset($this->tables["GPOS"])) { 
			$ffeats = array();
			$gpos_offset = $this->seek_table("GPOS");
			$this->skip(4);
			$ScriptList_offset = $gpos_offset + $this->read_ushort();
			$FeatureList_offset = $gpos_offset + $this->read_ushort();
			$LookupList_offset = $gpos_offset + $this->read_ushort();

			// ScriptList
			$this->seek($ScriptList_offset );
			$ScriptCount = $this->read_ushort();
			for ($i=0;$i<$ScriptCount;$i++) {
					$ScriptTag = $this->read_tag();	// = "beng", "deva" etc.
					$ScriptTableOffset = $this->read_ushort();
					$ffeats[$ScriptTag] = $ScriptList_offset + $ScriptTableOffset;
			}

			// Script Table
			foreach($ffeats AS $t=>$o) {
				$ls = array();
				$this->seek($o);
				$DefLangSys_offset = $this->read_ushort();
				if ($DefLangSys_offset > 0) {
					$ls['DFLT'] = $DefLangSys_offset + $o;
				}
				$LangSysCount = $this->read_ushort();
				for ($i=0;$i<$LangSysCount;$i++) {
					$LangTag = $this->read_tag();	// = 
					$LangTableOffset = $this->read_ushort();
					$ls[$LangTag] = $o + $LangTableOffset;
				}
				$ffeats[$t] = $ls;
			}


			// Get FeatureIndexList
			// LangSys Table - from first listed langsys
			foreach($ffeats AS $st=>$scripts) {
				foreach($scripts AS $t=>$o) {
					$FeatureIndex = array();
					$langsystable_offset = $o;
					$this->seek($langsystable_offset);
					$LookUpOrder = $this->read_ushort();	//==NULL
					$ReqFeatureIndex = $this->read_ushort();
					if ($ReqFeatureIndex != 0xFFFF) { $FeatureIndex[] = $ReqFeatureIndex ; }
					$FeatureCount = $this->read_ushort();
					for ($i=0;$i<$FeatureCount;$i++) {
							$FeatureIndex[] = $this->read_ushort();	// = index of feature
					}
					$ffeats[$st][$t] = $FeatureIndex; 
				}
			}
//print_r($ffeats); exit;


			// Feauture List => LookupListIndex es
			$this->seek($FeatureList_offset );
			$FeatureCount = $this->read_ushort();
			$Feature = array();
			for ($i=0;$i<$FeatureCount;$i++) {
				$tag = $this->read_tag() ;
				if ($tag == 'kern') { $this->haskernGPOS = true; }
				$Feature[$i] = array('tag' => $tag);
				$Feature[$i]['offset'] = $FeatureList_offset + $this->read_ushort();
			}
			for ($i=0;$i<$FeatureCount;$i++) {
				$this->seek($Feature[$i]['offset']);
				$this->read_ushort(); // null
				$Feature[$i]['LookupCount'] = $Lookupcount = $this->read_ushort();
				$Feature[$i]['LookupListIndex'] = array();
				for ($c=0;$c<$Lookupcount;$c++) {
					$Feature[$i]['LookupListIndex'][] = $this->read_ushort();
				}
			}


			foreach($ffeats AS $st=>$scripts) {
				foreach($scripts AS $t=>$o) {
					$FeatureIndex = $ffeats[$st][$t];
					foreach($FeatureIndex AS $k=>$fi) {
						$ffeats[$st][$t][$k] = $Feature[$fi];
					}
				}
			}
//print_r($ffeats); exit;
			//=====================================================================================
			$gpos = array();
			$GPOSScriptLang = array();
			foreach($ffeats AS $st=>$scripts) {
				foreach($scripts AS $t=>$langsys) {
					$lg = array();
					foreach($langsys AS $ft) {
						$lg[$ft['LookupListIndex'][0]] = $ft;
					}
					// list of Lookups in order they need to be run i.e. order listed in Lookup table
					ksort($lg);
					foreach($lg AS $ft) {
						$gpos[$st][$t][$ft['tag']] = $ft['LookupListIndex'];
					}
					if (!isset($GPOSScriptLang[$st])) { $GPOSScriptLang[$st] = ''; }
					$GPOSScriptLang[$st] .= $t.' ';
				}
			}

			//=====================================================================================
			// Get metadata and offsets for whole Lookup List table
			$this->seek($LookupList_offset );
			$LookupCount = $this->read_ushort();
			$Lookup = array();
			$Offsets = array();
			$SubtableCount = array();
			for ($i=0;$i<$LookupCount;$i++) {
				$Offsets[$i] = $LookupList_offset + $this->read_ushort();
			}
			for ($i=0;$i<$LookupCount;$i++) {
				$this->seek($Offsets[$i]);
				$Lookup[$i]['Type'] = $this->read_ushort();
				$Lookup[$i]['Flag'] = $flag = $this->read_ushort();
				$Lookup[$i]['SubtableCount'] = $SubtableCount[$i] = $this->read_ushort();
				for ($c=0;$c<$SubtableCount[$i] ;$c++) {
					$Lookup[$i]['Subtables'][$c] = $Offsets[$i] + $this->read_ushort();

				}
				// MarkFilteringSet = Index (base 0) into GDEF mark glyph sets structure
				if (($flag & 0x0010) == 0x0010) {
					$Lookup[$i]['MarkFilteringSet'] = $this->read_ushort();
				}
				else { $Lookup[$i]['MarkFilteringSet'] = ''; }

				// Lookup Type 9: Extension
				if ($Lookup[$i]['Type'] == 9) {
					// Overwrites new offset (32-bit) for each subtable, and a new lookup Type
					for ($c=0;$c<$SubtableCount[$i] ;$c++) {
						$this->seek($Lookup[$i]['Subtables'][$c]);
						$ExtensionPosFormat = $this->read_ushort();
						$type = $this->read_ushort();
						$Lookup[$i]['Subtables'][$c] = $Lookup[$i]['Subtables'][$c] + $this->read_ulong();
					}
					$Lookup[$i]['Type'] = $type;
				}

			}


			//=====================================================================================
			// Process Whole LookupList - Get LuCoverage = Lookup coverage just for first glyph
			$this->LuCoverage = array();
			for ($i=0;$i<$LookupCount;$i++) {
				for ($c=0;$c<$Lookup[$i]['SubtableCount'] ;$c++) {

					$this->seek($Lookup[$i]['Subtables'][$c]);
					$PosFormat= $this->read_ushort();

					if ($Lookup[$i]['Type']==7 && $PosFormat==3) { $this->skip(4); }
					else if ($Lookup[$i]['Type']==8 && $PosFormat==3) {
						$BacktrackGlyphCount= $this->read_ushort();
						$this->skip(2*$BacktrackGlyphCount + 2); 
					}
					// NB Coverage only looks at glyphs for position 1 (i.e. 7.3 and 8.3)	// NEEDS TO READ ALL ********************
					// NB For e.g. Type 4, this may be the Coverage for the Mark
					$Coverage = $Lookup[$i]['Subtables'][$c] + $this->read_ushort();
					$this->seek($Coverage);
					$glyphs = $this->_getCoverage(false,2);
					$this->LuCoverage[$i][$c] = $glyphs;
				}
			}



			//=====================================================================================



//print_r($GPOSScriptLang); exit;
//print_r($gpos); exit;
//print_r($Lookup); exit;




			$s = '<?php
$LuCoverage = '.var_export($this->LuCoverage , true).';
?>';


			file_put_contents(_MPDF_TTFONTDATAPATH.$this->fontkey.'.GPOSdata.php', $s);



			return array($GPOSScriptLang, $gpos, $Lookup);

		}	// end if GPOS
	}

	//////////////////////////////////////////////////////////////////////////////////

			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
			//=====================================================================================

	function makeSubset($file, &$subset, $TTCfontID=0, $debug=false, $useOTL=false) {	// mPDF 5.7.1
		$this->useOTL = $useOTL;	// mPDF 5.7.1
		$this->filename = $file;
		$this->fh = fopen($file ,'rb') or die('Can\'t open file ' . $file);
		$this->_pos = 0;
		$this->charWidths = '';
		$this->glyphPos = array();
		$this->charToGlyph = array();
		$this->tables = array();
		$this->otables = array();
		$this->ascent = 0;
		$this->descent = 0;
		$this->strikeoutSize = 0;
		$this->strikeoutPosition = 0;
		$this->numTTCFonts = 0;
		$this->TTCFonts = array();
		$this->skip(4);
		$this->maxUni = 0;
		if ($TTCfontID > 0) {
			$this->version = $version = $this->read_ulong();	// TTC Header version now
			if (!in_array($version, array(0x00010000,0x00020000)))
				die("ERROR - Error parsing TrueType Collection: version=".$version." - " . $file);
			$this->numTTCFonts = $this->read_ulong();
			for ($i=1; $i<=$this->numTTCFonts; $i++) {
	      	      $this->TTCFonts[$i]['offset'] = $this->read_ulong();
			}
			$this->seek($this->TTCFonts[$TTCfontID]['offset']);
			$this->version = $version = $this->read_ulong();	// TTFont version again now
		}
		$this->readTableDirectory($debug);

		///////////////////////////////////
		// head - Font header table
		///////////////////////////////////
		$this->seek_table("head");
		$this->skip(50); 
		$indexToLocFormat = $this->read_ushort();
		$glyphDataFormat = $this->read_ushort();

		///////////////////////////////////
		// hhea - Horizontal header table
		///////////////////////////////////
		$this->seek_table("hhea");
		$this->skip(32); 
		$metricDataFormat = $this->read_ushort();
		$orignHmetrics = $numberOfHMetrics = $this->read_ushort();

		///////////////////////////////////
		// maxp - Maximum profile table
		///////////////////////////////////
		$this->seek_table("maxp");
		$this->skip(4);
		$numGlyphs = $this->read_ushort();


		///////////////////////////////////
		// cmap - Character to glyph index mapping table
		///////////////////////////////////
		$cmap_offset = $this->seek_table("cmap");
		$this->skip(2);
		$cmapTableCount = $this->read_ushort();
		$unicode_cmap_offset = 0;
		for ($i=0;$i<$cmapTableCount;$i++) {
			$platformID = $this->read_ushort();
			$encodingID = $this->read_ushort();
			$offset = $this->read_ulong();
			$save_pos = $this->_pos;
			if (($platformID == 3 && $encodingID == 1) || $platformID == 0) { // Microsoft, Unicode
				$format = $this->get_ushort($cmap_offset + $offset);
				if ($format == 4) {
					$unicode_cmap_offset = $cmap_offset + $offset;
					break;
				}
			}
			$this->seek($save_pos );
		}

		if (!$unicode_cmap_offset)
			die('Font ('.$this->filename .') does not have Unicode cmap (platform 3, encoding 1, format 4, or platform 0 [any encoding] format 4)');


		$glyphToChar = array();
		$charToGlyph = array();
		$this->getCMAP4($unicode_cmap_offset, $glyphToChar, $charToGlyph );

		///////////////////////////////////
		// mPDF 5.7.1
		// Map Unmapped glyphs - from $numGlyphs
		if ($useOTL) {
			$bctr = 0xE000;
			for ($gid=1; $gid<$numGlyphs; $gid++) {
				if (!isset($glyphToChar[$gid])) {
					while(isset($charToGlyph[$bctr])) { $bctr++; }	// Avoid overwriting a glyph already mapped in PUA
					if ($bctr > 0xF8FF) {
						die($file." : WARNING - Font cannot map all included glyphs into Private Use Area U+E000 - U+F8FF; cannot use useOTL on this font"); 
					}
					$glyphToChar[$gid][] = $bctr;
					$charToGlyph[$bctr] = $gid;
					$bctr++;
				}
			}
		}
		///////////////////////////////////

		$this->charToGlyph = $charToGlyph;
		$this->glyphToChar = $glyphToChar;


		///////////////////////////////////
		// hmtx - Horizontal metrics table
		///////////////////////////////////
		$scale = 1;	// not used
		$this->getHMTX($numberOfHMetrics, $numGlyphs, $glyphToChar, $scale);

		///////////////////////////////////
		// loca - Index to location
		///////////////////////////////////
		$this->getLOCA($indexToLocFormat, $numGlyphs);

		$subsetglyphs = array(0=>0, 1=>1, 2=>2);
		$subsetCharToGlyph = array();
		foreach($subset AS $code) {
			if (isset($this->charToGlyph[$code])) {
				$subsetglyphs[$this->charToGlyph[$code]] = $code;	// Old Glyph ID => Unicode
				$subsetCharToGlyph[$code] = $this->charToGlyph[$code];	// Unicode to old GlyphID

			}
			$this->maxUni = max($this->maxUni, $code);
		}

		list($start,$dummy) = $this->get_table_pos('glyf');

		$glyphSet = array();
		ksort($subsetglyphs);
		$n = 0;
		$fsLastCharIndex = 0;	// maximum Unicode index (character code) in this font, according to the cmap subtable for platform ID 3 and 