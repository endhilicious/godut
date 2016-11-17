16) + (ord($s[1])<<16) + (ord($s[2])<<8) + ord($s[3]); // 	16777216  = 1<<24
	}

	function pack_short($val) {
		if ($val<0) { 
			$val = abs($val);
			$val = ~$val;
			$val += 1;
		}
		return pack("n",$val); 
	}

	function splice($stream, $offset, $value) {
		return substr($stream,0,$offset) . $value . substr($stream,$offset+strlen($value));
	}

	function _set_ushort($stream, $offset, $value) {
		$up = pack("n", $value);
		return $this->splice($stream, $offset, $up);
	}

	function _set_short($stream, $offset, $val) {
		if ($val<0) { 
			$val = abs($val);
			$val = ~$val;
			$val += 1;
		}
		$up = pack("n",$val); 
		return $this->splice($stream, $offset, $up);
	}

	function get_chunk($pos, $length) {
		fseek($this->fh,$pos);
		if ($length <1) { return ''; }
		return (fread($this->fh,$length));
	}

	function get_table($tag) {
		list($pos, $length) = $this->get_table_pos($tag);
		if ($length == 0) { return ''; }
		fseek($this->fh,$pos);
		return (fread($this->fh,$length));
	}

	function add($tag, $data) {
		if ($tag == 'head') {
			$data = $this->splice($data, 8, "\0\0\0\0");
		}
		$this->otables[$tag] = $data;
	}




/////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////

	function extractInfo($debug=false, $BMPonly=false, $kerninfo=false, $useOTL=0) {
		$this->panose = array();
		$this->sFamilyClass = 0;
		$this->sFamilySubClass = 0;
		///////////////////////////////////
		// name - Naming table
		///////////////////////////////////
			$name_offset = $this->seek_table("name");
			$format = $this->read_ushort();
			if ($format != 0 && $format != 1)
				die("Unknown name table format ".$format);
			$numRecords = $this->read_ushort();
			$string_data_offset = $name_offset + $this->read_ushort();
			$names = array(1=>'',2=>'',3=>'',4=>'',6=>'');
			$K = array_keys($names);
			$nameCount = count($names);
			for ($i=0;$i<$numRecords; $i++) {
				$platformId = $this->read_ushort();
				$encodingId = $this->read_ushort();
				$languageId = $this->read_ushort();
				$nameId = $this->read_ushort();
				$length = $this->read_ushort();
				$offset = $this->read_ushort();
				if (!in_array($nameId,$K)) continue;
				$N = '';
				if ($platformId == 3 && $encodingId == 1 && $languageId == 0x409) { // Microsoft, Unicode, US English, PS Name
					$opos = $this->_pos;
					$this->seek($string_data_offset + $offset);
					if ($length % 2 != 0)
						die("PostScript name is UTF-16BE string of odd length");
					$length /= 2;
					$N = '';
					while ($length > 0) {
						$char = $this->read_ushort();
						$N .= (chr($char));
						$length -= 1;
					}
					$this->_pos = $opos;
					$this->seek($opos);
				}
				else if ($platformId == 1 && $encodingId == 0 && $languageId == 0) { // Macintosh, Roman, English, PS Name
					$opos = $this->_pos;
					$N = $this->get_chunk($string_data_offset + $offset, $length);
					$this->_pos = $opos;
					$this->seek($opos);
				}
				if ($N && $names[$nameId]=='') {
					$names[$nameId] = $N;
					$nameCount -= 1;
					if ($nameCount==0) break;
				}
			}
			if ($names[6])
				$psName = $names[6];
			else if ($names[4])
				$psName = preg_replace('/ /','-',$names[4]);
			else if ($names[1])
				$psName = preg_replace('/ /','-',$names[1]);
			else
				$psName = '';
			if (!$psName)
				die("Could not find PostScript font name: ".$this->filename);
			if ($debug) {
			   for ($i=0;$i<count($psName);$i++) {
				$c = $psName[$i];
				$oc = ord($c);
				if ($oc>126 || strpos(' [](){}<>/%',$c)!==false)
					die("psName=".$psName." contains invalid character ".$c." ie U+".ord(c));
			   }
			}
			$this->name = $psName;