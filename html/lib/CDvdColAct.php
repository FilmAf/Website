<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CDvdColAct
{
	function recountDvds($s_user_id)
	{
		$n_tot = CSql::query_and_fetch1("SELECT count(*) ".
										  "FROM my_dvd b ".
										  "LEFT JOIN my_folder f ON f.user_id = b.user_id and f.folder = b.folder ".
										 "WHERE b.folder like 'owned%' ".
										   "and b.user_id = '{$s_user_id}' ".
										   "and b.public_ind = 'Y' ".
										   "and f.public_ind = 'Y'",0,__FILE__,__LINE__);
		if ( ! $n_tot ) $n_tot = 0;

		CSql::query_and_free("DELETE FROM my_dvd_count WHERE user_id = '{$s_user_id}'",0,__FILE__,__LINE__);

		CSql::query_and_free("INSERT INTO my_dvd_count (user_id, folder, public_count, public_owned) ".
							 "SELECT b.user_id, b.folder, count(*), {$n_tot} ".
							   "FROM my_dvd b ".
							   "LEFT JOIN my_folder f ON f.user_id = b.user_id and f.folder = b.folder ".
							  "WHERE b.folder like 'owned%' ".
							"and b.public_ind = 'Y' ".
							"and f.public_ind = 'Y' ".
							"and b.user_id = '{$s_user_id}' ".
							  "GROUP BY b.user_id, b.folder",CSql_IGNORE_ERROR,__FILE__,__LINE__);
	}

    //////////////////////////////////////////////////////////////////////
	function moveDvd($s_csv_dvd, $s_user_id, &$s_display_affected, &$s_display_error, $s_unatrib_subdomain, $s_folder)
	{
		if ( $s_csv_dvd )
		{
			if ( $s_folder == '' )
				$s_folder = dvdaf3_getvalue('tar', DVDAF3_POST|DVDAF3_LOWER);

			if ( CSql::query_and_fetch1("SELECT 1 FROM my_folder WHERE user_id = '{$s_user_id}' and folder = '{$s_folder}'", 0,__FILE__,__LINE__) )
			{
				if ( $s_unatrib_subdomain != '' )
					setcookie('move', $s_folder, 0, '/', $s_unatrib_subdomain, 0);
				
				$n_rows_1 = CSql::query_and_free(
							"UPDATE my_dvd SET folder = '$s_folder', my_dvd_updated_tm = now(), my_dvd_expire_tm = NULL ".
							 "WHERE user_id = '{$s_user_id}' and dvd_id in ($s_csv_dvd) and folder <> '$s_folder'",
								0,__FILE__,__LINE__);

				$n_rows_2 = CSql::query_and_free(
							"INSERT INTO my_dvd (user_id, dvd_id, folder, my_dvd_created_tm, my_dvd_updated_tm) ".
							"SELECT u.user_id, a.dvd_id, '$s_folder', now(), now() ".
							  "FROM dvd a, dvdaf_user u ".
							 "WHERE a.dvd_id in ($s_csv_dvd) and u.user_id = '{$s_user_id}' ".
							   "and not exists (SELECT 1 FROM my_dvd b WHERE b.user_id = u.user_id and b.dvd_id = a.dvd_id)",
							0,__FILE__,__LINE__);
				CDvdColAct::recountDvds($s_user_id);

				CSql::query_and_free("UPDATE dvdaf_user_2 SET last_coll_tm = now() WHERE user_id = '{$s_user_id}'",0,__FILE__,__LINE__);
				
				if ( $n_rows_1 ) $n_rows_1 = "$n_rows_1 title".($n_rows_1 > 1 ? 's' : '')." updated";
				if ( $n_rows_2 ) $n_rows_2 = "$n_rows_2 title".($n_rows_2 > 1 ? 's' : '')." inserted";
				if ( $n_rows_2 )
					$s_display_affected = $n_rows_1 ? "$n_rows_1 and $n_rows_2.<br />" : "$n_rows_2.<br />";
				else
					$s_display_affected = $n_rows_1 ? "$n_rows_1.<br />" : "Nothing to update.<br />";
			}
			else
			{
				$s_display_error = "Unable to find folder '{$s_folder}' in {$s_user_id}'s collection.";
			}
		}
	}
	function deleteDvd($s_csv_dvd, $s_user_id, &$s_display_affected, &$s_display_error)
	{
		if ( $s_csv_dvd )
		{
			$n_rows_1 = CSql::query_and_free(
						"UPDATE my_dvd SET folder = 'trash-can', my_dvd_expire_tm = date_add(now(), INTERVAL 7 DAY) ".
						 "WHERE user_id = '{$s_user_id}' and dvd_id in ($s_csv_dvd) and folder != 'trash-can'",
						0,__FILE__,__LINE__);
			CDvdColAct::recountDvds($s_user_id);

			CSql::query_and_free("UPDATE dvdaf_user_2 SET last_coll_tm = now() WHERE user_id = '{$s_user_id}'",0,__FILE__,__LINE__);

			$s_display_affected = $n_rows_1 ? "$n_rows_1 title".($n_rows_1 > 1 ? 's' : '')." deleted.<br />" : "Nothing to delete.<br />";
		}
	}
	function editDvd($s_user_id, &$s_display_affected, &$s_display_error)
	{
		$s_update_1 = '';
		$s_update_2 = '';
		$s_insfld_2 = '';
		$s_insval_2 = '';
		$b_need_b2	= false;
		foreach ( $_POST as $s_key => $x_value )
		{
			if ( substr($s_key,0,2) == 'n_' )
			{
				$s_tbl   = substr($s_key,2,2);
				$s_col_1 = substr($s_key,4);

				if ( $s_tbl == 'b2' )
				{
					$s_val_1 = dvdaf3_getvalue($s_key, DVDAF3_POST);
					$s_val_2 = dvdaf_getdefault($s_tbl, $s_col_1);
					if ( $s_val_1 == '-'                         ) $s_val_1 = '';
					if ( $s_val_1 == '-1.00'                     ) $s_val_1 = '-1';
					if ( $s_val_2 == "'-'" || $s_val_2 == 'NULL' ) $s_val_2 = '';
					if ( $s_val_1 != '' && $s_val_1 != $s_val_2 && "'".$s_val_1."'" != $s_val_2 )
						$b_need_b2 = true;
				}

				if ( dvdaf_validateinput($s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2, dvdaf3_getvalue($s_key, DVDAF3_POST), dvdaf3_getvalue('o_' .substr($s_key,2), DVDAF3_POST), $s_display_error, 0) )
				{
					if ( $s_tbl == 'b2' )
					{
						CValidate::strUpdate($s_update_2			 , $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2);
						CValidate::strInsert($s_insfld_2, $s_insval_2, $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2);
					}
					else
					{
						CValidate::strUpdate($s_update_1			 , $s_tbl, $s_col_1, $s_val_1, $s_col_2, $s_val_2);
					}
				}
			}
		}
		if ( ! $s_display_error )
		{
			$s_dvd    = dvdaf3_getvalue('dvd_id', DVDAF3_POST|DVDAF3_INT);
			$n_rows_1 = 0;
			$n_rows_2 = 0;
			if ( $b_need_b2 )
			{
				if ( $s_update_2 )
				{
					$s_update_2 = substr($s_update_2, 0, -2);
					$n_rows_2   = CSql::query_and_free("UPDATE my_dvd_2 SET {$s_update_2} WHERE user_id = '{$s_user_id}' and dvd_id = $s_dvd", 0,__FILE__,__LINE__);
					if ( ! CSql::rows_matched() )
					{
						$n_rows_2 = CSql::query_and_free("INSERT INTO my_dvd_2 ({$s_insfld_2}user_id, dvd_id) VALUES ({$s_insval_2}'{$s_user_id}', {$s_dvd})", 0,__FILE__,__LINE__);
					}
				}
			}
			else
			{
				$n_rows_2   = CSql::query_and_free("DELETE FROM my_dvd_2 WHERE user_id = '{$s_user_id}' and dvd_id = $s_dvd", 0,__FILE__,__LINE__);
			}
			if ( $s_update_1 )
			{
				$s_update_1 = substr($s_update_1, 0, -2);
				$n_rows_1   = CSql::query_and_free("UPDATE my_dvd SET {$s_update_1} WHERE user_id = '{$s_user_id}' and dvd_id = $s_dvd", 0,__FILE__,__LINE__);
			}

			if ( ($n_rows_1 + $n_rows_2) > 0 )
			{
				$n_rows_1 = CSql::query_and_free("UPDATE my_dvd SET my_dvd_updated_tm = now() WHERE user_id = '{$s_user_id}' and dvd_id = $s_dvd", 0,__FILE__,__LINE__);
				$s_display_affected = "1 listing updated.<br />";
			}
			else
			{
				$s_display_affected = "Sorry, your update operation did not affect any listings.<br />";
			}

			CSql::query_and_free("UPDATE dvdaf_user_2 SET last_coll_tm = now() WHERE user_id = '{$s_user_id}'",0,__FILE__,__LINE__);
		}
	}
	function getDvdList($n_post_or_get, $s_separator)
	{
		$a_sub = explode($s_separator, dvdaf3_getvalue('sub', $n_post_or_get));
		$s_dvd = '';
		for ( $i = 0 ; $i < count($a_sub) ; $i++ )
		{
			$n_dvd = 0 + $a_sub[$i];
			if ( $n_dvd > 0 ) $s_dvd .= $n_dvd. ',';
		}
		return substr($s_dvd,0,-1);
	}
}

//////////////////////////////////////////////////////////////////////////

?>
