<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetTopDirectors extends CWidget
{
	function draw(&$wnd)
	{
		echo  "<h2><span style='white-space:nowrap'>Most Collected Directors</span></h2>".
			  "<img src='http://dv1.us/d1/1.gif' width='270' height='1' />";

		$ss = "SELECT c.director val, count(*) cnt ".
				"FROM (SELECT user_id ".
						"FROM dvdaf_user_2 ".
					   "WHERE last_post_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
						  "or last_link_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
						  "or last_coll_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
						  "or last_submit_tm >= DATE_ADD(now(), INTERVAL -3 MONTH)) a, ".
					 "my_dvd b, ".
					 "stats_dvd_dir c ".
			   "WHERE a.user_id = b.user_id ".
				 "and b.dvd_id = c.dvd_id ".
			   "GROUP BY c.director ".
			   "ORDER BY count(*) DESC ".
			   "LIMIT 100";

		CWidgetTopDirectors::drawStats($ss);
    }

	function drawStats($ss)
	{
		if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		{
			$s  = "<table class='stat_tbl'>";
			for ( $i = 1 ; ($a_row = CSql::fetch($rr)) ; $i++ )
			{
				$nocase = str_replace(' ','-',dvdaf3_translatestring($a_row['val'], DVDAF3_SEARCH));
				$s .= "<tr>".
						"<td class='stat_rank'>{$i}</td>".
						"<td><a href='{$this->ms_base_subdomain}/gd/{$nocase}' class='dvd_dir'>{$a_row['val']}</a> <span>({$a_row['cnt']})</span></td>".
//						"<td><a href='{$this->ms_base_subdomain}/search.html?dir={$a_row['val']}' class='dvd_dir'>{$a_row['val']}</a> <span>({$a_row['cnt']})</span></td>".
					  "</tr>";
			}
			CSql::free($rr);
			echo $s . "</table>";
		}
	}
}

?>
