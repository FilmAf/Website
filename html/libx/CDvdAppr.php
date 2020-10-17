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
require $gs_root.'/libx/dvd-update.inc.php';
require $gs_root.'/libx/edit-pic.inc.php';

define('CWnd_EDIT_INDEX'	,	 0);
define('CWnd_EDIT_EDIT'		,	 1);
define('CWnd_EDIT_PENDING'	,	 2);
define('CWnd_EDIT_DECLINED'	,	 3);
define('CWnd_EDIT_APPROVED'	,	 4);
define('CWnd_EDIT_DIRECTS'	,	 5);

define('MSG_ALREADY_SAVED'	,	 1);
define('MSG_SAVED'		,	 2);
define('MSG_NOT_SAVED'		,	 3);
define('MSG_AUTHENTICATE'	,	 4);
define('MSG_NOT_MODERATOR'	,	 5);
define('MSG_UNKNOWN'		,	 6);
define('MSG_REJECT'		,	 7);
define('MSG_REJECT_ERROR'	,	 8);
define('MSG_NOTHING'		,	 9);

class CDvdAppr extends CWnd2
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;
		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-dvd-appr_{$this->mn_lib_version}.js'></script>\n";
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
//			"tr.top td, td.top "		."{ vertical-align:top; }".
//			"tr.section td "			."{ text-align:center; font-size:12px; padding:2px 4px 2px 4px; font-weight:bold; color:#072b4b; background-color:#edf4fa; }".
		"</style>";

		$this->mb_include_cal		= true;
		$this->mb_include_menu		= true;
		$this->mb_get_user_status	= true;

		$this->mn_show_mode			= DVDAF_SHOW_SUBMISS;
		$this->mn_template_mode		= DVDAF_SELECT_DVD;
		$this->mb_inner_frame		= false;

		$this->ms_header_title		= 'Submission Processing';
		$this->ms_title				= 'Submission Processing';
		$this->mn_echo_zoom			= true;
		$this->mb_mod				= false;
		$this->mn_lookback			= 10;

		$this->ms_edit_id			= dvdaf_getvalue('edit', DVDAF_GET);
		$this->mn_edit_id			= 0;
		$this->mn_dvd_id			= 0;
		$this->ms_page				= dvdaf_getvalue('pg'  , DVDAF_GET | DVDAF_LOWER);
		$this->mn_page				= 1;
		$this->mn_seq_last			= 0;
		$this->mn_edit_mode			= CWnd_EDIT_INDEX;
		$this->ms_url				= $this->ms_edit_id ? "edit={$this->ms_edit_id}" : '';
		$this->ms_url				= dvdaf_getvalue('SCRIPT_NAME', DVDAF_SERVER) . ($this->ms_url ? '?' . $this->ms_url : '');
		$this->ms_reviewer_notes	= '-';

		$this->mn_prop_genre		= 0;
		$this->ms_prop_imdb			= '';
		$this->ms_prop_dvd_ids		= '';

		if ( isset($_POST['n_zareviewer_notes']) )
		{
			$this->ms_reviewer_notes = dvdaf_textarea2db(dvdaf_getvalue('n_zareviewer_notes', DVDAF_POST), 1000);
			unset($_POST['n_zareviewer_notes']);
		}

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
		}

		if ( $this->mn_edit_mode == CWnd_EDIT_INDEX )
		{
			switch ( $this->ms_page )
			{
			case 'pending':  $this->mn_edit_mode = CWnd_EDIT_PENDING;  break;
			case 'rejected': $this->mn_edit_mode = CWnd_EDIT_DECLINED; break;
			case 'approved': $this->mn_edit_mode = CWnd_EDIT_APPROVED; break;
			case 'directs':  $this->mn_edit_mode = CWnd_EDIT_DIRECTS;  break;
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
	}

	function validUserAccess()
	{
		if ( ! $this->mb_logged_in			 ) return CUser_NOACCESS_GUEST;
		if ( ! $this->mb_mod				 ) return CUser_NOACCESS_USER;
		if ( ! $this->mb_logged_in_this_sess ) return CUser_NOACCESS_SESSION;
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
			return "window.name='targetappr';if($('my-tab'))Tab.setup('my-tab',{background:'#e1ecf5', loadtab:0, width:'100%'});";
		}
		else
		{
			$s_config =
				'{baseDomain:"'.$this->ms_base_subdomain.'"'.
				',onPopup:DvdApproveMenuPrep.onPopup'.
				',objId:'.$this->mn_dvd_id.
				',context:1'.
				',ulCountry:1'.
				',ulDir:1'.
				',ulDvdTitle:1'.
				',ulExplain:1'.
				',ulGenre:1'.
				',ulLang:1'.
				',ulDvdComments:1'.
				',ulDvdPic:1'.
				',ulPub:1'.
				',ulRegion:1'.
				',imgPreLoad:"spin.explain.drop.undo"'.
				'}';

			return
				"function setSearchVal(mode, target, val){DvdEdit.setSearchVal(mode,target,val);};".
				"function onMenuClick(action){DvdApproveMenuAction.onClick(action);};".
				"Filmaf.config({$s_config});".
				"DvdApprove.setup();";
		}
	}

	function drawBodyBottom()
	{
		switch ( $this->mn_edit_mode )
		{
		case CWnd_EDIT_PENDING:
		case CWnd_EDIT_DECLINED:
		case CWnd_EDIT_APPROVED:
		case CWnd_EDIT_DIRECTS:
			break;
		default:
			parent::drawBodyBottom();
			break;
		}
	}

	function tellUser($n_line, $n_what, $s_parm)
	{
		switch ( $n_what )
		{
		case MSG_ALREADY_SAVED:	$this->ms_display_error	= "Submission already saved as dvd_id <a href='/search.html?has={$s_parm}&init_form=str0_has_{$s_parm}&pm=one' target='filmaf'>{$s_parm}</a>."; break;
		case MSG_SAVED:			$this->ms_display_affected = getAffectedMessage($s_parm, $this->ms_prop_dvd_ids, $this->mn_prop_genre, $this->ms_prop_imdb, $this->ms_base_subdomain); break;
		case MSG_NOT_SAVED:		$this->ms_display_error	= "Submission {$s_parm} could not be saved.  Perhaps it has already been processed."; break;
		case MSG_AUTHENTICATE:	$this->ms_display_error	= "This level of access requires that you be re-authenticated. ".
														  "Please click <a href='/utils/login.html?redirect=/utils/close.html%3Fmsg%3D1' target='_blank' onclick='".
														  "return Win.openPop(0,\"_blank\",this.href,680,520,1,0)'>here</a>. Once this session has been authenticated you ".
														  "can either redo your changes or in some browsers hit &lt;F5&gt; to reload this page and retry to save it. ".
														  "Thanks!."; break;
		case MSG_NOT_MODERATOR:	$this->ms_display_error	= "Sorry, this function is only available to moderators. If you are a moderator please follow ".
														  "<a href='/utils/login.html?redirect=/utils/close.html%3Fmsg%3D1' target='_blank' onclick='".
														  "Win.openPop(0,\"_blank\",this.href,680,520,1,0)'>this link</a> to be re-authenticated. Once you have done ".
														  "that you may be able to hit &lt;F5&gt; in some browsers to reload this page."; break;
		case MSG_UNKNOWN:		$this->ms_display_error	= "Sorry, we did not find the requested submission."; break;
		case MSG_REJECT:		$this->ms_display_error	= "Submission {$s_parm} rejected."; break;
		case MSG_REJECT_ERROR:	$this->ms_display_error	= "Unable to reject submission {$s_parm}. May be it has been approved or previously rejected."; break;
		case MSG_NOTHING:		$this->ms_display_error	= "Nothing to save."; break;
		}
		if ( $this->ms_display_error ) $this->ms_display_error .= " (code {$n_line})";

		return false;
	}

	function updateSubmission($n_edit_id, $n_dvd_id, $b_approve, $b_append_rejects)
	{
		$n_dvd_id			= $n_dvd_id ? "dvd_id = {$n_dvd_id}, " : '';
		$s_reject			= dvdaf_getvalue('reject', DVDAF_POST);
		$s_disposition_cd	= $b_approve ? ($s_reject == 'none' ? 'A' : 'P') : 'R';

		if ( $b_append_rejects && $s_reject != 'none' )
		{
			$this->ms_reviewer_notes = ($this->ms_reviewer_notes == '-' ? '' : $this->ms_reviewer_notes. '<br />'). 'AUTO GEN MSG - Overwritten:'. str_replace(' a_', ' ', ' '.$s_reject);
			$this->ms_reviewer_notes = $this->ms_reviewer_notes == '' ? '-' : dvdaf_substr($this->ms_reviewer_notes, 0, 1000);
		}

		CSql::query_and_free("UPDATE dvd_submit SET {$n_dvd_id}disposition_cd = '{$s_disposition_cd}', reviewer_id = '{$this->ms_user_id}', ".
							 "reviewer_notes = '{$this->ms_reviewer_notes}', reviewed_tm = now() WHERE edit_id = {$n_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__);

		if ( ! $b_approve && ! $n_dvd_id )
		{
			CSql::query_and_free("UPDATE pic_submit SET disposition_cd = 'R', reviewer_id = '{$this->ms_user_id}', reviewed_tm = now() ".
								  "WHERE obj_type = 'D' and obj_edit_id = {$n_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__);
		}
	}

	function saveNew()
	{
		$this->validateInput($s_update, $s_values, DVDAF_INSERT, false);
		$s_seed		= dvdaf_getvalue('seed', DVDAF_POST);
		$n_edit_id	= dvdaf_getvalue('edit_id', DVDAF_POST | DVDAF_INT);
		$n_dvd_id	= 0;

		if ( $s_seed )
			$n_dvd_id = CSql::query_and_fetch1("SELECT dvd_id FROM dvd WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);

		if ( $n_dvd_id )
			return $this->tellUser(__LINE__, MSG_ALREADY_SAVED, $n_dvd_id);

		$s_values .= "max(dvd_id)+1, '{$s_seed}', now(), now(), now(), '{$this->ms_user_id}', 0, {$n_edit_id}";
		$s_update .= "dvd_id, creation_seed, dvd_created_tm, dvd_updated_tm, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id";
		$s_update  = "INSERT INTO dvd ({$s_update}) SELECT {$s_values} from dvd";

		$n_updated = CSql::query_and_free($s_update,0,__FILE__,__LINE__);
		$n_dvd_id  = 0;

		if ( $n_updated )
		{
			$n_dvd_id = CSql::query_and_fetch1("SELECT dvd_id FROM dvd WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);
			if ( $n_edit_id ) $this->updateSubmission($n_edit_id, $n_dvd_id, true, true);
			$this->propagateGenre($n_dvd_id);
		}

		if ( $n_dvd_id && $n_edit_id )
		{
			CSql::query_and_free("UPDATE pic_submit SET obj_id = {$n_dvd_id} WHERE obj_edit_id = {$n_edit_id}",0,__FILE__,__LINE__);
			CSql::query_and_free("UPDATE pic_def_submit SET obj_id = {$n_dvd_id} WHERE obj_edit_id = {$n_edit_id}",0,__FILE__,__LINE__);
		}

		if ( $n_dvd_id )
		{
			CSql::query_and_free("CALL update_dvd_search_index({$n_dvd_id},1)",0,__FILE__,__LINE__);
			$this->tellUser(__LINE__, MSG_SAVED, $n_dvd_id);
		}
		else
			$this->tellUser(__LINE__, MSG_NOT_SAVED, $n_edit_id);
	}

	function propagateGenre($n_dvd_id)
	{
		if ( dvdaf_getvalue('cb_a_genre', DVDAF_POST | DVDAF_LOWER) == 'on' )
		{
			$this->mn_prop_genre	= dvdaf_getvalue('n_a_genre'	, DVDAF_POST | DVDAF_INT);
			$this->ms_prop_imdb		= sprintf('%08d', dvdaf_getvalue('n_a_imdb_id_0', DVDAF_POST | DVDAF_INT));
			$this->ms_prop_dvd_ids	= propagateGenre($n_dvd_id, $this->ms_user_id, $this->mn_prop_genre, $this->ms_prop_imdb);
		}
	}

	function saveEdit()
	{
		$this->validateInput($s_update, $s_values, DVDAF_UPDATE, false);
		$n_edit_id = dvdaf_getvalue('edit_id', DVDAF_POST | DVDAF_INT);
		$n_dvd_id  = dvdaf_getvalue('dvd_id' , DVDAF_POST | DVDAF_INT);
		$s_update .= "version_id = version_id+1, dvd_updated_tm = now(), dvd_verified_tm = now(), dvd_verified_by = '{$this->ms_user_id}', verified_version = version_id, dvd_edit_id = {$n_edit_id}";

		unset($_POST['n_a_last_justify']);
		unset($_POST['n_a_dvd_updated_by']);
		$this->validateInput($s_change, $s_values, DVDAF_UPDATE, false);

		if ( $s_change )
		{
			if ( $n_edit_id )
			{
				$n_edit_id = CSql::query_and_fetch1("SELECT edit_id FROM dvd_submit WHERE edit_id = {$n_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__);
				if ( $n_edit_id )
				{
					snapHistory($n_dvd_id);

//					if ( strpos($s_update, 'update_justify') === false )
//						$s_update .= "update_justify = '-', ";
					if ( strpos($s_update, 'last_justify') === false )
						$s_update .= "last_justify = '-', ";

					$n_updated = CSql::query_and_free("UPDATE dvd SET {$s_update} WHERE dvd_id = {$n_dvd_id}",0,__FILE__,__LINE__);
					if ( $n_updated )
					{
						$this->updateSubmission($n_edit_id, $n_dvd_id, true, true);
						$this->propagateGenre($n_dvd_id);
						CSql::query_and_free("CALL update_dvd_search_index({$n_dvd_id},1)",0,__FILE__,__LINE__);
						return $this->tellUser(__LINE__, MSG_SAVED, $n_dvd_id);
					}
				}
			}
			$this->tellUser(__LINE__, MSG_NOT_SAVED, $n_edit_id);
		}
		else
		{
			$this->tellUser(__LINE__, MSG_NOTHING, false);
			if ( ! $this->ms_reviewer_notes || $this->ms_reviewer_notes == '-' ) $this->ms_reviewer_notes = 'AUTO GEN MSG - No changes detected';
				$this->updateSubmission($n_edit_id, 0, false, false);
		}
	}

	function discard()
	{
		$n_edit_id = $n_orig = dvdaf_getvalue('edit_id', DVDAF_POST | DVDAF_INT);
		if ( $n_edit_id )
		{
			$n_edit_id = CSql::query_and_fetch1("SELECT edit_id FROM dvd_submit WHERE edit_id = {$n_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__);
			if ( $n_edit_id )
			{
				$this->updateSubmission($n_edit_id, 0, false, false);
				return $this->tellUser(__LINE__, MSG_REJECT, $n_edit_id);
			}
		}
		$this->tellUser(__LINE__, MSG_REJECT_ERROR, $n_orig);
	}

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		$this->mb_mod = intval(CSql::query_and_fetch1("SELECT moderator_cd FROM dvdaf_user WHERE user_id = '{$this->ms_user_id}'", 0,__FILE__,__LINE__)) >= 5;

		if ( $this->mb_mod )
		{
			switch ( dvdaf_getvalue('act', DVDAF_POST | DVDAF_LOWER) )
			{
			case 'edit':	$this->saveEdit(); break;
			case 'new':	 $this->saveNew();	break;
			case 'discard': $this->discard();	break;
			}
		}
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		if ( $this->mb_mod )
		{
			switch ( $this->mn_edit_mode )
			{
			case CWnd_EDIT_INDEX:
				$this->drawBodyIndex();
				break;

			case CWnd_EDIT_EDIT:
				$this->drawBodyPageDvd();
				break;

			case CWnd_EDIT_PENDING:
				$this->drawSubmissions(	'Pending',
										"disposition_cd = '-'",
										"updated_tm", '',
										"pending submissions",
										false,
										true);
				break;
			case CWnd_EDIT_DECLINED:
				$this->drawSubmissions(	'Rejected',
										"reviewed_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd = 'R'",
										"reviewed_tm", " DESC",
										"declined submissions in the past {$this->mn_lookback} days",
										true,
										false);
				break;
			case CWnd_EDIT_APPROVED:
				$this->drawSubmissions(	'Approved (includes partial)',
										"reviewed_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd in ('A','P')",
										"reviewed_tm", " DESC",
										"approved submissions in the past {$this->mn_lookback} days",
										false,
										false);
				break;
			case CWnd_EDIT_DIRECTS:
				$this->drawDirects();
				break;
			}
		}
		else
		{
			$this->tellUser(__LINE__, MSG_NOT_MODERATOR, false);
			echo $this->getMessageString(true, 'margin:20px 50px 20px 50px');
		}
	}

	function drawBodyPageDvd() // <<-------------------------------<< 7.2.x
	{
		$n_page		= $this->mn_page;
		$n_seq_last = $this->mn_seq_last;
		$n_edit_id	= intval($this->mn_edit_id);
		$ln			= CSql::query_and_fetch("SELECT dvd_id, request_cd FROM dvd_submit WHERE edit_id = {$n_edit_id}", 0,__FILE__,__LINE__);
		$n_dvd_id	= $ln['dvd_id'];
		$s_request	= $ln['request_cd'];
		$ln			= false;
		$s_pics		= '';

		if ( $n_dvd_id == '' || ! $n_edit_id )
			return $this->tellUser(__LINE__, MSG_UNKNOWN, false);

		$n_dvd_id				= intval($n_dvd_id);
		$s_def_pic				= 0;
		$n_req_def_edit_id		= 0;
		$n_req_def_pic_id		= 0;
		$n_req_def_pic_edit_id	= 0;
		$s_def_dispo_txt		= '';
		$s_def_dispo_cd			= '';

		if ( $n_dvd_id )
		{
			$s_def_pic = CSql::query_and_fetch1("SELECT b.pic_id ".
												  "FROM dvd a ".
												  "LEFT JOIN dvd_pic b ON a.dvd_id = b.dvd_id ".
												  "LEFT JOIN pic c ON b.pic_id = c.pic_id and a.pic_name = c.pic_name ".
												 "WHERE a.dvd_id = {$n_dvd_id}", 0,__FILE__,__LINE__);
		}

		if ( ($rr = CSql::query_and_fetch("SELECT s.def_edit_id, s.pic_id, s.pic_edit_id, s.disposition_cd, d.descr disposition_txt ".
											"FROM pic_def_submit s ".
											"LEFT JOIN decodes d ON d.domain_type = 'disposition_cd' and s.disposition_cd = d.code_char ".
										   "WHERE s.obj_type = 'D' and s.obj_id = {$n_dvd_id} and s.obj_edit_id = {$n_edit_id}", 0,__FILE__,__LINE__)) )
		{
			$n_req_def_edit_id		= $rr['def_edit_id'];
			$n_req_def_pic_id		= $rr['pic_id'];
			$n_req_def_pic_edit_id = $rr['pic_edit_id'];
			$s_def_dispo_txt		= $rr['disposition_txt'];
			$s_def_dispo_cd		= $rr['disposition_cd'];
		}

		$n_pic = 0;
		$a_str = array();
		$k	   = -1;
		if ( ($rr = CSql::query(getPicEditListSql($n_dvd_id, $n_edit_id, ''), 0,__FILE__,__LINE__)) )
		{
			$pc = CSql::fetch($rr);
			while ( $pc )
			{
				$k++;
				$j					= 0;
				$a_str[$k]			= array();
				$a_str[$k][$j]		= array();
				$a_str[$k][$j][0]	= drawPicSubCurr ($pc, $s_def_pic, true);
				$a_str[$k][$j][1]	= drawPicSubProp ($pc, $pc['disposition_cd'], $pc['request_cd'], $this->ms_base_subdomain, $s_prop_id, true, $this->ms_user_id == $pc['proposer_id']);
				$a_str[$k][$j][2]	= drawPicSubNotes($pc, $n_req_def_edit_id, $n_req_def_pic_id, $n_req_def_pic_edit_id, $pc['disposition_txt'], $s_def_dispo_txt, true, $s_prop_id);
				$n_pic				= $pc['pic_id'];
				$pc					= CSql::fetch($rr);
				while ( $pc && $n_pic && $n_pic == $pc['pic_id'] )
				{
					$j++;
					$a_str[$k][$j]	  = array();
					$a_str[$k][$j][0] = '';
					$a_str[$k][$j][1] = drawPicSubProp ($pc, $pc['disposition_cd'], $pc['request_cd'], $this->ms_base_subdomain, $s_prop_id, true, $this->ms_user_id == $pc['proposer_id']);
					$a_str[$k][$j][2] = drawPicSubNotes($pc, $n_req_def_edit_id, $n_req_def_pic_id, $n_req_def_pic_edit_id, $pc['disposition_txt'], $s_def_dispo_txt, false, $s_prop_id);
					$pc = CSql::fetch($rr);
				}
				if ( ! $pc ) $pc = CSql::fetch($rr);
			}
			CSql::free($rr);
		}

		if ( $k >= 0 )
		{
			$n_rght = 0;
			$n_left = 0;
			$a_cell = array();
			for ( $i = 0 ; $i <= $k ; $i++ )
			{
				$b_left = $n_left <= $n_rght;
				$n_offs = $b_left ? 0 : 3;
				for ( $j = 0 ; $j < count($a_str[$i]) ; $j++ )
				{
					if ( $b_left ) $n = $n_left++; else $n = $n_rght++;
					if ( count($a_cell) <= $n )
					{
						$a_cell[$n] = array();
						$a_cell[$n][5] = $a_cell[$n][4] = $a_cell[$n][3] = $a_cell[$n][2] = $a_cell[$n][1] = $a_cell[$n][0] = '<td>&nbsp;</td>';
					}
					$a_cell[$n][$n_offs+0] = $a_str[$i][$j][0];
					$a_cell[$n][$n_offs+1] = $a_str[$i][$j][1];
					$a_cell[$n][$n_offs+2] = $a_str[$i][$j][2];
				}
			}
			unset($a_str);

			$n_rght = 0;
			$n_left = 0;
			for ( $i = count($a_cell) - 1 ; $i >= 0 ; $i-- )
			{
				if ( $a_cell[$i][0] == '' ) $n_left++; else if ( $n_left )
				{
					$a_cell[$i][0] = "<td rowspan='". ($n_left + 1) ."'". substr($a_cell[$i][0], 3);
					$n_left = 0;
				}
				if ( $a_cell[$i][3] == '' ) $n_rght++; else if ( $n_rght )
				{
					$a_cell[$i][3] = "<td rowspan='". ($n_rght + 1) ."'". substr($a_cell[$i][3], 3);
					$n_rght = 0;
				}
			}

			$s_pics = "<td style='padding:2px'>Current</td>".
					  "<td style='padding:2px'>Proposed</td>".
					  "<td width='50%' style='padding:2px;text-align:left'>Notes</td>";
			$s_pics = "<table border='1' width='100%'>".
						"<tr class='x1'>{$s_pics}<td>&nbsp;</td>{$s_pics}</tr>";

			for ( $i = 0 ; $i < count($a_cell) ; $i++ )
				$s_pics .= "<tr class='se' style='vertical-align:top'>". $a_cell[$i][0]. $a_cell[$i][1]. $a_cell[$i][2]. "<td>&nbsp;</td>". $a_cell[$i][3]. $a_cell[$i][4]. $a_cell[$i][5]. "</tr>";
			unset($a_cell);

			$s_pics .= "</table>";
		}
		else
		{
			$s_pics	= '&nbsp;';
		}

		$s_tmpl	= dvdaf_parsetemplate("", $s_select, $s_from, $s_where, $s_sort, $this->mn_show_mode, $this->mn_template_mode, '', '', $n_dvd_id);

		if ( ! ($ln = CSql::query_and_fetch("SELECT ".
				// dvd
				"x.dvd_id, x.version_id, x.pic_status, '-' pic_overwrite, x.pic_name, x.best_price, x.dvd_created_tm, x.dvd_updated_tm, ".
				"x.dvd_updated_by, x.last_justify, x.dvd_verified_tm, x.dvd_verified_by, x.verified_version, x.pic_count, ".
				// dvd + dvd_submit
				"x.dvd_title, x.dvd_title x_dvd_title, a.dvd_title p_dvd_title, ".
				"x.film_rel_year, x.film_rel_year x_film_rel_year, a.film_rel_year p_film_rel_year, ".
				"x.director, x.director x_director, a.director p_director, ".
				"x.publisher, x.publisher x_publisher, a.publisher p_publisher, ".
				"x.orig_language, x.orig_language x_orig_language, a.orig_language p_orig_language, ".
				"x.country, x.country x_country, a.country p_country, ".
				"x.region_mask, x.region_mask x_region_mask, a.region_mask p_region_mask, ".
				"x.genre, x.genre x_genre, a.genre p_genre, ".
				"x.media_type, x.media_type x_media_type, a.media_type p_media_type, ".
				"x.num_titles, x.num_titles x_num_titles, a.num_titles p_num_titles, ".
				"x.num_disks, x.num_disks x_num_disks, a.num_disks p_num_disks, ".
				"x.source, x.source x_source, a.source p_source, ".
				"x.rel_status, x.rel_status x_rel_status, a.rel_status p_rel_status, ".
				"x.film_rel_dd, x.film_rel_dd x_film_rel_dd, a.film_rel_dd p_film_rel_dd, ".
				"x.dvd_rel_dd, x.dvd_rel_dd x_dvd_rel_dd, a.dvd_rel_dd p_dvd_rel_dd, ".
				"x.dvd_oop_dd, x.dvd_oop_dd x_dvd_oop_dd, a.dvd_oop_dd p_dvd_oop_dd, ".
				"x.imdb_id, x.imdb_id x_imdb_id, a.imdb_id p_imdb_id, ".
				"x.list_price, x.list_price x_list_price, a.list_price p_list_price, ".
				"x.sku, x.sku x_sku, a.sku p_sku, ".
				"x.upc, x.upc x_upc, a.upc p_upc, ".
				"x.asin, x.asin x_asin, a.asin p_asin, ".
				"x.amz_country, x.amz_country x_amz_country, a.amz_country p_amz_country, ".
				// dvd_submit
				"a.edit_id, a.request_cd, a.disposition_cd, a.proposer_id, a.proposer_notes, a.proposed_tm, a.updated_tm, a.reviewer_id, ".
//				"a.reviewer_notes, a.reviewed_tm, a.hist_version_id, a.update_justify p_update_justify, a.creation_seed ".
				"a.reviewer_notes, a.reviewed_tm, a.hist_version_id, a.update_justify p_last_justify, a.creation_seed ".
				"FROM dvd_submit a LEFT JOIN dvd x ON a.dvd_id = x.dvd_id ".
				"WHERE edit_id = {$n_edit_id}", 0,__FILE__,__LINE__)) )
		{
			$ln = CSql::query_and_fetch("SELECT ".
				// dvd
				"0 dvd_id, 0 version_id, '-' pic_status, '-' pic_name, '-' pic_overwrite, 0 best_price, null dvd_created_tm, null dvd_updated_tm, ".
				"'-' dvd_updated_by, '-' last_justify, null dvd_verified_tm, '-' dvd_verified_by, -1 verified_version, 0 pic_count, ".
				// dvd + dvd_submit
				"a.dvd_title, a.dvd_title x_dvd_title, a.dvd_title p_dvd_title, ".
				"a.film_rel_year, a.film_rel_year x_film_rel_year, a.film_rel_year p_film_rel_year, ".
				"a.director, a.director x_director, a.director p_director, ".
				"a.publisher, a.publisher x_publisher, a.publisher p_publisher, ".
				"a.orig_language, a.orig_language x_orig_language, a.orig_language p_orig_language, ".
				"a.country, a.country x_country, a.country p_country, ".
				"a.region_mask, a.region_mask x_region_mask, a.region_mask p_region_mask, ".
				"a.genre, a.genre x_genre, a.genre p_genre, ".
				"a.media_type, a.media_type x_media_type, a.media_type p_media_type, ".
				"a.num_titles, a.num_titles x_num_titles, a.num_titles p_num_titles, ".
				"a.num_disks, a.num_disks x_num_disks, a.num_disks p_num_disks, ".
				"a.source, a.source x_source, a.source p_source, ".
				"a.rel_status, a.rel_status x_rel_status, a.rel_status p_rel_status, ".
				"a.film_rel_dd, a.film_rel_dd x_film_rel_dd, a.film_rel_dd p_film_rel_dd, ".
				"a.dvd_rel_dd, a.dvd_rel_dd x_dvd_rel_dd, a.dvd_rel_dd p_dvd_rel_dd, ".
				"a.dvd_oop_dd, a.dvd_oop_dd x_dvd_oop_dd, a.dvd_oop_dd p_dvd_oop_dd, ".
				"a.imdb_id, a.imdb_id x_imdb_id, a.imdb_id p_imdb_id, ".
				"a.list_price, a.list_price x_list_price, a.list_price p_list_price, ".
				"a.sku, a.sku x_sku, a.sku p_sku, ".
				"a.upc, a.upc x_upc, a.upc p_upc, ".
				"a.asin, a.asin x_asin, a.asin p_asin, ".
				"a.amz_country, a.amz_country x_amz_country, a.amz_country p_amz_country, ".
				// dvd_submit
				"a.edit_id, a.request_cd, a.disposition_cd, a.proposer_id, a.proposer_notes, a.proposed_tm, a.updated_tm, a.reviewer_id, ".
//				"a.reviewer_notes, a.reviewed_tm, a.hist_version_id, a.update_justify p_update_justify, a.creation_seed ".
				"a.reviewer_notes, a.reviewed_tm, a.hist_version_id, a.update_justify p_last_justify, a.creation_seed ".
				"FROM dvd_submit a ".
				"WHERE edit_id = {$n_edit_id}", 0,__FILE__,__LINE__);
		}

		$s_request_type		= dvdaf_decode($ln['request_cd'	], DVDAF_DICT_REQUEST_DVD);
		$s_disposition		= dvdaf_decode($ln['disposition_cd'], DVDAF_DICT_DISPOSITION );
		$s_proposer_notes	= dvdaf_db2textarea($ln['proposer_notes']);
		$s_reviewer_notes	= dvdaf_db2textarea($ln['reviewer_notes']);
		$s_proposed			= $ln['proposer_id'];
		$s_version			= $ln['hist_version_id'];	if ( $s_version			 >= 0  ) $s_version			= " - <span class='mh'>version {$s_version}</span>";
		$s_proposed_tm		= $ln['updated_tm'];										 $s_proposed_tm		= $s_proposed_tm ? "<span class='oi'>on</span> {$s_proposed_tm}{$s_version}" : '&nbsp;';
		$s_reviewed			= $ln['reviewer_id'];
		$s_reviewed_tm		= $ln['reviewed_tm'];										 $s_reviewed_tm		= $s_reviewed_tm ? "<span class='oi'>on</span> {$s_reviewed_tm}" : '&nbsp;';
		$s_current			= $ln['dvd_updated_by'];
		$s_version			= $ln['version_id'];		if ( $s_version			 >= 0  ) $s_version			= " - <span class='mh'>version {$s_version}</span>";
		$s_current_tm		= $ln['dvd_updated_tm'];									 $s_current_tm		= $s_current_tm ? "<span class='oi'>on</span> {$s_current_tm}{$s_version}" : '&nbsp;';
		$s_verified			= $ln['dvd_verified_by'];
		$s_version			= $ln['verified_version'];	if ( $s_version			 >= 0  ) $s_version			= " - <span class='mh'>version {$s_version}</span>";
		$s_verified_tm		= $ln['dvd_verified_tm'];									 $s_verified_tm		= $s_verified_tm ? "<span class='oi'>on</span> {$s_verified_tm}{$s_version}" : '&nbsp;';
		$s_created_tm		= $ln['dvd_created_tm'];									 $s_created_tm		= $s_created_tm ? "<span class='oi'>on</span> {$s_created_tm}" : '-';
		$s_seed				= $ln['creation_seed'];		if ( $s_seed			== '-' ) $s_seed			= '';
//		$s_update_justify	= $ln['p_update_justify'];	if ( $s_update_justify	== '-' ) $s_update_justify	= '';
		$s_update_justify	= $ln['p_last_justify'];	if ( $s_update_justify	== '-' ) $s_update_justify	= '';

		$s_err	= $this->getMessageString(true, 'margin:0 16px 16px 16px');
		$s_nav	= formNaviPage($this->ms_url, $n_page, $n_seq_last, 'mg', '', '', true, false, false);
		$n_pos	= strpos($s_nav, '&nbsp;&nbsp;&nbsp;');
		$s_nav	= "<div style='margin:6px 6px 6px 6px'>". substr($s_nav, $n_pos + 18). "</div>".
				  "<div style='margin:6px 6px 12px 6px'>". substr($s_nav, 0, $n_pos	). "</div>";
		$s_action = $ln['request_cd']	 == 'N' ? 'new' : 'edit';
		$s_nosave = $ln['disposition_cd'] == '-' ? '' : " disabled='disabled'";

		echo  "<div style='margin:20px 0 10px 0'>".
				$s_err.
				"<form id='myform' name='myform' method='post' action='{$this->ms_url}'>".
				  "<table border='1' class='padded'>".
					"<tr>".
					  "<td colspan='5' valign='top'>".
						"<table class='nowrap' width='100%'>".
						  "<tr>".
							"<td width='80px' style='white-space:normal'>".
							  "<div class='mh' style='text-align:center'><span class='oi'>Audit id:</span> {$n_edit_id}</div>".
							  "<div class='mh' style='text-align:center'>{$s_request_type}</div>".
							  "<input id='act' name='act' type='hidden' value='{$s_action}' />".
							  "<input id='reject' name='reject' type='hidden' value='all' />".
							  "<input id='dvd_id' name='dvd_id' type='hidden' value='{$n_dvd_id}' />".
							  "<input id='edit_id' name='edit_id' type='hidden' value='{$n_edit_id}' />".
							  "<input id='seed' name='seed' type='hidden' value='{$s_seed}' />".
//							  "<input id='n_a_update_justify' name='n_a_update_justify' type='hidden' value='{$s_update_justify}' />".
							  "<input id='n_a_last_justify' name='n_a_last_justify' type='hidden' value='{$s_update_justify}' />".
							  "<input id='n_a_dvd_updated_by' name='n_a_dvd_updated_by' type='hidden' value='{$s_proposed}' />".
							  "<img src='{$this->ms_pics_icons}/1.gif' width='80px' height='1px' alt='' />".
							"</td>".
							"<td width='1%'>".
							  "<div class='oj'>Proposer notes:</div>".
							  "<textarea class='ok' cols='60' rows='5' readonly='readonly' maxlegth='1000' wrap='soft'>{$s_proposer_notes}</textarea>".
							  "<div class='oj'>".
								"Reviewer notes: ".
								"<input type='button' style='width:140px;font-size:9px' value='Standard comments' id='mod_txt'{$s_nosave} /> ".
								"<input type='button' style='width:60px;font-size:9px' value='Clear' onclick='document.getElementById(\"n_zareviewer_notes\").value=\"\"'{$s_nosave} />".
							  "</div>".
							  "<textarea id='n_zareviewer_notes' name='n_zareviewer_notes' class='oh' wrap='soft' cols='60' rows='5' maxlegth='1000'>{$s_reviewer_notes}</textarea>".
							"</td>".
							"<td width='1%'>".
							  "<table class='nowrap' width='100%' border='1'>".
								"<tr>".
								  "<td style='text-align:center'>".
									"<div>{$s_nav}</div>".
									"<div style='margin:6px 0 6px 0'><input id='b_submit' type='button' value='Save Right' style='width:90px' onclick='DvdApprove.saveRight({$n_page})'{$s_nosave} /></div>".
									"<div style='margin:6px 0 6px 0'><input id='b_approv' type='button' value='Approve All' style='width:90px' onclick='DvdApprove.approveAll({$n_page})'{$s_nosave} /></div>".
									"<div style='margin:6px 0 6px 0'><input id='b_cancel' type='button' value='Discard All' style='width:90px' onclick='DvdApprove.discard()'{$s_nosave} /></div>".
									"<div style='margin:6px 0 6px 0'><input type='button' value='Rem UPC -' style='width:90px;color:#008d14' onclick='DvdEdit.removeUpcDashes()' /></div>".
								  "</td>".
								"</tr>".
							  "</table>".
							"</td>".
							"<td width='1%'>".
							  "<table class='nowrap' width='100%'>".
								"<tr><td class='oi'>Disposition:</td><td class='mh'>{$s_disposition}</td><td>&nbsp;</td></tr>".
								"<tr><td class='oi'>Proposed by:</td><td class='mh'>{$s_proposed}</td><td>{$s_proposed_tm}</td></tr>".
								"<tr><td class='oi'>Reviewed by:</td><td class='mh'>{$s_reviewed}</td><td>{$s_reviewed_tm}</td></tr>".
								"<tr><td colspan='3'>&nbsp;</td></tr>".
								"<tr><td colspan='3'>&nbsp;</td></tr>".
								"<tr><td class='oi'>Current version by:	</td><td class='mh'>{$s_current}</td><td>{$s_current_tm}</td></tr>".
								"<tr><td class='oi'>Last verified by:</td><td class='mh'>{$s_verified}</td><td>{$s_verified_tm}</td></tr>".
								"<tr><td class='oi' colspan='2'>Original listing created</td><td>{$s_created_tm}</td></tr>".
							  "</table>".
							"</td>".
							"<td width='95%'>".
							  "&nbsp;".
							"</td>".
						  "</tr>".
						"</table>".
					  "</td>".
					"</tr>";
		echo		"<tr>".
					  "<td colspan='5' style='border:none'>{$s_pics}</td>".
					"</tr>";
		echo		"<tr class='x2'>".
					  "<td colspan='2'>Proposed</td>".
					  "<td><input type='button' onclick='DvdApprove.copyFields(false)' value='&hellip;&laquo; Rej' style='width:54px' /></td>".
					  "<td><input type='button' onclick='DvdApprove.copyFields(true)' value='App &raquo;&hellip;' style='width:54px' /></td>".
					  "<td><table><tr>".
					  "<td width='1%'><input type='button' value='Diff Prop' style='width:70px' onclick='DvdApprove.diffFields(true)' /></td>".
					  "<td width='1%'><input type='button' value='Diff Curr' style='width:70px' onclick='DvdApprove.diffFields(false)' /></td>".
					  "<td width='98%'>Approved</td></tr></table></td>".
					"</tr>";
					dvdaf_getbrowserow($ln, $s_tmpl, 0, '', DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_DVD_ID_OK | DVDAF4_CB_PROPAGATE);
		echo	  "</table>".
				"</form>".
			  "</div>".
			  "<ul id='context-menu' style='display:none'><li></li></ul>";

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
					"<li><a href='{$s_raw}pending'>Pending</a></li>".
					"<li><a href='{$s_raw}rejected'>Rejected</a></li>".
					"<li><a href='{$s_raw}approved'>Approved</a></li>".
					"<li><a href='{$s_raw}directs'>Direct Edits</a></li>".
				  "</div>".
				"</div>";
	}

	function drawSubmissions($s_what, $s_criteria, $s_sort, $s_desc, $s_what_not, $b_resurrect, $b_star_first)
	{
		$str	= '';
		$i		= 0;
		$j		= 0;
		$max	= 200;
		$va		= '';
		$srt	= $b_star_first ? "b.membership_cd DESC, " : '';
		$ss		=	"SELECT a.*, b.membership_cd ".
					  "FROM ( SELECT d.edit_id dvd_edit_id, 0 pic_edit_id, d.dvd_id, 0 pic_id, d.dvd_title title, ".
									"d.request_cd, e.descr request_txt, 0 version_id, 0 sub_version_id, '' pic_name, '' transforms_old, ".
									"'' pic_uploaded_tm, '' pic_uploaded_by, '' pic_edited_tm, '' pic_edited_by, '' pic_verified_tm, ".
									"'' pic_verified_by, '' pic_proposer_id, '' proposed_tm, '' uploaded_pic, '' transforms_new, 0 obj_edit_id, ".
									"'' diff, '' pic_refresh_tm, ".
									"d.updated_tm, d.reviewer_id, d.reviewed_tm, d.disposition_cd, d.reviewer_notes, d.proposer_id, ".
									"d.disposition_cd dvd_disposition_cd, ".
									"d.{$s_sort} sort_1, 0 sort_2 ".
							   "FROM dvd_submit d ".
							   "LEFT JOIN decodes e ON domain_type = 'request_cd' and d.request_cd = e.code_char ".
							  "WHERE {$s_criteria} ".
							  "UNION ".
							 "SELECT s.obj_edit_id dvd_edit_id, s.pic_edit_id, s.obj_id dvd_id, s.pic_id, '' title, ".
									"s.request_cd, e.descr request_txt, p.version_id, p.sub_version_id, s.pic_name, p.transforms transforms_old, ".
									"p.pic_uploaded_tm, p.pic_uploaded_by, p.pic_edited_tm, p.pic_edited_by, p.pic_verified_tm, ".
									"p.pic_verified_by, s.proposer_id pic_proposer_id, s.proposed_tm, s.uploaded_pic, s.transforms transforms_new, s.obj_edit_id, ".
									"IF(s.pic_id, ".
									   "CONCAT_WS(', ', if(p.pic_type = s.pic_type, NULL, 'picture type'), ".
													   "if(p.transforms = s.transforms, NULL, 'transforms'), ".
													   "if(p.caption = s.caption, NULL, 'caption'), ".
													   "if(p.copy_holder = s.copy_holder, NULL, 'copyright holder'), ".
													   "if(p.copy_year = s.copy_year, NULL, 'copyright year'), ".
													   "if(p.suitability_cd = s.suitability_cd, NULL, 'suitability')), ".
									   "'') diff, ".
									"s.updated_tm pic_refresh_tm, s.updated_tm, s.reviewer_id, s.reviewed_tm, s.disposition_cd, s.reviewer_notes, s.proposer_id, ".
									"IF(s.obj_edit_id,(SELECT a.disposition_cd FROM dvd_submit a WHERE a.edit_id = s.obj_edit_id),'?') dvd_disposition_cd, ".
									"IF(s.obj_edit_id,(SELECT a.{$s_sort} FROM dvd_submit a WHERE a.edit_id = s.obj_edit_id),s.{$s_sort}) sort_1, s.{$s_sort} sort_2 ".
							   "FROM pic_submit s ".
							   "LEFT JOIN pic p ON s.pic_id = p.pic_id ".
							   "LEFT JOIN decodes e ON domain_type = 'request_cd (pic)' and s.request_cd = e.code_char ".
							  "WHERE s.obj_type = 'D' and {$s_criteria}) a ".
					  "LEFT JOIN dvdaf_user b ON a.proposer_id = b.user_id ".
					 "ORDER BY {$srt}a.sort_1{$s_desc}, a.sort_2";

		if ( ($rr = CSql::query($ss, 0,__FILE__,__LINE__)) )
		{
			$n_foo = 0;
			while ( ($ln = CSql::fetch($rr)) && $i < $max + 1 )
			{
				if ( $i < $max )
				{
					if ( $ln['pic_edit_id'] )
					{
						if ( $ln['dvd_id'] )
						{
							$s_link = "?pic={$ln['pic_id']}&mod=1&pic_edit={$ln['pic_edit_id']}&obj_type=dvd&obj={$ln['dvd_id']}".
									  ($ln['dvd_edit_id'] ? "&obj_edit={$ln['dvd_edit_id']}" : '');
							$s_link = "<a href='javascript:void(DvdApproveMenuAction.picEdit(\"{$s_link}\",0))'>review pic sub&nbsp;{$ln['pic_edit_id']}</a>".
									  "<div style='margin:4px'>or</div>".
									  "<a href='javascript:void(DvdApprove.managePics({$ln['dvd_id']}))'>open&nbsp;DVD {$ln['dvd_id']} pic&nbsp;mngt</a>";
						}
						else
						{
							$s_link = "{$ln['pic_edit_id']} need to process {$ln['dvd_edit_id']}";
							if ( $ln['dvd_disposition_cd'] == 'R' && ! $b_resurrect )
							{
								$s_link .= "<br />&nbsp;<br /><a href='javascript:void(DvdApprove.resurrect({$ln['dvd_edit_id']},{$ln['pic_edit_id']}))'>&lt;Resurrect&gt;<br />{$ln['pic_edit_id']}</a>";
							}
						}
						$s_id	= $ln['pic_id'];
						$s_title = "<table><tr>".drawPicSub($ln, 0, 0, $n_foo, 0, '', '', '', '', $ln['request_cd'], $this->ms_base_subdomain, false, false, $this->ms_user_id == $ln['proposer_id'])."</tr></table>";
					}
					else
					{
						$j++;
						$s_link	 = "<a href='/utils/x-dvd-appr.html?edit=XXX&pg={$j}' target='targetappr'>review DVD sub {$ln['dvd_edit_id']}</a>";
						$s_id	 = $ln['dvd_id'];
						$s_title = $ln['title'];
						$va	    .= $ln['dvd_edit_id'] . ',';
					}

					if ( $ln['membership_cd'] <> '-' )
					{
						$n_star	  = intval($ln['membership_cd']);
						$s_title  = "<div style='padding:0 2px 4px 2px;border-bottom:solid 1px #bd0b0b;margin-bottom:4px;color:#bd0b0b'>".
									  "<img src='http://dv1.us/st/smb{$n_star}.gif' style='position:relative;top:3px' /> ".
									  dvdaf_stardescription($n_star).
									"</div>".
									$s_title;
					}

					$s_resurrect = $b_resurrect ? "<br />&nbsp;<br /><a href='javascript:void(DvdApprove.resurrect({$ln['dvd_edit_id']},{$ln['pic_edit_id']}))'>&lt;Resurrect&gt;</a>" : '';

					$str .= "<tr style='text-align:center'>".
							  "<td>{$ln['updated_tm']}</td>".
							  "<td>{$s_link}{$s_resurrect}</td>".
							  "<td>{$s_id}</td>".
							  "<td style='text-align:left'>{$s_title}</td>".
							  "<td>{$ln['request_txt']}</td>".
							  "<td>{$ln['proposer_id']}</td>".
							  "<td>". dvdaf_decode($ln['disposition_cd'], DVDAF_DICT_DISPOSITION ). "</td>".
							  "<td style='text-align:left'>{$ln['reviewer_notes']}</td>".
							  "<td>{$ln['reviewer_id']}</td>".
							  "<td>". ($ln['reviewed_tm'] ? $ln['reviewed_tm'] : '&nbsp;'). "</td>".
							"</tr>";
				}
				$i++;
			}
			CSql::free($rr);
		}
		$va  = 'edit='. substr($va,0,-1);
		$str = str_replace('edit=XXX', $va, $str);

		if ( $str )
			echo	"<div style='margin-top:20px'>".
					  "<div style='margin:10px 0 10px 0;color:#142a3b;font-weight:bold;font-size:12px'>{$s_what} Submissions: ".( $i <= $max ? "($i)" : "(first $max)")."</div>".
						"<table border='1' class='padded' width='100%'>".
						  "<tr class='x2'>".
							"<td width='1%'>Last &nbsp;&nbsp;updated&nbsp;&nbsp;</td>".
							"<td width='1%'>Audit&nbsp;id</td>".
							"<td width='1%'>Object&nbsp;id</td>".
							"<td width='40%' style='text-align:left'>Object</td>".
							"<td width='1%'>Sub type</td>".
							"<td width='1%'>Proposed by</td>".
							"<td width='1%'>Status</td>".
							"<td width='40%' style='text-align:left'>Reviewer notes</td>".
							"<td width='1%'>Reviewed by</td>".
							"<td width='1%'>&nbsp;&nbsp;&nbsp;Review&nbsp;&nbsp;&nbsp; time</td>".
						  "</tr>".
						  $str.
						"<table>".
					  "</div>".
					  "&nbsp;";
		else
			echo	  "<div style='margin-top:20px'>".
						"<div style='margin:10px 0 10px 0;color:#142a3b'>Sorry, there are no {$s_what_not}.</div>".
					"</div>".
					"&nbsp;";
	}

	function drawDirects()
	{
		$str = '';
		$i   = 0;
		$max = 200;
		if ( ($rr = CSql::query("SELECT dvd_id, dvd_title, version_id, dvd_updated_by, dvd_updated_tm, verified_version, dvd_verified_tm, dvd_verified_by ".
								  "FROM dvd ".
								 "WHERE dvd_updated_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) ".
//								   "and version_id > 0 ".
//								   "and verified_version < version_id ".
								   "and dvd_edit_id = 0 ".
								 "ORDER BY dvd_updated_tm DESC",0,__FILE__,__LINE__)) )
		{
			while ( ($ln = CSql::fetch($rr)) && $i < $max + 1 )
			{
				if ( $i < $max )
				{
					$id	= sprintf('%06d', intval($ln['dvd_id']));
					$s_verified_version = $ln['verified_version']; if ( $s_verified_version < 0 ) $s_verified_version = '&nbsp;';
					$s_dvd_verified_tm	= $ln['dvd_verified_tm'];	if ( ! $s_dvd_verified_tm	) $s_dvd_verified_tm	= '&nbsp;';
					$str .= "<tr style='text-align:center'>".
							  "<td><a href='{$this->ms_base_subdomain}/search.html?has={$id}&init_form=str0_has_{$id}&pm=one' target='filmaf'>{$id}</a></td>".
							  "<td style='text-align:left'>{$ln['dvd_title']}</td>".
							  "<td>{$ln['version_id']}</td>".
							  "<td>{$ln['dvd_updated_by']}</td>".
							  "<td>{$ln['dvd_updated_tm']}</td>".
							  "<td>{$s_verified_version}</td>".
							  "<td>{$s_dvd_verified_tm}</td>".
							  "<td>{$ln['dvd_verified_by']}</td>".
							"</tr>";
				}
				$i++;
			}
			CSql::free($rr);
		}

		if ( $str )
			echo	"<div style='margin-top:20px'>".
					  "<div style='margin:10px 0 10px 0;color:#142a3b;font-weight:bold;font-size:12px'>Recent Directly Edited Titles: ".( $i <= $max ? "($i)" : "(first $max)")."</div>".
						"<table border='1' class='padded' width='100%'>".
						  "<tr class='x2'>".
							"<td width='1%'>&nbsp;DVD&nbsp;id&nbsp;</td>".
							"<td width='50%' style='text-align:left'>DVD&nbsp;title</td>".
							"<td width='1%'>Version</td>".
							"<td width='1%'>Updated By</td>".
							"<td width='1%'>Updated Time<br /><img src='{$this->ms_pics_icons}/1.gif' width='85px' height='1px' alt='' /></td>".
							"<td width='1%'>Last Verified Version</td>".
							"<td width='1%'>Verification Time<br /><img src='{$this->ms_pics_icons}/1.gif' width='85px' height='1px' alt='' /></td>".
							"<td width='1%'>Verified By</td>".
						  "</tr>".
						  $str.
						"</table>".
					  "</div>".
					  "&nbsp;";
		else
			echo	  "<div style='margin-top:20px'>".
						"<div style='margin:10px 0 10px 0;color:#142a3b'>Sorry, there are no direct edits in the past {$this->mn_lookback} days.</div>".
					  "</div>".
					  "&nbsp;";
	}
}

?>

