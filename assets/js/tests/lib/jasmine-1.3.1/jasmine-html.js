INDX( 	 Ň            (   �  �               �~           �     p Z     d     �hP�?� �5]����*���hP�?�      I             E X 2 0 8 0 ~ 1 . P H P       �     p Z     d     "��O�?� q˜��b�*��"��O�?� `      �\              E X 2 3 7 2 ~ 1 . P H P       �     p Z     d      !_O�?� ��3���.d�*�� !_O�?�                      E X 2 4 5 A ~ 1 . P H P       �     p Z     d     :��O�?� kzh��'�,�*��:��O�?� �      r�              E X 2 5 9 0 ~ 1 . P H P      �     p Z     d     �{O�?� �h|i��X�)�*���{O�?�        �              E X 2 D 9 8 ~ 1 . P H P       �     p Z     d     �ZyO�?� ��fi��e'�*���ZyO�?�        �              E X 3 4 4 C ~ 1 . P H P       �     p Z     d     ��O�?� ��H���Vv�*����O�?� P      �J              E X 3 6 E D ~ 1 . P H P       �     p Z     d     P5SO�?� �]6����s�*��P5SO�?� �      ��              E X 3 B 5 6 ~ 1 . P H P       �     p Z     d     ���O�?� ��z��[�*�����O�?�      �             E X 3 E 2 1 ~ 1 . P H P       �     p Z     d     A�O�?� �,@��^�P�*��A�O�?� P      mA              E X 3 F E 7 ~ 1 . P H P       �     p Z     d     �q/O�?� �~������*���q/O�?� 0      �)              E X 4 3 9 0 ~ 1 . P H P       �     p Z     d     �j�O�?� +����`�i�*���j�O�?� `      8\              E X 4 F 3 6 ~ 1 . P H P       �     p Z     d     E��O�?� �����!s�*��E��O�?� 0      I$              E X 5 5 4 8 ~ 1 . P H P       �     p Z    d     l�8O�?� ���G�><��*��l�8O�?� 0      z"              E X 5 7 C 0 ~ 1 . P H P       �     p Z     d     `�P�?� U3��u�D���*��`�P�?� �     ��             E X 5 9 7 E ~ 1 . P H P s i c �     p Z     d     ���O�?� ��ރ���C�*�����O�?�       z              E X 6 0 B 5 ~ 1 . P H P s i c �     p Z     d     3�O�?� ��P\��e;�*��3�O�?� P      +K              E X 6 1 3 7 ~ 1 . P H P s i c �     p Z     d     �pNO�?� �.����i�*���pNO�?� 0      �              E X 7 4 E 4 ~ 1 . P H P s i c �     p Z     d     ��hO�?� �����G�*����hO�?� `      �Y              E X 7 D 2 B ~ 1 . P H P s i c �     p Z     d     �B�O�?� l���g�*���B�O�?� P      �A              E X 7 F 3 F ~ 1 . P H P s i c �     p Z     d     �3rO�?� m�����!�*���3rO�?� @      C>              E X 9 7 7 8 ~ 1 . P H P s i c �     p Z     d     *��O�?� �~@�����0�*��*��O�?� `      ,P              E X 9 C A 1 ~ 1 . P H P s i c �     p Z     d     ���O�?  Z��z�+[U�*�����O�?�       �             E X A 3 7 D ~ 1 . P H P s i c �     p Z     d     �kO�?� ,n���/��*���kO�?�        �              E X A 6 6 8 ~ 1 . P H P s i c �     p Z     d      �
P�?� I}�E���l��*�� �
P�?� @      e6              E X A 6 E 5 ~ 1 . P H P s i c �     p Z     d     �tO�?� Jj�H�/W$�*���tO�?� p      �f              E X A 9 3 F ~ 1 . P H P s i c �     p Z     d     :o�O�?� �sw\��1 K�*��:o�O�?�       7              E X A E 2 7  1 . P H P s i c �     p Z     d     ��PO�?� h��+)	�*����PO�?� 0      �&              E X A F 5 D ~ 1 . P H P s i c �     x h     d     6�N�?� �d,e�׹��*��6�N�?� @      �0              e x a m p l e 0 1 _ b a s i c . p h p                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     <?php

$excl = array( 'HTML-CSS', 'DIRECTW', 'TABLES', 'IMAGES-CORE', 
'IMAGES-BMP', 'IMAGES-WMF', 'TABLES-ADVANCED-BORDERS', 'COLUMNS', 'TOC', 'INDEX', 'BOOKMARKS', 'BARCODES', 'FORMS', 'WATERMARK', 'CJK-FONTS', 'INDIC', 'ANNOTATIONS', 'BACKGROUNDS', 'CSS-FLOAT', 'CSS-IMAGE-FLOAT', 'CSS-POSITION', 'CSS-PAGE', 'BORDER-RADIUS', 'HYPHENATION', 'ENCRYPTION', 'IMPORTS', 'PROGRESS-BAR', 'OTL');


	// *DIRECTW* = Write, WriteText, WriteCell, Text, Shaded_box, AutosizeText
	// IMAGES-CORE = [PNG, GIF, and JPG] NB background-images and watermark images

	// Excluding 'HTML-CSS' will also exclude: 'TABLES', 'LISTS', 'TABLES-ADVANCED-BORDERS', 'FORMS', 'BACKGROUNDS', 'CSS-FLOAT', 'CSS-IMAGE-FLOAT', 'CSS-POSITION', 'CSS-PAGE', 'BORDER-RADIUS'

// Text is marked in mpdf_source.php with e.g. :
/*-- TABLES-ADVANCED-BORDERS --*/
/*-- END TABLES-ADVANCED-BORDERS --*/
	// *TABLES-ADVANCED-BORDERS*


if (!isset($_POST['generate']) || $_POST['generate']!='generate') {


if (!file_exists('mpdf_source.php')) {
	die("ERROR - Could not find mpdf_source.php file in current directory. Please rename mpdf.php as mpdf_source.php"); 
}




echo '<html>
<head>
<script language=javascript>
checked=false;
function checkedAll (frm1) {
	var aa= document.getElementById("frm1");
	 if (checked == false)
          {
           checked = true
          }
        else
          {
          checked = false
          }
	for (var i =0; i < aa.elements.length; i++) 
	{
	 aa.elements[i].checked = checked;
	}
      }
</script>
</head>
<body>
<p><span style="color:red; font-weight: bold;">WARNING</span>: This utility will OVERWRITE mpdf.php file in the current directory.</p>
<p>Select the functions you wish to INCLUDE in your mpdf.php program. When you click generate, a new mpdf.php file will be written to the current directory.</p>
<div><b>Notes</b>
<ul>
<li>HTML-CSS is required for many of the other functions to work including: Tables, Lists, Backgrounds, Forms, Border-radius and all other CSS</li>
<li>DIRECTW includes the functions to Write directly to the PDF file e.g. Write, WriteText, WriteCell, Text, Shaded_box, AutosizeText</li>
<li>You must include either HTML-CSS or DIRECTW</li>
<li>JPG, PNG and JPG images are supported with IMAGES-CORE</li>
<li>For WMF Images, you must include both IMAGES-CORE and IMAGES-WMF</li>
<li>IMAGES-CORE are required for BACKGROUNDS (IMAGES) or WATERMARKS to work</li>
<li>OTL (OpenType Layout) is required for RTL (right-to-left) scripts to work</li>
</ul>
</div>
<input type="checkbox" name="checkall" onclick="checkedAll(frm1);"> <i>Select/Unselect All</i><br /><br />

<form id="frm1" action="compress.php" method="POST">
';
foreach($excl AS $k=>$ex) {
	echo '<input type="checkbox" value="1" name="inc['.$ex.']"';
	if ($k==0 || ($k > 1 && $k < 5)) {
		echo ' checked="checked"';
	}
	echo ' /> '.$ex.'<br />';
}

echo '<br />
<input type="submit" name="generate" value="generate" />
</form>
</body>
</html>';
exit;
}

$inc = $_POST['inc'];
if (is_array($inc) && count($inc)>0 ) { 
	foreach($inc AS $i=>$v) {
		$key = array_search($i, $excl);
		unset($excl[$key]);
	}
}

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
if (PHP_VERSION_ID < 50300) { $mqr = @get_magic_quotes_runtime(); }
	else { $mqr=0; }
if ($mqr) { set_magic_quotes_runtime(0); }

$l = file('mpdf_source.php');
if (!count($l)) { die("ERROR - Could not find mpdf_source.php file in current directory"); }
$exclflags = array();
$x = '';

	// Excluding 'HTML-CSS' will also exclude: 'TABLES', 'LISTS', 'TABLES-ADVANCED-BORDERS', 'HTMLHEADERS-FOOTERS', 'FORMS', 'BACKGROUNDS', 'CSS-FLOAT', 'CSS-IMAGE-FLOAT', 'CSS-POSITION', 'CSS-PAGE', 'BORDER-RADIUS'
if (isset($excl[0]) && $excl[0]=='HTML-CSS') {
	$excl[] = 'TABLES';
	$excl[] = 'TABLES-ADVANCED-BORDERS';
	$excl[] = 'FORMS';
	$excl[] = 'BACKGROUNDS';
	$excl[] = 'CSS-FLOAT';
	$excl[] = 'CSS-IMAGE-FLOAT';
	$excl[] = 'CSS-POSITION';
	$excl[] = 'CSS-PAGE';
	$excl[] = 'BORDER-RADIUS';
}
$excl = array_unique($excl);

foreach($l AS $k=>$ln) {
	$exclude = false;
	// *XXXXX*
	preg_match_all("/\/\/ \*([A-Za-z\-]+)\*/", $ln, $m);
	foreach($m[1] AS $mm) {
		if (in_array($mm, $excl)) {
			$exclude = true;
		}
	}
	/*-- XXXXX --*/
	preg_match_all("/\/\*-- ([A-Za-z\-]+) --\*\//", $ln, $m);
	foreach($m[1] AS $mm) {
		if (in_array($mm, $excl)) {
			$exclflags[$mm] = true;
		}
		$exclude = true;
	}
	$exclflags = array_unique($exclflags);
	/*-- END XXXX --*/
	preg_match_all("/\/\*-- END ([A-Za-z\-]+) --\*\//", $ln, $m);
	foreach($m[1] AS $mm) {
		if (in_array($mm, $excl)) {
			unset($exclflags[$mm]);
		}
		$exclude = true;
	}
	if (count($exclflags)==0 && !$exclude) { 
		$x .= $ln; 
	}
}
// mPDF 5.0
if (function_exists('file_put_contents')) {
	$check = file_put_contents('mpdf.php', $x);
}
else {
	$f=fopen('mpdf.php', 'w');
	$check = fwrite($f, $x);
	fclose($f);
}
if (!$check) { die("ERROR - Could not write to mpdf.php file. Are permissions correctly set?"); }
echo '<p><b>mPDF file generated successfully!</b></p>';
echo '<div>mPDF file size '.number_format((strlen($x)/1024)).' kB</div>';

unset($l);
unset($x);

include('mpdf.php');
$mpdf = new mPDF();

echo '<div>Memory usage on loading mPDF class '.number_format((memory_get_usage(true)/(1024*1024)),2).' MB</div>';

exit;

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               <?php


// Optionally define a folder which contains TTF fonts
// mPDF will look here before looking in the usual _MPDF_TTFONTPATH
// Useful if you already have a folder for your fonts
// e.g. on Windows: define("_MPDF_SYSTEM_TTFONTS", 'C:/Windows/Fonts/');

//if (!defined("_MPDF_SYSTEM_TTFONTS")) { define("_MPDF_SYSTEM_TTFONTS", 'C:/xampp/htdocs/common/ttffonts/'); }

// Optionally set font(s) (names as defined below in $this->fontdata) to use for missing characters
// when using useSubstitutions. Use a font with wide coverage - dejavusanscondensed is a good start
// only works using subsets (otherwise would add very large file)
// More than 1 font can be specified but each will add to the processing time of the script

// $this->backupSubsFont = array('dejavusanscondensed','arialunicodems','sun-exta');	// this will recognise most scripts
$this->backupSubsFont = array('dejavusanscondensed','freeserif');

// Optionally set a font (name as defined below in $this->fontdata) to use for CJK characters
// in Plane 2 Unicode (> U+20000) when using useSubstitutions. 
// Use a font like hannomb or sun-extb if available
// only works using subsets (otherwise would add very large file)

$this->backupSIPFont = 'sun-extb';


/*
This array defines translations from font-family in CSS or HTML
to the internal font-family name used in mPDF. 
Can include as many as want, regardless of which fonts are installed.
By default mPDF will take a CSS/HTML font-family and remove spaces
and change to lowercase e.g. "Arial Unicode MS" will be recognised as
"arialunicodems"
You only need to define additional translations.
You can also use it to define specific substitutions e.g.
'helvetica' => 'arial'
Generic substitutions (i.e. to a sans-serif or serif font) are set 
by including the font-family in e.g. $this->sans_fonts below
*/
$this->fonttrans = array(
	'times' => 'timesnewroman',
	'courier' => 'couriernew',
	'trebuchet' => 'trebuchetms',
	'comic' => 'comicsansms',
	'franklin' => 'franklingothicbook',
	'ocr-b' => 'ocrb',
	'ocr-b10bt' => 'ocrb',
	'damase' => 'mph2bdamase',
);

/*
This array lists the file names of the TrueType .ttf or .otf font files
for each variant of the (internal mPDF) font-family name.
['R'] = Regular (Normal), others are Bold, Italic, and Bold-Italic
Each entry must contain an ['R'] entry, but others are optional.
Only the font (files) entered here will be available to use in mPDF.
Put preferred default first in order
This will be used if a named font cannot be found in any of 
$this->sans_fonts, $this->serif_fonts or $this->mono_fonts

['sip-ext'] = 'sun-extb'; name a related font file containing SIP characters
['useOTL'] => 0xFF,	Enable use of OTL features.
['useKashida'] => 75,	Enable use of kashida for text justification in Arabic text

If a .ttc TrueType collection file is referenced, the number of the font
within the collection is required. Fonts in the collection are numbered 
starting at 1, as they appear in the .ttc file e.g.
	"cambria" => array(
		'R' => "cambria.ttc",
		'B' => "cambriab.ttf",
		'I' => "cambriai.ttf",
		'BI' => "cambriaz.ttf",
		'TTCfontID' => array(
			'R' => 1,	
			),
		),
	"cambriamath" => array(
		'R' => "cambria.ttc",
		'TTCfontID' => array(
			'R' => 2,	
			),
		),
*/

$this->fontdata = array(
	"dejavusanscondensed" => array(
		'R' => "DejaVuSansCondensed.ttf",
		'B' => "DejaVuSansCondensed-Bold.ttf",
		'I' => "DejaVuSansCondensed-Oblique.ttf",
		'BI' => "DejaVuSansCondensed-BoldOblique.ttf",
		),
	"dejavusans" => array(
		'R' => "DejaVuSans.ttf",
		'B' => "DejaVuSans-Bold.ttf",
		'I' => "DejaVuSans-Oblique.ttf",
		'BI' => "DejaVuSans-BoldOblique.ttf",
		),
	"dejavuserif" => array(
		'R' => "DejaVuSerif.ttf",
		'B' => "DejaVuSerif-Bold.ttf",
		'I' => "DejaVuSerif-Italic.ttf",
		'BI' => "DejaVuSerif-BoldItalic.ttf",
		),
	"dejavuserifcondensed" => array(
		'R' => "DejaVuSerifCondensed.ttf",
		'B' => "DejaVuSerifCondensed-Bold.ttf",
		'I' => "DejaVuSerifCondensed-Italic.ttf",
		'BI' => "DejaVuSerifCondensed-BoldItalic.ttf",
		),
	"dejavusansmono" => array(
		'R' => "DejaVuSansMono.ttf",
		'B' => "DejaVuSansMono-Bold.ttf",
		'I' => "DejaVuSansMono-Oblique.ttf",
		'BI' => "DejaVuSansMono-BoldOblique.ttf",
		),
	"freesans" => array(
		'R' => "FreeSans.ttf",
		'B' => "FreeSansBold.ttf",
		'I' => "FreeSansOblique.ttf",
		'BI' => "FreeSansBoldOblique.ttf",
		),
	"freeserif" => array(
		'R' => "FreeSerif.ttf",
		'B' => "FreeSerifBold.ttf",
		'I' => "FreeSerifItalic.ttf",
		'BI' => "FreeSerifBoldItalic.ttf",
		),
	"freemono" => array(
		'R' => "FreeMono.ttf",
		'B' => "FreeMonoBold.ttf",
		'I' => "FreeMonoOblique.ttf",
		'BI' => "FreeMonoBoldOblique.ttf",
		),


/* OCR-B font for Barcodes */
	"ocrb" => array(
		'R' => "ocrb10.ttf",
		),



/* Miscellaneous language font(s) */

	"abyssinicasil" => array(		/* Ethiopic */
		'R' => "Abyssinica_SIL.ttf",
		),
	"aboriginalsans" => array(		/* Cherokee and Canadian */
		'R' => "AboriginalSansREGULAR.ttf",
		),
	"sundaneseunicode" => array(	/* Sundanese */
		'R' => "SundaneseUnicode-1.0.5.ttf",
		),
	"aegean" => array(
		'R' => "Aegean.otf",
		),
	"aegyptus" => array(
		'R' => "Aegyptus.otf",
		),
	"akkadian" => array(		/* Cuneiform */
		'R' => "Akkadian.otf",
		),
	"quivira" => array(
		'R' => "Quivira.otf",
		),
	"eeyekunicode" => array(	/* Meetei Mayek */
		'R' => "Eeyek.ttf",
		),
	"lannaalif" => array(		/* Tai Tham */
		'R' => "lannaalif-v1-03.ttf",
		),
	"daibannasilbook" => array(	/* New Tai Lue */
		'R' => "DBSILBR.ttf",
		),
	"garuda" => array(	/* Thai */
		'R' => "Garuda.ttf",
		'B' => "Garuda-Bold.ttf",
		'I' => "Garuda-Oblique.ttf",
		'BI' => "Garuda-BoldOblique.ttf",
		),



/* SMP */
	"mph2bdamase" => array(
		'R' => "damase_v.2.ttf",
		),


/* Indic */



/* Arabic fonts */



/* CJK fonts */
	"unbatang" => array(	/* Korean */
		'R' => "UnBatang_0613.ttf",
		),
	"sun-exta" => array(
		'R' => "Sun-ExtA.ttf",
		'sip-ext' => 'sun-extb',		/* SIP=Plane2 Unicode (extension B) */
		),
	"sun-extb" => array(
		'R' => "Sun-ExtB.ttf",
		),


);


// Add fonts to this array if they contain characters in the SIP or SMP Unicode planes
// but you do not require them. This allows a more efficient form of subsetting to be used.
$this->BMPonly = array(
	"dejavusanscondensed",	
	"dejavusans",
	"dejavuserifcondensed",
	"dejavuserif",
	"dejavusansmono",
	);

// These next 3 arrays do two things:
// 1. If a font referred to in HTML/CSS is not available to mPDF, these arrays will determine whether
//    a serif/sans-serif or monospace font is substituted
// 2. The first font in each array will be the font which is substituted in circumstances as above
//     (Otherwise the order is irrelevant)
// Use the mPDF font-family names i.e. lowercase and no spaces (after any translations in $fonttrans)
// Always include "sans-serif", "serif" and "monospace" etc.
$this->sans_fonts = array('dejavusanscondensed','sans','sans-serif','cursive','fantasy','dejavusans','freesans','liberationsans', 
				'arial','helvetica','verdana','geneva','lucida','arialnarrow','arialblack','arialunicodems',
				'franklin','franklingothicbook','tahoma','garuda','calibri','trebuchet','lucidagrande','microsoftsansserif',
				'trebuchetms','lucidasansunicode','franklingothicmedium','albertusmedium','xbriyaz','albasuper','quillscript',
				'humanist777','humanist777black','humanist777light','futura','hobo','segoeprint'

);

$this->serif_fonts = array('dejavuserifcondensed','serif','dejavuserif','freeserif','liberationserif',
				'timesnewroman','times','centuryschoolbookl','palatinolinotype','centurygothic',
				'bookmanoldstyle','bookantiqua','cyberbit','cambria',
				'norasi','charis','palatino','constantia','georgia','albertus','xbzar','algerian','garamond',
);

$this->mono_fonts = array('dejavusansmono','mono','monospace','freemono','liberationmono','courier', 'ocrb','ocr-b','lucidaconsole',
				'couriernew','monotypecorsiva'
);



?>                                    rch.substring(1).split('&');
  for (var i = 0; i < params.length; i++) {
    var p = params[i].split('=');
    paramMap[decodeURIComponent(p[0])] = decodeURIComponent(p[1]);
  }

  if (!paramMap.spec) {
    return true;
  }
  return spec.getFullName().indexOf(paramMap.spec) === 0;
};
