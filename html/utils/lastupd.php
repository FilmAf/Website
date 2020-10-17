<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/lib/CSqlReplica.php';

	if ( !isset($gn_host) || $gn_host == HOST_FILMAF_COM ) $s_cookie_domain = '.filmaf.com'; else
	if (					 $gn_host == HOST_FILMAF_EDU ) $s_cookie_domain = '.filmaf.edu'; else
														   $s_cookie_domain = '.filmaf.com';

	setcookie('lastupd', time() - 1317000000, mktime(0,0,0,3,1,2030), '/', $s_cookie_domain, 0);

	header('Expires: Wed, 1 Jan 2007 05:00:00 GMT');				// date in the past
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');		// always modified
	header('Cache-Control: no-store, no-cache, must-revalidate');	// HTTP/1.1
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');										// HTTP/1.0

	echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'DTD/xhtml1-transitional.dtd'>\n".
		 "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>\n".
		 "<head></head>\n".
		 "<body>done.</body>\n";

?>
