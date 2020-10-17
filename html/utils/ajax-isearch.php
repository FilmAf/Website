<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

class CSearchComp extends CAjax
{
	// ?what=dir&parm=hil&target=2
	// ?what=dvd&parm=loe&target=0
	// ?what=pub&parm=bar&target=1
	function getSql()
		{
		$this->ms_what		= dvdaf3_getvalue('what'	,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_key		= dvdaf3_getvalue('key'		,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_val		= dvdaf3_getvalue('val'		,DVDAF3_GET|DVDAF3_LOWER);	$this->ms_val = str_replace('\\','',$this->ms_val);
		$this->ms_obj		= strtoupper(dvdaf3_getvalue('obj',DVDAF3_GET));
		$this->ms_reg		= dvdaf3_getvalue('reg'		,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_med		= strtoupper(dvdaf3_getvalue('med',DVDAF3_GET));
		$this->mn_page		= dvdaf3_getvalue('pg'		,DVDAF3_GET|DVDAF3_INT	);
		$this->ms_row		= dvdaf3_getvalue('row'		,DVDAF3_GET	);
		$this->mn_single	= dvdaf3_getvalue('single'	,DVDAF3_GET|DVDAF3_INT	);
		$this->ms_target	= dvdaf3_getvalue('target'	,DVDAF3_GET|DVDAF3_LOWER);
		$this->ms_context	= ( $this->ms_what   != '' ? "what='{$this->ms_what}' "     : '').
							  ( $this->ms_key    != '' ? "key='{$this->ms_key}' "       : '').
							  ( $this->ms_val    != '' ? "val='{$this->ms_val}' "       : '').
							  ( $this->ms_obj    != '' ? "obj='{$this->ms_obj}' "       : '').
							  ( $this->ms_reg    != '' ? "reg='{$this->ms_reg}' "       : '').
							  ( $this->ms_med    != '' ? "med='{$this->ms_med}' "       : '').
							  ( $this->mn_page   != '' ? "pg='{$this->mn_page}' "       : '').
							  ( $this->ms_row    != '' ? "row='{$this->ms_row}' "       : '').
							  ( $this->mn_single != '' ? "single='{$this->mn_single}' " : '').
							  ( $this->ms_target != '' ? "target='{$this->ms_target}' " : '');
		if ( $this->mn_page <= 0 ) $this->mn_page = 1;

		// end of word?
		if ( substr($this->ms_val,-1) == '/' ) $this->ms_val = substr($this->ms_val,0,-1) . ' ';
		$s = $this->ms_val;

		// normalize and transforms
		switch ( $this->ms_reg )
		{
		case '1,a,0': $this->ms_reg = '1A0'; break;
		case '2,b,0': $this->ms_reg = '2B0'; break;
		case 'z':     $this->ms_reg = '0';   break;
		}

		switch ( $this->ms_med )
		{
		case 'H,C,T': $this->ms_med = 'H'; break;
		case 'A,P,O': $this->ms_med = 'O'; break;
		}

		switch ( $this->ms_key )
		{
		case 'dir': $this->ms_obj = 'D'; break;
		case 'pub': $this->ms_obj = 'P'; break;
		}

		// translate conditions
		$s_join = '';
		if ( $this->ms_obj && $this->ms_what != 'expand' ) $s_join .= "and a.obj_type = '{$this->ms_obj}' ";
		if ( $this->ms_reg <> ''						 ) $s_join .= "and b.region = '{$this->ms_reg}' ";
		if ( $this->ms_med								 ) $s_join .= "and b.media_type = '{$this->ms_med}' ";

		$s_where = '';

		// create sql
		switch ( $this->ms_key )
		{
		case 'dir':
		case 'pub':
			if ( strlen($s) < 2 ) break;
		case 'has':
			switch ( $this->ms_what )
			{
			case 'counts':
				// min a.whole == 'N' is used to determine if a studio or director need expansion before pic preview
				$this->ms_sql = "SELECT a.nocase, a.obj_type, count(distinct b.dvd_id), min(a.whole) ".
								  "FROM search_all_1 a ".
								  "JOIN search_all_2 b ON a.dvd_id = b.dvd_id {$s_join}".
								 "WHERE a.nocase like '{$s}%' {$s_where}".
								 "GROUP BY a.nocase, a.obj_type ".
								 "ORDER BY IF(INSTR(a.nocase,'{$s}')<=2,1,2), a.nocase ".
								 "LIMIT 201";
				$this->mn_max = 200;
				break;
			case 'expand':
				// min a.whole == 'N' is used to determine if a studio or director need expansion before pic preview
				$this->ms_sql = "SELECT a.nocase, a.obj_type, count(distinct a.dvd_id), 'Y' ".
								  "FROM search_all_1 c ".
								  "JOIN search_all_1 a ON a.dvd_id = c.dvd_id and a.whole ='Y' and a.obj_type = '{$this->ms_obj}' and (a.nocase like '% {$s} /' or a.nocase = '{$s} /') ".
								  "JOIN search_all_2 b ON a.dvd_id = b.dvd_id {$s_join}".
								 "WHERE c.nocase = '{$s} /' ".
								 "GROUP BY a.nocase, a.obj_type";
								 "LIMIT 201";
				$this->mn_max = 200;
				break;
			case 'dvds':
				$n = $this->mn_single ? 2 : 17;
				$p = ($this->mn_page - 1) * ($n - 1);
				$p= ! $p ? "$n" : "$p,$n";
				$this->ms_sql = "SELECT a.dvd_id, x.pic_name, x.dvd_title, x.director, x.publisher, x.country, x.region_mask, x.film_rel_year, x.media_type ".
								  "FROM (SELECT distinct a.dvd_id ".
										  "FROM search_all_1 a ".
										  "JOIN search_all_2 b ON a.dvd_id = b.dvd_id {$s_join}".
										 "WHERE a.nocase = '{$s} /' {$s_where}) a ".
								  "JOIN dvd x ON a.dvd_id = x.dvd_id ".
								 "ORDER BY IF(INSTR(x.dvd_title_nocase,' {$s}')<=2,IF(INSTR(LOWER(x.dvd_title),'{$s}, the')=1,1,2),3), x.amz_rank, x.dvd_title_nocase, a.dvd_id ".
								 "LIMIT {$p}";
				$this->mn_max = $n - 1;
				break;
			default:
				return $this->on_error("Unrecognized request: ".__LINE__);
			}
			break;
		default:
			return $this->on_error("Unrecognized request: ".__LINE__);
		}

//$this->log_debug(false);
//$this->log_debug("this->ms_key = {$this->ms_key}");
//$this->log_debug("this->ms_val = {$this->ms_val}");
//$this->log_debug("this->ms_sql = {$this->ms_sql}");
		return true;
	}

	function done()
	{
		$this->ms_context .= $this->mb_over_max ? '' : "last='1' ";
	}
}

$a = new CSearchComp();
$a->main();

?>
