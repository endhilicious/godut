gin_right = $this->TOC_margin_top = $this->TOC_margin_bottom = $this->TOC_margin_header = $this->TOC_margin_footer = '';
	  if (isset($attr['TOC-MARGIN-RIGHT'])) { $this->TOC_margin_right = $this->mpdf->ConvertSize($attr['TOC-MARGIN-RIGHT'],$this->mpdf->w,$this->mpdf->FontSize,false); }
	  if (isset($attr['TOC-MARGIN-LEFT'])) { $this->TOC_margin_left = $this->mpdf->ConvertSize($attr['TOC-MARGIN-LEFT'],$this->mpdf->w,$this->mpdf->FontSize,false); }
	  if (isset($attr['TOC-MARGIN-TOP'])) { $this->TOC_margin_top = $this->mpdf->ConvertSize($attr['TOC-MARGIN-TOP'],$this->mpdf->w,$this->mpdf->FontSize,false); }
	  if (isset($attr['TOC-MARGIN-BOTTOM'])) { $this->TOC_margin_bottom = $this->mpdf->ConvertSize($attr['TOC-MARGIN-BOTTOM'],$this->mpdf->w,$this->mpdf->FontSize,false); }
	  if (isset($attr['TOC-MARGIN-HEADER'])) { $this->TOC_margin_header = $this->mpdf->ConvertSize($attr['TOC-MARGIN-HEADER'],$this->mpdf->w,$this->mpdf->FontSize,false); }
	  if (isset($attr['TOC-MARGIN-FOOTER'])) { $this->TOC_margin_footer = $this->mpdf->ConvertSize($attr['TOC-MARGIN-FOOTER'],$this->mpdf->w,$this->mpdf->FontSize,false); }
	  $this->TOC_odd_header_name = $this->TOC_even_header_name = $this->TOC_odd_footer_name = $this->TOC_even_footer_name = '';
	  if (isset($attr['TOC-ODD-HEADER-NAME']) && $attr['TOC-ODD-HEADER-NAME']) { $this->TOC_odd_header_name = $attr['TOC-ODD-HEADER-NAME']; }
	  if (isset($attr['TOC-EVEN-HEADER-NAME']) && $attr['TOC-EVEN-HEADER-NAME']) { $this->TOC_even_header_name = $attr['TOC-EVEN-HEADER-NAME']; }
	  if (isset($attr['TOC-ODD-FOOTER-NAME']) && $attr['TOC-ODD-FOOTER-NAME']) { $this->TOC_odd_footer_name = $attr['TOC-ODD-FOOTER-NAME']; }
	  if (isset($attr['TOC-EVEN-FOOTER-NAME']) && $attr['TOC-EVEN-FOOTER-NAME']) { $this->TOC_even_footer_name = $attr['TOC-EVEN-FOOTER-NAME']; }
	  $this->TOC_odd_header_value = $this->TOC_even_header_value = $this->TOC_odd_footer_value = $this->TOC_even_footer_value = 0;
	  if (isset($attr['TOC-ODD-HEADER-VALUE']) && ($attr['TOC-ODD-HEADER-VALUE']=='1' || strtoupper($attr['TOC-ODD-HEADER-VALUE'])=='ON')) { $this->TOC_odd_header_value = 1; }
	  else if (isset($attr['TOC-ODD-HEADER-VALUE']) && ($attr['TOC-ODD-HEADER-VALUE']=='-1' || strtoupper($attr['TOC-ODD-HEADER-VALUE'])=='OFF')) { $this->TOC_odd_header_value = -1; }
	  if (isset($attr['TOC-EVEN-HEADER-VALUE']) && ($attr['TOC-EVEN-HEADER-VALUE']=='1' || strtoupper($attr['TOC-EVEN-HEADER-VALUE'])=='ON')) { $this->TOC_even_header_value = 1; }
	  else if (isset($attr['TOC-EVEN-HEADER-VALUE']) && ($attr['TOC-EVEN-HEADER-VALUE']=='-1' || strtoupper($attr['TOC-EVEN-HEADER-VALUE'])=='OFF')) { $this->TOC_even_header_value = -1; }

	  if (isset($attr['TOC-ODD-FOOTER-VALUE']) && ($attr['TOC-ODD-FOOTER-VALUE']=='1' || strtoupper($attr['TOC-ODD-FOOTER-VALUE'])=='ON')) { $this->TOC_odd_footer_value = 1; }
	  else if (isset($attr['TOC-ODD-FOOTER-VALUE']) && ($attr['TOC-ODD-FOOTER-VALUE']=='-1' || strtoupper($attr['TOC-ODD-FOOTER-VALUE'])=='OFF')) { $this->TOC_odd_footer_value =