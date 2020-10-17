<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CVendor.php';

$gs_db_host			= (isset($gn_host) && $gn_host != HOST_FILMAF_COM) ? 'localhost' : '10.80.225.130';
$gn_sql_connection	= 0;

class CItemRedirect
{
	function issueHeader($s_loc)
	{
		if ( $s_loc ) header("Location: $s_loc");

		header('Expires: Wed, 1 Jan 2012 05:00:00 GMT');                // date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');      // always modified
		header('Cache-Control: no-store, no-cache, must-revalidate');   // HTTP/1.1
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');                                     // HTTP/1.0
	}

	function hasOrig()
	{
		return dvdaf3_getvalue('orig',DVDAF3_COOKIE|DVDAF3_LOWER) != '';
	}

	function connect()
	{	global $gs_db_host, $gn_sql_connection;

		if ( ! $gn_sql_connection )
		{
			//$gn_sql_connection = @mysql_pconnect($gs_db_host, 'dvdaf', 'dvdaf');
			$gn_sql_connection = @mysql_pconnect('localhost', 'dvdaf', 'dvdaf');
			@mysql_select_db('dvdaf', $gn_sql_connection);
		}
	}

	function getUpc($n_dvd_id)
	{	global $gn_sql_connection;

		CItemRedirect::connect();
		if ( ($rr = @mysql_query("SELECT upc FROM dvd where dvd_id = {$n_dvd_id} LIMIT 1", $gn_sql_connection)) )
		{
			$ln = @mysql_fetch_assoc($rr);
				  @mysql_free_result($rr);
			if ( $ln )
			{
				$ln = explode(' ',$ln['upc']);
				return strlen($ln[0]) > 1 ? $ln[0] : false;
			}
		}
		return false;
	}

	function getUrl($n_vendor, $n_dvd_id)
	{	global $gn_sql_connection;

		if ( ($s_upc = $this->getUpc($n_dvd_id)) )
		{
			if ( ($rr = @mysql_query("SELECT url FROM imported_vendor WHERE vendor = {$n_vendor} and upc = '{$s_upc}' LIMIT 1", $gn_sql_connection)) )
			{
				$ln = @mysql_fetch_assoc($rr);
					  @mysql_free_result($rr);
				if ( $ln )
				{
					switch ( CVendor::$ma_pcmp[$n_vendor] )
					{
					case 'buy':	return CVendor::getHome('buy');
					default:	return $ln['url'] == '-' ? '' : $ln['url'];
					}
				}
			}
		}
	}

	function logClick($s_from, $s_vendor, $n_dvd_id)
	{	global $gn_sql_connection;

		$s_orig   = dvdaf3_getvalue('orig',DVDAF3_COOKIE|DVDAF3_LOWER);
		$s_user   = dvdaf3_getvalue('user',DVDAF3_COOKIE|DVDAF3_LOWER);

		if ( $s_orig == '' ) $s_orig = '-'; else $s_orig = substr($s_orig,0,32);
		if ( $s_user == '' ) $s_user = '-'; else $s_user = substr($s_user,0,32);
		$s_from   = substr($s_from,0,1);
		$s_vendor = substr($s_vendor,0,8);
		$n_dvd_id = intval($n_dvd_id);

		CItemRedirect::connect();
		@mysql_free_result(@mysql_query(
			"INSERT INTO clicks (advert_type, vendor, dvd_id, user_id, terminal_id, click_tm) ".
			"VALUES('{$s_from}', '{$s_vendor}', $n_dvd_id, '{$s_user}', '{$s_orig}', now())", $gn_sql_connection));

		@mysql_free_result(@mysql_query(
			"UPDATE dvdaf_user_2 SET last_link_tm = now() WHERE user_id = '{$s_user}'", $gn_sql_connection));
	}

	function redirect($s_loc, $s_from, $s_vendor, $n_dvd_id)
	{
		if ( $s_loc )
		{
			$this->issueHeader($s_loc);
			$this->logClick($s_from, $s_vendor, $n_dvd_id);
		}
		else
		{
			$this->issueHeader(false);
			echo  "<html>".
					"<head><title>Film Aficionado - Link</title></head>".
					"<body><div style='margin: 20px 20px 20px 20px'>Sorry, we are not able to find the requested link.</div></body>".
				  "</html>";
		}
	}

