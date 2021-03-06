l) {
				$graph->yaxis->title->SetFont(FF_USERFONT,FS_NORMAL,8*$k);
				$graph->yaxis->SetTitle($ylabel,'middle');
				$graph->yaxis->SetTitleMargin($yaxislblmargin*$k); 
				// Finally setup the title
				$graph->yaxis->SetTitleSide(SIDE_RIGHT);
				// To align the title to the right use :
				$graph->yaxis->title->Align('right');
				$graph->yaxis->title->SetAngle(0);

			}

			// Show 0 label on Y-axis (default is not to show)
			$graph->yscale->ticks->SupressZeroLabel(false);
			// Setup X-axis labels
			$graph->xaxis->SetFont(FF_USERFONT,FS_NORMAL,8*$k);
			$graph->xaxis->title->SetAngle(90);
			$graph->xaxis->SetTickLabels($legends);
			$graph->xaxis->SetLabelMargin(4*$k); 
			// X-axis title
			if ($xlabel) {
				$graph->xaxis->title->SetFont(FF_USERFONT,FS_NORMAL,8*$k);
				$graph->xaxis->SetTitleMargin($xaxislblmargin*$k); 
				$graph->xaxis->SetTitle($xlabel,'middle');
			}
			$group = array();
			foreach($data AS $series => $dat) { 
				$rdata = array();
				foreach($data[$series] AS $row) { $rdata[] = $row;  }
				// Create the bar pot
				$bplot = new BarPlot($rdata);
				$bplot->SetWidth(0.6);	// for SINGLE??
				// Setup color for gradient fill style 
				if ($bandw) { $bplot->SetPattern( $patterns[$series]); }
				else { $bplot->SetFillGradient($fills[$series],"#EEEEEE",GRAD_LEFT_REFLECTION); }

				// Set color for the frame of each bar
				$bplot->SetColor("darkgray");
				$bplot->SetLegend($labels[$series]);
				if ($bandw) { $bplot->SetShadow("gray5"); }
				if ($show_values) {
					$bplot->value-> Show();
					$bplot->value->SetMargin(6*$k); 
					$bplot->value->SetColor("darkred");
					$bplot->value->SetFont( FF_USERFONT, FS_NORMAL, 8*$k);
					if ($percent || $show_percent) { $bplot->value->SetFormat( '%d%%'); }
					else { $bplot->value->SetFormat("%s"); }
				}

				$group[] = $bplot;
			}
			if (count($data)==1) {
				$graph->Add($group[0]);
			}
			else {
				// Create the grouped bar plot 
				if ($stacked) {
					$gbplot = new AccBarPlot ($group); 
				}
				else {
					$gbplot = new GroupBarPlot ($group); 
				}
				$graph->Add($gbplot);
			}
	}
	if ($graph) {
		$graph->Stroke( _MPDF_PATH.$figure_file);
		$srcpath = str_replace("\\","/",dirname(__FILE__)) . "/";
		$srcpath .= $figure_file;
		return array('file'=>$srcpath, 'w'=>$w, 'h'=>$h);
	}
   }
   return false;
}
//======================================================================================================
//======================================================================================================
//======================================================================================================
//======================================================================================================

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       


/* LISTS */

/* 
A default margin-top/bottom for lists is NOT set in config.php - (standard browsers give outermost list a top and bottom margin).
[mPDF default CSS set in config.php only works on basic elements, cannot do selectors such as "ol ol"]
Need to add the following to do this, but also prevent margins in nested lists as per default HTML in most browsers: 
*/

ul, ol { margin-top: 0.83em; margin-bottom: 0.83em; }
ul ul, ul ol, ol ul, ol ol { margin-top: 0; margin-bottom: 0; }




/* INDEXES */
div.mpdf_index_main {
	font-family: sans-serif;
	line-height: normal;
}
div.mpdf_index_entry {
	line-height: normal;
	text-indent: -1.5em;
}
div.mpdf_index_letter {
	font-family: sans-serif;
	font-size: 1.8em;
	font-weight: bold;
	line-height: normal;
	text-transform: uppercase;
	page-break-after: avoid; 
	margin-top: 0.3em; 
	margin-collapse: collapse;
}
a.mpdf_index_link { 
	color: #000000; 
	text-decoration: none; 
}



/* TABLE OF CONTENTS */
div.mpdf_toc {
	font-family: sans-serif;
	line-height: normal;
}
a.mpdf_toc_a  {
	text-decoration: none;
	color: black;
}
div.mpdf_toc_level_0 {		/* Whole line level 0 */
	line-height: 1.5;
	margin-left: 0;
	padding-right: 0em;	/* should match the outdent specified for ToC; 0 is default; suggested value 2em */
}
span.mpdf_toc_t_level_0 {	/* Title level 0 - may be inside <a> */
	font-weight: bold;
}
span.mpdf_toc_p_level_0 {	/* Page no. level 0 - may be inside <a> */
}
div.mpdf_toc_level_1 {		/* Whole line level 1 */
	margin-left: 2em;
	text-indent: -2em;
	padding-right: 0em;	/* should match the outdent specified for ToC; 0 is default; suggested value 2em */
}
span.mpdf_toc_t_level_1 {	/* Title level 1 */
	font-style: italic;
	font-weight: bold;
}
span.mpdf_toc_p_level_1  {	/* Page no. level 1 - may be inside <a> */
}
div.mpdf_toc_level_2 {		/* Whole line level 2 */
	margin-left: 4em;
	text-indent: -2em;
	padding-right: 0em;	/* should match the outdent specified for ToC; 0 is default; suggested value 2em */
}
span.mpdf_toc_t_level_2 {	/* Title level 2 */
}
span.mpdf_toc_p_level_2 {	/* Page no. level 2 - may be inside <a> */
}

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               