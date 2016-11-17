#x200d;';
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

//print_r($Lookup[$i]);
//print_r($volt[(count($volt)-1)]); exit;
					}
					// LookupType 6: Chaining Contextual Substitution Subtable
					else if ($Lookup[$i]['Type'] == 6) {
						$html .= '<div class="lookuptype">LookupType 6: Chaining Contextual Substitution Subtable</div>'; 
						// Format 1: Simple Chaining Context Glyph Substitution  p255
						if ($SubstFormat==1) {	
							$html .= '<div class="lookuptypesub">Format 1: Simple Chaining Context Glyph Substitution  </div>'; 
							for($s=0;$s<$Lookup[$i]['Subtable'][$c]['ChainSubRuleSetCount'];$s++) {
								// ChainSubRuleSet
$subRule = array();
								$html .= '<div class="rule">Subrule Set: '.$s.'</div>'; 
								$firstInputGlyph = $Lookup[$i]['Subtable'][$c]['CoverageGlyphs'][$s];	// First input gyyph
								foreach($Lookup[$i]['Subtable'][$c]['ChainSubRuleSet'][$s]['ChainSubRule'] AS $rctr=>$rule) {
									$html .= '<div class="rule">SubRule: '.$rctr.'</div>'; 
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

									if ($rule['LookaheadGlyphCount']) { $lookaheadGlyphs = $rule['LookaheadGlyphs']; }
									else { $lookaheadGlyphs = array(); }


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


									for ($b=0;$b<$rule['SubstCount'];$b++) {
										$lup = $rule['LookupListIndex'][$b];
										$seqIndex = $rule['SequenceIndex'][$b];

										// GENERATE exampleB[n] exampleI[<seqIndex] .... exampleI[>seqIndex] exampleL[n]
										$exB = '';
										$exL = '';
										if (count($exampleB)) { $exB .= '<span class="backtrack">'.implode('&#x200d;',$exampleB).'</span>'; }

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

										if (count($exampleL)) { $exL .= '<span class="lookahead">'.implode('&#x200d;',$exampleL).'</span>'; }

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
						// Format 2: Class-based Chaining Context Glyph Substitution  p257
						else if ($SubstFormat==2) {
							$html .= '<div class="lookuptypesub">Format 2: Class-based Chaining Context Glyph Substitution  </div>'; 
							foreach($Lookup[$i]['Subtable'][$c]['ChainSubClassSet'] AS $inputClass=>$cscs) {
								$html .= '<div class="rule">Input Class: '.$inputClass.'</div>'; 
								for($cscrule=0;$cscrule<$cscs['ChainSubClassRuleCnt'];$cscrule++) {
									$html .= '<div class="rule">Rule: '.$cscrule.'</div>'; 
									$rule = $cscs['ChainSubClassRule'][$cscrule];

									// These contain classes of glyphs as strings
									// $Lookup[$i]['Subtable'][$c]['InputClasses'][(class)] e.g. 02E6|02E7|02E8
									// $Lookup[$i]['Subtable'][$c]['LookaheadClasses'][(class)]
									// $Lookup[$i]['Subtable'][$c]['BacktrackClasses'][(class)]

									// These contain arrays of classIndexes
									// [Backtrack] [Lookahead] and [Input] (Input is from the second position only)

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

									$nInput = $rule['InputGlyphCount'];

									if ($rule['BacktrackGlyphCount']) {
										for ($gcl=0;$gcl<$rule['BacktrackGlyphCount'];$gcl++) {
											$classindex = $rule['Backtrack'][$gcl];
											$backtrackGlyphs[$gcl] = $Lookup[$i]['Subtable'][$c]['BacktrackClasses'][$classindex];
										}
									}
									else { $backtrackGlyphs = array(); }

									if ($rule['LookaheadGlyphCount']) {
										for ($gcl=0;$gcl<$rule['LookaheadGlyphCount'];$gcl++) {
											$classindex = $rule['Lookahead'][$gcl];
											$lookaheadGlyphs[$gcl] = $Lookup[$i]['Subtable'][$c]['LookaheadClasses'][$classindex];
										}
									}
									else { $lookaheadGlyphs = array(); }


									$exampleB = array();
									$exampleI = array();
									$exampleL = array();
									$html .= '<div class="context">CONTEXT: ';
									for ($ff=count($backtrackGlyphs)-1;$ff>=0;$ff--) {
										if (!$backtrackGlyphs[$ff]) { 
											$html .= '<div>Backtrack #'.$ff.': <span class="unchanged">&nbsp;[NOT '.$this->formatEntityStr($class0excl).']&nbsp;</span></div>';
											$exampleB[] = '[NOT '.$this->formatEntityFirst($class0excl).']';

										}
										else {
											$html .= '<div>Backtrack #'.$ff.': <span class="unicode">'.$this->formatUniStr($backtrackGlyphs[$ff]).'</span></div>';
											$exampleB[] = $this->formatEntityFirst($backtrackGlyphs[$ff]);
										}
									}
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
									for ($ff=0;$ff<count($lookaheadGlyphs);$ff++) {
										if (!$lookaheadGlyphs[$ff]) { 
											$html .= '<div>Lookahead #'.$ff.': <span class="unchanged">&nbsp;[NOT '.$this->formatEntityStr($class0excl).']&nbsp;</span></div>';
											$exampleL[] = '[NOT '.$this->formatEntityFirst($class0excl).']';

										}
										else {
											$html .= '<div>Lookahead #'.$ff.': <span class="unicode">'.$this->formatUniStr($lookaheadGlyphs[$ff]).'</span></div>';
											$exampleL[] = $this->formatEntityFirst($lookaheadGlyphs[$ff]);
										}
									}
									$html .= '</div>'; 


									for ($b=0;$b<$rule['SubstCount'];$b++) {
										$lup = $rule['LookupListIndex'][$b];
										$seqIndex = $rule['SequenceIndex'][$b];

						