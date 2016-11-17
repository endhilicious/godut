this->GlyphClassLigatures.'\';
$GlyphClassComponents = \''.$this->GlyphClassComponents.'\';
$MarkGlyphSets = '.var_export($this->MarkGlyphSets , true).';
$MarkAttachmentType = '.var_export($this->MarkAttachmentType , true).';
?>';


			file_put_contents(_MPDF_TTFONTDATAPATH.$this->fontkey.'.GDEFdata.php', $s);

			//=====================================================================================


//echo $this->GlyphClassMarks ; exit;
//print_r($GlyphClass); exit;
//print_r($GlyphByClass); exit;
	}

	function _getClassDefinitionTable() {

	// NB Any glyph not included in the range of covered GlyphIDs automatically belongs to Class 0. This is not returned by this function
		$ClassFormat = $this->read_ushort();
		$GlyphByClass = array();
		if ($ClassFormat == 1) {
			$StartGlyph = $this->read_ushort();
			$GlyphCount = $this->read_ushort();
			for ($i=0;$i<$GlyphCount;$i++) {
				$gid = $StartGlyph + $i;
				$class = $this->read_ushort();
				// Several fonts  (mainly dejavu.../Freeserif etc) have a MarkAttachClassDef Format 1, where StartGlyph is 0 and GlyphCount is 1
				// This doesn't seem to do anything useful?
				// Freeserif does not have $this->glyphToChar[0] allocated and would throw an error, so check if isset:
				if (isset($this->glyphToChar[$gid][0])) {
					$GlyphByClass[$class][] = unicode_hex($this->glyphToChar[$gid][0]);
				}
			}
		}
		else if ($ClassFormat == 2) {
			$tableCount = $this->read_ushort();
			for ($i=0;$i<$tableCount;$i++) {
				$startGlyphID = $this->read_ushort();
				$endGlyphID = $this->read_ushort();
				$class = $this->read_ushort();
				for($gid=$startGlyphID;$gid<=$endGlyphID;$gid++) {
					if (isset($this->glyphToChar[$gid][0])) {
						$GlyphByClass[$class][] = unicode_hex($this->glyphToChar[$gid][0]);
					}
				}
			}
		}
		foreach($GlyphByClass AS $class=>$glyphs) {
			sort($GlyphByClass[$class], SORT_STRING);	// SORT makes it easier to read in development ? order not important ???
		}
		ksort($GlyphByClass);
		return $GlyphByClass;
	}

	function _getGSUBtables() {
		///////////////////////////////////
		// GSUB - Glyph Substitution
		///////////////////////////////////
		if (isset($this->tables["GSUB"])) { 
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
				$tag = $this->read_tag() ;
				if ($tag == 'smcp') { $this->hassmallcapsGSUB = true; }
				$Feature[$i] = array('tag' => $tag);
				$Feature[$i]['offset'] = $FeatureList_offset + $this->read_ushort();
			}
			for ($i=0;$i<$FeatureCount;$i++) {
				$this->seek($Feature[$i]['offset']);
				$this->read_ushort(); // null [FeatureParams]
				$Feature[$i]['LookupCount'] = $Lookupcount = $this->read_ushort();
				$Feature[$i]['LookupListIndex'] = array();
				for ($c=0;$c<$Lookupcount;$c++) {
					$Feature[$i]['LookupListIndex'][] = $this->read_ushort();
				}
			}

//print_r($Feature); exit;

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
			foreach($ffeats AS $st=>$scripts) {
				foreach($scripts AS $t=>$langsys) {
					$lg = array();
					foreach($langsys AS $ft) {
						$lg[$ft['LookupListIndex'][0]] = $ft;
					}
					// list of Lookups in order they need to be run i.e. order listed in Lookup table
					ksort($lg);
					foreach($lg AS $ft) {
						$gsub[$st][$t][$ft['tag']] = $ft['LookupListIndex'];
					}
					if (!isset($GSUBScriptLang[$st])) { $GSUBScriptLang[$st] = ''; }
					$GSUBScriptLang[$st] .= $t.' ';
				}
			}

//print_r($gsub); exit;



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
				if (($flag & 0x0010