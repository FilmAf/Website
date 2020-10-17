<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */
	// 'What to watch'	,'Pick a title to watch from within this folder or selection'
	// 'What to get'	,'Suggest what I might like based on this folder or selection'
	// 'Similartastes'	,'Find out people with similar tastes based on selected titles'
	//					  "<a class='mf' href='{$this->ms_base_subdomain}/dvd/{$n_dvd_id}'>Tips&nbsp;on&nbsp;scanning</a>".
	//					  "<a class='mf' href='{$this->ms_base_subdomain}/dvd/{$n_dvd_id}'>Submission&nbsp;guidelines</a>".
	//					  "<a class='mf' href='{$this->ms_base_subdomain}/dvd/{$n_dvd_id}'>See&nbsp;Details&nbsp;and&nbsp;Comments</a>".

define('CWnd_LIST_OTHER'		,      0);	// ms_list_kind
define('CWnd_LIST_DVDS'			,      1);
define('CWnd_LIST_LISTS'		,      2);
define('CWnd_LIST_REPORTS'		,      3);

define('CWnd_RECURSIVE_NONE'	,      0);
define('CWnd_RECURSIVE_ALL'		,      1);
define('CWnd_RECURSIVE_FOLDERS'	,      2);

require $gs_root.'/lib/CWndMenu.php';
require $gs_root.'/lib/CValidate.php';
require $gs_root.'/lib/CDvdColAct.php';
require $gs_root.'/lib/CDvdColSql.php';
require $gs_root.'/lib/CNavi.php';

//DVDAF3_PRES_DVD_MULTI
//DVDAF3_PRES_DVD_PRINT
//DVDAF3_PRES_DVD_ONE
//DVDAF3_PRES_PRICE_MULTI
//DVDAF3_PRES_PRICE_ONE

