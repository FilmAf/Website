<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';
require $gs_root.'/lib/CSecure.php';

class CSeed extends CAjax
{
    function isHuman()
    {
		$n_int   = dvdaf3_getvalue('int',DVDAF3_GET);
		$n_ext   = dvdaf3_getvalue('ext',DVDAF3_GET|DVDAF3_INT);
		$b_human = CSecure::validateJpg($n_ext, $n_int);
		if ( ! $b_human )
		{
			// humans will be reverified prior to the actual action.
			$ss = "DELETE FROM human_check WHERE external_id = {$n_ext}";
			CSql::query_and_free($ss, 0,__FILE__,__LINE__);
		}
		return $b_human;
    }
    function getSeed()
    {
		return CSecure::randJpg();
    }
    function getSql()
    {

		$this->ms_what    = dvdaf3_getvalue('what'  ,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_context = ( $this->ms_what != '' ? "what='{$this->ms_what}' " : '');
		$this->mb_assoc   = true;

		switch ( $this->ms_what )
		{
			case 'check':
				if ( $this->isHuman() )
				{
					$this->ms_sql = "SELECT 'good' status";
					return true;
				}
				// let it fall

			case 'get':
				$this->ms_sql = "SELECT '" . $this->getSeed() . "' seed";
				return true;
		}
		return false;
	}
    function formatLine(&$row)
    {
		return isset($row['seed']) ? "seed\t{$row['seed']}\n"
								   : "status\t{$row['status']}\n";
    }
}

$a = new CSeed();
$a->main();

?>
