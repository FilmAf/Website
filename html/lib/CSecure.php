<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CSecure
{
    function randEmail()
    {
		$s_auth_hash = sprintf('%07d', (mt_rand(1000,9999) * 1000 + (intval(microtime() * 1000) % 1000)));
		return substr($s_auth_hash,4). '-'. substr($s_auth_hash,0,4);
    }

	function genExt()
	{
		$a_dig  = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z');
		$n_cnt  = count($a_dig);
		$n_dig  = 6;
		$n_rnd	= mt_rand(pow($n_cnt, $n_dig - 1), pow($n_cnt, $n_dig));
		$s_ext  = '';

		for ( $i = 0 ; $i < $n_dig ; $i++ )
		{
			$s_ext .= $a_dig[$n_rnd % $n_cnt];
			$n_rnd  = floor($n_rnd / $n_cnt);
		}
				
		// Replace zero with an uppercase letter 'o' as the zero in this font is too similar to the number '8'
		$s_ext = str_replace('0','O',$s_ext);
		return $s_ext;
	}

    function randJpg()
    {
		$n_ext	= mt_rand(100,999) * 1000 + (intval(microtime() * 1000) % 1000);
		$s_int	= CSecure::genExt();

		$ss = "DELETE FROM human_check WHERE created_tm < date_add(now(), INTERVAL -1 DAY)";
		CSql::query_and_free($ss, 0,__FILE__,__LINE__);

		$ss = "INSERT INTO human_check (external_id, internal_id, created_tm) VALUES ({$n_ext}, '{$s_int}', now())";
		CSql::query_and_free($ss, 0,__FILE__,__LINE__);

		return $n_ext;
    }

    function validateJpg($n_ext, $s_int)
    {
//		CSql::log_debug(__CLASS__.'::'.__METHOD__.' '."n_ext $n_ext, s_int = $s_int");
		$n_ext = intval($n_ext);
		// i, I => 1 (number 1)
		// 0, o -> O (letter o)
		$s_int = str_replace('I', '1', str_replace('0', 'O', strtoupper(substr($s_int,0,6))));

		$ss = "SELECT 1 FROM human_check where external_id = {$n_ext} and internal_id = '{$s_int}'";
//		CSql::log_debug(__CLASS__.'::'.__METHOD__.' '.$ss);
		return CSql::query_and_fetch1($ss, 0,__FILE__,__LINE__) == 1;
    }

    function seedJpg(&$a_field_int, &$a_field_ext)
    {
		$n_ext = CSecure::randJpg();
		$a_field_int['label'] = str_replace('id=______', "id={$n_ext}", $this->ma_fields['code_int']['label']);
		$a_field_int['value'] = '';
		$a_field_ext['value'] = $n_ext;
    }
}

class CForm
{
    function valSecurityCode(&$b_error_msg, &$a_field, $n_code_ext, $s_code_int)
    {
		if ( ! CSecure::validateJpg($n_code_ext, $s_code_int) )
		{
			$a_field['valid'] = false;
			$a_field['value'] = '';
			$a_field['error'] = 'The Security code confirmation you entered does not match one in the picture.';
			$b_error_msg = true;
			return false;
		}
		return true;
    }

    function valEmailCode(&$b_error_msg, &$a_field, $s_val_cd_1, $s_val_cd_2)
    {
		if ( $s_val_cd_1 != $s_val_cd_2 )
		{
			$a_field['valid'] = false;
			$a_field['value'] = '';
			$a_field['error'] = 'The email validation code you entered does not match the last one we sent you.';
			$b_error_msg = true;
			return false;
		}
		return true;
    }

    function valEmail(&$b_error_msg, &$a_field, $s_email)
    {
		if ( preg_match('/^[^@]+@([^@\.]+\.)+[A-Za-z]{2,4}$/', $s_email) <  1 )
//		if ( preg_match('/^[^@]+@[^@\.]+\.(([A-Za-z]{2,3})|([A-Za-z]{2}\.[A-Za-z]{2}))$/', $s_email) <  1 )
//		if ( preg_match('/^[^@]+@[^@\.]+\.(([A-Za-z]{2,3})|([^@\.]+\.[A-Za-z]{2}\.[A-Za-z]{2}))$/', $s_email) <  1 )
		{
			$a_field['valid'] = false;
			$a_field['error'] = 'The email address you entered does not look right.';
			$b_error_msg = true;
			return false;
		}
		return true;
    }