class CDvdList extends CWndMenu
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

		// Context
		$this->ms_list_kind			= CWnd_LIST_OTHER;
		$this->mn_show_mode			= DVDAF3_PRES_DVD_MULTI;
		$this->ms_pres_mode			= '';
		$this->mn_recursive			= dvdaf3_getvalue('rc'  , DVDAF3_GET|DVDAF3_INT, 0, 2); // CWnd_RECURSIVE_NONE CWnd_RECURSIVE_ALL CWnd_RECURSIVE_FOLDERS
		$this->mb_valid_view		= true;
		$this->mb_valid_folder		= true;
		$this->mn_dvd_id			= 0;		// only used in single page modes
		$this->mn_first_row_no		= 1;
		$this->mn_rows_shown		= 0;

		// Facebook
		$this->mb_facebook_div			= true;

		// Presentation
		$this->mb_long_titles		= dvdaf3_getvalue('longtitles', DVDAF3_COOKIE|DVDAF3_BOOLEAN);
		$this->mb_show_release_dt	= false;

		// Javascript Features
		$this->mb_context_menu		= true;
		$this->mb_cart_handlers		= true;
		$this->mn_echo_zoom			= CWnd_ZOOM_STAR;
		$this->mn_open_edit			= dvdaf3_getvalue('edit', DVDAF3_GET|DVDAF3_INT, 0, 1);

		// Location
		$this->ms_folder			= '';	// same as folder
		$this->ms_clean_uri			= preg_replace('/[&?](pm|rc|pg|edit)=[0-9a-z]+/i', '', str_replace('&amp;', '&', dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER|DVDAF3_NO_AMP_EXPANSION)));
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
		$this->mn_max_listings		= 10000;
		$this->mn_req_page_size		= 200;	// initialized from cookies in verifyUser()
		$this->mn_page_size			= 50;	// initialized in verifyUser()
		$this->mn_max_page			= 200;	// initialized in verifyUser()
		$this->mn_page_number		= 1;	// initialized from get in verifyUser()

		// Extra Conditions (modal or pinned)
		$this->ms_extra_js_config	= '';
		$this->ms_collection_cond	= '';
		$this->mn_exclude_mine		= 0;
		$this->ms_in_collection		= '';

		switch ( dvdaf3_getvalue('pm', DVDAF3_COOKIE|DVDAF3_LOWER) )
		{
		case 'prn':
			$this->mn_show_mode		= DVDAF3_PRES_DVD_PRINT;
			$this->ms_pres_mode		= 'prn';
			$this->mb_cart_handlers	= false;
			$this->mb_context_menu	= false;
			$this->mb_advert		= false;
			break;
		case 'one':
			$this->mn_show_mode		= DVDAF3_PRES_DVD_ONE;
			$this->ms_pres_mode		= 'one';
			break;
		}
	}

	function getFooterJavaScript()
	{
		$s_user   = $this->mb_logged_in  ? $this->ms_user_id : '';
		$s_view   = $this->mb_collection ? $this->ms_view_id : '';
		$s_one    = $this->mn_show_mode      == DVDAF3_PRES_DVD_ONE ? 'One' : 'Many';
		$s_page   = ( $this->mn_show_mode    == DVDAF3_PRES_DVD_ONE && $this->mb_view_self	) ? 'DvdMine.checkAndEdit();' : (
					( $this->mn_show_mode    == DVDAF3_PRES_DVD_MULTI						) ? 'PageSize.attach("sz_page_0");PageSize.attach("sz_page_1");' : '');
		$s_config = '{baseDomain:"'.		$this->ms_base_subdomain.'"'.
					($this->mn_echo_zoom ? ',preloadImgPop:1' : '').
					',userCollection:"'.	$s_user.'"'.
					',viewCollection:"'.	$s_view.'"'.
					',onPopup:DvdListMenuPrep.onPopup'.
					',objId:'.				$this->mn_dvd_id.
					',presentationMode:"'.	$this->ms_pres_mode.'"'.
					',reqPageSize:'.		$this->mn_req_page_size.
					',firstRowNo:'.			$this->mn_first_row_no.
					',rowsShown:'.			$this->mn_rows_shown.
					',optionsTag:"user_collection"'.
					',cartHandlers:'.		($this->mb_cart_handlers																	  ? 1 : 0).
					',ulDvd:'.				($this->mb_context_menu																		  ? 1 : 0).
					",ulDvd{$s_one}:1".
					',ulExplain:1'.
					',ulGenre:'.			($this->mb_view_self && $this->mn_show_mode == DVDAF3_PRES_DVD_ONE							  ? 1 : 0).
					',ulJump:1'.
					',ulPageSize:'.			($this->mn_show_mode != DVDAF3_PRES_DVD_ONE && $this->mn_show_mode != DVDAF3_PRES_PRICE_MULTI ? 1 : 0).
					',ulStars:'.			($this->mb_view_self && $this->mn_show_mode == DVDAF3_PRES_DVD_ONE							  ? 1 : 0).
					',imgPreLoad:"pin.cart.price.help.explain.spin.drop.pagesize.spun.home.coll"'.
					$this->ms_extra_js_config.
					'}';
		return
					"function onMenuClick(action){DvdListMenuAction.onClick(action);};".
					"Filmaf.config({$s_config});".
					"setTimeout('".
						"Menus.setup();".
						"DvdList.synchLongTitles();".
						"DvdList.selectIfOne();".
						"Jump.attach(\"dp_jump_0\",null);".
						"Jump.attach(\"dp_jump_1\",null);".
						$s_page.
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
								 $this->mn_show_mode == DVDAF3_PRES_DVD_ONE ? 1 : ($this->mn_show_mode == DVDAF3_PRES_DVD_PRINT ? 1000 : 0),
								 1,
								 $this->mn_req_page_size,
								 $this->mn_page_size,
								 $this->mn_max_page,
								 $this->mn_page_number);
	}

	function badRequester()
	{
		if ( strpos(dvdaf3_getvalue('HTTP_USER_AGENT', DVDAF3_SERVER|DVDAF3_LOWER),'googlebot') !== false )
		{
			if ( $this->mn_show_mode != DVDAF3_PRES_DVD_MULTI ) return true;
			$s_path = explode('?', substr(dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER | DVDAF3_NO_AMP_EXPANSION),1));
			$s_path = preg_replace('/\/+$/', '', str_replace('%20', ' ', $s_path[0]));
			if ( $s_path != 'owned' ) return true;
		}
		return false;
	}

