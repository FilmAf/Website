<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

define('MSG_NOT_LOGGED_IN'		,     1);
define('MSG_OBJ_TYPE'			,     2);
define('MSG_DVD_ID_NOT_FOUND'	,     3);
define('MSG_EDIT_ID_NOT_FOUND'	,     4);
define('MSG_MOD_ONLY'			,     5);
define('MSG_USER_BLOCKED'		,     6);

require $gs_root.'/lib/rights-dvd.inc.php';
require $gs_root.'/lib/CPicUtils.php';
require $gs_root.'/lib/edit-pic.inc.php';
require $gs_root.'/lib/CWnd.php';

class CPicMngt extends CWnd
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-pic-mngt_{$this->mn_lib_version}.js'></script>\n";
		$this->ms_title				= 'Picture Management';
		$this->ms_request_uri		= dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER|DVDAF3_NO_AMP_EXPANSION);
		$this->mb_include_menu		= true;
		$this->mb_menu_context		= true;
		$this->mb_get_user_status	= true;

		$this->mn_edit				= 0;
		$this->mn_new				= 0;
		$this->mn_pic				= 0;
		$this->mn_used_edit			= 0;
		$this->mn_used_new			= 0;
		$this->mn_used_pic			= 0;

		$this->mn_obj_type			= dvdaf3_getvalue('obj_type',DVDAF3_GET|DVDAF3_LOWER);
		$this->mn_obj_id			= dvdaf3_getvalue('obj'     ,DVDAF3_GET|DVDAF3_INT  );
		$this->mn_obj_edit_id		= dvdaf3_getvalue('obj_edit',DVDAF3_GET|DVDAF3_INT  );
		$this->mn_pic_width			= 0;
		$this->mn_pic_height		= 0;
		// Find dvd_id for this edit_id
		// if dvd_id it exists show current pics for dvd_id and all pending pics by this user for that dvd_id
		// if edit_id show all pending pics by this user for that edit_id
	}

	function tellUser($n_line, $n_what)
	{
		switch ( $n_what )
		{
		case MSG_NOT_LOGGED_IN:		$this->ms_display_error    = "Sorry, we can not honor your request because you are not logged in."; break;
		case MSG_OBJ_TYPE:			$this->ms_display_affected = "No known object type specified."; break;
		case MSG_DVD_ID_NOT_FOUND:	$this->ms_display_affected = "DVD {$this->mn_obj_id} id not found."; break;
		case MSG_EDIT_ID_NOT_FOUND:	$this->ms_display_affected = "Audit id {$this->mn_obj_edit_id} by {$this->ms_user_id} not found."; break;
		case MSG_MOD_ONLY:			$this->ms_display_error    = 'This function is currently only available to moderators. Please check back in a couple of days.'; break;
		case MSG_USER_BLOCKED:		$this->ms_display_error    = "Sorry, we encountered an error. We appologize for any inconveniece, please try again in a few minutes."; break;
		}
		if ( $this->ms_display_error ) $this->ms_display_error .= " (code {$n_line})";

		return false;
	}

	function getFooterJavaScript()
	{
		$s_config =	'{baseDomain:"'.	$this->ms_base_subdomain.'"'.
					',onPopup:PicMngt.onPopup'.
					',context:1'.
					',ulExplain:1'.
					',ulPicMngt:1'.
					'}';

		return	"function onMenuClick(action){PicMngt.onClick(action);};".
				"Filmaf.config({$s_config});".
				"PicMngt.setup();";
	}

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		if ( ! $this->mb_logged_in )
			return $this->tellUser(__LINE__, MSG_NOT_LOGGED_IN);

		if ( CSql::query_and_fetch1("SELECT block_submissions FROM dvdaf_user_2 WHERE user_id = '{$this->ms_user_id}'", 0,__FILE__,__LINE__) != 'N' )
			return $this->tellUser(__LINE__, MSG_USER_BLOCKED, false);

		getDvdRights($this);
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		if ( $this->ms_display_error )
		{
			$this->drawMessages(true,false);
			return;
		}

		echo "<div id='content'>";
		$this->drawPicMngt();
		echo "</div>";
	}

	function drawPicMngt()
	{
		$s_title   = '';
		$s_item    = '';
		$ss		   = '';
		$s_def_pic = '-';

		switch ( $this->mn_obj_type )
		{
		case 'dvd':
			if ( $this->mn_obj_id )
			{
				if ( ! ($rr = CSql::query_and_fetch("SELECT a.dvd_title, a.film_rel_year, a.region_mask, a.director, a.publisher, a.pic_status, ".
														   "a.pic_name, '-' pic_overwrite, a.media_type, a.source, a.pic_count, b.pic_id ".
														  "FROM dvd a ".
														  "LEFT JOIN dvd_pic b ON a.dvd_id = b.dvd_id ".
														  "LEFT JOIN pic c ON b.pic_id = c.pic_id ".
														 "WHERE a.pic_name = c.pic_name and a.dvd_id = {$this->mn_obj_id}", 0,__FILE__,__LINE__)) )
					if ( ! ($rr = CSql::query_and_fetch("SELECT a.dvd_title, a.film_rel_year, a.region_mask, a.director, a.publisher, a.pic_status, ".
															   "a.pic_name, '-' pic_overwrite, a.media_type, a.source, a.pic_count, b.pic_id ".
															  "FROM dvd a ".
															  "LEFT JOIN dvd_pic b ON a.dvd_id = b.dvd_id ".
															  "LEFT JOIN pic c ON b.pic_id = c.pic_id and a.pic_name = c.pic_name ".
															 "WHERE a.dvd_id = {$this->mn_obj_id}", 0,__FILE__,__LINE__)) )
					return $this->tellUser(__LINE__, MSG_DVD_ID_NOT_FOUND);
				$s_item = 'this DVD';
			}
			else
			{
				$rr  = CSql::query_and_fetch("SELECT a.dvd_title, a.film_rel_year, a.region_mask, a.director, a.publisher, '-' pic_status, ".
													"'-' pic_name, '-' pic_overwrite, 0 pic_count, 0 pic_id, a.media_type, a.source, d.descr disposition ".
												   "FROM dvd_submit a LEFT JOIN decodes d ON d.domain_type = 'disposition_cd' and d.code_char = a.disposition_cd ".
												  "WHERE edit_id = {$this->mn_obj_edit_id} and proposer_id = '{$this->ms_user_id}'", 0,__FILE__,__LINE__);
				if ( ! $rr ) return $this->tellUser(__LINE__, MSG_EDIT_ID_NOT_FOUND);
				$s_item = "DVD submission {$this->mn_obj_edit_id} ({$rr['disposition']})";
			}
			$s_title =  "<table class='border'>".
						  "<thead>".
							"<tr>".
							  "<td>Picture</td>".
							  "<td>Title</td>".
							  "<td>Director</td>".
							  "<td>Publisher</td>".
							"</tr>".
						  "</thead>".
						  "<tbody>".
							"<tr class='dvd_row0'>".
// DVDAF_zz_small_pic no longer defined
							  "<td class='dvd_pic'>". dvdaf_getbrowserfield($rr, DVDAF_zz_small_pic, 0, 0, 0, 0, 0, $this->mn_obj_id). "</td>".
							  "<td class='dvd_tit'>". dvdaf_getbrowserfield($rr, DVDAF_zz_title_2). "</td>".
							  "<td class='dvd_dir'>{$rr['director']}</td>".
							  "<td class='dvd_pub'>{$rr['publisher']}</td>".
							"</tr>".
						  "</tbody>".
					   "</table>";
			$s_def_pic = $rr['pic_id'];
			$ss = getPicEditListSql($this->mn_obj_id, 0, $this->mb_mod ? '' : $this->ms_user_id);
			break;
		default:
			return $this->tellUser(__LINE__, MSG_OBJ_TYPE);
			break;
		}

		$n_req_def_edit_id		= 0;
		$n_req_def_pic_id	= 0;
		$n_req_def_pic_edit_id	= 0;
		if ( ($rr = CSql::query_and_fetch  ("SELECT def_edit_id, pic_id, pic_edit_id ".
							  "FROM pic_def_submit s ".
							 "WHERE obj_type = 'D' and obj_id = {$this->mn_obj_id} and disposition_cd = '-' and proposer_id = '{$this->ms_user_id}'", 0,__FILE__,__LINE__)) )
		{
			$n_req_def_edit_id	   = $rr['def_edit_id'];
			$n_req_def_pic_id	   = $rr['pic_id'];
			$n_req_def_pic_edit_id = $rr['pic_edit_id'];
		}

		$n_pic  = 0;
		$s_type = '';
		$s_pics = '';
		$s_one  = '';
		if ( $ss )
		{
			if ( ($rr = CSql::query($ss, 0,__FILE__,__LINE__)) )
			{
				$pc = CSql::fetch($rr);
				while ( $pc )
				{
					if ( $s_type != $pc['pic_type_txt'] )
					{
						$s_pics .= ($s_pics ? "</tbody></table>" : '').
									"<h3>{$pc['pic_type_txt']}</h3>".
									"<table class='border' style='margin-left:20px'>".
									  "<thead>".
										"<tr>".
										  "<td>Current</td>".
										  "<td>Proposed</td>".
										  "<td>Notes</td>".
										"</tr>".
									  "</thead>".
									  "<tbody>";
						$s_type  = $pc['pic_type_txt'];
					}

					$s_one  = drawPicSubCurr ($pc, $s_def_pic, true). drawPicSubProp ($pc, $pc['disposition_cd'], $pc['request_cd'], $this->ms_base_subdomain, $s_prop_id, true, $this->ms_user_id == $pc['proposer_id']);
					$s_one .= drawPicSubNotes($pc, $n_req_def_edit_id, $n_req_def_pic_id, $n_req_def_pic_edit_id, $pc['disposition_txt'], '', true, $s_prop_id);
					$n_pic  = $pc['pic_id'];
					$pc     = CSql::fetch($rr);
					$n_cnt  = 1;
					while ( $pc && $n_pic && $n_pic == $pc['pic_id'] )
					{
						$s_one .= "</tr><tr class='dvd_row0' style='vertical-align:top'>". drawPicSubProp ($pc, $pc['disposition_cd'], $pc['request_cd'], $this->ms_base_subdomain, $s_prop_id, true, $this->ms_user_id == $pc['proposer_id']);
						$s_one .= drawPicSubNotes($pc, $n_req_def_edit_id, $n_req_def_pic_id, $n_req_def_pic_edit_id, $pc['disposition_txt'], '', false, $s_prop_id);
						$n_cnt++;
						$pc = CSql::fetch($rr);
					}

					$s_pics .= "<tr class='dvd_row0' style='vertical-align:top'>".
						 ($n_cnt > 1 ? "<td rowspan='{$n_cnt}'". substr($s_one, 3) : $s_one).
						   "</tr>";

					if ( ! $pc ) $pc = CSql::fetch($rr);
				}
				CSql::free($rr);

				if ( $s_pics ) $s_pics = $s_pics. "</tbody></table>";
			}
		}

		echo	"<form id='myform' name='myform' method='post' action='{$this->ms_request_uri}'>".
				  "<input type='hidden' id='h_time' value='". time() ."000' />".
				  "<input type='hidden' id='h_ip' value='". dvdaf3_getvalue('REMOTE_ADDR', DVDAF3_SERVER) ."' />".
				  "<h2>Managing Pictures for $s_item</h2>".
				  "<div style='margin:10px 0 30px 40px'>{$s_title}</div>".
				  "<h2>Add new picture</h2>".
				  "<div style='margin:10px 0 30px 40px'>".
					"<div style='width:480px;margin-bottom:20px'>".
					"All pictures must be for the same edition of a title. If you wish to represent a different edition ".
					"of this film/show please use the &quot;FilmAf / Contributions / Submit New Title&quot; menu option.".
					"</div>".
					"<input type='button' id='b_add' value='Add new picture' />".
				  "</div>";

		if ( $s_pics ) echo
				  "<h2>Manage existing or proposed pictures</h2>".
				  "<div style='margin:10px 0 0 20px'>".
					"<div style='width:480px;margin-left:20px'>".
					  "<span class='highkey'>Click on a button below</span> to be presented with options for the picture above it. ".
					  "Options include: edit, delete, upload better quality picture, withdraw pending requests, etc.".
					"</div>".
					$s_pics.
				  "</div>";

		echo	"</form>";
	}
}

?>
