<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

define('CWnd_INPUT_PROMPT'		,     0);
define('CWnd_INPUT_GOOD'		,     1);
define('CWnd_INPUT_ERROR'		,     2);
define('CWnd_INPUT_DUPLICATE'	,     3);

define('CWnd_DLG_KIND_TITLE'	,      1);
define('CWnd_DLG_KIND_SECTION'	,      2);
define('CWnd_DLG_KIND_INFORM'	,      3);
define('CWnd_DLG_KIND_INPUT'	,      4);
define('CWnd_DLG_KIND_RESPONSE'	,      5);

define('CWnd_DLG_VISI_SHOW'		,      0);
define('CWnd_DLG_VISI_HIDE'		,      1);
define('CWnd_DLG_VISI_ABSENT'	,      2);
define('CWnd_DLG_VISI_MASK'		,      3);

define('CWnd_DLG_OPT_RIGHT'		,      4);
define('CWnd_DLG_SKIP_TD_BEG'	,      8);
define('CWnd_DLG_SKIP_TD_END'	,     16);
define('CWnd_DLG_READONLY'		,     32);
define('CWnd_DLG_ONE_COLUMN'	,     64);
define('CWnd_DLG_NEED'			,    128);
define('CWnd_DLG_UCWORD'		,    256);
define('CWnd_DLG_NOAUTOCOMPL'	,    512);

define('CWnd_DLG_SQL_SELECT'	,   1024); 
define('CWnd_DLG_SQL_INSERT_'	,   2048);
define('CWnd_DLG_SQL_INSERT'	,   3072); // 2048 + 1024 insert implies select
define('CWnd_DLG_SQL_UPDATE_'	,   4096);
define('CWnd_DLG_SQL_UPDATE'	,   5120); // 4096 + 1024 update implies update
define('CWnd_DLG_SQL_ALL'		,   7168); // 4096 + 2048 + 1024
define('CWnd_DLG_SQL_KEY'		,   8192);
define('CWnd_DLG_SQL_QUOTE'		,  16384);
//  32768
define('CWnd_DLG_SQL_NULL'		 ,  65536);
define('CWnd_DLG_SQL_NOW_INSERT' , 131072);
define('CWnd_DLG_SQL_NOW_UPDATE' , 393216); // 262144 + 131072
define('CWnd_DLG_SQL_NOW_UPDATE_', 262144);
define('CWnd_DLG_SQL_USR_INSERT' , 524288);
define('CWnd_DLG_SQL_USR_UPDATE' ,1572864); // 1048576 + 524288
define('CWnd_DLG_SQL_USR_UPDATE_',1048576);
define('CWnd_DLG_SQL_CNT_UPDATE' ,2097152);

define('CWnd_DLG_INPUT_TEXT'	,      1);
define('CWnd_DLG_INPUT_TEXTAREA',      2);
define('CWnd_DLG_INPUT_DATE'	,      3);
define('CWnd_DLG_INPUT_DATETIME',      4);
define('CWnd_DLG_INPUT_DECODE'	,      5);
define('CWnd_DLG_INPUT_PASS'	,      6);
define('CWnd_DLG_INPUT_BOOL'	,      7);
define('CWnd_DLG_INPUT_SELECT'	,      8);
define('CWnd_DLG_INPUT_SUBMIT'	,      9);
define('CWnd_DLG_INPUT_BUTTONS'	,     10);
define('CWnd_DLG_INPUT_HTML'	,     11);

define('CWnd_DLG_POST'			,      1);
define('CWnd_DLG_GET'			,      2);

require $gs_root.'/lib/CWnd.php';

class CWndDlg extends CWnd
{
	function constructor() // <<--------------------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
		$this->mn_footer_type		= CWnd_FOOTER_TIME;

