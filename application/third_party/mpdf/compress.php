<?php

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
	$excl[] = INDX( 	 �p'            (   �  �          N ����        �     p \     �     &{�?� e.`������*��&{�?� P     �A             C H A N G E L O G . t x t     �     p Z     �     &{�?� e.`������*��&{�?� P     �A             C H A N G E ~ 1 . T X T       �     ` P     �     3@��?�?���?�?���?�?���?�                       c l a s s e s 2     h V     �     2Z��?��~1��?��~1��?��~1��?�                       
c o l l a t i o n s h 2     h R     �     2Z��?��~1��?��~1��?��~1��?�                       C O L L A T ~ 1 . p h �     p Z     �     �5��?� 5 Ʊ\�ߵ��*���5��?�        �              c o m p r e s s . p h p       �     h V     �     ����?� /
��Yկ�*������?� p      bf              
c o n f i g . p h p   �     � �     �     z���?� �7b��Ӻ�*��z���?�        �              "c o n f i g _ f o n t s - d i s t r - w i t h o u t - O T L . p h p   �     x b     �     �\��?� �H���-��*���\��?� 0     6(              c o n f i g _ f o n t s . p h p       �     � l     �     I���?� ��b���y���*��I���?� @      4              c o n f i g _ l a n g 2 f o n t s . p h p     �     � n     �     �m��?� a�O������*���m��?�        e              c o n f i g _ s c r i p t 2 l a n g . p h p   �     p Z     �     z���?� �7b��Ӻ�*��z���?�        �              C O N F