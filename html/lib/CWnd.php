<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CTrace.php';
require $gs_root.'/lib/CSqlMysql.php';
require $gs_root.'/lib/CSnippets.php';

define('CWnd_FOOTER_NONE'			,		 1);
define('CWnd_FOOTER_CONTACT'		,		 2);	// BITMAP for $this->mn_footer_type
define('CWnd_FOOTER_TIME'			,		 4);

define('CWnd_ZOOM_NONE'				,		 0);
define('CWnd_ZOOM_STAR'				,		 1);	// show if member is star or display a star's collection
define('CWnd_ZOOM_ALL'				,		 2);

define('CWnd_STAR_USER_NOT_EXIST'	,		-3);
define('CWnd_STAR_NOT_INIT'			,		-2);
define('CWnd_STAR_NO_USER'			,		-1);

define('CUser_ACCESS_GRANTED'		,		 0);	// return value for validUserAccess
define('CUser_NOACCESS_GUEST'		,		 1);
define('CUser_NOACCESS_USER'		,		 2);
define('CUser_NOACCESS_SESSION'		,		 3);

define('CWnd_HEADER_NONE'			,		 0);
define('CWnd_HEADER_BIG'			,		 1);
define('CWnd_HEADER_SMALL'			,		 2);

//////////////////////////////////////////////////////////////////////////

class CWnd
{
	function CWnd()
	{
		global $gb_trace_sql;

		$this->constructor();

		$gb_trace_sql		= $this->mb_trace_sql;
		$n_maintenance		= 0;				// Set it to 1 when turning site off
		$b_valid_server		= false;
		$b_valid_request	= false;
		$n_invalid_access	= CUser_ACCESS_GRANTED;

//		include '/home/bkuser/db-backup-status.php';

		if ( ! $n_maintenance )
		{
			if ( $this->verifyDomain() )
			{
				$this->verifyUser();
				$b_valid_server = true;

				if ( $this->mb_log_request ) $this->logRequest();

				if ( $this->validRequest() )
				{
					if ( $this->badRequester() )
					{
						$this->repondBadRequest();
						return;
					}
					$this->initializeMenu();
					$b_valid_request  = true;
					$n_invalid_access = $this->validUserAccess();
					if ( ! $n_invalid_access )
						$this->validateDataSubmission();
				}
			}

			if ( ! $b_valid_server || ! $b_valid_request )
				if ( $this->ms_redirect == '' ) $this->ms_redirect = $this->ms_base_subdomain;
		}

		$this->draw($n_maintenance, $n_invalid_access);
	}

	//////////////////////////////////////////////////////////////////////
	function draw($n_maintenance, $n_invalid_access)
	{
		$this->issueHeader();
		$this->drawHead();
		$this->drawBodyBeg();
		$this->drawCornersBeg();

		switch ($n_maintenance)
		{
			case 1:
				$this->drawMaintenanceDowntime();
				break;
			case 2:
				$this->drawBackupDowntime();
				break;
			default:
				//		if ( ! $gn_sql_connection ) $this->NotConnected();					else
				if ( $this->ms_redirect   ) $this->drawRedirect();					else
					if ( $n_invalid_access    ) $this->drawNoAccess($n_invalid_access);	else
					{
						$this->drawBodyTop();
						$this->drawBodyPage();
						$this->drawBodyBottom();
					}
				break;
		}

		$this->drawCornersEnd();
		$this->drawBodyEnd();
	}

