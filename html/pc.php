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
		$s_vendor	= dvdaf3_getvalue('tg'  ,DVDAF3_GET);
		$s_upc		= dvdaf3_getvalue('upc' ,DVDAF3_GET);
		$n_dvd_id	= dvdaf3_getvalue('id'  ,DVDAF3_GET|DVDAF3_INT);
		$s_from		= dvdaf3_getvalue('qc'  ,DVDAF3_GET|DVDAF3_INT) > 0 ? 'Q' : 'C';

		$this->gotoDvd($s_vendor, $n_dvd_id, $s_upc, $s_from);
	}
}

new	CDvdRedirect();

?>
