<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';
require $gs_root.'/lib/CSecure.php';

define('CFriend_STATUS'	,     0);
define('CFriend_INVITE'	,     1);
define('CFriend_LIST'	,     2);
define('CFriend_LISTALL',     3);

class CFriend extends CAjax
{
	// ?what=invite&user=abc
	function isHuman()
	{
		$n_int   = dvdaf3_getvalue('int',DVDAF3_POST|DVDAF3_LOWER);
		$n_ext   = dvdaf3_getvalue('ext',DVDAF3_POST|DVDAF3_LOWER);
		$b_human = CSecure::validateJpg($n_ext, $n_int);
		$ss		 = "DELETE FROM human_check WHERE external_id = {$n_ext}";
//		CSql::log_debug(__CLASS__.'::'.__METHOD__.' '.$ss);
		CSql::query_and_free($ss,0,__FILE__,__LINE__);
		return $b_human;
	}

	function getSql()
	{
		$this->get_requester();

		$this->ms_what			= dvdaf3_getvalue('what'    ,DVDAF3_GET|DVDAF3_LOWER);
		$this->mn_page			= dvdaf3_getvalue('page'    ,DVDAF3_GET|DVDAF3_INT  );
		$this->ms_user			= dvdaf3_getvalue('user'    ,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_email			= dvdaf3_getvalue('email'   ,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_rejected		= dvdaf3_getvalue('rejected',DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_context		= ( $this->ms_what     != '' ? "what='{$this->ms_what}' "         : '').
								  ( $this->mn_page		     ? "page='{$this->mn_page}' "         : '').
								  ( $this->ms_user     != '' ? "user='{$this->ms_user}' "         : '').
								  ( $this->ms_rejected != '' ? "rejected='{$this->ms_rejected}' " : '');
		$this->mb_self			= $this->ms_user && $this->ms_user == $this->ms_requester;
		$this->mb_assoc			= true;
		$this->mn_result_kind	= CFriend_STATUS;
		$this->mb_show_rejected	= $this->ms_rejected ? true : false;
		$this->mb_has_rejected	= false;
		$this->mn_shown			= 0;
		$this->mn_friends		= -1;

		if ( $this->mn_page <= 0 )
			$this->mn_page = 1;

		switch ( $this->ms_what )
		{
		case 'invite':
			$s_version	= $this->valstr(dvdaf3_getvalue('version',DVDAF3_POST|DVDAF3_LOWER, 3),'-'); // need to validate
			$s_invite	= $this->valstr(dvdaf3_getvalue('invite' ,DVDAF3_POST            ,600),'-');
			if ( $s_version != 'v1' ) return $this->on_error("Unrecognized request format: ".__LINE__);

			if ( $this->isHuman() )
			{
				$s_invited_id = $this->ms_user == 'NONE' ? $this->ms_email : $this->ms_user;
				$ss = "INSERT INTO friend_request (invited_id, invitee_id, invite, created_tm) ".
					  "SELECT '{$s_invited_id}', '$this->ms_requester', '{$s_invite}', now() ".
						"FROM one ".
					   "WHERE not exists (SELECT 1 FROM friend_request WHERE invited_id = '{$s_invited_id}' and invitee_id = '$this->ms_requester') ".
						 "and not exists (SELECT 1 FROM friend_request WHERE invitee_id = '{$s_invited_id}' and invited_id = '$this->ms_requester')";
				if ( CSql::query_and_free($ss,0,__FILE__,__LINE__) == 1 )
				{
					$this->sendInvite($this->ms_requester, $this->ms_user, $this->ms_email, $s_invite, false);
					$this->ms_sql = "SELECT 'sent' status, '{$s_invited_id}' invited";
				}
				else
				{
					if ( ($n_days = CSql::query_and_fetch1("SELECT datediff(now(),last_sent_dt)+1 FROM friend_request WHERE invited_id = '{$s_invited_id}' and invitee_id = '$this->ms_requester'",0,__FILE__,__LINE__)) )
					{
						if ( $n_days - 1 < 7 )
						{
							$this->ms_sql = "SELECT 'already sent' status, 'aa{$s_invited_id}aa' invited";
						}
						else
						{
							$this->sendInvite($this->ms_requester, $this->ms_user, $this->ms_email, $s_invite, true);
							$this->ms_sql = "SELECT 'sent again' status, '{$s_invited_id}' invited";
						}
					}
					else
					{
						if ( CSql::query_and_fetch1("SELECT 1 FROM friend_request WHERE invitee_id = '{$s_invited_id}' and invited_id = '$this->ms_requester'",0,__FILE__,__LINE__) )
						{
							$this->acceptInvite($this->ms_requester, $this->ms_user);
							$this->ms_sql = "SELECT 'accepted mutual' status, '{$s_invited_id}' invited";
						}
						else
						{
							return $this->on_error("Unrecognized error: ".__LINE__);
						}
					}
				}
			}
			else
			{
				$this->ms_sql = "SELECT 'not human' status, '{$s_invited_id}' invited";
			}
			break;
		case 'accept':
			$this->acceptInvite($this->ms_requester, $this->ms_user);
			$this->ms_sql = "SELECT 'sucess' status";
			break;
		case 'reject':
			$this->rejectInvite($this->ms_requester, $this->ms_user);
			$this->ms_sql = "SELECT 'sucess' status";
			break;
		case 'divorce':
			$this->divorce($this->ms_requester, $this->ms_user);
			$this->ms_sql = "SELECT 'sucess' status";
			break;
		case 'showinvites':
			$this->mn_max	  = 500;
			$this->mn_result_kind = CFriend_INVITE;
			$this->ms_sql	  = "SELECT r.invitee_id, r.invite, r.created_tm, r.rejected_ind, a.name ".
								  "FROM friend_request r ".
								  "LEFT JOIN dvdaf_user_3 a ON r.invitee_id = a.user_id ".
								 "WHERE r.invited_id = '{$this->ms_requester}' ".
								 "ORDER BY r.created_tm DESC";
			break;
		case 'list':
			$this->mn_max	  = 100;
			$this->mn_result_kind = CFriend_LIST;
			$this->mn_friends	  = CSql::query_and_fetch1("SELECT count(*) FROM friend f WHERE f.user_id = '{$this->ms_user}' and feed_only_ind = 'N'",0,__FILE__,__LINE__);
			$n_min		  = ($this->mn_page - 1) * $this->mn_max;
			$n_max		  = $n_min + $this->mn_max + 1;
			$this->ms_sql	  = "SELECT f.friend_id, a.name, f.top_friend_ind, a.photo, a.gender ".
								  "FROM friend f ".
								  "LEFT JOIN dvdaf_user_3 a ON f.friend_id = a.user_id ".
								 "WHERE f.user_id = '{$this->ms_user}' and feed_only_ind = 'N' ".
								 "ORDER BY f.top_friend_ind DESC, f.seq_num, IF(a.name is null or a.name = '-', f.friend_id, LOWER(a.name)) ".
								 "LIMIT " . ($n_min ? "{$n_min}, " : '') . "{$n_max}";
			break;
		case 'listall':
			$this->mn_max	  = 500;
			$this->mn_result_kind = CFriend_LISTALL;
			$this->ms_sql	  = "SELECT f.friend_id, a.name, f.top_friend_ind, a.photo, a.gender ".
								  "FROM friend f ".
								  "LEFT JOIN dvdaf_user_3 a ON f.friend_id = a.user_id ".
								 "WHERE f.user_id = '{$this->ms_requester}' and feed_only_ind = 'N' ".
								 "ORDER BY f.top_friend_ind DESC, f.seq_num, IF(a.name is null or a.name = '-', f.friend_id, LOWER(a.name))";
			break;
		default:
			return $this->on_error("Unrecognized request: ".__LINE__);
		}

		return true;
	}

	function formatLine(&$row)
	{
		switch ( $this->mn_result_kind )
		{
		case CFriend_STATUS:
			$str = '';
			if ( isset($row['status' ]) ) $str .= "status\t{$row['status']}\t";
			if ( isset($row['invited']) ) $str .= "invited\t{$row['invited']}\t";
			return substr($str,0,-1) . "\n";

		case CFriend_INVITE:
			if ( ($b_rejected = $row['rejected_ind'] == 'Y') )
			$this->mb_has_rejected = true;
			if ( $row['name'     ] == '-' ) $row['name'     ] = '';

			if ( ! $b_rejected || $this->mb_show_rejected )
			{
				$this->mn_shown++;
				return    "invitee_id\t"	.$row['invitee_id'		   ].
						"\tname\t"			.$row['name'			   ].
						"\tuc\t"			.ucfirst($row['invitee_id']).
						"\tinvite\t"		.$row['invite'			   ].
						"\tcreated_tm\t"	.$row['created_tm'		   ].
						"\trejected_ind\t"	.$row['rejected_ind'	   ].
						"\n";
			}
			return '';

		case CFriend_LIST:
		case CFriend_LISTALL:
			if ( $row['friend_id'] == '-' ) $row['friend_id'] = '';
			if ( $row['name'     ] == '-' ) $row['name'     ] = '';
			if ( $row['photo'    ] == '-' ) $row['photo'    ] = '';

			return    "friend_id\t"		.$row['friend_id'     ].
					"\tname\t"			.$row['name'	      ].
					"\ttop_friend_ind\t".$row['top_friend_ind'].
					"\tphoto\t"			.$row['photo'	      ].
					"\tgender\t"		.$row['gender'	      ].
					"\n";
		}
	}

	function done()
	{
		switch ( $this->mn_result_kind )
		{
		case CFriend_INVITE:
			$this->ms_ajax =   "count\t"		   .$this->mn_shown.
							 "\thas_rejected\t"	   .($this->mb_has_rejected  ? 1 : 0).
							 "\tshowing_rejected\t".($this->mb_show_rejected ? 1 : 0).
							 "\n".
							 $this->ms_ajax;
			break;

		case CFriend_LIST:
			$this->ms_ajax =   "page\t"	.$this->mn_page.
							 "\tlast\t"	.($this->mb_over_max ? '0' : '1').
							 "\ttotal\t".$this->mn_friends.
							 "\n".
					 $this->ms_ajax;
			break;
		}
	}

	function acceptInvite($s_accepting, $s_accepted)
	{
		CSql::query_and_free("INSERT INTO friend (user_id, friend_id, created_tm) VALUES ('{$s_accepting}', '{$s_accepted}', now())",0,__FILE__,__LINE__);
		CSql::query_and_free("INSERT INTO friend (friend_id, user_id, created_tm) VALUES ('{$s_accepting}', '{$s_accepted}', now())",0,__FILE__,__LINE__);
		CSql::query_and_free("DELETE FROM friend_request WHERE (invitee_id = '{$s_accepting}' and invited_id = '{$s_accepted}') or (invited_id = '{$s_accepting}' and invitee_id = '{$s_accepted}')",0,__FILE__,__LINE__);
		CEmail::notifyAccepted($s_accepting, $s_accepted);
	}

	function rejectInvite($s_rejecting, $s_rejected)
	{
		CSql::query_and_free("UPDATE friend_request SET rejected_ind = 'Y' WHERE invited_id = '{$s_rejecting}' and invitee_id = '{$s_rejected}'",0,__FILE__,__LINE__);
		CEmail::notifyRejected($s_rejecting, $s_rejected);
	}

	function divorce($s_rejecting, $s_rejected)
	{
		CSql::query_and_free("DELETE FROM friend WHERE (user_id = '{$s_rejecting}' and friend_id = '{$s_rejected}') or (friend_id = '{$s_rejecting}' and user_id = '{$s_rejected}')",0,__FILE__,__LINE__);
		CEmail::notifyDivorce($s_rejecting, $s_rejected);
	}

	function sendInvite($s_invitee, $s_invited, $s_email, $s_invitation, $b_repeat)
	{
		if ( $s_invited == 'NONE' )
		{
//			CSql::query_and_free("UPDATE friend_request SET last_sent_dt = now() WHERE invitee_id = '{$s_invitee}' and invited_id = '{$s_email}'",0,__FILE__,__LINE__);
//			CEmail::notifyInviteEmail($s_invitee, $s_email, $s_invitation, $b_repeat);
		}
		else
		{
			CSql::query_and_free("UPDATE friend_request SET last_sent_dt = now() WHERE invitee_id = '{$s_invitee}' and invited_id = '{$s_invited}'",0,__FILE__,__LINE__);
			CEmail::notifyInvite($s_invitee, $s_invited, $s_invitation, $b_repeat);
		}
	}
}

$a = new CFriend();
$a->main();

?>
