ogram is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


Also add information on how to contact you by electronic and paper mail.

If the program is interactive, make it output a short notice like this
when it starts in an interactive mode:

    Gnomovision version 69, Copyright (C) year name of author
    Gnomovision comes with ABSOLUTELY NO WARRANTY; for details type `show w'.
    This is free software, and you are welcome to redistribute it
    under certain conditions; type `show c' for details.

The hypothetical commands `show w' and `show c' should show the appropriate
parts of the General Public License.  Of course, the commands you use may
be called something other than `show w' and `show c'; they could even be
mouse-clicks or menu items--whatever suits your program.

You should also get your employer (if you work as a programmer) or your
school, if any, to sign a "copyright disclaimer" for the program, if
necessary.  Here is a sample; alter the names:

  Yoyodyne, Inc., hereby disclaims all copyright interest in the program
  `Gnomovision' (which makes passes at compilers) written by James Hacker.

  <signature of Ty Coon>, 1 April 1989
  Ty Coon, President of Vice

This General Public License does not permit incorporating your program into
proprietary programs.  If your program is a subroutine library, you may
consider it more useful to permit linking proprietary applications with the
library.  If this is what you want to do, use the GNU Library General
Public License instead of this License.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   Installation
============
    * Download the .zip file and unzip it
    * Create a folder e.g. /mpdf on your server
    * Upload all of the files to the server, maintaining the folders as they are
    * Ensure that you have write permissions set (CHMOD 6xx or 7xx) for the following folders:
	 /ttfontdata/ - used to cache font data; improves performance a lot
	 /tmp/ - used for some images and ProgressBar
	 /graph_cache/ - if you are using JpGraph in conjunction with mPDF

To test the installation, point your browser to the basic example file : [path_to_mpdf_folder]/mpdf/examples/example01_basic.php

If you wish to define a different folder for temporary files rather than /tmp/ see the note on 'Folder for temporary files' in 
 the section on Installation & Setup in the manual (http://mpdf1.com/manual/).

If you have problems, please read the section on troubleshooting in the manual.


Fonts
=====
Let us refer to font names in 2 ways:
"CSS font-family name" - mPDF is designed primarily to read HTML and CSS. This is the name used in CSS e.g.
	<p style="font-family: 'Trebuchet MS';">

"mPDF font-family name" - the name used internally to process fonts. This could be anything you like,
	but by default mPDF will convert CSS font-family names by removing any spaces and changing
	to lowercase. Reading the name above, mPDF will look for a "mPDF font-family name" of
	'trebuchetms'.

The configurable values referred to below are set in the config_fonts.php file

When parsing HTML/CSS, mPDF will read the CSS font-family name (e.g. 'Trebuchet MS') and convert 
by removing any spaces and changing to lowercase, to look for a mPDF font-family name (trebuchetms). 

Next it will look for a translation (if set) in config_font.php e.g.:
$this->fonttrans = array(
	'trebuchetms' => 'trebuchet'
)

Now the mPDF font-family name to be used is 'trebuchet'

If you wish to make this font available, you need to specify the Truetype .ttf font files for each variant.
These should be defined in config_font.php in the array:
$this->fontdata = array(
	"trebuchet" => array(
		'R' => "trebuc.ttf",
		'B' => "trebucbd.ttf",
		'I' => "trebucit.ttf",
		'BI' => "trebucbi.ttf",
		)
)

This is the array which determines whether a font is available to mPDF. Each font-family must have a
Regular ['R'] file defined - the others (bold, italic, bold-italic) are optional.

mPDF will try to load the font-file. If you have defined _MPDF_SYSTEM_TTFONTS at the top of the 
config_fonts.php file, it will first look for the font-file there. This is useful if you are running 
mPDF on a computer which already has a folder with TTF fonts in (e.g. on Windows)

If the