<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CValidate
{
	function strUpdate(&$s_update, $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2)
	{
		$s_update .= ($s_col_1 ? (dvdaf_columname($s_tbl, $s_col_1). ' = '. $s_val_1. ', ') : '').
					 ($s_col_2 ? (dvdaf_columname($s_tbl, $s_col_2). ' = '. $s_val_2. ', ') : '');
	}

	function strInsert(&$s_insert, &$s_values, $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2)
	{
		if ( $s_col_1 )
		{
			$s_insert .= dvdaf_columname($s_tbl, $s_col_1). ', ';
			$s_values .= $s_val_1. ', ';
		}
		if ( $s_col_2 )
		{
			$s_insert .= dvdaf_columname($s_tbl, $s_col_2). ', ';
			$s_values .= $s_val_2. ', ';
		}
	}

	function validateInput(&$s_update, &$s_values, $n_gen, $b_skip_sec, &$s_display_error)
	{
		$a_new = array();
		$a_old = array();
		foreach ( $_POST as $s_key => $x_value )
		{
			if ( substr($s_key,0,2) == 'n_' )
			{
				$s_key = substr($s_key, 2);
				$s_new = dvdaf3_getvalue('n_'.$s_key, DVDAF3_POST);
				$s_old = dvdaf3_getvalue('o_'.$s_key, DVDAF3_POST);
				// echo "s_key = [{$s_key}] = [$s_old] => [$s_new]<br />";
				if ( $s_new || $s_old || $n_gen == DVDAF_INSERT )
				{
					if ( preg_match('/_[0-9]+$/', $s_key, $a_matches) > 0 )
					{
						$s_key = substr($s_key, 0, -strlen($a_matches[0]));
						if ( ! isset($a_new[$s_key]) )
						{
							$a_new[$s_key] = '';
							$a_old[$s_key] = '';
						}
						$s_tbl   = substr($s_key,0,2);
						$s_col_1 = substr($s_key,2);
						$s_sep   = $s_col_1 == 'region_mask' ? ',' : dvdaf_fieldseparator($s_tbl, $s_col_1);
						if ( $s_new != '' ) $a_new[$s_key] .= ($a_new[$s_key] != '' ? $s_sep : '') . $s_new;
						if ( $s_old != '' ) $a_old[$s_key] .= ($a_old[$s_key] != '' ? $s_sep : '') . $s_old;
					}
					else
					{
					$a_new[$s_key] = $s_new;
					$a_old[$s_key] = $s_old;
					}
				}
			}
		}

		$s_key = 'a_region_mask';
		if ( array_key_exists($s_key, $a_new) )
		{
			$a_new[$s_key] = dvdaf3_encoderegion($a_new[$s_key]);
			$a_old[$s_key] = dvdaf3_encoderegion($a_old[$s_key]);
		}

		$s_update = '';
		$s_values = '';
		$s_error  = '';
		foreach ( $a_new as $s_key => $x_value )
		{
			$s_new   = $a_new[$s_key];
			$s_old   = $a_old[$s_key];
			$s_tbl   = substr($s_key,0,2);
			$s_col_1 = substr($s_key,2);
			$s_col_2 = '';
			$s_val_1 = '';
			$s_val_2 = '';

			//echo "s_key = [{$s_key}]<br />".
			//     "s_new = [{$s_new}]<br />".
			//     "s_old = [{$s_old}]<br />".
			//     "s_tbl = [{$s_tbl}]<br />".
			//     "s_col_1 = [{$s_col_1}]<br />";
			//echo   "dvdaf_validateinput($s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2, $s_new, $s_old, $s_error, $n_gen | DVDAF_HTML | DVDAF_GET_SEC) )<br />";

			if ( dvdaf_validateinput($s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2, $s_new, $s_old, $s_error, $n_gen | DVDAF_GET_SEC) )
			{
				switch ( $n_gen )
				{
				case DVDAF_UPDATE:
					CValidate::strUpdate($s_update, $s_tbl, $s_col_1, $s_val_1, $b_skip_sec ? '' : $s_col_2, $b_skip_sec ? '' : $s_val_2);
					//echo "strUpdate($s_update, $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2)<br />";
					break;
				case DVDAF_INSERT:
					CValidate::strInsert($s_update, $s_values, $s_tbl, $s_col_1, $s_val_1, $b_skip_sec ? '' : $s_col_2, $b_skip_sec ? '' : $s_val_2);
					//echo "strInsert($s_update, $s_values, $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2)<br />";
					break;
				}
			}
			//echo "s_error = {$s_error}<br />";
			//echo "&nbsp;<br />";
		}
		if ( $s_error )
			$s_display_error .= str_replace("\n",'<br />',substr($s_error,0,-1));
	}
}

//////////////////////////////////////////////////////////////////////////

?>
