<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CFilmTab.php';

class CWidgetMedia extends CWidget
{
	function draw(&$wnd, $s_tab)
	{
		$this->ms_setupParms = CFilmTab::getSetupParms('hometab', true, true, $s_tab);

		CFilmTab::drawSelectors(true, $s_tab);
		$ss = CFilmTab::getDvdSql(false, 1);
		CFilmTab::fetchAndDraw($ss, CFilmTab::getCaption(), false);
    }
}

?>
