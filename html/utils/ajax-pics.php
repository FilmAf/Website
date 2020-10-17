<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

class CListPics extends CAjax
{
    // ?dvd=000000
    function getSql()
    {
		$this->mn_dvd_id = dvdaf3_getvalue('dvd' , DVDAF3_GET|DVDAF3_INT);
		$this->mb_assoc  = true;
		$this->ms_sql    = "SELECT a.pic_name FROM dvd_pic b LEFT JOIN pic a ON a.pic_id = b.pic_id WHERE b.dvd_id = {$this->mn_dvd_id}";
		$this->mn_max    = 1000;

		return true;
    }
    function formatLine(&$row)
    {
		return ($this->mn_count <= 1 ? "dvd_id\t{$this->mn_dvd_id}\timages\t" : ',') . $row['pic_name'];
    }
    function done()
    {
		if ( $this->ms_ajax )
		{
			$this->ms_ajax .= "\n";
		}
    }
}

class CSelectCollectionPic extends CAjax
{
    // ?dvd=000000&pic=048408-d0
    // ?dvd=000000&pic=0 -> default
    function getSql()
    {
		$this->get_requester();

		$this->mn_dvd_id = dvdaf3_getvalue('dvd' , DVDAF3_GET|DVDAF3_INT);
		$this->mn_pic    = dvdaf3_getvalue('pic' , DVDAF3_GET|DVDAF3_LOWER);
		$this->mb_self   = $this->ms_requester != '' && $this->ms_requester != 'guest';
		$this->mb_assoc  = true;

//$this->log_debug("");
		if ( $this->mb_self )
		{
			if ( $this->mn_pic )
				$ss = "UPDATE my_dvd ".
						 "SET pic_overwrite = (SELECT a.pic_name FROM dvd_pic b LEFT JOIN pic a ON a.pic_id = b.pic_id WHERE b.dvd_id = {$this->mn_dvd_id} and a.pic_name = '{$this->mn_pic}') ".
					   "WHERE user_id = '{$this->ms_requester}' ".
						 "and dvd_id = {$this->mn_dvd_id} ".
						 "and exists (SELECT * FROM dvd_pic b LEFT JOIN pic a ON a.pic_id = b.pic_id WHERE b.dvd_id = {$this->mn_dvd_id} and a.pic_name = '{$this->mn_pic}')";
			else
				$ss = "UPDATE my_dvd ".
						 "SET pic_overwrite = '-' ".
					   "WHERE user_id = '{$this->ms_requester}' ".
						 "and dvd_id = {$this->mn_dvd_id}";
//$this->log_debug("ss = {$ss}");
			CSql::query_and_free($ss,0,__FILE__,__LINE__);
		}

		$this->ms_sql = "SELECT a.pic_name, b.pic_overwrite ".
						  "FROM dvd a ".
						  "LEFT JOIN my_dvd b ON a.dvd_id = b.dvd_id and b.user_id = '{$this->ms_requester}' ".
						 "WHERE a.dvd_id = {$this->mn_dvd_id}";
//$this->log_debug("this->ms_sql = {$this->ms_sql}");
		return true;
    }
    function formatLine(&$row)
    {
		return "dvd_id\t{$this->mn_dvd_id}\tpic_name\t".
			   ($row['pic_overwrite'] && $row['pic_overwrite'] != '-' ? $row['pic_overwrite'] : $row['pic_name']).
			   "\n";
    }
}

switch ( dvdaf3_getvalue('what',DVDAF3_GET|DVDAF3_LOWER) )
{
case 'listpics':		 $a = new CListPics();			  break;
case 'selcollectionpic': $a = new CSelectCollectionPic(); break;
default:				 $a = new CUnrecognized();		  break;
}

$a->main();

?>
