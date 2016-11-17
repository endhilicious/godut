/////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
//////////       BIDI ALGORITHM         ////////////////////////
////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
// These functions are called from mpdf after GSUB/GPOS has taken place
// At this stage the bidi-type is in string form
////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
/*
Bidirectional Character Types
=============================
Type 	Description 	General Scope
Strong 	
L 	Left-to-Right 		LRM, most alphabetic, syllabic, Han ideographs, non-European or non-Arabic digits, ...
LRE 	Left-to-Right Embedding LRE
LRO 	Left-to-Right Override 	LRO
R 	Right-to-Left 		RLM, Hebrew alphabet, and related punctuation
AL 	Right-to-Left Arabic 	Arabic, Thaana, and Syriac alphabets, most punctuation specific to those scripts, ...
RLE 	Right-to-Left Embedding RLE
RLO 	Right-to-Left Override 	RLO
Weak 	
PDF 	Pop Directional Format 		PDF
EN 	European Number 			European digits, Eastern Arabic-Indic digits, ...
ES 	European Number Separator 	Plus sign, minus sign
ET 	European Number Terminator 	Degree sign, currency symbols, ...
AN 	Arabic Number 			Arabic-Indic digits, Arabic decimal and thousands separators, ...
CS 	Common Number Separator 	Colon, comma, full stop (period), No-break space, ...
NSM 	Nonspacing Mark 			Characters marked Mn (Nonspacing_Mark) and Me (Enclosing_Mark) in the Unicode Character Database
BN 	Boundary Neutral 			Default ignorables, non-characters, and control characters, other than those explicitly given other types.
Neutral 	
B 	Paragraph Separator 	Paragraph separator, appropriate Newline Functions, higher-level protocol paragraph determination
S 	Segment Separator 	Tab
WS 	Whitespace 			Space, figure space, line separator, form feed, General Punctuation spaces, ...
ON 	Other Neutrals 		All other characters, including OBJECT REPLACEMENT CHARACTER
*/

function _bidiSort($ta, $str='', $dir, &$chunkOTLdata, $