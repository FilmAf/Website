<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function insert($dvd_id, $s, $s_code, $s_sep, $b_words, $s_ignore)
{
    if ( $s == '' || $s == $s_ignore ) return '';

    if ( $s_sep != '' )
	$x = explode($s_sep, $s);
    else
	$x = array($s);

    for ( $i = 0 ; $i < count($x) ; $i++ )
    {
	if ( ($s = trim(substr($x[$i],0,200))) )
	{
	    CSql::query_and_free("INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES($dvd_id, '{$s_code}', '{$s} /', 'Y')", CSql_IGNORE_ERROR,__FILE__,__LINE__);

	    if ( $b_words )
		while ( ($j = strpos($s, ' ')) && ($s = trim(substr($s,$j))) )
		    CSql::query_and_free("INSERT INTO search_all_1 (dvd_id, obj_type, nocase, whole) VALUES($dvd_id, '{$s_code}', '{$s} /', 'N')", CSql_IGNORE_ERROR,__FILE__,__LINE__);
	}
    }
}


function propagateGenre($n_dvd_id, $s_user_id, $n_genre, $n_imdb)
{
    if ( $n_genre > 0 && $n_imdb > 0 )
    {
	$n_imdb   = sprintf('%08d', intval($n_imdb));
	$s_dvd_id = CSql::query_and_fetch1("SELECT group_concat(dvd_id) dvd_ids FROM dvd WHERE imdb_id like '{$n_imdb}%' and genre != {$n_genre}",0,__FILE__,__LINE__);
	$s_just   = "Genre propagation from {$n_dvd_id} affected " . str_replace(',', ', ', $s_dvd_id) . ".";
	if ( strlen($s_just) > 200 ) $s_just = substr($s_just, 0, 200-3) . '...';
	if ( $s_dvd_id )
	{
	    CSql::query_and_free("INSERT INTO dvd_hist (".
					"dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
					"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
					"dvd_updated_tm, dvd_updated_by, dvd_id_merged, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id) ".
				 "SELECT dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
					"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
					"dvd_updated_tm, dvd_updated_by, dvd_id_merged, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id ".
				   "FROM dvd a ".
				  "WHERE dvd_id in ({$s_dvd_id}) ".
				    "and not exists (SELECT 1 FROM dvd_hist b WHERE a.dvd_id = b.dvd_id and a.version_id = b.version_id)",0,__FILE__,__LINE__);

	    CSql::query_and_free("UPDATE dvd ".
				    "SET genre = {$n_genre}, ".
				        "last_justify = '{$s_just}', ".
					"version_id = version_id + 1, ".
					"dvd_updated_tm = now(), ".
					"dvd_updated_by = '{$s_user_id}', ".
					"dvd_verified_tm = now(), ".
					"dvd_verified_by = '{$s_user_id}', ".
					"verified_version = version_id ".		// strange behavior!!! should really be "version + 1"
				  "WHERE dvd_id in ({$s_dvd_id})",0,__FILE__,__LINE__);
	    return $s_dvd_id;
	}
    }
    return '';
}

function snapHistory($n_dvd_id)
{
    return  CSql::query_and_free("INSERT INTO dvd_hist (".
					"dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
					"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
					"dvd_updated_tm, dvd_updated_by, dvd_id_merged, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id) ".
				 "SELECT dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
					"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
					"dvd_updated_tm, dvd_updated_by, dvd_id_merged, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id ".
				   "FROM dvd a ".
				  "WHERE dvd_id = {$n_dvd_id} ".
				    "and not exists (SELECT 1 FROM dvd_hist b WHERE a.dvd_id = b.dvd_id and a.version_id = b.version_id)",0,__FILE__,__LINE__);
}

