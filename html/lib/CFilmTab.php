<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CFilmTab
{
	function getSetupParms($s_cookie, $b_months, $b_crit, $s_tab)
	{
		$this->ms_med  = '';
		$this->ms_reg  = '';
		$this->ms_col  = '';
		$this->ms_cat  = '';
		$this->mn_gen  = 0;
		$this->mn_sub  = 0;
		$this->mb_crit = false;

		$a = explode('|', dvdaf3_getvalue($s_cookie, DVDAF3_COOKIE));
		if ( count($a) >= 4 )
		{
			$this->ms_med = $a[0];
			$this->ms_reg = $a[1];
			$this->ms_col = $a[2];
			$this->ms_cat = $a[3];
//			$this->mn_sub = count($a) >= 5 ? intval($a[4]) : 0;
		}

		$s_cat_selector = ',col,rnk,prc,'.
						  ($b_crit   ? 'spn,' : '').
						  ($b_months ? 'm01,m02,m03,m04,m05,m06,m07,m08,m09,m10,m11,m12,' : '');

		if ( strpos(',bd,dvd,'			,",{$this->ms_med},") === false ) $this->ms_med = 'bd';
		if ( strpos(',us,uk,ot,'		,",{$this->ms_reg},") === false ) $this->ms_reg = 'us';
		if ( strpos(',all,min,wis,not,'	,",{$this->ms_col},") === false ) $this->ms_col = 'all';
		if ( strpos($s_cat_selector		,",{$this->ms_cat},") === false ) $this->ms_cat = 'col';

		switch ( $s_tab )
		{
		case 'blu-ray':			$this->ms_med =  'bd'; break;
		case 'dvd':				$this->ms_med = 'dvd'; break;
		case 'criterion':		$this->ms_reg =  'us'; $this->mb_crit = true; break;
		case 'comedy':			$this->mn_gen = 20000; break;
		case 'drama':			$this->mn_gen = 28000; break;
		case 'horror':			$this->mn_gen = 55000; break;
		case 'action':			$this->mn_gen = 10000; break;
		case 'sci-fi':			$this->mn_gen = 70000; break;
		case 'animation':		$this->mn_gen = 13000; break;
		case 'anime':			$this->mn_gen = 16000; break;
		case 'suspense':		$this->mn_gen = 84000; break;
		case 'fantasy':			$this->mn_gen = 43000; break;
		case 'documentary':		$this->mn_gen = 24000; break;
		case 'western':			$this->mn_gen = 91000; break;
		case 'sports':			$this->mn_gen = 80000; break;
		case 'war':				$this->mn_gen = 88000; break;
		case 'exploitation':	$this->mn_gen = 41000; break;
		case 'musical':			$this->mn_gen = 62000; break;
		case 'filmnoir':		$this->mn_gen = 47000; break;
		case 'music':			$this->mn_gen = 59000; break;
		case 'erotica':			$this->mn_gen = 36000; break;
		case 'silent':			$this->mn_gen = 76000; break;
		case 'experimental':	$this->mn_gen = 39000; break;
		case 'short':			$this->mn_gen = 73000; break;
		case 'performing-arts':	$this->mn_gen = 66000; break;
		case 'educational':		$this->mn_gen = 32000; break;
		case 'dvd-audio':		$this->mn_gen = 95000; break;
		}

		// collection criteria only if one is logged in
		if ( ! $this->mb_logged_in )
			$this->ms_col = 'all';

		// subgenres only for star users
		if ( $this->mn_user_stars <= 0 )
			$this->mn_sub = 0;

		// rank and price not available outside the US
		if ( $this->ms_reg != 'us' && ($this->ms_cat == 'rnk' || $this->ms_cat == 'prc') )
			$this->ms_cat = 'col';

		// spine only available for criterion
		if ( ! $this->mb_crit && $this->ms_cat == 'spn' )
			$this->ms_cat = 'col';

		if ( $this->mn_sub % 1000 == 0 || $this->mn_sub <= $this->mn_gen || $this->mn_sub >= $this->mn_gen + 1000 )
			$this->mn_sub = 0;

		return	"{cookie:\"{$s_cookie}\"".
				",med:\"{$this->ms_med}\"".
				",reg:\"{$this->ms_reg}\"".
				",col:\"{$this->ms_col}\"".
				",cat:\"{$this->ms_cat}\"".
				",sbg:{$this->mn_sub}".
				",gen:{$this->mn_gen}}";
	}

	function getMonthSelector()
	{
		$mm = array ('J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D');
		$mn = array ('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
		$m0 = intval(date("n")) - 1;
		$m1 = ($m0 + 1) % 12;
		$m2 = ($m1 + 1) % 12;
		$m3 = ($m2 + 1) % 12;

		$s = '';
		for ( $i = 0 ; $i < 12 ; $i++ )
		{
			if ( $m0 == $i )
				$s .= "<td class='td_opt' id='stat_m{$mn[$i]}' style='color:#de4141'>{$mm[$i]}</td>";
		    else
				if ( $i == $m1 || $i == $m2 || $i == $m3 )
					$s .= "<td class='td_opt' id='stat_m{$mn[$i]}' style='color:#70a2cf'>{$mm[$i]}</td>";
				else
					$s .= "<td class='td_opt' id='stat_m{$mn[$i]}'>{$mm[$i]}</td>";
		}

		return $s;
	}

	function drawSelectors($b_months, $s_tab)
	{
		$b_bd	= $s_tab == 'blu-ray';
		$b_dvd	= $s_tab == 'dvd';
		$b_us	= $this->ms_reg == 'us';
		$b_crit	= $this->mb_crit;
		$b_col	= $this->mb_logged_in;
/*
		if ( $this->mn_gen && $b_subgen )
		{
			$s = CFilmTab::getSubgenres($this->mn_gen);
			if ( $s ) echo
			"<div style='padding:1px 0 1px 0'>".
			  "<table id='tab_sbg' style='border-collapse:separate;border-spacing:2px 0'>".
				"<tr>".
				  "<td>Genre:</td>".
				  $s.
				"</tr>".
			  "</table>".
			"</div>";
		}
*/
		echo
			"<div style='padding:1px 0 8px 0'>".
			  "<table>".
				"<tr>".
				  "<td style='vertical-align:top'>".
					"<table>".
					  "<tr>".
						"<td style='padding-right:36px'>".
						  "<table id='tab_med' style='border-collapse:separate;border-spacing:2px 0'>".
							"<tr>".
($b_dvd ? '' :				  "<td class='td_opt' id='stat_bd'>Blu-ray</td>"		).
($b_bd  ? '' :				  "<td class='td_opt' id='stat_dvd'>DVD</td>"			).
							"</tr>".
						  "</table>".
						"</td>".
						"<td style='padding-right:36px'>".
						  "<table id='tab_reg' style='border-collapse:separate;border-spacing:2px 0'>".
							"<tr>".
							  "<td class='td_opt' id='stat_us'>US</td>".
($b_crit ? '' :				  "<td class='td_opt' id='stat_uk'>UK</td>"				).
($b_crit ? '' :				  "<td class='td_opt' id='stat_ot'>Other</td>"			).
							"</tr>".
						  "</table>".
						"</td>".
(!$b_col ? '' :			"<td style='padding-right:36px'>".
						  "<table id='tab_col' style='border-collapse:separate;border-spacing:2px 0'>".
							"<tr>".
							  "<td class='td_opt' id='stat_all'>All</td>".
							  "<td class='td_opt' id='stat_min'>Mine</td>".
							  "<td class='td_opt' id='stat_wis'>Wish</td>".
							  "<td class='td_opt' id='stat_not'>Not</td>".
							"</tr>".
						  "</table>".
						"</td>"														).
						"<td>".
						  "<table id='tab_cat' style='border-collapse:separate;border-spacing:2px 0'>".
							"<tr>".
							  "<td class='td_opt' id='stat_col'>Collections</td>".
(! $b_crit ? '' :			  "<td class='td_opt' id='stat_spn'>Spine</td>"			).
(! $b_us   ? '' :			  "<td class='td_opt' id='stat_rnk'>Rank</td>"			).
(! $b_us   ? '' :			  "<td class='td_opt' id='stat_prc'>Price</td>"			).
(!$b_months? '' :			  CFilmTab::getMonthSelector()).
							"</tr>".
						  "</table>".
						"</td>".
					  "</tr>".
					"</table>".
				  "</td>".
				"</tr>".
			  "</table>".
			"</div>";
	}

	function getDvdSql($s_dir_nocase, $n_min_rank)
	{
		$s_select	= 'a.dvd_id, a.media_type';
		switch ( $this->ms_med )
		{
		case 'dvd':  $s_where = "a.media_type = 'D' and "; break;
		case 'film': $s_where = "a.media_type = 'F' and "; break;
		default:     $s_where = "a.media_type = 'B' and "; break;
		}

		if ( $this->mn_gen > 0 )
		{
			if ( $this->mn_sub > 0 )
				$s_where .= "a.subgenre = {$this->mn_sub} and ";
			else
				$s_where .= "a.genre = {$this->mn_gen} and ";
		}
		else
		{
			$this->mn_sub = 0;
		}

		if ( $this->mb_crit )
		{
			$s_where  .= "a.criterion != 'N' and ";
			$s_select .= ", a.criterion, a.spine";
		}
		else
		{
			$s_where  .= "a.region = '{$this->ms_reg}' and ";
		}

		switch ( $this->ms_cat )
		{
		case 'rnk':
			$s_where  .= "a.amz_rank < 9999999 and ";
			$s_order   = "a.amz_rank, a.dvd_id DESC";
			$s_limit   = $this->mb_crit ? '' : " LIMIT 500";
			break;
		case 'spn':
			$s_order   = "a.criterion, IF(a.spine = 0,99999,a.spine), a.dvd_id DESC";
			$s_limit   = '';
			break;
		case 'col':
			if ( $n_min_rank > 0 )
				$s_where  .= "a.collection_rank >= {$n_min_rank} and ";
			$s_order   = "a.collection_rank DESC, a.dvd_id DESC";
			$s_limit   = $this->mb_crit ? '' : " LIMIT 500";
			break;
		case 'prc':
			$s_select .= ", a.best_price";
			$s_where  .= "a.amz_rank < 100000 and a.best_price > 0 and ";
			$s_order   = "a.best_price, a.amz_rank, a.dvd_id DESC";
			$s_limit   = $this->mb_crit ? '' : " LIMIT 500";
			break;
		default:
			$mm = intval(substr($this->ms_cat,1));
			$yy = intval(date("Y"));
			$m0 = intval(date("n")) - 1;
			$m1 = ($m0 + 1) % 12;
			$m2 = ($m1 + 1) % 12;
			$m3 = ($m2 + 1) % 12;
			if ( ($mm == $m1 && $m1 <= 1) || ($mm == $m2 && $m2 <= 2) || ($mm == $m3 && $m3 <= 3) ) $yy++;
			$m2 = ($mm % 12) + 1;
			$y2 = $yy + ($m2 < $mm ? 1 : 0);
			$mm = substr('0'.$mm, -2);
			$m2 = substr('0'.$m2, -2);
			$s_where  .= "a.dvd_rel_dd >= '{$yy}{$mm}00' and a.dvd_rel_dd < '{$y2}{$m2}00' and ";
			$s_order   = "a.dvd_rel_dd, a.amz_rank, a.dvd_id DESC";
			$s_limit   = '';
			break;
		}

		if ( $s_dir_nocase !== false )
		{
			$s_from   = "(SELECT dvd_id FROM search_all_1 s WHERE s.obj_type = 'D' and s.nocase = '{$s_dir_nocase} /' and s.whole='Y') s ".
						"JOIN active_cache a ON s.dvd_id = a.dvd_id and a.good_listing != 'N'";
		}
		else
		{
			$s_from   = "active_cache a";
			if ( $this->ms_col == 'all' || $this->ms_col == 'not' )
				$s_where .= "a.good_listing = 'Y' and ";
		}

		$s_where = substr($s_where, 0, -5);

		switch ( $this->ms_col )
		{
		case 'all':
			$ss = "SELECT {$s_select} FROM {$s_from} ".
				   "WHERE {$s_where} ORDER BY {$s_order}{$s_limit}";
			break;
		case 'min': 
			$ss = "SELECT {$s_select} FROM {$s_from} ".
					"JOIN v_my_dvd_ref b ON a.dvd_id = b.dvd_id and b.user_id = '{$this->ms_user_id}' and (b.folder like 'owned%' or b.folder like 'on-order%') ".
				   "WHERE {$s_where} ORDER BY {$s_order}{$s_limit}";
			break;
		case 'wis': 
			$ss = "SELECT {$s_select} FROM {$s_from} ".
					"JOIN v_my_dvd_ref b ON a.dvd_id = b.dvd_id and b.user_id = '{$this->ms_user_id}' and b.folder like 'wish-list%' ".
				   "WHERE {$s_where} ORDER BY {$s_order}{$s_limit}";
			break;
		case 'not':
			$ss = "SELECT {$s_select} FROM {$s_from} ".
				   "WHERE {$s_where} ORDER BY {$s_order}{$s_limit}";
			$ss = "SELECT {$s_select} ".
					"FROM ({$ss}) a ".
				   "WHERE not exists (SELECT 1 FROM v_my_dvd_ref b WHERE a.dvd_id = b.dvd_id and b.user_id = '{$this->ms_user_id}')";
			break;
		}

		return "SELECT {$s_select}, d.pic_name FROM ({$ss}) a JOIN dvd d ON a.dvd_id = d.dvd_id";
	}

	function getCaption()
	{
		if ( $this->ms_cat == 'prc' ) return 'price';
		if ( $this->mb_crit         ) return 'criterion';
		return '';
	}

	function fetchAndDraw($ss, $s_caption, $b_dir)
	{
		$aa = array();

		if ( ($rr = CSql::query($ss, 0,__FILE__,__LINE__)) )
		{
			while ( ($ln = CSql::fetch($rr)) )
				$aa[] = $ln;
			CSql::free($rr);
		}

		if ( count($aa) > 0 )
			CFilmTab::drawFound($aa, $s_caption);
		else
			if ( $b_dir )
				CFilmTab::drawDirNotFound();
	}

	function drawFound(&$aa, $s_caption)
	{
		$s = "<div>";
		for ( $i = 0 ; $i < count($aa) ; $i++ )
		{
			$n = $aa[$i]['dvd_id'];
			$m = strtolower($aa[$i]['media_type']);

			switch ( $s_caption )
			{
			case 'criterion':
				$p = $aa[$i]['spine'];
				$c = $aa[$i]['criterion'];
				switch ( $c )
				{
				case 'C': $p = $p ? "spine {$p}"   : 'n/a'; break;
				case 'E': $p = $p ? "eclipse {$p}" : 'eclipse ?'; break;
				}
				$s .= "<div class='img_float'>".
						"<a id='{$m}_{$n}' class='dvd_pic' href='javascript:void(0)'>".
						  "<img id='zo_{$n}' src='".CPic::location($aa[$i]['pic_name'],CPic_THUMB)."' width='63' height='90' />".
						"</a> ".
						"<div class='img_text'>{$p}</div>".
					  "</div>";
				break;
			case 'price':
				$s .= "<div class='img_float'>".
						"<a id='{$m}_{$n}' class='dvd_pic' href='javascript:void(0)'>".
						  "<img id='zo_{$n}' src='".CPic::location($aa[$i]['pic_name'],CPic_THUMB)."' width='63' height='90' />".
						"</a> ".
						"<div class='img_text'>".
						  "<a href='{$this->ms_base_subdomain}/rt.php?vd=amz{$n}'>" . sprintf('$%0.2f', $aa[$i]['best_price']) . "</a>".
						"</div>".
					  "</div>";
				break;
			default:
				$s .= "<a id='{$m}_{$n}' class='dvd_pic' href='javascript:void(0)'>".
						"<img id='zo_{$n}' src='".CPic::location($aa[$i]['pic_name'],CPic_THUMB)."' width='63' height='90' class='img_space' />".
					  "</a> ";
				break;
			}
		}
		echo $s . "</div>";
	}

	function drawDirNotFound()
	{
		$s = "<span style='color:#de4141'>";
		switch ( $this->ms_reg )
		{
		case 'us':	$s .= 'US ';					break;
		case 'uk':	$s .= 'UK ';					break;
		default:    $s .= 'non-US/UK ';				break;
		}

		switch ( $this->ms_med )
		{
		case 'bd':	$s .= 'Blu-ray ';				break;
		default:    $s .= 'DVD ';					break;
		}
		$s .= "</span>";

		$s = "Sorry we could not find {$s} titles for <span style='color:#de4141'>{$this->ms_title}</span> with the criteria above.";

		switch ( $this->ms_col )
		{
		case 'min':	$s .= 'in your collection ';	break;
		case 'wis':	$s .= 'in your wish list ';		break;
		case 'not':	$s .= 'not in your collection ';break;
		}

		echo  "<div class='msgbox-a'><div class='msgbox-b'><div class='msgbox-c'><div class='msgbox-d'>".
				"<div class='msgbox'>".
				  "<div>{$s}</div>".
				  "<div style='padding-top:10px'>Please use the selectors above to try a different combination.</div>".
				"</div>".
			  "</div></div></div></div>";
	}
/*
	function getSubgenres($g)
	{
		$s = '';
		switch ( $g )
		{
		case 20000: $s =
			"<td class='td_opt' id='gno_20000'>All</td>".
			"<td class='td_opt' id='gno_20100'>Dark</td>".
			"<td class='td_opt' id='gno_20200'>Farce</td>".
			"<td class='td_opt' id='gno_20300'>Horror</td>".
			"<td class='td_opt' id='gno_20400'>Romantic</td>".
			"<td class='td_opt' id='gno_20600'>Satire</td>".
			"<td class='td_opt' id='gno_20650'>Sci-Fi</td>".
			"<td class='td_opt' id='gno_20700'>Screwball</td>".
			"<td class='td_opt' id='gno_20750'>Sitcom</td>".
			"<td class='td_opt' id='gno_20800'>Sketches/Stand-Up</td>".
			"<td class='td_opt' id='gno_20850'>Slapstick</td>".
			"<td class='td_opt' id='gno_20900'>Teen</td>".
			"<td class='td_opt' id='gno_20999'>NoSub</td>";
			break;
		case 28000: $s =
			"<td class='td_opt' id='gno_28000'>All</td>".
			"<td class='td_opt' id='gno_28100'>Courtroom</td>".
			"<td class='td_opt' id='gno_28150'>Crime</td>".
			"<td class='td_opt' id='gno_28200'>Docudrama</td>".
			"<td class='td_opt' id='gno_28400'>Melodrama</td>".
			"<td class='td_opt' id='gno_28600'>Period</td>".
			"<td class='td_opt' id='gno_28800'>Romance</td>".
			"<td class='td_opt' id='gno_28900'>Sports</td>".
			"<td class='td_opt' id='gno_28950'>War</td>".
			"<td class='td_opt' id='gno_28999'>NoSub</td>";
			break;
		case 55000: $s =
			"<td class='td_opt' id='gno_55000'>All</td>".
			"<td class='td_opt' id='gno_55050'>Anth</td>".
			"<td class='td_opt' id='gno_55250'>Creat</td>".
			"<td class='td_opt' id='gno_55300'>Ghost</td>".
			"<td class='td_opt' id='gno_55350'>Eurotrash</td>".
			"<td class='td_opt' id='gno_55400'>Exploitation</td>".
			"<td class='td_opt' id='gno_55450'>Gialli</td>".
			"<td class='td_opt' id='gno_55500'>Gore</td>".
			"<td class='td_opt' id='gno_55550'>Gothic</td>".
			"<td class='td_opt' id='gno_55700'>Poss/Satan</td>".
			"<td class='td_opt' id='gno_55850'>Slash/Surv</td>".
			"<td class='td_opt' id='gno_55900'>Vamp</td>".
			"<td class='td_opt' id='gno_55950'>Zomb/Inf</td>".
			"<td class='td_opt' id='gno_55960'>OtherUnd</td>".
			"<td class='td_opt' id='gno_55999'>NoSub</td>";
			break;
		case 10000: $s =
			"<td class='td_opt' id='gno_10000'>All</td>".
			"<td class='td_opt' id='gno_10100'>Comedy</td>".
			"<td class='td_opt' id='gno_10200'>Crime</td>".
			"<td class='td_opt' id='gno_10300'>Disaster</td>".
			"<td class='td_opt' id='gno_10400'>Epic</td>".
			"<td class='td_opt' id='gno_10500'>Espionage</td>".
			"<td class='td_opt' id='gno_10600'>MartialArts</td>".
			"<td class='td_opt' id='gno_10700'>Military</td>".
			"<td class='td_opt' id='gno_10750'>Samurai</td>".
			"<td class='td_opt' id='gno_10999'>NoSub</td>";
			break;
		case 70000: $s =
			"<td class='td_opt' id='gno_70000'>All</td>".
			"<td class='td_opt' id='gno_70100'>Alien</td>".
			"<td class='td_opt' id='gno_70200'>AltReal</td>".
			"<td class='td_opt' id='gno_70250'>Apoc</td>".
			"<td class='td_opt' id='gno_70300'>CyberPunk</td>".
			"<td class='td_opt' id='gno_70400'>GiantMonst</td>".
			"<td class='td_opt' id='gno_70500'>LostWorlds</td>".
			"<td class='td_opt' id='gno_70550'>Military</td>".
			"<td class='td_opt' id='gno_70600'>OtherWorlds</td>".
			"<td class='td_opt' id='gno_70800'>Space</td>".
			"<td class='td_opt' id='gno_70850'>SpaceHor</td>".
			"<td class='td_opt' id='gno_70870'>Superheroes</td>".
			"<td class='td_opt' id='gno_70900'>Dystopia</td>".
			"<td class='td_opt' id='gno_70999'>NoSub</td>";
			break;
		case 13000: $s =
			"<td class='td_opt' id='gno_13000'>All</td>".
			"<td class='td_opt' id='gno_13100'>Cartoons</td>".
			"<td class='td_opt' id='gno_13300'>Family</td>".
			"<td class='td_opt' id='gno_13600'>Mature</td>".
			"<td class='td_opt' id='gno_13700'>Puppetry/Stop-Motion</td>".
			"<td class='td_opt' id='gno_13800'>Sci-Fi</td>".
			"<td class='td_opt' id='gno_13900'>Superheroes</td>".
			"<td class='td_opt' id='gno_13999'>NoSub</td>";
			break;
		case 16000: $s =
			"<td class='td_opt' id='gno_16000'>All</td>".
			"<td class='td_opt' id='gno_16200'>Action</td>".
			"<td class='td_opt' id='gno_16250'>Comedy</td>".
			"<td class='td_opt' id='gno_16300'>Drama</td>".
			"<td class='td_opt' id='gno_16400'>Fantasy</td>".
			"<td class='td_opt' id='gno_16500'>Horror</td>".
			"<td class='td_opt' id='gno_16600'>MagicalGirls</td>".
			"<td class='td_opt' id='gno_16700'>MartialArts</td>".
			"<td class='td_opt' id='gno_16750'>GiantRobots</td>".
			"<td class='td_opt' id='gno_16800'>CuteGirls</td>".
			"<td class='td_opt' id='gno_16850'>Romance</td>".
			"<td class='td_opt' id='gno_16900'>Sci-Fi</td>".
			"<td class='td_opt' id='gno_16999'>NoSub</td>";
			break;
		case 84000: $s =
			"<td class='td_opt' id='gno_84000'>All</td>".
			"<td class='td_opt' id='gno_84400'>Mystery</td>".
			"<td class='td_opt' id='gno_84700'>Thriller</td>".
			"<td class='td_opt' id='gno_84999'>NoSub</td>";
			break;
		case 24000: $s =
			"<td class='td_opt' id='gno_24000'>All</td>".
			"<td class='td_opt' id='gno_24100'>Biography</td>".
			"<td class='td_opt' id='gno_24200'>Crime</td>".
			"<td class='td_opt' id='gno_24250'>Culture</td>".
			"<td class='td_opt' id='gno_24270'>Entertainment</td>".
			"<td class='td_opt' id='gno_24300'>History</td>".
			"<td class='td_opt' id='gno_24400'>Nature</td>".
			"<td class='td_opt' id='gno_24500'>Propaganda</td>".
			"<td class='td_opt' id='gno_24600'>Religion</td>".
			"<td class='td_opt' id='gno_24700'>Science</td>".
			"<td class='td_opt' id='gno_24750'>Social</td>".
			"<td class='td_opt' id='gno_24800'>Sports</td>".
			"<td class='td_opt' id='gno_24900'>Travel</td>".
			"<td class='td_opt' id='gno_24999'>NoSub</td>";
			break;
		case 91000: $s =
			"<td class='td_opt' id='gno_91000'>All</td>".
			"<td class='td_opt' id='gno_91400'>Epic</td>".
			"<td class='td_opt' id='gno_91700'>SingingCowboy</td>".
			"<td class='td_opt' id='gno_91800'>Spaghetti</td>".
			"<td class='td_opt' id='gno_91999'>NoSub</td>";
			break;
		case 80000: $s =
			"<td class='td_opt' id='gno_80000'>All</td>".
			"<td class='td_opt' id='gno_80100'>Baseball</td>".
			"<td class='td_opt' id='gno_80130'>Basketball</td>".
			"<td class='td_opt' id='gno_80170'>Biking</td>".
			"<td class='td_opt' id='gno_80200'>Fit</td>".
			"<td class='td_opt' id='gno_80250'>Football</td>".
			"<td class='td_opt' id='gno_80300'>Golf</td>".
			"<td class='td_opt' id='gno_80350'>Hockey</td>".
			"<td class='td_opt' id='gno_80400'>Martial</td>".
			"<td class='td_opt' id='gno_80450'>Motor</td>".
			"<td class='td_opt' id='gno_80500'>Olymp</td>".
			"<td class='td_opt' id='gno_80600'>Skate</td>".
			"<td class='td_opt' id='gno_80700'>Skiing</td>".
			"<td class='td_opt' id='gno_80800'>Soccer</td>".
			"<td class='td_opt' id='gno_80850'>Tennis</td>".
			"<td class='td_opt' id='gno_80900'>Wrest</td>".
			"<td class='td_opt' id='gno_80999'>NoSub</td>";
			break;
		case 88000: $s =
			"<td class='td_opt' id='gno_88000'>All</td>".
			"<td class='td_opt' id='gno_88200'>USCivilWar</td>".
			"<td class='td_opt' id='gno_88300'>WWI</td>".
			"<td class='td_opt' id='gno_88400'>WWII</td>".
			"<td class='td_opt' id='gno_88500'>Korea</td>".
			"<td class='td_opt' id='gno_88600'>Vietnam</td>".
			"<td class='td_opt' id='gno_88700'>PostColdWar</td>".
			"<td class='td_opt' id='gno_88900'>Other</td>".
			"<td class='td_opt' id='gno_88999'>NoSub</td>";
			break;
		case 41000: $s =
			"<td class='td_opt' id='gno_41000'>All</td>".
			"<td class='td_opt' id='gno_41100'>Blaxploitation</td>".
			"<td class='td_opt' id='gno_41300'>Nazisploitation</td>".
			"<td class='td_opt' id='gno_41400'>Nunsploitation</td>".
			"<td class='td_opt' id='gno_41500'>PinkuEiga</td>".
			"<td class='td_opt' id='gno_41600'>Sexploitation</td>".
			"<td class='td_opt' id='gno_41700'>Shockumentary</td>".
			"<td class='td_opt' id='gno_41800'>WIP</td>".
			"<td class='td_opt' id='gno_41999'>NoSub</td>";
			break;
		case 59000: $s =
			"<td class='td_opt' id='gno_59000'>All</td>".
			"<td class='td_opt' id='gno_59300'>LiveInConcert</td>".
			"<td class='td_opt' id='gno_59700'>MusicVideos</td>".
			"<td class='td_opt' id='gno_59999'>NoSub</td>";
			break;
		case 36000: $s =
			"<td class='td_opt' id='gno_36000'>All</td>".
			"<td class='td_opt' id='gno_36100'>Hentai</td>".
			"<td class='td_opt' id='gno_36999'>NoSub</td>";
			break;
		case 76000: $s =
			"<td class='td_opt' id='gno_76000'>All</td>".
			"<td class='td_opt' id='gno_76100'>Animation</td>".
			"<td class='td_opt' id='gno_76300'>Horror</td>".
			"<td class='td_opt' id='gno_76500'>Melodrama</td>".
			"<td class='td_opt' id='gno_76700'>Slapstick</td>".
			"<td class='td_opt' id='gno_76800'>Western</td>".
			"<td class='td_opt' id='gno_76999'>NoSub</td>";
			break;
		case 66000: $s =
			"<td class='td_opt' id='gno_66000'>All</td>".
			"<td class='td_opt' id='gno_66100'>Circus</td>".
			"<td class='td_opt' id='gno_66300'>Concerts</td>".
			"<td class='td_opt' id='gno_66500'>Dance</td>".
			"<td class='td_opt' id='gno_66700'>Opera</td>".
			"<td class='td_opt' id='gno_66900'>Theater</td>".
			"<td class='td_opt' id='gno_66999'>NoSub</td>";
			break;
		case 32000: $s =
			"<td class='td_opt' id='gno_32000'>All</td>".
			"<td class='td_opt' id='gno_32200'>Children</td>".
			"<td class='td_opt' id='gno_32700'>School</td>".
			"<td class='td_opt' id='gno_32999'>NoSub</td>";
			break;
		}
		return $s;
	}
*/
}

?>