///////////////////////////////////////////////////////////////////////
// Validation and SQL

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		if ( ! $this->mb_logged_in ) return;

		if ( $this->mb_view_self && $this->mn_show_mode == DVDAF3_PRES_DVD_ONE )
			$this->ms_include_js = "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-dvd-mine_{$this->mn_lib_version}.js'></script>\n";

		switch ( dvdaf3_getvalue('act', DVDAF3_POST|DVDAF3_LOWER) )
		{
		case 'move': CDvdColAct::moveDvd  (CDvdColAct::getDvdList(DVDAF3_POST,','), $this->ms_user_id, $this->ms_display_affected, $this->ms_display_error, $this->ms_unatrib_subdomain, ''); break;
		case 'del':  CDvdColAct::deleteDvd(CDvdColAct::getDvdList(DVDAF3_POST,','), $this->ms_user_id, $this->ms_display_affected, $this->ms_display_error); break;
		case 'edit': CDvdColAct::editDvd  ($this->ms_user_id, $this->ms_display_affected, $this->ms_display_error); break;
		default:	 return;
		}
	}

    function ammendSqlQuery(&$s_select, &$s_from, &$s_where, &$s_sort, $b_skip_pins)
    {
		CDvdColSql::ammendSqlQuery(	$s_select, $s_from, $s_where, $s_sort, $this->ms_collection_cond, $this->mn_exclude_mine, $this->ms_display_what,
									$this->mb_collection, $this->mb_logged_in, $this->mb_view_self, $this->mn_show_mode, $this->mn_recursive,
									$this->mb_valid_view, $this->mb_valid_folder, $this->ms_folder, $this->ms_user_pinned, $b_skip_pins);
	}

	function sqlQuery($s_select, $s_from, $s_where, $s_sort, $n_begin, $n_count, $b_edit)
	{
//echo CDvdColSql::sqlQuery($s_select, $s_from, $s_where, $s_sort, $n_begin, $n_count, $b_edit, $this->ms_collection_cond, $this->mb_collection, $this->mb_logged_in, $this->mb_view_self, $this->ms_user_id, $this->mn_exclude_mine);
		return CDvdColSql::sqlQuery($s_select, $s_from, $s_where, $s_sort, $n_begin, $n_count, $b_edit, $this->ms_collection_cond,
									$this->mb_collection, $this->mb_logged_in, $this->mb_view_self, $this->ms_user_id, $this->mn_exclude_mine);
	}

///////////////////////////////////////////////////////////////////////
// Draw

	function drawBodyPage()
	{
		if ( ! $this->mb_collection && $this->mn_max_listings > 10000 )
		{
			$this->mn_max_listings = 10000;
			if ( $this->mn_page_number * $this->mn_page_size > $this->mn_max_listings )
				$this->mn_page_number = 1;
		}

		switch ( $this->mn_show_mode )
		{
		case DVDAF3_PRES_DVD_ONE:
			$this->drawBodyPage_One();
			break;
		case DVDAF3_PRES_DVD_MULTI:
		case DVDAF3_PRES_DVD_PRINT:
			$this->drawBodyPage_Many();
			break;
		}
	}

	function drawContext()
	{
		if ( ! $this->mb_collection ) return;
		if ( ! $this->mb_valid_view )
		{
			echo "<div id='colnav'>A Film collection for ". ucfirst($this->ms_view_id). " was not found.</div>";
			return;
		}

		$a_folders = null;
		$s_star    = '';

		if ( $this->mb_view_self )
		{
			$a_folders = &$this->ma_user_folders;
		}
		else
		{
			CDvdUtils::getFolders($a_folders, $this->ms_view_id, $this->ms_user_id, true);
			$this->mb_valid_view = count($a_folders) > 0;
		}

		if ( $this->getViewStars() > 0 )
		{
			$s_star .=  "<p>".
						  "<img src='http://dv1.us/s1/smb{$this->mn_view_stars}.png' /> ".
						  ucfirst($this->ms_view_id) ." is a FilmAf ".
						  "<a href='{$this->ms_base_subdomain}/thank-you.html'>". dvdaf3_stardescription($this->mn_view_stars). "</a>".
						"</p>";
		}

		$b_hide_links = $this->mb_collection && $this->ms_view_id == $this->ms_user_id && dvdaf3_getvalue('linksonwed',DVDAF3_COOKIE|DVDAF3_BOOLEAN);
		echo  "<div id='colnav'>".
				"Welcome to <a href='http://{$this->ms_view_id}{$this->ms_unatrib_subdomain}'>". ucfirst($this->ms_view_id .(substr($this->ms_view_id,-1) == 's' ? '&#039;' : '&#039;s')). "</a> Film Collection".
				$s_star.
				($b_hide_links ? '' : CDvdUtils::makeColNav($a_folders, $this->ms_folder, $this->mn_recursive != 0)).
			  "</div>";
	}

	function drawMessagesTot($n_row_begin, $n_row_end, $n_row_total, $n_total_titles, $n_total_disks, $b_validate, $b_explain, $s_msg, $s_error, $b_empty)
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
				$this->drawPageNav($n_row_begin, $n_row_end, $n_row_total, $n_total_titles, $n_total_disks, $b_validate, $b_explain, $this->mn_show_mode != DVDAF3_PRES_PRICE_MULTI);
				echo  "</div>";
			}

			if ( $s_msg )
				echo "<div id='msg-only'>{$s_msg}</div>";
		}
	}

