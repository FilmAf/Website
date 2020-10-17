<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetWall extends CWidget
{
    function draw(&$wnd)
    {
		CWidget::drawHeader("{$wnd->mws_ucview_id}&#39;s Wall", CWidget::getNav('Wall.newer()','Wall.curr()','Wall.older()'), '');

		echo  "<div class='wg_body'>";
				CWidget::drawPostInput('wall', $wnd->ms_user_id != 'guest' && ($wnd->mwb_view_self || $wnd->mb_is_friend));
				CWidget::drawPostShow ('wall', $wnd, $n_last);
		echo 	"<input type='hidden' id='wall_page' value='1' />".
				"<input type='hidden' id='wall_last' value='{$n_last}' />".
			  "</div>";
    }
}

?>
