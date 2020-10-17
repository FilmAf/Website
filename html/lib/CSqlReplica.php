<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

//////////////////////////////////////////////////////////////////////////

// CSql global variables
$gn_sql_connection	= 0;
$gs_sql_password	= 'dvdaf';
$gn_sql_affinity	= 15; // number of seconds after a database change that we keep looking at the master
$gb_sql_recent		= isset($_COOKIE['lastupd']) && ((time() - 1317000000) < intval($_COOKIE['lastupd']) + $gn_sql_affinity);
$gs_sql_host		= '-';
$gb_check_connetion	= true;
$gs_db_server		= '-';
$gs_db_master		= (isset($gn_host) && $gn_host != HOST_FILMAF_COM) ? 'localhost' : '10.80.225.130';
$gs_db_slave		= $gs_db_master;
$gb_db_cli			= php_sapi_name() === "cli";

//////////////////////////////////////////////////////////////////////////

class CSqlReplica
{
	function isRecent()
	{	global $_COOKIE, $gb_sql_recent, $gb_check_connetion, $gn_sql_affinity;

		$b_sql_recent = isset($_COOKIE['lastupd']) && ((time() - 1317000000) < intval($_COOKIE['lastupd']) + $gn_sql_affinity);

		if ( ! $gb_sql_recent && $b_sql_recent )
		{
			$gb_sql_recent	= true;
			$gb_check_connetion	= true;
		}

		return $gb_sql_recent;
	}

	function checkConnection($ss, $file, $line)
	{	global $_COOKIE, $_SERVER, $gn_host, $gb_sql_recent, $gb_check_connetion, $gn_sql_connection, $gb_db_cli, $gn_sql_affinity;

		//echo "gb_db_cli = [{$gb_db_cli}]<br />";
		//echo "<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />";
		//echo "here 0 time = [".(time() - 1317000000)."]<br />";
		//if (isset($_COOKIE['lastupd'])) echo "here 0 cookie = [".(intval($_COOKIE['lastupd']) + $gn_sql_affinity)."]<br />";
		//if (isset($_COOKIE['lastupd'])) echo "here 0 recent = [".((((time() - 1317000000) < intval($_COOKIE['lastupd']) + $gn_sql_affinity)) ? 'true' : 'false')."]<br />";
		//echo "here 1 gb_sql_recent = [$gb_sql_recent]<br />";

		if ( $gb_db_cli )
			return $gn_sql_connection == 0;

		switch ( substr($ss,0,3) )
		{
			case 'SEL':
				//echo "here 2 gb_check_connetion = [$gb_check_connetion], gn_sql_connection = [$gn_sql_connection], gb_sql_recent = [$gb_sql_recent]<br />";
				return $gb_check_connetion || ($gn_sql_connection == 0);
			/*
                    case 'UPD':
                    case 'DEL':
                    case 'INS':
                    case 'CAL':
                    case 'TRU':
                    case 'DRO':
                    case 'REN':
                    case 'OPT':
                        //echo "here 3<br />";
                        //error_log("<hr />".
                        //			"<div style='color=:#ff0000'>CSqlReplica::checkConnection</div>".
                        //			"<div>created_tm = [". date('Y-m-d H:i:s') ."]</div>".
                        //			"<div>error_type = [-]</div>".
                        //			"<div>message = [log only]</div>".
                        //			"<div>location = [". $file ." on line ". $line ."]</div>".
                        //			"<div>URL = [". (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') ."]</div>".
                        //			"<div>sql_query = [$ss]</div>".
                        //			"<hr />", 0);
                        break;
                    default:
                        //echo "here 4 ".substr($ss,0,3)."<br />";
                        error_log("<hr />".
                                  "<div style='color=:#ff0000'>CSqlReplica::checkConnection</div>".
                                  "<div>created_tm = [". date('Y-m-d H:i:s') ."]</div>".
                                  "<div>error_type = [E]</div>".
                                  "<div>message = [Unrecognized SQL]</div>".
                                  "<div>location = [". $file ." on line ". $line ."]</div>".
                                  "<div>URL = [". (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '') ."]</div>".
                                  "<div>sql_query = [$ss]</div>".
                                  "<hr />", 0);
                        break;
            */
		}

		if ( ! $gb_sql_recent )
		{
			$gb_sql_recent	= true;
			$gb_check_connetion = true;

			if ( !isset($gn_host) || $gn_host == HOST_FILMAF_COM ) $s_cookie_domain = '.filmaf.com'; else
				if (					 $gn_host == HOST_FILMAF_EDU ) $s_cookie_domain = '.filmaf.edu'; else
					$s_cookie_domain = '.filmaf.com';
			$n_time		= time() - 1317000000;
			$_COOKIE['lastupd'] = $n_time;

			if ( headers_sent() )
			{
				//echo "here 6 ".'http://www'+$s_cookie_domain+'/utils/lastupd.php'."<br />";
				//reanable when we have another database server
				//file('http://www'+$s_cookie_domain+'/utils/lastupd.php');
			}
			else
			{
				//echo "here 7 setcookie<br />";
				setcookie('lastupd', $n_time, mktime(0,0,0,3,1,date("Y") + 1), '/', $s_cookie_domain, 0);
			}
		}
		//echo "here 8 gb_check_connetion = [$gb_check_connetion], gb_sql_recent = [$gb_sql_recent]<br />";
		return $gb_check_connetion || ($gn_sql_connection == 0);
	}

