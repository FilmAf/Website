<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CAdvert.php';
require $gs_root.'/lib/CDvdUtils.php';
require $gs_root.'/lib/CWnd.php';

class CWndMenu extends CWnd
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-wnd-menu_{$this->mn_lib_version}.js'></script>";
		$this->mb_include_cal		= true;
		$this->mb_include_menu		= true;
		$this->mb_include_collect	= true;
		$this->mb_include_search	= true;
		$this->mb_menu_context		= true;
		$this->mb_hello_user		= true;
		$this->mb_get_user_status	= true;

		// Menus
		$this->ms_hello_user		= '';
		$this->ms_menu_links		= '';
		$this->ms_menu_main			= '';
		$this->ms_url_parm			= '';
		$this->mb_advert			= false;

		// Folder cache
		$this->ma_user_folders	= null;
	}
	function getFooterJavaScript()
	{
		$s_user    = $this->mb_logged_in ? $this->ms_user_id : '';
		$s_view    = $this->mb_collection ? $this->ms_view_id : '';
		$s_config  = '{baseDomain:"'.					$this->ms_base_subdomain.'"'.
					 ',userCollection:"'.				$s_user					.'"'.
					 ',viewCollection:"'.				$s_view					.'"'.
					 ($this->mb_include_search	? ',onPopup:SearchMenuPrep.onPopup' : '').
					 ',optionsTag:"user_collection"'.
					 ',ulExplain:1'.
					 ',imgPreLoad:"pin.help.explain"'.
					 '}';
		return
					($this->mb_include_search ? "function onMenuClick(action){SearchMenuAction.onClick(action);};" : '').
					"Filmaf.config({$s_config});".
					"setTimeout('".
						"Menus.setup();".
					"',100);";
	}
	function drawBodyTop()
	{
		if ( $this->mb_advert && (($this->getUserStars() > 0 && !$this->mb_mod) || $this->getViewStars() > 0) )
			$this->mb_advert = false;

		$this->drawHeader();
								echo  "<div id='body'>";
		if ( $this->mb_advert ) echo	"<table>".
										  "<tr>".
											"<td id='content-left'>";
								echo		  "<div id='content".($this->mb_advert ? '' : '-noadv')."'>";
	}
	function drawBodyBottom()
	{
								echo		  "</div>";
		if ( $this->mb_advert )
		{
								echo		"</td>".
											"<td id='content-right'>".
											  "<div id='advert'>";
//												CAdvert::drawAddThis(1);
//												CAdvert::drawSponsors();
												CAdvert::drawSkyScraper();
								echo		  "</div>".
											"</td>".
										  "</tr>".
										"</table>";
		}
								echo  "</div>";
		$this->drawFooter();
	}
