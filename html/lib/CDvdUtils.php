<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CDvdUtils
{
	function getFolders(&$a_folders, $s_view_id, $s_user_id, $b_query)
	{
		if ( $b_query )
		{
			$s_from    = ($s_user_id == $s_view_id) ? 'my_folder' : 'v_my_folder_pub';
			$a_folders = array();

			if ( ($rr = CSql::query("SELECT folder FROM $s_from WHERE user_id = '$s_view_id' ORDER BY sort_category, sort_order, folder", 0,__FILE__,__LINE__)) )
			{
				for ( $i = 0 ; ($rt = CSql::fetch($rr)) ; $i++ )
				{
					$s_full = $rt['folder'];
					$a      = explode('/', $s_full);
					$a_folders[$i]['level'] = $n = count($a);
					$a_folders[$i]['name']  = $a = $a[$n-1];
					$a_folders[$i]['full']  = $s_full;
					$a_folders[$i]['curr']  = substr($s_full,0,-1 - strlen($a));
					/*
						n >= 0	= position 'n' is the root to this folder
						-1	= the root to this is the collection
						-2	= later used to identify a breadcrumb to the current folder
						-3	= later used to identify the current folder
					*/
				}
				CSql::free($rr);
			}
		}
		else
		{
			$a_folders = array( array('level' => 1, 'name' => 'owned'		, 'full' => 'owned'		, 'curr' => ''),
								array('level' => 1, 'name' => 'on-order'	, 'full' => 'on-order'	, 'curr' => ''),
								array('level' => 1, 'name' => 'wish-list'	, 'full' => 'wish-list'	, 'curr' => ''),
								array('level' => 1, 'name' => 'work'		, 'full' => 'work'		, 'curr' => ''),
								array('level' => 1, 'name' => 'have-seen'	, 'full' => 'have-seen'	, 'curr' => ''),
								array('level' => 1, 'name' => 'trash-can'	, 'full' => 'trash-can'	, 'curr' => ''));
		}
	}
	function makeMenu(&$a_folders, $s_id_prefix, $s_domain, $s_url_parm)
	{
		$n_tot   = count($a_folders);
		$s_menu  = '';
		$n_level = 1;

		for ( $i = 0 ; $i < $n_tot ; $i++ )
		{
			while ( $a_folders[$i]['level'] > $n_level )
			{
//				$s_menu = substr($s_menu,0,-5). ($i == 0 || $a_folders[$i-1]['curr'] <= -2 ? "<ul id='{$s_id_prefix}_{$n_level}'>" : "<ul>");
				$s_menu = substr($s_menu,0,-5). "<ul>";
				$n_level++;
			}
			while ( $a_folders[$i]['level'] < $n_level )
			{
				$s_menu .= "</ul></li>";
				$n_level--;
			}
			$s_menu .= "<li>". ($a_folders[$i]['curr'] <= -2 ? "<img src='http://dv1.us/c0/rb1.gif' />" : '').
					   "<a href='{$s_domain}/{$a_folders[$i]['full']}$s_url_parm'>{$a_folders[$i]['name']}</a></li>";
		}
		while ( $n_level > 1 )
		{
			$s_menu .= "</ul></li>";
			$n_level--;
		}
		return $s_menu;
	}
	function makeColNav(&$a_folders, $s_folder, $b_all)
	{
		$a_curr			= explode('/', $s_folder);
		$n_curr_level	= count($a_curr);
		$n_tot			= count($a_folders);
		$a_nav			= array();
		$a_path			= array();
		$s_path			= '';

		for ( $i = 0 ; $i < $n_curr_level ; $i++ )
		{
			$s_path    .= $a_curr[$i] . '/';
			$a_nav [$i] = array();
			$a_path[$i] = $s_path;
		}
		$a_nav[$i] = array();
		$s_folder .= '/';

		for ( $i = 0 ; $i < $n_tot ; $i++ )
		{
			$a_folders[$i]['slash'] = $a_folders[$i]['full'] . '/';

			$n_level = $a_folders[$i]['level'];
			if ( $n_level == 1 )
			{
				$a_folders[$i]['txt'] = $a_path[0] == $a_folders[$i]['slash'];
				$a_nav[0][] = &$a_folders[$i];
			} else
			if ( $n_level <= $n_curr_level && $a_path[$n_level-2] == substr($a_folders[$i]['slash'],0,strlen($a_path[$n_level-2])) )
			{
				$a_folders[$i]['txt'] = $a_path[$n_level-1] == $a_folders[$i]['slash'];
				$a_nav[$n_level-1][] = &$a_folders[$i];
			} else
			if ( $n_level == $n_curr_level + 1 && $s_folder == substr($a_folders[$i]['slash'],0,strlen($s_folder)) )
			{
				$a_folders[$i]['txt'] = false;
				$a_nav[$n_level-1][] = &$a_folders[$i];
			}
		}

		$str = '';
		for ( $i = 0 ; $i < count($a_nav) ; $i++ )
		{
			$str  .= "<ul>";
			$n_tot = count($a_nav[$i]);
			for ( $k = 0 ; $k < $n_tot ; $k++ )
			{
				$s_href  = "href='/{$a_nav[$i][$k]['full']}'";
				$s_name  = $a_nav[$i][$k]['name'];
				$n_level = $a_nav[$i][$k]['level'];
				$b_txt   = $a_nav[$i][$k]['txt'];

				$str .= "<li>".
						  ($b_txt ? ( $n_level == $n_curr_level ? $s_name : "<span><a {$s_href}>{$s_name}</a></span>") : "<a {$s_href}>{$s_name}</a>").
						  ($k < $n_tot - 1 || $i == 1 ? "<span>&nbsp;| </span>" : '').
						"</li>";
			}
			if ( $i == 1 && $n_tot > 0 )
			{
				$s_href  = "href='/{$a_nav[$i][0]['curr']}?rc=1'";
				$str .= "<li>".
						  ($b_all ? '&lt;all&gt;' : "<a {$s_href}>&lt;all&gt;</a>").
						"</li>";
			}
			$str .= "</ul>";
		}

		return $str;
	}

	//////////////////////////////////////////////////////////////////////
	function getStar($n_stars)
	{
		if ( $n_stars <= 9 )
		{
			if ( $n_stars < 5 && $n_stars >= 2 )
				$n_stars++;
			return "http://dv1.us/s1/smb{$n_stars}.png";
		}
		return '';
	}

	//////////////////////////////////////////////////////////////////////
	function getPagingInfo($n_access_level_cd, $n_max_listings, $n_fixed_page_size, $n_min_page_size,		// input
						   &$n_req_page_size, &$n_page_size, &$n_max_page, &$n_page_number)					// output
	{
		$n_max_page = 200;
		switch ( $n_access_level_cd )
		{
		case 1: case 2: $n_max_page = 500; break;
		case 3: case 4: case 5: case 6: case 7: case 8: case 9: $n_max_page = 1000; break;
		}

		$n_req_page_size = dvdaf3_getvalue('page', DVDAF3_COOKIE|DVDAF3_INT);
		$n_page_number	 = dvdaf3_getvalue('pg'  , DVDAF3_GET|DVDAF3_INT, 1, 10000);

		if ( $n_req_page_size < 1				 ) $n_req_page_size =   50; else
		if ( $n_req_page_size < $n_min_page_size ) $n_req_page_size = $n_min_page_size; else
		if ( $n_req_page_size > 1000			 ) $n_req_page_size = 1000;
		if ( $n_req_page_size > $n_max_page		 ) $n_req_page_size = $n_max_page;

		$n_page_size = $n_fixed_page_size ? $n_fixed_page_size : $n_req_page_size;

		if ( $n_page_number * $n_page_size > $n_max_listings )
			$n_page_number = 1;
	}

	//////////////////////////////////////////////////////////////////////
	function stripUrlParm($s_url, $s_key)
	{
		$s_url = preg_replace("/[&\?]{$s_key}=[^&]*/", '', str_replace('&amp;','&',$s_url));
		if ( ! strpos($s_url,'?') && ($n_pos = strpos($s_url,'&')) )
			$s_url = substr($s_url,0,$n_pos-1).'?'.substr($s_url,$n_pos);
		return str_replace('&','&amp;',$s_url);
	}

	//////////////////////////////////////////////////////////////////////
	function drawPageNav($n_page_size, $n_req_page_size, $b_var_page_size, $n_max_page, $n_max_listings, $s_href, $s_url_parms, $s_onclick, $b_postfix_0,
						 $n_tot, $n_beg, $n_end, $n_total_titles, $n_total_disks, $b_explain, $s_pres_mode, $b_enable_mode, $s_base_subdomain)
	{
		// invoke page size option
		$pag = ($b_var_page_size ? " <img src='http://dv1.us/d1/00/sz00.gif' id='sz_page_0' sp_max='{$n_max_page}' height='16' width='16' alt='Set page size' align='top' />" : '');

		// show result count and pages
		if ( $n_tot <= 0					 ) { $res = "No results found."; } else
		if ( $n_tot == 1					 ) { $res = "One result found."; } else
		if ( $n_beg == 1 && $n_end == $n_tot ) { $res = "Showing all {$n_tot} results.{$pag}"; }
		else
		{
			$res = "Showing ". ($n_beg == $n_end ? "#$n_beg" : "$n_beg - $n_end"). $pag. " of {$n_tot}&nbsp; ";
			if ( $n_tot > $n_max_listings ) $n_tot = $n_max_listings;
			if ( $n_beg != 1 || $n_end < $n_tot )
			{
				$res .= CNavi::page($s_href,											// base url
									intval(($n_beg + $n_page_size - 1) / $n_page_size),	// cur
									intval(($n_tot + $n_page_size - 1) / $n_page_size),	// tot
									false,												// class
									$s_onclick,
									$s_url_parms,
									false,												// show index option
									true,												// left alpha
									$b_postfix_0);
			}
		}

		// show title and disk counts
		$cnt = '';
		if ( $n_total_titles ) $cnt .= ", {$n_total_titles} title". ($n_total_titles > 0 ? 's' :'');
		if ( $n_total_disks  ) $cnt .= ", {$n_total_disks} disc"  . ($n_total_disks  > 0 ? 's' :'');
		if ( $cnt			 ) $cnt  = "<br />(". substr($cnt,2). ")";

		echo	"<table class='nav-table'>".
				  "<tr>".
					"<td width='1%'>".
					  $res.
					  $cnt.
					"</td>".
				  ($b_enable_mode ? "<td width='8%'>".
									  "<select onchange='DvdList.setListFormat(DropDown.getSelValue(this),0,false)'>".
										"<option value='src'".($s_pres_mode == ''    ? " selected='selected'" : '').">DVD list format</option>".
										"<option value='one'".($s_pres_mode == 'one' ? " selected='selected'" : '').">One DVD per page</option>".
										"<option value='prn'".($s_pres_mode == 'prn' ? " selected='selected'" : '').">Print</option>".
									  "</select>".
									"</td>" : '').
					($b_explain   ? "<td width='45%' style='vertical-align:middle'>".
									  "<img id='ex_www_titles_disks_0' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' alt='Explain' />".
									"</td>" : '').
					($b_explain   ? "<td width='45%' style='vertical-align:middle;text-align:right'>".
									  "<img id='ex_www_dvd_cart_0' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' alt='Explain' />".
									"</td>" : '').
					"<td width='1%' style='text-align:right'>".
					  "<a href='{$s_base_subdomain}/price.html' title='Show shopping cart'>Show cart / Compare prices</a>".
					  "<br />".
					  "(<span id='cart_count_0'>your cart is empty</span>)".
					"</td>".
				  "</tr>".
				"</table>";
	}
}

//////////////////////////////////////////////////////////////////////////

?>
