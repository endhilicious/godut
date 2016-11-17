0) => '$T', chr(21) => '$U', chr(22) => '$V', chr(23) => '$W',
			chr(24) => '$X', chr(25) => '$Y', chr(26) => '$Z', chr(27) => '%A',
			chr(28) => '%B', chr(29) => '%C', chr(30) => '%D', chr(31) => '%E',
			chr(32) => ' ', chr(33) => '/A', chr(34) => '/B', chr(35) => '/C',
			chr(36) => '/D', chr(37) => '/E', chr(38) => '/F', chr(39) => '/G',
			chr(40) => '/H', chr(41) => '/I', chr(42) => '/J', chr(43) => '/K',
			chr(44) => '/L', chr(45) => '-', chr(46) => '.', chr(47) => '/O',
			chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
			chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
			chr(56) => '8', chr(57) => '9', chr(58) => '/Z', chr(59) => '%F',
			chr(60) => '%G', chr(61) => '%H', chr(62) => '%I', chr(63) => '%J',
			chr(64) => '%V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
			chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
			chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
			chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
			chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
			chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
			chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => '%K',
			chr(92) => '%L', chr(93) => '%M', chr(94) => '%N', chr(95) => '%O',
			chr(96) => '%W', chr(97) => '+A', chr(98) => '+B', chr(99) => '+C',
			chr(100) => '+D', chr(101) => '+E', chr(102) => '+F', chr(103) => '+G',
			chr(104) => '+H', chr(105) => '+I', chr(106) => '+J', chr(107) => '+K',
			chr(108) => '+L', chr(109) => '+M', chr(110) => '+N', chr(111) => '+O',
			chr(112) => '+P', chr(113) => '+Q', chr(114) => '+R', chr(115) => '+S',
			chr(116) => '+T', chr(117) => '+U', chr(118) => '+V', chr(119) => '+W',
			chr(120) => '+X', chr(121) => '+Y', chr(122) => '+Z', chr(123) => '%P',
			chr(124) => '%Q', chr(125) => '%R', chr(126) => '%S', chr(127) => '%T');
		$code_ext = '';
		$clen = strlen($code);
		for ($i = 0 ; $i < $clen; ++$i) {
			if (ord($code[$i]) > 127) {
				return false;
			}
			$code_ext .= $encode[$code[$i]];
		}
		return $code_ext;
	}
	
	/**
	 * Calculate CODE 39 checksum (modulo 43).
	 */
	protected function checksum_code39($code) {
		$chars = array(
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
			'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
			'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
		$sum = 0;
		$clen = strlen($code);
		for ($i = 0 ; $i < $clen; ++$i) {
			$k = array_keys($chars, $code[$i]);
			$sum += $k[0];
		}
		$j = ($sum % 43);
		return $chars[$j];
	}
	
	/**
	 * CODE 93 - USS-93
	 * Compact code similar to Code 39
	 */
	protected function barcode_code93($code) {
		$chr[48] = '131112'; // 0
		$chr[49] = '111213'; // 1
		$chr[50] = '111312'; // 2
		$chr[51] = '111411'; // 3
		$chr[52] = '121113'; // 4
		$chr[53] = '121212'; // 5
		$chr[54] = '121311'; // 6
		$chr[55] = '111114'; // 7
		$chr[56] = '131211'; // 8
		$chr[57] = '141111'; // 9
		$chr[65] = '211113'; // A
		$chr[66] = '211212'; // B
		$chr[67] = '211311'; // C
		$chr[68] = '221112'; // D
		$chr[69] = '221211'; // E
		$chr[70] = '231111'; // F
		$chr[71] = '112113'; // G
		$chr[72] = '112212'; // H
		$chr[73] = '112311'; // I
		$chr[74] = '122112'; // J
		$chr[75] = '132111'; // K
		$chr[76] = '111123'; // L
		$chr[77] = '111222'; // M
		$chr[78] = '111321'; // N
		$chr[79] = '121122'; // O
		$chr[80] = '131121'; // P
		$chr[81] = '212112'; // Q
		$chr[82] = '212211'; // R
		$chr[83] = '211122'; // S
		$chr[84] = '211221'; // T
		$chr[85] = '221121'; // U
		$chr[86] = '222111'; // V
		$chr[87] = '112122'; // W
		$chr[88] = '112221'; // X
		$chr[89] = '122121'; // Y
		$chr[90] = '123111'; // Z
		$chr[45] = '121131'; // -
		$chr[46] = '311112'; // .
		$chr[32] = '311211'; //
		$chr[36] = '321111'; // $
		$chr[47] = '112131'; // /
		$chr[43] = '113121'; // +
		$chr[37] = '211131'; // %
		$chr[128] = '121221'; // ($)
		$chr[129] = '311121'; // (/)
		$chr[130] = '122211'; // (+)
		$chr[131] = '312111'; // (%)
		$chr[42] = '111141'; // start-stop
		$code = strtoupper($code);
		$encode = array(
			chr(0) => chr(131).'U', chr(1) => chr(128).'A', chr(2) => chr(128).'B', chr(3) => chr(128).'C',
			chr(4) => chr(128).'D', chr(5) => chr(128).'E', chr(6) => chr(128).'F', chr(7) => chr(128).'G',
			chr(8) => chr(128).'H', chr(9) => chr(128).'I', chr(10) => chr(128).'J', chr(11) => '£K',
			chr(12) => chr(128).'L', chr(13) => chr(128).'M', chr(14) => chr(128).'N', chr(15) => chr(128).'O',
			chr(16) => chr(128).'P', chr(17) => chr(128).'Q', chr(18) => chr(128).'R', chr(19) => chr(128).'S',
			chr(20) => chr(128).'T', chr(21) => chr(128).'U', chr(22) => chr(128).'V', chr(23) => chr(128).'W',
			chr(24) => chr(128).'X', chr(25) => chr(128).'Y', chr(26) => chr(128).'Z', chr(27) => chr(131).'A',
			chr(28) => chr(131).'B', chr(29) => chr(131).'C', chr(30) => chr(131).'D', chr(31) => chr(131).'E',
			chr(32) => ' ', chr(33) => chr(129).'A', chr(34) => chr(129).'B', chr(35) => chr(129).'C',
			chr(36) => chr(129).'D', chr(37) => chr(129).'E', chr(38) => chr(129).'F', chr(39) => chr(129).'G',
			chr(40) => chr(129).'H', chr(41) => chr(129).'I', chr(42) => chr(129).'J', chr(43) => chr(129).'K',
			chr(44) => chr(129).'L', chr(45) => '-', chr(46) => '.', chr(47) => chr(129).'O',
			chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
			chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
			chr(56) => '8', chr(57) => '9', chr(58) => chr(129).'Z', chr(59) => chr(131).'F',
			chr(60) => chr(131).'G', chr(61) => chr(131).'H', chr(62) => chr(131).'I', chr(63) => chr(131).'J',
			chr(64) => chr(131).'V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
			chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
			chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
			chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
			chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
			chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
			chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => chr(131).'K',
			chr(92) => chr(131).'L', chr(93) => chr(131).'M', chr(94) => chr(131).'N', chr(95) => chr(131).'O',
			chr(96) => chr(131).'W', chr(97) => chr(130).'A', chr(98) => chr(130).'B', chr(99) => chr(130).'C',
			chr(100) => chr(130).'D', chr(101) => chr(130).'E', chr(102) => chr(130).'F', chr(103) => chr(130).'G',
			chr(104) => chr(130).'H', chr(105) => chr(130).'I', chr(106) => chr(130).'J', chr(107) => chr(130).'K',
			chr(108) => chr(130).'L', chr(109) => chr(130).'M', chr(110) => chr(130).'N', chr(111) => chr(130).'O',
			chr(112) => chr(130).'P', chr(113) => chr(130).'Q', chr(114) => chr(130).'R', chr(115) => chr(130).'S',
			chr(116) => chr(130).'T', chr(117) => chr(130).'U', chr(118) => chr(130).'V', chr(119) => chr(130).'W',
			chr(120) => chr(130).'X', chr(121) => chr(130).'Y', chr(122) => chr(130).'Z', chr(123) => chr(131).'P',
			chr(124) => chr(131).'Q', chr(125) => chr(131).'R', chr(126) => chr(131).'S', chr(127) => chr(131).'T');
		$code_ext = '';
		$clen = strlen($code);
		for ($i = 0 ; $i < $clen; ++$i) {
			if (ord($code{$i}) > 127) {
				return false;
			}
			$code_ext .= $encode[$code{$i}];
		}
		// checksum
		$code_ext .= $this->checksum_code93($code_ext);
		// add start and stop codes
		$code = '*'.$code_ext.'*';
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$clen = strlen($code);
		for ($i = 0; $i < $clen; ++$i) {
			$char = ord($code{$i});
			if(!isset($chr[$char])) {
				// invalid character
				return false;
			}
			for ($j = 0; $j < 6; ++$j) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $chr[$char]{$j};
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
			}
		}
		$bararray['bcode'][$k] = array('t' => true, 'w' => 1, 'h' => 1, 'p' => 0);
		$bararray['maxw'] += 1;
		++$k;
		return $bararray;
	}
	
	/**
	 * Calculate CODE 93 checksum (modulo 47).
	 */
	protected function checksum_code93($code) {
		$chars = array(
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
			'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
			'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%',
			'<', '=', '>', '?');
		// translate special characters
		$code = strtr($code, chr(128).chr(131).chr(129).chr(130), '<=>?');
		$len = strlen($code);
		// calculate check digit C
		$p = 1;
		$check = 0;
		for ($i = ($len - 1); $i >= 0; --$i) {
			$k = array_keys($chars, $code{$i});
			$check += ($k[0] * $p);
			++$p;
			if ($p > 20) {
				$p = 1;
			}
		}
		$check %= 47;
		$c = $chars[$check];
		$code .= $c;
		// calculate check digit K
		$p = 1;
		$check = 0;
		for ($i = $len; $i >= 0; --$i) {
			$k = array_keys($chars, $code{$i});
			$check += ($k[0] * $p);
			++$p;
			if ($p > 15) {
				$p = 1;
			}
		}
		$check %= 47;
		$k = $chars[$check];
		$checksum = $c.$k;
		// resto respecial characters
		$checksum = strtr($checksum, '<=>?', chr(128).chr(131).chr(129).chr(130));
		return $checksum;
	}
	
	/**
	 * Checksum for standard 2 of 5 barcodes.
	 */
	protected function checksum_s25($code) {
		$len = strlen($code);
		$sum = 0;
		for ($i = 0; $i < $len; $i+=2) {
			$sum += $code[$i];
		}
		$sum *= 3;
		for ($i = 1; $i < $len; $i+=2) {
			$sum += ($code[$i]);
		}
		$r = $sum % 10;
		if($r > 0) {
			$r = (10 - $r);
		}
		return $r;
	}
	
	/**
	 * MSI.
	 * Variation of Plessey code, with similar applications 
	 * Contains digits (0 to 9) and encodes the data only in the width of bars.
	 */
	protected function barcode_msi($code, $checksum=false) {
		$chr['0'] = '100100100100';
		$chr['1'] = '100100100110';
		$chr['2'] = '100100110100';
		$chr['3'] = '100100110110';
		$chr['4'] = '100110100100';
		$chr['5'] = '100110100110';
		$chr['6'] = '100110110100';
		$chr['7'] = '100110110110';
		$chr['8'] = '110100100100';
		$chr['9'] = '110100100110';
		$chr['A'] = '110100110100';
		$chr['B'] = '110100110110';
		$chr['C'] = '110110100100';
		$chr['D'] = '110110100110';
		$chr['E'] = '110110110100';
		$chr['F'] = '110110110110';
		$checkdigit = '';
		if ($checksum) {
			// add checksum
			$clen = strlen($code);
			$p = 2;
			$check = 0;
			for ($i = ($clen - 1); $i >= 0; --$i) {
				$check += (hexdec($code[$i]) * $p);
				++$p;
				if ($p > 7) {
					$p = 2;
				}
			}
			$check %= 11;
			if ($check > 0) {
				$check = 11 - $check;
			}
			$code .= $check;
			$checkdigit = $check;
		}
		$seq = '110'; // left guard
		$clen = strlen($code);
		for ($i = 0; $i < $clen; ++$i) {
			$digit = $code[$i];
			if (!isset($chr[$digit])) {
				// invalid character
				return false;
			}
			$seq .= $chr[$digit];
		}		
		$seq .= '1001'; // right guard
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$bararray['checkdigit'] = $checkdigit;
		return $this->binseq_to_array($seq, $bararray);
	}
	
	/**
	 * Standard 2 of 5 barcodes.
	 * Used in airline ticket marking, photofinishing
	 * Contains digits (0 to 9) and encodes the data only in the width of bars.
	 */
	protected function barcode_s25($code, $checksum=false) {
		$chr['0'] = '10101110111010';
		$chr['1'] = '11101010101110';
		$chr['2'] = '10111010101110';
		$chr['3'] = '11101110101010';
		$chr['4'] = '10101110101110';
		$chr['5'] = '11101011101010';
		$chr['6'] = '10111011101010';
		$chr['7'] = '10101011101110';
		$chr['8'] = '10101110111010';
		$chr['9'] = '10111010111010';
		$checkdigit = '';
		if ($checksum) {
			// add checksum
			$checkdigit = $this->checksum_s25($code);
			$code .= $checkdigit ;
		}
		if((strlen($code) % 2) != 0) {
			// add leading zero if code-length is odd
			$code = '0'.$code;
		}
		$seq = '11011010';
		$clen = strlen($code);
		for ($i = 0; $i < $clen; ++$i) {
			$digit = $code[$i];
			if (!isset($chr[$digit])) {
				// invalid character
				return false;
			}
			$seq .= $chr[$digit];
		}		
		$seq .= '1101011';
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$bararray['checkdigit'] = $checkdigit;
		return $this->binseq_to_array($seq, $bararray);
	}
	
	/**
	 * Convert binary barcode sequence to barcode array
	 */
	protected function binseq_to_array($seq, $bararray) {
		$len = strlen($seq);
		$w = 0;
		$k = 0;
		for ($i = 0; $i < $len; ++$i) {
			$w += 1;
			if (($i == ($len - 1)) OR (($i < ($len - 1)) AND ($seq[$i] != $seq[($i+1)]))) {
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
		return $bararray;
	}
	
	/**
	 * Interleaved 2 of 5 barcodes.
	 * Compact numeric code, widely used in industry, air cargo
	 * Contains digits (0 to 9) and encodes the data in the width of both bars and spaces.
	 */
	protected function barcode_i25($code, $checksum=false) {
		$chr['0'] = '11221';
		$chr['1'] = '21112';
		$chr['2'] = '12112';
		$chr['3'] = '22111';
		$chr['4'] = '11212';
		$chr['5'] = '21211';
		$chr['6'] = '12211';
		$chr['7'] = '11122';
		$chr['8'] = '21121';
		$chr['9'] = '12121';
		$chr['A'] = '11';
		$chr['Z'] = '21';
		$checkdigit = '';
		if ($checksum) {
			// add checksum
			$checkdigit = $this->checksum_s25($code);
			$code .= $checkdigit ;
		}
		if((strlen($code) % 2) != 0) {
			// add leading zero if code-length is odd
			$code = '0'.$code;
		}
		// add start and stop codes
		$code = 'AA'.strtolower($code).'ZA';
			
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$clen = strlen($code);
		for ($i = 0; $i < $clen; $i = ($i + 2)) {
			$char_bar = $code[$i];
			$char_space = $code[$i+1];
			if((!isset($chr[$char_bar])) OR (!isset($chr[$char_space]))) {
				// invalid character
				return false;
			}
			// create a bar-space sequence
			$seq = '';
			$chrlen = strlen($chr[$char_bar]);
			for ($s = 0; $s < $chrlen; $s++){
				$seq .= $chr[$char_bar][$s] . $chr[$char_space][$s];
			}
			$seqlen = strlen($seq);
			for ($j = 0; $j < $seqlen; ++$j) {
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
	 * C128 barcodes. 
	 * Very capable code, excellent density, high reliability; in very wide use world-wide
	 */
	protected function barcode_c128($code, $type='B', $ean=false) {
		$code = strcode2utf($code);	// mPDF 5.7.1	Allows e.g. <barcode code="5432&#013;1068" type="C128A" />
		$chr = array(
			'212222', /* 00 */
			'222122', /* 01 */
			'222221', /* 02 */
			'121223', /* 03 */
			'121322', /* 04 */
			'131222', /* 05 */
			'122213', /* 06 */
			'122312', /* 07 */
			'132212', /* 08 */
			'221213', /* 09 */
			'221312', /* 10 */
			'231212', /* 11 */
			'112232', /* 12 */
			'122132', /* 13 */
			'122231', /* 14 */
			'113222', /* 15 */
			'123122', /* 16 */
			'123221', /* 17 */
			'223211', /* 18 */
			'221132', /* 19 */
			'221231', /* 20 */
			'213212', /* 21 */
			'223112', /* 22 */
			'312131', /* 23 */
			'311222', /* 24 */
			'321122', /* 25 */
			'321221', /* 26 */
			'312212', /* 27 */
			'322112', /* 28 */
			'322211', /* 29 */
			'212123', /* 30 */
			'212321', /* 31 */
			'232121', /* 32 */
			'111323', /* 33 */
			'131123', /* 34 */
			'131321', /* 35 */
			'112313', /* 36 */
			'132113', /* 37 */
			'132311', /* 38 */
			'211313', /* 39 */
			'231113', /* 40 */
			'231311', /* 41 */
			'112133', /* 42 */
			'112331', /* 43 */
			'132131', /* 44 */
			'113123', /* 45 */
			'113321', /* 46 */
			'133121', /* 47 */
			'313121', /* 48 */
			'211331', /* 49 */
			'231131', /* 50 */
			'213113', /* 51 */
			'213311', /* 52 */
			'213131', /* 53 */
			'311123', /* 54 */
			'311321', /* 55 */
			'331121', /* 56 */
			'312113', /* 57 */
			'312311', /* 58 */
			'332111', /* 59 */
			'314111', /* 60 */
			'221411', /* 61 */
			'431111', /* 62 */
			'111