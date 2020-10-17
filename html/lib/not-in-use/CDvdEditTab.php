<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

define('CWnd_EDIT_CURRENT'		,     4);
define('CWnd_EDIT_PENDING'		,     5);
define('CWnd_EDIT_APPROVED'		,     6);
define('CWnd_EDIT_DECLINED'		,     7);
define('CWnd_EDIT_WITHDRAWN'	,     8);
define('CWnd_EDIT_DIRECTS'		,     9);

define('MSG_NOT_LOGGED_IN'		,     1);

require $gs_root.'/lib/rights-dvd.inc.php';
require $gs_root.'/lib/CWnd.php';
require $gs_root.'/lib/CPicUtils.php';

class CDvdEditTab extends CWnd
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-dvd-edit_{$this->mn_lib_version}.js'></script>\n";
		$this->ms_include_css	   .=
		"<style type='text/css'>".
			"td.left "					."{ text-align:left; }".
			"tr.center "				."{ text-align:center; }".
		"</style>";

		$this->ms_title				= 'Edit DVD Info';
		$this->mb_corners			= false;
		$this->mn_header_type		= CWnd_HEADER_NONE;
		$this->mb_get_user_status	= true;

		$this->mn_edit				= 0;
		$this->mn_new				= 0;
		$this->mn_pic				= 0;
		$this->mn_used_edit			= 0;
		$this->mn_used_new			= 0;
		$this->mn_used_pic			= 0;
		$this->mn_lookback			= 20;

		$this->ms_edit_id			= dvdaf3_getvalue('edit', DVDAF3_GET);
		$this->ms_dvd_id			= dvdaf3_getvalue('dvd' , DVDAF3_GET);
		$this->ms_page				= dvdaf3_getvalue('pg'  , DVDAF3_GET|DVDAF3_LOWER);
		$this->mn_edit_mode			= CWnd_EDIT_CURRENT;
		$this->ms_url				= substr(($this->ms_edit_id ? "&edit={$this->ms_edit_id}" : '').($this->ms_dvd_id  ? "&dvd={$this->ms_dvd_id}"   : ''), 1);
		$this->ms_url				= str_replace('-tab.html', '.html', dvdaf3_getvalue('SCRIPT_NAME', DVDAF3_SERVER)) . ($this->ms_url ? '?' . $this->ms_url : '');

		switch ( $this->ms_page )
		{
		case 'current':		$this->mn_edit_mode = CWnd_EDIT_CURRENT;   break;
		case 'pending':		$this->mn_edit_mode = CWnd_EDIT_PENDING;   break;
		case 'approved':	$this->mn_edit_mode = CWnd_EDIT_APPROVED;  break;
		case 'declined':	$this->mn_edit_mode = CWnd_EDIT_DECLINED;  break;
		case 'withdrawn':	$this->mn_edit_mode = CWnd_EDIT_WITHDRAWN; break;
		case 'directs':		$this->mn_edit_mode = CWnd_EDIT_DIRECTS;   break;
		}
	}

	function getOnLoadJavaScript()
	{
		return "parent.Tab.iframeResize(window.frameElement.id);";
	}

	function drawBodyTop() {}
	function drawBodyBottom() {}

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		if ( ! $this->mb_logged_in )
			return $this->tellUser(__LINE__, MSG_NOT_LOGGED_IN, false);
	}

	function tellUser($n_line, $n_what, $s_parm)
	{
		switch ( $n_what )
		{
		case MSG_NOT_LOGGED_IN:		$this->ms_display_error    = "Sorry, we can not honor your request because you are not logged in."; break;
		}
		if ( $this->ms_display_error ) $this->ms_display_error .= " (code {$n_line})";

		return false;
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		if ( true || $this->mb_mod )
		{
			switch ( $this->mn_edit_mode )
			{
			case CWnd_EDIT_CURRENT:   $this->drawCurrentList(); break;
			case CWnd_EDIT_PENDING:   $this->drawSubmissions('Pending', "disposition_cd = '-'", "pending submissions"); break;
			case CWnd_EDIT_APPROVED:  $this->drawSubmissions('Recently Approved', "updated_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd in ('A','P')", "approved submissions in the past {$this->mn_lookback} days"); break;
			case CWnd_EDIT_DECLINED:  $this->drawSubmissions('Recently Declined', "updated_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd = 'R'", "declined submissions in the past {$this->mn_lookback} days"); break;
			case CWnd_EDIT_WITHDRAWN: $this->drawSubmissions('Recently Withdrawn', "updated_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd = 'W'", "withdrawn submissions in the past {$this->mn_lookback} days"); break;
			case CWnd_EDIT_DIRECTS:   $this->drawDirects(); break;
			}
		}
		else
		{
			switch ( $this->mn_edit_mode )
			{
			case CWnd_EDIT_CURRENT:   $this->drawCurrentList(); break;
			case CWnd_EDIT_PENDING:   $this->drawSubmissions('Pending', "disposition_cd = '-'", "pending submissions"); break;
			case CWnd_EDIT_APPROVED:  $this->drawSubmissions('Recently Approved', "disposition_cd in ('A','P')", "approved submissions"); break;
			case CWnd_EDIT_DECLINED:  $this->drawSubmissions('Recently Declined', "disposition_cd = 'R'", "declined submissions"); break;
			case CWnd_EDIT_WITHDRAWN: $this->drawSubmissions('Recently Withdrawn', "disposition_cd = 'W'", "withdrawn submissions"); break;
			case CWnd_EDIT_DIRECTS:   $this->drawDirects(); break;
			}
		}
	}

	function drawCurrentList()
	{
		$fld = '';
		$str = '';
		$a   = array();
		$b   = array();

		if ( $this->ms_edit_id )
			$a = explode(',', $this->ms_edit_id);
		else
			if ( $this->ms_dvd_id && $this->ms_dvd_id != 'new' )
		$a = explode(',', $this->ms_dvd_id);

		for ( $i = 0 ; $i < count($a) ; $i++ )
		{
			$a[$i]     = intval($a[$i]);
			$b[$a[$i]] = null;
			$fld      .= $a[$i]. ',';
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
						$str .= "<tr class='center'>".
								  "<td>{$ln['updated_tm']}</td>".
								  "<td><a href='{$this->ms_url}&pg={$pg}' target='targetedit'>{$id}</a></td>".
								  "<td>{$ln['dvd_id']}</td>".
								  "<td class='left'>{$ln['dvd_title']}</td>".
								  "<td>". dvdaf3_decode($ln['request_cd']    , DVDAF3_DICT_REQUEST_DVD). "</td>".
								  "<td>". dvdaf3_decode($ln['disposition_cd'], DVDAF3_DICT_DISPOSITION ). "</td>".
								  "<td class='left'>{$ln['reviewer_notes']}</td>".
								  "<td>{$ln['reviewer_id']}</td>".
								  "<td>". ($ln['reviewed_tm'] ? $ln['reviewed_tm'] : '&nbsp;'). "</td>".
								"</tr>";
					}
				}
				if ( $str )
					$str =  "<table class='border'>".
							  "<thead>".
								"<tr class='center'>".
								  "<td width='1%'>Last updated<br /><img src='http://dv1.us/d1/1.gif' width='85' height='1' /></td>".
								  "<td width='1%'>Audit&nbsp;id</td>".
								  "<td width='1%'>DVD&nbsp;id</td>".
								  "<td width='40%' class='left'>DVD title</td>".
								  "<td width='1%'>Submission type</td>".
								  "<td width='1%'>Status</td>".
								  "<td width='40%' class='left'>Reviewer notes</td>".
								  "<td width='1%'>Reviewed&nbsp;by</td>".
								  "<td width='1%'>Review time<br /><img src='http://dv1.us/d1/1.gif' width='85' height='1' /></td>".
								"</tr>".
							  "</thead>".
							  "<tbody>".
								$str.
							  "</tbody>".
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
						$str .= "<tr class='center'>".
								  "<td>{$ln['dvd_updated_tm']}</td>".
								  "<td><a href='{$this->ms_url}&pg={$pg}' target='targetedit'>{$id}</a></td>".
								  "<td>{$ln['version_id']}</td>".
								  "<td class='left'>{$ln['dvd_title']}</td>".
								"</tr>";
					}
				}
				if ( $str )
					$str =  "<table class='border'>".
							  "<thead>".
								"<tr class='center'>".
								  "<td width='1%'>Last updated<br /><img src='http://dv1.us/d1/1.gif' width='85' height='1' /></td>".
								  "<td width='1%'>DVD&nbsp;id</td>".
								  "<td width='1%'>Version</td>".
								  "<td width='50%' class='left'>DVD title</td>".
								"</tr>".
							  "</thead>".
							  "<tbody>".
								$str.
							  "</tbody>".
							"</table>";
			}
		}

		if ( $str )
			echo  "<h2>List of DVDs you requested to edit:</h2>".
				  $str.
				  "&nbsp";
		else
			echo  "<div class='highkey' style='margin-top:20px'>Sorry, you are not editing anything at the moment. Please check the other tabs.</div>".
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
					  "if(d.reviewed_tm > d.updated_tm, d.reviewed_tm, d.updated_tm) sort_1,  ".
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
					  "if(s.reviewed_tm > s.updated_tm, s.reviewed_tm, s.updated_tm) sort_2 ".
				 "FROM pic_submit s ".
				 "LEFT JOIN pic p ON s.pic_id = p.pic_id ".
				 "LEFT JOIN decodes e ON domain_type = 'request_cd (pic)' and s.request_cd = e.code_char ".
				"WHERE proposer_id = '{$this->ms_user_id}' and {$s_criteria} and s.obj_type = 'D' ".

				"ORDER BY sort_1 DESC, sort_2";

		if ( ($rr = CSql::query($rr, 0,__FILE__,__LINE__)) )
		{
			while ( ($ln = CSql::fetch($rr)) )
			{
				$s_rand  = str_replace(' ','', str_replace(':','', str_replace('-','', $ln['pic_refresh_tm'])));
				if ( $ln['pic_edit_id'] )
				{
					if ( $ln['disposition_cd'] == 'A' && $ln['pic_name'] != '-' )
					{
						$s_src = "<img src='".CPic::location($ln['pic_name'],CPic_THUMB)."?tm={$s_rand}'>";
					}
					else
					{
						$s_src = $ln['uploaded_pic'] == '' || $ln['uploaded_pic'] == '-' ? sprintf('%06d',$ln['pic_edit_id']) : $ln['uploaded_pic'];
						$s_src = "<img src='{$this->ms_base_subdomain}/uploads/{$s_src}-prev.gif?tm={$s_rand}'>";
					}
					$s_sub  = "{$this->ms_base_subdomain}/utils/x-pic-edit.html?pic={$ln['pic_id']}&pic_edit={$ln['pic_edit_id']}&obj_type=dvd&obj={$ln['dvd_id']}".
							  ($ln['dvd_edit_id'] ? "&obj_edit={$ln['dvd_edit_id']}" : '');
					$s_sub  = "Pic&nbsp;Sub<br /><a href='javascript:void(Win.openStd(\"{$s_sub}\",\"target_pic\"))'>{$ln['pic_edit_id']}</a>";
					$s_obj  = $ln['pic_id'] ? "Pic<br />{$ln['pic_id']}" : "New<br />picture";
					if ( $ln['dvd_edit_id'] )
						if ( $ln['dvd_id'] )
							$s_title = "Picture for DVD submission {$ln['dvd_edit_id']} for DVD {$ln['dvd_id']} (".dvdaf3_decode($ln['dvd_disposition_cd'],DVDAF3_DICT_DISPOSITION).")";
						else
							$s_title = "Picture for DVD submission {$ln['dvd_edit_id']} for a new DVD (".dvdaf3_decode($ln['dvd_disposition_cd'],DVDAF3_DICT_DISPOSITION).")";
					else
						if ( $ln['dvd_id'] )
							$s_title = "Picture for DVD {$ln['dvd_id']}";
						else
							$s_title = "Picture for a new DVD}";
					$s_title = "<table class='no_border'><tr><td>{$s_src}</td><td>{$s_title}</td></tr></table>";
					$s_req   = dvdaf3_decode($ln['request_cd'], DVDAF3_DICT_REQUEST_PIC);
				}
				else
				{
					if ( $ln['pic_name'] && $ln['pic_name'] != '-' )
					{
						$s_src = "<img src='".CPic::location($ln['pic_name'],CPic_THUMB)."?tm={$s_rand}'>";
					}
					else
					{
						$s_src = "<img src='http://dv1.us/d1/00/pic-empty.gif' width='63' height='90' />";
					}
					$s_sub   = "DVD&nbsp;Sub<br /><a href='/utils/x-dvd-edit.html?edit={$ln['dvd_edit_id']}' target='targetedit'>{$ln['dvd_edit_id']}</a>";
					$s_obj   = $ln['dvd_id'] ? "DVD<br />{$ln['dvd_id']}" : "New<br />DVD";
					$s_title = "<table class='no_border'><tr><td>{$s_src}</td><td>{$ln['title']}</td></tr></table>";
					$s_req   = dvdaf3_decode($ln['request_cd'], DVDAF3_DICT_REQUEST_DVD);
				}
				$str .= "<tr class='center'>".
						  "<td>{$ln['updated_tm']}</td>".
						  "<td>$s_sub</td>".
						  "<td>$s_obj</td>".
						  "<td class='left'>{$s_title}</td>".
						  "<td>$s_req</td>".
						  "<td>". dvdaf3_decode($ln['disposition_cd'], DVDAF3_DICT_DISPOSITION ). "</td>".
						  "<td class='left'>{$ln['reviewer_notes']}</td>".
						  "<td>{$ln['reviewer_id']}</td>".
						  "<td>". ($ln['reviewed_tm'] ? $ln['reviewed_tm'] : '&nbsp;'). "</td>".
						"</tr>";
			}
			CSql::free($rr);
		}

		if ( $str )
			echo  "<h2>{$s_what} Submissions:</h2>".
				  "<table class='border'>".
					"<thead>".
					  "<tr class='center'>".
						"<td width='1%'>Last updated<br /><img src='http://dv1.us/d1/1.gif' width='85' height='1' /></td>".
						"<td width='1%'>Audit&nbsp;id</td>".
						"<td width='1%'>Object type</td>".
						"<td width='40%' class='left'>Details</td>".
						"<td width='1%'>Submission type</td>".
						"<td width='1%'>Status</td>".
						"<td width='40%' class='left'>Reviewer notes</td>".
						"<td width='1%'>Reviewed&nbsp;by</td>".
						"<td width='1%'>Review time<br /><img src='http://dv1.us/d1/1.gif' width='85' height='1' /></td>".
					  "</tr>".
					"</thead>".
					"<tbody>".
					  $str.
					"</tbody>".
				  "</table>".
				  "&nbsp";
		else
			echo  "<div class='highkey' style='margin-top:20px'>Sorry, you have no {$s_what_not}.</div>".
				  "&nbsp";
	}

	function drawDirects()
	{
		$str = '';
		$rr  = "SELECT z.dvd_id, z.version_id, b.version_id curr_version_id, z.dvd_updated_tm, b.dvd_title ".
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
				$str .= "<tr class='center'>".
						  "<td>{$ln['dvd_updated_tm']}</td>".
						  "<td><a href='{$this->ms_base_subdomain}/search.html?has={$id}&init_form=str0_has_{$id}' target='filmaf'>{$id}</a></td>".
						  "<td>". ($ln['version_id'] == $ln['curr_version_id'] ? 'current' : $ln['version_id']) ."</td>".
						  "<td>{$ln['curr_version_id']}</td>".
						  "<td class='left'>{$ln['dvd_title']}</td>".
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
				$n_new  = $this->mn_new  - $this->mn_used_new ; if ( $n_new  < 0 ) $n_new  = 0;
				$n_pic  = $this->mn_pic  - $this->mn_used_pic ; if ( $n_pic  < 0 ) $n_pic  = 0;
				if ( $this->mn_edit ) $s_dir .= ($s_dir ? '<br />' : ''). "Daily direct edits: {$this->mn_edit} <span class='hl'>" .($this->mn_edit == $n_edit ? '(0 used)' : "({$n_edit} remaining)."). "</span>";
				if ( $this->mn_new  ) $s_dir .= ($s_dir ? '<br />' : ''). "Daily direct creates: {$this->mn_new} <span class='hl'>".($this->mn_new  == $n_new  ? '(0 used)' : "({$n_new} remaining)." ). "</span>";
				if ( $this->mn_pic  ) $s_dir .= ($s_dir ? '<br />' : ''). "Daily direct pics: {$this->mn_pic} <span class='hl'>"   .($this->mn_pic  == $n_pic  ? '(0 used)' : "({$n_pic} remaining)." ). "</span>";
			}
			$s_dir  = "<div style='margin:10px 0 20px 0'>{$s_dir}</div>";
		}

		echo	  "<div style='margin-top:20px'>".
					$s_dir;

		if ( $str )
			echo  "<h2>Recently Directly Edited Titles:</h2>".
				  "<table class='border'>".
					"<thead>".
					  "<tr class='center'>".
						"<td width='5%'>Last updated<br /><img src='http://dv1.us/d1/1.gif' width='85' height='1' /></td>".
						"<td width='1%'>DVD&nbsp;id</td>".
						"<td width='1%'>Updated Version</td>".
						"<td width='1%'>Current Version</td>".
						"<td width='50%' class='left'>DVD title</td>".
					  "</tr>".
					"</thead>".
					"<tbody>".
					  $str.
					"</tbody>".
				  "</table>";
		
		echo	  "<div style='margin-top:20px'>".
					"Direct editing the FilmAf database is afforded to selected FilmAf members.<br />".
					"For more information please check our <a href='/utils/help-filmaf.html' target='_blank'>Star Member Priviledges</a>.".
				  "</div>".
				  "&nbsp";
	}
}

?>
