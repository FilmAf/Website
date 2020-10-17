<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetFriends extends CWidget
{
	function draw(&$wnd)
	{
		switch ( dvdaf3_getvalue('act', DVDAF3_GET) )
		{
		case 'email':
			if ( ! $wnd->mwb_view_self ) return;
			CWidget::drawHeader("Find friends by email address", '', '');
			CWidgetFriends::findFriendsByEmail($wnd);
			break;
		case 'name':
			if ( ! $wnd->mwb_view_self ) return;
			CWidget::drawHeader("Find friends by user name", '', '');
			CWidgetFriends::findFriendsByName($wnd);
			break;
		case 'net':
			if ( ! $wnd->mwb_view_self ) return;
			CWidget::drawHeader("Invite your friends to join FilmAf&nbsp;&nbsp;&nbsp;:)", '', '');
			CWidgetFriends::inviteFriends();
			break;
		case 'edit':
			if ( ! $wnd->mwb_view_self ) return;
			CWidgetFriends::editFriends($wnd);
			break;
		default:
			CWidgetFriends::showFriends($wnd);
			break;
		}
	}

	// ======================================================================
	function findFriendsByEmail(&$wnd)
	{
		if ( count($_POST) > 0 )
		{
			$s_email  = dvdaf3_getvalue('email', DVDAF3_POST|DVDAF3_LOWER);
			$s_found  = '';
			$ss = "SELECT u.user_id, f.user_id my_friend, v.gender ".
					"FROM dvdaf_user_2 u ".
					"LEFT JOIN friend f ON u.user_id = f.friend_id and f.user_id = '{$wnd->ms_user_id}' ".
					"LEFT JOIN dvdaf_user_3 v ON u.user_id = v.user_id ".
				   "WHERE lower(u.email) = '{$s_email}'";
			if ( ($rr = CSql::query_and_fetch($ss,0,__FILE__,__LINE__)) )
			{
				$b_friend = $rr['my_friend'] != '';
				$s_found  = $rr['user_id'];
				$s_gender = $rr['gender'] == 'M' ? 'him' : ($rr['gender'] == 'F' ? 'her' : 'him/her');
			}

			if ( $s_found )
			{
				if ( $s_found == $wnd->ms_user_id )
				{
					CWidgetFriends::postInviteQuestion(
						"<div><span class='highkey'>{$s_email}</span> belongs to you :)</div>",
						false, false, '', '', 0);
				}
				else
				{
					$s_msg = "<span class='highkey'>".ucfirst($s_email)."</span> belongs to <span class='highkey'>".ucfirst($s_found)."</span>";
					if ( $b_friend )
						CWidgetFriends::postInviteQuestion(
							"<div>{$s_msg}, who is already in your network.</div>",
							true, false, $s_email, $s_found, 0);
					else
						CWidgetFriends::postInviteQuestion(
							"<div>{$s_msg}</div>".
							"<div style='padding-top:8px'>Would you like to invite {$s_gender} to your network?</div>",
							true, true, $s_email, $s_found, 0);
				}
			}
			else
			{
				CWidgetFriends::postInviteQuestion(
					"<div>Sorry, we did not find a member with an email address of <span class='highkey'>{$s_email}</span></div>",
//					"<div style='padding-top:8px'>Would you like to invite him/her to join FilmAf and your network?</div>",
					false, false, $s_email, $s_found, 0);
			}
		}
		echo  "<table id='dlg' class='dlg_table' style='width:480px;'>".
				"<tr>".
				  "<td class='dlg_left'>Email address<p>Please enter the email address you would like us to search for.</p></td>".
				  "<td class='dlg_right'><input type='text' autocomplete='off' size='36' value='' id='email' name='email' /></td>".
				"</tr>".
				"<tr>".
				  "<td class='dlg_resp' colspan='2'>".
					"<input type='button' onclick='location.href=\"/?tab=friends\"' value='Cancel' name='cancel' />".
					"<input type='submit' onclick='Friends.validateEmail()' value='Find' />".
				  "</td>".
				"</tr>".
			  "</table>";
    }

	function postInviteQuestion($s_msg, $b_in_filmaf, $b_invite, $s_email, $s_found, $i)
	{
		$s_str = $b_invite
					? "<div style='padding-top:18px'>".
						"<input type='button' id='friend_invite' n_email='{$s_email}' n_user='".($b_in_filmaf ? $s_found : 'NONE')."' ".
							"value='&nbsp;&nbsp;&nbsp;Invite ".($b_in_filmaf ? ucfirst($s_found) : $s_email)."&nbsp;&nbsp;&nbsp;' /> ".
					  "</div>"
					: '';
		echo  "<div id='invite_div'>".
				"<div style='text-align:center'>{$s_msg}{$s_str}</div>".
				"<div class='ruler' style='padding-top:36px'>&nbsp;</div>".
			  "</div>";
	}

	// ======================================================================
	function findFriendsByName(&$wnd)
	{
		if ( count($_POST) > 0 )
		{
			$s_user_id = dvdaf3_getvalue('name', DVDAF3_POST|DVDAF3_LOWER);
			$a_matches = array();
			$ss = "SELECT u.user_id, f.user_id my_friend, i.rejected_ind ".
					"FROM dvdaf_user u ".
					"LEFT JOIN friend f ON u.user_id = f.friend_id and f.user_id = '{$wnd->ms_user_id}' ".
					"LEFT JOIN friend_request i ON i.invited_id = u.user_id and i.invitee_id ='{$wnd->ms_user_id}' ".
				   "WHERE u.user_id = '{$s_user_id}' and u.user_id != '{$wnd->ms_user_id}'";
			if ( ($rr = CSql::query_and_fetch($ss,0,__FILE__,__LINE__)) )
			{
				$a_matches[] = $rr;
			}
			else
			{
				$ss = "SELECT u.user_id, f.user_id my_friend, i.rejected_ind ".
						"FROM dvdaf_user u ".
						"LEFT JOIN friend f ON u.user_id = f.friend_id and f.user_id = '{$wnd->ms_user_id}' ".
						"LEFT JOIN friend_request i ON i.invited_id = u.user_id and i.invitee_id ='{$wnd->ms_user_id}' ".
					   "WHERE u.user_id like '{$s_user_id}%' and u.user_id != '{$wnd->ms_user_id}'";
				if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
				{
					while ( $a_row = CSql::fetch($rr) )
						$a_matches[] = $a_row;
					CSql::free($rr);
				}
			}

			if ( count($a_matches) > 0 )
			{
				$s_str = "<table width='100%'>";
				for ( $i = 0 ; $i < count($a_matches) ; $i++ )
				{
					$b_friend  = $a_matches[$i]['my_friend'] != '';
					$b_invited = $a_matches[$i]['rejected_ind'] != '';
					$s_user_id = $a_matches[$i]['user_id'];
					if ( $i % 2 == 0 ) $s_str .= '<tr>';
					if ( $b_friend )
						$s_str .= "<td><input type='checkbox' style='visibility:hidden' />&nbsp;</td><td style='text-align:left'><span class='highkey'>{$s_user_id}</span> is already in your network.</td>";
					else
						if ( $b_invited )
							$s_str .= "<td><input type='checkbox' style='visibility:hidden' />&nbsp;</td><td style='text-align:left'><span class='highkey'>{$s_user_id}</span> already invited.</td>";
						else
							$s_str .= "<td><input type='checkbox' id='cbi_".str_replace('-','_',$s_user_id)."' /></td><td style='text-align:left'>{$s_user_id}</td>";
					if ( $i % 2 != 0 ) $s_str .= '</tr>';
				}
				if ( $i % 2 != 0 ) $s_str .= '<td>&nbsp;</td><td>&nbsp;</td></tr>';
				$s_str .= "</table>".
						  "<div style='text-align:left;padding:12px 0 0 36px'>".
							"<input type='button' id='friend_invite' onclick='alert(\"Please select someone first.\")' n_email='' n_user='' ".
								"value='&nbsp;&nbsp;&nbsp;Invite selected to your network&nbsp;&nbsp;&nbsp;' />".
							"<input type='hidden' id='friend_by_name' />".
						  "</div>";
			}
			else
			{
				$s_str = "Sorry, we did not find a member with a handle similar to {$s_user_id}";
			}
			echo  "<div id='invite_div'>".
					"<div style='text-align:center'>{$s_str}</div>".
					"<div class='ruler' style='padding-top:36px'>&nbsp;</div>".
				  "</div>";
		}
		echo  "<table id='dlg' class='dlg_table' style='width:480px;'>".
				"<tr>".
				  "<td class='dlg_left'>User name<p>Please enter name you would like us to search for.  A URL for the Film Collection works as well.</p></td>".
				  "<td class='dlg_right'><input type='text' autocomplete='off' size='36' value='' id='name' name='name' /></td>".
				"</tr>".
				"<tr>".
				  "<td class='dlg_resp' colspan='2'>".
					"<input type='button' onclick='location.href=\"/?tab=friends\"' value='Cancel' name='cancel' />".
					"<input type='submit' onclick='Friends.validateName()' value='Find' />".
				  "</td>".
				"</tr>".
			  "</table>";
    }

	// ======================================================================
	function inviteFriends()
	{
		echo  "<form>".
				"<table id='dlg' class='dlg_table' style='width:600px;'>".
				  "<tr>".
					"<td class='dlg_left' style='vertical-align:top'>Email address list<p>Please enter the email addresses of the folks you would like us to invite to join you at FilmAf.</p></td>".
					"<td class='dlg_right'><textarea style='width:320px;height:240px' wrap='soft' id='email' name='email'></textarea>".
				  "</tr>".
				  "<tr>".
					"<td class='dlg_left' style='vertical-align:top'>Personalize the invitation<p>Text of the email they will receive.</p></td>".
					"<td class='dlg_right'><textarea style='width:320px;height:120px' wrap='soft' id='msg' name='msg'></textarea>".
				  "</tr>".
				  "<tr>".
					"<td class='dlg_resp' colspan='2'>".
					  "<input type='button' onclick='location.href=\"/?tab=friends\"' value='No thanks, I changed my mind' name='cancel' />".
					  "<input type='submit' onclick='Friends.validateEdit()' value='Invite' />".
					"</td>".
				  "</tr>".
				"</table>".
			  "</form>";
    }

	// ======================================================================
	function editFriends(&$wnd)
	{
		$str    = '';
		$b_last = ! CWidgetFriends::drawEditFriends($str, $wnd);
		$n_tot  = CSql::query_and_fetch1("SELECT count(*) FROM friend f WHERE f.user_id = '{$wnd->ms_user_id}' and feed_only_ind = 'N'",0,__FILE__,__LINE__);
		$n_tot = $n_tot > 10 ? "({$n_tot})" : '';

		CWidget::drawHeader("Friends <span id='tot_friends'>{$n_tot}</span>",
//							"<a class='wga' href='/?tab=friends' title='Edit'>Delete Selected</a>&nbsp;&nbsp;&nbsp;".
							"<a class='wga' href='/?tab=friends' title='Edit'>Done</a>", '');

		echo  "<div class='wg_body'>".
				"<div id='tbl_friends'>".
				  $str.
				"</div>".
			  "</div>";
    }

    function drawEditFriends(&$str, &$wnd)
    {
		$n_cols  = 5;
		$n_width = '20%';

		$ss  = "SELECT f.friend_id, a.name, f.top_friend_ind, a.photo, a.gender ".
				 "FROM friend f ".
				 "LEFT JOIN dvdaf_user_3 a ON f.friend_id = a.user_id ".
				"WHERE f.user_id = '{$wnd->ms_user_id}' and feed_only_ind = 'N' ".
				"ORDER BY f.top_friend_ind DESC, f.seq_num, IF(a.name is null or a.name = '-', f.friend_id, LOWER(a.name))";
		$str = '&nbsp;';
		$i   = 1;

		if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		{
			$str =	"<table width='100%'>";
			while ( $a_row = CSql::fetch($rr) )
			{
				$w = ucfirst($a_row['friend_id']);
				$t = $a_row['top_friend_ind'] == 'Y' ? '*' : '';

				$str .= "<tr class='dvd_row". ($i % 2) ."'>".
						  "<td style='padding:8px 6px 8px 12px;text-align:center' width='1%'>".
							"<div>delete</div>".
							"<input type='checkbox' name='{$a_row['friend_id']}' onclick='javascript:void(Friends.divorce(\"{$a_row['friend_id']}\",\"{$w}\"));return false' /></td>".
						  "<td style='padding:8px 12px 8px 6px;text-align:center' width='1%'>".
							($a_row['photo'] == '-' || $a_row['photo'] == ''
								? "<img src='http://dv1.us/d1/m_64.png' />"
								: "<img onmouseover='ImgPop.show(this,0)' zoom_hoz='left' src='http://dv1.us{$a_row['photo']}_t.jpg' />").
						  "</td>".
						  "<td>".
							($a_row['name' ] == '-' || $a_row['name' ] == ''
								? "{$w}{$t}"
								: "{$a_row['name']}{$t}<br />({$w})").
						  "</td>".
						"</tr>";
				$i++;
			}
			CSql::free($rr);

			$str .=	"</table>";
		}
    }

	// ======================================================================
	function showFriends(&$wnd)
	{
		$str    = '';
		$b_last = ! CWidgetFriends::drawShowFriends($str, $wnd);
		$n_tot  = CSql::query_and_fetch1("SELECT count(*) FROM friend f WHERE f.user_id = '{$wnd->ms_view_id}' and feed_only_ind = 'N'",0,__FILE__,__LINE__);
		$n_tot = $n_tot > 10 ? "({$n_tot})" : '';

		CWidget::drawHeader("Friends <span id='tot_friends'>{$n_tot}</span>",
			($wnd->mwb_view_self ? "<a class='wga' href='/?tab=friends&act=edit' title='Edit'>Edit</a> " : '').
			(! $b_last			 ? CWidget::getNav('Friends.newer()','Friends.curr()','Friends.older()') : ''), '');

		echo  "<div class='wg_body'>".
				"<div id='tbl_friends'>".
				  $str.
				"</div>".
				"<input type='hidden' id='friends_page' value='1' />".
				"<input type='hidden' id='friends_last' value='" .($b_last ? '1' : '0')."' />".
			  "</div>";
    }

    function drawShowFriends(&$str, &$wnd)
    {
		$n_cols  = 5;
		$n_width = '20%';
		$n_limit = 100;

		$ss  = "SELECT f.friend_id, a.name, f.top_friend_ind, a.photo, a.gender ".
				 "FROM friend f ".
				 "LEFT JOIN dvdaf_user_3 a ON f.friend_id = a.user_id ".
				"WHERE f.user_id = '{$wnd->ms_view_id}' and feed_only_ind = 'N' ".
				"ORDER BY f.top_friend_ind DESC, f.seq_num, IF(a.name is null or a.name = '-', f.friend_id, LOWER(a.name)) ".
				"LIMIT ". ($n_limit + 1);
		$str = '&nbsp;';
		$i   = 0;
		$j   = 0;

		if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		{
			$str =	"<table width='100%'>".
					  "<tr>";
			while ( $a_row = CSql::fetch($rr) )
			{
				if ( $i < $n_limit )
				{
					$w = ucfirst($a_row['friend_id']);
					$t = $a_row['top_friend_ind'] == 'Y' ? '*' : '';

					if ( $i && ($i % $n_cols) == 0 )
						$str .= "</tr><tr>";

					$str .= "<td style='text-align:center;vertical-align:top;padding:10px 0 10px 0' width='{$n_width}'>".
							  "<a href='http://{$a_row['friend_id']}{$wnd->ms_unatrib_subdomain}'>".
								($a_row['photo'] == '-' || $a_row['photo'] == ''
									? "<img src='http://dv1.us/d1/m_64.png' />"
									: "<img onmouseover='ImgPop.show(this,0)' zoom_hoz='left' src='http://dv1.us{$a_row['photo']}_t.jpg' />").
								"<br />".
								($a_row['name' ] == '-' || $a_row['name' ] == ''
									? "{$w}{$t}"
									: "{$a_row['name']}{$t}<br />({$w})").
							  "</a>".
							"</td>";
					$i++;
				}
				$j++;
			}
			CSql::free($rr);

			if ( ($k = $i % $n_cols) )
				for (  ;  $k % $n_cols  ;  $k++ ) $str .= "<td>&nbsp;</td>";

			$str .=	  "</tr>".
					"</table>";
		}

		return $j > $i;
    }

	// ======================================================================
	function validateDataSubmission(&$wnd)
	{
		if ( $wnd->mwb_view_self )
		{
			switch ( dvdaf3_getvalue('act', DVDAF3_GET) )
			{
			case 'net':   CWidgetFriends::validateDataSubmissionNet($wnd->ms_user_id); break;
			case 'edit':  CWidgetFriends::validateDataSubmissionEdit($wnd->ms_user_id); break;
			}
		}
	}

	function validateDataSubmissionNet($s_user_id)
	{
//			$o_youtube_id	= dvdaf3_getvalue('o_youtube_id_'.$i, DVDAF3_POST);
	}

	function validateDataSubmissionEdit($s_user_id)
	{
//			$o_youtube_id	= dvdaf3_getvalue('o_youtube_id_'.$i, DVDAF3_POST);
	}

	// ======================================================================
    function drawInvites(&$wnd)
    {
		// Cross reference with mil-home.js where this cookie is written
		$ss = explode('|', dvdaf3_getvalue('home',DVDAF3_COOKIE));
		$b_show_rejected = count($ss) > 5 ? $ss[5] : false;

		$a_invitees = array();
		$ss = "SELECT r.invitee_id, r.invite, r.created_tm, r.rejected_ind, u.name ".
				"FROM friend_request r ".
				"LEFT JOIN dvdaf_user_3 u ON r.invitee_id = u.user_id ".
			   "WHERE r.invited_id = '{$wnd->ms_user_id}' ORDER BY r.created_tm DESC";
		$b_has_rejected = false;

		if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		{
			while ( $a_row = CSql::fetch($rr) )
			{
				$b_rejected = $a_row['rejected_ind'] == 'Y';
				if ( $a_row['name'] == '-'				) $a_row['name']       = '';
				if ( ! $b_rejected || $b_show_rejected	) $a_invitees[] = $a_row;
				if (   $b_rejected						) $b_has_rejected      = true;
			}
			CSql::free($rr);
		}

		if ( ($n_count = count($a_invitees)) )
		{
			echo  "<div id='invites'>".
					"<table width='100%'>".
					  "<thead>".
						"<tr>".
						  "<td colspan='4'>".
							"<div style='float:right;margin:0 2px 0 6px'>".
							  ( $b_show_rejected ? "<a href='javascript:void(FriendInvite.setShowDeclined(0))'>Hide previously rejected...</a>"
												 : "<a href='javascript:void(FriendInvite.setShowDeclined(1))'>Show previously rejected...</a>"
							  ).
							"</div>".
							($n_count ? ( $n_count > 1 ? "You have {$n_count} new people who want to be your friend"
													   : "You have one new person who wants to be your friend"
										)
									  : "You have no new friend invitations"
							).
						  "</td>".
						"</tr>";

			if ( $n_count > 0 )
			{
				echo	"<tr>".
						  "<td width='1%'>Invite from</td>".
						  "<td>Message to you</td>".
						  "<td width='1%' style='text-align:center'>Created on</td>".
						  "<td width='1%' style='text-align:center'>Your reponse</td>".
						"</tr>";
			}

			echo 	  "</thead>".
					  "<tbody>";

			for ( $i = 0 ; $i < $n_count ; $i++ )
			{
				$id = $a_invitees[$i]['invitee_id'];
				$uc = $a_invitees[$i]['name'] ? $a_invitees[$i]['name'].' ('.ucfirst($id).')' : ucfirst($id);
				echo	"<tr>".
						  "<td style='white-space:nowrap'><a href='http://{$id}{$wnd->ms_unatrib_subdomain}' target='invitee'>{$uc}</a></td>".
						  "<td>{$a_invitees[$i]['invite']}</td>".
						  "<td style='white-space:nowrap'>{$a_invitees[$i]['created_tm']}</td>".
						  "<td style='white-space:nowrap;text-align:center'>".
							"<input type='button' id='ba_{$i}' onclick='FriendInvite.accept({$i},\"{$id}\")' value='Accept' style='width:72px;margin:0 4px 0 8px' />".
							( $a_invitees[$i]['rejected_ind'] == 'Y'
							  ? "<span style='width:72px;margin:0 4px 0 8px;position:relative;top:-2px'>Declined</span>"
							  : "<input type='button' id='br_{$i}' onclick='FriendInvite.decline({$i},\"{$id}\")' value='Decline' style='width:72px;margin:0 4px 0 8px' />").
						  "</td>".
						"</tr>";
			}

			echo	  "</tbody>".
					"</table>".
				  "</div>";
		}
	}
}

?>
