 => $code) {
			$glidx = $codeToGlyph[$code]; 
			if ($cid == ($prevcid + 1) && $glidx == ($prevglidx + 1)) {
				$range[$rangeid][] = $glidx;
			} else {
				// new range
				$rangeid = $cid;
				$range[$rangeid] = array();
				$range[$rangeid][] = $glidx;
			}
			$prevcid = $cid;
			$prevglidx = $glidx;
		}
		// cmap - Character to glyph mapping 
		$segCount = count($range) + 1;	// + 1 Last segment has missing character 0xFFFF
		$searchRange = 1;
		$entrySelector = 0;
		while ($searchRange * 2 <= $segCount ) {
			$searchRange = $searchRange * 2;
			$entrySelector = $entrySelector + 1;
		}
		$searchRange = $searchRange * 2;
		$rangeShift = $segCount * 2 - $searchRange;
		$length = 16 + (8*$segCount ) + ($numGlyphs+1);
		$cmap = array(
			4, $length, 0, 		// Format 4 Mapping subtable: format, length, language
			$segCount*2,
			$searchRange,
			$entrySelector,
			$rangeShift);

		// endCode(s)
		foreach($range AS $start=>$subrange) {
			$endCode = $start + (count($subrange)-1);
			$cmap[] = $endCode;	// endCode(s)
		}
		$cmap[] =	0xFFFF;	// endCode of last Segment
		$cmap[] =	0;	// reservedPad

		// startCode(s)
		foreach($range AS $start=>$subrange) {
			$cmap[] = $start;	// startCode(s)
		}
		$cmap[] =	0xFFFF;	// startCode of last Segment
		// idDelta(s) 
		foreach($range AS $start=>$subrange) {
			$idDelta = -($start-$subrange[0]);
			$n += count($subrange);
			$cmap[] = $idDelta;	// idDelta(s)
		}
		$cmap[] =	1;	// idDelta of last Segment
		// idRangeOffset(s) 
		foreach($range AS $subrange) {
			$cmap[] = 0;	// idRangeOffset[segCount]  	Offset in bytes to glyph indexArray, or 0

		}
		$cmap[] =	0;	// idRangeOffset of last Segment
		foreach($range AS $subrange) {
			foreach($subrange AS $glidx) {
				$cmap[] = $glidx;
			}
		}
		$cmap[] = 0;	// Mapping for last character
		$cmapstr4 = '';
		foreach($cmap AS $cm) { $cmapstr4 .= pack("n",$cm); }

		///////////////////////////////////
		// cmap - Character to glyph mapping
		///////////////////////////////////
		$entryCount = count($subset);
		$length = 10 + $entryCount * 2;

		$off = 20 + $length;
		$hoff = $off >> 16;
		$loff = $off & 0xFFFF;

		$cmap = array(0, 2,	// Index : version, number of subtables
			1, 0,			// Subtable : platform, encoding
			0, 20,		// offset (hi,lo)
			3, 0,			// Subtable : platform, encoding	// See note above for 'name'
			$hoff, $loff,	// offset (hi,lo)
			6, $length, 	// Format 6 Mapping table: format, length
			0, 1,			// language, First char code
			$entryCount
		);
		$cmapstr = '';
		foreach($subset AS $code) { $cmap[] = $codeToGlyph[$code]; }
		foreach($cmap AS $cm) { $cmapstr .= pack("n",$cm); }
		$cmapstr .= $cmapstr4;
		$this->add('cmap', $cmapstr);

		///////////////////////////////////
		// hmtx - Horizontal Metrics
		///////////////////////////////////
		$hmtxstr = '';
		for($n=0;$n<$numGlyphs;$n++) {
			$originalGlyphIdx = $glyphMap[$n];
			$hm = $this->getHMetric($orignHmetrics, $originalGlyphIdx);
			$hmtxstr .= $hm;
		}
		$this->add('hmtx', $hmtxstr);

		///////////////////////////////////
		// glyf - Glyph data
		///////////////////////////////////
		list($glyfOffset,$glyfLength) = $this->get_table_pos('glyf');
		if ($glyfLength < $this->maxStrLenRead) {
			$glyphData = $this->get_table('glyf');
		}

		$offsets = array();
		$glyf = '';
		$pos = 0;
		for ($n=0;$n<$numGlyphs;$n++) {
			$offsets[] = $pos;
			$originalGlyphIdx = $glyphMap[$n];
			$glyphPos = $this->glyphPos[$originalGlyphIdx];
			$glyphLen = $this->glyphPos[$originalGlyphIdx + 1] - $glyphPos;
			if ($glyfLength < $this->maxStrLenRead) {
				$data = substr($glyphData,$glyphPos,$glyphLen);
			}
			else {
				if ($glyphLen > 0) $data = $this->get_chunk($glyfOffset+$glyphPos,$glyphLen);
				else $data = '';
			}
			if ($glyphLen > 0) $up = unpack("n", substr($data,0,2));
			if ($glyphLen > 2 && ($up[1] & (1 << 15)) ) {
				$pos_in_glyph = 10;
				$flags = GF_MORE;
				while ($flags & GF_MORE) {
					$up = unpack("n", substr($data,$pos_in_glyph,2));
					$flags = $up[1];
					$up = unpack("n", substr($data,$pos_in_glyph+2,2));
					$glyphIdx = $up[1];
					$data = $this->_set_ushort($data, $pos_in_glyph + 2, $glyphSet[$glyphIdx]);
					$pos_in_glyph += 4;
					if ($flags & GF_WORDS) { $pos_in_glyph += 4; }
					else { $pos_in_glyph += 2; }
					if ($flags & GF_SCALE) { $pos_in_glyph += 2; }
					else if ($flags & GF_XYSCALE) { $pos_in_glyph += 4; }
					else if ($flags & GF_TWOBYTWO) { $pos_in_glyph += 8; }
				}
			}
			$glyf .= $data;
			$pos += $glyphLen;
			if ($pos % 4 != 0) {
				$padding = 4 - ($pos % 4);
				$glyf .= str_repeat("\0",$padding);
				$pos += $padding;
			}
		}
		$offsets[] = $pos;
		$this->add('glyf', $glyf);

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
		$this->add('head', $head);

		fclose($this->fh);

		// Put the TTF file together
		$stm = '';
		$this->endTTFile($stm);
		//file_put_contents('testfont.ttf', $stm); exit;
		return $stm ;
	}

	//////////////////////////////////////////////////////////////////////////////////
	// Recursively get composite glyph data
	function getGlyphData($originalGlyphIdx, &$maxdepth, &$depth, &$points, &$contours) {
		$depth++;
		$maxdepth = max($maxdepth, $depth);
		if (count($this->glyphdata[$originalGlyphIdx]['compGlyphs'])) {
			foreach($this->glyphdata[$originalGlyphIdx]['compGlyphs'] AS $glyphIdx) {
				$this->getGlyphData($glyphIdx, $maxdepth, $depth, $points, $contours);
			}
		}
		else if (($this->glyphdata[$originalGlyphIdx]['nContours'] > 0) && $depth > 0) {	// simple
			$contours += $this->glyphdata[$originalGlyphIdx]['nContours'];
			$points += $this->glyphdata[$originalGlyphIdx]['nPoints'];
		}
		$depth--;
	}


	//////////////////////////////////////////////////////////////////////////////////
	// Recursively get composite glyphs
	function getGlyphs($originalGlyphIdx, &$start, &$glyphSet, &$subsetglyphs) {
		$glyphPos = $this->glyphPos[$originalGlyphIdx];
		$glyphLen = $this->glyphPos[$originalGlyphIdx + 1] - $glyphPos;
		if (!$glyphLen) { 
			return;
		}
		$this->seek($start + $glyphPos);
		$numberOfContours = $this->read_short();
		if ($numberOfContours < 0) {
			$this->skip(8);
			$flags = GF_MORE;
			while ($flags & GF_MORE) {
				$flags = $this->read_ushort();
				$glyphIdx = $this->read_ushort();
				if (!isset($glyphSet[$glyphIdx])) {
					$glyphSet[$glyphIdx] = count($subsetglyphs);	// old glyphID to new glyphID
					$subsetglyphs[$glyphIdx] = true;
				}
				$savepos = ftell($this->fh);
				$this->getGlyphs($glyphIdx, $start, $glyphSet, $subsetglyphs);
				$this->seek($savepos);
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

	//////////////////////////////////////////////////////////////////////////////////

	function getHMTX($numberOfHMetrics, $numGlyphs, &$glyphToChar, $scale) {
		$start = $this->seek_table("hmtx");
		$aw = 0;
		$this->charWidths = str_pad('', 256*256*2, "\x00");
		if ($this->maxUniChar > 65536) { $this->charWidths .= str_pad('', 256*256*2, "\x00"); }	// Plane 1 SMP
		if ($this->maxUniChar > 131072) { $this->charWidths .= str_pad('', 256*256*2, "\x00"); }	// Plane 2 SMP
		$nCharWidths = 0;
		if (($numberOfHMetrics*4) < $this->maxStrLenRead) {
			$data = $this->get_chunk($start,($numberOfHMetrics*4));
			$arr = unpack("n*", $data);
		}
		else { $this->seek($start); }
		for( $glyph=0; $glyph<$numberOfHMetrics; $glyph++) {
			if (($numberOfHMetrics*4) < $this->maxStrLenRead) {
				$aw = $arr[($glyph*2)+1];
			}
			else {
				$aw = $this->read_ushort();
				$lsb = $this->read_ushort();
			}
			if (isset($glyphToChar[$glyph]) || $glyph == 0) {
				if ($aw >= (1 << 15) ) { $aw = 0; }	// 1.03 Some (arabic) fonts have -ve values for width
					// although should be unsigned value - comes out as e.g. 65108 (intended -50)
				if ($glyph == 0) {
					$this->defaultWidth = $scale*$aw;
					continue;
				}
				foreach($glyphToChar[$glyph] AS $char) {
					if ($char != 0 && $char != 65535) {
 						$w = intval(round($scale*$aw));
						if ($w == 0) { $w = 65535; }
						if ($char < 196608) {
							$this->charWidths[$char*2] = chr($w >> 8);
							$this->charWidths[$char*2 + 1] = chr($w & 0xFF);
							$nCharWidths++;

						}
					}
				}
			}
		}

		$data = $this->get_chunk(($start+$numberOfHMetrics*4),($numGlyphs*2));
		$arr = unpack("n*", $data);
		$diff = $numGlyphs-$numberOfHMetrics;
		$w = intval(round($scale*$aw));
		if ($w == 0) { $w = 65535; }
		for( $pos=0; $pos<$diff; $pos++) {
			$glyph = $pos + $numberOfHMetrics;
			if (isset($glyphToChar[$glyph])) {
				foreach($glyphToChar[$glyph] AS $char) {
					if ($char != 0 && $char != 65535) {
						if ($char < 196608) { 
							$this->charWidths[$char*2] = chr($w >> 8);
							$this->charWidths[$char*2 + 1] = chr($w & 0xFF);
							$nCharWidths++;
						}
					}
				}
			}
		}
		// NB 65535 is a set width of 0
		// First bytes define number of chars in font
		$this->charWidths[0] = chr($nCharWidths >> 8);
		$this->charWidths[1] = chr($nCharWidths & 0xFF);
	}





	function getHMetric($numberOfHMetrics, $gid) {
		$start = $this->seek_table("hmtx");
		if ($gid < $numberOfHMetrics) {
			$this->seek($start+($gid*4));
			$hm = fread($this->fh,4);
		}
		else {
			$this->seek($start+(($numberOfHMetrics-1)*4));
			$hm = fread($this->fh,2);
			$this->seek($start+($numberOfHMetrics*2)+($gid*2));
			$hm .= fread($this->fh,2);
		}
		return $hm;
	}

	function getLOCA($indexToLocFormat, $numGlyphs) {
		$start = $this->seek_table('loca');
		$this->glyphPos = array();
		if ($indexToLocFormat == 0) {
			$data = $this->get_chunk($start,($numGlyphs*2)+2);
			$arr = unpack("n*", $data);
			for ($n=0; $n<=$numGlyphs; $n++) {
				$this->glyphPos[] = ($arr[$n+1] * 2);
			}
		}
		else if ($indexToLocFormat == 1) {
			$data = $this->get_chunk($start,($numGlyphs*4)+4);
			$arr = unpack("N*", $data);
			for ($n=0; $n<=$numGlyphs; $n++) {
				$this->glyphPos[] = ($arr[$n+1]);
			}
		}
		else 
			die('Unknown location table format '.$indexToLocFormat);
	}


	// CMAP Format 4
	function getCMAP4($unicode_cmap_offset, &$glyphToChar, &$charToGlyph ) {
		$this->maxUniChar = 0;	
		$this->seek($unicode_cmap_offset + 2);
		$length = $this->read_ushort();
		$limit = $unicode_cmap_offset + $length;
		$this->skip(2);

		$segCount = $this->read_ushort() / 2;
		$this->skip(6);
		$endCount = array();
		for($i=0; $i<$segCount; $i++) { $endCount[] = $this->read_ushort(); }
		$this->skip(2);
		$startCount = array();
		for($i=0; $i<$segCount; $i++) { $startCount[] = $this->read_ushort(); }
		$idDelta = array();
		for($i=0; $i<$segCount; $i++) { $idDelta[] = $this->read_short(); }		// ???? was unsigned short
		$idRangeOffset_start = $this->_pos;
		$idRangeOffset = array();
		for($i=0; $i<$segCount; $i++) { $idRangeOffset[] = $this->read_ushort(); }

		for ($n=0;$n<$segCount;$n++) {
			$endpoint = ($endCount[$n] + 1);
			for ($unichar=$startCount[$n];$unichar<$endpoint;$unichar++) {
				if ($idRangeOffset[$n] == 0)
					$glyph = ($unichar + $idDelta[$n]) & 0xFFFF;
				else {
					$offset = ($unichar - $startC