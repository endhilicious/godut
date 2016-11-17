<?php

// ******************************************************************************
// Software: mPDF, Unicode-HTML Free PDF generator                              *
// Version:  6.0        based on                                                *
//           FPDF by Olivier PLATHEY                                            *
//           HTML2FPDF by Renato Coelho                                         *
// Date:     2014-11-24                                                         *
// Author:   Ian Back <ianb@bpm1.com>                                           *
// License:  GPL                                                                *
//                                                                              *
// Changes:  See changelog.txt                                                  *
// ******************************************************************************


define('mPDF_VERSION','6.0');

//Scale factor
define('_MPDFK', (72/25.4));


// Specify which font metrics to use:
// 'winTypo' uses sTypoAscender etc from the OS/2 table and is the one usually recommended - BUT
// 'win' use WinAscent etc from OS/2 and inpractice seems to be used more commonly in Windows environment
// 'mac' uses Ascender etc from hhea table, and is used on Mac/OSX environment
if (!defined('_FONT_DESCRIPTOR')) define("_FONT_DESCRIPTOR", 'win');	// Values: '' [BLANK] or 'win', 'mac', 'winTypo'


/*-- HTML-CSS --*/

define('_BORDER_ALL',15);
define('_BORDER_TOP',8);
define('_BORDER_RIGHT',4);
define('_BORDER_BOTTOM',2);
define('_BORDER_LEFT',1);
/*-- END HTML-CSS --*/

// mPDF 6.0
// Used for $textvars - user settings via CSS
define('FD_UNDERLINE',1);	// font-decoration
define('FD_LINETHROUGH',2);
define('FD_OVERLINE',4);
define('FA_SUPERSCRIPT',8);	// font-(vertical)-align
define('FA_SUBSCRIPT',16);
define('FT_UPPERCASE',32);	// font-transform
define('FT_LOWERCASE',64);
define('FT_CAPITALIZE',128);
define('FC_KERNING',256);	// font-(other)-controls
define('FC_SMALLCAPS',512);


if (!defined('_MPDF_PATH')) define('_MPDF_PATH', dirname(preg_replace('/\\\\/','/',__FILE__)) . '/');
if (!defined('_MPDF_URI')) define('_MPDF_URI',_MPDF_PATH);

require_once(_MPDF_PATH.'includes/functions.php');
require_once(_MPDF_PATH.'config_lang2fonts.php');

require_once(_MPDF_PATH.'classes/ucdn.php');	// mPDF 6.0

/*-- OTL --*/
require_once(_MPDF_PATH.'classes/indic.php');	// mPDF 6.0
require_once(_MPDF_PATH.'classes/myanmar.php');	// mPDF 6.0
require_once(_MPDF_PATH.'classes/sea.php');	// mPDF 6.0
/*-- END OTL --*/


if (!defined('_JPGRAPH_PATH')) define("_JPGRAPH_PATH", _MPDF_PATH.'jpgraph/'); 

if (!defined('_MPDF_TEMP_PATH')) define("_MPDF_TEMP_PATH", _MPDF_PATH.'tmp/');

if (!defined('_MPDF_TTFONTPATH')) { define('_MPDF_TTFONTPATH',_MPDF_PATH.'ttfonts/'); }
if (!defined('_MPDF_TTFONTDATAPATH')) { define('_MPDF_TTFONTDATAPATH',_MPDF_PATH.'ttfontdata/'); }

$errorlevel=error_reporting();
$errorlevel=error_reporting($errorlevel & ~E_NOTICE);

//error_reporting(E_ALL);

if(function_exists("date_default_timezone_set")) {
	if (ini_get("date.timezone")=="") { date_default_timezone_set("Europe/London"); }
}
if (!function_exists("mb_strlen")) { die("Error - mPDF requires mb_string functions. Ensure that PHP is compiled with php_mbstring.dll enabled."); }

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

