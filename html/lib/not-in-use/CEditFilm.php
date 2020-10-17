<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CFilm.php';
require $gs_root.'/lib/CEditObj.php';

class CEditFilm extends CEditObj
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
		$this->ms_title				= $this->mn_edit_mode == CWnd_EDIT_NEW ? 'Submit New Film' : 'Submit Film Change';
		$this->mn_template			= DVDAF_EDIT_FILM;
		$this->mn_select_mode		= DVDAF_SELECT_FILM;
		$this->mo_obj				= new CObjFilm();
	}

	function getFooterJavaScript()
	{
		$s_user    = $this->mb_logged_in ? $this->ms_user_id : '';
		$s_view    = $this->mb_collection ? $this->ms_view_id : '';
		$s_config  = '{baseDomain:"'.					$this->ms_base_subdomain.'"'.
					 ',userCollection:"'.				$s_user					.'"'.
					 ',viewCollection:"'.				$s_view					.'"'.
					 ',objId:'.							$this->mn_obj_id		.
					 ',onPopup:FilmEdit.onPopup'.
					 ',ulExplain:1,ulLang:1,ulGenre:1,ulAspect:1'.
					 ',imgPreLoad:"explain.undo.spin"'.
					 '}';
		return
					"function validate(a){FilmEdit.validate(a)};".
					"function onMenuClick(action){FilmEdit.onClick(action)};".
					"Filmaf.config({$s_config});".
					"setTimeout('".
						"FilmEdit.setup();".
					"',100);";
	}

	function drawBodyPage()
	{
		$s_tmpl_top	= dvdaf_parsetemplate      ("", $this->ms_select, $this->ms_from, $this->ms_where, $s_sort, $this->mn_template, $this->mn_select_mode, '', '', $this->mn_obj_id);
		$s_tmpl_dat	= dvdaf_parsetemplateformat("", $this->mn_template+1, $this->mn_select_mode, '', '', $this->mn_obj_id);
		$this->runSql();

		if ( $this->ma_result )
		{
			$this->drawFormBeg();
			dvdaf_getbrowserow($this->ma_result, $s_tmpl_top, 0, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_KEY_OK);
			$this->drawNav();
			dvdaf_getbrowserow($this->ma_result, $s_tmpl_dat, 0, 0, $this->ms_user_id, DVDAF2_ECHO, DVDAF3_INPUT, DVDAF4_ZERO_KEY_OK);
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
			$this->ma_result['e_title_matrix']		= $this->getFilmTitle(0,0);
			$this->ma_result['e_director_matrix']	= $this->getFilmRole (0,0,'D');
			$this->ma_result['e_cast_matrix']		= $this->getFilmRole (0,0,'C');
			$this->ma_result['e_crew_matrix']		= $this->getFilmRole (0,0,'E');

			if ( $this->mn_audit_id || $this->mn_obj_id )
			{
				$this->ma_result['title_matrix']		= $this->getFilmTitle($this->mn_audit_id, $this->mn_obj_id);
				$this->ma_result['director_matrix']	= $this->getFilmRole ($this->mn_audit_id, $this->mn_obj_id, 'D');
				$this->ma_result['cast_matrix']		= $this->getFilmRole ($this->mn_audit_id, $this->mn_obj_id, 'C');
				$this->ma_result['crew_matrix']		= $this->getFilmRole ($this->mn_audit_id, $this->mn_obj_id, 'E');
			}
			else
			{
				$this->ma_result['title_matrix']	= $this->ma_result['e_title_matrix'];
				$this->ma_result['director_matrix']	= $this->ma_result['e_director_matrix'];
				$this->ma_result['cast_matrix']		= $this->ma_result['e_cast_matrix'];
				$this->ma_result['crew_matrix']		= $this->ma_result['e_crew_matrix'];
			}
		}
	}

	function getFilmTitle($n_audit_id, $n_obj_id)
	{
		$o     = $this->mo_obj;
		$s_sql = '';

		if ( $n_audit_id || $n_obj_id )
		{
// Needs to be redone to add x_ fields
			for ( $i = 0 ; $i < count($o->ma_title_attributes) ; $i++ )
				if ( $o->ma_title_attributes[$i]['eedt'] & 1 )
					$s_sql .= "{$o->ma_title_attributes[$i]['col']}, ";

			$s_sql = 'SELECT title_seq, '. substr($s_sql, 0, -2);

			if ( $n_audit_id )
				$s_sql .= " FROM title_submit WHERE film_edit_id = {$n_audit_id} ORDER BY sort_order";
			else
				$s_sql .= " FROM title WHERE film_id = {$n_obj_id} ORDER BY sort_order";
		}
		else
		{
			for ( $i = 0 ; $i < count($o->ma_title_attributes) ; $i++ )
				if ( $o->ma_title_attributes[$i]['eedt'] & 1 )
				{
					$s_val  = $o->ma_title_attributes[$i]['def'];
					$s_sql .= "{$s_val} {$o->ma_title_attributes[$i]['col']}, {$s_val} x_{$o->ma_title_attributes[$i]['col']}, ";
				}

			$s_sql = 'SELECT 0 title_seq, 0 x_title_seq, '. substr($s_sql, 0, -2);
		}

		return CSql::query_and_fetch_all($s_sql, 0,__FILE__,__LINE__);
	}

	function getFilmRole($n_audit_id, $n_obj_id, $s_person_role)
	{
		$o     = $this->mo_obj;
		$s_sql = '';

		if ( $n_audit_id || $n_obj_id )
		{
// Needs to be redone to add x_ fields
			for ( $i = 0 ; $i < count($o->ma_role_attributes) ; $i++ )
				if ( $o->ma_role_attributes[$i]['eedt'] & 1 )
					$s_sql .= "r.{$o->ma_role_attributes[$i]['col']}, ";

			$s_sql = "SELECT r.person_id, r.seq_num, p.surname, p.given_name, p.surname_first_ind, ". substr($s_sql, 0, -2);

			if ( $n_audit_id )
				$s_sql .= " FROM role_submit r LEFT JOIN p ON r.person_id = p.person_id ".
						  "WHERE r.film_edit_id = {$n_audit_id} and r.person_role='{$s_person_role}' ORDER BY r.sort_order";
			else
				$s_sql .= " FROM role r LEFT JOIN p ON r.person_id = p.person_id ".
						  "WHERE r.film_id = {$n_obj_id} and r.person_role='{$s_person_role}' ORDER BY r.sort_order";
		}
		else
		{
			switch ( $s_person_role )
			{
			case 'D': $s_role_def = "'D'"; break; // Director
			case 'C': $s_role_def = "'-'"; break; // Principal
			case 'E': $s_role_def = "'P'"; break; // Producer
			}

			for ( $i = 0 ; $i < count($o->ma_role_attributes) ; $i++ )
				if ( $o->ma_role_attributes[$i]['eedt'] & 1 )
				{
					$s_val  = $o->ma_role_attributes[$i]['col'] == 'role_type' ? $s_role_def : $o->ma_role_attributes[$i]['def'];
					$s_sql .= "{$s_val} {$o->ma_role_attributes[$i]['col']}, {$s_val} x_{$o->ma_role_attributes[$i]['col']}, ";
				}

			$s_sql = "SELECT 0 person_id, 0 x_person_id, ".
							"0 seq_num, 0 x_seq_num, ".
							"'-' surname, ".
							"'-' given_name, ".
							"'Y' surname_first_ind, ".
							substr($s_sql, 0, -2);
		}

		return CSql::query_and_fetch_all($s_sql, 0,__FILE__,__LINE__);
	}
}

?>