	function gotoDvd($s_vendor, $n_dvd_id, $s_upc, $s_from)
	{
		$a_vendors	= &CVendor::$ma_pcmp;
		$s_loc		= false;

		if ( $this->hasOrig() && $s_vendor !== '' )
		{
			switch ( $s_vendor )
			{
			case 'ebay':
				if ( $s_upc )
					$s_loc = CVendor::getLink('ebay',$s_upc);
				break;
			default:
				$n_vendor = intval($s_vendor);
				if ( $n_vendor >= 0 && $n_vendor < count($a_vendors) )
				{
					$s_vendor = $a_vendors[$n_vendor];
					if ( $n_vendor >= 0 && $n_vendor < count($a_vendors) && $n_dvd_id > 0 && $n_dvd_id < 999999 )
						$s_loc = $this->getUrl(CVendor::getIndex($s_vendor), $n_dvd_id);
				}
				else
				{
					$s_vendor = "bad($n_vendor}";
				}
				break;
			}
		}

		$this->redirect($s_loc, $s_from, $s_vendor, $n_dvd_id);
	}

	function gotoVendor($s_vendor)
	{
		$a_vendors	= &CVendor::$ma_vendors;
		$s_loc		= false;

		if ( isset($a_vendors[$s_vendor]) )
			$s_loc = $a_vendors[$s_vendor]['lnk0'];

		$this->redirect($s_loc, 'V', $s_vendor, 0);
	}

	function gotoAmazon($n_dvd_id)
	{	global $gn_sql_connection;

		$a_vendors	= &CVendor::$ma_vendors;
		$s_loc		= false;
		$s_vendor	= false;

		if ( $this->hasOrig() )
		{
			CItemRedirect::connect();
			if ( ($rr = @mysql_query("SELECT asin, amz_country FROM dvd WHERE dvd_id = {$n_dvd_id} LIMIT 1", $gn_sql_connection)) )
			{
				$ln = @mysql_fetch_assoc($rr);
					  @mysql_free_result($rr);

				if ( $ln && strlen($ln['asin']) > 1 )
				{
					$this->gotoAmazon_($n_dvd_id, $ln['amz_country'], $ln['asin']);
					return;
				}
			}
		}

		$this->redirect($s_loc, 'L', $s_vendor, $n_dvd_id);
	}

	function gotoImdb($n_dvd_id, $n_index)
	{	global $gn_sql_connection;

		if ( $this->hasOrig() )
		{
			CItemRedirect::connect();
			if ( ($rr = @mysql_query("SELECT imdb_id FROM dvd WHERE dvd_id = {$n_dvd_id} LIMIT 1", $gn_sql_connection)) )
			{
				$ln = @mysql_fetch_assoc($rr);
					  @mysql_free_result($rr);

				if ( $ln && strlen($ln['imdb_id']) > 1 )
				{
					$s_imdb = $ln['imdb_id'];
					$s_imdb = explode(' ', $s_imdb);

					if ( isset($s_imdb[$n_index]) )
					{
						$this->gotoImdb_($n_dvd_id, $s_imdb[$n_index]);
						return;
					}
				}
			}
		}

		$this->redirect('', 'L', 'imd', $n_dvd_id);
	}

	function gotoAmazon_($n_dvd_id, $s_amz_country, $s_asin)
	{
		$a_vendors = &CVendor::$ma_vendors;
		$s_loc     = false;

		switch ( $s_amz_country )
		{
			case '-': $s_vendor = 'amz';	break;
			case 'C': $s_vendor = 'amz.ca';	break;
			case 'K': $s_vendor = 'amz.uk';	break;
			case 'F': $s_vendor = 'amz.fr';	break;
			case 'D': $s_vendor = 'amz.de';	break;
			case 'I': $s_vendor = 'amz.it'; break;
			case 'E': $s_vendor = 'amz.es'; break;
			case 'J': $s_vendor = 'amz.jp';	break;
			default:  $s_vendor = 'amz';	break;
		}

		if ( isset($a_vendors[$s_vendor]) )
			$s_loc = $a_vendors[$s_vendor]['lnk1'] . $s_asin . $a_vendors[$s_vendor]['lnk2'];

		$this->redirect($s_loc, 'L', $s_vendor, $n_dvd_id);
	}

	function gotoImdb_($n_dvd_id, $s_imdb_id)
	{
		$a_vendors = &CVendor::$ma_vendors;
		$s_loc = $a_vendors['imd']['lnk1'] . $s_imdb_id . $a_vendors['imd']['lnk2'];
		$this->redirect($s_loc, 'L', 'imd', $n_dvd_id);
	}
}

?>
