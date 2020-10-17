<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require_once $gs_root.'/lib/CSqlReplica.php';

define('CSql_IGNORE_ERROR'	,     1);

//////////////////////////////////////////////////////////////////////////

// CSql global variables
$gn_sql_time		= 0;
$gn_sql_begin		= 0;
$gn_sql_error		= 0;

$gn_sql_connection_log	= 0;
$gn_sql_log_threshold	= 10;	// time in miliseconds that warrants logging

//////////////////////////////////////////////////////////////////////////

class CSql
{
	function connect($file, $line)
	{
	}

    function log_long($s_sql, $n_time)
    {	global $gb_trace_sql, $gs_sql_password, $gn_sql_connection_log, $gs_sql_host;

		if ( true ) return;

		if ( $gn_sql_connection_log == 0 )
			//$gn_sql_connection_log = @mysql_pconnect($gs_sql_host, 'dvdaf', $gs_sql_password);
			$gn_sql_connection_log = @mysql_pconnect("localhost", "dvdaf", "dvdaf");

		if ( $gn_sql_connection_log !== false )
		{
			$s_user  = dvdaf3_getvalue('user'        ,DVDAF3_COOKIE); if ( ! $s_user  ) $s_user  = '-';
			$s_ip    = dvdaf3_getvalue('REMOTE_ADDR' ,DVDAF3_SERVER); if ( ! $s_ip    ) $s_ip    = '-';
			$s_orig  = dvdaf3_getvalue('orig'        ,DVDAF3_COOKIE); if (   $s_orig  ) $s_orig = explode('|', $s_orig); $s_orig = isset($s_orig[1]) ? $s_orig[1] : '-'; if ( ! $s_orig  ) $s_orig  = '-';
			$s_host  = dvdaf3_getvalue('HTTP_HOST'   ,DVDAF3_SERVER); if ( ! $s_host  ) $s_host  = '-';
			$s_url   = dvdaf3_getvalue('SCRIPT_URL'  ,DVDAF3_SERVER); if ( ! $s_url   ) $s_url   = '-';
			$s_query = dvdaf3_getvalue('QUERY_STRING',DVDAF3_SERVER); if ( ! $s_query ) $s_query = '-';
			$s_sql   = str_replace("'", "\'", $s_sql);
			$ss      = "INSERT INTO long_sql (user_id, ip, orig, run_time, host, url, query, run_sql, created_tm) ".
					   "VALUES ('{$s_user}', '{$s_ip}', '{$s_orig}', {$n_time}, '{$s_host}', '{$s_url}', '{$s_query}', '{$s_sql}', now())";
			$rr      = @mysql_query($ss, $gn_sql_connection_log);
			@mysql_free_result($rr);
			if ( $gb_trace_sql ) CTrace::log_var('CSql::query', $ss);
		}
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
			$ss      = "DELETE FROM debug_msg WHERE user_id = '{$s_user}' and ip = '{$s_ip}'";
		}
		else
		{
			$s_msg   = str_replace("'", "_", $s_msg);
			$ss      = "INSERT INTO debug_msg (user_id, ip, host, url, query, msg, created_tm) ".
					   "VALUES ('{$s_user}', '{$s_ip}', '{$s_host}', '{$s_url}', '{$s_query}', '{$s_msg}', now())";
		}

