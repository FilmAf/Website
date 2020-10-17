<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetWelcome extends CWidget
{
	function draw(&$wnd)
	{
		echo "<h2 style='margin:24px 0 2px 0; padding:0 0 0 0; text-align:center'>Welcome to Film Aficionado</h2>";
		echo "<div style='text-align:center'>Your favorite spot for Film collecting</div>";
    }
}

?>
