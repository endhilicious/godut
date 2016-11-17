								if ($Value2['XPlacement']) { $html .= ' Xpl[2]: '.$Value2['XPlacement'].';'; }
													if ($Value2['YPlacement']) { $html .= ' YPl[2]: '.$Value2['YPlacement'].';'; }
													if ($Value2['XAdvance']) { $html .= ' Xadv[2]: '.$Value2['XAdvance']; }
													$html .= '</span>';
													$html .= '</div>'; 

												}
											}
									}

							}
						}
					}
					////////////////////////////////////////////////////////////////////////////////
					// LookupType 3: Cursive attachment 	Attach cursive glyphs
					////////////////////////////////////////////////////////////////////////////////
					else if ($Lookup[$luli]['Type'] == 3) {
						$html .= '<div class="lookuptype">LookupType 3: Cursive attachment </div>'; 
						$Coverage = $subtable_offset + $this->read_ushort();
						$EntryExitCount = $this->read_ushort();
						$EntryAnchors = array();
						$ExitAnchors = array();
						for($i=0;$i<$EntryExitCount;$i++) {
							$EntryAnchors[$i] = $this->read_ushort();
							$ExitAnchors[$i] = $this->read_ushort();
						}

						$this->seek($Coverage);
						$Glyphs = $this->_getCoverage();
						for($i=0;$i<$EntryExitCount;$i++) {
							// Need default XAdvance for glyph
							$pdfWidth = $this->mpdf->_getCharWidth($this->mpdf->fonts[$this->fontkey]['cw'], hexdec($Glyphs[$i]));
							$EntryAnchor = $EntryAnchors[$i] ;
							$ExitAnchor = $ExitAnchors[$i] ;
							$html .= '<div class="glyphs">';
							$html .= '<span class="unchanged">'.$this->formatEntity($Glyphs[$i]).' </span> ';
							$html .= '<span class="unicode"> '.$this->formatUni($Glyphs[$i]).' => '; 

							if ($EntryAnchor != 0) {
								$EntryAnchor += $subtable_offset;
								list($x,$y) = $this->_getAnchorTable($EntryAnchor);
								if ($dir == 'RTL') {
									if (round($pdfWidth) == round($x * 1000/ $this->mpdf->fonts[$this->fontkey]['desc']['unitsPerEm']) ) {
										$x = 0;
									}
									else { $x = $x - ($pdfWidth * $this->mpdf->fonts[$this->fontkey]['desc']['unitsPerEm']/1000); }
								}
								$html .= " Entry X: ".$x." Y: ".$y."; "; 
							}
							if ($ExitAnchor != 0) {
								$ExitAnchor += $subtable_offset;
								list($x,$y) = $this->_getAnchorTable($ExitAnchor);
								if ($dir == 'LTR') {
									if (round($pdfWidth) == round($x * 1000/ $this->mpdf->fonts[$this->fontkey]['desc']['unitsPerEm']) ) {
										$x = 0;
									}
									else { $x = $x - ($pdfWidth * $this->mpdf->fonts[$this->fontkey]['desc']['unitsPerEm']/1000); }
								}
								$html .= " Exit X: ".$x." Y: ".$y."; "; 
							}


							$html .= '</span></div>';
						}

					}
					////////////////////////////////////////////////////////////////////////////////
					// LookupType 4: MarkToBase attachment 	Attach a combining mark to a base glyph
					////////////////////////////////////////////////////////////////////////////////
					else if ($Lookup[$luli]['Type'] == 4) {
						$html .= '<div class="lookuptype">LookupType 4: MarkToBase attachment </div>'; 
						$MarkCoverage = $subtable_offset + $this->read_ushort();
						$BaseCoverage = $subtable_offset + $this->read_ushort();

						$this->seek($MarkCoverage);
						$MarkGlyphs = $this->_getCoverage();

						$this->seek($BaseCoverage);
						$BaseGlyphs = $this->_getCoverage();

						$firstMark = '';
						$html .= '<div class="glyphs">Marks: ';
						for($i=0;$i<count($MarkGlyphs);$i++) {
							if ($level==2 && strpos($lcoverage, $MarkGlyphs[$i])===false) { continue; }
							else { 
								if (!$firstMark) { $firstMark = $MarkGlyphs[$i]; }
							}
							$html .= ' '.$this->formatEntity($MarkGlyphs[$i]).' ';
						}
						$html .= '</div>';
						if (!$firstMark) { return; }

						$html .= '<div class="glyphs">Bases: ';
						for($j=0;$j<count($BaseGlyphs);$j++) {
							$html .= ' '.$this->formatEntity($BaseGlyphs[$j]).' ';
						}
						$html .= '</div>';

						// Example
						$html .= '<div class="glyphs" style="font-feature-settings:\''.$tag.'\' 1;">Example(s): ';
						for ($j=0;$j<min(count($BaseGlyphs),20);$j++) {
							$html .= ' '.$this->formatEntity($BaseGlyphs[$j]).$this->formatEntity($firstMark,true).' &nbsp; ';
						}
						$html .= '</div>';


					}
					////////////////////////////////////////////////////////////////////////////////
					// LookupType 5: MarkToLigature attachment 	Attach a combining mark to a ligature
					////////////////////////////////////////////////////////////////////////////////
					else if ($Lookup[$luli]['Type'] == 5) {
						$html .= '<div class="lookuptype">LookupType 5: MarkToLigature attachment </div>'; 
						$MarkCoverage = $subtable_offset + $this->read_ushort();
						//$MarkCoverage is already set in $lcoverage 00065|00073 etc
						$LigatureCoverage = $subtable_offset + $this->read_ushort();
						$ClassCount = $this->read_ushort(); // Number of classes defined for marks = Number of mark glyphs in the MarkCoverage table
						$MarkArray = $subtable_offset + $this->read_ushort();	// Offset to MarkArray table
						$LigatureArray = $subtable_offset + $this->read_ushort(); // Offset to LigatureArray table

						$this->seek($MarkCoverage);
						$MarkGlyphs = $this->_getCoverage();
						$this->seek($LigatureCoverage);
						$LigatureGlyphs = $this->_getCoverage();

						$firstMark = '';
						$html .= '<div class="glyphs">Marks: <span class="unchanged">';
						$MarkRecord = array();
						for ($i=0;$i<count($MarkGlyphs);$i++) {
							if ($level==2 && strpos($lcoverage, $MarkGlyphs[$i])===false) { continue; }
							else { 
								if (!$firstMark) { $firstMark = $MarkGlyphs[$i]; }
							}
							// Get the relevant MarkRecord
							$MarkRecord[$i] = $this->_getMarkRecord($MarkArray, $i);
							//Mark Class is = $MarkRecord[$i]['Class']
							$html .= ' '.$this->formatEntity($MarkGlyphs[$i]).' ';
						}
						$html .= '</span></div>';
						if (!$firstMark) { return; }

						$this->seek($LigatureArray);
						$LigatureCount = $this->read_ushort();
						$LigatureAttach = array();
						$html .= '<div class="glyphs">Ligatures: <span class="unchanged">';
						for ($j=0;$j<count($LigatureGlyphs);$j++) {
							// Get the relevant LigatureRecord
							$LigatureAttach[$j] = $LigatureArray + $this->read_ushort();
							$html .= ' '.$this->formatEntity($LigatureGlyphs[$j]).' ';
						}
						$html .= '</span></div>';

/*
						for ($i=0;$i<count($MarkGlyphs);$i++) {
							$html .= '<div class="glyphs">';
							$html .= '<span class="unchanged">'.$this->formatEntity($MarkGlyphs[$i]).'</span>';

							for ($j=0;$j<count($LigatureGlyphs);$j++) {
								$this->seek($LigatureAttach[$j]);
								$ComponentCount = $this->read_ushort();
								$html .= '<span class="unchanged">'.$this->formatEntity($LigatureGlyphs[$j]).'</span>';
								$offsets = array();
								for ($comp=0;$comp<$ComponentCount;$comp++) {
									// ComponentRecords
									for ($class=0;$class<$ClassCount;$class++) {
										$offset = $this->read_ushort();
										if ($offset!= 0 && $class == $MarkRecord[$i]['Class']) {

											$html .= ' ['.$comp.'] ';

										}
									}
								}
							}
							$html .= '</span></div>';
						}
*/


					}
					////////////////////////////////////////////////////////////////////////////////
					// LookupType 6: MarkToMark attachment 	Attach a combining mark to another mark
					///////////////////////////////////////////////////