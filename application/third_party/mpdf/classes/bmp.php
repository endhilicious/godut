 -4.447 c 1.535 -4.410 1.579 -4.367 1.647 -4.319 c 1.733 -4.259 1.828 -4.210 1.935 -4.172 c 2.040 -4.134 2.131 -4.115 2.205 -4.115 c 2.267 -4.115 2.341 -4.232 2.429 -4.469 c 2.437 -4.494 2.444 -4.511 2.448 -4.522 c 2.451 -4.531 2.456 -4.546 2.465 -4.568 c 2.546 -4.795 2.614 -4.910 2.668 -4.910 c 2.714 -4.910 2.898 -4.652 3.219 -4.136 c 3.539 -3.620 3.866 -3.136 4.197 -2.683 c 4.426 -2.367 4.633 -2.103 4.816 -1.889 c 4.998 -1.676 5.131 -1.544 5.211 -1.493 c 5.329 -1.426 5.483 -1.368 5.670 -1.319 c 5.856 -1.271 6.066 -1.238 6.296 -1.217 c 6.321 -1.352 l h  f  Q ';
			$cb_off = 'q '.$matrix.' cm '.$fill. $radio_color.' rg '.$square.' f Q ';

		}
		$this->mpdf->_newobj();
		$p=($this->mpdf->compress) ? gzcompress($cb_on) : $cb_on;
		$this->mpdf->_out('<<'.$filter.'/Length '.strlen($p).' /Resources 2 0 R>>');
		$this->mpdf->_putstream($p);
		$this->mpdf->_out('endobj');

		// output appearance stream for check box off (only if not using ZapfDingbats)
		if (!$this->formUseZapD) {
			$this->mpdf->_newobj();
			$p=($this->mpdf->compress) ? gzcompress($cb_off) : $cb_off;
			$this->mpdf->_out('<<'.$filter.'/Length '.strlen($p).' /Resources 2 0 R>>');
			$this->mpdf->_putstream($p);
			$this->mpdf->_out('endobj');
		}

	}
	return $n;
}


function _putform_ch( $form, $hPt ) {
	$put_js = 0;
	$this->mpdf->_newobj();
	$n = $this->mpdf->n;
	$this->pdf_acro_array .= $n.' 0 R ';
	$this->forms[ $form['n'] ]['obj'] = $n;

	$this->mpdf->_out('<<');
	$this->mpdf->_out('/Type /Annot ');
	$this->mpdf->_out('/Subtype /Widget');
	$this->mpdf->_out('/Rect [ '.$this->_form_rect($form['x'],$form['y'],$form['w'],$form['h'], $hPt).' ]');
	$this->mpdf->_out('/F 4');
	$this->mpdf->_out('/FT /Ch');
	if ($form['Q']) $this->mpdf->_out('/Q '.$form['Q'].'');
	$temp = '';
	$temp .= '/W '.$form['BS_W'].' ';
	$temp .= '/S /'.$form['BS_S'].' ';
	$this->mpdf->_out("/BS << $temp >>");

	$temp = '';
	$temp .= '/BC [ '.$form['BC_C']." ] ";
	$temp .= '/BG [ '.$form['BG_C']." ] ";
	$this->mpdf->_out('/MK << '.$temp.' >>');

	$this->mpdf->_out('/NM '.$this->mpdf->_textstring(sprintf('%04u-%04u', $n, (6000 + $form['n']))));
	$this->mpdf->_out('/M '.$this->mpdf->_textstring('D:'.date('YmdHis')));

	$this->mpdf->_out('/T '.$this->mpdf->_textstring($form['T']) );
	$this->mpdf->_out('/DA (/F'.$this->mpdf->fonts[$form['style']['font']]['i'].' '.$form['style']['fontsize'].' Tf '.$form['style']['fontcolor'].')');

	$opt = '';
	for( $i = 0; $i < count($form['OPT']['VAL']) ; $i++ ) {
		$opt .= '[ '.$this->mpdf->_textstring($form['OPT']['VAL'][$i]).' '.$this->mpdf->_textstring($form['OPT']['OPT'][$i]).' ] ';
	}
	$this->mpdf->_out('/Opt [ '.$opt.']');

	// selected
	$selectItem = false;
	$selectIndex = false;
	foreach ( $form['OPT']['SEL'] as $selectKey => $selectVal ) {
      	$selectName = $this->mpdf->_textstring($form['OPT']['VAL'][$selectVal]);
      	$selectItem .= ' '.$selectName.' ';
      	$selectIndex .= ' '.$selectVal.' ';
	}
	if ( $selectItem ) {
		if (count($form['OPT']['SEL']) < 2) {
	      	$this->mpdf->_out('/V '.$selectItem.' ');
	      	$this->mpdf->_out('/DV '.$selectItem.' ');
		}
		else {
	      	$this->mpdf->_out('/V ['.$selectItem.'] ');
	      	$this->mpdf->_out('/DV ['.$selectItem.'] ');
		}
	      $this->mpdf->_out('/I ['.$selectIndex.'] ');
	}
    
	if ( is_array($form['FF']) && count($form['FF'])>0 ) {
		$this->mpdf->_out('/Ff '.$this->_setflag($form['FF']).' ');
	}
	// Javascript
	if ( isset($this->array_form_choice_js[$form['T']]) ) {
		$this->mpdf->_out("/AA << /V ".($this->mpdf->n+1)." 0 R >>"); 
		$put_js = 1;
	}

	$this->mpdf->_out('>>');
	$this->mpdf->_out('endobj');
	// obj + 1
	if ( $put_js == 1 ) {
		$this->mpdf->_set_object_javascript( $this->array_form_choice_js[$form['T']]['js'] );
		unset( $this->array_form_choice_js[$form['T']] );
		$put_js = NULL;
	}

	return $n;
}


