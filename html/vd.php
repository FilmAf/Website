<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/lib/CItemRedirect.php';

class CVendorRedirect extends CItemRedirect
{
	function __construct()
	{
	    $s_vendor = dvdaf3_getvalue('vd',DVDAF3_GET|DVDAF3_LOWER);

		$this->gotoVendor($s_vendor);
	}
}

new	CVendorRedirect();

?>