	//////////////////////////////////////////////////////////////////////
	function constructor()
	{
		global $gn_host;

		switch ( $gn_host )
		{
			case HOST_FILMAF_COM: $this->ms_base_subdomain = 'http://www'.($this->ms_unatrib_subdomain = '.filmaf.com'); break;
			case HOST_FILMAF_EDU: $this->ms_base_subdomain = 'http://www'.($this->ms_unatrib_subdomain = '.filmaf.edu'); break;
		}

		// Debug
		$this->mb_show_trace			= false;
		$this->mb_trace_environment		= false;
		$this->mb_trace_sql				= false;
		$this->mb_log_request			= false;
		$this->mb_allow_redirect		= true;

		// Version
		$this->mn_lib_version			= 31;
		$this->ms_release				= '3.0.0.0';

		// View -- verifyDomain
		$this->ms_view_subdomain		= $this->ms_base_subdomain;
		$this->ms_view_id				= 'www';
		$this->mb_collection			= false;

		// User -- verifyUser
		$this->ms_user_subdomain		= $this->ms_base_subdomain;
		$this->ms_user_id				= 'guest';
		$this->mb_logged_in				= false;
		$this->mb_view_self				= false;
		$this->mb_logged_in_this_sess	= false;

		// User Status
		$this->mb_get_user_status		= false;
		$this->mb_mod					= false;
		$this->mn_contributor_cd		= 0;
		$this->mn_membership_cd			= 0;
		$this->mn_moderator_cd			= 0;
		$this->mn_access_level_cd		= 0; // max between mn_contributor_cd, mn_membership_cd, mn_moderator_cd
		$this->mn_user_stars			= -2;
		$this->ms_user_pinned			= '';
		// Viewer Status
		$this->mn_view_stars			= -2;

		// Format
		$this->mb_corners				= true;
		$this->mn_footer_type			= CWnd_FOOTER_TIME; // CWnd_FOOTER_CONTACT;
		$this->ms_style					= "{$this->ms_base_subdomain}/styles/00";
		$this->mn_header_type			= CWnd_HEADER_BIG;

		// Menus
		$this->mb_menu_context			= false;
		$this->ms_footer_supp			= '';

		// Messages
		$this->ms_display_affected		= '';
		$this->ms_display_what			= '';
		$this->ms_display_error			= '';
		$this->ms_redirect				= '';

		// Code loading
		$this->ms_onload_js				= '';	// internal, do not overwrite
		$this->ms_include_css			= "<link rel='stylesheet' type='text/css' href='{$this->ms_style}/filmaf_{$this->mn_lib_version}.css' />";
		$this->ms_include_js			= '';
		$this->ms_include_meta			= '';
		$this->ms_head_attrib			= '';
		$this->mb_include_cal			= false;
		$this->mb_include_menu			= false;
		$this->mb_facebook_div			= false;

		// Image auto popup
		$this->mn_echo_zoom				= CWnd_ZOOM_NONE;

		// Meta and Title
		$this->ms_copyright				= 'Film Aficionado is public domain software. Promotional images rights retained by the respective copyright holders. There are no warranties expressed on implied';
		$this->ms_title					= 'Film Aficionado';
		$this->ms_keywords				= 'bluray, collection, tracker, movies, films, blu-ray, dvd, database, criterion, dvdaf';
		$this->ms_description			= "Your favorite spot for Film collecting.";
	}

