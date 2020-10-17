<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndDlgStep.php';

class CDvdClone extends CWndDlgStep
{
    function constructor() // <<--------------------------------<< 1.0
    {
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$this->ms_title					= 'Clone DVD';
		$this->ms_include_js			= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-register_{$this->mn_lib_version}.js'></script>\n";
		$this->mb_mod_only				= true;
		$this->mn_old_dvd_id			= 0;
		$this->mn_new_dvd_id			= 0;

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
		$s_seed = date('Y-m-d H:i:s '). dvdaf3_getvalue('REMOTE_ADDR', DVDAF3_SERVER);
		$s_prev = '';
		if ( $n_step == 1 && $this->mb_success && $this->mn_old_dvd_id && $this->mn_new_dvd_id )
		{
			$s_prev = "<div style='margin:12px 0 36px 0'>".
						$this->getDvdLink($this->mn_old_dvd_id, false). " duplicated as ".
						$this->getDvdLink($this->mn_new_dvd_id, true ).
					  "</div>";
		}

		$this->ma_fields = array(
		'_title'	=> array('kind'  => CWnd_DLG_KIND_TITLE,
							 'flags' => 0,
							 'label' => "Clone DVD"),

		'step'		=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => CWnd_DLG_VISI_HIDE, 'input' => CWnd_DLG_INPUT_TEXT, 'uparm' => DVDAF3_POST, 'value' => '1'),
		'seed'		=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => CWnd_DLG_VISI_HIDE, 'input' => CWnd_DLG_INPUT_TEXT, 'uparm' => DVDAF3_POST, 'value' => $s_seed),

		'_id'		=> array('kind'  => CWnd_DLG_KIND_INFORM,
							 'flags' => 0,
							 'label' => $s_prev.
										  "Special behavior on the new DVD listing:".
										  "<ul>".
											"<li><strong>DVD title</strong> will be prefixed with &quot;DUPLICATED by {$this->ms_user_id}&quot; (to be edited by moderator)</li>".
											"<li><strong>Update justification</strong> will be initialized to &quot;Listing duplicated by {$this->ms_user_id} based on dvd ###.&quot;</li>".
										  "</ul>".

										  "Fields copied:".
										  "<table>".
											"<tr>".
											  "<td valign='top'><ul><li>DVD title</li><li>Screening year</li><li>Original language</li></ul></td>".
											  "<td valign='top'><ul><li>Genre</li><li>Release status</li><li>Screening date</li></ul></td>".
											  "<td valign='top'><ul><li>Imdb links</li><li>Director</li><li>Number of titles</li></ul></td>".
											"</tr>".
										  "</table>".

										  "Fields not copied:".
										  "<table>".
											"<tr>".
											  "<td valign='top'><ul><li>DVD country</li><li>Region</li><li>DVD release date</li><li>Amazon ASIN</li><li>Amazon country</li><li>DVD publisher</li></ul></td>".
											  "<td valign='top'><ul><li>Number of discs</li><li>UPC</li><li>Studio product code</li><li>List price</li><li>Best price</li><li>Picture</li></ul></td>".
											  "<td valign='top'><ul><li>Update justification</li><li>Last verified version</li><li>Verified by</li><li>Verification time</li></ul></td>".
											"</tr>".
										  "</table>".
										""),

