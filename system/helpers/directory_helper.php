ass = $this->read_ushort();
				for($gid=$startGlyphID;$gid<=$endGlyphID;$gid++) {
					$GlyphByClass[$class][] = unicode_hex($this->glyphToChar[$gid][0]);
				}
			}
		}
		ksort($GlyphByClass);
		return $GlyphByClass;
	}

	function _getGSUBtables() {
		///////////////////////////////////
		// GSUB - Glyph Substitution
		///////////////////////////////////
		if (isset($this->tables["GSUB"])) { 
			$this->mpdf->WriteHTML('<h1>GSUB Tables</h1>'); 
			$ffeats = array();
			$gsub_offset = $this->seek_table("GSUB");
			$this->skip(4);
			$ScriptList_offset = $gsub_offset + $this->read_ushort();
			$FeatureList_offset = $gsub_offset + $this->read_ushort();
			$LookupList_offset = $gsub_offset + $this->read_ushort();

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
//print_r($ffeats); exit;


			// Get FeatureIndexList
			// LangSys Table - from first listed langsys
			foreach($ffeats AS $st=>$scripts) {
				foreach($scripts AS $t=>$o) {
					$FeatureIndex = array();
					$langsystable_offset = $o;
					$this->seek($langsystable_offset);
					$LookUpOrder = $this->read_ushort();	//==NULL
					$ReqFeatureIndex = $this->read_ushort();
					if ($ReqFeatureIndex != 0xFFFF) { $FeatureIndex[] = $ReqFeatureIndex; }
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
				$Feature[$i] = array('tag' => $this->read_tag() );
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
			//=====================================================================================
			$gsub = array();
			$GSUBScriptLang = array();
			foreach($ffea