///////////////////////////////////////////////////////////////////////
// Draw Many

	function drawResults_Many($n_row_begin, $n_row_end, $n_row_total, $n_total_titles, $n_total_disks, $rr)
	{
		$b_print = $this->mn_show_mode == DVDAF3_PRES_DVD_PRINT;
		$b_hide_best_price = $this->mb_collection && $this->ms_view_id == $this->ms_user_id && substr($this->ms_folder,0,5) == 'owned' && ! dvdaf3_getvalue('bestonwed',DVDAF3_COOKIE|DVDAF3_BOOLEAN);
		$n_parm	 = ((  $this->getUserStars() > 0	) ? DVDAF3_FLG_STARMEMBER	  : 0) |
				   ((  $this->mb_collection			) ? DVDAF3_FLG_COLLECTION	  : 0) |
				   ((  $b_hide_best_price			) ? DVDAF3_FLG_HIDEBESTPRICE  : 0) |
				   ((! $this->mb_collection			) ? DVDAF3_FLG_NOCOMMENT	  : 0) |
				   ((! $this->mb_collection			) ? DVDAF3_FLG_SKIPOUTOFPRINT : 0) |
				   ((  $this->mb_show_release_dt	) ? DVDAF3_FLG_SHOWRELEASEDT  : 0) |
				   ((  $this->mb_long_titles		) ? DVDAF3_FLG_EXPANDTITLE	  : 0);
		CTrace::log_var('drawResults_Many:n_parm', $n_parm);

		echo  "<form id='f_act' name='f_act' method='post' action=''>".
				"<input type='hidden' name='act' value='' />".
				"<input type='hidden' name='sub' value='' />".
				"<input type='hidden' name='tar' value='' />".
			  "</form>".
			  "<form id='f_list' name='f_list' action=''>".
				"<table class='".($b_print ? 'prn' : 'dvd')."_table'>".
				  "<thead>";
					$this->drawTableMenu(6);
		echo	  "</thead>".
				  "<tbody>";

					$this->mn_rows_shown = 0;
					for ( $n_line_number = $n_row_begin, $i = 0  ;  $n_line_number <= $n_row_end && $a_line = CSql::fetch($rr)  ;  $n_line_number++, $i++ )
					{
						echo dvdaf3_getbrowserow($a_line, $this->mn_show_mode, $n_parm | ($i % 2 ? DVDAF3_FLG_HIGHLIGHTROW : 0), 0, 0, 0, 0, $n_line_number, $n_row_total, $this->ms_view_id);
						$this->mn_rows_shown++;
					}

		echo	  "</tbody>".
				"</table>".
				($n_line_number - $n_row_begin > 4 ? "<div id='nav_bop'></div>" : '').
			  "</form>";
	}

	function drawBodyPage_Many()
	{
		// prepare query
		switch ( $this->mn_show_mode )
		{
		case DVDAF3_PRES_DVD_MULTI:
			$s_select = "a.dvd_id, a.pic_status, a.pic_name, a.pic_count, b.pic_overwrite, a.dvd_title, a.film_rel_year, b.comments, ".
						"b.user_film_rating, b.user_dvd_rating, a.genre, b.genre_overwrite, a.region_mask, a.dvd_rel_dd, b.folder, ".
						"a.media_type, a.source, a.imdb_id, a.asin, a.amz_country, a.list_price, a.director, a.publisher, a.country, ".
						"a.rel_status, a.best_price";
			break;
		case DVDAF3_PRES_DVD_PRINT:
			$s_select = "a.dvd_id, a.dvd_title, a.film_rel_year, a.region_mask, a.media_type, a.source, a.director, a.publisher, a.country";
			break;
		}
		if ( $this->mb_collection )
		{
			$s_from   = "my_dvd b JOIN dvd a ON b.dvd_id = a.dvd_id";
			$s_where  = "b.user_id = '{$this->ms_view_id}' and b.folder = '{$this->ms_folder}'";
		}
		else
		{
			$s_from   = "dvd a";
			$s_where  = '';
		}
		$s_from		 .= $this->ms_in_collection;
		$s_sort		  = "a.dvd_title_nocase, a.film_rel_year, a.director_nocase, a.dvd_id";
		$this->ammendSqlQuery($s_select, $s_from, $s_where, $s_sort, false);

		// execute query
		$n_row_begin	= ($this->mn_page_number - 1) * $this->mn_page_size;
		$ss				= $this->sqlQuery('count(*) results, sum(a.num_titles) titles, sum(a.num_disks) disks', $s_from, $s_where, '', 0, 0, false);
		// echo "<div>{$ss}</div>";
		$rr				= CSql::query_and_fetch($ss,0,__FILE__,__LINE__);
		$n_row_total	= $rr['results'];
		$n_total_titles	= $rr['titles'];
		$n_total_disks	= $rr['disks'];
		$n_row_begin++;
		$n_row_end		= $n_row_begin + min($n_row_total - $n_row_begin, $this->mn_page_size - 1);

		$s_msg			= '';
		$s_error		= '';
		$b_no_results	= true;
		$b_empty		= false;
		if ( $n_row_end >= $n_row_begin )
		{
			$ss = $this->sqlQuery($s_select, $s_from, $s_where, $s_sort, $n_row_begin, $this->mn_page_size, true);
			// echo "<div>{$ss}</div>";
			if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
				$b_no_results = false;
			else
				$s_error = "We encountered a problem. ".$this->getWaitAndRetry();
		}
		else
		{
			if ( $n_row_total )
				$s_msg = 'Nothing to show. You may be past the last page.';
			else
				$b_empty = true;
		}

		// draw
		$this->mn_first_row_no = $n_row_begin;
		$this->drawContext();
		$this->drawMessagesTot($n_row_begin, $n_row_end, $n_row_total, $n_total_titles, $n_total_disks, false, true, $s_msg, $s_error, $b_empty);
		if ( $b_no_results )
		{
			if ( $b_empty ) $this->drawEmptyFolder();
		}
		else
		{
			$this->drawResults_Many($n_row_begin, $n_row_end, $n_row_total, $n_total_titles, $n_total_disks, $rr);
			CSql::free($rr);
		}
	}

	function drawTableMenu($n_cols)
	{
		switch ( $this->mn_show_mode )
		{
		case DVDAF3_PRES_DVD_MULTI:
		case DVDAF3_PRES_PRICE_MULTI:
			echo
			  "<tr>".
				"<td width='1%' align='center'><a href='javascript:void(0)' onclick='DvdList.uncheckAll(this)' class='me' title='Clear selection'>clr</a></td>".
				"<td width='99%' colspan='".($n_cols - 1)."'>".
				  "&nbsp;".
				  ( $this->mb_logged_in ?
					  "Move&nbsp;selected&nbsp;to:&nbsp;".
					  "<select class='mb' id='sel_folder'>".
						"<option value='owned' selected='selected'>owned</option>".
						"<option value='on-order'>on-order</option>".
						"<option value='wish-list'>wish-list</option>".
						"<option value='work'>work</option>".
						"<option value='have-seen'>have-seen</option>".
					  "</select>&nbsp;".
					  "<a href='javascript:void(DvdList.dvdAction(\"f_list\",0,\"move\",0))' title='Move selected titles to chosen folder'>Move</a>&nbsp; ".
					  "<a href='javascript:void(DvdList.dvdAction(\"f_list\",0,\"delete\",0))' title='Delete selected titles from my collection'>Delete&nbsp;from&nbsp;my&nbsp;Collection</a>&nbsp;" : '').
				  " ".
				  "<a href='javascript:void(0)' onclick='DvdList.cartAdd(this)' class='me' title='Add selected titles to shopping cart'>Add&nbsp;to&nbsp;cart</a>&nbsp; ".
				  "<a href='javascript:void(0)' onclick='DvdList.cartDel(this)' class='me' title='Remove selected titles from shopping cart'>Remove&nbsp;from&nbsp;cart</a>".
				"</td>".
			  "</tr>";

			if ( $this->mn_show_mode == DVDAF3_PRES_DVD_MULTI )
				echo
			  "<tr>".
				"<td width='1%' align='center'><input type='checkbox' id='cb_all' onclick='DvdList.checkAll(this.form)' title='Select or unselect all' /></td>".
				"<td width='1%' align='center'>Picture</td>".
				"<td width='60%'>".
				  "<table id='exp_table'>".
					"<tr>".
					  "<td>Title</td>".
					  "<td id='exp_cb'><input id='cb_longtitles' type='checkbox' onclick='DvdList.setLongTitles(this.checked)' /></td>".
					  "<td id='exp_txt'>expand longer titles</td>".
					"</tr>".
				  "</table>".
				"</td>".
				"<td width='12%'>Links</td>".
				"<td width='18%'>Director</td>".
				"<td width='18%'>Publisher</td>".
			  "</tr>";
			break;

		case DVDAF3_PRES_DVD_PRINT:
			echo
			  "<tr class='prn_hdr'>".
				"<td width='1%' align='center'>&nbsp;</td>".
				"<td width='1%' align='center'>&nbsp;</td>".
				"<td width='60%'>Title</td>".
				"<td width='19%'>Director</td>".
				"<td width='19%'>Publisher</td>".
			  "</tr>";
			break;
		}
	}

