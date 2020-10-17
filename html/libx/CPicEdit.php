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
require $gs_root.'/libx/dvd-update.inc.php';

define('MSG_ALREADY_SAVED'		,     1);
define('MSG_BAD_FILENAME'		,     2);
define('MSG_BAD_REPLACE'		,     3);
define('MSG_FAIL_TO_PROCESS'	,     4);
define('MSG_NO_DATA'			,     5);
define('MSG_NOT_LOGGED_IN'		,     6);
define('MSG_TOO_SMALL'			,     7);
define('MSG_UNKNOWN_ACTION'		,     8);
define('MSG_UNKNOWN_OBJ_TYPE'	,     9);
define('MSG_UNKNOWN_PICNAME'	,    10);
define('MSG_UNKNOWN_STEP'		,    11);
define('MSG_UPLOAD_BAD_DIR'		,    12);
define('MSG_UPLOAD_BASE_DIR'	,    13);
define('MSG_UPLOAD_EMPTY'		,    14);
define('MSG_UPLOAD_ERROR'		,    15);
define('MSG_UPLOAD_PARTIAL'		,    16);
define('MSG_UPLOAD_SIZE'		,    17);
define('MSG_MOD_ONLY'			,    18);
define('MSG_NOT_DEL_DEFAULT'	,    19);
define('MSG_DELPIC_NOTFOUND'	,    20);
define('MSG_DELSUB_NOTFOUND'	,    21);
define('MSG_DEFAULT_NOTFOUND'	,    22);
define('MSG_DEFAULT_ALREADY'	,    23);
define('MSG_DEFAULT_FAILLED'	,    24);
define('MSG_DEFAULT_NOTSAVED'	,    25);
define('MSG_REJSUB_NOTFOUND'	,    26);
define('MSG_USER_BLOCKED'		,    27);
define('MSG_BAD_DVD_EDIT_ID'	,    28);

//  p0/###    small                *.gif +++ already populated
//  p1/###    medium               *.jpg --- to be populated
//  p2/###    large                *.jpg -
//  p3/###    huge                 *.jpg -
//  p4/###    original display     *.jpg --- to be populated
//- p5/###    edited               *.pgn
//- p6/###    original             *.jpg +++
//  uploads   unprocessed pics     *.jpg, *.gif, *.bmp, *.png

class CPicEdit extends CWnd2
{
	function constructor() // <<--------------------------------<< 1.0
	{
		parent::constructor();
	//	$this->mb_trace_functions	= true;
	//	$this->mb_show_trace		= true;
	//	$this->mb_trace_variables	= true;
	//	$this->mb_trace_environment	= true;
	//	$this->mb_trace_sql		= true;
	//	$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-pic-mngt_{$this->mn_lib_version}.js'></script>\n";
		$this->ms_header_title		= 'Edit Picture';
		$this->ms_title				= 'Edit Picture';
		$this->ms_request_uri		= dvdaf_getvalue('REQUEST_URI', DVDAF_SERVER | DVDAF_NO_AMP_EXPANSION);
		$this->mb_include_menu		= true;
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

		$this->mn_pic_dx			= 0;
		$this->mn_pic_dy			= 0;
		$this->ms_base_pic			= '';
		$this->ms_bord_pic			= '';
		$this->ms_prev_pic			= '';
		$this->ma_data				= null;
		$this->mb_pic_saved			= false;
		$this->mb_pic_deleted		= false;
		$this->mb_is_preview		= false;
		$this->ma_cmdout			= array();
		$this->mn_cmdret			= 0;

		$this->ms_obj_type			= dvdaf_getvalue('obj_type', DVDAF_POST|DVDAF_LOWER);
		$this->mc_obj_type			= '';
		$this->mc_def_pic_type		= '';
		$this->mc_pic_prefix		= '';
		$this->mn_def_edit_id		= 0;
		$this->mb_review_sub		= false;

		if ( $this->ms_obj_type )
		{
			$this->mb_dvd_submission	= dvdaf_getvalue('pdvd'			  ,DVDAF_POST|DVDAF_BOOLEAN); // pdvd=1 if oppened from dvd submission processing, as opposed to picture management
			$this->ms_act				= dvdaf_getvalue('act'			  ,DVDAF_POST|DVDAF_LOWER  ); // new, rep, edit, asnew
			$this->ms_step				= dvdaf_getvalue('step'			  ,DVDAF_POST|DVDAF_LOWER  ); // upload, preview, save
			$this->mn_pic_id			= dvdaf_getvalue('pic'			  ,DVDAF_POST|DVDAF_INT    );
			$this->mn_pic_edit_id		= dvdaf_getvalue('n_p_pic_edit_id',DVDAF_POST|DVDAF_INT	   ); if ( ! $this->mn_pic_edit_id ) $this->mn_pic_edit_id = dvdaf_getvalue('pic_edit', DVDAF_POST|DVDAF_INT);
			$this->ms_obj_type			= dvdaf_getvalue('obj_type'		  ,DVDAF_POST|DVDAF_LOWER  );
			$this->mn_obj_id			= dvdaf_getvalue('obj'			  ,DVDAF_POST|DVDAF_INT    );
			$this->mn_obj_edit_id		= dvdaf_getvalue('obj_edit'		  ,DVDAF_POST|DVDAF_INT    );
			$this->ms_seed				= dvdaf_getvalue('seed'			  ,DVDAF_POST			   ); // 2007-10-10 21:08:50 192.168.1.100
			$this->ms_replace			= dvdaf_getvalue('replace_pic'	  ,DVDAF_POST|DVDAF_LOWER  );
		}
		else
		{
			$this->mb_dvd_submission	= dvdaf_getvalue('pdvd'			  ,DVDAF_GET|DVDAF_BOOLEAN );
			$this->ms_act				= dvdaf_getvalue('act'			  ,DVDAF_GET|DVDAF_LOWER   ); if ( ! $this->ms_act ) $this->ms_act = 'open'; // in preparation for edit (del, def, wdr, open)
			$this->ms_step				= '';
			$this->mn_pic_id			= dvdaf_getvalue('pic'			  ,DVDAF_GET|DVDAF_INT     );
			$this->mn_pic_edit_id		= dvdaf_getvalue('pic_edit'		  ,DVDAF_GET|DVDAF_INT     );
			$this->ms_obj_type			= dvdaf_getvalue('obj_type'		  ,DVDAF_GET|DVDAF_LOWER   );
			$this->mn_obj_id			= dvdaf_getvalue('obj'			  ,DVDAF_GET|DVDAF_INT     );
			$this->mn_obj_edit_id		= dvdaf_getvalue('obj_edit'		  ,DVDAF_GET|DVDAF_INT     );
			$this->ms_seed				= date('Y-m-d H:i:s '). dvdaf_getvalue('REMOTE_ADDR', DVDAF_SERVER);
			$this->ms_replace			= '';
		}
		$this->ms_pic_name		= '';
		$this->ms_pic_mngt_url		= "/utils/x-pic-mngt.html?obj_type={$this->ms_obj_type}&obj={$this->mn_obj_id}". ($this->mn_obj_edit_id ? "&obj_edit={$this->mn_obj_edit_id}" : '');
		$this->ms_return_url		= $this->mb_dvd_submission ? "/utils/dvd-appr.html" : $this->ms_pic_mngt_url;
	}

