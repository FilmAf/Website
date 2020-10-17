<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CFilmGx.php';

class CFilmGxDir extends CFilmGx
{
    function constructor()
    {
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

		$this->ms_nocase	= str_replace('-',' ',$this->ms_canonical);
		$this->ms_obj_type	= 'director';
	}

	function validRequest()
	{
		$this->ms_setupParms = CFilmTab::getSetupParms('dirtab', false, false, '');
		$ss					 = "SELECT name FROM search_director WHERE nocase = '/ {$this->ms_nocase} /'";
		$ss					 = CSql::query_and_fetch1($ss,0,__FILE__,__LINE__);
		$this->ms_title		 = $ss ? $ss : ucwords($this->ms_nocase);

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
								  "<meta property='og:url' content='http://www.filmaf.com/gd/{$this->ms_canonical}' />".
								  "<meta property='og:title' content='{$s_tit}' />".
								  "<meta property='og:image' content='{$s_pic}' />".
								  "<meta property='og:description' content='{$s_tit}' />".
								  "<meta property='og:updated_time' content='1355469531' />";

		//echo str_replace('<','<br />[',str_replace('>',']',$this->ms_include_meta));
	}

	function drawContent()
	{
		CFilmTab::drawSelectors(false, false);
		$ss = CFilmTab::getDvdSql($this->ms_nocase, 0);
		CFilmTab::fetchAndDraw($ss, CFilmTab::getCaption(), true);
	}
}

?>
