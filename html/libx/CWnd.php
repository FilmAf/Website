<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CTrace.php';
require $gs_root.'/lib/CSqlMysql.php';

define('CWnd_PAGE_TOPMENU'	,     0);	// $this->mn_page_type
define('CWnd_PAGE_SIDEBAR'	,     1);
define('CWnd_PAGE_PLAIN'	,     2);
define('CWnd_PAGE_NOHEADER'	,     3);

define('CWnd_FOOTER_CONTACT'	,     1);	// $this->mn_footer_type
define('CWnd_FOOTER_TIME'	,     2);
define('CWnd_FOOTER_W3C'	,     4);
define('CWnd_FOOTER_W3C_SMALL'	,     8);
define('CWnd_FOOTER_ACCURACY'	,    16);

define('CWnd_ZOOM_NONE'		,     0);
define('CWnd_ZOOM_STAR'		,     1);	// show if member is star or display a star's collection
define('CWnd_ZOOM_ALL'		,     2);

define('CUser_ACCESS_NOTHING'	,        0);	// $this->mn_user_access
define('CUser_ACCESS_DVD_MASK'	,     4095);	// ---- ---- ---- ---- ---- xxxx xxxx xxxx
define('CUser_ACCESS_FILM_MASK'	, 16773120);	// ---- ---- xxxx xxxx xxxx ---- ---- ----
define('CUser_ACCESS_REVIEWER'	, 16777216);	// ---- ---1 ---- ---- ---- ---- ---- ----

define('CUser_USER_GUEST'	,     0);	// $this->mn_user_authentic
define('CUser_USER_REMEMBERED'	,     1);
define('CUser_USER_AUTHENTICATED',    2);

define('CUser_SESSION_NONE'	,     0);	// $this->mn_user_session
define('CUser_SESSION_TEMP'	,     1);
define('CUser_SESSION_PERM'	,     2);

//////////////////////////////////////////////////////////////////////////

class CWnd
{
    function CWnd()
    {
	global $gb_trace_sql;

	$this->constructor();					// 1.0
	$gb_trace_sql = $this->mb_trace_sql;
	if ( $this->maintenanceDowntime() )
	{
	    $this->issueHeader();
	    $this->drawBodyTag();				// 7.0.a
	    $this->drawMaintenanceDowntime();			// 7.x
	    $this->drawBodyEndTag();				// 7.3.a
	}
	else
	{
	    if ( $this->validServer() )				// 2.0
	    {
		$this->verifyUser();				// 3.0
		if ( $this->mb_log_request )			// 3.1
		{
		    $this->logRequest();
		}
		if ( $this->validRequest() )			// 4.0
		{
		    if ( $this->validUserAccess() )		// 5.0
		    {
			if ( $this->badRequester() )
			{
			    $this->repondBadRequest();
			    return;
			}

			$this->validateDataSubmission();	// 6.0
			$this->issueHeader();			// 6.1
			$this->drawHeader();			// 6.2
			if ( $this->mb_frameset )
			{
			    $this->drawBodyTag();		// 7.0.a
			    $this->drawFrameSet();		// 7.x
			    $this->drawBodyEndTag();		// 7.3.a
			}
			else
			{
			    if ( $this->mb_initialize_menu )
				$this->initializeMenu();	// 7.0
			    $this->drawBodyTag();		// 7.0.a
			    $this->drawBodyTop();		// 7.1
			    $this->drawBodyTopMargin();		// 7.1.a
			    $this->drawBodyTopMenu();		// 7.1.b
			    $this->drawBodyPage();		// 7.2
			    $this->drawBodyBottomMargin();	// 7.2.a
			    $this->drawBodyBottom();		// 7.3
			    $this->drawBodyEndTag();		// 7.3.a
			}


		    }
		    else
		    {
			$this->issueHeader();			// 6.1
			$this->drawBodyTag();			// 7.0.a
			$this->drawNoAccess();			// 7.x
			$this->drawBodyEndTag();		// 7.3.a
		    }
		}
		else
		{
		    $this->redirectBadRequest();		// 4.1
		}
	    }
	    else
	    {
		$this->redirectBadRequest();			// 4.1
	    }
	}
	$this->destructor();					// 8.0
    }

