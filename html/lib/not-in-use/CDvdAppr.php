<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

define('CWnd_EDIT_INDEX'	,     0);
define('CWnd_EDIT_EDIT'		,     1);

define('MSG_ALREADY_SAVED'	,     1);
define('MSG_SAVED'			,     2);
define('MSG_NOT_SAVED'		,     3);
define('MSG_AUTHENTICATE'	,     4);
define('MSG_NOT_MODERATOR'	,     5);
define('MSG_UNKNOWN'		,     6);
define('MSG_REJECT'			,     7);
define('MSG_REJECT_ERROR'	,     8);
define('MSG_NOTHING'		,     9);

require $gs_root.'/lib/CValidate.php';
require $gs_root.'/lib/CNavi.php';
require $gs_root.'/lib/CPicUtils.php';
require $gs_root.'/lib/dvd-update.inc.php';
require $gs_root.'/lib/edit-pic.inc.php';
require $gs_root.'/lib/CWnd.php';

class CDvdAppr extends CWnd
{
	/**
	 * This is method constructor
	 *
	 * @return mixed This is the return value description
	 *
	 */
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	//	$this->mb_show_trace		= true;
	//	$this->mb_trace_environment	= true;
	//	$this->mb_trace_sql			= true;
	//	$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-dvd-appr_{$this->mn_lib_version}.js'></script>\n";
		$this->ms_include_css	   .=
		"<style type='text/css'>".
			"tr.nowrap td "				."{ white-space:nowrap; }".
			"tr.top td, td.top "		."{ vertical-align:top; }".
			"tr.section td "			."{ text-align:center; font-size:12px; padding:2px 4px 2px 4px; font-weight:bold; color:#072b4b; background-color:#edf4fa; }".
		"</style>";

		$this->mn_show_mode			= DVDAF_SHOW_SUBMISS;
		$this->mn_template_mode		= DVDAF_SELECT_DVD;
		$this->ms_title				= 'Submission Processing';
		$this->mb_include_cal		= true;
		$this->mb_include_menu		= true;
		$this->mn_echo_zoom			= true;
		$this->mb_get_user_status	= true;
		$this->mb_menu_context		= true;

		$this->ms_edit_id			= dvdaf3_getvalue('edit', DVDAF3_GET);
		$this->mn_edit_id			= 0;
		$this->mn_dvd_id			= 0;
		$this->ms_page				= dvdaf3_getvalue('pg'  , DVDAF3_GET|DVDAF3_LOWER);
		$this->mn_page				= 1;
		$this->mn_seq_last			= 0;
		$this->mn_edit_mode			= CWnd_EDIT_INDEX;
		$this->ms_url				= $this->ms_edit_id ? "edit={$this->ms_edit_id}" : '';
		$this->ms_url				= dvdaf3_getvalue('SCRIPT_NAME', DVDAF3_SERVER) . ($this->ms_url ? '?' . $this->ms_url : '');
		$this->ms_reviewer_notes	= '-';

		$this->mn_prop_genre		= 0;
		$this->ms_prop_imdb			= '';
		$this->ms_prop_dvd_ids		= '';

		if ( isset($_POST['n_zareviewer_notes']) )
		{
			$this->ms_reviewer_notes = dvdaf3_textarea2db(dvdaf3_getvalue('n_zareviewer_notes', DVDAF3_POST), 1000);
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
	}

	function getOnLoadJavaScript()
	{
		return "this.focus();";
	}

	function getFooterJavaScript()
	{
		switch ( $this->mn_edit_mode )
		{
		case CWnd_EDIT_INDEX:
			return  "if($('my-tab')){window.name='targetappr';Tab.setup('my-tab',{background:'#ffffff', loadtab:0, width:'100%'})};";
		case CWnd_EDIT_EDIT:
			$s_config =	'{baseDomain:"'.$this->ms_base_subdomain.'"'.
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

			return  "function setSearchVal(mode, target, val){DvdEdit.setSearchVal(mode,target,val);};".
					"function onMenuClick(action){DvdApproveMenuAction.onClick(action);};".
					"Filmaf.config({$s_config});".
					"DvdApprove.setup();";
		}
	}

