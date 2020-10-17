<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndMenu.php';
require $gs_root.'/lib/CPicUtils.php';

require $gs_root.'/lib/CWidget.php';
//require $gs_root.'/lib/CWidgetFriends.php';
require $gs_root.'/lib/CWidgetWelcome.php';
require $gs_root.'/lib/CWidgetTutorial.php';
require $gs_root.'/lib/CWidgetUpcomingDvd.php';
require $gs_root.'/lib/CWidgetTopDirectors.php';
require $gs_root.'/lib/CWidgetTopMembers.php';
require $gs_root.'/lib/CWidgetPic.php';
require $gs_root.'/lib/CWidgetMedia.php';
require $gs_root.'/lib/CRegenSqlCache.php';

/*
   http://www.filmaf.edu/?gen-cache=1
   http://www.filmaf.com/?gen-cache=1
*/

class CMain extends CWndMenu
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace			= true;
//		$this->mb_trace_environment		= true;
//		$this->mb_trace_sql				= true;
//		$this->mb_allow_redirect		= true;
		$this->ms_include_js			= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-main_{$this->mn_lib_version}.js'></script>\n";
		$this->mn_tab					= 0;
		$this->mn_tab_jsinit			= 0;
		$this->ms_tab					= '/';
		$this->mb_advert				= false;
		$this->mn_page					= 0;
		$this->ma_dvd					= array();
		$this->mb_gen_cache				= dvdaf3_getvalue('regen', DVDAF3_GET|DVDAF3_LOWER) == 'regen';
		$this->ms_setupParms			= "{cookie:\"hometab\",med:0,reg:0,col:0,cat:0,sbg:0,gen:0}";
		$this->mb_facebook_div			= true;

		$this->ms_include_css .=
			"<style type='text/css'>".
				"A.more, A.more:visited, A.more:active, A.more:hover {text-decoration:underline; color:#de4141}".

				// stats
				".stat_tbl, #inp_tbl {border-collapse:separate;border-spacing:8px 1px;}".
				".stat_tbl a:link, .stat_tbl a:visited, .stat_tbl a:active, .stat_tbl a:hover {color:#072b4b}".
				".stat_tbl td {white-space:nowrap;vertical-align:top}".
				".stat_tbl span {color:#5988b1}".
				".stat_tbl p {color:#5988b1}".
				".stat_spc {padding-bottom:6px}".
				".stat_rank {text-align:right;color:#5988b1}".
			"</style>";
	}

	function validateDataSubmission()
	{
//		if ( $this->ms_user_id == 'ash' )
//			$this->ms_include_js = "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-main2_{$this->mn_lib_version}.js'></script>\n";

		$this->getTabs();
		$this->validateTab();
	}

	function getTabs()
	{
		$s_home = "<span style='color:#de4141'>FilmAf</span>";
		$s_myho = "<span style='color:#de4141'>Home</span>";
		$s_coll = "<span style='color:#de4141'>Collection</span>";
		$s_prev = "<span style='color:#70a2cf'>&lt;&lt;Prev</span>";
		$s_next = "<span style='color:#70a2cf'>Next&gt;&gt;</span>";

		$this->ma_home = array(
			array('label'=>$s_home			,'pg'=>0,'lg'=>false,'url'=>'/'									,'widget'=>array(
				array('S-welcome', 'S-bd', 'S-dvd', 'tutorial', 'network', 'pic1', 'microblog', 'pic2', 'recent'					  ),
				array('contrib-30days', 'pic3', 'contrib-year', 'pic4'																  ),
				array('directors'																									  ))),
			array('label'=>$s_myho			,'pg'=>0,'lg'=>true ,'url'=>"{$this->ms_user_subdomain}"		,'widget'=>null				),
			array('label'=>$s_coll			,'pg'=>0,'lg'=>true ,'url'=>"{$this->ms_user_subdomain}/owned"	,'widget'=>null				),
			array('label'=>'BDs'			,'pg'=>0,'lg'=>false,'url'=>'/blu-ray'							,'widget'=>array(array('x'))),
			array('label'=>'DVDs'			,'pg'=>0,'lg'=>false,'url'=>'/dvd'								,'widget'=>array(array('x'))),
			array('label'=>'Criterion'		,'pg'=>0,'lg'=>false,'url'=>'/criterion'						,'widget'=>array(array('x'))),
			array('label'=>'Comedy'			,'pg'=>0,'lg'=>false,'url'=>'/comedy'							,'widget'=>array(array('x'))),
			array('label'=>'Drama'			,'pg'=>0,'lg'=>false,'url'=>'/drama'							,'widget'=>array(array('x'))),
			array('label'=>'Horror'			,'pg'=>0,'lg'=>false,'url'=>'/horror'							,'widget'=>array(array('x'))),
			array('label'=>'Action'			,'pg'=>0,'lg'=>false,'url'=>'/action'							,'widget'=>array(array('x'))),
			array('label'=>'Sci-Fi'			,'pg'=>0,'lg'=>false,'url'=>'/sci-fi'							,'widget'=>array(array('x'))),
			array('label'=>'Animation'		,'pg'=>0,'lg'=>false,'url'=>'/animation'						,'widget'=>array(array('x'))),
			array('label'=>$s_next			,'pg'=>0,'lg'=>false,'url'=>'/anime'		,'redir'=>true		,'widget'=>array(array('x'))),
			array('label'=>$s_prev			,'pg'=>1,'lg'=>false,'url'=>'/'									,'widget'=>array(array('x'))),
			array('label'=>'Anime'			,'pg'=>1,'lg'=>false,'url'=>'/anime'							,'widget'=>array(array('x'))),
			array('label'=>'Suspense'		,'pg'=>1,'lg'=>false,'url'=>'/suspense'							,'widget'=>array(array('x'))),
			array('label'=>'Fantasy'		,'pg'=>1,'lg'=>false,'url'=>'/fantasy'							,'widget'=>array(array('x'))),
			array('label'=>'Documentary'	,'pg'=>1,'lg'=>false,'url'=>'/documentary'						,'widget'=>array(array('x'))),
			array('label'=>'Western'		,'pg'=>1,'lg'=>false,'url'=>'/western'							,'widget'=>array(array('x'))),
			array('label'=>'Sports'			,'pg'=>1,'lg'=>false,'url'=>'/sports'							,'widget'=>array(array('x'))),
			array('label'=>'War'			,'pg'=>1,'lg'=>false,'url'=>'/war'								,'widget'=>array(array('x'))),
			array('label'=>'Exploitation'	,'pg'=>1,'lg'=>false,'url'=>'/exploitation'						,'widget'=>array(array('x'))),
			array('label'=>'Musical'		,'pg'=>1,'lg'=>false,'url'=>'/musical'							,'widget'=>array(array('x'))),
			array('label'=>'Film Noir'		,'pg'=>1,'lg'=>false,'url'=>'/filmnoir'							,'widget'=>array(array('x'))),
			array('label'=>$s_next			,'pg'=>1,'lg'=>false,'url'=>'/music'		,'redir'=>true		,'widget'=>array(array('x'))),
			array('label'=>$s_prev			,'pg'=>2,'lg'=>false,'url'=>'/filmnoir'							,'widget'=>array(array('x'))),
			array('label'=>'Music'			,'pg'=>2,'lg'=>false,'url'=>'/music'							,'widget'=>array(array('x'))),
			array('label'=>'Erotica'		,'pg'=>2,'lg'=>false,'url'=>'/erotica'							,'widget'=>array(array('x'))),
			array('label'=>'Silent'			,'pg'=>2,'lg'=>false,'url'=>'/silent'							,'widget'=>array(array('x'))),
			array('label'=>'Experimental'	,'pg'=>2,'lg'=>false,'url'=>'/experimental'						,'widget'=>array(array('x'))),
			array('label'=>'Short'			,'pg'=>2,'lg'=>false,'url'=>'/short'							,'widget'=>array(array('x'))),
			array('label'=>'Performing Arts','pg'=>2,'lg'=>false,'url'=>'/performing-arts'					,'widget'=>array(array('x'))),
			array('label'=>'Educational'	,'pg'=>2,'lg'=>false,'url'=>'/educational'						,'widget'=>array(array('x'))),
			array('label'=>'DVD Audio'		,'pg'=>2,'lg'=>false,'url'=>'/dvd-audio'						,'widget'=>array(array('x'))));
	}

	function validateTab()
	{
		$s_url  = dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER|DVDAF3_NO_AMP_EXPANSION);
		$s_path = explode('?', substr($s_url,1));
		$s_path = preg_replace('/\/+$/', '', $s_path[0]);
		$a_path = explode('/', $s_path);
		$s_tab = '/' . $a_path[0];

		$this->mn_tab	  = 0;
		$n_curr_pg		  = 0;

		for ( $i = 0 ; $i < count($this->ma_home) ; $i++ )
		{
			if ( $n_curr_pg != $this->ma_home[$i]['pg'] )
			{
				$n_curr_pg = $this->ma_home[$i]['pg'];
			}
			if ( $this->ma_home[$i]['url'] == $s_tab && !isset($this->ma_home[$i]['redir']) )
			{
				$this->mn_tab = $i;
				$this->mn_page = $this->ma_home[$i]['pg'];
				break;
			}
		}

		$this->ms_tab = $s_tab;
	}

	function getFooterJavaScript()
	{
		$s_user    = $this->mb_logged_in ? $this->ms_user_id : '';
		$s_config  = '{baseDomain:"'.			$this->ms_base_subdomain.'"'.
					 ',userCollection:"'.		$s_user					.'"'.
					 ',preloadImgPop:1'.
					 ',onPopup:Main.onPopup'.
					 ',ulDvd:1'.
					 ',ulExplain:1'.
					 ',imgPreLoad:"pin.help.explain.tabbk.spun.home.coll"'.
					 '}';
		return
					"function onMenuClick(action){Main.onClick(action);};".
					"Filmaf.config({$s_config});".
					"setTimeout('Menus.setup();Main.setup({$this->ms_setupParms})',100);".
					"if($('my-tab')){Tab.setup('my-tab',{background:'#ffffff', loadtab:{$this->mn_tab_jsinit}, width:'100%', wholepage:1})};";
	}

	function drawBodyPage()
	{
		if ( $this->mb_gen_cache )
		{
			CRegenSqlCache::CollectionRank();
			CRegenSqlCache::ActiveCache();
			CRegenSqlCache::ActiveTopCache();
		}

		echo  "<div style='margin:0px 10px 10px 10px'>";

		if ( $this->mn_tab == 0 )
		{
			// CWidgetFriends::drawInvites($this);
			CWidgetPic::getSampleDvds($this, 4);
		}

		echo	"<div id='my-tab'><ul>";
		for ( $i = 0, $k = 0 ; $i < count($this->ma_home) ; $i++ )
		{
			if ($this->ma_home[$i]['pg'] == $this->mn_page && ($this->mb_logged_in || $this->ma_home[$i]['lg'] == false))
			{
				$s_home = $this->ma_home[$i]['label'];

				if ( $this->mn_tab != $i )
				{
					if ( $this->ma_home[$i]['url'] == '-' )
						if ( $this->ma_home[$i]['trg'] == 'home' )
							$s_trg = "/";
						else
							$s_trg = "/?tab={$this->ma_home[$i]['trg']}";
					else
						$s_trg = $this->ma_home[$i]['url'];

					echo "<li><a href='{$s_trg}'>{$s_home}</a></li>";
					$k++;
				}
				else
				{
					echo  "<li>{$s_home}<div>";
					$this->drawTab($i);
					echo "</div></li>";
					$this->mn_tab_jsinit = $k++;
				}
			}
		}
		echo	"</ul></div>".
			  "</div>";
	}

	function drawTab($i)
	{
		echo		"<img src='http://dv1.us/d1/1.gif' width='640' height='1' />".
					"<table style='width:100%'>".
					  "<tr>";
		$n_cols = count($this->ma_home[$i]['widget']);
		for ( $k = 0 ; $k < $n_cols ; $k++ )
		{
			$a_widget  = &$this->ma_home[$i]['widget'][$k];

			for ( $j = 0 ; $j < count($a_widget) && $a_widget[$j]{0} == 'S' ; $j++ )
			{
				echo	"<td colspan='". (2 * $n_cols - 1) ."' style='vertical-align:top;width:460px;padding:0 8px 0 8px'>";
						  $this->drawWidget($a_widget[$j]);
				echo	"</td>".
					  "</tr>".
					  "</tr>";
			}

			echo	"<td style='vertical-align:top;width:460px;padding:0 8px 0 8px'>".
					  "<table style='width:100%'>";

			for (  ; $j < count($a_widget) ; $j++ )
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
				"</table>";
	}

	function drawWidget($s_widget)
	{
		switch ( $s_widget )
		{
		case 'S-welcome':
			CWidgetWelcome::draw($this);
			break;
		case 'tutorial':
			CWidgetTutorial::draw($this);
			break;
		case 'pic1':
		case 'pic2':
		case 'pic3':
		case 'pic4':
			$i = intval(substr($s_widget,3)) - 1;
			if ( array_key_exists($i, $this->ma_dvd) )
				CWidgetPic::draw($this, $this->ma_dvd[$i]);
			break;
		case 'x':
			CWidgetMedia::draw($this, substr($this->ms_tab, 1));
			break;

		case 'S-bd':
		case 'S-dvd':
			$this->drawCacheableWidget(substr($s_widget,2));
			break;
		case 'directors':
		case 'network':
		case 'microblog':
		case 'recent':
		case 'contrib-30days':
		case 'contrib-year':
			$this->drawCacheableWidget($s_widget);
			break;
		default:
			echo $s_widget;
			break;
		}
	}

	function drawCacheableWidget($s_widget)
	{
		$s_cachefile = "cache/index-widget-{$s_widget}.txt";

		if ( ! $this->mb_gen_cache )
		{
			readfile($s_cachefile);
			return;
		}

		$s_cachetemp = $s_cachefile . '.tmp';
		ob_start();

		switch ( $s_widget )
		{
		case 'directors':
			CWidgetTopDirectors::draw($this);
			break;
		case 'bd':
		case 'dvd':
			CWidgetUpcomingDvd::draw($this,$s_widget);
			break;
		case 'network':
		case 'microblog':
		case 'recent':
		case 'contrib-30days':
		case 'contrib-year':
			CWidgetTopMembers::draw	($this,$s_widget);
			break;
		}

		$fp = fopen($s_cachetemp, 'w');
		fwrite($fp, ob_get_contents());
		fclose($fp);
		rename($s_cachetemp, $s_cachefile);
		ob_end_flush();
	}
}

?>
