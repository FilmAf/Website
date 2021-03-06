<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CRenDualMulti.php';

class CRenPublisher extends CRenDualMulti
{
    function constructor() // <<--------------------------------<< 1.0
    {
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$this->ms_field			= 'publisher';
		$this->ms_field_nocase	= 'publisher_nocase';
		$this->ms_field_uc		= 'Publisher';
		$this->ms_title			= 'Rename '+$this->ms_field_uc;
		parent::constructor();
	}
}

?>
