I
		CASE "khar":	// KHAROSHTHI
			$unifont = "mph2bdamase";
			break;
		CASE "mtei":	// MEETEI_MAYEK
			$unifont = "eeyekunicode";
			break;
		//CASE "shrd":	// SHARADA
		//CASE "sora":	// SORA_SOMPENG

		/* South East Asian */
		CASE "kali":	// KAYAH_LI
			$unifont = "freemono";
			break;
		//CASE "rjng":	// REJANG
		CASE "lana":	// TAI_THAM
			$unifont = "lannaalif";
			break;
		CASE "talu":	// NEW_TAI_LUE
			$unifont = "daibannasilbook";
			break;

		/* East Asian */
		CASE "hans":	// HAN (SIMPLIFIED)
			if ($adobeCJK) { $unifont = "gb"; }
			else { $unifont = "sun-exta"; }
			break;
		CASE "bopo":	// BOPOMOFO
			$unifont = "sun-exta";
			break;
		//CASE "plrd":	// MIAO
		CASE "yiii":	// YI
			$unifont = "sun-exta";
			break;

		/* American */
		CASE "dsrt":	// DESERET
			$unifont = "mph2bdamase";
			break;

		/* Other */
		CASE "b