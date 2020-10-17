<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

define('CPic_THUMB' ,'0');
define('CPic_PIC'   ,'1');
define('CPic_BORDER','4');

class CPic
{
	function dir($s_pic_name)
	{
		if ( ($n = strpos($s_pic_name,'-')) <= 2 ) $n = strpos($s_pic_name,'.');
		return $n > 2 ? substr($s_pic_name, $n - 3, 3) : '';
	}

	function server($s_pic_name)
	{
		return '';
//		if ( ($n = strpos($s_pic_name,'-')) <= 2 ) $n = strpos($s_pic_name,'.');
//		if ( $n <= 2 ) return '';
//		$c = intval($s_pic_name{$n - 1});
//		return $c <= 1 ? '' : ($c <= 4 ? 'a.' : ($c <= 6 ? 'b.' : 'c.'));
	}

	function location($s_pic_name, $c_which /* CPic_* */)
	{
		if ( ($n = strpos($s_pic_name,'-')) <= 2 ) $n = strpos($s_pic_name,'.');
		if ( $n <= 2 ) return '';
//		$c = intval($s_pic_name{$n - 1});
//		$c = $c <= 1 ? '' : ($c <= 4 ? 'a.' : ($c <= 6 ? 'b.' : 'c.'));
		$c = '';
		$p = strpos($s_pic_name,'/');
		if ( $p !== false )
			return "http://{$c}dv1.us" . ($p > 0 ? '/' : '') . $s_pic_name;
		else
			return "http://{$c}dv1.us/p{$c_which}/" . substr($s_pic_name, $n - 3, 3) . '/' . $s_pic_name . ($c_which == CPic_THUMB ? '.gif' : '.jpg');
	}
}

?>
