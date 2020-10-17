<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CPerson.php';
require $gs_root.'/lib/CHistObj.php';

class CHistPerson extends CHistObj
{
	function constructor()
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$this->ms_title				= 'Person History';
		$this->mo_obj				= new CObjPerson();
	}
}

?>
