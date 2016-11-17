g[] = $glyphID; }
				}
			}
		}
		return $g;						
	}

	//////////////////////////////////////////////////////////////////////////////////
	function _getClasses($offset) {
		$this->seek($offset);
		$ClassFormat = $this->read_ushort();
		$GlyphByClass = array();
		if ($ClassFormat == 1) {
			$StartGlyph = $this->read_ushort();
			$GlyphCount = $this->read_ushort();
			for ($i=0;$i<$GlyphCount;$i++) {
				$startGlyphID = $StartGlyph + $i;
				$endGlyphID = $StartGlyph + $i;
				$class = $this->read_ushort();
				for($g=$startGlyphID;$g<=$endGlyphID;$g++) {
					if ($this->glyphToChar[$g][0]) {
						$GlyphByClass[$class][] = unicode_hex($this->glyphToChar[$g][0]);
					}
				}
			}
		}
		else if ($ClassFormat == 2) {
			$tableCount = $this->read_ushort();
			for ($i=0;$i<$tableCount;$i++) {
				$startGlyphID = $this->read_ushort();
				$endGlyphID = $this->read_ushort();
				$class = $this->read_ushort();
				for($g=$startGlyphID;$g<=$endGlyphID;$g++) {
					if ($this->glyphToChar[$g][0]) {
						$GlyphByClass[$class][] = unicode_hex($this->glyphToChar[$g][0]);
					}
				}
			}
		}
		$gbc = array();
		foreach($GlyphByClass AS $class=>$garr) { $gbc[$class] = implode('|',$garr); }
		return $gbc;
	}
	//////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////
	function _getGPOStables() {
		///////////////////////////////////
		// GPOS - Glyph Positioning
		///////////////////////////////////
		if (isset($this->tables["GPOS"])) { 
			$this->mpdf->WriteHTML('<h1>GPOS Tables</h1>'); 
			$ffeats = array();
			$gpos_offset = $this->seek_table("GPOS");
			$this->skip(4);
			$ScriptList_offset = $gpos_offset + $this->read_ushort();
			$FeatureList_offset = $gpos_offset + $this->read_ushort();
			$LookupList_offset = $gpos_offset + $this->read_ushort();

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
				$ffeats[$t] 