op=>$val) {
										$styl .= strtolower($prop).':'.$val.';';
									}
								}
							}
						}

						if (_SVG_AUTOFONT && isset($attribs['lang']) && $attribs['lang']) { 
							if (!$svg_class->mpdf_ref->usingCoreFont) { 
								if ($attribs['lang'] != $svg_class->mpdf_ref->default_lang) {
									list ($coreSuitable,$mpdf_unifont) = GetLangOpts($attribs['lang'], $svg_class->mpdf_ref->useAdobeCJK, $svg_class->mpdf_ref->fontdata);
									if ($mpdf_unifont) { $styl .= 'font-family:'.$mpdf_unifont.';'; }
								}
							}
						}

						if ($styl) {
							if (isset($attribs['style'])) { $attribs['style'] = $styl . $attribs['style']; }
							else { $attribs['style'] = $styl; }
						}

						$array_style = $svg_class->svgDefineStyle($attribs);
						if ($array_style['transformations']) {
							$svg_class->textoutput .= ' q '.$array_style['transformations'];	// mPDF 5.7.4
						}
						array_push($svg_class->svg_style,$array_style);

						$svg_class->txt_data = array();
						$x = isset($attribs['x']) ? $svg_class->ConvertSVGSizePixels($attribs['x'],'x') : 0;		// mPDF 5.7.4
						$y = isset($attribs['y']) ? $svg_class->ConvertSVGSizePixels($attribs['y'],'y') : 0;		// mPDF 5.7.4
						$x += isset($attribs['dx']) ? $svg_class->ConvertSVGSizePixels($attribs['dx'],'x') : 0;		// mPDF 5.7.4
						$y += isset($attribs['dy']) ? $svg_class->ConvertSVGSizePixels($attribs['dy'],'y') : 0;		// mPDF 5.7.4

						$svg_class->txt_data[0] = $x;	// mPDF 5.7.4
						$svg_class->txt_data[1] = $y;	// mPDF 5.7.4
						$critere_style = $attribs;
						unset($critere_style['x'], $critere_style['y']);
						$svg_class->svgDefineTxtStyle($critere_style);

						$svg_class->textanchor = $svg_class->txt_style[count($svg_class->txt_style)-1]['text-anchor'];	// mPDF 5.7.4
						$svg_class->textXorigin = $svg_class->txt_data[0];		// mPDF 5.7.4
						$svg_class->textYorigin = $svg_class->txt_data[1];		// mPDF 5.7.4
						$svg_class->textjuststarted = true;		// mPDF 5.7.4

					break;

				// mPDF 5.7.4
				case 'tspan':

					// OUTPUT CHUNK(s) UP To NOW (svgText updates $svg_class->textlength)
					$p_cmd = $svg_class->svgText();
					$svg_class->textoutput .= $p_cmd;
					$tmp = count($svg_class->svg_style)-1;
					$current_style = $svg_class->svg_style[$tmp];

					$styl = '';
					if (_SVG_CLASSES && isset($attribs['class']) && $attribs['class']) {
						$classes = preg_split('/\s+/',trim($attribs['class']));
						foreach($classes AS $class) {
							if (isset($svg_class->mpdf_ref->cssmgr->CSS['CLASS>>'.strtoupper($class)])) {
								$c = $svg_class->mpdf_ref->cssmgr->CSS['CLASS>>'.strtoupper($class)];
								foreach($c AS $prop=>$val) {
									$styl .= strtolower($prop).':'.$val.';';
								}
							}
						}
					}

					if (_SVG_AUTOFONT && isset($attribs['lang']) && $attribs['lang']) { 
						if (!$svg_class->mpdf_ref->usingCoreFont) { 
							if ($attribs['lang'] != $svg_class->mpdf_ref->default_lang) {
									list ($coreSuitable,$mpdf_unifont) = GetLangOpts($attribs['lang'], $svg_class->mpdf_ref->useAdobeCJK, $svg_class->mpdf_ref->fontdata);
									if ($mpdf_unifont) { $styl .= 'font-family:'.$mpdf_unifont.';'; }
							}
						}
					}

					if ($styl) {
						if (isset($attribs['style'])) { $attribs['style'] = $styl . $attribs['style']; }
						else { $attribs['style'] = $styl; }
					}

					$array_style = $svg_class->svgDefineStyle($attribs);

					$svg_class->txt_data = array();


					// If absolute position adjustment (x or y), creates new block of text for text-alignment
					if (isset($attribs['x']) || isset($attribs['y'])) {
						// If text-anchor middle|end, adjust
						if ($svg_class->textanchor == 'end') { $tx = -$svg_class->texttotallength; }
						else if ($svg_class->textanchor == 'middle') { $tx = -$svg_class->texttotallength/2; }
						else { $tx = 0; }
						while(preg_match('/mPDF-AXS\((.*?)\)/',$svg_class->textoutput,$m)) {
							if ($tx) { 
								$txk = $m[1] + ($tx*$svg_class->kp);
								$svg_class->textoutput = preg_replace('/mPDF-AXS\((.*?)\)/', sprintf('%.4F', $txk) ,$svg_class->textoutput,1);
							}
							else {
								$svg_class->textoutput = preg_replace('/mPDF-AXS\((.*?)\)/','\\1',$svg_class->textoutput,1);
							}
						}

						$svg_class->svgWriteString($svg_class->textoutput);

						$svg_class->textXorigin += $svg_class->textlength;
						$currentX = $svg_class->textXorigin;
						$currentY = $svg_class->textYorigin;
						$svg_class->textlength = 0;
						$svg_class->texttotallength = 0;
						$svg_class->textoutput = '';

						$x = isset($attribs['x']) ? $svg_class->ConvertSVGSizePixels($attribs['x'],'x') : $currentX;
						$y = isset($attribs['y']) ? $svg_class->ConvertSVGSizePixels($attribs['y'],'y') : $currentY;

						$svg_class->txt_data[0] = $x;
						$svg_class->txt_data[1] = $y;
						$critere_style = $attribs;
						unset($critere_style['x'], $critere_style['y']);
						$svg_class->svgDefineTxtStyle($critere_style);

						$svg_class->textanchor = $svg_class->txt_style[count($svg_class->txt_style)-1]['text-anchor'];
						$svg_class->textXorigin = $x;
						$svg_class->textYorigin = $y;

					}
					else {

						$svg_class->textXorigin += $svg_class->textlength;
						$currentX = $svg_class->textXorigin;
						$currentY = $svg_class->textYorigin;

						$currentX += isset($attribs['dx']) ? $svg_class->ConvertSVGSizePixels($attribs['dx'],'x') : 0;
						$currentY += isset($attribs['dy']) ? $svg_class->ConvertSVGSizePixels($attribs['dy'],'y') : 0;

						$svg_class->txt_data[0] = $currentX;
						$svg_class->txt_data[1] = $currentY;
						$critere_style = $attribs;
						unset($critere_style['x'], $critere_style['y']);
						$svg_class->svgDefineTxtStyle($critere_style);
						$svg_class->textXorigin = $currentX;
						$svg_class->textYorigin = $currentY;

					}

					if ($array_style['transformations']) {
						$svg_class->textoutput .= ' q '.$array_style['transformations'];
					}
					array_push($svg_class->svg_style,$array_style);

					break;
				}

				//
				//insertion des path et du style dans le flux de donné general.
				if (isset($path_cmd) && $path_cmd) {
					// mPDF 5.0
					list($prestyle,$poststyle) = $svg_class->svgStyle($path_style, $attribs, strtolower($name));
					if (isset($path_style['transformations']) && $path_style['transformations']) {	// transformation on an element
						$svg_class->svgWriteString(" q ".$path_style['transformations']. $prestyle . $path_cmd . $poststyle . " Q\n");
					}
					else {
						$svg_class->svgWriteString(" q ".$prestyle . $path_cmd . $poststyle ." Q\n");	// mPDF 5.7.4
					}
				}
			}

			function characterData($parser, $data)
			{
				global $svg_class;
				if ($svg_class->inDefs) { return; }		// mPDF 5.7.2
				if(isset($svg_class->txt_data[2])) {
					$svg_class->txt_data[2] .= $data;
				}
				else {
					$svg_class->txt_data[2] = $data;
					$svg_class->txt_data[0] = $svg_class->textXorigin ;
					$svg_class->txt_data[1] = $svg_class->textYorigin ;				}
			}


			function xml_svg2pdf_end($parser, $name){
				global $svg_class;
				// mPDF 5.7.2
		 		// Don't output stuff inside <defs>
				if ($name == 'defs') {
					$svg_class->inDefs = false;
					return;
				}
				if ($svg_class->inDefs) { return; }
				switch($name){

					case "g":
					case "a":
						if ($svg_class->intext) { 
							$p_cmd = $svg_class->svgText();
							$svg_class->textoutput .= $p_cmd;
						}

						$tmp = count($svg_class->svg_style)-1;
						$current_style = $svg_class->svg_style[$tmp];
						if ($current_style['transformations']) {
							// If in the middle of <text> element, add to textoutput, else WriteString
							if ($svg_class->intext) { $svg_class->textoutput .= " Q\n"; }	// mPDF 5.7.4
							else { $svg_class->svgWriteString(" Q\n"); }
						}
						array_pop($svg_class->svg_style);
						array_pop($svg_class->txt_style);
						if ($svg_class->intext) { 
							$svg_class->textXorigin += $svg_class->textlength;
							$svg_class->textlength = 0;
						}

						break;
					case 'font':
						$last_svg_fontdefw = '';
						break;
					case 'font-face':
						$last_svg_fontid = '';
						$last_svg_fontstyle = '';
						break;
					case 'radialgradient':
					case 'lineargradient':
						$last_gradid = '';
						break;
					case "text":
						$svg_class->txt_data[2] = rtrim($svg_class->txt_data[2]);	// mPDF 5.7.4
						$path_cmd = $svg_class->svgText();
						$svg_class->textoutput .= $path_cmd;	// mPDF 5.7.4
						$tmp = count($svg_class->svg_style)-1;
						$current_style = $svg_class->svg_style[$tmp];
						if ($current_style['transformations']) {
							$svg_class->textoutput .= " Q\n";	// mPDF 5.7.4
						}
						array_pop($svg_class->svg_style);
						array_pop($svg_class->txt_style);	// mPDF 5.7.4

						// mPDF 5.7.4
						// If text-anchor middle|end, adjust
						if ($svg_class->textanchor == 'end') { $tx = -$svg_class->texttotallength; }
						else if ($svg_class->textanchor == 'middle') { $tx = -$svg_class->texttotallength/2; }
						else { $tx = 0; }
						while(preg_match('/mPDF-AXS\((.*?)\)/',$svg_class->textoutput,$m)) {
							if ($tx) { 
								$txk = $m[1] + ($tx*$svg_class->kp);
								$svg_class->textoutput = preg_replace('/mPDF-AXS\((.*?)\)/', sprintf('%.4F', $txk) ,$svg_class->textoutput,1);
							}
							else {
								$svg_class->textoutput = preg_replace('/mPDF-AXS\((.*?)\)/','\\1',$svg_class->textoutput,1);
							}
						}

						$svg_class->svgWriteString($svg_class->textoutput);
						$svg_class->textlength = 0;
						$svg_class->texttotallength = 0;
						$svg_class->textoutput = '';
						$svg_class->intext = false;			// mPDF 5.7.4

						break;
					// mPDF 5.7.4
					case "tspan":
						$p_cmd = $svg_class->svgText();
						$svg_class->textoutput .= $p_cmd;
						$tmp = count($svg_class->svg_style)-1;
						$current_style = $svg_class->svg_style[$tmp];
						if ($current_style['transformations']) {
							$svg_class->textoutput .= " Q\n";
						}
						array_pop($svg_class->svg_style);
						array_pop($svg_class->txt_style);

						$svg_class->textXorigin += $svg_class->textlength;
						$svg_class->textlength = 0;

						break;
				}

			}

		}

		$svg2pdf_xml='';
		global $svg_class;
		$svg_class = $this;
		// Don't output stuff inside <defs>
		$svg_class->inDefs = false;
 		$svg2pdf_xml_parser = xml_parser_create("utf-8");
		xml_parser_set_option($svg2pdf_xml_parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_element_handler($svg2pdf_xml_parser, "xml_svg2pdf_start", "xml_svg2pdf_end");
		xml_set_character_data_handler($svg2pdf_xml_parser, "ch