		$this->ma_fields			= array();
		$this->mn_fields			= 0;
		$this->ma_keys				= array();
		$this->ms_form_name			= 'fname';
		$this->ms_form_action		= dvdaf3_getvalue('SCRIPT_NAME', DVDAF3_SERVER);
		$this->ms_form_method		= CWnd_DLG_POST;
		$this->mb_form_uploads		= false;
		$this->mb_include_edit		= false;
		$this->mn_action			= CWnd_INPUT_PROMPT;
		$this->ms_onsubmit			= '';
		$this->mb_all_needed		= true;
		$this->mb_any_field			= false;
		$this->ms_error_msg			= '';
		$this->mb_extra_post		= false;
		$this->ma_extra_post		= null;
		$this->ms_sql_table			= '';
		$this->mb_new_record		= false;
		$this->mb_validate			= count($_POST) > 0;
		$this->mb_must_login		= false;
		$this->ms_table_id			= 'dlg';
		$this->mn_max_width			= 600;
	}

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		$this->mn_action		= CWnd_INPUT_PROMPT;
		$this->ms_error_msg		= '';
		$this->mb_all_needed	= true;
		$this->mb_any_field		= false;
		$this->ma_keys			= array_keys($this->ma_fields);
		$this->mn_fields		= count($this->ma_fields);

		for ( $i = 0  ;  $i < $this->mn_fields  ;  $i++ )
		{
			$s_key			  =   $this->ma_keys[$i];
			$a_field		  = & $this->ma_fields[$s_key];
			$n_flags		  = $a_field['flags'];
			$a_field['valid'] = true;
			if ( ! ($n_flags & CWnd_DLG_VISI_HIDE) )
				$a_field['value'] = '';

			if ( $a_field['kind'] == CWnd_DLG_KIND_INPUT )
			{
				switch ( $a_field['input'] )
				{
				case CWnd_DLG_INPUT_SUBMIT:
				case CWnd_DLG_INPUT_BUTTONS:
				case CWnd_DLG_INPUT_HTML:
					$a_field['value'] = '';
					break;

				case CWnd_DLG_INPUT_DATE:
				case CWnd_DLG_INPUT_DATETIME:
					$this->mb_include_cal = true;
					// let it fall

				default:
					if ( $this->mb_validate && ($n_flags & CWnd_DLG_VISI_MASK) != CWnd_DLG_VISI_ABSENT )
					{
						$a_field['value'] = dvdaf3_getvalue ($s_key, $a_field['uparm'],
											array_key_exists('min', $a_field) ? $a_field['min'] : 0,
											array_key_exists('max', $a_field) ? $a_field['max'] : 0);
						//echo "reading $s_key = [{$a_field['value']}]<br>";

						if ( ($n_flags & CWnd_DLG_VISI_MASK) == CWnd_DLG_VISI_SHOW )
						{
							if ( $a_field['value'] != '' )
							{
								if ( ! $this->mb_any_field ) $this->mb_any_field = true;
							}
							else
							{
								if ( $n_flags & CWnd_DLG_NEED )
								{
									$a_field['valid'] = false;
									if ( $this->mb_all_needed ) $this->mb_all_needed = false;
								}
							}
						}
					}
					break;
				}
			}
		}

		if ( $this->mb_extra_post && $this->mb_validate )
		{
			$this->ma_extra_post = array();
			$a_post = array_keys($_POST);
			for ( $i = 0  ;  $i < count($a_post)  ;  $i++ )
			{
				$s_key = $a_post[$i];
				if ( ! array_key_exists($s_key, $this->ma_fields) )
				{
					$this->ma_extra_post[$s_key] = dvdaf3_getvalue ($s_key, DVDAF3_POST);
					$this->mb_any_field = true;
				}
			}
		}

		if ( $this->mb_any_field || $this->mb_validate )
		{
			if ( $this->mb_all_needed )
			{
				$this->mn_action    = CWnd_INPUT_GOOD;
			}
			else
			{
				$this->ms_error_msg = 'We are missing some fields.';
				$this->mn_action    = CWnd_INPUT_ERROR;
			}
		}
	}

	function getFooterJavaScript()
	{
		if ( $this->ms_table_id && $this->mn_max_width )
		{
			return  "window.onload=checkWidth;".
					"window.onresize=checkWidth;".
					"dlgWidth_{$this->ms_table_id}=0;".
					"function checkWidth()".
					"{".
						"var e=document.getElementById('{$this->ms_table_id}');".
						"if(e)".
						"{".
							"if(!dlgWidth_{$this->ms_table_id})dlgWidth_{$this->ms_table_id}=document.body.clientWidth-Dom.getElementWidth(e);".
							"e.style.width=(document.body.clientWidth-dlgWidth_{$this->ms_table_id}>{$this->mn_max_width})?{$this->mn_max_width}+'px':'auto';".
						"}".
					"}";
		}
		return '';
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		if ( $this->mb_must_login && ! $this->mb_logged_in )
		{
			$this->ma_fields = array(
			'_title'	=> array('kind'  => CWnd_DLG_KIND_TITLE,
								 'flags' => 0,
								 'label' => $this->ms_title),
			'info'		=> array('kind'  => CWnd_DLG_KIND_INFORM,
								 'flags' => 0,
								 'label' => "<p>Sorry you must login before you can proceed.</p>"),
			'enter'		=> array('kind'  => CWnd_DLG_KIND_RESPONSE,
								 'flags' => 0,
								 'label' => '',
								 'opt'   => array(
											array('type' => 'button', 'name' => 'cancel', 'value' => 'Go to homepage', 'onclick' => "location.href=\"/\""),
											array('type' => 'button', 'name' => 'ok'    , 'value' => 'Login'         , 'onclick' => "location.href=\"/utils/login.html\"")),
								 'input' => CWnd_DLG_INPUT_BUTTONS));
		}

		if ( $this->ms_redirect )
		{
			parent::drawBodyPage();
			return;
		}

		$this->ma_keys   = array_keys($this->ma_fields);
		$this->mn_fields = count($this->ma_fields);

		for ( $i = 0  ;  $i < $this->mn_fields  ;  $i++ )
		{
			$s_key   = $this->ma_keys[$i];
			$a_field = & $this->ma_fields[$s_key];
			$n_flags = $a_field['flags'];
		}

		switch ( $this->mn_action )
		{
		case CWnd_INPUT_DUPLICATE:
		case CWnd_INPUT_GOOD:
			break;
		case CWnd_INPUT_ERROR:
			echo  "<div class='msgbox-a'><div class='msgbox-b'><div class='msgbox-c'><div class='msgbox-d'>".
					"Oops, we found one or more problems.".
					"<p>{$this->ms_error_msg}</p>".
				  "</div></div></div></div>";
			// let it drop
		case CWnd_INPUT_PROMPT:
			$hid  = '';
			$str  = "<form id='{$this->ms_form_name}' name='{$this->ms_form_name}' action='{$this->ms_form_action}' method='".
					($this->ms_form_method == CWnd_DLG_POST ? 'post' : 'get'). "'".
					($this->ms_onsubmit	? " onsubmit='return {$this->ms_onsubmit}'" : '').
					($this->mb_form_uploads	? " enctype='multipart/form-data'" : '').
					" style='padding:0 20px 0 20px'>".
					  "<table class='dlg_table'". ($this->ms_table_id ? " id='{$this->ms_table_id}'" : '') .">";
			for ( $i = 0  ;  $i < $this->mn_fields  ;  $i++ )
			{
				$s_key		 = $this->ma_keys[$i];
				$a_field	 = & $this->ma_fields[$s_key];
				$n_flags	 = $a_field['flags'];
				$s_posf		 = array_key_exists('posf', $a_field) ?   $a_field['posf'] : '';
				$b_opt_right = false;
				$b_opt_left	 = false;
				$s_opt_div	 = '';
				$a_opt		 = array();
				if ( array_key_exists('opt', $a_field) )
				{
					if ( $n_flags & CWnd_DLG_OPT_RIGHT ) $b_opt_right = true; else $b_opt_left  = true;
					if ( array_key_exists('optd', $a_field) ) $s_opt_div = $a_field['optd'];
					$a_opt = & $a_field['opt'];
				}

				$err = '';
				if ( $this->mn_action == CWnd_INPUT_ERROR )
				{
					if ( isset($a_field['error']) )
						$err = "<div class='dlg_error'>{$a_field['error']}</div>";
					else
						if ( ! $a_field['valid'] ) $a_field['style'] = "style='color:#ff0000'";
				}

				switch ( $n_flags & CWnd_DLG_VISI_MASK )
				{
				case CWnd_DLG_VISI_SHOW:
					switch ( $a_field['kind'] )
					{
					case CWnd_DLG_KIND_TITLE:
						$str .= "<tr><td class='dlg_tit' colspan='2'>{$err}{$a_field['label']}</td></tr>";
						break;
					case CWnd_DLG_KIND_SECTION:
						$str .= "<tr><td class='dlg_sec' colspan='2'>{$err}{$a_field['label']}</td></tr>";
						break;
					case CWnd_DLG_KIND_INFORM:
						$str .= "<tr><td class='dlg_info' colspan='2'>{$err}{$a_field['label']}</td></tr>";
						break;
					case CWnd_DLG_KIND_INPUT:
						if ( $n_flags & CWnd_DLG_UCWORD      ) $a_field['value']  = ucwords($a_field['value']);
						if ( $n_flags & CWnd_DLG_NOAUTOCOMPL ) $a_field['parm' ] .= " autocomplete='off'";
						$s_style      = isset($a_field['style']) ? trim($a_field['style']) : ''; if ( $s_style ) $s_style = ' '. $s_style;
						$s_parm       = isset($a_field['parm' ]) ? trim($a_field['parm' ]) : ''; if ( $s_parm  ) $s_parm  = ' '. $s_parm;
						$s_value      = isset($a_field['value']) ? trim($a_field['value']) : '';
						$s_readonly   = ($n_flags & CWnd_DLG_READONLY) || ($a_field['input'] == CWnd_DLG_INPUT_DECODE) ? " readonly='readonly' style='color:#999999;background-color:#eeeeee'" : '';
						$b_single_col = $n_flags & CWnd_DLG_ONE_COLUMN;
						if ( $n_flags & CWnd_DLG_SKIP_TD_BEG )
						{
							$str .= $a_field['label'];
						}
						else
						{
							$s_label = $a_field['label'];
							if ( $s_style )
							{
								if ( strpos($s_label, " class='") )
									$s_label = str_replace(" class='", "{$s_style} class='", $s_label);
								$s_label = "<span{$s_style}>{$s_label}</span>";
							}

							$str .= ( $b_single_col ? "<tr><td colspan='2' class='dlg_right'>" : "<tr><td class='dlg_left'>{$s_label}" ).
									( $b_opt_left   ? $this->getButtons($a_opt, $s_opt_div) : '' ).
									( $b_single_col ? $s_label. ($a_field['input'] == CWnd_DLG_INPUT_HTML ? '' : "<br />") : "</td><td class='dlg_right'>" );
						}
						$str .= $err;

						switch ( $a_field['input'] )
						{
						case CWnd_DLG_INPUT_DECODE:
							if ( array_key_exists('sele', $a_field) )
							if ( array_key_exists($s_value, $a_field['sele']) )
								$s_value = $a_field['sele'][$s_value];
							// let it fall
						case CWnd_DLG_INPUT_TEXT:
							$str .= "<input type='text' name='{$s_key}' value='{$s_value}'{$s_parm}$s_readonly />";
							break;
						case CWnd_DLG_INPUT_TEXTAREA:
							$str .= "<textarea name='{$s_key}'{$s_parm}$s_readonly>". str_replace('<br />', "\n", $s_value) . "</textarea>";
							break;
						case CWnd_DLG_INPUT_DATE:
							$str .= "<input type='text' name='{$s_key}' id='{$s_key}' readonly='readonly' ".
									   "onkeydown=\"alert('Use the calendar button (&quot;...&quot;) to specify a date.')\" ".
									   "onchange='return false;' value='{$s_value}'{$s_parm}/>".
									"<input type='button' id='b_{$s_key}' value=' ... ' />&nbsp;".
									"<input type='button' id='c_{$s_key}' value=' x ' onclick='{$s_key}.value=\"\";return false;' />\n".
									"<script type='text/javascript'>Calendar.setup({inputField:'{$s_key}',ifFormat:'%Y-%m-%d',showsTime:false,button:'b_{$s_key}',singleClick:true,step:1});</script>\n";
							break;
						case CWnd_DLG_INPUT_DATETIME:
							$str .= "<input type='text' name='{$s_key}' id='{$s_key}' readonly='readonly' ".
									   "onkeydown=\"alert('Use the calendar button (&quot;...&quot;) to specify a date and time.')\" ".
									   "onchange='return false;' value='{$s_value}'{$s_parm}/>".
									"<input type='button' id='b_{$s_key}' value=' ... ' />&nbsp;".
									"<input type='button' id='c_{$s_key}' value=' x ' onclick='{$s_key}.value=\"\";return false;' />\n".
									"<script type='text/javascript'>Calendar.setup({inputField:'{$s_key}',ifFormat:'%Y-%m-%d %H:%M',showsTime:true,button:'b_{$s_key}',singleClick:true,step:1});</script>\n";
							break;
						case CWnd_DLG_INPUT_SELECT:
							if ( array_key_exists('sele', $a_field) )
							{
								$a_keys   = array_keys($a_field['sele']);
								if ( count($a_keys) > 0 )
								{
									$str .= "<select name='{$s_key}'{$s_parm}$s_readonly>";
									for ( $j = 0  ;  $j < count($a_keys)  ;  $j++ )
									{
										$str .= "<option value='{$a_keys[$j]}'". ($a_keys[$j] == $s_value ? ' selected' : ''). ">{$a_field['sele'][$a_keys[$j]]}</option>";
									}
									$str .= "</select>";
								}
							}
							break;
						case CWnd_DLG_INPUT_BOOL:
							$str .= "<input type='radio' name='{$s_key}'{$s_parm} value='Y' ". ($s_value == 'Y' ? "checked='checked' " : '') ."$s_readonly/>Yes ".
									"<input type='radio' name='{$s_key}'{$s_parm} value='N' ". ($s_value != 'Y' ? "checked='checked' " : '') ."$s_readonly/>No";
							break;
						case CWnd_DLG_INPUT_PASS:
							$str .= "<input type='password' name='{$s_key}' value='{$s_value}'{$s_parm}$s_readonly />";
							break;
						case CWnd_DLG_INPUT_HTML:
							$str .= "<span id='{$s_key}'{$s_parm}>". str_replace("\n", '<br />', $s_value) . "</span>";
							break;
						}
						$str .= "{$s_posf}". ( $b_opt_right ? $this->getButtons($a_opt, $s_opt_div) : '' );
						if ( ! ($n_flags & CWnd_DLG_SKIP_TD_END) ) $str .= "</td></tr>";
						break;
					case CWnd_DLG_KIND_RESPONSE:
						$str .= "<tr><td class='dlg_resp' colspan='2'>{$err}";
						switch ( $a_field['input'] )
						{
						case CWnd_DLG_INPUT_SUBMIT:
							$str .= "<input type='submit' name='{$s_key}' value='{$a_field['label']}' />";
							break;
						case CWnd_DLG_INPUT_BUTTONS:
							$str .= $this->getButtons($a_opt, $s_opt_div);
							break;
						}
						$str .= "</td></tr>";
						break;
					}
					break;
				case CWnd_DLG_VISI_HIDE:
					$hid .= "<input type='hidden' name='{$s_key}' value='{$a_field['value']}' />";
					break;
				}
			}
			echo $str.
				  "</table>".
				"$hid".
				"</form>";
			break;
		}
	}

	function getButtons(&$a_opt, $s_opt_div)
	{
		$str = '';
		if ( $s_opt_div ) $str .= "<div $s_opt_div>";
		for ( $j = 0  ;  $j < count($a_opt)  ;  $j++ )
		{
			$onclick = isset($a_opt[$j]['onclick']) ? $a_opt[$j]['onclick'] : ''; if ( $onclick ) $onclick = "onclick='{$onclick}' ";
			$style   = isset($a_opt[$j]['style'  ]) ? $a_opt[$j]['style'  ] : ''; if ( $style   ) $style   = "style='{$style}' ";
			if ( $a_opt[$j]['type'] == 'link' )
				$str .= "<a href={$a_opt[$j]['href']} {$onclick}{$style}/>{$a_opt[$j]['name']}</a>";
			else
				$str .= "<input type='{$a_opt[$j]['type']}' name='{$a_opt[$j]['name']}' value='{$a_opt[$j]['value']}' $onclick/> ";
		}
		$str = substr($str, 0 , -1);
		if ( $s_opt_div ) $str .= "</div>";
		return $str;
	}

	function loadDatabaseData($ss)
	{
		$rt = CSql::query_and_fetch($ss, 0,__FILE__,__LINE__);
		if ( $rt )
		{
			$a_keys = array_keys($rt);
			for ( $i = 0  ;  $i < count($a_keys)  ;  $i++ )
			{
				$s_key = $a_keys[$i];
				if ( array_key_exists($s_key, $this->ma_fields) )
				{
					$a_field = & $this->ma_fields[$s_key];
					$a_field['value'] = $rt[$s_key];
				}
			}
			return true;
		}
		return false;
	}

	function loadData()
	{
		if ( $this->mb_new_record ) return;
		$this->setSqlInclusion(CWnd_DLG_SQL_SELECT);
		$ss = "SELECT ". $this->getSqlCols(CWnd_DLG_SQL_SELECT) ." FROM {$this->ms_sql_table} WHERE ". $this->getSqlWhere(CWnd_DLG_SQL_SELECT);
		return $this->loadDatabaseData($ss);
	}

	function saveData()
	{
		if ( $this->mb_new_record )
		{
			$this->setSqlInclusion(CWnd_DLG_SQL_INSERT_);
			$ss = "INSERT INTO {$this->ms_sql_table} (". $this->getSqlCols(CWnd_DLG_SQL_INSERT_) .") VALUES (". $this->getSqlValues() .")";
		}
		else
		{
			$this->setSqlInclusion(CWnd_DLG_SQL_UPDATE_);
			$ss = "UPDATE {$this->ms_sql_table} SET ". $this->getSqlSet() ." WHERE ". $this->getSqlWhere(CWnd_DLG_SQL_UPDATE_);
		}
		return CSql::query_and_free($ss, 0,__FILE__,__LINE__) > 0;
	}

	function setSqlInclusion($n_mode)
	{
		switch ( $n_mode )
		{
		case CWnd_DLG_SQL_SELECT:
		case CWnd_DLG_SQL_INSERT_:
		case CWnd_DLG_SQL_UPDATE_:
			if ( $this->ma_keys == null ) { $this->ma_keys = array_keys($this->ma_fields); $this->mn_fields = count($this->ma_fields); }
			for ( $i = 0  ;  $i < $this->mn_fields  ;  $i++ )
			{
				$s_key			= $this->ma_keys[$i];
				$a_field		= & $this->ma_fields[$s_key];
				$n_flags		= $a_field['flags'];
				$a_field['inc']	= $a_field['kind'] == CWnd_DLG_KIND_INPUT && ($n_flags & $n_mode);
				if ( $n_mode == CWnd_DLG_SQL_INSERT_ && $a_field['inc'] && ! (($n_flags & (CWnd_DLG_SQL_NOW_INSERT | CWnd_DLG_SQL_USR_INSERT)) || $a_field['value'] != '' ) )
					$a_field['inc'] = false;
			}
			break;
		default:
			echo "<span class='error_note'>ERROR: Invalid parameter for ". __CLASS__ .'::'. __FUNCTION__ .'</span><br />';
			break;
		}
	}

	function getSqlCols($n_mode)
	{
		switch ( $n_mode )
		{
		case CWnd_DLG_SQL_SELECT:
		case CWnd_DLG_SQL_INSERT_:
			for ( $i = 0, $str = ''  ;  $i < $this->mn_fields  ;  $i++ )
			{
				$s_key   = $this->ma_keys[$i];
				$a_field = & $this->ma_fields[$s_key];
				if ( $a_field['inc'] ) $str .= "$s_key, ";
			}
			return substr($str, 0, -2);
			break;
		}
		return '';
	}

	function getSqlWhere($n_mode)
	{
		$n_match_flags = CWnd_DLG_SQL_KEY | ( $n_mode == CWnd_DLG_SQL_UPDATE_ ? CWnd_DLG_SQL_CNT_UPDATE : 0);
		for ( $i = 0, $str = ''  ;  $i < $this->mn_fields  ;  $i++ )
		{
			$s_key   = $this->ma_keys[$i];
			$a_field = & $this->ma_fields[$s_key];
			$n_flags = $a_field['flags'];
			if ( $a_field['kind'] == CWnd_DLG_KIND_INPUT && ($n_flags & $n_match_flags) )
			{
				$s_quote = ($n_flags & CWnd_DLG_SQL_QUOTE) ? "'" : '';
				$s_value = $s_quote. $a_field['value']. $s_quote;
				if ( $s_value == '' ) $s_value = '0';
				$str .= "$s_key = $s_value and ";
			}
		}
		return substr($str, 0, -5);
	}

	function getSqlValues()
	{
		for ( $i = 0, $str = ''  ;  $i < $this->mn_fields  ;  $i++ )
		{
			$s_key   = $this->ma_keys[$i];
			$a_field = & $this->ma_fields[$s_key];
			$n_flags = $a_field['flags'];
			if ( $a_field['inc'] )
			{
				$s_quote = ($n_flags & CWnd_DLG_SQL_QUOTE) ? "'" : '';
				$s_value = $s_quote. $a_field['value']. $s_quote;
				if ( $n_flags & CWnd_DLG_UCWORD ) $s_value = strtolower($s_value);
				if ( $n_flags & CWnd_DLG_SQL_NOW_INSERT ) $s_value = 'now()';
				if ( $n_flags & CWnd_DLG_SQL_USR_INSERT ) $s_value = "'{$this->ms_user_id}'";
				if ( $s_value == '' ) $s_value = '0';
				$str .= "$s_value, ";
			}
		}
		return substr($str, 0, -2);
	}

	function getSqlSet()
	{
		for ( $i = 0, $str = ''  ;  $i < $this->mn_fields  ;  $i++ )
		{
			$s_key   = $this->ma_keys[$i];
			$a_field = & $this->ma_fields[$s_key];
			$n_flags = $a_field['flags'];
			if ( $a_field['inc'] )
			{
				if ( $n_flags & CWnd_DLG_SQL_CNT_UPDATE )
				{
					$str .= "$s_key = $s_key + 1, ";
				}
				else
				{
					$s_quote = ($n_flags & CWnd_DLG_SQL_QUOTE) ? "'" : '';
					$s_value = $s_quote. $a_field['value']. $s_quote;
					if ( $n_flags & CWnd_DLG_UCWORD ) $s_value = strtolower($s_value);
					if ( $n_flags & CWnd_DLG_SQL_NOW_INSERT || $n_flags & CWnd_DLG_SQL_NOW_UPDATE_ ) $s_value = 'now()';
					if ( $n_flags & CWnd_DLG_SQL_USR_INSERT || $n_flags & CWnd_DLG_SQL_USR_UPDATE_ ) $s_value = "'{$this->ms_user_id}'";
					if ( ($n_flags & CWnd_DLG_SQL_NULL) && $s_value == '' || $s_value == "''" ) $s_value = 'NULL';
					if ( $s_value == '' ) $s_value = '0';
					$str .= "$s_key = $s_value, ";
				}
			}
		}
		return substr($str, 0, -2);
	}

	function checkJpegSecurityCode($s_external, $s_candidate)
	{
		$s_external = substr($s_external,0,16);
		$n_len		= strlen($s_external);
		for ( $i = 0  ;  $i < $n_len  ;  $i++ )
		{
			if ( ord($s_external{$i}) < 48 || ord($s_external{$i}) > 57 )
			{
				$s_external = '';
				break;
			}
		}
		if ( $s_external == '' ) return false;

		CSql::query_and_free("DELETE FROM human_verification WHERE external_id = '$s_external' or created_tm < date_add(now(), INTERVAL -2 HOUR)", 0,__FILE__,__LINE__);
		return $s_candidate == CSql::query_and_fetch1("SELECT internal_id FROM human_verification WHERE external_id = '$s_external'", 0,__FILE__,__LINE__);
	}

	function setJpegSecurityCode()
	{
		for (  $i = 0  ;  $i < 10  ;  $i++  )
		{
			$s_external	= microtime();
			$s_external	= substr($s_external,-7) . mt_rand(999999,100000) . substr($s_external,2,3);
			$s_internal	= ''.mt_rand(999999,100000);
			$ss			= "INSERT INTO human_verification (external_id, internal_id, created_tm) VALUES ('$s_external', '$s_internal', now())";
			if ( CSql::query_and_free($ss, 0,__FILE__,__LINE__) ) return $s_external;
		}
		return '';
	}
}

?>
