<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

//////////////////////////////////////////////////////////////////////////

// Audit global variables
$gf_start_clock		= CTime::get_time();	// when the servicing of the page begun (aprox.) this should be the first file loaded!
$gs_trace_msg		= '';			// string used to store the trace info to be output at the end
$gn_trace_count		= 0;
$gf_trace_time		= 0;
$gb_trace_sql		= false;
$gb_log_call		= false;

//////////////////////////////////////////////////////////////////////////

class CTime
{
	function get_time()
	{	// Number of elapsed seconds since Jan 1, 1970 as in '1007871334.8028'
		$s_mt = microtime();
		return ((float)strstr($s_mt, ' ') + (float)substr($s_mt, 0, strpos($s_mt,' ')));   
	}

	function get_microtime()
	{	// subsecond component of the number of elapsed seconds since Jan 1, 1970 as in '0.744936'
		$s_mt = microtime();
		return (float)substr($s_mt, 0, strpos($s_mt,' '));
	}
}

class CTrace
{
	function log_txt($s_str)
	{	global $gs_trace_msg, $gn_trace_count, $gf_trace_time, $gf_start_clock;

		$e_time		= CTime::get_time();
		$e_delta	= $gf_start_clock ? sprintf("%0.3f ms", ($e_time - ($gf_trace_time ? $gf_trace_time : $gf_start_clock)) * 1000) : '&nbsp;';
		$s_str		= CTrace::normalize_class_name($s_str);
		$gf_trace_time	= $e_time;
		$gs_trace_msg  .= "<tr><td>$gn_trace_count</td>".
						  "<td nowrap='nowrap'>$e_delta</td>".
						  "<td align='left'>". str_replace("\t","<span style='color:green'>[\\t]</span>",$s_str). "</td>".
						  "</tr>";
		$gn_trace_count++;
	}
	function normalize_class_name($s_str)
	{
		switch ( substr($s_str,0,4) )
		{
		case 'cwnd': return 'CWnd'. strtoupper($s_str{4}). substr($s_str,5);
		case 'cdvd': return 'CDvd'. strtoupper($s_str{4}). substr($s_str,5);
		}
		return $s_str;
	}
	function log_var($s_name, &$x_value)
	{
		switch ( gettype($x_value) )
		{
		case 'array':	CTrace::log_txt("$s_name = <br />".CTrace::dump_array($x_value,1)); break;
		case 'object':	CTrace::log_txt("<span style='color:red'>[</span>$s_name<span style='color:red'>]</span> is an object"); $x_value->dump(); break;
		default:		CTrace::log_txt("$s_name = <span style='color:red'>[</span>".str_replace("~tab~","\t",dvdaf3_translatestring(str_replace("\t","~tab~",$x_value)))."<span style='color:red'>]</span>"); break;
		}
	}
	function log_val($s_name, $x_value)
	{
		CTrace::log_txt("$s_name = <span style='color:red'>[</span>".
						str_replace("~tab~","\t",dvdaf3_translatestring(str_replace("\t","~tab~",$x_value))).
						"<span style='color:red'>]</span>");
	}
	function dump_array($a_value, $n_level)
	{
		reset($a_value);
		for ( $s_str = '' ; list($s_key, $x_value) = each($a_value) ;  )
		{
			for ( $i = 0 ; $i < $n_level ; $i++ )
			$s_str .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$s_str .= "<span style='color:green'>[</span>".dvdaf3_translatestring($s_key)."<span style='color:green'>]</span> = ";
			switch ( gettype($x_value) )
			{
			case 'array'    : $s_str .= "<br />".CTrace::dump_array($x_value, $n_level + 1); break; 
			case 'object'   : $x_value->dump(); break;
			default         : $s_str .= "<span style='color:red'>[</span>".
										str_replace("~tab~","\t",dvdaf3_translatestring(str_replace("\t","~tab~",$x_value))).
										"<span style='color:red'>]</span><br />"; break;
			}
		}
		reset($a_value);
		return $s_str;
	}
	function logError($s_file, $n_line, $s_topic, $n_error_number, $s_error_msg, $s_sql_query = '')
	{	global $gb_log_call;
		// do not user ' as they will get escaped when persisted to the db

		if ( $gb_log_call ) return; // avoid recursive loops
		$gb_log_call = true;

		$s_http_host		= dvdaf3_getvalue('HTTP_HOST'		, DVDAF3_SERVER);
		$s_http_user_agent	= dvdaf3_getvalue('HTTP_USER_AGENT'	, DVDAF3_SERVER);
		$s_remote_addr		= dvdaf3_getvalue('REMOTE_ADDR'		, DVDAF3_SERVER);
		$s_request_uri		= dvdaf3_getvalue('REQUEST_URI'		, DVDAF3_SERVER);
		$s_http_referer		= dvdaf3_getvalue('HTTP_REFERER'	, DVDAF3_SERVER);
		$n_request_time		= dvdaf3_getvalue('REQUEST_TIME'	, DVDAF3_SERVER|DVDAF3_INT);
		$n_line				= intval($n_line);
		$n_error_number		= intval($n_error_number);
		$s_created_on		= date('Y-m-d H:i:s');


		for ( reset($_POST  ), $s_post_variables = '' ; list($s_key, $s_val) = each($_POST  ) ; ) $s_post_variables .= "<div style=\"padding-left:20px\">".dvdaf3_translatestring("$s_key = [$s_val]")."</div>";
		for ( reset($_COOKIE), $s_cookies        = '' ; list($s_key, $s_val) = each($_COOKIE) ; ) $s_cookies        .= "<div style=\"padding-left:20px\">".dvdaf3_translatestring("$s_key = [$s_val]")."</div>";

		$ss = "<div>URL = [{$s_http_host}{$s_request_uri}]</div>".
			  "<div>file = [$s_file]</div>".
			  "<div>line = [$n_line]</div>".
			  "<div style=\"margin-top:12px\">topic = [$s_topic]</div>".
			  "<div>error_number = [$n_error_number]</div>".
			  "<div>error_msg = [$s_error_msg]</div>".
			  "<div>sql_query = [$s_sql_query]</div>".
			  "<div style=\"margin-top:12px\">cookies = [$s_cookies]</div>".
			  "<div>post_variables = [$s_post_variables]</div>".
			  "<div style=\"margin-top:12px\">http_referer = [$s_http_referer]</div>".
			  "<div>remote_addr = [$s_remote_addr]</div>".
			  "<div>request_time = [$n_request_time]</div>".
			  "<div>created_tm = [$s_created_on]</div>".
			  "<div>s_http_user_agent = [$s_http_user_agent]</div>".
			  CTrace::stackTrace();

		error_log("<hr /><div>CTrace::logError</div>{$ss}<hr />",0);

		$ss = "INSERT INTO error_log (remote_ip,request_tm, msg) ".
			  "VALUES ('{$s_remote_addr}', {$n_request_time}, '".str_replace("'",'&#39;',$ss)."')";
		CSql::query_and_free($ss,CSql_IGNORE_ERROR,__FILE__,__LINE__);

		$gb_log_call = false;
		return $n_error_number;
	}
	function stackTrace()
	{
		// do not user ' as they will get escaped when persisted to the db
		$a = debug_backtrace();
		$s = '<div style=\"margin-top:12px\">Stack Trace</div>';
		for ( $i = 1 ; $i < count($a) ; $i++ )
		{
			$e =& $a[$i];
			$s .= "<div style=\"padding-left:20px\"><span style=\"color:#bd0b0b\">".
					(isset($e['class']) ? $e['class'] : 'noclass').
					"->".
					(isset($e['function']) ? $e['function'] : 'nofunction').
					"</span>(";
			$b =& $e['args'];
			$c  = '';
			for ( $j = 0 ; $j < count($b) ; $j++ )
			{
				switch ( gettype($b[$j]) )
				{
				case 'array':	$d = 'array'; break;
				case 'object':	$d = get_class($b[$j]); break;
				default:		$d = ''.$b[$j]; break;
				}
				if ( strlen($d) > 50 )
					$d = substr($d,0,50).'...';
				$c .= "<div style=\"padding-left:40px\">[{$d}],</div>";
			}
			if ( $c )
				$s .= substr($c,0,-7).')</div>';
			else
				$s .= ')';
			$s .= "<div style=\"margin:4px 0 12px 0\">called at {$e['file']}:{$e['line']}</div></div>";
		}
		return $s;
	}
	function dump($b_environment, $s_base_subdomain)
	{	global $gn_host, $gs_trace_msg, $gf_start_clock, $gf_trace_time;

		$e_time		 = CTime::get_time();
		$e_delta	 = sprintf("%0.3f ms", ($e_time - ($gf_trace_time ? $gf_trace_time : $gf_start_clock)) * 1000);
		$f_ellapsed_time = ($e_time - $gf_start_clock) * 1000.0;

		if ( $gn_host != HOST_FILMAF_COM )
		{
			echo	"\n<!--\n".
					"\tCTrace::dump BEGIN\n".
					"-->\n".
					"<table>".
					"<tr><td><div class='ru'>&nbsp;</div><br />&nbsp;</td></tr>".
					"<tr><td align='center'>".
					"<table border='1' width='80%'>".
					"<tr><td colspan='3'>\$CTrace debug information</td></tr>".
					"<tr><td colspan='3'>Elapsed time: <span style='color:red'>". round($f_ellapsed_time, 1) ." ms</span><br />".
								  "Instances per second: <span style='color:green'>". round(1000.0/$f_ellapsed_time, 1) ."</span></td></tr>".
					$gs_trace_msg.
					"</table>";

			if ( $b_environment )
			{
				echo  " <br />".
					  "<table border='1' width='80%'>".
						"<tr><td colspan='2'>Environment variables</td></tr>";
						  CTrace::dump_env('_POST'   , $_POST   );
						  CTrace::dump_env('_FILES'  , $_FILES  );
						  CTrace::dump_env('_GET'    , $_GET    );
						  CTrace::dump_env('_COOKIE' , $_COOKIE );
			//			  CTrace::dump_env('_SESSION', $_SESSION);
						  CTrace::dump_env('_SERVER' , $_SERVER );
						  CTrace::dump_env('_ENV'    , $_ENV    );
				echo    "<tr>".
						  "<td colspan='2'>".
							"<a href='$s_base_subdomain/server-info'>server-info</a> ".
							"<a href='$s_base_subdomain/server-status'>server-status</a> ".
							"<a href='$s_base_subdomain/php-info.php'>php-info.php</a>".
						  "</td>".
						"</tr>".
					  "</table>";
			}

			echo	"</td></tr>".
					"<tr><td><div class='ru'>&nbsp;</div></td></tr>".
					"</table>".
					"\n<!--\n".
					"\tCTrace::dump END\n".
					"-->\n";
		}
	}
	function dump_env($s_name, $a_value)
	{
		reset($a_value);
		echo "<tr><td>\$$s_name</td><td>";
		for (  $b_first = true  ;  list($s_key, $s_val) = each($a_value)  ;  )
		{
			if (
				 $s_key != 'CONTENT_LENGTH'			&& // = [749]
//				 $s_key != 'CONTENT_TYPE'			&& // = [application/x-www-form-urlencoded]
				 $s_key != 'DOCUMENT_ROOT'			&& // = [/var/www/html]
				 $s_key != 'GATEWAY_INTERFACE'		&& // = [CGI/1.1]
				 $s_key != 'HTTP_ACCEPT'			&& // = [*/*]
				 $s_key != 'HTTP_ACCEPT_ENCODING'	&& // = [gzip, deflate]
				 $s_key != 'HTTP_ACCEPT_LANGUAGE'	&& // = [en-us]
				 $s_key != 'HTTP_CACHE_CONTROL'		&& // = [no-cache]
				 $s_key != 'HTTP_CONNECTION'		&& // = [Keep-Alive]
				 $s_key != 'HTTP_COOKIE'			&& // = [orig=192.168.1.100%7C1177271694.8948]
//				 $s_key != 'HTTP_HOST'				&& // = [ash.filmaf.com]
//				 $s_key != 'HTTP_REFERER'			&& // = [http://www.filmaf.com/utils/edit-person.html?obj=1]
				 $s_key != 'HTTP_UA_CPU'			&& // = [x86]
				 $s_key != 'HTTP_USER_AGENT'		&& // = [Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322)]
				 $s_key != 'PATH'					&& // = [/sbin:/usr/sbin:/bin:/usr/bin]
				 $s_key != 'PATH_INFO'				&& // = [/ownedwoody]
				 $s_key != 'PATH_TRANSLATED'		&& // = [/var/www/html/ownedwoody]
				 $s_key != 'PHP_SELF'				&& // = [/owned/woody]
//				 $s_key != 'QUERY_STRING'			&& // = [obj=1]
//				 $s_key != 'REMOTE_ADDR'			&& // = [192.168.1.100]
				 $s_key != 'REMOTE_PORT'			&& // = [1271]
				 $s_key != 'REQUEST_METHOD'			&& // = [GET]
				 $s_key != 'REQUEST_TIME'			&& // = [1177806463]
//				 $s_key != 'REQUEST_URI'			&& // = [/utils/edit-person.html?obj=1]
//				 $s_key != 'SCRIPT_FILENAME'		&& // = [/var/www/html/list.html]
				 $s_key != 'SCRIPT_NAME'			&& // = [/owned]
//				 $s_key != 'SCRIPT_URI'				&& // = [http://www.filmaf.com/utils/edit-person.html]
//				 $s_key != 'SCRIPT_URL'				&& // = [/utils/edit-person.html]
				 $s_key != 'SERVER_ADDR'			&& // = [192.168.1.9]
				 $s_key != 'SERVER_ADMIN'			&& // = [you@example.com]
				 $s_key != 'SERVER_NAME'			&& // = [ash.filmaf.com]
				 $s_key != 'SERVER_PORT'			&& // = [80]
				 $s_key != 'SERVER_PROTOCOL'		&& // = [HTTP/1.1]
				 $s_key != 'SERVER_SIGNATURE'		&& // = []
				 $s_key != 'SERVER_SOFTWARE'		&& // = [Apache/2.2.4 (Unix) mod_ssl/2.2.4 OpenSSL/0.9.8e DAV/2 PHP/5.2.1]
				 true )
			{
				if ( $b_first ) $b_first = false; else echo '<br />';
				switch ( gettype($s_val) )
				{
				case 'array':
					echo "$s_key = <span style='color:red'>&lt;array&gt;</span>";
					while ( list($s_key2, $s_val2) = each($s_val) )
					{
						echo "<br />{$s_key}['<span style='color:green'>$s_key2</span>'] = <span style='color:red'>[</span>$s_val2<span style='color:red'>]</span>";
					}
					break; 
				default:
					echo "$s_key = <span style='color:red'>[</span>$s_val<span style='color:red'>]</span>";
					break;
				}
			}
		}
		echo "&nbsp;</td></tr>";
	}
}

//////////////////////////////////////////////////////////////////////////

?>
