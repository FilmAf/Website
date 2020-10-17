<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndDlgStep.php';

class CSetFeaturetteDir extends CWndDlgStep
{
    function constructor() // <<--------------------------------<< 1.0
    {
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$this->ms_title					= 'Set Featurette Director';
		$this->ms_include_js			= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-register_{$this->mn_lib_version}.js'></script>\n";
		$this->mb_mod_only				= true;
		$this->ms_changed				= '';
		$this->ms_not_changed			= '';
		$this->mb_done					= false;
		$this->ms_director				= '';

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
		$s_prev = '';
		if ( $n_step == 1 && $this->mb_success )
		{
			if ( $this->ms_changed )
				$s_prev .= "<div style='margin:0 0 24px 0'>".
							 "Listings changed<div style='font-weight:normal'>{$this->ms_changed}</div>".
						   "</div>";
			if ( $this->ms_not_changed )
				$s_prev .= "<div style='margin:0 0 24px 0'>".
							 "Listings not changed<div style='font-weight:normal'>{$this->ms_not_changed}</div>".
						   "</div>";
		}
		else
		{
			$s_prev = "<ul>".
						"<li>A &quot;(-)&quot; will be appended to (or removed from) the director name indicating he/she to be (or not to be) the director of a featurette.</li>".
						"<li><strong>Update justification</strong> will be initialized to &quot;Setting (or resetting) featurette director by {$this->ms_user_id}.&quot;</li>".
					  "</ul>";
		}

		$this->ma_fields = array(
		'_title'	=> array('kind'  => CWnd_DLG_KIND_TITLE,
							 'flags' => 0,
							 'label' => "Set or Reset One as a Featurette Director"),

		'step'		=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => CWnd_DLG_VISI_HIDE, 'input' => CWnd_DLG_INPUT_TEXT, 'uparm' => DVDAF3_POST, 'value' => '1'),

		'_id'		=> array('kind'  => CWnd_DLG_KIND_INFORM,
							 'flags' => 0,
							 'label' => $s_prev),

		'director'	=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => CWnd_DLG_NEED | CWnd_DLG_NOAUTOCOMPL, 'input' => CWnd_DLG_INPUT_TEXT, 'parm' => "size='40'", 'uparm' => DVDAF3_POST,
							 'label' => "Director name".
										"<p>".
										  "The change will be applied to all listings that match the name you provide. Do not include &quot;(-)&quot;.".
										"</p>"),

		'featuret'		=> array('kind'  => CWnd_DLG_KIND_INPUT,
							 'flags' => 0,
							 'label' => "Featurette director?".
										"<p>".
										  "Featurette directors are not included in director statistics, otherwise they work the same other directors.".
										"</p>",
							 'input' => CWnd_DLG_INPUT_BOOL,
							 'parm'  => '',
							 'value' => 'Y',
							 'uparm' => DVDAF3_POST|DVDAF3_LOWER),

