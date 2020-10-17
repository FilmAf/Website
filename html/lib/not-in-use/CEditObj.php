<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

define('CWnd_EDIT_NEW'			,     0);
define('CWnd_EDIT_AUDIT'		,     1);
define('CWnd_EDIT_OBJ'			,     2);

define('MSG_ERROR_NOT_SAVED'	,     1);
define('MSG_ERROR_DB_CHANGED'	,     2);
define('MSG_ERROR_HISTORY'		,     3);
define('MSG_ERROR_VALIDATION'	,     4);
define('MSG_ALREADY_SAVED'		,     5);
define('MSG_NOTHING_TO_SAVE'	,     6);
define('MSG_SAVED'				,     7);
define('MSG_REQUEST_SUBMITTED'	,     8);
define('MSG_OLD_NOT_FOUND'		,     9);
define('MSG_USER_BLOCKED'		,    10);

require $gs_root.'/lib/CValidate.php';
require $gs_root.'/lib/CWndMenu.php';
require $gs_root.'/lib/rights-dvd.inc.php';

class CEditObj extends CWndMenu
{
	function constructor()
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

		$this->ms_include_css	   .=
		"<style type='text/css'>".
			"#audit_lbl "				."{ text-align:left; }".
			"#td_nav "					."{ text-align:right;padding:0 0 20px 0; }".
			"#td_nav span "				."{ white-space:nowrap;padding-left:16px; }".
		"</style>";

		$this->mb_include_collect	= false;
		$this->mb_include_search	= false;
		$this->mb_advert			= false;
		$this->mb_get_user_status	= true;
		$this->mb_direct_update		= false;

		$this->ma_result			= array();
		$this->ms_audit_id			= dvdaf3_getvalue('audit', DVDAF3_GET);
		$this->mn_audit_id			= 0;
		$this->ms_obj_id			= dvdaf3_getvalue('obj'  , DVDAF3_GET|DVDAF3_LOWER);
		$this->mn_obj_id			= 0;
		$this->mn_page				= dvdaf3_getvalue('pg'   , DVDAF3_GET|DVDAF3_INT  );
		$this->mn_edit_mode			= CWnd_EDIT_NEW;
		$this->mn_seq_last			= 0;

		if ( $this->ms_obj_id )
		{
			$this->mn_seq_last	= count(($a = explode(',', $this->ms_obj_id)));
			if ( $this->mn_page > $this->mn_seq_last ) $this->mn_page = $this->mn_seq_last;
			if ( $this->mn_page < 1					 ) $this->mn_page = 1;
			$this->mn_obj_id	= intval($a[$this->mn_page - 1]);
			if ( $this->mn_obj_id > 0 )
			{
				$this->mn_edit_mode = CWnd_EDIT_OBJ;
				$this->ms_audit_id  = '';
			}
		}
		else
		{
			if ( $this->ms_audit_id )
			{
				$this->mn_seq_last	= count(($a = explode(',', $this->ms_audit_id)));
				if ( $this->mn_page > $this->mn_seq_last ) $this->mn_page = $this->mn_seq_last;
				if ( $this->mn_page < 1					 ) $this->mn_page = 1;
				$this->mn_audit_id	= intval($a[$this->mn_page - 1]);
				if ( $this->mn_audit_id > 0 )
				{
					$this->mn_edit_mode = CWnd_EDIT_AUDIT;
					$this->ms_obj_id  = '';
				}
			}
		}

