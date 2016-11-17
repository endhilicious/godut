$html .= '<h5>'.$st.'</h5>'; 
						foreach ($g AS $l=>$t) {
							$html .= '<div><a href="font_dump_OTL.php?script='.$st.'&lang='.$l.'">'.$l.'</a></b>: '; 
							foreach ($t AS $tag=>$o) {
								$html .= $tag.' '; 
							}
							$html .= '</div>'; 
						}
					}
				}
				else {
					$html .= '<div>No entries in GSUB table.</div>';
				}
				$this->mpdf->WriteHTML($html); 
				$this->mpdf->WriteHTML('</div>'); 
				return 0;
			}



			//=====================================================================================
			// Get metadata and offsets for whole Lookup List table
			$this->seek($LookupList_offset );
			$LookupCount = $this->read_ushort();
			$GSLookup = array();
			$Offsets = array();
			$SubtableCount = array();
			for ($i=0;$i<$LookupCount;$i++) {
				$Offsets[$i] = $LookupList_offset + $this->read_ushort();
			}
			for ($i=0;$i<$LookupCount;$i++) {
				$this->seek($Offsets[$i]);
				$GSLookup[$i]['Type'] = $this->read_ushort();
				$GSLookup[$i]['Flag'] = $flag = $this->read_ushort();
				$GSLookup[$i]['SubtableCount'] = $SubtableCount[$i] = $this->read_ushort();
				for ($c=0;$c<$SubtableCount[$i] ;$c++) {
					$GSLookup[$i]['Subtables'][$c] = $Offsets[$i] + $this->read_ushort();

				}
				// MarkFilteringSet = Index (base 0) into GDEF mark glyph sets structure
				if (($flag & 0x0010) == 0x0010) {
					$GSLookup[$i]['MarkFilteringSet'] = $this->read_ushort();
				}
				// else { $GSLookup[$i]['MarkFilteringSet'] = ''; }

				// Lookup Type 7: Extension
				if ($GSLookup[$i]['Type'] == 7) {
					// Overwrites new offset (32-bit) for each subtable, and a new lookup Type
					for ($c=0;$c<$SubtableCount[$i] ;$c++) {
						$this->seek($GSLookup[$i]['Subtables'][$c]);
						$ExtensionPosFormat = $this->read_ushort();
						$type = $this->read_ushort();
						$GSLookup[$i]['Subtables'][$c] = $GSLookup[$i]['Subtables'][$c] + $this->read_ulong();
					}
					$GSLookup[$i]['Type'] = $type;
				}

			}

//print_r($GSLookup); exit;
			//=====================================================================================
			// Process Whole LookupList - Get LuCoverage = Lookup coverage just for first glyph
			$this->GSLuCoverage = array();
			for ($i=0;$i<$LookupCount;$i++) {
				for ($c=0;$c<$GSLookup[$i]['SubtableCount'] ;$c++) {

					$this->seek($GSLookup[$i]['Subtables'][$c]);
					$PosFormat= $this->read_ushort();

					if ($GSLookup[$i]['Type']==5 && $PosFormat==3) { $this->skip(4); }
					else if ($GSLookup[$i]['Type']==6 && $PosFormat==3) {
						$BacktrackGlyphCount= $this->read_ushort();
						$this->skip(2*$BacktrackGlyphCount + 2); 
					}
					// NB Coverage only looks at glyphs for position 1 (i.e. 5.3 and 6.3)	// NEEDS TO READ ALL ********************
					$Coverage = $GSLookup[$i]['Subtables'][$c] + $this->read_ushort();
					$this->seek($Coverage);
					$glyphs = $this->_getCoverage();
					$this->GSLuCoverage[$i][$c] = implode('|',$glyphs);
				}
			}

// $this->GSLuCoverage and $GSLookup

			//=====================================================================================
			$s = '<?php
$GSLuCoverage = '.var_export($this->GSLuCoverage , true).';
?>';


			//=====================================================================================
			$s = '<?php
$GlyphClassBases = \''.$this->GlyphClassBases.'\';
$GlyphClassMarks = \''.$this->GlyphClassMarks.'\';
$GlyphClassLigatures = \''.$this->GlyphClassLigatures.'\';
$GlyphClassComponents = \''.$this->GlyphClassComponents.'\';
$MarkGlyphSets = '.var_export($this->MarkGlyphSets , true).';
$MarkAttachmentType = '.var_export($this->MarkAttachmentType , true).';
?>';


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
				$Lookup[$i]['offset'] = $LookupList_offset