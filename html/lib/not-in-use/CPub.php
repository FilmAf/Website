<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CObj.php';

class CObjPub extends CObj
{
	function propagateChanges($n_obj_id)
	{
		// If the publisher name changes it needs to be updated in all DVD listings that contain it
	}

	function snapHistory($n_obj_id, $n_version_id)
	{
		return  CSql::query_and_free(
		 "INSERT INTO pub_hist (".
				"pub_id, version_id, pub_name, official_site, wikipedia, mod_flags, pub_created_tm, pub_updated_tm, ".
				"pub_updated_by, last_justify, pub_verified_tm, pub_verified_by, verified_version, pub_edit_id) ".
		 "SELECT pub_id, version_id, pub_name, official_site, wikipedia, mod_flags, pub_created_tm, pub_updated_tm, ".
				"pub_updated_by, last_justify, pub_verified_tm, pub_verified_by, verified_version, pub_edit_id ".
		   "FROM pub a ".
		  "WHERE pub_id = {$n_obj_id} ". ($n_version_id >= 0 ? "and version_id = {$n_version_id} " : '').
			"and not exists (SELECT 1 FROM pub_hist b WHERE a.pub_id = b.pub_id and a.version_id = b.version_id)",0,__FILE__,__LINE__);
	}

	function validate($b_new, $b_real_table, $bmod)
	{
		$n_obj_id	= dvdaf3_getvalue('obj_id'		,DVDAF3_POST|DVDAF3_INT);
		$s_n_name	= dvdaf3_getvalue('n_u_pub_name'	,DVDAF3_POST);
		$s_o_name	= dvdaf3_getvalue('o_u_pub_name'	,DVDAF3_POST);
		$s_n_nocase	= dvdaf3_translatestring($s_n_name, DVDAF3_SEARCH);

		if ( $s_n_nocase )
			if ( ($s_url = intval(CSql::query_and_fetch1("SELECT url FROM pub WHERE pub_name_nocase = '{$s_n_nocase}'".($b_new ? '' : " and pub_id <> {$n_obj_id}"), 0,__FILE__,__LINE__))) )
				return "The publisher name &#39;{$s_n_name}&#39; is already present in publisher number <a href='{$s_url}' target='_blank'>{$s_url}</a>";

		if ( $b_real_table )
		{
			$_POST['n_u_pub_name_nocase']	= $s_n_nocase;
			$_POST['o_u_pub_name_nocase']	= dvdaf3_translatestring($s_o_name, DVDAF3_SEARCH);
			$_POST['n_u_url']				= $s_n_name !== '' ? ("/bus/{$n_obj_id}-". dvdaf3_translatestring($s_n_name, DVDAF3_URL)) : '';
			$_POST['o_u_url']				= $s_o_name !== '' ? ("/bus/{$n_obj_id}-". dvdaf3_translatestring($s_o_name, DVDAF3_URL)) : '';
		}
	}

	function __construct()
	{
		parent::__construct();
		$this->ms_obj_name				= 'publisher';
		$this->ms_tbl_obj				= 'pub';
		$this->ms_tbl_obj_				= 'u';
		$this->ms_tbl_direct			= false;
		$this->ms_tbl_hist				= 'pub_hist';
		$this->ms_tbl_pic				= false;
		$this->ms_tbl_pic_hist			= false;
		$this->ms_tbl_submit			= 'pub_submit';
		$this->ms_tbl_video				= false;
		$this->ms_obj_type				= '';
		//--------------------------------------------------
		// pub
		//--------------------------------------------------
		$this->ms_key					= 'pub_id';
		$this->ms_created_tm			= 'pub_created_tm';
		$this->ms_updated_tm			= 'pub_updated_tm';
		$this->ms_updated_by			= 'pub_updated_by';
		$this->ms_verified_tm			= 'pub_verified_tm';
		$this->ms_verified_by			= 'pub_verified_by';
		$this->ms_gen_by_edit_id		= 'pub_edit_id';
		$this->ms_pic_name				= false;
		$this->ms_pic_count				= false;
		$this->ma_attributes			= array(
			// eedt 1 = select, 2 = calc, 4 = select but do not display
			// ehis 1 = select col, 2 = select subquery, 4 = text only (no decode, no text area)
			array('col'=>'pub_name'				,'eedt'=>1,'ehis'=>1,'def'=>"'-'"),
			array('col'=>'pub_name_nocase'		,'eedt'=>2,'ehis'=>0,'def'=>"'-'"),
			array('col'=>'url'					,'eedt'=>2,'ehis'=>0,'def'=>"'-'"),
			array('col'=>'official_site'		,'eedt'=>1,'ehis'=>1,'def'=>"'-'"),
			array('col'=>'wikipedia'			,'eedt'=>1,'ehis'=>1,'def'=>"'-'"));
		//--------------------------------------------------
		// pub_hist
		//--------------------------------------------------
		$this->ms_id_merged				= 'pub_id_merged';
		//--------------------------------------------------
		// pub_submit
		//--------------------------------------------------
		$this->ms_submit_id				= 'pub_edit_id';
		//--------------------------------------------------
		// pub_direct_update
		//--------------------------------------------------
		$this->ms_direct_seq_num		= 'pub_direct_seq';
		$this->ms_direct_edit_obj		= 'edit_pub';
		$this->ms_direct_new_obj		= 'new_pub';
	}
}

?>
