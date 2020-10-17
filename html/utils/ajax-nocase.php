<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

class CNoCase extends CAjax
{
    // ?dir=dfsdgsg
    function getSql()
    {
		$this->get_requester();
		$this->ms_dir     = dvdaf3_getvalue('dir', DVDAF3_GET);
		$this->ms_nocase  = dvdaf3_translatestring($this->ms_dir, DVDAF3_SEARCH);
		$this->ms_context = "dir='{$this->ms_dir}' ";

		$this->ms_sql	  = "SELECT '{$this->ms_nocase}' FROM one";
		$this->mn_max	  = 100;

		return true;
    }
}

$a = new CNoCase();
$a->main();

?>
