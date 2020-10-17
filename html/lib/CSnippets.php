<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CSnippets
{
	function drawAddThis()
	{
		echo
		"<script type='text/javascript'>var addthis_pub='dvdaf';</script>".
		"<a href='http://www.addthis.com/bookmark.php?v=20' onmouseover='return addthis_open(this, \"\", \"[URL]\", \"[TITLE]\")' onmouseout='addthis_close()' onclick='return addthis_sendto()'>".
		  "<img src='http://s7.addthis.com/static/btn/lg-share-en.gif' width='125' height='16' alt='Bookmark and Share' style='border:0'/>".
		"</a>".
		"<script type='text/javascript' src='http://s7.addthis.com/js/200/addthis_widget.js'></script>";
	}
	function drawFollowUs()
	{
		echo
		"<div class='addthis_toolbox addthis_default_style' style='float:left'>".
		  "<span style='white-space:nowrap;float:left'>Follow us:</span>".
		  "<a class='addthis_button_facebook_follow' addthis:userid='filmafi'></a>".
		  "<a class='addthis_button_twitter_follow' addthis:userid='dvdaf'></a>".
		  "<a class='addthis_button_youtube_follow' addthis:userid='FilmAf'></a>".
		  "<span style='white-space:nowrap;float:left'>&nbsp;&nbsp;</span>".
		"</div>".
		"<script type='text/javascript' src='//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-50aecb1b12a0d41a'></script>";

	}
	function drawQuantcast()
	{
		echo
		"<script type='text/javascript'>_qoptions={qacct:'p-f4DZHT-AOEy72'};</script>".
		"<script type='text/javascript' src='http://edge.quantserve.com/quant.js'></script>".
		"<noscript><img src='http://pixel.quantserve.com/pixel/p-f4DZHT-AOEy72.gif' style='display: none;' border='0' height='1' width='1' /></noscript>";
	}
	function drawGoogleAnalytics()
	{
		echo
		"<script type='text/javascript'>".
			"var gaJsHost = (('https:' == document.location.protocol) ? 'https://ssl.' : 'http://www.');".
			"document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));".
		"</script>".
		"<script type='text/javascript'>try {var pageTracker = _gat._getTracker('UA-8030055-2');pageTracker._trackPageview();} catch(err) {}</script>";
	}
}

//////////////////////////////////////////////////////////////////////////

?>
