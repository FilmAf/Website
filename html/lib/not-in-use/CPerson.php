<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CObj.php';

class CObjPerson extends CObj
{
	function propagateChanges($n_obj_id)
	{
		// If the person name changes it needs to be updated in all Person (father, mother, alias), Film and DVD listings that contain it
	}

	function snapHistory($n_obj_id, $n_version_id)
	{
		return  CSql::query_and_free(
		 "INSERT INTO person_hist (".
				"person_id, version_id, alias_of, surname, given_name, surname_first_ind, father_id, mother_id, country_birth, state_birth, city_birth, ".
				"date_of_birth, date_of_death, official_site, wikipedia, imdb_id, youtube_id, pic_name, mod_flags, person_created_tm, person_updated_tm, ".
				"person_updated_by, last_justify, person_verified_tm, person_verified_by, verified_version, person_edit_id) ".
		 "SELECT person_id, version_id, alias_of, surname, given_name, surname_first_ind, father_id, mother_id, country_birth, state_birth, city_birth, ".
				"date_of_birth, date_of_death, official_site, wikipedia, imdb_id, youtube_id, pic_name, mod_flags, person_created_tm, person_updated_tm, ".
				"person_updated_by, last_justify, person_verified_tm, person_verified_by, verified_version, person_edit_id ".
		   "FROM person a ".
		  "WHERE person_id = {$n_obj_id} ". ($n_version_id >= 0 ? "and version_id = {$n_version_id} " : '').
			"and not exists (SELECT 1 FROM person_hist b WHERE a.person_id = b.person_id and a.version_id = b.version_id)",0,__FILE__,__LINE__);
	}

	function validate($b_new, $b_real_table, $bmod)
	{
		if ( $b_real_table )
		{
			$n_obj_id		= dvdaf3_getvalue('obj_id'				,DVDAF3_POST|DVDAF3_INT);
			$b_has_alias	= dvdaf3_getvalue('has_alias'			,DVDAF3_POST|DVDAF3_BOOLEAN); // takes care of 'Y' and 'on'
			$s_n_surname	= dvdaf3_getvalue('n_c_surname'			,DVDAF3_POST);
			$s_o_surname	= dvdaf3_getvalue('o_c_surname'			,DVDAF3_POST);
			$s_n_given_name	= dvdaf3_getvalue('n_c_given_name'		,DVDAF3_POST);
			$s_o_given_name	= dvdaf3_getvalue('o_c_given_name'		,DVDAF3_POST);
			$b_n_sfirst		= dvdaf3_getvalue('n_c_surname_first_ind',DVDAF3_POST|DVDAF3_BOOLEAN); // takes care of 'Y' and 'on'
			$b_o_sfirst		= dvdaf3_getvalue('o_c_surname_first_ind',DVDAF3_POST|DVDAF3_BOOLEAN);
			$s_n_name		= $this->makeName($s_n_surname, $s_n_given_name, $b_n_sfirst);
			$s_o_name		= $this->makeName($s_o_surname, $s_o_given_name, $b_o_sfirst);
			$s_n_name_url	= str_replace(',','',$this->makeName($s_n_surname, $s_n_given_name, false));
			$s_o_name_url	= str_replace(',','',$this->makeName($s_o_surname, $s_o_given_name, false));
			$_POST['n_c_name_nocase']		= '/ '.dvdaf3_translatestring($s_n_name, DVDAF3_SEARCH).' /';
			$_POST['o_c_name_nocase']		= '/ '.dvdaf3_translatestring($s_o_name, DVDAF3_SEARCH).' /';
			$_POST['n_c_url']				= $s_n_name_url !== '' ? ("/plp/{$n_obj_id}-". dvdaf3_translatestring($s_n_name_url, DVDAF3_URL)) : '';
			$_POST['o_c_url']				= $s_o_name_url !== '' ? ("/plp/{$n_obj_id}-". dvdaf3_translatestring($s_o_name_url, DVDAF3_URL)) : '';
			$_POST['n_c_surname_first_ind']	= $b_n_sfirst ? 'Y' : 'N';
			/*
			   alias_of_name
			   name_nocase
			   url
			   father_name
			   mother_name
			*/
		}
	}

	function makeName($s_surname, $s_given_name, $b_sfirst)
	{
		if ( $s_surname !== '' )
			if ( $s_given_name !== '' )
				return $b_sfirst ? "{$s_surname}, {$s_given_name}" : "{$s_given_name} {$s_surname}";
			else
				return $s_surname;
		else
			return $s_given_name;
	}