	function connect($file, $line)
	{	global $gn_host, $gs_sql_host, $gn_sql_connection, $gs_sql_password, $gb_sql_recent, $gb_check_connetion, $gs_db_server, $gs_db_master, $gs_db_slave, $gb_db_cli;

		if ( $gb_db_cli )
		{
			$gs_sql_host		= !isset($gn_host) || $gn_host == HOST_FILMAF_COM ? $gs_db_master : 'localhost';
		}
		else
		{
			$s_wish_host	 	= !isset($gn_host) || $gn_host == HOST_FILMAF_COM ? ($gb_sql_recent ? $gs_db_master : $gs_db_slave) : 'localhost';
			$gb_check_connetion = false;
			//echo "here 9 s_wish_host = [$s_wish_host], gn_sql_connection = [$gn_sql_connection], gb_sql_recent = [$gb_sql_recent], gs_db_slave = [$gs_db_slave]<br />";

			if ( $gn_sql_connection != 0 )
			{
				if ( $gs_sql_host == $s_wish_host )
					return;
				//mysql_close($gn_sql_connection);
				$gn_sql_connection	= 0;
				$gs_sql_host		= $s_wish_host;
			}
			else
			{
				if ( $gs_sql_host = '-' )
					$gs_sql_host = $s_wish_host;
			}
		}

		if ( $gn_sql_connection == 0 )
		{
			switch ( $gs_sql_host )
			{
				case '10.80.225.130':	$gs_db_server = 'cyllene'; break;
				case '173.193.185.162':	$gs_db_server = 'cyllene'; break;
				default:				$gs_db_server = 'kore';    break;
			}
			//$gn_sql_connection = @mysql_pconnect($gs_sql_host, 'dvdaf', $gs_sql_password);
			$gn_sql_connection = mysql_pconnect("localhost", "dvdaf", "dvdaf");

			//echo "here 10 gs_sql_host = [$gs_sql_host], gn_sql_connection = [$gn_sql_connection]<br />";
			if ( $gn_sql_connection === false )
			{
				error_log("<hr />".
					"<div style='color=:#ff0000'>CSqlReplica::connect</div>".
					"<div>created_tm = [". date('Y-m-d H:i:s') ."]</div>".
					"<div>error_type = [S]</div>".
					"<div>message = [mysql_pconnect]</div>".
					"<div>location = [". $file ." on line ". $line ."]</div>".
					"<div style='margin-top:12px'>sql_error_number = [". @mysql_errno() ."]</div>".
					"<div>sql_error_msg = [". @mysql_error() ."]</div>".
					"<hr />", 0);
			}
			else
			{
				if ( @mysql_select_db('dvdaf', $gn_sql_connection) === false )
				{
					error_log("<hr />".
						"<div style='color=:#ff0000'>CSqlReplica::connect</div>".
						"<div>created_tm = [". date('Y-m-d H:i:s') ."]</div>".
						"<div>error_type = [S]</div>".
						"<div>message = [mysql_select_db]</div>".
						"<div>location = [". $file ." on line ". $line ."]</div>".
						"<div style='margin-top:12px'>sql_error_number = [". @mysql_errno() ."]</div>".
						"<div>sql_error_msg = [". @mysql_error() ."]</div>".
						"<hr />", 0);
				}
			}
		}
	}
}

?>
