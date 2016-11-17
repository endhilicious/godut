();	// Array of Hex Glyphs

							for($g=0;$g<count($glyphs);$g++) {
								if ($level==2 && strpos($lcoverage, $glyphs[$g])===false) { continue; }
								$Value = $Values[$g];

								$html .= '<div class="substitution">';
								$html .= '<span class="unicode">'.$this->formatUni($glyphs[$g]).'&nbsp;</span> ';
								if ($level==2 && $exB) { $html .= $exB; }
								$html .= '<span class="unchanged">&nbsp;'.$this->formatEntity($glyphs[$g]).'</span>';
								if ($level==2 && $exL) { $html .= $exL; }
								$html .= '&nbsp; &raquo; &raquo; &nbsp;';
								if ($level==2 && $exB) { $html .= $exB; }
								$html .= '<span class="changed" style="font-feature-settings:\''.$tag.'\' 1;">&nbsp;'.$this->formatEntity($glyphs[$g]).'</span>';
								if ($level==2 && $exL) { $html .= $exL; }
								$html .= ' <span class="unicode">';
								if ($Value['XPlacement']) { $html .= ' Xpl: '.$Value['XPlacement'].';'; }
								if ($Value['YPlacement']) { $html .= ' YPl: '.$Value['YPlacement'].';'; }
								if ($Value['XAdvance']) { $html .= ' Xadv: '.$Value['XAdvance']; }
								$html .= '</span>';
								$html .= '</div>'; 
							}
						}



					}
					////////////////////////////////////////////////////////////////////////////////
					// LookupType 2: Pair adjustment 	Adjust position of a pair of glyphs (Kerning)
					////////////////////////////////////////////////////////////////////////////////
					else if ($Lookup[$luli]['Type'] == 2) {
						$html .= '<div class="lookuptype">LookupType 2: Pair adjustment e.g. Kerning [Format '.$PosFormat.']</div>'; 
						$Coverage = $subtable_offset + $this->read_ushort();
						$ValueFormat1 = $this->read_ushort();
						$ValueFormat2 = $this->read_ushort();
						//===========
						// Format 1: 
						//===========
						if ($PosFormat==1) {
							$PairSetCount = $this->read_ushort();
							$PairSetOffset = array();
							for($p=0;$p<$PairSetCount;$p++) {
								$PairSetOffset[] = $subtable_offset + $this->read_ushort();
							}
							$this->seek($Coverage);
							$glyphs = $this->_getCoverage();	// Array of Hex Glyphs
							for($p=0;$p<$PairSetCount;$p++) {
								if ($level==2 && strpos($lcoverage, $glyphs[$p])===false) { continue; }
								$this->seek($PairSetOffset[$p]);
								// First Glyph = $glyphs[$p]

// Takes too long e.g. Calibri font - just list kerning pairs with this:
$html .= '<div class="glyphs">';
$html .= '<span class="unchanged">&nbsp;'.$this->formatEntity($glyphs[$p]).' </span>';

								//PairSet table
								$PairValueCount = $this->read_ushort();
								for($pv=0;$pv<$PairValueCount;$pv++) {
									//PairValueRecord
									$gid = $this->read_ushort();
									$SecondGlyph = unicode_hex($this->glyphToChar[$gid][0]); 
									$Value1 = $this->_getValueRecord($ValueFormat1);
									$Value2 = $this->_getValueRecord($ValueFormat2);

									// If RTL pairs, GPOS declares a XPlacement e.g. -180 for an XAdvance of -180 to take 
									// account of direction. mPDF does not need the XPlacement adjustment
									if ($dir=='RTL' && $Value1['XPlacement']) {
										$Value1['XPlacement'] -= $Value1['XAdvance'];
									}

									if($ValueFormat2) { 
										// If RTL pairs, GPOS declares a XPlacement e.g. -180 for an XAdvance of -180 to take 
										// account of direction. mPDF does not need the XPlacement adjustment
										if ($dir=='RTL' && $Value2['XPlacement'] && $Value2['XAdvance']) {
											$Value2['XPlacement'] -= $Value2['XAdvance'];
										}
									}

$html .= ' '.$this->formatEntity($SecondGlyph).' ';

/*
									$html .= '<div class="substitution">';
									$html .= '<span class="unicode">'.$this->formatUni($glyphs[$p]).'&nbsp;</span> ';
									if ($level==2 && $exB) { $html .= $exB; }
									$html .= '<span class="unchanged">&nbsp;'.$this->formatEntity($glyphs[$p]).$this->formatEntity($SecondGlyph).'</span>';
									if ($level==2 && $exL) { $html .= $exL; }
									$html .= '&nbsp; &raquo; &raquo; &nbsp;';
									if ($level==2 && $exB) { $html .= $exB; }
									$html .= '<span class="changed" style="font-feature-settings:\''.$tag.'\' 1;">&nbsp;'.$this->formatEntity($glyphs[$p]).$this->formatEntity($SecondGlyph).'</span>';
									if ($level==2 && $exL) { $html .= $exL; }
									$html .= ' <span class="unicode">';
									if ($Value1['XPlacement']) { $html .= ' Xpl[1]: '.$Value1['XPlacement'].';'; }
									if ($Value1['YPlacement']) { $html .= ' YPl[1]: '.$Value1['YPlacement'].';'; }
									if ($Value1['XAdvance']) { $html .= ' Xadv[1]: '.$Value1['XAdvance']; }
									if ($Value2['XPlacement']) { $html .= ' Xpl[2]: '.$Value2['XPlacement'].';'; }
									if ($Value2['YPlacement']) { $html .= ' YPl[2]: '.$Value2['YPlacement'].';'; }
									if ($Value2['XAdvance']) { $html .= ' Xadv[2]: '.$Value2['XAdvance']; }
									$html .= '</span>';
									$html .= '</div>'; 
*/

								}
$html .= '</div>';
							}
						}
						//===========
						// Format 2: 
						//===========
						else if ($PosFormat==2) {	
							$ClassDef1 = $subtable_offset + $this->read_ushort();
							$ClassDef2 = $subtable_offset + $this->read_ushort();
							$Class1Count = $this->read_ushort();
							$Class2Count = $this->read_ushort();

							$sizeOfPair = ( 2*$this->count_bits($ValueFormat1) ) + ( 2*$this->count_bits($ValueFormat2) );
							$sizeOfValueRecords = $Class1Count * $Class2Count * $sizeOfPair;


							// NB Class1Count includes Class 0 even though it is not defined by $ClassDef1
							// i.e. Class1Count = 5; Class1 will contain array(indices 1-4);
							$Class1 = $this->_getClassDefinitionTable($ClassDef1);
							$Class2 = $this->_getClassDefinitionTable($ClassDef2);

							$this->seek($subtable_offset + 16);

							for($i=0;$i<$Class1Count;$i++) {
									for($j=0;$j<$Class2Count;$j++) {
											$Value1 = $this->_getValueRecord($ValueFormat1);
											$Value2 = $this->_getValueRecord($ValueFormat2);

											// If RTL pairs, GPOS declares a XPlacement e.g. -180 for an XAdvance of -180 
											// of direction. mPDF does not need the XPlacement adjustment
											if ($dir=='RTL' && $Value1['XPlacement'] && $Value1['XAdvance']) {
												$Value1['XPlacement'] -= $Value1['XAdvance'];
											}
											if($ValueFormat2) { 
												if ($dir=='RTL' && $Value2['XPlacement'] && $Value2['XAdvance']) {
													$Value2['XPlacement'] -= $Value2['XAdvance'];
												}
											}


											for($c1=0;$c1<count($Class1[$i]);$c1++) {

												$FirstGlyph = $Class1[$i][$c1];
												if ($level==2 && strpos($lcoverage, $FirstGlyph)===false) { 
													continue; 
												}


												for($c2=0;$c2<count($Class2[$j]);$c2++) {
													$Secon