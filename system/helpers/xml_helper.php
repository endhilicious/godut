FOOTER-NAME']; }
	  $this->m_TOC[$toc_id]['TOC_odd_header_value'] = $this->m_TOC[$toc_id]['TOC_even_header_value'] = $this->m_TOC[$toc_id]['TOC_odd_footer_value'] = $this->m_TOC[$toc_id]['TOC_even_footer_value'] = 0;
	  if (isset($attr['TOC-ODD-HEADER-VALUE']) && ($attr['TOC-ODD-HEADER-VALUE']=='1' || strtoupper($attr['TOC-ODD-HEADER-VALUE'])=='ON')) { $this->m_TOC[$toc_id]['TOC_odd_header_value'] = 1; }
	  else if (isset($attr['TOC-ODD-HEADER-VALUE']) && ($attr['TOC-ODD-HEADER-VALUE']=='-1' || strtoupper($attr['TOC-ODD-HEADER-VALUE'])=='OFF')) { $this->m_TOC[$toc_id]['TOC_odd_header_value'] = -1; }
	  if (isset($attr['TOC-EVEN-HEADER-VALUE']) && ($attr['TOC-EVEN-HEADER-VALUE']=='1' || strtoupper($attr['TOC-EVEN-HEADER-VALUE'])=='ON')) { $this->m_TOC[$toc_id]['TOC_even_header_value'] = 1; }
	  else if (isset($attr['TOC-EVEN-HEADER-VALUE']) && ($attr['TOC-EVEN-HEADER-VALUE']=='-1' || strtoupper($attr['TOC-EVEN-HEADER-VALUE'])=='OFF')) { $this->m_TOC[$toc_id]['TOC_even_header_value'] = -1; }
	  if (isset($attr['TOC-ODD-FOOTER-VALUE']) && ($attr['TOC-ODD-FOOTER-VALUE']=='1' || strtoupper($attr['TOC-ODD-FOOTER-VALUE'])=='ON')) { $this->m_TOC[$toc_id]['TOC_odd_footer_value'] = 1; }
	  else if (isset($attr['TOC-ODD-FOOTER-VALUE']) && ($attr['TOC-ODD-FOOTER-VALUE']=='-1' || strtoupper($attr['TOC-ODD-FOOTER-VALUE'])=='OFF')) { $this->m_TOC[$toc_id]['TOC_odd_footer_value'] = -1; }
	  if (isset($attr['TOC-EVEN-FOOTER-VALUE']) && ($attr['TOC-EVEN-FOOTER-VALUE']=='1' || strtoupper($attr['TOC-EVEN-FOOTER-VALUE'])=='ON')) { $this->m_TOC[$toc_id]['TOC_even_footer_value'] = 1; }
	  else if (isset($attr['TOC-EVEN-FOOTER-VALUE']) && ($attr['TOC-EVEN-FOOTER-VALUE']=='-1' || strtoupper($attr['TOC-EVEN-FOOTER-VALUE'])=='OFF')) { $this->m_TOC[$toc_id]['TOC_even_footer_value'] = -1; }
	  if (isset($attr['TOC-RESETPAGENUM']) && $attr['TOC-RESETPAGENUM']) { $this->m_TOC[$toc_id]['TOC_resetpagenum'] = $attr['TOC-RESETPAGENUM']; }
	  else { $this->m_TOC[$toc_id]['TOC_resetpagenum'] = ''; }	// mPDF 6
	  if (isset($attr['TOC-PAGENUMSTYLE']) && $attr['TOC-PAGENUMSTYLE']) { $this->m_TOC[$toc_id]['TOC_pagenumstyle'] = $attr['TOC-PAGENUMSTYLE']; }
	  else { $this->m_TOC[$toc_id]['TOC_pagenumstyle'] = ''; }	// mPDF 6
	  if (isset($attr['TOC-SUPPRESS']) && ($attr['TOC-SUPPRESS'] || $attr['TOC-SUPPRESS']==='0')) { $this->m_TOC[$toc_id]['TOC_suppress'] = $attr['TOC-SUPPRESS']; }
	  else { $this->m_TOC[$toc_id]['TOC_suppress'] = ''; }	// mPDF 6
	  if (isset($attr['TOC-PAGE-SELECTOR']) && $attr['TOC-PAGE-SELECTOR']) { $this->m_TOC[$toc_id]['TOC_page_selector'] = $attr['TOC-PAGE-SELECTOR']; }
	  else { $this->m_TOC[$toc_id]['TOC_page_selector'] = ''; }
	  if (isset($attr['TOC-SHEET-SIZE']) && $attr['TOC-SHEET-SIZE']) { $this->m_TOC[$toc_id]['TOCsheetsize'] = $attr['TOC-SHEET-SIZE']; } else { $this->m_TOC[$toc_id]['TOCsheetsize'] = '';