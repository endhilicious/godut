<?php
//	svg class modified for mPDF version 6.0 by Ian Back: based on -
//	svg2pdf fpdf class
//	sylvain briand (syb@godisaduck.com), modified by rick trevino (rtrevino1@yahoo.com)
//	http://www.godisaduck.com/svg2pdf_with_fpdf
//	http://rhodopsin.blogspot.com
//	
//	cette class etendue est open source, toute modification devra cependant etre repertoriée~


// If you wish to use Automatic Font selection within SVG's. change this definition to true.
// This selects different fonts for different scripts used in text.
// This can be enabled/disabled independently of the use of Automatic Font selection within mPDF generally.
// Choice of font is determined by the config_script2lang.php and config_lang2fonts.php files, the same as for mPDF generally.
if (!defined("_SVG_AUTOFONT")) { define("_SVG_AUTOFONT", false); }

// Enable a limited use of classes within SVG <text> elements by setting this to true.
// This allows recognition of a "class" attribute on a <text> element.
// The CSS style for that class should be outside the SVG, and cannot use