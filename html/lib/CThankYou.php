<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndMenu.php';

class CThankYou extends CWndMenu
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();

		$this->ms_include_css .=
		"<style type='text/css'>".
			"#thanks { margin:0 0 24px 10px; }".
			"#thanks table { background:#ffffff; }".
			"#thanks td { color:#b2bbc4; padding:0 4px 0 16px; vertical-align:top; white-space:nowrap; }".
			"#thanks a { color:#3E719E; }".
			"#thanks .till { text-align:right; }".
			"#thanks .section { color:#005e0d; font-size:12px; padding:30px 0 12px 0; }".
		"</style>";
	}

	function drawBodyPage()
	{
		echo  "<h1>".
				"Friends of Film Aficionado".
				"<p>Film Aficionado depends heavily on contributions from members like you</p>".
				"<p>Please join this group of select people who make Film Aficionado possible</p>".
				"<p><a href='/utils/help-filmaf.html'>Benefits!</a></p>".
				"<p><a href='/utils/help-filmaf.html?step=1'>Make a Contribution Today</a></p>".
			  "</h1>";

		$s  = "<div id='thanks'>".
				"<table>".
				  "<tr>";
		$cd = 0;
		$k  = 0;

		if ( ($rr = CSql::query("SELECT u.user_id, u.membership_cd, u.moderator_cd, DATE_FORMAT(u.last_donation_tm,'%b-%Y') expire_month, d.descr membership ".
								  "FROM dvdaf_user u ".
								  "LEFT JOIN decodes d ON d.domain_type = 'membership_cd' and d.code_char = u.membership_cd ".
								 "WHERE (u.moderator_cd >= '5' || (u.membership_cd != '-' and u.last_donation_tm >= DATE_ADD(CONCAT(DATE_FORMAT(now(),'%Y-%m'),'-01'), INTERVAL -1 YEAR))) ".
								 "ORDER BY u.moderator_cd, u.membership_cd DESC, u.user_id", 0,__FILE__,__LINE__)) )
		{
			for ( $i = 0 ; ($ln = CSql::fetch($rr)) ; $i++ )
			{
				$title = '';
				if ( $ln['membership_cd'] != $cd )
				{
					if ( $k != 0 )
					{
						for (  ; $k < 3 ; $k++ )
							$s .= "<td>&nbsp;</td><td>&nbsp;</td>";
						$s .= "</tr><tr>";
						$k  = 0;
					}

					$cd = $ln['membership_cd'];
					if ( $ln['moderator_cd'] > 0 )
					{
						$ln['membership_cd'] = 5;
						$ln['membership']    = 'Moderator';
					}

					$s .= "<td class='section' colspan='6'><img src='".CDvdUtils::getStar($ln['membership_cd'])."' style='position:relative;top:3px' /> {$ln['membership']}</td></tr><tr>";
				}

				switch ( $ln['user_id'] )
				{
					case 'ashirg': $title = " (mod extraordinaire)"; break;
				}

				$s .= "<td><a href='http://{$ln['user_id']}{$this->ms_unatrib_subdomain}/'>{$ln['user_id']}</a>{$title}</td>".
					  "<td class='till'>".( $ln['membership_cd'] == 5 ? '&nbsp;' : $ln['expire_month'])."</td>";

				if ( ++$k == 3 )
				{
					$s .= "</tr><tr>";
					$k  = 0;
				}
			}
			CSql::free($rr);
		}

		if ( $k != 0 )
		{
			for (  ; $k < 3 ; $k++ )
				$s .= "<td>&nbsp;</td><td>&nbsp;</td>";
			$s .= "</tr><tr>";
		}

		echo	  substr($s,0,-4).
				"</table>".
			  "</div>";
	}
}

?>