	function __construct()
	{
		parent::__construct();
		$this->ms_obj_name				= 'person';
		$this->ms_tbl_obj				= 'person';
		$this->ms_tbl_obj_				= 'c';
		$this->ms_tbl_direct			= 'person_direct_update';
		$this->ms_tbl_hist				= 'person_hist';
		$this->ms_tbl_pic				= 'person_pic';
		$this->ms_tbl_pic_hist			= 'person_pic_hist';
		
		$this->ms_tbl_submit			= 'person_submit';
		$this->ms_tbl_video				= 'person_video';
		$this->ms_obj_type				= 'C';
		//--------------------------------------------------
		// person
		//--------------------------------------------------
		$this->ms_key					= 'person_id';
		$this->ms_created_tm			= 'person_created_tm';
		$this->ms_updated_tm			= 'person_updated_tm';
		$this->ms_updated_by			= 'person_updated_by';
		$this->ms_verified_tm			= 'person_verified_tm';
		$this->ms_verified_by			= 'person_verified_by';
		$this->ms_gen_by_edit_id		= 'person_edit_id';
		$this->ma_attributes			= array(
			// eedt 1 = select, 2 = calc, 4 = select but do not display
			// ehis 1 = select col, 2 = select subquery, 4 = text only (no decode, no text area)
			array('col'=>'alias_of_name'		,'eedt'=>3,'ehis'=>2,'def'=>"'-'"	,'actu' => 'alias_of_name',
																					 'hist' => "ifnull((SELECT x.display_name FROM person x WHERE x.person_id = h.alias_of),'-')",
																					 'subm' => "ifnull((SELECT x.display_name FROM person x WHERE x.person_id = b.alias_of),'-')",
																					 'parh' => 'alias_of'),
			array('col'=>'alias_of'				,'eedt'=>1,'ehis'=>4,'def'=>"0"		),
			array('col'=>'surname'				,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'given_name'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'surname_first_ind'	,'eedt'=>1,'ehis'=>1,'def'=>"'Y'"	),
			array('col'=>'display_name'			,'eedt'=>2,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'name_nocase'			,'eedt'=>2,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'url'					,'eedt'=>2,'ehis'=>0,'def'=>"'-'"	),
			array('col'=>'father_name'			,'eedt'=>3,'ehis'=>2,'def'=>"'-'"	,'actu' => 'father_name',
																					 'hist' => "ifnull((SELECT x.display_name FROM person x WHERE x.person_id = h.father_id),'-')",
																					 'subm' => "ifnull((SELECT x.display_name FROM person x WHERE x.person_id = b.father_id),'-')",
																					 'parh' => 'father_id'),
			array('col'=>'father_id'			,'eedt'=>1,'ehis'=>4,'def'=>"0"		),
			array('col'=>'mother_name'			,'eedt'=>3,'ehis'=>2,'def'=>"'-'"	,'actu' => 'mother_name',
																					 'hist' => "ifnull((SELECT x.display_name FROM person x WHERE x.person_id = h.mother_id),'-')",
																					 'subm' => "ifnull((SELECT x.display_name FROM person x WHERE x.person_id = b.mother_id),'-')",
																					 'parh' => 'mother_id'),
			array('col'=>'mother_id'			,'eedt'=>1,'ehis'=>4,'def'=>"0"		),
			array('col'=>'country_birth'		,'eedt'=>1,'ehis'=>1,'def'=>"0"		),
			array('col'=>'state_birth'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'city_birth'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'date_of_birth'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'date_of_death'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'official_site'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'wikipedia'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'imdb_id'				,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	),
			array('col'=>'youtube_id'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"	));
		//--------------------------------------------------
		// person_hist
		//--------------------------------------------------
		$this->ms_id_merged				= 'person_id_merged';
		//--------------------------------------------------
		//--------------------------------------------------
		// person_submit
		//--------------------------------------------------
		$this->ms_submit_id				= 'person_edit_id';
		//--------------------------------------------------
		// person_direct_update
		//--------------------------------------------------
		$this->ms_direct_seq_num		= 'person_direct_seq';
		$this->ms_direct_edit_obj		= 'edit_person';
		$this->ms_direct_new_obj		= 'new_person';
	}
}

?>
