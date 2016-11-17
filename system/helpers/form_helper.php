][$c]['SubClassSetCnt'];$s++) {
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
								$SubClassRuleCnt = $Lookup[$i]['Subtable'][$c]['SubClassSet'][$s]['SubClassRuleCnt'];
								for($b=0;$b<$SubClassRuleCnt;$b++) {
								   if ($Lookup[$i]['Subtable'][$c]['SubClassSetOffset'][$s]>0) {
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
								$ChainSubClassRuleCnt = $Lookup[$i]['Subtable'][$c]['ChainSubClassSet'][$s]['ChainSubClassRuleCnt'];
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



			$st = $this->mpdf->OTLscript;
			$t = $this->mpdf->OTLlang;
			$langsys = $gsub[$st][$t];


			$lul = array();	// array of LookupListIndexes
			$tags = array();	// corresponding array of feature tags e.g. 'ccmp'
			foreach($langsys AS $tag=>$ft) {
				foreach($ft AS $ll) { 
					$lul[$ll] = $tag; 
				}
			}
			ksort($lul);	// Order the Lookups in the order they are in the GUSB table, regardless of Feature order
			$this->_getGSUBarray($Lookup, $lul, $st);
//print_r($lul); exit;




		}
//print_r($Lookup); exit;

		return array($GSUBScriptLang, $gsub, $GSLookup, $rtlPUAstr, $rtlPUAarr);

	}
/////////////////////////////////////////////////////////////////////////////////////////
	// GSUB functions
	function _getGSUBarray(&$Lookup, &$lul, $scripttag, $level=1, $coverage='', $exB='', $exL='') {
			// Process (3) LookupList for specific Script-LangSys
			// Generate preg_replace
			$html = '';
			if ($level==1) { $html .= '<bookmark level="0" content="GSUB features">'; }
			foreach($lul AS $i=>$tag) {
				$html .= '<div class="level'.$level.'">'; 
				$html .= '<h5 class="level'.$level.'">';
				if ($level==1) { $html .= '<bookmark level="1" content="'.$tag.' [#'.$i.']">'; }
				$html .= 'Lookup #'.$i.' [tag: <span style="color:#000066;">'.$tag.'</span>]</h5>'; 
				$ignore = $this->_getGSUBignoreString($Lookup[$i]['Flag'], $Lookup[$i]['MarkFilteringSet']);
				if ($ignore) { $html .= '<div class="ignore">Ignoring: '.$ignore.'</div> '; }

				$Type = $Lookup[$i]['Type'];
				$Flag = $Lookup[$i]['Flag'];
				if (($Flag  & 0x0001) == 1) { $dir = 'RTL'; }
				else { $dir = 'LTR'; }

				for ($c=0;$c<$Lookup[$i]['SubtableCount'] ;$c++) {
					$html .= '<div class="subtable">Subtable #'.$c;
					if ($level==1) { $html .= '<bookmark level="2" content="Subtable #'.$c.'">'; }
					$html .= '</div>'; 

					$SubstFormat= $Lookup[$i]['Subtable'][$c]['Format'] ;

					// LookupType 1: Single Substitution Subtable
					if ($Lookup[$i]['Type'] == 1) {
						$html .= '<div class="lookuptype">LookupType 1: Single Substitution Subtable</div>'; 
						for ($s=0;$s<count($Lookup[$i]['Subtable'][$c]['subs']);$s++) {
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['subs'][$s]['Replace'];
							$substitute = $Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute'][0];
							if ($level==2 && strpos($coverage, $inputGlyphs[0])===false) { continue; }
							$html .= '<div class="substitution">';
							$html .= '<span class="unicode">'.$this->formatUni($inputGlyphs[0]).'&nbsp;</span> ';
							if ($level==2 && $exB) { $html .= $exB; }
							$html .= '<span class="unchanged">&nbsp;'.$this->formatEntity($inputGlyphs[0]).'</span>';
							if ($level==2 && $exL) { $html .= $exL; }
							$html .= '&nbsp; &raquo; &raquo; &nbsp;';
							if ($level==2 && $exB) { $html .= $exB; }
							$html .= '<span class="changed">&nbsp;'.$this->formatEntity($substitute).'</span>';
							if ($level==2 && $exL) { $html .= $exL; }
							$html .= '&nbsp; <span class="unicode">'.$this->formatUni($substitute).'</span> ';
							$html .= '</div>'; 
						}
					}
					// LookupType 2: Multiple Substitution Subtable
					else if ($Lookup[$i]['Type'] == 2) {
						$html .= '<div class="lookuptype">LookupType 2: Multiple Substitution Subtable</div>'; 
						for ($s=0;$s<count($Lookup[$i]['Subtable'][$c]['subs']);$s++) {
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['subs'][$s]['Replace'];
							$substitute = $Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute'];
							if ($level==2 && strpos($coverage, $inputGlyphs[0])===false) { continue; }
							$html .= '<div class="substitution">';
							$html .= '<span class="unicode">'.$this->formatUni($inputGlyphs[0]).'&nbsp;</span> ';
							if ($level==2 && $exB) { $html .= $exB; }
							$html .= '<span class="unchanged">&nbsp;'.$this->formatEntity($inputGlyphs[0]).'</span>';
							if ($level==2 && $exL) { $html .= $exL; }
							$html .= '&nbsp; &raquo; &raquo; &nbsp;';
							if ($level==2 && $exB) { $html .= $exB; }
							$html .= '<span class="changed">&nbsp;'.$this->formatEntityArr($substitute).'</span>';
							if ($level==2 && $exL) { $html .= $exL; }
							$html .= '&nbsp; <span class="unicode">'.$this->formatUniArr($substitute).'</span> ';
							$html .= '</div>'; 
						}
					}
					// LookupType 3: Alternate Forms
					else if ($Lookup[$i]['Type'] == 3) {
						$html .= '<div class="lookuptype">LookupType 3: Alternate Forms</div>'; 
						for ($s=0;$s<count($Lookup[$i]['Subtable'][$c]['subs']);$s++) {
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['subs'][$s]['Replace'];
							$substitute = $Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute'][0];
							if ($level==2 && strpos($coverage, $inputGlyphs[0])===false) { continue; }
							$html .= '<div class="substitution">';
							$html .= '<span class="unicode">'.$this->formatUni($inputGlyphs[0]).'&nbsp;</span> ';
							if ($level==2 && $exB) { $html .= $exB; }
							$html .= '<span class="unchanged">&nbsp;'.$this->formatEntity($inputGlyphs[0]).'</span>';
							if ($level==2 && $exL) { $html .= $exL; }
							$html .= '&nbsp; &raquo; &raquo; &nbsp;';
							if ($level==2 && $exB) { $html .= $exB; }
							$html .= '<span class="changed">&nbsp;'.$this->formatEntity($substitute).'</span>';
							if ($level==2 && $exL) { $html .= $exL; }
							$html .= '&nbsp; <span class="unicode">'.$this->formatUni($substitute).'</span> ';
							if (count($Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute'])>1) {
								for ($alt=1;$alt<count($Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute']);$alt++) {
									$substitute = $Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute'][$alt];
									$html .= '&nbsp; | &nbsp; ALT #'.$alt.' &nbsp; ';
									$html .= '<span class="changed">&nbsp;'.$this->formatEntity($substitute).'</span>';
									$html .= '&nbsp; <span class="unicode">'.$this->formatUni($substitute).'</span> ';
								}
							}
							$html .= '</div>'; 
						}
					}
					// LookupType 4: Ligature Substitution Subtable
					else if ($Lookup[$i]['Type'] == 4) {
						$html .= '<div class="lookuptype">LookupType 4: Ligature Substitution Subtable</div>'; 
						for ($s=0;$s<count($Lookup[$i]['Subtable'][$c]['subs']);$s++) {
							$inputGlyphs = $Lookup[$i]['Subtable'][$c]['subs'][$s]['Replace'];
							$substitute = $Lookup[$i]['Subtable'][$c]['subs'][$s]['substitute'][0];
							if ($level==2 && strpos($coverage, $inputGlyphs[0])===false) { continue; }
							$html .= '<div class="substitution">';
							$html .= '<span class="unicode">'.$this->formatUniArr($inputGlyphs).'&nbsp;</span> ';
							if ($level==2 && $exB) { $html .= $exB; }
							$html .= '<span class="unchanged">&nbsp;'.$this->formatEntityArr($inputGlyphs).'</span>';
							if ($level==2 && $exL) { $html .= $exL; }
							$html .= '&nbsp; &raquo; &raquo; &nbsp;';
							if ($level==2 && $exB) { $html .= $exB; }
							$html .= '<span class="changed">&nbsp;'.$this->formatEntity($substitute).'</span>';
							if ($level==2 && $exL) { $html .= $exL; }
							$html .= '&nbsp; <span class="unicode">'.$this->formatUni($substitute).'</span> ';
							$html .= '</div>'; 
						}
					}

					// LookupType 5: Contextual Substitution Subtable
					else if ($Lookup[$i]['Type'] == 5) {
						$html .= '<div class="lookuptype">LookupType 5: Contextual Substitution Subtable</div>'; 
						// Format 1: Context Substitution
						if ($SubstFormat==1) {	
							$html .= '<div class="lookuptypesub">Format 1: Context Substitution</div>'; 
							for($s=0;$s<$Lookup[$i]['Subtable'][$c]['SubRuleSetCount'];$s++) {
								// SubRuleSet
$subRule = array();
								$html .= '<div class="rule">Subrule Set: '.$s.'</div>'; 
								foreach($Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['SubRule'] AS $rctr=>$rule) {
									// SubRule
									$html .= '<div class="rule">SubRule: '.$rctr.'</div>'; 
									$inputGlyphs = array(); 
									if ($rule['GlyphCount']>1) { 
										$inputGlyphs = $rule['InputGlyphs']; 
									}
									$inputGlyphs[0] = $Lookup[$i]['Subtable'][$c]['SubRuleSet'][$s]['FirstGlyph'];
									ksort($inputGlyphs);
									$nInput = count($inputGlyphs);


									$exampleI = array();
									$html .= '<div class="context">CONTEXT: ';
									for ($ff=0;$ff<count($inputGlyphs);$ff++) {
										$html .= '<div>Input #'.$ff.': <span class="unchanged">&nbsp;'.$this->formatEntityStr($inputGlyphs[$ff]).'&nbsp;</span></div>';
										$exampleI[] = $this->formatEntityFirst($inputGlyphs[$ff]);
									}
									$html .= '</div>'; 


									for ($b=0;$b<$rule['SubstCount'];$b++) {
										$lup = $rule['SubstLookupRecord'][$b]['LookupListIndex'];
										$seqIndex = $rule['SubstLookupRecord'][$b]['SequenceIndex'];

										// GENERATE exampleI[<seqIndex] .... exampleI[>seqIndex] 
										$exB = '';
										$exL = '';
										if ($seqIndex>0) {
											$exB .= '<span class="inputother">';
											for($ip=0;$ip<$seqIndex;$ip++) {
												$exB .=  $this->formatEntity($inputGlyphs[$ip]).'&#x200d;';
											}
											$exB .= '</span>';
										}
										if (count($inputGlyphs)>($seqIndex+1)) {
											$exL .= '<span class="inputother">';
											for($ip=$seqIndex+1;$ip<count($inputGlyphs);$ip++) {
												$exL .=  $this->formatEntity($inputGlyphs[$ip]).'&#x200d;';
											}
											$exL .= '</span>';
										}
										$html .= '<div class="sequenceIndex">Substitution Position: '.$seqIndex.'</div>'; 

										$lul2 = array($lup=>$tag);

										// Only apply if the (first) 'Replace' glyph from the 
										// Lookup list is in the [inputGlyphs] at ['SequenceIndex']
										// Pass $inputGlyphs[$seqIndex] e.g. 00636|00645|00656
										// to level 2 and only apply if first Replace glyph is in this list
										$html .= $this->_getGSUBarray($Lookup, $lul2, $scripttag, 2, $inputGlyphs[$seqIndex], $exB, $exL);
									}


									if (count($subRule['rules'])) $volt[] = $subRule;

								}
							}
						}
						// Format 2: Class-based Context Glyph Substitution
						else if ($SubstFormat==2) {	
							$html .= '<div class="lookuptypesub">Format 2: Class-based Context Glyph Substitution</div>'; 
							foreach($Lookup[$i]['Subtable'][$c]['SubClassSet'] AS $inputClass=>$cscs) {
								$html .= '<div class="rule">Input Class: '.$inputClass.'</div>'; 
								for($cscrule=0;$cscrule<$cscs['SubClassRuleCnt'];$cscrule++) {
									$html .= '<div class="rule">Rule: '.$cscrule.'</div>'; 
									$rule = $cscs['SubClassRule'][$cscrule];

									$inputGlyphs = array();

									$inputGlyphs[0] = $Lookup[$i]['Subtable'][$c]['InputClasses'][$inputClass];

									if ($rule['InputGlyphCount']>1) {
										//  NB starts at 1 
										for ($gcl=1;$gcl<$rule['InputGlyphCount'];$gcl++) {
											$classindex = $rule['Input'][$gcl];
											$inputGlyphs[$gcl] = $Lookup[$i]['Subtable'][$c]['InputClasses'][$classindex];
										}
									}

									// Class 0 contains all the glyphs NOT in the other classes
									$class0excl = implode('|', $Lookup[$i]['Subtable'][$c]['InputClasses']);

									$exampleI = array();
									$html .= '<div class="context">CONTEXT: ';
									for ($ff=0;$ff<count($inputGlyphs);$ff++) {

										if (!$inputGlyphs[$ff]) { 

											$html .= '<div>Input #'.$ff.': <span class="unchanged">&nbsp;[NOT '.$this->formatEntityStr($class0excl).']&nbsp;</span></div>';
											$exampleI[] = '[NOT '.$this->formatEntityFirst($class0excl).']';
										}
										else {
											$html .= '<div>Input #'.$ff.': <span class="unchanged">&nbsp;'.$this->formatEntityStr($inputGlyphs[$ff]).'&nbsp;</span></div>';
											$exampleI[] = $this->formatEntityFirst($inputGlyphs[$ff]);
										}
									}
									$html .= '</div>'; 


									for ($b=0;$b<$rule['SubstCount'];$b++) {
										$lup = $rule['LookupListIndex'][$b];
										$seqIndex = $rule['SequenceIndex'][$b];

										// GENERATE exampleI[<seqIndex] .... exampleI[>seqIndex] 
										$exB = '';
										$exL = '';

										if ($seqIndex>0) {
											$exB .= '<span class="inputother">';
											for($ip=0;$ip<$seqIndex;$ip++) {
												if (!$inputGlyphs[$ip]) { 
													$exB .=  '[*]';
												}
												else {
													$exB .=  $this->formatEntityFirst($inputGlyphs[$ip]).'&#x200d;';
												}
											}
											$exB .= '</span>';
										}

										if (count($inputGlyphs)>($seqIndex+1)) {
											$exL .= '<span class="inputother">';
											for($ip=$seqIndex+1;$ip<count($inputGlyphs);$ip++) {
												if (!$inputGlyphs[$ip]) { 
													$exL .=  '[*]';
												}
												else {
													$exL .=  $this->formatEntityFirst($inputGlyphs[$ip]).'&#x200d;';
												}
											}
											$exL .= '</span>';
										}

										$html .= '<div class="sequenceIndex">Substitution Position: '.$seqIndex.'</div>'; 

										$lul2 = array($lup=>$tag);

										// Only apply if the (first) 'Replace' glyph from the 
										// Lookup list is in the [inputGlyphs] at ['SequenceIndex']
										// Pass $inputGlyphs[$seqIndex] e.g. 00636|00645|00656
										// to level 2 and only apply if first Replace glyph is in this list
										$html .= $this->_getGSUBarray($Lookup, $lul2, $scripttag, 2, $inputGlyphs[$seqIndex], $exB, $exL);
									}
									if (count($subRule['rules'])) $volt[] = $subRule;

								}
							}



						}
						// Format 3: Coverage-based Context Glyph Substitution  p259
						else if ($SubstFormat==3) {
							$html .= '<div class="lookuptypesub">Format 3: Coverage-based Context Glyph Substitution  </div>'; 
							// IgnoreMarks flag set on main Lookup table
							$inputGlyphs = $Lookup[$i]['Subtable']