    /* ------------------------------------------------------------------- *\
     *	CWnd::constructor
     *	CWnd::logRequest
     *	CWnd::initializeMenu
     *	CWnd::destructor
    \* ------------------------------------------------------------------- */
    function constructor() // <<--------------------------------<< 1.0
    {
	global $gn_host, $gs_sql_password;

	// $this->ms_pic_dir
	// $this->ms_pic_domain

	CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	$this->mb_trace_functions	= false;
	$this->mb_show_trace		= false;
	$this->mb_trace_variables	= false;
	$this->mb_trace_environment	= false;
	$this->mb_trace_sql		= false;
	$this->mb_log_request		= false;
	$this->mb_allow_redirect	= true;
	$this->mb_initialize_menu	= true;
	$this->ms_menu_breadcrumbs_li	= '';
	$this->mn_echo_zoom		= CWnd_ZOOM_NONE;

	$this->mn_lib_version		= 37;
	$this->ms_release		= '2.2.8.3';

	$this->mn_page_type		= CWnd_PAGE_SIDEBAR;
	$this->mn_footer_type		= CWnd_FOOTER_TIME | CWnd_FOOTER_CONTACT | CWnd_FOOTER_W3C;
	$this->ms_onload		= '';
	$this->mb_include_cal		= false;
	$this->mb_include_menu		= false;
	$this->ms_include_js		= '';
	$this->ms_include_more		= '';
	$this->ms_script_end		= '';

	$this->mb_frameset		= false;
	$this->ms_margin_top		= '';
	$this->ms_margin_bottom		= '10pt';
	$this->ms_margin_left		= '';
	$this->ms_margin_right		= '';
	$this->ms_margin_body		= true;
	$this->mn_max_width_px		= 0;

	$this->ms_search		= '';
	$this->ms_wbr			= substr(dvdaf_getvalue('HTTP_USER_AGENT', DVDAF_SERVER | DVDAF_LOWER),0,13) == 'w3c_validator' ? '' : '<wbr />';
	$this->ms_redirect		= '';
	$this->mn_web_site		= $gn_host;

	$this->mn_user_access		= CUser_ACCESS_NOTHING;
	$this->mn_user_authentic	= CUser_USER_GUEST;
	$this->mn_user_session		= CUser_SESSION_NONE;
	$this->ms_user_id		= 'guest';
	$this->ms_view_id		= 'www';
	$this->mb_get_user_status	= false;
	$this->mn_user_stars		= -2;
	$this->mn_view_stars		= -2;
	$this->mn_contributor_cd	= 0;
	$this->mn_membership_cd		= 0;
	$this->mn_moderator_cd		= 0;
	$this->mn_access_level_cd	= 0;

	$this->mb_view_self		= false;
	$this->ms_menu_mode		= '';
	$this->ms_menu_sub		= '';
	$this->ms_menu_top		= '';
	$this->ms_menu_trail		= '';
	$this->ms_menu_search		= '';
	$this->ms_menu_context		= '';
	$this->mn_page_default		= dvdaf_getvalue('page', DVDAF_COOKIE);
	$this->ms_display_affected	= '';
	$this->ms_display_what		= '';
	$this->ms_display_error		= '';

	if ( $this->mn_page_default < 1    ) $this->mn_page_default =   50; else
	if ( $this->mn_page_default > 1000 ) $this->mn_page_default = 1000;

	switch ( $this->mn_web_site )
	{
	case HOST_FILMAF_COM:
	    $this->ms_base_subdomain	= 'http://www.filmaf.com';
	    $this->ms_unatrib_subdomain	= '.filmaf.com';
	    $this->ms_cookie_domain		= '.filmaf.com';
	    $this->ms_pics_icons		= 'http://dv1.us/di';
	    $this->ms_pics_large		= "{$this->ms_base_subdomain}/pics";
	    $this->mn_web_site			= HOST_FILMAF_COM;
	    break;
	case HOST_FILMAF_EDU:
	    $this->ms_base_subdomain	= 'http://www.filmaf.edu';
	    $this->ms_unatrib_subdomain	= '.filmaf.edu';
	    $this->ms_cookie_domain		= '.filmaf.edu';
	    $this->ms_pics_icons		= 'http://dv1.us/di';
	    $this->ms_pics_large		= "{$this->ms_base_subdomain}/pics";
	    $this->mn_web_site			= HOST_FILMAF_COM;
	    break;
	default:
	    exit('Unable to identify target libraries');
	    break;
	}

	$this->ms_style				= "{$this->ms_base_subdomain}/styles/00";
	$this->ms_pics_thumbs		= 'http://dv1.us/thumbs';
	$this->ms_pics_style		= "{$this->ms_pics_icons}/00";
	$this->ms_user_subdomain	= $this->ms_base_subdomain;
	$this->ms_view_subdomain	= $this->ms_base_subdomain;
	$this->ms_header_title		= '';

	switch ( $this->mn_web_site )
	{
	case HOST_FILMAF_COM:
	    $this->ms_title			= 'Film Aficionado';
	    $this->ms_keywords		= '';
	    $this->ms_description	= '';
	    $this->ms_pic_left		= '';
	    $this->ms_pic_right		= '';
	    $this->ms_include_css	= "<link rel='stylesheet' type='text/css' href='{$this->ms_style}/filmaf_{$this->mn_lib_version}.css' />\n";
	    $this->ms_site_name		= "Film Aficionado";
	    break;
	}
	CSql::connect(__FILE__,__LINE__);
    }

    function logRequest() // <<---------------------------------<< 3.1
    {
	CSql::query_and_free(dvdaf_logrequeststr($this->ms_user_id,0), 0,__FILE__,__LINE__);
    }

    function initializeMenu() // <<-----------------------------<< 7.0
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	$s_class		= "class='ma'";
	$s_href_base		= "href='{$this->ms_base_subdomain}";
	$s_href_user		= "href='{$this->ms_user_subdomain}";
	$s_href_rele		= "$s_href_base/releases.html";
	$s_top_begin		= "<a $s_class $s_href_base/' title='Film Aficionado front page'>FilmAf</a>&nbsp;| ".
				  "<a $s_class $s_href_base/price.html' title='Price Comparison'>Price&nbsp;Comparison</a>&nbsp;| ".
				  "<a $s_class href='http://dvdaf.net/' title='FilmAf Forums'>Support Forums</a>&nbsp;| ";
	$s_map_faq_br		= "<a $s_class $s_href_base/utils/site-map.html' title='Site map'>Site&nbsp;Map</a><br />";
	$s_top_releases		= "<a $s_class $s_href_rele?med=D&init_form=med_0_D' title='New DVD releases'>DVD Releases</a>&nbsp;| ".
				  "<a $s_class $s_href_rele?med=B&init_form=med_0_B' title='New Blu-ray releases'>Blu-ray Releases</a>&nbsp;| ";

	if ( $this->mn_user_authentic == CUser_USER_GUEST )
	{
	    $this->ms_menu_top	= $s_top_begin.
				  $s_map_faq_br.
				  $s_top_releases.
				  "<a $s_class $s_href_base/utils/register.html' title='Become a Film Aficionado member'>Register</a>&nbsp;| ".
				  "<a $s_class $s_href_base/utils/login.html' title='Welcome back!'>Login</a>";
	    $this->ms_menu_maint= "<li><a $s_href_base/utils/login.html'>Login</a></li>".
				  "<li><a $s_href_base/utils/register.html'>Create an Account</a></li>";
	}
	else
	{
	    $this->ms_menu_top	= $s_top_begin.
				  $s_map_faq_br.
				  "<a $s_class $s_href_user/' title='Create and maintain your DVD collection'>My&nbsp;Home</a>&nbsp;| ".
				  "<a $s_class $s_href_base/utils/folders.html' title='Create and maintain folders for your collection' target='folders'>My&nbsp;Folders</a>&nbsp;| ".
				  $s_top_releases.
				  "<a $s_class $s_href_base/utils/logout.html' title='Logout... We hope you will be back soon!'>Logout</a>";
	    $this->ms_menu_maint= '';
	}
	$this->ms_menu_quick	= "Support FilmAf by <a href='http://www{$this->ms_unatrib_subdomain}/utils/help-filmaf.html' target='_blank' class='ml'>becoming a Star Member</a> and by using our Quick Links: ".
				  "<a href='http://www.amazon.com/exec/obidos/redirect?tag=dvdaficionado&path=tg/browse/-/130' target='_blank' class='ml'>Amazon</a> ".
				  "<a href='http://www.amazon.ca/exec/obidos/redirect-home?site=amazon&tag=dvdaficiona05-20' target='_blank' class='mj'>Amz.<span class='mk'>ca</span></a> ".
				  "<a href='http://www.amazon.co.uk/exec/obidos/redirect-home?tag=dvdaficionado-21&site=amazon' target='_blank' class='mj'>Amz.<span class='mk'>uk</span></a> ".
				  "<a href='http://www.amazon.fr/exec/obidos/redirect-home?site=amazon&tag=dvdaficiona01-21' target='_blank' class='mj'>Amz.<span class='mk'>fr</span></a> ".
				  "<a href='http://www.amazon.de/exec/obidos/redirect-home?tag=dvdaficiona0e-21&site=home' target='_blank' class='mj'>Amz.<span class='mk'>de</span></a> ".
				  "<a href='http://www.amazon.co.jp/exec/obidos/redirect?tag=dvdaficionado-22&path=tg/browse/-/489986' target='_blank' class='mj'>Amz.<span class='mk'>jp</span></a> ".
				  "<a href='http://www.amazon.it/b?node=412606031&tag=dvaf-21?_encoding=UTF8&tag=dvaf-21&linkCode=ur2&camp=3370&creative=23322' target='_blank' class='mj'>Amz.<span class='mk'>it</span></a><span style='color:red'> &lt;- new addition: Italy</span>".
//				  "<a href='http://www.jdoqocy.com/click-1161233-10389848' target='_blank' class='mj'>Buy.com</a>".
//				      "<img src='http://www.ftjcfx.com/image-1161233-10389848' width='1' height='1' border='0' alt='' /> ".
//				  "<a href='http://www.anrdoezrs.net/click-1161233-10674925?url=http%3A%2F%2Fwww.deepdiscount.com%2F' target='_blank' class='mj'>Deep-Discount</a> ".
//				      "<img src='http://www.tqlkg.com/image-1161233-10674925' width='1' height='1' border='0' alt='' /> ".
//				  "<a href='http://www.dvdempire.com/index.asp?partner_id=54845827' target='_blank' class='mj'>DVD-Empire</a> ".
//				  "<a href='http://www.dvdplanet.com/index.cfm?affiliate=DA' target='_blank' class='mj'>DVD-Planet</a> ".
//				  "<a href='http://click.linksynergy.com/fs-bin/click?id=cwyRRur06I4&offerid=57189.10001580&type=3&subid=0' target='_blank' class='mj'>Overstock</a>".
//				      "<img src='http://ad.linksynergy.com/fs-bin/show?id=cwyRRur06I4&bids=57189.10001580&type=3&subid=0' width='1' height='1' border='0' alt='' /> ".
				  "";

