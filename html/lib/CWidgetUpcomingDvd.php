<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetUpcomingDvd extends CWidget
{
	function draw(&$wnd, $s_media)
	{
		switch ( $s_media )
		{
		case 'bd':
			CWidgetUpcomingDvd::drawHeader("Hot Blu-rays");
			CWidgetUpcomingDvd::fetchAndDraw(0, '/blu-ray', "('B','2','3','R')",'b');
			break;
		case 'dvd':
			CWidgetUpcomingDvd::drawHeader("Hot DVD&#39;s");
			CWidgetUpcomingDvd::fetchAndDraw(1, '/dvd', "('D','V')",'d');
			break;
		}
    }

    function drawHeader($s_title)
    {
		echo  "<h2 style='margin:16px 0 4px 0'><span style='white-space:nowrap'>{$s_title}</span></h2>".
			  "<img src='http://dv1.us/d1/1.gif' width='866' height='1' />";
    }

	function fetchAndDraw($s_id, $s_more, $s_media, $s_media_prefix)
	{
		$aa = array();
		$ss = "SELECT a.dvd_id, a.pic_name ".
				"FROM dvd a JOIN stats_dvd_country sc ON a.dvd_id = sc.dvd_id ".
			   "WHERE a.pic_name != '-' and a.source = 'A' and sc.country = 'us' and a.media_type in {$s_media} ".
			   "ORDER BY a.amz_rank LIMIT 19";

		if ( ($rr = CSql::query($ss, 0,__FILE__,__LINE__)) )
		{
			while ( ($ln = CSql::fetch($rr)) )
				$aa[] = $ln;
			CSql::free($rr);
		}

		if ( count($aa) > 0 )
		{
			$s   =  "<div>";
			for ( $i = 0 ; $i < count($aa) ; $i++ )
			{
				$n = $aa[$i]['dvd_id'];
				$s .= "<a id='{$s_media_prefix}_{$n}' class='dvd_pic' href='javascript:void(0)'>".
						"<img id='zo_{$n}' src='".CPic::location($aa[$i]['pic_name'],CPic_THUMB)."' width='63' height='90' class='img_space' />".
					  "</a> ";
			}
			echo $s.  "<span style='position:relative; top:-11px; padding-left:20px; white-space:nowrap; color:#de4141'><a href='{$s_more}' class='more'>More</a>...</span>".
					"</div>";
		}
	}
}

?>
