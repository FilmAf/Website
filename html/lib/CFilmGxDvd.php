<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CFilmGx.php';

class CFilmGxDvd extends CFilmGx
{
    function constructor()
    {
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
		$this->ms_title			= 'Film not found';

		$a_parm					= explode('/', substr(dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER | DVDAF3_NO_AMP_EXPANSION),4));
		$this->mn_dvd_id		= intval($this->ms_canonical);
		$this->ms_obj_type		= 'dvd';
		$this->ms_obj_type1		= $this->ms_obj_type{1};
		$this->mb_valid			= false;
		$this->ms_folder		= '';
		$this->ms_pic			= '';
		$this->ma_director		= array();
	}

	function badRequester()
	{
		if ( strpos(dvdaf3_getvalue('HTTP_USER_AGENT', DVDAF3_SERVER|DVDAF3_LOWER),'googlebot') !== false )
			return true;

		return false;
	}

	function validRequest()
	{
		$ss = "a.dvd_id, a.dvd_title, a.publisher, a.director, a.director_nocase, a.genre, a.country, a.media_type, ".
			  "a.dvd_rel_dd, a.imdb_id, a.pic_name, unix_timestamp(a.dvd_updated_tm) dvd_updated_tm, p.price_00 amz_price,";
		if ( $this->mb_logged_in )
		{
			$ss = "SELECT {$ss} substr(b.folder,1,4) folder ".
					"FROM dvd a ".
					"LEFT JOIN v_my_dvd_ref b ON a.dvd_id = b.dvd_id and b.user_id = '{$this->ms_user_id}' ".
					"LEFT JOIN price p ON a.upc = p.upc ".
				   "WHERE a.dvd_id = {$this->mn_dvd_id}";
		}
		else
		{
			$ss = "SELECT {$ss} '' folder ".
					"FROM dvd a ".
					"LEFT JOIN price p ON a.upc = p.upc ".
				   "WHERE a.dvd_id = {$this->mn_dvd_id}";
		}

		if ( ($this->ma_data = CSql::query_and_fetch($ss,0,__FILE__,__LINE__)) )
		{
			$this->ma_data['dvd_rel_dd'] = $this->formatDate     ($this->ma_data['dvd_rel_dd']);
			$this->ma_data['country'   ] = $this->formatCountry  ($this->ma_data['country'   ]);
			$this->ma_data['publisher' ] = $this->formatPublisher($this->ma_data['publisher' ]);
			$this->ma_data['genre'     ] = $this->formatGenre	 ($this->ma_data['genre'     ]);
			$this->ms_title				 = $this->formatTitle    ($this->ma_data['dvd_title' ]);
			$this->ms_obj_type			 = $this->getObjType	 ($this->ma_data['media_type']);
			$this->ms_obj_type1			 = $this->ms_obj_type{1};
			$this->mb_valid				 = true;
			$this->ms_folder			 = $this->ma_data['folder'];
			$this->ms_pic				 = CPic::location($this->ma_data['pic_name'],CPic_PIC);

			$this->formatDirector($this->ma_data['director'], $this->ma_data['director_nocase']);
		}

		$this->initFacebookMeta();
		return true;
	}

	function initFacebookMeta()
	{
		$s_dir = '';
		$s_gen = '';
		$s_tit = str_replace('-','',str_replace(',',' ',$this->ms_title));
		if ( $this->mb_valid )
		{	
			$s_dvd_id	= $this->mn_dvd_id;
			$s_pic		= $this->ms_pic;
			$s_pub		= $this->ma_data['publisher'     ] == '' ? ' ' : str_replace('-','',str_replace(',','/',$this->ma_data['publisher']));
			$s_upd		= $this->ma_data['dvd_updated_tm'];
			$s_med		= str_replace('-','',$this->formatMediaType($this->ma_data['media_type'],false,'-'));
			$s_type		= $this->ms_obj_type;

			for ( $i = 0 ; $i < 3 && $i < count($this->ma_director) ; $i++ )
				$s_dir .= "<meta property='filmafi:director' content='http://www.filmaf.com/gd/{$this->ma_director[$i]}' />";

			if ( $this->ma_data['genre'] )
				$s_gen  = "<meta property='filmafi:genre' content='http://www.filmaf.com/gg/{$this->ma_data['genre']}' />";
		}
		else
		{
			$s_dvd_id	= 0;
			$s_pic		= 'http://dv1.us/d1/filmaf-med.png';
			$s_pub		= '';
			$s_upd		= 0;
			$s_med		= '';
			$s_type		= 'dvd';
		}



		$this->ms_head_attrib	= " prefix='og: http://ogp.me/ns# filmafi: http://ogp.me/ns/apps/filmafi#'";
		$this->ms_include_meta	= "<meta property='fb:app_id' content='413057338766015' />".
								  "<meta property='og:type' content='filmafi:{$s_type}' />".
								  "<meta property='og:url' content='http://www.filmaf.com/gp/{$s_dvd_id}' />".
								  "<meta property='og:updated_time' content='{$s_upd}' />".
								  "<meta property='og:title' content='{$s_tit}' />".
								  $s_dir.
								  "<meta property='filmafi:studio' content='{$s_pub}' />".
								  $s_gen.
								  "<meta property='filmafi:edition' content='{$s_med}' />".
								  "<meta property='og:image' content='{$s_pic}' />";

		//echo str_replace('<','<br />[',str_replace('>',']',$this->ms_include_meta));
	}

	function drawBodyPage()
	{
		if ( $this->ma_data )
			$this->drawBodyFound();
		else
			$this->drawBodyNotFound();
	}

	function drawBodyFound()
	{
        echo  "<table style='width:100%'>".
				"<tr>".
				  "<td style='vertical-align:top;width:1%'>";
					$this->drawBodyLeft();
		echo	  "</td>".
				  "<td style='vertical-align:top;width:99%'>";
					$this->drawBodyRight();
		echo	  "</td>".
				"</tr>".
			  "</table>";
	}

	function drawBodyLeft()
	{
		$s_dvd		= $this->ma_data['dvd_id'];
		$s_folder	= $this->ma_data['folder'];
		$s_price	= $this->ma_data['amz_price'];
		$s_imdb		= $this->ma_data['imdb_id'];
		$s_media	= $this->formatMediaType($this->ma_data['media_type'],false,'&nbsp;');
		$s_cnt		= $this->ma_data['country']    == '' ? '&nbsp;' : $this->ma_data['country'];
		$s_pub		= $this->ma_data['publisher'];
		$s_rel		= $this->ma_data['dvd_rel_dd'] == '' ? '&nbsp;' : $this->ma_data['dvd_rel_dd'];

		$s_price	= $s_price > 0 ? "@amz: <a href='/rt.php?vd=amz{$s_dvd}'>$".sprintf('%0.2f',$s_price)."</a>" : '&nbsp;';
		$n			= sprintf('%07d',$s_dvd);
		$s_media	= "<a href='/search.html?has=".$n."&init_form=str0_has_".$n."'>{$s_media}</a>";

		if ( $s_imdb == '-' )
		{
			$s_imdb	= '&nbsp;';
		}
		else
		{
			$n = intval((strlen($s_imdb) + 1) / 8);
			$s_imdb = 'imdb: ';
			for ( $i = 0 ; $i < $n ; $i++ )
				$s_imdb .= "<a href='/rt.php?vd=imd{$s_dvd}-{$i}'>x</a> ";
		}

		echo  "<div style='background:#0059a6;width:354px;margin:20px 10px 10px 20px'>".
				"<div style='border:1px solid #6c8ba6;padding:3px'>".
				  "<div style='background:#ffffff;text-align:center'>".

					"<div style='padding:8px 0 4px 0'>".
					  "<img src='{$this->ms_pic}' />".
					"</div>".
					"<div id='pop_text'>".
					  $this->paintInColl().
					"</div>".
					"<div style='overflow:hidden'>".
					  "<table class='img_bar' style='width:100%'>".
						"<tr>".
						  "<td class='img_bar' style='border-right:solid 2px #0059a6;border-top:solid 2px #0059a6'>{$s_media}</td>".
						  "<td class='img_bar' style='border-left:solid 2px #0059a6;border-right:solid 2px #0059a6;border-top:solid 2px #0059a6'>{$s_price}</td>".
						  "<td class='img_bar' style='border-left:solid 2px #0059a6;border-right:solid 2px #0059a6;border-top:solid 2px #0059a6'>{$s_rel}</td>".
						  "<td class='img_bar' style='border-left:solid 2px #0059a6;border-top:solid 2px #0059a6'>{$s_imdb}</td>".
						"</tr>".
						"<tr>".
						  "<td class='img_bar' style='border-right:solid 2px #0059a6;border-top:solid 2px #0059a6'>{$s_cnt}</td>".
						  "<td class='img_bar' style='border-left:solid 2px #0059a6;border-top:solid 2px #0059a6;overflow:hidden' colspan='3'>{$s_pub}</td>".
						"</tr>".
					  "</table>".
					"</div>".
				  "</div>".
				"</div>".
			  "</div>";
	}

	function drawBodyRight()
	{
		$b = $this->mb_logged_in;
		$w = $b ? ucfirst($this->ms_user_id .(substr($this->ms_user_id,-1) == 's' ? '&#039;' : '&#039;s')) : '';
		$t = $b ? '' : " style='color:#999999'";
		$n = $this->mn_dvd_id;
		$c = $this->ms_folder != '';
		$f = $this->ms_folder;
		$v = $c ? 'Move to ': 'Add to ';
		$m = $this->ms_obj_type1;

        echo
		  "<div style='padding:0 20px 10px 10px'>".
			"<table style='width:100%'>".
				"<tr>".
				  "<td colspan='3'>".
					"<table style='text-align:center;width:100%'>".
					  "<tr>".
						"<td>".
						  "<h2 style='margin:24px 0 2px 0; padding:0 0 0 0; text-align:center'>Welcome to Film Aficionado</h2>".
						  "<div style='text-align:center'>Your favorite spot for Film collecting</div>".
						  "<div style='text-align:center'><a href='/'>Explore FilmAf</a></div>".
						"</td>".
					  "</tr>".
					"</table>".
				  "</td>".
				"</tr>".
				"<tr>".

				  "<td style='vertical-align:top;width:1%'>".
					"<div style='padding:0 24px 0 0'>".
					"<input id='obj_type' type='hidden' value='{$m}' />".
					"<table>".
					  "<tr><td><h2 style='margin:12px 0 10px 0;white-space:nowrap'>Publish to Facebook</h2></td></tr>".
					  "<tr><td><a href='javascript:Facebook.see({$n},{$m})'>Seeing it</a></td></tr>".
					  "<tr><td><a href='javascript:Facebook.want({$n},{$m})'>Want it</a></td></tr>".
					  "<tr><td><a href='javascript:Facebook.order({$n},{$m})'>Ordered it</a></td></tr>".
					  "<tr><td><a href='javascript:Facebook.get({$n},{$m})'>Got it</a></td></tr>".
					  "<tr><td><a href='javascript:void(0)' id='a_blog' class='blog_pop'{$t}>Blog it</a></td></tr>".
					  "<tr><td><a href='javascript:void(0)' id='a_more' class='more_pop'{$t}>More</a></td></tr>".
					  "<tr><td><h2 style='margin:20px 0 10px 0;white-space:nowrap'>Film Collection</h2></td></tr>".
($b ?				  "<tr><td style='padding-bottom:10px'><a href='{$this->ms_user_subdomain}/owned'>{$w} collection</a></td></tr>" : '').
					  "<tr><td id='f_owned'>".($f == 'owne' ? "<span style='color:#de4141'>In owned</span>"     : "<a href='javascript:FilmGp.owned({$n})'{$t}>{$v}owned</a>"   )."</td></tr>".
					  "<tr><td id='f_wish'>" .($f == 'wish' ? "<span style='color:#de4141'>In wish-list</span>" : "<a href='javascript:FilmGp.wish({$n})'{$t}>{$v}wish-list</a>")."</td></tr>".
					  "<tr><td id='f_order'>".($f == 'on-o' ? "<span style='color:#de4141'>In on-order</span>"  : "<a href='javascript:FilmGp.order({$n})'{$t}>{$v}on-order</a>")."</td></tr>".
					  "<tr><td id='f_work'>" .($f == 'work' ? "<span style='color:#de4141'>In work</span>"      : "<a href='javascript:FilmGp.work({$n})'{$t}>{$v}work</a>"     )."</td></tr>".
					  "<tr><td id='f_have'>" .($f == 'have' ? "<span style='color:#de4141'>In have-seen</span>" : "<a href='javascript:FilmGp.seen({$n})'{$t}>{$v}have-seen</a>")."</td></tr>".
					  "<tr><td id='f_del'>"  .($c           ? "<a href='javascript:FilmGp.del({$n})'>Delete from collection</a>" : '&nbsp;'									)."</td></tr>".
					"</table>".
					"</div>".
				  "</td>".

				  "<td rowspan='2' style='vertical-align:top;width:1%'>".
					"<div style='padding:0 0 0 24px'>".
					"<table>".
					  "<tr><td><h2 style='margin:12px 0 10px 0;white-space:nowrap'>Browse by Format</h2></td></tr>".
					  "<tr><td><a href='/blu-ray'>BDs</a></td></tr>".
					  "<tr><td><a href='/dvd'>DVDs</a></td></tr>".
					  "<tr><td><h2 style='margin:20px 0 10px 0;white-space:nowrap'>Browse by Publisher</h2></td></tr>".
					  "<tr><td><a href='/criterion'>Criterion</a></td></tr>".
					  "<tr><td><h2 style='margin:20px 0 10px 0;white-space:nowrap'>Browse by genre:</h2></td></tr>".
					  "<tr><td><a href='/comedy'>Comedy</a></td></tr>".
					  "<tr><td><a href='/drama'>Drama</a></td></tr>".
					  "<tr><td><a href='/horror'>Horror</a></td></tr>".
					  "<tr><td><a href='/action'>Action</a></td></tr>".
					  "<tr><td><a href='/sci-fi'>Sci-Fi</a></td></tr>".
					  "<tr><td><a href='/animation'>Animation</a></td></tr>".
					  "<tr><td><a href='/anime'>Anime</a></td></tr>".
					  "<tr><td><a href='/suspense'>Suspense</a></td></tr>".
					  "<tr><td><a href='/fantasy'>Fantasy</a></td></tr>".
					  "<tr><td><a href='/documentary'>Documentary</a></td></tr>".
					  "<tr><td><a href='/western'>Western</a></td></tr>".
					  "<tr><td><a href='/sports'>Sports</a></td></tr>".
					  "<tr><td><a href='/war'>War</a></td></tr>".
					  "<tr><td><a href='/exploitation'>Exploitation</a></td></tr>".
					  "<tr><td><a href='/musical'>Musical</a></td></tr>".
					  "<tr><td><a href='/filmnoir'>Film Noir</a></td></tr>".
					  "<tr><td><a href='/music'>Music</a></td></tr>".
					  "<tr><td><a href='/erotica'>Erotica</a></td></tr>".
					  "<tr><td><a href='/silent'>Silent</a></td></tr>".
					  "<tr><td><a href='/experimental'>Experimental</a></td></tr>".
					  "<tr><td><a href='/short'>Short</a></td></tr>".
					  "<tr><td><a href='/performing-arts'>Performing Arts</a></td></tr>".
					  "<tr><td><a href='/educational'>Educational</a></td></tr>".
					  "<tr><td><a href='/dvd-audio'>DVD Audio</a></td></tr>".
					"</table>".
					"</div>".
				  "</td>".

				  "<td rowspan='2' style='vertical-align:top;width:97%'>".
					"&nbsp;".
				  "</td>".

				"</tr>".
				"<tr>".

				  "<td style='vertical-align:bottom'>".
					"<div style='padding:0 24px 0 0'>".
					"&nbsp;".
					"<table>".
					  "<tr><td><h2 style='margin:12px 0 10px 0;white-space:nowrap'>Youtube Channel</h2></td></tr>".
					  "<tr><td><a href='http://www.youtube.com/user/FilmAf' target='yt'>Film Aficionado</a></td></tr>".
					"</table>".
					"</div>".
				  "</td>".

				"</tr>".
			  "</table>".
			"</div>";
	}

	function drawBodyNotFound()
	{
		echo  "<div class='msgbox-a'><div class='msgbox-b'><div class='msgbox-c'><div class='msgbox-d'>".
				"<div class='msgbox'>".
				  "<div>Sorry we could not find the title you requested.</div>".
				  "<div style='padding-top:10px'>Perhaps you want to check out some hot <a href='/blu-ray'>Blu-rays</a> instead.</div>".
				"</div>".
			  "</div></div></div></div>";
	}

	function paintInColl()
	{
		if ( $this->ms_folder )
		{
			return  "<div>".
					  "<div><div style='float:right'><a href='javascript:FilmGp.editions({$this->mn_dvd_id})'>[+]</a></div>In your collection</div>".
					  "<img src='http://dv1.us/d1/".$this->formatFolderImg($this->ms_folder,'1.gif')."' width='90px' height='90px' />".
					  "<div class='img_text'>".$this->formatFolderName($this->ms_folder)."</div>".
					"</div>";
		}
		else
		{
			return "<div style='padding:0 4px 4px 4px'><div style='float:right'><a href='javascript:FilmGp.editions({$this->mn_dvd_id})'>[+]</a></div>&nbsp;</div>";
		}
	}

	function getFooterJavaScript()
	{
		$s_user   = $this->mb_logged_in  ? $this->ms_user_id : '';
		$s_config = '{baseDomain:"'.		$this->ms_base_subdomain.'"'.
					',userCollection:"'.	$s_user.'"'.
					',onPopup:FilmGp.onPopup'.
					',objId:'.				$this->mn_dvd_id.
					',ulBlog:1'.
					 ',imgPreLoad:"spun.home.coll"'.
					'}';

		return		"Filmaf.config({$s_config});".
					"FilmGp.setup();";
	}

	// ================================================================================================================

	function formatDate($s)
	{
		$s = trim($s);
		return $s == '-' ? '' : substr($s,0,4).'-'.substr($s,4,2).'-'.substr($s,6,2);
	}

	function formatCountry($s)
	{
		return $s == '-' ? '' : strtoupper(substr($this->ma_data['country'],1,-1));
	}

	function formatPublisher($s)
	{
		if ( $s == '-' ) return '';
		$s = explode(',',$s);
		return count($s) > 1 ? "{$s[0]}, {$s[1]}" : $s[0];
	}

	function formatDirector($ca,$nc)
	{
		$ca = explode(',',$ca);
		$nc = explode('/',substr($nc,2,-2));

		for ( $i = 0 ; $i < 3 && $i < count($nc) ; $i++ )
			if ( ! strpos($ca[$i],'(+)') )
				$this->ma_director[] = str_replace(' ','-',trim($nc[$i]));
	}

	function formatTitle($s)
	{
		$m = strlen($s);
		if ( ($n = strpos($s,'<' )) !== false ) if ( $m > $n ) $m = $n;
		if ( ($n = strpos($s,'(' )) !== false ) if ( $m > $n ) $m = $n;
		if ( ($n = strpos($s,'- ')) !== false ) if ( $m > $n ) $m = $n;
		$a    = explode(',',substr($s,0,$m));
		$a[0] = trim($a[0]);
		if ( count($a) > 1 )
		{
			$a[1] = trim($a[1]);
			switch ($a[1])
			{
			case 'The': case 'An': case 'A':
				$a[0] = $a[1] . ' ' . $a[0];
				break;
			default:
				$a[0] = $a[0] . ', ' . $a[1];
				break;
			}
		}
		return $a[0];
	}

	function getObjType($s_code)
	{
		$a = array('D'=>'dvd',
				   'B'=>'bluray',
				   '3'=>'bluray',
				   '2'=>'bluray',
				   'R'=>'bluray',
				   'V'=>'dvd',
				   'H'=>'dvd',
				   'C'=>'dvd',
				   'T'=>'dvd',
				   'A'=>'dvd',
				   'P'=>'dvd',
				   'F'=>'film',
				   'S'=>'film',
				   'L'=>'film',
				   'E'=>'film',
				   'N'=>'film',
				   'O'=>'dvd');
		return isset($a[$s_code]) ? $a[$s_code] : 'dvd';
	}

	function formatMediaType($s_code, $s_on_empty)
	{
		$a = array('D'=>'DVD',
				   'B'=>'Blu-ray',
				   '3'=>'Blu-ray 3D',
				   '2'=>'Blu-ray/DVD',
				   'R'=>'BD-R',
				   'V'=>'DVD-R',
				   'H'=>'HD DVD',
				   'C'=>'HD DVD/DVD',
				   'T'=>'HD DVD/DVD',
				   'A'=>'DVD Audio',
				   'P'=>'Placeholder',
				   'F'=>'Film',
				   'S'=>'Short',
				   'L'=>'Television',
				   'E'=>'Featurette',
				   'N'=>'Events/Perf',
				   'O'=>'Other');
		return isset($a[$s_code]) ? $a[$s_code] : $s_on_empty;
	}

	function formatGenre($n)
	{
		switch ( intval($n / 1000) * 1000 )
		{
		case 20000: return 'comedy';
		case 28000: return 'drama';
		case 55000: return 'horror';
		case 10000: return 'action';
		case 70000: return 'sci-fi';
		case 13000: return 'animation';
		case 16000: return 'anime';
		case 84000: return 'suspense';
		case 43000: return 'fantasy';
		case 24000: return 'documentary';
		case 91000: return 'western';
		case 80000: return 'sports';
		case 88000: return 'war';
		case 41000: return 'exploitation';
		case 62000: return 'musical';
		case 47000: return 'filmnoir';
		case 59000: return 'music';
		case 36000: return 'erotica';
		case 76000: return 'silent';
		case 39000: return 'experimental';
		case 73000: return 'short';
		case 66000: return 'performing-arts';
		case 32000: return 'educational';
		case 95000: return 'dvd-audio';
		}
		return '';
	}

	function formatFolderName($s)
	{
		$a = array('owne'=>'owned','wish'=>'wish&nbsp;list','on-o'=>'on&nbsp;order','work'=>'work','have'=>'have&nbsp;seen');
		return isset($a[$s]) ? $a[$s] : '';
	}

	function formatFolderImg($s,$s_on_empty)
	{
		$a = array('owne'=>'chk-g.gif','wish'=>'chk-r.gif','on-o'=>'chk-b.gif','work'=>'chk-y.gif','have'=>'chk-z.gif');
		return isset($a[$s]) ? $a[$s] : $s_on_empty;
	}
}

?>