	function tellUser($n_line, $n_what, $s_parm)
	{
		switch ( $n_what )
		{
		case MSG_ALREADY_SAVED:	$this->ms_display_error    = "Submission already saved as dvd_id <a href='/search.html?has={$s_parm}&init_form=str0_has_{$s_parm}' target='filmaf'>".
															 "{$s_parm}</a>."; break;
		case MSG_SAVED:			$this->ms_display_affected = getAffectedMessage($s_parm, $this->ms_prop_dvd_ids, $this->mn_prop_genre, $this->ms_prop_imdb, $this->ms_base_subdomain); break;
		case MSG_NOT_SAVED:		$this->ms_display_error    = "Submission {$s_parm} could not be saved.  Perhaps it has already been processed."; break;
		case MSG_AUTHENTICATE:	$this->ms_display_error    = "This level of access requires that you be re-authenticated. Please click <a href='javascript:void(Win.reauth(0))'>here</a>. ".
															 "Once this session has been authenticated you can either redo your changes or in some browsers hit &lt;F5&gt; to reload this ".
															 "page and retry to save it. Thanks!."; break;
		case MSG_NOT_MODERATOR:	$this->ms_display_error    = "Sorry, this function is only available to moderators. If you are a moderator please follow <a href='javascript:void(".
															 "Win.reauth(0))'>this link</a> to be re-authenticated. Once you have done that you may be able to hit &lt;F5&gt; in some ".
															 "browsers to reload this page."; break;
		case MSG_UNKNOWN:		$this->ms_display_error    = "Sorry, we did not find the requested submission."; break;
		case MSG_REJECT:		$this->ms_display_error    = "Submission {$s_parm} rejected."; break;
		case MSG_REJECT_ERROR:	$this->ms_display_error    = "Unable to reject submission {$s_parm}. May be it has been approved or previously rejected."; break;
		case MSG_NOTHING:		$this->ms_display_error    = "Nothing to save."; break;
		}
		if ( $this->ms_display_error ) $this->ms_display_error .= " (code {$n_line})";

		return false;
	}