///////////////////////////////////////////////////////////////////////
// Draw One

	function drawResults_One($n_dvd_id, &$a_line)
	{
		$this->mn_dvd_id	= $a_line['dvd_id'];
		$b_big_picture		= $this->getViewStars() > 0 || $this->getUserStars() > 0;
		if ( $b_big_picture ) $this->mn_echo_zoom = CWnd_ZOOM_NONE;

		$n_parm		 = (($b_big_picture											) ? DVDAF3_FLG_HIGHRES	  : 0) |
					   (($this->getUserStars() > 0								) ? DVDAF3_FLG_STARMEMBER : 0);
					   	
		if ( $this->mb_collection )
		{
			$this->setDefaultForMyDvd2($a_line);
			$a_line['SubmmitUrl'] = str_replace('&amp;', '&', dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER|DVDAF3_NO_AMP_EXPANSION));
			$n_parm |= (($this->mb_collection									) ? DVDAF3_FLG_COLLECTION : 0) |
					   (($this->mb_view_self && $this->ms_folder != 'trash-can'	) ? DVDAF3_FLG_DOINPUT	  : 0) |
					   (($this->mn_open_edit									) ? DVDAF3_FLG_INITTOPEN  : 0);
		}
		CTrace::log_var('drawResults_One:n_parm', $n_parm);

		echo dvdaf3_getbrowserow($a_line, $this->mn_show_mode, $n_parm, 0, 0, 0, 0, 0, 0, $this->ms_view_id);
	}

	function drawBodyPage_One()
	{
		// prepare query
		if ( $this->mb_collection )
		{
			$s_select = "a.dvd_id, a.director, a.publisher, a.country, a.source, a.media_type, a.region_mask, a.film_rel_year, a.film_rel_dd, ".
						"a.orig_language, a.genre, a.rel_status, a.dvd_rel_dd, a.dvd_oop_dd, a.imdb_id, a.asin, a.amz_country, a.num_titles, ".
						"a.num_disks, a.best_price, a.upc, a.list_price, a.sku, a.pic_status, a.pic_name, a.pic_count, b.pic_overwrite, ".
						"a.dvd_title, b.genre_overwrite, b.comments, b.user_dvd_rating, b.user_film_rating, b.sort_text, b.owned_dd, ".
						"b2.last_watched_dd, b2.price_paid, b2.trade_loan, b2.loaned_to, b2.loan_dd, b2.return_dd, b2.asking_price, ".
						"b2.retailer, b2.order_dd, b2.order_number, b2.custom_1, b2.custom_2, b2.custom_3, b2.custom_4, b2.custom_5";
			$s_from   = "my_dvd b JOIN dvd a ON b.dvd_id = a.dvd_id LEFT JOIN my_dvd_2 b2 ON b2.dvd_id = b.dvd_id and b2.user_id = b.user_id";
			$s_where  = "b.user_id = '{$this->ms_view_id}' and b.folder = '{$this->ms_folder}'";
			$s_sort   = "a.dvd_title_nocase, a.film_rel_year, a.director_nocase, a.dvd_id";
		}
		else
		{
			$s_select = "a.dvd_id, a.director, a.publisher, a.country, a.source, a.media_type, a.region_mask, a.film_rel_year, a.film_rel_dd, ".
						"a.orig_language, a.genre, a.rel_status, a.dvd_rel_dd, a.dvd_oop_dd, a.imdb_id, a.asin, a.amz_country, a.best_price, ".
						"a.num_titles, a.num_disks, a.upc, a.list_price, a.sku, a.pic_status, a.pic_name, a.pic_count, b.pic_overwrite, ".
						"a.dvd_title, b.genre_overwrite";
			$s_from   = "dvd a";
			$s_where  = '';
			$s_sort   = "a.dvd_title_nocase, a.film_rel_year, a.director_nocase, a.dvd_id";
		}
		$s_from		 .= $this->ms_in_collection;
		$this->ammendSqlQuery($s_select, $s_from, $s_where, $s_sort, false);

		// execute query
		$n_row_begin	= ($this->mn_page_number - 1) * $this->mn_page_size;
		$rr				= CSql::query_and_fetch($this->sqlQuery('count(*) results, sum(a.num_titles) titles, sum(a.num_disks) disks', $s_from, $s_where, '', 0, 0, false), 0,__FILE__,__LINE__);
		$n_row_total	= $rr['results'];
		$n_total_titles	= $rr['titles'];
		$n_total_disks	= $rr['disks'];

		$s_msg			= '';
		$s_error		= '';
		$b_no_results	= true;
		$b_empty		= false;
		if ( $n_row_total > 0 )
		{
			$n_row_begin++;
			$n_row_end	= $n_row_begin + min($n_row_total - $n_row_begin, $this->mn_page_size - 1);
			$rr			= CSql::query_and_fetch($this->sqlQuery('a.dvd_id', $s_from, $s_where, $s_sort, $n_row_begin, $this->mn_page_size, false), 0,__FILE__,__LINE__);
			$n_dvd_id	= $rr['dvd_id'];
			if ( $n_dvd_id > 0 && ($rr = CSql::query_and_fetch($this->sqlQuery($s_select, $s_from, $s_where . " and a.dvd_id = {$n_dvd_id}", '', 1, 1, true), 0,__FILE__,__LINE__)) )
				$b_no_results = false;
			else
				$s_error = "We encountered a problem. ".$this->getWaitAndRetry();
		}
		else
		{
			if ( $n_row_total )
				$s_msg = 'Nothing to show. You may be past the last page.';
			else
				$b_empty = true;
		}

		// draw
		$this->mn_first_row_no = $n_row_begin;
		$this->drawContext();
		$this->drawMessagesTot($n_row_begin, $n_row_begin, $n_row_total, $n_total_titles, $n_total_disks, $this->mb_view_self, false, $s_msg, $s_error, $b_empty);
		if ( $b_no_results )
		{
			if ( $b_empty ) $this->drawEmptyFolder();
		}
		else
		{
			$this->drawResults_One($n_dvd_id, $rr);
		}
	}

	function setDefaultForMyDvd2(&$a_line)
	{
		if ( $a_line['trade_loan'] == '' )
		{
			$a_line['last_watched_dd']	= '-';
			$a_line['price_paid']		= -1;
			$a_line['trade_loan']		= '-';
			$a_line['loaned_to']		= '-';
			$a_line['loan_dd']			= '-';
			$a_line['return_dd']		= '-';
			$a_line['asking_price']		= -1;
			$a_line['retailer']			= '-';
			$a_line['order_dd']			= '-';
			$a_line['order_number']		= '-';
			$a_line['custom_1']			= '-';
			$a_line['custom_2']			= '-';
			$a_line['custom_3']			= '-';
			$a_line['custom_4']			= '-';
			$a_line['custom_5']			= '-';
		}
	}

