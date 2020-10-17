<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetFavVideos extends CWidget
{
	function draw(&$wnd)
	{
		$s_act    = dvdaf3_getvalue('act', DVDAF3_GET);
		$s_title  = "About {$wnd->mws_ucview_id} / Favorite Videos";
		$s_subtit = '';
		$s_menu   = '';
		if ( $wnd->mwb_view_self )
		{
			switch ( $s_act )
			{
			case 'cat':
				$s_title .= " - Edit Categories";
				$s_menu   = "<a class='wga' href='javascript:void(FavVideosCat.validate())' title='Apply Changes'>Apply</a>&nbsp;&nbsp;&nbsp;".
							"<a class='wga' href='/?tab=favvideos' title='Done'>Done</a>";
				break;
			case 'vid':
				$s_title .= " - Edit Video Links";
				$s_menu   = "<a class='wga' href='javascript:void(FavVideosVid.validate())' title='Apply Changes'>Apply</a>&nbsp;&nbsp;&nbsp;".
							"<a class='wga' href='/?tab=favvideos' title='Done'>Done</a>";
				break;
			default:
				$s_subtit = "<p style='margin:4px 0 0 0'>Show off your likes and describe your Film Aficionado persona with your favorite videos&nbsp;&nbsp;:)</p>";
				$s_menu   = "<a class='wga' href='/?tab=favvideos&act=cat' title='Edit Categories'>Categories</a>".
							"&nbsp;&nbsp;&nbsp;".
							"<a class='wga' href='/?tab=favvideos&act=vid' title='Edit Videos'>Videos</a>";
				break;
			}
		}

		CWidget::drawHeader($s_title, $s_menu, $s_subtit);

		switch ( $s_act )
		{
			case 'cat':
				CWidgetFavVideos::drawCat($wnd);
				break;
			case 'vid':
				CWidgetFavVideos::drawVid($wnd);
				break;
			default:
				CWidgetFavVideos::drawFav($wnd);
				break;
		}
	}

	// ======================================================================
	function drawCat(&$wnd)
	{
		CWidgetFavVideos::getCategories($wnd->ms_view_id, $a_cats);
		$n_tot = count($a_cats);
		$s_opc = '';
		for ( $i = 0 ; $i < $n_tot ; $i++ )
			$s_opc .= "<option value='{$a_cats[$i]['cat_id']}'>{$a_cats[$i]['cat_name']}</option>";
		$s_opc .= "<option value='0'>-- Uncategorized --</option>";

		$a_row = array('cat_id' => 0, 'cat_name' => '', 'sort_order' => 0);

		for ( $i = 0 ; $i < 5 ; $i++ )
			$a_cats[] = $a_row;

		$n_tot = count($a_cats);
		$s_ops = '';
		for ( $i = 1 ; $i <= $n_tot ; $i++ )
			$s_ops .= "<option value='{$i}'>{$i}</option>";

		echo  "<table>".
				"<tr>".
				  "<td>".
					"<table id='inp_tbl'>".
					  "<thead>".
						"<tr>".
						  "<td>Delete</td>".
						  "<td>Category</td>".
						  "<td>Sort</td>".
						"</tr>".
					  "</thead>".
					  "<tbody>";

		for ( $i = 0 ; $i < $n_tot ; $i++ )
		{
			$a_row = &$a_cats[$i];
			$j = $i + 1;
			echo		"<tr>".
						  "<td style='text-align:center'>".
							"<input type='checkbox' id='n_del_{$j}' name='o_del_{$j}' />".
						  "</td>".
						  "<td>".
							"<input type='hidden' id='o_cat_id_{$j}' name='o_cat_id_{$j}' value='{$a_row['cat_id']}' />".
							"<input type='hidden' id='o_cat_name_{$j}' name='o_cat_name_{$j}' value='{$a_row['cat_name']}' />".
							"<input type='text' id='n_cat_name_{$j}' name='n_cat_name_{$j}' value='{$a_row['cat_name']}' />".
						  "</td>".
						  "<td>".
							"<input type='hidden' id='x_sort_{$j}' name='x_sort_{$j}' value='{$a_row['sort_order']}' />".
							"<input type='hidden' id='o_sort_{$j}' name='o_sort_{$j}' value='{$j}' />".
							"<select id='n_sort_{$j}' name='n_sort_{$j}' onchange='FavVideosCat.resort(this)'>".
							  str_replace("='{$j}'>", "='{$j}' selected='selected'>", $s_ops).
							"</select>".
						  "</td>".
						"</tr>";
		}
		echo		  "</tbody>".
					"</table>".
				  "</td>".
				  "<td style='vertical-align:top;padding:20px 20px 0 20px'>".
					"If deleting a category, move any videos in it to the following (do not select the same):".
					"<div style='padding:10px 0 0 20px'>".
					  "<select id='move_to_cat' name='move_to_cat'>".
						str_replace("='0'>", "='0' selected='selected'>", $s_opc).
					  "</select>".
					"</div>".
				  "</td>".
				"</tr>".
			  "</table>";
	}

	// ======================================================================
	function drawVid(&$wnd)
	{
		// categories
		CWidgetFavVideos::getCategories($wnd->ms_view_id, $a_cats);
		$n_tot = count($a_cats);
		$s_opc = '';
		for ( $i = 0 ; $i < $n_tot ; $i++ )
			$s_opc .= "<option value='{$a_cats[$i]['cat_id']}'>{$a_cats[$i]['cat_name']}</option>";
		$s_opc .= "<option value='0'>-- Uncategorized --</option>";

		// videos
		$a_vids  = array();
		$ss = "SELECT blog_id, cat_id, youtube_id, sort_order FROM microblog WHERE user_id = '{$wnd->ms_view_id}' and location = 'V' ORDER BY sort_order, thread_tm DESC";
		if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		{
			while ( ($a_row = CSql::fetch($rr)) )
				$a_vids[] = $a_row;
			CSql::free($rr);
		}
		$a_row = array('blog_id' => 0, 'cat_id' => 0, 'youtube_id' => '', 'sort_order' => 0);
		for ( $i = 0 ; $i < 5 ; $i++ )
			$a_vids[] = $a_row;

		$n_tot = count($a_vids);
		$s_ops = '';
		for ( $i = 1 ; $i <= $n_tot ; $i++ )
			$s_ops .= "<option value='{$i}'>{$i}</option>";

		echo  "<table>".
				"<tr>".
				  "<td>".
					"<table id='inp_tbl'>".
					  "<thead>".
						"<tr>".
						  "<td>Delete</td>".
						  "<td>Category</td>".
						  "<td colspan='2'>YouTube URL/id</td>".
						  "<td>Sort</td>".
						"</tr>".
					  "</thead>".
					  "<tbody>";

		for ( $i = 0 ; $i < $n_tot ; $i++ )
		{
			$a_row = &$a_vids[$i];
			$j = $i + 1;
			echo		"<tr>".
						  "<td style='text-align:center'>".
							"<input type='checkbox' id='n_del_{$j}' name='o_del_{$j}' />".
						  "</td>".
						  "<td>".
							"<input type='hidden' id='o_blog_id_{$j}' name='o_blog_id_{$j}' value='{$a_row['blog_id']}' />".
							"<input type='hidden' id='o_cat_id_{$j}' name='o_cat_id_{$j}' value='{$a_row['cat_id']}' />".
							"<select id='n_cat_id_{$j}' name='n_cat_id_{$j}'>".
							  str_replace("='{$a_row['cat_id']}'>", "='{$a_row['cat_id']}' selected='selected'>", $s_opc).
							"</select>".
						  "</td>".
						  "<td>".
							"<input type='hidden' id='o_youtube_id_{$j}' name='o_youtube_id_{$j}' value='{$a_row['youtube_id']}' />".
							"<input type='text' id='n_youtube_id_{$j}' name='n_youtube_id_{$j}' value='{$a_row['youtube_id']}' />".
						  "</td>".
						  "<td>".
							"<a href='javascript:void(FavVideos.testTub(\"n_youtube_id_{$j}\"))'>&lt;test link&gt;</a>".
						  "</td>".
						  "<td>".
							"<input type='hidden' id='x_sort_{$j}' name='x_sort_{$j}' value='{$a_row['sort_order']}' />".
							"<input type='hidden' id='o_sort_{$j}' name='o_sort_{$j}' value='{$j}' />".
							"<select id='n_sort_{$j}' name='n_sort_{$j}' onchange='FavVideosVid.resort(this)'>".
							  str_replace("='{$j}'>", "='{$j}' selected='selected'>", $s_ops).
							"</select>".
						  "</td>".
						"</tr>";
		}
		echo		  "</tbody>".
					"</table>".
				  "</td>".
				  "<td style='vertical-align:top;padding:20px 20px 0 20px'>".
					"The sort order is across categories. This is to allow you to define the sequence for the &quot;Show All Videos&quot; option.".
				  "</td>".
				"</tr>".
			  "</table>";
	}

	// ======================================================================
	function drawFav(&$wnd)
	{
		CWidgetFavVideos::getCategories($wnd->ms_view_id, $a_cats);
		echo  "<div class='wg_body'>";
				CWidgetFavVideos::drawPostInput($wnd->mwb_view_self, $a_cats);
		echo	"<div style='padding-top:18px'>".
				  "<table>".
					"<tr>".
					  "<td style='vertical-align:top'>".
						"<table id='fvid_menu'>".
						  "<tr><td><h2 style='margin-top:2px'>Show Videos:</h2></td></tr>".
						  "<tr><td class='td_opt' id='fvid_all'>All</td></tr>";
						  CWidgetFavVideos::drawPostCats($wnd->ms_view_id, $a_cats);
		echo			"</table>".
					  "</td>".
					  "<td style='vertical-align:top'>".
						"<div id='fvid_div' style='padding-bottom:18px' >".
						  "<table>".
							"<tr>".
							  "<td colspan='2'>".
								"<div id='fvid_nav' style='text-align:right;visibility:hidden'>".
								  CWidget::getNav('FavVideos.newer()','FavVideos.curr()','FavVideos.older()').
								  "<img src='http://dv1.us/d1/00/ad00.gif' height='14' width='14' alt='goto' title='Go to' style='padding-left:14px' ".
									   "id='fvid_jump' onmouseover='Img.mouseOver(event,this,16)' onmouseout='Img.mouseOut(this,16)' />".
								"</div>".
							  "</td>".
							"</tr>".
							"<tr>".
							  "<td id='fvid_target' style='padding:4px 0 2px 0' colspan='2'>".
							  	"<img src='http://dv1.us/d1/1.gif' height='395' width='640' />".
//								"<iframe src='http://www.youtube.com/embed/IAH-0GKvIrM?hd=1&wmode=opaque&rel=0' width='640' height='395' frameborder='0' allowfullscreen></iframe>".
							  "</td>".
							"</tr>".
							"<tr>".
							  "<td id='fvid_ref'>".
								"&nbsp;".
							  "</td>".
							  "<td>".
( $wnd->mwb_view_self		?	"<div id='fvid_ctr' style='text-align:right;visibility:hidden'>".
//								  "<img src='http://dv1.us/d1/00/ad00.gif' height='14' width='14' alt='goto' title='Change Category' style='padding-left:14px' ".
//									   "onclick='' onmouseover='Img.mouseOver(event,this,16)' onmouseout='Img.mouseOut(this,16)' />".
								  "<img src='http://dv1.us/d1/00/ax00.gif' height='14' width='14' alt='delete' title='Delete' ".
									   "onclick='FavVideos.del()' onmouseover='Img.mouseOver(event,this,13)' onmouseout='Img.mouseOut(this,13)' />"
							:	  "&nbsp;").
								"</div>".
							  "</td>".
							"</tr>".
						  "</table>".
						"</div>".
						"<input type='hidden' id='fvid_page' value='1' />".
						"<input type='hidden' id='fvid_total' value='0' />".
						"<input type='hidden' id='fvid_last' value='0' />".
						"<input type='hidden' id='fvid_cat' value='-1' />".
						"<input type='hidden' id='fvid_id' value='' />".
					  "</td>".
					"</tr>".
				  "</table>".
				"</div>".
			  "</div>";
    }

	// ======================================================================
	function getCategories($s_view_id, &$a_cats)
	{
		$a_cats  = array();
		$ss = "SELECT cat_id, cat_name, sort_order FROM my_vid_category WHERE user_id = '{$s_view_id}' ORDER BY sort_order";
		if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		{
			while ( ($a_row = CSql::fetch($rr)) )
				$a_cats[] = $a_row;
			CSql::free($rr);
		}
	}

	function drawPostInput($b_doit, &$a_cats)
    {
		if ( ! $b_doit ) return;

		$s_opt = "<option value='0' selected='selected'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>";
		if ( count($a_cats) )
		{
			for ( $i = 0 ; $i < count($a_cats) ; $i++ )
				$s_opt .= "<option value='{$a_cats[$i]['cat_id']}'>{$a_cats[$i]['cat_name']}</option>";
		}

		echo  "<div style='text-align:center'>".
				"<table class='no_border'>".
				  "<tr>".
					"<td style='text-align:left;white-space:nowrap'>Category <img id='ex_fvid_category' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>".
					"<td style='text-align:left;white-space:nowrap'>YouTube URL/id <img id='ex_fvid_tub' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>".
					"<td style='text-align:right;vertical-align:bottom' rowspan='2'><input id='post_fvid' onclick='FavVideos.blog()' type='button' value='Post' style='width:52px' /></td>".
				  "</tr>".
				  "<tr>".
					"<td style='text-align:left;white-space:nowrap'><select id='n_fvid_category'>".
					  $s_opt.
					"</td>".
					"<td style='text-align:left;white-space:nowrap'><input id='n_fvid_tub' size='40' maxlength='200' type='text' value='' />&nbsp;</td>".
				  "</tr>".
				"</table>".
			  "</div>";
    }

	function drawPostCats($s_view_id, &$a_cats)
	{
		if ( count($a_cats) )
		{
			$s = "<tr><td><h2 style='padding-top:20px'>By Category:</h2></td></tr>";
			for ( $i = 0 ; $i < count($a_cats) ; $i++ )
				$s .= "<tr><td class='td_opt' id='fvid_{$a_cats[$i]['cat_id']}'>{$a_cats[$i]['cat_name']}</td></tr>";
			echo $s . "<tr><td class='td_opt' id='fvid_0'>Uncategorized</td></tr>";
		}
	}

	// ======================================================================
	function validateDataSubmission(&$wnd)
	{
		if ( $wnd->mwb_view_self )
		{
			switch ( dvdaf3_getvalue('act', DVDAF3_GET) )
			{
			case 'cat': CWidgetFavVideos::validateDataSubmissionCat($wnd->ms_user_id); break;
			case 'vid': CWidgetFavVideos::validateDataSubmissionVid($wnd->ms_user_id); break;
			}
		}
	}

	function validateDataSubmissionCat($s_user_id)
	{
		$n_move_to_cat = dvdaf3_getvalue('move_to_cat',DVDAF3_POST|DVDAF3_INT);

		for ( $i = 1 ; isset($_POST['o_cat_id_'.$i]) ; $i++ )
		{
			$o_del		= dvdaf3_getvalue('o_del_'.$i		, DVDAF3_POST) != '';
			$o_cat_id	= dvdaf3_getvalue('o_cat_id_'.$i	, DVDAF3_POST|DVDAF3_INT);
			$o_cat_name	= dvdaf3_getvalue('o_cat_name_'.$i	, DVDAF3_POST);
			$n_cat_name	= dvdaf3_getvalue('n_cat_name_'.$i	, DVDAF3_POST);
			$x_sort		= dvdaf3_getvalue('x_sort_'.$i		, DVDAF3_POST|DVDAF3_INT);
			$o_sort		= dvdaf3_getvalue('o_sort_'.$i		, DVDAF3_POST|DVDAF3_INT);
			$n_sort		= dvdaf3_getvalue('n_sort_'.$i		, DVDAF3_POST|DVDAF3_INT);
			$ss			= '';

			if ( $o_del || $n_cat_name == '' )
			{
				if ( $o_cat_id )
				{
					$ss = "DELETE FROM my_vid_category WHERE user_id = '{$s_user_id}' and cat_id = {$o_cat_id}";
					if ( $n_move_to_cat == $i ) $n_move_to_cat = 0;
				}
			} else
			if ( $n_cat_name && ! $o_cat_id )
			{
				$ss = "INSERT INTO my_vid_category (user_id, cat_name, sort_order) ".
					  "VALUES ('{$s_user_id}', '{$n_cat_name}', {$n_sort})";
			} else
			if ( $o_cat_name != $n_cat_name || $n_sort != $x_sort )
			{
				$ss = "UPDATE my_vid_category ".
						 "SET cat_name = '{$n_cat_name}', sort_order = {$n_sort} ".
					   "WHERE user_id = '{$s_user_id}' ".
						 "and cat_id = {$o_cat_id} ";
			}
			if ( $ss )
				CSql::query_and_free($ss,0,__FILE__,__LINE__);
		}
		$ss = "UPDATE microblog ".
				 "SET cat_id = {$n_move_to_cat} ".
			   "WHERE user_id = '{$s_user_id}' ".
				 "and location = 'V' ".
				 "and not exists (SELECT 1 FROM my_vid_category v WHERE v.user_id = microblog.user_id and v.cat_id = microblog.cat_id)";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);
	}

	function validateDataSubmissionVid($s_user_id)
	{
		for ( $i = 1 ; isset($_POST['o_blog_id_'.$i]) ; $i++ )
		{
			$o_del			= dvdaf3_getvalue('o_del_'.$i		, DVDAF3_POST) != '';
			$o_blog_id		= dvdaf3_getvalue('o_blog_id_'.$i	, DVDAF3_POST|DVDAF3_INT);
			$o_cat_id		= dvdaf3_getvalue('o_cat_id_'.$i	, DVDAF3_POST|DVDAF3_INT);
			$n_cat_id		= dvdaf3_getvalue('n_cat_id_'.$i	, DVDAF3_POST|DVDAF3_INT);
			$o_youtube_id	= dvdaf3_getvalue('o_youtube_id_'.$i, DVDAF3_POST);
			$n_youtube_id	= dvdaf3_getvalue('n_youtube_id_'.$i, DVDAF3_POST);
			$x_sort			= dvdaf3_getvalue('x_sort_'.$i		, DVDAF3_POST|DVDAF3_INT);
			$o_sort			= dvdaf3_getvalue('o_sort_'.$i		, DVDAF3_POST|DVDAF3_INT);
			$n_sort			= dvdaf3_getvalue('n_sort_'.$i		, DVDAF3_POST|DVDAF3_INT);
			$ss				= '';

			if ( $o_del || $n_youtube_id == '' )
			{
				if ( $o_blog_id )
				{
					$ss = "DELETE FROM microblog WHERE user_id = '{$s_user_id}' and location = 'V' and blog_id = {$o_blog_id}";
				}
			} else
			if ( $n_youtube_id && ! $o_blog_id )
			{
				$ss = "INSERT INTO microblog (user_id, location, cat_id, youtube_id, sort_order, thread_tm, updated_tm, created_by, created_tm) ".
					  "SELECT '{$s_user_id}', 'V', {$n_cat_id}, '{$n_youtube_id}', {$n_sort}, now(), now(), '{$s_user_id}', now() ".
						"FROM one ".
					   "WHERE {$n_cat_id} = 0 or exists (SELECT 1 FROM my_vid_category WHERE user_id = '{$s_user_id}' and cat_id = {$n_cat_id})";
			} else
			if ( $o_cat_id != $n_cat_id || $o_youtube_id != $n_youtube_id || $n_sort != $x_sort )
			{
				$ss = "UPDATE microblog ".
						 "SET cat_id = {$n_cat_id}, youtube_id = '{$n_youtube_id}', sort_order = {$n_sort}, thread_tm = now(), updated_tm = now() ".
					   "WHERE user_id = '{$s_user_id}' ".
						 "and location = 'V' ".
						 "and blog_id = {$o_blog_id} ".
						 "and reply_num = 0 ".
						 "and created_by = '{$s_user_id}' ".
						 "and ({$n_cat_id} = 0 or exists (SELECT 1 FROM my_vid_category WHERE user_id = '{$s_user_id}' and cat_id = {$n_cat_id}))";
			}
			if ( $ss )
				CSql::query_and_free($ss,0,__FILE__,__LINE__);
		}
	}

	// ======================================================================
}

?>
