yf');
		if ($glyfLength < $this->maxStrLenRead) {
			$glyphData = $this->get_table('glyf');
		}

		$offsets = array();
		$glyf = '';
		$pos = 0;
		$hmtxstr = '';
		$xMinT = 0;
		$yMinT = 0;
		$xMaxT = 0;
		$yMaxT = 0;
		$advanceWidthMax = 0;
		$minLeftSideBearing = 0;
		$minRightSideBearing = 0;
		$xMaxExtent = 0;
		$maxPoints = 0;			// points in non-compound glyph
		$maxContours = 0;			// contours in non-compound glyph
		$maxComponentPoints = 0;	// points in compound glyph
		$maxComponentContours = 0;	// contours in compound glyph
		$maxComponentElements = 0;	// number of glyphs referenced at top level
		$maxComponentDepth = 0;		// levels of recursion, set to 0 if font has only simple glyphs
		$this->glyphdata = array();

		foreach($subsetglyphs AS $originalGlyphIdx => $uni) {
			// hmtx - Horizontal Metrics
			$hm = $this->getHMetric($orignHmetrics, $originalGlyphIdx);
			$hmtxstr .= $hm;

			$offsets[] = $pos;
			$glyphPos = $this->glyphPos[$originalGlyphIdx];
			$glyphLen = $this->glyphPos[$originalGlyphIdx + 1] - $glyphPos;
			if ($glyfLength < $this->maxStrLenRead) {
				$data = substr($glyphData,$glyphPos,$glyphLen);
			}
			else {
				if ($glyphLen > 0) $data = $this->get_chunk($glyfOffset+$glyphPos,$glyphLen);
				else $data = '';
			}

			if ($glyphLen > 0) {
			  if (_RECALC_PROFILE) {
				$xMin = $this->unpack_short(substr($data,2,2));
				$yMin = $this->unpack_short(substr($data,4,2));
				$xMax = $this->unpack_short(substr($data,6,2));
				$yMax = $this->unpack_short(substr($data,8,2));
				$xMinT = min($xMinT,$xMin);
				$yMinT = min($yMinT,$yMin);
				$xMaxT = max($xMaxT,$xMax);
				$yMaxT = max($yMaxT,$yMax);
				$aw = $this->unpack_short(substr($hm,0,2)); 
				$lsb = $this->unpack_short(substr($hm,2,2));
				$advanceWidthMax = max($advanceWidthMax,$aw);
				$minLeftSideBearing = min($minLeftSideBearing,$lsb);
				$minRightSideBearing = min($minRightSideBearing,($aw - $lsb - ($xMax - $xMin)));
				$xMaxExtent = max($xMaxExtent,($lsb + ($xMax - $xMin)));
			   }
				$up = unpack("n", substr($data,0,2));
			}
			if ($glyphLen > 2 && ($up[1] & (1 << 15)) ) {	// If number of contours <= -1 i.e. composiste glyph
				$pos_in_glyph = 10;
				$flags = GF_MORE;
				$nComponentElements = 0;
				while ($flags & GF_MORE) {
					$nComponentElements += 1;	// number of glyphs referenced at top level
					$up = unpack("n", substr($data,$pos_in_glyph,2));
					$flags = $up[1];
					$up = unpack("n", substr($data,$pos_in_glyph+2,2));
					$glyphIdx = $up[1];
					$this->glyphdata[$originalGlyphIdx]['compGlyphs'][] = $glyphIdx;
					$data = $this->_set_ushort($data, $pos_in_glyph + 2, $glyphSet[$glyphIdx]);
					$pos_in_glyph += 4;
					if ($flags & GF_WORDS) { $pos_in_glyph += 4; }
					else { $pos_in_glyph += 2; }
					if ($flags & GF_SCALE) { $pos_in_glyph += 2; }
					else if ($flags & GF_XYSCALE) { $pos_in_glyph += 4; }
					else if ($flags & GF_TWOBYTWO) { $pos_in_glyph += 8; }
				}
				$maxComponentElements = max($maxComponentElements, $nComponentElements);
			}
			// Simple Glyph
			else if (_RECALC_PROFILE && $glyphLen > 2 && $up[1] < (1 << 15) && $up[1] > 0) { 	// Number of contours > 0 simple glyph
				$nContours = $up[1];
				$this->glyphdata[$originalGlyphIdx]['nContours'] = $nContours;
				$maxContours = max($maxContours, $nContours);

				// Count number of points in simple glyph
				$pos_in_glyph = 10 + ($nContours  * 2) - 2;	// Last endContourPoint
				$up = unpack("n", substr($data,$pos_in_glyph,2));
				$points = $up[1]+1;
				$this->glyphdata[$originalGlyphIdx]['nPoints'] = $points;
				$maxPoints = max($maxPoints, $points);
			}

			$glyf .= $data;
			$pos += $glyphLen;
			if ($pos % 4 != 0) {
				$padding = 4 - ($pos % 4);
				$glyf .= str_repeat("\0",$padding);
				$pos += $padding;
			}
		}

		if (_RECALC_PROFILE) {
		   foreach($this->glyphdata AS $originalGlyphIdx => $val) {
			$maxdepth = $depth = -1;
			$points = 0;
			$contours = 0;
			$this->getGlyphData($originalGlyphIdx, $maxdepth, $depth, $points, $contours) ;
			$maxComponentDepth = max($maxComponentDepth , $maxdepth);
			$maxComponentPoints = max($maxComponentPoints , $points);
			$maxComponentContours = max($maxComponentContours , $contours);
		   }
		}


		$offsets[] = $pos;
		$this->add('glyf', $glyf);

		///////////////////////////////////
		// hmtx - Horizontal Metrics
		///////////////////////////////////
		$this->add('hmtx', $hmtxstr);


		///////////////////////////////////
		// loca - Index to location
		///////////////////////////////////
		$locastr = '';
		if ((($pos + 1) >> 1) > 0xFFFF) {
			$indexToLocFormat = 1;        // long format
			foreach($offsets AS $offset) { $locastr .= pack("N",$offset); }
		}
		else {
			$indexToLocFormat = 0;        // short format
			foreach($offsets AS $offset) { $locastr .= pack("n",($offset/2)); }
		}
		$this->add('loca', $locastr);

		///////////////////////////////////
		// head - Font header
		///////////////////////////////////
		$head = $this->get_table('head');
		$head = $this->_set_ushort($head, 50, $indexToLocFormat);
		if (_RECALC_PROFILE) {
			$head = $this->_set_short($head, 36, $xMinT);	// for all glyph bounding boxes
			$head = $this->_set_short($head, 38, $yMinT);	// for all glyph bounding boxes
			$head = $this->_set_short($head, 40, $xMaxT);	// for all glyph bounding boxes
			$head = $this->_set_short($head, 42, $yMaxT);	// for all glyph bounding boxes
			$head[17] = chr($head[17] & ~(1 << 4)); 	// Unset Bit 4 (as hdmx/LTSH tables not included)
		}
		$this->add('head', $head);


		///////////////////////////////////
		// hhea - Horizontal Header
		///////////////////////////////////
		$hhea = $this->get_table('hhea');
		$hhea = $this->_set_ushort($hhea, 34, $numberOfHMetrics);
		if (_RECALC_PROFILE) {
			$hhea = $this->_set_ushort($hhea, 10, $advanceWidthMax);	
			$hhea = $this->_set_short($hhea, 12, $minLeftSideBearing);	
			$hhea = $this->_set_short($hhea, 14, $minRightSideBearing);	
			$hhea = $this->_set_short($hhea, 16, $xMaxExtent);	
		}
		$this->add('hhea', $hhea);

		///////////////////////////////////
		// maxp - Maximum Profile
		///////////////////////////////////
		$maxp = $this->get_table('maxp');
		$maxp = $this->_set_ushort($maxp, 4, $numGlyphs);
		if (_RECALC_PROFILE) {
			$maxp = $this->_set_ushort($maxp, 6, $maxPoints);	// points in non-compound glyph
			$maxp = $this->_set_ushort($maxp, 8, $maxContours);	// contours in non-compound glyph
			$maxp = $this->_set_ushort($maxp, 10, $maxComponentPoints);	// points in compound glyph
			$maxp = $this->_set_ushort($maxp, 12, $maxComponentContours);	// contours in compound glyph
			$maxp = $this->_set_ushort($maxp, 28, $maxComponentElements);	// number of glyphs referenced at top level
			$maxp = $this->_set_ushort($maxp, 30, $maxComponentDepth);	// levels of recursion, set to 0 if font has only simple glyphs
		}
		$this->add('maxp', $maxp);


		///////////////////////////////////
		// OS/2 - OS/2
		///////////////////////////////////
		if (isset($this->tables['OS/2'])) { 
			$os2_offset = $this->seek_table("OS/2");
			if (_RECALC_PROFILE) {
				$fsSelection = $this->get_ushort($os2_offset+62);
				$fsSelection = ($fsSelection & ~(1 << 6)); 	// 2-byte bit field containing information concerning the nature of the font patterns
					// bit#0 = Italic; bit#5=Bold
					// Match name table's font subfamily string
					// Clear bit#6 used for 'Regular' and optional
			}

			// NB Currently this method never subsets characters above BMP
			// Could set nonBMP bit according to $this->maxUni 
			$nonBMP = $this->get_ushort($os2_offset+46);
			$nonBMP = ($nonBMP & ~(1 << 9)); 	// Unset Bit 57 (indicates non-BMP) - for interactive forms

			$os2 = $this->get_table('OS/2');
			if (_RECALC_PROFILE) {
				$os2 = $this->_set_ushort($os2, 62, $fsSelection);	
				$os2 = $this->_set_ushort($os2, 66, $fsLastCharIndex);
				$os2 = $this->_set_ushort($os2, 42, 0x0000);	// ulCharRange (ulUnicodeRange) bits 24-31 | 16-23
				$os2 = $this->_set_ushort($os2, 44, 0x0000);	// ulCharRange (Unicode ranges) bits  8-15 |  0-7
				$os2 = $this->_set_ushort($os2, 46, $nonBMP);	// ulCharRange (Unicode ranges) bits 56-63 | 48-55
				$os2 = $this->_set_ushort($os2, 48, 0x0000);	// ulCharRange (Unicode ranges) bits 40-47 | 32-39
				$os2 = $this->_set_ushort($os2, 50, 0x0000);	// ulCharRange (Unicode ranges) bits  88-95 | 80-87
				$os2 = $this->_set_ushort($os2, 52, 0x0000);	// ulCharRange (Unicode ranges) bits  72-79 | 64-71
				$os2 = $this->_set_ushort($os2, 54, 0x0000);	// ulCharRange (Unicode ranges) bits  120-127 | 112-119
				$os2 = $this->_set_ushort($os2, 56, 0x0000);	// ulCharRange (Unicode ranges) bits  104-111 | 96-103
			}
			$os2 = $this->_set_ushort($os2, 46, $nonBMP);	// Unset Bit 57 (indicates non-BMP) - for interactive forms

			$this->add('OS/2', $os2 );
		}

		fclose($this->fh);
		// Put the TTF file together
		$stm = '';
		$this->endTTFile($stm);
		//file_put_contents('testfont.ttf', $stm); exit;
		return $stm ;
	}

