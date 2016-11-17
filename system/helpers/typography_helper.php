<?php

class tocontents {

var $mpdf = null;
var $_toc;
var $TOCmark;
var $TOCoutdent;	// mPDF 5.6.31
var $TOCpreHTML;
var $TOCpostHTML;
var $TOCbookmarkText;
var $TOCusePaging;
var $TOCuseLinking;
var $TOCorientation;
var $TOC_margin_left;
var $TOC_margin_right;
var $TOC_margin_top;
var $TOC_margin_bottom;
var $TOC_margin_header;
var $TOC_margin_footer;
var $TOC_odd_header_name;
var $TOC_even_header_name;
var $TOC_odd_footer_name;
var $TOC_even_footer_name;
var $TOC_odd_header_value;
var $TOC_even_header_value;
var $TOC_odd_footer_value;
var $TOC_even_footer_value;
var $TOC_page_selector;
var $TOC_resetpagenum;	// mPDF 6
var $TOC_pagenumstyle;	// mPDF 6
var $TOC_suppress;	// mPDF 6
var $m_TOC; 

function tocontents(&$mpdf) {
	$this->mpdf = $mpdf;
	$this->_toc=array();
	$this->TOCmark = 0;
	$this->m_TOC=array();
}

function TOCpagebreak($tocfont='', $tocfontsize='', $tocindent='', $TOCusePaging=true, $TOCuseLinking='', $toc_orientation='', $toc_mgl='',$toc_mgr='',$toc_mgt='',$toc_mgb='',$toc_mgh='',$toc_mgf='',$toc_ohname='',$toc_ehname='',$toc_ofname='',$toc_efname='',$toc_ohvalue=0,$toc_ehvalue=0,$toc_ofvalue=0, $toc_efvalue=0, $toc_preHTML='', $toc_postHTML='', $toc_bookmarkText='', $resetpagenum='', $pagenumstyle='', $suppress='', $orientation='', $mgl='',$mgr='',$mgt='',$mgb='',$mgh='',$mgf='',$ohname='',$ehname='',$ofname='',$efname='',$ohvalue=0,$ehvalue=0,$ofvalue=0,$efvalue=0, $toc_id=0, $pagesel='', $toc_pagesel='', $sheetsize='', $toc_sheetsize='', $tocoutdent='', $toc_resetpagenum='', $toc_pagenumstyle='', $toc_suppress='') {	// mPDF 5.6.19	// mPDF 6
		if (strtoupper($toc_id)=='ALL') { $toc_id = '_mpdf_all'; }
		else if (!$toc_id) { $toc_id = 0; }
		else { $toc_id = strtolower($toc_id); }

		if ($TOCusePaging === false || strtolower($TOCusePaging) == "off" || $TOCusePaging === 0 || $TOCusePaging === "0" || $TOCusePaging === "") { $TOCusePaging = false; }
		else { $TOCusePaging = true; }
		if (!$TOCuseLinking) { $TOCuseLinking = false; }
		if ($toc_id) {
			$this->m_TOC[$toc_id]['TOCmark'] = $this->mpdf->page; 
			$this->m_TOC[$toc_id]['TOCoutdent'] = $tocoutdent;
			$this->m_TOC[$toc_id]['TOCorientation'] = $toc_orientation;
			$this->m_TOC[$toc_id]['TOCuseLinking'] = $TOCuseLinking;
			$this->m_TOC[$toc_id]['TOCusePaging'] = $TOCusePaging;

			if ($toc_preHTML) { $this->m_TOC[$toc_id]['TOCpreHTML'] = $toc_preHTML; }
			if ($toc_postHTML) { $this->m_TOC[$toc_id]['TOCpostHTML'] = $toc_postHTML; }
			if ($toc_bookmarkText) { $this->m_TOC[$toc_id]['TOCbookmarkText'] = $toc_bookmarkText; }

			$this->m_TOC[$toc_id]['TOC_margin_left'] = $toc_mgl;
			$this->m_TOC[$toc_id]['TOC_margin_right'] = $toc_mgr;
			$this->m_TOC[$toc_id]['TOC_margin_top'] = $toc_mgt;
			$this->m_TOC[$toc_id]['TOC_margin_bottom'] = $toc_mgb;
			$this->m_TOC[$toc_id]['TOC_margin_header'] = $toc_mgh;
			$this->m_TOC[$toc_id]['TOC_margin_footer'] = $toc_mgf;
			$this->m_TOC[$toc_id]['TOC_odd_header_name'] = $toc_ohname;
			$this->m_TOC[$toc_id]['TOC_even_header_name'] = $toc_ehname;
			$this->m_TOC[$toc_id]['TOC_odd_footer_name'] = $toc_ofname;
			$this->m_TOC[$toc_id]['TOC_even_footer_name'] = $toc_efname;
			$this->m_TOC[$toc_id]['TOC_odd_header_value'] 