function _putform_tx( $form, $hPt ) {
	$put_js = 0;
	$this->mpdf->_newobj();
	$n = $this->mpdf->n;
	$this->pdf_acro_array .= $n.' 0 R ';
	$this->forms[ $form['n'] ]['obj'] = $n;

	$this->mpdf->_out('<<');
	$this->mpdf->_out('/Type /Annot ');
	$this->mpdf->_out('/Subtype /Widget ');

	$this->mpdf->_out('/Rect [ '.$this->_form_rect($form['x'],$form['y'],$form['w'],$form['h'], $hPt).' ] ');
	$form['hidden'] ? $this->mpdf->_out('/F 2 ') : $this->mpdf->_out('/F 4 ');
	$this->mpdf->_out('/FT /Tx ');

	$this->mpdf->_out('/H /N ');
	$this->mpdf->_out('/R 0 ');

	if ( is_array($form['FF']) && count($form['FF'])>0 ) {
		$this->mpdf->_out('/Ff '.$this->_setflag($form['FF']).' ');
	}
	if ( isset($form['maxlen']) && $form['maxlen']>0 ) {
		$this->mpdf->_out('/MaxLen '.$form['maxlen']);
	}

	$temp = '';
	$temp .= '/W '.$form['BS_W'].' ';
	$temp .= '/S /'.$form['BS_S'].' ';
	$this->mpdf->_out("/BS << $temp >>");

	$temp = '';
	$temp .= '/BC [ '.$form['BC_C']." ] ";
	$temp .= '/BG [ '.$form['BG_C']." ] ";
	$this->mpdf->_out('/MK <<'.$temp.' >>');

	$this->mpdf->_out('/T '.$this->mpdf->_textstring($form['T']) );
	$this->mpdf->_out('/TU '.$this->mpdf->_textstring($form['TU']) );
	if ($form['V'] || $form['V']==='0')
		$this->mpdf->_out('/V '.$this->mpdf->_textstring($form['V']) );
	$this->mpdf->_out('/DV '.$this->mpdf->_textstring($form['DV']) );
	$this->mpdf->_out('/DA (/F'.$this->mpdf->fonts[$form['style']['font']]['i'].' '.$form['style']['fontsize'].' Tf '.$form['style']['fontcolor'].')');
	if ( $form['Q'] ) $this->mpdf->_out('/Q '.$form['Q'].'');

	$this->mpdf->_out('/NM '.$this->mpdf->_textstring(sprintf('%04u-%04u', $n, (5000 + $form['n']))));
	$this->mpdf->_out('/M '.$this->mpdf->_textstring('D:'.date('YmdHis')));


	if ( isset($this->array_form_text_js[$form['T']]) ) {
		$put_js = 1;
		$cc = 0;
		$js_str = '';

		if ( isset($this->array_form_text_js[$form['T']]['F']) ) { 
			$cc++; 
			$js_str .= '/F '.($cc + $this->mpdf->n).' 0 R '; 
		}
		if ( isset($this->array_form_text_js[$form['T']]['K']) ) { 
			$cc++; 
			$js_str .= '/K '.($cc + $this->mpdf->n).' 0 R '; 
		}
		if ( isset($this->array_form_text_js[$form['T']]['V']) ) { 
			$cc++; 
			$js_str .= '/V '.($cc + $this->mpdf->n).' 0 R '; 
		}
		if ( isset($this->array_form_text_js[$form['T']]['C']) ) { 
			$cc++; 
			$js_str .= '/C '.($cc + $this->mpdf->n).' 0 R '; 
			$this->pdf_array_co .= $this->mpdf->n.' 0 R ';
		}
		$this->mpdf->_out('/AA << '.$js_str.' >>');
	}

	$this->mpdf->_out('>>');
	$this->mpdf->_out('endobj');

	if ( $put_js == 1 ) {
		if ( isset($this->array_form_text_js[$form['T']]['F']) ) { 
			$this->mpdf->_set_object_javascript( $this->array_form_text_js[$form['T']]['F']['js'] ); 
			unset( $this->array_form_text_js[$form['T']]['F'] );
		}
		if ( isset($this->array_form_text_js[$form['T']]['K']) ) { 
			$this->mpdf->_set_object_javascript( $this->array_form_text_js[$form['T']]['K']['js'] ); 
			unset( $this->array_form_text_js[$form['T']]['K'] );
		}
		if ( isset($this->array_form_text_js[$form['T']]['V']) ) { 
			$this->mpdf->_set_object_javascript( $this->array_form_text_js[$form['T']]['V']['js'] ); 
			unset( $this->array_form_text_js[$form['T']]['V'] );
		}
		if ( isset($this->array_form_text_js[$form['T']]['C']) ) { 
			$this->mpdf->_set_object_javascript( $this->array_form_text_js[$form['T']]['C']['js'] ); 
			unset( $this->array_form_text_js[$form['T']]['C'] );
		}
	}
	return $n;
}



}

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                           