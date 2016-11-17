///////////////////////////////////////////////////////////////////

			//=====================================================================================
			//=====================================================================================
			//=====================================================================================
/////////////////////////////////////////////////////////////////////////////////////////
	// GPOS functions
	function _getGPOSarray(&$Lookup, $lul, $scripttag, $level=1, $lcoverage='', $exB='', $exL='') {
			// Process (3) LookupList for specific Script-LangSys
			$html = '';
			if ($level==1) { $html .= '<bookmark level="0" content="GPOS features">'; }
			foreach($lul AS $luli=>$tag) {
				$html .= '<div class="level'.$level.'">'; 
				$html .= '<h5 class="level'.$level.'">';
				if ($level==1) { $html .= '<bookmark level="1" content="'.$tag.' [#'.$luli.']">'; }
				$html .= 'Lookup #'.$luli.' [tag: <span style="color:#000066;">'.$tag.'</span>]</h5>'; 
				$ignore = $this->_getGSUBignoreString($Lookup[$luli]['Flag'], $Lookup[$luli]['MarkFilteringSet']);
				if ($ignore) { $html .= '<div class="ignore">Ignoring: '.$ignore.'</div> '; }

				$Type = $Lookup[$luli]['Type'];
				$Flag = $Lookup[$luli]['Flag'];
				if (($Flag  & 0x0001) == 1) { $dir = 'RTL'; }
				else { $dir = 'LTR'; }

				for ($c=0;$c<$Lookup[$luli]['SubtableCount'] ;$c++) {
					$html .= '<div class="subtable">Subtable #'.$c;
					if ($level==1) { $html .= '<bookmark level="2" content="Subtable #'.$c.'">'; }
					$html .= '</div>'; 

					// Lets start
					$subtable_offset = $Lookup[$luli]['Subtables'][$c];
					$this->seek($subtable_offset);
					$PosFormat = $this->read_ushort();

					////////////////////////////////////////////////////////////////////////////////
					// LookupType 1: Single adjustment 	Adjust position of a single glyph (e.g. SmallCaps/Sups/Subs)
					////////////////////////////////////////////////////////////////////////////////
					if ($Lookup[$luli]['Type'] == 1) {
						$html .= '<div class="lookuptype">LookupType 1: Single adjustment [Format '.$PosFormat.']</div>'; 
						//===========
						// Format 1: 
						//===========
						if ($PosFormat==1) {
							$Coverage = $subtable_offset + $this->read_ushort();
							$ValueFormat = $this->read_ushort();
							$Value = $this->_getValueRecord($ValueFormat);

							$this->seek($Coverage);
							$glyphs = $this->_getCoverage();	// Array of Hex Glyphs
							for($g=0;$g<count($glyphs);$g++) {
								if ($level==2 && strpos($lcoverage, $glyphs[$g])===false) { continue; }

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
						//===========
						// Format 2: 
						//===========