	$this->ms_menu_maint   .= "<li></li>".
				  "<li>New Releases".
				    "<ul>".
				      "<li>DVD".
					"<ul>".
					  "<li><a $s_href_rele?pubct=us&med=D&init_form=str0_pubct_us*med_0_D'><span style='color:blue'>U.S.</span></a></li>".
					  "<li><a $s_href_rele?pubct=uk&med=D&init_form=str0_pubct_uk*med_0_D'><span style='color:blue'>U.K.</span></a></li>".
					  "<li><a $s_href_rele?pubct=ca&med=D&init_form=str0_pubct_ca*med_0_D'>Canada</a></li>".
					  "<li><a $s_href_rele?pubct=fr&med=D&init_form=str0_pubct_fr*med_0_D'>France</a></li>".
					  "<li><a $s_href_rele?pubct=de&med=D&init_form=str0_pubct_de*med_0_D'>Germany</a></li>".
					  "<li><a $s_href_rele?pubct=it&med=D&init_form=str0_pubct_it*med_0_D'>Italy</a></li>".
					  "<li><a $s_href_rele?pubct=jp&med=D&init_form=str0_pubct_jp*med_0_D'>Japan</a></li>".
					  "<li><a $s_href_rele?pubct=.ne.us%2Cuk%2Cca%2Cfr%2Cde%2Cit%2Cjp&med=D&init_form=str0_pubct_%3C%3Eus%2Cuk%2Cca%2Cfr%2Cde%2Cit%2Cjp*med_0_D'>Others</a></li>".
					  "<li><a $s_href_rele?med=D&init_form=med_0_D'><span style='color:red'>All</span></a></li>".
					"</ul>".
				      "</li>".
				      "<li>Blu-ray".
					"<ul>".
					  "<li><a $s_href_rele?pubct=us&med=B&init_form=str0_pubct_us*med_0_B'><span style='color:blue'>U.S.</span></a></li>".
					  "<li><a $s_href_rele?pubct=uk&med=B&init_form=str0_pubct_uk*med_0_B'><span style='color:blue'>U.K.</span></a></li>".
					  "<li><a $s_href_rele?pubct=ca&med=B&init_form=str0_pubct_ca*med_0_B'>Canada</a></li>".
					  "<li><a $s_href_rele?pubct=fr&med=B&init_form=str0_pubct_fr*med_0_B'>France</a></li>".
					  "<li><a $s_href_rele?pubct=de&med=B&init_form=str0_pubct_de*med_0_B'>Germany</a></li>".
					  "<li><a $s_href_rele?pubct=it&med=B&init_form=str0_pubct_it*med_0_B'>Italy</a></li>".
					  "<li><a $s_href_rele?pubct=jp&med=B&init_form=str0_pubct_jp*med_0_B'>Japan</a></li>".
					  "<li><a $s_href_rele?pubct=.ne.us%2Cuk%2Cca%2Cfr%2Cde%2Cit%2Cjp&med=B&init_form=str0_pubct_%3C%3Eus%2Cuk%2Cca%2Cfr%2Cde%2Cit%2Cjp*med_0_B'>Others</a></li>".
					  "<li><a $s_href_rele?med=B&init_form=med_0_B'><span style='color:red'>All</span></a></li>".
					"</ul>".
				      "</li>".
				    "</ul>".
				  "</li>".
				  "<li>Contributions".
				    "<ul>".
				      "<li><a $s_href_base/utils/dvd-edit.html?dvd=new' target='targetedit'>Submit New Title</a></li>".
				      "<li><a $s_href_base/utils/dvd-edit.html' target='targetedit'>Submission History</a></li>".
      ( $this->mn_moderator_cd >= 5 ? "<li><a $s_href_base/utils/dvd-appr.html' target='targetappr'>Submission Processing</a></li>" : '' ).
				      "<li></li>".
				      "<li><a $s_href_base/utils/help-filmaf.html'>Donations</a></li>".
				    "</ul>".
				  "</li>".
				  "<li></li>".
				  "<li><a $s_href_base/price.html'>Price Comparison</a></li>";

