<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CFilmGx.php';

class CFilmGxGenre extends CFilmGx
{
	function validRequest()
	{
		if ( strpos(',comedy,drama,horror,action,sci-fi,animation,anime,suspense,fantasy,documentary,western,sports,war,exploitation,musical,filmnoir,music,erotica,silent,experimental,short,performing-arts,educational,dvd-audio,',",{$this->ms_canonical},") === false )
			return false;

		$this->ms_setupParms	= CFilmTab::getSetupParms('gentab', true, false, $this->ms_canonical);
		$this->ms_title			= ucwords($this->ms_canonical);
		$this->ms_obj_type		= 'genre';

		$this->initFacebookMeta();
		return true;
	}

	function initFacebookMeta()
	{
		$s_tit		= str_replace('-','',str_replace(',',' ',$this->ms_title));
		$s_pic		= 'http://dv1.us/d1/filmaf-med.png';

		$this->ms_head_attrib	= " prefix='og: http://ogp.me/ns# filmafi: http://ogp.me/ns/apps/filmafi#'";
		$this->ms_include_meta	= "<meta property='fb:app_id' content='413057338766015' />".
								  "<meta property='og:type' content='filmafi:{$this->ms_obj_type}' />".
								  "<meta property='og:url' content='http://www.filmaf.com/gg/{$this->ms_canonical}' />".
								  "<meta property='og:title' content='{$s_tit}' />".
								  "<meta property='og:image' content='{$s_pic}' />".
								  "<meta property='og:description' content='{$s_tit}' />".
								  "<meta property='og:updated_time' content='1355469531' />";

		//echo str_replace('<','<br />[',str_replace('>',']',$this->ms_include_meta));
	}

	function drawContent()
	{
		CFilmTab::drawSelectors(true, $this->ms_canonical);
		$ss = CFilmTab::getDvdSql(false, 1);
		CFilmTab::fetchAndDraw($ss, CFilmTab::getCaption(), true);
	}
}

?>
