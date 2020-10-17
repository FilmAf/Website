<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetTutorial extends CWidget
{
	function draw(&$wnd)
	{
		echo  "<h2><span style='white-space:nowrap'>Youtube Channel</span></h2>".
			  "<img src='http://dv1.us/d1/1.gif' width='270' height='1' />";

		$i = 1;

		echo  "<table class='stat_tbl'>".
				"<tr><td class='stat_rank'>"      .($i++)."</td>".
				"<td class='stat_spc'><a href='http://youtu.be/XwTlBVc_8X4' style='color:#de4141'>Director Explorer</a><p>- Films by your favorite directors</p></td></tr>".
				"<tr><td class='stat_rank'>&nbsp;".($i++)."</td>".
				"<td class='stat_spc'><a href='http://youtu.be/B2sGxT0qj2c' style='color:#de4141'>Help picking good movies</a><p>- Tabs explained</p></td></tr>".
				"<tr><td class='stat_rank'>"      .($i++)."</td>".
				"<td class='stat_spc'><a href='http://youtu.be/7z7DAAU4XwY' style='color:#de4141'>UPC Importer</a><p>- The fastest way to update your collection</p></td></tr>".
				"<tr><td class='stat_rank'>"      .($i++)."</td>".
				"<td class='stat_spc'><a href='http://www.youtube.com/user/filmaf' style='color:#de4141'>Film Aficionado&#39;s Youtube channel</a></td></tr>".
//				"<tr><td class='stat_rank'>".($i++)."</td><td><a href='http://www.youtube.com/user/filmaf'>Site navigation</a></td></tr>".
//				"<tr><td class='stat_rank'>".($i++)."</td><td><a href='http://www.youtube.com/user/filmaf'>Finding a title</a></td></tr>".
//				"<tr><td class='stat_rank'>".($i++)."</td><td><a href='http://www.youtube.com/user/filmaf'>What if you do not find a title</a></td></tr>".
			  "</table>";


    }
}

?>
