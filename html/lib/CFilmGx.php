<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWnd.php';
require $gs_root.'/lib/CPicUtils.php';
require $gs_root.'/lib/CFilmTab.php';

class CFilmGx extends CWnd
{
    function constructor()
    {
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$this->ms_title			= 'Director not found';
		$this->ms_keywords		= '';
		$this->ms_description	= '';
		$this->ms_include_js	= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-filmgp_{$this->mn_lib_version}.js'></script>\n";
		$this->mb_menu_context	= true;
		$this->mb_include_menu	= true;

		$this->parseUri();
	}

	function parseUri()
	{
		$this->ms_uri			= dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER|DVDAF3_LOWER|DVDAF3_NO_AMP_EXPANSION);
		$this->ms_referer		= dvdaf3_getvalue('HTTP_REFERER',DVDAF3_SERVER);
		$a						= explode('?',substr($this->ms_uri,4));
		$this->ms_canonical		= $a[0];
		$this->ms_action_ids	= '';
		$this->ms_action_types	= '';
		$this->mb_save_parms	= false;

		if ( count($a) > 1 )
		{
			$a = explode('&',$a[1]);
			for ( $i = 0 ; $i < count($a) ; $i++ )
			{
				$b = explode('=',str_replace('%3a',':',str_replace('%22','',$a[$i])));
				switch ( $b[0] )
				{
				case 'fb_action_ids':
					// 104942473013886
					$this->ms_action_ids = $b[1];
					break;
				case 'fb_action_types':
					// filmafi:see
					$this->ms_action_types = $b[1];
					break;
				}
			}
			$this->mb_save_parms = true;
		}
		if ( strpos($this->ms_referer, 'facebook.com') )
		{
			$this->mb_save_parms = true;
		}
	}

	function badRequester()
	{
		if ( strpos(dvdaf3_getvalue('HTTP_USER_AGENT', DVDAF3_SERVER|DVDAF3_LOWER),'googlebot') !== false )
			return true;

		return false;
	}

	function drawHeader()
	{
		$s_hello = $this->getHello(true);

		echo  "<table id='header'><tr>".
				"<td><a href='{$this->ms_base_subdomain}'><img src='http://dv1.us/d1/filmaf.png' /></a></td>".
				"<td><div id='fb-root'></div></td>".
				"<td id='hello'>{$s_hello}</td>".
			  "</tr></table>";
	}

	function validateDataSubmission()
	{
		if ( $this->mb_save_parms )
		{
			$s_uri			= dvdaf3_translatestring($this->ms_uri			,DVDAF3_NO_TRANSLATION,512);
			$s_canonical	= dvdaf3_translatestring($this->ms_canonical	,DVDAF3_NO_TRANSLATION,128);
			$s_action_ids	= dvdaf3_translatestring($this->ms_action_ids	,DVDAF3_NO_TRANSLATION,255);
			$s_action_types	= dvdaf3_translatestring($this->ms_action_types	,DVDAF3_NO_TRANSLATION,255);
			$s_referer		= dvdaf3_translatestring($this->ms_referer		,DVDAF3_NO_TRANSLATION,512);
			$s_orig			= dvdaf3_getvalue('orig'		,DVDAF3_COOKIE);

			if ( $s_uri			 == '' ) $s_uri			 = '-';
			if ( $s_canonical	 == '' ) $s_canonical	 = '-';
			if ( $s_action_ids	 == '' ) $s_action_ids	 = '-';
			if ( $s_action_types == '' ) $s_action_types = '-';
			if ( $s_referer		 == '' ) $s_referer		 = '-';
			if ( $s_orig		 == '' ) $s_orig		 = '-';

			$ss = "INSERT INTO fb_visits (obj_type, canonical_id, action_id, action_types, uri, referer, user_id, terminal_id, click_tm) ".
				  "VALUES('{$this->ms_obj_type}', '{$s_canonical}', '{$s_action_ids}', '{$s_action_types}', '{$s_uri}', '{$s_referer}', '{$this->ms_user_id}', '{$s_orig}', now())";
			CSql::query_and_free($ss, 0,__FILE__,__LINE__);
		}
		return true;
	}

	function drawBodyPage()
	{
		echo  "<div style='margin:14px 10px 10px 10px'>".
				"<div id='my-tab'>".
				  "<ul>".
					"<li><a href='/'><span style='color:#de4141'>FilmAf</span></a></li>".
					"<li>{$this->ms_title}".
					  "<div>".
						"<table style='width:100%'>".
						  "<tr>".
							"<td>";
							  $this->drawContent();
		echo				"</td>".
						  "</tr>".
						"</table>".
					  "</div>".
					"</li>".
				  "</ul>".
				"</div>".
			  "</div>";
	}

	function getFooterJavaScript()
	{
		$s_user   = $this->mb_logged_in  ? $this->ms_user_id : '';
		$s_config = '{baseDomain:"'.		$this->ms_base_subdomain.'"'.
					',userCollection:"'.	$s_user.'"'.
					',preloadImgPop:1'.
					',onPopup:Main.onPopup'.
					',ulDvd:1'.
					',imgPreLoad:"tabbk.home.coll"'.
					'}';
		return
					"function onMenuClick(action){Main.onClick(action);};".
					"Filmaf.config({$s_config});".
					"setTimeout('Menus.setup();Main.setup({$this->ms_setupParms})',100);".
					"if($('my-tab')){Tab.setup('my-tab',{background:'#ffffff', loadtab:1, width:'100%', wholepage:1})};";
					"";
	}
}

?>
