<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWnd.php';
require $gs_root.'/libx/CWnd2.php';
require $gs_root.'/libx/rights-dvd.inc.php';
require $gs_root.'/libx/edit-pic.inc.php';

define('MSG_NOT_LOGGED_IN'		,     1);
define('MSG_OBJ_TYPE'			,     2);
define('MSG_DVD_ID_NOT_FOUND'	,     3);
define('MSG_EDIT_ID_NOT_FOUND'	,     4);
define('MSG_MOD_ONLY'			,     5);
define('MSG_USER_BLOCKED'		,     6);

class CPicMngt extends CWnd2
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
	//	$this->mb_trace_functions	= true;
	//	$this->mb_show_trace		= true;
	//	$this->mb_trace_variables	= true;
	//	$this->mb_trace_environment	= true;
	//	$this->mb_trace_sql			= true;
	//	$this->mb_allow_redirect	= false;
		$this->mb_initialize_menu	= false;
		$this->ms_include_css	   .=
		"<style type='text/css'>".
			// BUTTONS
			".ia { width:24px; }".																/* button context menu		input.but_ctx	*/
			".ib { width:24px; color:#777777; }".												/* button default value		input.but_def	*/
			".ic { width:24px; color:#bd0b0b; }".												/* button invoke tests		input.but_tst	*/
			// MENUS & NAVIGATION
			".mg { color:#072b4b; }".															/* page navigation			aqua + gray	*/
			"A.mg, A.mg:visited, A.mg:active { color:#bd0b0b; text-decoration:underline; }".	/*							red			*/
			"A.mg:hover { color:#6caad9; }".													/*							aqua		*/
			".mh { color:#bd0b0b; }".															/* popup menu highlight		red			*/
			".mp {}".																			/* used for menu attach					*/
			".x1 { background:transparent url('http://dv1.us/d1/00/header-back-small.jpg') repeat-x; text-align:center; color:#ffffff; }".
			".x2 { background:transparent url('http://dv1.us/d1/00/header-back-small.jpg') repeat-x; text-align:center; color:#ffffff; font-weight:bold; }".
			// TITLE PRESENTATION: ONE
			".og { color:#072b4b; }".															/* field td					red + black	*/
			".oh { color:#072b4b; }".															/* field					red + black	*/
			".oi { color:#57af63; white-space:nowrap; text-align:right; vertical-align:top; }".	/* label td					green		*/
			".oj { color:#57af63; }".															/* label					green		*/
			".ok { color:#072b4b; background-color:#eeeeee; border:#cccccc solid 1px; }".		/* field td disabled		red + black	*/
			// PICTURE SUBMISSION
			".qa { color:#144067; }".															/* pictures curr			blue + gray	*/
			".qb { color:#70a2cf; }".															/* pictures prop done		aqua		*/
			".qc { color:#bd0b0b;}".															/* pictures prop pend		red			*/
			".qd { color:#ef6262; text-decoration:underline; }".								/* pictures high key		redish		*/
			".qe {}".																			/* pictures low key						*/
			// TITLE PRESENTATION: SCREEN
			".se { background-color:#ffffff; }".												/* field tr					white		*/
			".sg { color:#072b4b; padding:0px 2px 0px 2px; }".									/* field td					blue + black*/
			".sl { color:#57af63; }".															/* comments					aqua + gray	*/
			".sz { color:#bd0b0b; font-weight:bold; }".											/* highlights				red			*/
			// STANDARD
			"table.padded td { padding:2px; }".
			"table.padded_10 td { padding:2px 10px 2px 10px; }".
			"table.nowrap td { white-space:nowrap; vertical-align:top; }".
		"</style>";

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-pic-mngt_{$this->mn_lib_version}.js'></script>\n";
		$this->ms_header_title		= 'Picture Management';
		$this->ms_title				= 'Picture Management';
		$this->ms_request_uri		= dvdaf_getvalue('REQUEST_URI', DVDAF_SERVER | DVDAF_NO_AMP_EXPANSION);
		$this->mb_include_menu		= true;

		$this->mn_edit				= 0;
		$this->mn_new				= 0;
		$this->mn_pic				= 0;
		$this->mn_used_edit			= 0;
		$this->mn_used_new			= 0;
		$this->mn_used_pic			= 0;
		$this->mb_mod				= false;

		$this->mn_obj_type			= dvdaf_getvalue('obj_type',DVDAF_GET|DVDAF_LOWER);
		$this->mn_obj_id			= dvdaf_getvalue('obj'     ,DVDAF_GET|DVDAF_INT  );
		$this->mn_obj_edit_id		= dvdaf_getvalue('obj_edit',DVDAF_GET|DVDAF_INT  );
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
			echo $this->getMessageString(true, 'margin:20px 50px 20px 50px');
			return;
		}

		$this->drawPicMngt();
	}

	function drawPicMngt()
	{
		$s_title	= '';
		$s_item		= '';
		$ss			= '';
		$s_def_pic	= '-';

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
			$s_title =  "<table class='sa' border='1'>".
						  "<tr class='sb'>".
							"<td class='sc' style='text-align:center'>Picture</td>".
							"<td class='sc' style='padding:2px'>Title</td>".
							"<td class='sc' style='padding:2px'>Director</td>".
							"<td class='sc' style='padding:2px'>Publisher</td>".
						  "</tr>".
						  "<tr class='se'>".
							"<td class='sg' style='text-align:center'>". dvdaf_getbrowserfield($rr, DVDAF_zz_small_pic, 0, 0, 0, $this->mn_obj_id). "</td>".
							"<td class='sg' style='padding:2px 10px 2px 2px'>". dvdaf_getbrowserfield($rr, DVDAF_zz_title_2  ). "</td>".
							"<td class='sg' style='padding:2px 10px 2px 2px'>{$rr['director']}</td>".
							"<td class='sg' style='padding:2px 10px 2px 2px'>{$rr['publisher']}</td>".
						  "</tr>".
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
			$n_req_def_edit_id		= $rr['def_edit_id'];
			$n_req_def_pic_id		= $rr['pic_id'];
			$n_req_def_pic_edit_id	= $rr['pic_edit_id'];
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
						$s_pics .= ($s_pics ? "</table>" : '').
								   "<h3 style='margin:20px 0 10px 0'>{$pc['pic_type_txt']}</h3>".
								   "<table class='sa' border='1' style='margin-left:20px'>".
									 "<tr class='sb'>".
									   "<td class='sc' style='padding:2px;text-align:center'>Current</td>".
									   "<td class='sc' style='padding:2px;text-align:center'>Proposed</td>".
									   "<td class='sc' style='padding:2px 200px 2px 2px'>Notes</td>".
									 "</tr>";
						$s_type  = $pc['pic_type_txt'];
					}

					$s_one  = drawPicSubCurr ($pc, $s_def_pic, true). drawPicSubProp ($pc, $pc['disposition_cd'], $pc['request_cd'], $this->ms_base_subdomain, $s_prop_id, true, $this->ms_user_id == $pc['proposer_id']);
					$s_one .= drawPicSubNotes($pc, $n_req_def_edit_id, $n_req_def_pic_id, $n_req_def_pic_edit_id, $pc['disposition_txt'], '', true, $s_prop_id);
					$n_pic  = $pc['pic_id'];
					$pc     = CSql::fetch($rr);
					$n_cnt  = 1;
					while ( $pc && $n_pic && $n_pic == $pc['pic_id'] )
					{
						$s_one .= "</tr><tr class='se' style='vertical-align:top'>". drawPicSubProp ($pc, $pc['disposition_cd'], $pc['request_cd'], $this->ms_base_subdomain, $s_prop_id, true, $this->ms_user_id == $pc['proposer_id']);
						$s_one .= drawPicSubNotes($pc, $n_req_def_edit_id, $n_req_def_pic_id, $n_req_def_pic_edit_id, $pc['disposition_txt'], '', false, $s_prop_id);
						$n_cnt++;
						$pc = CSql::fetch($rr);
					}

					$s_pics .= "<tr class='se' style='vertical-align:top'>".
								 ($n_cnt > 1 ? "<td rowspan='{$n_cnt}'". substr($s_one, 3) : $s_one).
							   "</tr>";

					if ( ! $pc ) $pc = CSql::fetch($rr);
				}
				CSql::free($rr);

				if ( $s_pics ) $s_pics = $s_pics.'</table>';
			}
		}

		echo	"<div style='margin:20px 10px 0 10px'>".
				  "<form id='myform' name='myform' method='post' action='{$this->ms_request_uri}'>".
				  "<input type='hidden' id='h_time' value='". time() ."000' />".
				  "<input type='hidden' id='h_ip' value='". dvdaf_getvalue('REMOTE_ADDR', DVDAF_SERVER) ."' />".
				  "<h2>Managing Pictures for $s_item</h2>".
				  "<div style='margin:10px 0 30px 40px'>{$s_title}</div>".
				  "<h2>Add new picture</h2>".
				  "<div style='margin:10px 0 30px 40px'>".
					"<div style='width:480px;margin-bottom:20px'>".
					"All pictures must be for the same edition of a title. If you wish to represent a different edition ".
					"of this film/show please use the &quot;Film Aficionado / Contributions / Submit New Title&quot; menu option.".
					"</div>".
					"<input type='button' id='b_add' value='Add new picture' />".
				  "</div>";

		if ( $s_pics ) echo
				  "<h2>Manage existing or proposed pictures</h2>".
				  "<div style='margin:10px 0 0 20px'>".
					"<div style='width:480px;margin-left:20px'>".
					  "<span class='hh'>Click on a button below</span> to be presented with options for the picture above it. ".
					  "Options include: edit, delete, upload better quality picture, withdraw pending requests, etc.".
					"</div>".
					$s_pics.
				  "</div>";

		echo	  "</form>".
				  "<ul id='context-menu' style='display:none'><li></li></ul>".
				"</div>";
	}

	function getFooterJavaScript()
	{
		$s_config =
			'{baseDomain:"'.	$this->ms_base_subdomain.'"'.
			',onPopup:PicMngt.onPopup'.
			',context:1'.
			',ulExplain:1'.
			',ulPicMngt:1'.
			'}';

		return
			"function onMenuClick(action){PicMngt.onClick(action);};".
			"Filmaf.config({$s_config});".
			"PicMngt.setup();";
	}
}

?>
