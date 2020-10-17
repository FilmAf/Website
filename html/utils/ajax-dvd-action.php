<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';
require $gs_root.'/lib/CDvdColAct.php';

class CDvdAction extends CAjax
{
    // ?dvd=3252&action=del
    // ?dvd=3252&action=mov&folder=owned
    function getSql()
    {
		$this->get_requester();
		$this->mn_dvd     = dvdaf3_getvalue('dvd'   ,DVDAF3_GET|DVDAF3_INT  );
		$this->ms_action  = dvdaf3_getvalue('action',DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_folder  = dvdaf3_getvalue('folder',DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_context = "dvd='{$this->mn_dvd}' action='{$this->ms_action}' folder='{$this->ms_folder}' ";

		if ( $this->ms_requester == '' || $this->ms_requester == 'guest' )
			return $this->on_error("Not logged in.");

		if ( $this->mn_dvd <= 0 )
			return $this->on_error("Invalid dvd id.");

		$s_affected	= '';
		$s_error	= '';
		$this->mn_count = 0;

		switch ( $this->ms_action )
		{
		case 'del':
			CDvdColAct::deleteDvd($this->mn_dvd, $this->ms_requester, $s_affected, $s_error);
			break;
		case 'mov':
			switch ( $this->ms_folder )
			{
			case 'owned':
			case 'on-order':
			case 'wish-list':
			case 'work':
			case 'have-seen':
				CDvdColAct::moveDvd($this->mn_dvd, $this->ms_requester, $s_affected, $s_error, '', $this->ms_folder);
				break;
			default:
				return $this->on_error("Unrecognized folder.");
			}
			break;
		default:
			return $this->on_error("Unsuported action.");
		}

		if ( $s_error != '' )
			return $this->on_error($s_error);

		$this->mn_count = 1;
		$this->ms_msg	= str_replace('<br />','',$s_affected);
		$this->ms_ajax  = '';

		return true;
    }

    function runSql()
	{
		return true;
	}
}

$a = new CDvdAction();
$a->main();

?>
