<?php

/* ----------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ----------------------------------------------------------------------- */

define('HOST_UNKNOWN'		 , 0);
define('HOST_FILMAF_COM'	 , 1);
define('HOST_FILMAF_EDU'	 , 2);

/*
 * With the change from dvdaf to filmaf the following $_SERVER variables
 * maybe inconsistent if the request is been rerouted from dvdaf.*
 *
 *  - HTTP_HOST
 *  - SCRIPT_URI
 *  - SERVER_NAME
 */

$gn_host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : 'www.filmaf.com';
$gn_host = str_replace('dvdaf.','filmaf.',$gn_host);

if ( strpos($gn_host,'filmaf.com') !== false ) { $gn_host = HOST_FILMAF_COM; $gs_root = '/var/www/html'; } else
if ( strpos($gn_host,'filmaf.edu') !== false ) { $gn_host = HOST_FILMAF_EDU; $gs_root = '/var/www/html'; } else
{
	$gn_host = HOST_UNKNOWN;
/*
	header('Expires: Wed, 1 Jan 2012 05:00:00 GMT');				// date in the past
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');		// always modified
	header('Cache-Control: no-store, no-cache, must-revalidate');	// HTTP/1.1
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');										// HTTP/1.0

	echo
	"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n".
	"<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>\n".
	"<head>".
	"</head>\n".
	"<body>".
	str_replace("\n","<br />\n",
	str_replace(">","&gt;",
	str_replace("<","&lt;",
	str_replace(" ","&nbsp;",
			print_r($_SERVER, true))))).
	"</body></html>";

	exit();
*/	
	exit("We are unable to process your request. Please try again in a few minutes.");
}

date_default_timezone_set( $gn_host != HOST_FILMAF_COM ? 'America/New_York' : 'America/Chicago');

?>
