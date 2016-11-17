reHTML; }
			if ($toc_postHTML) { $this->TOCpostHTML = $toc_postHTML; }
			if ($toc_bookmarkText) { $this->TOCbookmarkText = $toc_bookmarkText; }

			$this->TOC_margin_left = $toc_mgl;
			$this->TOC_margin_right = $toc_mgr;
			$this->TOC_margin_top = $toc_mgt;
			$this->TOC_margin_bottom = $toc_mgb;
			$this->TOC_margin_header = $toc_mgh;
			$this->TOC_margin_footer = $toc_mgf;
			$this->TOC_odd_header_name = $toc_ohname;
			$this->TOC_even_header_name = $toc_ehname;
			$this->TOC_odd_footer_name = $toc_ofname;
			$this->TOC_even_footer_name = $toc_efname;
			$this->TOC_odd_header_value = $toc_ohvalue;
			$this->TOC_even_header_value = $toc_ehvalue;
			$this->TOC_odd_footer_value = $toc_ofvalue;
			$this->TOC_even_footer_value = $toc_efvalue;
			$this->TOC_page_selector = $toc_pagesel;
			$this->TOC_resetpagenum = $toc_resetpagenum;	// mPDF 6
			$this->TOC_pagenumstyle = $toc_pagenumstyle;	// mPDF 6
			$this->TOC_suppress = $toc_suppress;	// mPDF 6
			$this->TOCsheetsize = $toc_sheetsize;
		}
}

// Initiate, and Mark a place for the Table of Contents to be inserted
function TOC($tocfont='', $tocfontsize=0, $tocindent=0, $resetpagenum='', $pagenumstyle='', $suppress='', $toc_orientation='', $TOCusePaging=true, $TOCuseLinking=false, $toc_id=0, $tocoutdent='', $toc_resetpagenum='', $toc_pagenumstyle='', $toc_suppress='') {	// mPDF 5.6.19	// mPDF 6
		if (strtoupper($toc_id)=='ALL') { $toc_id = '_mpdf_all'; }
		else if (!$toc_id) { $toc_id = 0; }
		else { $toc_id = strtolower($toc_id); }
		// To use odd and even pages
		// Cannot start table of contents on an even page
		if (($this->mpdf->mirrorMargins) && (($this->mpdf->page)%2==0)) {	// EVEN
			if ($this->mpdf->ColActive) {
				if (count($this->mpdf->columnbuffer)) { $this->mpdf->printcolumnbuffer(); }
			}
			$this->mpdf->AddPage($this->mpdf->CurOrientation,'',$resetpagenum, $pagenumstyle, $suppress);
		}
		else { 
			$this->mpdf->PageNumSubstitutions[] = array('from'=>$this->mpdf->page, 'reset'=> $resetpagenum, 'type'=>$pagenumstyle, 'suppress'=>$suppress);
		}
		if ($toc_id) {
			$this->m_TOC[$toc_id]['TOCmark'] = $this->mpdf->page; 
			$this->m_TOC[$toc_id]['TOCoutdent'] = $tocoutdent;
			$this->m_TOC[$toc_id]['TOCorientation'] = $toc_orientation;
			$this->m_TOC[$toc_id]['TOCuseLinking'] = $TOCuseLinking;
			$this->m_TOC[$toc_id]['TOCusePaging'] = $TOCusePaging;
			$this->m_TOC[$toc_id]['TOC_resetpagenum'] = $toc_resetpagenum;	// mPDF 6
			$this->m_TOC[$toc_id]['TOC_pagenumstyle'] = $toc_pagenumstyle;	// mPDF 6
			$this->m_TOC[$toc_id]['TOC_suppress'] = $toc_suppress;	// mPDF 6
		}
		else {
			$this->TOCmark = $this->mpdf->page; 
			$this->TOCoutdent = $tocoutdent;
			$this->TOCorientation = $toc_orientation;
			$this->TOCuseLinking = $TOCuseLinking;
			$this->TOCusePaging = $TOCusePaging;
			$this->TOC_resetpagenum = $toc_resetpagenum;	// mPDF 6
			$this->TOC_pagenumstyle = $toc_pagenumstyle;	// mPDF 6
			$this->TOC_suppress = $toc_suppress;	// mPDF 6
		}
}


