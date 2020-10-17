<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CObj.php';

class CObjDvd extends CObj
{
	function propagateChanges($n_obj_id)
	{
		// $this->propagateGenre($n_obj_id);
		// CSql::query_and_free("CALL update_dvd_search_index({$n_obj_id},1)",0,__FILE__,__LINE__);
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
		$this->ms_obj_name				= 'DVD';
		$this->ms_tbl_obj				= 'dvd';
		$this->ms_tbl_obj_				= 'a';
		$this->ms_tbl_direct			= 'dvd_direct_update';
		$this->ms_tbl_hist				= 'dvd_hist';
		$this->ms_tbl_pic				= 'dvd_pic';
		$this->ms_tbl_pic_hist			= 'dvd_pic_hist';
		$this->ms_tbl_submit			= 'dvd_submit';
		$this->ms_tbl_video				= false;
		$this->ms_obj_type				= 'D';
		//--------------------------------------------------
		// dvd
		//--------------------------------------------------
		$this->ms_key					= 'dvd_id';
		$this->ms_created_tm			= 'dvd_created_tm';
		$this->ms_updated_tm			= 'dvd_updated_tm';
		$this->ms_updated_by			= 'dvd_updated_by';
		$this->ms_verified_tm			= 'dvd_verified_tm';
		$this->ms_verified_by			= 'dvd_verified_by';
		$this->ms_gen_by_edit_id		= 'dvd_edit_id';
		$this->ms_pic_status			= 'pic_status';
		$this->ma_attributes			= array(
			// eedt 1 = select, 2 = calc, 4 = select but do not display
			// ehis 1 = select col, 2 = select subquery, 4 = text only (no decode, no text area)
			array('col'=>'dvd_title_tmpl'		,'eedt'=>1,'ehis'=>5,'def'=>"'-'"	),
			array('col'=>'bonus_items'			,'eedt'=>1,'ehis'=>5,'def'=>"'-'"	),
			array('col'=>'dvd_notes'			,'eedt'=>1,'ehis'=>5,'def'=>"'-'"	),
			array('col'=>'dvd_title'			,'eedt'=>1,'ehis'=>5,'def'=>"'-'"	),
			array('col'=>'dvd_title_nocase'		,'eedt'=>3,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'dvd_title_sort'		,'eedt'=>3,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'url'					,'eedt'=>2,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'film_rel_year'		,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'film_rel_year_end'	,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'director'				,'eedt'=>0,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'director_nocase'		,'eedt'=>2,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'publisher'			,'eedt'=>0,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'publisher_nocase'		,'eedt'=>2,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'orig_language'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'language_add'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'country'				,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'country_block'		,'eedt'=>2,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'film_rating'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'region_mask'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'genre'				,'eedt'=>1,'ehis'=>1,'def'=>"99999"	),
			array('col'=>'media_type'			,'eedt'=>1,'ehis'=>1,'def'=>"'D'"	),
			array('col'=>'num_titles'			,'eedt'=>1,'ehis'=>1,'def'=>"1"		),
			array('col'=>'num_disks'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'source'				,'eedt'=>1,'ehis'=>1,'def'=>"'A'"	),
			array('col'=>'rel_status'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'film_rel_dd'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'dvd_rel_dd'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'dvd_oop_dd'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'imdb_id'				,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'youtube_id'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'list_price'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'sku'					,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'upc'					,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'asin'					,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'amz_country'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'best_price'			,'eedt'=>3,'ehis'=>0,'def'=>"0"		),
			array('col'=>'amz_rank'				,'eedt'=>2,'ehis'=>0,'def'=>"0"		),
			array('col'=>'collection_rank'		,'eedt'=>2,'ehis'=>0,'def'=>"0"		));
		$this->ma_edition_attributes	= array(
			// eedt 1 = select, 2 = calc, 4 = select but do not display
			// ehis 1 = select col, 2 = select subquery, 4 = text only (no decode, no text area)
			array('col'=>'alias_of'				,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'prim_title_seq'		,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'editon_notes'			,'eedt'=>1,'ehis'=>1,'def'=>"null"	),
			array('col'=>'language_dubbed'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'language_opt'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'subtitles_fixed'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'subtitles_opt'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'sound_system'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'surround_mode'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'video_mode'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'aspect_ratio'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'frame_conversion'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'run_time'				,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'sort_order'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		));
		//--------------------------------------------------
		// dvd_hist
		//--------------------------------------------------
		$this->ms_id_merged				= 'dvd_id_merged';
		//--------------------------------------------------
		//--------------------------------------------------
		// dvd_submit
		//--------------------------------------------------
		$this->ms_submit_id				= 'edit_id';
		//--------------------------------------------------
		// dvd_direct_update
		//--------------------------------------------------
		$this->ms_direct_seq_num		= 'seq_num';
		$this->ms_direct_edit_obj		= 'edit_title';
		$this->ms_direct_new_obj		= 'new_title';

		//--------------------------------------------------
		// pub
		//--------------------------------------------------
		$this->ma_pub_attributes		= array(
			// eedt 1 = select, 2 = calc
			// ehis 1 = select col, 2 = select subquery
			array('col'=>'sort_order'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		));

		//--------------------------------------------------
		// dir
		//--------------------------------------------------
		$this->ma_dir_attributes		= array(
			// eedt 1 = select, 2 = calc, 4 = select but do not display
			// ehis 1 = select col, 2 = select subquery, 4 = text only (no decode, no text area)
			array('col'=>'sort_order'			,'eedt'=>1,'ehis'=>1,'def'=>"0"		));
	}
}

/*
	function propagateGenre($n_obj_id, $s_user_id, $n_genre, $n_imdb)
	{
		if ( $n_genre > 0 && $n_imdb > 0 )
		{
			$n_imdb   = sprintf('%07d', intval($n_imdb));
			$s_dvd_id = CSql::query_and_fetch1("SELECT group_concat(dvd_id) dvd_ids FROM dvd WHERE imdb_id like '{$n_imdb}%' and genre != {$n_genre}",0,__FILE__,__LINE__);
			$s_just   = "Genre propagation from {$n_obj_id} affected " . str_replace(',', ', ', $s_dvd_id) . ".";
			if ( strlen($s_just) > 200 ) $s_just = substr($s_just, 0, 200-3) . '...';
			if ( $s_dvd_id )
			{
				CSql::query_and_free("INSERT INTO dvd_hist (".
							"dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
							"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
							"dvd_updated_tm, dvd_updated_by, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id) ".
						 "SELECT dvd_id, version_id, dvd_title, film_rel_year, director, publisher, orig_language, country, region_mask, genre, media_type, num_titles, ".
							"num_disks, source, rel_status, film_rel_dd, dvd_rel_dd, dvd_oop_dd, imdb_id, pic_status, pic_name, list_price, sku, upc, asin, amz_country, dvd_created_tm, ".
							"dvd_updated_tm, dvd_updated_by, last_justify, dvd_verified_tm, dvd_verified_by, verified_version, dvd_edit_id ".
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
*/

?>
