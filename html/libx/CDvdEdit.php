<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWnd.php';
require $gs_root.'/libx/CWnd2.php';
require $gs_root.'/libx/navi-page.inc.php';
require $gs_root.'/libx/rights-dvd.inc.php';
require $gs_root.'/libx/dvd-update.inc.php';

define('CWnd_EDIT_INDEX'		,	0);
define('CWnd_EDIT_EDIT'			,	1);
define('CWnd_EDIT_DVD'			,	2);
define('CWnd_EDIT_NEW'			,	3);
define('CWnd_EDIT_CURRENT'		,	4);
define('CWnd_EDIT_PENDING'		,	5);
define('CWnd_EDIT_APPROVED'		,	6);
define('CWnd_EDIT_DECLINED'		,	7);
define('CWnd_EDIT_WITHDRAWN'	,	8);
define('CWnd_EDIT_DIRECTS'		,	9);

define('MSG_NOT_LOGGED_IN'		,	1);
define('MSG_ALREADY_SAVED'		,	2);
define('MSG_NOTHING_TO_SAVE'	,	3);
define('MSG_SAVED'				,	4);
define('MSG_ERROR_NOT_SAVED'	,	5);
define('MSG_REQUEST_SUBMITTED'	,	6);
define('MSG_ERROR_DB_CHANGED'	,	7);
define('MSG_AUTHENTICATE'		,	8);
define('MSG_LIMIT_CREATE'		,	9);
define('MSG_LIMIT_EDIT'			,  10);
define('MSG_OLD_NOT_FOUND'		,  11);
define('MSG_SOMEONE_ELSE'		,  12);
define('MSG_EDIT_NOT_FOUND'		,  13);
define('MSG_WHAT_NOT_FOUND'		,  14);
define('MSG_DVD_NOT_FOUND'		,  15);
define('MSG_UNABLE_TO_PROC'		,  16);
define('MSG_USER_BLOCKED'		,  17);

function getPicLocation($s_pic_name,$b_thumbs)
{
	$n = strpos($s_pic_name,'-');
	if ( $n <= 2 ) $n = strpos($s_pic_name,'.');
	if ( $n > 2 )
	{
		$c = intval($s_pic_name{$n - 1});
		$c = $c <= 1 ? '' : ($c <= 4 ? 'a.' : ($c <= 6 ? 'b.' : 'c.'));
		return "http://{$c}dv1.us/p". ($b_thumbs ? '0' : '1') ."/". substr($s_pic_name, $n - 3, 3);
	}
	return '';
}

