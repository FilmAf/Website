<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetUpdates extends CWidget
{
	function draw(&$wnd)
	{
		CWidget::drawHeader('Updates &amp; Feeds', CWidget::getNav('Updates.newer()','Updates.curr()','Updates.older()'), '');

		echo "<div class='wg_body'>";
				CWidget::drawPostShow('updates', $wnd, $n_last);
		echo	"<input type='hidden' id='updates_page' value='1' />".
				"<input type='hidden' id='updates_last' value='{$n_last}' />".
			  "</div>";
    }
}

?>
