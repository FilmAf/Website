<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CDvdColSql
{
	function ammendSqlQuery(&$s_select, &$s_from, &$s_where, &$s_sort,
							&$s_collection_cond, &$n_exclude_mine, &$s_display_what,
							$b_collection, $b_logged_in, $b_view_self, $n_show_mode, $n_recursive,
							$b_valid_view, $b_valid_folder, $s_folder, $s_user_pinned, $b_skip_pins)
	{
		//  "Problem fields":
		//  genre_overwrite	- custom genre for the viewer or the collection (not available in searches): overwritten in combination in ammendSqlQuery() and sqlQuery()
		//  comments		- for collections: erased in this is a search in ammendSqlQuery()
		//  sort_text		- collection sort: added oncce for every one in ammendSqlQuery() -- not used in select count(*)
		//  folder, subfolder	- to indicate where in your collection the title is: provide by a wrapper in sqlQuery() -- not used in select count(*)

		if ( ! $b_valid_view || ! $b_valid_folder ) $s_where .= ' and 1=0';
		
		if ( $b_collection )
		{
			// This is a collection, use a public view if not the owner
			if ( ! $b_view_self )
				$s_from = str_replace('my_dvd b', 'v_my_dvd_pub b', $s_from). " JOIN v_my_folder_pub f ON b.folder = f.folder and b.user_id = f.user_id";

			// Use the custom sort for displayed collection
			$s_sort_overwrite = "IF(b.sort_text='-',a.dvd_title_nocase,CONCAT('/ ',LOWER(b.sort_text),SUBSTRING(a.dvd_title_nocase,2)))";
			$s_sort = str_replace('a.dvd_title_nocase', $s_sort_overwrite, $s_sort);

			// Ammend for recurcive folders
			if ( $n_recursive )
				$s_where = str_replace("b.folder = '{$s_folder}'", "(b.folder = '{$s_folder}' or b.folder like '{$s_folder}/%')", $s_where);

			if ( $b_logged_in && ! $b_view_self )
			{
				// Logged member looking at someone else's collection
				// need to show where it is in his collection (continues in B)
				//	b.genre_overwrite keep it
				//	b.pic_overwrite	  keep it
				//	b.comments	  keep it
				//	b.folder	  delete it, provided in part B
				//	b.sort_text	  add it, incorporate into a.dvd_title_nocase
				// echo "1:<br />s_select = $s_select<br />";
				$s_select = str_replace(', b.folder' ,'' ,$s_select). ", {$s_sort_overwrite} dvd_title_nocase, a.director_nocase";
				// echo "2:<br />s_select = $s_select<br />";
			}
			else
			{
				// Logged member looking at his own collection or member not logged looking at a collection
				// do not show where it is in his collection
				//	b.genre_overwrite keep it, use collection's
				//	b.pic_overwrite	  keep it
				//	b.comments	  keep it, use collection's
				//	b.folder	  blank it, not applicable to display it
				//	b.sort_text	  do not add it, it is already in there
				// echo "3:<br />s_select = $s_select<br />";
				$s_select = str_replace(', b.folder' ,", '' folder" ,$s_select);
				// echo "4:<br />s_select = $s_select<br />";
			}
		}
		else
		{
			// Not a collection thus, not linking to my_dvd at this level, will do so at "A" if appropriate
			$s_from = str_replace('my_dvd b JOIN dvd a ON b.dvd_id = a.dvd_id', 'dvd a', $s_from);

			if ( $b_logged_in )
			{
				// Search of a logged member
				// need to show where it is in his collection (continues in A)
				//	b.genre_overwrite delete it, use viewer's
				//	b.pic_overwrite	  blank it, not applicable to display it
				//	b.comments	  delete it, not applicable
				//	b.folder	  delete it, provided in part A
				//	b.sort_text	  do not add it, pass along regular a.dvd_title_nocase
				// echo "5:<br />s_select = $s_select<br />";
				$s_select = str_replace(', b.genre_overwrite'	,''						,
							str_replace(', b.pic_overwrite'		,", '-' pic_overwrite"	,
							str_replace(', b.user_film_rating'	,''						,
							str_replace(', b.user_dvd_rating'	,''						,
							str_replace(', b.comments'			,''						,
							str_replace(', b.folder'			,''						,$s_select)))))). ", a.dvd_title_nocase, a.director_nocase";
				// echo "6:<br />s_select = $s_select<br />";
			}
			else
			{
				// Search and user is not logged in, do not join to my_dvd_table
				// do not show where it is in his collection
				//	b.genre_overwrite blank it, not applicable to display it
				//	b.pic_overwrite	  blank it, not applicable to display it
				//	b.comments	  delete it, not applicable
				//	b.folder	  blank it, not applicable to display it
				//	b.sort_text	  do not add it, use the regular a.dvd_title_nocase
				// echo "7:<br />s_select = $s_select<br />";
				$s_select = str_replace(', b.genre_overwrite'	,', 0 genre_overwrite'	,
							str_replace(', b.pic_overwrite'		,", '-' pic_overwrite"	,
							str_replace(', b.user_film_rating'	,''						,
							str_replace(', b.user_dvd_rating'	,''						,
							str_replace(', b.comments'			,''						,
							str_replace(', b.folder'			,", '' folder"			,$s_select))))));
				// echo "8:<br />s_select = $s_select<br />";
			}
		}

		if ( $n_show_mode == DVDAF3_PRES_DVD_PRINT )
		{
			$s_select .= ", pic_status, pic_name";
		}

		$n_exclude_mine	= 0;
		$b_region				= false;
		$b_media				= false;
		if ( ! $b_skip_pins && $b_logged_in )
		{
			$s_super = $s_user_pinned;
			$a_parm  = explode('*',$s_super);
			for ( $i = 0 ; $i < count($a_parm) ; $i++ )
			{
				$a_item = explode('_', $a_parm[$i]);
				switch ( $a_item[0] )
				{
				case 'rgn':
					$b_region = true;
					switch ( $a_item[1] )
					{
					case 'us': case 'uk': case 'eu': case 'la': case 'as': case 'se': case 'jp':
					case 'au':	  $s_where .= " and a.country_block like '%,{$a_item[1]},%'";	break;
					case '1,a,0': $s_where .= " and a.region_mask & (2+128+1)";			break;
					case '2,b,0': $s_where .= " and a.region_mask & (4+256+1)";			break;
					case 'z':	  $s_where .= " and a.region_mask & 1";				break;
					case '1':	  $s_where .= " and a.region_mask & 2";				break;
					case '2':	  $s_where .= " and a.region_mask & 4";				break;
					case '3':	  $s_where .= " and a.region_mask & 8";				break;
					case '4':	  $s_where .= " and a.region_mask & 16";			break;
					case '5':	  $s_where .= " and a.region_mask & 32";			break;
					case '6':	  $s_where .= " and a.region_mask & 64";			break;
					case 'a':	  $s_where .= " and a.region_mask & 128";			break;
					case 'b':	  $s_where .= " and a.region_mask & 256";			break;
					case 'c':	  $s_where .= " and a.region_mask & 512";			break;
					default:
					$b_region = false;
					break;
					}
					break;
				case 'med':
					$b_media = true;
					switch ( $a_item[1] )
					{
					case 'd':     $s_where .= " and a.media_type = 'D'";		break;
					case 'b':     $s_where .= " and a.media_type = 'B'";		break;
					case 'h,c,t': $s_where .= " and a.media_type in ('H','C','T')";	break;
					case 'a,p,o': $s_where .= " and a.media_type in ('A','P','O')";	break;
					default:
					$b_media = false;
					break;
					}
					break;
				case 'xcmy':
					if ( ! $b_view_self )
					{
						switch ( $a_item[1] )
						{
						case 1:
						case 2:
							$n_exclude_mine = $a_item[1];
							break;
						}
					}
				}
			}
		}
		$s_collection_cond = ($b_region ? ', region' : '').
									($b_media  ? ', media'  : '').
									($n_exclude_mine ? ($n_exclude_mine == 1 ? ', exclude mine' : ', include mine') : '');
		if ( $s_collection_cond )
			$s_display_what .= "<div class='saff' style='margin-top:2px'>Additional criteria: Pinned". substr($s_collection_cond, 1). "</div>";
	}

	function sqlQuery($s_select, $s_from, $s_where, $s_sort, $n_begin, $n_count, $b_edit,
					  &$s_collection_cond,
					  $b_collection, $b_logged_in, $b_view_self, $s_user_id, $n_exclude_mine)
	{

		if ( $n_begin < 1  ) $n_begin = 1;

		$b_join   = false;
		switch ( $n_exclude_mine )
		{
		case 1:  $s_exclude = "c.user_id is NULL";	break;
		case 2:  $s_exclude = "c.user_id is not NULL";	break;
		default: $s_exclude = '';			break;
		}

		if ( $b_edit && $b_logged_in )
			if ( $b_collection )
				$b_join = ! $b_view_self;
			else
				$b_join = true;
		else
			$b_join = $s_exclude && ! $b_view_self;

//		echo "s_from = $s_from<br />";
//		echo "s_where = $s_where<br />";
//		echo "s_exclude = $s_exclude<br />";
		if ( $b_join )
		{
			if ( strpos($s_from, 'JOIN') )
			{
				$s_sql = "SELECT $s_select ".
						   ($b_edit && $b_logged_in && $b_collection && ! $b_view_self ? ", c.folder " : '').
						   ($b_edit && $b_logged_in && ! $b_collection                        ? ", c.genre_overwrite, c.folder " : '').
						   "FROM $s_from ".
						   ($s_where   ?   "and $s_where "   : '').
						   "LEFT JOIN v_my_dvd_ref c ON c.dvd_id = a.dvd_id and c.user_id = '{$s_user_id}' ".
						   ($s_exclude ? "WHERE $s_exclude " : '').
						   ($s_sort    ? "ORDER BY $s_sort " : '');
			}
			else
			{
				$s_sql = "SELECT $s_select ".
						   ($b_edit && $b_logged_in && $b_collection && ! $b_view_self ? ", c.folder " : '').
						   ($b_edit && $b_logged_in && ! $b_collection                        ? ", c.genre_overwrite, c.folder " : '').
						   "FROM $s_from ".
						   "LEFT JOIN v_my_dvd_ref c ON c.dvd_id = a.dvd_id and c.user_id = '{$s_user_id}' ".
						   ($s_exclude || $s_where ? "WHERE $s_where $s_exclude " : '').
						   ($s_sort    ? "ORDER BY $s_sort " : '');
			}
			if ( $s_exclude )
				$s_collection_cond .= $n_exclude_mine == 1 ? ', exclude mine' : ', include mine';
		}
		else
		{
			$s_sql = "SELECT $s_select ".
					   "FROM $s_from ".
					  "WHERE $s_where ".
		   ($s_sort ? "ORDER BY $s_sort " : '');
		}

		$s_sql = CSql::limitRows($s_sql, $n_begin, $n_count);

		if ( $b_edit && $b_logged_in )
		{
			if ( $b_collection )
			{
				if ( ! $b_view_self )
				{
					// Logged member looking at someone else's collection (B continuation)
					// echo "B:<br />n_begin = $n_begin<br />n_count = $n_count<br />s_sql = $s_sql<br />";
					return  "SELECT a.* FROM ($s_sql) a ORDER BY a.dvd_title_nocase, a.film_rel_year, a.director_nocase, a.dvd_id";
				}
			}
			else
			{
				// Search of a logged member (A continuation)
				// echo "A:<br />n_begin = $n_begin<br />n_count = $n_count<br />s_sql = $s_sql<br />";
				$n_p1   = strpos($s_sql, 'ORDER BY');
				$n_p2   = strlen($s_sql);
				$n_p3   = strpos($s_sql, ' GROUP BY'); if ( $n_p3 && $n_p3 < $n_p2 ) $n_p2 = $n_p3;
				$n_p3   = strpos($s_sql, ' HAVING'  ); if ( $n_p3 && $n_p3 < $n_p2 ) $n_p2 = $n_p3;
				$n_p3   = strpos($s_sql, ' LIMIT'   ); if ( $n_p3 && $n_p3 < $n_p2 ) $n_p2 = $n_p3;
				$s_sort = ($n_p1 && $n_p2) ? substr($s_sql, $n_p1, $n_p2 - $n_p1)
										   : "ORDER BY a.dvd_title_nocase, a.film_rel_year, a.director_nocase, a.dvd_id";
				// now we have preserved the sort that prioritizes the search string
				return  "SELECT a.* FROM ($s_sql) a ". $s_sort;
			}
		}

		return $s_sql;
	}
}

//////////////////////////////////////////////////////////////////////////

?>