	function tellUser($n_line, $n_what)
	{
		switch ( $n_what )
		{
		case MSG_ALREADY_SAVED:		$this->ms_display_error = 'Could not apply your changes. This entry may already have been processed.'; break;
		case MSG_BAD_FILENAME:		$this->ms_display_error = 'Bad Uploaded filename.'; break;
		case MSG_BAD_REPLACE:		$this->ms_display_error = 'Could not find the pictute you are trying to edit or replace.'; break;
		case MSG_FAIL_TO_PROCESS:	$this->ms_display_error = "Sorry the picture you uploaded seems to be invalid or corrupted.<br />&nbsp;<br /><a href='{$this->ms_return_url}'>Go back</a>."; break;
		case MSG_NO_DATA:			$this->ms_display_error	= 'Sorry, somehow we are not finding the data for your request.  Please try again.'; break;
		case MSG_NOT_LOGGED_IN:		$this->ms_display_error = "Sorry, we can not honor your request because you are not logged in."; break;
		case MSG_TOO_SMALL:			$this->ms_display_error	= "Uploaded picture is too small ({$this->mn_pic_dx}x{$this->mn_pic_dy}). To maintain and improve ".
															  "quality standards the minimum size we accept is 280x280. Note that resizing or padding images to bypass ".
															  "this automated check will generate more work for us and only cause them to be rejected later when we ".
															  "inspect them for quality. Sorry."; break;
		case MSG_UNKNOWN_ACTION:	$this->ms_display_error = 'Unrecognized action.'; break;
		case MSG_UNKNOWN_OBJ_TYPE:	$this->ms_display_error = 'Unrecognized object type.'; break;
		case MSG_UNKNOWN_PICNAME:	$this->ms_display_error = 'Unrecognized picture name.'; break;
		case MSG_UNKNOWN_STEP:		$this->ms_display_error = 'Unrecognized step.'; break;
		case MSG_UPLOAD_BAD_DIR:	$this->ms_display_error = 'Base folder not set.'; break;
		case MSG_UPLOAD_BASE_DIR:	$this->ms_display_error = 'Unrecognized base folder.'; break;
		case MSG_UPLOAD_EMPTY:		$this->ms_display_error = 'No file was uploaded.'; break;
		case MSG_UPLOAD_ERROR:		$this->ms_display_error = 'Error uploading file.'; break;
		case MSG_UPLOAD_PARTIAL:	$this->ms_display_error = 'Your file was only partially uploaded.'; break;
		case MSG_UPLOAD_SIZE:		$this->ms_display_error = 'The uploaded file exceeds the maximum file size ('+get_cfg_var('upload_max_filesize')+').'; break;
		case MSG_MOD_ONLY:			$this->ms_display_error = 'This function is currently only available to moderators. Please check back in a couple of days.'; break;
		case MSG_NOT_DEL_DEFAULT:	$this->ms_display_error = 'We can not delete the default picture for a listing.'; break;
		case MSG_DELPIC_NOTFOUND:	$this->ms_display_error = 'We could not find the picture you want to delete. It may have been deleted already.'; break;
		case MSG_DELSUB_NOTFOUND:	$this->ms_display_error = 'We could not find the submission you want to delete. It may have been deleted already.'; break;
		case MSG_DEFAULT_NOTFOUND:	$this->ms_display_error = 'No picture to be replaced by default.'; break;
		case MSG_DEFAULT_ALREADY:	$this->ms_display_error = 'The selected picture is already the default for this item.'; break;
		case MSG_DEFAULT_FAILLED:	$this->ms_display_error = 'Sorry, we are not able to set this picture as the default for this listing. Perhaps it is no longer associated with it.'; break;
		case MSG_DEFAULT_NOTSAVED:	$this->ms_display_error = 'Please approve the submission of this picture before making it the default for this title. Thanks.'; break;
		case MSG_REJSUB_NOTFOUND:	$this->ms_display_error = 'We could not find the submission you want to reject. It may have already been processed or rejected.'; break;
		case MSG_USER_BLOCKED:		$this->ms_display_error = "Sorry, we encountered an error. We appologize for any inconveniece, please try again in a few minutes."; break;
		case MSG_BAD_DVD_EDIT_ID:	$this->ms_display_error = 'Bad link to DVD submission.'; break;
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

//		echo "1: {$this->ms_obj_type} {$this->ms_act}<br />";
		getDvdRights($this);

		switch ( $this->ms_obj_type )
		{
		case 'dvd':
			$this->mc_obj_type		= 'D';
			$this->mc_def_pic_type	= 'D';
			$this->mc_pic_prefix	= 'd';
			$this->ms_col_DVD_ID	= 'dvd_id';
			$this->ms_tbl_DVD_PIC	= 'dvd_pic';
			$this->ms_tbl_DVD		= 'dvd';
			$this->ms_tbl_MY_DVD	= 'my_dvd';
			$this->ms_tbl_DVD_SUB	= 'dvd_submit';

			switch ( $this->ms_act )
			{
			case 'dap':			$this->validateDelete(true);						break;
			case 'del':			$this->validateDelete(false);						break;
			case 'wdr':			$this->validateWithdraw();							break;
			case 'rej':			$this->validateReject();							break;
			case 'def':			$this->validateDefault();							break;
			case 'open':		$this->validateOpen();								break;

			case 'new':
			case 'asnew':
			case 'rep':
			case 'edit':
				switch ( $this->ms_step )
				{
				case 'upload':		$this->validateUpload();						break;
				case 'preview':		$this->validatePreview();						break;
				case 'save':		$this->validateSave();							break;
				default:			$this->tellUser(__LINE__, MSG_UNKNOWN_STEP);	break;
				}
				break;
			default:			$this->tellUser(__LINE__, MSG_UNKNOWN_ACTION);		break;
			}
			break;

		case 'profile':
			switch ( $this->ms_act )
			{
			case 'open':		$this->validateOpen();								break;
			}
			break;

		default:				$this->tellUser(__LINE__, MSG_UNKNOWN_OBJ_TYPE);	break;
		}
	}

	function insIntoPicSubmit($s_request_cd, $s_disposition_cd, &$s_pic_name)
	{
		if ( $s_disposition_cd == '-' )
		{
			CSql::query_and_free("INSERT INTO pic_submit ".
										"(obj_edit_id, obj_id, obj_type, pic_id, request_cd, proposer_id, ".
										 "proposed_tm, updated_tm, hist_version_id, hist_sub_version_id, pic_name, pic_type, transforms, caption, copy_holder, ".
										 "copy_year, suitability_cd, pic_dx, pic_dy, creation_seed) ".
								  "SELECT {$this->mn_obj_edit_id}, {$this->mn_obj_id}, '{$this->mc_obj_type}', pic_id, '{$s_request_cd}', '{$this->ms_user_id}', ".
										 "now(), now(), version_id, sub_version_id, pic_name, pic_type, transforms, caption, copy_holder, ".
										 "copy_year, suitability_cd, pic_dx, pic_dy, '{$this->ms_seed}' ".
									"FROM pic ".
								   "WHERE pic_id = {$this->mn_pic_id}",0,__FILE__,__LINE__);
		}
		else
		{
			CSql::query_and_free("INSERT INTO pic_submit ".
										"(obj_edit_id, obj_id, obj_type, pic_id, request_cd, disposition_cd, proposer_id, ".
										 "proposed_tm, updated_tm, reviewer_id, reviewed_tm, hist_version_id, hist_sub_version_id, pic_name, pic_type, transforms, caption, copy_holder, ".
										 "copy_year, suitability_cd, pic_dx, pic_dy, creation_seed) ".
								  "SELECT {$this->mn_obj_edit_id}, {$this->mn_obj_id}, '{$this->mc_obj_type}', pic_id, '{$s_request_cd}', '{$s_disposition_cd}', '{$this->ms_user_id}', ".
										 "now(), now(), '{$this->ms_user_id}', now(), version_id, sub_version_id, pic_name, pic_type, transforms, caption, copy_holder, ".
										 "copy_year, suitability_cd, pic_dx, pic_dy, '{$this->ms_seed}' ".
									"FROM pic ".
								   "WHERE pic_id = {$this->mn_pic_id}",0,__FILE__,__LINE__);
		}

		$rr			= CSql::query_and_fetch("SELECT pic_edit_id, pic_name FROM pic_submit WHERE proposer_id = '{$this->ms_user_id}' and creation_seed = '{$this->ms_seed}'",0,__FILE__,__LINE__);
		$s_pic_name = $rr['pic_name'];
		return $rr['pic_edit_id'];
	}

	function validateDelete($b_sub_approved)
	{
		if ( ! $this->mn_pic_id ) return $this->tellUser(__LINE__, MSG_UNKNOWN_PICNAME);

		// skip if this is the default picture for this title
		if ( CSql::query_and_fetch1("SELECT count(*) ".
									  "FROM pic p ".
									  "JOIN {$this->ms_tbl_DVD} a ON a.pic_name = p.pic_name ".
									 "WHERE p.pic_id = {$this->mn_pic_id} and a.{$this->ms_col_DVD_ID} = $this->mn_obj_id",0,__FILE__,__LINE__) > 0 )
			return $this->tellUser(__LINE__, MSG_NOT_DEL_DEFAULT);

		if ( $this->mb_mod )
		{
			// delete association, insert into pic_submit + obj_submit_pic a request for deletion and approve it (partial approval if remaining associations do not allow actual deletion)
			CSql::query_and_free("DELETE FROM {$this->ms_tbl_DVD_PIC} WHERE pic_id = {$this->mn_pic_id} and {$this->ms_col_DVD_ID} = $this->mn_obj_id",0,__FILE__,__LINE__);

			// delete references to this pic in pic overwrite
			$s_pic_name = CSql::query_and_fetch1("SELECT pic_name FROM pic WHERE pic_id = {$this->mn_pic_id}",0,__FILE__,__LINE__);
			if ( $s_pic_name && $s_pic_name != '-' )
				CSql::query_and_free("UPDATE {$this->ms_tbl_MY_DVD} SET pic_overwrite = '-' WHERE pic_overwrite = '{$s_pic_name}'",0,__FILE__,__LINE__);

			// update pic count in dvd table
			CSql::query_and_free("UPDATE {$this->ms_tbl_DVD} ".
									"SET pic_count = (SELECT count(*) FROM {$this->ms_tbl_DVD_PIC} WHERE {$this->ms_col_DVD_ID} = {$this->mn_obj_id})".
								  "WHERE {$this->ms_col_DVD_ID} = {$this->mn_obj_id}",0,__FILE__,__LINE__);

			// check if this picture still should be around
			$n_assoc = CSql::query_and_fetch1("SELECT count(*) ".
												"FROM {$this->ms_tbl_DVD_PIC} b ".
												"JOIN {$this->ms_tbl_DVD} a ON a.{$this->ms_col_DVD_ID} = b.{$this->ms_col_DVD_ID} ".
											   "WHERE b.pic_id = {$this->mn_pic_id}",0,__FILE__,__LINE__);
			if ( ! $b_sub_approved )
				$this->mn_pic_edit_id = $this->insIntoPicSubmit('D', $n_assoc > 0 ? 'P' : 'A', $this->ms_pic_name);

			if ( $n_assoc == 0 )
			{
				// no remaining associations, copy it to pic_hist and delete it from pic, save the old picture with the supplement of "del-{pic_edit_id}"
				if ( $this->ms_pic_name && $this->mn_pic_edit_id )
				{
					$s_suplement	= "del-{$this->mn_pic_edit_id}";
					$s_deleted_pic	= "{$this->ms_pic_name}.{$s_suplement}";
					CSql::query_and_free("UPDATE pic_submit SET pic_name = '{$s_deleted_pic}' WHERE pic_edit_id = {$this->mn_pic_edit_id}",0,__FILE__,__LINE__);
					CSql::query_and_free("INSERT INTO pic_hist (pic_id, version_id, sub_version_id, pic_name, pic_type, transforms, caption, copy_holder, copy_year, suitability_cd, ".
												"pic_dx, pic_dy, pic_uploaded_tm, pic_uploaded_by, pic_edited_tm, pic_edited_by, pic_verified_tm, pic_verified_by, verified_version, ".
												"pic_edit_id, pic_deleted_tm) ".
										 "SELECT pic_id, version_id, sub_version_id, '{$s_deleted_pic}', pic_type, transforms, caption, copy_holder, copy_year, suitability_cd, ".
												"pic_dx, pic_dy, pic_uploaded_tm, pic_uploaded_by, pic_edited_tm, pic_edited_by, pic_verified_tm, pic_verified_by, verified_version, ".
												"pic_edit_id, now() ".
										   "FROM pic ".
										  "WHERE pic_id = {$this->mn_pic_id}",0,__FILE__,__LINE__);
					CSql::query_and_free("DELETE FROM pic WHERE pic_id = {$this->mn_pic_id}",0,__FILE__,__LINE__);
					$s_cmd = "/var/www/html/shell/move-del {$this->ms_pic_name} {$s_suplement}";
					dvdaf_exec($s_cmd, $this->ma_cmdout, $this->mn_cmdret);
				}
				$this->mb_pic_deleted = true;
			}
			if ( $b_sub_approved )
			{
				// Mark as approved
				CSql::query_and_free("UPDATE pic_submit ".
										"SET disposition_cd = 'A', reviewer_id = '{$this->ms_user_id}', reviewed_tm = now() ".
									  "WHERE pic_edit_id = {$this->mn_pic_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__);
			}
			CSql::query_and_free("UPDATE pic_submit ".
									"SET disposition_cd = 'R', reviewer_id = '{$this->ms_user_id}', reviewed_tm = now() ".
								  "WHERE pic_id = {$this->mn_pic_id} and disposition_cd = '-'",0,__FILE__,__LINE__);
		}
		else
		{
			if ( $b_sub_approved )
				return $this->tellUser(__LINE__, MSG_UNKNOWN_ACTION);

			// insert into pic_submit + obj_submit_pic a request for deletion ('D')
			$this->mn_pic_edit_id = CSql::query_and_fetch1("SELECT pic_edit_id ".
															 "FROM pic_submit ".
															"WHERE obj_id = {$this->mn_obj_id} ".
															  "and obj_type = '{$this->mc_obj_type}' ".
															  "and pic_id = {$this->mn_pic_id} ".
															  "and request_cd = 'D' ".
															  "and disposition_cd = '-' ".
															  "and proposer_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
			if ( ! $this->mn_pic_edit_id )
				$this->mn_pic_edit_id = $this->insIntoPicSubmit('D', '-', $this->ms_pic_name);
		}
		$this->ma_data = true;
	}

	function deleteUploadedPic($n_pic_edit_id, $s_user_id)
	{
		if ( ($s_uploaded_pic = CSql::query_and_fetch1("SELECT uploaded_pic FROM pic_submit WHERE pic_edit_id = {$n_pic_edit_id}".($s_user_id === false ? '' : " and proposer_id = '{$s_user_id}'"),0,__FILE__,__LINE__)) )
		{
			if ( $s_uploaded_pic == '-' )
				$s_uploaded_pic = sprintf('%06d', $n_pic_edit_id);
			$s_cmd = "rm -f /var/www/html/uploads/{$s_uploaded_pic}*";
			dvdaf_exec($s_cmd, $this->ma_cmdout, $this->mn_cmdret);
		}
	}

	function validateWithdraw()
	{
		if ( ! $this->mn_pic_edit_id ) return $this->tellUser(__LINE__, MSG_UNKNOWN_PICNAME);

		if ( ! CSql::query_and_free("UPDATE pic_submit ".
									   "SET disposition_cd = 'W', updated_tm = now() ".
									 "WHERE pic_edit_id = {$this->mn_pic_edit_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'",0,__FILE__,__LINE__) )
			return $this->tellUser(__LINE__, MSG_DELSUB_NOTFOUND);

		$this->deleteUploadedPic($this->mn_pic_edit_id, $this->ms_user_id);
		$this->ma_data = true;
	}

	function validateReject()
	{
		if ( ! $this->mn_pic_edit_id ) return $this->tellUser(__LINE__, MSG_UNKNOWN_PICNAME);

		$s_reviewer_notes = dvdaf_textarea2db(dvdaf_getvalue('modjust', DVDAF_POST), 1000);

		if ( $this->mb_mod && $s_reviewer_notes != '-' )
		{
			if ( ! CSql::query_and_free("UPDATE pic_submit ".
										   "SET disposition_cd = 'R', reviewer_id = '{$this->ms_user_id}', reviewed_tm = now(), reviewer_notes = '{$s_reviewer_notes}' ".
										 "WHERE pic_edit_id = {$this->mn_pic_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__) )
				return $this->tellUser(__LINE__, MSG_REJSUB_NOTFOUND);

//			$this->deleteUploadedPic($this->mn_pic_edit_id, false);
			$this->ma_data = true;
		}
	}

	function validateDefault()
	{
		if ( $this->mb_mod && ! $this->mn_pic_id ) return $this->tellUser(__LINE__, MSG_DEFAULT_NOTSAVED);

		if ( ! ($rr = CSql::query_and_fetch("SELECT pic_name, pic_status, version_id FROM {$this->ms_tbl_DVD} WHERE {$this->ms_col_DVD_ID} = {$this->mn_obj_id}",0,__FILE__,__LINE__)) )
			return $this->tellUser(__LINE__, MSG_DEFAULT_NOTFOUND);
		$s_curr_def			= $rr['pic_name'];
		$s_pic_status		= $rr['pic_status'];
		$n_obj_version_id	= $rr['version_id'];

		if ( $this->mn_pic_edit_id )
		{
			if ( $this->mn_pic_id )
			{
				if ( ! ($rr = CSql::query_and_fetch("SELECT b.pic_type, a.pic_name, a.version_id, a.sub_version_id ".
													  "FROM pic a ".
													  "JOIN pic_submit b ON a.pic_id = b.pic_id and pic_edit_id = {$this->mn_pic_edit_id} ".
													 "WHERE pic_id = {$this->mn_pic_id}",0,__FILE__,__LINE__)) )
					return $this->tellUser(__LINE__, MSG_DEFAULT_NOTFOUND);
			}
			else
			{
				if ( ! ($rr = CSql::query_and_fetch("SELECT pic_type, '-' pic_name, 0 version_id, 0 sub_version_id FROM pic_submit WHERE pic_edit_id = {$this->mn_pic_edit_id}",0,__FILE__,__LINE__)) )
					return $this->tellUser(__LINE__, MSG_DEFAULT_NOTFOUND);
			}
		}
		else
		{
			if ( ! ($rr = CSql::query_and_fetch("SELECT pic_type, pic_name, version_id, sub_version_id FROM pic WHERE pic_id = {$this->mn_pic_id}",0,__FILE__,__LINE__)) )
				return $this->tellUser(__LINE__, MSG_DEFAULT_NOTFOUND);
		}
		$s_pic_type				= $rr['pic_type'];
		$s_prop_def				= $rr['pic_name'];
		$n_pic_version_id		= $rr['version_id'];
		$n_pic_sub_version_id	= $rr['sub_version_id'];

		if ( $this->mb_mod )
		{
			if ( $s_curr_def == $s_prop_def ) return $this->tellUser(__LINE__, MSG_DEFAULT_ALREADY);
			$s_pic_status = $s_pic_type == 'P' ? 'P' : 'Y';
			snapHistory($this->mn_obj_id);
			if ( CSql::query_and_free("UPDATE {$this->ms_tbl_DVD}, {$this->ms_tbl_DVD_PIC} ".
										 "SET {$this->ms_tbl_DVD}.pic_name = '{$s_prop_def}', ".
											 "{$this->ms_tbl_DVD}.pic_status = '{$s_pic_status}', ".
											 "{$this->ms_tbl_DVD}.last_justify = 'Changing default picture from {$s_curr_def} to {$s_prop_def}', ".
											 "{$this->ms_tbl_DVD}.version_id = {$this->ms_tbl_DVD}.version_id + 1, ".
											 "{$this->ms_tbl_DVD}.{$this->ms_tbl_DVD}_updated_tm = now(), ".
											 "{$this->ms_tbl_DVD}.{$this->ms_tbl_DVD}_updated_by = '{$this->ms_user_id}', ".
											 "{$this->ms_tbl_DVD}.{$this->ms_tbl_DVD}_verified_tm = now(), ".
											 "{$this->ms_tbl_DVD}.{$this->ms_tbl_DVD}_verified_by = '{$this->ms_user_id}', ".
											 "{$this->ms_tbl_DVD}.verified_version = {$this->ms_tbl_DVD}.version_id ".        
									   "WHERE {$this->ms_tbl_DVD_PIC}.{$this->ms_col_DVD_ID} = {$this->ms_tbl_DVD}.{$this->ms_col_DVD_ID} ".
										 "and {$this->ms_tbl_DVD_PIC}.pic_id = {$this->mn_pic_id} ".
										 "and {$this->ms_tbl_DVD}.{$this->ms_col_DVD_ID} = {$this->mn_obj_id} ".
										 "and {$this->ms_tbl_DVD}.pic_name = '{$s_curr_def}'",0,__FILE__,__LINE__) < 1 )
				return $this->tellUser(__LINE__, MSG_DEFAULT_FAILLED);
		}
		$this->ms_pic_name = $s_prop_def;

		$s_update   = "UPDATE pic_def_submit ".
						 "SET obj_edit_id = {$this->mn_obj_edit_id}, ".
							 "pic_id = {$this->mn_pic_id}, ".
							 "pic_edit_id = {$this->mn_pic_edit_id}, ".
							 "updated_tm = now(), ".
							 ($this->mb_mod ? "disposition_cd = 'A', ".
											  "reviewer_id = '{$this->ms_user_id}', ".
											  "reviewed_tm = now(), "
											: '').
							 "obj_version_id = {$n_obj_version_id}, ".
							 "pic_version_id = {$n_pic_version_id}, ".
							 "pic_sub_version_id = {$n_pic_sub_version_id} ".
					   "WHERE obj_id = {$this->mn_obj_id} ".
						 "and obj_type = '{$this->mc_obj_type}' ".
						 "and disposition_cd = '-' ".
						 "and proposer_id = '{$this->ms_user_id}'";

		$s_insert   = "INSERT INTO pic_def_submit ".
							"(obj_edit_id, obj_id, obj_type, pic_id, ".
							 "pic_edit_id, proposer_id, proposed_tm, updated_tm, ".
							 ($this->mb_mod ? "disposition_cd, reviewer_id, reviewed_tm, " : '').
							 "obj_version_id, pic_version_id, pic_sub_version_id) ".
					 "VALUES ({$this->mn_obj_edit_id}, {$this->mn_obj_id}, '{$this->mc_obj_type}', {$this->mn_pic_id}, ".
							 "{$this->mn_pic_edit_id}, '{$this->ms_user_id}', now(), now(), ".
							 ($this->mb_mod ? "'A', '{$this->ms_user_id}', now(), " : '').
							 "{$n_obj_version_id}, {$n_pic_version_id}, {$n_pic_sub_version_id})";

		if ( ! CSql::query_and_free($s_update,0,__FILE__,__LINE__) )
			if( ! CSql::query_and_free($s_insert,0,__FILE__,__LINE__) )
				if ( ! $this->mb_mod )
					return $this->tellUser(__LINE__, MSG_DEFAULT_FAILLED);

		$this->mn_def_edit_id = CSql::query_and_fetch1("SELECT def_edit_id ".
														 "FROM pic_def_submit ".
														"WHERE obj_id = {$this->mn_obj_id} ".
														  "and obj_type = '{$this->mc_obj_type}' ".
														  "and disposition_cd = '-' ".
														  "and proposer_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);

		$this->ma_data = true;
	}

	function validateOpen()
	{
		if ( ! $this->mn_pic_edit_id )
			$this->mn_pic_edit_id = CSql::query_and_fetch1("SELECT max(pic_edit_id) ".
															 "FROM pic_submit ".
															"WHERE proposer_id = '{$this->ms_user_id}' ".
															  "and request_cd = 'E' ".
															  "and pic_id = {$this->mn_pic_id} ".
															  "and disposition_cd = '-'",0,__FILE__,__LINE__);

		if ( $this->mn_pic_edit_id )
		{
			$rr = "SELECT uploaded_pic, pic_name, pic_edit_id, request_cd, disposition_cd, proposer_id, proposed_tm, ".
						 "updated_tm, reviewer_id, reviewed_tm, pic_type, pic_type x_pic_type, transforms, caption, ".
						 "caption x_caption, copy_holder, copy_holder x_copy_holder, copy_year, copy_year x_copy_year, ".
						 "suitability_cd, suitability_cd x_suitability_cd, pic_dx, pic_dy, proposer_notes, reviewer_notes ".
					"FROM pic_submit ".
				   "WHERE pic_edit_id = {$this->mn_pic_edit_id}".
					   ( $this->mb_mod ? '' : " and proposer_id = '{$this->ms_user_id}'");
		}
		else
		{
			$rr = "SELECT '-' uploaded_pic, pic_name, 0 pic_edit_id, 'E' request_cd, '-' disposition_cd, '{$this->ms_user_id}' proposer_id, now() proposed_tm, ".
						 "now() updated_tm, '-' reviewer_id, NULL reviewed_tm, pic_type, pic_type x_pic_type, transforms, caption, ".
						 "caption x_caption, copy_holder, copy_holder x_copy_holder, copy_year, copy_year x_copy_year, ".
						 "suitability_cd, suitability_cd x_suitability_cd, pic_dx, pic_dy, '-' proposer_notes, '-' reviewer_notes ".
					"FROM pic ".
				   "WHERE pic_id = '{$this->mn_pic_id}'";
		}

		if ( ! ($this->ma_data = CSql::query_and_fetch($rr,0,__FILE__,__LINE__)) ) return;
		decodeTransforms('', $this->ma_data, $this->ma_data['transforms']);

		$this->ma_data[$this->ms_col_DVD_ID]	= $this->mn_obj_id;
		$this->ma_data['x_img_treatment']		= $this->ma_data['img_treatment'];
		$this->ma_data['x_rot_degrees']			= $this->ma_data['rot_degrees'];
		$this->ma_data['x_rot_degrees_x']		= $this->ma_data['rot_degrees_x'];
		$this->ma_data['x_crop_fuzz']			= $this->ma_data['crop_fuzz'];
		$this->ma_data['x_crop_x1']				= $this->ma_data['crop_x1'];
		$this->ma_data['x_crop_x2']				= $this->ma_data['crop_x2'];
		$this->ma_data['x_crop_y1']				= $this->ma_data['crop_y1'];
		$this->ma_data['x_crop_y2']				= $this->ma_data['crop_y2'];
		$this->ma_data['x_black_pt']			= $this->ma_data['black_pt'];
		$this->ma_data['x_white_pt']			= $this->ma_data['white_pt'];
		$this->ma_data['x_gamma']				= $this->ma_data['gamma'];
		$this->mb_review_sub					= $this->mb_mod && $this->ma_data['proposer_id'] != $this->ms_user_id;

		switch ( $this->ma_data['request_cd'] )
		{
		case 'N':
		case 'R':
			$s_pic_name			= $this->ma_data['uploaded_pic'];
			$this->ms_base_pic	= "/uploads/{$s_pic_name}.jpg";
			$this->ms_bord_pic	= "/uploads/{$s_pic_name}-bord.jpg";
			$this->ms_prev_pic	= "/uploads/{$s_pic_name}-prev.jpg";
			$this->ms_act		= $this->ma_data['request_cd'] == 'N' ? 'new' : 'rep';
			break;
		case 'E':
			$s_pic_name			= $this->ma_data['pic_name'];
			$s_dir				= getPicDir($s_pic_name);
			$this->ms_base_pic	= "/p6/{$s_dir}/{$s_pic_name}.jpg";
			$this->ms_bord_pic	= "/p4/{$s_dir}/{$s_pic_name}.jpg";
			$this->ms_prev_pic	= $this->mb_review_sub			? "/uploads/". sprintf('%06d',$this->mn_pic_edit_id) ."-prev.jpg" : (
//								  $this->ma_data['uploaded_pic'] == '-' ? "http://dv1.us/p1/{$s_dir}/{$s_pic_name}.jpg"
								  $this->ma_data['uploaded_pic'] == '-' ? getPicLocation($s_pic_name,false) . "/{$s_pic_name}.jpg"
																		: "/uploads/{$s_pic_name}-prev.jpg");
			$this->ms_act		= 'edit';
			break;
		case 'D':
			$s_pic_name			= $this->ma_data['pic_name'];
			$s_dir				= getPicDir($s_pic_name);
			$this->ms_base_pic	= "/p6/{$s_dir}/{$s_pic_name}.jpg";
			$this->ms_bord_pic	= "/p4/{$s_dir}/{$s_pic_name}.jpg";
//			$this->ms_prev_pic	= "http://dv1.us/p1/{$s_dir}/{$s_pic_name}.jpg";
			$this->ms_prev_pic	= getPicLocation($s_pic_name,false) . "/{$s_pic_name}.jpg";
			$this->ms_act		= 'des';
			break;
		}
	}

	function validateUpload()
	{
		if ( ! $this->ms_seed ) return;
		$this->delPreviousSeed();

		switch ( $this->ms_act )
		{
		case 'new':
			$s_pic_name    = '-';
			$s_request_cd  = 'N';
			$n_pic_id      = 0;
			$n_version     = 0;
			$n_sub_version = 0;
			break;
		case 'rep':
			$s_pic_name    = $this->ms_replace;
			if ( ! $s_pic_name || ! ($rr = CSql::query_and_fetch("SELECT pic_id, version_id FROM pic WHERE pic_name = '{$s_pic_name}'",0,__FILE__,__LINE__)) )
			return $this->tellUser(__LINE__, MSG_BAD_REPLACE);
			$s_request_cd  = 'R';
			$n_pic_id      = $rr['pic_id'];
			$n_version     = $rr['version_id'] + 1;
			$n_sub_version = 0;
			break;
		case 'edit':
			$s_pic_name    = $this->ms_replace;
			if ( ! $s_pic_name || ! ($rr = CSql::query_and_fetch("SELECT pic_id, version_id, sub_version_id FROM pic WHERE pic_name = '{$s_pic_name}'",0,__FILE__,__LINE__)) )
			return $this->tellUser(__LINE__, MSG_BAD_REPLACE);
			$s_request_cd  = 'E';
			$n_pic_id      = $rr['pic_id'];
			$n_version     = $rr['version_id'];
			$n_sub_version = $rr['sub_version_id'] + 1;
			break;
		}

		if ( ! ($this->mn_pic_edit_id  = $this->saveAndGetPicEditId($n_pic_id, $s_pic_name, $s_request_cd, $n_version, $n_sub_version)) )
			return $this->tellUser(__LINE__, MSG_BAD_REPLACE);

		$s_uploaded_pic = sprintf("%06d", $this->mn_pic_edit_id);
		if ( ! $this->renUploadedPicTo($s_uploaded_pic) )
		{
			CSql::query_and_free("DELETE FROM pic_submit WHERE pic_edit_id = {$this->mn_pic_edit_id} and proposer_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
			return;
		}

		CSql::query_and_free("UPDATE pic_submit ".
								"SET uploaded_pic = '{$s_uploaded_pic}', pic_dx = {$this->mn_pic_dx}, pic_dy = {$this->mn_pic_dy} ".
							  "WHERE pic_edit_id = {$this->mn_pic_edit_id} and proposer_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);

		if ( ($this->ma_data = CSql::query_and_fetch("SELECT pic_edit_id, request_cd, disposition_cd, proposer_id, proposed_tm, updated_tm, reviewer_id, reviewed_tm, ".
															"pic_type, pic_type x_pic_type, transforms, caption, caption x_caption, copy_holder, copy_holder x_copy_holder, ".
															"copy_year, copy_year x_copy_year, suitability_cd, suitability_cd x_suitability_cd, pic_dx, pic_dy, ".
															"proposer_notes, reviewer_notes ".
													   "FROM pic_submit ".
													  "WHERE pic_edit_id = {$this->mn_pic_edit_id} and proposer_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__)) )
		{
			$this->ma_data[$this->ms_col_DVD_ID]= $this->mn_obj_id;
			$this->ma_data['x_img_treatment']	= $this->ma_data['img_treatment']	= 'O';
			$this->ma_data['x_rot_degrees']		= $this->ma_data['rot_degrees']		= '0';
			$this->ma_data['x_rot_degrees_x']	= $this->ma_data['rot_degrees_x']	= '0';
			$this->ma_data['x_crop_fuzz']		= $this->ma_data['crop_fuzz']		= '0';
			$this->ma_data['x_crop_x1']			= $this->ma_data['crop_x1']			= '0';
			$this->ma_data['x_crop_x2']			= $this->ma_data['crop_x2']			= '0';
			$this->ma_data['x_crop_y1']			= $this->ma_data['crop_y1']			= '0';
			$this->ma_data['x_crop_y2']			= $this->ma_data['crop_y2']			= '0';
			$this->ma_data['x_black_pt']		= $this->ma_data['black_pt']		= '0';
			$this->ma_data['x_white_pt']		= $this->ma_data['white_pt']		= '100';
			$this->ma_data['x_gamma']			= $this->ma_data['gamma']			= '1.0';
		}
	}

	function isObjEditIdBad()
	{
		if ( $this->mn_obj_edit_id )
		{
			$n_dvd = CSql::query_and_fetch1("SELECT dvd_id FROM dvd_submit d WHERE d.edit_id = {$this->mn_obj_edit_id}",0,__FILE__,__LINE__);
			return $this->mn_obj_id != $n_dvd;
		}
		return false;
	}

	function validatePreview()
	{
		if ( $this->isObjEditIdBad() ) return $this->tellUser(__LINE__, MSG_BAD_DVD_EDIT_ID);
		$this->mb_is_preview = $this->validateSavePrev($this->ms_act, $this->mn_pic_id, $this->mn_pic_edit_id, false);
	}

	function validateSave()
	{
		if ( $this->isObjEditIdBad() ) return $this->tellUser(__LINE__, MSG_BAD_DVD_EDIT_ID);
		if ( $this->ms_act == 'asnew' )
		{
			CSql::query_and_free("UPDATE pic_submit SET pic_id = 0, request_cd = 'N' WHERE pic_edit_id = {$this->mn_pic_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__);
		}

		// get and validate proposed transformation values
		$this->ma_data[$this->ms_col_DVD_ID]	= $this->mn_obj_id;
		$this->ma_data['img_treatment']			= dvdaf_getvalue('n_p_img_treatment' ,DVDAF_POST          ,1); unset($_POST['n_p_img_treatment' ]);
		$this->ma_data['rot_degrees']			= dvdaf_getvalue('n_p_rot_degrees'   ,DVDAF_POST|DVDAF_FLOAT); unset($_POST['n_p_rot_degrees'   ]);
		$this->ma_data['rot_degrees_x']			= dvdaf_getvalue('n_p_rot_degrees_x' ,DVDAF_POST|DVDAF_INT  ); unset($_POST['n_p_rot_degrees_x' ]);
		$this->ma_data['crop_fuzz']				= dvdaf_getvalue('n_p_crop_fuzz'     ,DVDAF_POST|DVDAF_INT  ); unset($_POST['n_p_crop_fuzz'     ]);
		$this->ma_data['crop_x1']				= dvdaf_getvalue('n_p_crop_x1'       ,DVDAF_POST|DVDAF_INT  ); unset($_POST['n_p_crop_x1'       ]);
		$this->ma_data['crop_x2']				= dvdaf_getvalue('n_p_crop_x2'       ,DVDAF_POST|DVDAF_INT  ); unset($_POST['n_p_crop_x2'       ]);
		$this->ma_data['crop_y1']				= dvdaf_getvalue('n_p_crop_y1'       ,DVDAF_POST|DVDAF_INT  ); unset($_POST['n_p_crop_y1'       ]);
		$this->ma_data['crop_y2']				= dvdaf_getvalue('n_p_crop_y2'       ,DVDAF_POST|DVDAF_INT  ); unset($_POST['n_p_crop_y2'       ]);
		$this->ma_data['black_pt']				= dvdaf_getvalue('n_p_black_pt'      ,DVDAF_POST|DVDAF_INT  ); unset($_POST['n_p_black_pt'      ]);
		$this->ma_data['white_pt']				= dvdaf_getvalue('n_p_white_pt'      ,DVDAF_POST|DVDAF_INT  ); unset($_POST['n_p_white_pt'      ]);
		$this->ma_data['gamma']					= dvdaf_getvalue('n_p_gamma'         ,DVDAF_POST|DVDAF_FLOAT); unset($_POST['n_p_gamma'         ]);
		$this->ma_data['proposer_notes']		= dvdaf_textarea2db(dvdaf_getvalue('n_p_proposer_notes',DVDAF_POST), 1000); unset($_POST['n_p_proposer_notes']);
		$this->ma_data['reviewer_notes']		= dvdaf_textarea2db(dvdaf_getvalue('n_p_reviewer_notes',DVDAF_POST), 1000); unset($_POST['n_p_reviewer_notes']);

		if ( $this->mn_pic_edit_id )
			$a_pic_submit = CSql::query_and_fetch("SELECT proposer_id, proposed_tm, updated_tm, proposer_notes, proposer_id FROM pic_submit WHERE pic_edit_id = {$this->mn_pic_edit_id}",0,__FILE__,__LINE__);
		else
			$a_pic_submit = null;
				 
		$this->mb_review_sub = $this->mb_mod && $a_pic_submit && $a_pic_submit['proposer_id'] != $this->ms_user_id;
		$s_transforms		 = encodeTransforms('', $this->ma_data);
		$s_seed				 = date('Y-m-d H:i:s '). dvdaf_getvalue('REMOTE_ADDR', DVDAF_SERVER);
		$s_pic_name			 = '-';
		$n_version			 = 0;
		$n_sub_version		 = 0;

		if ( strpos('BHROKF',$this->ma_data['img_treatment']) === false )
			$this->ma_data['img_treatment'] = 'O';
		unset($_POST['n_p_pic_edit_id']);

		if ( ! $this->mn_pic_edit_id || $this->mb_review_sub )
		{
			if ( $this->mn_pic_id )
			{
				if ( ! ($rr = CSql::query_and_fetch("SELECT pic_name, version_id, sub_version_id FROM pic WHERE pic_id = '{$this->mn_pic_id}'",0,__FILE__,__LINE__)) )
					return $this->tellUser(__LINE__, MSG_BAD_REPLACE);
				$s_pic_name		= $rr['pic_name'];
				$n_version		= $rr['version_id'];
				$n_sub_version	= $rr['sub_version_id'];
			}

			$this->validateInput($s_change, $s_values, DVDAF_INSERT, false);
			if ( $a_pic_submit )
			{
				$s_change .= "proposer_id, proposed_tm, updated_tm, proposer_notes, ";
				$s_values .= "'{$a_pic_submit['proposer_id']}', '{$a_pic_submit['proposed_tm']}', '{$a_pic_submit['updated_tm']}', '{$a_pic_submit['proposer_notes']}', ";
			}
			else
			{
				$s_change .= "proposer_id, proposed_tm, updated_tm, proposer_notes, ";
				$s_values .= "'{$this->ms_user_id}', now(), now(), '{$this->ma_data['proposer_notes']}', ";
			}
			if ( $this->mb_mod )
			{
				$s_change .= "disposition_cd, reviewer_id, reviewed_tm, ";
				$s_values .= "'A', '{$this->ms_user_id}', now(), ";
			}
			$s_change .= "pic_id, pic_name, request_cd, hist_version_id, hist_sub_version_id, ".
						 "creation_seed, transforms, obj_edit_id, obj_id, obj_type";
			$s_values .= "{$this->mn_pic_id}, '{$s_pic_name}', '".($this->mn_pic_id ? 'E' : 'N')."', {$n_version}, {$n_sub_version}, ".
						 "'{$s_seed}', '{$s_transforms}', {$this->mn_obj_edit_id}, {$this->mn_obj_id}, '{$this->mc_obj_type}'";

			if ( ! $this->mn_pic_edit_id )
			{
				CSql::query_and_free("INSERT INTO pic_submit ({$s_change}) VALUES ({$s_values})",0,__FILE__,__LINE__);
				if ( ! ($this->mn_pic_edit_id = CSql::query_and_fetch1("SELECT pic_edit_id FROM pic_submit WHERE proposer_id = '{$this->ms_user_id}' and creation_seed = '{$s_seed}'",0,__FILE__,__LINE__)) )
					return $this->tellUser(__LINE__, MSG_BAD_REPLACE);
			}
			if ( $this->mb_review_sub )
			{
				// if moderating save as negative so not to change the original submission
				CSql::query_and_free("DELETE FROM pic_submit WHERE pic_edit_id = -{$this->mn_pic_edit_id}",0,__FILE__,__LINE__);
				CSql::query_and_free("INSERT INTO pic_submit ({$s_change}, pic_edit_id) VALUES ({$s_values}, -{$this->mn_pic_edit_id})",0,__FILE__,__LINE__);
				CSql::query_and_free("UPDATE pic_submit SET reviewer_notes = '{$this->ma_data['reviewer_notes']}' WHERE pic_edit_id = {$this->mn_pic_edit_id}",0,__FILE__,__LINE__);
			}
		}
		else
		{
			// update old values from the database as what is in DVDAF_POST may not agree with it because of the preview functionality
			if ( ($rr = CSql::query_and_fetch("SELECT caption, copy_holder, copy_year, pic_type, suitability_cd ".
												"FROM pic_submit ".
											   "WHERE pic_edit_id = {$this->mn_pic_edit_id}",0,__FILE__,__LINE__)) )
			{
				$_POST['o_p_caption'		] = $rr['caption'];
				$_POST['o_p_copy_holder'	] = $rr['copy_holder'];
				$_POST['o_p_copy_year'		] = $rr['copy_year'];
				$_POST['o_p_pic_type'		] = $rr['pic_type'];
				$_POST['o_p_suitability_cd'	] = $rr['suitability_cd'];
			}

			$this->validateInput($s_change, $s_values, DVDAF_UPDATE, false);
			$s_change .= ( $this->mb_mod
						   ?
							 "disposition_cd = 'A', ".
							 "reviewer_id = '{$this->ms_user_id}', ".
							 "reviewed_tm = now(), ".
							 "reviewer_notes = '{$this->ma_data['reviewer_notes']}', ".
							 "proposer_notes = '{$this->ma_data['proposer_notes']}', "
						   :
							 "proposer_notes = '{$this->ma_data['proposer_notes']}', "
						 ).
						 "transforms = '{$s_transforms}', ".
						 "updated_tm = now() ";

			CSql::query_and_free("UPDATE pic_submit SET {$s_change} WHERE pic_edit_id = {$this->mn_pic_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__);
		}

		if ( $this->mb_mod )
		{
			$this->validateSaveMod($s_seed);
		}
		else
		{
			if ( $this->mn_obj_edit_id )
			{
				if ( ($s_proposer_notes = CSql::query_and_fetch1("SELECT proposer_notes FROM {$this->ms_tbl_DVD_SUB} WHERE edit_id = {$this->mn_obj_edit_id} and disposition_cd <> '-'",0,__FILE__,__LINE__)) )
				{
					if ( $s_proposer_notes == '-' )
						$s_proposer_notes = 'AUTO GEN MSG - new picture uploaded';
					else
						$s_proposer_notes = dvdaf_translatestring("AUTO GEN MSG - new picture uploaded<br />{$s_proposer_notes}", DVDAF_NO_TRANSLATION, 1000);
				}
				CSql::query_and_free("UPDATE {$this->ms_tbl_DVD_SUB} ".
										"SET disposition_cd = '-', ". ($s_proposer_notes ? "proposer_notes = '{$s_proposer_notes}', " : ''). "updated_tm = now() ".
									  "WHERE edit_id = {$this->mn_obj_edit_id} and disposition_cd <> '-'",0,__FILE__,__LINE__);
			}
			$this->mb_pic_saved = $this->validateSavePrev($this->ms_act, $this->mn_pic_id, $this->mn_pic_edit_id, true);
		}

		CSql::query_and_free("UPDATE dvdaf_user_2 SET last_submit_tm = now() WHERE user_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
	}

	function validateSaveMod($s_seed)
	{
		if ( ! $this->mb_mod ) return;

		$n_pic_edit_id	  = $this->mn_pic_edit_id;
		$s_transforms_ori = '';

		if ( $this->mb_review_sub )
		{
			$s_transforms_ori = CSql::query_and_fetch1("SELECT transforms FROM pic_submit WHERE pic_edit_id = {$n_pic_edit_id}",0,__FILE__,__LINE__);
			$n_pic_edit_id	  = -$n_pic_edit_id;
		}

		if ( ! ($rr = CSql::query_and_fetch("SELECT pic_id, pic_name, request_cd, uploaded_pic, transforms FROM pic_submit WHERE pic_edit_id = {$this->mn_pic_edit_id}",0,__FILE__,__LINE__)) )
			return $this->tellUser(__LINE__, MSG_NO_DATA);

		$this->mn_pic_id      = $rr['pic_id'];
		$s_pic_name           = $rr['pic_name'];
		$s_request_cd         = $rr['request_cd'];
		$s_uploaded_pic       = $rr['uploaded_pic'];
		$s_transforms_mod     = $rr['transforms'];
		$s_old_pic_name       = '-';
		$n_old_version_id     = 0;
		$n_old_sub_version_id = 0;
		$n_version            = 0;
		$n_sub_version        = 0;

		switch ( $s_request_cd )
		{
		case 'N':
			// insert new record
			$s_pic_name = sprintf('%06d', $this->mn_obj_id). "-{$this->mc_pic_prefix}";
			$s_used     = ','. CSql::query_and_fetch1("SELECT GROUP_CONCAT(SUBSTR(pic_name,INSTR(pic_name,'-')+2)) FROM pic WHERE pic_name like '{$s_pic_name}%'",0,__FILE__,__LINE__). ',';
			//for ( $i = 0 ; strpos($s_used, ",{$i},") === false ; $i++ ) ;
			for ( $i = 0 ; strpos($s_used, ",{$i},") !== false ; $i++ ) ;
				CSql::query_and_free("INSERT INTO pic (pic_name, creation_seed, pic_uploaded_tm) ".
									 "SELECT '{$s_pic_name}{$i}', '{$s_seed}', now() FROM (SELECT 1) a WHERE not exists (SELECT 1 FROM pic where pic_name = '{$s_pic_name}{$i}')",0,__FILE__,__LINE__);
			if ( ! ($rr = CSql::query_and_fetch("SELECT pic_id, pic_name FROM pic WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__)) )
			{
				CSql::query_and_free("INSERT INTO pic (pic_name, creation_seed, pic_uploaded_tm) ".
									 "SELECT CONCAT('{$s_pic_name}',COALESCE(MAX(1+SUBSTR(pic_name,INSTR(pic_name,'-')+2)),0)), '{$s_seed}', now() FROM pic WHERE pic_name LIKE '{$s_pic_name}%'",0,__FILE__,__LINE__);
				if ( ! ($rr = CSql::query_and_fetch("SELECT pic_id, pic_name FROM pic WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__)) )
					return $this->tellUser(__LINE__, MSG_NO_DATA);
			}
			$this->mn_pic_id      = $rr['pic_id'];
			$s_pic_name           = $rr['pic_name'];
			break;

		case 'E':
		case 'R':
			// figure versions
			if ( ! ($rr = CSql::query_and_fetch("SELECT pic_name, version_id, sub_version_id FROM pic WHERE pic_id = '{$this->mn_pic_id}'",0,__FILE__,__LINE__)) )
				return $this->tellUser(__LINE__, MSG_UNKNOWN_PICNAME);
			$s_pic_name           = $rr['pic_name'];
			$n_old_version_id     = intval($rr['version_id']);
			$n_old_sub_version_id = intval($rr['sub_version_id']);
			switch ( $s_request_cd )
			{
			case 'E':
				$s_uploaded_pic = '-';
				$n_version      = $n_old_version_id;
				$n_sub_version  = $n_old_sub_version_id + 1;
				break;
			case 'R':
				$s_old_pic_name = $s_pic_name;
				$n_version      = $n_old_version_id + 1;
				$n_sub_version  = 0;
				break;
			}
			// snapshot history
			CSql::query_and_free("INSERT INTO pic_hist (pic_id, version_id, sub_version_id, pic_name, pic_type, transforms, caption, copy_holder, copy_year, suitability_cd, ".
										"pic_dx, pic_dy, pic_uploaded_tm, pic_uploaded_by, pic_edited_tm, pic_edited_by, pic_verified_tm, pic_verified_by, verified_version, pic_edit_id) ".
								 "SELECT pic_id, version_id, sub_version_id, pic_name, pic_type, transforms, caption, copy_holder, copy_year, suitability_cd, ".
										"pic_dx, pic_dy, pic_uploaded_tm, pic_uploaded_by, pic_edited_tm, pic_edited_by, pic_verified_tm, pic_verified_by, verified_version, pic_edit_id ".
								   "FROM pic ".
								  "WHERE pic_id = {$this->mn_pic_id}",0,__FILE__,__LINE__);
			break;
		}

		if ( $s_pic_name == '-' || ! $s_pic_name )
			return $this->tellUser(__LINE__, MSG_UNKNOWN_PICNAME);

		// process picture
		$s_cmd = $this->cmdSavTransform($s_old_pic_name, $n_old_version_id, $n_old_sub_version_id, $s_request_cd == 'R', $s_uploaded_pic, $s_pic_name, '', $this->ma_data);
		dvdaf_exec($s_cmd, $this->ma_cmdout, $this->mn_cmdret);

		// propagate picture (no longer propagating, we only have one server)
		//$s_temp = '/var/www/html/uploads/filmaf.'.CTime::get_time().'.tmp';
		//copy("http://dv1.us/utils/propagate.php?pic={$s_pic_name}", $s_temp);
		//dvdaf_exec("rm -f {$s_temp}", $this->ma_cmdout, $this->mn_cmdret);

		// update pic table
		$n_abs_pic_edit_id = $n_pic_edit_id < 0 ? -$n_pic_edit_id : $n_pic_edit_id;
		CSql::query_and_free("UPDATE pic, pic_submit ".
								"SET pic.version_id = {$n_version}, ".
									"pic.sub_version_id = {$n_sub_version}, ".
									"pic.pic_type = pic_submit.pic_type, ".
									"pic.transforms = pic_submit.transforms, ".
									"pic.caption = pic_submit.caption, ".
									"pic.copy_holder = pic_submit.copy_holder, ".
									"pic.copy_year = pic_submit.copy_year, ".
									"pic.suitability_cd = pic_submit.suitability_cd, ".
									"pic.pic_dx = pic_submit.pic_dx, ".
									"pic.pic_dy = pic_submit.pic_dy, ".
									( $s_request_cd == 'N' || $s_request_cd == 'R'
									  ? "pic.pic_uploaded_tm = pic_submit.proposed_tm, pic.pic_uploaded_by = pic_submit.proposer_id, "
									  : '').
									( $s_transforms_mod == $s_transforms_ori
									  ? "pic_edited_tm = pic_submit.proposed_tm, pic.pic_edited_by = pic_submit.proposer_id, "
									  : "pic_edited_tm = pic_submit.reviewed_tm, pic.pic_edited_by = pic_submit.reviewer_id, ").
									"pic.pic_verified_tm = pic_submit.reviewed_tm, ".
									"pic.pic_verified_by = '{$this->ms_user_id}', ".
									"pic.verified_version = {$n_version}, ".
									"pic.pic_edit_id = {$n_abs_pic_edit_id} ".
							  "WHERE pic.pic_name = '{$s_pic_name}' ".
								"and pic_submit.pic_edit_id = {$n_pic_edit_id}",0,__FILE__,__LINE__);
		if ( $n_pic_edit_id < 0 )
		{
			CSql::query_and_free("DELETE from pic_submit WHERE pic_edit_id = {$n_pic_edit_id}",0,__FILE__,__LINE__);
			CSql::query_and_free("UPDATE pic_submit SET disposition_cd = 'A', reviewer_id = '{$this->ms_user_id}', reviewed_tm = now() WHERE pic_edit_id = {$this->mn_pic_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__);
		}

		// update picture relatioship tables
		if ( $this->mn_pic_id )
		{
			CSql::query_and_free("UPDATE pic_submit, pic ".
									"SET pic_submit.pic_id = {$this->mn_pic_id}, ".
										"pic_submit.pic_name = pic.pic_name ".
								  "WHERE pic.pic_id = {$this->mn_pic_id} ".
									"and pic_submit.pic_edit_id = {$this->mn_pic_edit_id}",0,__FILE__,__LINE__);
		}
		$s_status = dvdaf_getvalue('n_p_pic_type', DVDAF_POST, 1) == 'P' ? 'P' : 'Y';
		if ( $s_request_cd == 'N' )
		{
			CSql::query_and_free("INSERT INTO {$this->ms_tbl_DVD_PIC} ({$this->ms_col_DVD_ID}, pic_id, sort_order, link_created_tm, link_created_by) ".
								 "SELECT {$this->mn_obj_id}, {$this->mn_pic_id}, IFNULL(max(sort_order)+1,10000), now(), '{$this->ms_user_id}' ".
								   "FROM {$this->ms_tbl_DVD_PIC} ".
								  "WHERE {$this->ms_col_DVD_ID} = {$this->mn_obj_id}",0,__FILE__,__LINE__);
			CSql::query_and_free("UPDATE {$this->ms_tbl_DVD} ".
									"SET pic_name = '{$s_pic_name}', ".
										"pic_status = '{$s_status}' ".
								  "WHERE {$this->ms_col_DVD_ID} = {$this->mn_obj_id} ".
									"and pic_name = '-'",0,__FILE__,__LINE__);
			CSql::query_and_free("UPDATE {$this->ms_tbl_DVD} ".
									"SET pic_count = (SELECT count(*) FROM {$this->ms_tbl_DVD_PIC} WHERE {$this->ms_col_DVD_ID} = {$this->mn_obj_id}) ".
								  "WHERE {$this->ms_col_DVD_ID} = {$this->mn_obj_id}",0,__FILE__,__LINE__);
		}
		else
		{
			CSql::query_and_free("UPDATE {$this->ms_tbl_DVD} SET pic_status = '{$s_status}' WHERE {$this->ms_col_DVD_ID} = {$this->mn_obj_id} and pic_name = '{$s_pic_name}'",0,__FILE__,__LINE__);
		}

		$this->ms_prev_pic  = $s_pic_name;
		$this->mb_pic_saved = true;
	}

	function validateSavePrev($s_act, $n_pic_id, $n_pic_edit_id, $b_save)
	{
		if ( $s_act == 'new' )
			$rr = "SELECT uploaded_pic, pic_name, pic_edit_id, request_cd, disposition_cd, proposer_id, proposed_tm, updated_tm, reviewer_id, reviewed_tm, ".
						 "pic_type x_pic_type, transforms x_transforms, caption x_caption, copy_holder x_copy_holder, copy_year x_copy_year, ".
						 "suitability_cd x_suitability_cd, pic_dx, pic_dy, '' b_transforms, transforms saved_transforms, proposer_notes, reviewer_notes ".
					"FROM pic_submit ".
				   "WHERE pic_edit_id = {$n_pic_edit_id} ".
				  ( $this->mb_mod ? '' : " and proposer_id = '{$this->ms_user_id}'");
		else
			if ( $n_pic_edit_id )
			$rr = "SELECT b.uploaded_pic, b.pic_name, b.pic_edit_id, b.request_cd, b.disposition_cd, b.proposer_id, b.proposed_tm, b.updated_tm, b.reviewer_id, b.reviewed_tm, ".
						 "a.pic_type x_pic_type, ".($s_act == 'edit' ? "a.transforms" : "''")." x_transforms, a.caption x_caption, a.copy_holder x_copy_holder, a.copy_year x_copy_year, ".
						 "a.suitability_cd x_suitability_cd, b.pic_dx, b.pic_dy, b.transforms b_transforms, b.transforms saved_transforms, b.proposer_notes, b.reviewer_notes ".
					"FROM pic_submit b LEFT JOIN pic a ON a.pic_id = b.pic_id ".
				   "WHERE b.pic_edit_id = {$n_pic_edit_id} ".
						  ( $this->mb_mod ? '' : " and proposer_id = '{$this->ms_user_id}'");
			else
			$rr = "SELECT '-' uploaded_pic, pic_name, 0 pic_edit_id, 'E' request_cd, '-' disposition_cd, '{$this->ms_user_id}' proposer_id, now() proposed_tm, now() updated_tm, '-' reviewer_id, NULL reviewed_tm, ".
						 "pic_type x_pic_type, transforms x_transforms, caption x_caption, copy_holder x_copy_holder, copy_year x_copy_year, ".
						 "suitability_cd x_suitability_cd, pic_dx, pic_dy, transforms b_transforms, '' saved_transforms, '' proposer_notes, '' reviewer_notes ".
						 // 'saved_transforms' should never be used from here because if there is no n_pic_edit_id it has not been saved
					"FROM pic ".
				   "WHERE pic_id = '{$n_pic_id}'";

		if ( ($this->ma_data = CSql::query_and_fetch($rr,0,__FILE__,__LINE__)) )
		{
			switch ( $s_act )
			{
			case 'new':
			case 'rep':
				$s_pic_name			= $this->ma_data['uploaded_pic'];
				$this->ms_base_pic	= "/uploads/{$s_pic_name}.jpg";
				$this->ms_bord_pic	= "/uploads/{$s_pic_name}-bord.jpg";
				break;
			case 'edit':
				$s_pic_name			= $this->ma_data['pic_name'];
				$s_dir				= getPicDir($s_pic_name);
				$this->ms_base_pic	= "/p6/{$s_dir}/{$s_pic_name}.jpg";
				$this->ms_bord_pic	= "/p4/{$s_dir}/{$s_pic_name}.jpg";
				break;
			}

			if ( $b_save )
			{
				$this->ms_prev_pic				 = '/uploads/'. sprintf('%06d', $n_pic_edit_id) .'-prev.jpg';
				$this->ma_data['pic_type']		 = $this->ma_data['x_pic_type'];
				$this->ma_data['caption']		 = $this->ma_data['x_caption'];
				$this->ma_data['copy_holder']	 = $this->ma_data['x_copy_holder'];
				$this->ma_data['copy_year']		 = $this->ma_data['x_copy_year'];
				$this->ma_data['suitability_cd'] = $this->ma_data['x_suitability_cd'];
				decodeTransforms('', $this->ma_data, $this->ma_data['saved_transforms']);
			}
			else
			{
				$this->ms_prev_pic				 = "/uploads/prev-{$this->ms_user_id}.jpg";
				$this->ma_data['pic_type']		 = dvdaf_getvalue('n_p_pic_type'      ,DVDAF_POST          ,1);
				$this->ma_data['caption']		 = dvdaf_getvalue('n_p_caption'       ,DVDAF_POST            );
				$this->ma_data['copy_holder']	 = dvdaf_getvalue('n_p_copy_holder'   ,DVDAF_POST            );
				$this->ma_data['copy_year']		 = dvdaf_getvalue('n_p_copy_year'     ,DVDAF_POST|DVDAF_INT  );
				$this->ma_data['suitability_cd'] = dvdaf_getvalue('n_p_suitability_cd',DVDAF_POST          ,1);
				$this->ma_data['img_treatment']	 = dvdaf_getvalue('n_p_img_treatment' ,DVDAF_POST          ,1);
				$this->ma_data['rot_degrees']	 = dvdaf_getvalue('n_p_rot_degrees'   ,DVDAF_POST|DVDAF_FLOAT);
				$this->ma_data['rot_degrees_x']	 = dvdaf_getvalue('n_p_rot_degrees_x' ,DVDAF_POST|DVDAF_INT  );
				$this->ma_data['crop_fuzz']		 = dvdaf_getvalue('n_p_crop_fuzz'     ,DVDAF_POST|DVDAF_INT  );
				$this->ma_data['crop_x1']		 = dvdaf_getvalue('n_p_crop_x1'       ,DVDAF_POST|DVDAF_INT  );
				$this->ma_data['crop_x2']		 = dvdaf_getvalue('n_p_crop_x2'       ,DVDAF_POST|DVDAF_INT  );
				$this->ma_data['crop_y1']		 = dvdaf_getvalue('n_p_crop_y1'       ,DVDAF_POST|DVDAF_INT  );
				$this->ma_data['crop_y2']		 = dvdaf_getvalue('n_p_crop_y2'       ,DVDAF_POST|DVDAF_INT  );
				$this->ma_data['black_pt']		 = dvdaf_getvalue('n_p_black_pt'      ,DVDAF_POST|DVDAF_INT  );
				$this->ma_data['white_pt']		 = dvdaf_getvalue('n_p_white_pt'      ,DVDAF_POST|DVDAF_INT  );
				$this->ma_data['gamma']			 = dvdaf_getvalue('n_p_gamma'         ,DVDAF_POST|DVDAF_FLOAT);
				$this->ma_data['proposer_notes'] = dvdaf_textarea2db(dvdaf_getvalue('n_p_proposer_notes',DVDAF_POST), 1000);
				$this->ma_data['reviewer_notes'] = dvdaf_textarea2db(dvdaf_getvalue('n_p_reviewer_notes',DVDAF_POST), 1000);
			}
			$this->ma_data[$this->ms_col_DVD_ID] = $this->mn_obj_id;
			decodeTransforms('x_', $this->ma_data, $this->ma_data['x_transforms']); // applied to existing pic
			decodeTransforms('b_', $this->ma_data, $this->ma_data['b_transforms']); // previously submitted

			$s_cmd = $this->cmdTryTransform($this->ms_base_pic, $this->ms_prev_pic, '', $this->ma_data);
			dvdaf_exec($s_cmd, $this->ma_cmdout, $this->mn_cmdret);

			return true;
		}
		return false;
	}

	function delPreviousSeed()
	{
		if ( ! $this->ms_seed ) return;

		$an_pic_edit_id  = array();
		$as_uploaded_pic = array();

		if ( ($rr = CSql::query("SELECT pic_edit_id, uploaded_pic FROM pic_submit WHERE proposer_id = '{$this->ms_user_id}' and creation_seed = '{$this->ms_seed}' and disposition_cd = '-'",0,__FILE__,__LINE__)) )
		{
			while ( ($ln = CSql::fetch($rr)) )
			{
				$an_pic_edit_id [] = intval($ln['pic_edit_id']);
				$as_uploaded_pic[] = $ln['uploaded_pic'] == '-' ? '' : $ln['uploaded_pic'];
			}
			CSql::free($rr);

			for ( $i = 0 ; $i < count($an_pic_edit_id) ; $i++ )
			{
				if ( $an_pic_edit_id [$i] ) CSql::query_and_free("DELETE FROM pic_submit WHERE pic_edit_id = {$an_pic_edit_id[$i]}",0,__FILE__,__LINE__);
				if ( $as_uploaded_pic[$i] ) dvdaf_exec("rm -f /var/www/html/uploads/{$as_uploaded_pic[$i]}*.jpg /var/www/html/uploads/{$as_uploaded_pic[$i]}*.gif", $this->ma_cmdout, $this->mn_cmdret);
			}
		}
	}

	function renUploadedPicTo($s_filename)
	{
		if ( ! $this->ms_seed ) return;
		if ( ! isset($_FILES['file']) ) return;

		$s_base     = '/var/www/html/uploads';
		$s_load_pic = $_FILES['file']['tmp_name'];

		if ( preg_match('/[^a-zA-Z0-9\/\.-]/', $s_load_pic) )
			return $this->tellUser(__LINE__, MSG_BAD_FILENAME);

		if ( $_FILES['file']['error'] != 0 )
		{
			switch ( $_FILES['file']['error'] )
			{
			case UPLOAD_ERR_INI_SIZE:	return $this->tellUser(__LINE__, MSG_UPLOAD_SIZE);
			case UPLOAD_ERR_PARTIAL:	return $this->tellUser(__LINE__, MSG_UPLOAD_PARTIAL);
			case UPLOAD_ERR_NO_FILE:	return $this->tellUser(__LINE__, MSG_UPLOAD_EMPTY);
			default:					return $this->tellUser(__LINE__, MSG_UPLOAD_ERROR);
			}
		}

		if ( ! $s_load_pic										) return $this->tellUser(__LINE__, MSG_UPLOAD_EMPTY);
		if ( $_FILES['file']['size'] <= 0						) return $this->tellUser(__LINE__, MSG_UPLOAD_EMPTY);
		if ( get_cfg_var('upload_tmp_dir')           != $s_base ) return $this->tellUser(__LINE__, MSG_UPLOAD_BAD_DIR);
		if ( substr($s_load_pic, 0, strlen($s_base)) != $s_base ) return $this->tellUser(__LINE__, MSG_UPLOAD_BASE_DIR); // check chmod 777 /var/www/html/uploads

		// identify picture dimensions and rename it to std + jpg
		$s_base_pic = "/uploads/{$s_filename}.jpg";
		$s_bord_pic = "/uploads/{$s_filename}-bord.jpg";
		$s_prev_pic = "/uploads/{$s_filename}-prev.jpg";
		$s_cmd      = "/var/www/html/shell/process-uploaded {$s_load_pic} /var/www/html{$s_base_pic} /var/www/html{$s_bord_pic} /var/www/html{$s_prev_pic}";
		dvdaf_exec($s_cmd, $this->ma_cmdout, $this->mn_cmdret);
		//echo "<div>s_cmd = {$s_cmd}<br />". print_r($this->ma_cmdout). "<br />n_ret = {$this->mn_cmdret}<br /></div>";

		if ( count($this->ma_cmdout) == 1 )
		{
			$a_out = explode(' ', $this->ma_cmdout[0]);
			if ( count($a_out) == 3 )
			{
				$this->mn_pic_dx = intval($a_out[1]);
				$this->mn_pic_dy = intval($a_out[2]);

				switch ( $a_out[0] )
				{
				case 'SUCCESS':
					$this->ms_base_pic = $s_base_pic;
					$this->ms_bord_pic = $s_bord_pic;
					$this->ms_prev_pic = $s_prev_pic;
					return true;

				case 'SMALL':
					return $this->tellUser(__LINE__, MSG_TOO_SMALL);
				}
			}
		}
		return $this->tellUser(__LINE__, MSG_FAIL_TO_PROCESS);
	}

	function saveAndGetPicEditId($n_pic_id, $s_pic_name, $s_request_cd, $n_version, $n_sub_version)
	{
	CSql::query_and_free("INSERT INTO pic_submit (obj_edit_id, obj_id, obj_type, pic_id, request_cd, proposer_id, ".
								"proposed_tm, updated_tm, hist_version_id, hist_sub_version_id, pic_name, pic_type, creation_seed) ".
						 "VALUES ({$this->mn_obj_edit_id}, {$this->mn_obj_id}, '{$this->mc_obj_type}', {$n_pic_id}, '{$s_request_cd}', '{$this->ms_user_id}', ".
								"now(), now(), {$n_version}, {$n_sub_version}, '{$s_pic_name}', '{$this->mc_def_pic_type}', '{$this->ms_seed}')",0,__FILE__,__LINE__);
	return CSql::query_and_fetch1("SELECT pic_edit_id ".
									"FROM pic_submit ".
								   "WHERE proposer_id = '{$this->ms_user_id}' and disposition_cd = '-' and creation_seed = '{$this->ms_seed}'",0,__FILE__,__LINE__);
	}

	function cmdSavTransform($s_old_source, $n_old_version, $n_old_sub_version, $b_copy_p6, $s_new_source, $s_new_target, $s_prefix, &$a_transform)
	{
		$s = cmdTransforms($s_prefix, $a_transform);
		$b = $b_copy_p6 ? '1' : '0';
		return $s ? "/var/www/html/shell/sav-transform $s_old_source $n_old_version $n_old_sub_version $b {$s_new_source}.jpg $s_new_target $s" : '';
	}

	function cmdTryTransform($s_full_source, $s_full_target, $s_prefix, &$a_transform)
	{
		$s = cmdTransforms($s_prefix, $a_transform);
		return $s ? "/var/www/html/shell/try-transform /var/www/html{$s_full_source} /var/www/html{$s_full_target} $s": '';
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		if ( ! $this->ms_display_error && ! $this->ma_data )
			$this->tellUser(__LINE__, MSG_NO_DATA);

		if ( $this->ms_display_error )
		{
			echo $this->getMessageString(true, 'margin:20px 50px 20px 50px');
			$this->drawReturn();
			return;
		}

	//echo "3: {$this->ms_act} {$this->mb_pic_saved}<br />";
		switch ( $this->ms_act )
		{
		case 'des': $this->drawDeleteSubmission(); break;
		case 'dap':
		case 'del': $this->drawDelete();	   break;
		case 'wdr': $this->drawWithdraw();	   break;
		case 'rej': $this->drawReject();	   break;
		case 'def': $this->drawDefault();	   break;
		case 'new':
		case 'asnew':
		case 'rep':
		case 'edit':
			if ( $this->mb_pic_saved )
				$this->drawPicSaved();
			else
				$this->drawPicEdit();
			break;
		}
	}

	function drawDeleteSubmission()
	{
		if ( ! $this->mb_mod ) return;

		$s_rand     = microtime(true);
		$s_tmpl_top = dvdaf_parsetemplate      ("", $s_select, $s_from, $s_where, $s_sort, DVDAF_SHOW_PIC, DVDAF_SELECT_DVD, '', '', 0);
		$s_tmpl_mid = dvdaf_parsetemplateformat("", DVDAF_SHOW_PIC+1, DVDAF_SELECT_DVD, '', '', 0);

		echo  "<form id='myform' name='myform' method='post' action='{$this->ms_request_uri}'>".
			  "<table class='padded'>".
				"<tr>".
				  "<td>".
					"<table border='1' width='100%' class='padded' style='margin-top:10px'>".
					  "<tr><td class='x2' style='padding:4px'>Request Status</td></tr>".
					  "<tr>".
						"<td>";
						  dvdaf_getbrowserow($this->ma_data, $s_tmpl_top, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_DVD_ID_OK);
		echo			"</td>".
					  "</tr>".
					"</table>".
					"<table border='1' width='100%' class='padded' style='margin-top:14px'>".
					  "<tr><td class='x2' style='padding:4px'>Picture Information</td></tr>".
					  "<tr>".
						"<td>".
						  "<table cellspacing='4' width='100%'>".
							"<tr class='se'>";
							  dvdaf_getbrowserow($this->ma_data, $s_tmpl_mid, 0, $this->ms_user_id, DVDAF2_ECHO, 0, DVDAF4_ZERO_DVD_ID_OK);
		echo			      "</td>".
							"</tr>".
						  "</table>".
						"</td>".
					  "</tr>".
					"</table>".
				  "</td>".
				"</tr>".
				"<tr>".
				  "<td>".
					"<table border='1' width='100%' class='padded' style='margin-top:10px'>".
					  "<tr><td colspan='3' class='x2' style='padding:4px'>Picture Processing</td></tr>".
					  "<tr class='mc' style='text-align:center;font-weight:bold;font-size:12px'>".
						"<td style='padding:4px'>Uploaded</td>".
						"<td style='padding:4px'>Options</td>".
						"<td style='padding:4px'>As Shown</td>".
					  "</tr>".
					  "<tr>".
						"<td style='vertical-align:top'>".
						  "<img style='padding:5px' src='{$this->ms_bord_pic}?tm={$s_rand}' alt='' />".
						"</td>".
						"<td style='vertical-align:top;text-align:center'>".
						  "<input type='button' value='Close' onclick='window.close()' style='width:148px' /><br />".
						  "<input type='button' value='Reject Submission' onclick='PicEdit.reject()' style='width:148px' /><br />".
						  "<input type='button' value='Approve / Delete Pic' onclick='PicEdit.deleteApproval()' style='width:148px' />".
						"</td>".
						"<td style='vertical-align:top'>".
						  "<img style='padding:20px' src='{$this->ms_prev_pic}?tm={$s_rand}' alt='' />".
						"</td>".
					  "</tr>".
					"</table>".
				  "</td>".
				"</tr>".
			  "</table>".
			  "</form>";
	}

	function drawDelete()
	{
		$s_pic_name = $this->ms_pic_name ? $this->ms_pic_name : $this->mn_pic_id;
		if ( $this->mb_mod )
			if ( $this->mb_pic_deleted )
				$str = "Image {$s_pic_name} deleted.";
			else
				$str = "Association with image {$s_pic_name} deleted.";
		else
			$str = "Deletion request for image {$s_pic_name} submitted as request {$this->mn_pic_edit_id}.<br />&nbsp;<br />Thank you.";

		echo  "<div style='margin:20px;text-align:center'>{$str}</div>";
		$this->drawReturn();
	}

	function drawDefault()
	{
		$s_pic_name = $this->ms_pic_name ? $this->ms_pic_name : $this->mn_pic_id;
		if ( $this->mb_mod )
			$str = "Default association with image {$s_pic_name} established.";
		else
			$str = "Default association with image {$s_pic_name} submitted as request {$this->mn_def_edit_id}.<br />&nbsp;<br />Thank you.";

		echo  "<div style='margin:20px;text-align:center'>{$str}</div>";
		$this->drawReturn();
	}

	function drawWithdraw()
	{
		echo  "<div style='margin:20px;text-align:center'>Request {$this->mn_pic_edit_id} withdrawn.</div>";
		$this->drawReturn();
	}

	function drawReject()
	{
		echo  "<div style='margin:20px;text-align:center'>Picture submission {$this->mn_pic_edit_id} rejected.</div>".
		$this->drawReturn();
	}

	function drawPicSaved()
	{
		$s_rand       = microtime(true);
		$s_transf_now = describeTransforms('', $this->ma_data);    if ( ! $s_transf_now ) $s_transf_now = 'none';
		$s_transf_now = "<div style='margin:20px;text-align:center'>Transforms:<br />{$s_transf_now}</div>";

		if ( $this->mb_mod )
		{
//			$s_dir = getPicDir($this->ms_prev_pic);
			echo  "<div style='margin:20px;text-align:center'>Image saved as {$this->ms_prev_pic}{$s_transf_now}</div>";
				  $this->drawReturn();
			echo  "<div style='margin:20px;text-align:center'>".
//					"<img style='padding:20px' src='http://dv1.us/p1/{$s_dir}/{$this->ms_prev_pic}.jpg?tm={$s_rand}' alt='' /> ".
//					"<img style='padding:20px' src='http://dv1.us/p0/{$s_dir}/{$this->ms_prev_pic}.gif?tm={$s_rand}' alt='' />".
					"<img style='padding:20px' src='".getPicLocation($this->ms_prev_pic,false)."/{$this->ms_prev_pic}.jpg?tm={$s_rand}' alt='' /> ".
					"<img style='padding:20px' src='".getPicLocation($this->ms_prev_pic,true )."/{$this->ms_prev_pic}.gif?tm={$s_rand}' alt='' />".
				  "</div>";
		}
		else
		{
			echo  "<div style='margin:20px;text-align:center'>Image proposed{$s_transf_now}</div>";
				  $this->drawReturn();
			echo  "<div style='margin:20px;text-align:center'>".
					"<img style='padding:20px' src='{$this->ms_prev_pic}?tm={$s_rand}' alt='' /> ".
				  "</div>";
		}
	}

	function drawPicEdit()
	{
		$this->mb_review_sub = $this->mb_mod && $this->ma_data['proposer_id'] != $this->ms_user_id;
		$s_rand				 = microtime(true);
		$s_tmpl_top			 = dvdaf_parsetemplate      ("", $s_select, $s_from, $s_where, $s_sort, DVDAF_SHOW_PIC, DVDAF_SELECT_DVD, '', '', 0);
		$s_tmpl_mid			 = dvdaf_parsetemplateformat("", DVDAF_SHOW_PIC+1, DVDAF_SELECT_DVD, '', '', 0);
		$s_tmpl_sup			 = dvdaf_parsetemplateformat("", DVDAF_SHOW_PIC+2, DVDAF_SELECT_DVD, '', '', 0);
		$s_transf_old		 = describeTransforms('x_', $this->ma_data);  if ( ! $s_transf_old ) $s_transf_old = 'none';
		$s_transf_prop		 = '';
		$s_transf_now 		 = describeTransforms('', $this->ma_data);    if ( ! $s_transf_now ) $s_transf_now = 'none';
		$trt				 = $this->ma_data['img_treatment'];
		$gif				 = str_replace('/p1/', '/p0/', substr($this->ms_prev_pic, 0, -3).'gif');
		$s_proposer_notes	 = dvdaf_db2textarea($this->ma_data['proposer_notes']);
		$s_reviewer_notes	 = dvdaf_db2textarea($this->ma_data['reviewer_notes']);
		$s_input_bt			 = "<input type='button'";
		$s_style_br			 = "style='width:80px' /><br />";
		$s_prop_read		 = $this->mb_review_sub ? "readonly='readonly' style='color:#999999;background-color:#eeeeee' " : '';
		$s_revi_read		 = $this->mb_review_sub ? '' : "readonly='readonly' style='color:#999999;background-color:#eeeeee' ";
		$s_disable			 = $this->ma_data['disposition_cd'] != '-' ? "disabled='disabled' " : '';

		if ( $this->mb_review_sub )
		{
			$s_savenew  = $s_disable || $this->ma_data['request_cd'] != 'R' ? "disabled='disabled' " : '';
			$s_controls = $s_input_bt. " value='Close' "	."onclick='window.close()' "						   .$s_style_br.
						  $s_input_bt. " value='Reject' "	."onclick='PicEdit.reject()' "					.$s_disable.$s_style_br.
						  $s_input_bt. " value='Save as New' "	."onclick='PicEdit.validate(1,1)' "				.$s_savenew.$s_style_br.
						  $s_input_bt. " value='Reset' "	."onclick='PicEdit.reset();return PicEdit.validate(0,0)' "	.$s_disable.$s_style_br.
						  $s_input_bt. " value='Preview' "	."onclick='PicEdit.validate(0,0)' "				.$s_disable.$s_style_br.
						  $s_input_bt. " value='Approve' "	."onclick='PicEdit.validate(1,0)' "				.$s_disable.$s_style_br;
			$s_helpers	= "<br />".
						  "<input type='button' style='width:42px;font-size:9px' value='Clear' onclick='document.getElementById(\"n_p_reviewer_notes\").value=\"\"' {$s_disable}/> ".
						  "<input type='button' style='width:64px;font-size:9px' value='Std comts' id='mod_txt' {$s_disable}/>";
		}
		else
		{
			$s_controls = $s_input_bt. " value='Return' "	."onclick='location.href=\"{$this->ms_return_url}\"' "			   .$s_style_br.
						  $s_input_bt. " value='Reset' "	."onclick='PicEdit.reset();PicEdit.validate(0,0)' "		.$s_disable.$s_style_br.
						  $s_input_bt. " value='Preview' "	."onclick='PicEdit.validate(0,0)' "				.$s_disable.$s_style_br.
						  $s_input_bt. " value='Save' "		."onclick='PicEdit.validate(1,0)' "				.$s_disable.$s_style_br;
			$s_helpers	= '';
		}
	//if ( $this->mb_mod )
	//{
	//    $s_controls = "<div>Picture processing disabled as we migrate the picture server, please see http://dvdaf.net for an update</div>";
	//}

		echo  "<form id='rjform' name='rjform' method='post' action='' style='visibility:hidden'>".
				"<input type='hidden' id='modjust' name='modjust' value='' />".
			  "</form>".
			  "<form id='myform' name='myform' method='post' action='{$this->ms_request_uri}'>".
			  "<table class='padded'>".
				"<tr>".
				  "<td>".
					"<table border='1' width='100%' class='padded' style='margin-top:10px'>".
					  "<tr><td class='x2' style='padding:4px'>Request Status</td></tr>".
					  "<tr>".
						"<td>";
						  dvdaf_getbrowserow($this->ma_data, $s_tmpl_top, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_DVD_ID_OK);
		echo			"</td>".
					  "</tr>".
					"</table>".
					"<table border='1' width='100%' class='padded' style='margin-top:14px'>".
					  "<tr><td class='x2' style='padding:4px'>Picture Information</td></tr>".
					  "<tr>".
						"<td>".
						  "<table cellspacing='4' width='100%'>".
							"<tr class='se'>";
							  dvdaf_getbrowserow($this->ma_data, $s_tmpl_mid, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_DVD_ID_OK);
		echo					"<input type='hidden' id='act' name='act' value='{$this->ms_act}' />".
								"<input type='hidden' id='step' name='step' value='preview' />".
								"<input type='hidden' id='pic' name='pic' value='{$this->mn_pic_id}' />".
								"<input type='hidden' id='n_p_pic_edit_id' name='n_p_pic_edit_id' value='{$this->mn_pic_edit_id}' />".
								"<input type='hidden' id='obj_type' name='obj_type' value='{$this->ms_obj_type}' />".
								"<input type='hidden' id='obj' name='obj' value='{$this->mn_obj_id}' />".
								"<input type='hidden' id='obj_edit' name='obj_edit' value='{$this->mn_obj_edit_id}' />".
								"<input type='hidden' id='seed' name='seed' value='{$this->ms_seed}' />".
								"<input type='hidden' id='preview' name='preview' value='".($this->mb_is_preview ? '1' : '0')."' />".
								($this->mb_dvd_submission ? "<input type='hidden' id='pdvd' name='pdvd' value='1' />" : '').
								($this->mb_mod            ? "<input type='hidden' id='mod' name='mod' value='1' />" : '').
							  "</td>".
							"</tr>".
						  "</table>".
						"</td>".
					  "</tr>".
					"</table>".
				  "</td>".
				"</tr>".
				"<tr>".
				  "<td>".
					"<table border='1' width='100%' class='padded' style='margin-top:10px'>".
					  "<tr><td colspan='3' class='x2' style='padding:4px'>Picture Processing</td></tr>".
					  "<tr>".
						"<td class='oj'>".($s_transf_old  ? "Previous transforms:<div class='oh' style='margin-left:20px'>{$s_transf_old }</div>" : '&nbsp;' )."</td>".
						"<td class='oj'>".($s_transf_prop ? "Proposed transforms:<div class='oh' style='margin-left:20px'>{$s_transf_prop}</div>" : '&nbsp;' )."</td>".
						"<td class='oj'>".($s_transf_now  ? "Tranforms as shown:<div class='oh' style='margin-left:20px'>{$s_transf_now }</div>" : '&nbsp;' )."</td>".
					  "</tr>".
					  "<tr class='mc' style='text-align:center;font-weight:bold;font-size:12px'>".
						"<td style='padding:4px'>Source Picture</td>".
						"<td style='padding:4px'>Transformations</td>".
						"<td style='padding:4px'>Resulting Picture</td>".
					  "</tr>".
					  "<tr>".
						"<td style='vertical-align:top' rowspan='5'>".
						  "<img style='padding:5px' src='{$this->ms_bord_pic}?tm={$s_rand}' alt='' />".
						"</td>".
						"<td>".
						  "<table width='100%'>".
							"<tr>".
							  "<td class='oj' style='vertical-align:top;white-space:nowrap'>".
								"Proposer notes:".
								"<img id='ex_p_proposer_notes' src='http://dv1.us/di/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' />".
							  "</td>".
							  "<td>".
								"<textarea id='n_p_proposer_notes' name='n_p_proposer_notes' class='oh' cols='40' rows='2' {$s_prop_read}maxlegth='1000' wrap='soft'>{$s_proposer_notes}</textarea>".
								"<input id='o_p_proposer_notes' name='o_p_proposer_notes' type='hidden' value='{$s_proposer_notes}' />".
								"<input id='z_p_proposer_notes' type='hidden' value='{$s_proposer_notes}' />".
								"<img id='zi_p_proposer_notes' src='http://dv1.us/di/1.gif' align='top' />".
							  "</td>".
							"</tr>".
							"<tr>".
							  "<td class='oj' style='vertical-align:top;white-space:nowrap'>".
								"Reviewer notes:".
								"<img id='ex_p_reviewer_notes' src='http://dv1.us/di/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' />".
								$s_helpers.
							  "</td>".
							  "<td>".
								"<textarea id='n_p_reviewer_notes' name='n_p_reviewer_notes' class='oh' cols='40' rows='2' {$s_revi_read}maxlegth='1000' wrap='soft'>{$s_reviewer_notes}</textarea>".
								"<input id='o_p_reviewer_notes' name='o_p_reviewer_notes' type='hidden' value='{$s_reviewer_notes}' />".
								"<input id='z_p_reviewer_notes' type='hidden' value='{$s_reviewer_notes}' />".
								"<img id='zi_p_reviewer_notes' src='http://dv1.us/di/1.gif' align='top' />".
							  "</td>".
							"</tr>".
						  "</table>".
						"</td>".
						"<td style='vertical-align:top' rowspan='5'>".
						  "<img style='padding:20px' src='{$this->ms_prev_pic}?tm={$s_rand}' alt='' />".
						"</td>".
					  "</tr>".
					  "<tr>".
						"<td>".
						  "<table width='100%'>".
							"<tr>";
		echo				  "<td width='90%' style='white-space:nowrap'>".
								"<input type='radio' name='n_p_img_treatment' id='n_p_img_treatment_O' value='O' ".($trt =='O' ? "checked='checked' " : '')."/> DVD: no border<br />".
								"<input type='radio' name='n_p_img_treatment' id='n_p_img_treatment_B' value='B' ".($trt =='B' ? "checked='checked' " : '')."/> White cover DVDs: adds border<br />".
								"<input type='radio' name='n_p_img_treatment' id='n_p_img_treatment_K' value='K' ".($trt =='K' ? "checked='checked' " : '')."/> 3D and posters: keeps HxV ratio<br />".
								"<input type='radio' name='n_p_img_treatment' id='n_p_img_treatment_H' value='H' ".($trt =='H' ? "checked='checked' " : '')."/> HD DVD: shorter size<br />".
								"<input type='radio' name='n_p_img_treatment' id='n_p_img_treatment_R' value='R' ".($trt =='R' ? "checked='checked' " : '')."/> Blu-ray: shorter size<br />".
								"<input type='radio' name='n_p_img_treatment' id='n_p_img_treatment_F' value='F' ".($trt =='F' ? "checked='checked' " : '')."/> Movie frame 16:9: adds bars [&nbsp;&nbsp;&nbsp;]".
								"<input id='o_p_img_treatment' name='o_p_img_treatment' type='hidden' value='{$this->ma_data['x_img_treatment']}' />".
								"<input id='z_p_img_treatment' type='hidden' value='{$trt}' />".
								"<img id='zi_p_img_treatment' src='http://dv1.us/di/1.gif' align='top' />".
							  "</td>".
							  "<td width='1%' style='padding: 10px 10px 0 10px;text-align:center;vertical-align:bottom'>".
								$s_controls.
							  "</td>".
							  "<td width='1%' style='padding:10px 10px 0 10px;text-align:center;vertical-align:top'>".
								"<img src='{$gif}?tm={$s_rand}' alt='' />".
							  "</td>".
							"</tr>".
						  "</table>".
						"</td>".
					  "</tr>";
					  dvdaf_getbrowserow($this->ma_data, $s_tmpl_sup, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_DVD_ID_OK);
		echo	    "</table>".
				  "</td>".
				"</tr>".
			  "</table>".
			  "</form>";
		echo  "<ul id='context-menu' style='display:none'><li></li></ul>";
	}

	function getFooterJavaScript()
	{
		$s_config =
			'{baseDomain:"'.	$this->ms_base_subdomain.'"'.
			',onPopup:PicEdit.onPopup'.
			',context:1'.
			',ulExplain:1'.
			',ulPicComments:1'.
			',imgPreLoad:"spin.explain.undo"'.
			'}';

		return
			"function onMenuClick(action){PicEdit.onClick(action);};".
			"Filmaf.config({$s_config});".
			"PicEdit.setup();";
	}

	function drawReturn()
	{
		if ( $this->mb_dvd_submission )
		{
			echo  "<div style='margin:20px;text-align:center'>".
					"<div style='margin-bottom:10px'><input type='button' value='Close window' onclick='window.close()' style='width:120px'></div>".
					"<div><input type='button' value='Picture management' onclick='location.href=\"{$this->ms_pic_mngt_url}\"' style='width:160px'></div>".
				  "</div>";
		}
		else
		{
			echo  "<div style='margin:20px;text-align:center'>".
					"<div><input type='button' value='Return' onclick='location.href=\"{$this->ms_return_url}\"' style='width:120px'></div>".
				  "</div>";
		}
	}
}

?>