	///////////////////////////////////////////////////////////////////
	function issueHeader()
	{
		if ( $this->ms_redirect && $this->mb_allow_redirect )
		{
			if ( substr($this->ms_redirect,0,7) != 'http://' )
				$this->ms_redirect = $this->ms_base_subdomain. (substr($this->ms_redirect,0,1) == '/' ? '' : '/'). $this->ms_redirect;
			header('HTTP/1.0 302');
			header("Location: {$this->ms_redirect}");
		}

		header('Expires: Wed, 1 Jan 2012 05:00:00 GMT');				// date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');		// always modified
		header('Cache-Control: no-store, no-cache, must-revalidate');	// HTTP/1.1
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');										// HTTP/1.0
	}
	//////////////////////////////////////////////////////////////
	function drawHead()
	{
		$s_javascript = $this->getHeaderJavaScript();

		echo
			"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n".
			"<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>\n".
			"<!--\n".
			"\n".
            "Film Aficionado is public domain software. Promotional material images,\n".
            "if present, are copyrighted by the respective copyright owners and should\n".
            "only be used under the provisions dictated by those copyright holders.\n".
            "There are no warranties expressed on implied.\n".
			"\n".
			"-->\n".
			"<head{$this->ms_head_attrib}>".
			"<title>{$this->ms_title}</title>".
			"<link rel='icon' href='http://www.filmaf.com/favicon.ico' type='image/x-icon' />".
			"<meta name='application-name' content='Film Aficionado'/>".
			"<meta name='msapplication-TileColor' content='#094ab2'/>".
			"<meta name='msapplication-TileImage' content='http://dv1.us/d1/filmaf-win8.png'/>".
			$this->ms_include_meta.
			($this->ms_keywords		? "<meta name='keywords' content='{$this->ms_keywords}' />" : '').
			($this->ms_description	? "<meta name='description' content='{$this->ms_description}' />" : '').
			"<meta http-equiv='Content-type' content='text/html; charset=UTF-8' />".
			$this->ms_include_css.
			$this->ms_include_js.
			($this->mb_include_cal	? "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/controls/calendar_{$this->mn_lib_version}.js'></script>" : '').
			($this->mb_include_menu	? "<script type='text/javascript' src='/lib/controls/hmenu_{$this->mn_lib_version}.js'></script>" : '').
			($s_javascript			? "<script language='javascript' type='text/javascript'>".$s_javascript."</script>" : '').
			"</head>\n";
	}
	//////////////////////////////////////////////////////////////
	function drawBodyBeg()
	{
		$str = $this->mb_corners ? '' : " style='background:#ffffff;padding:6px;'";

		if ( ($this->ms_onload_js = $this->getOnLoadJavaScript()) )
			echo "<body onload='filmafOnLoad()'{$str}>";
		else
			echo "<body{$str}>";

		if ( ($s_img = $this->echoPreloadImg()) )
			echo    "<div style='display:none'>{$s_img}</div>";
	}
	//////////////////////////////////////////////////////////////
	function drawCornersBeg()
	{
		if ( $this->mb_corners )
			echo	"<div id='corner'>".
				"<div class='corner-t'><div class='corner-tl'><div class='corner-tr'></div></div></div>".
				"<div class='corner-l'>".
				"<div class='corner-r'>".
				"<div class='corner-c'>";
	}
	//////////////////////////////////////////////////////
	function drawBodyTop()
	{
		$this->drawHeader();
		echo  "<div id='body'>";
	}
	//////////////////////////////////////////////
	function drawBodyPage()
	{
	}
	//////////////////////////////////////////////
	function drawBodyBottom()
	{
		echo  "</div>";
		$this->drawFooter();
	}
	//////////////////////////////////////////////////////
	function drawCornersEnd()
	{
		if ( $this->mb_corners ) echo
			"</div>".
			"<div class='corner-b'><div class='corner-bl'><div class='corner-br'></div></div></div>".
			"</div>".
			"</div>".
			"</div>";
	}
	//////////////////////////////////////////////////////////////
	function drawBodyEnd()
	{
		global $gn_host;

		$s_end_js = $this->getFooterJavaScript();
		if ( $this->ms_onload_js	) $s_end_js .= "function filmafOnLoad(){".$this->ms_onload_js."};";

		if ( $this->mb_menu_context	) echo "<ul id='context-menu' style='display:none'><li></li></ul>";
		if ( $s_end_js				) echo "<script language='javascript' type='text/javascript'>{$s_end_js}</script>";
		if ( $this->mb_show_trace	) CTrace::dump($this->mb_trace_environment, $this->ms_base_subdomain);

		echo	  "</body>".
			"</html>\n";
	}

	//////////////////////////////////////////////////////////////////////
	function logRequest()					{ CSql::query_and_free(dvdaf_logrequeststr($this->ms_user_id,0),0,__FILE__,__LINE__); }
	function validUserAccess()				{ return CUser_ACCESS_GRANTED; }
	function validRequest()					{ return true; }
	function validateDataSubmission()		{ return false; /* false for no changes */ }
	function getHeaderJavaScript()			{ return ''; }
	function getFooterJavaScript()			{ return ''; }
	function getOnLoadJavaScript()			{ return ''; }
	function initializeMenu()				{ }
	function drawMessages($b_box,$b_slim)	{ echo $this->getMessageString($b_box,$b_slim); }

	function echoPreloadImg()
	{
		return
			( $this->mb_include_cal ? "<img src='http://dv1.us/c0/active-bg.gif' />".
				"<img src='http://dv1.us/c0/dark-bg.gif' />".
				"<img src='http://dv1.us/c0/hover-bg.gif' />".
				"<img src='http://dv1.us/c0/menuarrow.gif' />".
				"<img src='http://dv1.us/c0/normal-bg.gif' />".
				"<img src='http://dv1.us/c0/rowhover-bg.gif' />".
				"<img src='http://dv1.us/c0/status-bg.gif' />".
				"<img src='http://dv1.us/c0/title-bg.gif' />".
				"<img src='http://dv1.us/c0/today-bg.gif' />" : '');
	}

