<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetMicroblog extends CWidget
{
    function draw(&$wnd)//&$a_profile, $s_user_id, $s_view_id, $s_unatrib_subdomain, $s_base_subdomain, $b_show_replies, $b_browser_is_ie)
    {
		CWidget::drawHeader('Film Microblog', CWidget::getNav('Microblog.newer()','Microblog.curr()','Microblog.older()'), '');

		echo  "<div class='wg_body'>";
				CWidget::drawPostInput('blog', $wnd->mwb_view_self);
				CWidget::drawPostShow ('blog', $wnd, $n_last);
		echo 	"<input type='hidden' id='blog_page' value='1' />".
				"<input type='hidden' id='blog_last' value='{$n_last}' />".
			  "</div>";
    }
}

/*
		$s_allow	 = $a_profile['microblog_reply_ind'] == 'Y' ? "checked='checked' " : '';
		$s_showr	 = $b_show_replies ? "checked='checked' " : '';
		if ( $b_browser_is_opera )
		{
			$s_opt =  "<span class='wga' style='text-decoration:none;font-size:10px;position:relative;top:1px'>".
						( $b_view_self ? 
						"allow friends to post replies <input id='cb_blog_reply' type='checkbox' onclick='Microblog.setAllowReplies(this)' style='height:13px' {$s_allow}/> " : '').
						"show replies <input id='cb_blog_reply_show' type='checkbox' onclick='Microblog.setShowReplies(this.checked)' style='height:13px' {$s_showr}/> ".
					  "</span>";
		}
		else
		{
			$s_opt =  ( $b_view_self ?
					  "<span class='wga' style='text-decoration:none;font-size:10px;position:relative;top:1px'>allow friends to post replies".
						"<input id='cb_blog_reply' type='checkbox' onclick='Microblog.setAllowReplies(this)' style='position:absolute;bottom:-2px;right:-19px' {$s_allow}/>".
					  "</span>".
					  "<img style='width:20px;height:1px' src='http://dv1.us/d1/1.gif' />" : '').
					  "<span class='wga' style='text-decoration:none;font-size:10px;position:relative;top:1px'>show replies ".
						"<input id='cb_blog_reply_show' type='checkbox' onclick='Microblog.setShowReplies(this.checked)' style='position:absolute;bottom:-2px;right:-19px' {$s_showr}/>".
					  "</span>".
					  "<img style='width:20px;height:1px' src='http://dv1.us/d1/1.gif' />";
		}
*/

?>
