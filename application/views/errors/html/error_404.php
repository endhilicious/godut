  CASE "tgk":	// Tajik
	CASE "tt":  CASE "tat":	// Tatar
	CASE "tk":  CASE "tuk":	// Turkmen
	CASE "uk":  CASE "ukr":	// Ukrainian
		$unifont = "dejavusanscondensed";	/* freeserif best coverage for supplements etc. */
		break;


	CASE "hy":  CASE "hye":	// ARMENIAN
		$unifont = "dejavusans";
		break;
	CASE "ka":  CASE "kat":	// GEORGIAN
		$unifont = "dejavusans";
		break;

	CASE "el":  CASE "ell":	// GREEK
		$unifont = "dejavusanscondensed";
		break;
	CASE "cop":		// COPTIC
		$unifont = "quivira";
		break;

	CASE "got":		// GOTHIC
		$unifont = "freeserif";
		break;




/* African */
	CASE "nqo":		// NKO
		$unifont = "dejavusans";
		break;
	//CASE "bax":	// BAMUM
	//CASE "ha":  CASE "hau":	// Hausa
	CASE "vai":		// VAI
		$unifont = "freesans";
		break;
	CASE "am":  CASE "amh":	// Amharic ETHIOPIC
	CASE "ti":  CASE "tir":	// Tigrinya ETHIOPIC
		$unifont = "abyssinicasil";
		break;



/* Middle Eastern */
	CASE "ar":  CASE "ara":	// Arabic	NB Arabic text identified by Autofont will be marked as und-Arab
		$unifont = "xbriyaz";
		break;
	CASE "fa":  CASE "fas":	// Persian (Farsi)
		$unifont = "xbriyaz";  
		break;
	CASE "