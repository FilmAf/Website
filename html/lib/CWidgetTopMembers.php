<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetTopMembers extends CWidget
{
	function draw(&$wnd, $s_type)
	{
		$s_post  = '';

		switch ( $s_type )
		{
		case 'network':
			$s_title = 'Largest Member Networks';
//			$s_post  = '/?tab=friends';
			$ss		 = "SELECT b.user_id val, count(*) cnt ".
						 "FROM (SELECT user_id ".
								 "FROM dvdaf_user_2 ".
								"WHERE last_post_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
								   "or last_link_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
								   "or last_coll_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
								   "or last_submit_tm >= DATE_ADD(now(), INTERVAL -3 MONTH)) a, ".
							  "friend b ".
						"WHERE a.user_id = b.user_id ".
						"GROUP BY val ".
						"ORDER BY count(*) DESC ".
						"LIMIT 12";
			break;
		case 'microblog':
			$s_title = 'Most Active Film Microblogs';
			$ss		 = "SELECT b.user_id val, count(*) cnt ".
						 "FROM (SELECT user_id ".
								 "FROM dvdaf_user_2 ".
								"WHERE last_post_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
								   "or last_link_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
								   "or last_coll_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
								   "or last_submit_tm >= DATE_ADD(now(), INTERVAL -3 MONTH)) a, ".
							  "microblog b ".
						"WHERE a.user_id = b.user_id ".
						  "and b.location = 'B' ".
						  "and created_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
						"GROUP BY val ".
						"ORDER BY count(*) DESC ".
						"LIMIT 12";
			break;
		case 'recent':
			$s_title = 'Recently Joined';
			$ss		 = "SELECT user_id val, date_format(dvdaf_user_created_tm,'%Y-%m-%d') cnt ".
						 "FROM dvdaf_user ".
						"WHERE dvdaf_user_created_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
						"GROUP BY user_id ".
						"ORDER BY dvdaf_user_created_tm DESC ".
						"LIMIT 12";
			break;
		case 'contrib-30days':
			$s_title = 'Top Content Contributors (last 30 days)';
			$ss		 = "SELECT b.proposer_id val, sum(b.submissions) cnt ".
						 "FROM (SELECT proposer_id, count(*) submissions ".
								 "FROM dvd_submit ".
								"WHERE proposed_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
								  "and disposition_cd in ('A','P') ".
								"GROUP BY proposer_id ".
								"UNION ".
							   "SELECT proposer_id, count(*) submissions ".
								 "FROM pic_submit ".
								"WHERE proposed_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
								  "and disposition_cd in ('A','P') ".
								"GROUP BY proposer_id) b, ".
							  "dvdaf_user a ".
						"WHERE a.user_id = b.proposer_id ".
						  "and a.moderator_cd = '-' ".
						"GROUP BY proposer_id ".
						"ORDER BY sum(b.submissions) DESC ".
						"LIMIT 12";
			break;
		case 'contrib-year':
			$s_title = 'Top Content Contributors (this year)';
			$ss		 = "SELECT b.proposer_id val, sum(b.submissions) cnt ".
						 "FROM (SELECT proposer_id, count(*) submissions ".
								 "FROM dvd_submit ".
								"WHERE proposed_tm >= MAKEDATE(YEAR(now()),1) ".
								  "and disposition_cd in ('A','P') ".
								"GROUP BY proposer_id ".
								"UNION ".
							   "SELECT proposer_id, count(*) submissions ".
								 "FROM pic_submit ".
								"WHERE proposed_tm >= MAKEDATE(YEAR(now()),1) ".
								  "and disposition_cd in ('A','P') ".
								"GROUP BY proposer_id) b, ".
							  "dvdaf_user a ".
						"WHERE a.user_id = b.proposer_id ".
						  "and a.moderator_cd = '-' ".
						"GROUP BY proposer_id ".
						"ORDER BY sum(b.submissions) DESC ".
						"LIMIT 36";
			break;
		}

		echo  "<h2><span style='white-space:nowrap'>{$s_title}</span></h2>".
			  "<img src='http://dv1.us/d1/1.gif' width='270' height='1' />";

		CWidgetTopMembers::drawStats($ss, $s_post);
    }

	function drawStats($ss, $s_post)
	{
		if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		{
			$s  = "<table class='stat_tbl'>";
			for ( $i = 1 ; ($a_row = CSql::fetch($rr)) ; $i++ )
				$s .= "<tr>".
						"<td class='stat_rank'>{$i}</td>".
						"<td><a href='http://{$a_row['val']}{$this->ms_unatrib_subdomain}{$s_post}'>".ucfirst($a_row['val'])."</a> <span>({$a_row['cnt']})</span></td>".
					  "</tr>";
			CSql::free($rr);
			echo $s . "</table>";
		}
	}
}

?>