		CSql::query_and_free($ss,0,__FILE__,__LINE__);
    }

    function query($ss, $n_parm, $file, $line)
    {   global $gn_sql_connection, $gn_sql_begin, $gn_sql_time, $gb_trace_sql, $gn_sql_error, $gn_sql_log_threshold;

		$gn_sql_error = 0;

		if ( $gn_sql_connection !== false )
		{
			$gn_sql_begin = CTime::get_time();
			if ( CSqlReplica::checkConnection($ss,$file,$line) ) CSqlReplica::connect($file,$line);
//echo "\n\n<br />[$ss]<br />\n\n";
			$rr = @mysql_query($ss, $gn_sql_connection);
			$n_sql_time   = (CTime::get_time() - $gn_sql_begin) * 1000;
			$gn_sql_time += $n_sql_time;
			if ( $rr )
			{
				if ( $n_sql_time >= $gn_sql_log_threshold ) CSql::log_long($ss, $n_sql_time);
				if ( $gb_trace_sql ) CTrace::log_var('CSql::query', $ss);
				return $rr;
			}
			CSql::log_error('SQL Syntax', $ss, $n_parm, $file, $line);
		}
		return false;
    }

    function rows_matched()
    {	global $gn_sql_connection, $gb_trace_sql;

		$n_matched  = 0;

		// 'Rows matched: 1 Changed: 0 Warnings: 0'
		$ss = mysql_info($gn_sql_connection);
		if ( $gb_trace_sql ) CTrace::log_var('CSql::query info', $ss);

		$p1 = stripos($ss, 'matched:');
		if ( $p1 )
		{
			$p2 = strpos($ss, ' ', $p1 + 9);
			return intval(substr($ss, $p1 + 8, $p2 ? $p2 - $p1 - 8 : 10));
		}
    }

    function query_and_free($ss, $n_parm, $file, $line)
    {	global $gn_sql_connection, $gb_trace_sql;

		$rr = CSql::query($ss, $n_parm, $file, $line);
		if ( $rr )
		{
			$n_rows = @mysql_affected_rows($gn_sql_connection);
			if ( $gb_trace_sql ) CTrace::log_var('CSql::query affected_rows', $n_rows);
			@mysql_free_result($rr);
			return $n_rows;
		}
		return false;
    }

    function query_and_fetch($ss, $n_parm, $file, $line)
    {   global $gn_sql_connection, $gn_sql_begin, $gn_sql_time, $gb_trace_sql, $gn_sql_error, $gn_sql_log_threshold;

		$gn_sql_error = 0;

		if ( $gn_sql_connection !== false )
		{
			$gn_sql_begin = CTime::get_time();
			if ( CSqlReplica::checkConnection($ss,$file,$line) ) CSqlReplica::connect($file,$line);
			$rr = @mysql_query($ss, $gn_sql_connection);
			$n_sql_time   = (CTime::get_time() - $gn_sql_begin) * 1000;
			$gn_sql_time += $n_sql_time;
			if ( $rr )
			{
				if ( $n_sql_time >= $gn_sql_log_threshold ) CSql::log_long($ss, $n_sql_time);
				if ( $gb_trace_sql ) CTrace::log_var('CSql::query', $ss);
				$rt = @mysql_fetch_assoc($rr);
				@mysql_free_result($rr);
				return $rt;
			}
			CSql::log_error('SQL Syntax', $ss, $n_parm, $file, $line);
		}
		return null;
    }

    function query_and_fetch1($ss, $n_parm, $file, $line)
    {   global $gn_sql_connection, $gn_sql_begin, $gn_sql_time, $gb_trace_sql, $gn_sql_error, $gn_sql_log_threshold;

		$gn_sql_error = 0;

		if ( $gn_sql_connection !== false )
		{
			$gn_sql_begin = CTime::get_time();
			if ( CSqlReplica::checkConnection($ss,$file,$line) ) CSqlReplica::connect($file,$line);
			$rr = @mysql_query($ss, $gn_sql_connection);
			$n_sql_time   = (CTime::get_time() - $gn_sql_begin) * 1000;
			$gn_sql_time += $n_sql_time;
			if ( $rr )
			{
				if ( $n_sql_time >= $gn_sql_log_threshold ) CSql::log_long($ss, $n_sql_time);
				if ( $gb_trace_sql ) CTrace::log_var('CSql::query', $ss);
				$rt = @mysql_fetch_row($rr);
				@mysql_free_result($rr);
				if ( $rt && count($rt) >= 1 ) return $rt[0];
				return null;
			}
			CSql::log_error('SQL Syntax', $ss, $n_parm, $file, $line);
		}
		return null;
    }

    function last_insert_id()
    {	global $gn_sql_connection;

		return @mysql_insert_id($gn_sql_connection);
    }

    function num_rows($rr)
    {
		if ( $rr ) return @mysql_num_rows($rr);
		return 0;
    }

    function seek($rr,$pos)
    {
		if ( $rr )
		{
			$rt = @mysql_data_seek($rr,$pos);
			if ( ! $rt ) CSql::log_error("seek($pos)", '', 0, $file, $line);
			return $rt;
		}
		return false;
    }

    function fetch($rr)
    {	global $gn_sql_begin, $gn_sql_time;

		if ( $rr )
		{
			$gn_sql_begin = CTime::get_time();
			$rt = @mysql_fetch_assoc($rr);
			$gn_sql_time += (CTime::get_time() - $gn_sql_begin) * 1000;
			return $rt;
		}
		return false;
    }
    function free($rr)
    {
		if ( $rr ) return @mysql_free_result($rr);
		return false;
    }
    function get_error()
    {	global $gn_sql_error;

		return $gn_sql_error;
    }

    function log_error($topic, $ss, $n_parm, $file, $line)
    {   global $gn_sql_error;

		if ( $n_parm & CSql_IGNORE_ERROR )
		{
			$gn_sql_error = @mysql_errno();
		}
		else
		{
			$n_error = @mysql_errno();
			$s_error = @mysql_error();
//echo "<br />*** ERROR $topic *** in $file, line $line<br />ERROR $n_error: $s_error<br />$ss<br />&nbsp;<br />";
			CTrace::log_txt("*** ERROR $topic *** in $file, line $line<br />ERROR $n_error: $s_error<br />$ss");
			CTrace::logError($file, $line,			      $topic, $n_error, $s_error, $ss);
//			CTrace::logError(null, CTrace_SQL, "$file on line $line", $topic, $n_error, $s_error, $ss);
					
			$gn_sql_error = $n_error;
		}
    }

    function getFolders(&$a_folders, $s_view_id, $s_user_id)
    {
		$ss		 = ($s_user_id == $s_view_id) ? 'my_folder' : 'v_my_folder_pub';
		$ss		 = "SELECT folder FROM $ss WHERE user_id = '$s_view_id' ORDER BY sort_category, sort_order, folder";
		$a_folders	 = array();
		$an_last_root	 = array();
		$rr		 = CSql::query($ss, 0,__FILE__,__LINE__);
		$an_last_root[0] = -1;

		if ( $rr )
		{
			$rt = @mysql_fetch_assoc($rr);
			for ( $i = 0 ; $rt ; $i++ )
			{
				$s_folder = $rt['folder'];
				$k	  = 1;
				$n_last	  = 0;
				$n_pos    = strpos($s_folder, '/');

				while ( $n_pos > 0 )
				{
					$k++;
					$n_last = ++$n_pos;
					$n_pos  = strpos($s_folder, '/', $n_pos);
					$an_last_root[$k]	= isset($an_last_root[$k - 1]) ? $an_last_root[$k - 1] : ''; // isset added due to error log entries
				}

				$a_folders[$i]['level'] = $k;
				$a_folders[$i]['name']  = substr($s_folder, $n_last);
				$a_folders[$i]['full']  = $s_folder;
				$a_folders[$i]['curr']  = isset($an_last_root[$k - 1]) ? $an_last_root[$k - 1] : ''; // isset added due to error log entries
				$an_last_root[$k]	= $i;
				/*
					n >= 0	= position 'n' is the root to this folder
					-1	= the root to this is the collection
					-2	= later used to identify a breadcrumb to the current folder
					-3	= later used to identify the current folder
				*/
				$rt = mysql_fetch_assoc($rr);
			}
			mysql_free_result($rr);
		}
    }

    function limitRows($s_sql, $n_begin, $n_count)
    {
		$n_begin = $n_begin <= 1 ? 0 : $n_begin - 1;

		if ( $n_count > 0 )
		{
			if ( $n_begin > 0 )
				return "$s_sql LIMIT $n_begin,$n_count";
			else
				return "$s_sql LIMIT $n_count";
		}
		else
		{
			if ( $n_begin > 0 )
				return "$s_sql LIMIT $n_begin,10000";
			else
				return $s_sql;
		}
    }
}

?>
