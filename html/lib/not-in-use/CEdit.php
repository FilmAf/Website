<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndMenu.php';

class CEdit extends CWndMenu
{
	function constructor()
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-edit_{$this->mn_lib_version}.js'></script>\n";
		$this->ms_title				= 'FilmAf - Submissions';
		$this->mb_include_collect	= false;
		$this->mb_include_search	= false;
		$this->mb_advert			= false;
		$this->mb_get_user_status	= true;
		$this->mb_direct_update		= false;
		$this->ms_url_base			= dvdaf3_getvalue('SCRIPT_NAME' , DVDAF3_SERVER);
		$this->ms_url_parms			= dvdaf3_getvalue('QUERY_STRING', DVDAF3_SERVER|DVDAF3_LOWER);
		// REQUEST_URI = [/utils/edit.html?me=here&time-2]
		// SCRIPT_NAME = [/utils/edit.html]
		// QUERY_STRING = [me=here&time-2]
	}

	function validUserAccess()
	{
		if ( ! $this->mb_logged_in )
			return CUser_NOACCESS_GUEST;
		if ( $this->mb_mod )
			if ( $this->mb_logged_in_this_sess )
				$this->mb_direct_update = true;
			else
				return CUser_NOACCESS_SESSION;
		return CUser_ACCESS_GRANTED;
	}

	function getOnLoadJavaScript()
	{
		return "this.focus();";
	}

	function getFooterJavaScript()
	{
		$s_user    = $this->mb_logged_in ? $this->ms_user_id : '';
		$s_view    = $this->mb_collection ? $this->ms_view_id : '';
		$s_config  = '{baseDomain:"'.					$this->ms_base_subdomain.'"'.
					 ',userCollection:"'.				$s_user					.'"'.
					 ',viewCollection:"'.				$s_view					.'"'.
					 ',ulExplain:1'.
					 '}';
		return
					"Filmaf.config({$s_config});".
					"setTimeout('".
						"Menus.setup();".
						"if($(\"my-tab\"))Tab.setup(\"my-tab\",{background:\"#ffffff\", loadtab:0, width:\"100%\"});".
					"',100);";
	}

	function drawBodyPage()
	{
		$s_tab_no_parms	= "/utils/edit-tab.html?pg=";
		$s_tab_parms	= "/utils/edit-tab.html" . ($this->ms_url_parms ? '?'.$this->ms_url_parms.'&pg=' : '?pg=');

		echo  "<div style='margin:24px 10px 10px 10px'>".
				"<div id='my-tab'>".
				  ($this->ms_url_parms ? "<li><a href='{$s_tab_parms}current'>Current Edits</a></li>" : '').
				  "<li><a href='{$s_tab_no_parms}pending'>Pending</a></li>".
				  "<li><a href='{$s_tab_no_parms}approved'>Approved</a></li>".
				  "<li><a href='{$s_tab_no_parms}declined'>Declined</a></li>".
				  "<li><a href='{$s_tab_no_parms}withdrawn'>Withdrawn</a></li>".
				  ( $this->mb_mod ? "<li><a href='{$s_tab_no_parms}directs'>Direct Edits</a></li>" : '').
				  "<li><a href='javascript:void(location.href=\"/utils/edit-dvd.html\");'>Submit New DVD</a></li>".
				  "<li><a href='javascript:void(location.href=\"/utils/edit-film.html\");'>Submit New Film</a></li>".
				  "<li><a href='javascript:void(location.href=\"/utils/edit-person.html\");'>Submit New Person</a></li>".
				  "<li><a href='javascript:void(location.href=\"/utils/edit-pub.html\");'>Submit New Publisher</a></li>".
				"</div>".
			  "</div>";
	}
}

?>
