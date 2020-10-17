<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CRegenSqlCache
{
	function CollectionRank()
	{
		$ss = "TRUNCATE TABLE active_dvd";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "INSERT INTO active_dvd (dvd_id, collection_rank) ".
			  "SELECT b.dvd_id, count(*) ".
				"FROM (SELECT user_id ".
						"FROM dvdaf_user_2 ".
					   "WHERE last_post_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
						  "or last_link_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
						  "or last_coll_tm >= DATE_ADD(now(), INTERVAL -3 MONTH) ".
						  "or last_submit_tm >= DATE_ADD(now(), INTERVAL -3 MONTH)) c, ".
					 "my_dvd b ".
			   "WHERE c.user_id = b.user_id ".
			   "GROUP BY b.dvd_id";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "UPDATE dvd ".
				 "SET collection_rank = 0 ".
			   "WHERE collection_rank != 0 ".
				 "and not exists (SELECT count(*) FROM active_dvd WHERE active_dvd.dvd_id = dvd.dvd_id)";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "UPDATE dvd, active_dvd ".
				 "SET dvd.collection_rank = active_dvd.collection_rank ".
			   "WHERE active_dvd.dvd_id = dvd.dvd_id";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);
	}

	function ActiveCache()
	{
		$ss = "DROP TABLE IF EXISTS active_cache_tmp";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "TRUNCATE TABLE active_cache_new";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		// sub query is needed to get around conversion warnings treated as errors
		$ss = "INSERT INTO active_cache_new ".
					 "(dvd_id, criterion, spine, media_type, region, dvd_rel_dd, genre, ".
					  "subgenre, amz_rank, collection_rank, best_price, good_listing) ".
			  "SELECT * ".
				"FROM (SELECT a.dvd_id, ".
							 "IF(INSTR(a.publisher_nocase, 'criterion collection') > 0, ".
							 "IF(INSTR(a.dvd_title_nocase, 'eclipse series') > 0, 'E', 'C'), 'N') criterion, ".
							 "CONVERT(REPLACE(SUBSTR(a.dvd_title,INSTR(a.dvd_title,'Spine #')),'Spine #',''),SIGNED INT) + ".
							 "CONVERT(REPLACE(SUBSTR(a.dvd_title,INSTR(a.dvd_title,'Eclipse Series ')),'Eclipse Series ',''),SIGNED INT) spine, ".
							 "IF(a.media_type in ('D','V'),'D',".
							 "IF(a.media_type in ('B','2','3','R'),'B',".
							 "IF(a.media_type in ('F','S','L','E','N'),'F','O'))) media_type, ".
							 "IF(INSTR(a.country, 'us') > 0, 'us', IF(INSTR(a.country, 'uk') > 0, 'uk', 'ot')) region, ".
							 "a.dvd_rel_dd, ".
							 "CONVERT(floor((a.genre + 0.5) / 1000) * 1000,SIGNED INT) genre, ".
							 "a.genre subgenre, ".
							 "a.amz_rank, ".
							 "a.collection_rank, ".
							 "a.best_price, ".
							 "IF(a.source = 'A', IF(a.pic_name != '-', 'Y', '-'), 'N') good_listing ".
						"FROM dvd a) b";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "RENAME TABLE active_cache TO active_cache_tmp, ".
						   "active_cache_new TO active_cache, ".
						   "active_cache_tmp TO active_cache_new";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "TRUNCATE TABLE active_cache_new";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "OPTIMIZE TABLE active_cache";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);
	}

	function ActiveTopCache()
	{
		$ss = "DROP TABLE IF EXISTS active_top_cache_tmp";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "TRUNCATE TABLE active_top_cache_new";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "INSERT INTO active_top_cache_new (pane, dvd_id, best_price) ".
			  "SELECT 'D', a.dvd_id, p.price_00 ".
				"FROM dvd a ".
				"JOIN stats_dvd_country sc ON a.dvd_id = sc.dvd_id ".
				"JOIN price p ON p.upc = a.upc ".
			   "WHERE a.pic_name != '-' and a.source = 'A' and a.asin != '-' and p.price_00 > 0 and a.media_type in ('D','V') ".
				 "and sc.country = 'us' ".
			   "ORDER BY a.amz_rank LIMIT 50";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "INSERT INTO active_top_cache_new (pane, dvd_id, best_price) ".
			  "SELECT 'B', a.dvd_id, p.price_00 ".
				"FROM dvd a ".
				"JOIN stats_dvd_country sc ON a.dvd_id = sc.dvd_id ".
				"JOIN price p ON p.upc = a.upc ".
			   "WHERE a.pic_name != '-' and a.source = 'A' and a.asin != '-' and p.price_00 > 0 and a.media_type in ('B','2','3','R') ".
				 "and sc.country = 'us' ".
			   "ORDER BY a.amz_rank LIMIT 50";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "RENAME TABLE active_top_cache TO active_top_cache_tmp, ".
						   "active_top_cache_new TO active_top_cache, ".
						   "active_top_cache_tmp TO active_top_cache_new";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "TRUNCATE TABLE active_top_cache_new";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);

		$ss = "OPTIMIZE TABLE active_top_cache_new";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);
	}
}

?>