	//////////////////////////////////////////////////////////////////////
	function drawHeader()
	{
		switch ( $this->mn_header_type )
		{
			case CWnd_HEADER_BIG:
				echo "<table id='header'><tr>".
					"<td><a href='{$this->ms_base_subdomain}'><img src='http://dv1.us/d1/filmaf.png' /></a></td>".
					($this->mb_facebook_div ? "<td><div id='fb-root'></div></td>" : '').
					"</tr></table>";
				break;

			case CWnd_HEADER_SMALL:
				echo "<div id='header-small'><img src='http://dv1.us/d1/filmaf-small.png' /></div>";
				break;
		}
	}
	function getHello($b_link_to_self)
	{
		if ( $this->mb_logged_in )
			return "Hello, ".
				($b_link_to_self ? "<a href='{$this->ms_user_subdomain}'>{$this->ms_user_id}</a>" : $this->ms_user_id).
				". Thank you for your support. ".
				"(<a href='{$this->ms_base_subdomain}/utils/logout.html'>Not {$this->ms_user_id}</a>?)<br />";
		else
			return "Hello there! Follow this link to <a href='{$this->ms_base_subdomain}/utils/register.html'>create an account</a> or ".
				"to access your collection <a href='{$this->ms_base_subdomain}/utils/login.html'>sign in</a>.<br />";
	}
	function drawFooter()
	{	global $gf_start_clock, $gn_sql_time, $gs_db_server;

		$s_time = '';
		$s_cont = '&nbsp;';

		if ( $this->mn_footer_type & CWnd_FOOTER_NONE )
			return;

		if ( $this->mn_footer_type & CWnd_FOOTER_TIME )
		{
			$n_http_time = sprintf("%0.1f", (CTime::get_time() - $gf_start_clock) * 1000);
			$gn_sql_time = sprintf("%0.1f", $gn_sql_time);
			$s_time     .= "&nbsp;&nbsp;&nbsp;{$gn_sql_time}/{$n_http_time}&nbsp;ms";
		}

		if ( $this->mn_footer_type & CWnd_FOOTER_CONTACT )
		{
			$s_cont = "<a href='{$this->ms_base_subdomain}/term-of-use'>Terms of Use</a> | <a href='{$this->ms_base_subdomain}/contact-center'>Contact Center</a>".
				( $this->ms_footer_supp
					? " | <a href='{$this->ms_base_subdomain}/report-problem'>Report a Problem</a><br />{$this->ms_footer_supp}"
					: "<br /><a href='{$this->ms_base_subdomain}/report-problem'>Report a Problem</a>"
				);
		}

		echo  "<div id='footer'>".
			"<div class='ruler'>&nbsp;</div>".
			"<div id='footer-copy'>Release&nbsp;{$this->ms_release}{$s_time}&nbsp;@{$gs_db_server}<br />{$this->ms_copyright}.</div>".
			"<div id='footer-cont'>{$s_cont}</div>".
			"<div class='synch'>&nbsp;</div>".
			"</div>";
	}

