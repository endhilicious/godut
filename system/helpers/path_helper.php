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
			if ($this->mode == 'summary') {
				$this->mpdf->WriteHTML('<h3>GPOS Scripts &amp; Languages</h3>'); 
				$html = '';
				if (count($gpos)) {
					foreach ($gpos AS $st=>$g) {
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
					$html .= '<div>No entries in GPOS table.</div>';
				}
				$this->mpdf->WriteHTML($html); 
				$this->mpdf->WriteHTML('</div>'); 
				return 0;
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
				// else { $Lookup[$i]['MarkFilteringSet'] = ''; }

		