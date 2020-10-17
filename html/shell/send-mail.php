<?php
// vi: noet ai ts=4 sw=4
// passing parameters such as the php.ini location via ! does not seem to work in this installation

require 'identify.php';
require $gs_root.'/utils/ajax.php';

function sendThem()
{
	//CSql::connect(__FILE__,__LINE__);
	$a_email_id   = array();
	$a_claim_seed = array();

	$rr = CSql::query("SELECT email_id, claim_seed FROM email WHERE sent_dt is null and (claim_dt is null or claim_dt < date_add(now(), INTERVAL -15 MINUTE))", 0,__FILE__,__LINE__);
	while ( $a_row = CSql::fetch($rr) )
	{
		$a_email_id[]   = intval($a_row['email_id']);
		$a_claim_seed[] = $a_row['claim_seed'];
	}
	CSql::free($rr);

	for ( $i = 0 ; $i < count($a_email_id) ; $i++ )
	{
		$n_email_id = $a_email_id[$i];
		$n_old_seed = $a_claim_seed[$i];
		$s_new_seed = date('Y-m-d H:i:s '). mt_rand(999999,1000000) . ' ' . mt_rand(999999,1000000);
		if ( CSql::query_and_free("UPDATE email SET claim_dt = now(), claim_seed = '{$s_new_seed}' WHERE email_id = {$n_email_id} and claim_seed = '{$n_old_seed}'", 0,__FILE__,__LINE__) == 1 )
		{
			$a_row = CSql::query_and_fetch("SELECT a.recepient_id, b.email, a.email_override, a.header, a.subject, a.message ".
							 "FROM email a ".
							 "LEFT JOIN dvdaf_user_2 b ON a.recepient_id = b.user_id ".
							"WHERE a.email_id = {$n_email_id}", 0,__FILE__,__LINE__);
			if ( $a_row )
			{
				$s_email = $a_row['email_override'];
				$s_msg   = str_replace('&#39;', "'", $a_row['message']);

				if ( $s_email == '' || $s_email == '-' )
					$s_email = $a_row['email'];

				if ( $s_email != '' && $s_email != '-' )
				{
					$s_subject = $a_row['subject'];
					$s_headers = "MIME-Version: 1.0\n".
								 "Content-type: text/plain; charset=iso-8859-1\n".
								 "From: Film Aficionado <noreply@filmaf.com>\n".
								 "Reply-To: Film Aficionado <noreply@filmaf.com>\n".
								 "Return-Path: noreply@filmaf.com\n".
								 "X-Mailer: PHP/".phpversion();

					mail($s_email, $s_subject, $s_msg, $s_headers, "-fnoreply@filmaf.com");

					CSql::query_and_free("UPDATE email SET sent_dt = now() WHERE email_id = {$n_email_id}", 0,__FILE__,__LINE__);
				}
				else
				{
					// Can not send it... mark it as sent so that it does not bug us later
					CSql::query_and_free("UPDATE email SET sent_dt = '2000-01-01' WHERE email_id = {$n_email_id}", 0,__FILE__,__LINE__);
				}
			}
		}
	}

}

sendThem();

?>