    function valPass(&$b_error_msg, &$a_field, $s_pass, $s_hash)
    {
		if ( $s_hash != CHash::hash_password($s_pass) )
		{
			$a_field['valid'] = false;
			$a_field['error'] = 'The password you typed does not match our records.  Please try again.';
			$a_field['value'] = '';
			$b_error_msg = true;
			return false;
		}
	return true;
    }

    function setUserNotFound(&$b_error_msg, &$a_field, $s_name)
    {
		$a_field['valid'] = false;
		$a_field['error'] = 'Sorry, we do not have a user with that name. You may have used a different handle '.
							'or your registration may have expired.'.
							"<ul>".
							"<li>Follow <a href='/utils/find-account.html'>this link</a> to look up your user name.</ li>".
							"<li>Follow <a href='/utils/register.html'>this link</a> to sign up again.</li>".
							'</ul>';
		$b_error_msg = true;
	}

	function setUserExists(&$b_error_msg, &$a_field, $s_name)
	{
		$a_field['valid'] = false;
		$a_field['error'] = "The user name {$s_name} is already in use.";
		$b_error_msg = true;
	}

	function setUserEmailExists(&$b_error_msg, &$a_field)
	{
		$a_field['valid'] = false;
		$a_field['error'] = 'This email address is already in use. Is that you?'.
							"<ul>".
							"<li>Follow <a href='/utils/find-account.html'>this link</a> to look up your existing user name.</ li>".
							"<li>Follow <a href='/utils/rename-account.html'>this link</a> to change your existing user name.</li>".
							'</ul>'.
							'Otherwise enter another email address below.';
		$b_error_msg = true;
	}

	function valNewEmail(&$b_error_msg, &$a_field, $s_email)
	{
		if ( ! CForm::valEmail($b_error_msg, $a_field, $s_email) )
			return false;

		if ( strlen($s_email) > 60 )
		{
			$a_field['valid'] = false;
			$a_field['error'] = 'We can not accept email addresses longer than 60 characters.';
			$b_error_msg = true;
			return false;
		}
		return true;
	}

	function valUserName(&$b_error_msg, &$a_field, $s_name)
	{
		$s_error = '';

		if ( strlen($s_name) < 3 || strlen($s_name) > 30 )
			$s_error = 'User names must have between 3 and 30 characters.';
		else
			if ( preg_match('/[^a-z0-9-]/', $s_name) > 0 )
				$s_error = 'User names should only have lowercase letters [a-z], numbers [0-9], and dashes [-].';
			else
				if ( preg_match('/^-|-$/', $s_name) > 0 )
					$s_error = 'User names should neither begin nor end with a dash [-].';
				else
					switch ( $s_name )
					{
					case 'www':
					case 'root':
					case 'admin':
						$s_error = "The user name [{$s_name}] is reserved.";
					break;
					}

		if ( $s_error )
		{
			$a_field['valid'] = false;
			$a_field['error'] = $s_error;
			$b_error_msg = true;
			return false;
		}
		return true;
	}

	function valNewPass(&$b_error_msg, &$a_field_1, &$a_field_2, $s_pass_1, $s_pass_2)
	{
		$s_error = '';

		if ( strlen($s_pass_1) < 6 || strlen($s_pass_1) > 30 )
			$s_error = 'Passwords must have between 6 and 30 characters.';
		else
			if ( $s_pass_1 != $s_pass_2 )
				$s_error = 'The two passwords you entered do not match.';

		if ( $s_error )
		{
			$a_field_1['valid'] = false;
			$a_field_2['valid'] = false;
			$a_field_1['value'] = '';
			$a_field_2['value'] = '';
			$a_field_1['error'] = $s_error;
			$b_error_msg = true;
			return false;
		}
		return true;
	}
}