class CDvdEdit extends CWnd2
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;
		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-dvd-edit_{$this->mn_lib_version}.js'></script>\n";
		$this->mb_include_cal		= true;
		$this->mb_include_menu		= true;
		$this->mb_get_user_status	= true;
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

		$this->mn_show_mode			= DVDAF_SHOW_EDIT;
		$this->mn_template_mode		= DVDAF_SELECT_DVD;
		$this->mb_inner_frame		= false;

		$this->mn_edit				= 0;
		$this->mn_new				= 0;
		$this->mn_pic				= 0;
		$this->mn_used_edit			= 0;
		$this->mn_used_new			= 0;
		$this->mn_used_pic			= 0;
		$this->mb_mod				= false;
		$this->mn_lookback			= 20;

		$this->ms_edit_id			= dvdaf_getvalue('edit', DVDAF_GET);
		$this->mn_edit_id			= 0;
		$this->ms_dvd_id			= dvdaf_getvalue('dvd' , DVDAF_GET);
		$this->mn_dvd_id			= 0;
		$this->ms_page				= dvdaf_getvalue('pg'	, DVDAF_GET | DVDAF_LOWER);
		$this->mn_page				= 1;
		$this->mn_seq_last			= 0;
		$this->mn_edit_mode			= CWnd_EDIT_INDEX;
		$this->ms_url				= substr(($this->ms_edit_id ? "&edit={$this->ms_edit_id}" : '').
											 ($this->ms_dvd_id  ? "&dvd={$this->ms_dvd_id}"	  : ''), 1);
		$this->ms_url				= dvdaf_getvalue('SCRIPT_NAME', DVDAF_SERVER) . ($this->ms_url ? '?' . $this->ms_url : '');

		$this->mn_prop_genre		= 0;
		$this->ms_prop_imdb			= '';
		$this->ms_prop_dvd_ids		= '';

		if ( ! $this->ms_page || is_numeric($this->ms_page) )
		{
			if ( $this->ms_page ) $this->mn_page = intval($this->ms_page);
			if ( $this->ms_edit_id )
			{
				$a_edit_id = explode(',', $this->ms_edit_id);
				$this->mn_seq_last = count($a_edit_id);
				if ( $this->mn_page > 0 && $this->mn_page <= $this->mn_seq_last )
				{
					$this->mn_edit_id = intval($a_edit_id[$this->mn_page - 1]);
					if ( $this->mn_edit_id > 0 ) $this->mn_edit_mode = CWnd_EDIT_EDIT;
				}
			}
			else
			{
				if ( $this->ms_dvd_id == 'new' )
				{
					if ( $this->mn_page >= 0 )
					$this->mn_edit_mode = CWnd_EDIT_NEW;
				}
				else
				{
					if ( $this->ms_dvd_id )
					{
						$a_dvd_id = explode(',', $this->ms_dvd_id);
						$this->mn_seq_last = count($a_dvd_id);
						if ( $this->mn_page > 0 && $this->mn_page <= $this->mn_seq_last )
						{
							$this->mn_dvd_id = 0 + $a_dvd_id[$this->mn_page - 1];
							if ( $this->mn_dvd_id > 0 ) $this->mn_edit_mode = CWnd_EDIT_DVD;
						}
					}
				}
			}
		}

		if ( $this->mn_edit_mode == CWnd_EDIT_INDEX )
		{
			switch ( $this->ms_page )
			{
			case 'current':   $this->mn_edit_mode = CWnd_EDIT_CURRENT;   break;
			case 'pending':   $this->mn_edit_mode = CWnd_EDIT_PENDING;   break;
			case 'approved':  $this->mn_edit_mode = CWnd_EDIT_APPROVED;  break;
			case 'declined':  $this->mn_edit_mode = CWnd_EDIT_DECLINED;  break;
			case 'withdrawn': $this->mn_edit_mode = CWnd_EDIT_WITHDRAWN; break;
			case 'directs':   $this->mn_edit_mode = CWnd_EDIT_DIRECTS;   break;
			}
			if ( $this->mn_edit_mode != CWnd_EDIT_INDEX )
			{
				$this->mb_corners		= false;
				$this->mn_footer_type	= CWnd_FOOTER_NONE;
				$this->mn_header_type	= CWnd_HEADER_NONE;
				$this->ms_margin_top	= $this->ms_margin_bottom = $this->ms_margin_left = $this->ms_margin_right = $this->mn_max_width_px = '';
				$this->mb_inner_frame	= true;
			}
		}

		switch ( $this->mn_edit_mode )
		{
		case CWnd_EDIT_NEW:
			$this->ms_header_title	= 'Submit New DVD';
			$this->ms_title			= "New DVD Submission";
			break;
		case CWnd_EDIT_INDEX:
		case CWnd_EDIT_EDIT:
		case CWnd_EDIT_DVD:
		default:
			$this->ms_header_title	= 'Edit DVD Info';
			$this->ms_title			= "DVD Information Change Request";
			break;
		}
	}

	function validUserAccess()
	{
		if ( ! $this->mb_logged_in							  ) return CUser_NOACCESS_GUEST;
		if ( $this->mb_mod && ! $this->mb_logged_in_this_sess ) return CUser_NOACCESS_SESSION;
		return CUser_ACCESS_GRANTED;
	}

	function getOnLoadJavaScript()
	{
		if ( $this->mb_inner_frame )
			return "parent.Tab.iframeResize(window.frameElement.id)";
		else
			return "this.focus()";
	}

	function getFooterJavaScript()
	{
		if ( $this->mn_edit_mode == CWnd_EDIT_INDEX )
		{
			return	"if($('my-tab'))Tab.setup('my-tab',{background:'#e1ecf5', loadtab:0, width:'100%'});";
		}
		else
		{
			$s_config =
					'{baseDomain:"'.$this->ms_base_subdomain.'"'.
					',onPopup:DvdEdit.onPopup'.
					',objId:'.$this->mn_dvd_id.
					',context:1'.
					',ulCountry:1'.
					',ulDir:1'.
					',ulDvdTitle:1'.
					',ulExplain:1'.
					',ulGenre:1'.
					',ulLang:1'.
					',ulPub:1'.
					',ulRegion:1'.
					',imgPreLoad:"spin.explain.drop.undo"'.
					'}';

			return	"function setSearchVal(mode, target, val){DvdEdit.setSearchVal(mode,target,val);};".
					"function onMenuClick(action){DvdEdit.onClick(action);};".
					"Filmaf.config({$s_config});".
					"DvdEdit.setup();";
		}
	}

	function drawBodyBottom()
	{
		switch ( $this->mn_edit_mode )
		{
		case CWnd_EDIT_CURRENT:
		case CWnd_EDIT_PENDING:
		case CWnd_EDIT_APPROVED:
		case CWnd_EDIT_DECLINED:
		case CWnd_EDIT_WITHDRAWN:
		case CWnd_EDIT_DIRECTS:
			break;
		default:
			parent::drawBodyBottom();
			break;
		}
	}

	function useNewDvdVersion($n_dvd_id)
	{
		$i = CSql::query_and_fetch1("SELECT dvd_id ".
									  "FROM dvd ".
									 "WHERE dvd_id = $n_dvd_id ".
									   "and dvd_updated_by = '{$this->ms_user_id}' ".
									   "and dvd_updated_tm > date_add(now(), INTERVAL -30 MINUTE) ".
									   "and (dvd_verified_tm is NULL || dvd_verified_by = dvd_updated_by)",
									0,__FILE__,__LINE__);
		return $i <> $n_dvd_id;
	}

	function tellUser($n_line, $n_what, $s_parm)
	{
		switch ( $n_what )
		{
		case MSG_NOT_LOGGED_IN:		$this->ms_display_error		= "Sorry, we can not honor your request because you are not logged in."; break;
		case MSG_ALREADY_SAVED:		$this->ms_display_error		= "Submission already saved."; break;
		case MSG_NOTHING_TO_SAVE:	$this->ms_display_affected	= "Nothing to save."; break;
		case MSG_SAVED:				$this->ms_display_affected	= getAffectedMessage($s_parm, $this->ms_prop_dvd_ids, $this->mn_prop_genre, $this->ms_prop_imdb, $this->ms_base_subdomain); break;
		case MSG_ERROR_NOT_SAVED:	$this->ms_display_error		= "Sorry, your changes could not be saved. Please try again in a few minutes. ".
																  "If that does not help, please contact support <a href='http://dvdaf.net/' target='_blank'>here</a>. We appologize ".
																  "for the inconvenience."; break;
		case MSG_REQUEST_SUBMITTED:	$this->ms_display_affected	= 'Request for change submitted.'; break;
		case MSG_ERROR_DB_CHANGED:	$this->ms_display_error		= "Sorry, the database data was changed just before you submitted your changes. Please try again."; break;
		case MSG_AUTHENTICATE:		$this->ms_display_error		= "This level of access requires that you be re-authenticated. ".
																  "Please click <a href='/utils/login.html?redirect=/utils/close.html%3Fmsg%3D1' target='_blank' onclick='".
																  "return Win.openPop(0,\"_blank\",this.href,680,520,1,0)'>here</a>. Once this session has been authenticated you ".
																  "can either redo your changes or in some browsers hit &lt;F5&gt; to reload this page and retry to save it. ".
																  "Thanks!."; break;
		case MSG_LIMIT_CREATE:		$this->ms_display_error		= "Your daily limit of direct creates has been reached. ".
																  "Your change has been submitted as a request and will be processed by a moderator."; break;
		case MSG_LIMIT_EDIT:		$this->ms_display_error		= "Your daily limit of direct updates has been reached. ".
																  "Your change has been submitted as a request and will be processed by a moderator."; break;
		case MSG_OLD_NOT_FOUND:		$this->ms_display_error		= "Sorry, we are unable to edit your submission. Please create a new submission (as opposed to editing an old one). Thanks."; break;
		case MSG_SOMEONE_ELSE:		$this->ms_display_error		= "Sorry, the submission with an id of '{$s_parm}' belongs to someone else."; break;
		case MSG_EDIT_NOT_FOUND:	$this->ms_display_error		= "Sorry, we did not find a submission with an id of '{$s_parm}'."; break;
		case MSG_WHAT_NOT_FOUND:	$this->ms_display_error		= "Sorry, we are unable to find and audit id {$s_parm}. You may have bookmarked a stale URL."; break;
		case MSG_DVD_NOT_FOUND:		$this->ms_display_error		= "Sorry, we are unable to find a DVD with an id of {$s_parm}."; break;
		case MSG_UNABLE_TO_PROC:	$this->ms_display_error		= "Sorry, we are unable to process your request at this time."; break;
		case MSG_USER_BLOCKED:		$this->ms_display_error		= "Sorry, we encountered an error. We appologize for any inconveniece, please try again in a few minutes."; break;
		}
		if ( $this->ms_display_error ) $this->ms_display_error .= " (code {$n_line})";

		return false;
	}

	function propagateGenre($n_dvd_id)
	{
		if ( $this->mb_mod && dvdaf_getvalue('cb_a_genre', DVDAF_POST | DVDAF_LOWER) == 'on' )
		{
			$this->mn_prop_genre	= dvdaf_getvalue('n_a_genre'	, DVDAF_POST | DVDAF_INT);
			$this->ms_prop_imdb		= sprintf('%07d', dvdaf_getvalue('n_a_imdb_id_0', DVDAF_POST | DVDAF_INT));
			$this->ms_prop_dvd_ids	= propagateGenre($n_dvd_id, $this->ms_user_id, $this->mn_prop_genre, $this->ms_prop_imdb);
		}
	}

	function saveNew($b_direct, $b_log_direct)
	{
		if ( $b_direct )
		{
			unset($_POST['n_zaproposer_notes']);
			unset($_POST['o_zaproposer_notes']);
		}
		$this->validateInput($s_update, $s_values, DVDAF_INSERT, ! $b_direct);
		$s_seed = dvdaf_getvalue('seed', DVDAF_POST);

		if ( $s_update && $s_values && $s_seed )
		{
			if ( $b_direct )
				$this->saveNewDirect($s_update, $s_values, $s_seed, $b_log_direct);
			else
				$this->saveNewSubmit($s_update, $s_values, $s_seed);

			CSql::query_and_free("UPDATE dvdaf_user_2 SET last_submit_tm = now() WHERE user_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
		}
		else
		{
			$this->tellUser(__LINE__, MSG_NOTHING_TO_SAVE, false);
		}
	}

	function saveNewDirect($s_update, $s_values, $s_seed, $b_log_direct)
	{
		$n_dvd_id	= CSql::query_and_fetch1("SELECT dvd_id FROM dvd WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);
		if ( $n_dvd_id )
		{
			$this->ms_redirect = "/utils/x-dvd-edit.html?dvd={$n_dvd_id}";
			return $this->tellUser(__LINE__, MSG_ALREADY_SAVED, false);
		}

		$s_values .= "max(dvd_id)+1, '{$s_seed}', now(), now(), '{$this->ms_user_id}'";
		$s_update .= 'dvd_id, creation_seed, dvd_created_tm, dvd_updated_tm, dvd_updated_by';
		if ( $this->mb_mod )
		{
			$s_values .= ", now(), '{$this->ms_user_id}', 0";
			$s_update .= ", dvd_verified_tm, dvd_verified_by, verified_version";
		}
		$s_update	= str_replace('update_justify', 'last_justify', $s_update);
		$s_update	= "INSERT INTO dvd ({$s_update}) SELECT {$s_values} from dvd";

		$n_updated	= CSql::query_and_free($s_update,0,__FILE__,__LINE__);
		$n_dvd_id	= 0;

		if ( $n_updated )
			$n_dvd_id = CSql::query_and_fetch1("SELECT dvd_id FROM dvd WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);

		if ( $n_dvd_id	)
		{
			if ( $b_log_direct )
				CSql::query_and_free("INSERT INTO dvd_direct_update (user_id, update_tm, dvd_id, version_id, new_title) ".
									 "VALUES ('{$this->ms_user_id}', now(), {$n_dvd_id}, 0, 'Y')",0,__FILE__,__LINE__);
			$this->propagateGenre($n_dvd_id);
			$this->ms_redirect = "/utils/x-dvd-edit.html?dvd={$n_dvd_id}";
			CSql::query_and_free("CALL update_dvd_search_index({$n_dvd_id},1)",0,__FILE__,__LINE__);
			$this->tellUser(__LINE__, MSG_SAVED, $n_dvd_id);
		}
		else
		{
			$this->tellUser(__LINE__, MSG_ERROR_NOT_SAVED, false);
		}
	}

	function saveNewSubmit($s_update, $s_values, $s_seed)
	{
		$n_edit_id	= CSql::query_and_fetch1("SELECT edit_id FROM dvd_submit WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);
		if ( $n_edit_id )
		{
			$this->ms_redirect = "/utils/x-dvd-edit.html?edit={$n_edit_id}";
			return $this->tellUser(__LINE__, MSG_ALREADY_SAVED, false);
		}

		$s_values .= "0, '{$s_seed}', 'N', '{$this->ms_user_id}', now(), now()";
		$s_update .= "dvd_id, creation_seed, request_cd, proposer_id, proposed_tm, updated_tm";
		$s_update  = "INSERT INTO dvd_submit ({$s_update}) VALUES ({$s_values})";

		$n_updated = CSql::query_and_free($s_update,0,__FILE__,__LINE__);
		$n_dvd_id  = 0;

		if ( $n_updated )
			$n_edit_id = CSql::query_and_fetch1("SELECT edit_id FROM dvd_submit WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);

		if ( $n_edit_id	)
		{
			$this->ms_redirect = "/utils/x-dvd-edit.html?edit={$n_edit_id}";
			$this->tellUser(__LINE__, MSG_REQUEST_SUBMITTED, false);
		}
		else
		{
			$this->tellUser(__LINE__, MSG_ERROR_NOT_SAVED, false);
		}
	}

	function saveEdit($b_direct, $n_dvd_id, $n_version_id, $b_version, $b_log_direct, $b_history, $n_edit_id)
	{
		$s_update = '';
		$s_values = '';

		if ( $b_direct )
		{
			unset($_POST['n_zaproposer_notes']);
			unset($_POST['o_zaproposer_notes']);
			$this->validateInput($s_update, $s_values, DVDAF_UPDATE, false);
		}
		else
		{
			if ( $n_edit_id )
				$this->validateInput($s_update, $s_values, DVDAF_UPDATE, true);
			else
				$this->validateInput($s_update, $s_values, DVDAF_INSERT, true);
		}
		$s_seed = dvdaf_getvalue('seed', DVDAF_POST);

		if ( $s_update )
		{
			if ( $b_direct )
				$this->saveEditDirect($s_update, $n_dvd_id, $n_version_id, $b_version, $b_log_direct, $b_history);
			else
				$this->saveEditSubmit($s_update, $s_values, $n_dvd_id, $n_version_id, $n_edit_id, $s_seed);

			CSql::query_and_free("UPDATE dvdaf_user_2 SET last_submit_tm = now() WHERE user_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
		}
		else
		{
			$this->tellUser(__LINE__, MSG_NOTHING_TO_SAVE, false);
		}
	}

	function saveEditDirect($s_update, $n_dvd_id, $n_version_id, $b_version, $b_log_direct, $b_history)
	{
		$s_update	= str_replace('update_justify', 'last_justify', $s_update);

		if ( strpos($s_update, 'last_justify') === false )
			$s_update .= "last_justify = '-', ";

		$n_updated = 1;
		$s_update  = "UPDATE dvd ".
						"SET $s_update".
							($b_version ? "version_id = version_id + 1, " : '').
							"dvd_updated_tm = now(), ".
							"dvd_updated_by = '{$this->ms_user_id}', ".
							"dvd_edit_id = 0, ".
							($this->mb_mod ? "dvd_verified_tm = now(), ".
											 "dvd_verified_by = '{$this->ms_user_id}', ".
											 "verified_version = version_id "		// strange behavior!!! should really be "version + 1"
										   : "dvd_verified_tm = NULL, ".
											 "dvd_verified_by = '-' ").
					  "WHERE dvd_id = {$n_dvd_id} and version_id = {$n_version_id}";

		// save history
		if ( $b_history )
		{
			$n_updated = snapHistoryVersion($n_dvd_id, $n_version_id);

			// could not save history, check to see if it is stuck
			if ( ! $n_updated )
			{
				$n_cur_version = CSql::query_and_fetch1("SELECT version_id from dvd WHERE dvd_id = {$n_dvd_id}",0,__FILE__,__LINE__);
				if ( $n_cur_version == $n_version_id )
				{
					CSql::query_and_free("DELETE FROM dvd_hist WHERE dvd_id = {$n_dvd_id} and version_id = {$n_version_id}",0,__FILE__,__LINE__);
					$n_updated = snapHistoryVersion($n_dvd_id, $n_version_id);
				}
			}
		}

		if ( $n_updated )
			$n_updated = CSql::query_and_free($s_update,0,__FILE__,__LINE__);

		if ( $n_updated )
		{
			if ( $b_log_direct )
				CSql::query_and_free("INSERT INTO dvd_direct_update (user_id, update_tm, dvd_id, version_id, edit_title) ".
									 "VALUES ('{$this->ms_user_id}', now(), {$n_dvd_id}, {$n_version_id}, 'Y')",0,__FILE__,__LINE__);
			$this->propagateGenre($n_dvd_id);
		}

		if ( $n_updated )
		{
			CSql::query_and_free("CALL update_dvd_search_index({$n_dvd_id},1)",0,__FILE__,__LINE__);
			$this->tellUser(__LINE__, MSG_SAVED, $n_dvd_id);
		}
		else
			$this->tellUser(__LINE__, MSG_ERROR_DB_CHANGED, false);
	}

	function saveEditSubmit($s_update, $s_values, $n_dvd_id, $n_version_id, $n_edit_id, $s_seed)
	{
		$b_unreject_pic = false;
		if ( $n_edit_id )
		{
			$s_update = "UPDATE dvd_submit ".
						   "SET {$s_update}disposition_cd = '-', updated_tm = now(), hist_version_id = {$n_version_id} ".
						 "WHERE edit_id = {$n_edit_id}";
			$b_unreject_pic = $n_dvd_id == 0;
		}
		else
		{
			$n_edit_id	= CSql::query_and_fetch1("SELECT edit_id FROM dvd_submit WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);
			if ( $n_edit_id )
			{
//				$this->ms_redirect = "/utils/x-dvd-edit.html?edit={$n_edit_id}";
				return $this->tellUser(__LINE__, MSG_ALREADY_SAVED, false);
			}
			$s_update = "INSERT INTO dvd_submit ({$s_update}dvd_id, request_cd, proposer_id, proposed_tm, updated_tm, hist_version_id) ".
						"VALUES ({$s_values}{$n_dvd_id}, 'E', '{$this->ms_user_id}', now(), now(), {$n_version_id})";
		}

		$n_updated = CSql::query_and_free($s_update, 0,__FILE__,__LINE__);

		if ( $n_updated )
		{
//			$this->ms_redirect = "/utils/x-dvd-edit.html?edit={$n_edit_id}";
			if ( $b_unreject_pic )
				CSql::query_and_free("UPDATE pic_submit SET disposition_cd = '-' WHERE obj_type = 'D' and obj_edit_id = {$n_edit_id} and disposition_cd = 'R'",0,__FILE__,__LINE__);
			$this->tellUser(__LINE__, MSG_REQUEST_SUBMITTED, false);
		}
		else
		{
			$this->tellUser(__LINE__, MSG_ERROR_NOT_SAVED, false);
		}
	}

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		if ( CSql::query_and_fetch1("SELECT block_submissions FROM dvdaf_user_2 WHERE user_id = '{$this->ms_user_id}'", 0,__FILE__,__LINE__) != 'N' )
			return $this->tellUser(__LINE__, MSG_USER_BLOCKED, false);

		getDvdRights($this);

		switch ( dvdaf_getvalue('act', DVDAF_POST | DVDAF_LOWER) )
		{
		case 'new':
			$b_submit		= false;
			$b_direct		= false;
			$b_log_direct = false;

			if ( $this->mb_mod || $this->mn_new )
			{
				if ( ! $this->mb_mod && $this->mn_new <= $this->mn_used_new )
				{
					$b_submit = true;
					$this->tellUser(__LINE__, MSG_LIMIT_EDIT, false);
				}
				else
				{
					$b_log_direct = ! $this->mb_mod;
					$b_direct	  = $this->mb_logged_in_this_sess;
					if ( ! $b_direct ) $this->tellUser(__LINE__, MSG_AUTHENTICATE, false);
				}
			}
			else
			{
				$b_submit = true;
			}
			if ( $b_direct || $b_submit )
				$this->saveNew($b_direct, $b_log_direct);
			break;

		case 'edit':
			$n_version_id = dvdaf_getvalue('version_id', DVDAF_POST | DVDAF_INT);
			$n_edit_id	  = dvdaf_getvalue('edit_id', DVDAF_POST | DVDAF_INT);
			$n_dvd_id	  = 0;
			if ( $n_edit_id )
			{
				$n_edit_id = CSql::query_and_fetch1("SELECT edit_id FROM dvd_submit WHERE proposer_id = '{$this->ms_user_id}' and edit_id = {$n_edit_id}", 0,__FILE__,__LINE__);
				if ( ! $n_edit_id )
				{
					$this->tellUser(__LINE__, MSG_OLD_NOT_FOUND, false);
					break;
				}
			}
			else
			{
				$n_dvd_id = dvdaf_getvalue('dvd_id', DVDAF_POST | DVDAF_INT);
				if ( $n_dvd_id )
					$n_edit_id = CSql::query_and_fetch1("SELECT edit_id FROM dvd_submit WHERE dvd_id = {$n_dvd_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'", 0,__FILE__,__LINE__);
			}
			$b_submit	  = false;
			$b_direct	  = false;
			$b_history	  = false;
			$b_version	  = false;
			$b_log_direct = false;

			if ( $this->mb_mod || $this->mn_edit )
			{
				if ( ! $this->mb_mod )
					$b_log_direct = $this->useNewDvdVersion($n_dvd_id);

				if ( ! $this->mb_mod && ($this->mn_edit <= $this->mn_used_edit && $b_log_direct) )
				{
					$b_version = $b_submit = true;
					$this->tellUser(__LINE__, MSG_LIMIT_EDIT, false);
				}
				else
				{
					$b_version = $b_direct = $b_history = $this->mb_logged_in_this_sess;
					if ( ! $b_direct ) $this->tellUser(__LINE__, MSG_AUTHENTICATE, false);
				}
			}
			else
			{
				$b_submit = true;
			}
			if ( $b_direct || $b_submit )
				$this->saveEdit($b_direct, $n_dvd_id, $n_version_id, $b_version, $b_log_direct, $b_history, $n_edit_id);
			break;

		case 'del_sub':
			$n_edit_id	= dvdaf_getvalue('edit_id', DVDAF_POST | DVDAF_INT);
			if ( $n_edit_id )
			{
				$n_rows = CSql::query_and_free("UPDATE dvd_submit SET disposition_cd = 'W', updated_tm = now() ".
												"WHERE edit_id = {$n_edit_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'",0,__FILE__,__LINE__);
						  CSql::query_and_free("UPDATE pic_submit SET disposition_cd = 'W', updated_tm = now() ".
												"WHERE obj_type = 'D' and obj_edit_id = {$n_edit_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'",0,__FILE__,__LINE__);
				$this->ms_display_affected = $n_rows ? "$n_rows submission request".($n_rows > 1 ? 's' : '')." withdrawn.<br />" : "Sorry, your withdraw operation did not affect any submission requests.<br />";
			}
			break;
		}
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		if ( $this->ms_redirect )
		{
			parent::drawBodyPage();
			return;
		}

		switch ( $this->mn_edit_mode )
		{
		case CWnd_EDIT_INDEX:
			$this->drawBodyIndex();
			break;

		case CWnd_EDIT_EDIT:
		case CWnd_EDIT_DVD:
		case CWnd_EDIT_NEW:
			$this->drawBodyPageDvd();
			break;

		case CWnd_EDIT_CURRENT:
			$this->drawCurrentList();
			break;

		case CWnd_EDIT_PENDING:
			$this->drawSubmissions('Pending', "disposition_cd = '-'", "pending submissions");
			break;
		case CWnd_EDIT_APPROVED:
//			if ( $this->mb_mod )
				$this->drawSubmissions('Recently Approved', "updated_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd in ('A','P')", "approved submissions in the past {$this->mn_lookback} days");
//			else
//				$this->drawSubmissions('Recently Approved', "disposition_cd in ('A','P')", "approved submissions");
			break;
		case CWnd_EDIT_DECLINED:
//			if ( $this->mb_mod )
				$this->drawSubmissions('Recently Declined', "updated_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd = 'R'", "declined submissions in the past {$this->mn_lookback} days");
//			else
//				$this->drawSubmissions('Recently Declined', "disposition_cd = 'R'", "declined submissions");
			break;
		case CWnd_EDIT_WITHDRAWN:
//			if ( $this->mb_mod )
				$this->drawSubmissions('Recently Withdrawn', "updated_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd = 'W'", "withdrawn submissions in the past {$this->mn_lookback} days");
//			else
//				$this->drawSubmissions('Recently Withdrawn', "disposition_cd = 'W'", "withdrawn submissions");
			break;
		case CWnd_EDIT_DIRECTS:
			$this->drawDirects();
			break;
		}
	}

	function drawBodyPageDvd() // <<-------------------------------<< 7.2.x
	{
		$n_page		= $this->mn_page;
		$n_seq_last = $this->mn_seq_last;
		$n_dvd_id	= 0;
		$n_edit_id	= 0;

		switch ( $this->mn_edit_mode )
		{
		case CWnd_EDIT_EDIT:
			$rr = CSql::query_and_fetch("SELECT proposer_id, dvd_id FROM dvd_submit WHERE edit_id = {$this->mn_edit_id}", 0,__FILE__,__LINE__);
			if ( $rr )
			{
				$s_proposer_id = $rr['proposer_id'];
				if ( $s_proposer_id == $this->ms_user_id )
				{
					$n_dvd_id	= $rr['dvd_id'];
					$n_edit_id = $this->mn_edit_id;
				}
				else
				{
					return $this->tellUser(__LINE__, MSG_SOMEONE_ELSE, $this->mn_edit_id);
				}
			}
			else
			{
				return $this->tellUser(__LINE__, MSG_EDIT_NOT_FOUND, $this->mn_edit_id);
			}
			break;
		case CWnd_EDIT_DVD:
			$n_dvd_id	= $this->mn_dvd_id;
			$n_edit_id	= CSql::query_and_fetch1("SELECT max(edit_id) edit_id FROM dvd_submit WHERE dvd_id = {$n_dvd_id} and proposer_id = '{$this->ms_user_id}' and disposition_cd = '-'", 0,__FILE__,__LINE__);
			break;
		case CWnd_EDIT_NEW:
			break;
		}

		/*
		if ( $n_edit_id )
			read from existing submission
		else
			if ( $n_dvd_id )
			read from dvd
			else
			creating a new dvd
		*/
		$s_tmpl_top = dvdaf_parsetemplate	   ("", $s_select, $s_from, $s_where, $s_sort, $this->mn_show_mode, $this->mn_template_mode, '', '', $n_dvd_id);
		$s_tmpl_mid = dvdaf_parsetemplateformat("", $this->mn_show_mode+1, $this->mn_template_mode, '', '', $n_dvd_id);
		$s_tmpl_sup = dvdaf_parsetemplateformat("", $this->mn_show_mode+2, $this->mn_template_mode, '', '', $n_dvd_id);

		if ( $n_edit_id )
		{
			$s_action = 'edit';
			if ( $n_dvd_id )
			{
				$s_select = // dvd
							"x.dvd_id, x.version_id, x.pic_status, x.pic_name, '-' pic_overwrite, x.best_price, x.dvd_created_tm, x.dvd_updated_tm, ".
							"x.dvd_updated_by, x.last_justify, x.dvd_verified_tm, x.dvd_verified_by, x.verified_version, x.pic_count, ".
							// dvd + dvd_submit
							"a.dvd_title, x.dvd_title x_dvd_title, ".				"a.film_rel_year, x.film_rel_year x_film_rel_year, ".
							"a.director, x.director x_director, ".					"a.publisher, x.publisher x_publisher, ".
							"a.orig_language, x.orig_language x_orig_language, ".	"a.country, x.country x_country, ".
							"a.region_mask, x.region_mask x_region_mask, ".			"a.genre, x.genre x_genre, ".
							"a.media_type, x.media_type x_media_type, ".			"a.num_titles, x.num_titles x_num_titles, ".
							"a.num_disks, x.num_disks x_num_disks, ".				"a.source, x.source x_source, ".
							"a.rel_status, x.rel_status x_rel_status, ".			"a.film_rel_dd, x.film_rel_dd x_film_rel_dd, ".
							"a.dvd_rel_dd, x.dvd_rel_dd x_dvd_rel_dd, ".			"a.imdb_id, x.imdb_id x_imdb_id, ".
							"a.dvd_oop_dd, x.dvd_oop_dd x_dvd_oop_dd, ".			"a.list_price, x.list_price x_list_price, ".
							"a.sku, x.sku x_sku, ".									"a.upc, x.upc x_upc, ".
							"a.asin, x.asin x_asin, ".								"a.amz_country, x.amz_country x_amz_country, ".
							// dvd_submit
							"datediff(now(),a.reviewed_tm) reviewed_int, ".
							"a.edit_id, a.request_cd, a.disposition_cd, a.proposer_id, a.proposer_notes, a.proposed_tm, a.updated_tm, ".
							"a.reviewer_id, a.reviewer_notes, a.reviewed_tm, a.hist_version_id, a.update_justify update_justify_edit";
				$s_from	  = "dvd_submit a LEFT JOIN dvd x ON a.dvd_id = x.dvd_id";
				$s_where  = "edit_id = {$n_edit_id} and proposer_id = '{$this->ms_user_id}'";
			}
			else
			{
				$s_select = // dvd
							"0 dvd_id, 0 version_id, '-' pic_status, '-' pic_name, '-' pic_overwrite, 0 best_price, null dvd_created_tm, null dvd_updated_tm, ".
							"'-' dvd_updated_by, '-' last_justify, null dvd_verified_tm, '-' dvd_verified_by, -1 verified_version, 0 pic_count, ".
							// dvd + dvd_submit
							"a.dvd_title, a.dvd_title x_dvd_title, ".				"a.film_rel_year, a.film_rel_year x_film_rel_year, ".
							"a.director, a.director x_director, ".					"a.publisher, a.publisher x_publisher, ".
							"a.orig_language, a.orig_language x_orig_language, ".	"a.country, a.country x_country, ".
							"a.region_mask, a.region_mask x_region_mask, ".			"a.genre, a.genre x_genre, ".
							"a.media_type, a.media_type x_media_type, ".			"a.num_titles, a.num_titles x_num_titles, ".
							"a.num_disks, a.num_disks x_num_disks, ".				"a.source, a.source x_source, ".
							"a.rel_status, a.rel_status x_rel_status, ".			"a.film_rel_dd, a.film_rel_dd x_film_rel_dd, ".
							"a.dvd_rel_dd, a.dvd_rel_dd x_dvd_rel_dd, ".			"a.imdb_id, a.imdb_id x_imdb_id, ".
							"a.dvd_oop_dd, a.dvd_oop_dd x_dvd_oop_dd, ".			"a.list_price, a.list_price x_list_price, ".
							"a.sku, a.sku x_sku, ".									"a.upc, a.upc x_upc, ".
							"a.asin, a.asin x_asin, ".								"a.amz_country, a.amz_country x_amz_country, ".
							// dvd_submit
							"datediff(now(),a.reviewed_tm) reviewed_int, ".
							"a.edit_id, a.request_cd, a.disposition_cd, a.proposer_id, a.proposer_notes, a.proposed_tm, a.updated_tm, ".
							"a.reviewer_id, a.reviewer_notes, a.reviewed_tm, a.hist_version_id, a.update_justify update_justify_edit";
				$s_from	  = "dvd_submit a";
				$s_where  = "edit_id = {$n_edit_id} and proposer_id = '{$this->ms_user_id}'";
			}
		}
		else
		{
			if ( $n_dvd_id )
			{
				$s_action = 'edit';
				$s_select = // dvd
							"x.dvd_id, x.version_id, x.pic_status, x.pic_name, '-' pic_overwrite, x.best_price, x.dvd_created_tm, x.dvd_updated_tm, ".
							"x.dvd_updated_by, x.last_justify, x.dvd_verified_tm, x.dvd_verified_by, x.verified_version, x.pic_count, ".
							// dvd + dvd_submit
							"x.dvd_title, x.dvd_title x_dvd_title, ".				"x.film_rel_year, x.film_rel_year x_film_rel_year, ".
							"x.director, x.director x_director, ".					"x.publisher, x.publisher x_publisher, ".
							"x.orig_language, x.orig_language x_orig_language, ".	"x.country, x.country x_country, ".
							"x.region_mask, x.region_mask x_region_mask, ".			"x.genre, x.genre x_genre, ".
							"x.media_type, x.media_type x_media_type, ".			"x.num_titles, x.num_titles x_num_titles, ".
							"x.num_disks, x.num_disks x_num_disks, ".				"x.source, x.source x_source, ".
							"x.rel_status, x.rel_status x_rel_status, ".			"x.film_rel_dd, x.film_rel_dd x_film_rel_dd, ".
							"x.dvd_rel_dd, x.dvd_rel_dd x_dvd_rel_dd, ".			"x.imdb_id, x.imdb_id x_imdb_id, ".
							"x.dvd_oop_dd, x.dvd_oop_dd x_dvd_oop_dd, ".			"x.list_price, x.list_price x_list_price, ".
							"x.sku, x.sku x_sku, ".									"x.upc, x.upc x_upc, ".
							"x.asin, x.asin x_asin, ".								"x.amz_country, x.amz_country x_amz_country, ".
							// dvd_submit
							"0 reviewed_int, ".
							"0 edit_id, 'E' request_cd, '-' disposition_cd, '{$this->ms_user_id}' proposer_id, '-' proposer_notes, null proposed_tm, null updated_tm, ".
							"'-' reviewer_id, '-' reviewer_notes, null reviewed_tm, x.version_id hist_version_id, '-' update_justify_edit";
				$s_from   = "dvd x";
				$s_where  = "x.dvd_id = {$n_dvd_id}";
			}
			else
			{
				$s_action = 'new';
				$s_select = // dvd
							"0 dvd_id, 0 version_id, '-' pic_status, '-' pic_name, '-' pic_overwrite, 0 best_price, null dvd_created_tm, null dvd_updated_tm, ".
							"'-' dvd_updated_by, '-' last_justify, null dvd_verified_tm, '-' dvd_verified_by, -1 verified_version, 0 pic_count, ".
							// dvd + edit_title
							"'' dvd_title, '' x_dvd_title, ".						"0 film_rel_year, 0 x_film_rel_year, ".
							"'-' director, '-' x_director, ".						"'-' publisher, '-' x_publisher, ".
							"'-' orig_language, '-' x_orig_language, ".				"'-' country, '-' x_country, ".
							"0 region_mask, 0 x_region_mask, ".						"99999 genre, 99999 x_genre, ".
							"'D' media_type, 'D' x_media_type, ".					"1 num_titles, 1 x_num_titles, ".
							"1 num_disks, 1 x_num_disks, ".							"'A' source, 'A' x_source, ".
							"'-' rel_status, '-' x_rel_status, ".					"'-' film_rel_dd, '-' x_film_rel_dd, ".
							"'-' dvd_rel_dd, '-' x_dvd_rel_dd, ".					"'-' dvd_oop_dd, '-' x_dvd_oop_dd, ".
							"'-' imdb_id, '-' x_imdb_id, ".							"0 list_price, 0 x_list_price, ".
							"'-' sku, '-' x_sku, ".									"'-' upc, '-' x_upc, ".
							"'-' asin, '-' x_asin, ".								"'-' amz_country, '-' x_amz_country, ".
							// edit_title
							"0 reviewed_int, ".
							"0 edit_id, 'N' request_cd, '-' disposition_cd, '{$this->ms_user_id}' proposer_id, '-' proposer_notes, null proposed_tm, null updated_tm, ".
							"'-' reviewer_id, '-' reviewer_notes, null reviewed_tm, 0 hist_version_id, '-' update_justify_edit";
				$s_from   = '';
				$s_where  = '';
			}
		}
		$a_line = CSql::query_and_fetch("SELECT $s_select, 0 genre_overwrite".
							($s_from  ?  " FROM $s_from"  : '').
							($s_where ? " WHERE $s_where" : ''), 0,__FILE__,__LINE__);
		if ( ! $a_line )
		{
			if ( $n_edit_id )
				$this->tellUser(__LINE__, MSG_WHAT_NOT_FOUND, "{$n_edit_id} ".($n_dvd_id ? "for DVD {$n_dvd_id} " : '')."by {$this->ms_user_id}");
			else
				if ( $n_dvd_id )
					$this->tellUser(__LINE__, MSG_DVD_NOT_FOUND, $n_dvd_id);
				else
					$this->tellUser(__LINE__, MSG_UNABLE_TO_PROC, false);
		}

		if ( $this->ms_display_error )
		{
			echo $this->getMessageString(true, 'margin:20px 50px 20px 50px');
			return;
		}

		$n_dvd_id		= $a_line['dvd_id'] ? $a_line['dvd_id'] : 0;
		$n_version_id	= $a_line['version_id'];
		$s_moderator	= $this->mb_mod ? "<input id='moderator' type='hidden' value='1' />" : '';
		$n_propagate	= $this->mb_mod ? DVDAF4_CB_PROPAGATE : 0;
		$s_rem_dashes	= $this->mb_mod ? "<div style='padding-top:30px'>Additional Moderator Options:".
											"<div><input type='button' value='Remove UPC dashes' style='width:150px;color:#008d14' onclick='DvdEdit.removeUpcDashes()' /></div>".
										  "</div>"
										: '';
		$s_title		= dvdaf_getbrowserfield($a_line, DVDAF_zz_title_2, DVDAF1_STYLE_ONE, 0, DVDAF3_NO_STYLE);
		$n_parm			= $this->getUserStars() > 0 ? DVDAF3_HIRES : 0;
		$b_pic			= $a_line['pic_status'] != '-';
		$b_saved		= $a_line['proposed_tm'] != '';
		$s_justify		= $a_line['last_justify'];
		$s_disable		= $a_line['disposition_cd'] == '-' || ($a_line['disposition_cd'] == 'R' && $a_line['reviewed_int'] <= 10) ? '' : "disabled='disabled' ";
		$s_del			= '';

		$a_line['update_justify'] = $a_line['update_justify_edit'];
		$s_top = dvdaf_getbrowserow($a_line, $s_tmpl_sup, 0, $this->ms_user_id, 0, DVDAF3_INPUT, DVDAF4_ZERO_DVD_ID_OK);
		$a_line['update_justify'] = $s_justify;

		if ( $b_saved )
		{
			if ( $a_line['disposition_cd'] == '-' )
				$s_del = " <input id='b_del' type='button' value='Delete request' style='width:120px' onclick='DvdEdit.withdrawSub()' {$s_disable}/>";
		}
		else
		{
			$s_top = str_replace(">Not processed<", ">No changes submitted<", $s_top);
		}

		$s_msg = $this->getMessageString(true, false);
		$s_js  = "{$this->ms_base_subdomain}/lib/cat-dvd-edit_{$this->mn_lib_version}.js";

		echo  "<form id='myform' name='myform' method='post' action='{$this->ms_url}'>".
				"<table width='100%' border='1' class='padded' style='margin-top:14px'>".
				  "<tr>".
					"<td class='x2' style='padding:4px'>".
					  "Request Status".
					"</td>".
				  "</tr>".
				  "<tr>".
					"<td>".
					  $s_top.
					"</td>".
				  "</tr>".
				"</table>".
				"<table width='100%' border='1' style='margin:14px 0px 4px 0px'>".
				  "<tr>".
					"<td colspan='2' class='x2' style='padding:4px'>".
					  "Title Information".
					"</td>".
				  "</tr>".
				"</table>".
				"<table width='100%' class='padded'>".
				  "<tr>".
					"<td width='30%' valign='top' align='center' rowspan='2' style='padding:0px 8px 0px 0px'>".
					  "<input id='act' name='act' type='hidden' value='{$s_action}' />".
					  "<input id='dvd_id' name='dvd_id' type='hidden' value='{$n_dvd_id}' />".
					  "<input id='version_id' name='version_id' type='hidden' value='{$n_version_id}' />".
					  "<input id='edit_id' name='edit_id' type='hidden' value='{$n_edit_id}' />".
					  "<input id='seed' name='seed' type='hidden' value='". date('Y-m-d H:i:s '). dvdaf_getvalue('REMOTE_ADDR', DVDAF_SERVER) ."' />".
					  $s_moderator.
					  dvdaf_getbrowserfield($a_line, DVDAF_zz_med_pic, DVDAF1_STYLE_ONE, 0, $n_parm|DVDAF3_NO_STYLE, $n_dvd_id).
					  "<div style='white-space:nowrap'>".
						"<input id='b_mpic' type='button' value='Manage Pictures' style='width:124px' onclick='DvdEdit.managePics()' /> ".
						$s_rem_dashes.
					  "</div>".
					"</td>".
					"<td width='70%' valign='top' class='og'>".
					  "<table width='100%'>".
						"<tr>".
						  "<td style='white-space:nowrap'>".
							"<input id='b_submit' type='button' value='Submit' style='width:70px' onclick='DvdEdit.validate(true,{$n_page})' {$s_disable}/> ".
							"<input id='b_cancel' type='button' value='Cancel' style='width:70px' onclick='Validate.reload(\"{$this->ms_url}&pg={$n_page}\")' {$s_disable}/>".
							$s_del.
						  "</td>".
						  "<td style='white-space:nowrap;text-align:right'>".
							formNaviPage($this->ms_url, $n_page, $n_seq_last, 'mg', '', '', true, false, false).
						  "</td>".
						"</tr>".
						($s_msg ? "<tr><td colspan='2'><div style='padding:12px 0 16px 0'>{$s_msg}</div></td></tr>" : '').
					  "</table>".
					  dvdaf_getbrowserow($a_line, $s_tmpl_top, 0, $this->ms_user_id, 0, DVDAF3_INPUT, DVDAF4_ZERO_DVD_ID_OK | $n_propagate).
					"</td>".
				  "</tr>".
				"</table>";

		dvdaf_getbrowserow($a_line, $s_tmpl_mid, 0, '', DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_DVD_ID_OK);
		echo "</form>";
		echo "<ul id='context-menu' style='display:none'><li></li></ul>";

		$this->mn_dvd_id = $n_dvd_id;
	}

	function drawBodyIndex()
	{
		$s_raw = dvdaf_getvalue('SCRIPT_NAME', DVDAF_SERVER). '?pg=';
		$s_url = $this->ms_url . (strpos($this->ms_url, '?') ? '&' : '?'). 'pg=';
		$s_err = $this->getMessageString(true, 'margin:0 16px 16px 16px');

		echo	"<div style='margin:24px 10px 10px 10px'>".
				  $s_err.
				  "<div id='my-tab'>".
					($this->ms_edit_id || $this->ms_dvd_id ? "<li><a href='{$s_url}current'>Current Edits</a></li>" : '').
					"<li><a href='{$s_raw}pending'>Pending</a></li>".
					"<li><a href='{$s_raw}approved'>Approved</a></li>".
					"<li><a href='{$s_raw}declined'>Declined</a></li>".
					"<li><a href='{$s_raw}withdrawn'>Withdrawn</a></li>".
					"<li><a href='{$s_raw}directs'>Direct Edits</a></li>".
				  "</div>".
				"</div>";
	}

	function drawCurrentList()
	{
		$fld = '';
		$str = '';
		$a	 = array();
		$b	 = array();

		if ( $this->ms_edit_id )
			$a = explode(',', $this->ms_edit_id);
		else
			if ( $this->ms_dvd_id && $this->ms_dvd_id != 'new' )
				$a = explode(',', $this->ms_dvd_id);

		for ( $i = 0 ; $i < count($a) ; $i++ )
		{
			$a[$i]		= intval($a[$i]);
			$b[$a[$i]]	= null;
			$fld	   .= $a[$i]. ',';
		}

		if ( $fld )
		{
			$fld = substr($fld, 0, -1);
			if ( $this->ms_edit_id )
			{
				if ( ($rr = CSql::query("SELECT edit_id, dvd_id, request_cd, updated_tm, reviewer_id, reviewed_tm, dvd_title, disposition_cd, reviewer_notes FROM dvd_submit ".
										 "WHERE proposer_id = '{$this->ms_user_id}' and edit_id in ({$fld})",0,__FILE__,__LINE__)) )
				{
					while ( ($ln = CSql::fetch($rr)) ) $b[$ln['edit_id']] = $ln;
					CSql::free($rr);
				}
				for ( $i = 0 ; $i < count($a) ; $i++ )
				{
					if ( $b[$a[$i]] )
					{
						$ln   = &$b[$a[$i]];
						$id   = intval($ln['edit_id']);
						$pg   = $i + 1;
						$str .= "<tr style='text-align:center'>".
								  "<td>{$ln['updated_tm']}</td>".
								  "<td><a href='{$this->ms_url}&pg={$pg}' target='targetedit'>{$id}</a></td>".
								  "<td>{$ln['dvd_id']}</td>".
								  "<td style='text-align:left'>{$ln['dvd_title']}</td>".
								  "<td>". dvdaf_decode($ln['request_cd']	, DVDAF_DICT_REQUEST_DVD). "</td>".
								  "<td>". dvdaf_decode($ln['disposition_cd'], DVDAF_DICT_DISPOSITION ). "</td>".
								  "<td style='text-align:left'>{$ln['reviewer_notes']}</td>".
								  "<td>{$ln['reviewer_id']}</td>".
								  "<td>". ($ln['reviewed_tm'] ? $ln['reviewed_tm'] : '&nbsp;'). "</td>".
								"</tr>";
					}
				}
				if ( $str )
					$str =	"<table border='1' class='padded' width='100%'>".
							  "<tr class='x2'>".
								"<td width='1%'>Last updated time<br /><img src='{$this->ms_pics_icons}/1.gif' width='85px' height='1px' alt='' /></td>".
								"<td width='1%'>Audit&nbsp;id</td>".
								"<td width='1%'>DVD&nbsp;id</td>".
								"<td width='40%' style='text-align:left'>DVD title</td>".
								"<td width='1%'>Submission type</td>".
								"<td width='1%'>Status</td>".
								"<td width='40%' style='text-align:left'>Reviewer notes</td>".
								"<td width='1%'>Reviewed by</td>".
								"<td width='1%'>Review time<br /><img src='{$this->ms_pics_icons}/1.gif' width='85px' height='1px' alt='' /></td>".
							  "</tr>".
							  $str.
							"</table>";
			}
			else
			{
				if ( ($rr = CSql::query("SELECT dvd_id, version_id, dvd_updated_tm, dvd_title FROM dvd WHERE dvd_id in ({$fld})",0,__FILE__,__LINE__)) )
				{
					while ( ($ln = CSql::fetch($rr)) ) $b[$ln['dvd_id']] = $ln;
					CSql::free($rr);
				}
				for ( $i = 0 ; $i < count($a) ; $i++ )
				{
					if ( $b[$a[$i]] )
					{
						$ln   = &$b[$a[$i]];
						$id   = sprintf('%06d', intval($ln['dvd_id']));
						$pg   = $i + 1;
						$str .= "<tr style='text-align:center'>".
								  "<td>{$ln['dvd_updated_tm']}</td>".
								  "<td><a href='{$this->ms_url}&pg={$pg}' target='targetedit'>{$id}</a></td>".
								  "<td>{$ln['version_id']}</td>".
								  "<td style='text-align:left'>{$ln['dvd_title']}</td>".
								"</tr>";
					}
				}
				if ( $str )
					$str =	"<table border='1' class='padded' width='100%'>".
							  "<tr class='x2'>".
								"<td width='1%'>Last updated time</td>".
								"<td width='1%'>DVD id</td>".
								"<td width='1%'>Version</td>".
								"<td width='50%' style='text-align:left'>DVD title</td>".
							  "</tr>".
							  $str.
							"</table>";
			}
		}

		if ( $str )
			echo  "<div style='margin-top:20px'>".
					"<div style='margin:10px 0 10px 0;color:#142a3b;font-weight:bold;font-size:12px'>List of DVDs you requested to edit:</div>".
					$str.
//					"<table>".
				  "</div>".
				  "&nbsp";
		else
			echo  "<div style='margin-top:20px'>".
					"<div style='margin:10px 0 10px 0;color:#142a3b'>Sorry, you are not editing anything at the moment. Please check the other tabs.</div>".
				  "</div>".
				  "&nbsp";
	}

	function drawSubmissions($s_what, $s_criteria, $s_what_not)
	{
		$str = '';
		$rr  = "SELECT d.edit_id dvd_edit_id, 0 pic_edit_id, d.dvd_id, 0 pic_id, d.dvd_title title, ".
					  "d.request_cd, e.descr request_txt, 0 version_id, 0 sub_version_id, ".
					  "(SELECT a.pic_name FROM dvd a WHERE a.dvd_id = d.dvd_id) pic_name, ".
					  "'' transforms_old, ".
					  "'' pic_uploaded_tm, '' pic_uploaded_by, '' pic_edited_tm, '' pic_edited_by, '' pic_verified_tm, ".
					  "'' pic_verified_by, '' proposer_id, '' proposed_tm, '' uploaded_pic, '' transforms_new, 0 obj_edit_id, ".
					  "now() pic_refresh_tm, ".
					  "d.updated_tm, d.reviewer_id, d.reviewed_tm, d.disposition_cd, d.reviewer_notes, ".
					  "d.disposition_cd dvd_disposition_cd, ".
					  "if(d.reviewed_tm > d.updated_tm, d.reviewed_tm, d.updated_tm) sort_1,	".
					  "0 sort_2 ".
			 	 "FROM dvd_submit d ".
				 "LEFT JOIN decodes e ON domain_type = 'request_cd' and d.request_cd = e.code_char ".
				"WHERE proposer_id = '{$this->ms_user_id}' and {$s_criteria} ".
			 "UNION ".
			   "SELECT s.obj_edit_id dvd_edit_id, s.pic_edit_id, s.obj_id dvd_id, s.pic_id, '' title, ".
					  "s.request_cd, e.descr request_txt, p.version_id, p.sub_version_id, ".
					  "s.pic_name, ".
					  "p.transforms transforms_old, ".
					  "p.pic_uploaded_tm, p.pic_uploaded_by, p.pic_edited_tm, p.pic_edited_by, p.pic_verified_tm, ".
					  "p.pic_verified_by, s.proposer_id, s.proposed_tm, s.uploaded_pic, s.transforms transforms_new, s.obj_edit_id, ".
					  "s.updated_tm pic_refresh_tm, ".
					  "s.updated_tm, s.reviewer_id, s.reviewed_tm, s.disposition_cd, s.reviewer_notes, ".
					  "IF(s.obj_edit_id,(SELECT a.disposition_cd FROM dvd_submit a WHERE a.edit_id = s.obj_edit_id),'?') dvd_disposition_cd, ".
					  "IF(s.obj_edit_id,(SELECT if(a.reviewed_tm > a.updated_tm, a.reviewed_tm, a.updated_tm) FROM dvd_submit a WHERE a.edit_id = s.obj_edit_id), if(s.reviewed_tm > s.updated_tm, s.reviewed_tm, s.updated_tm)) sort_1, ".
					  "IF(s.reviewed_tm > s.updated_tm, s.reviewed_tm, s.updated_tm) sort_2 ".
				 "FROM pic_submit s ".
				 "LEFT JOIN pic p ON s.pic_id = p.pic_id ".
				 "LEFT JOIN decodes e ON domain_type = 'request_cd (pic)' and s.request_cd = e.code_char ".
				"WHERE proposer_id = '{$this->ms_user_id}' and {$s_criteria} and s.obj_type = 'D' ".

				"ORDER BY sort_1 DESC, sort_2";

		if ( ($rr = CSql::query($rr, 0,__FILE__,__LINE__)) )
		{
			while ( ($ln = CSql::fetch($rr)) )
			{
				$s_rand	= str_replace(' ','', str_replace(':','', str_replace('-','', $ln['pic_refresh_tm'])));
				if ( $ln['pic_edit_id'] )
				{
					if ( $ln['disposition_cd'] == 'A' && $ln['pic_name'] != '-' )
					{
//						$s_src = "<img src='http://dv1.us/p0/".getPicDir($ln['pic_name'])."/{$ln['pic_name']}.gif?tm={$s_rand}'>";
						$s_src = "<img src='".getPicLocation($ln['pic_name'],true)."/{$ln['pic_name']}.gif?tm={$s_rand}'>";
					}
					else
					{
						$s_src = $ln['uploaded_pic'] == '' || $ln['uploaded_pic'] == '-' ? sprintf('%06d',$ln['pic_edit_id']) : $ln['uploaded_pic'];
						$s_src = "<img src='{$this->ms_base_subdomain}/uploads/{$s_src}-prev.gif?tm={$s_rand}'>";
					}
					$s_sub	= "{$this->ms_base_subdomain}/utils/x-pic-edit.html?pic={$ln['pic_id']}&pic_edit={$ln['pic_edit_id']}&obj_type=dvd&obj={$ln['dvd_id']}".
							  ($ln['dvd_edit_id'] ? "&obj_edit={$ln['dvd_edit_id']}" : '');
					$s_sub	= "Pic&nbsp;Sub<br /><a href='javascript:void(Win.openStd(\"{$s_sub}\",\"target_pic\"))'>{$ln['pic_edit_id']}</a>";
					$s_obj	= $ln['pic_id'] ? "Pic<br />{$ln['pic_id']}" : "New<br />picture";
					if ( $ln['dvd_edit_id'] )
						if ( $ln['dvd_id'] )
							$s_title = "Picture for DVD submission {$ln['dvd_edit_id']} for DVD {$ln['dvd_id']} (".dvdaf_decode($ln['dvd_disposition_cd'],DVDAF_DICT_DISPOSITION).")";
						else
							$s_title = "Picture for DVD submission {$ln['dvd_edit_id']} for a new DVD (".dvdaf_decode($ln['dvd_disposition_cd'],DVDAF_DICT_DISPOSITION).")";
					else
						if ( $ln['dvd_id'] )
							$s_title = "Picture for DVD {$ln['dvd_id']}";
						else
							$s_title = "Picture for a new DVD}";
					$s_title = "<table><tr><td>{$s_src}</td><td>{$s_title}</td></tr></table>";
					$s_req   = dvdaf_decode($ln['request_cd'], DVDAF_DICT_REQUEST_PIC);
				}
				else
				{
					if ( $ln['pic_name'] && $ln['pic_name'] != '-' )
					{
//						$s_src = "<img src='http://dv1.us/p0/".getPicDir($ln['pic_name'])."/{$ln['pic_name']}.gif?tm={$s_rand}'>";
						$s_src = "<img src='".getPicLocation($ln['pic_name'],true)."/{$ln['pic_name']}.gif?tm={$s_rand}'>";
					}
					else
					{
						$s_src = "<img src='http://dv1.us/di/pic-empty.gif' width='63' height='90' />";
					}
					$s_sub	 = "DVD&nbsp;Sub<br /><a href='/utils/x-dvd-edit.html?edit={$ln['dvd_edit_id']}' target='targetedit'>{$ln['dvd_edit_id']}</a>";
					$s_obj	 = $ln['dvd_id'] ? "DVD<br />{$ln['dvd_id']}" : "New<br />DVD";
					$s_title = "<table><tr><td>{$s_src}</td><td>{$ln['title']}</td></tr></table>";
					$s_req	 = dvdaf_decode($ln['request_cd'], DVDAF_DICT_REQUEST_DVD);
				}
				$str .= "<tr style='text-align:center'>".
						  "<td>{$ln['updated_tm']}</td>".
						  "<td>$s_sub</td>".
						  "<td>$s_obj</td>".
						  "<td style='text-align:left'>{$s_title}</td>".
						  "<td>$s_req</td>".
						  "<td>". dvdaf_decode($ln['disposition_cd'], DVDAF_DICT_DISPOSITION ). "</td>".
						  "<td style='text-align:left'>{$ln['reviewer_notes']}</td>".
						  "<td>{$ln['reviewer_id']}</td>".
						  "<td>". ($ln['reviewed_tm'] ? $ln['reviewed_tm'] : '&nbsp;'). "</td>".
						"</tr>";
			}
			CSql::free($rr);
		}

		if ( $str )
			echo  "<div style='margin-top:20px'>".
					"<div style='margin:10px 0 10px 0;color:#142a3b;font-weight:bold;font-size:12px'>{$s_what} Submissions:</div>".
					"<table border='1' class='padded' width='100%'>".
					  "<tr class='x2'>".
						"<td width='1%'>Last &nbsp;&nbsp;updated&nbsp;&nbsp;</td>".
						"<td width='1%'>Sub</td>".
						"<td width='1%'>Object</td>".
						"<td width='40%' style='text-align:left'>Object</td>".
						"<td width='1%'>Sub type</td>".
						"<td width='1%'>Status</td>".
						"<td width='40%' style='text-align:left'>Reviewer notes</td>".
						"<td width='1%'>Reviewed by</td>".
						"<td width='1%'>&nbsp;&nbsp;&nbsp;Review&nbsp;&nbsp;&nbsp; time</td>".
					  "</tr>".
					  $str.
					"</table>".
				  "</div>".
				  "&nbsp";
		else
			echo  "<div style='margin-top:20px'>".
					"<div style='margin:10px 0 10px 0;color:#142a3b'>Sorry, you have no {$s_what_not}.</div>".
				  "</div>".
				  "&nbsp";
	}

	function drawDirects()
	{
		$str = '';
		$rr	= "SELECT z.dvd_id, z.version_id, b.version_id curr_version_id, z.dvd_updated_tm, b.dvd_title ".
				"FROM (SELECT dvd_id, max(version_id) version_id, max(dvd_updated_tm) dvd_updated_tm ".
					    "FROM (SELECT dvd_id, version_id, dvd_updated_tm ".
								"FROM dvd ".
							   "WHERE dvd_edit_id = 0 and dvd_updated_by = '{$this->ms_user_id}' and dvd_updated_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) ".
							"UNION ".
							  "SELECT dvd_id, version_id, dvd_updated_tm ".
					 			"FROM dvd_hist ".
							   "WHERE dvd_edit_id = 0 and dvd_updated_by = '{$this->ms_user_id}' and dvd_updated_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY)) a ".
					   "GROUP BY dvd_id)z ".
				"LEFT JOIN dvd b ON z.dvd_id = b.dvd_id ".
				"ORDER BY z.dvd_updated_tm DESC";

		if ( ($rr = CSql::query($rr,0,__FILE__,__LINE__)) )
		{
			while ( ($ln = CSql::fetch($rr)) )
			{
				$id   = sprintf('%06d', intval($ln['dvd_id']));
				$str .= "<tr style='text-align:center'>".
						  "<td>{$ln['dvd_updated_tm']}</td>".
						  "<td><a href='{$this->ms_base_subdomain}/search.html?has={$id}&init_form=str0_has_{$id}&pm=one' target='filmaf'>{$id}</a></td>".
						  "<td>". ($ln['version_id'] == $ln['curr_version_id'] ? 'current' : $ln['version_id']) ."</td>".
						  "<td>{$ln['curr_version_id']}</td>".
						 "<td style='text-align:left'>{$ln['dvd_title']}</td>".
						"</tr>";
			}
			CSql::free($rr);
		}

		$s_dir = '';
		if ( ! $this->mb_mod )
		{
			if ( $this->mn_edit || $this->mn_new || $this->mn_pic )
			{
				$n_edit = $this->mn_edit - $this->mn_used_edit; if ( $n_edit < 0 ) $n_edit = 0;
				$n_new	= $this->mn_new	- $this->mn_used_new ; if ( $n_new	< 0 ) $n_new	= 0;
				$n_pic	= $this->mn_pic	- $this->mn_used_pic ; if ( $n_pic	< 0 ) $n_pic	= 0;
				if ( $this->mn_edit ) $s_dir .= ($s_dir ? '<br />' : ''). "Daily direct edits: {$this->mn_edit} <span class='hl'>" .($this->mn_edit == $n_edit ? '(0 used)' : "({$n_edit} remaining)."). "</span>";
				if ( $this->mn_new	) $s_dir .= ($s_dir ? '<br />' : ''). "Daily direct creates: {$this->mn_new} <span class='hl'>".($this->mn_new	== $n_new	? '(0 used)' : "({$n_new} remaining)." ). "</span>";
				if ( $this->mn_pic	) $s_dir .= ($s_dir ? '<br />' : ''). "Daily direct pics: {$this->mn_pic} <span class='hl'>"	.($this->mn_pic	== $n_pic	? '(0 used)' : "({$n_pic} remaining)." ). "</span>";
			}
			$s_dir	= "<div style='margin:10px 0 20px 0'>{$s_dir}</div>";
		}

		echo	"<div style='margin-top:20px'>".
				  $s_dir;

		if ( $str )
			echo  "<div style='margin:10px 0 10px 0;color:#142a3b;font-weight:bold;font-size:12px'>Recently Directly Edited Titles:</div>".
					"<table border='1' class='padded' width='100%'>".
					  "<tr class='x2'>".
						"<td width='5%'>Last updated time<br /><img src='{$this->ms_pics_icons}/1.gif' width='85px' height='1px' alt='' /></td>".
						"<td width='1%'>&nbsp;DVD&nbsp;id&nbsp;</td>".
						"<td width='1%'>Updated Version</td>".
						"<td width='1%'>Current Version</td>".
						"<td width='50%' style='text-align:left'>DVD title</td>".
					  "</tr>".
					  $str.
					"</table>";
		
		echo	  "<div style='margin-top:20px'>".
					"Direct editing the FilmAf database is afforded to selected FilmAf members.<br />".
					"For more information please check our <a href='/utils/help-filmaf.html' target='_blank'>Star Member Priviledges</a>.".
				  "</div>".
				"</div>".
				"&nbsp";
	}
}

?>
