<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CUserUtils
{
	function getUserDomain($s_user, $b_link)
	{
		$s_url = "http://{$s_user}{$this->ms_unatrib_subdomain}";
		return $b_link ? "<a href='{$s_url}'>$s_user</a>" : $s_url;
	}
}

//////////////////////////////////////////////////////////////////////////

?>
