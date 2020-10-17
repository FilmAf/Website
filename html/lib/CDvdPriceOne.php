<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWnd.php';
require $gs_root.'/lib/CVendor.php';

class CDvdPriceOne extends CWnd
{
    function constructor() // <<--------------------------------<< 1.0
    {
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-price_{$this->mn_lib_version}.js'></script>\n";
		$this->ms_title				= 'DVD Prices';
		$this->mn_footer_type		= CWnd_FOOTER_TIME;
		$this->mn_header_type		= CWnd_HEADER_SMALL;

		$this->ma_result_set		= null;
		$this->mn_vendors_shown		= 0;
		$this->mn_showbits			= 0;
		$this->mb_price_found		= false;

		$this->mo_price				= new CVendor();
		//$this->mo_price->ma_price	= initialized in CVendor
		//$this->mo_price->mn_price	= initialized in CVendor
    }

    function getFooterJavaScript()
    {
		return	"DvdPrice.getState();".
				"DvdPrice.calcPricesOne();";
	}

    function validateDataSubmission() // <<---------------------<< 6.0
    {
		$n_dvd_id		= dvdaf3_getvalue('dvd', DVDAF3_GET|DVDAF3_INT);
		$this->ms_title	= "DVD Prices";

		if ( $n_dvd_id > 0 )
		{
			$ss = "SELECT a.dvd_id, a.dvd_title, a.film_rel_year, a.source, a.media_type, a.region_mask, a.list_price, a.pic_status, a.pic_name, a.upc a_upc, p.* ".
					"FROM dvd a LEFT JOIN price p ON if(position(' ' in a.upc)>0, left(a.upc,position(' ' in a.upc)-1), a.upc) = p.upc ".
				   "WHERE a.dvd_id = {$n_dvd_id}";

			$this->ma_result_set = CSql::query_and_fetch($ss, 0,__FILE__,__LINE__);

			if ( $this->ma_result_set )
			{
				$str = $this->ma_result_set['dvd_title'];
				$pos = strpos($str, '<');
				if ( $pos ) $str = substr($str, 0, $pos);
				$this->ms_title = "DVD Prices - ". $str;
			}
		}
    }

	function badRequester()
	{
		if ( strpos(dvdaf3_getvalue('HTTP_USER_AGENT', DVDAF3_SERVER|DVDAF3_LOWER),'googlebot') !== false )
			return true;
		return false;
	}

    function getHeaderJavaScript()
    {
		$s_parent = parent::getHeaderJavaScript();
		if ( $this->ma_result_set )
		{
			$s_prices = '';

			for ( $i = 0 ; $i < $this->mo_price->mn_price ; $i++ )
			{
				$e_price   = $this->ma_result_set[sprintf('price_%02d',$i)];
				$s_prices .= $this->mo_price->ma_price[$i]['in'] ? sprintf("%0.2f,",$e_price) : '0,';
				if ( $e_price > 0 ) $this->mb_price_found = true;
			}

			return  $s_parent.
					"var compare={};".
					"compare.prices=[".substr($s_prices ,0,-1)."];";
		}
		return $s_parent;
    }

    function drawBodyPage() // <<-------------------------------<< 7.2
    {
		if ( $this->ma_result_set )
		{
			$this->mo_price->addVendorsOne($this->ma_result_set);

			if ( $this->mb_price_found )
			{
				echo dvdaf3_getbrowserow($this->ma_result_set, DVDAF3_PRES_PRICE_ONE, 0, 0, 0, 0, 0, 1, 1, $this->ms_view_id);
			}
			else
			{
				echo dvdaf3_getbrowserow($this->ma_result_set, DVDAF3_PRES_PRICE_ONE, DVDAF3_FLG_NOPRICE, 0, 0, 0, 0, 1, 1, $this->ms_view_id);
				$s_upc = explode(' ',$this->ma_result_set['a_upc'],1);
				$s_upc = $s_upc[0];
				if ( strlen($s_upc) > 1 )
				{
					echo  "<div id='msg-only' style='padding-top:0;padding-bottom:0'>".
							"<p>Oops. We are sorry. We are not able to locate the title you are looking for within the list of vendors participating in FilmAf&#39;s Price ".
							"Search. Common reasons for this include out of print titles and non-US editions.</p>".
							"<p><img height='66' width='120' src='http://dv1.us/d1/upc-sample.gif' style='float:right;margin-left:12px;margin-bottom:8px' alt='UPC sample' />".
							"It is also possible that we have an incorrect UPC. UPC&#39;s are usually 12 or 13-digit codes that uniquely identify a product.</p>".
							"<p>The UPC we have for the title you search is <strong>{$s_upc}</strong>. If you believe that to be incorrect please use the &quot;Correct ".
							"Listing Info&quot; option in the contextual menu for this title. The menu can be accessed by clicking on the DVD picture displayed when you ".
							"<strong>search</strong> or <strong>display a collection</strong>. All information at FilmAf is contributed by members like you.</p>".
							"<p>The FilmAf Team thanks you for your continued support and loyalty.</p>".
						  "</div>";
				}
				else
				{
					echo  "<div id='msg-only' style='padding-top:0;padding-bottom:0'>".
							"<p><img height='66' width='120' src='http://dv1.us/d1/upc-sample.gif' style='float:right;margin-left:12px;margin-bottom:8px' alt='UPC sample' />".
							"Oops. We are sorry. This title is missing its UPC. UPC&#39;s are 12 or 13-digit codes that uniquely identify a product.</p>".
							"<p>If you can provide us with the correct UPC for this title please use the &quot;Correct Listing Info&quot; option in the contextual menu for ".
							"this title. The menu can be accessed by clicking on the DVD picture displayed when you <strong>search</strong> or <strong>display a collection".
							"</strong>. All information at FilmAf is contributed by members like you.</p>".
							"<p>The FilmAf Team thanks you for your continued support and loyalty.</p>".
						  "</div>";
				}
			}
		}
		else
		{
			echo  "<div id='msg-only'>".
					"<p>Sorry, we could not find the DVD you requested.</p>".
				  "</div>";
		}
    }
}
/*
  td_sub0	price for vendor 0
  td_shp0	shipping for vendor 0
  td_tax0	sales tax for vendor 0
  td_tot0	total for vendor 0
  td_off0	offers for vendor 0

  cx_shipping	wheterh to include shipping costs in calculation
  cx_exclude0	exclusion check box for vendor 0
  sel_state	state for sales tax calculation
  sp_state	says which state we are calculating the sale tax for

  compare.prices	= [[35.98,33.87,0,0,0,0,34.44,0],[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0],[35.96,33.85,25.97,28.76,28.47,27.95,46.21,27.55]];
*/
?>
