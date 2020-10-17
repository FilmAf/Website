<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

class CCountDvds extends CAjax
{
	function getSql()
	{
		$this->get_requester(); // $this->ms_requester									// the person issuing the request
		$this->ms_user	  = dvdaf3_getvalue('user'	,DVDAF3_GET|DVDAF3_LOWER);
		$this->mn_list	  = dvdaf3_getvalue('list'	,DVDAF3_GET|DVDAF3_INT);
		$this->mn_paid	  = dvdaf3_getvalue('paid'	,DVDAF3_GET|DVDAF3_INT);
		$this->ms_show	  = dvdaf3_getvalue('show'	,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_group	  = dvdaf3_getvalue('group'	,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_target  = dvdaf3_getvalue('target',DVDAF3_GET);
		$this->mb_self	  = $this->ms_user && $this->ms_user == $this->ms_requester;

		if ( $this->ms_show != 'folder' && $this->ms_show != 'genre' )
			$this->ms_group = 'dvds';

		$this->ms_context = ( $this->ms_user	!== '' ? "user='{$this->ms_user}' "		: '').
							( $this->mn_list	!== '' ? "list='{$this->mn_list}' "		: '').
							( $this->mn_paid	!== '' ? "paid='{$this->mn_paid}' "		: '').
							( $this->ms_show	!== '' ? "show='{$this->ms_show}' "		: '').
							( $this->ms_group	!== '' ? "group='{$this->ms_group}' "	: '').
							( $this->ms_target	!== '' ? "target='{$this->ms_target}' "	: '');

		if ( $this->ms_user == '' || $this->ms_user == 'www' )
			return $this->on_error("Unrecognized request: ".__LINE__);

		$s_my_dvd	 = $this->mb_self ? 'my_dvd' : 'v_my_dvd_pub';
		$s_my_folder = $this->mb_self ? 'my_folder' : 'v_my_folder_pub';
		$s_my_dvd_2	 = '';
		$s_dvd		 = '';
		$s_sql		 = '';

		switch ( $this->ms_group )
		{
		case 'dvds':
			$s_counts = ", count(*) counter";
			if ( $this->mn_list )
			{
				$s_counts  .= ", sum(a.list_price) list_price";
				$s_dvd      = 'JOIN dvd a ON b.dvd_id = a.dvd_id ';
			}
			if ( $this->mn_paid )
			{
				$s_counts  .= ", sum(b2.price_paid) price_paid";
				$s_my_dvd_2 = 'LEFT JOIN my_dvd_2 b2 ON b.dvd_id = b2.dvd_id and b.user_id = b2.user_id ';
			}
			break;
		case 'titles':
			$s_counts   = ", sum(a.num_titles) counter";
			$s_dvd      = 'JOIN dvd a ON b.dvd_id = a.dvd_id ';
			break;
		case 'disks':
			$s_counts   = ", sum(a.num_disks) counter";
			$s_dvd      = 'JOIN dvd a ON b.dvd_id = a.dvd_id ';
			break;
		case 'list':
			$s_counts   = ", sum(a.list_price) counter";
			$s_dvd      = 'JOIN dvd a ON b.dvd_id = a.dvd_id ';
			break;
		case 'paid':
			$s_counts   = ", sum(b2.price_paid) counter";
			$s_dvd      = 'JOIN dvd a ON b.dvd_id = a.dvd_id ';
			$s_my_dvd_2 = 'LEFT JOIN my_dvd_2 b2 ON b.dvd_id = b2.dvd_id and b.user_id = b2.user_id ';
			break;
		default:
			return $this->on_error("Unrecognized request: ".__LINE__);
		}

		switch ( $this->ms_show )
		{
		case 'folder':
			$s_sql = "SELECT f.folder use_folder{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN {$s_my_folder} f ON b.user_id = f.user_id and b.folder = f.folder {$s_dvd}{$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' GROUP BY f.sort_category, f.sort_order, use_folder ORDER BY f.sort_category, f.sort_order, use_folder";
			break;
		case 'genre':
			$s_sql = "SELECT IF(INSTR(b.folder,'/')=0, b.folder, LEFT(b.folder,INSTR(b.folder,'/')-1)) use_folder, ".
					 "IF(b.genre_overwrite=99999, a.genre, b.genre_overwrite) use_genre{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN {$s_my_folder} f ON b.user_id = f.user_id and b.folder = f.folder JOIN dvd a ON b.dvd_id = a.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder <> 'trash-can' GROUP BY f.sort_category, use_folder, use_genre ORDER BY f.sort_category, use_folder, use_genre";
			break;
		case '_dir':
			$s_sql = "SELECT c.director cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id JOIN stats_dvd_dir c ON a.dvd_id = c.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY counter DESC, cat LIMIT 51";
			break;
		case '_pub':
			$s_sql = "SELECT c.publisher cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id JOIN stats_dvd_pub c ON a.dvd_id = c.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY counter DESC, cat LIMIT 51";
			break;
		case '_lang':
			$s_sql = "SELECT c.language cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id JOIN stats_dvd_language c ON a.dvd_id = c.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY counter DESC, cat";
			$s_sql = "SELECT d.descr, a.* FROM ({$s_sql}) a LEFT JOIN decodes d ON d.domain_type = 'language' and d.code_str = a.cat";
			break;
		case '_pubcnt':
			$s_sql = "SELECT c.country cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id JOIN stats_dvd_country c ON a.dvd_id = c.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY counter DESC, cat";
			$s_sql = "SELECT d.descr, a.* FROM ({$s_sql}) a LEFT JOIN decodes d ON d.domain_type = 'country' and d.code_str = a.cat";
			break;
		case '_region':
			$s_sql = "SELECT c.region cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id JOIN stats_dvd_region c ON a.dvd_id = c.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY counter DESC, cat";
			$s_sql = "SELECT d.descr, a.* FROM ({$s_sql}) a LEFT JOIN decodes d ON d.domain_type = 'region' and d.code_char = a.cat";
			break;
		case '_genre':
			$s_sql = "SELECT truncate(a.genre,-3) cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY counter DESC, cat";
			$s_sql = "SELECT d.descr, e.descr, a.* FROM ({$s_sql}) a ".
					   "LEFT JOIN decodes d ON d.domain_type = 'genre' and d.code_int = a.cat + 999 ".
					   "LEFT JOIN decodes e ON e.domain_type = 'genre_lower' and e.code_int = a.cat";
			break;
		case '_format':
			$s_sql = "SELECT a.media_type cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY counter DESC, cat";
			$s_sql = "SELECT d.descr, a.* FROM ({$s_sql}) a LEFT JOIN decodes d ON d.domain_type = 'media_type' and d.code_char = a.cat";
			break;
		case '_decade':
			$s_sql = "SELECT truncate(a.film_rel_year,-1) cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY cat DESC";
			break;
		case '_dvd_rel':
			$s_sql = "SELECT left(a.dvd_rel_dd,4) cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY cat DESC";
			break;
		case '_onwed_yy':
			$s_sql = "SELECT if(b.owned_dd = '-', '-', left(b.owned_dd,4)) cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY cat DESC";
			break;
		case '_onwed_mm':
			$s_sql = "SELECT if(b.owned_dd = '-', '-', concat(left(b.owned_dd,4),'-',mid(b.owned_dd,5,2))) cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id {$s_my_dvd_2}".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY cat DESC";
			break;
		case '_watch_yy':
			$s_sql = "SELECT if(b2.last_watched_dd = '-' or b2.last_watched_dd is null, '-', left(b2.last_watched_dd,4)) cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id LEFT JOIN my_dvd_2 b2 ON b.dvd_id = b2.dvd_id and b.user_id = b2.user_id ".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY cat DESC";
			break;
		case '_watch_mm':
			$s_sql = "SELECT if(b2.last_watched_dd = '-' or b2.last_watched_dd is null, '-', concat(left(b2.last_watched_dd,4),'-',mid(b2.last_watched_dd,5,2))) cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id LEFT JOIN my_dvd_2 b2 ON b.dvd_id = b2.dvd_id and b.user_id = b2.user_id ".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY cat DESC";
			break;
		case '_retailer':
			$s_sql = "SELECT ifnull(b2.retailer,'-') cat{$s_counts} ".
					   "FROM {$s_my_dvd} b JOIN dvd a ON b.dvd_id = a.dvd_id LEFT JOIN my_dvd_2 b2 ON b.dvd_id = b2.dvd_id and b.user_id = b2.user_id ".
					  "WHERE b.user_id = '{$this->ms_user}' and b.folder like 'owned%' GROUP BY cat ORDER BY counter DESC, cat";
			break;
		default:
			return $this->on_error("Unrecognized request: ".__LINE__);
		}

		$this->ms_sql = $s_sql;
		$this->mn_max = 1000;
		return true;
	}
	function done()
	{
		if ( ! $this->mn_count )
		{
			if ( CSql::query_and_fetch1("SELECT count(*) FROM v_my_dvd_ref WHERE user_id = '{$this->ms_user}'",0,__FILE__,__LINE__) > 0 )
			{
				$this->ms_msg = 'The DVD collection has been made private.';
			}
			else
			{
				if ( CSql::query_and_fetch1("SELECT 1 FROM dvdaf_user WHERE user_id = '{$this->ms_user}'",0,__FILE__,__LINE__) == 1 )
					$this->ms_msg = 'The DVD collection is empty.';
				else
					$this->ms_msg = 'Strange... we did not find this user.';
			}
		}
	}
}

class CProfile extends CAjax
{
	// ?mode=profile&what=get&user=ash
	// ?mode=profile&what=set&user=ash
	function getSql()
	{
		$this->get_requester();

		$this->ms_what	  = dvdaf3_getvalue('what',DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_user	  = dvdaf3_getvalue('user',DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_context = ( $this->ms_mode != '' ? "mode='{$this->ms_mode}' " : '').
							( $this->ms_what != '' ? "what='{$this->ms_what}' " : '').
							( $this->ms_user != '' ? "user='{$this->ms_user}' " : '');
		$this->mb_self	  = $this->ms_user && $this->ms_user == $this->ms_requester;
		$this->mb_assoc	  = true;

		switch ( $this->ms_what )
		{
		case 'set':
			$s_version		 	 = $this->valstr(dvdaf3_getvalue('version',DVDAF3_POST|DVDAF3_LOWER,3),'-'); // need to validate
			if ( $s_version != 'v1' )
				return $this->on_error("Unrecognized request format: ".__LINE__);
			if ( ! $this->mb_self )
				return $this->on_error("Unrecognized request: ".__LINE__);
			
			$s_name				= $this->valstr(dvdaf3_getvalue('name'				,DVDAF3_POST, 64),'-'	);
			$s_dob				= $this->valdat(dvdaf3_getvalue('dob'				,DVDAF3_POST    )		); // need to validate date... and create a field for it when showing self
			$s_gender			= $this->vala_3(dvdaf3_getvalue('gender'			,DVDAF3_POST,  1),'-','M','F','P');
			$s_city				= $this->valstr(dvdaf3_getvalue('city'				,DVDAF3_POST, 64),'-'	);
			$s_state			= $this->valstr(dvdaf3_getvalue('state'				,DVDAF3_POST, 32),'-'	);
			$s_country			= $this->valstr(dvdaf3_getvalue('country'			,DVDAF3_POST, 32),'-'	);
			$s_status			= $this->vala_4(dvdaf3_getvalue('status'			,DVDAF3_POST,  1),'-','S','M','R','P');
			$s_my_space			= $this->valstr(dvdaf3_getvalue('my_space'			,DVDAF3_POST, 32),'-'	); // need to validate
			$s_facebook			= $this->valstr(dvdaf3_getvalue('facebook'			,DVDAF3_POST, 32),'-'	); // need to validate
			$s_homepage			= $this->valstr(dvdaf3_getvalue('homepage'			,DVDAF3_POST,128),'-'	); // need to validate
			$s_about_me			= $this->valstr(dvdaf3_getvalue('about_me'			,DVDAF3_POST,600),'-'	);
			$s_youtube			= $this->valstr(dvdaf3_getvalue('youtube'			,DVDAF3_POST,255),'-'	); // need to validate
			$s_youtube_auto_ind	= $this->vala_b(dvdaf3_getvalue('youtube_auto_ind'	,DVDAF3_POST,  1),'N','N','Y');

			$s_dash    = "'-', ";
			$s_null    = "null, ";
			$s_values  = '';
			$s_fields  = '';
			$s_values .=													"'{$this->ms_requester}', "	; $s_fields .= "user_id, "				;
			$s_values .= $s_name			 = $s_name	   == '' ? $s_dash :"'{$s_name}', "				; $s_fields .= "name, "					;
			$s_values .= $s_dob				 = $s_dob	   == '' ? $s_null :"'{$s_dob}', "				; $s_fields .= "dob, "					;
			$s_values .= $s_gender			 =								"'{$s_gender}', "			; $s_fields .= "gender, "				;
			$s_values .= $s_city			 = $s_city	   == '' ? $s_dash :"'{$s_city}', "				; $s_fields .= "city, "					;
			$s_values .= $s_state			 = $s_state	   == '' ? $s_dash :"'{$s_state}', "			; $s_fields .= "state, "				;
			$s_values .= $s_country			 = $s_country  == '' ? $s_dash :"'{$s_country}', "			; $s_fields .= "country, "				;
			$s_values .= $s_status			 =								"'{$s_status}', "			; $s_fields .= "status, "				;
			$s_values .= $s_my_space		 = $s_my_space == '' ? $s_dash :"'{$s_my_space}', "			; $s_fields .= "my_space_id, "			;
			$s_values .= $s_facebook		 = $s_facebook == '' ? $s_dash :"'{$s_facebook}', "			; $s_fields .= "facebook_id, "			;
			$s_values .= $s_homepage = strlen($s_homepage) < 10	 ? $s_dash :"'{$s_homepage}', "			; $s_fields .= "homepage, "				;
			$s_values .= $s_about_me		 = $s_about_me == '' ? $s_dash :"'{$s_about_me}', "			; $s_fields .= "about_me, "				;
			$s_values .= $s_youtube			 = $s_youtube  == '' ? $s_dash :"'{$s_youtube}', "			; $s_fields .= "youtube_id, "			;
			$s_values .= $s_youtube_auto_ind =								"'{$s_youtube_auto_ind}', "	; $s_fields .= "youtube_auto_ind, "		;
			$s_values .=													"now(), now()"				; $s_fields .= "created_tm, updated_tm"	;

			// INSERT if not EXISTS
			$ss = "INSERT into dvdaf_user_3 ({$s_fields}) VALUES ({$s_values})";

			if ( ! CSql::query_and_free($ss,CSql_IGNORE_ERROR,__FILE__,__LINE__) )
			{
				$ss =	 "UPDATE dvdaf_user_3 SET ".
								'name = '.				$s_name.
								'dob = '.				$s_dob.
								'gender = '.			$s_gender.
								'city = '.				$s_city.
								'state = '.				$s_state.
								'country = '.			$s_country.
								'status = '.			$s_status.
								'my_space_id = '.		$s_my_space.
								'facebook_id = '.		$s_facebook.
								'homepage = '.			$s_homepage.
								'about_me = '.			$s_about_me.
								'youtube_id = '.		$s_youtube.
								'youtube_auto_ind = '.	$s_youtube_auto_ind.
								'updated_tm = now() '.
						  "WHERE user_id = '{$this->ms_requester}'";
				CSql::query_and_free($ss,0,__FILE__,__LINE__);
			}
			// let it fall
		case 'get':
			$this->ms_sql = "SELECT a.user_id, a.last_visit_tm, c.photo, c.name, date_format(c.dob,'%Y-%m-%d') dob, c.gender, c.city, c.state, ".
								   "c.country, c.status, c.my_space_id, c.facebook_id, c.homepage, c.about_me, c.youtube_id, c.youtube_auto_ind, ".
								   "c.updated_tm, year(current_date()) - year(c.dob) - (right(current_date(),5) < right(date(c.dob),5)) age ".
							  "FROM dvdaf_user a LEFT JOIN dvdaf_user_3 c ON a.user_id = c.user_id ".
							 "WHERE a.user_id = '{$this->ms_user}'";
			$this->mn_max = 1;
			break;
		case 'photo':
		default:
			return $this->on_error("Unrecognized request: ".__LINE__);
		}
		return true;
	}
	function formatLine(&$row)
	{
		if ( $row['user_id'			] == '-' ) $row['user_id'			] = '';
		if ( $row['photo'			] == '-' ) $row['photo'				] = '';
		if ( $row['name'			] == '-' ) $row['name'				] = '';
		if ( $row['gender'			] == '-' ) $row['gender'			] = '';
		if ( $row['city'			] == '-' ) $row['city'				] = '';
		if ( $row['state'			] == '-' ) $row['state'				] = '';
		if ( $row['country'			] == '-' ) $row['country'			] = '';
		if ( $row['status'			] == '-' ) $row['status'			] = '';
		if ( $row['my_space_id'		] == '-' ) $row['my_space_id'		] = '';
		if ( $row['facebook_id'		] == '-' ) $row['facebook_id'		] = '';
		if ( $row['homepage'		] == '-' ) $row['homepage'			] = '';
		if ( $row['about_me'		] == '-' ) $row['about_me'			] = '';
		if ( $row['youtube_id'		] == '-' ) $row['youtube_id'		] = '';
		if ( $row['youtube_auto_ind'] == '-' ) $row['youtube_auto_ind'	] = '';
		if ( $row['age'				] == '-' ) $row['age'				] = '';

	return			  "user_id\t"			.$row['user_id'			 ].
					"\tlast_visit_tm\t"		.$row['last_visit_tm'	 ].
					"\tphoto\t"				.$row['photo'			 ].
					"\tname\t"				.$row['name'			 ].
  ($this->mb_self ? "\tdob\t"				.$row['dob'				 ] : "\t\t" ).
					"\tgender\t"			.$row['gender'			 ].
					"\tcity\t"				.$row['city'			 ].
					"\tstate\t"				.$row['state'			 ].
					"\tcountry\t"			.$row['country'			 ].
					"\tstatus\t"			.$row['status'			 ].
					"\tmy_space\t"			.$row['my_space_id'		 ].
					"\tfacebook\t"			.$row['facebook_id'		 ].
					"\thomepage\t"			.$row['homepage'		 ].
					"\tabout_me\t"			.$row['about_me'		 ].
					"\tyoutube\t"			.$row['youtube_id'		 ].
					"\tyoutube_auto_ind\t"	.$row['youtube_auto_ind' ].
					"\tage\t"				.$row['age'				 ].
					"\tupdated_tm\t"		.$row['updated_tm'		 ].
					"\n";
	}
}

define('CBlog_GET'	, 0);
define('CBlog_POST'	, 1);
define('CBlog_EDIT'	, 2);
define('CBlog_REPLY', 3);
define('CBlog_DEL'	, 4);

class CBlog extends CAjax
{
	// ?mode=blog&what=del&post=9&user=ash
	// ?mode=blog&what=set&user=ash
	// ?mode=wall&what=del&post=3&user=ash
	// ?mode=wall&what=set&user=ash
	function getSql()
	{
		$this->get_requester(); // $this->ms_requester									// the person issuing the request
		$this->ms_user		= dvdaf3_getvalue('user',DVDAF3_GET|DVDAF3_LOWER);			// part of db index of the data we are (changing if any)
		$this->ms_view		= dvdaf3_getvalue('view',DVDAF3_GET|DVDAF3_LOWER);			// whose pare are we looking at
		$this->mb_self		= $this->ms_user && $this->ms_user == $this->ms_requester;	// am I changing my own data?

		$this->ms_what		= dvdaf3_getvalue('what',DVDAF3_GET|DVDAF3_LOWER);
		$this->mn_page		= dvdaf3_getvalue('page',DVDAF3_GET|DVDAF3_INT);
		$n_facebook			= dvdaf3_getvalue('fb'  ,DVDAF3_POST|DVDAF3_INT);
		$this->ms_context	= ( $this->ms_mode	!= '' ? "mode='{$this->ms_mode}' " : '').
							  ( $this->ms_what	!= '' ? "what='{$this->ms_what}' " : '').
							  ( $this->mn_page		  ? "page='{$this->mn_page}' " : '').
							  ( $this->ms_user	!= '' ? "user='{$this->ms_user}' " : '').
							  ( $this->ms_view	!= '' ? "view='{$this->ms_view}' " : ''). "fb='{$n_facebook}' ";
		$this->mb_assoc		= true;
		$this->mn_prev_id	= 0;
		$this->mn_action	= CBlog_GET;
		$this->mb_has_more	= false;

		if ( $this->mn_page <= 0 )
			$this->mn_page = 1;

		if ( $this->mn_page <= 0 )
			$this->mn_page = 1;

		switch ( $this->ms_what )
		{
		case 'set':
			$s_user_id		= $this->ms_user;
			$s_location		= $this->ms_mode == 'wall' ? 'W' : 'B';
			$n_blog_id		= $this->valint(dvdaf3_getvalue('blog_id'	,DVDAF3_POST|DVDAF3_INT			)	 );
			$n_reply_num	= $this->valint(dvdaf3_getvalue('reply_num'	,DVDAF3_POST|DVDAF3_INT			)	 );
			$n_pic_id		= 0;	// $this->valint(dvdaf3_getvalue('pic_id'	,DVDAF3_POST|DVDAF3_INT	)	 ); // need to validate
			$s_pic_name		= '-';	// $this->valstr(dvdaf3_getvalue('pic_name'	,DVDAF3_POST		, 40),'-'); // need to validate
			$s_pic_source	= $this->valstr(dvdaf3_getvalue('pic_source',DVDAF3_POST				,  1),'-'); // need to validate
			$n_obj_id		= $this->valint(dvdaf3_getvalue('obj_id'	,DVDAF3_POST|DVDAF3_INT			)	 ); // need to validate
			$s_obj_type		= $this->valstr(dvdaf3_getvalue('obj_type'	,DVDAF3_POST				,  1),'-'); // need to validate
			$s_youtube_id	= $this->valstr(dvdaf3_getvalue('youtube_id',DVDAF3_POST				, 16),'-'); // need to validate
			$s_blog			= $this->valstr(dvdaf3_getvalue('blog'		,DVDAF3_POST				,600),'-');
			$s_version		= $this->valstr(dvdaf3_getvalue('version'	,DVDAF3_POST|DVDAF3_LOWER	,  3),'-'); // need to validate
			$b_reply		= $this->valint(dvdaf3_getvalue('reply'		,DVDAF3_POST|DVDAF3_BOOLEAN		)	 ) && $n_blog_id > 0;
			$b_edit			= $n_blog_id > 0 && ! $b_reply;
			$this->mn_action= $b_reply ? CBlog_REPLY : ($b_edit ? CBlog_EDIT : CBlog_POST);
			$b_update_pic	= true;
			$b_reply_cntr	= false;
			$b_thread_tm	= false;

			if ( $this->mn_action == CBlog_POST )
			{
				switch ( $this->ms_mode )
				{
				case 'wall': break;
				case 'blog': if ( ! $this->mb_self ) return $this->on_error("One cannot post to someone else's blog: ".__LINE__); break;
				default:	 return $this->on_error("Unrecognized request: ".__LINE__);
				}
			}

			if ( $s_version != 'v1' ) return $this->on_error("Unrecognized request format: ".__LINE__);

			$b_good_pic	= false;
			if ( $s_pic_source == 'D' && $n_obj_id > 0 && $s_obj_type == 'D' )
			{
				if ( $b_edit )
				{
					$n_prev_obj_id = CSql::query_and_fetch1("SELECT obj_id ".
															  "FROM microblog ".
															 "WHERE user_id = '{$s_user_id}' ".
															   "and location = '{$s_location}' ".
															   "and blog_id = {$n_blog_id} ".
															   "and reply_num = {$n_reply_num} ".
															   "and pic_source = 'D' ".
															   "and obj_type = 'D'",0,__FILE__,__LINE__);
					$b_update_pic = ! $n_prev_obj_id || $n_prev_obj_id != $n_obj_id;
				}

				if ( $b_update_pic )
				{
					$s_pic_name = CSql::query_and_fetch1("SELECT pic_name FROM dvd WHERE dvd_id = {$n_obj_id}",0,__FILE__,__LINE__);
					if ( $s_pic_name && $s_pic_name != '-' )
					{
						if ( ($n = strpos($s_pic_name, '-')) > 3 )
						{
							$s_pic_name	= 'p0/'. substr($s_pic_name, $n - 3, 3). '/'. $s_pic_name. '.gif';
							$b_good_pic	= true;
						}
					}
				}
			}
			if ( ! $b_good_pic )
			{
				$s_pic_source = '-';
				$n_obj_id	  = 0;
				$s_obj_type	  = '-';
			}

			if ( ! preg_match('/^[0-9a-zA-Z_-]{9,12}$/', $s_youtube_id) )
				$s_youtube_id = '-';

			$b_update_stats = false;
			switch ( $this->mn_action )
			{
			case CBlog_POST:
				// post if self or a friend
				$ss = "INSERT INTO microblog (user_id, location, blog_id, pic_id, pic_source, pic_name, youtube_id, ".
							 "obj_id, obj_type, blog, thread_tm, updated_tm, created_by, created_tm) ".
					  "SELECT '{$s_user_id}', '{$s_location}', {$n_blog_id}, {$n_pic_id}, '{$s_pic_source}', '{$s_pic_name}', '{$s_youtube_id}', ".
							 "{$n_obj_id}, '{$s_obj_type}', '{$s_blog}', now(), now(), '{$this->ms_requester}', now() ".
						"FROM one ". ($this->ms_requester != $s_user_id ?
					   "WHERE exists (SELECT * FROM friend f WHERE f.user_id = '{$s_user_id}' and f.friend_id = '{$this->ms_requester}')" : '');
				$b_update_stats = true;
				break;
			case CBlog_EDIT:
				// edit if creator
				$ss = "UPDATE microblog ".
						 "SET blog = '{$s_blog}', updated_tm = now(), youtube_id = '{$s_youtube_id}', ". ($b_update_pic ?
							 "pic_id = {$n_pic_id}, pic_source = '{$s_pic_source}', pic_name = '{$s_pic_name}', ".
							 "obj_id = {$n_obj_id}, obj_type = '{$s_obj_type}', " : '');

				$ss = substr($ss, 0, -2). ' '.
					   "WHERE user_id = '{$s_user_id}' ".
						 "and location = '{$s_location}' ".
						 "and blog_id = {$n_blog_id} ".
						 "and reply_num = {$n_reply_num} ".
						 "and created_by = '{$this->ms_requester}'";

				$b_thread_tm	= true;
				$b_update_stats = true;
				break;
			case CBlog_REPLY:
				// reply if self or a friend
				$ss = "INSERT INTO microblog (user_id, location, blog_id, reply_num, pic_id, pic_source, pic_name, youtube_id, ".
							 "obj_id, obj_type, blog, thread_tm, updated_tm, created_by, created_tm) ".
					  "SELECT '{$s_user_id}', '{$s_location}', {$n_blog_id}, max(b.reply_num)+1, {$n_pic_id}, '{$s_pic_source}', '{$s_pic_name}', '{$s_youtube_id}', ".
							 "{$n_obj_id}, '{$s_obj_type}', '{$s_blog}', now(), now(), '{$this->ms_requester}', now() ".
						"FROM microblog b ".
					   "WHERE user_id = '{$s_user_id}' ".
						 "and location = '{$s_location}' ".
						 "and blog_id = {$n_blog_id} ". ($this->ms_requester != $s_user_id ?
						 "and exists (SELECT * FROM friend f WHERE b.user_id = f.user_id and f.friend_id = '{$this->ms_requester}')".
						 "and not exists (SELECT * FROM dvdaf_user_3 u WHERE u.user_id = '{$s_user_id}' and microblog_reply_ind = 'N')" : '');
				$b_reply_cntr	= true;
				$b_thread_tm	= true;
				$b_update_stats = true;
				break;
			}

			CSql::query_and_free($ss,0,__FILE__,__LINE__);

			if ( $b_update_stats )
				CSql::query_and_free("UPDATE dvdaf_user_2 SET last_post_tm = now() WHERE user_id = '{$this->ms_requester}'",0,__FILE__,__LINE__);

			if ( $b_reply_cntr )
				$this->updateReplyCounter($s_user_id, $s_location, $n_blog_id);
			if ( $b_thread_tm )
				$this->updateThreadTime($s_user_id, $s_location, $n_blog_id);
			break;
		case 'del':
			$s_user_id		 = $this->ms_user;
			$s_location		 = $this->ms_mode == 'wall' ? 'W' : 'B';
			$n_blog_id		 = $this->valint(dvdaf3_getvalue('blog_id'	,DVDAF3_GET|DVDAF3_INT));
			$n_reply_num	 = $this->valint(dvdaf3_getvalue('reply_num',DVDAF3_GET|DVDAF3_INT));
			$n_page			 = $this->valint(dvdaf3_getvalue('page'		,DVDAF3_GET|DVDAF3_INT));
			$this->mn_action = CBlog_DEL;

			if ( $n_reply_num )
			{
				$ss = "DELETE FROM microblog ".
					   "WHERE user_id = '{$s_user_id}' and location = '{$s_location}' and blog_id = {$n_blog_id} ".
						 "and reply_num = {$n_reply_num} ".
						 "and (created_by = '{$this->ms_requester}' or user_id = '{$this->ms_requester}')";
				CSql::query_and_free($ss,0,__FILE__,__LINE__);
				$this->updateReplyCounter($s_user_id, $s_location, $n_blog_id);
			}
			else
			{
				if ( $s_user_id == $this->ms_requester )
				{
					$ss = "DELETE FROM microblog ".
						   "WHERE location = '{$s_location}' and blog_id = {$n_blog_id} ".
							 "and user_id = '{$this->ms_requester}'";
					CSql::query_and_free($ss,0,__FILE__,__LINE__);
				}
			}
			break;
		case 'get':
			$this->mn_action	= CBlog_GET;
			// all do this
			break;
		default:
			return $this->on_error("Unrecognized request: ".__LINE__);
		}

		// ---------------------------------------------------------------------------------
		// Compose response SQL

		$s_select = "b.user_id, b.location, b.blog_id, b.reply_num, b.pic_id, b.pic_source, b.pic_name, ".
					"b.youtube_id, b.obj_id, b.obj_type, b.blog, b.reply_count, b.created_by, b.created_tm, ".
					"b.updated_tm, a.media_type";

		// If being posted from another window, return only the key of the new entry
		if ( $this->ms_view == '' )
		{
			// putting media type under name so that we can use the same encoder
			$this->ms_sql = "SELECT {$s_select}, '' name, 0 post_age, '' is_user_id_friend, 'N' friend_reply ".
							  "FROM microblog b ".
							  "LEFT JOIN dvd a ON a.dvd_id = b.obj_id and b.obj_type = 'D' ".
							 "WHERE b.user_id = '{$s_user_id}' ".
							   "and b.created_by = '{$s_user_id}' ".
							   "and b.location = 'B' ".
							 "ORDER BY b.created_tm DESC ".
							 "LIMIT 1";
//			$this->log_debug(false);
//			$this->log_debug($this->ms_sql);
			return true;
		}

		$s_select .= ", x.name, datediff(now(),b.created_tm) post_age";

		switch ( $this->ms_mode )
		{
		case 'wall':
			$s_from  = "microblog b";
			$s_where = "b.user_id = '{$this->ms_view}' and b.location = 'W'";
			break;
		case 'blog':
			$s_from  = "microblog b";
			$s_where = "b.user_id = '{$this->ms_view}' and b.location = 'B'";
			break;
		case 'updates':
			$s_from  = "friend f JOIN microblog b ON f.friend_id = b.user_id";
			$s_where = "f.user_id = '{$this->ms_view}' and b.location = 'B'";
			break;
		default:
			return false;
		}
		$s_from .= " LEFT JOIN dvd a ON a.dvd_id = b.obj_id and b.obj_type = 'D'";

		if ( $this->mn_action == CBlog_DEL && $n_reply_num )
			$this->mn_action = CBlog_REPLY;

		switch ( $this->mn_action )
		{
		case CBlog_EDIT:
			// only return the post edited
			$this->mn_max = 1;
			$s_from		.= " LEFT JOIN dvdaf_user_3 x ON b.created_by = x.user_id";
			$s_where	.= " and b.blog_id = {$n_blog_id} and reply_num = {$n_reply_num}";
			$s_sort		 = "b.reply_num";
			break;
		case CBlog_REPLY:
			// return post and replies. Post will be ignored but it is needed in order to identify situations where there are no replies left
			$this->mn_max = 1000;
			$s_from		.= " LEFT JOIN dvdaf_user_3 x ON b.created_by = x.user_id";
			$s_where	.= " and b.user_id = '{$s_user_id}' and b.blog_id = {$n_blog_id}";
			$s_sort		 = "b.reply_num";
			break;
		default:
			// this would be the normal get
			$this->mn_max = 10;
			$n_min		= ($this->mn_page - 1) * $this->mn_max;
			$n_max		= $n_min + $this->mn_max + 1;
			// Add 1 and do not return when it gets to 11
			$this->mn_max++;
			$n_max++;
			$s_subq		 = "SELECT b.user_id, b.location, b.blog_id, b.thread_tm FROM {$s_from} WHERE {$s_where} and b.reply_num = 0 ORDER BY b.thread_tm DESC LIMIT " . ($n_min ? "{$n_min}, " : '') . "{$n_max}";
			$s_from		.= " JOIN ({$s_subq}) z ON z.user_id = b.user_id and z.location = b.location and z.blog_id = b.blog_id".
						   " LEFT JOIN dvdaf_user_3 x ON b.created_by = x.user_id";
			$s_sort		 = "z.thread_tm DESC, b.reply_num";
			break;
		}

		if ( $this->ms_view == 'guest' || ($this->mb_self && $this->ms_mode != 'updates' ) )
		{
			$s_select .= ", '' is_user_id_friend, 'N' friend_reply";
		}
		else
		{
			$s_select	.= ", us.friend_id is_user_id_friend, y.microblog_reply_ind friend_reply";
			$s_from		.= " LEFT JOIN friend us ON us.user_id = b.user_id and us.friend_id = '{$this->ms_requester}'".
						   " LEFT JOIN dvdaf_user_3 y ON y.user_id = b.user_id";
		}

		$this->ms_sql = "SELECT {$s_select} FROM {$s_from} WHERE {$s_where} ORDER BY {$s_sort}";
		return true;
	}
	function updateReplyCounter($s_user_id, $s_location, $n_blog_id)
	{
		$ss = "UPDATE microblog ".
				 "SET reply_count = (SELECT a.cnt ".
									  "FROM (SELECT count(*) cnt ".
											  "FROM microblog ".
											 "WHERE user_id = '{$s_user_id}' and location = '{$s_location}' and blog_id = {$n_blog_id} and reply_num > 0".
										   ") a".
								   ") ".
			   "WHERE user_id = '{$s_user_id}' and location = '{$s_location}' and blog_id = {$n_blog_id} and reply_num = 0";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);
	}
	function updateThreadTime($s_user_id, $s_location, $n_blog_id)
	{
		$ss = "UPDATE microblog ".
				 "SET thread_tm = now() ".
			   "WHERE user_id = '{$s_user_id}' and location = '{$s_location}' and blog_id = {$n_blog_id}";
		CSql::query_and_free($ss,0,__FILE__,__LINE__);
	}
	function formatLine(&$row)
	{
		if ( $this->mn_prev_id == $row['blog_id'] )
			$this->mn_count--;
		else
			$this->mn_prev_id = $row['blog_id'];

		switch ( $this->mn_action )
		{
		case CBlog_EDIT:
		case CBlog_REPLY:
			break;
		default:
			if ( $this->mn_count > 10 )
			{
				$this->mb_has_more = true;
				return;
			}
			break;
		}

		if ( $row['user_id'			 ] == '-' ) $row['user_id'			] = '';
		if ( $row['location'		 ] == '-' ) $row['location'			] = '';
		if ( $row['pic_source'		 ] == '-' ) $row['pic_source'		] = '';
		if ( $row['pic_name'		 ] == '-' ) $row['pic_name'			] = '';
		if ( $row['youtube_id'		 ] == '-' ) $row['youtube_id'		] = '';
		if ( $row['obj_type'		 ] == '-' ) $row['obj_type'			] = '';
		if ( $row['blog'			 ] == '-' ) $row['blog'				] = '';
		if ( $row['created_by'		 ] == '-' ) $row['created_by'		] = '';
		if ( $row['name'			 ] == '-' ) $row['name'				] = '';
		if ( $row['is_user_id_friend'] == '-' ) $row['is_user_id_friend'] = '';
		if ( $row['friend_reply'	 ] == '-' ) $row['friend_reply'		] = '';

		$m = $row['media_type'];
		$m = strpos("23BR",$m) !== false ? 'b' : (strpos("EFLNS",$m) !== false ? 'f' : 'd');

		return		  "mode\t"				.$this->ms_mode			  .
					"\tuser_id\t"			.$row['user_id'			 ].
					"\tlocation\t"			.$row['location'		 ].
					"\tblog_id\t"			.$row['blog_id'			 ].
					"\treply_num\t"			.$row['reply_num'		 ].
					"\tpic_id\t"			.$row['pic_id'			 ].
					"\tpic_source\t"		.$row['pic_source'		 ].
					"\tpic_name\t"			.$row['pic_name'		 ].
					"\tyoutube_id\t"		.$row['youtube_id'		 ].
					"\tobj_id\t"			.$row['obj_id'			 ].
					"\tobj_type\t"			.$row['obj_type'		 ].
					"\tblog\t"				.$row['blog'			 ].
					"\treply_count\t"		.$row['reply_count'		 ].
					"\tcreated_by\t"		.$row['created_by'		 ].
					"\tcreated_tm\t"		.$row['created_tm'		 ].
					"\tupdated_tm\t"		.$row['updated_tm'		 ].
					"\tname\t"				.$row['name'			 ].
					"\tpost_age\t"			.$row['post_age'		 ].
					"\tis_user_id_friend\t"	.$row['is_user_id_friend'].
					"\tfriend_reply\t"		.$row['friend_reply'	 ].
					"\tmedia_type\t"		.$m.
					"\n";
	}
	function done()
	{
		switch ( $this->mn_action )
		{
		case CBlog_EDIT:  $refresh = 'msg';	break;
		case CBlog_REPLY: $refresh = 'replies';	break;
		default:		  $refresh = 'widget';	break;
		}

		$this->ms_ajax =  "mode\t"		.$this->ms_mode.
						"\trefresh\t"	.$refresh.
						"\tpage\t"		.$this->mn_page.
						"\tlast\t"		.($this->mb_over_max || $this->mb_has_more ? '0' : '1').
						"\n"			.$this->ms_ajax;
	}
}

class CFavVideos extends CBlog
{
	function getSql()
	{
		$this->get_requester(); // $this->ms_requester									// the person issuing the request
		$this->ms_what		= dvdaf3_getvalue('what'  ,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_user		= dvdaf3_getvalue('user'  ,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_view		= dvdaf3_getvalue('view'  ,DVDAF3_GET|DVDAF3_LOWER);
		$this->mn_cat_id	= dvdaf3_getvalue('cat_id',DVDAF3_GET|DVDAF3_INT);
		$this->mn_page		= dvdaf3_getvalue('page'  ,DVDAF3_GET|DVDAF3_INT);
		$this->ms_target	= dvdaf3_getvalue('target',DVDAF3_GET);
		$this->mb_self		= $this->ms_user && $this->ms_user == $this->ms_requester;	// am I changing my own data?
		$this->mb_assoc		= true;
		$this->mb_has_more	= false;

		$this->ms_context	= ( $this->ms_mode	  != ''	? "mode='{$this->ms_mode}' "	 : '').
							  ( $this->ms_what	  != ''	? "what='{$this->ms_what}' "	 : '').
							  ( $this->mn_page			? "page='{$this->mn_page}' "	 : '').
							  (							  "cat_id='{$this->mn_cat_id}' "	 ).
							  ( $this->ms_target  != ''	? "target='{$this->ms_target}' " : '').
							  ( $this->ms_user	  != ''	? "user='{$this->ms_user}' "	 : '').
							  ( $this->ms_view	  != ''	? "view='{$this->ms_view}' "	 : '');

		switch ( $this->ms_what )
		{
		case 'set':
			$s_user_id		 = $this->ms_user;
			$n_cat_id		 = $this->mn_cat_id;
			$n_blog_id		 = $this->valint(dvdaf3_getvalue('blog_id'   ,DVDAF3_POST|DVDAF3_INT		)    );
			$s_youtube_id	 = $this->valstr(dvdaf3_getvalue('youtube_id',DVDAF3_POST				, 16),'-'); // need to validate
			$s_version		 = $this->valstr(dvdaf3_getvalue('version'   ,DVDAF3_POST|DVDAF3_LOWER	,  3),'-'); // need to validate
			$this->mn_action = $n_blog_id > 0 ? CBlog_EDIT : CBlog_POST;

			if ( ! $this->mb_self										) return $this->on_error("One cannot post to someone else's video favorites: ".__LINE__);
			if ( $s_version != 'v1'										) return $this->on_error("Unrecognized request format: ".__LINE__);
			if ( ! preg_match('/^[0-9a-zA-Z_-]{9,12}$/', $s_youtube_id)	) return $this->on_error("Unrecognized youtube id: ".__LINE__);

			switch ( $this->mn_action )
			{
			case CBlog_POST:
				// post if self
				$ss = "INSERT INTO microblog (user_id, location, cat_id, youtube_id, thread_tm, updated_tm, created_by, created_tm) ".
					  "SELECT '{$s_user_id}', 'V', {$n_cat_id}, '{$s_youtube_id}', now(), now(), '{$this->ms_requester}', now() ".
					    "FROM one ".
					   "WHERE {$n_cat_id} = 0 or exists (SELECT 1 FROM my_vid_category WHERE user_id = '{$s_user_id}' and cat_id = {$n_cat_id})";
				break;
			case CBlog_EDIT:
				// edit if creator
				$ss = "UPDATE microblog ".
						 "SET cat_id = {$n_cat_id}, youtube_id = '{$s_youtube_id}', thread_tm = now(), updated_tm = now() ".
					   "WHERE user_id = '{$s_user_id}' ".
						 "and location = 'V' ".
						 "and blog_id = {$n_blog_id} ".
						 "and reply_num = 0 ".
						 "and created_by = '{$this->ms_requester}' ".
						 "and ({$n_cat_id} = 0 or exists (SELECT 1 FROM my_vid_category WHERE user_id = '{$s_user_id}' and cat_id = {$n_cat_id}))";
				break;
			}
			CSql::query_and_free($ss,0,__FILE__,__LINE__);
			break;

		case 'del':
			$s_user_id		 = $this->ms_user;
			$n_blog_id		 = $this->valint(dvdaf3_getvalue('blog_id',DVDAF3_POST|DVDAF3_INT));
			$this->mn_action = CBlog_DEL;

			if ( $s_user_id == $this->ms_requester )
			{
				$ss = "DELETE FROM microblog ".
					   "WHERE location = 'V' and blog_id = {$n_blog_id} ".
						 "and user_id = '{$this->ms_requester}'";
				CSql::query_and_free($ss,0,__FILE__,__LINE__);
			}
			break;

		case 'get':
			$this->mn_action = CBlog_GET;
			break;

		default:
			return $this->on_error("Unrecognized request: ".__LINE__);
		}

		// ---------------------------------------------------------------------------------
		// Compose response SQL
		$s_where	  = "WHERE b.user_id = '{$this->ms_view}' and b.location = 'V' ". ($this->mn_cat_id >= 0 ? "and b.cat_id = {$this->mn_cat_id} " : '');
		$n_total	  = CSql::query_and_fetch1("SELECT count(*) FROM microblog b ". $s_where,0,__FILE__,__LINE__);

		if ( $n_total > 0 )
		{
			if ( $this->mn_page > $n_total )
				$this->mn_page = $n_total;
			$n_min		  = $this->mn_page - 1;
			$n_max		  = $n_min + 1;
			$this->mn_max = 1;
			$this->ms_sql = "SELECT b.user_id, b.blog_id, {$this->mn_cat_id} cat_id, b.youtube_id, b.created_tm, b.updated_tm, {$this->mn_page} page, {$n_total} total, c.cat_name ".
							  "FROM microblog b LEFT JOIN my_vid_category c ON c.cat_id = {$this->mn_cat_id} and b.user_id = c.user_id ". $s_where .
							 "ORDER by b.sort_order, b.thread_tm DESC, b.blog_id DESC ".
							 "LIMIT ". ($n_min ? "{$n_min}, " : '') . "{$n_max}";
		}
		else
		{
			$this->mn_page = 1;
			$this->mn_max  = 1;
			$this->ms_sql  = "SELECT '{$this->ms_view}' user_id, 0 blog_id, {$this->mn_cat_id} cat_id, '' youtube_id, '' created_tm, '' updated_tm, {$this->mn_page} page, {$n_total} total, c.cat_name ".
							   "FROM one LEFT JOIN my_vid_category c ON c.cat_id = {$this->mn_cat_id} and c.user_id = '{$this->ms_view}'";
		}

		return true;
	}
	function formatLine(&$row)
	{
		if ( $row['user_id'		] == '-' ) $row['user_id'		] = '';
		if ( $row['youtube_id'	] == '-' ) $row['youtube_id'	] = '';

		return		  "mode\t"				.$this->ms_mode		 .
					"\tuser_id\t"			.$row['user_id'		].
					"\tblog_id\t"			.$row['blog_id'		].
					"\tcat_id\t"			.$row['cat_id'		].
					"\tcat_name\t"			.$row['cat_name'	].
					"\tyoutube_id\t"		.$row['youtube_id'	].
					"\tcreated_tm\t"		.$row['created_tm'	].
					"\tupdated_tm\t"		.$row['updated_tm'	].
					"\tpage\t"				.$row['page'		].
					"\ttotal\t"				.$row['total'		].
					"\n";
	}
	function done()
	{
	}
}

class CSetGet extends CAjax
{
	// ?mode=set&what=microblog_reply_ind&val=Y / N
	function getSql()
	{
		$this->get_requester();

		$this->ms_what	  = dvdaf3_getvalue('what'  ,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_context = ( $this->ms_mode   != '' ? "mode='{$this->ms_mode}' " : '').
							( $this->ms_what   != '' ? "what='{$this->ms_what}' " : '');
		$this->mb_assoc	  = true;
		$this->mb_set	  = $this->ms_mode == 'set';

		if ( $this->ms_requester != 'guest' )
		{
			switch ( $this->ms_what )
			{
			case 'microblog_reply_ind':
				if ( $this->mb_set )
				{
					$s_reply = dvdaf3_getvalue('val',DVDAF3_GET|DVDAF3_BOOLEAN) ? 'Y' : 'N';
					if ( ! CSql::query_and_free("INSERT into dvdaf_user_3 (user_id, microblog_reply_ind) VALUES ('{$this->ms_requester}', '{$s_reply}')",0,__FILE__,__LINE__) )
						CSql::query_and_free("UPDATE dvdaf_user_3 SET microblog_reply_ind = '{$s_reply}' WHERE user_id = '{$this->ms_requester}'",0,__FILE__,__LINE__);
				}
				break;
			default:
				return $this->on_error("Unrecognized request: ".__LINE__);
			}
			$this->ms_sql = "SELECT user_id, {$this->ms_what} FROM dvdaf_user_3 WHERE user_id = '{$this->ms_requester}'";
			return true;
		}
		return $this->on_error("Unrecognized request: ".__LINE__);
	}
	function formatLine(&$row)
	{
		return	  "user_id\t"			.$row['user_id'		].
				"\t{$this->ms_what}\t"	.$row[$this->ms_what].
				"\n";
	}
}

switch ( dvdaf3_getvalue('mode',DVDAF3_GET|DVDAF3_LOWER) )
{
case 'countdvds': $a = new CCountDvds();	break;
case 'profile':	  $a = new CProfile();		break;
case 'updates':
case 'wall':
case 'blog':	  $a = new CBlog();			break;
case 'favvideos': $a = new CFavVideos();	break;
case 'get':
case 'set':		  $a = new CSetGet();		break;
default:		  $a = new CUnrecognized(); break;
}

$a->main();

//								   "youtube_loop_ind, ".
//								   "show_profile_ind, ".
//								   "show_blog_ind, show_stats_ind, show_updates_ind, show_friends_ind, show_wall_ind, ".
// $s_photo
//			$s_show_profile_ind	 = $this->vala_b(dvdaf3_getvalue('show_profile_ind'	,DVDAF3_POST,  1),'Y','N','Y');
//			$s_show_blog_ind	 = $this->vala_b(dvdaf3_getvalue('show_blog_ind'	,DVDAF3_POST,  1),'Y','N','Y');
//			$s_show_stats_ind	 = $this->vala_b(dvdaf3_getvalue('show_stats_ind'	,DVDAF3_POST,  1),'Y','N','Y');
//			$s_show_updates_ind	 = $this->vala_b(dvdaf3_getvalue('show_updates_ind'	,DVDAF3_POST,  1),'Y','N','Y');
//			$s_show_friends_ind	 = $this->vala_b(dvdaf3_getvalue('show_friends_ind'	,DVDAF3_POST,  1),'Y','N','Y');
//			$s_show_wall_ind	 = $this->vala_b(dvdaf3_getvalue('show_wall_ind'	,DVDAF3_POST,  1),'Y','N','Y');
//			$s_youtube_loop_ind	 = $this->vala_b(dvdaf3_getvalue('youtube_loop_ind'	,DVDAF3_POST,  1),'N','N','Y');
//			$s_values .= $s_show_profile_ind	= "'{$s_show_profile_ind}', "	; $s_fields .= "show_profile_ind, "	;
//			$s_values .= $s_show_blog_ind		= "'{$s_show_blog_ind}', "		; $s_fields .= "show_blog_ind, "	;
//			$s_values .= $s_show_stats_ind		= "'{$s_show_stats_ind}', "		; $s_fields .= "show_stats_ind, "	;
//			$s_values .= $s_show_updates_ind	= "'{$s_show_updates_ind}', "	; $s_fields .= "show_updates_ind, "	;
//			$s_values .= $s_show_friends_ind	= "'{$s_show_friends_ind}', "	; $s_fields .= "show_friends_ind, "	;
//			$s_values .= $s_show_wall_ind		= "'{$s_show_wall_ind}', "		; $s_fields .= "show_wall_ind, "	;
//			$s_values .= $s_youtube_loop_ind	= "'{$s_youtube_loop_ind}', "	; $s_fields .= "youtube_loop_ind, "	;
//								'show_profile_ind = '.	$s_show_profile_ind.
//								'show_blog_ind = '.		$s_show_blog_ind.
//								'show_stats_ind = '.	$s_show_stats_ind.
//								'show_updates_ind = '.	$s_show_updates_ind.
//								'show_friends_ind = '.	$s_show_friends_ind.
//								'show_wall_ind = '.		$s_show_wall_ind.
//								'youtube_loop_ind = '.	$s_youtube_loop_ind.
//			$ss = "SELECT concat(show_profile_ind,show_blog_ind,show_stats_ind,show_updates_ind,show_friends_ind,show_wall_ind) ".
//					"FROM dvdaf_user_3 ".
//				   "WHERE user_id = '{$this->ms_requester}'";
//			if ( ! ($s_show_old = CSql::query_and_fetch1($ss,0,__FILE__,__LINE__)) ) $s_show_old = 'YYYYYY';
//			$this->mb_refresh = $s_show_old != $s_show_profile_ind{1}.$s_show_blog_ind{1}.$s_show_stats_ind{1}.$s_show_updates_ind{1}.$s_show_friends_ind{1}.$s_show_wall_ind{1};
//		if ( $row['youtube_loop_ind'] == '-' ) $row['youtube_loop_ind'	] = '';
//		if ( $row['show_profile_ind'] == '-' ) $row['show_profile_ind'	] = '';
//		if ( $row['show_blog_ind'	] == '-' ) $row['show_blog_ind'		] = '';
//		if ( $row['show_stats_ind'	] == '-' ) $row['show_stats_ind'	] = '';
//		if ( $row['show_updates_ind'] == '-' ) $row['show_updates_ind'	] = '';
//		if ( $row['show_friends_ind'] == '-' ) $row['show_friends_ind'	] = '';
//		if ( $row['show_wall_ind'	] == '-' ) $row['show_wall_ind'		] = '';
//					"\tyoutube_loop_ind\t"	.$row['youtube_loop_ind' ].
//					"\tshow_profile_ind\t"	.$row['show_profile_ind' ].
//					"\tshow_blog_ind\t"		.$row['show_blog_ind'	 ].
//					"\tshow_stats_ind\t"	.$row['show_stats_ind'	 ].
//					"\tshow_updates_ind\t"	.$row['show_updates_ind' ].
//					"\tshow_friends_ind\t"	.$row['show_friends_ind' ].
//					"\tshow_wall_ind\t"		.$row['show_wall_ind'	 ].
//					"\trefresh\t"			.($this->mb_refresh ? '1' : '0').

?>