	function updateSubmission($n_edit_id, $n_dvd_id, $b_approve, $b_append_rejects)
	{
		$n_dvd_id		  = $n_dvd_id ? "dvd_id = {$n_dvd_id}, " : '';
		$s_reject		  = dvdaf3_getvalue('reject', DVDAF3_POST);
		$s_disposition_cd = $b_approve ? ($s_reject == 'none' ? 'A' : 'P') : 'R';

		if ( $b_append_rejects && $s_reject != 'none' )
		{
			$this->ms_reviewer_notes = ($this->ms_reviewer_notes == '-' ? '' : $this->ms_reviewer_notes. '<br />'). 'AUTO GEN MSG - Overwritten:'. str_replace(' a_', ' ', ' '.$s_reject);
			$this->ms_reviewer_notes = $this->ms_reviewer_notes == '' ? '-' : dvdaf3_substr($this->ms_reviewer_notes, 0, 1000);
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
		CValidate::validateInput($s_update, $s_values, DVDAF_INSERT, false, $this->ms_display_error);
		$s_seed    = dvdaf3_getvalue('seed', DVDAF3_POST);
		$n_edit_id = dvdaf3_getvalue('edit_id', DVDAF3_POST|DVDAF3_INT);
		$n_dvd_id  = 0;

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

		if ( $n_dvd_id  )
		{
			CSql::query_and_free("CALL update_dvd_search_index({$n_dvd_id},1)",0,__FILE__,__LINE__);
			$this->tellUser(__LINE__, MSG_SAVED, $n_dvd_id);
		}
		else
			$this->tellUser(__LINE__, MSG_NOT_SAVED, $n_edit_id);
	}

	function propagateGenre($n_dvd_id)
	{
		if ( dvdaf3_getvalue('cb_a_genre', DVDAF3_POST|DVDAF3_LOWER) == 'on' )
		{
			$this->mn_prop_genre   = dvdaf3_getvalue('n_a_genre', DVDAF3_POST|DVDAF3_INT);
			$this->ms_prop_imdb    = sprintf('%08d', dvdaf3_getvalue('n_a_imdb_id_0', DVDAF3_POST|DVDAF3_INT));
			$this->ms_prop_dvd_ids = propagateGenre($n_dvd_id, $this->ms_user_id, $this->mn_prop_genre, $this->ms_prop_imdb);
		}
	}

	function saveEdit()
	{
		CValidate::validateInput($s_update, $s_values, DVDAF_UPDATE, false, $this->ms_display_error);
		$n_edit_id = dvdaf3_getvalue('edit_id', DVDAF3_POST|DVDAF3_INT);
		$n_dvd_id  = dvdaf3_getvalue('dvd_id' , DVDAF3_POST|DVDAF3_INT);
		$s_update .= "version_id = version_id+1, dvd_updated_tm = now(), dvd_verified_tm = now(), dvd_verified_by = '{$this->ms_user_id}', verified_version = version_id, dvd_edit_id = {$n_edit_id}";

		unset($_POST['n_a_last_justify']);
		unset($_POST['n_a_dvd_updated_by']);
		CValidate::validateInput($s_change, $s_values, DVDAF_UPDATE, false, $this->ms_display_error);

		if ( $s_change )
		{
			if ( $n_edit_id )
			{
				$n_edit_id = CSql::query_and_fetch1("SELECT edit_id FROM dvd_submit WHERE edit_id = {$n_edit_id} and disposition_cd = '-'",0,__FILE__,__LINE__);
				if ( $n_edit_id )
				{
					snapHistory($n_dvd_id);

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
		$n_edit_id = $n_orig = dvdaf3_getvalue('edit_id', DVDAF3_POST|DVDAF3_INT);
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
		if ( $this->mb_logged_in_this_sess && $this->mb_mod )
		{
			switch ( dvdaf3_getvalue('act', DVDAF3_POST|DVDAF3_LOWER) )
			{
			case 'edit':    $this->saveEdit(); break;
			case 'new':     $this->saveNew();  break;
			case 'discard': $this->discard();  break;
			}
		}
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		if ( ! $this->mb_logged_in_this_sess )
		{
			$this->tellUser(__LINE__, MSG_AUTHENTICATE, false);
			$this->drawMessages(true,false);
			return;
		}

		if ( ! $this->mb_mod )
		{
			$this->tellUser(__LINE__, MSG_NOT_MODERATOR, false);
			$this->drawMessages(true,false);
			return;
		}

		echo "<div id='content'>";
		switch ( $this->mn_edit_mode )
		{
		case CWnd_EDIT_INDEX:	 $this->drawBodyIndex(); break;
		case CWnd_EDIT_EDIT:	 $this->drawBodyPageDvd(); break;
		}
		echo "</div>";
	}

	function drawBodyPageDvd() // <<-------------------------------<< 7.2.x
	{
		$n_page     = $this->mn_page;
		$n_seq_last = $this->mn_seq_last;
		$n_edit_id  = intval($this->mn_edit_id);
		$ln		    = CSql::query_and_fetch("SELECT dvd_id, request_cd FROM dvd_submit WHERE edit_id = {$n_edit_id}", 0,__FILE__,__LINE__);
		$n_dvd_id   = $ln['dvd_id'];
		$s_request  = $ln['request_cd'];
		$ln         = false;
		$s_pics     = '';

		if ( $n_dvd_id == '' || ! $n_edit_id )
			return $this->tellUser(__LINE__, MSG_UNKNOWN, false);

		$this->mn_dvd_id		= intval($n_dvd_id);
		$s_def_pic				= 0;
		$n_req_def_edit_id		= 0;
		$n_req_def_pic_id		= 0;
		$n_req_def_pic_edit_id	= 0;
		$s_def_dispo_txt		= '';
		$s_def_dispo_cd			= '';

		if ( $this->mn_dvd_id )
		{
			$s_def_pic = CSql::query_and_fetch1("SELECT b.pic_id ".
												  "FROM dvd a ".
												  "LEFT JOIN dvd_pic b ON a.dvd_id = b.dvd_id ".
												  "LEFT JOIN pic c ON b.pic_id = c.pic_id and a.pic_name = c.pic_name ".
												 "WHERE a.dvd_id = {$this->mn_dvd_id}", 0,__FILE__,__LINE__);
		}

		if ( ($rr = CSql::query_and_fetch  ("SELECT s.def_edit_id, s.pic_id, s.pic_edit_id, s.disposition_cd, d.descr disposition_txt ".
											  "FROM pic_def_submit s ".
											  "LEFT JOIN decodes d ON d.domain_type = 'disposition_cd' and s.disposition_cd = d.code_char ".
											 "WHERE s.obj_type = 'D' and s.obj_id = {$this->mn_dvd_id} and s.obj_edit_id = {$n_edit_id}", 0,__FILE__,__LINE__)) )
		{
			$n_req_def_edit_id		= $rr['def_edit_id'];
			$n_req_def_pic_id		= $rr['pic_id'];
			$n_req_def_pic_edit_id	= $rr['pic_edit_id'];
			$s_def_dispo_txt		= $rr['disposition_txt'];
			$s_def_dispo_cd			= $rr['disposition_cd'];
		}

		$n_pic = 0;
		$a_str = array();
		$k     = -1;
		if ( ($rr = CSql::query(getPicEditListSql($this->mn_dvd_id, $n_edit_id, ''), 0,__FILE__,__LINE__)) )
		{
			$pc = CSql::fetch($rr);
			while ( $pc )
			{
				$k++;
				$j				  = 0;
				$a_str[$k]		  = array();
				$a_str[$k][$j]	  = array();
				$a_str[$k][$j][0] = drawPicSubCurr ($pc, $s_def_pic, true);
				$a_str[$k][$j][1] = drawPicSubProp ($pc, $pc['disposition_cd'], $pc['request_cd'], $this->ms_base_subdomain, $s_prop_id, true, $this->ms_user_id == $pc['proposer_id']);
				$a_str[$k][$j][2] = drawPicSubNotes($pc, $n_req_def_edit_id, $n_req_def_pic_id, $n_req_def_pic_edit_id, $pc['disposition_txt'], $s_def_dispo_txt, true, $s_prop_id);
				$n_pic			  = $pc['pic_id'];
				$pc				  = CSql::fetch($rr);
				while ( $pc && $n_pic && $n_pic == $pc['pic_id'] )
				{
					$j++;
					$a_str[$k][$j]    = array();
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

			$s_pics = "<td>Current</td>".
					  "<td>Proposed</td>".
					  "<td width='50%'>Notes</td>";
			$s_pics = "<table class='border'>".
						"<thead>".
						  "<tr>{$s_pics}<td>&nbsp;</td>{$s_pics}</tr>".
						"</thead>".
						"<tbody>";

			for ( $i = 0 ; $i < count($a_cell) ; $i++ )
				$s_pics .= "<tr class='top'>". $a_cell[$i][0]. $a_cell[$i][1]. $a_cell[$i][2]. "<td>&nbsp;</td>". $a_cell[$i][3]. $a_cell[$i][4]. $a_cell[$i][5]. "</tr>";
			unset($a_cell);

			$s_pics .=  "</tbody>".
					  "</table>";
		}
		else
		{
			$s_pics  = '&nbsp;';
		}

		$s_tmpl   = dvdaf_parsetemplate("", $s_select, $s_from, $s_where, $s_sort, $this->mn_show_mode, $this->mn_template_mode, '', '', $this->mn_dvd_id);

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
				"a.reviewer_notes, a.reviewed_tm, a.hist_version_id, a.update_justify, a.creation_seed ".
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
				"a.reviewer_notes, a.reviewed_tm, a.hist_version_id, a.update_justify, a.creation_seed ".
			   "FROM dvd_submit a ".
			  "WHERE edit_id = {$n_edit_id}", 0,__FILE__,__LINE__);
		}

		if ( $ln['x_source']		== '' ) $ln['x_source']		 = 'A';
		if ( $ln['x_media_type']	== '' ) $ln['x_media_type']	 = 'D';
		if ( $ln['x_rel_status']	== '' ) $ln['x_rel_status']	 = '-';
		if ( $ln['x_amz_country']	== '' ) $ln['x_amz_country'] = '-';
		if ( $ln['source']			== '' ) $ln['source']		 = 'A';
		if ( $ln['media_type']		== '' ) $ln['media_type']	 = 'D';
		if ( $ln['rel_status']		== '' ) $ln['rel_status']	 = '-';
		if ( $ln['amz_country']		== '' ) $ln['amz_country']	 = '-';

		$s_request_type		= dvdaf3_decode($ln['request_cd'    ], DVDAF3_DICT_REQUEST_DVD);
		$s_disposition		= dvdaf3_decode($ln['disposition_cd'], DVDAF3_DICT_DISPOSITION );
		$s_proposer_notes	= dvdaf3_db2textarea($ln['proposer_notes']);
		$s_reviewer_notes	= dvdaf3_db2textarea($ln['reviewer_notes']);
		$s_proposed			= $ln['proposer_id'];
		$s_version			= $ln['hist_version_id'];	if ( $s_version         >= 0   ) $s_version       =					 " - <span class='highkey'>version {$s_version}</span>";
		$s_proposed_tm		= $ln['updated_tm'];										 $s_proposed_tm   = $s_proposed_tm ?	"<span class='one_lbl'>on</span> {$s_proposed_tm}{$s_version}" : '&nbsp;';
		$s_reviewed			= $ln['reviewer_id'];
		$s_reviewed_tm		= $ln['reviewed_tm'];										 $s_reviewed_tm   = $s_reviewed_tm ?	"<span class='one_lbl'>on</span> {$s_reviewed_tm}" : '&nbsp;';
		$s_current			= $ln['dvd_updated_by'];
		$s_version			= $ln['version_id'];		if ( $s_version         >= 0   ) $s_version       =					 " - <span class='highkey'>version {$s_version}</span>";
		$s_current_tm		= $ln['dvd_updated_tm'];									 $s_current_tm    = $s_current_tm ?	"<span class='one_lbl'>on</span> {$s_current_tm}{$s_version}" : '&nbsp;';
		$s_verified			= $ln['dvd_verified_by'];
		$s_version			= $ln['verified_version'];	if ( $s_version         >= 0   ) $s_version       =					 " - <span class='highkey'>version {$s_version}</span>";
		$s_verified_tm		= $ln['dvd_verified_tm'];									 $s_verified_tm   = $s_verified_tm ?	"<span class='one_lbl'>on</span> {$s_verified_tm}{$s_version}" : '&nbsp;';
		$s_created_tm		= $ln['dvd_created_tm'];									 $s_created_tm    = $s_created_tm ?	"<span class='one_lbl'>on</span> {$s_created_tm}" : '-';
		$s_seed				= $ln['creation_seed'];		if ( $s_seed			== '-' ) $s_seed          = '';
		$s_update_justify	= $ln['update_justify'];	if ( $s_update_justify	== '-' ) $s_update_justify  = '';

		$s_err		= $this->getMessageString(true,false);
		$s_nav		= CNavi::page($this->ms_url, $n_page, $n_seq_last, false, '', '', true, false, false);
		$n_pos		= strpos($s_nav, '&nbsp;&nbsp;&nbsp;');
		$s_nav		= "<div style='margin:6px 6px 6px 6px'>" . substr($s_nav, $n_pos + 18). "</div>".
					  "<div style='margin:6px 6px 12px 6px'>". substr($s_nav, 0, $n_pos  ). "</div>";
		$s_action	= $ln['request_cd']     == 'N' ? 'new' : 'edit';
		$s_nosave	= $ln['disposition_cd'] == '-' ? '' : " disabled='disabled'";

		echo  
				$s_err.
				"<form id='myform' name='myform' method='post' action='{$this->ms_url}'>".
				  "<table class='border'>".
					"<tr>".
					  "<td colspan='5' valign='top'>".
						"<table class='no_border'>".
						  "<tr class='nowrap'>".
							"<td width='80px' style='white-space:normal'>".
							  "<div class='highkey' style='text-align:center'><span class='one_lbl'>Audit id:</span> {$n_edit_id}</div>".
							  "<div class='highkey' style='text-align:center'>{$s_request_type}</div>".
							  "<input id='act' name='act' type='hidden' value='{$s_action}' />".
							  "<input id='reject' name='reject' type='hidden' value='all' />".
							  "<input id='dvd_id' name='dvd_id' type='hidden' value='{$this->mn_dvd_id}' />".
							  "<input id='edit_id' name='edit_id' type='hidden' value='{$n_edit_id}' />".
							  "<input id='seed' name='seed' type='hidden' value='{$s_seed}' />".
							  "<input id='n_a_update_justify' name='n_a_update_justify' type='hidden' value='{$s_update_justify}' />".
							  "<input id='n_a_dvd_updated_by' name='n_a_dvd_updated_by' type='hidden' value='{$s_proposed}' />".
							  "<img src='http://dv1.us/d1/1.gif' width='80' height='1' />".
							"</td>".
							"<td width='1%'>".
							  "<div class='one_lbl'>Proposer notes:</div>".
							  "<textarea cols='60' rows='5' readonly='readonly' maxlength='1000' wrap='soft'>{$s_proposer_notes}</textarea>".
							  "<div class='one_lbl'>".
								"Reviewer notes: ".
								"<input type='button' style='width:140px;font-size:9px' value='Standard comments' id='mod_txt'{$s_nosave} /> ".
								"<input type='button' style='width:60px;font-size:9px' value='Clear' onclick='document.getElementById(\"n_zareviewer_notes\").value=\"\"'{$s_nosave} />".
							  "</div>".
							  "<textarea id='n_zareviewer_notes' name='n_zareviewer_notes' wrap='soft' cols='60' rows='5' maxlength='1000'>{$s_reviewer_notes}</textarea>".
							"</td>".
							"<td width='1%'>".
							  "<table class='border'>".
								"<tr class='nowrap'>".
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
							  "<table>".
								"<tr><td class='one_lbl'>Disposition:</td><td class='highkey'>{$s_disposition}</td><td>&nbsp;</td></tr>".
								"<tr><td class='one_lbl'>Proposed by:</td><td class='highkey'>{$s_proposed}</td><td>{$s_proposed_tm}</td></tr>".
								"<tr><td class='one_lbl'>Reviewed by:</td><td class='highkey'>{$s_reviewed}</td><td>{$s_reviewed_tm}</td></tr>".
								"<tr><td colspan='3'>&nbsp;</td></tr>".
								"<tr><td colspan='3'>&nbsp;</td></tr>".
								"<tr><td class='one_lbl'>Current version by:	</td><td class='highkey'>{$s_current}</td><td>{$s_current_tm}</td></tr>".
								"<tr><td class='one_lbl'>Last verified by:</td><td class='highkey'>{$s_verified}</td><td>{$s_verified_tm}</td></tr>".
								"<tr><td class='one_lbl' colspan='2'>Original listing created</td><td>{$s_created_tm}</td></tr>".
							  "</table>".
							"</td>".
							"<td width='95%'>".
							  "&nbsp;".
							"</td>".
						  "</tr>".
						"</table>".
					  "</td>".
					"</tr>";
		echo	    "<tr>".
					  "<td colspan='5' style='border:none'>{$s_pics}</td>".
					"</tr>";
		echo	    "<tr class='section'>".
					  "<td colspan='2'>Proposed</td>".
					  "<td><input type='button' onclick='DvdApprove.copyFields(false)' value='&hellip;&laquo; Rej' style='width:54px' /></td>".
					  "<td><input type='button' onclick='DvdApprove.copyFields(true)' value='App &raquo;&hellip;' style='width:54px' /></td>".
					  "<td>".
						"<table class='no_border'>".
						  "<tr>".
							"<td width='1%'><input type='button' value='Diff Prop' style='width:70px' onclick='DvdApprove.diffFields(true)' /></td>".
							"<td width='1%'><input type='button' value='Diff Curr' style='width:70px' onclick='DvdApprove.diffFields(false)' /></td>".
							"<td width='98%'>Approved</td>".
						  "</tr>".
						"</table>".
					  "</td>".
					"</tr>";
					dvdaf_getbrowserow($ln, $s_tmpl, 0, 0, '', DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_KEY_OK | DVDAF4_CB_PROPAGATE);
		echo     "</table>".
				"</form>";
	}

	function drawBodyIndex()
	{
		$s_raw = str_replace('.html', '-tab.html', dvdaf3_getvalue('SCRIPT_NAME', DVDAF3_SERVER)). '?pg=';
		$s_url = str_replace('.html', '-tab.html', $this->ms_url							  ). (strpos($this->ms_url, '?') ? '&' : '?'). 'pg=';
		$s_err = $this->getMessageString(true,false);

		echo  "<div style='margin:24px 10px 10px 10px'>".
				$s_err.
				"<div id='my-tab'>".
				  "<li><a href='{$s_raw}pending'>Pending</a></li>".
				  "<li><a href='{$s_raw}rejected'>Rejected</a></li>".
				  "<li><a href='{$s_raw}approved'>Approved</a></li>".
				  "<li><a href='{$s_raw}directs'>Direct Edits</a></li>".
				"</div>".
			  "</div>";
	}
}

?>
