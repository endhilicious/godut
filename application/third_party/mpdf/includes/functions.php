r $inlineDisplayOff;
var $kt_y00;
var $kt_p00;
var $upperCase;
var $checkSIP;
var $checkSMP;
var $checkCJK;

var $watermarkImgAlpha;
var $PDFAXwarnings;
var $MetadataRoot; 
var $OutputIntentRoot;
var $InfoRoot; 
var $current_filename;
var $parsers;
var $current_parser;
var $_obj_stack;
var $_don_obj_stack;
var $_current_obj_id;
var $tpls;
var $tpl;
var $tplprefix;
var $_res;

var $pdf_version;
var $noImageFile;
var $lastblockbottommargin;
var $baselineC;
// mPDF 5.7.3  inline text-decoration parameters
var $baselineSup;
var $baselineSub;
var $baselineS;
var $subPos;
var $subArrMB;
var $ReqFontStyle;
var $tableClipPath ;

var $fullImageHeight;
var $inFixedPosBlock;		// Internal flag for position:fixed block
var $fixedPosBlock;		// Buffer string for position:fixed block
var $fixedPosBlockDepth;
var $fixedPosBlockBBox;
var $fixedPosBlockSave;
var $maxPosL;
var $maxPosR;

var $loaded;

var $extraFontSubsets;
var $docTemplateStart;		// Internal flag for page (page no. -1) that docTemplate starts on
var $time0;

// Classes
var $indic;
var $barcode;

var $SHYpatterns;
var $loadedSHYpatterns;
var $loadedSHYdictionary;
var $SHYdictionary;
var $SHYdictionaryWords;

var $spanbgcolorarray;
var $default_font;
var $headerbuffer;
var $lastblocklevelchange;
var $nestedtablejustfinished;
var $linebreakjustfinished;
var $cell_border_dominance_L;
var $cell_border_dominance_R;
var $cell_border_dominance_T;
var $cell_border_dominance_B;
var $table_keep_together;
var $plainCell_properties;
var $shrin_k1;
var $outerfilled;

var $blockContext;
var $floatDivs;


var $patterns;
var $pageBackgrounds;

var $bodyBackgroundGradient;
var $bodyBackgroundImage;
var $bodyBackgroundColor;

var $writingHTMLheader;	// internal flag - used both for writing HTMLHeaders/Footers and FixedPos block
var $writingHTMLfooter;
var $angle;

var $gradients;

var $kwt_Reference;
var $kwt_BMoutlines;
var $kwt_toc;

var $tbrot_BMoutlines;
var $tbrot_toc;

var $col_BMoutlines;
var $col_toc;

var $currentGraphId;
var $graphs;

var $floatbuffer;
var $floatmargins;

var $bullet;
var $bulletarray;


var $currentLang;
var $default_lang;
var $default_available_fonts;
var $pageTemplate;
var $docTemplate;
var $docTemplateContinue;

var $arabGlyphs;
var $arabHex;
var $persianGlyphs;
var $persianHex;
var $arabVowels;
var $arabPrevLink;
var $arabNextLink;


var $formobjects; // array of Form Objects for WMF
var $InlineProperties;
var $InlineAnnots;
var $InlineBDF;	// mPDF 6 Bidirectional formatting
var $InlineBDFctr;	// mPDF 6
var $ktAnnots;
var $tbrot_Annots;
var $kwt_Annots;
var $columnAnnots;
var $columnForms;

var $PageAnnots;

var $pageDim;	// Keep track of page wxh for orientation changes - set in _beginpage, used in _putannots

var $breakpoints;

var $tableLevel;
var $tbctr;
var $innermostTableLevel;
var $saveTableCounter;
var $cellBorderBuffer;

var $saveHTMLFooter_height;
var $saveHTMLFooterE_height;

var $firstPageBoxHeader;
var $firstPageBoxHeaderEven;
var $firstPageBoxFooter;
var $firstPageBoxFooterEven;

var $page_box;
var $show_marks;	// crop or cross marks

var $basepathIsLocal;

var $use_kwt;
var $kwt;
var $kwt_height;
var $kwt_y0;
var $kwt_x0;
var $kwt_buffer;
var $kwt_Links;
var $kwt_moved;
var $kwt_saved;

var $PageNumSubstitutions;

var $table_borders_separate;
var $base_table_properties;
var $borderstyles;

var $blockjustfinished;

var $orig_bMargin;
var $orig_tMargin;
var $orig_lMargin;
var $orig_rMargin;
var $orig_hMargin;
var $orig_fMargin;

var $pageHTMLheaders;
var $pageHTMLfooters;

var $saveHTMLHeader;
var $saveHTMLFooter;

var $HTMLheaderPageLinks;
var $HTMLheaderPageAnnots;
var $HTMLheaderPageForms;

// See config_fonts.php for these next 5 values
var $available_unifonts;
var $sans_fonts;
var $serif_fonts;
var $mono_fonts;
var $defaultSubsFont;

// List of ALL available CJK fonts (incl. styles) (Adobe add-ons)  hw removed
var $available_CJK_fonts;

var $HTMLHeader;
var $HTMLFooter;
var $HTMLHeaderE;
var $HTMLFooterE;
var $bufferoutput; 


// CJK fonts
var $Big5_widths;
var $GB_widths;
var $SJIS_widths;
var $UHC_widths;

// SetProtection
var $encrypted;	//whether document is protected
var $Uvalue;	//U entry in pdf document
var $Ovalue;	//O entry in pdf document
var $Pvalue;	//P entry in pdf document
var $enc_obj_id;	//encryption object id
var $last_rc4_key;	//last RC4 key encrypted (cached for opti