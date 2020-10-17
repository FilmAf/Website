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

require $gs_root.'/lib/CWndMenu.php';
require $gs_root.'/lib/CDvdColAct.php';
require $gs_root.'/lib/CDvdColSql.php';

class CUpcImport extends CWndMenu
{
	function constructor()
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
		$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-import_{$this->mn_lib_version}.js'></script>\n";
		$this->mn_echo_zoom			= CWnd_ZOOM_STAR;
		$this->ms_folder			= dvdaf3_getvalue('n_folder',DVDAF3_POST|DVDAF3_LOWER	);
		$this->mb_sort				= dvdaf3_getvalue('cb_sort'	,DVDAF3_POST|DVDAF3_BOOLEAN	);
		$this->ms_prefix			= dvdaf3_getvalue('n_prefix',DVDAF3_POST|DVDAF3_LOWER	);
		$this->mn_next				= dvdaf3_getvalue('n_next'	,DVDAF3_POST|DVDAF3_INT		);
		$this->mn_inc				= dvdaf3_getvalue('n_inc'	,DVDAF3_POST|DVDAF3_INT		);
		$this->ms_upc				= preg_replace('/[^0-9,]/','',dvdaf3_getvalue('n_upc',DVDAF3_POST|DVDAF3_LOWER));
		$this->mn_dvd_id			= dvdaf3_getvalue('n_dvd_id',DVDAF3_POST|DVDAF3_INT		);
		$this->ms_matches			= '';
		$this->mn_matches			= -3;

		switch ( substr($this->ms_folder,0,4) )
		{
		case 'owne':
		case 'on-o':
		case 'wish':
		case 'work':
		case 'have':
			setcookie('move', $this->ms_folder, 0, '/', $this->ms_unatrib_subdomain, 0);
			break;
		}
	}

	function getFooterJavaScript()
	{
		$s_user   = $this->mb_logged_in  ? $this->ms_user_id : '';
		$s_view   = $this->mb_collection ? $this->ms_view_id : '';
		$s_one    = 'Many';
		$s_config = '{baseDomain:"'.		$this->ms_base_subdomain.'"'.
					',optionsTag:"user_collection"'.
					'}';
		return
					"function f_import(e){UpcImport.doImport(e)};".
					"Filmaf.config({$s_config});".
					"setTimeout('".
						"Menus.setup();".
						"UpcImport.setup();".
					"',100);";
	}

	function verifyUser()
	{
		parent::verifyUser();

		if ( $this->mn_access_level_cd <= 0 && $this->mn_echo_zoom == CWnd_ZOOM_STAR )
			$this->mn_echo_zoom = CWnd_ZOOM_NONE;
	}

///////////////////////////////////////////////////////////////////////
// Validation and SQL

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		if ( ! $this->mb_logged_in ) return;

		switch ( dvdaf3_getvalue('act', DVDAF3_POST|DVDAF3_LOWER) )
		{
		case 'move': CDvdColAct::moveDvd  (CDvdColAct::getDvdList(DVDAF3_POST,','), $this->ms_user_id, $this->ms_display_affected, $this->ms_display_error, $this->ms_unatrib_subdomain, ''); break;
		case 'del':  CDvdColAct::deleteDvd(CDvdColAct::getDvdList(DVDAF3_POST,','), $this->ms_user_id, $this->ms_display_affected, $this->ms_display_error); break;
		case 'edit': CDvdColAct::editDvd  ($this->ms_user_id, $this->ms_display_affected, $this->ms_display_error); break;
		default:	 return;
		}
	}

    function ammendSqlQuery(&$s_select, &$s_from, &$s_where, &$s_sort)
    {
		CDvdColSql::ammendSqlQuery(	$s_select, $s_from, $s_where, $s_sort, $this->ms_collection_cond, $this->mn_exclude_mine, $this->ms_display_what,
									false, $this->mb_logged_in, false, DVDAF3_PRES_DVD_MULTI, false, true, true, '', '', true);
	}

	function sqlQuery($s_select, $s_from, $s_where, $s_sort, $n_begin, $n_count, $b_edit)
	{
		return CDvdColSql::sqlQuery($s_select, $s_from, $s_where, $s_sort, $n_begin, $n_count, $b_edit, $this->ms_collection_cond,
									$this->mb_collection, $this->mb_logged_in, $this->mb_view_self, $this->ms_user_id, $this->mn_exclude_mine);
	}

