<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetPic extends CWidget
{
	function draw(&$wnd, $a)
	{
		echo  "<div style='text-align:center; margin:36px 0 10px 0; width:285px'>".
				"<a href='{$wnd->ms_base_subdomain}/rt.php?vd=amz{$a['dvd_id']}'>".
				  "<img src='".CPic::location($a['pic_name'],CPic_PIC)."' width='85%' height='85%' />".
				  "<div>(from ". sprintf("%3.2f", $a['best_price']) .")</div>".
				"</a>".
			  "</div>";
    }

	function getSampleDvds(&$wnd, $n_want)
	{
		$a = array();
		$i = 0;
		if ( $wnd->mb_logged_in )
		{
			$ss = "SELECT a.dvd_id, a.pic_name, p.price_00 best_price ".
					"FROM v_my_dvd_ref b ".
					"JOIN dvd a ON b.dvd_id = a.dvd_id ".
					"JOIN price p ON p.upc = a.upc ".
				   "WHERE b.user_id ='{$wnd->ms_user_id}' and b.folder like 'wish-list%' and a.pic_status = 'Y' and a.asin != '-' and p.price_00 > 0 ".
				   "ORDER BY rand() ".
				   "LIMIT {$n_want}";
			$i = CWidgetPic::getSampleDvdsQuery($wnd, $ss, $i, $n_want);
		}

		if ( $i < $n_want )
		{
			$ss = "SELECT a.dvd_id, a.pic_name, h.best_price ".
					"FROM active_top_cache h ".
					"JOIN dvd a ON h.dvd_id = a.dvd_id ".
				   "WHERE h.pane = 'D' ".
				   "ORDER BY rand() ".
				   "LIMIT 2";
			$i = CWidgetPic::getSampleDvdsQuery($wnd, $ss, $i, $n_want);
		}

		if ( $i < $n_want )
		{
			$ss = "SELECT a.dvd_id, a.pic_name, h.best_price ".
					"FROM active_top_cache h ".
					"JOIN dvd a ON h.dvd_id = a.dvd_id ".
				   "WHERE h.pane = 'B' ".
				   "ORDER BY rand() ".
				   "LIMIT 2";
			$i = CWidgetPic::getSampleDvdsQuery($wnd, $ss, $i, $n_want);
		}
	}

	function getSampleDvdsQuery(&$wnd, $ss, $i, $n_want)
	{
		if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		{
			for (  ; ($a_row = CSql::fetch($rr)) && $i < $n_want ; $i++ )
				$wnd->ma_dvd[$i] = $a_row;
			CSql::free($rr);
		}
		return $i;
	}
}

?>