function snapHistoryVersion($n_dvd_id, $n_version_id)
{
    return  CSql::query_and_free("INSERT INTO dvd_hist (".
					"dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
					"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
					"dvd_updated_tm, dvd_updated_by, dvd_id_merged, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id) ".
				 "SELECT dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
					"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
					"dvd_updated_tm, dvd_updated_by, dvd_id_merged, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id ".
				   "FROM dvd a ".
				  "WHERE dvd_id = {$n_dvd_id} ".
				    "and version_id = {$n_version_id} ".
				    "and not exists (SELECT 1 FROM dvd_hist b WHERE a.dvd_id = b.dvd_id and a.version_id = b.version_id)",0,__FILE__,__LINE__);
}

function countDifferentGenres($n_genre, $n_imdb)
{
    if ( $n_genre > 0 && $n_imdb > 0 )
    {
	$s_imdb = sprintf('%08d', intval($n_imdb));
	return CSql::query_and_fetch1("SELECT count(*) FROM dvd WHERE imdb_id like '%{$s_imdb}%' and genre <> {$n_genre}",0,__FILE__,__LINE__);
    }
}

function urlDifferentGenres($n_genre, $n_imdb, $s_base)
{
    if ( $n_genre > 0 && $n_imdb > 0 )
    {
	$a_field = '-:10000:action:10100:action-comedy:10200:action-crime:10300:action-disaster:10400:action-epic:10500:action-espionage:10600:action-martialarts:10700:action-military:10750:action-samurai:10999:action-nosub:13000:animation:13100:animation-cartoons:13300:animation-family:13600:animation-mature:13700:animation-puppetrystopmotion:13800:animation-scifi:13900:animation-superheroes:13999:animation-nosub:16000:anime:16200:anime-action:16250:anime-comedy:16300:anime-drama:16400:anime-fantasy:16500:anime-horror:16600:anime-mahoushoujo:16700:anime-martialarts:16750:anime-mecha:16800:anime-moe:16850:anime-romance:16900:anime-scifi:16999:anime-nosub:20000:comedy:20100:comedy-dark:20200:comedy-farce:20300:comedy-horror:20400:comedy-romantic:20600:comedy-satire:20650:comedy-scifi:20700:comedy-screwball:20750:comedy-sitcom:20800:comedy-sketchesstandup:20850:comedy-slapstick:20900:comedy-teen:20999:comedy-nosub:24000:documentary:24100:documentary-biography:24200:documentary-crime:24250:documentary-culture:24270:documentary-entertainment:24300:documentary-history:24400:documentary-nature:24500:documentary-propaganda:24600:documentary-religion:24700:documentary-science:24750:documentary-social:24800:documentary-sports:24900:documentary-travel:24999:documentary-nosub:28000:drama:28100:drama-courtroom:28150:drama-crime:28200:drama-docudrama:28400:drama-melodrama:28600:drama-period:28800:drama-romance:28900:drama-sports:28950:drama-war:28999:drama-nosub:32000:educational:32200:educational-children:32700:educational-school:32999:educational-nosub:36000:erotica:36100:erotica-hentai:36999:erotica-nosub:39999:experimental:41000:exploitation:41100:exploitation-blaxploitation:41300:exploitation-nazisploitation:41400:exploitation-nunsploitation:41500:exploitation-pinkueiga:41600:exploitation-sexploitation:41700:exploitation-shockumentary:41800:exploitation-wip:41999:exploitation-nosub:43999:fantasy:47999:filmnoir:'.
	'55000:horror:55050:horror-anthology:55250:horror-creatureanimal:55300:horror-espghosts:55350:horror-eurotrash:55400:horror-exploitation:55450:horror-gialli:55500:horror-goreshock:55550:horror-gothic:55700:horror-possessionsatan:55800:horror-shockumentary:55850:horror-slashersurvival:55900:horror-vampires:55950:horror-zombiesinfected:55960:horror-otherundead:55999:horror-nosub:59000:music:59300:music-liveinconcert:59700:music-musicvideos:59999:music-nosub:62999:musical:66000:performing:66100:performing-circus:66300:performing-concerts:66500:performing-dance:66700:performing-operas:66900:performing-theater:66999:performing-nosub:70000:scifi:70100:scifi-alien:70200:scifi-alternatereality:70250:scifi-apocalyptic:70300:scifi-cyberpunk:70400:scifi-kaiju:70500:scifi-lostworlds:70550:scifi-military:70600:scifi-otherworlds:70800:scifi-space:70850:scifi-spacehorror:70870:scifi-superheroes:70900:scifi-utopiadystopia:70999:scifi-nosub:73999:short:76000:silent:76100:silent-animation:76300:silent-horror:76500:silent-melodrama:76700:silent-slapstick:76800:silent-western:76999:silent-nosub:80000:sports:80100:sports-baseball:80130:sports-basketball:80170:sports-biking:80200:sports-fitness:80250:sports-football:80300:sports-golf:80350:sports-hockey:80400:sports-martialarts:80450:sports-motorsports:80500:sports-olympics:80600:sports-skateboard:80700:sports-skiing:80800:sports-soccer:80850:sports-tennis:80900:sports-wrestling:80999:sports-nosub:84000:suspense:84400:suspense-mystery:84700:suspense-thriller:84999:suspense-nosub:88000:war:88200:war-uscivilwar:88300:war-wwi:88400:war-wwii:88500:war-korea:88600:war-vietnam:88700:war-postcoldwar:88900:war-other:88999:war-nosub:91000:western:91400:western-epic:91700:western-singingcowboy:91800:western-spaghetti:91999:western-nosub:95999:dvdaudio:98000:other:98200:other-digitalcomicbooks:98250:other-gameshows:98300:other-games:98999:other-nosub:99999:unspecifiedgenre:';
	$n_pos = strpos($a_field, ':'.$n_genre.':');
	if ( $n_pos > 0 )
	{
	    $n_pas   = strpos($a_field, ':', $n_pos + 7);
	    $s_genre = substr($a_field, $n_pos + 7, $n_pas - $n_pos - 7);
	    $s_imdb  = sprintf('%08d', intval($n_imdb));
	    return "{$s_base}/search.html?imdb={$s_imdb}&genre=.ne.{$s_genre}&init_form=str0_imdb_{$s_imdb}*str1_0_genre_%3C%3E+{$s_genre}*mode_more";
	}
    }
    return '';
}

