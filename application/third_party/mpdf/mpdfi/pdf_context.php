] = 32;
		}
		$txt = preg_replace("/".$char."/",' ',$txt, 1);
	}
}

function trimOTLdata(&$cOTLdata, $Left=true, $Right=true) {

	$len = count($cOTLdata['char_data']);
	$nLeft = 0;
	$nRight = 0;
	for($i=0;$i<$len;$i++) {
		if($cOTLdata['char_data'][$i]['uni']==32 || $cOTLdata['char_data'][$i]['uni']==12288) { $nLeft++; }	// 12288 = 0x3000 = CJK space
		else { break; }
	}
	for($i=($len-1);$i>=0;$i--) {
		if($cOTLdata['char_data'][$i]['uni']==32 || $cOTLdata['char_data'][$i]['uni']==12288) { $nRight++; }	// 12288 = 0x3000 = CJK space
		else { break; }
	}

	// Trim Right
	if ($Right && $nRight) {
		$cOTLdata['group'] = substr($cOTLdata['group'],0,strlen($cOTLdata['group'])-$nRight);
		if ($cOTLdata['GPOSinfo']) {
			foreach($cOTLdata['GPOSinfo'] AS $k => $val) {
				if ($k >= $len-$nRight) {
					unset($cOTLdata['GPOSinfo'][$k]);
				}
			}
		}
		if (isset($cOTLdata['char_data'])) {
			for($i=0;$i<$nRight;$i++) {
				array_pop($cOTLdata['char_data']);
			}
		}
	}
	// Trim Left
	if ($Left && $nLeft) {
		$cOTLdata['group'] = substr($cOTLdata['group'],$nLeft);
		if ($cOTLdata['GPOSinfo']) {
			$newPOSinfo = array();
			foreach($cOTLdata['GPOSinfo'] AS $k => $val) {
				if ($k >= $nLeft) {
					$newPOSinfo[$k-$nLeft] = $cOTLdata['GPOSinfo'][$k];
				}
			}
			$cOTLdata['GPOSinfo'] = $newPOSinfo;
		}
		if (isset($cOTLdata['char_data'])) {
			for($i=0;$i<$nLeft;$i++) {
				array_shift($cOTLdata['char_data']);
			}
		}
	}
}


////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
//////////         GENERAL OTL FUNCTIONS       /////////////////
////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////

