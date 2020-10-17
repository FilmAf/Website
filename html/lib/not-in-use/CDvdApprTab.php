<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

define('CWnd_EDIT_PENDING'	,     2);
define('CWnd_EDIT_DECLINED'	,     3);
define('CWnd_EDIT_APPROVED'	,     4);
define('CWnd_EDIT_DIRECTS'	,     5);

define('MSG_AUTHENTICATE'	,     4);
define('MSG_NOT_MODERATOR'	,     5);

require $gs_root.'/lib/CPicUtils.php';
require $gs_root.'/lib/edit-pic.inc.php';
require $gs_root.'/lib/CWnd.php';

class CDvdApprTab extends CWnd
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-dvd-appr_{$this->mn_lib_version}.js'></script>\n";
		$this->ms_include_css	   .=
		"<style type='text/css'>".
			"td.left "					."{ text-align:left; }".
			"tr.center "				."{ text-align:center; }".
		"</style>";

		$this->ms_title				= 'Submission Processing';
		$this->mb_corners			= false;
		$this->mn_header_type		= CWnd_HEADER_NONE;
		$this->mb_get_user_status	= true;

		$this->mn_lookback			= 10;

		$this->ms_edit_id			= dvdaf3_getvalue('edit', DVDAF3_GET);
		$this->ms_page				= dvdaf3_getvalue('pg'  , DVDAF3_GET|DVDAF3_LOWER);
		$this->mn_edit_mode			= CWnd_EDIT_PENDING;
		$this->ms_url				= $this->ms_edit_id ? "edit={$this->ms_edit_id}" : '';
		$this->ms_url				= str_replace('-tab.html', '.html', dvdaf3_getvalue('SCRIPT_NAME', DVDAF3_SERVER)) . ($this->ms_url ? '?' . $this->ms_url : '');

		switch ( $this->ms_page )
		{
		case 'pending':		$this->mn_edit_mode = CWnd_EDIT_PENDING;  break;
		case 'rejected':	$this->mn_edit_mode = CWnd_EDIT_DECLINED; break;
		case 'approved':	$this->mn_edit_mode = CWnd_EDIT_APPROVED; break;
		case 'directs':		$this->mn_edit_mode = CWnd_EDIT_DIRECTS;  break;
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
		if ( ! $this->mb_logged_in_this_sess )
			return $this->tellUser(__LINE__, MSG_AUTHENTICATE, false);
		if ( ! $this->mb_mod )
			return $this->tellUser(__LINE__, MSG_NOT_MODERATOR,false);
	}

	function tellUser($n_line, $n_what, $s_parm)
	{
		switch ( $n_what )
		{
		case MSG_AUTHENTICATE:	$this->ms_display_error    = "This level of access requires that you be re-authenticated. Please click <a href='javascript:void(Win.reauth(0))'>here</a>. ".
															 "Once this session has been authenticated you can either redo your changes or in some browsers hit &lt;F5&gt; to reload this ".
															 "page and retry to save it. Thanks!."; break;
		case MSG_NOT_MODERATOR:	$this->ms_display_error    = "Sorry, this function is only available to moderators. If you are a moderator please follow <a href='javascript:void(".
															 "Win.reauth(0))'>this link</a> to be re-authenticated. Once you have done that you may be able to hit &lt;F5&gt; in some ".
															 "browsers to reload this page."; break;
		}
		if ( $this->ms_display_error ) $this->ms_display_error .= " (code {$n_line})";

		return false;
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		if ( $this->mb_mod && $this->mb_logged_in_this_sess )
		{
			switch ( $this->mn_edit_mode )
			{
			case CWnd_EDIT_PENDING:	 $this->drawSubmissions('Pending', "disposition_cd = '-'", "updated_tm", '', "pending submissions", false, true); break;
			case CWnd_EDIT_DECLINED: $this->drawSubmissions('Rejected', "reviewed_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd = 'R'", "reviewed_tm", " DESC", "declined submissions in the past {$this->mn_lookback} days", true, false); break;
			case CWnd_EDIT_APPROVED: $this->drawSubmissions('Approved (includes partial)', "reviewed_tm > date_add(now(), INTERVAL -{$this->mn_lookback} DAY) and disposition_cd in ('A','P')", "reviewed_tm", " DESC", "approved submissions in the past {$this->mn_lookback} days", false, false); break;
			case CWnd_EDIT_DIRECTS:	 $this->drawDirects(); break;
			}
		}
	}

	function drawSubmissions($s_what, $s_criteria, $s_sort, $s_desc, $s_what_not, $b_resurrect, $b_star_first)
	{
		$str = '';
		$i   = 0;
		$j   = 0;
		$max = 200;
		$va  = '';
		$srt = $b_star_first ? "b.membership_cd DESC, " : '';
		$ss  =  "SELECT a.*, b.membership_cd ".
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
						"if(s.pic_id, CONCAT_WS(', ', if(p.pic_type = s.pic_type, NULL, 'picture type'), ".
										 "if(p.transforms = s.transforms, NULL, 'transforms'), ".
										 "if(p.caption = s.caption, NULL, 'caption'), ".
										 "if(p.copy_holder = s.copy_holder, NULL, 'copyright holder'), ".
										 "if(p.copy_year = s.copy_year, NULL, 'copyright year'), ".
										 "if(p.suitability_cd = s.suitability_cd, NULL, 'suitability')), ".
								 "'') diff, s.updated_tm pic_refresh_tm, ".
						"s.updated_tm, s.reviewer_id, s.reviewed_tm, s.disposition_cd, s.reviewer_notes, s.proposer_id, ".
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
						$s_id     = $ln['pic_id'];
						$s_title  = "<table class='no_border'><tr>".
									drawPicSub($ln, 0, 0, $n_foo, 0, '', '', '', '', $ln['request_cd'], $this->ms_base_subdomain, false, false, $this->ms_user_id == $ln['proposer_id']).
									"</tr></table>";
					}
					else
					{
						$j++;
						$s_link   = "<a href='/utils/x-dvd-appr.html?edit=XXX&pg={$j}' target='targetappr'>review DVD sub {$ln['dvd_edit_id']}</a>";
						$s_id     = $ln['dvd_id'];
						$s_title  = $ln['title'];
						$va      .= $ln['dvd_edit_id'] . ',';
					}

					if ( $ln['membership_cd'] <> '-' )
					{
						$n_star  = intval($ln['membership_cd']);
						$s_title = "<div style='padding:0 2px 4px 2px;border-bottom:solid 1px #bd0b0b;margin-bottom:4px;color:#bd0b0b'>".
									 "<img src='http://dv1.us/s1/smb{$n_star}.png' style='position:relative;top:3px' /> ".
									 dvdaf3_stardescription($n_star).
								   "</div>".
								   $s_title;
					}

					$s_resurrect = $b_resurrect ? "<br />&nbsp;<br /><a href='javascript:void(DvdApprove.resurrect({$ln['dvd_edit_id']},{$ln['pic_edit_id']}))'>&lt;Resurrect&gt;</a>" : '';

					$str .= "<tr class='center'>".
							  "<td>{$ln['updated_tm']}</td>".
							  "<td>{$s_link}{$s_resurrect}</td>".
							  "<td>{$s_id}</td>".
							  "<td class='left'>{$s_title}</td>".
							  "<td>{$ln['request_txt']}</td>".
							  "<td>{$ln['proposer_id']}</td>".
							  "<td>". dvdaf3_decode($ln['disposition_cd'], DVDAF3_DICT_DISPOSITION ). "</td>".
							  "<td class='left'>{$ln['reviewer_notes']}</td>".
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
			echo  "<h2>{$s_what} Submissions: ".( $i <= $max ? "($i)" : "(first $max)")."</h2>".
				  "<table class='border'>".
					"<thead>".
					  "<tr class='center'>".
						"<td width='1%'>Last updated<br /><img src='http://dv1.us/d1/1.gif' width='85' height='1' /></td>".
						"<td width='1%'>Audit&nbsp;id</td>".
						"<td width='1%'>Object&nbsp;id</td>".
						"<td width='40%' class='left'>Object</td>".
						"<td width='1%'>Sub type</td>".
						"<td width='1%'>Proposed&nbsp;by</td>".
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
				  "&nbsp;";
		else
			echo  "<div class='highkey' style='margin-top:20px'>Sorry, there are no {$s_what_not}.</div>".
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
	//				   "and version_id > 0 ".
	//				   "and verified_version < version_id ".
					   "and dvd_edit_id = 0 ".
					 "ORDER BY dvd_updated_tm DESC",0,__FILE__,__LINE__)) )
		{
			while ( ($ln = CSql::fetch($rr)) && $i < $max + 1 )
			{
				if ( $i < $max )
				{
					$id   = sprintf('%06d', intval($ln['dvd_id']));
					$s_verified_version = $ln['verified_version']; if ( $s_verified_version < 0 ) $s_verified_version = '&nbsp;';
					$s_dvd_verified_tm  = $ln['dvd_verified_tm'];  if ( ! $s_dvd_verified_tm    ) $s_dvd_verified_tm  = '&nbsp;';
					$str .= "<tr class='center'>".
							  "<td><a href='{$this->ms_base_subdomain}/search.html?has={$id}&init_form=str0_has_{$id}' target='filmaf'>{$id}</a></td>".
							  "<td class='left'>{$ln['dvd_title']}</td>".
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
			echo  "<h2>Recent Directly Edited Titles: ".( $i <= $max ? "($i)" : "(first $max)")."</h2>".
				  "<table class='border'>".
					"<thead>".
					  "<tr class='center'>".
						"<td width='1%'>DVD&nbsp;id</td>".
						"<td width='50%' class='left'>DVD&nbsp;title</td>".
						"<td width='1%'>Version</td>".
						"<td width='1%'>Updated&nbsp;by</td>".
						"<td width='1%'>Updated&nbsp;time<br /><img src='http://dv1.us/d1/1.gif' width='85' height='1' /></td>".
						"<td width='1%'>Last verified version</td>".
						"<td width='1%'>Verification time<br /><img src='http://dv1.us/d1/1.gif' width='85' height='1' /></td>".
						"<td width='1%'>Verified&nbsp;by</td>".
					  "</tr>".
					"</thead>".
					"<tbody>".
					  $str.
					"</tbody>".
				  "</table>".
				  "&nbsp;";
		else
			echo  "<div class='highkey' style='margin-top:20px'>Sorry, there are no direct edits in the past {$this->mn_lookback} days.</div>".
				  "&nbsp;";
	}
}

?>
