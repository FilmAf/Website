<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndDlgStep.php';

class CLogin extends CWndDlgStep
{
	function constructor() // <<--------------------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace			= true;
//		$this->mb_trace_environment		= true;
//		$this->mb_trace_sql				= true;
//		$this->mb_allow_redirect		= false;

		$this->ms_title					= 'Login';
		$this->ms_include_js			= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-register_{$this->mn_lib_version}.js'></script>\n";

		$this->ms_good_redirect			= dvdaf3_getvalue('redirect', DVDAF3_GET);
		$this->ms_form_action		   .= ($this->ms_good_redirect ? "?redirect={$this->ms_good_redirect}" : '');
		$this->mn_header_type			= substr($this->ms_good_redirect,0,17) == '/utils/close.html' ? CWnd_HEADER_SMALL : CWnd_HEADER_BIG;

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
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$this->ma_fields = array(
			'_title'	=> array('kind'  => CWnd_DLG_KIND_TITLE,
								 'flags' => 0,
								 'label' => "Login"),

			'step'		=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => CWnd_DLG_VISI_HIDE, 'input' => CWnd_DLG_INPUT_TEXT, 'uparm' => DVDAF3_POST, 'value' => '1'),

			'name'		=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => CWnd_DLG_NEED, 'input' => CWnd_DLG_INPUT_TEXT, 'parm' => "size='36'", 'min' => 0, 'max' => 32, 'uparm' => DVDAF3_POST|DVDAF3_LOWER,
								 'label' => "User name".
											"<p>".
											  "If you forgot your user name you can <a href='/utils/find-account.html'>".
											  "find your account based on your email address</a>. If you are not a Film Aficionado member ".
											  "you can <a href='/utils/register.html'>quickly sign up for a free ".
											  "account</a>. Don't worry, we do not spam. If you have already signed up and need ".
											  "to validate your account you can <a href='/utils/validate-email.html?".
											  "cd=resend'>request a new confirmation email</a> or <a href='/utils/".
											  "validate-email.html'>enter the validation code</a> you received in your email.".
											"</p>"),

//			'pass'		=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => CWnd_DLG_NEED, 'input' => CWnd_DLG_INPUT_PASS, 'parm' => "size='36'", 'min' => 0, 'max' => 32, 'uparm' => DVDAF3_POST, 'value' => 'Not required',
			'pass'		=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => 0, 'input' => CWnd_DLG_INPUT_TEXT, 'parm' => "size='36'", 'min' => 0, 'max' => 32, 'uparm' => DVDAF3_POST, 'value' => 'Not required',
								 'label' => "Password".
											"<p>".
											  "If you forgot your password you can <a href='/utils/reset-password.html'>".
											  "have a new password sent to you</a>.".
											"</p>"),
			
			'remeb'		=> array('kind'  => CWnd_DLG_KIND_INPUT,
								 'flags' => 0,
								 'label' => "Remember me?".
											"<p>".
											  "If enabled, you will be automatically logged in when you visit us again from this ".
											  "computer and browser. This is not recommended for shared computers.".
											"</p>",
								 'input' => CWnd_DLG_INPUT_BOOL,
								 'parm'  => '',
								 'value' => 'N',
								 'uparm' => DVDAF3_POST|DVDAF3_LOWER),

			'enter'		=> array('kind'  => CWnd_DLG_KIND_RESPONSE,
								 'flags' => 0,
								 'label' => '',
								 'opt'   => array(array('type' => 'submit', 'name' => 'ok', 'value' => 'Login', 'onclick' => '')),
								 'input' => CWnd_DLG_INPUT_BUTTONS));

		$s_id = dvdaf3_getvalue('id', DVDAF3_GET);
		if ( $s_id ) $this->ma_fields['name']['value'] = $s_id;

		$this->ms_onsubmit = 'Login.validate()';
	}

	function validateStep_0()
	{
		CWndDlg::validateDataSubmission();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		if ( $this->mn_action == CWnd_INPUT_GOOD )
		{
			$s_name      = $this->ma_fields['name']['value'];
			$s_pass      = $this->ma_fields['pass']['value'];
			$b_error_msg = false;

			$rr = CSql::query_and_fetch("SELECT a.password_hash, b.email FROM dvdaf_user a JOIN dvdaf_user_2 b ON a.user_id = b.user_id WHERE a.user_id = '$s_name'", 0,__FILE__,__LINE__);
			if ( $rr )
			{
				// Passwords and email accounts have been removed in this data distribution due to user privacy
				//if ( $rr['email'] == '-' )
				//	$this->mb_success = false;
				//else
				//	CForm::valPass($b_error_msg, $this->ma_fields['pass'], $s_pass, $rr['password_hash']);
				$this->mb_success = true;
			}
			else
			{
				CForm::setUserNotFound($b_error_msg, $this->ma_fields['name'], $s_name);
			}

			if ( $b_error_msg )
			{
				$this->ms_error_msg = 'Please see below for details.';
				$this->mn_action = CWnd_INPUT_ERROR;
			}
		}

		if ( $this->mn_action == CWnd_INPUT_GOOD && $this->mb_success )
		{
			$this->ms_user_id = $s_name;
			$this->loginUser($this->ma_fields['remeb']['value'] == 'y');
		}
	}

	function initStep_1()
	{
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		if ( $this->mb_success )
		{
			$this->ma_fields   = array();
			$this->ms_redirect = $this->ms_good_redirect ? $this->ms_good_redirect : $this->ms_base_subdomain;
		}
		else
		{
			$this->ma_fields = array(
				'_title'	=> array('kind'  => CWnd_DLG_KIND_TITLE,
									 'flags' => 0,
									 'label' => "Login"),

					'_id'	=> array('kind'  => CWnd_DLG_KIND_INFORM,
									 'flags' => 0,
									 'label' => "Sorry, you email address has not been validate yet."),

					'name'	=> array('kind'  => CWnd_DLG_KIND_INPUT, 'flags' => CWnd_DLG_VISI_HIDE, 'input' => CWnd_DLG_INPUT_TEXT, 'uparm' => DVDAF3_POST, 'value' => $this->ma_fields['name']['value']),

					'enter'	=> array('kind'  => CWnd_DLG_KIND_RESPONSE,
									 'flags' => 0,
									 'label' => '',
									 'opt'   => array(array('type' => 'button', 'name' => 'cancel', 'value' => 'Continue to homepage'   , 'onclick' => "location.href=\"/\""),
													  array('type' => 'button', 'name' => 'ok'    , 'value' => 'Resend validation email', 'onclick' => "location.href=\"/utils/validate-email.html?cd=resend\"")),
									 'input' => CWnd_DLG_INPUT_BUTTONS));
		}
	}
}

?>