	//////////////////////////////////////////////////////////////////////
	function getMessageString($b_box,$b_slim)
	{
		$str = '';

		if ( $this->ms_display_error	) $str .= "<div id='msg-err'>{$this->ms_display_error}</div>";
		if ( $this->ms_display_affected	) $str .= "<div id='msg-affect'>{$this->ms_display_affected}</div>";
		if ( $str && $b_box				) $str  = "<div class='msgbox-a'".($b_slim ? " style='margin-top:4px;margin-bottom:0'" : '').">".
			"<div class='msgbox-b'><div class='msgbox-c'><div class='msgbox-d'>{$str}</div></div></div></div>";
		if ( $this->ms_display_what		) $str .= "<div id='msg-what'>{$this->ms_display_what}</div>";

		return $str;
	}
	function showMsgOnly($str)
	{
		CWnd::drawBodyTop();
		echo  "<div class='msgbox-a'><div class='msgbox-b'><div class='msgbox-c'><div class='msgbox-d'>".
			"<div class='msgbox'>".
			($this->mb_logged_in ? "<p>Dear ".ucfirst($this->ms_user_id).",</p>" : "<p>Dear FilmAf supporter,</p>").
			$str.
			"<p>Thank you.</p>".
			"</div>".
			"</div></div></div></div>";
		CWnd::drawBodyBottom();
	}
	function drawMaintenanceDowntime()
	{
		$off_beg = 'Thu 9:00 AM EST';
		$off_end = 'Thu 9:20 PM EST';
		$this->showMsgOnly(
			"<p>Film Aficionado is down for maintenance.</p>".
			"<p>The work started on <strong>$off_beg</strong> and is expected to end on <strong>$off_end</strong>.</p>".
			"<p>We apologize for the inconvenience.</p>");
	}
	function drawBackupDowntime()
	{
		$this->showMsgOnly(
			"<p>Film Aficionado is down for some quick maintenance.</p>".
			"<p>This usually takes 5 to 10 minutes.  Please try again in a little bit.  Thanks.</p>".
			"<p>We apologize for the inconvenience.</p>");
	}
	function NotConnected()
	{
		$this->showMsgOnly(
			"We could not get a connection to the database".
			"<p>FilmAf may be too busy, down for maintenance, or experiencing problems.</p>".
			$this->getWaitAndRetry());
	}
	function drawNoAccess($n_invalid_access)
	{
		switch ( $n_invalid_access )
		{
			case CUser_NOACCESS_GUEST:	 $this->showMsgOnly(
				"<p>This level of access requires that you be logged in. Please click <a href='javascript:void(Win.reauth(0))'>here</a>.</p>".
				"<p>Once you are logged in you can navigate back here or hit refresh to reload this page.</p>");
				break;
			case CUser_NOACCESS_USER:	 $this->showMsgOnly(
				"<p>It seems that you do not have access to this function. Please <a href='javascript:void(Win.reauth(0))'>re-authenticate here</a> if you want to try again.</p>");
				break;
			case CUser_NOACCESS_SESSION: $this->showMsgOnly(
				"<p>This level of access requires that you be re-authenticated. Please click <a href='javascript:void(Win.reauth(0))'>here</a>.</p>".
				"<p>Once you have done that you can navigate back here or hit refresh to reload this page.</p>");
				break;
		}
	}
	function drawRedirect()
	{
		$this->showMsgOnly("<p>Your browser should have taken you <a href='{$this->ms_redirect}'>here</a>.</p>");
	}
	function getWaitAndRetry()
	{
		return	"<p>Please <strong>wait a few minutes</strong>. Then refresh your browser. If that does not work please check ".
			"<a href='http://dvdaf.net/'>dvdaf.net</a> and <a href='http://twitter.com/dvdaf'>Twitter</a> for updates.</p>".

			"<p>If you can, please attach a screen print showing the error and the URL.  They go a long way helping us ".
			"find problems.</p>".

			"<p>We apologize for the inconvenience.</p>";
	}

