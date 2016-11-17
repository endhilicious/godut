ce = true; // UPC-E mode
		}
		$data_len = $len - 1;
		//Padding
		$code = str_pad($code, $data_len, '0', STR_PAD_LEFT);
		$code_len = strlen($code);
		// calculate check digit
		$sum_a = 0;
		for ($i = 1; $i < $data_len; $i+=2) {
			$sum_a += $code[$i];
		}
		if ($len > 12) {
			$sum_a *= 3;
		}
		$sum_b = 0;
		for ($i = 0; $i < $data_len; $i+=2) {
			$sum_b += ($code[$i]);
		}
		if ($len < 13) {
			$sum_b *= 3;
		}
		$r = ($sum_a + $sum_b) % 10;
		if($r > 0) {
			$r = (10 - $r);
		}
		if ($code_len == $data_len) {
			// add check digit
			$code .= $r;
			$checkdigit = $r;
		} elseif ($r !== intval($code[$data_len])) {
			// wrong checkdigit
			return false;
		}
		if ($len == 12) {
			// UPC-A
			$code = '0'.$code;
			++$len;
		}
		if ($upce) {
			// convert UPC-A to UPC-E
			$tmp = substr($code, 4, 3);
			$prod_code = intval(substr($code,7,5));	// product code
			$invalid_upce = false;
			if (($tmp == '000') OR ($tmp == '100') OR ($tmp == '200')) {
				// manufacturer code ends in 000, 100, or 200
				$upce_code = substr($code, 2, 2).substr($code, 9, 3).substr($code, 4, 1);
				if ($prod_code > 999) { $invalid_upce = true; }
			} else {
				$tmp = substr($code, 5, 2);
				if ($tmp == '00') {
					// manufacturer code ends in 00
					$upce_code = substr($code, 2, 3).substr($code, 10, 2).'3';
					if ($prod_code > 99) { $invalid_upce = true; }
				} else {
					$tmp = substr($code, 6, 1);
					if ($tmp == '0') {
						// manufacturer code ends in 0
						$upce_code = substr($code, 2, 4).substr($code, 11, 1).'4';
						if ($prod_code > 9) { $invalid_upce = true; }
					} else {
						// manufacturer code does not end in zero
						$upce_code = substr($code, 2, 5).substr($code, 11, 1);
						if ($prod_code > 9) { $invalid_upce = true; }
					}
				}
			}
			if ($invalid_upce) { die("Error - UPC-A cannot produce a valid UPC-E barcode"); }	// Error generating a UPCE code
		}
		//Convert digits to bars
		$codes = array(
			'A'=>array( // left odd parity
				'0'=>'0001101',
				'1'=>'0011001',
				'2'=>'0010011',
				'3'=>'0111101',
				'4'=>'0100011',
				'5'=>'0110001',
				'6'=>'0101111',
				'7'=>'0111011',
				'8'=>'0110111',
				'9'=>'0001011'),
			'B'=>array( // left even parity
				'0'=>'0100111',
				'1'=>'0110011',
				'2'=>'0011011',
				'3'=>'0100001',
				'4'=>'0011101',
				'5'=>'0111001',
				'6'=>'0000101',
				'7'=>'0010001',
				'8'=>'0001001',
				'9'=>'0010111'),
			'C'=>array( // right
				'0'=>'1110010',
				'1'=>'1100110',
				'2'=>'1101100',
				'3'=>'1000010',
				'4'=>'1011100',
				'5'=>'1001110',
				'6'=>'1010000',
				'7'=>'1000100',
				'8'=>'1001000',
				'9'=>'1110100')
		);
		$parities = array(
			'0'=>array('A','A','A','A','A','A'),
			'1'=>array('A','A','B','A','B','B'),
			'2'=>array('A','A','B','B','A','B'),
			'3'=>array('A','A','B','B','B','A'),
			'4'=>array('A','B','A','A','B','B'),
			'5'=>array('A','B','B','A','A','B'),
			'6'=>array('A','B','B','B','A','A'),
			'7'=>array('A','B','A','B','A','B'),
			'8'=>array('A','B','A','B','B','A'),
			'9'=>array('A','B','B','A','B','A')
		);
		$upce_parities = array();
		$upce_parities[0] = array(
			'0'=>array('B','B','B','A','A','A'),
			'1'=>array('B','B','A','B','A','A'),
			'2'=>array('B','B','A','A','B','A'),
			'3'=>array('B','B','A','A','A','B'),
			'4'=>array('B','A','B','B','A','A'),
			'5'=>array('B','A','A','B','B','A'),
			'6'=>array('B','A','A','A','B','B'),
			'7'=>array('B','A','B','A','B','A'),
			'8'=>array('B','A','B','A','A','B'),
			'9'=>array('B','A','A','B','A','B')
		);
		$upce_parities[1] = array(
			'0'=>array('A','A','A','B','B','B'),
			'1'=>array('A','A','B','A','B','B'),
			'2'=>array('A','A','B','B','A','B'),
			'3'=>array('A','A','B','B','B','A'),
			'4'=>array('A','B','A','A','B','B'),
			'5'=>array('A','B','B','A','A','B'),
			'6'=>array('A','B','B','B','A','A'),
			'7'=>array('A','B','A','B','A','B'),
			'8'=>array('A','B','A','B','B','A'),
			'9'=>array('A','B','B','A','B','A')
		);
		$k = 0;
		$seq = '101'; // left guard bar
		if ($upce) {
			$bararray = array('code' => $upce_code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
			$p = $upce_parities[$code{1}][$r];
			for ($i = 0; $i < 6; ++$i) {
				$seq .= $codes[$p[$i]][$upce_code[$i]];
			}
			$seq .= '010101'; // right guard bar
		} else {
			$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
			$half_len = ceil($len / 2);
			if ($len == 8) {
				for ($i = 0; $i < $half_len; ++$i) {
					$seq .= $codes['A'][$code[$i]];
				}
			} else {
				$p = $parities[$code{0}];
				for ($i = 1; $i < $half_len; ++$i) {
					$seq .= $codes[$p[$i-1]][$code[$i]];
				}
			}
			$seq .= '01010'; // center guard bar
			for ($i = $half_len; $i < $len; ++$i) {
				$seq .= $codes['C'][$code[$i]];
			}
			$seq .= '101'; // right guard bar
		}
		$clen = strlen($seq);
		$w = 0;
		for ($i = 0; $i < $clen; ++$i) {
			$w += 1;
			if (($i == ($clen - 1)) OR (($i < ($clen - 1)) AND ($seq[$i] != $seq[($i+1)]))) {
				if ($seq[$i] == '1') {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
				$w = 0;
			}
		}
		$bararray['checkdigit'] = $checkdigit;
		return $bararray;
	}
	
	/**
	 * UPC-Based Extentions
	 * 2-Digit Ext.: Used to indicate magazines and newspaper issue numbers
	 * 5-Digit Ext.: Used to mark suggested retail price of books
	 */
	protected function barcode_eanext($code, $len=5) {
		//Padding
		$code = str_pad($code, $len, '0', STR_PAD_LEFT);
		// calculate check digit
		if ($len == 2) {
			$r = $code % 4;
		} elseif ($len == 5) {
			$r = (3 * ($code{0} + $code{2} + $code{4})) + (9 * ($code{1} + $code{3}));
			$r %= 10;
		} else {
			return false;
		}
		//Convert digits to bars
		$codes = array(
			'A'=>array( // left odd parity
				'0'=>'0001101',
				'1'=>'0011001',
				'2'=>'0010011',
				'3'=>'0111101',
				'4'=>'0100011',
				'5'=>'0110001',
				'6'=>'0101111',
				'7'=>'0111011',
				'8'=>'0110111',
				'9'=>'0001011'),
			'B'=>array( // left even parity
				'0'=>'0100111',
				'1'=>'0110011',
				'2'=>'0011011',
				'3'=>'0100001',
				'4'=>'0011101',
				'5'=>'0111001',
				'6'=>'0000101',
				'7'=>'0010001',
				'8'=>'0001001',
				'9'=>'0010111')
		);
		$parities = array();
		$parities[2] = array(
			'0'=>array('A','A'),
			'1'=>array('A','B'),
			'2'=>array('B','A'),
			'3'=>array('B','B')
		);
		$parities[5] = array(
			'0'=>array('B','B','A','A','A'),
			'1'=>array('B','A','B','A','A'),
			'2'=>array('B','A','A','B','A'),
			'3'=>array('B','A','A','A','B'),
			'4'=>array('A','B','B','A','A'),
			'5'=>array('A','A','B','B','A'),
			'6'=>array('A','A','A','B','B'),
			'7'=>array('A','B','A','B','A'),
			'8'=>array('A','B','A','A','B'),
			'9'=>array('A','A','B','A','B')
		);	
		$p = $parities[$len][$r];
		$seq = '1011'; // left guard bar
		$seq .= $codes[$p[0]][$code{0}];
		for ($i = 1; $i < $len; ++$i) {
			$seq .= '01'; // separator
			$seq .= $codes[$p[$i]][$code[$i]];
		}
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		return $this->binseq_to_array($seq, $bararray);
	}
	
	/**
	 * POSTNET and PLANET barcodes.
	 * Used by U.S. Postal Service for automated mail sorting
	 */
	protected function barcode_postnet($code, $planet=false) {
		// bar lenght
		if ($planet) {
			$barlen = Array(
				0 => Array(1,1,2,2,2),
				1 => Array(2,2,2,1,1),
				2 => Array(2,2,1,2,1),
				3 => Array(2,2,1,1,2),
				4 => Array(2,1,2,2,1),
				5 => Array(2,1,2,1,2),
				6 => Array(2,1,1,2,2),
				7 => Array(1,2,2,2,1),
				8 => Array(1,2,2,1,2),
				9 => Array(1,2,1,2,2)
			);
		} else {
			$barlen = Array(
				0 => Array(2,2,1,1,1),
				1 => Array(1,1,1,2,2),
				2 => Array(1,1,2,1,2),
				3 => Array(1,1,2,2,1),
				4 => Array(1,2,1,1,2),
				5 => Array(1,2,1,2,1),
				6 => Array(1,2,2,1,1),
				7 => Array(2,1,1,1,2),
				8 => Array(2,1,1,2,1),
				9 => Array(2,1,2,1,1)
			);
		}
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 5, 'bcode' => array());
		$k = 0;
		$code = str_replace('-', '', $code);
		$code = str_replace(' ', '', $code);
		$len = strlen($code);
		// calculate checksum
		$sum = 0;
		for ($i = 0; $i < $len; ++$i) {
			$sum += intval($code[$i]);
		}
		$chkd = ($sum % 10);
		if($chkd > 0) {
			$chkd = (10 - $chkd);
		}
		$code .= $chkd;
		$checkdigit = $chkd;
		$len = strlen($code);
		// start bar
		$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => 5, 'p' => 0);
		$bararray['bcode'][$k++] = array('t' => 0, 'w' => $this->gapwidth , 'h' => 5, 'p' => 0);
		$bararray['maxw'] += (1 + $this->gapwidth );
		for ($i = 0; $i < $len; ++$i) {
			for ($j = 0; $j < 5; ++$j) {
				$bh = $barlen[$code[$i]][$j];
				if ($bh == 2) {
					$h = 5; 
					$p = 0;
				}
				else {
					$h = 2; 
					$p = 3;
				}
				$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => $h, 'p' => $p);
				$bararray['bcode'][$k++] = array('t' => 0, 'w' => $this->gapwidth , 'h' => 2, 'p' => 0);
				$bararray['maxw'] += (1 + $this->gapwidth );
			}
		}
		// end bar
		$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => 5, 'p' => 0);
		$bararray['maxw'] += 1;
		$bararray['checkdigit'] = $checkdigit;
		return $bararray;
	}
	
	/**
	 * RM4SCC - CBC - KIX
	 * RM4SCC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code) - KIX (Klant index - Customer index)
	 * RM4SCC is the name of the barcode symbology used by the Royal Mail for its Cleanmail service.
	 */
	protected function barcode_rm4scc($code, $kix=false) {
		$notkix = !$kix;
		// bar mode
		// 1 = pos 1, length 2
		// 2 = pos 1, length 3
		// 3 = pos 2, length 1
		// 4 = pos 2, length 2
		$barmode = array(
			'0' => array(3,3,2,2),
			'1' => array(3,4,1,2),
			'2' => array(3,4,2,1),
			'3' => array(4,3,1,2),
			'4' => array(4,3,2,1),
			'5' => array(4,4,1,1),
			'6' => array(3,1,4,2),
			'7' => array(3,2,3,2),
			'8' => array(3,2,4,1),
			'9' => array(4,1,3,2),
			'A' => array(4,1,4,1),
			'B' => array(4,2,3,1),
			'C' => array(3,1,2,4),
			'D' => array(3,2,1,4),
			'E' => array(3,2,2,3),
			'F' => array(4,1,1,4),
			'G' => array(4,1,2,3),
			'H' => array(4,2,1,3),
			'I' => array(1,3,4,2),
			'J' => array(1,4,3,2),
			'K' => array(1,4,4,1),
			'L' => array(2,3,3,2),
			'M' => array(2,3,4,1),
			'N' => array(2,4,3,1),
			'O' => array(1,3,2,4),
			'P' => array(1,4,1,4),
			'Q' => array(1,4,2,3),
			'R' => array(2,3,1,4),
			'S' => array(2,3,2,3),
			'T' => array(2,4,1,3),
			'U' => array(1,1,4,4),
			'V' => array(1,2,3,4),
			'W' => array(1,2,4,3),
			'X' => array(2,1,3,4),
			'Y' => array(2,1,4,3),
			'Z' => array(2,2,3,3)		
		);
		$code = strtoupper($code);
		$len = strlen($code);
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => $this->daft['F'], 'bcode' => array());
		if ($notkix) {
			// table for checksum calculation (row,col)
			$checktable = array(
				'0' => array(1,1),
				'1' => array(1,2),
				'2' => array(1,3),
				'3' => array(1,4),
				'4' => array(1,5),
				'5' => array(1,0),
				'6' => array(2,1),
				'7' => array(2,2),
				'8' => array(2,3),
				'9' => array(2,4),
				'A' => array(2,5),
				'B' => array(2,0),
				'C' => array(3,1),
				'D' => array(3,2),
				'E' => array(3,3),
				'F' => array(3,4),
				'G' => array(3,5),
				'H' => array(3,0),
				'I' => array(4,1),
				'J' => array(4,2),
				'K' => array(4,3),
				'L' => array(4,4),
				'M' => array(4,5),
				'N' => array(4,0),
				'O' => array(5,1),
				'P' => array(5,2),
				'Q' => array(5,3),
				'R' => array(5,4),
				'S' => array(5,5),
				'T' => array(5,0),
				'U' => array(0,1),
				'V' => array(0,2),
				'W' => array(0,3),
				'X' => array(0,4),
				'Y' => array(0,5),
				'Z' => array(0,0)
			);
			$row = 0;
			$col = 0;
			for ($i = 0; $i < $len; ++$i) {
				$row += $checktable[$code[$i]][0];
				$col += $checktable[$code[$i]][1];
			}
			$row %= 6;
			$col %= 6;
			$chk = array_keys($checktable, array($row,$col));
			$code .= $chk[0];
			$bararray['checkdigit'] = $chk[0];
			++$len;
		}
		$k = 0;
		if ($notkix) {
			// start bar
			$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => $this->daft['A'] , 'p' => 0);
			$bararray['bcode'][$k++] = array('t' => 0, 'w' => $this->gapwidth , 'h' => $this->daft['A'] , 'p' => 0);
			$bararray['maxw'] += (1 + $this->gapwidth) ;
		}
		for ($i = 0; $i < $len; ++$i) {
			for ($j = 0; $j < 4; ++$j) {
				switch ($barmode[$code[$i]][$j]) {
					case 1: {
						// ascender (A)
						$p = 0;
						$h = $this->daft['A'];
						break;
					}
					case 2: {
						// full bar (F)
						$p = 0;
						$h = $this->daft['F'];
						break;
					}
					case 3: {
						// tracker (T)
						$p = ($this->daft['F'] - $this->daft['T'])/2;
						$h = $this->daft['T'];
						break;
					}
					case 4: {
						// descender (D)
						$p = $this->daft['F'] - $this->daft['D'];
						$h = $this->daft['D'];
						break;
					}
				}

				$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => $h, 'p' => $p);
				$bararray['bcode'][$k++] = array('t' => 0, 'w' => $this->gapwidth, 'h' => 2, 'p' => 0);
				$bararray['maxw'] += (1 + $this->gapwidth) ;
			}
		}
		if ($notkix) {
			// stop bar
			$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => $this->daft['F'], 'p' => 0);
			$bararray['maxw'] += 1;
		}
		return $bararray;
	}
	
	/**
	 * CODABAR barcodes.
	 * Older code often used in library systems, sometimes in blood banks
	 */
	protected function barcode_codabar($code) {
		$chr = array(
			'0' => '11111221',
			'1' => '11112211',
			'2' => '11121121',
			'3' => '22111111',
			'4' => '11211211',
			'5' => '21111211',
			'6' => '12111121',
			'7' => '12112111',
			'8' => '12211111',
			'9' => '21121111',
			'-' => '11122111',
			'$' => '11221111',
			':' => '21112121',
			'/' => '21211121',
			'.' => '21212111',
			'+' => '11222221',
			'A' => '11221211',
			'B' => '12121121',
			'C' => '11121221',
			'D' => '11122211'
		);
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$w = 0;
		$seq = '';
		$code = strtoupper($code);
		$len = strlen($code);
		for ($i = 0; $i < $len; ++$i) {
			if (!isset($chr[$code[$i]])) {
				return false;
			}
			$seq = $chr[$code[$i]];
			for ($j = 0; $j < 8; ++$j) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$x = $seq[$j];
				if ($x == 2) { $w = $this->print_ratio; }
				else { $w = 1; }
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
			}
		}
		return $bararray;
	}
	
	/**
	 * CODE11 barcodes.
	 * Used primarily for labeling telecommunications equipment
	 */
	protected function barcode_code11($code) {
		$chr = array(
			'0' => '111121',
			'1' => '211121',
			'2' => '121121',
			'3' => '221111',
			'4' => '112121',
			'5' => '212111',
			'6' => '122111',
			'7' => '111221',
			'8' => '211211',
			'9' => '211111',
			'-' => '112111',
			'S' => '112211'
		);
		
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$w = 0;
		$seq = '';
		$len = strlen($code);
		// calculate check digit C
		$p = 1;
		$check = 0;
		for ($i = ($len - 1); $i >= 0; --$i) {
			$digit = $code[$i];
			if ($digit == '-') {
				$dval = 10;
			} else {
				$dval = intval($digit);
			}
			$check += ($dval * $p);
			++$p;
			if ($p > 10) {
				$p = 1;
			}
		}
		$check %= 11;
		if ($check == 10) {
			$check = '-';
		} 
		$code .= $check;
		$checkdigit = $check;
		if ($len > 10) {
			// calculate check digit K
			$p = 1;
			$check = 0;
			for ($i = $len; $i >= 0; --$i) {
				$digit = $code[$i];
				if ($digit == '-') {
					$dval = 10;
				} else {
					$dval = intval($digit);
				}
				$check += ($dval * $p);
				++$p;
				if ($p > 9) {
					$p = 1;
				}
			}
			$check %= 11;
			$code .= $check;
			$checkdigit .= $check;
			++$len;
		}
		$code = 'S'.$code.'S';
		$len += 3;
		for ($i = 0; $i < $len; ++$i) {
			if (!isset($chr[$code[$i]])) {
				return false;
			}
			$seq = $chr[$code[$i]];
			for ($j = 0; $j < 6; ++$j) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$x = $seq[$j];
				if ($x == 2) { $w = $this->print_ratio; }
				else { $w = 1; }
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
			}
		}
		$bararray['checkdigit'] = $checkdigit;
		return $bararray;
	}
	
	
	/**
	 * IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200
	 * (requires PHP bcmath extension) 
	 * Intelligent Mail barcode is a 65-bar code for use on mail in the United States.
	 * The fields are described as follows:<ul><li>The Barcode Identifier shall be assigned by USPS to encode the presort identification that is currently printed in human readable form on the optional endorsement line (OEL) as well as for future USPS use. This shall be two digits, with the second digit in the range of 0-4. The allowable encoding ranges shall be 00-04, 10-14, 20-24, 30-34, 40-44, 50-54, 60-64, 70-74, 80-84, and 90-94.</li><li>The Service Type Identifier shall be assigned by USPS for any combination of services requested on the mailpiece. The allowable encoding range shall be 000-999. Each 3-digit value shall correspond to a particular mail class with a particular combination of service(s). Each service program, such as OneCode Confirm and OneCode ACS, shall provide the list of Service Type Identifier values.</li><li>The Mailer or Customer Identifier shall be assigned by USPS as a unique, 6 or 9 digit number that identifies a business entity. The allowable encoding range for the 6 digit Mailer ID shall be 000000- 899999, while the allowable encoding range for the 9 digit Mailer ID shall be 900000000-999999999.</li><li>The Serial or Sequence Number shall be assigned by the mailer for uniquely identifying and tracking mailpieces. The allowable encoding range shall be 000000000-999999999 when used with a 6 digit Mailer ID and 000000-999999 when used with a 9 digit Mailer ID. e. The Delivery Point ZIP Code shall be assigned by the mailer for routing the mailpiece. This shall replace POSTNET for routing the mailpiece to its final delivery point. The length may be 0, 5, 9, or 11 digits. The allowable encoding ranges shall be no ZIP Code, 00000-99999,  000000000-999999999, and 00000000000-99999999999.</li></ul>
	 */
	protected function barcode_imb($code) {
		$asc_chr = array(4,0,2,6,3,5,1,9,8,7,1,2,0,6,4,8,2,9,5,3,0,1,3,7,4,6,8,9,2,0,5,1,9,4,3,8,6,7,1,2,4,3,9,5,7,8,3,0,2,1,4,0,9,1,7,0,2,4,6,3,7,1,9,5,8);
		$dsc_chr = array(7,1,9,5,8,0,2,4,6,3,5,8,9,7,3,0,6,1,7,4,6,8,9,2,5,1,7,5,4,3,8,7,6,0,2,5,4,9,3,0,1,6,8,2,0,4,5,9,6,7,5,2,6,3,8,5,1,9,8,7,4,0,2,6,3);
		$asc_pos = array(3,0,8,11,1,12,8,11,10,6,4,12,2,7,9,6,7,9,2,8,4,0,12,7,10,9,0,7,10,5,7,9,6,8,2,12,1,4,2,0,1,5,4,6,12,1,0,9,4,7,5,10,2,6,9,11,2,12,6,7,5,11,0,3,2);
		$dsc_pos = array(2,10,12,5,9,1,5,4,3,9,11,5,10,1,6,3,4,1,10,0,2,11,8,6,1,12,3,8,6,4,4,11,0,6,1,9,11,5,3,7,3,10,7,11,8,2,10,3,5,8,0,3,12,11,8,4,5,1,3,0,7,12,9,8,10);
		$code_arr = explode('-', $code);
		$tracking_number = $code_arr[0];
		if (isset($code_arr[1])) {
			$routing_code = $code_arr[1];
		} else {
			$routing_code = '';
		}
		// Conversion of Routing Code
		switch (strlen($routing_code)) {
			case 0: {
				$binary_code = 0;
				break;
			}
			case 5: {
				$binary_code = bcadd($routing_code, '1');
				break;
			}
			case 9: {
				$binary_code = bcadd($routing_code, '100001');
				break;
			}
			case 11: {
				$binary_code = bcadd($routing_code, '1000100001');
				break;
			}
			default: {
				return false;
				break;
			}
		}
		$binary_code = bcmul($binary_code, 10);
		$binary_code = bcadd($binary_code, $tracking_number{0});
		$binary_code = bcmul($binary_code, 5);
		$binary_code = bcadd($binary_code, $tracking_number{1});
		$binary_code .= substr($tracking_number, 2, 18);
		// convert to hexadecimal
		$binary_code = $this->dec_to_hex($binary_code);
		// pad to get 13 bytes
		$binary_code = str_pad($binary_code, 26, '0', STR_PAD_LEFT);
		// convert string to array of bytes
		$binary_code_arr = chunk_split($binary_code, 2, "\r");
		$binary_code_arr = substr($binary_code_arr, 0, -1);
		$binary_code_arr = explode("\r", $binary_code_arr);
		// calculate frame check sequence
		$fcs = $this->imb_crc11fcs($binary_code_arr);
		// exclude first 2 bits from first byte
		$first_byte = sprintf('%2s', dechex((hexdec($binary_code_arr[0]) << 2) >> 2));
		$binary_code_102bit = $first_byte.substr($binary_code, 2);
		// convert binary data to codewords
		$codewords = array();
		$data = $this->hex_to_dec($binary_code_102bit);
		$codewords[0] = bcmod($data, 636) * 2;
		$data = bcdiv($data, 636);
		for ($i = 1; $i < 9; ++$i) {
			$codewords[$i] = bcmod($data, 1365);
			$data = bcdiv($data, 1365);
		}
		$codewords[9] = $data;
		if (($fcs >> 10) == 1) {
			$codewords[9] += 659;
		}
		// generate lookup tables
		$table2of13 = $this->imb_tables(2, 78);
		$table5of13 = $this->imb_tables(5, 1287);
		// convert codewords to characters
		$characters = array();
		$bitmask = 512;
		foreach($codewords as $k => $val) {
			if ($val <= 1286) {
				$chrcode = $table5of13[$val];
			} else {
				$chrcode = $table2of13[($val - 1287)];
			}
			if (($fcs & $bitmask) > 0) {
				// bitwise invert
				$chrcode = ((~$chrcode) & 8191);
			}
			$characters[] = $chrcode;
			$bitmask /= 2;
		}
		$characters = array_reverse($characters);
		// build bars
		$k = 0;
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => $this->daft['F'], 'bcode' => array());
		for ($i = 0; $i < 65; ++$i) {
			$asc = (($characters[$asc_chr[$i]] & pow(2, $asc_pos[$i])) > 0);
			$dsc = (($characters[$dsc_chr[$i]] & pow(2, $dsc_pos[$i])) > 0);
			if ($asc AND $dsc) {
				// full bar (F)
				$p = 0;
				$h = $this->daft['F'];
			} elseif ($asc) {
				// ascender (A)
				$p = 0;
				$h = $this->daft['A'];
			} elseif ($dsc) {
				// descender (D)
				$p = $this->daft['F'] - $this->daft['D'];
				$h = $this->daft['D'];
			} else {
				// tracker (T)
				$p = ($this->daft['F'] - $this->daft['T'])/2;
				$h = $this->daft['T'];
			}
			$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => $h, 'p' => $p);
			// Gap
			$bararray['bcode'][$k++] = array('t' => 0, 'w' => $this->gapwidth , 'h' => 1, 'p' => 0);
			$bararray['maxw'] += (1 + $this->gapwidth );
		}
		unset($bararray['bcode'][($k - 1)]);
		$bararray['maxw'] -= $this->gapwidth ;
		return $bararray;
	}
	
	/**
	 * Convert large integer number to hexadecimal representation.
	 * (requires PHP bcmath extension) 
	 */
	public function dec_to_hex($number) {
		$i = 0;
		$hex = array();
		if($number == 0) {
			return '00';
		}
		while($number > 0) {
			if($number == 0) {
				array_push($hex, '0');
			} else {
				array_push($hex, strtoupper(dechex(bcmod($number, '16'))));
				$number = bcdiv($number, '16', 0);
			}
		}
		$hex = array_reverse($hex);
		return implode($hex);
	}
	
	/**
	 * Convert large hexadecimal number to decimal representation (string).
	 * (requires PHP bcmath extension) 
	 */
	public function hex_to_dec($hex) {
		$dec = 0;
		$bitval = 1;
		$len = strlen($hex);
		for($pos = ($len - 1); $pos >= 0; --$pos) {
			$dec = bcadd($dec, bcmul(hexdec($hex[$pos]), $bitval));
			$bitval = bcmul($bitval, 16);
		}
		return $dec;
	}	
	
	/**
	 * Intelligent Mail Barcode calculation of Frame Check Sequence
	 */
	protected function imb_crc11fcs($code_arr) {
		$genpoly = 0x0F35; // generator polynomial
		$fcs = 0x07FF; // Frame Check Sequence
		// do most significant byte skipping the 2 most significant bits
		$data = hexdec($code_arr[0]) << 5;
		for ($bit = 2; $bit < 8; ++$bit) {
			if (($fcs ^ $data) & 0x400) {
				$fcs = ($fcs << 1) ^ $genpoly;
			} else {
				$fcs = ($fcs << 1);
			}
			$fcs &= 0x7FF;
			$data <<= 1;
		}
		// do rest of bytes
		for ($byte = 1; $byte < 13; ++$byte) {
			$data = hexdec($code_arr[$byte]) << 3;
			for ($bit = 0; $bit < 8; ++$bit) {
				if (($fcs ^ $data) & 0x400) {
					$fcs = ($fcs << 1) ^ $genpoly;
				} else {
					$fcs = ($fcs << 1);
				}
				$fcs &= 0x7FF;
				$data <<= 1;
			}
		}
		return $fcs;		
	}
	
	/**
	 * Reverse unsigned short value
	 */
	protected function imb_reverse_us($num) {
		$rev = 0;
		for ($i = 0; $i < 16; ++$i) {
			$rev <<= 1;
			$rev |= ($num & 1);
			$num >>= 1;
		}
		return $rev;
	}
	
	/**
	 * generate Nof13 tables used for Intelligent Mail Barcode
	 */
	protected function imb_tables($n, $size) {
		$table = array();
		$lli = 0; // LUT lower index
		$lui = $size - 1; // LUT upper index
		for ($count = 0; $count < 8192; ++$count) {
			$bit_count = 0;
			for ($bit_index = 0; $bit_index < 13; ++$bit_index) {
				$bit_count += intval(($count & (1 << $bit_index)) != 0);
			}
			// if we don't have the right number of bits on, go on to the next value
			if ($bit_count == $n) {
				$reverse = ($this->imb_reverse_us($count) >> 3);
				// if the reverse is less than count, we have already visited this pair before
				if ($reverse >= $count) {
					// If count is symmetric, place it at the first free slot from the end of the list.
					// Otherwise, place it at the first free slot from the beginning of the list AND place $reverse ath the next free slot from the beginning of the list
					if ($reverse == $count) {
						$table[$lui] = $count;
						--$lui;
					} else {
						$table[$lli] = $count;
						++$lli;
						$table[$lli] = $reverse;
						++$lli;
					}
				}
			}
		}
		return $table;
	}
	
} // end of class