class mPDF
{

///////////////////////////////
// EXTERNAL (PUBLIC) VARIABLES
// Define these in config.php
///////////////////////////////
var $useFixedNormalLineHeight;	// mPDF 6
var $useFixedTextBaseline;	// mPDF 6
var $adjustFontDescLineheight;	// mPDF 6
var $interpolateImages; // mPDF 6
var $defaultPagebreakType;	// mPDF 6 pagebreaktype

var $indexUseSubentries; // mPDF 6

var $autoScriptToLang; // mPDF 6
var $baseScript; // mPDF 6
var $autoVietnamese; // mPDF 6
var $autoArabic; // mPDF 6

var $CJKforceend;
var $h2bookmarks;
var $h2toc;
var $decimal_align;
var $margBuffer;
var $splitTableBorderWidth;

var $bookmarkStyles;
var $useActiveForms;

var $repackageTTF;
var $allowCJKorphans;
var $allowCJKoverflow;

var $useKerning;
var $restrictColorSpace;
var $bleedMargin;
var $crossMarkMargin;
var $cropMarkMargin;
var $cropMarkLength;
var $nonPrintMargin;

var $PDFX;
var $PDFXauto;

var $PDFA;
var $PDFAauto;
var $ICCProfile;

var $printers_info;
var $iterationCounter;
var $smCapsScale;
var $smCapsStretch;

var $backupSubsFont;
var $backupSIPFont;
var $debugfonts;
var $useAdobeCJK;
var $percentSubset;
var $maxTTFFilesize;
var $BMPonly;

var $tableMinSizePriority;

var $dpi;
var $watermarkImgAlphaBlend;
var $watermarkImgBehind;
var $justifyB4br;
var $packTableData;
var $pgsIns;
var $simpleTables;
var $enableImports;

var $debug;
var $showStats;
var $setAutoTopMargin;
var $setAutoBottomMargin;
var $autoMarginPadding;
var $collapseBlockMargins;
var $falseBoldWeight;
var $normalLineheight;
var $progressBar;
var $incrementFPR1;
var $incrementFPR2;
var $incrementFPR3;
var $incrementFPR4;

var $SHYlang;
var $SHYleftmin;
var $SHYrightmin;
var $SHYcharmin;
var $SHYcharmax;
var $SHYlanguages;
// PageNumber Conditional Text
var $pagenumPrefix;
var $pagenumSuffix;
var $nbpgPrefix;
var $nbpgSuffix;
var $showImageErrors;
var $allow_output_buffering;
var $autoPadding;
var $useGraphs;
var $tabSpaces;
var $autoLangToFont;
var $watermarkTextAlpha;
var $watermarkImageAlpha;
var $watermark_size;
var $watermark_pos;
var $annotSize;
var $annotMargin;
var $annotOpacity;
var $title2annots;
var $keepColumns;
var $keep_table_proportions;
var $ignore_table_widths;
var $ignore_table_percents;
var $list_number_suffix;
var $list_auto_mode;	// mPDF 6
var $list_indent_first_level;	// mPDF 6
var $list_indent_default;	// mPDF 6
var $list_marker_offset;	// mPDF 6
var $useSubstitutions;
var $CSSselectMedia;

var $forcePortraitHeaders;
var $forcePortraitMargins;
var $displayDefaultOrientation;
var $ignore_invalid_utf8;
var $allowedCSStags;
var $onlyCoreFonts;
var $allow_charset_conversion;

var $jSWord;
var $jSmaxChar;
var $jSmaxCharLast;
var $jSmaxWordLast;

var $max_colH_correction;


var $table_error_report;
var $table_error_report_param;
var $biDirectional;
var $text_input_as_HTML; 
var $anchor2Bookmark;
var $shrink_tables_to_fit;

var $allow_html_optional_endtags;

var $img_dpi;

var $defaultheaderfontsize;
var $defaultheaderfontstyle;
var $defaultheaderline;
v