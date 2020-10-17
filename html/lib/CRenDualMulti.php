<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndDlgStep.php';

class CRenDualMulti extends CWndDlgStep
{
    function constructor() // <<--------------------------------<< 1.0
    {
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->ms_field					= 'publisher';
//		$this->ms_field_nocase			= 'publisher_nocase';
//		$this->ms_field_uc				= 'Publisher';
//		$this->ms_title					= 'Rename '+$this->ms_field_uc;

		$this->ms_include_js			= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-register_{$this->mn_lib_version}.js'></script>\n";
		$this->mb_mod_only				= true;
		$this->ms_changed				= '';
		$this->ms_not_changed			= '';
		$this->mb_done					= false;

		$this->ma_steps					= array();
		$this->ma_steps[0]				= array();
		$this->ma_steps[0]['seed']		= false;
		$this->ma_steps[0]['validate']	= true;
		$this->ma_steps[0]['redirect']	= '';
		$this->ma_steps[1]				= array();
		$this->ma_steps[1]['seed']		= false;
		$this->ma_steps[1]['validate']	= false;
		$this->ma_steps[1]['redirect']	= '';
	}

	function initStep_0()
	{
		$this->initStep_01(0);
	}

	function initStep_1()
	{
		$this->initStep_01(1);
	}

	function getDvdLink($n_dvd_id, $b_edit)
	{
		$s_dvd_id = sprintf("%07d", $n_dvd_id);
		return "<a href='/search.html?has={$s_dvd_id}&init_form=str0_has_{$s_dvd_id}' target='filmaf'>{$s_dvd_id}</a>".
			   ($b_edit ? " (<a href='/utils/x-dvd-edit.html?dvd={$n_dvd_id}' target='targetedit'>edit</a>)" : '');
	}
	   
	function initStep_01($n_step)
	{
		if ( $n_step == 1 && $this->mb_success )
		{
			$s_prev = "<div style='font-weight:normal;color:#072b4b;font-size:11px;margin-bottom:8px'>".
						"Changing from &quot;{$this->ma_fields['current']['value']}&quot; to &quot;{$this->ma_fields['proposed']['value']}&quot;".
					  "</div>".
					  ( $this->ms_changed ?
					  "<div style='margin-bottom:24px'>".
						"Listings changed<div style='font-weight:normal'>{$this->ms_changed}</div>".
					  "</div>" : '').
					  ( $this->ms_not_changed ?
					  "<div style='margin-bottom:24px'>".
						"Listings not changed<div style='font-weight:normal'>{$this->ms_not_changed}</div>".
					  "</div>" : '' );
		}
		else
		{
			$s_prev = "<ul>".
						"<li><strong>Update justification</strong> will be initialized to &quot;{$this->ms_field_uc} renamed by {$this->ms_user_id}.&quot;</li>".
					  "</ul>";
		}

		$this->ma_fields = array(
		'_title'	=> array('kind'  => CWnd_DLG_KIND_TITLE,
							 'flags' => 0,
							 'label' => "Rename {$this->ms_field_uc}"),

		'step'		=> array('kind'  => CWnd_DLG_KIND_INPUT,
							 'flags' => CWnd_DLG_VISI_HIDE,
							 'input' => CWnd_DLG_INPUT_TEXT,
							 'uparm' => DVDAF3_POST,
							 'value' => '1'),

		'_id'		=> array('kind'  => CWnd_DLG_KIND_INFORM,
							 'flags' => 0,
							 'label' => $s_prev),

		'current'	=> array('kind'  => CWnd_DLG_KIND_INPUT,
							 'flags' => CWnd_DLG_NEED,
							 'input' => CWnd_DLG_INPUT_TEXT,
							 'parm'	 => "size='60'",
							 'uparm' => DVDAF3_POST,
							 'label' => "Current ".$this->ms_field),

		'proposed'	=> array('kind'  => CWnd_DLG_KIND_INPUT,
							 'flags' => CWnd_DLG_NEED,
							 'input' => CWnd_DLG_INPUT_TEXT,
							 'parm'  => "size='60'",
							 'uparm' => DVDAF3_POST,
							 'label' => "Renamed ".$this->ms_field),

		'enter'		=> array('kind'  => CWnd_DLG_KIND_RESPONSE,
							 'flags' => 0,
							 'label' => '',
							 'opt'   => array(
										array('type' => 'button', 'name' => 'cancel', 'value' => 'Cancel'   , 'onclick' => "history.go(-1)"),
										array('type' => 'submit', 'name' => 'ok'    , 'value' => 'Apply change', 'onclick' => '')),
							 'input' => CWnd_DLG_INPUT_BUTTONS));

		$this->ms_onsubmit = 'Rename.validate()';
	}

