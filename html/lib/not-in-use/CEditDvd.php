<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CDvd.php';
require $gs_root.'/lib/CEditObj.php';

class CEditDvd extends CEditObj
{
	function constructor()
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-edit-obj_{$this->mn_lib_version}.js'></script>";
		$this->ms_title				= $this->mn_edit_mode == CWnd_EDIT_NEW ? 'Submit New DVD' : 'Submit DVD Change';
		$this->mn_template			= DVDAF_EDIT_DVD;
		$this->mn_select_mode		= DVDAF_SELECT_DVD;
		$this->mo_obj				= new CObjDvd();
	}

	function getFooterJavaScript()
	{
		$s_user    = $this->mb_logged_in ? $this->ms_user_id : '';
		$s_view    = $this->mb_collection ? $this->ms_view_id : '';
		$s_config  = '{baseDomain:"'.					$this->ms_base_subdomain.'"'.
					 ',userCollection:"'.				$s_user					.'"'.
					 ',viewCollection:"'.				$s_view					.'"'.
					 ',objId:'.							$this->mn_obj_id		.
					 ',onPopup:DvdEdit.onPopup'.
					 ',ulExplain:1,ulGenre:1,ulFilmRating:1,ulCountry:1,ulRegion:1,ulLang:1,ulDir:1,ulPub:1'.
					 ',imgPreLoad:"explain.undo.spin"'.
					 '}';
		return
					"function setSearchVal(a,b,c,d){DvdEdit.setSearchVal(a,b,c,d)};".
					"function validate(a){DvdEdit.validate(a)};".
					"function onMenuClick(action){DvdEdit.onClick(action)};".
					"Filmaf.config({$s_config});".
					"setTimeout('".
						"DvdEdit.setup();".
					"',100);";
	}

	function drawBodyPage()
	{
		$s_tmpl_top	= dvdaf_parsetemplate      ("", $this->ms_select, $this->ms_from, $this->ms_where, $s_sort, $this->mn_template, $this->mn_select_mode, '', '', $this->mn_obj_id);
		$s_tmpl_d_1	= dvdaf_parsetemplateformat("", $this->mn_template+1, $this->mn_select_mode, '', '', $this->mn_obj_id);
		$s_tmpl_d_2	= dvdaf_parsetemplateformat("", $this->mn_template+2, $this->mn_select_mode, '', '', $this->mn_obj_id);
		$s_tmpl_d_3	= dvdaf_parsetemplateformat("", $this->mn_template+3, $this->mn_select_mode, '', '', $this->mn_obj_id);
		$s_tmpl_d_4	= dvdaf_parsetemplateformat("", $this->mn_template+4, $this->mn_select_mode, '', '', $this->mn_obj_id);
		$this->runSql();

		if ( $this->ma_result )
		{
			$n_tot = count($this->ma_result['edition_matrix']);

			$this->drawFormBeg();
			dvdaf_getbrowserow($this->ma_result, $s_tmpl_top, 0, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_KEY_OK);
			$this->drawNav();
			dvdaf_getbrowserow($this->ma_result, $s_tmpl_d_1, 0, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_KEY_OK);
			dvdaf_getbrowserow($this->ma_result, $s_tmpl_d_2, 0, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_KEY_OK);
			for ( $i = 0 ; $i < $n_tot ; $i++ )
				dvdaf_getbrowserow($this->ma_result['edition_matrix'][$i], $s_tmpl_d_3, $i+1, $n_tot, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_KEY_OK);
			dvdaf_getbrowserow($this->ma_result, $s_tmpl_d_4, 0, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_KEY_OK);
			$this->drawFormEnd();
		}
		else
		{
			$this->drawNotFound();
		}
	}

	function runSql()
	{
		$this->getSql();
		$s_sql = "SELECT {$this->ms_select}".($this->ms_from  ?  " FROM {$this->ms_from}"  : '').($this->ms_where ? " WHERE {$this->ms_where}" : '');
		if ( ($this->ma_result = CSql::query_and_fetch($s_sql,0,__FILE__,__LINE__)) )
		{
			$this->ma_result['e_edition_matrix'] = $this->getEdition  (0,0);
			$this->ma_result['e_dvd_pub_matrix'] = $this->getPublisher(0,0);
			$this->ma_result['e_dvd_dir_matrix'] = $this->getDirector (0,0);

			if ( $this->mn_audit_id || $this->mn_obj_id )
			{
				$this->ma_result['edition_matrix'] = $this->getEdition  ($this->mn_audit_id, $this->mn_obj_id);
				$this->ma_result['dvd_pub_matrix'] = $this->getPublisher($this->mn_audit_id, $this->mn_obj_id);
				$this->ma_result['dvd_dir_matrix'] = $this->getDirector ($this->mn_audit_id, $this->mn_obj_id);
			}
			else
			{
				$this->ma_result['edition_matrix'] = $this->ma_result['e_edition_matrix'];
				$this->ma_result['dvd_pub_matrix'] = $this->ma_result['e_dvd_pub_matrix'];
				$this->ma_result['dvd_dir_matrix'] = $this->ma_result['e_dvd_dir_matrix'];
			}
		}
	}

	function getEdition($n_audit_id, $n_obj_id)
	{
		$o     = $this->mo_obj;
		$s_sql = '';

		if ( $n_audit_id || $n_obj_id )
		{
// Needs to be redone to add x_ fields
			for ( $i = 0 ; $i < count($o->ma_edition_attributes) ; $i++ )
				if ( $o->ma_edition_attributes[$i]['eedt'] & 1 )
					$s_sql .= "e.{$o->ma_edition_attributes[$i]['col']}, ";

			$s_sql = "SELECT e.film_id, e.edition_seq, '-' prim_title, ". substr($s_sql, 0, -2);

			if ( $n_audit_id )
				$s_sql .= " FROM edition_submit r LEFT JOIN p ON r.film_id = p.film_id ".
						  "WHERE r.film_edit_id = {$n_audit_id} ORDER BY r.sort_order";
			else
				$s_sql .= " FROM edition r LEFT JOIN p ON r.film_id = p.film_id ".
						  "WHERE r.film_id = {$n_obj_id} ORDER BY r.sort_order";
		}
		else
		{
			for ( $i = 0 ; $i < count($o->ma_edition_attributes) ; $i++ )
				if ( $o->ma_edition_attributes[$i]['eedt'] & 1 )
				{
					$s_val  = $o->ma_edition_attributes[$i]['def'];
					$s_sql .= "{$s_val} {$o->ma_edition_attributes[$i]['col']}, {$s_val} x_{$o->ma_edition_attributes[$i]['col']}, ";
				}

			$s_sql = "SELECT 0 film_id, 0 edition_seq, 0 x_film_id, 0 x_edition_seq, '-' prim_title, ". substr($s_sql, 0, -2);
		}

		return CSql::query_and_fetch_all($s_sql, 0,__FILE__,__LINE__);
	}

	function getPublisher($n_audit_id, $n_obj_id)
	{
		$o     = $this->mo_obj;
		$s_sql = '';

		if ( $n_audit_id || $n_obj_id )
		{
/*
// Needs to be redone to add x_ fields
			for ( $i = 0 ; $i < count($o->ma_pub_attributes) ; $i++ )
				if ( $o->ma_pub_attributes[$i]['eedt'] & 1 )
					$s_sql .= "v.{$o->ma_pub_attributes[$i]['col']}, ";

			$s_sql = "SELECT v.pub_id, v.pub_seq, ". substr($s_sql, 0, -2);

			if ( $n_audit_id )
				$s_sql .= " FROM edpub_submit v ".
						  "WHERE v.edit_id = {$n_audit_id} ORDER BY v.sort_order";
			else
				$s_sql .= " FROM edpub v ".
						  "WHERE v.dvd_id = {$n_obj_id} ORDER BY v.sort_order";
*/
		}
		else
		{
			for ( $i = 0 ; $i < count($o->ma_pub_attributes) ; $i++ )
				if ( $o->ma_pub_attributes[$i]['eedt'] & 1 )
				{
					$s_val  = $o->ma_pub_attributes[$i]['def'];
					$s_sql .= "{$s_val} {$o->ma_pub_attributes[$i]['col']}, {$s_val} x_{$o->ma_pub_attributes[$i]['col']}, ";
				}

			$s_sql = "SELECT 0 pub_id, 0 x_pub_id, 0 pub_seq, 0 x_pub_seq, '-' pub_name, '-' x_pub_name, ". substr($s_sql, 0, -2);
		}

		return CSql::query_and_fetch_all($s_sql, 0,__FILE__,__LINE__);
	}

	function getDirector($n_audit_id, $n_obj_id)
	{
		$o     = $this->mo_obj;
		$s_sql = '';

		if ( $n_audit_id || $n_obj_id )
		{
/*
// Needs to be redone to add x_ fields
			for ( $i = 0 ; $i < count($o->ma_dir_attributes) ; $i++ )
				if ( $o->ma_dir_attributes[$i]['eedt'] & 1 )
					$s_sql .= "v.{$o->ma_dir_attributes[$i]['col']}, ";

			$s_sql = "SELECT v.person_id, v.dir_seq, ". substr($s_sql, 0, -2);

			if ( $n_audit_id )
				$s_sql .= " FROM eddir_submit v ".
						  "WHERE v.edit_id = {$n_audit_id} ORDER BY v.sort_order";
			else
				$s_sql .= " FROM eddir v ".
						  "WHERE v.dvd_id = {$n_obj_id} ORDER BY v.sort_order";
*/
		}
		else
		{
			for ( $i = 0 ; $i < count($o->ma_dir_attributes) ; $i++ )
				if ( $o->ma_dir_attributes[$i]['eedt'] & 1 )
				{
					$s_val  = $o->ma_dir_attributes[$i]['def'];
					$s_sql .= "{$s_val} {$o->ma_dir_attributes[$i]['col']}, {$s_val} x_{$o->ma_dir_attributes[$i]['col']}, ";
				}

			$s_sql = "SELECT 0 person_id, 0 x_person_id, ".
							"0 dir_seq, 0 x_dir_seq, ".
							"'-' dir_name, '-' x_dir_name, ".
							"'-' surname, ".
							"'-' given_name, ".
							"'Y' surname_first_ind, ".
							substr($s_sql, 0, -2);
		}

		return CSql::query_and_fetch_all($s_sql, 0,__FILE__,__LINE__);
	}
}

?>
