e >= $this->MaxCode) {
				$this->Stack[$this->sp++] = $this->FirstCode;
				$Code = $this->OldCode;
			}

			while($Code >= $this->ClearCode) {
				$this->Stack[$this->sp++] = $this->Vals[$Code];

				if($Code == $this->Next[$Code]) // Circular table entry, big GIF Error!
					return -1;

				$Code = $this->Next[$Code];
			}

			$this->FirstCode = $this->Vals[$Code];
			$this->Stack[$this->sp++] = $this->FirstCode;

			if(($Code = $this->MaxCode) < (1 << $this->MAX_LZW_BITS)) {
				$this->Next[$Code] = $this->OldCode;
				$this->Vals[$Code] = $this->FirstCode;
				$this->MaxCode++;

				if(($this->MaxCode >= $this->MaxCodeSize) && ($this->MaxCodeSize < (1 << $this->MAX_LZW_BITS))) {
					$this->MaxCodeSize *= 2;
					$this->CodeSize++;
				}
			}

			$this->OldCode = $InCode;
			if($this->sp > 0) {
				$this->sp--;
				return $this->Stack[$this->sp];
			}
		}

		return $Code;
	}

	///////////////////////////////////////////////////////////////////////////

	function GetCodeInit(&$data, &$dp)
	{
			$this->CurBit   = 0;
			$this->LastBit  = 0;
			$this->Done     = 0;
			$this->LastByte = 2;
			return 1;
	}

	function GetCode(&$data, &$dp)
	{
		if(($this->CurBit + $this->CodeSize) >= $this->LastBit) {
			if($this->Done) {
				if($this->CurBit >= $this->LastBit) {
					// Ran off the end of my bits
					return 0;
				}
				return -1;
			}

			$this->Buf[0] = $this->Buf[$this->LastByte - 2];
			$this->Buf[1] = $this->Buf[$this->LastByte - 1];

			$Count = ord($data[$dp]);
			$dp += 1;

			if($Count) {
				for($i = 0; $i < $Count; $i++) {
					$this->Buf[2 + $i] = ord($data[$dp+$i]);
				}
				$dp += $Count;
			}
			else {
				$this->Done = 1;
			}

			$this->LastByte = 2 + $Count;
			$this->CurBit   = ($this->CurBit - $this->LastBit) + 16;
			$this->LastBit  = (2 + $Count) << 3;
		}

		$iRet = 0;
		for($i = $this->CurBit, $j = 0; $j < $this->CodeSize; $i++, $j++) {
			$iRet |= (($this->Buf[intval($i / 8)] & (1 << ($i % 8))) != 0) << $j;
		}

		$this->CurBit += $this->CodeSize;
		return $iRet;
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////

class CGIFCOLORTABLE
{
	var $m_nColors;
	var $m_arColors;

	///////////////////////////////////////////////////////////////////////////

	// CONSTRUCTOR
	function CGIFCOLORTABLE()
	{
		unSet($this->m_nColors);
		unSet($this->m_arColors);
	}

	///////////////////////////////////////////////////////////////////////////

	function load($lpData, $num)
	{
		$this->m_nColors  = 0;
		$this->m_arColors = array();

		for($i = 0; $i < $num; $i++) {
			$rgb = substr($lpData, $i * 3, 3);
			if(strlen($rgb) < 3) {
				return false;
			}

			$this->m_arColors[] = (ord($rgb[2]) << 16) + (ord($rgb[1]) << 8) + ord($rgb[0]);
			$this->m_nColors++;
		}

		return true;
	}

	///////////////////////////////////////////////////////////////////////////

	function toString()
	{
		$ret = "";

		for($i = 0; $i < $this->m_nColors; $i++) {
			$ret .=
				chr(($this->m_arColors[$i] & 0x000000FF))       . // R
				chr(($this->m_arColors[$i] & 0x0000FF00) >>  8) . // G
				chr(($this->m_arColors[$i] & 0x00FF0000) >> 16);  // B
		}

		return $ret;
	}


	///////////////////////////////////////////////////////////////////////////

	function colorIndex($rgb)
	{
		$rgb  = intval($rgb) & 0xFFFFFF;
		$r1   = ($rgb & 0x0000FF);
		$g1   = ($rgb & 0x00FF00) >>  8;
		$b1   = ($rgb & 0xFF0000) >> 16;
		$idx  = -1;

		for($i = 0; $i < $this->m_nColors; $i++) {
			$r2 = ($this->m_arColors[$i] & 0x000000FF);
			$g2 = ($this->m_arColors[$i] & 0x0000FF00) >>  8;
			$b2 = ($this->m_arColors[$i] & 0x00FF0000) >> 16;
			$d  = abs($r2 - $r1) + abs($g2 - $g1) + abs($b2 - $b1);

			if(($idx == -1) || ($d < $dif)) {
				$idx = $i;
				$dif = $d;
			}
		}

		return $idx;
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////

class CGIFFILEHEADER
{
	var $m_lpVer;
	var $m_nWidth;
	var $m_nHeight;
	var $m_bGlobalClr;
	var $m_nColorRes;
	var $m_bSorted;
	var $m_nTableSize;
	var $m_nBgColor;
	var $m_nPixelRatio;
	var $m_colorTable;

	///////////////////////////////////////////////////////////////////////////

	// CONSTRUCTOR
	function CGIFFILEHEADER()
	{
		unSet($this->m_lpVer);
		unSet($this->m_nWidth);