	$this->ms_menu_trail	= "FilmAf&nbsp;";
    }

    function destructor() // <<---------------------------------<< 8.0
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	if ( ! $this->mb_frameset )
	{
	    if ( $this->mb_show_trace )
	    {
		if ( $this->mb_trace_variables ) $this->dump();
		CTrace::dump($this->mb_trace_environment, $this->ms_base_subdomain);
	    }
	}

	echo
	// Start Quantcast tag
	"<script type='text/javascript'>_qoptions={qacct:'p-f4DZHT-AOEy72'};</script>".
	"<script type='text/javascript' src='http://edge.quantserve.com/quant.js'></script>".
	"<noscript><img src='http://pixel.quantserve.com/pixel/p-f4DZHT-AOEy72.gif' style='display: none;' border='0' height='1' width='1' alt=''/></noscript>".
	// End Quantcast tag
	// Start Google Analytics tag
	"<script type='text/javascript'>var gaJsHost = (('https:' == document.location.protocol) ? 'https://ssl.' : 'http://www.');document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));</script>".
	"<script type='text/javascript'>try {var pageTracker = _gat._getTracker('UA-8030055-2');pageTracker._trackPageview();} catch(err) {}</script>".
	// End Google Analytics tag
	"</body></html>\n";
    }

    /* ------------------------------------------------------------------- *\
     *	CWnd::issueHeader
     *	CWnd::drawHeader
     *	CWnd::echoJavaScript
    \* ------------------------------------------------------------------- */
    function issueHeader() // <<--------------------------------<< 6.1
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

	if ( $this->ms_redirect && $this->mb_allow_redirect )
	{
	    if ( substr($this->ms_redirect,0,7) != 'http://' )
		$this->ms_redirect = $this->ms_base_subdomain. (substr($this->ms_redirect,0,1) == '/' ? '' : '/'). $this->ms_redirect;
	    header('HTTP/1.0 302');
	    header("Location: {$this->ms_redirect}");
	}

	header('Expires: Wed, 1 Jan 2007 05:00:00 GMT');		// date in the past
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');	// always modified
	header('Cache-Control: no-store, no-cache, must-revalidate');	// HTTP/1.1
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');					// HTTP/1.0
    }

    function drawHeader() // <<---------------------------------<< 6.2
    {
	if ( $this->mb_include_cal )
	{
	    $this->ms_include_more .=
		"<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/controls/calendar_{$this->mn_lib_version}.js'></script>\n";
	}
	if ( $this->mb_include_menu )
	{
	    $this->ms_include_more .=
		"<script type='text/javascript' src='/lib/controls/hmenu_{$this->mn_lib_version}.js'></script>\n";
	}

	$s_more_javascript = $this->echoJavaScript();

	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	echo
	"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'DTD/xhtml1-transitional.dtd'>\n".
	"<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>\n".
	"<!--\n".
	"\n".
	"Film Aficionado is public domain software. Promotional material images,\n".
	"if present, are copyrighted by the respective copyright owners and should\n".
	"only be used under the provisions dictated by those copyright holders.\n".
	"There are no warranties expressed on implied.\n".
	"\n".
	"-->\n".
	"<head>\n".
	  "<title>". ($this->ms_header_title ? $this->ms_header_title : $this->ms_title) ."</title>\n".
	  "<link rel='shortcut icon' href='/favicon.ico' />\n".
	  "<meta name='keywords' content='{$this->ms_keywords}' />\n".
	  "<meta name='description' content='{$this->ms_description}' />\n".
	  "<meta name='robots' content='all' />\n".
	  "<meta http-equiv='Content-type' content='text/html; charset=UTF-8' />\n".
	  $this->ms_include_css.
	  $this->ms_include_js.
	  $this->ms_include_more.
	  ($s_more_javascript ? "<script language='javascript' type='text/javascript'>".$s_more_javascript."</script>" : '').
	"</head>\n";
    }

    function echoJavaScript() // <<-----------------------------<< 6.3
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
    }

    /* ------------------------------------------------------------------- *\
     *	CWnd::drawFrameSet
     *	CWnd::drawBodyTop
     *	CWnd::drawBodyPage
     *	CWnd::drawBodyBottom
     *	CWnd::drawNoAccess
    \* ------------------------------------------------------------------- */
    function drawFrameSet() // <<-------------------------------<< 7.x
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
    }

    function drawBodyTag() // << -------------------------------<< 7.0.a
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

	if ( $this->ms_onload )
	    echo "<body  onload='{$this->ms_onload}'>";
	else
	    echo "<body>";

	if ( $this->ms_margin_body )
	    echo "<div id='contents' style='margin:10px 10px 10px 10px;'>";
    }

    function drawBodyEndTag() // << ----------------------------<< 7.3.a
    {
	if ( $this->mb_trace_functions	) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	if ( $this->ms_margin_body	) echo "</div>";
	if ( $this->ms_script_end	) echo "<script language='javascript' type='text/javascript'>{$this->ms_script_end}</script>";
    }

    function drawBodyTop() // <<--------------------------------<< 7.1
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
    }

    function drawBodyTopMargin() // <<--------------------------<< 7.1.a
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

	if ( $this->mb_include_cal )
	{
	    echo
	      "<div style='display:none'>".
		"<img src='http://dv1.us/di/c0/active-bg.gif' alt='' />".
		"<img src='http://dv1.us/di/c0/dark-bg.gif' alt='' />".
		"<img src='http://dv1.us/di/c0/hover-bg.gif' alt='' />".
		"<img src='http://dv1.us/di/c0/menuarrow.gif' alt='' />".
		"<img src='http://dv1.us/di/c0/normal-bg.gif' alt='' />".
		"<img src='http://dv1.us/di/c0/rowhover-bg.gif' alt='' />".
		"<img src='http://dv1.us/di/c0/status-bg.gif' alt='' />".
		"<img src='http://dv1.us/di/c0/title-bg.gif' alt='' />".
		"<img src='http://dv1.us/di/c0/today-bg.gif' alt='' />".
	      "</div>";
	}

	if ( $this->ms_margin_top || $this->ms_margin_bottom || $this->ms_margin_left || $this->ms_margin_right || $this->mn_max_width_px )
	{
	    $str = "<div style='";
	    if ( $this->mn_max_width_px )
	    {
		if ( $this->ms_margin_top    ) $str .= "margin-top:{$this->ms_margin_top};";
		if ( $this->ms_margin_bottom ) $str .= "margin-bottom:{$this->ms_margin_bottom};";
		echo $str. "margin-left:auto;margin-right:auto;max-width:{$this->mn_max_width_px}px;width:expression(document.body.clientWidth > {$this->mn_max_width_px}? \"{$this->mn_max_width_px}px\":\"auto\")'>";
	    }
	    else
	    {
		if ( $this->ms_margin_top    ) $str .= "margin-top:{$this->ms_margin_top};";
		if ( $this->ms_margin_bottom ) $str .= "margin-bottom:{$this->ms_margin_bottom};";
		if ( $this->ms_margin_left   ) $str .= "margin-left:{$this->ms_margin_left};";
		if ( $this->ms_margin_right  ) $str .= "margin-right:{$this->ms_margin_right};";
		echo substr($str,0,-1). "'>";
	    }
	}
    }

    function drawBodyTopMenu() // <<--------------------------------<< 7.1.b
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	$s_pic   = $this->ms_pic_right = '' ? "<img width='95' height='1' src='{$this->ms_pics_icons}/1.gif' alt='' />" : $this->ms_pic_right;
	$b_trail = $this->ms_menu_trail || $this->ms_menu_context;
	switch ( $this->mn_page_type )
	{
	case CWnd_PAGE_SIDEBAR:
	    $s_colspan = 3 - ($this->ms_margin_left ? 1 : 0) - ($this->ms_margin_right ? 1 : 0);
	    $s_colspan = $s_colspan > 1 ? "colspan='{$s_colspan}' " : '';
	    echo  "<table cellspacing='0' width='100%'>".
		    "<tr>".
		      ( $this->ms_margin_left  ? '' : "<td align='left' width='1%' class='ma' rowspan='2'>{$this->ms_pic_left}</td>").
		      "<td align='right' class='ma' valign='top' style='padding:1px 4px 1px 4px;white-space:nowrap'>{$this->ms_menu_top}</td>".
		      ( $this->ms_margin_right ? '' : "<td align='right' width='1%' class='ma' rowspan='2'>{$this->ms_pic_right}</td>").
		    "</tr>".
		    "<tr>".
		      "<td valign='bottom' align='left' style='padding:1px 4px 1px 4px' class='ma'>{$this->ms_menu_quick}</td>".
		    "</tr>".
		    "<tr>".
		      "<td {$s_colspan}class='mc' style='padding:1px 4px 1px 4px'>{$this->ms_menu_search}{$this->ms_menu_sub}</td>".
		    "</tr>".
		    ( $b_trail ?
		    "<tr>".
		      "<td {$s_colspan}class='ma' style='height:22px'>{$this->ms_menu_trail}{$this->ms_menu_context}</td>".
		    "</tr>" : '' ).
		  "</table>\n";
	    break;
	case CWnd_PAGE_TOPMENU:
	    echo  "<table cellspacing='0' width='100%'>".
		    "<tr class='ma'>".
		      "<td align='left' width='1%' rowspan='2'>{$this->ms_pic_left}</td>".
		      "<td align='right' width='98%' valign='top' style='padding:1px 4px 1px 4px;white-space:nowrap'>{$this->ms_menu_top}</td>".
		      "<td align='right' width='1%' rowspan='2'>{$this->ms_pic_right}</td>".
		    "</tr>".
		    "<tr>".
		      "<td valign='bottom' align='left' style='padding:1px 4px 1px 4px' class='ma'>{$this->ms_menu_quick}</td>".
		    "</tr>".
		    "<tr>".
		      "<td colspan='3' class='mc' style='padding:1px 4px 1px 4px'>{$this->ms_menu_search}{$this->ms_menu_sub}</td>".
		    "</tr>".
		    ( $b_trail ?
		    "<tr>".
		      "<td colspan='3' class='ma' style='height:22px'>{$this->ms_menu_trail}{$this->ms_menu_context}</td>".
		    "</tr>" : '' ).
		  "</table>\n";
	    break;
	case CWnd_PAGE_PLAIN:
	    echo  "<table cellspacing='0' width='100%'>".
		    "<tr class='ma'>".
		      "<td align='left'>{$this->ms_pic_left}</td>".
		      "<td align='center' align='center' valign='center' class='da'>{$this->ms_title}</td>".
		      "<td align='right'>{$this->ms_pic_right}</td>".
		    "</tr>".
		  "</table>\n";
	    break;
	case CWnd_PAGE_NOHEADER:
	    break;
	}
    }

    function sqlQuery($s_select, $s_from, $s_where, $s_sort)
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	return "SELECT $s_select FROM $s_from WHERE $s_where". ($s_sort ? " ORDER BY $s_sort" : '');
    }

    function constructMenu($s_label, &$a_folders, $s_mode, $url_prefix)
    {
	$n_tot   = count($a_folders);
	$s_menu  = '';
	$n_level = 0;
	for ( $i = 0 ; $i < $n_tot ; $i++ )
	{
	    while ( $a_folders[$i]['level'] > $n_level )
	    {
		$s_menu = substr($s_menu,0,-5). ($i == 0 || $a_folders[$i-1]['curr'] <= -2 ? "<ul id='{$s_label}_{$n_level}'>" : "<ul>");
		$n_level++;
	    }
	    while ( $a_folders[$i]['level'] < $n_level )
	    {
		$s_menu .= "</ul></li>";
		$n_level--;
	    }
	    $s_menu .= "<li>". ($a_folders[$i]['curr'] <= -2 ? "<img src='{$this->ms_pics_icons}/c0/rb1.gif' alt='' />" : '').
		       "<a href='{$url_prefix}{$a_folders[$i]['full']}$s_mode'>{$a_folders[$i]['name']}</a></li>";
	}
	while ( 0 < $n_level )
	{
	    $s_menu .= "</ul></li>";
	    $n_level--;
	}
	return substr($s_menu,0,-5);
    }

    function drawBodyPage() // <<-------------------------------<< 7.2
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

	if ( $this->ms_redirect && ! $this->mb_allow_redirect )
	{
	    echo "<div style='margin:32px 0 16px 0'>".
		   "Oops!<br />".
		   "&nbsp;<br />".
		   "Your browser should have taken you <a href='{$this->ms_redirect}'>here</a>.".
		 "</div>";
	}
    }

    function drawBodyBottomMargin() // <<-----------------------<< 7.2.a
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

	if ( $this->ms_margin_top || $this->ms_margin_bottom || $this->ms_margin_left || $this->ms_margin_right || $this->mn_max_width_px )
	{
	    echo "</div>";
	}
    }

    function drawBodyBottom() // <<-----------------------------<< 7.3
    {	global $gf_start_clock, $gn_sql_time, $gs_db_server;

	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

	$s_left = "Film Aficionado is public domain software. Promotional images rights retained by the respective copyright holders. There are no warranties expressed on implied";
	if ( $this->mn_footer_type & CWnd_FOOTER_TIME )
	{
	    $n_http_time = sprintf("%0.1f", (CTime::get_time() - $gf_start_clock) * 1000);
	    $gn_sql_time = sprintf("%0.1f", $gn_sql_time);
	    $s_left	 = "Release {$this->ms_release}&nbsp;&nbsp;&nbsp;{$gn_sql_time}/{$n_http_time}&nbsp;ms&nbsp;@{$gs_db_server}<br />". $s_left;
	}

	$s_right = '';
	if ( $this->mn_footer_type & CWnd_FOOTER_CONTACT )
	{
	    $s_right .= "<a class='mf' href='{$this->ms_base_subdomain}/term-of-use'>Terms&nbsp;of&nbsp;Use</a>".
			"&nbsp;| <a class='mf' href='{$this->ms_base_subdomain}/contact-center'>Contact&nbsp;Center</a>".
			"&nbsp;| <a class='mf' href='{$this->ms_base_subdomain}/report-problem'>Report&nbsp;a&nbsp;Problem</a>".
			($this->ms_menu_mode != '' ? "<br />{$this->ms_menu_mode}" : '');
	}

	if ( ($this->mn_footer_type & CWnd_FOOTER_W3C) || ($this->mn_footer_type & CWnd_FOOTER_W3C_SMALL) )
	{
	    if ( $s_right ) $s_right .= "</td><td width='1%' align='left' valign='bottom' class='mf' style='padding-left:10px'>";

	    if ( $this->mn_footer_type & CWnd_FOOTER_W3C )
		$s_right .= "<img height='31' width='88' src='{$this->ms_pics_style}/w3c.gif' alt='Valid XHTML 1.0!' />";
	    if ( $this->mn_footer_type & CWnd_FOOTER_W3C_SMALL )
		$s_right .= "<img height='20' width='57' src='{$this->ms_pics_style}/w3c-small.gif' alt='Valid XHTML 1.0!' />";
	}

	if ( $s_right == '' ) $s_right = '&nbsp;';

	if ( $this->mn_footer_type & CWnd_FOOTER_ACCURACY )
	{
	    echo"<div class='mf' style='margin:10px 0px 0px 0px'>".
		  "Information at FilmAf is contributed by members like you. Management of this data is done on a best-effort basis with no explicit or implied assurances to its accuracy.".
		"</div>";
	}
	
	echo 	"<div class='ru'>&nbsp;</div>".
		"<table cellspacing='0' width='100%'>".
		  "<tr>".
		    "<td align='left' valign='bottom' class='mf'>$s_left</td>".
		    "<td align='right' valign='bottom' class='mf'>$s_right</td>".
		  "</tr>".
		"</table>";
    }

    function drawNoAccess() // <<-------------------------------<< 7.x
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
    }

    function getMessageString($b_highlight, $s_div_style)
    {
	$str = '';

	if ( $this->ms_display_affected	) $str .= "<div class='saff'>{$this->ms_display_affected}</div>";
	if ( $this->ms_display_what	) $str .= "<div class='swha'>{$this->ms_display_what}</div>";
	if ( $this->ms_display_error	) $str .= "<div class='serr'>{$this->ms_display_error}</div>";

	if ( $str )
	{
	    if ( $b_highlight ) $str  = "<div class='sbox'>{$str}</div>";
	    if ( $s_div_style ) $str  = "<div style='{$s_div_style}'>{$str}</div>";
	}

	return $str;
    }

    function drawMessages()
    {
	echo $this->getMessageString(false, false);
    }

    /* ------------------------------------------------------------------- *\
     *	CWnd::dump
    \* ------------------------------------------------------------------- */
    function dump()
    {
    }

    /* ------------------------------------------------------------------- *\
     *	CWnd::maintenanceDowntime
     *	  - returns true if the site is momentarely down
     *	CWnd::drawMaintenanceDowntime
     *	  - displays the sitedown notice
    \* ------------------------------------------------------------------- */
    function maintenanceDowntime()
    {
	return false;
    }

    function drawMaintenanceDowntime() //<<---------------------<< 7.x
    {
	$off_beg = 'Wed 7:20PM EST';
	$off_end = 'Thu 7:00AM EST';
	$this->issueHeader();
	$this->drawHeader();
	echo
	  "&nbsp;<br />".
	  "&nbsp;<br />".
	  "&nbsp;<br />".
	  "&nbsp;<br />".
	  "&nbsp;<br />".
	  "&nbsp;<br />".
	  "&nbsp;<br />".
	  "&nbsp;<br />".
	  "Hi, FilmAf is moving to a new set of servers.<br />".
	  "&nbsp;<br />".
	  "The migration started on <font color=blue>$off_beg</font> and is expected to end on <font color=blue>$off_end</font>.<br />".
	  "&nbsp;<br />".
	  "We apologize for any inconvenience.";
    }

    /* ------------------------------------------------------------------- *\
     *	CWnd::validServer
     *	  - validate the server 'filmaf.com'
     *	  - obtain the user_subdomain: $this->ms_view_id ('www', 'ash'...)
     *	CWnd::validRequest
     *	  - verify if request is valid (proper path and or parameters)
     *	CWnd::redirectBadRequest
     *	  - the url seems to be badly formed, send the user to something close to it
     *	CWnd::verifyUser
     *	  - load user and user preferences
    \* ------------------------------------------------------------------- */
    function validServer() // <<--------------------------------<< 2.0
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

	$s_host = dvdaf_getvalue('HTTP_HOST', DVDAF_SERVER | DVDAF_LOWER);
	$s_host	= str_replace('dvdaf.','filmaf.',$s_host);
	$n_pos  = strpos($s_host, $this->ms_unatrib_subdomain);
	$s_subd = $n_pos >  0 ? substr($s_host,0,$n_pos) : '';
	$s_host = $n_pos >= 0 ? substr($s_host,$n_pos) : '.'.$s_host;

	if ( $s_subd == '' || strpos($s_subd,'.') )
	{
	    $this->ms_redirect = $this->ms_base_subdomain;
	    return false;
	}

	switch ( $this->mn_web_site )
	{
	case HOST_FILMAF_COM:
		$this->ms_view_id	     = $s_subd;				// note that ms_view_id may be an invalid user name or 'www'
		$this->ms_base_subdomain = "http://www{$s_host}";
		$this->ms_view_subdomain = "http://{$s_subd}{$s_host}";
		return true;
	}
	return false;
    }

    function validRequest() // <<-------------------------------<< 4.0
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	return true;
    }

    function redirectBadRequest() // <<-------------------------<< 4.1
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	if ( $this->ms_redirect == '' ) $this->ms_redirect = $this->ms_base_subdomain;
	$this->issueHeader();
	$this->drawHeader();
	echo
	"<body>\n".
	  "&nbsp;<br />".
	  "Hi, you send us a request we could not understand.<br />".
	  "&nbsp;<br />".
	  "Your browser should have taken you <a href='{$this->ms_redirect}'>here</a>";
    }

    function verifyUser() // <<---------------------------------<< 3.0
    {	global $gb_allow_dump;

	if ( ! $gb_allow_dump )
	{
	    $this->mb_trace_functions	= false;
	    $this->mb_show_trace	= false;
	    $this->mb_trace_variables	= false;
	    $this->mb_trace_environment	= false;
	    $this->mb_trace_sql		= false;
	}

	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);

	// take care of the original cookie to track terminals
	if ( dvdaf_getvalue('orig', DVDAF_COOKIE) == '' )
	{
	    $s_orig = dvdaf_getvalue('REMOTE_ADDR', DVDAF_SERVER). '|'. (time()+microtime());
	    setcookie('orig', $s_orig, mktime(0,0,0,3,1,2030), '/', $this->ms_cookie_domain, 0);
	}

	// load cookies
	$s_user = dvdaf_getvalue('user', DVDAF_COOKIE);
	$s_parm = dvdaf_getvalue('parm', DVDAF_COOKIE);
	$s_sess = dvdaf_getvalue('sess', DVDAF_COOKIE);
	$s_md5p = substr($s_parm,0,32);
	$s_parm = substr($s_parm,32);
	$b_good = $s_md5p == CHash::hash_parm($s_user . $s_parm);

	if ( $s_sess )
	{
	    $s_md5p = substr($s_sess,0,32);
	    $s_sess = substr($s_sess,32);
	    $b_good = $s_md5p == CHash::hash_sess($s_user . $s_sess);
	}

	if ( $b_good )
	{
	    $s_parm = explode('|', $s_parm);
	    $n_parm = count($s_parm);
	    $s_sess = explode('|', $s_sess);
	    $n_sess = count($s_sess);
	    $b_good = $s_user && $s_user != 'guest';
	    if ( $b_good )
	    {
		$this->mn_user_session	 = ($n_parm > 0 && $s_parm[0] == CUser_SESSION_PERM	 ) ? CUser_SESSION_PERM : CUser_SESSION_TEMP;
		$this->mn_user_authentic = ($n_sess > 0 && $s_sess[0] == CUser_USER_AUTHENTICATED) ? CUser_USER_AUTHENTICATED : CUser_USER_REMEMBERED;
		$b_good = $this->mn_user_session == CUser_SESSION_PERM || $this->mn_user_authentic == CUser_USER_AUTHENTICATED;
		if ( $b_good )
		{
		    $this->ms_user_id	     = $s_user;
		    $this->mn_user_access    = CUser_ACCESS_NOTHING; //-- query db
		    $this->ms_user_subdomain = "http://{$this->ms_user_id}". substr($this->ms_user_subdomain,10); // partially filled in $this->validServer()
		}
	    }
	}

	if ( $b_good )
	{
	    // load additional parameters in $s_parm[] and $s_sess[]
	    if ( $this->mb_get_user_status )
	    {
		$rr = CSql::query_and_fetch("SELECT contributor_cd, membership_cd, moderator_cd FROM dvdaf_user WHERE user_id = '$this->ms_user_id'", 0,__FILE__,__LINE__);
		if ( $rr )
		{
		    $this->mn_contributor_cd  = intval($rr['contributor_cd']);
		    $this->mn_membership_cd   = intval($rr['membership_cd' ]);
		    $this->mn_moderator_cd    = intval($rr['moderator_cd'  ]);
		    $this->mn_access_level_cd = $n_stars = max($this->mn_contributor_cd, $this->mn_membership_cd, $this->mn_moderator_cd);
		    if ( $n_stars >= 1 && $n_stars <= 9 )
			$this->mn_user_stars = $n_stars >= 5 ? $n_stars : ($n_stars >= 2 ? $n_stars + 1 : 1);
		}
	    }
	}
	else
	{
	    $this->ms_user_id		= 'guest';
	    $this->mn_user_access	= CUser_ACCESS_NOTHING;
	    $this->mn_user_authentic	= CUser_USER_GUEST;
	    $this->mn_user_session	= CUser_SESSION_NONE;
	}
	$this->mb_view_self  = $this->ms_view_id == $this->ms_user_id;
    }

    /* ------------------------------------------------------------------- *\
     *	CWnd::validUserAccess
    \* ------------------------------------------------------------------- */
    function validUserAccess() // <<----------------------------<< 5.0
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
	return true;
    }

    /* ------------------------------------------------------------------- *\
     *	CWnd::validateDataSubmission
    \* ------------------------------------------------------------------- */
    function validateDataSubmission() // <<---------------------<< 6.0
    {
	if ( $this->mb_trace_functions ) CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
    }

    function strUpdate(&$s_update, $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2)
    {
	$s_update .= ($s_col_1 ? (dvdaf_columname($s_tbl, $s_col_1). ' = '. $s_val_1. ', ') : '').
		     ($s_col_2 ? (dvdaf_columname($s_tbl, $s_col_2). ' = '. $s_val_2. ', ') : '');
    }

    function strInsert(&$s_insert, &$s_values, $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2)
    {
	if ( $s_col_1 )
	{
	    $s_insert .= dvdaf_columname($s_tbl, $s_col_1). ', ';
	    $s_values .= $s_val_1. ', ';
	}
	if ( $s_col_2 )
	{
	    $s_insert .= dvdaf_columname($s_tbl, $s_col_2). ', ';
	    $s_values .= $s_val_2. ', ';
	}
    }

    function validateInput(&$s_update, &$s_values, $n_gen, $b_skip_sec)
    {
	$a_new = array();
	$a_old = array();
	foreach ( $_POST as $s_key => $x_value )
	{
	    if ( substr($s_key,0,2) == 'n_' )
	    {
		$s_key = substr($s_key, 2);
		$s_new = dvdaf_getvalue('n_'.$s_key, DVDAF_POST);
		$s_old = dvdaf_getvalue('o_'.$s_key, DVDAF_POST);
//echo "s_key = [{$s_key}] = [$s_old] => [$s_new]<br />";
		if ( $s_new || $s_old || $n_gen == DVDAF_INSERT )
		{
		    if ( preg_match('/_[0-9]+$/', $s_key, $a_matches) > 0 )
		    {
			$s_key = substr($s_key, 0, -strlen($a_matches[0]));
			if ( ! isset($a_new[$s_key]) )
			{
			    $a_new[$s_key] = '';
			    $a_old[$s_key] = '';
			}
			$s_tbl   = substr($s_key,0,2);
			$s_col_1 = substr($s_key,2);
			$s_sep   = $s_col_1 == 'region_mask' ? ',' : dvdaf_fieldseparator($s_tbl, $s_col_1);
			if ( $s_new != '' ) $a_new[$s_key] .= ($a_new[$s_key] != '' ? $s_sep : '') . $s_new;
			if ( $s_old != '' ) $a_old[$s_key] .= ($a_old[$s_key] != '' ? $s_sep : '') . $s_old;
		    }
		    else
		    {
			$a_new[$s_key] = $s_new;
			$a_old[$s_key] = $s_old;
		    }
		}
	    }
	}

	$s_key = 'a_region_mask';
	if ( array_key_exists($s_key, $a_new) )
	{
	    $a_new[$s_key] = dvdaf_encoderegion($a_new[$s_key]);
	    $a_old[$s_key] = dvdaf_encoderegion($a_old[$s_key]);
	}

	$s_update = '';
	$s_values = '';
	foreach ( $a_new as $s_key => $x_value )
	{
	    $s_new   = $a_new[$s_key];
	    $s_old   = $a_old[$s_key];
	    $s_tbl   = substr($s_key,0,2);
	    $s_col_1 = substr($s_key,2);
	    $s_col_2 = '';
	    $s_val_1 = '';
	    $s_val_2 = '';

//echo "s_key = [{$s_key}]<br />".
//     "s_new = [{$s_new}]<br />".
//     "s_old = [{$s_old}]<br />".
//     "s_tbl = [{$s_tbl}]<br />".
//     "s_col_1 = [{$s_col_1}]<br />";
//echo   "dvdaf_validateinput($s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2, $s_new, $s_old, $this->ms_display_error, $n_gen | DVDAF_HTML | DVDAF_GET_SEC) )<br />";

	    if ( dvdaf_validateinput($s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2, $s_new, $s_old, $this->ms_display_error, $n_gen | DVDAF_HTML | DVDAF_GET_SEC) )
	    {
		switch ( $n_gen )
		{
		case DVDAF_UPDATE:
		    $this->strUpdate($s_update, $s_tbl, $s_col_1, $s_val_1, $b_skip_sec ? '' : $s_col_2, $b_skip_sec ? '' : $s_val_2);
//echo "strUpdate($s_update, $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2)<br />";
		    break;
		case DVDAF_INSERT:
		    $this->strInsert($s_update, $s_values, $s_tbl, $s_col_1, $s_val_1, $b_skip_sec ? '' : $s_col_2, $b_skip_sec ? '' : $s_val_2);
//echo "strInsert($s_update, $s_values, $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2)<br />";
		    break;
		}
	    }
//echo "this->ms_display_error = {$this->ms_display_error}<br />";
//echo "&nbsp;<br />";
	}
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
    /* ------------------------------------------------------------------- *\
     *	CWnd::setCookies
    \* ------------------------------------------------------------------- */
    function setCookies($b_logout) // <<---------------------------------<< aux
    {
	global $_COOKIE;
	$s_expire_time	= mktime(0, 0, 0, 3, 1, 2030);
	$s_delete_time	= mktime(0, 0, 0, 3, 1, 2009);
	$s_keepers	= '-,user,parm,sess,pinned,excl,state,cart,saved,orig,more,';

	// delete previous versions cookies
	/*
	$a_keys = array_keys($_COOKIE);
	for ( $i = 0  ;  $i < count($a_keys)  ;  $i++ )
	{
	    $s_key = $a_keys[$i];
	    if ( strpos($s_keepers, ",{$s_key},") === false )
	    {
		CTrace::log_txt("Erasing cookie '{$s_key}'");
		setcookie($s_key, '', $s_delete_time, '/', $this->ms_cookie_domain);
		setcookie($s_key, '', $s_delete_time, '/');
	    }
	}
	*/

	// write this versions cookies
	if ( $b_logout )
	{
	    setcookie('user', '', $s_delete_time, '/', $this->ms_cookie_domain);
	    setcookie('sess', '', $s_delete_time, '/', $this->ms_cookie_domain);
	}
	else
	{
	    $s_user = $this->ms_user_id;
	    $s_parm = "{$this->mn_user_session}";	// separate additional values with pipe '|'
	    $s_parm = CHash::hash_parm($s_user . $s_parm). $s_parm;
	    $s_sess = "{$this->mn_user_authentic}";	// separate additional values with pipe '|'
	    $s_sess = CHash::hash_sess($s_user . $s_sess). $s_sess;
	    setcookie('user', $s_user, $s_expire_time, '/', $this->ms_cookie_domain, 0);
	    setcookie('parm', $s_parm, $s_expire_time, '/', $this->ms_cookie_domain, 0);
	    setcookie('sess', $s_sess,              0, '/', $this->ms_cookie_domain, 0);
	}
    }

    /* ------------------------------------------------------------------- *\
     *	CWnd::getUserStars
     *	CWnd::getViewStars
     *	CWnd::getStars
    \* ------------------------------------------------------------------- */
    function getUserStars()
    {
	if ( $this->mn_user_stars == -2 )
	{
	    if ( $this->ms_user_id == 'guest' )
	    {
		$this->mn_user_stars = -1;
	    }
	    else
	    {
		$this->mn_user_stars = $this->getStars($this->ms_user_id);
		if ( $this->mb_view_self ) $this->ms_view_id = $this->ms_user_id;
	    }
	}
	return $this->mn_user_stars;
    }

    function getViewStars()
    {
	if ( $this->mn_view_stars == -2 )
	{
	    if ( $this->ms_view_id == 'www' )
	    {
		$this->mn_view_stars = -1;
	    }
	    else
	    {
		$this->mn_view_stars = $this->getStars($this->ms_view_id);
		if ( $this->mb_view_self ) $this->ms_user_id = $this->ms_view_id;
	    }
	}
	return $this->mn_view_stars;
    }

    function getStars($s_user_id)
    {
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

	$rr = CSql::query_and_fetch("SELECT contributor_cd, membership_cd, moderator_cd FROM dvdaf_user WHERE user_id = '$s_user_id'", 0,__FILE__,__LINE__);
	if ( $rr )
	{
	    $n_stars = max(intval($rr['moderator_cd']), intval($rr['membership_cd']), intval($rr['contributor_cd']));
	    if ( $n_stars <= 9 )
	    {
		if ( $n_stars >= 5 ) return $n_stars;
		if ( $n_stars >= 2 ) return $n_stars + 1;
		if ( $n_stars == 1 ) return 1;
	    }
	}

	return 0;
    }
}

//////////////////////////////////////////////////////////////////////////

class CHash
{
    function hash_user($s_value)
    {
	return md5($s_value . "gx<B:2ip|iv-Q'{3");
    }
    function hash_parm($s_value)
    {
	return md5($s_value . 'ZB;Xz0@_N]mYru%3');
    }
    function hash_sess($s_value)
    {
	return md5($s_value . 'a2leB +g/{Ut2k:3');
    }
    function hash_password($s_value)
    {
	return md5($s_value . 'm{v~Cqe(3X/r>aV.'); // *** NEVER CHANGE THIS VALUE ***
    }
}

//////////////////////////////////////////////////////////////////////////

?>