		'dvd_id'	=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => CWnd_DLG_NEED | CWnd_DLG_NOAUTOCOMPL, 'input' => CWnd_DLG_INPUT_TEXT, 'parm' => "size='10'", 'uparm' => DVDAF3_POST,
							 'label' => "Please enter the dvd_id you wish to duplicate"),

		'enter'		=> array('kind'  => CWnd_DLG_KIND_RESPONSE,
							 'flags' => 0,
							 'label' => '',
							 'opt'   => array(
										array('type' => 'button', 'name' => 'cancel', 'value' => 'Cancel'   , 'onclick' => "history.go(-1)"),
										array('type' => 'submit', 'name' => 'ok'    , 'value' => 'Duplicate', 'onclick' => '')),
							 'input' => CWnd_DLG_INPUT_BUTTONS));

		$this->ms_onsubmit = 'CloneDvd.validate()';
	}

	function validateStep_0()
	{
		CWndDlg::validateDataSubmission();
		if ( $this->mb_mod && $this->mn_action == CWnd_INPUT_GOOD )
		{
			$n_dvd_id = $this->ma_fields['dvd_id']['value'];
			if ( is_numeric($n_dvd_id) )
			{
				$n_dvd_id = intval($n_dvd_id);
				$s_seed   = dvdaf3_getvalue('seed', DVDAF3_POST);
				if ( $s_seed )
				{
					$n_new_id = CSql::query_and_fetch1("SELECT dvd_id FROM dvd WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);
					if ( $n_new_id > 0 )
					{
						$this->ma_fields['dvd_id']['valid'] = $this->mb_success = false;
						$this->ma_fields['dvd_id']['error'] = "<div style='white-space:nowrap'>".
																$this->getDvdLink($n_dvd_id, false). " has already been duplicated as ". $this->getDvdLink($n_new_id, true).
															  "</div>";
					}
					else
					{
						$s_lower  = preg_replace('/[ -]+/', ' ',$this->ms_user_id);
						$s_update = "INSERT INTO dvd ".
										"(dvd_id, version_id, ".
										"dvd_title, ".
										"dvd_title_nocase, ".
										"film_rel_year, director, director_nocase, orig_language, genre, media_type, num_titles, source, rel_status, film_rel_dd, imdb_id, ".
										"dvd_created_tm, dvd_updated_tm, dvd_updated_by, last_justify, creation_seed) ".
									"SELECT (SELECT max(dvd_id)+1 FROM dvd), 0, ".
										"concat('DUPLICATED by {$this->ms_user_id}<br />',dvd_title), ".
										"concat('/ duplicated by {$s_lower} / ',substring(dvd_title_nocase,3)), ".
										"film_rel_year, director, director_nocase, orig_language, genre, media_type, num_titles, source, rel_status, film_rel_dd, imdb_id, ".
										"now(), now(), '{$this->ms_user_id}', 'Listing duplicated by {$this->ms_user_id} based on dvd {$n_dvd_id}.', '{$s_seed}' ".
									  "FROM dvd ".
									 "WHERE dvd_id = {$n_dvd_id} ".
									   "and not exists (SELECT * FROM dvd WHERE creation_seed = '{$s_seed}')";

						$n_updated = CSql::query_and_free($s_update,0,__FILE__,__LINE__);
						if ( $n_updated )
						{
							$this->mn_old_dvd_id = $n_dvd_id;
							$this->mn_new_dvd_id = CSql::query_and_fetch1("SELECT dvd_id FROM dvd WHERE creation_seed = '{$s_seed}'",0,__FILE__,__LINE__);
							CSql::query_and_free("CALL update_dvd_search_index({$this->mn_new_dvd_id},1)",0,__FILE__,__LINE__);
						}
						if ( ! $this->mn_new_dvd_id )
						{
							$this->ma_fields['dvd_id']['valid'] = $this->mb_success = false;
							$this->ma_fields['dvd_id']['error'] = "The dvd_id entered {$n_dvd_id} could not be duplicated.  Is this an existing listing?";
						}
					}
				}
			}
			else
			{
				$this->ma_fields['dvd_id']['valid'] = $this->mb_success = false;
				$this->ma_fields['dvd_id']['error'] = "The dvd_id {$n_dvd_id} is not numeric.";
			}

			if ( ! $this->mb_success )
			{
				$this->ma_fields['dvd_id']['value'] = '';
				$this->ms_error_msg = 'Please see below for details.';
				$this->mn_action    = CWnd_INPUT_ERROR;
			}
			$this->ma_fields['seed']['value'] = date('Y-m-d H:i:s '). dvdaf3_getvalue('REMOTE_ADDR', DVDAF3_SERVER);
		}
    }
}

?>
