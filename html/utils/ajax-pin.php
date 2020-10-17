<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

class CUpload extends CAjax
{
    function main()
    {
		$this->get_requester();
		if ( $this->ms_requester == '' || $this->ms_requester == 'guest' ) return;

		$this->ms_pin			= dvdaf3_getvalue('pin',DVDAF3_GET);
		$this->ms_context		= $this->ms_pin != '' ? "pin='{$this->ms_pin}' " : '';
		$this->mn_region_state	= '';
		$this->ms_region_opt	= '';
		$this->mn_media_state	= '';
		$this->ms_media_opt		= '';

		if ( $this->ms_pin == 'cls' )
		{
			$this->mn_count	= CSql::query_and_free("UPDATE dvdaf_user SET pinned = '-' WHERE user_id = '{$this->ms_requester}' and pinned <> '-'",0,__FILE__,__LINE__);
		}
		else
		{
			$a_parm = explode('*',$this->ms_pin);
			$s_parm = '';
			for ( $i = 0 ; $i < count($a_parm) ; $i++ )
			{
				$a_item = explode('_', $a_parm[$i]);
				switch ( $a_item[0] )
				{
				case 'str0':
				case 'str1':
				case 'str2':
				case 'str3': if ( preg_match('/^(has|pricele|pricege|asin|imdb|upc|dir|pub|pubct|genre|rel|reldt|year|lang|pic|src|created)$/',$a_item[1]) ) $s_parm .= "*{$a_item[0]}_{$a_item[1]}_{$a_item[2]}"; break;
				case 'rgn':  if ( preg_match('/^(us|uk|eu|la|as|se|jp|au|z|1|1,b,0|2|2,b,0|3|4|5|6|a|b|c|all)$/'							  ,$a_item[1]) ) $s_parm .= "*rgn_{$a_item[1]}"; break;
				case 'med':  if ( preg_match('/^(all|d,v|b,3,2,r|3|h,c,t|a,p,o|f,s,l,e,n)$/'												  ,$a_item[1]) ) $s_parm .= "*med_{$a_item[1]}"; break;
				case 'xcmy': if ( preg_match('/^[012]$/'																					  ,$a_item[1]) ) $s_parm .= "*xcmy_{$a_item[1]}"; break;
				}
			}

			$s_parm = $s_parm == '' ? '-' : substr($s_parm,1);
			$this->mn_count	= CSql::query_and_free("UPDATE dvdaf_user SET pinned = '$s_parm' WHERE user_id = '{$this->ms_requester}' and pinned <> '$s_parm'",0,__FILE__,__LINE__);
		}

		$this->respond();
    }

    function respond()
    {
		echo  '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'.
			  "<html>".
				"<head><title>404 Not Found</title><meta http-equiv='Content-type' content='text/html; charset=UTF-8' /></head>".
				"<body>".
				  "<h1>Not Found</h1>".
				  "<p>The requested URL /utils/ajax.php was not found on this server.</p>\n".
				  "<div style='visibility:hidden' {$this->ms_context}count='{$this->mn_count}'></div>\n".
				"</body>".
			  "</html>";
    }
}

$a = new CUpload();
$a->main();

?>