///////////////////////////////////////////////////////////////////////
// Draw

	function drawBodyPage()
	{
		echo  "<h1>UPC Importer</h1>".
			  "<form id='f_list' name='f_list' method='post' action='".dvdaf3_getvalue('REQUEST_URI',DVDAF3_SERVER)."'>";
		if ( $this->ms_upc || $this->mn_dvd_id )
			$this->drawBodyPageResults();

		$this->drawBodyPageInput();
		echo  "</form>";
	}

	function drawBodyPageInput()
	{
		$s_visible	= $this->mb_sort   ? '' : 'visibility:hidden;';
		$s_sort		= $this->mb_sort   ? " checked='checked'" : '';
		$s_prefix	= $this->mn_next   ? $this->ms_prefix : 'a';
		$n_inc		= $this->mn_inc    ? $this->mn_inc  : '10';
		$n_next		= $this->mn_next   ? $this->mn_next + $n_inc : '1000';
		$s_upc		= ($this->mn_matches == -1 || $this->mn_matches == 0) ? $this->ms_upc : '';

		echo  "<table class='dlg_table' style='width:520px;'>".
				"<tr>".
				  "<td class='dlg_left' style='vertical-align:top;white-space:nowrap;width:1%'>".
					"Select destination folder:".
				  "</td>".
				  "<td class='dlg_right' style='vertical-align:top'>".
					"<select class='mb' id='sel_folder' name='n_folder' onchange='$(\"n_upc\").focus();'>".
					  "<option value='owned' selected='selected'>owned</option>".
					  "<option value='on-order'>on-order</option>".
					  "<option value='wish-list'>wish-list</option>".
					  "<option value='work'>work</option>".
					  "<option value='have-seen'>have-seen</option>".
					"</select>".
					"<div><input type='checkbox' id='cb_sort' name='cb_sort'{$s_sort} onclick='UpcImport.showSort()' /> Add custom sort</div>".
					"<div id='d_sort'></div>".
				  "</td>".
				"</tr>".
				"<tr>".
				  "<td class='dlg_left'>UPC:</td>".
				  "<td class='dlg_right'>".
					"<input type='text' id='n_upc' name='n_upc' size='20' maxlength='20' value='{$s_upc}' autocomplete='off' />".
					"<input type='hidden' id='n_dvd_id' name='n_dvd_id' value='' />".
					"<input type='hidden' id='o_prefix' value='{$s_prefix}' />".
					"<input type='hidden' id='o_next' value='{$n_next}' />".
					"<input type='hidden' id='o_inc' value='{$n_inc}' />".
					"<input type='submit' value='Import' />".
				  "</td>".
				"</tr>".
			  "</table>";
	}

	function checkResults()
	{
		if ( $this->mn_dvd_id )
		{
			$this->ms_matches = CSql::query_and_fetch1("SELECT dvd_id FROM dvd WHERE dvd_id = {$this->mn_dvd_id}",0,__FILE__,__LINE__);
			$this->mn_matches = strlen($this->ms_matches) > 0 ? 1 : 0;
		}
		else
		{
			if ( strlen($this->ms_upc) < 12 )
			{
				$this->ms_matches = '';
				$this->mn_matches = -1;
			}
			else
			{
				$this->ms_matches = CSql::query_and_fetch1("SELECT GROUP_CONCAT(dvd_id) FROM dvd WHERE upc like '%{$this->ms_upc}%'",0,__FILE__,__LINE__);
				$this->mn_matches = strlen($this->ms_matches) > 0 ? sizeof(explode(',',$this->ms_matches)) : 0;
			}
		}
	}

	function drawBodyPageResults()
	{
		$this->checkResults();
		switch ( $this->mn_matches )
		{
		case -1: $this->drawShotUPC();		break;
		case 0:  $this->drawNotFound();		break;
		case 1:  $this->drawMove();			break;
		default: $this->drawSelectWhich();	break;
		}
		echo "<div style='padding:10px 0 0 0'>&nbsp;</div>";
	}


	function drawShotUPC()			// -1
	{
		$this->ms_display_error =
			"Hi, &quot;{$this->ms_upc}&quot; is too short for an UPC. ".
			"They are usually at least 12 characters long.".
			"<div style='padding:12px 0 0 0'>".
			  "<img height='66' width='120' src='http://dv1.us/d1/upc-sample.gif' alt='UPC sample' />".
			"</div>";
		$this->drawMessages(true,false);
	}

	function drawNotFound()			// 0
	{
		$this->ms_display_error =
			"Hi, we did not find a title with a UPC &quot;{$this->ms_upc}".
			"&quot;. Please use the search above to find it by title, Amazon ".
			"ASIN, or imdb id. If you can still not find it please use the ".
			"&quot;My Contribution/Submit New DVD&quot; menu option.";
		$this->drawMessages(true,false);
	}

	function drawInvalidFolder()	// -2
	{
		$this->ms_display_error =
			"Hi, &quot;{$this->ms_folder}&quot; is not a valid folder in your collection.";
		$this->drawMessages(true,false);
	}

	function drawSelectWhich()		// n > 1
	{
		$this->drawAffected($this->ms_matches,
			"Hi, we found more than one listing with &quot;{$this->ms_upc}&quot; as an UPC. Please select which one you would like.");
	}

	function drawMove()				// 1
	{
		$s_user_id = $this->ms_user_id;
		$s_folder  = $this->ms_folder;
		$s_dvd_id  = $this->ms_matches;
		$s_sort    = ($this->mb_sort && $this->mn_next > 0 ) ? substr($this->ms_prefix,0,5) . sprintf('%06d',$this->mn_next) : '-';

		if ( CSql::query_and_fetch1("SELECT 1 FROM my_folder WHERE user_id = '{$s_user_id}' and folder = '{$s_folder}'", 0,__FILE__,__LINE__) )
		{
			$n_rows_1 = CSql::query_and_free(
						"UPDATE my_dvd SET folder = '$s_folder', sort_text = '{$s_sort}', my_dvd_updated_tm = now(), my_dvd_expire_tm = NULL ".
						 "WHERE user_id = '{$s_user_id}' and dvd_id = $s_dvd_id ",
							0,__FILE__,__LINE__);

			$n_rows_2 = CSql::query_and_free(
						"INSERT INTO my_dvd (user_id, dvd_id, folder, sort_text, my_dvd_created_tm, my_dvd_updated_tm) ".
						"SELECT u.user_id, a.dvd_id, '$s_folder', '{$s_sort}', now(), now() ".
						  "FROM dvd a, dvdaf_user u ".
						 "WHERE u.user_id = '{$s_user_id}' and a.dvd_id = $s_dvd_id ".
						   "and not exists (SELECT 1 FROM my_dvd b WHERE b.user_id = u.user_id and b.dvd_id = a.dvd_id)",
						0,__FILE__,__LINE__);
			CDvdColAct::recountDvds($s_user_id);

			CSql::query_and_free("UPDATE dvdaf_user_2 SET last_coll_tm = now() WHERE user_id = '{$s_user_id}'",0,__FILE__,__LINE__);

			if ( $n_rows_1 ) $s_display_affected = "$n_rows_1 listing".($n_rows_1 > 1 ? 's' : '')." updated<br />";  else
			if ( $n_rows_2 ) $s_display_affected = "$n_rows_2 listing".($n_rows_2 > 1 ? 's' : '')." inserted<br />"; else
							 $s_display_affected = "Sorry, your update operation did not affect any listings. Your DVD may already be in the selected folder.<br />";

			$this->drawAffected($s_dvd_id, $s_display_affected);
		}
		else
		{
			$this->mn_matches = -2;
			$this->drawAffected('','');
		}
	}

	function drawAffected($s_dvd_id_set, $s_display_affected)
	{
		$this->ms_display_affected = $s_display_affected;
		$this->drawMessages(true,false);

		$s_select = "a.dvd_id, a.pic_status, a.pic_name, a.pic_count, b.pic_overwrite, a.dvd_title, a.film_rel_year, b.comments, ".
					"b.user_film_rating, b.user_dvd_rating, a.genre, b.genre_overwrite, a.region_mask, a.dvd_rel_dd, b.folder, ".
					"a.media_type, a.source, a.imdb_id, a.asin, a.amz_country, a.list_price, a.director, a.publisher, a.country, ".
					"a.rel_status, a.best_price";
		$s_from   = "dvd a";
		$s_where  = $s_dvd_id_set ? "a.dvd_id in ({$s_dvd_id_set})" : "a.upc = '{$this->ms_upc}'";
		$s_sort	  = "a.dvd_title_nocase, a.film_rel_year, a.director_nocase, a.dvd_id";
		$this->ammendSqlQuery($s_select, $s_from, $s_where, $s_sort, false);

		$a_line   = array();
		$n_tot	  = 0;
		if ( ($rr = CSql::query($this->sqlQuery($s_select, $s_from, $s_where, $s_sort, 1, 100, true), 0,__FILE__,__LINE__)) )
		{
			while ( ($a_line[] = CSql::fetch($rr)) )
				$n_tot++;
			CSql::free($rr);
		}

		$n_parm	  = ($this->getUserStars() > 0 ? DVDAF3_FLG_STARMEMBER : 0) | ($n_tot > 1 ? 0 : DVDAF3_FLG_COL1_SKIP);

		if ( $n_tot > 0 )
		{
			echo  "<table class='dvd_table'>".
					"<thead>".
					  "<tr>".
						($n_tot > 1 ? "<td width='1%' align='center'>&nbsp;</td>" : '').
						"<td width='1%' align='center'>Picture</td>".
						"<td width='79%'>Title</td>".
						"<td width='20%'>Director</td>".
						"<td width='20%'>Publisher</td>".
					  "</tr>".
					"</thead>".
					"<tbody>";
					  for ( $i = 0  ;  $i < $n_tot  ;  $i++ )
						  echo dvdaf3_getbrowserow($a_line[$i], DVDAF3_PRES_DVD_UPC, $n_parm | ($i % 2 ? DVDAF3_FLG_HIGHLIGHTROW : 0), 0, 0, 0, 0, $i + 1, $n_tot, $this->ms_view_id);
			echo	"</tbody>".
				  "</table>";
		}
	}
}

?>