	//////////////////////////////////////////////////////////////////////
	function verifyDomain()
	{
		$s_host = dvdaf3_getvalue('HTTP_HOST', DVDAF3_SERVER|DVDAF3_LOWER);
		$s_host = str_replace('dvdaf.','filmaf.',$s_host);

		$n_pos  = strpos($s_host, $this->ms_unatrib_subdomain);
		$s_subd = $n_pos >  0 ? substr($s_host,0,$n_pos) : '';
		$s_host = $n_pos >= 0 ? substr($s_host,$n_pos) : '.'.$s_host;

		if ( strlen($s_subd) <= 2  || strpos($s_subd,'.') )
		{
			$this->ms_redirect = $this->ms_base_subdomain;
			return false;
		}

		if ( $s_subd != 'www' )
		{
			$this->ms_view_id		 = $s_subd;
			$this->ms_view_subdomain = "http://{$s_subd}{$this->ms_unatrib_subdomain}";
			$this->mb_collection	 = true;
		}

		return true;
	}
	function verifyUser()
	{
		// take care of the original cookie to track terminals
		if ( dvdaf3_getvalue('orig', DVDAF3_COOKIE) == '' )
		{
			$s_orig = dvdaf3_getvalue('REMOTE_ADDR', DVDAF3_SERVER). '|'. (time()+microtime());
			setcookie('orig', $s_orig, mktime(0,0,0,3,1,date("Y") + 1), '/', $this->ms_unatrib_subdomain, 0);
		}

		// load cookies
		$s_user = dvdaf3_getvalue('user', DVDAF3_COOKIE);
		$s_parm = dvdaf3_getvalue('parm', DVDAF3_COOKIE);
		$s_sess = dvdaf3_getvalue('sess', DVDAF3_COOKIE);
		$s_md5p = substr($s_parm,0,32);
		$s_parm = substr($s_parm,32);
		$b_good = $s_md5p == CHash::hash_parm($s_user . $s_parm);
		$b_remember_me = false;

		if ( $s_sess )
		{
			$s_md5p = substr($s_sess,0,32);
			$s_sess = substr($s_sess,32);
			$b_good = $s_md5p == CHash::hash_sess($s_user . $s_sess);
		}

		$this->ms_user_id = 'guest';
		if ( $b_good )
		{
			$s_parm = explode('|', $s_parm);
			$n_parm = count($s_parm);
			$s_sess = explode('|', $s_sess);
			$n_sess = count($s_sess);
			$b_good = strlen($s_user) > 2 && $s_user != 'guest';
			if ( $b_good )
			{
				$b_remember_me				  = $n_parm > 0 && $s_parm[0] == 2;
				$this->mb_logged_in_this_sess = $n_sess > 0 && $s_sess[0] == 2;
				$b_good = $b_remember_me || $this->mb_logged_in_this_sess;
				if ( $b_good )
				{
					$this->ms_user_id = $s_user;
					if ( ($this->mb_logged_in = $this->ms_user_id != 'guest') )
					{
						$this->mb_view_self      = $this->ms_view_id == $this->ms_user_id;
						$this->ms_user_subdomain = "http://{$this->ms_user_id}{$this->ms_unatrib_subdomain}";
						if ( $this->mb_get_user_status ) $this->getUserStars();
					}
				}
			}
		}
	}
	function loginUser($b_remember_me)
	{
		$s_expire_time = mktime(0, 0, 0, 3, 1, date("Y") + 1);
		$this->mb_logged_in_this_sess = true;

		$s_user = $this->ms_user_id;
		$s_parm = $b_remember_me ? '2': '1';					// separate additional values with pipe '|'
		$s_parm = CHash::hash_parm($s_user . $s_parm). $s_parm;
		$s_sess = $this->mb_logged_in_this_sess ? '2' : '0';	// separate additional values with pipe '|'
		$s_sess = CHash::hash_sess($s_user . $s_sess). $s_sess;
		setcookie('user', $s_user, $s_expire_time, '/', $this->ms_unatrib_subdomain, 0);
		setcookie('parm', $s_parm, $s_expire_time, '/', $this->ms_unatrib_subdomain, 0);
		setcookie('sess', $s_sess,              0, '/', $this->ms_unatrib_subdomain, 0);
	}
	function logoutUser()
	{
		$s_delete_time = mktime(0, 0, 0, 3, 1, 2012);
		setcookie('user', '', $s_delete_time, '/', $this->ms_unatrib_subdomain);
		setcookie('sess', '', $s_delete_time, '/', $this->ms_unatrib_subdomain);
	}