/*
	function drawBodyTop()
	{
		$this->drawHeader();
		echo					  "<div id='body'>".
									"<div id='contentwrap'>".
									  "<div id='content".($this->mb_advert ? '' : '-noadv')."'>";
	}
	function drawBodyBottom()
	{
		echo						  "</div>".
									"</div>";
		if ( $this->mb_advert )
		{
			echo					"<div id='advert'>";
									  CAdvert::drawAddThis(1);
									  CAdvert::drawSponsors();
									  CAdvert::drawSkyScraper();
			echo					"</div>";
		}
		echo					  "</div>";
		$this->drawFooter();
	}
*/
	function drawHeader()
	{
		echo  "<table id='header'><tr>".
				"<td><a href='{$this->ms_base_subdomain}'><img src='http://dv1.us/d1/filmaf.png' /></a></td>".
// removed as part of site closure so that it would not interfere with msg
				($this->mb_facebook_div ? "<td><div id='fb-root'></div></td>" : '').
//				"<td style='color:#dceaf6;padding: 2px 4px 4px 4px;text-align:center'>ANNOUNCEMENT: FilmAf will be ceasing activities on June 10, 2019.&nbsp; In nearly 20 years DVDAF and then FilmAf helped tens of thousands of people.&nbsp; That is quite a run!&nbsp; Unfortunately the economics have not been working for the past few years, and so it is time to call it.&nbsp; We would like to thank you for all the work and dedication you have put into the site.&nbsp; A special thanks to our moderators-extraordinaire, Greg and Anthony.&nbsp; Thank you for a great journey.&nbsp; Fair winds and following seas to all.</td>".
//				"<td style='color:#dceaf6;padding: 2px 4px 4px 4px;text-align:center'>ANNOUNCEMENT: The rumors of our demise have been greatly exaggerated -- well kind of.&nbsp; I’m happy to announce that Samuel from <a href='http://www.dvdcompare.net/' style='color:#dceaf6;text-decoration:underline'>DVDCompare</a> has graciously agreed to take over FilmAf.&nbsp; We are working on the transition details.&nbsp; No interruption of service is expected.&nbsp; Long live FilmAf!&nbsp; Long live DVDCompare!</td>".
				"<td id='hello'>".($this->mb_hello_user	  ? "{$this->ms_hello_user}" : '&nbsp;')."</td>".
			  "</tr></table>".
			  "<div id='bar'>";
		
		if ( $this->ms_menu_links )
		{
			echo "<div id='links' style='overflow:hidden'>{$this->ms_menu_links}<div style='float:right;white-space:nowrap'>";
			CAdvert::drawAddThis(1);
			echo "</div></div>";
		}
		echo
				($this->ms_menu_main	  ? "<div id='main'>{$this->ms_menu_main}</div>"   : '').
				($this->mb_include_search ? "<div id='search'>&nbsp;</div>"				   : '').
			  "</div>";
	}
	function initializeMenu()
	{
		if ( $this->mb_hello_user )
			$this->ms_hello_user = $this->getHello(true);

		$this->initMainMenu();
		$this->initTrailMenu();
	}
	function initMainMenu()
	{
		$s_href_base = "href='{$this->ms_base_subdomain}";
		$s_href_user = "href='{$this->ms_user_subdomain}";

		if ( $this->mb_logged_in )
		{
			$this->ma_user_folders = array();
			CDvdUtils::getFolders($this->ma_user_folders, $this->ms_user_id, $this->ms_user_id, $this->mb_include_collect);
			$s_user_col =
				"<li>My Collection".
				  "<ul id='user_collection'>".
					"<li><a $s_href_user/'>My Home</a></li>".
					"<li></li>".
					CDvdUtils::makeMenu($this->ma_user_folders, 'user', $this->ms_user_subdomain, $this->ms_url_parm).
					"<li></li>".
					"<li><a $s_href_base/utils/folders.html'>Manage Folders</a></li>".
					"<li><a $s_href_user/export.html'>Export Collection</a></li>";
		}
		else
		{
			$s_user_col =
				"<li>My Collection".
				  "<ul>".
					"<li><a $s_href_base/utils/login.html'>Login</a></li>".
					"<li></li>".
					"<li><a $s_href_base/utils/register.html'>Create an Account</a></li>";
		}
		$s_user_col .=
				  "</ul>".
				"</li>";

		$s_href_rele = "$s_href_base/releases.html";
		$b_log		 = $this->mb_logged_in;
		$b_mod		 = $this->mn_moderator_cd >= 5;
		$b_ash		 = $b_mod && $this->ms_user_id == 'ash';

		$blk  = "' target='_blank'>";
		$href = "<a href='/vd.php?vd=";
	
		$this->ms_menu_links =
			"Quick Links: ".
			"{$href}amz' style='color:#de4141{$blk}Amazon</a> ".
			"{$href}amz.ca{$blk}Amz.ca</a> ".
			"{$href}amz.uk' style='color:#de4141{$blk}Amz.uk</a> ".
			"{$href}amz.fr{$blk}Amz.fr</a> ".
			"{$href}amz.de{$blk}Amz.de</a> ".
			"{$href}amz.it{$blk}Amz.it</a> ".
			"{$href}amz.es{$blk}Amz.es</a> ".
			"{$href}amz.jp{$blk}Amz.jp</a>&nbsp; ".
			"{$href}deep{$blk}Deep&nbsp;Discount</a>&nbsp; ".
			"{$href}emp{$blk}DVD&nbsp;Empire</a>&nbsp; ".
			"{$href}bno{$blk}Barnes&amp;Noble</a>";

		$this->ms_menu_main =
			  "<ul id='main-menu' style='display:none'>".
				$s_user_col.
				"<li></li>".
($b_log ?		"<li><a href='javascript:void(0);'>Friends</a>"														: '').
($b_log ?		  "<ul>"																							: '').
($b_log ?			"<li><a $s_href_user/?tab=friends'>My Friends</a></li>"											: '').
($b_log ?			"<li></li>"																						: '').
($b_log ?			"<li><a $s_href_user/?tab=friends&act=email'>Find by Email</a></li>"							: '').
($b_log ?			"<li><a $s_href_user/?tab=friends&act=name'>Find by User Name</a></li>"							: '').
($b_log ?		  "</ul>"																							: '').
($b_log ?		"</li>"																								: '').
($b_log ?		"<li></li>"																							: '').
				"<li><a href='javascript:void(0);'>Find DVDs</a>".
				  "<ul>".
					"<li><a $s_href_base/utils/upc-import.html'>UPC Importer</a></li>".
					"<li></li>".
					"<li><a $s_href_rele?pubct=us&amp;med=D&amp;init_form=str0_pubct_us*med_0_D'>New Releases: <strong>U.S.</strong></a></li>".
					"<li><a $s_href_rele?pubct=uk&amp;med=D&amp;init_form=str0_pubct_uk*med_0_D'>New Releases: <strong>U.K.</strong></a></li>".
					"<li><a $s_href_rele?pubct=ca&amp;med=D&amp;init_form=str0_pubct_ca*med_0_D'>New Releases: <strong>Canada</strong></a></li>".
					"<li><a $s_href_rele?pubct=.ne.us%2Cuk%2Cca%2Cfr%2Cde%2Cjp&amp;med=D&amp;init_form=str0_pubct_%3C%3Eus%2Cuk%2Cca*med_0_D'>New Releases: <strong>Others</strong></a></li>".
					"<li><a $s_href_rele?med=D&amp;init_form=med_0_D'>New Releases: <strong>All</strong></a></li>".
				  "</ul>".
				"</li>".
				"<li></li>".
				"<li><a href='javascript:void(0);'>Find Blu-rays</a>".
				  "<ul>".
					"<li><a $s_href_base/utils/upc-import.html'>UPC Importer</a></li>".
					"<li></li>".
					"<li><a $s_href_rele?pubct=us&amp;med=B&amp;init_form=str0_pubct_us*med_0_B'>New Releases: <strong>U.S.</strong></a></li>".
					"<li><a $s_href_rele?pubct=uk&amp;med=B&amp;init_form=str0_pubct_uk*med_0_B'>New Releases: <strong>U.K.</strong></a></li>".
					"<li><a $s_href_rele?pubct=ca&amp;med=B&amp;init_form=str0_pubct_ca*med_0_B'>New Releases: <strong>Canada</strong></a></li>".
					"<li><a $s_href_rele?pubct=.ne.us%2Cuk%2Cca%2Cfr%2Cde%2Cjp&amp;med=B&amp;init_form=str0_pubct_%3C%3Eus%2Cuk%2Cca*med_0_B'>New Releases: <strong>Others</strong></a></li>".
					"<li><a $s_href_rele?med=B&amp;init_form=med_0_B'>New Releases: <strong>All</strong></a></li>".
				  "</ul>".
				"</li>".
				"<li></li>".
				"<li>Price Comparison".
				  "<ul>".
					"<li><a $s_href_base/price.html'>Compare Prices</a></li>".
				  "</ul>".
				"</li>".
				"<li></li>".
				"<li>My Contribution".
				  "<ul>".
					"<li><a $s_href_base/utils/help-filmaf.html'>Donations</a></li>".
					"<li><a $s_href_base/utils/benefits.html'>Star Membership Benefits</a></li>".
					"<li><a $s_href_base/thank-you.html'>Friends of FilmAf</a></li>".
($b_log ?			"<li></li>"																						: '').
($b_log ?			"<li><a $s_href_base/utils/x-dvd-edit.html?dvd=new'>Submit New DVD</a></li>"					: '').
($b_log ?			"<li><a $s_href_base/utils/x-dvd-edit.html'>Submission History</a></li>"						: '').
($b_mod ?			"<li></li>"																						: '').
($b_mod ?			"<li><a $s_href_base/utils/x-dvd-appr.html' target='targetappr'>Submission Processing</a></li>"	: '').
($b_mod ?			"<li><a $s_href_base/utils/clone-dvd.html'>Clone DVD</a></li>"									: '').
($b_mod ?			"<li><a $s_href_base/utils/diff-dvd.html'>Diff DVD</a></li>"									: '').
($b_mod ?			"<li><a $s_href_base/utils/resurrect.html'>Resurrect Submission</a></li>"						: '').
($b_mod ?			"<li><a $s_href_base/utils/errorlog.html'>Error Log Lookup</a></li>"							: '').
($b_mod ?			"<li><a $s_href_base/utils/set-featurette-dir.html'>Set Featurette Dir</a></li>"				: '').
($b_ash ?			"<li></li>"																						: '').
($b_ash ?			"<li><a $s_href_base/utils/ren-director.html'>Rename Director</a></li>"							: '').
($b_ash ?			"<li><a $s_href_base/utils/ren-publisher.html'>Rename Publisher</a></li>"						: '').
($b_ash ?			"<li><a $s_href_base/utils/diff-dvd.html'>Merge Titles</a></li>"								: '').
				  "</ul>".
				"</li>".
				"<li></li>".
				"<li>The Rest".
				  "<ul>".
					"<li><a $s_href_base/utils/find-account.html'>Find FilmAf Account</a></li>".
($b_log ?			"<li><a $s_href_base/utils/email.html'>Update Email Address</a></li>"							: '').
($b_log ?			"<li><a $s_href_base/utils/password.html'>Change Password</a></li>"								: '').
					"<li><a $s_href_base/utils/reset-password.html'>Reset Password</a></li>".
					"<li><a $s_href_base/utils/validate-email.html'>Validate Email Address</a></li>".
					"<li><a $s_href_base/utils/validate-email.html?cd=resend'>Resend Validation Code</a></li>".
($b_log ?			"<li><a $s_href_user/?tab=options'>Options</a></li>"											: '').
					"<li><a $s_href_base/utils/show-cookies.html'>Cookies</a></li>".
					"<li></li>".
					"<li><a $s_href_base/utils/site-map.html'>Site Map</a></li>".
($b_log ?			"<li></li>"																						: '').
($b_log ?			"<li><a $s_href_base/utils/logout.html'>Logout</a></li>"										: '').
				  "</ul>".
				"</li>".
			  "</ul>";
	}
	function initTrailMenu()
	{
	}
}

?>