		'enter'		=> array('kind'  => CWnd_DLG_KIND_RESPONSE,
							 'flags' => 0,
							 'label' => '',
							 'opt'   => array(
										array('type' => 'button', 'name' => 'cancel', 'value' => 'Cancel'   , 'onclick' => "history.go(-1)"),
										array('type' => 'submit', 'name' => 'ok'    , 'value' => 'Apply change', 'onclick' => '')),
							 'input' => CWnd_DLG_INPUT_BUTTONS));

		$this->ms_onsubmit = 'FeaturetteDir.validate()';
	}

	function validateStep_0()
	{
		CWndDlg::validateDataSubmission();
		if ( $this->mb_mod && $this->mn_action == CWnd_INPUT_GOOD )
		{
			$c_director = 'director';
			$s_director = $this->ma_fields['director']['value'];
			$b_featuret = $this->ma_fields['featuret']['value'] == 'y';
			dvdaf_validateinput('a_', $c_director, $s_director_case, $s_col_2, $s_director_nocase, $s_director, '', $s_error, DVDAF_INSERT | DVDAF_HTML | DVDAF_GET_SEC);

			if ( $s_director_nocase != '' )
			{
				$a_data = array();
				$s_director_nocase	= trim(str_replace("/", '', str_replace("'", '', $s_director_nocase)));
				$s_director_case	= trim(str_replace("'", '', $s_director));
				$this->ms_director	= $s_director_case;

				$ss = "SELECT dvd_id, director, director_nocase FROM dvd WHERE director_nocase like '%/ {$s_director_nocase} /%' ORDER BY dvd_id";

				if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
				{
					while ( ($a_row = CSql::fetch($rr)) )
						$a_data[] = $a_row;
					CSql::free($rr);
				}

				for ( $i = 0 ; $i < count($a_data) ; $i++ )
				{
					$aa = explode(',', $a_data[$i]['director']);
					$bb = explode('/', $a_data[$i]['director_nocase']);
					$cc = array();
					for ( $j = 0 ; $j < count($aa) ; $j++ ) $aa[$j] = trim($aa[$j]);
					for ( $j = 0 ; $j < count($bb) ; $j++ ) if ($bb[$j] != '') $cc[] = trim($bb[$j]);
					if ( count($aa) == count($cc) )
					{
						for ( $j = 0 ; $j < count($cc) ; $j++ )
							if ( $cc[$j] == $s_director_nocase )
								$aa[$j] = $s_director_case . ($b_featuret ? ' (-)' : '');

						$a_data[$i]['director2'] = implode(',', $aa);
					}
				}

				$s_changed = '';
				$s_not_changed = '';
				for ( $i = 0 ; $i < count($a_data) ; $i++ )
				{
					$n_dvd_id = $a_data[$i]['dvd_id'];
					if ( isset($a_data[$i]['director2']) && $a_data[$i]['director2'] != $a_data[$i]['director'] && count(explode(',',$a_data[$i]['director2'])) == count(explode(',',$a_data[$i]['director'])) )
					{
						dvdaf_validateinput('a_', $c_director, $s_director_case, $s_col_2, $s_director_nocase, $a_data[$i]['director2'], '', $s_error, DVDAF_INSERT | DVDAF_HTML | DVDAF_GET_SEC);
						$ss = "UPDATE dvd ".
								 "SET director = {$s_director_case}, ".
									 "director_nocase = {$s_director_nocase}, ".
									 "dvd_updated_tm = now(), ".
									 "dvd_updated_by = '{$this->ms_user_id}' ".
							   "WHERE dvd_id = {$n_dvd_id}";
						CSql::query_and_free($ss, 0,__FILE__,__LINE__);

						$ss = ($b_featuret ? "Setting" : "Resetting") . " featurette director by {$this->ms_user_id}";
						$ss = "INSERT INTO dvd_submit (".
									 "dvd_id, request_cd, disposition_cd, proposer_id, proposer_notes, proposed_tm, ".
									 "updated_tm, reviewer_id, reviewer_notes, reviewed_tm, hist_version_id, dvd_title, film_rel_year, director, publisher, ".
									 "orig_language, country, region_mask, genre, media_type, num_titles, num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, ".
									 "imdb_id, list_price, sku, upc, asin, amz_country, update_justify, creation_seed) ".
							  "SELECT dvd_id, 'E' request_cd, 'A' disposition_cd, '{$this->ms_user_id}' proposer_id, '{$ss}' proposer_notes, now() proposed_tm, ".
									 "now() updated_tm, '{$this->ms_user_id}' reviewer_id, '-' reviewer_notes, now() reviewed_tm, version_id hist_version_id, dvd_title, film_rel_year, director, publisher, ".
									 "orig_language, country, region_mask, genre, media_type, num_titles, num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, ".
									 "imdb_id, list_price, sku, upc, asin, amz_country, 'Duplicate' update_justify, '-' creation_seed ".
								"FROM dvd ".
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
					$this->ma_fields['director']['valid'] = $this->mb_success = false;
					$this->ma_fields['director']['error'] = "No matches found.";
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
				$this->ma_fields['director']['valid'] = $this->mb_success = false;
				$this->ma_fields['director']['error'] = strlen($s_director) > 0 ? "The director name is not valid." : "Missing director name.";
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
