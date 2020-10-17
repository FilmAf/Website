<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

class CExplain extends CAjax
{
    // ?s=keyword
    function getSql()
    {
//	$this->log_debug(false);
//	$this->log_debug('URL');
	$this->ms_keyword = dvdaf3_getvalue('s', DVDAF3_GET|DVDAF3_LOWER);
	$this->mb_assoc   = true;
	$this->ms_sql     = "SELECT keyword, width, descr FROM explain_keyword WHERE keyword = '{$this->ms_keyword}'";

	return true;
    }
    function formatLine(&$row)
    {
	if ( $row['keyword'] == '-' ) $row['keyword'] = '';
	if ( $row['descr'  ] == '-' ) $row['descr'  ] = '';

	return	  "keyword\t" .$row['keyword'].
		"\twidth\t"   .$row['width'].
		"\texplain\t" .$row['descr'].
		"\n";
    }
    function done()
    {
	if ( ! $this->mn_count )
	{
	    $row = array();
	    $row['keyword'] = $this->ms_keyword;
	    $row['width']   = 0;
	    $row['descr']   = 'Not found';
	    $this->ms_ajax .= $this->formatLine($row);
	    $this->mn_count++;
	}
    }
}

$a = new CExplain();
$a->main();

?>
