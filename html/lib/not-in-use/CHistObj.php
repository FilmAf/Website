<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWnd.php';

class CHistObj extends CWnd
{
	function constructor()
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace        = true;
//		$this->mb_trace_environment = true;
//		$this->mb_trace_sql         = true;
//		$this->mb_allow_redirect    = true;
		$this->mn_obj_id			= dvdaf3_getvalue('obj'  , DVDAF3_GET|DVDAF3_INT);
		$this->mb_iframe			= dvdaf3_getvalue('frm'  , DVDAF3_GET|DVDAF3_BOOLEAN);
		$this->ms_sql				= '';
		$this->ma_result			= null;

		if ( $this->mb_iframe )
		{
			$this->mb_show_trace        = false;
			$this->mb_trace_environment = false;
			$this->mb_trace_sql         = false;
			$this->mb_corners			= false;
			$this->mn_header_type		= CWnd_HEADER_NONE;
			$this->mn_footer_type		= CWnd_FOOTER_NONE;

			$this->ms_include_css .=
			"<style type='text/css'>".
				"div.hist {margin:0;}".
				"div.version1 {margin:0 4px 4px 4px;}".
				"div.versionN {margin:20px 4px 4px 4px;}".
			"</style>";
		}
		else
		{
			$this->ms_include_css .=
			"<style type='text/css'>".
				"div.hist {margin:4px;}".
				"div.version1 {margin:20px 4px 4px 4px;}".
				"div.versionN {margin:20px 4px 4px 4px;}".
			"</style>";
		}
	}

	function getSql()
	{
		$o = &$this->mo_obj;

		$s_actu = '';
		$s_hist = '';
		$s_subm = '';
		for ( $i = 0 ; $i < count($o->ma_attributes) ; $i++ )
		{
			if ( $o->ma_attributes[$i]['ehis'] & 2 )
			{
				$s_actu .= "{$o->ma_attributes[$i]['actu']} {$o->ma_attributes[$i]['col']}, ";
				$s_hist .= "{$o->ma_attributes[$i]['hist']}, ";
				$s_subm .= "{$o->ma_attributes[$i]['subm']} u_{$o->ma_attributes[$i]['col']}, ";
			}
			else
			{
				if ( $o->ma_attributes[$i]['ehis'] & (1|4) )
				{
					$s_actu .= "{$o->ma_attributes[$i]['col']}, ";
					$s_hist .= "{$o->ma_attributes[$i]['col']}, ";
					$s_subm .= "b.{$o->ma_attributes[$i]['col']} u_{$o->ma_attributes[$i]['col']}, ";
				}
			}
		}

		$this->ms_sql  = "SELECT a.*, ".
								"b.{$o->ms_submit_request_cd} request_cd, ".			// request_cd
								"b.{$o->ms_submit_disposition_cd} disposition_cd, ".	// disposition_cd
								"b.{$o->ms_submit_proposer_id} proposer_id, ".			// proposer_id
								"b.{$o->ms_submit_proposer_notes} proposer_notes, ".	// proposer_notes
								"b.{$o->ms_submit_proposed_tm} proposed_tm, ".			// proposed_tm
								"b.{$o->ms_submit_updated_tm} u_updated_tm, ".			// updated_tm **********
								"b.{$o->ms_submit_reviewer_id} reviewer_id, ".			// reviewer_id
								"b.{$o->ms_submit_reviewer_notes} reviewer_notes, ".	// reviewer_notes
								"b.{$o->ms_submit_reviewed_tm} reviewed_tm, ".			// reviewed_tm
								"b.{$o->ms_submit_hist_version_id} hist_version_id, ".	// hist_version_id
								$s_subm.
								"b.{$o->ms_submit_update_justify} update_justify ".		// update_justify
						   "FROM (SELECT {$o->ms_version} version_id, ".				// version_id
										"{$o->ms_subversion} subversion_id, ".			// subversion_id
										$s_actu.
										"{$o->ms_mod_flags} mod_flags, ".				// mod_flags
										"{$o->ms_created_tm} created_tm, ".				// pub_created_tm
										"{$o->ms_updated_tm} updated_tm, ".				// pub_updated_tm
										"{$o->ms_updated_by} updated_by, ".				// pub_updated_by
										"{$o->ms_justify} last_justify, ".				// last_justify
										"{$o->ms_verified_tm} verified_tm, ".			// pub_verified_tm
										"{$o->ms_verified_by} verified_by, ".			// pub_verified_by
										"{$o->ms_verified_version} verified_version, ".	// verified_version
										"{$o->ms_gen_by_edit_id} obj_edit_id, ".		// pub_edit_id
										"0 obj_id_merged ".								// pub_id_merged
								   "FROM {$o->ms_tbl_obj} c ".
								  "WHERE {$o->ms_key} = {$this->mn_obj_id} ".
								  "UNION ".
								 "SELECT {$o->ms_version}, ".							// version_id
										"{$o->ms_subversion}, ".						// subversion_id
										$s_hist.
										"{$o->ms_mod_flags}, ".							// mod_flags
										"{$o->ms_created_tm}, ".						// pub_created_tm
										"{$o->ms_updated_tm}, ".						// pub_updated_tm
										"{$o->ms_updated_by}, ".						// pub_updated_by
										"{$o->ms_justify}, ".							// last_justify
										"{$o->ms_verified_tm}, ".						// pub_verified_tm
										"{$o->ms_verified_by}, ".						// pub_verified_by
										"{$o->ms_verified_version}, ".					// verified_version
										"{$o->ms_gen_by_edit_id}, ".					// pub_edit_id
										"{$o->ms_id_merged} obj_id_merged ".			// pub_id_merged
								   "FROM {$o->ms_tbl_hist} h ".
								  "WHERE {$o->ms_key} = {$this->mn_obj_id}) a ".
						   "LEFT JOIN {$o->ms_tbl_submit} b ".
							 "ON a.obj_edit_id = b.{$o->ms_submit_id} ".
						  "ORDER BY a.version_id DESC, subversion_id DESC";
/*
			pub_direct_update
			pub_submit
			+-----------------+---------------+------+-----+---------+----------------+
			| pub_edit_id     | int(11)       | NO   | PRI | NULL    | auto_increment |
			| pub_id          | int(11)       | NO   |     | 0       |                |
			| request_cd      | char(1)       | NO   |     | -       |                |
			| disposition_cd  | char(1)       | NO   |     | -       |                |
			| proposer_id     | varchar(32)   | NO   |     | -       |                |
			| proposer_notes  | varchar(1000) | NO   |     | -       |                |
			| proposed_tm     | datetime      | NO   |     |         |                |
			| updated_tm      | datetime      | NO   |     |         |                |
			| reviewer_id     | varchar(32)   | NO   |     | -       |                |
			| reviewer_notes  | varchar(1000) | NO   |     | -       |                |
			| reviewed_tm     | datetime      | YES  |     | NULL    |                |
			| hist_version_id | int(11)       | NO   |     | 0       |                |
			| pub_name        | varchar(100)  | NO   |     | -       |                |
			| official_site   | varchar(255)  | NO   |     | -       |                |
			| wikipedia       | varchar(255)  | NO   |     | -       |                |
			| update_justify  | varchar(200)  | NO   |     | -       |                |
			| creation_seed   | varchar(36)   | NO   |     | -       |                |
			+-----------------+---------------+------+-----+---------+----------------+
*/
	}

	function validUserAccess()
	{
		return $this->mb_iframe || $this->mb_logged_in ? CUser_ACCESS_GRANTED : CUser_NOACCESS_GUEST; 
	}
	function drawField(&$a, $i, $b_label)
	{
		$o  = &$this->mo_obj;
		$j  = $o->ma_attributes[$i]['id'];

		$p1 = DVDAF1_STYLE_ONE;
		$p2 = 0;
		$p3 = DVDAF3_NO_STYLE_TD | DVDAF3_NBSP_ON_EMPTY;
		$p4 = 0;
		$p5 = (($o->ma_attributes[$i]['ehis'] & 4) ? DVDAF5_TEXT_ONLY : 0) | DVDAF5_NO_OVERWRITES;

		if ( isset($o->ma_attributes[$i]['parh']) )
		{
			if ( isset($o->ma_attributes[$i]['parh_id']) )
			{
				return	dvdaf_getbrowserfield($a, $j,
											$p1 | ($b_label ? DVDAF1_FIELD_NAME_TD : 0),
											$p2 | DVDAF2_TABLE_TD_BEG,
											$p3,
											$p4,
											$p5).
						($a[$o->ma_attributes[$i]['parh']]
						  ?	' ('.dvdaf_getbrowserfield($a, $o->ma_attributes[$i]['parh_id'],
											$p1,
											$p2,
											$p3,
											$p4,
											$p5).')'
						  :	'').
						'</td>';
			}
		}
		else
		{
			return	dvdaf_getbrowserfield($a, $j,
											$p1 | DVDAF1_TABLE_TD | ($b_label ? DVDAF1_FIELD_NAME_TD : 0),
											$p2,
											$p3,
											$p4,
											$p5);
		}
	}
	function drawBodyPage()
	{
		if ( ! $this->mb_logged_in )
			return;

		$a		= null;
		$o		= &$this->mo_obj;
		$n_tot	= count($o->ma_attributes);
		$n_cnt	= 0;
		$s_cls	= '1';

		if ( $this->mn_obj_id > 0 )
		{
			$this->getSql();
			$this->ma_result = CSql::query($this->ms_sql,0,__FILE__,__LINE__);
			if ( $this->ma_result ) $a = CSql::fetch($this->ma_result);
		}

		if ( ! $this->ma_result || ! $a )
		{
			$this->drawNotFound();
			return;
		}

		$o->initLabel();

		echo "<div class='hist'>";
		while ( ($b = CSql::fetch($this->ma_result)) )
		{
			$s_changes = '';
			if ( $a['mod_flags'] != $b['mod_flags'] ) $s_changes .= "<tr><td>Mod flags</td><td>{$b['mod_flags']}</td><td>{$a['mod_flags']}</td></tr>";

			if ( $a['obj_edit_id'] )
			{
				$s_head = "<tr><td>Field</td><td>Approved value</td><td>Proposed value</td><td>Old value</td></tr>";
				$c = array();
				for ( $i = 0 ; $i < $n_tot ; $i++ )
				{
					if ( $o->ma_attributes[$i]['ehis'] & (1|2) )
					{
						$k = $o->ma_attributes[$i]['col'];
						$c[$k] = $b['u_'.$k];
						if ( $a[$k] !== $b[$k] || $a[$k] !== $c[$k] )
							$s_changes .= "<tr>".$this->drawField($a,$i,1).$this->drawField($c,$i,0).$this->drawField($b,$i,0)."</tr>";
					}
				}
			}
			else
			{
				$s_head = "<tr><td>Field</td><td>New value</td><td>Old value</td></tr>";
				for ( $i = 0 ; $i < $n_tot ; $i++ )
				{
					if ( $o->ma_attributes[$i]['ehis'] & (1|2) )
					{
						$k = $o->ma_attributes[$i]['col'];
						if ( $a[$k] !== $b[$k] )
							$s_changes .= "<tr>".$this->drawField($a,$i,1).$this->drawField($b,$i,0)."</tr>";
					}
				}
			}

			echo											  "<div class='version{$s_cls}'>".
			($n_cnt					== 0				? '' :	"<div class='ruler'>&nbsp;</div>").	
																"<div>Version {$a['version_id']}.{$a['subversion_id']}</div>".
																"<div><span class='one_lbl'>Updated on</span> {$a['updated_tm']} <span class='one_lbl'>by</span> {$a['updated_by']}</div>".
			($a['verified_version']	!= $a['version_id']	? '' :	"<div><span class='one_lbl'>Verified on</span> {$b['verified_tm']} <span class='one_lbl'>by</span> {$a['verified_by']}</div>").
			($a['last_justify']		== '-'				? '' :	"<div><span class='one_lbl'>Update Justification:</span> {$a['last_justify']}</div>").
																"<div><span class='one_lbl'>Submission:</span> ".($a['obj_edit_id'] ? "{$a['obj_edit_id']}" : "Direct Edit")."</div>".
			($a['obj_id_merged']	== 0				? '' :	"<div><span class='one_lbl'>Merged into:</span> {$a['obj_id_merged']}</div>");

			if ( $s_changes )
			{
				echo											"<table class='dvd_table'>".
																  "<thead>{$s_head}</thead>".
																  "<tbody>{$s_changes}</tbody>".
																"</table>".
															  "</div>";
			}
			else
			{
				if ( $a['obj_id_merged'] == 0 )
					echo										"<div>Changes undone shortly after version was created.</div>".
															  "</div>";
			}
			$a = $b;
			$n_cnt++;
			$s_cls = 'N';
		}

		if ( $a )
		{
			$s_changes = '';
			if ( $a['obj_edit_id'] )
			{
				$s_head = "<tr><td>Field</td><td>Approved value</td><td>Proposed value</td></tr>";
				$c = array();
				for ( $i = 0 ; $i < $n_tot ; $i++ )
				{
					if ( $o->ma_attributes[$i]['ehis'] & (1|2) )
					{
						$k = $o->ma_attributes[$i]['col'];
						$c[$k] = $a['u_'.$k];
						$s_changes .= "<tr>".$this->drawField($a,$i,1).$this->drawField($c,$i,0)."</tr>";
					}
				}
			}
			else
			{
				$s_head = "<tr><td>Field</td><td>Value</td></tr>";
				for ( $i = 0 ; $i < $n_tot ; $i++ )
					if ( $o->ma_attributes[$i]['ehis'] & (1|2) )
						$s_changes .= "<tr>".$this->drawField($a,$i,1)."</tr>";
			}
			echo											  "<div class='version{$s_cls}'>".
																"<div class='ruler'>&nbsp;</div>".
																"<div><span class='one_lbl'>Version</span> {$a['version_id']}.{$a['subversion_id']}</div>".
																"<div><span class='one_lbl'>Created on</span> {$a['updated_tm']} <span class='one_lbl'>by</span> {$a['updated_by']}</div>".
			($a['verified_version']	<  0				? '' :	"<div><span class='one_lbl'>Verified by</span> {$a['verified_by']}</div>").
			($a['last_justify']		== '-'				? '' :	"<div><span class='one_lbl'>Justification:</span> {$a['last_justify']}</div>").
																"<div><span class='one_lbl'>Submission:</span> ".($a['obj_edit_id'] ? "{$a['obj_edit_id']}" : "Direct Edit")."</div>".
			($a['obj_id_merged']	== 0				? '' :	"<div><span class='one_lbl'>Merged into:</span> {$a['obj_id_merged']}</div>").
																"<table class='dvd_table'>".
																  "<thead>{$s_head}</thead>".
																  "<tbody>{$s_changes}</tbody>".
																"</table>".
															  "</div>";
		}
		echo "</div>";
	}

	function drawNotFound()
	{
		$this->ms_display_error = $this->mn_obj_id > 0 ? "We do not have a {$this->mo_obj->ms_obj_name} with an id of {$this->mn_obj_id}."
													   : "A valid {$this->mo_obj->ms_obj_name} id was not specified.";
		$this->drawMessages(true,false);
	}
}

?>
