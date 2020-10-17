<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

define('CWnd_EDIT_INDEX'	,     0);
define('CWnd_DVD_FOLDERS'	,     1);
define('CWnd_DVD_LIST'		,     2);
define('CWnd_FILM_FOLDERS'	,     3);

define('MSG_LOGIN'			,     1);
define('MSG_PARENT_NAME'	,     2);
define('MSG_SAME_NAME'		,     3);
define('MSG_UNABLE_DELETE'	,     4);
define('MSG_UNABLE_UPDATE'	,     5);
define('MSG_UNABLE_CREATE'	,     6);
define('MSG_LONG_PATH'		,     7);

require $gs_root.'/lib/CWndMenu.php';
require $gs_root.'/lib/CDvdColAct.php';

class CFolders extends CWndMenu
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-folders_{$this->mn_lib_version}.js'></script>\n";
		$this->ms_title				= 'My Folders';

		$this->ms_request_uri		= dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER|DVDAF3_NO_AMP_EXPANSION);
		$this->mb_folder_renamed	= false;
		$this->ms_include_css	   .=
		"<style type='text/css'>".
			"#folders {border:solid 1px #b9d1e7; clear:both; margin-top:3px; margin-top:6px; }".
			"#folders tbody td {border:solid 1px #b9d1e7; padding:1px 2px 1px 2px; }".
			"#folders thead {color:#2c75b7; background:#eaeaea; }".
			"#folders thead td {border:solid 1px #b9d1e7; padding:4px; }".
			"#myform {margin:0 20px 6px 20px; }".
			"#b_submit {width:80px; }".
			"#b_cancel {width:80px; }".
			"#b_flat {width:110px; }".
		"</style>";
	}
	function getFooterJavaScript()
	{
		$s_user    = $this->mb_logged_in ? $this->ms_user_id : '';
		$s_config  = '{baseDomain:"'.					$this->ms_base_subdomain.'"'.
					 ',userCollection:"'.				$s_user					.'"'.
					 ',viewCollection:""'.
					 ',onPopup:SearchMenuPrep.onPopup'.
					 ',ulExplain:1'.
					 ',imgPreLoad:"pin.help.explain.spin.spin_hoz.undo"'.
					 '}';
		return
					"function onMenuClick(action){SearchMenuAction.onClick(action);};".
					"Filmaf.config({$s_config});".
					"Folders.setup();".
					"setTimeout('".
						"Menus.setup();".
					"',100);";
	}
	function tellUser($n_line, $n_what, $s_parm1, $s_parm2)
	{
		switch ( $n_what )
		{
		case MSG_LOGIN:			$this->ms_display_error = "This level of access requires that you be logged in. Please click <a href='javascript:".
														  "void(Win.reauth(0))'>here</a>. Once this session has been authenticated you can either ".
														  "navigate to this page again or in some browsers hit &lt;F5&gt; to reload it. Thanks!."; break;
		case MSG_PARENT_NAME:	$this->ms_display_error = "Parent folders must have a name."; break;
		case MSG_SAME_NAME:		$this->ms_display_error = "Sorry, no two folders at the same level can have the same name ({$s_parm1})."; break;
		case MSG_UNABLE_DELETE:	$this->ms_display_error = "Sorry, we are unable to delete the folder '{$s_parm1}'. Please check if the folder is empty. ".
														  "If not, please move your titles to another folder or delete them.  Thanks!"; break;
		case MSG_UNABLE_UPDATE:	$this->ms_display_error = "Sorry, we are unable to update the folder '{$s_parm1}'."; break;
		case MSG_UNABLE_CREATE:	$this->ms_display_error = "Sorry, we are unable to create the folder '{$s_parm1}'."; break;
		case MSG_LONG_PATH:		$this->ms_display_error = "Path length for '{$s_parm1}' is longer than the allowed 200 characters ({$s_parm2})."; break;
		}
		if ( $this->ms_display_error ) $this->ms_display_error .= " (code {$n_line})";

		return false;
	}
	function saveDvdFolders()
	{
		// Ensure parents have name
		// Ensure old folders have a name
		// Ensure no two folders have the same name
		$as_n_folder = array();
		$as_o_folder = array();
		$as_n_full   = array();
		$as_o_full   = array();
		$an_level    = array();
		$ab_n_pub    = array();
		$ab_o_pub    = array();
		$an_o_seq    = array();
		$an_n_sort   = array();
		$an_o_sort   = array();
		$an_beg      = array(0,0,0,0,0);
		$an_end      = array(0,0,0,0,0);
		$as_path     = array();
		$n_sort      = 0;

		for ( $i = 0 ; array_key_exists("n_folder_{$i}", $_POST) ; $i++ )
		{
			$as_n_folder[$i] = preg_replace('/[^a-z0-9-]/', '', preg_replace('/ +/', '-', dvdaf3_getvalue("n_folder_{$i}", DVDAF3_POST|DVDAF3_LOWER)));
			$as_o_folder[$i] = dvdaf3_getvalue("o_folder_{$i}", DVDAF3_POST|DVDAF3_LOWER);
			$as_o_full[$i]   = dvdaf3_getvalue("o_full_{$i}"  , DVDAF3_POST|DVDAF3_LOWER);
			$an_level[$i]    = dvdaf3_getvalue("n_levl_{$i}"  , DVDAF3_POST|DVDAF3_INT  );
			$ab_n_pub[$i]    = dvdaf3_getvalue("n_pub_{$i}"   , DVDAF3_POST|DVDAF3_LOWER) == 'on';
			$ab_o_pub[$i]    = dvdaf3_getvalue("o_pub_{$i}"   , DVDAF3_POST|DVDAF3_LOWER) == 'y';
			$an_o_seq[$i]    = dvdaf3_getvalue("o_seq_{$i}"   , DVDAF3_POST|DVDAF3_INT  );
			$an_o_sort[$i]   = dvdaf3_getvalue("o_sort_{$i}"  , DVDAF3_POST|DVDAF3_INT  );

			if ( $as_n_folder[$i] == '---delete-this-folder---' ) $as_n_folder[$i] = '';

			// calculate sort to save
			if ( $an_level[$i] <= 0 )
				$n_sort = 0;
			else
				if ( $as_n_folder[$i] != '' )
					$n_sort++;

			// compose folder full name
			$as_path[$an_level[$i]] = $as_n_folder[$i];
			for ( $j = 0, $s_path = '' ; $j < $an_level[$i] ; $j++ ) $s_path .= $as_path[$j].'/';

			if ( $as_n_folder[$i] )
			{
				$as_n_full[$i] = $s_path . $as_n_folder[$i];
				$an_n_sort[$i] = 1000 + 10 * $n_sort;
			}
			else
			{
				$as_n_full[$i] = '';
				$an_n_sort[$i] = 0;
			}
		}
		$n_total = $i;

		$i = 1;
		$j = 0;
		for ( $an_beg[$j] = $i-1 ; $i < $n_total && $an_level[$i] > 0 ; $i++ ) $an_end[$j] = $i; $i++; $j++; // owned
		for ( $an_beg[$j] = $i-1 ; $i < $n_total && $an_level[$i] > 0 ; $i++ ) $an_end[$j] = $i; $i++; $j++; // on-order
		for ( $an_beg[$j] = $i-1 ; $i < $n_total && $an_level[$i] > 0 ; $i++ ) $an_end[$j] = $i; $i++; $j++; // wish-list
		for ( $an_beg[$j] = $i-1 ; $i < $n_total && $an_level[$i] > 0 ; $i++ ) $an_end[$j] = $i; $i++; $j++; // work
		for ( $an_beg[$j] = $i-1 ; $i < $n_total && $an_level[$i] > 0 ; $i++ ) $an_end[$j] = $i;             // have-seen

		// Ensure parents have name
		// Ensure no two folders have the same name
		for ( $j = 0 ; $j < 5 ; $j++ )
		{
			for ( $i = $an_beg[$j] ; $i <= $an_end[$j] ; $i++ )
			{
				if ( $as_n_folder[$i] == '' )
				{
					for ( $k = $i + 1 ; $k <= $an_end[$j] && $an_level[$k] > $an_level[$i] ; $k++ )
					if ( $as_n_folder[$k] != '' )
						return $this->tellUser(__LINE__, MSG_PARENT_NAME, false, false);
				}
				else
				{
					for ( $k = $i + 1 ; $k <= $an_end[$j] && $an_level[$i] <= $an_level[$k] ; $k++ )
					if ( $an_level[$k] == $an_level[$i] && $an_level[$k] != '' && $as_n_folder[$k] == $as_n_folder[$i] )
						return $this->tellUser(__LINE__, MSG_SAME_NAME, $as_n_folder[$k], false);
				}
				if ( strlen($as_n_full[$i]) > 200 )
				{
					return $this->tellUser(__LINE__, MSG_LONG_PATH, $as_n_full[$i], strlen($as_n_full[$i]));
				}
			}
		}

		$this->mb_folder_renamed = false;
		for ( $i = 0 ; $i < $n_total ; $i++ )
		{
			if ( $an_level[$i] > 0 )
			{
				if ( $as_n_folder[$i] == '' )
				{
					if ( $as_o_folder[$i] != '' )
					{
						if ( ! $this->deleteDvdFolder($as_o_full[$i]) )
							return $this->tellUser(__LINE__, MSG_UNABLE_DELETE, $as_o_full[$i], false);
					}
				}
				else
				{
					if ( $as_o_folder[$i] != '' )
					{
						if ( $as_n_full[$i] != $as_o_full[$i] || $ab_n_pub[$i] != $ab_o_pub[$i] || $an_n_sort[$i] != $an_o_sort[$i] )
							if ( ! $this->updateDvdFolder($as_n_full[$i], $as_o_full[$i], $ab_n_pub[$i], $an_n_sort[$i]) )
								return $this->tellUser(__LINE__, MSG_UNABLE_UPDATE, $as_n_full[$i], false);
					}
					else
					{
						if ( ! $this->createDvdFolder($as_n_full[$i], $ab_n_pub[$i], $an_n_sort[$i]) )
							return $this->tellUser(__LINE__, MSG_UNABLE_CREATE, $as_n_full[$i], false);
					}
				}
			}
			else
			{
				if ( $ab_n_pub[$i] != $ab_o_pub[$i] )
					$this->updateTopDvdFolder($as_n_folder[$i], $ab_n_pub[$i]);
			}
		}
		if ( $this->mb_folder_renamed )
			CDvdColAct::recountDvds($this->ms_user_id);
	}

	function deleteDvdFolder($s_old_full)
	{
		$n_updated = 0;
		if ( strpos($s_old_full,'/') )
		{
			$n_updated = CSql::query_and_free("DELETE FROM my_folder ".
							   "WHERE user_id = '{$this->ms_user_id}' and folder = '{$s_old_full}' ".
								 "and not exists (SELECT * FROM my_dvd WHERE user_id = '{$this->ms_user_id}' and folder = '{$s_old_full}')",0,__FILE__,__LINE__);
		}
		return $n_updated > 0;
	}

	function updateDvdFolder($s_new_full, $s_old_full, $b_priv, $n_sort)
	{
		$n_updated = 0;
		$s_pub     = $b_priv ? 'N' : 'Y';

		if ( (strpos($s_new_full,'/') && strpos($s_old_full,'/')) || $s_new_full == $s_old_full )
		{
			if ( $s_new_full == $s_old_full )
			{
				$n_updated = CSql::query_and_free("UPDATE my_folder SET sort_order = $n_sort, public_ind = '$s_pub' ".
												   "WHERE folder = '$s_old_full' and user_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
			}
			else
			{
				$n_pos      = strpos($s_old_full, '/');
				$s_old_base = substr($s_old_full, 0, $n_pos);
				$n_pos      = strpos($s_new_full, '/');
				$s_new_base = substr($s_new_full, 0, $n_pos);
				if ( $s_old_base == $s_new_base )
				{
					$n_updated = CSql::query_and_free(	"INSERT INTO my_folder (user_id, folder, sort_category, sort_order, public_ind) ".
														  "SELECT '{$this->ms_user_id}', '{$s_new_full}', sort_category, $n_sort, '$s_pub' ".
															"FROM folder_tmpl ".
														   "WHERE folder = '$s_new_base' ".
															  "ON DUPLICATE KEY UPDATE public_ind = '$s_pub'",0,__FILE__,__LINE__);
					if ( $n_updated > 0 )
					{
									 CSql::query_and_free("UPDATE my_dvd SET folder = '$s_new_full' WHERE folder = '$s_old_full' and user_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
						$n_updated = CSql::query_and_free("DELETE FROM my_folder ".
														   "WHERE user_id = '{$this->ms_user_id}' and folder = '{$s_old_full}' ".
															 "and not exists (SELECT * FROM my_dvd WHERE user_id = '{$this->ms_user_id}' and folder = '{$s_old_full}')",0,__FILE__,__LINE__);
						$this->mb_folder_renamed = true;
					}
				}
			}
		}
		return true;
	}

	function updateTopDvdFolder($s_folder, $b_priv)
	{
		$s_pub = $b_priv ? 'N' : 'Y';
		CSql::query_and_free("UPDATE my_folder SET public_ind = '$s_pub' ".
							  "WHERE folder = '$s_folder' and user_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
	}

	function createDvdFolder($s_new_full, $b_priv, $n_sort)
	{
		$n_updated = 0;
		$s_pub     = $b_priv ? 'N' : 'Y';
		if ( strpos($s_new_full,'/') )
		{
			$n_pos      = strpos($s_new_full, '/');
			$s_new_base = substr($s_new_full, 0, $n_pos);
			$n_updated  = CSql::query_and_free("INSERT INTO my_folder (user_id, folder, sort_category, sort_order, public_ind) ".
											   "SELECT '{$this->ms_user_id}', '{$s_new_full}', sort_category, $n_sort, '$s_pub' ".
												 "FROM folder_tmpl ".
												"WHERE folder = '$s_new_base'",CSql_IGNORE_ERROR,__FILE__,__LINE__);
		}
		return $n_updated > 0;
	}

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		if ( ! $this->mb_logged_in )
		{
			$this->tellUser(__LINE__, MSG_LOGIN, false, false);
			return;
		}

		switch ( dvdaf3_getvalue('act', DVDAF3_POST|DVDAF3_LOWER) )
		{
		case 'dvd_folders': $this->saveDvdFolders(); break;
		}
	}

	function drawDvdLine(&$str, $i, $n_level, $n_sort, $s_folder, $s_full_folder, $b_always_priv, $b_priv)
	{
		$s_pub    = '';
		$s_priv   = $b_priv ? 'Y' : 'N';
		$s_name   = "folder_{$i}";

		if ( $n_level > 0 )
		{
			$s_level  = "<input id='n_levl_{$i}' name='n_levl_{$i}' type='hidden' value='{$n_level}' />".
						"<input id='o_levl_{$i}' type='hidden' value='{$n_level}' />".
						"<input id='o_seq_{$i}' name='o_seq_{$i}' type='hidden' value='{$i}' />".
						"<input id='o_sort_{$i}' name='o_sort_{$i}' type='hidden' value='{$n_sort}' />".
						"<img src='http://dv1.us/d1/00/ph00.gif' id='ih_1_levl_{$i}' height='10' width='17' sp_min='1' sp_max='10' sp_inc='F' align='bottom' alt='Level' />";
			$s_folder = "<img id='ni_{$s_name}' src='http://dv1.us/d1/1.gif' width='". (1 + $n_level * 30) ."' height='1' />".
						"<input id='n_{$s_name}' name='n_{$s_name}' type='text' size='32' maxsize='48' value='{$s_folder}' />".
						"<input id='o_{$s_name}' name='o_{$s_name}' type='hidden' value='{$s_folder}' />".
						"<input id='z_{$s_name}' type='hidden' value='{$s_folder}' />".
						"<input id='o_full_{$i}' name='o_full_{$i}' type='hidden' value='{$s_full_folder}' />".
						"<img id='zi_{$s_name}' src='http://dv1.us/d1/1.gif' height='21' width='19' align='top' />";
		}
		else
		{
			$s_level  = "<input id='n_levl_{$i}' name='n_levl_{$i}' type='hidden' value='{$n_level}' />".
						"<input id='skip_{$i}' type='hidden' value='1' />".
						"&nbsp;";
			$s_folder = "<img src='http://dv1.us/d1/1.gif' />".
						"<input id='n_{$s_name}' name='n_{$s_name}' type='text' size='32' maxsize='48' value='{$s_folder}' readonly='readonly' />";
		}

		if ( $b_always_priv )
		{
			$s_pub  = "<input type='checkbox' disabled='disabled' checked='checked' />".
					  "<img src='http://dv1.us/d1/1.gif' height='21' width='19' align='top' />";
		}
		else
		{
			$s_name = "pub_{$i}";
			$s_pub  = "<input id='n_{$s_name}' name='n_{$s_name}' type='checkbox'". ($b_priv ? " checked='checked'" : '')." />".
					  "<input id='o_{$s_name}' name='o_{$s_name}' type='hidden' value='{$s_priv}' />".
					  "<input id='z_{$s_name}' type='hidden' value='{$s_priv}' />".
					  "<img id='zi_{$s_name}' src='http://dv1.us/d1/1.gif' height='21' width='19' align='top' />";
		}

		$str .= "<tr>".
				  "<td>$s_level</td>".
				  "<td>$s_folder</td>".
				  "<td id='td_{$i}'>&nbsp;</td>".
				  "<td>$s_pub</td>".
				"</tr>";
	}

	function drawBodyPage()
	{
		if ( ! $this->mb_logged_in )
		{
			$this->tellUser(__LINE__, MSG_LOGIN, false, false);
			$this->drawMessages(true,false);
			return;
		}

		$str  = '';
		$i    = 0;
		$max  = 200;
		$va   = '';
		if ( ($rr = CSql::query("SELECT folder, sort_category, sort_order, public_ind, edit_ind FROM my_folder ".
								 "WHERE user_id = '{$this->ms_user_id}' and folder <> 'trash-can' ".
								 "ORDER BY sort_category, sort_order, folder", 0,__FILE__,__LINE__)) )
		{
			$b_last_priv = true;
			while ( ($ln = CSql::fetch($rr)) )
			{
				$s_folder = $ln['folder'];
				$n_level  =  0;
				$b_priv   = substr($s_folder,0,4) == 'work';

				while ( ($pos = strpos($s_folder, '/') ) > 0 )
				{
					$n_level++;
					$s_folder = substr($s_folder, $pos + 1);
				}

				if ( $i > 0 && $n_level == 0 )
				{
					$this->drawDvdLine($str, $i++, 1, 10000, '', '', $b_last_priv, false);
					$this->drawDvdLine($str, $i++, 1, 10000, '', '', $b_last_priv, false);
				}
				$this->drawDvdLine($str, $i++, $n_level, $ln['sort_order'], $s_folder, $ln['folder'], $b_priv, $ln['public_ind'] != 'Y');
				$b_last_priv = $b_priv;
			}
			$this->drawDvdLine($str, $i++, 1, 10000, '', '', $b_last_priv, false);
			$this->drawDvdLine($str, $i++, 1, 10000, '', '', $b_last_priv, false);
			CSql::free($rr);
		}

		echo  "<div>".
				$this->getMessageString(true,false).
				"<form id='myform' name='myform' method='post' action='{$this->ms_request_uri}'>".
				  "<table>".
					"<tr>".
					  "<td>".
						"<div>".
						  "<input id='b_submit' type='button' value='Submit' onclick='Folders.validate()' /> ".
						  "<input id='b_cancel' type='button' value='Cancel' onclick='Validate.reload(\"{$this->ms_request_uri}\")' /> ".
						  "<input id='b_flat' type='button' value='Reset Levels' onclick='Folders.resetLevels()' />".
						  "<input id='act' name='act' type='hidden' value='dvd_folders' />".
						"</div>".
					  "</td>".
					"</tr>".
					"<tr>".
					  "<td valign='top'>".
						"<table id='folders'>".
						  "<thead>".
							"<tr>".
							  "<td>Level</td>".
							  "<td>Folder</td>".
							  "<td>Sort / Delete</td>".
							  "<td>Private</td>".
							"</tr>".
						  "</thead>".
						  "<tbody>".
							$str.
						  "</tbody>".
						"</table>".
					  "</td>".
					"</tr>".
				  "</table>".
				"</form>".
			  "</div>";
	}
}

?>
