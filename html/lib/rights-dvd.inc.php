<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function getDvdRights(&$obj)
{
	$n_edit			  = 0;
	$n_new			  = 0;
	$n_pic			  = 0;
	$n_used_edit	  = 0;
	$n_used_new		  = 0;
	$n_used_pic		  = 0;
	$n_moderator_cd   = $obj->mn_moderator_cd;
	$n_membership_cd  = 0; // $obj->mn_membership_cd
	$n_contributor_cd = 0; // $obj->mn_contributor_cd

	if ( ! $obj->mb_mod )
	{
		/*
		1 -   $10 donation     1.0 stars (smb1.gif) Supporting Member
		2 -   $20 donation     2.0 stars (smb3.gif) Sponsor Member
		3 -   $30 donation     2.5 stars (smb4.gif) Donor  Member
		4 -   $50 donation     3.0 stars (smb5.gif) Fellow Member
		5 - charter member     3.0 stars (smb5.gif) 
		6 -  $100 donation     3.5 stars (smb6.gif) Benefactor Member
		7 -  $200 donation     4.0 stars (smb7.gif) Patron
		8 -  $500 donation     4.5 stars (smb8.gif) Sponsor Patron
		9 - $1000 donation     5.0 stars (smb9.gif) Benefactor Patron
		*/
		switch ( $n_membership_cd )
		{
		case 3: $n_edit =   5; break;	
		case 4: $n_edit =   5; $n_new =   5; break;
		case 5: $n_edit =   5; $n_new =   5; break;
		case 6: $n_edit =  10; $n_new =  10; $n_pic =  10; break;
		case 7: $n_edit =  50; $n_new =  50; $n_pic =  50; break;
		case 8: $n_edit = 200; $n_new = 200; $n_pic = 200; break;
		case 9: $n_edit = 500; $n_new = 500; $n_pic = 500; break;
		}

		if ( $n_edit )
		{
			// To do: adjust for rejections in the past 7 days
			if ( ($rr = CSql::query("SELECT edit_title, new_title, new_picture, count(*) count ".
									  "FROM dvd_direct_update ".
									 "WHERE user_id = '{$obj->ms_user_id}' and rejected != 'N' and updated_tm > date_add(now(), INTERVAL -7 DAY) ".
									 "GROUP BY edit_title, new_title, new_picture", 0,__FILE__,__LINE__)) )
			{
				while ( ($ln = CSql::fetch($rr)) )
				{
					if ( $ln['edit_title' ] != 'N' ) $n_used_edit += intval($ln['count']);
					if ( $ln['new_title'  ] != 'N' ) $n_used_new  += intval($ln['count']);
					if ( $ln['new_picture'] != 'N' ) $n_used_pic  += intval($ln['count']);
				}
				CSql::free($rr);
			}

			// To do: adjust for updates done in the last 24hrs
			if ( ($rr = CSql::query("SELECT edit_title, new_title, new_picture, count(*) count ".
									  "FROM dvd_direct_update ".
									 "WHERE user_id = '{$obj->ms_user_id}' and rejected = 'N' and updated_tm > date_add(now(), INTERVAL -1 DAY) ".
									 "GROUP BY edit_title, new_title, new_picture", 0,__FILE__,__LINE__)) )
			{
				while ( ($ln = CSql::fetch($rr)) )
				{
					if ( $ln['edit_title' ] != 'N' ) $n_used_edit += intval($ln['count']);
					if ( $ln['new_title'  ] != 'N' ) $n_used_new  += intval($ln['count']);
					if ( $ln['new_picture'] != 'N' ) $n_used_pic  += intval($ln['count']);
				}
				CSql::free($rr);
			}
		}
	}
	$obj->mn_edit		= $n_edit;
	$obj->mn_new		= $n_new;
	$obj->mn_pic		= $n_pic;
	$obj->mn_used_edit	= $n_used_edit;
	$obj->mn_used_new	= $n_used_new;
	$obj->mn_used_pic	= $n_used_pic;
}

?>