//================================================================================

	// Also does SMP
	function makeSubsetSIP($file, &$subset, $TTCfontID=0, $debug=false, $useOTL=0) {	// mPDF 5.7.1
		$this->fh = fopen($file ,'rb') or die('Can\'t open file ' . $file);
		$this->filename = $file;
		$this->_pos = 0;
		$this->useOTL = $useOTL;	// mPDF 5.7.1
		$this->charWidths = '';
		$this->glyphPos = array();
		$this->charToGlyph = array();
		$this->tables = array();
		$this->otables = array();
		$this->ascent = 0;
		$this->descent = 0;
		$this->strikeoutSize = 0;
		$this->strikeoutPosition = 0;
		$this->numTTCFonts = 0;
		$this->TTCFonts = array();
		$this->skip(4);
		if ($TTCfontID > 0) {
			$this->version = $version = $this->read_ulong();	// TTC Header version now
			if (!in_array($version, array(0x00010000,0x00020000)))
				die("ERROR - Error parsing TrueType Collection: version=".$version." - " . $file);
			$this->numTTCFonts = $this->read_ulong();
			for ($i=1; $i<=$this->numTTCFonts; $i++) {
	      	      $this->TTCFonts[$i]['offset'] = $this->read_ulong();
			}
			$this->seek($this->TTCFonts[$TTCfontID]['offset']);
			$this->version = $version = $this->read_ulong();	// TTFont version again now
		}
		$this->readTableDirectory($debug);


		///////////////////////////////////
		// head - Font header table
		///////////////////////////////////
		$this->seek_table("head");
		$this->skip(50); 
		$indexToLocFormat = $this->read_ushort();
		$glyphDataFormat = $this->read_ushort();

		///////////////////////////////////
		// hhea - Horizontal header table
		///////////////////////////////////
		$this->seek_table("hhea");
		$this->skip(32); 
		$metricDataFormat = $this->read_ushort();
		$orignHmetrics = $numberOfHMetrics = $this->read_ushort();

		///////////////////////////////////
		// maxp - Maximum profile table
		///////////////////////////////////
		$this->seek_table("maxp");
		$this->skip(4);
		$numGlyphs = $this->read_ushort();


		///////////////////////////////////
		// cmap - Character to glyph index mapping table
		///////////////////////////////////

		$cmap_offset = $this->seek_table("cmap");
		$this->skip(2);
		$cmapTableCount = $this->read_ushort();
		$unicode_cmap_offset = 0;
		for ($i=0;$i<$cmapTableCount;$i++) {
			$platformID = $this->read_ushort();
			$encodingID = $this->read_ushort();
			$offset = $this->read_ulong();
			$save_pos = $this->_pos;
			if (($platformID == 3 && $encodingID == 10) || $platformID == 0) { // Microsoft, Unicode Format 12 table HKCS
				$format = $this->get_ushort($cmap_offset + $offset);
				if ($format == 12) {
					$unicode_cmap_offset = $cmap_offset + $offset;
					break;
				}
			}
			// mPDF 5.7.1
			if (($platformID == 3 && $encodingID == 1) || $platformID == 0) { // Microsoft, Unicode
				$format = $this->get_ushort($cmap_offset + $offset);
				if ($format == 4) {
					$unicode_cmap_offset = $cmap_offset + $offset;
				}
			}
			$this->seek($save_pos );
		}

		if (!$unicode_cmap_offset)
			die('Font does not have cmap for Unicode (platform 3, encoding 1, format 4, or platform 0, any encoding, format 4)');


		// Format 12 CMAP does characters above Unicode BMP i.e. some HKCS characters U+20000 and above
		if ($format == 12) {
			$this->maxUniChar = 0;
			$this->seek($unicode_cmap_offset + 4);
			$length = $this->read_ulong();
			$limit = $unicode_cmap_offset + $length;
			$this->skip(4);

			$nGroups = $this->read_ulong();

			$glyphToChar = array();
			$charToGlyph = array();
			for($i=0; $i<$nGroups ; $i++) { 
				$startCharCode = $this->read_ulong(); 
				$endCharCode = $this->read_ulong(); 
				$startGlyphCode = $this->read_ulong(); 
				$offset = 0;
				for ($unichar=$startCharCode;$unichar<=$endCharCode;$unichar++) {
					$glyph = $startGlyphCode + $offset ;
					$offset++;
					// ZZZ98
					if ($unichar < 0x30000) { 
						$charToGlyph[$unichar] = $glyph;
						$this->maxUniChar = max($unichar,$this->maxUniChar); 
						$glyphToChar[$glyph][] = $unichar;
					}
				}
			}
		}
		// mPDF 5.7.1
		else {
			$glyphToChar = array();
			$charToGlyph = array();
			$this->getCMAP4($unicode_cmap_offset, $glyphToChar, $charToGlyph );
		}

		///////////////////////////////////
		// mPDF 5.7.1
		// Map Unmapped glyphs - from $numGlyphs
		if ($useOTL) {
			$bctr = 0xE000;
			for ($gid=1; $gid<$numGlyphs; $gid++) {
				if (!isset($glyphToChar[$gid])) {
					while(isset($charToGlyph[$bctr])) { $bctr++; }	// Avoid overwriting a glyph already mapped in PUA
					// ZZZ98
					if ($bctr > 0xF8FF && $bctr < 0x2CEB0) {
						$bctr = 0x2CEB0; 
						while(isset($charToGlyph[$bctr])) { $bctr++; }	
					}
					$glyphToChar[$gid][] = $bctr;
					$charToGlyph[$bctr] = $gid;
					$this->maxUniChar = max($bctr,$this->maxUniChar); 
					$bctr++;
				}
			}
		}
		///////////////////////////////////

		///////////////////////////////////
		// hmtx - Horizontal metrics table
		///////////////////////////////////
		$scale = 1; // not used here
		$this->getHMTX($numberOfHMetrics, $numGlyphs, $glyphToChar, $scale);

		///////////////////////////////////
		// loca - Index to location
		///////////////////////////////////
		$this->getLOCA($indexToLocFormat, $numGlyphs);

		///////////////////////////////////////////////////////////////////

		$glyphMap = array(0=>0); 
		$glyphSet = array(0=>0);
		$codeToGlyph = array();
		// Set a substitute if ASCII characters do not have glyphs
		if (isset($charToGlyph[0x3F])) { $subs = $charToGlyph[0x3F]; }	// Question mark
		else { $subs = $charToGlyph[32]; }
		foreach($subset AS $code) {
			if (isset($charToGlyph[$code]))
				$originalGlyphIdx = $charToGlyph[$code];
			else if ($code<128) {
				$originalGlyphIdx = $subs;
			}
			else { $originalGlyphIdx = 0; }
			if (!isset($glyphSet[$originalGlyphIdx])) {
				$glyphSet[$originalGlyphIdx] = count($glyphMap);
				$glyphMap[] = $originalGlyphIdx;
			}
			$codeToGlyph[$code] = $glyphSet[$originalGlyphIdx];
		}

		list($start,$dummy) = $this->get_table_pos('glyf');

		$n = 0;
		while ($n < count($glyphMap)) {
			$originalGlyphIdx = $glyphMap[$n];
			$glyphPos = $this->glyphPos[$originalGlyphIdx];
			$glyphLen = $this->glyphPos[$originalGlyphIdx + 1] - $glyphPos;
			$n += 1;
			if (!$glyphLen) continue;
			$this->seek($start + $glyphPos);
			$numberOfContours = $this->read_short();
			if ($numberOfContours < 0) {
				$this->skip(8);
				$flags = GF_MORE;
				while ($flags & GF_MORE) {
					$flags = $this->read_ushort();
					$glyphIdx = $this->read_ushort();
					if (!isset($glyphSet[$glyphIdx])) {
						$glyphSet[$glyphIdx] = count($glyphMap);
						$glyphMap[] = $glyphIdx;
					}
					if ($flags & GF_WORDS)
						$this->skip(4);
					else
						$this->skip(2);
					if ($flags & GF_SCALE)
						$this->skip(2);
					else if ($flags & GF_XYSCALE)
						$this->skip(4);
					else if ($flags & GF_TWOBYTWO)
						$this->skip(8);
				}
			}
		}

		$numGlyphs = $n = count($glyphMap);
		$numberOfHMetrics = $n;

		///////////////////////////////////
		// name
		///////////////////////////////////
		// MS spec says that "Platform and encoding ID's in the name table should be consistent with those in the cmap table. 
		// If they are not, the font will not load in Windows"
		// Doesn't seem to be a problem?
		///////////////////////////////////
		// Needs to have a name entry in 3,0 (e.g. symbol) - original font will be 3,1 (i.e. Unicode)
		$name = $this->get_table('name'); 
		$name_offset = $this->seek_table("name");
		$format = $this->read_ushort();
		$numRecords = $this->read_ushort();
		$string_data_offset = $name_offset + $this->read_ushort();
		for ($i=0;$i<$numRecords; $i++) {
			$platformId = $this->read_ushort();
			$encodingId = $this->read_ushort();
			if ($platformId == 3 && $encodingId == 1) {
				$pos = 6 + ($i * 12) + 2;
				$name = $this->_set_ushort($name, $pos, 0x00);	// Change encoding to 3,0 rather than 3,1
			}
			$this->skip(8);
		}
		$this->add('name', $name);

		///////////////////////////////////
		// OS/2
		///////////////////////////////////
		if (isset($this->tables['OS/2'])) { 
			$os2 = $this->get_table('OS/2');
			$os2 = $this->_set_ushort($os2, 42, 0x00);	// ulCharRange (Unicode ranges)
			$os2 = $this->_set_ushort($os2, 44, 0x00);	// ulCharRange (Unicode ranges)
			$os2 = $this->_set_ushort($os2, 46, 0x00);	// ulCharRange (Unicode ranges)
			$os2 = $this->_set_ushort($os2, 48, 0x00);	// ulCharRange (Unicode ranges)