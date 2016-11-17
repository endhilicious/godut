okup[$i]['Subtable'][$c]['Sequences'][$s]['GlyphCount'];$g++) { 
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
									$Lookup[$i]['Subta