<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CPub.php';
require $gs_root.'/lib/CEditObj.php';

class CEditPub extends CEditObj
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
		$this->ms_title				= $this->mn_edit_mode == CWnd_EDIT_NEW ? 'Submit New Publisher' : 'Submit Publisher Change';
		$this->mn_template			= DVDAF_EDIT_PUB;
		$this->mn_select_mode		= DVDAF_SELECT_PUB;
		$this->mo_obj				= new CObjPub();
	}

	function getFooterJavaScript()
	{
		$s_user    = $this->mb_logged_in ? $this->ms_user_id : '';
		$s_view    = $this->mb_collection ? $this->ms_view_id : '';
		$s_config  = '{baseDomain:"'.					$this->ms_base_subdomain.'"'.
					 ',userCollection:"'.				$s_user					.'"'.
					 ',viewCollection:"'.				$s_view					.'"'.
					 ',objId:'.							$this->mn_obj_id		.
					 ',onPopup:PubEdit.onPopup'.
					 ',ulExplain:1,ulBirth:1'.
					 ',imgPreLoad:"explain.undo.spin"'.
					 '}';
		return
					"function setSearchVal(a,b,c,d){PubEdit.setSearchVal(a,b,c,d)};".
					"function validate(a){PubEdit.validate(a)};".
					"function onMenuClick(action){PubEdit.onClick(action)};".
					"Filmaf.config({$s_config});".
					"setTimeout('".
						"PubEdit.setup();".
					"',100);";
	}

	function drawBodyPage()
	{
		$s_tmpl_top = dvdaf_parsetemplate      ("", $this->ms_select, $this->ms_from, $this->ms_where, $s_sort, $this->mn_template, $this->mn_select_mode, '', '', $this->mn_obj_id);
		$s_tmpl_dat = dvdaf_parsetemplateformat("", $this->mn_template+1, $this->mn_select_mode, '', '', $this->mn_obj_id);
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
}

?>
