<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

class CFacebook extends CAjax
{
    // ?dir=dfsdgsg
    function getSql()
    {
		$this->get_requester();
		$this->ms_act     = dvdaf3_getvalue('act', DVDAF3_GET);
		$this->ms_context = "act='{$this->ms_act}' ";

		switch ( $this->ms_act )
		{
		case 'log':
			$n_dvd_id		= dvdaf3_getvalue('id'			,DVDAF3_POST|DVDAF3_INT);
			$s_media		= dvdaf3_getvalue('media'		,DVDAF3_POST,1);
			$s_message		= dvdaf3_getvalue('message'		,DVDAF3_POST,512);
			$n_start_time	= dvdaf3_getvalue('start_time'	,DVDAF3_POST|DVDAF3_INT);
			$n_end_time		= dvdaf3_getvalue('end_time'	,DVDAF3_POST|DVDAF3_INT);
			$n_rating		= dvdaf3_getvalue('rating'		,DVDAF3_POST|DVDAF3_FLOAT);
			$n_rating		= intval($n_rating * 2 + 0.05);
			$s_tags			= dvdaf3_getvalue('tags'		,DVDAF3_POST,256);
			$s_response		= dvdaf3_getvalue('response'	,DVDAF3_POST|DVDAF3_INT);

			$ss = "INSERT INTO fb_posts (dvd_id, media, message, start_time, end_time, rating, tags, response_id, user_id, post_tm) ".
				  "VALUES ({$n_dvd_id}, '{$s_media}', '{$s_message}', {$n_start_time}, {$n_end_time}, {$n_rating}, '{$s_tags}', '{$s_response}', '{$this->ms_requester}', now())";
			CSql::query_and_free($ss,0,__FILE__,__LINE__);
			break;
		}

		return false;
    }
}

$a = new CFacebook();
$a->main();

?>