function insertTOC() {
	$notocs = 0;
	if ($this->TOCmark) { $notocs = 1; }
	$notocs += count($this->m_TOC);

	if ($notocs==0) { return; }

	if (count($this->m_TOC)) { reset($this->m_TOC); }
	$added_toc_pages = 0;

	if ($this->mpdf->ColActive) { $this->mpdf->SetColumns(0); }
	if (($this->mpdf->mirrorMargins) && (($this->mpdf->page)%2==1)) {	// ODD
		$this->mpdf->AddPage($this->mpdf->CurOrientation);
		$extrapage = true;
	}
	else { $extrapage = false; }

	for ($toci = 0; $toci<$notocs; $toci++) {
		if ($toci==0 && $this->TOCmark) {
			$toc_id = 0;
			$toc_page = $this->TOCmark; 
			$tocoutdent = $this->TOCoutdent;
			$toc_orientation = $this->TOCorientation;
			$TOCuseLinking = $this->TOCuseLinking;
			$TOCusePaging = $this->TOCusePaging;
			$toc_preHTML = $this->TOCpreHTML;
			$toc_postHTML = $this->TOCpostHTML;
			$toc_bookmarkText = $this->TOCbookmarkText;
			$toc_mgl = $this->TOC_margin_left;
			$toc_mgr = $this->TOC_margin_right;
			$toc_mgt = $this->TOC_margin_top;
			$toc_mgb = $this->TOC_margin_bottom;
			$toc_mgh = $this->TOC_margin_header;
			$toc_mgf = $this->TOC_margin_footer;
			$toc_ohname = $this->TOC_odd_header_name;
			$toc_ehname = $this->TOC_even_header_name;
			$toc_ofname = $this->TOC_odd_footer_name;
			$toc_efname = $this->TOC_even_footer_name;
			$toc_ohvalue = $this->TOC_odd_header_value;
			$toc_ehvalue = $this->TOC_even_header_value;
			$toc_ofvalue = $this->TOC_odd_footer_value;
			$toc_efvalue = $this->TOC_even_footer_value;
			$toc_page_selector = $this->TOC_page_selector;
			$toc_resetpagenum = $this->TOC_resetpagenum;	// mPDF 6
			$toc_pagenumstyle = $this->TOC_pagenumstyle;	// mPDF 6
			$toc_suppress = $this->TOC_suppress;	// mPDF 6
			$toc_sheet_size = (isset($this->TOCsheetsize) ? $this->TOCsheetsize : '');
		}
		else {
			$arr = current($this->m_TOC);

			$toc_id = key($this->m_TOC);
			$toc_page = $this->m_TOC[$toc_id]['TOCmark'];
			$tocoutdent = $this->m_TOC[$toc_id]['TOCoutdent'];
			$toc_orientation = $this->m_TOC[$toc_id]['TOCorientation'];
			$TOCuseLinking = $this->m_TOC[$toc_id]['TOCuseLinking'];
			$TOCusePaging = $this->m_TOC[$toc_id]['TOCusePaging'];
			if (isset($this->m_TOC[$toc_id]['TOCpreHTML'])) { $toc_preHTML = $this->m_TOC[$toc_id]['TOCpreHTML']; }
			else { $toc_preHTML = ''; }
			if (isset($this->m_TOC[$toc_id]['TOCpostHTML'])) { $toc_postHTML = $this->m_TOC[$toc_id]['TOCpostHTML']; }
			else { $toc_postHTML = ''; }
			if (isset($this->m_TOC[$toc_id]['TOCbookmarkText'])) { $toc_bookmarkText = $this->m_TOC[$toc_id]['TOCbookmarkText']; }
			else { $toc_bookmarkText = ''; }	// *BOOKMARKS*
			$toc_mgl = $this->m_TOC[$toc_id]['TOC_margin_left'];
			$toc_mgr = $this->m_TOC[$toc_id]['TOC_margin_right'];
			$toc_mgt = $this->m_TOC[$toc_id]['TOC_margin_top'];
			$toc_mgb = $this->m_TOC[$toc_id]['TOC_margin_bottom'];
			$toc_mgh = $this->m_TOC[$toc_id]['TOC_margin_header'];
			$toc_mgf = $this->m_TOC[$toc_id]['TOC_margin_footer'];
			$toc_ohname = $this->m_TOC[$toc_id]['TOC_odd_header_name'];
			$toc_ehname = $this->m_TOC[$toc_id]['TOC_even_header_name'];
			$toc_ofname = $this->m_TOC[$toc_id]['TOC_odd_footer_name'];
			$toc_efname = $this->m_TOC[$toc_id]['TOC_even_footer_name'];
			$toc_ohvalue = $this->m_TOC[$toc_id]['TOC_odd_header_value'];
			$toc_ehvalue = $this->m_TOC[$toc_id]['TOC_even_header_value'];
			$toc_ofvalue = $this->m_TOC[$toc_id]['TOC_odd_footer_value'];
			$toc_efvalue = $this->m_TOC[$toc_id]['TOC_even_footer_value'];
			$toc_page_selector = $this->m_TOC[$toc_id]['TOC_page_selector'];
			$toc_resetpagenum = $this->m_TOC[$toc_id]['TOC_resetpagenum'];	// mPDF 6
			$toc_pagenumstyle = $this->m_TOC[$toc_id]['TOC_pagenumstyle'];	// mPDF 6
			$toc_suppress = $this->m_TOC[$toc_id]['TOC_suppress'];	// mPDF 6
			$toc_sheet_size = (isset($this->m_TOC[$toc_id]['TOCsheetsize']) ? $this->m_TOC[$toc_id]['TOCsheetsize'] : '');
			next($this->m_TOC);
		}

		// mPDF 5.6.31
		if (!$toc_orientation) { $toc_orientation= $this->mpdf->DefOrientation; }

		//  mPDF 6 number style and suppress now picked up from section preceding ToC
		list($tp_pagenumstyle, $tp_suppress, $tp_reset) = $this->mpdf->docPageSettings($toc_page-1);

		if ($toc_resetpagenum) $tp_reset = $toc_resetpagenum;	// mPDF 6
		if ($toc_pagenumstyle) $tp_pagenumstyle = $toc_pagenumstyle;	// mPDF 6
		if ($toc_suppress || $toc_suppress==='0') $tp_suppress = $toc_suppress;	// mPDF 6

		$this->mpdf->AddPage($toc_orientation, '', $tp_reset, $tp_pagenumstyle, $tp_suppress, $toc_mgl, $toc_mgr, $toc_mgt, $toc_mgb, $toc_mgh, $toc_mgf, $toc_ohname, $toc_ehname, $toc_ofname, $toc_efname, $toc_ohvalue, $toc_ehvalue, $toc_ofvalue, $toc_efvalue, $toc_page_selector, $toc_sheet_size ); // mPDF 6


		$this->mpdf->writingToC = true;	// mPDF 5.6.38
		// mPDF 5.6.31
		$tocstart=count($this->mpdf->pages);
		if (isset($toc_preHTML) && $toc_preHTML) { $this->mpdf->WriteHTML($toc_preHTML); }


		// mPDF 5.6.19
		$html ='<div class="mpdf_toc" id="mpdf_toc_'.$toc_id.'">';
		foreach($this->_toc as $t) {
		 if ($t['toc_id']==='_mpdf_all' || $t['toc_id']===$toc_id ) {
			$html .= '<div class="mpdf_toc_level_'.$t['l'].'">';
			if ($TOCuseLinking) { $html .= '<a class="mpdf_toc_a" href="#__mpdfinternallink_'.$t['link'].'">'; }
			$html .= '<span class="mpdf_toc_t_level_'.$t['l'].'">'.$t['t'].'</span>';
			if ($TOCuseLinking) { $html .= '</a>'; }
			if (!$tocoutdent) { $tocoutdent = '0'; }
			if ($TOCusePaging) { $html .= ' <dottab outdent="'.$tocoutdent.'" /> ';
				if ($TOCuseLinking) { $html .= '<a class="mpdf_toc_a" href="#__mpdfinternallink_'.$t['link'].'">'; }
				$html .= '<span class="mpdf_toc_p_level_'.$t['l'].'">'.$this->mpdf->docPageNum($t['p']).'</span>';
				if ($TOCuseLinking) { $html .= '</a>'; }
			}
			$html .= '</div>';
		 } 
		}
		$html .= '</div>';
		$this->mpdf->WriteHTML($html);

		if (isset($toc_postHTML) && $toc_postHTML) { $this->mpdf->WriteHTML($toc_postHTML); }
		$this->mpdf->writingToC = false;	// mPDF 5.6.38
		$this->mpdf->AddPage($toc_orientation,'E');

		$n_toc = $this->mpdf->page - $tocstart + 1;

		if ($toci==0 && $this->TOCmark) {
			$TOC_start = $tocstart ;
			$TOC_end = $this->mpdf->page;
			$TOC_npages = $n_toc;
		}
		else {
			$this->m_TOC[$toc_id]['start'] = $tocstart ;
			$this->m_TOC[$toc_id]['end'] = $this->mpdf->page;
			$this->m_TOC[$toc_id]['npages'] = $n_toc;
		}
	}

	$s = '';

	$s .= $this->mpdf->PrintBodyBackgrounds();

	$s .= $this->mpdf->PrintPageBackgrounds();
	$this->mpdf->pages[$this->mpdf->page] = preg_replace('/(___BACKGROUND___PATTERNS'.$this->mpdf->uniqstr.')/', "\n".$s."\n".'\\1', $this->mpdf->pages[$this->mpdf->page]);
	$this->mpdf->pageBackgrounds = array();

	//Page footer
	$this->mpdf->InFooter=true;
	$this->mpdf->Footer();
	$this->mpdf->InFooter=false;

	// 2nd time through to move pages etc.
	$added_toc_pages = 0;
	if (count($this->m_TOC)) { reset($this->m_TOC); }

	for ($toci = 0; $toci<$notocs; $toci++) {
		if ($toci==0 && $this->TOCmark) {
			$toc_id = 0;
			$toc_page = $this->TOCmark + $added_toc_pages; 
			$toc_orientation = $this->TOCorientation;
			$TOCuseLinking = $this->TOCuseLinking;
			$TOCusePaging = $this->TOCusePaging;
			$toc_bookmarkText = $this->TOCbookmarkText;	// *BOOKMARKS*

			$tocstart = $TOC_start ;
			$tocend = $n = $TOC_end;
			$n_toc = $TOC_npages;
		}
		else {
			$arr = current($this->m_TOC);

			$toc_id = key($this->m_TOC);
			$toc_page = $this->m_TOC[$toc_id]['TOCmark'] + $added_toc_pages;
			$toc_orientation = $this->m_TOC[$toc_id]['TOCorientation'];
			$TOCuseLinking = $this->m_TOC[$toc_id]['TOCuseLinking'];
			$TOCusePaging = $this->m_TOC[$toc_id]['TOCusePaging'];
			$toc_bookmarkText = $this->m_TOC[$toc_id]['TOCbookmarkText'];	// *BOOKMARKS*

			$tocstart = $this->m_TOC[$toc_id]['start'] ;
			$tocend = $n = $this->m_TOC[$toc_id]['end'] ;
			$n_toc = $this->m_TOC[$toc_id]['npages'] ;

			next($this->m_TOC);
		}

		// Now pages moved
		$added_toc_pages += $n_toc;

		$this->mpdf->MovePages($toc_page, $tocstart, $tocend) ;
		$this->mpdf->pgsIns[$toc_page] = $tocend - $tocstart + 1;

/*-- BOOKMARKS --*/
		// Insert new Bookmark for Bookmark
		if ($toc_bookmarkText) {
			$insert = -1;
			foreach($this->mpdf->BMoutlines as $i=>$o) {
				if($o['p']<$toc_page) {	// i.e. before point of insertion
					$insert = $i;
				}
			}
			$txt = $this->mpdf->purify_utf8_text($toc_bookmarkText);
			if ($this->mpdf->text_input_as_HTML) {
				$txt = $this->mpdf->all_entities_to_utf8($txt);
			}
			$newBookmark[0] = array('t'=>$txt,'l'=>0,'y'=>0,'p'=>$toc_page );
			array_splice($this->mpdf->BMoutlines,($insert+1),0,$newBookmark);
		}
/*-- END BOOKMARKS --*/

	}

	// Delete empty page that was inserted earlier
	if ($extrapage) {
		unset($this->mpdf->pages[count($this->mpdf->pages)]);
		$this->mpdf->page--;	// Reset page pointer
	}


}