class CEmail
{
	function sendEmail($s_purpose, $s_sender_id, $s_recepient_id, $email_override, $s_subject, $s_message)
	{
		if ( ! $email_override ) $email_override = '-';
		$s_header = "From: \"Film Aficionado\" <filmaf-noreply@filmaf.com>\r\n";
		CSql::query_and_free("INSERT INTO email (sender_id, recepient_id, email_override, header, subject, message, purpose, created_dt) ".
							 "VALUES ('{$s_sender_id}', '{$s_recepient_id}', '{$email_override}', '{$s_header}', '{$s_subject}', '{$s_message}', '{$s_purpose}', now())", 0,__FILE__,__LINE__);
		$a_cmdout = array();
		$n_cmdret = 0;
		dvdaf3_exec("/var/www/html/shell/send-mail", $a_cmdout, $n_cmdret);
	}

	function getNameAndId($s_id)
	{
		$s_name = CSql::query_and_fetch1("SELECT name FROM dvdaf_user_3 WHERE user_id = '{$s_id}'", 0,__FILE__,__LINE__);
		$s_id   = ucfirst($s_id);
		return ($s_name && $s_name != '-') ? "{$s_name} ({$s_id})" : $s_id;
	}

	function getEmail($s_id)
	{
		return CSql::query_and_fetch1("SELECT email FROM dvdaf_user_2 WHERE user_id = '{$s_id}'", 0,__FILE__,__LINE__);
	}

	function sendValidation($s_user, $s_email, $s_code)
	{
		$uc_user   = ucfirst($s_user);
		$s_subject = 'Welcome to Film Aficionado';
		$s_message = "Dear {$uc_user},\n\n".
					 "Welcome to the Film Aficionado community!\n\n".
					 "Please follow this link in order to confirm your email address and validate your free membership.\n\n".
					 "{$this->ms_base_subdomain}/utils/validate-email.html?id={$s_user}&cd={$s_code}\n\n".
					 "Thank you for your support!\n\n".
					 "- The Film Aficionado Team.";
		CEmail::sendEmail('registration email validation', $s_user, $s_user, $s_email, $s_subject, $s_message);
	}

	function sendValidationEmailChange($s_user, $s_email, $s_code)
	{
		$uc_user   = CEmail::getNameAndId($s_user);
		$s_subject = 'Film Aficionado Email Verification';
		$s_message = "Dear {$uc_user},\n\n".
					 "Please follow this link in order to confirm your new email address.\n\n".
					 "{$this->ms_base_subdomain}/utils/validate-email.html?id={$s_user}&cd={$s_code}\n\n".
					 "Thank you for your support!\n\n".
					 "- The Film Aficionado Team.";
		CEmail::sendEmail('email change validation', $s_user, $s_user, $s_email, $s_subject, $s_message);
	}

	//function sendPassword($s_user, $s_email, $s_password)
	function sendPassword($s_user, $s_password)
	{
		$s_url     = "http://{$s_user}.filmaf.com/";
		$uc_user   = CEmail::getNameAndId($s_user);
		$s_subject = 'Your New Film Aficionado Password';
		$s_message = "Dear {$uc_user},\n\n".
					 "Your new Film Aficionado password is {$s_password}.\n\n".
					 "Thank you for your support!\n\n".
					 "- The Film Aficionado Team.\n{$s_url}";
		CEmail::sendEmail('password reset', $s_user, $s_user, '', $s_subject, $s_message);
	}

	function notifyAccepted($s_accepting, $s_accepted)
	{
		$s_url        = "http://{$s_accepted}.filmaf.com/";
		$uc_accepted  = CEmail::getNameAndId($s_accepted);
		$uc_accepting = CEmail::getNameAndId($s_accepting);
		$s_subject    = 'Film Aficionado Friend Invitation Accepted';
		$s_message    = "Congrats {$uc_accepted}!\n\n".
						"{$uc_accepting} accepted your invitation and was added to your Film Aficionado network of friends.\n\n".
						"Thank you for your support!\n\n".
						"- The Film Aficionado Team.\n{$s_url}";
		CEmail::sendEmail('friend invitation accepted', $s_accepting, $s_accepted, '', $s_subject, $s_message);
	}