//============================================================+
// END OF FILE                                                 
//============================================================+
?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <?php

class bmp {

var $mpdf = null;

function bmp(&$mpdf) {
	$this->mpdf = $mpdf;
}


function _getBMPimage($data, $file) {
	$info = array();
		// Adapted from script by Valentin Schmidt
		// http://staff.dasdeck.de/valentin/fpdf/fpdf_bmp/
		$bfOffBits=$this->_fourbytes2int_le(substr($data,10,4));
		$width=$this->_fourbytes2int_le(substr($data,18,4));
		$height=$this->_fourbytes2int_le(substr($data,22,4));
		$flip = ($height<0);
		if ($flip) $height =-$height;
		$biBitCount=$this->_twobytes2int_le(substr($data,28,2));
		$biCompression=$this->_fourbytes2int_le(substr($data,30,4)); 
		$info = array('w'=>$width, 'h'=>$height);
		if ($biBitCount<16){
			$info['cs'] = 'Indexed';
			$info['bpc'] = $biBitCount;
			$palStr = substr($data,54,($bfOffBits-54));
			$pal = '';
			$cnt = strlen($palStr)/4;
			for ($i=0;$i<$cnt;$i++){
				$n = 4*$i;
				$pal .= $palStr[$n+2].$palStr[$n+1].$palStr[$n];
			}
			$info['pal'] = $pal;
		}
		else{
			$info['cs'] = 'DeviceRGB';
			$info['bpc'] = 8;
		}

		if ($this->mpdf->restrictColorSpace==1 || $this->mpdf->PDFX || $this->mpdf->restrictColorSpace==3) {
			if (($this->mpdf->PDFA && !$this->mpdf->PDFAauto) || ($this->mpdf->PDFX && !$this->mpdf->PDFXauto)) { $this->mpdf->PDFAXwarnings[] = "Image cannot be converted to suitable colour space for PDFA or PDFX file - ".$file." - (Image replaced by 'no-image'.)"; }
			return array('error' => "BMP Image cannot be converted to suitable colour space - ".$file." - (Image replaced by 'no-image'.)"); 
		}

		$biXPelsPerMeter=$this->_fourbytes2int_le(substr($data,38,4));	// horizontal pixels per meter, usually set to zero
		//$biYPelsPerMeter=$this->_fourbytes2int_le(substr($data,42,4));	// vertical pixels per meter, usually set to zero
		$biXPelsPerMeter=round($biXPelsPerMeter/1000 *25.4);
		//$biYPelsPerMeter=round($biYPelsPerMeter/1000 *25.4);
		$info['set-dpi'] = $biXPelsPerMeter; 

		switch ($biCompression){
		  case 0:
			$str = substr($data,$bfOffBits);
			break;
		  case 1: # BI_RLE8
			$str = $this->rle8_decode(substr($data,$bfOffBits), $width);
			break;
		  case 2: # BI_RLE4
			$str = $this->rle4_decode(substr($data,$bfOffBits), $width);
			break;
		}
		$bmpdata = '';
		$padCnt = (4-ceil(($width/(8/$biBitCount)))%4)%4;
		switch ($biBitCount){
		  case 1:
		  case 4:
		  case 8:
			$w = floor($width/(8/$biBitCount)) + ($width%(8/$biBitCount)?1:0);
			$w_row = $w + $padCnt;
			if ($flip){
				for ($y=0;$y<$height;$y++){
					$y0 = $y*$w_row;
					for ($x=0;$x<$w;$x++)
						$bmpdata .= $str[$y0+$x];
				}
			}else{
				for ($y=$height-1;$y>=0;$y--){
					$y0 = $y*$w_row;
					for ($x=0;$x<$w;$x++)
						$bmpdata .= $str[$y0+$x];
				}
			}
			break;

		  case 16:
			$w_row = $width*2 + $padCnt;
			if ($flip){
				for ($y=0;$y<$height;$y++){
					$y0 = $y*$w_row;
					for ($x=0;$x<$width;$x++){
						$n = (ord( $str[$y0 + 2*$x + 1])*256 +    ord( $str[$y0 + 2*$x]));
						$b = ($n & 31)<<3; $g = ($n & 992)>>2; $r = ($n & 31744)>>7128;
						$bmpdata .= chr($r) . chr($g) . chr($b);
					}
				}
			}else{
				for ($y=$height-1;$y>=0;$y--){
					$y0 = $y*$w_row;
					for ($x=0;$x<$width;$x++){
						$n = (ord( $str[$y0 + 2*$x + 1])*256 +    ord( $str[$y0 + 2*$x]));
						$b = ($n & 31)<<3; $g = ($n & 992)>>2; $r = ($n & 31744)>>7;
						$bmpdata .= chr($r) . chr($g) . chr($b);
					}
				}
			}
			break;

		  case 24:
		  case 32:
			$byteCnt = $biBitCount/8;
			$w_row = $width*$byteCnt + $padCnt;

			if ($flip){
				for ($y=0;$y<$height;$y++){
					$y0 = $y*$w_row;
					for ($x=0;$x<$width;$x++){
						$i = $y0 + $x*$byteCnt ; # + 1
						$bmpdata .= $str[$i+2].$str[$i+1].$str[$i];
					}
				}
			}else{
				for ($y=$height-1;$y>=0;$y--){
					$y0 = $y*$w_row;
					for ($x=0;$x<$width;$x++){
						$i = $y0 + $x*$byteCnt ; # + 1
						$bmpdata .= $str[$i+2].$str[$i+1].$str[$i];
					}
				}
			}
			break;

		  default:
			return array('error' => 'Error parsing BMP image - Unsupported image biBitCount'); 
		}
		if ($this->mpdf->compress) {
			$bmpdata=gzcompress($bmpdata);
			$info['f']='FlateDecode';
		} 
		$info['data']=$bmpdata;
		$info['type']='bmp';
		return $info;
}

function _fourbytes2int_le($s) {
	//Read a 4-byte integer from string
	return (ord($s[3])<<24) + (ord($s[2])<<16) + (ord($s[1])<<8) + ord($s[0]);
}

function _twobytes2int_le($s) {
	/