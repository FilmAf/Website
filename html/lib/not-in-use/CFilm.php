<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CObj.php';

class CObjFilm extends CObj
{
	function propagateChanges($n_obj_id)
	{
		// 
	}

	function snapHistory($n_obj_id, $n_version_id)
	{
		return  CSql::query_and_free(
		 "INSERT INTO dvd_hist (".
				"dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
				"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
				"dvd_updated_tm, dvd_updated_by, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id) ".
		 "SELECT dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
				"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
				"dvd_updated_tm, dvd_updated_by, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id ".
		   "FROM dvd a ".
		  "WHERE dvd_id = {$n_obj_id} ". ($n_version_id >= 0 ? "and version_id = {$n_version_id} " : '').
				"and not exists (SELECT 1 FROM dvd_hist b WHERE a.dvd_id = b.dvd_id and a.version_id = b.version_id)",0,__FILE__,__LINE__);
	}

	function validate($b_new, $b_real_table, $bmod) {}

	function __construct()
	{
		parent::__construct();
		$this->ms_obj_name				= 'film';
		$this->ms_tbl_obj				= 'film';
		$this->ms_tbl_obj_				= 'f';
		$this->ms_tbl_direct			= 'film_direct_update';
		$this->ms_tbl_hist				= 'film_hist';
		$this->ms_tbl_pic				= 'film_pic';
		$this->ms_tbl_pic_hist			= 'film_pic_hist';
		$this->ms_tbl_submit			= 'film_submit';
		$this->ms_tbl_video				= 'film_video';
		$this->ms_obj_type				= 'F';
		//--------------------------------------------------
		// film
		//--------------------------------------------------
		$this->ms_key					= 'film_id';
		$this->ms_created_tm			= 'film_created_tm';
		$this->ms_updated_tm			= 'film_updated_tm';
		$this->ms_updated_by			= 'film_updated_by';
		$this->ms_verified_tm			= 'film_verified_tm';
		$this->ms_verified_by			= 'film_verified_by';
		$this->ms_gen_by_edit_id		= 'film_edit_id';
		$this->ma_attributes			= array(
			// eedt 1 = select, 2 = calc, 4 = select but do not display
			// ehis 1 = select col, 2 = select subquery, 4 = text only (no decode, no text area)
			array('col'=>'feature_cd'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'film_title'			,'eedt'=>1,'ehis'=>1,'def'=>"''"	),
			array('col'=>'url'					,'eedt'=>2,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'film_rel_year'		,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'film_rel_year_end'	,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'episode_of'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'episode_of_title'		,'eedt'=>3,'ehis'=>2,'def'=>"'-'"	,'actu' => 'episode_of_title',
																					 'hist' => "ifnull((SELECT x.episode_of_title FROM film x WHERE x.film_id = h.episode_of),'-')",
																					 'subm' => "ifnull((SELECT x.episode_of_title FROM film x WHERE x.film_id = b.episode_of),'-')",
																					 'parh' => 'episode_of'),
			array('col'=>'director'				,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'cast'					,'eedt'=>1,'ehis'=>1,'def'=>"null"	),
			array('col'=>'orig_language'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'language_add'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'aspect_ratio_ori'		,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'run_time_ori'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'genre'				,'eedt'=>1,'ehis'=>1,'def'=>"99999"	),
			array('col'=>'film_rel_dd'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'official_site'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'wikipedia'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'imdb_id'				,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'youtube_id'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	));
		//--------------------------------------------------
		// film_hist
		//--------------------------------------------------
		$this->ms_id_merged				= 'film_id_merged';
		//--------------------------------------------------
		//--------------------------------------------------
		// film_submit
		//--------------------------------------------------
		$this->ms_submit_id				= 'film_edit_id';
		//--------------------------------------------------
		// film_direct_update
		//--------------------------------------------------
		$this->ms_direct_seq_num		= 'film_direct_seq';
		$this->ms_direct_edit_obj		= 'edit_film';
		$this->ms_direct_new_obj		= 'new_film';

		//--------------------------------------------------
		// role
		//--------------------------------------------------
//		$this->ma_role_tbl				= 'role';
//		$this->ma_role_tbl_hist			= 'role_hist';
//		$this->ma_role_key_base			= array(
//			array('col'=>'film_id'				),
//			array('col'=>'person_id'			),
//			array('col'=>'person_role'			));
//		$this->ms_role_key				= 'seq_num';
		$this->ma_role_attributes		= array(
			// eedt 1 = select, 2 = calc, 4 = select but do not display
			// ehis 1 = select col, 2 = select subquery, 4 = text only (no decode, no text area)
			array('col'=>'character_name'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'role_type'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'credited_ind'			,'eedt'=>1,'ehis'=>1,'def'=>"'Y'"	),
			array('col'=>'role_cmts'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'pic_name'				,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'sort_order'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		));

		//--------------------------------------------------
		// title
		//--------------------------------------------------
//		$this->ma_title_tbl				= 'title';
//		$this->ma_title_tbl_hist		= 'title_hist';
//		$this->ma_title_key_base		= array(
//			array('col'=>'film_id'				));
//		$this->ms_title_key				= 'title_seq';
		$this->ma_title_attributes		= array(
			// eedt 1 = select, 2 = calc, 4 = select but do not display
			// ehis 1 = select col, 2 = select subquery, 4 = text only (no decode, no text area)
			array('col'=>'title'				,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'title_sort'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'title_search'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'search_article_ind'	,'eedt'=>1,'ehis'=>1,'def'=>"'Y'"	),
			array('col'=>'sort_order'			,'eedt'=>1,'ehis'=>1,'def'=>"0"	),
			array('col'=>'used_in_edition_ind'	,'eedt'=>1,'ehis'=>1,'def'=>"'N'"	));
	}
}

?>
