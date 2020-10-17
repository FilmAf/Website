<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndMenu.php';
require $gs_root.'/lib/CPicUtils.php';

class CFoo {};

require $gs_root.'/lib/CWidget.php';
require $gs_root.'/lib/CWidgetProfile.php';
require $gs_root.'/lib/CWidgetWall.php';
require $gs_root.'/lib/CWidgetMicroblog.php';
require $gs_root.'/lib/CWidgetFriends.php';
require $gs_root.'/lib/CWidgetUpdates.php';
require $gs_root.'/lib/CWidgetFavVideos.php';
require $gs_root.'/lib/CWidgetStats.php';
require $gs_root.'/lib/CWidgetOptions.php';

class CHome extends CWndMenu
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace			= true;
//		$this->mb_trace_environment		= true;
//		$this->mb_trace_sql				= true;
//		$this->mb_allow_redirect		= true;
		$this->ms_include_js			= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-home_{$this->mn_lib_version}.js'></script>\n";
		$this->mn_tab					= 0;

		$this->ms_include_css .=
			"<style type='text/css'>".
				// highlighted links (uderlined/red)
				".wga, A.wga, A.wga:visited, A.wga:active {text-decoration:underline; color:#0066b2}".
				"A.wga:hover {text-decoration:underline; color:#bd0b0b}".
				// User says + timestamp
				".wg_time {color:#83929d}".
				".wg_repl {color:#83929d}".
				// separator
				".wg_sepa {border-bottom-width:1px; border-bottom-style:solid; border-color:#b2daff; margin-bottom:4px; font-size:1px; line-height:1px}".
				// section body
				".wg_body {color:#5084b1; padding:2px 4px 4px 4px}".
				".wg_tdl {color:#57af63;padding:0 0 4px 2px;text-align:right;vertical-align:top;white-space:nowrap}".
				".wg_tdm {color:#072b4b;text-align:left;white-space:nowrap}".
				".wg_tdr {color:#072b4b;padding:1px 0 2px 2px;vertical-align:middle}".
				// friend invite
				"#invites {background:transparent url('http://dv1.us/d1/00/header-back-small.jpg') repeat-x;padding-bottom:12px}".
				"#invites thead td {color:#fff;padding:3px 8px 4px 8px;white-space:nowrap}".
				"#invites tbody td {background-color:#fff;padding:2px 8px 2px 8px}".
				"#invites thead a {color:#fff}".

				// stats and video side menu
				"#stat_menu, #fvid_menu {border-collapse:separate;border-spacing:0 2px;}".
				"#stat_menu td, #fvid_menu td, #inp_tbl td {white-space:nowrap}".

				// stats and video
				"#stat_div, #fvid_div {margin:0 10px 0 36px; border-left:1px solid #b9d1e7; padding-left:36px}".
				"#stat_tit {color:#072b4b; font-size:12px; font-weight:bold; margin:14px 0 20px 0}".
				"#stat_tbl, #inp_tbl {border-collapse:separate;border-spacing:8px 1px;}".
				"#stat_tbl a:link, #stat_tbl a:visited, #stat_tbl a:active, #stat_tbl a:hover {color:#072b4b}".
				"#stat_tbl td {white-space:nowrap}".
				"#stat_tbl span {color:#5988b1}".
				".stat_rank {text-align:right;color:#5988b1}".
				".stat_val {text-align:right;color:#5988b1}".

				// friend invites
				"#open_invites {white-space:nowrap}".

				// options
				".opt_tbl {white-space:nowrap;padding:0 2px 0 2px}".
			"</style>";
	}

	function validateDataSubmission()
	{
		$s_user_agent				= dvdaf3_getvalue('HTTP_USER_AGENT', DVDAF3_SERVER|DVDAF3_LOWER);
//		$this->ms_user_id			= $s_user_id;
//		$this->ms_view_id			= $s_view_id;
//		$this->ms_unatrib_subdomain	= $s_unatrib_subdomain;
//		$this->ms_base_subdomain	= $s_base_subdomain;
		$this->mwb_browser_is_opera	= strpos($s_user_agent,'opera') !== false;
		$this->mwb_browser_is_ie	= strpos($s_user_agent,'msie' ) !== false && ! $this->mwb_browser_is_opera;
		$this->mwb_show_replies		= dvdaf3_getvalue('showreplies', DVDAF3_COOKIE|DVDAF3_BOOLEAN);
		$this->mwb_view_self		= $this->ms_user_id == $this->ms_view_id;
		$this->mws_ucview_id		= ucfirst($this->ms_view_id);
		$this->ma_profile			= array();

		$n_blog_index = $this->getTabs();
		$s_tab		  = $this->validateTab();

		if ( $this->mn_tab == 1 )
		{
			CWidgetProfile::getFullProfile($this);
		}
		else
		{
			$this->validatePost($s_tab);
			CWidgetProfile::getPatialProfile($this);
		}

		$this->setBlogLink($n_blog_index);
	}

	function validatePost($s_tab)
	{
		if ( count($_POST) > 0 )
		{
			switch ( $s_tab )
			{
			case 'home':
				break;
			case 'friends':
				CWidgetFriends::validateDataSubmission($this);
				break;
			case 'updates':
				break;
			case 'favvideos':
				CWidgetFavVideos::validateDataSubmission($this);
				break;
			case 'stats':
				break;
			case 'sale':
				break;
			case 'options':
				CWidgetOptions::validateDataSubmission($this);
				break;
			}
		}
	}

	function getTabs()
	{
		$s_home = "<span style='color:#de4141'>FilmAf</span>";
		$s_myho = "<span style='color:#de4141'>Home</span>";
		$s_coll = "<span style='color:#de4141'>Collection</span>";

		$this->ma_home   = array(
			array('label'=>$s_home		,'self'=>false,'url'=>$this->ms_base_subdomain	,'trg'=>'-'			,'widget'=>null												 ),
			array('label'=>$s_myho		,'self'=>false,'url'=>'-'						,'trg'=>'home'		,'widget'=>array(array('profile','wall'),array('microblog')	)),
			array('label'=>$s_coll		,'self'=>false,'url'=>'/owned'					,'trg'=>'-'			,'widget'=>null												 ),
			array('label'=>'Friends'	,'self'=>false,'url'=>'-'						,'trg'=>'friends'	,'widget'=>array(array('friends')							)),
			array('label'=>'Updates'	,'self'=>false,'url'=>'-'						,'trg'=>'updates'	,'widget'=>array(array('updates')							)),
			array('label'=>'Videos'		,'self'=>false,'url'=>'-'						,'trg'=>'favvideos'	,'widget'=>array(array('favvideos')							)),
			array('label'=>'Stats'		,'self'=>false,'url'=>'-'						,'trg'=>'stats'		,'widget'=>array(array('stats')								)),
		//	array('label'=>'For Sale'	,'self'=>false,'url'=>'-'						,'trg'=>'sale'		,'widget'=>array(array('ebay')								)),
			array('label'=>'Options'	,'self'=>true ,'url'=>'-'						,'trg'=>'options'	,'widget'=>array(array('options')							)),
			array('label'=>'Blog...'	,'self'=>false,'url'=>'-'						,'trg'=>'blog'		,'widget'=>array(array('blog')								)));

		$n_blog_index	 = count($this->ma_home) - 1;

		return $n_blog_index;
	}

	function validateTab()
	{
		$s_tab = dvdaf3_getvalue('tab', DVDAF3_GET|DVDAF3_LOWER);

		$this->mn_tab = 1;

		for ( $i = 0 ; $i < count($this->ma_home) ; $i++ )
			if ( $this->ma_home[$i]['trg'] == $s_tab )
				$this->mn_tab = $i;

		return $this->mn_tab == 1 ? 'home' : $s_tab;
	}

	function setBlogLink($n_blog_index)
	{
		if ( $this->ma_profile['blog'] )
		{
			$this->ma_home[$n_blog_index]['url']	= $this->ma_profile['blog'];
			$this->ma_home[$n_blog_index]['widget']	= null;
		}
	}

	function getFooterJavaScript()
	{
		$s_user    = $this->mb_logged_in ? $this->ms_user_id : '';
		$s_view    = $this->mb_collection ? $this->ms_view_id : '';
		$s_config  = '{baseDomain:"'.					$this->ms_base_subdomain.'"'.
					 ',userCollection:"'.				$s_user					.'"'.
					 ',viewCollection:"'.				$s_view					.'"'.
					 ',onPopup:Home.onPopup'.
					 ',optionsTag:"user_collection"'.
					 ',preloadImgPop:1'.
					 ',ulDvd:1'.
					 ',ulExplain:1'.
					 ',ulHome:1'.
					 ',ulJump:1'.
					 ',imgPreLoad:"pin.help.explain.aleft.aright.astop.across.acheck.aplus.adown.spin.spun.home.coll"'.
					 '}';
		return
					"function onMenuClick(action){DvdListMenuAction.onClick(action);};".
					"Filmaf.config({$s_config});".
					"setTimeout('".
						"Menus.setup();".
						"Home.setup();".
					"',100);".
					"if($('my-tab')){Tab.setup('my-tab',{background:'#ffffff', loadtab:{$this->mn_tab}, width:'100%'})};";
	}

	function drawBodyPage()
	{
		echo  "<div style='margin:0px 10px 10px 10px'>";
		CWidgetFriends::drawInvites($this);

		echo	"<div id='my-tab'><ul>";
		for ( $i = 0 ; $i < count($this->ma_home) ; $i++ )
		{
			if ( $this->mwb_view_self || ! $this->ma_home[$i]['self'] )
			{
				$s_home = $this->ma_home[$i]['label'];// . ( $this->ma_home[$i]['trg'] == '-' ? '...' : '' );

				if ( $this->mn_tab != $i )
				{
					echo "<li><a href='javascript:void(location.href=\"";
					if ( $this->ma_home[$i]['url'] == '-' )
						if ( $this->ma_home[$i]['trg'] == 'home' )
							echo "/";
						else
							echo "/?tab={$this->ma_home[$i]['trg']}";
					else
						echo $this->ma_home[$i]['url'];
					echo "\");'>{$s_home}</a></li>";
				}
				else
				{
					echo  "<li>{$s_home}<div>";
					$this->drawTab($i);
					echo "</div></li>";
				}
			}
		}
		echo	"</ul></div>".
			  "</div>";
	}

	function drawTab($i)
	{
		echo  "<form id='myform' name='myform' method='get' action='javascript:void(0)' style='padding:0 8px 0 8px'>".
				"<img src='http://dv1.us/d1/1.gif' width='640' height='1' />".
				"<table style='width:100%'>".
				  "<tr>";
		$n_cols = count($this->ma_home[$i]['widget']);
		for ( $k = 0 ; $k < $n_cols ; $k++ )
		{
			echo	"<td style='vertical-align:top;width:460px;padding:0 8px 0 8px'>".
					  "<table style='width:100%'>";

			$a_widget  = &$this->ma_home[$i]['widget'][$k];
			for ( $j = 0 ; $j < count($a_widget) ; $j++ )
			{
				echo	"<tr>".
						  "<td>";
							$this->drawWidget($a_widget[$j]);
				echo	  "</td>".
						"</tr>";
			}
			echo	  "</table>".
					"</td>";

			if ( $k < $n_cols - 1 )
				echo "<td style='width:8px'>&nbsp;</td>";
		}
		echo	  "</tr>".
				"</table>".
			  "</form>";
	}

	function drawWidget($s_widget)
	{
		switch ( $s_widget )
		{
		case 'profile':		CWidgetProfile::draw	($this); break;
		case 'wall':		CWidgetWall::draw		($this); break;
		case 'microblog':	CWidgetMicroblog::draw	($this); break;
		case 'friends':		CWidgetFriends::draw	($this); break;
		case 'updates':		CWidgetUpdates::draw	($this); break;
		case 'favvideos':	CWidgetFavVideos::draw	($this); break;
		case 'stats':		CWidgetStats::draw		($this); break;
		case 'blog':		CWidgetBlog::draw		($this); break;
		case 'options':		CWidgetOptions::draw	($this); break;
		case 'ebay':		echo $s_widget; break;
		}
	}
}

class CWidgetBlog extends CWidget
{
	function draw(&$wnd)
	{
		echo "<div style='padding:16px 0 36px 0'>";
		if ( $wnd->ms_user_id == $wnd->ms_user_id )
		{
			echo "<div style='padding-bottom:16px'>Hi,</div>".
				 "<div>If you have a blog you can link to it from the Options tab :-)</div>";
		}
		else
		{
			$s_gender = CSql::query_and_fetch1("SELECT gender FROM dvdaf_user_3 WHERE user_id = '{$wnd->ms_view_id}'",0,__FILE__,__LINE__);
			$s_gender = $s_gender == 'M' ? 'his' : ($s_gender == 'F' ? 'her' : 'his/her');
			echo ucfirst($wnd->ms_view_id)." has not added a link to {$s_gender} blog yet";
		}
		echo "</div>";
	}
}

?>
