<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetProfile extends CWidget
{
	function draw(&$wnd)
	{
		switch ( dvdaf3_getvalue('act', DVDAF3_GET) )
		{
		case 'edit':
			if ( ! $wnd->mwb_view_self ) return;
			CWidgetProfile::editProfile($wnd);
			break;
		default:
			CWidgetProfile::showProfile($wnd);
			break;
		}
	}

	function showProfile(&$wnd)
	{
		$a =& $wnd->ma_profile;

		if ( $wnd->mwb_view_self )
			$s = "<a class='wga' href='javascript:void(0)' id='edit_profile'>Edit</a>";
		else
			if ( $a['is_friend'] )
				$s = "{$wnd->mws_ucview_id} is in your network";
			else
				if ( $a['is_requested'] )
					$s = "{$wnd->mws_ucview_id} has not responded to your friend invitation yet";
				else
					if ( $wnd->ms_user_id == 'guest' )
						$s = false;
					else
						$s = "<a class='wga' href='javascript:void(0)' id='friend_invite' n_user='{$wnd->ms_view_id}'>Invite {$wnd->mws_ucview_id} to your network of friends</a>";

		CWidget::drawHeader('Profile', $s, '');

		$s_photo		= $a['photo'];
		$s_name			= $a['name'];
		$s_status		= $a['status'];
		$s_gender		= $a['gender'];
		$s_age			= $a['age'];
		$s_city			= $a['city'];
		$s_state		= $a['state'];
		$s_country		= $a['country'];
		$s_member_since	= $a['member_since_dt'];
		$s_last_visit	= $a['last_visit_tm'];
		$s_user_id		= $a['user_id'];
		$s_my_space		= $a['my_space_id'];
		$s_facebook		= $a['facebook_id'];
		$s_homepage		= $a['homepage'];
		$s_youtube		= $a['youtube_id'];
		$s_about_me		= '';

		if ( $a['about_me'] )
		{
			$s_about_me = $wnd->mws_ucview_id;
			if ( $a['name'] && $a['name'] != '-' )
				$s_about_me = "{$a['name']} ({$wnd->mws_ucview_id})";

			$s_about_me	= "<div class='wg_time' style='margin:0 0 4px 0'>".
							"<div class='wg_sepa'>&nbsp;</div>".
							"<div style='float:right;margin:0 2px 0 6px'>{$a['updated_tm']}</div>".
							"<div>{$s_about_me} says:</div>".
						  "</div>".
						  $a['about_me'];
		}

		$n_stars		= max(intval($a['membership_cd']), intval($a['contributor_cd']));
		$n_stars		= ($n_stars >  9 ? 0 : ($n_stars >= 5 ? $n_stars : ($n_stars >= 2 ? $n_stars + 1 : ($n_stars == 1 ? 1 : 0))));
		$s_stars		= ($a['moderator_cd'] >= 5 ? "<span style='text-decoration:underline'>Film Aficionado moderator</span> and a " : '').
						  ($n_stars						   ? "<a class='wga' href='{$wnd->ms_base_subdomain}/utils/help-filmaf.html'>".dvdaf3_stardescription($n_stars)."</a> and a " : '');
		$s_stars		= substr($s_stars, 0, -7);
		$s_stars		= $s_stars ? "{$wnd->mws_ucview_id} helps Film Aficionado stay alive by being a {$s_stars}" : '';
		$s_photo		= $s_photo ? "<img src='http://dv1.us{$s_photo}_p.jpg' />".($wnd->mwb_view_self ? "<br /><a class='wga' href='javascript:void(0)' id='edit_photo'>Replace Pic</a>" : '')
								   : "<img src='http://dv1.us/d1/md100.png' />".   ($wnd->mwb_view_self ? "<br /><a class='wga' href='javascript:void(0)' id='edit_photo'>Upload Pic</a>" : '');
		$s_status		= substr(($s_status ? "{$s_status}, " : '').($s_gender ? "{$s_gender}, " : ''), 0, -2);
		$s_age			= $s_age > 0 ? ("{$s_age} year". ($s_age == 1 ? '' : 's'). " old") : '';
		$s_location		= substr(($s_city ? "{$s_city}, " : '').($s_state ? "{$s_state}, " : ''), 0, -2);
		$s_member_since	= $s_member_since ? "Member since: {$s_member_since}" : '';
		$s_last_visit	= $s_last_visit   ? "Last visit: {$s_last_visit}"	  : '';
		$s_filmaf		= "http://{$s_user_id}{$wnd->ms_unatrib_subdomain}/";
		$s_links		= substr(($s_facebook ? "{$s_facebook}, " : '').($s_my_space ? "{$s_my_space}, " : '').($s_homepage ? "{$s_homepage}, " : ''), 0, -2);

		echo  "<div class='wg_body'>".
				"<table width='100%'>".
				  "<tr>".
					"<td>".
					  "<div>".
						"<div id='profile_1' style='text-align:center;float:left;margin:0 10px 0 2px'>{$s_photo}</div>".
						"<div id='profile_2' style='display:table;height:1%'>".
						  ($s_name			? "<div>{$s_name}</div>"		: '').
						  ($s_status		? "<div>{$s_status}</div>"		: '').
						  ($s_age			? "<div>{$s_age}</div>"			: '').
						  ($s_location		? "<div>{$s_location}</div>"	: '').
						  ($s_country		? "<div>{$s_country}</div>"		: '').
						  ($s_last_visit	? "<div>{$s_last_visit}</div>"	: '').
						  ($s_links			? "<div>{$s_links}</div>"		: '').
						  "<div style='white-space:nowrap;margin-top:10px'>{$s_filmaf}</div>".
						"</div>".
					  "</div>".
					"</td>".
				  "</tr>".
				  "<tr>".
					"<td>".
					  "<div id='profile_3' style='padding:0 0 12px 0'>".
						($s_about_me	? "<div style='padding-top:8px'>{$s_about_me}</div>"  : '').
						($s_youtube		? "<div style='text-align:center;padding-top:8px'>{$s_youtube}</div>" : '').
					  "</div>".
					  "<div class='wg_sepa'>&nbsp;</div>".
					  "<div>".
						($s_member_since	 ? "<div>{$s_member_since}</div>"				: '').
						($s_stars			 ? "<div>{$s_stars}</div>"						: '').
					  "</div>".
					"</td>".
				  "</tr>".
				"</table>".
			  "</div>";
	}

	function getFullProfile(&$wnd)
	{
		$a =& $wnd->ma_profile;
		$a  = CSql::query_and_fetch("SELECT a.user_id, a.membership_cd, a.moderator_cd, a.contributor_cd, ".
										   "date_format(a.dvdaf_user_created_tm,'%Y-%m-%d') member_since_dt, a.last_visit_tm, ".
										   "b.name, b.photo, b.gender, b.city, b.state, b.country, b.status, b.my_space_id, ".
										   "b.facebook_id, b.youtube_id, b.youtube_auto_ind, b.blog, b.homepage, ".
										   "b.about_me, b.updated_tm, date_format(b.dob,'%Y-%m-%d') dob, ".
										   "year(current_date()) - year(b.dob) - (right(current_date(),5) < right(date(b.dob),5)) age, ".
										   "c.user_id is_friend, d.invited_id is_requested, microblog_reply_ind ".
									  "FROM dvdaf_user a ".
									  "LEFT JOIN dvdaf_user_3 b ON a.user_id = b.user_id ".
									  "LEFT JOIN friend c ON a.user_id = c.user_id and c.friend_id = '{$wnd->ms_user_id}' ".
									  "LEFT JOIN friend_request d ON a.user_id = d.invited_id and d.invitee_id = '{$wnd->ms_user_id}' ".
									 "WHERE a.user_id = '{$wnd->ms_view_id}'",0,__FILE__,__LINE__);

		if ( !isset($a['user_id'			])								 ) $a['user_id']			= '';
		if ( !isset($a['membership_cd'		])								 ) $a['membership_cd']		= '';
		if ( !isset($a['moderator_cd'		])								 ) $a['moderator_cd']		= '';
		if ( !isset($a['contributor_cd'		])								 ) $a['contributor_cd']		= '';
		if ( !isset($a['member_since_dt'	])								 ) $a['member_since_dt']	= '';
		if ( !isset($a['last_visit_tm'		])								 ) $a['last_visit_tm']		= '';
		if ( !isset($a['name'				]) || $a['name'			] == '-' ) $a['name']				= '';
		if ( !isset($a['photo'				]) || $a['photo'		] == '-' ) $a['photo']				= '';
		if ( !isset($a['gender'				]) || $a['gender'		] == '-' ) $a['gender']				= '';
		if ( !isset($a['city'				]) || $a['city'			] == '-' ) $a['city']				= '';
		if ( !isset($a['state'				]) || $a['state'		] == '-' ) $a['state']				= '';
		if ( !isset($a['country'			]) || $a['country'		] == '-' ) $a['country']			= '';
		if ( !isset($a['status'				]) || $a['status'		] == '-' ) $a['status']				= '';
		if ( !isset($a['my_space_id'		]) || $a['my_space_id'	] == '-' ) $a['my_space_id']		= '';
		if ( !isset($a['facebook_id'		]) || $a['facebook_id'	] == '-' ) $a['facebook_id']		= '';
		if ( !isset($a['youtube_id'			]) || $a['youtube_id'	] == '-' ) $a['youtube_id']			= '';
		if ( !isset($a['youtube_auto_ind'	])								 ) $a['youtube_auto_ind']	= 'Y';
		if ( !isset($a['homepage'			]) || $a['homepage'		] == '-' ) $a['homepage']			= '';
		if ( !isset($a['blog'				]) || $a['blog'			] == '-' ) $a['blog']				= '';
		if ( !isset($a['about_me'			]) || $a['about_me'		] == '-' ) $a['about_me']			= '';
		if ( !isset($a['updated_tm'			])								 ) $a['updated_tm']			= '';
		if ( !isset($a['dob'				])								 ) $a['dob']				= '';
		if ( !isset($a['age'				])								 ) $a['age']				= 0;
		if ( !isset($a['is_friend'			])								 ) $a['is_friend']			= '';
		if ( !isset($a['is_requested'		])								 ) $a['is_requested']		= '';
		if ( !isset($a['microblog_reply_ind'])								 ) $a['microblog_reply_ind']= 'N';

		$a['gender']	   = $a['gender'] == 'M' ? 'Male'   : ($a['gender'] == 'F' ? 'Female'  : '');
		$a['status']	   = $a['status'] == 'S' ? 'Single' : ($a['status'] == 'M' ? 'Married' : ($a['status'] == 'R' ? 'In a relationship' : ''));
		$a['my_space_id']  = $a['my_space_id']   ? "<a class='wga' href='http://profile.myspace.com/index.cfm?fuseaction=user.viewprofile&friendid={$a['my_space_id']}' target='myspace'>MySpace</a>" : '';
		$a['facebook_id']  = $a['facebook_id']	 ? "<a class='wga' href='http://www.facebook.com/profile.php?id={$a['facebook_id']}' target='facebook'>Facebook</a>" : ''; 
		$a['homepage']	   = $a['homepage']		 ? "<a class='wga' href='{$a['homepage']}' target='homepage'>Homepage</a>" : '';
		$a['is_friend']	   = $a['is_friend']	 ? true : false;
		$a['is_requested'] = $a['is_requested']	 ? true : false;

		if ( $a['youtube_id'] )
		{
			if ( strpos($a['youtube_id'],'|') )
			{
				$a = explode('|', $a['youtube_id']);
				$a['youtube_id'] = $a[0];
			}
			$a['youtube_id'] = CWidget::embedYouTube($a['youtube_id'], false, $a['youtube_auto_ind'] == 'Y', $wnd->mwb_browser_is_ie);
		}

		$wnd->mb_is_friend = $a['is_friend'];
		
		// http://profile.myspace.com/index.cfm?fuseaction=user.viewprofile&friendid=378257897
		// http://www.facebook.com/profile.php?id=547739148
	}

	function getPatialProfile(&$wnd)
	{
		$a =& $wnd->ma_profile;
		$a  = CSql::query_and_fetch("SELECT a.user_id, b.blog ".
									  "FROM dvdaf_user a ".
									  "LEFT JOIN dvdaf_user_3 b ON a.user_id = b.user_id ".
									 "WHERE a.user_id = '{$wnd->ms_view_id}'",0,__FILE__,__LINE__);

		if ( !isset($a['user_id'])						) $a['user_id'] = '';
		if ( !isset($a['blog'	]) || $a['blog'] == '-' ) $a['blog']	= '';
	}
}

?>
