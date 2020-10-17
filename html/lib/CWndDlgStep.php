<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndDlg.php';
require $gs_root.'/lib/CSecure.php';

class CWndDlgStep extends CWndDlg
{
	function constructor() // <<--------------------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	//	$this->mb_show_trace		= true;
	//	$this->mb_trace_environment	= true;
	//	$this->mb_trace_sql			= true;
	//	$this->mb_allow_redirect	= false;

		$this->ms_form_name			= 'fname';
		$this->ms_form_method		= CWnd_DLG_POST;
		$this->mb_mod_only			= false;

		$this->ma_steps				= array();
		$this->mb_success			= true;
		$this->mn_next_step			= dvdaf3_getvalue('step', DVDAF3_POST|DVDAF3_INT, 0, 10);
	}

	function verifyUser()
	{
		if ( $this->mb_mod_only )
			$this->mb_get_user_status = true;
		parent::verifyUser();
	}

	function initStep_0() { $this->ma_fields = array(); }
	function initStep_1() { $this->ma_fields = array(); }
	function initStep_2() { $this->ma_fields = array(); }
	function initStep_3() { $this->ma_fields = array(); }
	function initStep_4() { $this->ma_fields = array(); }
	function initStep_5() { $this->ma_fields = array(); }
	function initStep_6() { $this->ma_fields = array(); }

	function validateStep_0() { }
	function validateStep_1() { }
	function validateStep_2() { }
	function validateStep_3() { }
	function validateStep_4() { }
	function validateStep_5() { }

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		$this->mn_action = CWnd_INPUT_PROMPT;
		$b_seed			 = false;
		$n_next			 = $this->mn_next_step;
		$n_prev			 = $n_next - 1;
		$b_next			 = true;

		if ( $this->mb_mod_only && ! $this->mb_mod )
		{
			$this->ma_fields = array(
				'_title'	=> array('kind'  => CWnd_DLG_KIND_TITLE,
									 'flags' => 0,
									 'label' => $this->ms_title),
				'_id'		=> array('kind'  => CWnd_DLG_KIND_INFORM,
									 'flags' => 0,
									 'label' => "Sorry, this screen can only be accessed by moderators."));
			return;
		}

		if ( $n_prev >= 0 && $this->ma_steps[$n_prev]['validate'] )
		{
			switch ( $n_prev )
			{
			case 0: $this->initStep_0(); $this->validateStep_0(); break;
			case 1: $this->initStep_1(); $this->validateStep_1(); break;
			case 2: $this->initStep_2(); $this->validateStep_2(); break;
			case 3: $this->initStep_3(); $this->validateStep_3(); break;
			case 4: $this->initStep_4(); $this->validateStep_4(); break;
			}

			if ( $this->mn_action == CWnd_INPUT_GOOD )
			{
				$this->mn_action = CWnd_INPUT_PROMPT;
			}
			else
			{
				$b_seed = $this->ma_steps[$n_prev]['seed'];
				$b_next = false;
			}
		}

		if ( $b_next )
		{
			if ( $this->ma_steps[$n_next]['redirect'] )
			{
				$this->ms_redirect = $this->ma_steps[$n_next]['redirect'];
				$this->ma_fields = array();
			}
			else
			{
				switch ( $n_next )
				{
				case 0: $this->initStep_0(); break;
				case 1: $this->initStep_1(); break;
				case 2: $this->initStep_2(); break;
				case 3: $this->initStep_3(); break;
				case 4: $this->initStep_4(); break;
				case 5: $this->initStep_5(); break;
				case 5: $this->initStep_6(); break;
				}
				$b_seed = $this->ma_steps[$n_next]['seed'];
			}
		}

		if ( $b_seed )
		{
			$n_ext = CSecure::randJpg();
			$this->ma_fields['code_int']['label'] = str_replace('id=______', "id={$n_ext}", $this->ma_fields['code_int']['label']);
			$this->ma_fields['code_int']['value'] = '';
			$this->ma_fields['code_ext']['value'] = $n_ext;
		}
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		echo "&nbsp;<br />";
		parent::drawBodyPage();
	}
}

?>
