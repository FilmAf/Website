<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

define('HOST_UNKNOWN'		 , 0);
define('HOST_FILMAF_COM'	 , 1);
define('HOST_FILMAF_EDU'	 , 2);

$gn_host = 'www.filmaf.com';

if ( isset($_SERVER['HTTP_HOST']) )
{
	$gn_host = strtolower($_SERVER['HTTP_HOST']);
}
else
{
	if ( isset($_SERVER['HOSTNAME']) )
	{
		$gn_host = strtolower($_SERVER['HOSTNAME']);
		$gn_host = 'www' . substr($gn_host, strpos($gn_host,'.'), 1000);
	}
}

$gn_host = str_replace('dvdaf.','filmaf.',$gn_host);

if ( strpos($gn_host,'filmaf.com') !== false ) { $gn_host = HOST_FILMAF_COM; $gs_root = '/var/www/html'; } else
if ( strpos($gn_host,'filmaf.edu') !== false ) { $gn_host = HOST_FILMAF_EDU; $gs_root = '/var/www/html'; } else
											   { $gn_host = HOST_UNKNOWN; exit("We are unable to process your request. Please try again in a few minutes."); }

date_default_timezone_set( $gn_host != HOST_FILMAF_COM ? 'America/New_York' : 'America/Chicago');

?>