	function validateStep_0()
	{
		CWndDlg::validateDataSubmission();

		if ( $this->mb_mod && $this->ms_user_id == 'ash' && $this->mn_action == CWnd_INPUT_GOOD )
		{
			$c_field			= $this->ms_field;
			$s_current			= $this->ma_fields['current']['value'];
			$s_proposed			= $this->ma_fields['proposed']['value'];
			$s_proposed_nocase	= '';
			$s_proposed_case	= '';
			$s_current_nocase	= '';
			$s_current_case		= '';
			dvdaf_validateinput('a_', $c_field, $s_current_case , $s_col_2, $s_current_nocase , $s_current , '', $s_error, DVDAF_INSERT | DVDAF_HTML | DVDAF_GET_SEC);
			dvdaf_validateinput('a_', $c_field, $s_proposed_case, $s_col_2, $s_proposed_nocase, $s_proposed, '', $s_error, DVDAF_INSERT | DVDAF_HTML | DVDAF_GET_SEC);
			$s_proposed_nocase	= trim(str_replace("/", '', str_replace("'", '', $s_proposed_nocase)));
			$s_proposed_case	= trim(str_replace("'", '', $s_proposed));
			$s_current_nocase	= trim(str_replace("/", '', str_replace("'", '', $s_current_nocase)));
			$s_current_case		= trim(str_replace("'", '', $s_current));

			if ( $s_current_nocase != '' && $s_proposed_nocase != '' )
			{
				$a_data	= array();
				$ss		= "SELECT dvd_id, {$this->ms_field}, {$this->ms_field_nocase} FROM dvd WHERE {$this->ms_field_nocase} like '%/ {$s_current_nocase} /%' ORDER BY dvd_id";

				if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
				{
					while ( ($a_row = CSql::fetch($rr)) )
						$a_data[] = $a_row;
					CSql::free($rr);
				}

				for ( $i = 0 ; $i < count($a_data) ; $i++ )
				{
					$aa = explode(',', $a_data[$i][$this->ms_field]);
					$bb = explode('/', $a_data[$i][$this->ms_field_nocase]);
					$cc = array();
					for ( $j = 0 ; $j < count($aa) ; $j++ ) $aa[$j] = trim($aa[$j]);
					for ( $j = 0 ; $j < count($bb) ; $j++ ) if ($bb[$j] != '') $cc[] = trim($bb[$j]);
					if ( count($aa) == count($cc) )
					{
						for ( $j = 0 ; $j < count($cc) ; $j++ )
							if ( $cc[$j] == $s_current_nocase )
								$aa[$j] = $s_proposed_case;

						$a_data[$i]['case']   = implode(',', $aa);
					}
				}

				$s_what			= "{$this->ms_field_uc} renamed from [{$s_current_case}] to [{$s_proposed_case}] by {$this->ms_user_id}";
				$s_changed		= '';
				$s_not_changed	= '';
				for ( $i = 0 ; $i < count($a_data) ; $i++ )
				{
					$n_dvd_id = $a_data[$i]['dvd_id'];
					if ( isset($a_data[$i]['case']) && $a_data[$i]['case'] != $a_data[$i][$this->ms_field] && count(explode(',',$a_data[$i]['case'])) == count(explode(',',$a_data[$i][$this->ms_field])) )
					{
						dvdaf_validateinput('a_', $c_field, $s_proposed_case, $s_col_2, $s_proposed_nocase, $a_data[$i]['case'], '', $s_error, DVDAF_INSERT | DVDAF_HTML | DVDAF_GET_SEC);

						$ss = "INSERT INTO dvd_hist ".
									"(dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, ".
									 "media_type, num_titles, num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, ".
									 "pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, dvd_updated_tm, dvd_updated_by, dvd_id_merged, ".
									 "last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id) ".
							  "SELECT dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, ".
									 "media_type, num_titles, num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, ".
									 "pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, dvd_updated_tm, dvd_updated_by, dvd_id_merged, ".
									 "last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id ".
								"FROM dvd a ".
							   "WHERE not exists (SELECT 1 FROM dvd_hist b WHERE a.dvd_id = b.dvd_id and a.version_id = b.version_id) ".
								 "and a.dvd_id = {$n_dvd_id}";
						CSql::query_and_free($ss, 0,__FILE__,__LINE__);

						$ss = "UPDATE dvd ".
								 "SET {$this->ms_field} = {$s_proposed_case}, ".
									 "{$this->ms_field_nocase} = {$s_proposed_nocase}, ".
									 "version_id = version_id + 1, ".
									 "dvd_updated_tm = now(), ".
									 "dvd_updated_by = '{$this->ms_user_id}', ".
									 "last_justify = '{$s_what}' ".
							   "WHERE dvd_id = {$n_dvd_id}";
						CSql::query_and_free($ss, 0,__FILE__,__LINE__);

						CSql::query_and_free("CALL update_dvd_search_index({$n_dvd_id},1)",0,__FILE__,__LINE__);

						$s_changed .= $this->getDvdLink($n_dvd_id, false). ', ';
					}
					else
					{
						$s_not_changed .= $this->getDvdLink($n_dvd_id, false). ', ';
					}
				}

				if ( $s_changed == '' && $s_not_changed == '' )
				{
					$this->ma_fields['current']['valid'] = $this->mb_success = false;
					$this->ma_fields['current']['error'] = "No matches found.";
				}
				else
				{
					$this->ms_changed	  = substr($s_changed, 0, -2);
					$this->ms_not_changed = substr($s_not_changed, 0, -2);
					$this->mb_done		  = true;
				}
			}
			else
			{
				$this->mb_success = false;
				if ( $s_current_nocase == '' )
				{
					$this->ma_fields['current']['valid'] = false;
					$this->ma_fields['current']['error'] = strlen($s_current_case) > 0 ? "The current {$this->ms_field} value is not valid." : "Missing a current {$this->ms_field} value.";
				}
				if ( $s_proposed_nocase == '' )
				{
					$this->ma_fields['proposed']['valid'] = false;
					$this->ma_fields['proposed']['error'] = strlen($s_proposed_case) > 0 ? "The proposed {$this->ms_field} value is not valid." : "Missing a proposed {$this->ms_field} value.";
				}
			}

			if ( ! $this->mb_success )
			{
				$this->ms_error_msg = 'Please see below for details.';
				$this->mn_action    = CWnd_INPUT_ERROR;
			}
		}
    }
}

?>
