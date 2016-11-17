ttribs['font-family']) && $attribs['font-family']) {
						$tmp_svg_font = array(
							'units-per-em' => (isset($attribs['units-per-em']) ? $attribs['units-per-em'] : ''),
							'd' => '',
							'glyphs' => array()
						);
						$last_svg_fontid = strtolower($attribs['font-family']);
						if ($last_svg_fontdefw) { $tmp_svg_font['horiz-adv-x'] = $last_svg_fontdefw; }
						else { $tmp_svg_font['horiz-adv-x'] = 500; }
						$svg_class->svg_font[$last_svg_fontid][$last_svg_fontstyle] = $tmp_svg_font;
					}
					return;
				}
				// mPDF 6
				else if (strtolower($name) == 'missing-glyph'){
					if ($last_svg_fontid && isset($attribs['horiz-adv-x'])) {
						$svg_class->svg_font[$last_svg_fontid][$last_svg_fontstyle]['horiz-adv-x'] = (isset($attribs['horiz-adv-x']) ? $attribs['horiz-adv-x'] : '');
						$svg_class->svg_font[$last_svg_fontid][$last_svg_fontstyle]['d'] = (isset($attribs['d']) ? $attribs['d'] : '');
					}
					return;
				}
				// mPDF 6
				else if (strtolower($name) == 'glyph'){
					if ($last_svg_fontid && isset($attribs['unicode'])) {
						$svg_class->svg_font[$last_svg_fontid][$last_svg_fontstyle]['glyphs'][$attribs['unicode']] = array(
							'horiz-adv-x' => (isset($attribs['horiz-adv-x']) ? $attribs['horiz-adv-x'] : $last_svg_fontdefw),
							'd' => (isset($attribs['d']) ? $attribs['d'] : ''),
						);
					}
					return;
				}
				// mPDF 5.7.2
				else if (strtolower($name) == 'lineargradient'){
						$tmp_gradient = array(
							'type' => 'linear',
							'transform' => (isset($attribs['gradientTransform']) ? $attribs['gradientTransform'] : ''),
							'units' => (isset($attribs['gradientUnits']) ? $attribs['gradientUnits'] : ''),
							'spread' => (isset($attribs['spreadMethod']) ? $attribs['spreadMethod'] : ''),
							'color' => array()
						);
						if (isset($attribs['x1'])) $tmp_gradient['info']['x1'] = $attribs['x1'] ;
						if (isset($attribs['y1'])) $tmp_gradient['info']['y1'] = $attribs['y1'] ;
						if (isset($attribs['x2'])) $tmp_gradient['info']['x2'] = $attribs['x2'] ;
						if (isset($attribs['y2'])) $tmp_gradient['info']['y2'] = $attribs['y2'] ;
						$last_gradid = $attribs['id'];
						$svg_class->svgAddGradient($attribs['id'],$tmp_gradient);
					return;
				}
				else if (strtolower($name) == 'radialgradient'){
						$tmp_gradient = array(
							'type' => 'radial',
							'transform' => (isset($attribs['gradientTransform']) ? $attribs['gradientTransform'] : ''),
							'units' => (isset($attribs['gradientUnits']) ? $attribs['gradientUnits'] : ''),
							'spread' => (isset($attribs['spreadMethod']) ? $attribs['spreadMethod'] : ''),
							'color' => array()
						);
						if (isset($attribs['cx'])) $tmp_gradient['info']['x0'] = $attribs['cx'] ;
						if (isset($attribs['cy'])) $tmp_gradient['info']['y0'] = $attribs['cy'] ;
						if (isset($attribs['fx'])) $tmp_gradient['info']['x1'] = $attribs['fx'] ;
						if (isset($attribs['fy'])) $tmp_gradient['info']['y1'] = $attribs['fy'] ;
						if (isset($attribs['r'])) $tmp_gradient['info']['r'] = $attribs['r'] ;
						$last_gradid = $attribs['id'];
						$svg_class->svgAddGradient($attribs['id'],$tmp_gradient);
					return;
				}
				else if (strtolower($name) == 'stop'){
						if (!$last_gradid) break;
						$color = '#000000';
						if (isset($attribs['style']) AND preg_match('/stop-color:\s*([^;]*)/i',$attribs['style'],$m)) {
							$color = trim($m[1]);
						} else if (isset($attribs['stop-color']) && $attribs['stop-color']) {
							$color = $attribs['stop-color'];
						}
						$col = $svg_class->mpdf_ref->ConvertColor($color);
						if (!$col) { $col = $svg_class->mpdf_ref->ConvertColor('#000000'); }	// In case "transparent" or "inherit" returned
						if ($col{0}==3 || $col{0}==5) {	// RGB
							$color_final = sprintf('%.3F %.3F %.3F',ord($col{1})/255,ord($col{2})/255,ord($col{3})/255);
							$svg_class->svg_gradient[$last_gradid]['colorspace']='RGB';
						}
						else if ($col{0}==4 || $col{0}==6) {	// CMYK
							$color_final = sprintf('%.3F %.3F %.3F %.3F',ord($col{1})/100,ord($col{2})/100,ord($col{3})/100,ord($col{4})/100);
							$svg_class->svg_gradient[$last_gradid]['colorspace']='CMYK';
						}
						else if ($col{0}==1) {	// Grayscale
							$color_final = sprintf('%.3F',ord($col{1})/255);
							$svg_class->svg_gradient[$last_gradid]['colorspace']='Gray';
						}

						$stop_opacity = 1;
						if (isset($attribs['style']) AND preg_match('/stop-opacity:\s*([0-9.]*)/i',$attribs['style'],$m)) {
							$stop_opacity = $m[1];
						} else if (isset($attribs['stop-opacity'])) {
							$stop_opacity = $attribs['stop-opacity'];
						}
						else if ($col{0}==5) {	// RGBa
							$stop_opacity = ord($col{4}/100);
						}
						else if ($col{0}==6) {	// CMYKa
							$stop_opacity = ord($col{5}/100);
						}

						$tmp_color = array(
							'color' => $color_final,
							'offset' => (isset($attribs['offset']) ? $attribs['offset'] : ''),
							'opacity' => $stop_opacity
						);
						array_push($svg_class->svg_gradient[$last_gradid]['color'],$tmp_color);
					return;
				}
				if ($svg_class->inDefs) { return; }

				$svg_class->xbase = 0;
				$svg_class->ybase = 0;
				switch (strtolower($name)){

		 		// Don't output stuff inside <defs>
				case 'defs':
					$svg_class->inDefs = true;
					return;

				case 'svg':
					$svg_class->svgOffset($attribs);
					break;

				case 'path':
					$path = $attribs['d'];
					preg_match_all('/([MZLHVCSQTAmzlhvcsqta])([eE ,\-.\d]+)*/', $path, $commands, PREG_SET_ORDER);
					$path_cmd = '';
					$svg_class->subPathInit = true;
					$svg_class->pathBBox = array(999999,999999,-999999,-999999);
					foreach($commands as $c){
						if((isset($c) && count($c)==3) || (isset($c[2]) && $c[2]=='')){
							list($tmp, $command, $arguments) = $c;
						}
						else{
							list($tmp, $command) = $c;
							$arguments = '';
						}

						$path_cmd .= $svg_class->svgPath($command, $arguments);
					}
					if ($svg_class->pathBBox[2]==-1999998) { $svg_class->pathBBox[2] = 100; }
					if ($svg_class->pathBBox[3]==-1999998) { $svg_class->pathBBox[3] = 100; }
					if ($svg_class->pathBBox[0]==999999) { $svg_class->pathBBox[0] = 0; }
					if ($svg_class->pathBBox[1]==999999) { $svg_class->pathBBox[1] = 0; }
					$critere_style = $attribs;
					unset($critere_style['d']);
					$path_style = $svg_class->svgDefineStyle($critere_style);
					break;

				case 'rect':
					if (!isset($attribs['x'])) {$attribs['x'] = 0;}
					if (!isset($attribs['y'])) {$attribs['y'] = 0;}
					if (!isset($attribs['rx'])) {$attribs['rx'] = 0;}
					if (!isset($attribs['ry'])) {$attribs['ry'] = 0;}
					$arguments = array();
						if (isset($attribs['x'])) $arguments['x'] = $attribs['x'];
						if (isset($attribs['y'])) $arguments['y'] = $attribs['y'];
						if (isset($attribs['width'])) $arguments['w'] = $attribs['width'];
						if (isset($attribs['height'])) $arguments['h'] = $attribs['height'];
						if (isset($attribs['rx'])) $arguments['rx'] = $attribs['rx'];
						if (isset($attribs['ry'])) $arguments['ry'] = $attribs['ry'];
					$path_cmd =  $svg_class->svgRect($arguments);
					$critere_style = $attribs;
					unset($critere_style['x'],$critere_style['y'],$critere_style['rx'],$critere_style['ry'],$critere_style['height'],$critere_style['width']);
					$path_style = $svg_class->svgDefineStyle($critere_style);
					break;

				case 'circle':
					if (!isset($attribs['cx'])) {$attribs['cx'] = 0;}
					if (!isset($attribs['cy'])) {$attribs['cy'] = 0;}
					$arguments = array();
						if (isset($attribs['cx'])) $arguments['cx'] = $attribs['cx'];
						if (isset($attribs['cy'])) $arguments['cy'] = $attribs['cy'];
						if (isset($attribs['r'])) $arguments['rx'] = $attribs['r'];
						if (isset($attribs['r'])) $arguments['ry'] = $attribs['r'];
					$path_cmd =  $svg_class->svgEllipse($arguments);
					$critere_style = $attribs;
					unset($critere_style['cx'],$critere_style['cy'],$critere_style['r']);
					$path_style = $svg_class->svgDefineStyle($critere_style);
					break;

				