function getAffectedMessage($n_dvd_id, $s_prop_ids, $n_genre, $s_imdb, $s_base)
{
    $n_dvd_id   = sprintf('%07d', $n_dvd_id);
    $s_affected = "Changes saved as dvd_id <a href='/search.html?has={$n_dvd_id}&init_form=str0_has_{$n_dvd_id}&pm=one' target='filmaf'>{$n_dvd_id}</a>.";

    if ( $n_genre > 0 && $s_imdb > 0 )
    {
	if ( $s_prop_ids )
	{
	    $n_pos = strpos($s_prop_ids, ',');
	    if ( $n_pos > 0 && $n_pos < 7 )
		$s_prop_ids = substr('0000000', $n_pos - 7) . $s_prop_ids;

	    $s_show   = str_replace(',', ', ', $s_prop_ids);
	    $s_link   = str_replace(',', '|' , $s_prop_ids);
	    $s_plural = strpos($s_link, '|') ? "&#39;s" : '';
	    $s_affected .= "<br />Genre propagation updated dvd_id{$s_plural} <a href='/search.html?has={$s_link}&init_form=str0_has_{$s_link}' target='filmaf'>{$s_show}</a>.";
	}
	else
	{
	    $s_affected .= "<br />Genre propagation changed no additional entries.";
	}
	$n_diff = countDifferentGenres($n_genre, $s_imdb);
	if ( $n_diff )
	{
	    $s_affected .= "<br /><a href='". urlDifferentGenres($n_genre, $s_imdb, $s_base). "' target='filmaf'>{$n_diff}</a> listings have this imdb_id but a different genre (not first imdb_id).";
	}
	else
	{
	    $s_affected .= "<br />No listings have this imdb_id with a different genre.";
	}
    }

    return $s_affected;
}

?>