	function notifyRejected($s_rejecting, $s_rejected)
	{
		$s_url        = "http://{$s_rejected}.filmaf.com/";
		$uc_rejected  = CEmail::getNameAndId($s_rejected);
		$uc_rejecting = CEmail::getNameAndId($s_rejecting);
		$s_subject    = "Film Aficionado Friend Invitation Response from {$uc_rejecting}";
		$s_message    = "Dear {$uc_rejected},\n\n".
						"{$uc_rejecting} has not accepted your invitation to be added to your network of friends at Film Aficionado.\n\n".
						"If you believe this to be a mistake we suggest that you contact {$uc_rejecting} directly about it. ".
						"{$uc_rejecting} can still accept this invitation by visiting his/her friend page. There is not need to send another request.\n\n".
						"Thank you for your support!\n\n".
						"- The Film Aficionado Team.\n{$s_url}";
		CEmail::sendEmail('friend invitation rejected', $s_rejecting, $s_rejected, '', $s_subject, $s_message);
	}

	function notifyDivorce($s_rejecting, $s_rejected)
	{
		$s_url        = "http://{$s_rejected}.filmaf.com/";
		$uc_rejected  = CEmail::getNameAndId($s_rejected);
		$uc_rejecting = CEmail::getNameAndId($s_rejecting);
		$s_subject    = "Film Aficionado Friend Update from {$uc_rejecting}";
		$s_message    = "Dear {$uc_rejected},\n\n".
						"{$uc_rejecting} has request a no-fault divorce and the two of you no longer share the same Film Aficionado network.\n\n".
						"If you believe this to be a mistake we suggest that you contact {$uc_rejecting} directly about it. ".
						"Thank you!\n\n".
						"- The Film Aficionado Team.\n{$s_url}";
		CEmail::sendEmail('friend divorce', $s_rejecting, $s_rejected, '', $s_subject, $s_message);
	}

	function notifyInvite($s_invitee, $s_invited, $s_invitation, $b_repeat)
	{
		$uc_invitee   = CEmail::getNameAndId($s_invitee);
		$uc_invited   = CEmail::getNameAndId($s_invited);
		$s_url        = "http://{$s_invitee}.filmaf.com/";
		$s_subject    = "Film Aficionado Friend Invitation from {$uc_invitee}";
		$s_message    = "Dear {$uc_invited},\n\n".
						"{$uc_invitee} ($s_url) would like to add you to his/her list of friends at Film Aficionado. In order to accept or decline ".
						"this invitation please visit your FilmAf homepage at http://{$s_invited}.filmaf.com/\n\n".
						"Here is what {$uc_invitee} says:\n\n".
						"{$s_invitation}\n\n".
						"-----\n\n".
						"Film Aficionado did not write and is not responsible for the contents of this message. If you find it to be inappropriate ".
						"please report it at http://dvdaf.net/\n";
		CEmail::sendEmail('friend invitation', $s_invitee, $s_invited, '', $s_subject, $s_message);
	}
/*
	function notifyInviteEmail($s_invitee, $e_mail, $s_invitation, $b_repeat)
	{
		$uc_invitee			= CEmail::getNameAndId($s_invitee);
		$uc_invitee_email	= CEmail::getEmail($s_invitee);

		$s_url        = "http://{$s_invitee}.filmaf.com/";
		$s_subject    = "Film Aficionado Invitation by {$uc_invitee}";
		$s_message    = "Dear {$},\n\n".
						"{$uc_invitee} {$uc_invitee_email} is sending you an invitation to join Film Aficionado. In order to accept or decline ".
						"this invitation please visit your FilmAf homepage at http://{$s_invited}.filmaf.com/\n\n".
						"Here is what {$uc_invitee} says:\n\n".
						"{$s_invitation}\n\n".
						"-----\n\n".
						"Film Aficionado did not write and is not responsible for the contents of this message. If you find it to be inappropriate ".
						"please report it at http://dvdaf.net/\n";
		CEmail::sendEmail('friend invitation', $s_invitee, $s_invited, '', $s_subject, $s_message);
	}
*/
}

?>