///////////////////////////////////////////////////////////////////////

	function drawEmptyFolder()
	{
		$s_margin = '100px';
		$s_text   = "Hi, your query returned no results.";
		switch ( $this->ms_list_kind )
		{
		case CWnd_LIST_DVDS:
			if ( ! $this->mb_valid_view )
				$s_text = "A Film collection for ". ucfirst($this->ms_view_id). " was not found.";
			else
				if ( $this->ms_folder )
					$s_text = $this->getOnEmptyFolderList($s_margin);
			break;
		case CWnd_LIST_LISTS:
		case CWnd_LIST_OTHER:
		case CWnd_LIST_REPORTS:
			break;
		}
		echo "<div style='padding: 20px 10px {$s_margin} 10px;'>".
			   $s_text.
			 "</div>";
	}

	function getOnEmptyFolderList(&$s_margin)
	{
		if ( $this->ms_view_id && $this->mb_collection )
		{
			$s_str = '';
			if ( ($rr  = CSql::query("SELECT f.folder, f.sort_category, f.sort_order, count(*) count ".
									   "FROM ".($this->mb_view_self ? 'my_dvd b ' : 'v_my_dvd_pub b ').
									   "JOIN ".($this->mb_view_self ? 'my_folder' : 'v_my_folder_pub')." f ON b.user_id = f.user_id and b.folder = f.folder ".
									  "WHERE b.user_id = '{$this->ms_view_id}' ".
									  "GROUP BY f.folder, f.sort_category, f.sort_order ".
									 "HAVING count(*) > 0 ".
									  "ORDER BY sort_category, sort_order, folder", 0,__FILE__,__LINE__)) )
			{
				while ( $a_line = CSql::fetch($rr) )
				{
					$s_folder = $a_line['folder'];
					$n_count  = $a_line['count'];
					$s_trash  = $s_folder == 'trash-can' ? ' items in the trash can are automatically purged after 7 days.' : '';
					$s_str   .= "<div><a href='/{$s_folder}'>{$s_folder}</a><span class='dvd_cmts'> ({$n_count})$s_trash</span></div>";
				}
				CSql::free($rr);
			}

			if ( $s_str )
			{
				$s_margin = '20px';
				return ($this->mb_valid_folder ? 'Sorry, this folder is empty.' : 'Sorry, the folder requested does not exist.').
					   '<br />&nbsp;<br />'.
					   'Please use the menu above or browse through the following folders to navigate this collection:'.
					   '<br />&nbsp;<br />'.
					   "<div style='margin:0 0 0 20px'>".
						 "<div style='margin-bottom:10px'><a href='/'>Collection statistics</a></div>".
						 $s_str.
					   "</div>";
			}
		}

		return "Sorry, ".ucfirst($this->ms_view_id)." has not entered his/hers DVDs yet.";
	}

	function drawPageNav($n_beg, $n_end, $n_tot, $n_total_titles, $n_total_disks, $b_validate, $b_explain, $b_flip_modes)
	{
		CDvdUtils::drawPageNav(	$this->mn_page_size,
								$this->mn_req_page_size,
								$this->mn_show_mode == DVDAF3_PRES_DVD_MULTI,
								$this->mn_max_page,
								$this->mn_max_listings,
								$this->ms_clean_uri,
								($this->mn_recursive ? "&rc={$this->mn_recursive}" : '').($this->mn_open_edit ? "&edit={$this->mn_open_edit}"  : ''),
								$b_validate ? 'DvdMine.validate(0)' : '',
								true,
								$n_tot,
								$n_beg,
								$n_end,
								$n_total_titles,
								$n_total_disks,
								$b_explain,
								$this->ms_pres_mode,
								$b_flip_modes,
								$this->ms_base_subdomain);
	}

	function catUrl($s_parm)
	{
		if ( $s_parm )
			return $this->ms_clean_uri . ($this->mb_clean_uri_parm ? '&' : '?'). substr($s_parm,1);
		else
			return $this->ms_clean_uri;
	}

	function validRequest() // <<-------------------------------<< 4.0
	{
		$s_url  = dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER|DVDAF3_NO_AMP_EXPANSION);
		$s_path	= explode('?', substr($s_url,1));
		$s_path = preg_replace('/\/+$/', '', $s_path[0]);
		$a_path	= explode('/', $s_path);

		switch( $a_path[0] )
		{
		case 'owned':
		case 'on-order':
		case 'wish-list':
		case 'work':
		case 'have-seen':
		case 'trash-can':
			if ( $this->ms_view_id == 'www' )
				return false;
			$this->ms_folder		= $s_path;
			$this->ms_list_kind		= CWnd_LIST_DVDS;
			break;
		case 'reports':
			$this->ms_folder		= $s_path;
			$this->ms_list_kind		= CWnd_LIST_LISTS;
			break;
		case 'lists':
			$this->ms_folder		= $s_path;
			$this->ms_list_kind		= CWnd_LIST_REPORTS;
			break;
		default:
			$this->ms_folder		= '';
			$this->ms_list_kind		= CWnd_LIST_OTHER;
			return false;
		}
		return true;
	}
}

/*
	function drawHead_Pda()
	{
		echo
		"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'DTD/xhtml1-transitional.dtd'>\n".
		"<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>\n".
		"<head>\n".
		  "<title>Film Aficionado{$this->ms_title}</title>\n".
		  "<meta name='HandheldFriendly' content='True' />\n".
		  "<meta http-equiv='Content-type' content='text/html; charset=UTF-8' />\n".
		"</head>\n";
	}

	function drawBodyBottom_Pda()
	{
		echo "<hr />{$this->ms_copyright}.";
	}
*/
?>
