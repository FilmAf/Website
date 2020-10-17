<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

class CSearchComp extends CAjax
{
    // ?what=dir&parm=hil&target=2
    // ?what=dvd&parm=loe&target=0
    // ?what=pub&parm=bar&target=1
    function getSql()
    {
	$this->ms_what    = dvdaf3_getvalue('what'  ,DVDAF3_GET|DVDAF3_LOWER);
	$this->ms_parm    = dvdaf3_getvalue('parm'  ,DVDAF3_GET|DVDAF3_LOWER);
	$this->ms_target  = dvdaf3_getvalue('target',DVDAF3_GET|DVDAF3_LOWER);
	$this->ms_context = ( $this->ms_what   != '' ? "what='{$this->ms_what}' "     : '').
			    ( $this->ms_parm   != '' ? "parm='{$this->ms_parm}' "     : '').
			    ( $this->ms_target != '' ? "target='{$this->ms_target}' " : '');

	switch ( $this->ms_what )
	{
	case 'dvd':
	    if ( strlen($this->ms_parm) < 2 ) return $this->on_error("Please enter at least 2 characters to perform a title search.");
	    $this->ms_sql = "SELECT name FROM search_dvd WHERE nocase like '% {$this->ms_parm}%' ORDER BY IF(INSTR(nocase,' {$this->ms_parm}')<=2,1,2), nocase";
	    $this->mn_max = 500;
	    break;
	case 'dir':
	    if ( strlen($this->ms_parm) < 2 ) return $this->on_error("Please enter at least 2 characters to perform a director search.");
	    $this->ms_sql = "SELECT name FROM search_director WHERE nocase like '% {$this->ms_parm}%' ORDER BY IF(INSTR(nocase,' {$this->ms_parm}')<=2,1,2), nocase";
	    $this->mn_max = 500;
	    break;
	case 'pub':
	    if ( strlen($this->ms_parm) < 2 ) return $this->on_error("Please enter at least 2 characters to perform a publisher search.");
	    $this->ms_sql = "SELECT name FROM search_publisher WHERE nocase like '% {$this->ms_parm}%' ORDER BY IF(INSTR(nocase,' {$this->ms_parm}')<=2,1,2), nocase";
	    $this->mn_max = 500;
	    break;
	case 'person':
	    if ( strlen($this->ms_parm) < 2 ) return $this->on_error("Please enter at least 2 characters to perform a person search.");
	    $this->ms_sql = "SELECT p.surname, p.given_name, p.surname_first_ind, p.person_id, d.descr country ".
						  "FROM person p ".
						  "LEFT JOIN decodes d ON p.country_birth = d.code_int and d.domain_type = 'country_birth' ".
						 "WHERE p.name_nocase like '% {$this->ms_parm}%' ORDER BY IF(INSTR(p.name_nocase,' {$this->ms_parm}')<=2,1,2), p.name_nocase";
	    $this->mn_max = 500;
	    break;

	default:
	    return $this->on_error("Unrecognized request: ".__LINE__);
	}

	return true;
    }
}

$a = new CSearchComp();
$a->main();

?>
