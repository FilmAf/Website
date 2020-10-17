<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CAdvert
{
	function drawAddThis($f)
	{
		echo  "<div id='addthis' style='white-space:nowrap'>";
		if($f)CSnippets::drawFollowUs();
			  CSnippets::drawAddThis();
		echo  "</div>";
	}
	function drawSponsors()
	{
		$blk  = "' target='_blank'>";
		$href = "<a href='/vd.php?vd=";
		// Make sure to change Vendor.cpp, UnitTest_SnippetPriceOne.cpp, vd.php, CVendor.php, CAdvert.php, mil-dvd-price.js if the below changes

		echo
		"<div class='sponsor-a'><div class='sponsor-b'><div class='sponsor-c'><div class='sponsor-d'>".
		  "<div>Sponsors</div>".
		  "<div>Help FilmAf by using them when shopping</div>".
		  "<div class='sponsor-h'>&nbsp;</div>".
		  "{$href}amz{$blk}Amazon</a> ".
		  "{$href}amz.ca{$blk}Amz.ca</a> ".
		  "{$href}amz.uk{$blk}Amz.uk</a> ".
		  "{$href}amz.fr{$blk}Amz.fr</a> ".
		  "{$href}amz.de{$blk}Amz.de</a> ".
		  "{$href}amz.it{$blk}Amz.it</a> ".
		  "{$href}amz.es{$blk}Amz.es</a> ".
		  "{$href}amz.jp{$blk}Amz.jp</a> ".
		  "{$href}deep{$blk}DeepDiscount</a> ".
		  "{$href}yesa{$blk}YesAsia</a> ".
		  "{$href}buy{$blk}Buy.com</a> ".
		  "{$href}plan{$blk}DVD Planet</a> ".
		  "{$href}emp{$blk}DVD Empire</a> ".
		  "{$href}ovr{$blk}Overstock</a>".

		  "<div class='sponsor-h'>&nbsp;</div>".
		  "{$href}fand{$blk}Fandango</a> ".
		  "{$href}allp{$blk}All Posters</a>".

//		  "<div class='sponsor-h'>&nbsp;</div>".
//		  "{$href}disc{$blk}Discover Card</a> ".
//		  "{$href}dell{$blk}Dell Computers</a> ".
//		  "{$href}goda{$blk}Go Daddy</a>".
		"</div></div></div></div>";
	}
	function drawSkyScraper()
	{
		$a = CAdvert::getAd('rsky');

		echo
		"<div class='adsky-a'><div class='adsky-b'><div class='adsky-c'><div class='adsky-d'>".
		  "<div>Advertisement</div>".
		  "<div class='sponsor-h'>&nbsp;</div>".
//		  "<div>{$a['role']}</div>".
		  "<div class='adsky-e'>{$a['html']}</div>".
		"</div></div></div></div>";
	}
	function getAd($s_loc)
	{
		$n   = mt_rand(1,10000);
		$sql = "SELECT a.html, a.vendor, a.advert_id ".
				 "FROM advert_range r ".
				 "JOIN advert a ON r.vendor = a.vendor and r.advert_id = a.advert_id ".
				"WHERE r.location = '{$s_loc}' and r.range_beg <= $n and $n <= range_end ".
				"LIMIT 1;";

		if ( ($a = CSql::query_and_fetch($sql,0,__FILE__,__LINE__)) )
		{
			$a['role'] = $a['vendor'] == 'adsense' || $a['vendor'] == 'adbrite' ? 'Advertiser' : 'Sponsor';
			CSql::query_and_free("INSERT INTO impressions (display_dd, location, vendor, advert_id, counter) ".
								 "VALUES (date(now()), '{$s_loc}', '{$a['vendor']}', '{$a['advert_id']}', 1) ".
								 "ON DUPLICATE KEY UPDATE counter = counter + 1",0,__FILE__,__LINE__);
		}
		else
		{
			$a['html']   = '&nbsp;';
			$a['vendor'] = '';
			$a['role']   = '';
		}
		return $a;
	}
}

//////////////////////////////////////////////////////////////////////////

?>
