#!/usr/local/bin/php -c /etc/httpd/conf/php-cli.ini
<?php
// vi: noet ai ts=4 sw=4

function sendThem()
{
	$s_email   = 'edward.hoo@gmail.com';
	$s_subject = 'Test';
	$s_msg     = 'Test message';
	$s_headers = "MIME-Version: 1.0\n".
				 "Content-type: text/plain; charset=iso-8859-1\n".
				 "From: Film Aficionado <noreply@filmaf.com>\n".
				 "Reply-To: Film Aficionado <noreply@filmaf.com>\n".
				 "Return-Path: noreply@filmaf.com\n".
				 "X-Mailer: PHP/".phpversion();

	mail($s_email, $s_subject, $s_msg, $s_headers, "-fnoreply@filmaf.com");
}

sendThem();

?>
