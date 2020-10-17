<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CDvdList.php';

class CDvdSearch extends CDvdList
{
	function constructor() // <<--------------------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->mb_show_release_dt	= true;
		$this->mn_echo_zoom			= CWnd_ZOOM_ALL;

		$this->mb_release_week		= dvdaf3_getvalue('SCRIPT_URL', DVDAF3_SERVER|DVDAF3_LOWER) == '/releases.html';
		$this->mb_added_criteria	= false;
		$this->ms_extra_js_config	= $this->mb_release_week ? ',hasRelWeek:true' : '';

		$this->ms_search_all_1		= '';
		$this->ms_where				= '';
		$this->ms_ammend_sort		= '';
		$this->ms_requested_sort	= '';
		$this->mn_requested_week	= 0;
		$this->ms_super_region		= '';
		$this->ms_super_media		= '';
	}

	function validRequest() // <<-------------------------------<< 4.0
	{
		$this->ms_list_kind = CWnd_LIST_DVDS;
		return true;
	}

	function validateDataSubmission() // <<---------------------<< 6.0
	{
		parent::validateDataSubmission();

		if ( $this->mb_release_week )
		{
			$s_week = dvdaf3_getvalue('week', DVDAF3_GET);
			if ( ! $s_week ) $s_week = date('Y-m-d');
			$this->composeWhere('week', $s_week);
		}

		// do pinned (any updates also need to be done on ajax-pin.php)
		$a_parm		 = explode('*',$this->ms_user_pinned);
		$b_skip_rgn  = false;
		$b_skip_med  = false;
		$s_add_where = '';
		for ( $i = 0 ; $i < count($a_parm) ; $i++ )
		{
			$a_item = explode('_', $a_parm[$i]);
			switch ( $a_item[0] )
			{
				case 'str0':
				case 'str1':
				case 'str2':
				case 'str3':
					if ( preg_match('/^(has|pricele|pricege|asin|imdb|upc|dir|pub|pubct|genre|rel|reldt|year|lang|pic|src|created)$/',$a_item[1]) && $a_item[2] != '' )
						$this->composeWhere($a_item[1], $a_item[2]);
					break;
				case 'rgn':
					if ( preg_match('/^(us|uk|eu|la|as|sa|jp|au|z|1|1,a,0|2|2,b,0|3|4|5|6|a|b|c|all)$/',$a_item[1]) )
					{
						$this->composeWhere('rgn',$a_item[1]);
						$b_skip_rgn = true;
					}
					break;
				case 'med':
					if ( preg_match('/^(all|d,v|b,3,2,r|3|h,c,t|a,p,o|f,s,l,e,n)$/',$a_item[1]) )
					{
						$this->composeWhere('med',$a_item[1]);
						$b_skip_med = true;
					}
					break;
				case 'xcmy':
					// A: Weird order of SQL affects performance, do it in (B)
					switch ( $a_item[1] )
					{
						case 1: $s_add_where = ".ne.{$this->ms_user_id}"; break;
						case 2: $s_add_where = ".eq.{$this->ms_user_id}"; break;
					}
					break;
			}
		}

		// do get parms
		$a_keys = array('has', 'beg', 'price', 'asin', 'imdb', 'upc', 'dir', 'pub', 'pubct', 'genre', 'rel', 'reldt', 'year', 'lang', 'src', 'pic', 'created', 'rgn', 'med', 'where', 'ord');
		for ( $i = 0 ; $i < count($a_keys) ; $i++ )
		{
			$s_parm = dvdaf3_getvalue($a_keys[$i], DVDAF3_GET);
			switch ( $a_keys[$i] )
			{
				case 'rgn'  : if ( $b_skip_rgn  ) $s_parm != ''; break;
				case 'med'  : if ( $b_skip_med  ) $s_parm != ''; break;
			}
			if ( $s_parm != '' )
				$this->composeWhere($a_keys[$i], $s_parm);
		}

		// B: Tailend of pinned, from (A)
		if ( $s_add_where )
			$this->composeWhere('where', $s_add_where);

		if ( $this->ms_display_what  ) $this->ms_display_what  = "Search criteria: {$this->ms_display_what}"; // "<br />{$this->ms_where} <br />";
		if ( $this->ms_display_error ) $this->ms_display_error = "Bad search parameters:<ul class='seul'>{$this->ms_display_error}</ul>";
	}

	function drawMessagesTot($n_row_begin, $n_row_end, $n_row_total, $n_total_titles, $n_total_disks, $b_validate, $b_explain, $s_msg, $s_error, $b_empty)
	{
		parent::drawMessagesTot($n_row_begin, $n_row_end, $n_row_total, $n_total_titles, $n_total_disks, $b_validate, $b_explain, $s_msg, $s_error, $b_empty);

		if ( $this->mb_release_week && $this->mn_requested_week )
		{
			$url = dvdaf3_getvalue('REQUEST_URI', DVDAF3_SERVER);

			if ( preg_match('/^(.*)(week=[0-9]{4}-[0-9]{2}-[0-9]{2})(.*)$/i', $url, $a_matches) )
			{
				$s_beg = $a_matches[1];
				$s_end = $a_matches[3];
			}
			else
			{
				if ( ($i = strpos($url, '?')) )
				{
					$s_beg = substr($url,0,$i+1);
					$s_end = '&' . substr($url,$i+1);
				}
				else
				{
					$s_beg = $url . '?';
					$s_end = '';
				}
			}

			$now = time();
			$y   = intval(date('Y', $now));
			$m   = intval(date('m', $now));
			$d   = intval(date('d', $now));

			$d   = mktime(0,0,0,$m,$d,$y);
			$y   = intval(date('w', $d));
			$now = $d - ($y - 2) * 3600 * 24; // Tuesday from this week
			$y   = date('Y-m-d', $now);
			$m   = date('Y-m-d', $this->mn_requested_week);

			$ref  = abs($now - $this->mn_requested_week) + 3600 * 24 > 20 * 3600 * 24 * 7 ? $this->mn_requested_week : $now;

			for ( $url = '', $i = -20 ; $i <= 20 ; $i++ )
			{
				if ( ($w = date('Y-m-d', $ref + $i * 3600 * 24 * 7)) == $m )
				{
					$url .= "<span class='mo'>{$w}</span>&nbsp; ";
				}
				else
				{
					$d    = $w == $y ? 'mn' : 'mm';
					$url .= "<a href='{$s_beg}week={$w}{$s_end}' class='{$d}'>{$w}</a>&nbsp; ";
				}
			}

			$s_non_week = '';
			if ( $this->mb_release_week && $this->mb_added_criteria )
			{
				$s_non_week = str_replace('releases.html', 'search.html', $_SERVER['REQUEST_URI']);
				$s_non_week = preg_replace("/week=[0-9]+-[0-9]+-[0-9]+&?/", '', $s_non_week);
				$s_non_week = substr($s_non_week,-1) == '?'
					? ''
					: "<div style='padding:8px 0 8px 0' id='msg-only'><a href='{$s_non_week}'>Repeat query without the release week constraint</a></div>";
			}

			echo "<table style='margin-top:10px'>".
				"<tr><td style='vertical-align:top;white-space:nowrap'>Select new week:&nbsp;</td><td>". substr($url,0,-7) ."{$s_non_week}</td></tr>".
				"</table>";
		}
	}

	function ammendSqlQuery(&$s_select, &$s_from, &$s_where, &$s_sort, $b_skip_pins)
	{
		if ( strpos($this->ms_requested_sort, 'dvd_created_tm') )
		{
			$s_select = str_replace(", a.dvd_title,",
				", CONCAT(a.dvd_title,'<div class=\"dvd_ctm\">Listing Created on ',a.dvd_created_tm,'</div>') dvd_title, a.dvd_created_tm,",
				$s_select);
		}

		parent::ammendSqlQuery($s_select, $s_from, $s_where, $s_sort, true);
		$s_sort   = $this->ms_requested_sort . $this->ms_ammend_sort . $s_sort;
		$s_where .= ($s_where ? ' and ' : ''). ($this->ms_where ? $this->ms_where : ($this->ms_search_all_1 ? '1=1' : '1=0'));

		if ( $this->ms_search_all_1 )
			$s_from = "({$this->ms_search_all_1}) iz JOIN dvd a ON a.dvd_id = iz.dvd_id ";
	}

	function composeWhere($s_key, $s_value)
	{
		$s_value  = trim(preg_replace('/[\x22#$\x27()\*\+:;\?@\[\\\]\x5E_\x60{}~]/', ' ', preg_replace('/\x01-\x1F\x7F/', '', preg_replace('/[\s]+/', ' ',$s_value))));
		if ( $s_value == '' ) return;

		$a_value   = preg_split('/(\.gt\.|\.ge\.|\.lt\.|\.le\.|\.ne\.|\.eq\.|\|)/', $s_value, -1, PREG_SPLIT_NO_EMPTY + PREG_SPLIT_DELIM_CAPTURE);
		//echo CTrace::dump_array($a_value,0);
		$s_where   = '';
		$s_join_is = '';
		$s_join_iz = '';
		$s_desc    = '';
		$s_oper    = 'eq';
		$b_use_or  = false;
		$a_whr	   = array();
		$s_wop     = '';

		for ( $i = 0 ; $i < count($a_value) ; $i++ )
		{
			switch ( $a_value[$i] )
			{
				case '.eq.':
				case '.gt.':
				case '.ge.':
				case '.lt.':
				case '.le.':
				case '.ne.':
					$s_oper = substr($a_value[$i], 1, 2);
					break;
				case '|':
					$b_use_or = true;
					break;
				case 'queued_where':
				default:
					$s_sql			= '';
					$s_desc_this	= '';
					if ( $a_value[$i] == 'queued_where' )
					{
						//echo "s_wop = $s_wop<br>";
						//echo "a_whr[0] = $a_whr[0]<br>";
						//echo "a_whr[1] = $a_whr[1]<br>";
						$s_sql = $this->whereFromArray('where', $s_wop, $a_whr, $a_parm, $this->ms_display_error, $s_desc_this);
					}
					else
					{
						if ( ($s_cmp = trim(substr($a_value[$i], 0, 1000))) != '' )
						{
							$a_cmp = $this->splitWhereCommas($s_key, $s_cmp, $a_parm, $this->ms_display_error);

							if ( $s_key == 'where' )
							{
								//$s_sql = '1 = 2';
								if ( count($a_whr) < 2 )
								{
									//echo "&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>$s_wop = $s_wop<br>s_oper = $s_oper<br>a_cmp[0]={$a_cmp[0]}---- ".count($a_cmp)."<br>";
									if ( $s_wop == '' || $s_wop == $s_oper )
									{
										if ( $s_wop == '' ) $a_value[] = 'queued_where';
										$s_wop = $s_oper;
										for ( $k = 0 ; $k < count($a_cmp) ; $k++ ) if ( $a_cmp[$k] ) $a_whr[] = $a_cmp[$k];
									}
									else
									{
										// when different operators make sure the first is always the 'eq' and the second 'ne'
										if ( $s_oper == 'eq' )
										{
											$a_whr[1] = $a_whr[0];
											$a_whr[0] = $a_cmp[0];
										}
										else
										{
											$a_whr[1] = $a_cmp[0];
										}
										$s_wop = 'eqne';
									}
								}
							}
							else
							{
								$s_sql = $this->whereFromArray($s_key, $s_oper, $a_cmp, $a_parm, $this->ms_display_error, $s_desc_this);
							}
						}
					}

					if ( $s_sql       ) $s_where .= ($s_where != '' ? ($b_use_or ? ' or ' : ' and ') : '') . '('. $s_sql.       ')';
					if ( $s_desc_this ) $s_desc  .= ($s_desc  != '' ? ($b_use_or ? ' or ' : ' and ') : '') . '('. $s_desc_this. ')';
					$b_use_or = false;
					break;
			}
		}
		if ( ! $this->ms_display_error )
		{
			if ( $s_desc  ) $this->ms_display_what .= ($this->ms_display_what == '' ? '' : ' and '). $s_desc;
			if ( $s_where ) $this->ms_where        .= ($this->ms_where        == '' ? '' : ' and '). '('. $s_where   .')';
		}
	}

	function whereFromArray($s_key, $s_oper, $a_cmp, $a_parm, &$s_error, &$s_desc)
	{
		if ( ($n = count($a_cmp)) <= 0 ) return '';

		$eq	= $s_oper == 'eq';
		$ne	= $s_oper == 'ne';
		$cp	= ! $eq && ! $ne;
		$s	= '';
		$s_	= '';
		$a	= $eq ? ' or ' : ' and ';
		$o	= $s_oper;
		$b_miss = count($a_cmp) == 1 && $a_cmp[0] == 'missing';
		$m	= array();

		//for ( $i = 0 ; $i < $n ; $i++ ) echo "a_cmp[$i] = {$a_cmp[$i]}<br>";

		switch ( $s_key )
		{
			case 'has':
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= do not make sense for a title has search. ".
						"A title either has (=) or does not have (&lt;&gt;,!=) something. Note that range parameters do make sense ".
						"for &quot;title begins with&quot; searches. Perhaps that is what you intended. If that is the case either ".
						"select &quot;title begins&quot; in the dropdown list box or if you create your own URL's use the keyword ".
						"&quot;beg&quot; instead of the keyword &quot;has&quot;.</li>";
					break;
				}
				$a  = ' and ';
				for ( $i = $ii = 0 ; $i < $n ; $i++, $ii++ )
				{
					$v  = $a_cmp[$i];
					$v_ = strlen($v);
					if ( $ii > 0 ) { $s .= $a; $s_ .= $a; }

					if ( $v_ >= 5 && $v_ <= 7 && preg_match('/^[0-9]+$/', $v) )
					{
						$v  = intval($v);
						$f  = 'a.dvd_id';
						$f_ = 'dvd id';
						$s  .= $eq ? "$f = $v" : "$f <> $v";
						$s_ .= $eq ? "$f_ is $v" : "$f_ is not $v";
					}
					else
						if ( $this->mn_moderator_cd >= 5 &&  preg_match('/^([0-9]+) to ([0-9]+)$/', $v, $m) )
						{
							$u = intval($m[1]);
							$v = intval($m[2]);
							if ( $u > $v ) $u = $v;
							if ( $v > $u + 500 ) $v = $u + 500;
							$f  = 'a.dvd_id';
							$f_ = 'dvd id';
							$s  .= "$f >= $u and $f <= $v";
							$s_ .= "$f_ between $u and $v";
						}
						else
							/*
                            if ( $v_ == 7 && preg_match('/^[0-9]+$/', $v) )
                            {
                                $v  = substr('0000000' . $v,-7);
                                $f  = 'a.imdb_id';
                                $f_ = 'imdb id';
                                $s  .= $eq ? "$f like '%$v%'": "$f not like '%$v%'";
                                $s_ .= $eq ? "$f_ is '$v'": "$f_ is not '$v'";
                            }
                            else
                            */
							if ( $v_ == 10 && (preg_match('/^b0[0-9a-z]{8}$/', $v) || preg_match('/^[016][0-9]{8}[0-9x]$/', $v)) )
							{
								$f  = 'LOWER(a.asin)';
								$f_ = 'ASIN';
								$s  .= $eq ? "$f = '$v'" : "$f <> '$v'";
								$s_ .= $eq ? "$f_ is '$v'" : "$f_ is not '$v'";
							}
							else
								if ( preg_match('/^[0-9]{10,13}$/', str_replace('-','',str_replace(' ','',$v))) )
								{
									$f  = 'a.upc';
									$f_ = 'UPC';
									$v  = str_replace('-','',str_replace(' ','',$v));
									$s  .= $eq ? "$f like '%$v%'" : "$f not like '%$v%'";
									$s_ .= $eq ? "$f_ has '$v'" : "$f_ does not have '$v'";
								}
								else
								{
									$f_ = 'title';
									if ( $this->ms_search_all_1 || ! $eq || strlen($v) <= 2 )
									{
										$f  = 'a.dvd_title_nocase';
										if ( substr($v,-2) == '//' ) { $v = substr($v,0,-2); $s .= $eq ? "($f like '%/ {$v} /%')" : "($f not like '%/ {$v} /%')"; } else // whole title
											if ( substr($v,-1) == '/'  ) { $v = substr($v,0,-1); $s .= $eq ? "($f like '%/ {$v} %')"  : "($f not like '%/ {$v} %')" ; } else // end the word
											{						 $s .= $eq ? "($f like '%/ {$v}%')"   : "($f not like '%/ {$v}%')"  ; }      // patial match on the left
									}
									else
									{
										$f  = "iz.obj_type = 'V' and iz.nocase";
										if ( substr($v,-2) == '//' ) { $v = substr($v,0,-2); $iz = "$f = '{$v} /'"   ; } else // whole title
											if ( substr($v,-1) == '/'  ) { $v = substr($v,0,-1); $iz = "$f like '{$v} %'"; } else // end the word
											{						 $iz = "$f like '{$v}%'" ; }      // patial match on the left
										$this->ms_search_all_1 = "SELECT distinct iz.dvd_id FROM search_all_1 iz FORCE INDEX (XIE1search_all_1) WHERE $iz";
										$ii--;
									}
									$s_ .= $eq ? "$f_ has '$v'" : "$f_ does not have '$v'";
									if ( $eq && ! $this->ms_ammend_sort )
										$this->ms_ammend_sort = "IF(INSTR(a.dvd_title_nocase,' {$v}')<=2,IF(INSTR(LOWER(a.dvd_title),'{$v}, the')=1,1,2),3), ";
								}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'beg':
				$f  = 'a.dvd_title_nocase';
				$f_ = 'title';
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					$v = $a_cmp[$i];
					if ( $i > 0 ) { $s .= $a; $s_ .= $a; }
					switch ( $o )
					{
						case 'eq':
							$s  .= "$f like '/ $v%'";
							$s_ .= "$f_ begins with '$v'";
							if ( ! $this->ms_ammend_sort )
								$this->ms_ammend_sort = "IF(INSTR(LOWER(a.dvd_title),'{$v}, the')=1,1,2), ";
							break;

						case 'ne': $s .= "$f not like '/ $v%'";	$s_ .= "$f_ does not begin with '$v'";	break;
						case 'gt': $s .= "$f > '/ $v%'";		$s_ .= "$f_ &gt; '$v'";					break;
						case 'ge': $s .= "$f >= '/ $v%'";		$s_ .= "$f_ &gt;= '$v'";				break;
						case 'lt': $s .= "$f < '/ $v%'";		$s_ .= "$f_ &lt; '$v'";					break;
						case 'le': $s .= "$f <= '/ $v%'";		$s_ .= "$f_ &lt;= '$v'";				break;
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'price':
				$f  = 'a.best_price';
				$f_ = 'best price';
				if ( $b_miss ) { $s = "$f = 0"; $s_ = "$f_ is missing"; break; }
				$pc = false;
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					$v = $a_cmp[$i];
					if ( $i > 0 ) { $s .= $a; $s_ .= $a; }
					if ( $a_parm[$i] )
					{
						$v_  = "{$v}% of MSRP";
						$v  /= 100;
						$v   = "a.list_price * $v";
						$pc  = true;
					}
					else
					{
						$v_  = '$'. "{$v}";
					}
					switch ( $o )
					{
						case 'eq': $s .= "$f = $v";		$s_ .= "$f_ is $v_";		break;
						case 'ne': $s .= "$f <> $v";	$s_ .= "$f_ is not $v_";	break;
						case 'gt': $s .= "$f > $v";		$s_ .= "$f_ &gt; $v_";		break;
						case 'ge': $s .= "$f >= $v";	$s_ .= "$f_ &gt;= $v_";		break;
						case 'lt': $s .= "$f < $v";		$s_ .= "$f_ &lt; $v_";		break;
						case 'le': $s .= "$f <= $v";	$s_ .= "$f_ &lt;= $v_";		break;
					}
				}
				if ( $s && $eq && $n > 1						  ) $s = "($s)";
				if ( $s && $o != 'eq' && $o != 'gt' && $o != 'ge' ) $s .= " and a.best_price > 0";
				if ( $s && $pc									  ) $s .= " and a.list_price > 0";
				break;

			case 'asin':
				$f  = 'LOWER(a.asin)';
				$f_ = 'ASIN';
				$m  = 10;
				if ( $b_miss ) { $s = "$f = '-'"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying an ASIN (Amazon ".
						"Standard Item Number). However, we do support partial matches of 5 or more characters. That may help you find ".
						"the title you seek.</li>";
					break;
				}
				if ( $n > 1 )
				{
					$v_ = '';
					for ( $i = 0, $v = '' ; $i < $n ; $i++ )
					{
						$v  .= "'{$a_cmp[$i]}',";
						$v_ .= "'{$a_cmp[$i]}' or ";
					}
					$v   = substr($v,0,-1);
					$v_  = substr($v_,0,-4);
					$s  .= $eq ? "$f in ($v)" : "$f not in ($v)";
					$s_ .= $eq ? "$f_ is $v_" : "$f_ is not $v_";
				}
				else
				{
					$v = $a_cmp[0];
					if ( $eq )
					{
						if ( strlen($v) < $m )
						{
							$s  .= "$f like '%$v%'";
							$s_ .= "$f_ has '$v'";
						}
						else
						{
							$s  .= "$f = '$v'";
							$s_ .= "$f_ is '$v'";
						}
					}
					else
					{
						if ( strlen($v) < $m )
						{
							$s  .= "$f not like '%$v%'";
							$s_ .= "$f_ does not have '$v'";
						}
						else
						{
							$s  .= "$f <> '$v'";
							$s_ .= "$f_ is not '$v'";
						}
					}
				}
				break;

			case 'lang':
			case 'pubct':
				$f  = array('pubct' => 'a.country'				,'lang' => 'a.orig_language'   ); $f  = $f[$s_key];
				$f_ = array('pubct' => "publisher's country"	,'lang' => 'language'          ); $f_ = $f_[$s_key];
				$m  = array('pubct' => DVDAF3_DICT_DVD_COUNTRY	,'lang' => DVDAF3_DICT_LANGUAGE); $m  = $m[$s_key];
				if ( $b_miss ) { $s = "$f = '-'"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a country.</li>";
					break;
				}
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					$v   = $a_cmp[$i];
					if ( $v == 'missing' )
					{
						$v_  = 'missing';
						if ( $i > 0 ) { $s .= $a; $s_ .= $a; }
						$s  .= $eq ? "$f = '-'" : "$f != '-'";
					}
					else
					{
						$v_  = "'" .dvdaf3_decode($v, $m). "'";
						if ( $i > 0 ) { $s .= $a; $s_ .= $a; }
						$s  .= $eq ? "$f like '%,$v,%'" : "$f not like '%,$v,%'";
					}
					$s_ .= $eq ? "$f_ is $v_" : "$f_ is not $v_";
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'imdb':
				$f  = 'a.imdb_id';
				$f_ = 'imdb id';
				$f1 = "iz.obj_type = 'I' and iz.nocase";
				if ( $b_miss ) { $s = "$f = '-'"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying an imdb id.</li>";
					break;
				}
				for ( $i = $ii = 0 ; $i < $n ; $i++, $ii++ )
				{
					$v  = substr('0000000' . $a_cmp[$i],-7);
					if ( $ii > 0 ) { $s .= $a; $s_ .= $a; }

					if ( $this->ms_search_all_1 || ! $eq )
					{
						$s  .= $eq ? "$f like '%$v%'": "$f not like '%$v%'";
					}
					else
					{
						$iz = "$f1 = '{$v} /'" ;
						$this->ms_search_all_1 = "SELECT distinct iz.dvd_id FROM search_all_1 iz FORCE INDEX (XIE1search_all_1) WHERE $iz";
						$ii--;
					}
					$s_ .= $eq ? "$f_ is '$v'": "$f_ is not '$v'";
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'upc':
				$f  = 'a.upc';
				$f_ = 'UPC';
				$f1 = "iz.obj_type = 'U' and iz.nocase";
				if ( $b_miss ) { $s = "$f = '-'"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a {$f_}.</li>";
					break;
				}
				for ( $i = $ii = 0 ; $i < $n ; $i++, $ii++ )
				{
					$v  = dvdaf3_translatestring($a_cmp[$i], DVDAF3_SEARCH);
					$v  = $a_cmp[$i];
					if ( $ii > 0 ) { $s .= $a; $s_ .= $a; }

					if ( $this->ms_search_all_1 || ! $eq )
					{
						$s  .= $eq ? "$f like '%$v%'" : "$f not like '%$v%'";
					}
					else
					{
						if ( strlen($v) <= 9 )
							$iz = "$f1 like '{$v}%'";
						else
							$iz = "$f1 = '{$v} /'";
						$this->ms_search_all_1 = "SELECT distinct iz.dvd_id FROM search_all_1 iz FORCE INDEX (XIE1search_all_1) WHERE $iz";
						$ii--;
					}
					$s_ .= $eq ? "$f_ has '$v'" : "$f_ does not have '$v'";
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'dir':
			case 'pub':
				switch ( $s_key )
				{
					case 'dir': $f1 = "iz.obj_type = 'D' and iz.nocase"; $f = 'a.director_nocase';  $f_ = 'director';  break;
					case 'pub': $f1 = "iz.obj_type = 'P' and iz.nocase"; $f = 'a.publisher_nocase'; $f_ = 'publisher'; break;
				}
				if ( $b_miss ) { $s = "$f = '-'"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a {$f_}.</li>";
					break;
				}
				for ( $i = $ii = 0 ; $i < $n ; $i++, $ii++ )
				{
					$v  = dvdaf3_translatestring($a_cmp[$i], DVDAF3_SEARCH);
					$v  = $a_cmp[$i];
					if ( $ii > 0 ) { $s .= $a; $s_ .= $a; }

					if ( $this->ms_search_all_1 || ! $eq || strlen($v) <= 2 )
					{
						if ( substr($v,-2) == '//' ) { $v = substr($v,0,-2); $s .= $eq ? "($f like '%/ {$v} /%')" : "($f not like '%/ {$v} /%')"; } else // whole title
							if ( substr($v,-1) == '/'  ) { $v = substr($v,0,-1); $s .= $eq ? "($f like '% {$v} %)'"   : "($f not like '% {$v} %')"  ; } else // end the word
							{						 $s .= $eq ? "($f like '% {$v}%')"    : "($f not like '% {$v}%')"   ; }      // patial match on the left
					}
					else
					{
						if ( substr($v,-2) == '//' ) { $v = substr($v,0,-2); $iz = "$f1 = '{$v} /'"   ; } else // whole title
							if ( substr($v,-1) == '/'  ) { $v = substr($v,0,-1); $iz = "$f1 like '{$v} %'"; } else // end the word
							{						 $iz = "$f1 like '{$v}%'" ; }      // patial match on the left
						$this->ms_search_all_1 = "SELECT distinct iz.dvd_id FROM search_all_1 iz FORCE INDEX (XIE1search_all_1) WHERE $iz";
						$ii--;
					}
					$s_ .= $eq ? "$f_ has '$v'" : "$f_ does not have '$v'";
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'genre':
				$f  = 'a.genre';
				$f_ = 'genre';
				if ( $b_miss ) { $s = "$f = 99999"; $s_ = "$f_ is missing"; break; }
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					$v  = $a_cmp[$i];
					$v_ = dvdaf3_decode($v, DVDAF3_DICT_GENRE);
					$v_ = $v_ ? "'$v_'" : "$v";
					if ( $i > 0 ) { $s .= $a; $s_ .= $a; }
					if ( $v != 0 && $v % 1000 == 0 )
					{
						$t = $v + 999;
						switch ( $o )
						{
							case 'eq': $s .= "($f >= $v and $f <= $t)";	$s_ .= "$f_ is $v_";		break;
							case 'ne': $s .= "($f < $v or $f > $t)";	$s_ .= "$f_ is not $v_";	break;
							case 'gt': $s .= "$f > $t";					$s_ .= "$f_ &gt; $v_";		break;
							case 'ge': $s .= "$f >= $v";				$s_ .= "$f_ &gt;= $v_";		break;
							case 'lt': $s .= "$f < $v";					$s_ .= "$f_ &lt; $v_";		break;
							case 'le': $s .= "$f <= $t ";				$s_ .= "$f_ &lt;= $v_ ";	break;
						}
					}
					else
					{
						switch ( $s_oper )
						{
							case 'eq': $s .= "$f = $v";					$s_ .= "$f_ is $v_";		break;
							case 'ne': $s .= "$f <> $v";				$s_ .= "$f_ is not $v_";	break;
							case 'gt': $s .= "$f > $v";					$s_ .= "$f_ &gt; $v_";		break;
							case 'ge': $s .= "$f >= $v";				$s_ .= "$f_ &gt;= $v_";		break;
							case 'lt': $s .= "$f < $v";					$s_ .= "$f_ &lt; $v_";		break;
							case 'le': $s .= "$f <= $v";				$s_ .= "$f_ &lt;= $v_";		break;
						}
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'rel':
				$f  = 'a.rel_status';
				$f_ = 'release status';
				if ( $b_miss ) { $s = "$f = '-'"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a release status.</li>";
					break;
				}
				$a = ' or ';
				$b = $ne ? ' and ' : ' or ';
				$t = $ne ? ' not' : '';
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					if ( $i > 0 ) { $s .= $a; $s_ .= $b; }
					switch ( $a_cmp[$i] )
					{
						case 'c': $s .= "$f = 'C'";	$s_ .= "$f_ is$t 'Current'";		break;
						case 'o': $s .= "$f = 'O'";	$s_ .= "$f_ is$t 'Out of Print'";	break;
						case 'a': $s .= "$f = 'A'";	$s_ .= "$f_ is$t 'Announced'";		break;
						case 'n': $s .= "$f = 'N'";	$s_ .= "$f_ is$t 'Not Announced'";	break;
						case 'd': $s .= "$f = 'D'";	$s_ .= "$f_ is$t 'Delayed'";		break;
						case 'x': $s .= "$f = 'X'";	$s_ .= "$f_ is$t 'Cancelled'";		break;
						case 'u': $s .= "$f = '-'";	$s_ .= "$f_ is$t 'Unknown'";		break;
						//		case 'cur': $s .= "(best_price > 0 or $f in ('C','-'))";	  $s_ .= "title is current or being sold";				  break;
						//		case 'oop': $s .= "(best_price = 0 and $f = 'O')";		  $s_ .= "title is out of print and not being sold";			  break;
						//		case 'fut': $s .= "(best_price = 0 and $f not in ('C','-','O'))"; $s_ .= "title is not current, not out of print and nor being sold"; break;
						default:
							$n_julian  = unixtojd(time());		// today
							$n_julian -= jddayofweek($n_julian,0);	// Sunday for this week
							$n_begin   = intval($a_cmp[$i]) * 7;
							if ( $n_begin != 0 || $a_cmp[$i] == '0' )
							{
								$n_begin  += $n_julian;			// Sunday for the target week
								$n_julian  = $n_begin + 7;		// Sunday for the week after our target
								$n_begin   = date('Ymd', jdtounix($n_begin +1));	// for some reason needs to add 1 when translating from Julian to Std
								$n_julian  = date('Ymd', jdtounix($n_julian+1));	// for some reason needs to add 1 when translating from Julian to Std
								$s  .= "(a.dvd_rel_dd >= '$n_begin' and a.dvd_rel_dd < '$n_julian')";
								$n_begin  = dvdaf3_itod($n_begin);
								$n_julian = dvdaf3_itod($n_julian);
								$s_ .= $ne ? "(release date < $n_begin or release date >= $n_julian)" : "(release date >= $n_begin and release date < $n_julian)";
							}
							break;
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				if ( $s && $ne			 ) $s = "not ($s)";
				break;

			case 'week':
				$f  = 'a.dvd_rel_dd'; /* char(8)*/
				$f_ = 'release week'; /* char(8)*/
				$a_days = array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					if ( $i > 0 ) { $s .= $a; $s_ .= $a; }
					$v = false;
					$y = 0;
					$m = 0;
					$d = 0;

					if ( preg_match('/^([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$/', $a_cmp[$i], $a_matches) )
					{
						$y = intval($a_matches[1]);
						$m = intval($a_matches[2]);
						$d = intval($a_matches[3]);
						$v = $y >= 1880 && $y <= (date("Y") + 1) && $m >=1 && $m <= 12 && $d >= 1 && $d <= 31;
						if ( $v )
						{
							$a_days[2] = ($y % 4 == 0 && ($y % 100 != 0 || $y % 400 == 0)) ? 29 : 28;
							$v = $d <= $a_days[$m];
						}
						if ( $v )
						{
							// get Sunday compose range and say it is Tuesday
							$d  = mktime(0,0,0,$m,$d,$y);
							$y  = intval(date('w', $d));
							$m  = $d - $y * 3600 * 24; // Sunday
							$d  = $m +  2 * 3600 * 24; // Tuesday
							$y  = $m +  7 * 3600 * 24; // Next Sunday
							if ( ! $this->mn_requested_week ) $this->mn_requested_week = $d;
							$m  = date('Ymd', $m);
							$y  = date('Ymd', $y);
							$d  = date('Y-m-d', $d);

							switch ( $o )
							{
								case 'eq': $s .= "($f >= '$m' and $f < '$y')"; $s_ .= "<span class='vefv'>$f_ is $d</span>";		break;
								case 'ne': $s .= "($f < '$m' or $f >= '$y')";  $s_ .= "<span class='vefv'>$f_ is not $d</span>";	break;
								default:   $s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a {$f_}.</li>"; break;
							}
						}
					}

					if ( ! $v )
					{
						$s_error .= "<li class='seli'>We could not understand your release date parameter &quot;{$a_cmp[$i]}&quot;.</li>";
						break;
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'reldt':
			case 'created':
				$f  = array('reldt' => 'a.dvd_rel_dd' /* char(8)*/, 'created' => 'a.dvd_created_tm' /* date */); $f  = $f[$s_key];
				$f_ = array('reldt' => 'release date'  /* char(8)*/, 'created' => 'created date' /* date */); $f_ = $f_[$s_key];
				$p  = array('reldt' => false, 'created' => true); $p = $p[$s_key];
				if ( $b_miss ) { $s = $p ? "$f IS NULL" : "$f = '-'"; $s_ = "$f_ is missing"; break; }
				$a_days = array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					if ( $i > 0 ) { $s .= $a; $s_ .= $a; }
					$v = false;
					$y = 0;
					$m = 0;
					$d = 0;

					if ( preg_match('/^([0-9]{4})$/', $a_cmp[$i], $a_matches) )
					{
						$y = intval($a_matches[1]);
						$v = $y >= 1880 && $y <= (date("Y") + 1);
					}
					else
					{
						if ( preg_match('/^([0-9]{4})[-]([0-9]{2})$/', $a_cmp[$i], $a_matches) )
						{
							$y = intval($a_matches[1]);
							$m = intval($a_matches[2]);
							if ( $y >= 1880 && $y <= (date("Y") + 1) )
								$v = $m == 0 || ($m >= 1 && $m <= 12);
						}
						else
						{
							if ( preg_match('/^([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$/', $a_cmp[$i], $a_matches) )
							{
								$y = intval($a_matches[1]);
								$m = intval($a_matches[2]);
								$d = intval($a_matches[3]);
								if ( $y >= 1880 && $y <= (date("Y") + 1) )
								{
									if ( $m == 0 && $d == 0  ) $v = true; else
										if ( $m >= 1 && $m <= 12 ) $v = $d == 0 || ($d >= 1 && $d <= 31);
								}
							}
						}
					}

					if ( $v )
					{
						if ( $m && $d )
						{
							$a_days[2] = ($y % 4 == 0 && ($y % 100 != 0 || $y % 400 == 0)) ? 29 : 28;
							$v = $d <= $a_days[$m];
							if ( ! $v )
							{
								$s_error .= "<li class='seli'>The date &quot;{$a_cmp[$i]}&quot; is invalid.</li>";
								break;
							}
						}

						if ( $p )
						{
							// real date field
							if ( $d )
							{
								$vp_ = sprintf("%04d-%02d-%02d", $y, $m, $d);
								$vn_ = dvdaf3_itod(date('Ymd', jdtounix(gregoriantojd($m, $d, $y) + 2)));	// for some reason needs to add and extra 1 when translating from Julian to Std
								$vp  = sprintf("date_format('%04d-%02d-%02d','%%Y-%%m-%%d')", $y, $m, $d);
								$vn  = sprintf("date_add(date_format('%04d-%02d-%02d','%%Y-%%m-%%d'), interval 1 day)", $y, $m, $d);
							} else
								if ( $m )
								{
									$vp_ = sprintf("%04d-%02d-01", $y, $m);
									$vn_ = sprintf("%04d-%02d-01", $m < 12 ? $y : $y + 1, $m < 12 ? $m + 1 :  1);
									$vp  = sprintf("date_format('%04d-%02d-01','%%Y-%%m-%%d')", $y, $m);
									$vn  = sprintf("date_format('%04d-%02d-01','%%Y-%%m-%%d')", $m < 12 ? $y : $y + 1, $m < 12 ? $m + 1 :  1);
								}
								else
								{
									$vp_ = sprintf("%04d-01-01", $y);
									$vn_ = sprintf("%04d-01-01", $y + 1);
									$vp  = sprintf("date_format('%04d-01-01','%%Y-%%m-%%d')", $y);
									$vn  = sprintf("date_format('%04d-01-01','%%Y-%%m-%%d')", $y + 1);
								}
							switch ( $o )
							{
								case 'eq': $s .= "($f >= $vp and $f < $vn)";	$s_ .= "($f_ >= $vp_ and $f_ < $vn_)";	break;
								case 'ne': $s .= "($f < $vp or $f >= $vn)";		$s_ .= "($f_ < $vp_ or $f_ >= $vn_)";	break;
								case 'gt': $s .= "$f >= $vn";					$s_ .= "$f_ >= $vn_";					break;
								case 'ge': $s .= "$f >= $vp";					$s_ .= "$f_ >= $vp_";					break;
								case 'lt': $s .= "$f < $vp";					$s_ .= "$f_ < $vp_";					break;
								case 'le': $s .= "$f < $vn";					$s_ .= "$f_ < $vn_";					break;
							}
						}
						else
						{
							// char(8)
							if ( $d )
							{
								$v  = sprintf('%04d%02d%02d', $y, $m, $d);
								$v_ = sprintf('%04d-%02d-%02d', $y, $m, $d);
								switch ( $o )
								{
									case 'eq': $s .= "$f = '$v'";	$s_ .= "$f_ is $v_";	break;
									case 'ne': $s .= "$f <> '$v'";	$s_ .= "$f_ is not $v_";break;
									case 'gt': $s .= "$f > '$v'";	$s_ .= "$f_ > $v_";		break;
									case 'ge': $s .= "$f >= '$v'";	$s_ .= "$f_ >= $v_";	break;
									case 'lt': $s .= "$f < '$v'";	$s_ .= "$f_ < $v_";		break;
									case 'le': $s .= "$f <= '$v'";	$s_ .= "$f_ <= $v_";	break;
								}
							}
							else
							{
								if ( $m )
								{
									$v   = sprintf('%04d%02d00', $y, $m);
									$v_  = sprintf('%04d-%02d-00', $y, $m);
									$vp  = sprintf('%04d%02d00', $y, $m);
									$vp_ = sprintf('%04d-%02d-00', $y, $m);
									$vn  = sprintf('%04d%02d00', $m < 12 ? $y : $y + 1, $m < 12 ? $m + 1 :  0);
									$vn_ = sprintf('%04d-%02d-00', $m < 12 ? $y : $y + 1, $m < 12 ? $m + 1 :  0);
								}
								else
								{
									$v   = sprintf('%04d0000', $y);
									$v_  = sprintf('%04d-00-00', $y);
									$vp  = sprintf('%04d0000', $y);
									$vp_ = sprintf('%04d-00-00', $y);
									$vn  = sprintf('%04d0000', $y + 1);
									$vn_ = sprintf('%04d-00-00', $y + 1);
								}
								switch ( $o )
								{
									case 'eq': $s .= "($f >= '$v' and $f < '$vn')";	$s_ .= "($f_ >= $v_ and $f_ < $vn_)";	break;
									case 'ne': $s .= "($f < '$v' or $f >= '$vn')";	$s_ .= "($f_ < $v_ or $f_ >= $vn_)";	break;
									case 'gt': $s .= "$f >= '$vn'";					$s_ .= "$f_ >= $vn_";					break;
									case 'ge': $s .= "$f >= '$v'";					$s_ .= "$f_ >= $v_";					break;
									case 'lt': $s .= "$f < '$vp'";					$s_ .= "$f_ < $vp_";					break;
									case 'le': $s .= "$f < '$vn'";					$s_ .= "$f_ < $vn_";					break;
								}
							}
						}
					}
					else
					{
						$s_error .= "<li class='seli'>We could not understand your date parameter &quot;{$a_cmp[$i]}&quot;.</li>";
						break;
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'year':
				$f  = 'a.film_rel_year';
				$f_ = 'year';
				if ( $b_miss ) { $s = "$f = 0"; $s_ = "$f_ is missing"; break; }
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					$v = intval($a_cmp[$i]);
					if ( $i > 0 ) { $s .= $a; $s_ .= $a; }
					switch ( $o )
					{
						case 'eq': $s .= "$f = $v";					$s_ .= "$f_ is $v";		break;
						case 'ne': $s .= "$f <> $v and $f <> 0";	$s_ .= "$f_ is not $v";	break;
						case 'gt': $s .= "$f > $v";					$s_ .= "$f_ > $v";		break;
						case 'ge': $s .= "$f >= $v";				$s_ .= "$f_ >= $v";		break;
						case 'lt': $s .= "$f < $v and $f <> 0";		$s_ .= "$f_ < $v";		break;
						case 'le': $s .= "$f <= $v and $f <> 0";	$s_ .= "$f_ <= $v";		break;
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				break;

			case 'src':
				$f  = 'a.source';
				$f_ = 'source';
				if ( $b_miss ) { $s = "$f = '-'"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a source.</li>";
					break;
				}
				$a = ' or ';
				$b = $ne ? ' and ' : ' or ';
				$t = $ne ? ' not' : '';
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					if ( $i > 0 ) { $s .= $a; $s_ .= $b; }
					switch ( $a_cmp[$i] )
					{
						case 'a': $s .= "$f = 'A'";	$s_ .= "$f_ is$t DVD package";				break;
						case 'i': $s .= "$f = 'I'";	$s_ .= "$f_ is$t part of DVD package";		break;
						case 'e': $s .= "$f = 'E'";	$s_ .= "$f_ is$t DVD package bonus disc";	break;
						case 'c': $s .= "$f = 'C'";	$s_ .= "$f_ is$t audio CD bonus disc";		break;
						case 'g': $s .= "$f = 'G'";	$s_ .= "$f_ is$t game bonus disc";			break;
						case 'b': $s .= "$f = 'B'";	$s_ .= "$f_ is$t book bonus disc";			break;
						case 'm': $s .= "$f = 'M'";	$s_ .= "$f_ is$t magazine bonus disc";		break;
						case 'o': $s .= "$f = 'O'";	$s_ .= "$f_ is$t other product bonus disc";	break;
						case 't': $s .= "$f = 'T'";	$s_ .= "$f_ is$t theatrical or broadcast";	break;
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				if ( $s && $ne			 ) $s = "not ($s)";
				break;

			case 'pic':
				$f  = 'a.pic_status';
				$f_ = 'picture';
				if ( $b_miss ) { $s = "$f = '-'"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a picture status.</li>";
					break;
				}
				$a = ' or ';
				$b = $ne ? ' and ' : ' or ';
				$t = $ne ? ' not' : '';
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					if ( $i > 0 ) { $s .= $a; $s_ .= $b; }
					switch ( $a_cmp[$i] )
					{
						case 'n': $s .= "$f = '-'";	$s_ .= "$f_ is$t missing";				break;
						case 'y': $s .= "$f = 'Y'";	$s_ .= "$f_ is$t the DVD cover art";	break;
						case 'p': $s .= "$f = 'P'";	$s_ .= "$f_ is$t the film poster";		break;
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				if ( $s && $ne			 ) $s = "not ($s)";
				break;

			case 'rgn':
				$f  = 'a.region_mask';
				$f1 = 'a.country_block';
				$f_ = 'region';
				if ( $b_miss ) { $s = "$f = 0"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a DVD region.</li>";
					break;
				}
				$a = ' or ';
				$b = $ne ? ' and ' : ' or ';
				$t = $ne ? ' not' : '';
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					if ( $i > 0 ) { $s .= $a; $s_ .= $b; }

					switch ( $a_cmp[$i] )
					{
						case 'us': $s .= "$f1 like '%,us,%'";	$s_ .= "publisher's country is 'U.S. or Canada'";							break;
						case 'uk': $s .= "$f1 like '%,uk,%'";	$s_ .= "publisher's country is 'U.K.'";										break;
						case 'eu': $s .= "$f1 like '%,eu,%'";	$s_ .= "publisher's country in 'Europe or Africa'";							break;
						case 'la': $s .= "$f1 like '%,la,%'";	$s_ .= "publisher's country in 'Latin America'";							break;
						case 'as': $s .= "$f1 like '%,as,%'";	$s_ .= "publisher's country in 'Russia, China, or most of Asia'";			break;
						case 'sa': $s .= "$f1 like '%,sa,%'";	$s_ .= "publisher's country in 'Southeast Asia'";							break;
						case 'jp': $s .= "$f1 like '%,jp,%'";	$s_ .= "publisher's country is 'Japan'";									break;
						case 'au': $s .= "$f1 like '%,au,%'";	$s_ .= "publisher's country is 'Australia or New Zealand'";					break;

						case 'z':
						case '0':  $s .= "$f & 1";				$s_ .= "is$t region 0 (plays on any player)";								break;
						case '1':  $s .= "$f & 2";				$s_ .= "is$t region 1 (DVD: US and Canada)";								break;
						case '2':  $s .= "$f & 4";				$s_ .= "is$t region 2 (DVD: Europe, Middle East, Japan and South Africa)";	break;
						case '3':  $s .= "$f & 8";				$s_ .= "is$t region 3 (DVD: Southeast Asia)";								break;
						case '4':  $s .= "$f & 16";				$s_ .= "is$t region 4 (DVD: Australia, New Zealand and Latin America)";		break;
						case '5':  $s .= "$f & 32";				$s_ .= "is$t region 5 (DVD: Africa, Eastern Europe and the rest of Asia)";	break;
						case '6':  $s .= "$f & 64";				$s_ .= "is$t region 6 (DVD: China and Hong Kong)";							break;
						case 'a':  $s .= "$f & 128";			$s_ .= "is$t region A (Blu-ray: Americas, Japan, Korea and Southeast Asia)";break;
						case 'b':  $s .= "$f & 256";			$s_ .= "is$t region B (Blu-ray: Europe, Australia, New Zealand and Africa)";break;
						case 'c':  $s .= "$f & 512";			$s_ .= "is$t region C (Blu-ray: Eastern Europe and the rest of Asia)";		break;
						case 'u':  $s .= "$f = 0";				$s_ .= "is$t unknown region";												break;
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				if ( $s && $ne		   ) $s = "not ($s)";
				break;

			case 'med':
				$f  = 'a.media_type';
				$f_ = 'media type';
				if ( $b_miss ) { $s = "$f = 0"; $s_ = "$f_ is missing"; break; }
				if ( $cp )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a media type.</li>";
					break;
				}
				$a = ' or ';
				$b = $ne ? ' and ' : ' or ';
				$t = $ne ? ' not' : '';
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					if ( $i > 0 ) { $s .= $a; $s_ .= $b; }
					switch ( $a_cmp[$i] )
					{
						case 'd':  $s .= "$f = 'D'";	$s_ .= "is$t DVD";						break;
						case 'b':  $s .= "$f = 'B'";	$s_ .= "is$t Blu-ray";					break;
						case '3':  $s .= "$f = '3'";	$s_ .= "is$t Blu-ray 3D";				break;
						case '2':  $s .= "$f = '2'";	$s_ .= "is$t Blu-ray/DVD Combo";		break;
						case 'r':  $s .= "$f = 'R'";	$s_ .= "is$t BD-R";						break;
						case 'v':  $s .= "$f = 'V'";	$s_ .= "is$t DVD-R";					break;
						case 'h':  $s .= "$f = 'H'";	$s_ .= "is$t HD DVD";					break;
						case 'c':  $s .= "$f = 'C'";	$s_ .= "is$t HD DVD/DVD Combo";			break;
						case 't':  $s .= "$f = 'T'";	$s_ .= "is$t HD DVD/DVD TWIN Format";	break;
						case 'a':  $s .= "$f = 'A'";	$s_ .= "is$t DVD Audio";				break;
						case 'p':  $s .= "$f = 'P'";	$s_ .= "is$t Placeholder";				break;
						case 'o':  $s .= "$f = 'O'";	$s_ .= "is$t Other";					break;
						case 'f':  $s .= "$f = 'F'";	$s_ .= "is$t Film";						break;
						case 's':  $s .= "$f = 'S'";	$s_ .= "is$t Short";					break;
						case 'l':  $s .= "$f = 'L'";	$s_ .= "is$t Television";				break;
						case 'e':  $s .= "$f = 'E'";	$s_ .= "is$t Featurette";				break;
						case 'n':  $s .= "$f = 'N'";	$s_ .= "is$t Events &amp; Performances";break;
					}
				}
				if ( $s && $eq && $n > 1 ) $s = "($s)";
				if ( $s && $ne			 ) $s = "not ($s)";
				break;

			case 'where':
				if ( $cp && $o != 'eqne' )
				{
					$s_error .= "<li class='seli'>Sorry. &gt;, &lt;, &gt;= and &lt;= are not supported when specifying a Film Collection.</li>";
					break;
				}
				if ( $n > 2 ) $n = 2;
				switch ( $n )
				{
					case 1:
						$v = $a_cmp[0];
						switch ( $o )
						{
							case 'eq': // is
								$s_folder = dvdaf3_getvalue('folder', DVDAF3_GET);
								if ( $s_folder )
								{
									$b   = $this->ms_user_id == $v;
									$s  .= "exists (SELECT 1 FROM ". ($b ? 'my_dvd' : 'v_my_dvd_pub '). " x WHERE a.dvd_id = x.dvd_id and x.user_id = '$v' and x.folder like '{$s_folder}%')";
									$s_ .= "in '$v' collection ". ($b ? "<span class='dm'>PRIVATE</span> " : ''). ") and (in folder '{$s_folder}'";
								}
								else
								{
									$this->ms_in_collection = " JOIN v_my_dvd_sch x ON a.dvd_id = x.dvd_id and x.user_id = '$v'";
									$s  .= "exists (SELECT 1 FROM v_my_dvd_sch x WHERE a.dvd_id = x.dvd_id and x.user_id = '$v')";
									$s_ .= "in '$v' collection";
								}
								break;
							case 'ne': // is not
								$s  .= "not exists (SELECT 1 FROM v_my_dvd_sch x WHERE a.dvd_id = x.dvd_id and x.user_id = '$v')";
								$s_ .= "not in '$v' collection";
								break;
						}
						break;
					case 2:
						$v1 = $a_cmp[0];
						$v2 = $a_cmp[1];
						switch ( $o )
						{
							case 'eq': // is + is
								$s  .= "a.dvd_id in (SELECT x.dvd_id FROM v_my_dvd_sch x, v_my_dvd_sch y WHERE x.dvd_id = y.dvd_id and x.user_id = '$v1' and y.user_id = '$v2')";
								$s_ .= "in '$v1' and in '$v2' collection";
								break;
							case 'eqne':
								// is + is not
								$s  .= "a.dvd_id in (SELECT x.dvd_id ".
									"FROM v_my_dvd_sch x ".
									"WHERE x.user_id = '$v1' ".
									"and not exists (SELECT 1 FROM v_my_dvd_sch y WHERE x.dvd_id = y.dvd_id and y.user_id = '$v2'))";
								$s_ .= "in '$v1', but not in '$v2' collection";
								break;
							case 'ne': // is not + is not
								// $s  .= "not exists (SELECT 1 from v_my_dvd_sch x where a.dvd_id = x.dvd_id and x.user_id = '$v1')";
								// $s  .= " and ";
								// $s  .= "not exists (SELECT 1 from v_my_dvd_sch x where a.dvd_id = x.dvd_id and x.user_id = '$v2')";
								$s  .= "a.dvd_id not in (SELECT a.dvd_id FROM v_my_dvd_sch x WHERE a.dvd_id = x.dvd_id and x.user_id = '$v1') ".
									"UNION ".
									"SELECT a.dvd_id FROM v_my_dvd_sch x WHERE a.dvd_id = x.dvd_id and x.user_id = '$v2'))";
								$s_ .= "neither in '$v1' nor in '$v2' collection";
								break;
						}
						break;
				}
				break;
			case 'ord':
				for ( $i = 0 ; $i < $n ; $i++ )
				{
					switch ( $a_cmp[$i] )
					{
						case 'created': $this->ms_requested_sort .= 'a.dvd_created_tm DESC, '; break;
					}
				}
				break;
		}

		if ( $s && $this->mb_release_week )
		{
			switch ( $s_key )
			{
				case 'med':
				case 'pubct':
				case 'week':
					break;
				default:
					$this->mb_added_criteria = true;
					break;
			}
		}
		$s_desc   = $s_;
		return $s;
	}

	function splitWhereCommas($s_key, $s_cmp, &$a_parm, &$s_error)
	{
		$a_cmp	= explode(',', $s_cmp);
		$n_cmp	= count($a_cmp);
		$a_ret	= Array();
		$n_ret	= 0;
		$a_parm	= Array();

		//echo "s_key = [$s_key], s_cmp = [$s_cmp]<br />";
		if ( $n_cmp == 1 && $a_cmp[0] == 'missing' )
		{
			$a_parm[0] = false;
			$a_ret[0]  = $a_cmp[0];
			return $a_ret;
		}

		for ( $i = 0 ; $i < $n_cmp ; $i++ )
		{
			$s_cmp		= trim($a_cmp[$i]);
			$x_parm		= null;
			$re_badchr	= null;
			$re_exact	= null;

			// validate it
			if ( $s_cmp )
			{
				$s_cmc = $s_cmp;
				$s_cmp = strtolower($s_cmp);
				switch ( $s_key )
				{
					case 'has':
					case 'beg':
					case 'dir':
					case 'pub':
						// use search string reducer
						$s_cmp = dvdaf3_translatestring($s_cmc, DVDAF3_SEARCH);
						if ( substr($s_cmc,-2) == '//' ) { $x_parm = true; $s_cmp .= '//'; } else
							if ( substr($s_cmc,-1) == '/'  ) { $x_parm = true; $s_cmp .= '/';  }
						break;

					case 'price':
						// must be a floating point number, but allows for % [0-9\.]
						if ( substr($s_cmp,-1) == '%' )
						{
							$x_parm = true;
							$s_cmp  = trim(substr($s_cmp,0,-1));
						}
						$re_badchr = '/[^0-9\.]/';
						break;

					case 'asin':
						// [a-z0-9]
						$re_badchr = '/[^a-z0-9]/';
						break;

					case 'imdb':
						if ( preg_match('/^([0-9]+)(-[0-9]+)*$/', $s_cmp, $a_matches) )
						{
							if ( count($a_matches) == 3 )
							{
								$n_ndx  = intval($a_matches[1]);
								$s_imdb = CSql::query_and_fetch1("SELECT imdb_id FROM dvd WHERE dvd_id = {$n_ndx}", 0,__FILE__,__LINE__);
								$n_ndx  = intval(substr($a_matches[2],1));
								$s_imdb = explode(' ', $s_imdb);
								if ( count($s_imdb) > $n_ndx )
								{
									$s_cmp = $s_imdb[$n_ndx];
									break;
								}
							}
						}
						$re_badchr = '/[^0-9]/';
						break;

					case 'upc':
					case 'year':
						// [0-9]
						$re_badchr = '/[^0-9]/';
						break;

					case 'pubct':
						// exact match
						if ( ! strpos('-,us,ar,at,au,be,br,ca,ch,cl,cn,cu,cz,de,dk,ee,es,fi,fr,gr,hk,hr,hu,id,il,in,is,it,jp,kr,lt,mk,mx,my,nl,no,nz,ph,pl,pt,ro,rs,ru,se,sg,si,sk,th,tr,tw,uk,un,za,missing,', ",{$s_cmp},") )
						{
							$s_error .= "<li class='seli'>Unrecognized country code &quot;{$s_cmp}&quot; in search string for &quot;{$s_key}.&quot;</li>";
							$s_cmp = '';
						}
						break;

					case 'genre':
						// exact match
						$a_field = '-:10000:action:10100:action-comedy:10200:action-crime:10300:action-disaster:10400:action-epic:10500:action-espionage:10600:action-martialarts:10700:action-military:10750:action-samurai:10999:action-nosub:13000:animation:13100:animation-cartoons:13300:animation-family:13600:animation-mature:13700:animation-puppetrystopmotion:13800:animation-scifi:13900:animation-superheroes:13999:animation-nosub:16000:anime:16200:anime-action:16250:anime-comedy:16300:anime-drama:16400:anime-fantasy:16500:anime-horror:16600:anime-mahoushoujo:16700:anime-martialarts:16750:anime-mecha:16800:anime-moe:16850:anime-romance:16900:anime-scifi:16999:anime-nosub:20000:comedy:20100:comedy-dark:20200:comedy-farce:20300:comedy-horror:20400:comedy-romantic:20600:comedy-satire:20650:comedy-scifi:20700:comedy-screwball:20750:comedy-sitcom:20800:comedy-sketchesstandup:20850:comedy-slapstick:20900:comedy-teen:20999:comedy-nosub:24000:documentary:24100:documentary-biography:24200:documentary-crime:24250:documentary-culture:24270:documentary-entertainment:24300:documentary-history:24400:documentary-nature:24500:documentary-propaganda:24600:documentary-religion:24700:documentary-science:24750:documentary-social:24800:documentary-sports:24900:documentary-travel:24999:documentary-nosub:28000:drama:28100:drama-courtroom:28150:drama-crime:28200:drama-docudrama:28400:drama-melodrama:28600:drama-period:28800:drama-romance:28900:drama-sports:28950:drama-war:28999:drama-nosub:32000:educational:32200:educational-children:32700:educational-school:32999:educational-nosub:36000:erotica:36100:erotica-hentai:36999:erotica-nosub:39999:experimental:41000:exploitation:41100:exploitation-blaxploitation:41300:exploitation-nazisploitation:41400:exploitation-nunsploitation:41500:exploitation-pinkueiga:41600:exploitation-sexploitation:41700:exploitation-shockumentary:41800:exploitation-wip:41999:exploitation-nosub:43999:fantasy:47999:filmnoir:'.
							'55000:horror:55050:horror-anthology:55250:horror-creatureanimal:55300:horror-espghosts:55350:horror-eurotrash:55400:horror-exploitation:55450:horror-gialli:55500:horror-goreshock:55550:horror-gothic:55700:horror-possessionsatan:55800:horror-shockumentary:55850:horror-slashersurvival:55900:horror-vampires:55950:horror-zombiesinfected:55960:horror-otherundead:55999:horror-nosub:59000:music:59300:music-liveinconcert:59700:music-musicvideos:59999:music-nosub:62999:musical:66000:performing:66100:performing-circus:66300:performing-concerts:66500:performing-dance:66700:performing-operas:66900:performing-theater:66999:performing-nosub:70000:scifi:70100:scifi-alien:70200:scifi-alternatereality:70250:scifi-apocalyptic:70300:scifi-cyberpunk:70400:scifi-kaiju:70500:scifi-lostworlds:70550:scifi-military:70600:scifi-otherworlds:70800:scifi-space:70850:scifi-spacehorror:70870:scifi-superheroes:70900:scifi-utopiadystopia:70999:scifi-nosub:73999:short:76000:silent:76100:silent-animation:76300:silent-horror:76500:silent-melodrama:76700:silent-slapstick:76800:silent-western:76999:silent-nosub:80000:sports:80100:sports-baseball:80130:sports-basketball:80170:sports-biking:80200:sports-fitness:80250:sports-football:80300:sports-golf:80350:sports-hockey:80400:sports-martialarts:80450:sports-motorsports:80500:sports-olympics:80600:sports-skateboard:80700:sports-skiing:80800:sports-soccer:80850:sports-tennis:80900:sports-wrestling:80999:sports-nosub:84000:suspense:84400:suspense-mystery:84700:suspense-thriller:84999:suspense-nosub:88000:war:88200:war-uscivilwar:88300:war-wwi:88400:war-wwii:88500:war-korea:88600:war-vietnam:88700:war-postcoldwar:88900:war-other:88999:war-nosub:91000:western:91400:western-epic:91700:western-singingcowboy:91800:western-spaghetti:91999:western-nosub:95999:dvdaudio:98000:other:98200:other-digitalcomicbooks:98250:other-gameshows:98300:other-games:98999:other-nosub:99999:unspecifiedgenre:';
						$n_pos = strpos($a_field, ':'.$s_cmp.':');
						if ( $n_pos > 0 )
						{
							$s_cmp = substr($a_field,$n_pos-5,5);
						}
						else
						{
							$s_error .= "<li class='seli'>Unrecognized genre &quot;{$s_cmp}&quot; in search string for &quot;{$s_key}.&quot;</li>";
							$s_cmp = '';
						}
						break;

					case 'lang':
						// exact match
						if ( ! strpos('-,en,am,ar,bg,bn,br,ca,cs,ct,cz,de,dk,eo,es,et,fa,fi,fr,ge,gr,he,hi,ho,hu,id,il,in,is,it,jp,kh,kl,kr,ku,kz,la,lt,lv,ma,mk,ml,mn,my,nl,no,nz,ot,ph,pl,pt,pu,rm,ro,ru,sc,se,si,sk,sl,ta,te,th,tr,tw,uk,un,ur,ve,vi,missing,', ",{$s_cmp},") )
						{
							$s_error .= "<li class='seli'>Unrecognized language code &quot;{$s_cmp}&quot; in search string for &quot;{$s_key}.&quot;</li>";
							$s_cmp = '';
						}
						break;

					case 'src':
						// exact match
						$re_exact = '/^[aiecgbmot]$/';
						break;

					case 'pic':
						// exact match
						$re_exact = '/^[yfnp]$/';
						break;

					case 'rel':
						// exact match or +w__ -w__
						if ( preg_match('/^[-]?[0-9]{1,3}$/', $s_cmp) )
							$s_cmp = intval($s_cmp);
						else
							$re_exact = '/^[coandxu]$/';
						break;

					case 'reldt':
					case 'created':
						$re_exact = '/^[0-9]{4}(-?[0-9]{1,2}(-?[0-9]{1,2})?)?$/';
						break;

					case 'rgn':
						if ( $s_cmp == 'all' ) $s_cmp = '';
						$re_exact = '/^([0-6zuabc]|us|uk|eu|la|as|sa|jp|au)$/';
						break;

					case 'med':
						if ( $s_cmp == 'all' ) $s_cmp = '';
						$re_exact = '/^[db32rvhctapofslen]$/';
						break;

					case 'where':
						if ( $s_cmp == 'all' ) $s_cmp = '';
						$re_exact = '/^[a-z0-9-]+$/';
						break;

					case 'ord':
						$re_exact = '/^(created)$/';
						break;
				}
			}

			// finish validating with regexps set above for unexpected characters and exact matches
			if ( $s_cmp !== '' )
			{
				if ( $re_badchr )
				{
					if ( preg_match($re_badchr, $s_cmp, $a_matches) )
					{
						$s_error .= "<li class='seli'>Unexpected character &quot;{$a_matches[0]}&quot; in search string for &quot;{$s_key}.&quot;</li>";
						$s_cmp = '';
					}
				}
				else
				{
					if ( $re_exact )
					{
						if ( ! preg_match($re_exact, $s_cmp) )
						{
							$s_error .= "<li class='seli'>Bad search string &quot;{$s_cmp}&quot; for &quot;{$s_key}.&quot;</li>";
							$s_cmp = '';
						}
					}
				}
			}

			// check string length and number ranges
			if ( $s_cmp !== '' )
			{
				$min_len = 0;
				switch ( $s_key )
				{
					case 'beg':	$min_len = 2; break;
					case 'has':	$min_len = 3; break;
					case 'asin':	$min_len = $n_cmp > 1 ? 10 : 5; break;
					case 'upc':	$min_len = $n_cmp > 1 ? 12 : 5; break;
					case 'dir':	$min_len = 3; break;
					case 'pub':	$min_len = 3; break;
					case 'price':	if ( $s_cmp < 0 || $s_cmp >  1000	 ) { $s_error .= "<li class='seli'>Out of range value &quot;{$s_cmp}&quot; for &quot;{$s_key}.&quot;</li>"; $s_cmp = ''; } break;
					case 'imdb':	if ( $s_cmp <= 0 || $s_cmp >= 10000000 ) { $s_error .= "<li class='seli'>Out of range value &quot;{$s_cmp}&quot; for &quot;{$s_key}.&quot;</li>"; $s_cmp = ''; } break;
				}
				if ( $min_len > 0 && strlen($s_cmp) < $min_len )
				{
					$s_error .= "<li class='seli'>String &quot;{$s_cmp}&quot; too small (<{$min_len}) for &quot;{$s_key}.&quot;</li>";
					$s_cmp = '';
				}
			}

			if ( $s_cmp !== '' )
			{
				$a_parm[$n_ret]	 = $x_parm;
				$a_ret[$n_ret++] = $s_cmp;
			}
		}

		return $a_ret;
	}
}

?>
