<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndMenu.php';
require $gs_root.'/lib/CNavi.php';

class CDvdWho extends CWndMenu
{
	function constructor()
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-dvd-list_{$this->mn_lib_version}.js'></script>\n";
		$this->mb_menu_context		= true;

		// Location
		$this->ms_clean_uri			= preg_replace('/[&?](pg)=[0-9a-z]+/i', '', str_replace('&amp;', '&', dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER|DVDAF3_NO_AMP_EXPANSION)));
		$this->mb_clean_uri_parm	= strpos($this->ms_clean_uri, '?') !== false;
		if ( ! $this->mb_clean_uri_parm )
		{
			if ( ($n_pos = strpos($this->ms_clean_uri, '&')) )
			{
				$this->ms_clean_uri{$n_pos} = '?';
				$this->mb_clean_uri_parm = true;
			}
		}

		// Paging
		$this->mn_max_listings		= 1000;
		$this->mn_req_page_size		= 200;	// initialized from cookies in verifyUser()
		$this->mn_page_size			= 50;	// initialized in verifyUser()
		$this->mn_max_page			= 200;	// initialized in verifyUser()
		$this->mn_page_number		= 1;	// initialized from get in verifyUser()

		// Search parms
		$this->ms_dvd_id			= dvdaf3_getvalue('dvd', DVDAF3_GET);
		$this->ma_dvd_id			= array();
		$this->mb_imdb_id			= false;

		$a_matches					= array();
		$nm_max_dvd_ids				= 5;
		/*
		if ( preg_match('/^([0-9]+)(-[0-9]+)*$/', $this->ms_dvd_id, $a_matches) )
		{
			if ( count($a_matches) == 3 )
			{
				$n_dvd  = intval($a_matches[1]);
				$s_imdb = CSql::query_and_fetch1("SELECT imdb_id FROM dvd WHERE dvd_id = {$n_dvd}", 0,__FILE__,__LINE__);
				$n_ndx  = intval(substr($a_matches[2],1));
				$s_imdb = explode(' ', $s_imdb);
				if ( count($s_imdb) > $n_ndx )
				{
					$this->ms_dvd_id  = CSql::query_and_fetch1("SELECT GROUP_CONCAT(dvd_id) FROM dvd WHERE imdb_id like '%{$s_imdb[$n_ndx]}%'", 0,__FILE__,__LINE__);
					$this->mb_imdb_id = true;
					$nm_max_dvd_ids   = 1000;
				}
				else
				{
					$this->ms_dvd_id  = '';
				}
			}
		}
		*/

		if ( $this->ms_dvd_id )
		{
			$a_dvd = explode(',', $this->ms_dvd_id);
			$this->ms_dvd_id = '';
			for ( $i = 0, $k = 0 ; $i < count($a_dvd) ; $i++ )
			{
				if ( is_numeric($a_dvd[$i]) && intval($a_dvd[$i]) > 0 && $k < $nm_max_dvd_ids )
				{
					$this->ms_dvd_id  .= intval($a_dvd[$i]). ',';
					$this->ma_dvd_id[] = intval($a_dvd[$i]);
					$k++;
				}
			}
			$this->ms_dvd_id = substr($this->ms_dvd_id, 0, -1);
		}
	}

	function getFooterJavaScript()
	{
		$s_user   = $this->mb_logged_in  ? $this->ms_user_id : '';
		$s_config = '{baseDomain:"'.		$this->ms_base_subdomain.'"'.
					($this->mn_echo_zoom ? ',preloadImgPop:1' : '').
					',userCollection:"'.	$s_user.'"'.
					',viewCollection:""'.
					',onPopup:DvdListMenuPrep.onPopup'.
					',cartHandlers:1'.
					',ulDvd:1'.
					',ulExplain:1'.
					',ulJump:1'.
					',ulPageSize:1'.
					',imgPreLoad:"pin.cart.price.help.explain.spin.drop.pagesize"'.
					'}';
		return
					"function onMenuClick(action){DvdListMenuAction.onClick(action);};".
					"Filmaf.config({$s_config});".
					  "setTimeout('".
						"Menus.setup();".
						"DvdList.synchLongTitles();".
						"Jump.attach(\"dp_jump_0\",null);".
						"PageSize.attach(\"sz_page_0\");".
					  "',100);";
	}

	function verifyUser()
	{
		parent::verifyUser();

		if ( $this->mn_access_level_cd <= 0 && $this->mn_echo_zoom == CWnd_ZOOM_STAR )
			if ( $this->getViewStars() <= 0 )
				$this->mn_echo_zoom = CWnd_ZOOM_NONE;

		CDvdUtils::getPagingInfo($this->mn_access_level_cd,
								 $this->mn_max_listings, 
								 50,
								 50,
								 $this->mn_req_page_size,
								 $this->mn_page_size,
								 $this->mn_max_page,
								 $this->mn_page_number);
	}

	function sqlQuery($s_select, $s_from, $s_where, $s_sort, $n_begin, $n_count)
	{
		return CSql::limitRows("SELECT $s_select FROM $s_from WHERE $s_where". ($s_sort ? " ORDER BY $s_sort" : ''), $n_begin < 1 ? 1 : $n_begin, $n_count);
	}

	function createSql(&$s_select, &$s_from, &$s_where, &$s_sort)
	{
		// SELECT a.user_id, a.folder, b.folder FROM my_dvd a, my_dvd b WHERE a.folder like 'owned%' and a.dvd_id = 21 and b.folder like 'owned%' and b.dvd_id = 21141 and a.user_id = b.user_id;
		$n_count_dvds = count($this->ma_dvd_id);
		$s_select	  = 'a0.user_id, c.public_count, c.public_owned, ';
		$s_from		  = '';
		$s_where	  = '';
		$s_sort		  = 'a0.user_id';
		if ( $this->mb_imdb_id )
		{
			$s_select .= "a0.folder, ";
			$s_from   .= "my_dvd a0, ";
			$s_where  .= "a0.folder like 'owned%' and a0.dvd_id in ({$this->ms_dvd_id}) and ";
			$i = 0;
		}
		else
		{
			for ( $i = 0 ; $i < $n_count_dvds ; $i++ )
			{
				$s_select .= "a{$i}.folder, ";
				$s_from   .= "my_dvd a{$i}, ";
				$s_where  .= "a{$i}.folder like 'owned%' and a{$i}.dvd_id = {$this->ma_dvd_id[$i]} and ". ($i ? "a0.user_id = a{$i}.user_id and " : '');
			}
			$i--;
		}
		$s_select    = substr($s_select, 0, -2);
		$s_from      = substr($s_from  , 0, -2). " LEFT JOIN my_dvd_count c ON c.user_id = a{$i}.user_id and c.folder = a{$i}.folder";
		$s_where     = substr($s_where , 0, -5);
	}

	function setTitle()
	{
		$str = '';
		if ( $this->mb_imdb_id )
		{
			$str = 'Who&#39;s got any of its editions?';
		}
		else
		{
			$n_count_dvds = count($this->ma_dvd_id);

			switch ( $n_count_dvds )
			{
			case 1:  $str = "Who&#39;s got it?"; break;
			case 2:  $str = "Who&#39;s got them both?"; break;
			default: $str = "Who&#39;s got all {$n_count_dvds} of them?"; break;
			}
		}

		$this->ms_display_what = $str." <img id='ex_www_whos_got_it' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' alt='Explain' style='position:relative;top:4px' />";
	}

	function drawMessagesTot($n_row_begin, $n_row_end, $n_row_total, $s_msg, $s_error, $b_empty)
	{
		if ( $s_error )
		{
			$this->ms_display_error = $s_error;
			$this->drawMessages(true,false);
		}
		else
		{
			$this->drawMessages(false,false);
			if ( ! $b_empty && $n_row_total )
			{
				echo  "<div id='nav_top'>";
				$this->drawPageNav($n_row_begin, $n_row_end, $n_row_total, 0, 0, false,true);
				echo  "</div>";
			}

			if ( $s_msg )
				echo "<div id='msg-only'>{$s_msg}</div>";
		}
	}

	function drawDVD()
	{
		$s_select	= "a.dvd_id, a.pic_status, a.pic_name, a.pic_count, '-' pic_overwrite, a.dvd_title, a.film_rel_year, ".
					  "a.genre, 0 genre_overwrite, a.region_mask, a.dvd_rel_dd, '' folder, a.media_type, a.source, a.imdb_id, ".
					  "a.asin, a.amz_country, a.list_price, a.director, a.publisher, a.country, a.rel_status, a.best_price";
		$s_from		= "dvd a";
		$s_where	= $this->ms_dvd_id ? "a.dvd_id in ({$this->ms_dvd_id})" : "1 = 2";
		$s_sort		= "a.dvd_title_nocase, a.film_rel_year, a.director_nocase, a.dvd_id";
		$rr			= CSql::query("SELECT $s_select FROM $s_from WHERE $s_where ORDER BY $s_sort",0,__FILE__,__LINE__);

		for ( $n_line = 1 ; ($a_line = CSql::fetch($rr)) ; $n_line++ )
		{
			if ( $n_line == 1 ) echo  "<table class='border'>";
			echo dvdaf3_getbrowserow($a_line, DVDAF3_PRES_DVD_WHO, 0, 0, 0, 0, 0, $n_line, 1, $this->ms_view_id);
		}

		if ( $n_line > 1 ) echo  "</table>";
	}

	function drawResults($n_row_begin, $n_row_end, $rr)
	{
		echo "<table class='who_table'>";
		for ( $n_line_number = $n_row_begin, $i = 0  ;  $n_line_number <= $n_row_end && $a_line = CSql::fetch($rr)  ;  $n_line_number++, $i++ )
		{
			echo  "<tr>".
					"<td><a href='http://{$a_line['user_id']}{$this->ms_unatrib_subdomain}/{$a_line['folder']}'>{$a_line['user_id']}</a></td>".
					"<td>/{$a_line['folder']}</td>".
					"<td class='who_count'>".($a_line['public_count'] == $a_line['public_owned'] ? "{$a_line['public_count']}" : "{$a_line['public_count']}/{$a_line['public_owned']}")."</td>".
					"<td class='who_dvd' style='padding-left:0'>DVD".($a_line['public_owned'] == 1 ? '' : 's')."</td>".
				  "</tr>";
		}
		echo "</table>";
	}

	function drawBodyPage()
	{
		$n_row_begin  = 0;
		$n_row_end	  = 0;
		$n_row_total  = 0;
		$s_msg		  = '';
		$s_error	  = '';
		$s_tell		  = '';
		$b_empty	  = false;
		$b_no_results = true;
		$rr			  = null;

		if ( $this->ms_dvd_id )
		{
			// // prepare query
			$s_select	= '';
			$s_from		= '';
			$s_where	= '';
			$s_sort		= '';
			$this->createSql($s_select, $s_from, $s_where, $s_sort);

			// execute query
			$n_row_begin  = ($this->mn_page_number - 1) * $this->mn_page_size;
			$n_row_total  = CSql::query_and_fetch1($this->sqlQuery('count(*) results', $s_from, $s_where, '', 0, 0), 0,__FILE__,__LINE__);
			$n_row_begin++;
			$n_row_end    = $n_row_begin + min($n_row_total - $n_row_begin, $this->mn_page_size - 1);

			// draw
			$this->setTitle();
			if ( $n_row_end >= $n_row_begin )
			{
				if ( ($rr = CSql::query($this->sqlQuery($s_select, $s_from, $s_where, $s_sort, $n_row_begin, $this->mn_page_size),0,__FILE__,__LINE__)) )
					$b_no_results = false;
				else
					$s_error = "We encountered a problem. ".$this->getWaitAndRetry();
			}
			else
			{
				if ( $n_row_total )
					$s_tell = 'Nothing to show. You may be past the last page.';
				else
					$s_tell = 'Sorry, no matches.';
				$b_empty = true;
			}
		}
		else
		{
			$s_msg = 'Sorry, we did not find, or perhaps we did not understand, which DVDs you were interested in.';
		}

		// draw
		$this->drawMessagesTot($n_row_begin, $n_row_end, $n_row_total, $s_msg, $s_error, $b_empty);
		if ( ! $b_no_results || $s_tell )
		{
			$this->drawDVD();
			if ( $s_tell )
			{
				echo "<div id='msg-only'>{$s_tell}</div>";
			}
			else
			{
				$this->drawResults($n_row_begin, $n_row_end, $rr);
				CSql::free($rr);
			}
		}
	}

	function drawPageNav($n_beg, $n_end, $n_tot, $n_total_titles, $n_total_disks, $b_validate, $b_explain)
	{
		CDvdUtils::drawPageNav(	$this->mn_page_size,
								0,
								false,
								$this->mn_max_page,
								$this->mn_max_listings,
								$this->ms_clean_uri,
								'',
								'',
								true,
								$n_tot,
								$n_beg,
								$n_end,
								$n_total_titles,
								$n_total_disks,
								$b_explain,
								'',
								false,
								$this->ms_base_subdomain);
	}
}

?>
