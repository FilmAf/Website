<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndMenu.php';

class CDataWindow extends CWndMenu
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();

		$this->ms_include_css .=
		"<style type='text/css'>".
			"#tbl_wrapper {margin-left:20px;}".
			"#tbl_title {color:#144067; font-size:12px; font-weight:bold; padding:8px 0 12px 0; text-align:left}".
			".dat-table {border-bottom:solid 1px #B9D1E7; border-right:solid 1px #B9D1E7; clear:both; margin-top:3px}".
			".dat-table td {border-top:solid 1px #B9D1E7; border-left:solid 1px #B9D1E7; padding:1px 2px 1px 2px}".
			".dat-table thead {color:#2C75B7; background:#eaeaea}".
			".dat-table thead td {padding:5px 2px 5px 2px; text-align:center}".
			".dat-table tbody td {vertical-align:top}".
			".dat-table input, .dat-table select, .dat-table textarea {margin:2px 1px 2px 1px}".
			".dat-row0 {background:#ffffff}".
			".dat-row1 {background:#edf4fa}".
		"</style>";

		$this->ma_what = null;
	}

	function validUserAccess()
	{
		if ( ! $this->mb_logged_in			 ) return CUser_NOACCESS_GUEST;
		if ( $this->ms_user_id != 'ash'		 ) return CUser_NOACCESS_USER;
//		if ( ! $this->mb_logged_in_this_sess ) return CUser_NOACCESS_SESSION;
		return CUser_ACCESS_GRANTED;
	}

	function getRow($b_new, $n_row)
	{
		$tbl    = &$this->ma_what['tbl'];
		$s_pref = $b_new ? 'n_' : 'o_';
		$s_suff = "_{$n_row}";
		$a      = array();

		for ( $i = 0 ; $i < count($tbl) ; $i++ )
		{
			if ( $tbl[$i]['show'] && $tbl[$i]['inp'] != 'lit' )
			{
				$col  = $tbl[$i]['col'];
				$name = $s_pref.$col.$s_suff;

				switch ( $tbl[$i]['vali'] )
				{
				case 'html': $a[$col] = "'".(isset($_POST[$name]) ? trim(str_replace("'", "\'", $_POST[$name])) : '') ."'"; break;
				case 'int' : $a[$col] =     dvdaf3_getvalue($name,DVDAF3_POST|DVDAF3_INT )    ; break;
				case 'date': $a[$col] = "'".str_replace(' 00:00:00','',dvdaf3_getvalue($name,DVDAF3_POST))."'"; break;
				default:	 $a[$col] = "'".dvdaf3_getvalue($name,DVDAF3_POST			  )."'"; break;
				}
			}
		}
		return $a;
	}

	function updateRow(&$n, &$d)
	{
		$tbl = &$this->ma_what['tbl'];
		$upd = '';
		$whr = '';
		for ( $i = 0 ; $i < count($tbl) ; $i++ )
		{
			if ( $tbl[$i]['show'] && $tbl[$i]['inp'] != 'lit' )
			{
				$col = $tbl[$i]['col'];
				switch ( $tbl[$i]['upd'] )
				{
				case 'key': case 'aut': $whr .= "$col = {$n[$col]} and ";		  break;
				case 'upd':				if ( $d[$col] ) $upd .= "$col = {$n[$col]}, ";			  break;
				case 'usr':				if ( $d[$col] ) $upd .= "$col = '{$this->ms_user_id}', "; break;
				case 'now': break;
				}
			}
		}
		$upd = substr($upd,0,-2);
		$whr = substr($whr,0,-5);
		if ( $upd && $whr )
		{
			$sql = "UPDATE {$this->ma_what['myt']} SET {$upd} WHERE {$whr}";
			CSql::query_and_free($sql,0,__FILE__,__LINE__);
		}
	}

	function inserteRow(&$n)
	{
		$tbl = &$this->ma_what['tbl'];
		$fld = '';
		$val = '';
		for ( $i = 0 ; $i < count($tbl) ; $i++ )
		{
			if ( $tbl[$i]['show'] && $tbl[$i]['inp'] != 'lit' )
			{
				$col = $tbl[$i]['col'];
				switch ( $tbl[$i]['upd'] )
				{
				case 'key': case 'upd': $fld .= "$col, "; $val .= "{$n[$col]}, ";			 break;
				case 'usr':				$fld .= "$col, "; $val .= "'{$this->ms_user_id}', "; break;
				case 'now':				$fld .= "$col, "; $val .= "now(), ";				 break;
				case 'aut': break;
				}
			}
		}
		$fld = substr($fld,0,-2);
		$val = substr($val,0,-2);
		if ( $fld )
		{
			$sql = "INSERT INTO {$this->ma_what['myt']} ({$fld}) VALUES({$val})";
			CSql::query_and_free($sql,0,__FILE__,__LINE__);
		}
	}

	function validateDataSubmission()
	{
		$b_any_changes = false;
		$tbl = &$this->ma_what['tbl'];

		for ( $j = 0 ; ($n_new = dvdaf3_getvalue("upd_{$j}",DVDAF3_POST|DVDAF3_INT)) ; $j++ )
		{
			$b_changed = false;
			$n = $this->getRow(true ,$j);
			$o = $this->getRow(false,$j);
			$d = array();

			for ( $i = 0 ; $i < count($tbl) ; $i++ )
			{
				if ( $tbl[$i]['show'] && $tbl[$i]['inp'] != 'lit' )
				{
					$col     = $tbl[$i]['col'];
					$d[$col] = false;

					switch ( $tbl[$i]['vali'] )
					{
					case 'none': break;
					case 'int' : if ( ($d[$col] = $n[$col] != $o[$col]      ) ) $b_changed = true; break;
					default:     if ( ($d[$col] = strcmp($n[$col], $o[$col])) ) $b_changed = true; break;
					}
				}
			}

			if ( $b_changed )
			{
				$b_any_changes = true;
				switch ( $n_new )
				{
				case 1: $this->updateRow($n, $d); break;
				case 2: $this->inserteRow($n); break;
				}
			}
		}
		return $b_any_changes;
	}

	function getSel(&$opt, $sel, $id, $rdo, $size)
	{
		if ( $rdo )
		{
			$val = $sel;
			for ( $i = 0 ; $i < count($opt) ; $i++ )
			{
				if ( $opt[$i][0] == $sel )
				{
					$val = $sel;
					break;
				}
			}
			return "<input type='text' value='{$val}'{$rdo}{$size} />".
				   "<input type='hidden' id='{$id}' name='{$id}' value='{$sel}' />";
		}

		$str  = "<select id='{$id}' name='{$id}'$rdo>";
		for ( $i = 0 ; $i < count($opt) ; $i++ )
		{
			$sil  = ($sel === false ? isset($opt[$i]['def']) && $opt[$i]['def'] : $opt[$i][0] == $sel) ? " selected='selected'" : '';
			$str .= "<option value='{$opt[$i][0]}'{$sil}>{$opt[$i][1]}</option>";
		}
		return $str . "</select>";
	}

	function decodeLit($s_lit, $n_row)
	{
		return str_replace('#row#', $n_row, $s_lit);
	}

	function drawBodyPage()
	{
		$tit = &$this->ma_what['tit'];
		$sql = &$this->ma_what['sql'];
		$tbl = &$this->ma_what['tbl'];

		echo
		"<form action='{$_SERVER['REQUEST_URI']}' method='post'>".
		  "<div id='tbl_wrapper'>".
			"<table>".
			  "<tr>".
				"<td>".
				  "<div id='tbl_title'>".
					"<div style='float:right;white-space:nowrap'>".
					  "<input type='button' value='Save' style='width:80px' onclick='submit(this.form)' /> ".
					  "<input type='button' value='Reset' style='width:80px' onclick='location.href=location.href' />".
					"</div>".
					$tit.
				  "</div>";

		if ( ($rr = CSql::query($sql,0,__FILE__,__LINE__)) )
		{
			$str  = "<table class='dat-table'>".
					  "<thead>".
						"<tr>".
						  "<td>";
			for ( $i = 0, $b = 2 ; $i < count($tbl) ; $i++ )
			{
				if ( $tbl[$i]['show'] && $tbl[$i]['inp'] != 'lit' )
				{
					$str .= $b == 2 ? '' : ( $b ? "</td><td>" : '<br />');
					$str .= $tbl[$i]['lbl'];
					$b    = $tbl[$i]['cell'];
				}
			}
			$str .=		  "</td>".
						"</tr>".
					  "</thead>".
					  "<tbody>";

			for ( $j = 0, $k = 0 ; ($ln = CSql::fetch($rr)) ; $j++, $k++ )
			{
				$str .= "<tr class='".($k % 2 ? 'dat-row0' : 'dat-row1' )."'>".
						  "<td>".
							"<input type='hidden' id='upd_{$k}' name='upd_{$k}' value='1' />";
				for ( $i = 0, $b = 2 ; $i < count($tbl) ; $i++ )
				{
					if ( $tbl[$i]['show'] )
					{
						if ( $tbl[$i]['inp'] != 'lit' )
						{
							$col  = $tbl[$i]['col'];
							$val  = $ln[$col];
							$rdo  = $tbl[$i]['edit'] ? '' : " readonly='readonly' class='ronly'";
							$mle  = $tbl[$i]['mlen'] > 0 ? " maxlength='{$tbl[$i]['mlen']}'" : '';
							$nid  = "n_{$col}_{$k}"; $nnm = "id='{$nid}' name='{$nid}'";
							$oid  = "o_{$col}_{$k}"; $onm = "id='{$oid}' name='{$oid}'";
							$size = " size='{$tbl[$i]['size']}'";

							switch ( $tbl[$i]['vali'] )
							{
							case 'html': $val = str_replace("'", "&#39;", $val); break;
							case 'date': $val = str_replace(' 00:00:00','',$val); break;
							}

						}

						$str .= $b == 2 ? '' : ( $b ? "</td><td>" : '<br />');

						switch ( $tbl[$i]['inp'] )
						{
						case 'text': $str .= "<input type='text' {$nnm} value='{$val}'{$rdo}{$mle}{$size} /><input type='hidden' {$onm} value='{$val}' />"; break;
						case 'area': $str .= "<textarea {$nnm} cols='{$tbl[$i]['size']}' rows='5' wrap='soft'{$rdo}{$mle}>{$val}</textarea><input type='hidden' {$onm} value='{$val}' />"; break;
						case 'sele': $str .= $this->getSel($tbl[$i]['opt'], $val, $nid, $rdo, $size)."<input type='hidden' {$onm} value='{$val}' />"; break;
						case 'lit' : $str .= $this->decodeLit($tbl[$i]['txt'],$k); break;
						default:     $str .= "?";
						}
						$b = $tbl[$i]['cell'];
					}
				}
				$str .=	  "</td>".
						"</tr>";
			}
			CSql::free($rr);

			for ( $j = 0 ; $j < 5 ; $j++, $k++ )
			{
				$str .= "<tr class='".($k % 2 ? 'dat-row0' : 'dat-row1' )."'>".
						  "<td>".
							"<input type='hidden' id='upd_{$k}' name='upd_{$k}' value='2' />";
				for ( $i = 0, $b = 2 ; $i < count($tbl) ; $i++ )
				{
					if ( $tbl[$i]['show'] )
					{
						if ( $tbl[$i]['inp'] != 'lit' )
						{
							$col  = $tbl[$i]['col'];
							$val  = '';
							$rdo  = $tbl[$i]['edit1'] ? '' : " readonly='readonly' class='ronly'";
							$mle  = $tbl[$i]['mlen'] > 0 ? " maxlength='{$tbl[$i]['mlen']}'" : '';
							$nid  = "n_{$col}_{$k}"; $nnm = "id='{$nid}' name='{$nid}'";
							$oid  = "o_{$col}_{$k}"; $onm = "id='{$oid}' name='{$oid}'";
							$size = " size='{$tbl[$i]['size']}'";
						}

						$str .= $b == 2 ? '' : ( $b ? "</td><td>" : '<br />');

						switch ( $tbl[$i]['inp'] )
						{
						case 'text': $str .= "<input type='text' {$nnm} value='{$val}'{$rdo}{$mle}{$size} /><input type='hidden' {$onm} value='{$val}' />"; break;
						case 'area': $str .= "<textarea {$nnm} cols='{$tbl[$i]['size']}' rows='5' wrap='soft'{$rdo}{$mle}>{$val}</textarea><input type='hidden' {$onm} value='{$val}' />"; break;
						case 'sele': $str .= $this->getSel($tbl[$i]['opt'], false, $nid, $rdo, $size)."<input type='hidden' {$onm} value='{$val}' />"; break;
						case 'lit' : $str .= $this->decodeLit($tbl[$i]['txt'],$k); break;
						default:     $str .= "?";
						}
						$b = $tbl[$i]['cell'];
					}
				}
				$str .=	  "</td>".
						"</tr>";
			}

			$str .=   "</tbody>".
					"</table>";
		}

		echo	$str.
				"</td>".
			  "</tr>".
			"</table>".
		  "</div>".
		"</form>";
	}
}

?>
