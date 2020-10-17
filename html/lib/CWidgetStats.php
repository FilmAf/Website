<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetStats extends CWidget
{
	function draw(&$wnd)
	{
		$s_opt =  "<span style='white-space:nowrap'>".
					"<span id='stats_mode_span'>".
					"</span>".
					"<a href='javascript:void(DvdStats.load(\"stat_target\",0,-3));'>Go</a>".
				  "</span>";
		CWidget::drawHeader("{$wnd->mws_ucview_id}&#39;s Collection", $s_opt, '');

		echo  "<table>".
				"<tr>".
				  "<td style='vertical-align:top'>".
					"<table id='stat_menu'>".
					  "<tr><td><h2>Show Collection by:</h2></td></tr>".
					  "<tr><td class='td_opt' id='stat_folder'>Folder</td></tr>".
					  "<tr><td class='td_opt' id='stat_genre'>Genre</td></tr>".
					  "<tr><td><h2 style='padding-top:20px'>Show Statistics:</h2></td></tr>".
					  "<tr><td class='td_opt' id='stat__dir'>Director</td></tr>".
					  "<tr><td class='td_opt' id='stat__pub'>Publisher</td></tr>".
					  "<tr><td class='td_opt' id='stat__lang'>Original language</td></tr>".
					  "<tr><td class='td_opt' id='stat__pubcnt'>DVD country</td></tr>".
					  "<tr><td class='td_opt' id='stat__region'>Region</td></tr>".
					  "<tr><td class='td_opt' id='stat__genre'>Genre</td></tr>".
					  "<tr><td class='td_opt' id='stat__format'>Format</td></tr>".
					  "<tr><td class='td_opt' id='stat__decade'>Film decade</td></tr>".
					  "<tr><td class='td_opt' id='stat__dvd_rel'>DVD release year</td></tr>".
					  "<tr><td class='td_opt' id='stat__onwed_yy'>Owned since by year</td></tr>".
					  "<tr><td class='td_opt' id='stat__onwed_mm'>Owned since by month</td></tr>".
					  "<tr><td class='td_opt' id='stat__watch_yy'>Last watched by year</td></tr>".
					  "<tr><td class='td_opt' id='stat__watch_mm'>Last watched by month</td></tr>".
					  "<tr><td class='td_opt' id='stat__retailer'>Retailer</td></tr>".
					"</table>".
				  "</td>".
				  "<td style='vertical-align:top' id='stat_target'>".
				  "</td>".
				"</tr>".
			  "</table>";
    }
}

?>