function openTagTOC($attr) {
	if (isset($attr['OUTDENT']) && $attr['OUTDENT']) { $tocoutdent = $attr['OUTDENT']; } else { $tocoutdent = ''; }	// mPDF 5.6.19
	if (isset($attr['RESETPAGENUM']) && $attr['RESETPAGENUM']) { $resetpagenum = $attr['RESETPAGENUM']; } else { $resetpagenum = ''; }
	if (isset($attr['PAGENUMSTYLE']) && $attr['PAGENUMSTYLE']) { $pagenumstyle = $attr['PAGENUMSTYLE']; } else { $pagenumstyle= ''; }
	if (isset($attr['SUPPRESS']) && $attr['SUPPRESS']) { $suppress = $attr['SUPPRESS']; } else { $suppress = ''; }
	if (isset($attr['TOC-ORIENTATION']) && $attr['TOC-ORIENTATION']) { $toc_orientation = $attr['TOC-ORIENTATION']; } else { $toc_orientation = ''; }
	if (isset($attr['PAGING']) && (strtoupper($attr['PAGING'])=='OFF' || $attr['PAGING']==='0')) { $paging = false; }
	else { $paging = true; }
	if (isset($attr['LINKS']) && (strtoupper($attr['LINKS'])=='ON' || $attr['LINKS']==1)) { $links = true; }
	else { $links = false; }
	if (isset($attr['NAME']) && $attr['NAME']) { $toc_id = strtolower($attr['NAME']); } else { $toc_id = 0; }
	$this->TOC('',0,0,$resetpagenum, $pagenumstyle, $suppress, $toc_orientation, $paging, $links, $toc_id, $tocoutdent);  // mPDF 5.6.19 5.6.31 
}


function openTagTOCPAGEBREAK($attr) {
	if (isset($attr['NAME']) && $attr['NAME']) { $toc_id = strtolower($attr['NAME']); } else { $toc_id = 0; }
	if ($toc_id) {
	  if (isset($attr['OUTDENT']) && $attr['OUTDENT']) { $this->m_TOC[$toc_id]['TOCoutdent'] = $attr['OUTDENT']; } else { $this->m_TOC[$toc_id]['TOCoutdent'] = ''; }	// mPDF 5.6.19
	  if (isset($attr['TOC-ORIENTATION']) && $attr['TOC-ORIENTATION']) { $this->m_TOC[$toc_id]['TOCorientation'] = $attr['TOC-ORIENTATION']; } else { $this->m_TOC[$toc_id]['TOCorientation'] = ''; }
	  if (isset($attr['PAGING']) && (strtoupper($attr['PAGING'])=='OFF' || $attr['PAGING']==='0')) { $this->m_TOC[$toc_id]['TOCusePagin