<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

class CImgPop extends CAjax
{
    // ?dvd=3252
    // ?dvd=3252&action=del
    // ?dvd=3252&action=mov&folder=owned
    function getSql()
    {
		$this->get_requester();
		$this->mn_dvd     = dvdaf3_getvalue('dvd'   ,DVDAF3_GET|DVDAF3_INT  );
		$this->ms_context = "dvd='{$this->mn_dvd}' ";

		$this->ms_sql = "SELECT a.dvd_id, a.pic_name, substr(b.folder,1,4) folder, IF(a.asin != '-',1,0), a.best_price price_00, floor((length(a.imdb_id)+1)/8+0.05), a.media_type, a.country, a.publisher, a.dvd_rel_dd ".
						  "FROM (SELECT y.dvd_id ".
								  "FROM search_all_1 x ".
								  "JOIN search_all_1 y ON x.nocase = y.nocase and y.obj_type = 'I' ".
								  "JOIN dvd a ON a.dvd_id = y.dvd_id ".
								 "WHERE x.dvd_id = {$this->mn_dvd} and x.obj_type = 'I' ".
								 "UNION ".
								"SELECT {$this->mn_dvd}) y ".
						  "JOIN dvd a ON a.dvd_id = y.dvd_id ".
						  "LEFT JOIN v_my_dvd_ref b ON a.dvd_id = b.dvd_id and b.user_id = '{$this->ms_requester}' ".
//						  "LEFT JOIN price p ON a.upc = p.upc ".
// Uncomment the next line to limit to editions in the collection only + original
//						 "WHERE b.folder is not NULL or a.dvd_id = {$this->mn_dvd} ".
						 "ORDER BY IF(a.dvd_id = {$this->mn_dvd},1,2), IFNULL(b.folder,'z'), a.dvd_id ".
						 "LIMIT 100";

//		$this->ms_context .= "sql ='{$this->ms_sql}' ";

		$this->mn_max = 100;

		return true;
    }
}

$a = new CImgPop();
$a->main();

?>
