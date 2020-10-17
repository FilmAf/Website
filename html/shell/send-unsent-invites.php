#!/usr/local/bin/php -c /etc/httpd/conf/php-cli.ini
<?php
// vi: noet ai ts=4 sw=4

require 'identify.php';
require $gs_root.'/utils/ajax.php';
require $gs_root.'/lib/CSecure.php';

function sendInvite($s_invitee, $s_invited, $s_invitation, $b_repeat)
{
	CSql::query_and_free("UPDATE friend_request SET last_sent_dt = now() WHERE invitee_id = '{$s_invitee}' and invited_id = '{$s_invited}'", 0,__FILE__,__LINE__);
	CEmail::notifyInvite($s_invitee, $s_invited, $s_invitation, $b_repeat);
}

function notifyAcceptedDelayed($s_accepting, $s_accepted)
{
	$uc_accepted  = CEmail::getNameAndId($s_accepted);
	$uc_accepting = CEmail::getNameAndId($s_accepting);
	$s_url        = "http://{$s_accepted}.filmaf.com/";
	echo "$uc_accepted, $uc_accepting\n";
	$s_subject    = 'Film Aficionado Friend Invitation Accepted';
	$s_message    = "Dear {$uc_accepted},\n\n".
					"We had a system glitch where a few emails were not sent. We would like for you to know that ".
					"you have a new friend connection at Film Aficionado, but we do not know who did the inviting and who ".
					"did the accepting. So... we are sending this message to both of you. Sorry.\n\n".
					"Congrats {$uc_accepting} and {$uc_accepted}!\n\n".
					"Thanks.\n\n".
					"- The Film Aficionado Team.\n{$s_url}";
	CEmail::sendEmail($s_accepted, false, $s_subject, $s_message);
}
 
//CSql::connect(__FILE__,__LINE__);

switch ( 3 )
{
case 1:
//  $rr = CSql::query("SELECT invitee_id, invited_id, invite FROM friend_request WHERE last_sent_dt is null", 0,__FILE__,__LINE__);
//  $rr = CSql::query("SELECT invitee_id, invited_id, invite FROM friend_request WHERE last_sent_dt > '2008-08-09 1:00:00' and rejected_ind <> 'Y'", 0,__FILE__,__LINE__);
	while ( $a_row = CSql::fetch($rr) )
	{
	$s_invitee = $a_row['invitee_id'];
	$s_invited = $a_row['invited_id'];
	$s_invite  = $a_row['invite'];

	echo "$s_invitee, $s_invited, $s_invite\n";
	sendInvite($s_invitee, $s_invited, $s_invite, false);
	}
	break;
case 2:
	notifyAcceptedDelayed('beesonosu', 'ash');
	break;
case 3:
	sendInvite('ash', 'ash', 'test', false);
	break;
}

?>