	//////////////////////////////////////////////////////////////////////
	function getUserStars()
	{
		if ( $this->mn_user_stars != CWnd_STAR_NOT_INIT	)
			return $this->mn_user_stars;

		if ( $this->mb_logged_in )
			if ( ($rr = CSql::query_and_fetch("SELECT contributor_cd, membership_cd, moderator_cd, pinned, last_visit_tm, datediff(now(),last_visit_tm) days FROM dvdaf_user WHERE user_id = '{$this->ms_user_id}'", 0,__FILE__,__LINE__)) )
			{
				$this->mn_moderator_cd		= intval($rr['moderator_cd']);
				$this->mn_membership_cd		= intval($rr['membership_cd']);
				$this->mn_contributor_cd	= intval($rr['contributor_cd']);
				$this->ms_user_pinned		= $rr['pinned'] != '-' ? $rr['pinned'] : '';
				$this->mn_access_level_cd	= max($this->mn_moderator_cd, $this->mn_membership_cd, $this->mn_contributor_cd);
				$this->mn_user_stars		= $this->transStars($this->mn_access_level_cd);
				$this->mb_mod				= $this->mn_moderator_cd >= 5;
				if ( $rr['last_visit_tm'] == '' || $rr['days'] > 0 )
				{
					CSql::query_and_free("UPDATE dvdaf_user SET last_visit_tm = now() WHERE user_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
				}
			}
			else
				$this->mn_user_stars = CWnd_STAR_USER_NOT_EXIST;
		else
			$this->mn_user_stars = CWnd_STAR_NO_USER;

		if ( $this->mb_view_self )
			$this->mn_view_stars = $this->mn_user_stars;

		return $this->mn_user_stars;
	}
	function getViewStars()
	{
		if ( $this->mn_view_stars != CWnd_STAR_NOT_INIT	)
			return $this->mn_view_stars;

		if ( $this->mb_view_self )
			return $this->getUserStars();

		if ( $this->mb_collection )
			if ( ($rr = CSql::query_and_fetch("SELECT contributor_cd, membership_cd, moderator_cd FROM dvdaf_user WHERE user_id = '{$this->ms_view_id}'", 0,__FILE__,__LINE__)) )
				$this->mn_view_stars = $this->transStars(max(intval($rr['moderator_cd']), intval($rr['membership_cd']), intval($rr['contributor_cd'])));
			else
				$this->mn_view_stars = CWnd_STAR_USER_NOT_EXIST;
		else
			$this->mn_view_stars = CWnd_STAR_NO_USER;

		return $this->mn_view_stars;
	}
	function transStars($n_stars)
	{
		if ( $n_stars <= 9 )
		{
			if ( $n_stars >= 5 ) return $n_stars;
			if ( $n_stars >= 2 ) return $n_stars + 1;
			if ( $n_stars == 1 ) return 1;
		}
		return 0;
	}
	function badRequester()
	{
		return false;
	}
	function repondBadRequest()
	{
		header('HTTP/1.1 403 Forbidden');
		echo "<html><head></head><body></body></html>";
	}
}

//////////////////////////////////////////////////////////////////////////

class CHash
{
	function hash_user    ($s_value) { return md5($s_value . "gx<B:2ip|iv-Q'{3"); }
	function hash_parm    ($s_value) { return md5($s_value . 'ZB;Xz0@_N]mYru%3'); }
	function hash_sess    ($s_value) { return md5($s_value . 'a2leB +g/{Ut2k:3'); }
	function hash_password($s_value) { return md5($s_value . 'm{v~Cqe(3X/r>aV.'); } // *** NEVER CHANGE THIS VALUE ***
}

//////////////////////////////////////////////////////////////////////////

/*
SELECT contributor_cd, membership_cd, moderator_cd, count(*) FROM dvdaf_user group by contributor_cd, membership_cd, moderator_cd;
+----------------+---------------+--------------+----------+
| contributor_cd | membership_cd | moderator_cd | count(*) |
+----------------+---------------+--------------+----------+
| -              | -             | -            |    91337 |
| -              | -             | 5            |        4 |
| -              | 1             | -            |       52 |
| -              | 2             | -            |       53 |
| -              | 3             | -            |        8 |
| -              | 4             | -            |        6 |
| -              | 5             | -            |       19 |
| -              | 6             | -            |        4 |
| 1              | -             | -            |       10 |
| 2              | -             | -            |        1 |
| 2              | -             | 5            |        2 |
+----------------+---------------+--------------+----------+
1 -  $10 donation      1.0 stars (smb1.gif) Supporting Member
2 -  $20 donation      2.0 stars (smb3.gif) Sponsor Member
3 -  $30 donation      2.5 stars (smb4.gif) Donor  Member
4 -  $50 donation      3.0 stars (smb5.gif) Fellow Member
5 - charter member     3.0 stars (smb5.gif)
6 - $100 donation      3.5 stars (smb6.gif) Benefactor Member
7 - $200 donation      4.0 stars (smb7.gif) Patron
8 - $500 donation      4.5 stars (smb8.gif) Sponsor Patron
9 - $1000 donation     5.0 stars (smb9.gif) Benefactor Patron
*/

?>
