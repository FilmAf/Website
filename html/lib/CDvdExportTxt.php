<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWnd.php';

function strpad($s, $n, $d = STR_PAD_RIGHT)
{
	if ( $s === false ) $s = '';
		$n -= dvdaf3_strlen($s);
	if ( $n > 0 )
	{
		$x = str_repeat(' ', $n);
		return $d == STR_PAD_LEFT ? $x . $s : $s . $x;
	}

	return $s;
}

class CDvdExportTxt extends CWnd
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->mb_fields			= dvdaf3_getvalue('fields', DVDAF3_GET|DVDAF3_LOWER);
	}

	function drawHead()
	{
		header('Content-Type: plain/text');
		header('Content-Disposition: attachment; filename=filmaf.txt; modification-date="'.gmdate('D, d M Y H:i:s').' -0600"');
		header("Content-Transfer-Encoding: binary");
		header('Expires: Wed, 1 Jan 2012 05:00:00 GMT');		// date in the past
		header('Cache-Control: no-store, no-cache, must-revalidate');	// HTTP/1.1
		header('Cache-Control: post-check=0, pre-check=0', false);
		header("Pragma: no-cache");
	}

	function initializeMenu()	{}
	function drawBodyBeg()		{}
	function drawCornersBeg()	{}
	function drawBodyTop()		{}
	function drawBodyBottom()	{}
	function drawCornersEnd()	{}
	function drawBodyEnd()		{}

	function formatDate($s)
	{
		if ( $s == '-' || $s == '' ) return '';

		return substr($s,0,4).'-'.substr($s,4,2).'-'.substr($s,6,2);
	}

	function formatRating($n)
	{
		$n = intval($n);
		if ( $n < 0 ) return '';

		$n = $n + 1;
		$i = intval($n / 2);
		$h = $i * 2 == $n;
		return $h ? "{$i}" : "{$i}-1/2";
	}

	function formatTradeLoan($s)
	{
		return $s == 'L' ? 'On Loan' : ($s == 'T' ? 'For Trade' : '');
	}

	function drawBodyPage() // <<-------------------------------<< 7.2
	{
		if ( ! $this->mb_view_self )
		return;

		$b_a_dvd_id				= dvdaf3_getvalue('a_dvd_id'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_a_film_rel_year		= dvdaf3_getvalue('a_film_rel_year'		,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_a_region_mask		= dvdaf3_getvalue('a_region_mask'		,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_a_media_type			= dvdaf3_getvalue('a_media_type'		,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_a_publisher			= dvdaf3_getvalue('a_publisher'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_comments			= dvdaf3_getvalue('b_comments'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_owned_dd			= dvdaf3_getvalue('b_owned_dd'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_last_watched_dd	= dvdaf3_getvalue('b_last_watched_dd'	,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_user_film_rating	= dvdaf3_getvalue('b_user_film_rating'	,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_user_dvd_rating	= dvdaf3_getvalue('b_user_dvd_rating'	,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_sort_text			= dvdaf3_getvalue('b_sort_text'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_genre_overwrite	= dvdaf3_getvalue('b_genre_overwrite'	,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_pic_overwrite		= dvdaf3_getvalue('b_pic_overwrite'		,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_custom_1			= dvdaf3_getvalue('b_custom_1'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_custom_2			= dvdaf3_getvalue('b_custom_2'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_custom_3			= dvdaf3_getvalue('b_custom_3'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_custom_4			= dvdaf3_getvalue('b_custom_4'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_custom_5			= dvdaf3_getvalue('b_custom_5'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_retailer			= dvdaf3_getvalue('b_retailer'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_price_paid			= dvdaf3_getvalue('b_price_paid'		,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_order_dd			= dvdaf3_getvalue('b_order_dd'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_order_number		= dvdaf3_getvalue('b_order_number'		,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_trade_loan			= dvdaf3_getvalue('b_trade_loan'		,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_asking_price		= dvdaf3_getvalue('b_asking_price'		,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_loaned_to			= dvdaf3_getvalue('b_loaned_to'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_loan_dd			= dvdaf3_getvalue('b_loan_dd'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_return_dd			= dvdaf3_getvalue('b_return_dd'			,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_my_dvd_created_tm	= dvdaf3_getvalue('b_my_dvd_created_tm'	,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$b_b_my_dvd_updated_tm	= dvdaf3_getvalue('b_my_dvd_updated_tm'	,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$s_newline				= dvdaf3_getvalue('newline'				,DVDAF3_POST|DVDAF3_LOWER);
		$b_fixed_length			= dvdaf3_getvalue('format'				,DVDAF3_POST|DVDAF3_LOWER) == 'fix';
		$b_bars					= dvdaf3_getvalue('bars'				,DVDAF3_POST|DVDAF3_LOWER) == 'on';
		$n_title_len			= dvdaf3_getvalue('title_len'			,DVDAF3_POST|DVDAF3_INT  );

		switch ( $s_newline )
		{
		case 'cr':		$nl = "\r";   break;
		case 'lf':		$nl = "\n";   break;
		case 'crlf': default:	$nl = "\r\n"; break;
		}

		$sql				= '';
		$hdr				= '';
		$all				= array();
		$j					= 0;
		$k					= 0;
		$n_folder			= 0;
		$n_dvd_id			= 0;
		$n_dvd_title		= 0;
		$n_media			= 0;
		$n_region			= 0;
		$n_publisher		= 0;
		$n_comments			= 0;
		$n_owned_dd			= 0;
		$n_last_watched_dd	= 0;
		$n_user_film_rating	= 0;
		$n_user_dvd_rating	= 0;
		$n_sort_text		= 0;
		$n_genre_overwrite	= 0;
		$n_pic_overwrite	= 0;
		$n_custom_1			= 0;
		$n_custom_2			= 0;
		$n_custom_3			= 0;
		$n_custom_4			= 0;
		$n_custom_5			= 0;
		$n_retailer			= 0;
		$n_price_paid		= 0;
		$n_order_dd			= 0;
		$n_order_number		= 0;
		$n_trade_loan		= 0;
		$n_asking_price		= 0;
		$n_loaned_to		= 0;
		$n_loan_dd			= 0;
		$n_return_dd		= 0;
		$n_my_dvd_created_tm= 0;
		$n_my_dvd_updated_tm= 0;

		$hda = array(	'Folder',
						'DVD id',
						'DVD title',
						'Media',
						'Region',
						'Publisher',
						'Comments',
						'Owned since',
						'Last watched',
						'Film rating',
						'DVD rating',
						'Sort overwrite',
						'Genre overwrite',
						'Pic overwrite',
						'Custom #1',
						'Custom #2',
						'Custom #3',
						'Custom #4',
						'Custom #5',
						'Retailer',
						'Price paid',
						'Order date',
						'Order number',
						'Trade/loan',
						'Asking price',
						'Loaned to',
						'Loaned date',
						'Return date',
						'Added date',
						'Last moved date');

									  { $sql .= ', b.folder'			; $hdr .= "\t{$hda[ 0]}"; $n_folder				= strlen($hda[ 0])+1; }
		if ( $b_a_dvd_id			) { $sql .= ', a.dvd_id'			; $hdr .= "\t{$hda[ 1]}"; $n_dvd_id				= strlen($hda[ 1])+1; }
									  { $sql .= ', a.dvd_title'			; $hdr .= "\t{$hda[ 2]}"; $n_dvd_title			= strlen($hda[ 2])+1; }
		if ( $b_a_film_rel_year		) { $sql .= ', a.film_rel_year'		;								  									  }
									  { $sql .= ', a.media_type'		;								  									  }
		if ( $b_a_media_type		) {									  $hdr .= "\t{$hda[ 3]}"; $n_media				= strlen($hda[ 3])+1; }
		if ( $b_a_region_mask		) { $sql .= ', a.region_mask'		; $hdr .= "\t{$hda[ 4]}"; $n_region				= strlen($hda[ 4])+1; }
		if ( $b_a_publisher			) { $sql .= ', a.publisher'			; $hdr .= "\t{$hda[ 5]}"; $n_publisher			= strlen($hda[ 5])+1; }
		if ( $b_b_comments			) { $sql .= ', b.comments'			; $hdr .= "\t{$hda[ 6]}"; $n_comments			= strlen($hda[ 6])+1; }
		if ( $b_b_owned_dd			) { $sql .= ', b.owned_dd'			; $hdr .= "\t{$hda[ 7]}"; $n_owned_dd			= strlen($hda[ 7])+1; }
		if ( $b_b_last_watched_dd	) { $sql .= ', c.last_watched_dd'	; $hdr .= "\t{$hda[ 8]}"; $n_last_watched_dd	= strlen($hda[ 8])+1; }
		if ( $b_b_user_film_rating	) { $sql .= ', b.user_film_rating'	; $hdr .= "\t{$hda[ 9]}"; $n_user_film_rating	= strlen($hda[ 9])+1; }
		if ( $b_b_user_dvd_rating	) { $sql .= ', b.user_dvd_rating'	; $hdr .= "\t{$hda[10]}"; $n_user_dvd_rating	= strlen($hda[10])+1; }
		if ( $b_b_sort_text			) { $sql .= ', b.sort_text'			; $hdr .= "\t{$hda[11]}"; $n_sort_text			= strlen($hda[11])+1; }
		if ( $b_b_genre_overwrite	) { $sql .= ', b.genre_overwrite'	; $hdr .= "\t{$hda[12]}"; $n_genre_overwrite	= strlen($hda[12])+1; }
		if ( $b_b_pic_overwrite		) { $sql .= ', b.pic_overwrite'		; $hdr .= "\t{$hda[13]}"; $n_pic_overwrite		= strlen($hda[13])+1; }
		if ( $b_b_custom_1			) { $sql .= ', c.custom_1'			; $hdr .= "\t{$hda[14]}"; $n_custom_1			= strlen($hda[14])+1; }
		if ( $b_b_custom_2			) { $sql .= ', c.custom_2'			; $hdr .= "\t{$hda[15]}"; $n_custom_2			= strlen($hda[15])+1; }
		if ( $b_b_custom_3			) { $sql .= ', c.custom_3'			; $hdr .= "\t{$hda[16]}"; $n_custom_3			= strlen($hda[16])+1; }
		if ( $b_b_custom_4			) { $sql .= ', c.custom_4'			; $hdr .= "\t{$hda[17]}"; $n_custom_4			= strlen($hda[17])+1; }
		if ( $b_b_custom_5			) { $sql .= ', c.custom_5'			; $hdr .= "\t{$hda[18]}"; $n_custom_5			= strlen($hda[18])+1; }
		if ( $b_b_retailer			) { $sql .= ', c.retailer'			; $hdr .= "\t{$hda[19]}"; $n_retailer			= strlen($hda[19])+1; }
		if ( $b_b_price_paid		) { $sql .= ', c.price_paid'		; $hdr .= "\t{$hda[20]}"; $n_price_paid			= strlen($hda[20])+1; }
		if ( $b_b_order_dd			) { $sql .= ', c.order_dd'			; $hdr .= "\t{$hda[21]}"; $n_order_dd			= strlen($hda[21])+1; }
		if ( $b_b_order_number		) { $sql .= ', c.order_number'		; $hdr .= "\t{$hda[22]}"; $n_order_number		= strlen($hda[22])+1; }
		if ( $b_b_trade_loan		) { $sql .= ', c.trade_loan'		; $hdr .= "\t{$hda[23]}"; $n_trade_loan			= strlen($hda[23])+1; }
		if ( $b_b_asking_price		) { $sql .= ', c.asking_price'		; $hdr .= "\t{$hda[24]}"; $n_asking_price		= strlen($hda[24])+1; }
		if ( $b_b_loaned_to			) { $sql .= ', c.loaned_to'			; $hdr .= "\t{$hda[25]}"; $n_loaned_to			= strlen($hda[25])+1; }
		if ( $b_b_loan_dd			) { $sql .= ', c.loan_dd'			; $hdr .= "\t{$hda[26]}"; $n_loan_dd			= strlen($hda[26])+1; }
		if ( $b_b_return_dd			) { $sql .= ', c.return_dd'			; $hdr .= "\t{$hda[27]}"; $n_return_dd			= strlen($hda[27])+1; }
		if ( $b_b_my_dvd_created_tm	) { $sql .= ', b.my_dvd_created_tm'	; $hdr .= "\t{$hda[28]}"; $n_my_dvd_created_tm	= strlen($hda[28])+1; }
		if ( $b_b_my_dvd_updated_tm	) { $sql .= ', b.my_dvd_updated_tm'	; $hdr .= "\t{$hda[29]}"; $n_my_dvd_updated_tm	= strlen($hda[29])+1; }

		$b_my_dvd_2 = strpos($sql, ', c.') !== false;

		$sql = substr($sql,2);
		$hdr = substr($hdr,1);
		$sql = "SELECT $sql ".
				 "FROM v_my_dvd_ref b ".
					  ($b_my_dvd_2 ? "LEFT JOIN my_dvd_2 c ON b.dvd_id = c.dvd_id and b.user_id = c.user_id " : '').
				 "LEFT JOIN my_folder f ON b.folder = f.folder and b.user_id = f.user_id ".
				 "LEFT JOIN dvd a ON b.dvd_id = a.dvd_id ".
				"WHERE b.user_id = '{$this->ms_user_id}' ".
				  "and b.folder <> 'trash-can' ".
				"ORDER BY f.sort_category, f.sort_order, IF(b.sort_text='-',a.dvd_title_nocase,CONCAT('/ ',LOWER(b.sort_text),SUBSTRING(a.dvd_title_nocase,2))), a.film_rel_year, a.director_nocase, a.dvd_id";

		if ( $b_fixed_length )
			$all[] = $hda;
		else
			echo $hdr. $nl;

		$rr = CSql::query($sql, 0,__FILE__,__LINE__);
		$i  = 0;
		while ( $a = CSql::fetch($rr) )
		{
			$i++;
			$folder		= $a['folder'];
			$dvd_title	= $a['dvd_title'];
			$media		= $a['media_type'];
			$year		= $b_a_film_rel_year ? $a['film_rel_year'] : 0;
			$region     = $b_a_region_mask   ? $a['region_mask']   : 0;	

			if ( ($k = strpos($dvd_title, '<br')) > 0 )
				$dvd_title	= trim(substr($dvd_title, 0,$k));
			$dvd_title	= str_replace("&#39;", "'", html_entity_decode($dvd_title));
			$year		= $year ? " ({$year})" : '';
			$s			= '';
			if ( $region &   1 ) $s = '0,';
			if ( $region &   2 ) $s = '1,';
			if ( $region &   4 ) $s = '2,';
			if ( $region &   8 ) $s = '3,';
			if ( $region &  16 ) $s = '4,';
			if ( $region &  32 ) $s = '5,';
			if ( $region &  64 ) $s = '6,';
			if ( $region & 128 ) $s = 'A,';
			if ( $region & 256 ) $s = 'B,';
			if ( $region & 512 ) $s = 'C,';
			$region	= $s ? "REGION " . substr($s,0,-1) : '';
			switch ( $media )
			{
			case 'B': $media = 'Blu-ray';				 break;
			case 'H': $media = 'HD DVD';				 break;
			case 'C': $media = 'HD DVD/DVD Combo';		 break;
			case 'T': $media = 'HD DVD/DVD TWIN Format'; break;
			case 'A': $media = 'DVD Audio';				 break;
			case 'P': $media = 'Placeholder';			 break;
			case 'O': $media = 'Other';		 			 break;
			default:  $media = '';						 break;
			}

			if ( $b_fixed_length && $n_title_len > 0 )
			{
				$k = dvdaf3_strlen($dvd_title . $year . (!$b_a_media_type && $media ? " -- {$media}" : ''));
				if ( $k > $n_title_len )
				{
					$k = dvdaf3_strlen($dvd_title) - ($k - $n_title_len);
					if ( $k < 20 ) $k = 20;
					$dvd_title = dvdaf3_substr($dvd_title, 0, $k);
				}
			}
			$dvd_title .= $year . (!$b_a_media_type && $media ? " -- {$media}" : '');

			$folder				=								 "\t".($folder);
			$dvd_id				= !$b_a_dvd_id			  ? '' : "\t".($a['dvd_id']);
			$dvd_title			=								 "\t".($dvd_title);
			$media				= !$b_a_media_type		  ? '' : "\t".($media ? $media : 'DVD');
			$region				= !$b_a_region_mask		  ? '' : "\t".($region);
			$publisher			= !$b_a_publisher		  ? '' : "\t".($a['publisher']		==   '-' ? '' : str_replace("&#39;", "'", html_entity_decode($a['publisher'])));
			$comments			= !$b_b_comments		  ? '' : "\t".($a['comments']		==   '-' ? '' : str_replace('<br />', '\\', str_replace("&#39;", "'", html_entity_decode($a['comments']))));
			$owned_dd			= !$b_b_owned_dd		  ? '' : "\t".($this->formatDate    ($a['owned_dd']	   ));
			$last_watched_dd	= !$b_b_last_watched_dd	  ? '' : "\t".($this->formatDate    ($a['last_watched_dd'] ));
			$user_film_rating	= !$b_b_user_film_rating  ? '' : "\t".($this->formatRating  ($a['user_film_rating']));
			$user_dvd_rating	= !$b_b_user_dvd_rating	  ? '' : "\t".($this->formatRating  ($a['user_dvd_rating'] ));
			$sort_text			= !$b_b_sort_text		  ? '' : "\t".($a['sort_text']		==   '-' ? '' : $a['sort_text']);
			$genre_overwrite	= !$b_b_genre_overwrite	  ? '' : "\t".($a['genre_overwrite']	== 99999 ? '' : $a['genre_overwrite']);
			$pic_overwrite		= !$b_b_pic_overwrite	  ? '' : "\t".($a['pic_overwrite']	==   '-' ? '' : $a['pic_overwrite']);
			$custom_1			= !$b_b_custom_1		  ? '' : "\t".($a['custom_1']		==   '-' ? '' : $a['custom_1']);
			$custom_2			= !$b_b_custom_2		  ? '' : "\t".($a['custom_2']		==   '-' ? '' : $a['custom_2']);
			$custom_3			= !$b_b_custom_3		  ? '' : "\t".($a['custom_3']		==   '-' ? '' : $a['custom_3']);
			$custom_4			= !$b_b_custom_4		  ? '' : "\t".($a['custom_4']		==   '-' ? '' : $a['custom_4']);
			$custom_5			= !$b_b_custom_5		  ? '' : "\t".($a['custom_5']		==   '-' ? '' : $a['custom_5']);
			$retailer			= !$b_b_retailer		  ? '' : "\t".($a['retailer']		==   '-' ? '' : $a['retailer']);
			$price_paid			= !$b_b_price_paid		  ? '' : "\t".($a['price_paid']		==    -1 ? '' : $a['price_paid']);
			$order_dd			= !$b_b_order_dd		  ? '' : "\t".($this->formatDate     ($a['order_dd']	   ));
			$order_number		= !$b_b_order_number	  ? '' : "\t".($a['order_number']	==   '-' ? '' : $a['order_number']);
			$trade_loan			= !$b_b_trade_loan		  ? '' : "\t".($this->formatTradeLoan($a['trade_loan']	   ));
			$asking_price		= !$b_b_asking_price	  ? '' : "\t".($a['asking_price']	==    -1 ? '' : $a['asking_price']);
			$loaned_to			= !$b_b_loaned_to		  ? '' : "\t".($a['loaned_to']		==   '-' ? '' : $a['loaned_to']);
			$loan_dd			= !$b_b_loan_dd			  ? '' : "\t".($this->formatDate     ($a['loan_dd']	   ));
			$return_dd			= !$b_b_return_dd		  ? '' : "\t".($this->formatDate     ($a['return_dd']	   ));
			$my_dvd_created_tm	= !$b_b_my_dvd_created_tm ? '' : "\t".($a['my_dvd_created_tm']);
			$my_dvd_updated_tm	= !$b_b_my_dvd_updated_tm ? '' : "\t".($a['my_dvd_updated_tm']);

			if ( $b_fixed_length )
			{
				$all[] = array(
					substr($folder				,1),
					substr($dvd_id				,1),
					substr($dvd_title			,1),
					substr($media				,1),
					substr($region				,1),
					substr($publisher			,1),
					substr($comments			,1),
					substr($owned_dd			,1),
					substr($last_watched_dd		,1),
					substr($user_film_rating	,1),
					substr($user_dvd_rating		,1),
					substr($sort_text			,1),
					substr($genre_overwrite		,1),
					substr($pic_overwrite		,1),
					substr($custom_1			,1),
					substr($custom_2			,1),
					substr($custom_3			,1),
					substr($custom_4			,1),
					substr($custom_5			,1),
					substr($retailer			,1),
					substr($price_paid			,1),
					substr($order_dd			,1),
					substr($order_number		,1),
					substr($trade_loan			,1),
					substr($asking_price		,1),
					substr($loaned_to			,1),
					substr($loan_dd				,1),
					substr($return_dd			,1),
					substr($my_dvd_created_tm	,1),
					substr($my_dvd_updated_tm	,1));
				$n_folder			= max($n_folder			   ,dvdaf3_strlen($folder			));
				$n_dvd_id			= max($n_dvd_id			   ,dvdaf3_strlen($dvd_id			));
				$n_dvd_title		= max($n_dvd_title		   ,dvdaf3_strlen($dvd_title			));
				$n_media			= max($n_media			   ,dvdaf3_strlen($media				));
				$n_region			= max($n_region			   ,dvdaf3_strlen($region			));
				$n_publisher		= max($n_publisher		   ,dvdaf3_strlen($publisher			));
				$n_comments			= max($n_comments		   ,dvdaf3_strlen($comments			));
				$n_owned_dd			= max($n_owned_dd		   ,dvdaf3_strlen($owned_dd			));
				$n_last_watched_dd	= max($n_last_watched_dd   ,dvdaf3_strlen($last_watched_dd	));
				$n_user_film_rating	= max($n_user_film_rating  ,dvdaf3_strlen($user_film_rating	));
				$n_user_dvd_rating	= max($n_user_dvd_rating   ,dvdaf3_strlen($user_dvd_rating	));
				$n_sort_text		= max($n_sort_text		   ,dvdaf3_strlen($sort_text			));
				$n_genre_overwrite	= max($n_genre_overwrite   ,dvdaf3_strlen($genre_overwrite	));
				$n_pic_overwrite	= max($n_pic_overwrite	   ,dvdaf3_strlen($pic_overwrite		));
				$n_custom_1			= max($n_custom_1		   ,dvdaf3_strlen($custom_1			));
				$n_custom_2			= max($n_custom_2		   ,dvdaf3_strlen($custom_2			));
				$n_custom_3			= max($n_custom_3		   ,dvdaf3_strlen($custom_3			));
				$n_custom_4			= max($n_custom_4		   ,dvdaf3_strlen($custom_4			));
				$n_custom_5			= max($n_custom_5		   ,dvdaf3_strlen($custom_5			));
				$n_retailer			= max($n_retailer		   ,dvdaf3_strlen($retailer			));
				$n_price_paid		= max($n_price_paid		   ,dvdaf3_strlen($price_paid		));
				$n_order_dd			= max($n_order_dd		   ,dvdaf3_strlen($order_dd			));
				$n_order_number		= max($n_order_number	   ,dvdaf3_strlen($order_number		));
				$n_trade_loan		= max($n_trade_loan		   ,dvdaf3_strlen($trade_loan		));
				$n_asking_price		= max($n_asking_price	   ,dvdaf3_strlen($asking_price		));
				$n_loaned_to		= max($n_loaned_to		   ,dvdaf3_strlen($loaned_to			));
				$n_loan_dd			= max($n_loan_dd		   ,dvdaf3_strlen($loan_dd			));
				$n_return_dd		= max($n_return_dd		   ,dvdaf3_strlen($return_dd			));
				$n_my_dvd_created_tm= max($n_my_dvd_created_tm ,dvdaf3_strlen($my_dvd_created_tm	));
				$n_my_dvd_updated_tm= max($n_my_dvd_updated_tm ,dvdaf3_strlen($my_dvd_updated_tm	));
			}
			else
			{
				echo substr(
					$folder				.
					$dvd_id				.
					$dvd_title			.
					$media				.
					$region				.
					$publisher			.
					$comments			.
					$owned_dd			.
					$last_watched_dd	.
					$user_film_rating	.
					$user_dvd_rating	.
					$sort_text			.
					$genre_overwrite	.
					$pic_overwrite		.
					$custom_1			.
					$custom_2			.
					$custom_3			.
					$custom_4			.
					$custom_5			.
					$retailer			.
					$price_paid			.
					$order_dd			.
					$order_number		.
					$trade_loan			.
					$asking_price		.
					$loaned_to			.
					$loan_dd			.
					$return_dd			.
					$my_dvd_created_tm	.
					$my_dvd_updated_tm	.
					$nl, 1);
			}
		}
		CSql::free($rr);

		if ( $b_bars )
		{
			$a = ' | ';
			$b = ' |';
			$c = '-+-';
			$d = '-+';
		}
		else
		{
			$a = ' ';
			$b = '';
		}

		if ( $b_fixed_length )
		{
			for ( $j = 0 ; $j < count($all) ; $j++ )
			{
				if ( $b_bars && ($j == 0 || $j == $i) )
					$z = substr((						       $c.str_repeat('-', $n_folder				- 1)).
								(!$b_a_dvd_id			? '' : $c.str_repeat('-', $n_dvd_id				- 1)).
								(						       $c.str_repeat('-', $n_dvd_title			- 1)).
								(!$b_a_media_type		? '' : $c.str_repeat('-', $n_media				- 1)).
								(!$b_a_region_mask		? '' : $c.str_repeat('-', $n_region				- 1)).
								(!$b_a_publisher		? '' : $c.str_repeat('-', $n_publisher			- 1)).
								(!$b_b_comments			? '' : $c.str_repeat('-', $n_comments			- 1)).
								(!$b_b_owned_dd			? '' : $c.str_repeat('-', $n_owned_dd			- 1)).
								(!$b_b_last_watched_dd	? '' : $c.str_repeat('-', $n_last_watched_dd	- 1)).
								(!$b_b_user_film_rating	? '' : $c.str_repeat('-', $n_user_film_rating	- 1)).
								(!$b_b_user_dvd_rating	? '' : $c.str_repeat('-', $n_user_dvd_rating	- 1)).
								(!$b_b_sort_text		? '' : $c.str_repeat('-', $n_sort_text			- 1)).
								(!$b_b_genre_overwrite	? '' : $c.str_repeat('-', $n_genre_overwrite	- 1)).
								(!$b_b_pic_overwrite	? '' : $c.str_repeat('-', $n_pic_overwrite		- 1)).
								(!$b_b_custom_1			? '' : $c.str_repeat('-', $n_custom_1			- 1)).
								(!$b_b_custom_2			? '' : $c.str_repeat('-', $n_custom_2			- 1)).
								(!$b_b_custom_3			? '' : $c.str_repeat('-', $n_custom_3			- 1)).
								(!$b_b_custom_4			? '' : $c.str_repeat('-', $n_custom_4			- 1)).
								(!$b_b_custom_5			? '' : $c.str_repeat('-', $n_custom_5			- 1)).
								(!$b_b_retailer			? '' : $c.str_repeat('-', $n_retailer			- 1)).
								(!$b_b_price_paid		? '' : $c.str_repeat('-', $n_price_paid			- 1)).
								(!$b_b_order_dd			? '' : $c.str_repeat('-', $n_order_dd			- 1)).
								(!$b_b_order_number		? '' : $c.str_repeat('-', $n_order_number		- 1)).
								(!$b_b_trade_loan		? '' : $c.str_repeat('-', $n_trade_loan			- 1)).
								(!$b_b_asking_price		? '' : $c.str_repeat('-', $n_asking_price		- 1)).
								(!$b_b_loaned_to		? '' : $c.str_repeat('-', $n_loaned_to			- 1)).
								(!$b_b_loan_dd			? '' : $c.str_repeat('-', $n_loan_dd			- 1)).
								(!$b_b_return_dd		? '' : $c.str_repeat('-', $n_return_dd			- 1)).
								(!$b_b_my_dvd_created_tm? '' : $c.str_repeat('-', $n_my_dvd_created_tm	- 1)).
								(!$b_b_my_dvd_updated_tm? '' : $c.str_repeat('-', $n_my_dvd_updated_tm	- 1)).
								$d.$nl, 1);

				if ( $b_bars && $j == 0 ) echo $z;

					echo substr((						       $a.strpad($all[$j][ 0], $n_folder			- 1)).
								(!$b_a_dvd_id			? '' : $a.strpad($all[$j][ 1], $n_dvd_id			- 1, $j ? STR_PAD_LEFT : STR_PAD_RIGHT)).
								(						       $a.strpad($all[$j][ 2], $n_dvd_title			- 1)).
								(!$b_a_media_type		? '' : $a.strpad($all[$j][ 3], $n_media				- 1)).
								(!$b_a_region_mask		? '' : $a.strpad($all[$j][ 4], $n_region			- 1)).
								(!$b_a_publisher		? '' : $a.strpad($all[$j][ 5], $n_publisher			- 1)).
								(!$b_b_comments			? '' : $a.strpad($all[$j][ 6], $n_comments			- 1)).
								(!$b_b_owned_dd			? '' : $a.strpad($all[$j][ 7], $n_owned_dd			- 1)).
								(!$b_b_last_watched_dd	? '' : $a.strpad($all[$j][ 8], $n_last_watched_dd	- 1)).
								(!$b_b_user_film_rating	? '' : $a.strpad($all[$j][ 9], $n_user_film_rating	- 1)).
								(!$b_b_user_dvd_rating	? '' : $a.strpad($all[$j][10], $n_user_dvd_rating	- 1)).
								(!$b_b_sort_text		? '' : $a.strpad($all[$j][11], $n_sort_text			- 1)).
								(!$b_b_genre_overwrite	? '' : $a.strpad($all[$j][12], $n_genre_overwrite	- 1)).
								(!$b_b_pic_overwrite	? '' : $a.strpad($all[$j][13], $n_pic_overwrite		- 1)).
								(!$b_b_custom_1			? '' : $a.strpad($all[$j][14], $n_custom_1			- 1)).
								(!$b_b_custom_2			? '' : $a.strpad($all[$j][15], $n_custom_2			- 1)).
								(!$b_b_custom_3			? '' : $a.strpad($all[$j][16], $n_custom_3			- 1)).
								(!$b_b_custom_4			? '' : $a.strpad($all[$j][17], $n_custom_4			- 1)).
								(!$b_b_custom_5			? '' : $a.strpad($all[$j][18], $n_custom_5			- 1)).
								(!$b_b_retailer			? '' : $a.strpad($all[$j][19], $n_retailer			- 1)).
								(!$b_b_price_paid		? '' : $a.strpad($all[$j][20], $n_price_paid		- 1, $j ? STR_PAD_LEFT : STR_PAD_RIGHT)).
								(!$b_b_order_dd			? '' : $a.strpad($all[$j][21], $n_order_dd			- 1)).
								(!$b_b_order_number		? '' : $a.strpad($all[$j][22], $n_order_number		- 1)).
								(!$b_b_trade_loan		? '' : $a.strpad($all[$j][23], $n_trade_loan		- 1)).
								(!$b_b_asking_price		? '' : $a.strpad($all[$j][24], $n_asking_price		- 1, $j ? STR_PAD_LEFT : STR_PAD_RIGHT)).
								(!$b_b_loaned_to		? '' : $a.strpad($all[$j][25], $n_loaned_to			- 1)).
								(!$b_b_loan_dd			? '' : $a.strpad($all[$j][26], $n_loan_dd			- 1)).
								(!$b_b_return_dd		? '' : $a.strpad($all[$j][27], $n_return_dd			- 1)).
								(!$b_b_my_dvd_created_tm? '' : $a.strpad($all[$j][28], $n_my_dvd_created_tm	- 1)).
								(!$b_b_my_dvd_updated_tm? '' : $a.strpad($all[$j][29], $n_my_dvd_updated_tm	- 1)).
								$b.$nl, 1);

				if ( $b_bars && ($j == 0 || $j == $i) ) echo $z;
			}
		}
		echo "{$i} line".($i == 1 ? '' : 's')." exported{$nl}";
	}
}

?>
