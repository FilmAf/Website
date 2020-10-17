<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidget
{
    function getNav($s_onclick_1, $s_onclick_2, $s_onclick_3)
	{
		return	"<img src='http://dv1.us/d1/00/al00.gif' height='14' width='14' alt='prev' title='Previous' ".
					 "onclick='{$s_onclick_1}' onmouseover='Img.mouseOver(event,this,10)' onmouseout='Img.mouseOut(this,10)' />".
				"<img src='http://dv1.us/d1/00/as00.gif' height='14' width='14' alt='top' title='Refresh / First' ".
					 "onclick='{$s_onclick_2}' onmouseover='Img.mouseOver(event,this,12)' onmouseout='Img.mouseOut(this,12)' />".
				"<img src='http://dv1.us/d1/00/ar00.gif' height='14' width='14' alt='next' title='Next' ".
					 "onclick='{$s_onclick_3}' onmouseover='Img.mouseOver(event,this,11)' onmouseout='Img.mouseOut(this,11)' />";
	}

    function drawHeader($s_title, $s_menu, $s_subtit)
    {
		if ( ! $s_menu ) $s_menu = '&nbsp;';

		echo  "<h2>".
			    "<span style='white-space:nowrap'>{$s_title}</span>".
			    "<span style='float:right;white-space:nowrap;font-size:11px'>&nbsp;{$s_menu}</span>".
				$s_subtit.
			  "</h2>".
			  "<img src='http://dv1.us/d1/1.gif' width='400' height='1' />";
    }

    function drawPostInput($s_widget, $b_doit)
    {
		if ( ! $b_doit ) return;

		echo  "<div style='text-align:center'>".
				"<textarea id='n_text_{$s_widget}' style='width:98%;height:60px' maxlength='500' wrap='soft'></textarea>".
				"<table width='100%'>".
				  "<tr>".
					"<td style='text-align:left'><input id='n_{$s_widget}_up' name='n_{$s_widget}_up' type='checkbox' /> Attach</td>".
					"<td style='text-align:right'><input id='post_{$s_widget}' type='button' value='Post' style='width:52px' /></td>".
				  "</tr>".
				"</table>".
			  "</div>";
    }

	function getSqlPostWithReply($s_widget, &$wnd)
	{
		// threse are shared between the main query and the subquery
		switch ( $s_widget )
		{
		case 'wall':
			$s_from  = "microblog b";
			$s_where = "b.user_id = '{$wnd->ms_view_id}' and b.location = 'W'";
			break;
		case 'blog':
			$s_from  = "microblog b";
			$s_where = "b.user_id = '{$wnd->ms_view_id}' and b.location = 'B'";
			break;
		case 'updates':
			$s_from  = "friend f JOIN microblog b on f.friend_id = b.user_id";
			$s_where = "f.user_id = '{$wnd->ms_view_id}' and b.location = 'B'";
			break;
		default:
			return '';
		}
		$s_from .= " LEFT JOIN dvd a ON a.dvd_id = b.obj_id and b.obj_type = 'D'";

		// use a subquery to get the key for the threads that have been touched most recently
		$s_subq   = "SELECT b.user_id, b.location, b.blog_id, b.thread_tm FROM {$s_from} WHERE {$s_where} and b.reply_num = 0 ORDER BY b.thread_tm desc limit 11";

		// compose the rest of the main query
		$s_select = "b.user_id, b.location, b.blog_id, b.reply_num, b.pic_id, b.pic_source, b.pic_name, b.youtube_id, b.obj_id, b.obj_type, b.blog, ".
					"b.reply_count, b.created_by, b.created_tm,  b.updated_tm, a.media_type, x.name, datediff(now(),b.created_tm) post_age";
		$s_from  .= " JOIN ({$s_subq}) z ON z.user_id = b.user_id and z.location = b.location and z.blog_id = b.blog_id".
					" LEFT JOIN dvdaf_user_3 x on b.created_by = x.user_id";

		if ( $wnd->ms_user_id == 'guest' || ($wnd->mwb_view_self && $s_widget != 'updates' ) )
		{
			$s_select .= ", '' is_user_id_friend, 'n' friend_reply";
		}
		else
		{
			$s_select .= ", us.friend_id is_user_id_friend, y.microblog_reply_ind friend_reply";
			$s_from   .=  " LEFT JOIN friend us on us.user_id = b.user_id and us.friend_id = '{$wnd->ms_user_id}'".
						  " LEFT JOIN dvdaf_user_3 y on y.user_id = b.user_id";
		}

		return "SELECT {$s_select} FROM {$s_from} WHERE {$s_where} ORDER BY z.thread_tm DESC, reply_num";
	}

    function drawPostShow($s_widget, &$wnd, &$n_last)
    {
		$n_last = 1;
		$n_rows = 0;
		$b_more = false;
		$ss     = CWidget::getSqlPostWithReply($s_widget, $wnd);

		if ( ! $ss ) return;

		echo    "<div id='tbl_{$s_widget}'>";

		if ( ($rr = CSql::query($ss,0,__FILE__,__LINE__)) )
		{
			$a_row = CSql::fetch($rr);
			while ( $a_row && $n_rows < 10 )
			{
				if ( ! $n_rows )
					echo "<table width='100%'>";

				$s_ref_id = "{$a_row['user_id']}.{$a_row['location']}.{$a_row['blog_id']}";
				$s_div    = "<div id='hid.{$s_ref_id}_sav' style='visibility:hidden;position:absolute;top:0;left:0'>";
				$s_str    = CWidget::getBlogMsg($a_row, $s_widget, $s_ref_id, $wnd);
				$n_curr   = $a_row['blog_id'];
				$s_sub    = '';
				$b_more   = false;
				$n_rows++;
				while ( ($a_row = CSql::fetch($rr)) )
				{
					if ( $n_curr == $a_row['blog_id'] )
					{
						$s_sub .= CWidget::getBlogMsg($a_row, $s_widget, '', $wnd). "</td></tr>";
					}
					else
					{
						$b_more = true;
						break;
					}
				}

				if ( $s_sub )
				{
					$s_sub =  "<div style='margin:2px 0 0 32px;background-color:#ebf4fa'>".
								"<table width='100%'>".
								  $s_sub.
								"</table>".
							  "</div>";

					if ( $wnd->mwb_show_replies )
						$s_sub = "{$s_div}</div><div id='hid.{$s_ref_id}'>{$s_sub}</div>";
					else
						$s_sub = "{$s_div}{$s_sub}</div><div id='hid.{$s_ref_id}'></div>";
				}
				else
				{
					$s_sub = "{$s_div}</div><div id='hid.{$s_ref_id}'></div>";
				}

				echo $s_str. $s_sub. "</td></tr>";
			}
			CSql::free($rr);
		}

		echo $n_rows
			?			"</table>".
					  "</div>"
			:			"<div class='wg_sepa'>&nbsp;</div>".
						"<div>&nbsp;</div>".
					  "</div>";

		$n_last = $b_more ? 0 : 1;
    }

    function getBlogMsg(&$a_row, $s_widget, $s_ref_id, &$wnd)
    {
		$b_blog  = $s_widget == 'blog';
		$s_who   = ucfirst($a_row['created_by']); if ( $a_row['name'] && $a_row['name'] != '-' ) $s_who = "{$a_row['name']} ({$s_who})";
		$b_reply = $wnd->ms_user_id == $a_row['user_id'   ] || ($a_row['is_user_id_friend'] != '' && $a_row['friend_reply'] != 'N');
		$b_edit  = $wnd->ms_user_id == $a_row['created_by'] && $a_row['post_age'] <= 1;
		$b_del   = $wnd->ms_user_id == $a_row['created_by'] || ($wnd->ms_user_id == $a_row['user_id'] && ($s_widget == 'blog' || $s_widget == 'wall'));
		$b_hide  = $a_row['reply_num'] > 0;
		$s_ref   = "{$a_row['user_id']}.{$a_row['location']}.{$a_row['blog_id']}.{$a_row['reply_num']}";
		$s_med   = $a_row['media_type'];
		$s_med   = strpos("23BR",$s_med) !== false ? 'b' : (strpos("EFLNS",$s_med) !== false ? 'f' : 'd');

		// --------------------------------------------------------------------------------------------------------------------------------------------------------
		// classes ctx1 and ctx2 are only used for the context menu
		$s_header = $a_row['reply_num'] ? 'replies' : 'says';
		$s_header =		"<div class='wg_time' style='margin:0 0 4px 0'>".
						  ($b_del   ? "<img src='http://dv1.us/d1/00/ax00.gif' height='14' width='14' alt='delete' title='Delete' id='del.{$s_ref}' ".
						   				   "onmouseover='Img.mouseOver(event,this,13)' onmouseout='Img.mouseOut(this,13)' ".
										   "onclick='Microblog.del(\"{$s_widget}\",this.id)' style='float:right' />" : '').
						  ($b_reply ? "<img src='http://dv1.us/d1/00/ap00.gif' height='14' width='14' alt='reply' title='Reply' id='rep.{$s_ref}' ".
						   				   "onmouseover='Img.mouseOver(event,this,15)' onmouseout='Img.mouseOut(this,15)' ".
										   "class='ctx2' dynarch_below='div.{$s_ref}' style='float:right' />" : '').
						  ($b_edit  ? "<img src='http://dv1.us/d1/00/ak00.gif' height='14' width='14' alt='edit' title='Edit' id='edi.{$s_ref}' ".
						   				   "onmouseover='Img.mouseOver(event,this,14)' onmouseout='Img.mouseOut(this,14)' ".
										   "class='ctx1' dynarch_below='div.{$s_ref}' style='float:right' />" : '').
									  "<div id='tim.{$s_ref}' style='float:right;margin:0 2px 0 6px'>{$a_row['updated_tm']}</div>".
						  ($b_blog  ? "<div>{$s_who} {$s_header}:</div>" : "<div><a href='http://{$a_row['created_by']}{$wnd->ms_unatrib_subdomain}/'>{$s_who}</a> {$s_header}:</div>").
						"</div>";

		// --------------------------------------------------------------------------------------------------------------------------------------------------------
		$s_blog_dvd = '';
		$s_blog_tub = '';

		if ( $a_row['obj_type'] == 'D' && $a_row['obj_id'] > 0 && $a_row['pic_name'] != '-' )
		{
			$s_dvd_id = sprintf('%07d', $a_row['obj_id']);
			$s_blog_dvd = "<a id='{$s_med}_{$s_dvd_id}' class='dvd_pic' href='{$wnd->ms_base_subdomain}/search.html?has={$s_dvd_id}&init_form=str0_has_{$s_dvd_id}'>".
							"<img id='zo_{$s_dvd_id}' onmouseover='ImgPop.show(this,0)' zoom_hoz='left' style='float:left;margin:0px 6px 0 0' src='". CPic::location($a_row['pic_name'],0). "' alt='' />".
							"<input id='attd.{$s_ref}' type='hidden' value='D.{$a_row['obj_id']}' />".
						  "</a>";
		}

		if ( $a_row['youtube_id'] != '-' )
		{
			$s_style	= $a_row['reply_num'] ? ';padding:4px 0 0 30px' : ';padding-top:4px';
			$s_blog_tub	= "<div style='text-align:center{$s_style};clear:both'>".
							CWidget::embedYouTube($a_row['youtube_id'], false, false, $wnd->mwb_browser_is_ie).
							"<input id='atty.{$s_ref}' type='hidden' value='Y.{$a_row['youtube_id']}' />".
						  "</div>";
		}

		$s_blog = $s_blog_dvd.
				  "<span id='blo.{$s_ref}'>{$a_row['blog']}</span>".
				  $s_blog_tub;

		// --------------------------------------------------------------------------------------------------------------------------------------------------------
		$s_reply = $a_row['reply_num'] ? '' : "<div id='sho.{$s_ref_id}'>" . CWidget::getReply($s_widget, $s_ref_id, $a_row['reply_count']) . "</div>";
		$s_style = $a_row['reply_num'] ? " style='padding:1px 0 1px 4px'" : '';

		return	"<tr><td><div class='wg_sepa'>&nbsp;</div></td></tr>".
				"<tr><td{$s_style}><div id='div.{$s_ref}'>{$s_header}{$s_blog}{$s_reply}</div>";
		//	"</td></tr>";
    }

	function embedYouTube($s_id, $b_big, $b_auto, $b_browser_is_ie)
	{
		if ( $s_id && strlen($s_id) > 1 )
		{
			$s_parm = ($b_auto == 'Y' ? '&autoplay=1' : '');
			$s_size = $b_big ? "width='640' height='395'" : "width='380' height='251'";

			return $b_browser_is_ie
				? "<embed src='http://www.youtube.com/v/{$s_id}?hl=en&fs=1&fmt=18{$s_parm}' type='application/x-shockwave-flash' wmode='transparent' allowfullscreen='true' {$s_size} />"
				: "<iframe src='http://www.youtube.com/embed/{$s_id}?hd=1&wmode=opaque&rel=0{$s_parm}' {$s_size} frameborder='0' allowfullscreen></iframe>";
		}
		return '';
	}

    function getReply($s_widget, $s_ref_id, $n_replies)
    {
		if ( ! $n_replies ) return '';

		return  "<div class='wg_repl' style='text-align:left;font-size:10px;clear:both'>".
				  "<a href='javascript:void(Microblog.showReply(\"hid.{$s_ref_id}\"))' style='color:#bd0b0b'>".
				  "{$n_replies} repl".($n_replies > 1 ? 'ies' : 'y')." (".
				  "show/hide)".
				  "</a>".
				"</div>";
    }
}

?>
