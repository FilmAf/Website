<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/lib/CItemRedirect.php';

class CDvdRedirect extends CItemRedirect
{
	function __construct()
	{
		$s_vendor   = dvdaf3_getvalue('vd',DVDAF3_GET|DVDAF3_LOWER);
		$s_overwrt  = dvdaf3_getvalue('ov',DVDAF3_GET);

		if ( $s_overwrt )
		{
			switch( $s_vendor )
			{
			case 'imd': $this->gotoImdb_($n_dvd_id, $s_overwrt); break;
			default:    $this->gotoAmazon_($n_dvd_id, substr($s_vendor,3), $s_overwrt); break;
			}
		}
		else
		{
			$n_index  = explode('-', substr($s_vendor,3));
			$n_dvd_id = intval($n_index[0]);
			$n_index  = isset($n_index[1]) ? intval($n_index[1]) : 0;
			$s_vendor = substr($s_vendor,0,3);

			switch( $s_vendor )
			{
			case 'imd': $this->gotoImdb($n_dvd_id, $n_index); break;
			case 'amz':
			default:	$this->gotoAmazon($n_dvd_id); break;
			}
		}
	}
}

new	CDvdRedirect();

?>
