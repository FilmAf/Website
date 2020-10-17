<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function formNaviPage($s_href, $n_cur, $n_tot, $s_class, $s_onclick, $s_supp, $b_index, $b_left_alpha, $b_postfix_0)
{
    // $s_href:		left part of URL before the "pg" parameter
    // $n_cur:		current page
    // $n_tot:		total number of pages
    // $s_class:	class for the anchor tag as in "mg"
    // $s_onclick:	function to be called on anchor click as in "DvdMine.validate(0)"
    // $s_supp:		right part of the URL after the "pg", must start with a "&"
    // $b_index:	show link to "Index"
    // $b_left_alpha:	put the index, prev, nnd ext to the left instead of to the right

    $n_pre   = $n_cur - 1;
    $n_nxt   = $n_cur + 1;
    $s_alpha = '';
    $str     = '';

    if ( $b_index )		 $s_alpha .= formNaviPageLink($s_href, $s_class, $s_onclick, $s_supp, -1    , 'Index'             , 'index') .'&nbsp;';
    $s_alpha .= ($n_cur == 1      ? 'prev' : formNaviPageLink($s_href, $s_class, $s_onclick, $s_supp, $n_pre, 'Show previous page', 'prev' )).'&nbsp;'.
		($n_cur == $n_tot ? 'next' : formNaviPageLink($s_href, $s_class, $s_onclick, $s_supp, $n_nxt, 'Show next page'    , 'next' ));

    $str    = $b_left_alpha ? $s_alpha . '&nbsp;&nbsp;' : '';

    if ( $n_tot <= 7 )
    {
	for ( $i = 1 ; $i <= $n_tot ; $i++ )    $str .= '&nbsp;'.($i == $n_cur     ? "$i"     : formNaviPageLink($s_href,$s_class,$s_onclick,$s_supp,    $i,formNaviPageDecr($i, $n_tot),"$i"    ));
	$s_drop  = '';
    }
    else
    {
	$n_r1 = max(         2, $n_cur - 2);
	$n_r2 = min($n_tot - 1, $n_cur + 2);
						$str .= '&nbsp;'.($n_cur == 1      ? '1'      : formNaviPageLink($s_href,$s_class,$s_onclick,$s_supp,     1,'Show first page'		,"1"     ));
	if  ( $n_r1 != 2 )			$str .= '...';
	for ( $i = $n_r1 ; $i <= $n_r2 ; $i++ ) $str .= '&nbsp;'.($i == $n_cur     ? "$i"     : formNaviPageLink($s_href,$s_class,$s_onclick,$s_supp,    $i,formNaviPageDecr($i, $n_tot),"$i"    ));
	if  ( $n_r2 != $n_tot - 1 )		$str .= '...';
						$str .= '&nbsp;'.($n_cur == $n_tot ? "$n_tot" : formNaviPageLink($s_href,$s_class,$s_onclick,$s_supp,$n_tot,'Show last page'		,"$n_tot"));
	$s_drop = " <img src='http://dv1.us/di/00/dp00.gif' id='dp_jump".($b_postfix_0 ? '_0' : '')."' sp_max='{$n_tot}' height='15' width='14' alt='Jump to page' align='top' />";
    }

    return ($b_left_alpha ? $str : substr($str,6) . '&nbsp;&nbsp;&nbsp;' . $s_alpha) . $s_drop;
}

function formNaviPageLink($s_href, $s_class, $s_onclick, $s_supp, $n_page, $s_title, $s_text)
{
    if ( $s_onclick )
    {
	$n_pos = strpos($s_onclick,')');
	if ( $n_pos )
	    $s_onclick = substr($s_onclick,0,$n_pos) . ($s_onclick{$n_pos - 1} == '(' ? '' : ',') ."$n_page" . substr($s_onclick,$n_pos);
	else
	    $s_onclick = $s_onclick . "({$n_page})";
    }

    if ( $n_page != '0' && $n_page != '1' ) $s_href .= (strpos($s_href,'?') ? '&' : '?'). "pg=$n_page";
    if ( $s_supp                          ) $s_href .= (strpos($s_href,'?') ? '&' : '?'). substr($s_supp,1);

    return "<a".
	   ($s_class   ? " class='{$s_class}'" : '').
	   ($s_onclick ? " onclick='{$s_onclick}'" : '').
	   " href='{$s_href}'".
	   ($s_title   ? " title='$s_title'" : '').
	   ">$s_text</a>";
}

function formNaviPageDecr($n_cur, $n_tot)
{
    return $n_cur == 1 ? 'Show first page' : ($n_cur == $n_tot ? 'Show last page' : "Show page $n_cur");
}

?>
