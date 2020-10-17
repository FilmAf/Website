<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CSqlMysql.php';
require $gs_root.'/lib/CTrace.php';

class CAjax
{
    function CAjax()
    {
		header('Expires: Wed, 1 Jan 2012 05:00:00 GMT');                // date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');      // always modified
		header('Cache-Control: no-store, no-cache, must-revalidate');   // HTTP/1.1
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');                                     // HTTP/1.0

		$this->ms_sql       = '';
		$this->mn_max       = 1;
		$this->mb_over_max  = false;
		$this->ms_context   = '';
		$this->ms_requester = '';
		$this->mb_self      = false;
		$this->mb_assoc     = false;
		$this->ms_ajax      = '';
		$this->ms_msg       = '';
		$this->mn_count     = 0;
		$this->mn_conn      = false;
		$this->ms_mode      = dvdaf3_getvalue('mode',DVDAF3_GET|DVDAF3_LOWER);
    }
    function main()
    {
		if ( $this->getSql() )
		{
			if ( $this->runSql() )
			{
				$this->done();
				$this->respond();
			}
		}
    }
    function getSql()
    {
		return false;
    }
    function runSql()
    {
		if ( $this->ms_sql == '' )
			return true;

		if ( ($rr = CSql::query($this->ms_sql,0,__FILE__,__LINE__)) )
		{
			$this->mb_over_max = false;
			if ( $this->mb_assoc )
			{
				while ( ($row = @mysql_fetch_assoc($rr)) )
				{
					if ( ++$this->mn_count > $this->mn_max )
					{
						$this->ms_msg      = "stopping at {$this->mn_max} matches";
						$this->mn_count    = $this->mn_max;
						$this->mb_over_max = true;
						break;
					}
					$this->ms_ajax .= $this->formatLine($row);
				}
			}
			else
			{
				while ( ($row = @mysql_fetch_row($rr)) )
				{
					if ( ++$this->mn_count > $this->mn_max )
					{
						$this->ms_msg      = "stopping at {$this->mn_max} matches";
						$this->mn_count    = $this->mn_max;
						$this->mb_over_max = true;
						break;
					}
					$this->ms_ajax .= $this->formatLine($row);
				}
			}

			if ( ! $this->mb_over_max )
			{
				switch ( $this->mn_count )
				{
					case 0:  $this->ms_msg = "no matches found";				break;
					case 1:  $this->ms_msg = "1 match found";					break;
					default: $this->ms_msg = "{$this->mn_count} matches found"; break;
				}
			}
			CSql::free($rr);
			return true;
		}
		else
		{
			if ( $rr === 0 ) return true;
		}

		$this->on_error("Sorry, we could not run your query: ".__LINE__);
		return false;
    }
    function done()
    {
    }
    function respond()
    {
		echo  "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>".
			  "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>".
				"<head>".
				  "<title>404 Not Found</title>".
				  "<meta http-equiv='Content-type' content='text/html; charset=UTF-8' />".
				"</head>".
				"<body>".
				  "<h1>Not Found</h1>".
				  "<p>The requested URL /utils/ajax.php was not found on this server.</p>\n".
				  "<div style='visibility:hidden' status='SUCCESS' {$this->ms_context}count='{$this->mn_count}' msg='{$this->ms_msg}'>\n".
//				  "<div status='SUCCESS' {$this->ms_context}count='{$this->mn_count}' msg='{$this->ms_msg}'>\n".
					$this->ms_ajax.
				  "</div>".
				"</body>".
			  "</html>";
    }
    function formatLine(&$row)
    {
		for ( $i = 0, $ln = '' ; $i < count($row) ; $i++ ) $ln .= "{$row[$i]}\t";
			return substr($ln,0,-1). "\n";
    }
    function get_requester()
    {
		$s_user = dvdaf3_getvalue('user', DVDAF3_COOKIE);
		$s_parm = dvdaf3_getvalue('parm', DVDAF3_COOKIE);
		$s_md5p = substr($s_parm,0,32);
		$s_parm = substr($s_parm,32);
		$this->ms_requester = $s_md5p == md5($s_user . $s_parm . 'ZB;Xz0@_N]mYru%3') ? $s_user : 'guest';
    }
    function on_error($s_error)
    {
		echo "<div style='visibility:hidden' status='ERROR' {$this->ms_context}count='0' msg='{$s_error}'>\n</div>";
//		echo "<html><body>status='ERROR'<br />{$this->ms_context}count='0'<br />msg='{$s_error}'</body></html>";
		return false;
    }
    function log_debug($s_msg)
    {
		$s_user  = dvdaf3_getvalue('user'        ,DVDAF3_COOKIE); if ( ! $s_user  ) $s_user  = '-';
		$s_ip    = dvdaf3_getvalue('REMOTE_ADDR' ,DVDAF3_SERVER); if ( ! $s_ip    ) $s_ip    = '-';
		$s_host  = dvdaf3_getvalue('HTTP_HOST'   ,DVDAF3_SERVER); if ( ! $s_host  ) $s_host  = '-';
		$s_url   = dvdaf3_getvalue('SCRIPT_URL'  ,DVDAF3_SERVER); if ( ! $s_url   ) $s_url   = '-';
		$s_query = dvdaf3_getvalue('QUERY_STRING',DVDAF3_SERVER); if ( ! $s_query ) $s_query = '-';

		if ( $s_msg === false )
		{
			$ss = "DELETE FROM debug_msg WHERE user_id = '{$s_user}' and ip = '{$s_ip}'";
		}
		else
		{
			if ( $s_msg == 'URL' )
			{
				$s_msg = dvdaf3_getvalue('QUERY_STRING',DVDAF3_SERVER);
				$s_msg = dvdaf3_getvalue('SCRIPT_URI',DVDAF3_SERVER).($s_msg ? '?'.$s_msg : '');
			}
			$s_msg   = str_replace("'", "\'", $s_msg);
			$ss      = "INSERT INTO debug_msg (user_id, ip, host, url, query, msg, created_tm) ".
					   "VALUES ('{$s_user}', '{$s_ip}', '{$s_host}', '{$s_url}', '{$s_query}', '{$s_msg}', now())";
		}

		CSql::query_and_free($ss,0,__FILE__,__LINE__);
    }
    function valstr($s, $s_def)
    {
		return $s ? $s : $s_def;
    }
    function vala_b($s, $s_def, $s_opt1, $s_opt2)
    {
		return ($s == $s_opt1 || $s == $s_opt2) ? $s : $s_def;
    }
    function vala_3($s, $s_def, $s_opt1, $s_opt2, $s_opt3)
    {
		return ($s == $s_opt1 || $s == $s_opt2 || $s == $s_opt3) ? $s : $s_def;
    }
    function vala_4($s, $s_def, $s_opt1, $s_opt2, $s_opt3, $s_opt4)
    {
		return ($s == $s_opt1 || $s == $s_opt2 || $s == $s_opt3 || $s == $s_opt4) ? $s : $s_def;
    }
    function valdat($s)
    {
		return $s;
    }
    function valint($s)
    {
		return $s;
    }
}

class CUnrecognized extends CAjax
{
    function main()
    {
		$this->on_error("Unrecognized request: ".__LINE__);
    }
}

?>