		$this->ms_url_parms	= substr(($this->ms_audit_id ? "audit={$this->ms_audit_id}&" : '').
									 ($this->ms_obj_id   ? "obj={$this->ms_obj_id}&"     : ''),0,-1);
		$this->ms_url_plain	= dvdaf3_getvalue('SCRIPT_NAME' , DVDAF3_SERVER);
		$this->ms_url		= $this->ms_url_plain. ($this->ms_url_parms ? '?'.$this->ms_url_parms : '');
	}

	function validUserAccess()
	{
		if ( ! $this->mb_logged_in )
			return CUser_NOACCESS_GUEST;
		if ( $this->mb_mod )
			if ( $this->mb_logged_in_this_sess )
				$this->mb_direct_update = true;
			else
				return CUser_NOACCESS_SESSION;
		return CUser_ACCESS_GRANTED;
	}

	function getOnLoadJavaScript()
	{
		return "this.focus();";
	}

	function getSql()
	{
		//CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__ ."({$this->mn_audit_id},{$this->mn_obj_id})");
		$this->ms_select = '';
		$this->ms_from	 = '';
		$this->ms_where	 = '';
		$this->ms_action = 'edit';
		$o = &$this->mo_obj;
		$z = $o->ms_tbl_obj_;

		// '-' pic_overwrite, x.best_price
		if ( $this->mn_obj_id )
		{
			$this->ms_select .=	"x.{$o->ms_key}, ".
								"x.{$o->ms_version}, ".
								"x.{$o->ms_created_tm}, ".
								"x.{$o->ms_updated_tm}, ".
								"x.{$o->ms_updated_by}, ".
								"x.{$o->ms_justify}, ".
								"x.{$o->ms_verified_tm}, ".
								"x.{$o->ms_verified_by}, ".
								"x.{$o->ms_verified_version}, ".
								($o->ms_pic_status	? "x.{$o->ms_pic_status}, "	: '').
								($o->ms_pic_name	? "x.{$o->ms_pic_name}, "	: '').
								($o->ms_pic_count	? "x.{$o->ms_pic_count}, "	: '');
		}
		else
		{
			$this->ms_select .=	"0 {$o->ms_key}, ".
								"0 {$o->ms_version}, ".
								"null {$o->ms_created_tm}, ".
								"null {$o->ms_updated_tm}, ".
								"'-' {$o->ms_updated_by}, ".
								"'-' {$o->ms_justify}, ".
								"null {$o->ms_verified_tm}, ".
								"'-' {$o->ms_verified_by}, ".
								"-1 {$o->ms_verified_version}, ".
								($o->ms_pic_status	? "'-' {$o->ms_pic_status}, " : '').
								($o->ms_pic_name	? "'-' {$o->ms_pic_name}, "	  : '').
								($o->ms_pic_count	? "0 {$o->ms_pic_count}, "	  : '');
		}

		if ( $this->mn_audit_id )
		{
			if ( $this->mn_obj_id )
			{
				for ( $i = 0 ; $i < count($o->ma_attributes) ; $i++ )
					if ( $o->ma_attributes[$i]['eedt'] & 1 )
						$this->ms_select .= "{$z}.{$o->ma_attributes[$i]['col']}, ".
											($o->ma_attributes[$i]['eedt'] & 2 ? '' : "x.{$o->ma_attributes[$i]['col']} x_{$o->ma_attributes[$i]['col']}, ");
				$this->ms_from	  = "{$o->ms_tbl_submit} {$z} LEFT JOIN {$o->ms_tbl_obj} x ON {$z}.{$o->ms_key} = x.{$o->ms_key}";
				$this->ms_where	  = "{$z}.{$o->ms_submit_id} = {$this->mn_audit_id} and {$z}.{$o->ms_submit_proposer_id} = '{$this->ms_user_id}'";
			}
			else
			{
				for ( $i = 0 ; $i < count($o->ma_attributes) ; $i++ )
					if ( $o->ma_attributes[$i]['eedt'] & 1 )
						$this->ms_select .= "{$z}.{$o->ma_attributes[$i]['col']}, ".
											($o->ma_attributes[$i]['eedt'] & 2 ? '' : "{$z}.{$o->ma_attributes[$i]['col']} x_{$o->ma_attributes[$i]['col']}, ");
				$this->ms_from	  = "{$o->ms_tbl_submit} {$z}";
				$this->ms_where	  = "{$z}.{$o->ms_submit_id} = {$this->mn_audit_id} and {$z}.{$o->ms_submit_proposer_id} = '{$this->ms_user_id}'";
			}
			$this->ms_select .=		"{$z}.{$o->ms_submit_request_cd}, ".
									"{$z}.{$o->ms_submit_hist_version_id}, ".
									"datediff(now(),{$z}.{$o->ms_submit_reviewed_tm}) reviewed_int, ".
									"{$z}.{$o->ms_submit_id}, ".
									"{$z}.{$o->ms_submit_disposition_cd}, ".
									"{$z}.{$o->ms_submit_proposer_id}, ".
									"{$z}.{$o->ms_submit_proposer_notes}, ".
									"{$z}.{$o->ms_submit_proposed_tm}, ".
									"{$z}.{$o->ms_submit_updated_tm}, ".
									"{$z}.{$o->ms_submit_reviewer_id}, ".
									"{$z}.{$o->ms_submit_reviewer_notes}, ".
									"{$z}.{$o->ms_submit_reviewed_tm}, ".
									"{$z}.{$o->ms_submit_update_justify}";
		}
		else
		{
			if ( $this->mn_obj_id )
			{
				for ( $i = 0 ; $i < count($o->ma_attributes) ; $i++ )
					if ( $o->ma_attributes[$i]['eedt'] & 1 )
						$this->ms_select .= "x.{$o->ma_attributes[$i]['col']}, ".
											($o->ma_attributes[$i]['eedt'] & 2 ? '' : "x.{$o->ma_attributes[$i]['col']} x_{$o->ma_attributes[$i]['col']}, ");
				$this->ms_select .=	"'E' {$o->ms_submit_request_cd}, ".
									"x.{$o->ms_version} {$o->ms_submit_hist_version_id}, ";
				$this->ms_from	  = "{$o->ms_tbl_obj} x";
				$this->ms_where	  = "x.{$o->ms_key} = {$this->mn_obj_id}";
			}
			else
			{
				$this->ms_action = 'new';
				for ( $i = 0 ; $i < count($o->ma_attributes) ; $i++ )
					if ( $o->ma_attributes[$i]['eedt'] & 1 )
						$this->ms_select .= "{$o->ma_attributes[$i]['def']} {$o->ma_attributes[$i]['col']}, ".
											($o->ma_attributes[$i]['eedt'] & 2 ? '' : "{$o->ma_attributes[$i]['def']} x_{$o->ma_attributes[$i]['col']}, ");
				$this->ms_select .=	"'N' {$o->ms_submit_request_cd}, ".
									"0 {$o->ms_submit_hist_version_id}, ";
				$this->ms_from	  = '';
				$this->ms_where	  = '';
			}
			$this->ms_select .=		"0 reviewed_int, ".
									"0 {$o->ms_submit_id}, ".
									"'-' {$o->ms_submit_disposition_cd}, ".
									"'{$this->ms_user_id}' {$o->ms_submit_proposer_id}, ".
									"'-' {$o->ms_submit_proposer_notes}, ".
									"null {$o->ms_submit_proposed_tm}, ".
									"null {$o->ms_submit_updated_tm}, ".
									"'-' {$o->ms_submit_reviewer_id}, ".
									"'-' {$o->ms_submit_reviewer_notes}, ".
									"null {$o->ms_submit_reviewed_tm}, ".
									"'-' {$o->ms_submit_update_justify}";
		}
	}

	function runSql()
	{
		$this->getSql();
		$s_sql = "SELECT {$this->ms_select}".($this->ms_from  ?  " FROM {$this->ms_from}"  : '').($this->ms_where ? " WHERE {$this->ms_where}" : '');
		$this->ma_result = CSql::query_and_fetch($s_sql,0,__FILE__,__LINE__);
	}

	function tellUser($n_line, $n_what, $s_parm)
	{
		switch ( $n_what )
		{
		case MSG_ERROR_NOT_SAVED:	$this->ms_display_error    = "Sorry, your changes could not be saved. Please try again in a few minutes."; break;
		case MSG_ERROR_DB_CHANGED:	$this->ms_display_error    = "Sorry, the database data was changed just before you submitted your changes. Please try again in a few minutes."; break;
		case MSG_ERROR_HISTORY:		$this->ms_display_error    = "Sorry, we were unable to snapshot the object&#39;s history as required to apply your updates. Please try again in a few minutes."; break;
		case MSG_ERROR_VALIDATION:	if ( $this->ms_display_error )
										$this->ms_display_error = "Internal validation error<div style=\"margin-top:8px;margin-bottom:12px\">{$this->ms_display_error}</div>";
									else
										$this->ms_display_error = "We run into an unknown validation error."; break;
//		case MSG_SOMEONE_ELSE:		$this->ms_display_error    = "Sorry, the submission with an id of '{$s_parm}' belongs to someone else."; break;
//		case MSG_EDIT_NOT_FOUND:	$this->ms_display_error    = "Sorry, we did not find a submission with an id of '{$s_parm}'."; break;
//		case MSG_WHAT_NOT_FOUND:	$this->ms_display_error    = "Sorry, we are unable to find and audit id {$s_parm}. You may have bookmarked a stale URL."; break;
//		case MSG_DVD_NOT_FOUND:		$this->ms_display_error    = "Sorry, we are unable to find a DVD with an id of {$s_parm}."; break;
//		case MSG_UNABLE_TO_PROC:	$this->ms_display_error    = "Sorry, we are unable to process your request at this time."; break;
		case MSG_ALREADY_SAVED:		$this->ms_display_affected = "Submission already saved."; break;
		case MSG_NOTHING_TO_SAVE:	$this->ms_display_affected = "Nothing to save."; break;
		case MSG_SAVED:				$this->ms_display_affected = "Changes saved as {$s_parm}."; break;
		case MSG_REQUEST_SUBMITTED:	$this->ms_display_affected = 'Request for change submitted.'; break;
		case MSG_OLD_NOT_FOUND:		$this->ms_display_affected = "We are unable to edit your submission. Please create a new submission (as opposed to editing an old one). Thanks."; break;
		case MSG_USER_BLOCKED:		$this->ms_display_affected = "Sorry, we encountered an error. We appologize for any inconveniece, please try again in a few minutes."; break;
		}
		if ( $this->ms_display_error )
		{
			$this->ms_display_error .= " If seeking support at <a href='http://dvdaf.net' target='_blank'>http://dvdaf.net</a> please mention the ".
									   "following code: ".dvdaf3_getvalue('REMOTE_ADDR',DVDAF3_SERVER)."-".dvdaf3_getvalue('REQUEST_TIME',DVDAF3_SERVER).
									   "-{$n_line}. We apologize for the inconvenience.";
		}

		return false;
	}

	function incrementVersion($n_obj_id)
	{
		$o = &$this->mo_obj;
		$i = CSql::query_and_fetch1("SELECT {$o->ms_key} ".
									  "FROM {$o->ms_tbl_obj} ".
									 "WHERE {$o->ms_key} = $n_obj_id ".
									   "and {$o->ms_updated_by} = '{$this->ms_user_id}' ".
									   "and {$o->ms_updated_tm} > date_add(now(), INTERVAL -10 MINUTE) ".
									   "and ({$o->ms_verified_tm} is NULL || {$o->ms_verified_by} = {$o->ms_updated_by})",
									0,__FILE__,__LINE__);
		return $i <> $n_obj_id;
	}

	function validateDataSubmission()
	{
		if ( ! $this->mb_logged_in )
			return false;

		if ( CSql::query_and_fetch1("SELECT block_submissions FROM dvdaf_user_2 WHERE user_id = '{$this->ms_user_id}'", 0,__FILE__,__LINE__) != 'N' )
			return $this->tellUser(__LINE__, MSG_USER_BLOCKED, false);

		getDvdRights($this);
		$o		= &$this->mo_obj;
		$z		= $o->ms_tbl_obj_;
		$s_seed	= dvdaf3_getvalue('seed', DVDAF3_POST);

		if ( $this->mb_mod )
		{
			if ( isset($_POST["n_z{$z}update_justify"]) ) { $_POST["n_{$z}_update_justify"] = $_POST["n_z{$z}update_justify"]; unset($_POST["n_z{$z}update_justify"]); }
			if ( isset($_POST["o_z{$z}update_justify"]) ) { $_POST["o_{$z}_update_justify"] = $_POST["o_z{$z}update_justify"]; unset($_POST["o_z{$z}update_justify"]); }
			unset($_POST["n_z{$z}proposer_notes"]);
			unset($_POST["o_z{$z}proposer_notes"]);
		}

		switch ( dvdaf3_getvalue('act', DVDAF3_POST|DVDAF3_LOWER) )
		{
		case 'new':
			if ( ($s = $o->validate(true,$this->mb_mod,$this->mb_mod)) )
			{
				$this->ms_display_error = "{$s} (code {$n_line})";
				return false;
			}

			CValidate::validateInput($s_update, $s_values, DVDAF_INSERT, ! $this->mb_mod, $this->ms_display_error);

			if ( $s_update && $s_values && $s_seed )
				if ( $this->mb_mod )
					$this->saveNewDirect($s_update, $s_values, $s_seed, false);
				else
					$this->saveNewSubmit($s_update, $s_values, $s_seed);
			else
				if ( $this->ms_display_error )
					$this->tellUser(CTrace::logError(__FILE__,__LINE__,__CLASS__.'::'.__FUNCTION__,__LINE__,'Validation error.',$this->ms_display_error),MSG_ERROR_VALIDATION,false);
				else
					$this->tellUser(__LINE__, MSG_NOTHING_TO_SAVE, false);
			break;

		case 'edit':
			$n_version_id = dvdaf3_getvalue($o->ms_version,DVDAF3_POST|DVDAF3_INT); // of the current version of the object
			$n_edit_id	  = dvdaf3_getvalue('audit_id'	 ,DVDAF3_POST|DVDAF3_INT);
			$n_obj_id	  = dvdaf3_getvalue('obj_id'		 ,DVDAF3_POST|DVDAF3_INT);
			if ( $n_edit_id )
			{
				if ( ! ($n_edit_id = CSql::query_and_fetch1("SELECT {$o->ms_submit_id} FROM {$o->ms_tbl_submit} WHERE proposer_id = '{$this->ms_user_id}' and {$o->ms_submit_id} = {$n_edit_id}", 0,__FILE__,__LINE__)) )
				{
					$this->tellUser(__LINE__, MSG_OLD_NOT_FOUND, false);
					break;
				}
			}
			else
			{
				if ( $n_obj_id )
					$n_edit_id = CSql::query_and_fetch1("SELECT {$o->ms_submit_id} FROM {$o->ms_tbl_submit} WHERE {$o->ms_key} = {$n_obj_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'", 0,__FILE__,__LINE__);
			}

			if ( ($s = $o->validate(false,$this->mb_mod,$this->mb_mod)) )
			{
				$this->ms_display_error = "{$s} (code {$n_line})";

				return false;
			}

			CValidate::validateInput($s_update, $s_values, $this->mb_mod || $n_edit_id ? DVDAF_UPDATE : DVDAF_INSERT, ! $this->mb_mod, $this->ms_display_error);

			if ( $s_update )
				if ( $this->mb_mod )
					$this->saveEditDirect($s_update, $n_obj_id, $n_version_id, false);
				else
					$this->saveEditSubmit($s_update, $s_values, $n_obj_id, $n_version_id, $n_edit_id, $s_seed);
			else
				if ( $this->ms_display_error )
					$this->tellUser(CTrace::logError(__FILE__,__LINE__,__CLASS__.'::'.__FUNCTION__,__LINE__,'Validation error.',$this->ms_display_error),MSG_ERROR_VALIDATION,false);
				else
					$this->tellUser(__LINE__, MSG_NOTHING_TO_SAVE, false);
			break;

		case 'del_sub':
			if ( ($n_edit_id = dvdaf3_getvalue($o->ms_submit_id, DVDAF3_POST|DVDAF3_INT)) )
				$this->deleteSubmission($n_edit_id);
			break;

		default:
			if ( $this->mn_audit_id )
				$this->mn_obj_id = intval(CSql::query_and_fetch1("SELECT {$o->ms_key} FROM {$o->ms_tbl_submit} WHERE {$o->ms_submit_id} = {$this->mn_audit_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'", 0,__FILE__,__LINE__));
			else
				if ( $this->mn_obj_id )
					$this->mn_audit_id = intval(CSql::query_and_fetch1("SELECT {$o->ms_submit_id} FROM {$o->ms_tbl_submit} WHERE {$o->ms_key} = {$this->mn_obj_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'", 0,__FILE__,__LINE__));
			break;
		}

		if ( $this->ms_redirect && ($this->ms_display_error || $this->ms_display_affected) )
		{
			$this->ms_redirect = "{$this->ms_base_subdomain}/utils/redirect.html".
								 '?trg='.urlencode($this->ms_redirect).
								 '&msg='.urlencode($this->ms_display_error ? $this->ms_display_error : $this->ms_display_affected);
		}
	}

	function saveNewDirect($s_update, $s_values, $s_seed, $b_log_direct)
	{
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$o		  = &$this->mo_obj;
		$n_obj_id = CSql::query_and_fetch1("SELECT {$o->ms_key} FROM {$o->ms_tbl_obj} WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);
		if ( $n_obj_id )
		{
			$this->ms_redirect = "{$this->ms_url_plain}?obj={$n_obj_id}";
			return $this->tellUser(__LINE__, MSG_ALREADY_SAVED, false);
		}

		$s_values .= "max({$o->ms_key})+1, '{$s_seed}', now(), now(), '{$this->ms_user_id}'";
		$s_update .= "{$o->ms_key}, creation_seed, {$o->ms_created_tm}, {$o->ms_updated_tm}, {$o->ms_updated_by}";
		if ( $this->mb_mod )
		{
			$s_values .= ", now(), '{$this->ms_user_id}', 0";
			$s_update .= ", {$o->ms_verified_tm}, {$o->ms_verified_by}, verified_version";
		}
		$s_update  = "INSERT INTO {$o->ms_tbl_obj} ({$s_update}) SELECT {$s_values} from {$o->ms_tbl_obj}";

		$n_updated = CSql::query_and_free($s_update,0,__FILE__,__LINE__);
		$n_obj_id  = 0;

		if ( $n_updated )
			$n_obj_id = CSql::query_and_fetch1("SELECT {$o->ms_key} FROM {$o->ms_tbl_obj} WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);

		if ( $n_obj_id  )
		{
			if ( $b_log_direct )
				CSql::query_and_free("INSERT INTO {$o->ms_tbl_direct} (user_id, updated_tm, {$o->ms_key}, version_id, new_title) ".
									 "VALUES ('{$this->ms_user_id}', now(), {$n_obj_id}, 0, 'Y')",0,__FILE__,__LINE__);
			$this->ms_redirect = "{$this->ms_url_plain}?obj={$n_obj_id}";
			$o->propagateChanges($n_obj_id);
			$this->tellUser(__LINE__, MSG_SAVED, "{$o->ms_obj_name} #{$n_obj_id} version 0");
		}
		else
		{
			$this->tellUser(CTrace::logError(__FILE__,__LINE__,__CLASS__.'::'.__FUNCTION__,__LINE__,'Your changes could not be saved.',$s_update),MSG_ERROR_NOT_SAVED, false);
		}
	}

	function saveNewSubmit($s_update, $s_values, $s_seed)
	{
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$o		   = &$this->mo_obj;
		$n_edit_id = CSql::query_and_fetch1("SELECT {$o->ms_submit_id} FROM {$o->ms_tbl_submit} WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);
		if ( $n_edit_id )
		{
			$this->ms_redirect = "{$this->ms_url_plain}?audit={$n_edit_id}";
			return $this->tellUser(__LINE__, MSG_ALREADY_SAVED, false);
		}

		$s_values .= "0, '{$s_seed}', 'N', '{$this->ms_user_id}', now(), now()";
		$s_update .= "{$o->ms_key}, creation_seed, request_cd, proposer_id, proposed_tm, updated_tm";
		$s_update  = "INSERT INTO {$o->ms_tbl_submit} ({$s_update}) VALUES ({$s_values})";

		$n_updated = CSql::query_and_free($s_update,0,__FILE__,__LINE__);
		$n_obj_id  = 0;

		if ( $n_updated )
			$n_edit_id = CSql::query_and_fetch1("SELECT {$o->ms_submit_id} FROM {$o->ms_tbl_submit} WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);

		if ( $n_edit_id  )
		{
			$this->ms_redirect = "{$this->ms_url_plain}?audit={$n_edit_id}";
			$this->tellUser(__LINE__, MSG_REQUEST_SUBMITTED, false);
		}
		else
		{
			$this->tellUser(CTrace::logError(__FILE__,__LINE__,__CLASS__.'::'.__FUNCTION__,__LINE__,'Unable to perform insert.',$s_update),MSG_ERROR_NOT_SAVED,false);
		}
	}

	function saveEditDirect($s_update, $n_obj_id, $n_version_id, $b_log_direct)
	{
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$o = &$this->mo_obj;

		if ( strpos($s_update, 'last_justify') === false )
			$s_update .= "last_justify = '-', ";

		$b_inc_version = $this->incrementVersion($n_obj_id);
		$n_updated	   = 1;
		$s_update	   = "UPDATE {$o->ms_tbl_obj} ".
							"SET $s_update".
			  ($b_inc_version ? "version_id = version_id + 1, " : '').
								"{$o->ms_updated_tm} = now(), ".
								"{$o->ms_updated_by} = '{$this->ms_user_id}', ".
								"{$o->ms_gen_by_edit_id} = 0, ".
			   ($this->mb_mod ? "{$o->ms_verified_tm} = now(), ".
								"{$o->ms_verified_by} = '{$this->ms_user_id}', ".
								"verified_version = version_id "		// strange behavior!!! should really be "version + 1"
							  : "{$o->ms_verified_tm} = NULL, ".
								"{$o->ms_verified_by} = '-' ").
						  "WHERE {$o->ms_key} = {$n_obj_id} and version_id = {$n_version_id}";

		// save history
		if ( $b_inc_version )
		{
			$n_updated = $o->snapHistory($n_obj_id, $n_version_id);

			// could not save history, check to see if it is stuck
			if ( ! $n_updated )
			{
				$n_cur_version = CSql::query_and_fetch1("SELECT version_id from {$o->ms_tbl_obj} WHERE {$o->ms_key} = {$n_obj_id}",0,__FILE__,__LINE__);
				if ( $n_cur_version == $n_version_id )
				{
					CSql::query_and_free("DELETE FROM {$o->ms_tbl_hist} WHERE {$o->ms_key} = {$n_obj_id} and version_id = {$n_version_id}",0,__FILE__,__LINE__);
					$n_updated = $o->snapHistory($n_obj_id, $n_version_id);
				}
			}
			if ( ! $n_updated )
			{
				return $this->tellUser(CTrace::logError(__FILE__,__LINE__,__CLASS__.'::'.__FUNCTION__,__LINE__,'Unable to snapshot history.',"snapHistory($n_obj_id,$n_version_id)"),MSG_ERROR_HISTORY,false);
			}
		}

		if ( $n_updated )
			$n_updated = CSql::query_and_free($s_update,0,__FILE__,__LINE__);

		if ( $n_updated )
		{
			if ( $b_log_direct )
				CSql::query_and_free("INSERT INTO {$o->ms_tbl_direct} (user_id, updated_tm, {$o->ms_key}, version_id, edit_title) ".
									 "VALUES ('{$this->ms_user_id}', now(), {$n_obj_id}, {$n_version_id}, 'Y')",0,__FILE__,__LINE__);
			$o->propagateChanges($n_obj_id);
			if ( $b_inc_version ) $n_version_id++;
			$this->tellUser(__LINE__, MSG_SAVED, "{$o->ms_obj_name} #{$n_obj_id} version {$n_version_id}");
		}
		else
		{
			$this->tellUser(CTrace::logError(__FILE__,__LINE__,__CLASS__.'::'.__FUNCTION__,__LINE__,'Unable to perform update.',$s_update),MSG_ERROR_DB_CHANGED,false);
		}
	}

	function saveEditSubmit($s_update, $s_values, $n_obj_id, $n_version_id, $n_edit_id, $s_seed)
	{
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$o				= &$this->mo_obj;
		$b_unreject_pic = false;

		if ( $n_edit_id )
		{
			$s_update = "UPDATE {$o->ms_tbl_submit} ".
						   "SET {$s_update}disposition_cd = '-', updated_tm = now(), hist_version_id = {$n_version_id} ".
						 "WHERE {$o->ms_submit_id} = {$n_edit_id}";
			$b_unreject_pic = $n_obj_id == 0;
		}
		else
		{
			if ( ($n_edit_id = CSql::query_and_fetch1("SELECT {$o->ms_submit_id} FROM {$o->ms_tbl_submit} WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__)) )
			{
				$this->ms_redirect = "{$this->ms_url_plain}?audit={$n_edit_id}";
				return $this->tellUser(__LINE__, MSG_ALREADY_SAVED, false);
			}
			$s_update = "INSERT INTO {$o->ms_tbl_submit} ({$s_update}{$o->ms_key}, request_cd, proposer_id, proposed_tm, updated_tm, hist_version_id) ".
						"VALUES ({$s_values}{$n_obj_id}, 'E', '{$this->ms_user_id}', now(), now(), {$n_version_id})";
		}

		$n_updated = CSql::query_and_free($s_update, 0,__FILE__,__LINE__);

		if ( $n_updated )
		{
			$this->ms_redirect = "{$this->ms_url_plain}?audit={$n_edit_id}";
			if ( $b_unreject_pic )
				CSql::query_and_free("UPDATE pic_submit SET disposition_cd = '-' WHERE obj_type = '{$o->ms_obj_type}' and obj_edit_id = {$n_edit_id} and disposition_cd = 'R'",0,__FILE__,__LINE__);
			$this->tellUser(__LINE__, MSG_REQUEST_SUBMITTED, false);
		}
		else
		{
			$this->tellUser(CTrace::logError(__FILE__,__LINE__,__CLASS__.'::'.__FUNCTION__,__LINE__,'Unable to perform update.',$s_update),MSG_ERROR_DB_CHANGED,false);
		}
	}

	function deleteSubmission($n_edit_id)
	{
		$o		= &$this->mo_obj;
		$n_rows = CSql::query_and_free("UPDATE {$o->ms_tbl_submit} SET disposition_cd = 'W', updated_tm = now() ".
										"WHERE {$o->ms_submit_id} = {$n_edit_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'",0,__FILE__,__LINE__);
				  CSql::query_and_free("UPDATE pic_submit SET disposition_cd = 'W', updated_tm = now() ".
										"WHERE obj_type = '{$o->ms_obj_type}' and obj_edit_id = {$n_edit_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'",0,__FILE__,__LINE__);
		$this->ms_display_affected = $n_rows ? "$n_rows submission request".($n_rows > 1 ? 's' : '')." withdrawn.<br />" : "Sorry, your withdraw operation did not affect any submission requests.<br />";
	}

	function drawFormBeg()
	{
		$s_act		= $this->mn_edit_mode == CWnd_EDIT_NEW ? 'new' : 'edit';
		$s_seed		= date('Y-m-d H:i:s '). dvdaf3_getvalue('REMOTE_ADDR', DVDAF3_SERVER);
		$n_version	= $this->ma_result[$this->mo_obj->ms_version];

		echo  "<form id='myform' name='myform' method='post' action='{$this->ms_url}'>".
				"<input id='act' name='act' type='hidden' value='{$s_act}' />".
				"<input id='seed' name='seed' type='hidden' value='{$s_seed}' />".
				($this->mn_audit_id	? "<input id='audit_id' name='audit_id' type='hidden' value='{$this->mn_audit_id}' />"	: '').
				($this->mn_obj_id	? "<input id='obj_id' name='obj_id' type='hidden' value='{$this->mn_obj_id}' />"		: '').
				($n_version			? "<input id='version_id' name='version_id' type='hidden' value='{$n_version}' />"		: '').
				($this->mb_mod		? "<input id='mod' type='hidden' value='1' />"											: '');
	}

	function drawNav()
	{
		$b_withdraw = $this->mn_audit_id && ! $this->mb_mod;
		echo  "<span class='one_lbl'>".
				"Discard: ".
				"<input id='b_reload' type='button' value='Reload' onclick='ObjEdit.discard(\"Do you wish to discard any changes and reload this screen?\",0)' /> ".
				"<input id='b_cancel' type='button' value='Cancel' onclick='ObjEdit.discard(0,\"/utils/edit.html\")' /> ".
				($this->mn_obj_id	? "<input id='b_new' type='button' value='New' onclick='ObjEdit.discard(0,\"{$this->ms_url_plain}\")' /> " : '').
				($b_withdraw		? " <input id='b_withdraw' type='button' value='Withdraw' onclick='ObjEdit.withdraw(\"Do you wish to withdraw this submission request?\")' />" : '').
			  "</span>".
			  "<span class='one_lbl'>".
				"Save: ".
				"<input id='b_submit' type='button' value='Submit' onclick='validate(1)' /> ".
			  "</span>";
		$this->drawMessages(true,true);
	}

	function drawFormEnd()
	{
		echo  "</form>";
	}

	function getNotFoundReason()
	{
		$o = &$this->mo_obj;
		if ( $this->mn_audit_id > 0 )
		{
			if ( ($rr = CSql::query_and_fetch("SELECT proposer_id, disposition_cd FROM {$o->ms_tbl_submit} WHERE {$o->ms_submit_id} = {$this->mn_audit_id}", 0,__FILE__,__LINE__)) )
			{
				if ( $rr['proposer_id']    != $this->ms_user_id ) return "The {$o->ms_obj_name} submission with audit id {$this->mn_audit_id} belongs to the user {$rr['proposer_id']}.";
				if ( $rr['disposition_cd'] != '-'				) return "The status of this submission appears to have changed to ".dvdaf3_decode($rr['disposition_cd'],DVDAF3_DICT_DISPOSITION).".";
				$n_line = CTrace::logError(__FILE__,__LINE__,__CLASS__.'::'.__FUNCTION__,__LINE__,"Unable to figure why a {$o->ms_obj_name} submission was not displayed.",'');
				return "We run into an unknown error. Please try again in a few minutes. If the issue does not resolve itself please seek help at <a href='http://dvdaf.net' target='_blank'>http://dvdaf.net</a> ".
					   "and mention the following code: ".dvdaf3_getvalue('REMOTE_ADDR',DVDAF3_SERVER)."-".dvdaf3_getvalue('REQUEST_TIME',DVDAF3_SERVER)."-{$n_line}. We apologize for the inconvenience.";
			}
			return "A submission with an audit id of {$this->mn_audit_id} was not found.";
		}

		if ( $this->mn_obj_id > 0 )
		{
			if ( CSql::query_and_fetch1("SELECT 1 FROM {$o->ms_tbl_obj} WHERE {$o->ms_key} = {$this->mn_obj_id}", 0,__FILE__,__LINE__) != 1 )
				return "A {$o->ms_obj_name} with an id {$this->mn_obj_id} was not found.";
			$n_line = CTrace::logError(__FILE__,__LINE__,__CLASS__.'::'.__FUNCTION__,__LINE__,"Unable to figure why we could not display a submission for an existing {$o->ms_obj_name}.",'');
			return "We run into an unknown error. Please try again in a few minutes. If the issue does not resolve itself please seek help at <a href='http://dvdaf.net' target='_blank'>http://dvdaf.net</a> ".
				   "and mention the following code: ".dvdaf3_getvalue('REMOTE_ADDR',DVDAF3_SERVER)."-".dvdaf3_getvalue('REQUEST_TIME',DVDAF3_SERVER)."-{$n_line}. We apologize for the inconvenience.";
		}
		return "Neither a valid {$o->ms_obj_name} id nor a submission id were specified.";
	}

	function drawNotFound()
	{
		$this->ms_display_error = $this->getNotFoundReason();
		$this->drawMessages(true,false);
	}
}

?>
