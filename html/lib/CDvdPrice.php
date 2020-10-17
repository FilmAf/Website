<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CDvdList.php';
require $gs_root.'/lib/CVendor.php';

class CDvdPrice extends CDvdList
{
	function constructor() // <<--------------------------------<< 1.0
	{
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->mb_advert			= false;

		$this->ms_include_js		= "<script language='javascript' type='text/javascript' src='{$this->ms_base_subdomain}/lib/cat-cart_{$this->mn_lib_version}.js'></script>\n";
		$this->mn_max_listings		= 500;
		$this->mn_show_mode			= DVDAF3_PRES_PRICE_MULTI;
		$this->ms_show_mode_url		= '';
		$this->mb_cart_handlers		= true;
		$this->mb_context_menu		= true;

		$this->ma_data				= array();
		$this->mn_row_total			= 0;
		$this->mn_total_titles		= 0;
		$this->mn_total_disks		= 0;
		$this->mn_vendors_shown		= 0;
		$this->mn_showbits			= 0;

		$this->mo_price				= new CVendor();
		//$this->mo_price->ma_price = initialized in CVendor
		//$this->mo_price->mn_price = initialized in CVendor
	}
	function getFooterJavaScript()
	{
		$s_user   = $this->mb_logged_in  ?  $this->ms_user_id : '';
		$s_config = '{baseDomain:"'.		$this->ms_base_subdomain.'"'.
					($this->mn_echo_zoom ? ',preloadImgPop:1' : '').
					',userCollection:"'.	$s_user.'"'.
					',viewCollection:""'.
					',onPopup:DvdPrice.onPopup'.
					',cartHandlers:1'.
					',objId:'.				$this->mn_dvd_id.
					',ulDvd:1'.
					",ulDvdMany:1".
					',ulExplain:1'.
					',imgPreLoad:"pin.cart.price.help.explain"'.
					'}';
		return
					"function onMenuClick(action){DvdListMenuAction.onClick(action);};".
					"Filmaf.config({$s_config});".
					"DvdPrice.getState();".
					"DvdPrice.updateCartSelect();".
					"DvdPrice.populateVendors();".
					"DvdPrice.calcPricesMany();".
					"setTimeout('".
						"Menus.setup();".
					"',100);";
	}
	function validRequest() // <<-------------------------------<< 4.0
	{
		$this->ms_list_kind = CWnd_LIST_DVDS;
		return true;
	}
	function validateDataSubmission() // <<---------------------<< 6.0
	{
		parent::validateDataSubmission();
		$this->fetchTitles();
	}
	function getHeaderJavaScript()
	{
		$s_prices = '';
		$s_diff   = '';
		$s_best   = '';
		$s_vd_sel = '';
		$s_dvd_id = '';
		$a_price  = &$this->mo_price->ma_price;
		$n_price  = $this->mo_price->mn_price;

		for ( $k = 0 ; $k < count($this->ma_data) ; $k++ )
		{
			$n_best = -1;
			$e_best = 100000.0;
			$rr     = &$this->ma_data[$k];
			if ( $rr )
			{
				for ( $i = 0 ; $i < $n_price ; $i++ )
				{
					if ( $a_price[$i]['in'] )
					{
						$e_price = $rr[$a_price[$i]['colu']];
						if ( $e_price > 0 && $e_price < $e_best + 0.02 )
						{
							$n_best = $i;
							$e_best = $e_price;
						}
					}
				}
				$this->mn_row_total++;
				$this->mn_total_titles += intval($rr['num_titles']);
				$this->mn_total_disks  += intval($rr['num_disks']);
				$rr['vd-best-price']	= $n_best;

				$i = $rr['vd-sel-vendor'];
				if ( $i < 0 || $i >= $n_price || ! $a_price[$i]['in'] || $rr[$a_price[$i]['colu']] <= 0 )
					$rr['vd-sel-vendor'] = $n_best;

				$s_prices .= '[';
				$s_diff   .= '[';
				for ( $i = 0 ; $i < $n_price ; $i++ )
				{
					$e_price = $a_price[$i]['in'] ? $rr[$a_price[$i]['colu']] : 0;
					if ( $e_price > 0 )
					{
						$s_prices .= sprintf("%0.2f,",$e_price);
						$e_price   = $e_best - $e_price;
						$s_diff   .= (abs($e_price) >= 0.005 && $e_best < 100000.0) ? sprintf("%0.2f,",$e_price) : '0,';
					}
					else
					{
						$s_prices .= '0,';
						$s_diff   .= '0,';
					}
				}
				$s_prices  = substr($s_prices,0,-1). '],';
				$s_diff    = substr($s_diff  ,0,-1). '],';
				$s_best   .= "$n_best,";
				$s_vd_sel .= "{$rr['vd-sel-vendor']},";
				$s_dvd_id .= "{$rr['dvd_id']},";
			}
		}

		$s_shown = '';
		for ( $i = 0 ; $i < $n_price ; $i++ )
			$s_shown .= $a_price[$i]['in'] ? '1,' : '0,';

		return	parent::getHeaderJavaScript().
				'var compare={};'.
				'compare.prices=['		.substr($s_prices ,0,-1).'];'.
				'compare.diff=['		.substr($s_diff   ,0,-1).'];'.
				'compare.lowest=['		.substr($s_best   ,0,-1).'];'.
				'compare.vendorShown=['	.substr($s_shown  ,0,-1).'];'.
				'compare.vendorSel=['	.substr($s_vd_sel ,0,-1).'];'.
				'compare.dvdid=['		.substr($s_dvd_id ,0,-1).'];'.
				"compare.totRows={$this->mn_row_total};";
	}

	function drawTableMenu($n_cols)
	{
		parent::drawTableMenu($n_cols);
	}

	function drawBodyPage()
	{
		$a_price = &$this->mo_price->ma_price;
		$n_price = $this->mo_price->mn_price;
		$b_empty = $this->mn_row_total == 0;
		$s_msg   = '';

		$this->mn_vendors_shown = 0;
		for ( $i = 0 ; $i < $n_price ; $i++ )
			if ( $a_price[$i]['in'] )
				$this->mn_vendors_shown++;

		if ( $b_empty )
		{
			$s_msg =  "<p>Hi, there are no titles in your cart. Please use the search bar above to find the titles you are interested in. ".

					  "<p style='margin-left:12px'><img src='http://dv1.us/d1/00/bc11.png' style='float:left;margin-right:10px' /> This adds ".
					  "the title to your cart. Then compare prices for all titles in the cart together.</p>".

					  "<p style='margin-left:12px;clear:both'><img src='http://dv1.us/d1/00/bd10.png' style='float:left;margin-right:10px' /> ".
					  "This gets you quick pricing one title at a time, or</p>".

					  "<p style='clear:both'>We get a small commission when you buy things (Clothing, cookware, electronics, toys, tools and ".
					  "DVDs -- anything really, it all helps) through our links.</p>".

					  "<p>The FilmAf Team thanks you for your support!</p>";
		}

		echo  "<form id='f_act' name='f_act' method='post' action=''>".
				"<input type='hidden' name='act' value='' />".
				"<input type='hidden' name='sub' value='' />".
				"<input type='hidden' name='tar' value='' />".
			  "</form>".
			  "<form id='f_list' name='f_list' action='javascript:void(0)'>";

		$this->drawMessagesTot(1, $this->mn_row_total, $this->mn_row_total, $this->mn_total_titles, $this->mn_total_disks, false, true, $s_msg, '', $b_empty);

		if ( $this->mn_row_total )
		{
			echo	"<table class='border'>".
					  "<thead>";
			$this->drawTableMenu($this->mn_vendors_shown + 7);
			echo		"<tr>".
						  "<td width='1%'><input type='checkbox' id='cb_all' onclick='DvdList.checkAll(this.form);' title='Select or unselect all' /></td>".
						  "<td width='1%'>Picture</td>".
						  "<td width='25%'>".
							"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
							"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
							"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />".
							"Title<br />".
							"&nbsp;".
						  "</td>".
						  "<td colspan='2' width='2%'>Selected&nbsp;Value</td>".
						  "<td colspan='2' width='2%'>".
							"Lowest Price<br />".
							"<input type='checkbox' id='cx_lowest' onclick='DvdPrice.selLowestPrice()' />".
						  "</td>";
			for ( $i = 0, $str = '' ; $i < $n_price ; $i++ )
			{
				if ( $a_price[$i]['in'] )
				{
					$str .=
						  "<td width='1%'>".
							"<a href='{$a_price[$i]['link']}' target='_blank'>".
							  "<img src='{$a_price[$i]['spic']}' height='22' width='80' alt='{$a_price[$i]['disp']}' />".
							"</a><br />".
							$a_price[$i]['pixe'].
							"<input type='checkbox' id='cx_vendor{$i}' onclick='DvdPrice.selVendor($i)' />".
						  "</td>";
				}
			}
			echo $str . "</tr>".
					  "</thead>".
					  "<tbody>";

			for ( $k = 0, $n_line_number = 1 ; $k < count($this->ma_data) ; $k++ )
			{
				if ( ($rr = &$this->ma_data[$k]) )
				{
					$this->mo_price->addVendorsMany($rr);
//					echo dvdaf3_getbrowserow($rr, DVDAF3_PRES_PRICE_MULTI, ($rr['vd-best-price'] < 0 ? DVDAF3_FLG_EBAYLINK : 0), 0, 0, 0, 0, $n_line_number, 1, $this->ms_view_id);
					echo dvdaf3_getbrowserow($rr, DVDAF3_PRES_PRICE_MULTI, 0, 0, 0, 0, 0, $n_line_number, 1, $this->ms_view_id);
					$n_line_number++;
				}
			}
			echo	  "</tbody>".
					  "<tfoot>".
						$this->drawTableFooter($n_line_number).
					  "</tfoot>".
					"</table>";
		}

		echo  $this->drawSettings().
			"</form>";
	}

	function drawTableFooter($n_line_number)
	{
		if ( $n_line_number > 1 )
		{
			$s_sub = "<tr><td colspan='3'>Sub Total</td><td colspan='2' id='td_subsel'>-</td><td colspan='2' id='td_sublow'>-</td>";
			$s_shi = "<tr><td colspan='3'>Shipping</td><td colspan='2' id='td_shpsel'>-</td><td colspan='2' id='td_shplow'>-</td>";
			$s_tax = "<tr><td colspan='3'>Sales Tax (<span id='sp_state'>none</span>)</td><td colspan='2' id='td_taxsel'>-</td><td colspan='2' id='td_taxlow'>-</td>";
			$s_tot = "<tr><td colspan='3'>Total</td><td colspan='2' id='td_totsel'>-</td><td colspan='2' id='td_totlow'>-</td>";
			for ( $i = 0 ; $i < $this->mo_price->mn_price ; $i++ )
			{
				if ( $this->mo_price->ma_price[$i]['in'] )
				{
					$s_sub .= "<td id='td_sub{$i}'>-</td>";
					$s_shi .= "<td id='td_shp{$i}'>-</td>";
					$s_tax .= "<td id='td_tax{$i}'>-</td>";
					$s_tot .= "<td id='td_tot{$i}'>-</td>";
				}
			}
			return "$s_sub</tr>$s_shi</tr>$s_tax</tr>$s_tot</tr>";
		}
		return '';
	}

	function drawSettings()
	{
		return	"<table class='prm_parm'>".
				  "<tr>".
					"<td valign='top'>".
					  "<table width='100%'>".
						"<tr>".
						  "<td>".
							"<h6>Select a state for sales tax calculation:".
							  "<p>".$this->getStateChoice()."</p>".
							"</h6>".
						  "</td>".
						  "<td style='text-align:center' width='2%'>".
							"<h6>".
							  "Free&nbsp;Shipping<br />".
							  "<input type='checkbox' id='cx_shipping' onclick='DvdPrice.setFreeShipping(this)' style='position:relative;bottom:-2px' />".
							"</h6>".
						  "</td>".
						"</tr>".
					  "</table>".
					  "<h6>Save this cart as:".
						"<div class='lowkey'>".
						  "<p style='margin-top:0'>Use A-Z, a-z, 0-9, spaces and dashes (limited by cookie size)</p>".
						  "<p style='margin:0'><input type='text' maxlength='32' title='Cart name' style='width:220px' id='cartname' /></p>".
						  "<p style='margin:0'><input type='button' value='Save cart' onclick='DvdPrice.saveCart()' /></p>".
						"</div>".
					  "</h6>".
					  "<h6>Saved carts:".
						"<p style='margin-bottom:0'><select id='sel_cart' style='width:220px'><option value='-'>...you have no saved carts...</option></select></p>".
						"<p style='margin:0'>".
						  "<input type='button' value='Load cart' onclick='DvdPrice.loadCart()' />&nbsp;".
						  "<input type='button' value='Delete cart' onclick='DvdPrice.deleteCart()' />".
						"</p>".
					  "</h6>".
					"</td>".

					"<td>&nbsp;&nbsp;&nbsp;</td>".

					"<td>".
					  "<h6>Vendors to include in the price comparison:".
						"<p>".$this->getVendorExcl()."</p>".
						"<input type='button' value='Save vendor selection' onclick='DvdPrice.excludeVendor()' />".
					  "</h6>".
					"</td>".

				  "</tr>".
				"</table>";
	}

	function fetchTitles()
	{
		$a_all = array();
		$a_vnd = array();
		$a_dvd = explode(',', preg_replace('/[^0-9,]/' , '', dvdaf3_getvalue('dvd' , DVDAF3_GET   )));
		$x_dvd = explode(',', preg_replace('/[^0-9,-]/', '', dvdaf3_getvalue('cart', DVDAF3_COOKIE)));

		// adding new dvds from URL
		for ( $k = 0, $i = 0 ; $i < count($a_dvd) ; $i++ )
		{
			$n_dvd = intval($a_dvd[$i]);
			if ( $n_dvd > 0 )
			{
				$n_vendor = -1;
				for ( $i = 0 ; $i < count($x_dvd) ; $i++ )
				{
					if ( intval($x_dvd[$i]) == $n_dvd )
					{
						$n_vendor = explode('-', $x_dvd[$i]);
						$n_vendor = count($n_vendor) > 1 && $n_vendor[1] !== '' ? intval($n_vendor[1]) : -1;
						$x_dvd[$i] = false;
					}
				}
				$b_diff = true;
				for ( $j = 0 ; $j < $k ; $j++ ) $b_diff = $a_all[$j] != $n_dvd;
				if ( $b_diff && $k < $this->mn_max_listings )
				{
					$a_all[$k] = $n_dvd;
					$a_vnd[$k] = $n_vendor;
					$k++;
				}
			}
		}

		// adding dvds from previous cart
		for ( $i = 0 ; $i < count($x_dvd) ; $i++ )
		{
			if ( $x_dvd[$i] !== false )
			{
				$n_dvd    = explode('-', $x_dvd[$i]);
				$n_vendor = count($n_dvd) > 1 && $n_dvd[1] !== '' ? intval($n_dvd[1]) : -1;
				$n_dvd    = intval($n_dvd[0]);
				if ( $n_dvd > 0 )
				{
					$b_diff = true;
					for ( $j = 0 ; $j < $k ; $j++ ) $b_diff = $a_all[$j] != $n_dvd;
					if ( $b_diff && $k < $this->mn_max_listings )
					{
						$a_all[$k] = $n_dvd;
						$a_vnd[$k] = $n_vendor;
						$k++;
					}
				}
			}
		}
		if ( count($a_all) <= 0 ) return;

		// compose select statement
		$s_select = "a.dvd_id, a.director, a.region_mask, a.genre, a.media_type, a.source, a.rel_status, a.film_rel_year, ".
				 	"a.dvd_rel_dd, a.dvd_oop_dd, a.num_titles, a.num_disks, a.best_price, if(position(' ' in a.upc)>0, ".
					"left(a.upc,position(' ' in a.upc)-1), a.upc) upc, a.pic_status, a.pic_name, a.dvd_title, a.pic_count, ".
					"'-' pic_overwrite, p.*, ";
		$s_from   = "dvd a LEFT JOIN price p ON if(position(' ' in a.upc)>0, left(a.upc,position(' ' in a.upc)-1), a.upc) = p.upc";
		if ( $this->mb_logged_in )
		{
			$s_select .= "b.genre_overwrite, b.folder";
			$s_from   .= " LEFT JOIN v_my_dvd_ref b ON a.dvd_id = b.dvd_id and '{$this->ms_user_id}' = b.user_id ";
		}
		else
		{
			$s_select .= "0 genre_overwrite, '' folder";
		}

		// fetch the data for each dvd
		for ( $k = 0 ; $k < count($a_all) ; $k++ )
		{
			if ( ($rt = CSql::query_and_fetch("SELECT {$s_select} FROM {$s_from} WHERE a.dvd_id = {$a_all[$k]}", 0,__FILE__,__LINE__)) )
			{
				$rt['vd-sel-vendor'] = $a_vnd[$k];
				$this->ma_data[$k] = $rt;
			}
		}
	}

    function getVendorExcl()
    {
		$a_price = &$this->mo_price->ma_price;
		$n_price = $this->mo_price->mn_price;
		$str	 = ''; 

		for ( $i = 0 ; $i < $n_price ; $i++ )
			$str .= "<input type='checkbox' id='cx_exclude{$i}'". ($a_price[$i]['in'] ? " checked='checked'" : ''). " />{$a_price[$i]['disp']}<br />";

		return	$str;
    }

    function getStateChoice()
    {
		return	  "<select id='sel_state' onchange='return DvdPrice.setState(this)'>".
					"<option value='0'>&lt;none&gt;</option>".
					"<option value='AL'>Alabama</option>".
					"<option value='AK'>Alaska</option>".
					"<option value='AZ'>Arizona</option>".
					"<option value='AR'>Arkansas</option>".
					"<option value='CA'>California</option>".
					"<option value='CO'>Colorado</option>".
					"<option value='CT'>Connecticut</option>".
					"<option value='DE'>Delaware</option>".
					"<option value='FL'>Florida</option>".
					"<option value='GA'>Georgia</option>".
					"<option value='HI'>Hawaii</option>".
					"<option value='ID'>Idaho</option>".
					"<option value='IL'>Illinois</option>".
					"<option value='IN'>Indiana</option>".
					"<option value='IA'>Iowa</option>".
					"<option value='KS'>Kansas</option>".
					"<option value='KY'>Kentucky</option>".
					"<option value='LA'>Louisiana</option>".
					"<option value='ME'>Maine</option>".
					"<option value='MD'>Maryland</option>".
					"<option value='MA'>Massachusetts</option>".
					"<option value='MI'>Michigan</option>".
					"<option value='MN'>Minnesota</option>".
					"<option value='MS'>Mississippi</option>".
					"<option value='MO'>Missouri</option>".
					"<option value='MT'>Montana</option>".
					"<option value='NE'>Nebraska</option>".
					"<option value='NV'>Nevada</option>".
					"<option value='NH'>New Hampshire</option>".
					"<option value='NJ'>New Jersey</option>".
					"<option value='NM'>New Mexico</option>".
					"<option value='NYC'>New York City</option>".
					"<option value='NY'>New York State</option>".
					"<option value='NC'>North Carolina</option>".
					"<option value='ND'>North Dakota</option>".
					"<option value='OH'>Ohio</option>".
					"<option value='OK'>Oklahoma</option>".
					"<option value='OR'>Oregon</option>".
					"<option value='PA'>Pennsylvania</option>".
					"<option value='RI'>Rhode Island</option>".
					"<option value='SC'>South Carolina</option>".
					"<option value='SD'>South Dakota</option>".
					"<option value='TN'>Tennessee</option>".
					"<option value='TX'>Texas</option>".
					"<option value='UT'>Utah</option>".
					"<option value='VT'>Vermont</option>".
					"<option value='VA'>Virginia</option>".
					"<option value='WA'>Washington</option>".
					"<option value='WV'>West Virginia</option>".
					"<option value='WI'>Wisconsin</option>".
					"<option value='WY'>Wyoming</option>".
					"<option value='DC'>District of Columbia</option>".
				  "</select>";
    }
}

/*
  cb_028244	dvd listing check box
  ic_028244	dvd remove from cart img buttom
  id_028244	dvd compare prices buttom
  a_028244	dvd picture link

  td_vendor2	selected vendor for row 2
  td_price2	selected vendor price for row 2
  td_2_0	cell for row 2 vendor 0
  cx_2_0	check box for row 2 vendor 0

  td_subsel	subtotal for selected value
  td_sublow	subtotal for lowest price
  td_sub0	subtotal for vendor 0
  td_shpsel	shipping for selected value
  td_shplow	shipping for lowest price
  td_shp0	shipping for vendor 0
  td_taxsel	sales tax for selected value
  td_taxlow	sales tax for lowest price
  td_tax0	sales tax for vendor 0
  td_totsel	total for selected value
  td_totlow	total for lowest price
  td_tot0	total for vendor 0

  cx_exclude0	exclusion check box for vendor 0

  cx_lowest	select lowest price for all
  cx_vendor0	select lowest price for vendor 0
  sel_state	state for sales tax calculation
  sel_cart	cart to load or deleteA
  sp_state	says which state we are calculating the sale tax for

  compare.prices	= [[35.98,33.87,0,0,0,0,34.44,0],[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0],[35.96,33.85,25.97,28.76,28.47,27.95,46.21,27.55]];
  compare.diff		= [[-2.11,0,0,0,0,0,-0.57,0],[0,0,0,0,0,0,0,0],[0,0,0,0,0,0,0,0],[-9.99,-7.88,0,-2.79,-2.50,-1.98,-20.24,-1.58]];
  compare.lowest	= [1,-1,-1,2];
  compare.vendorShown	= [1,1,1,1,1,1,1,1];
  compare.totRows	= 8;
  compare.dvdid		= ;

	$this->ma_states		= array(
					  array('name' => '&lt;none&gt;'		,'abrev' => '-' , 'tax' => 0	),
					  array('name' => 'Alabama'			,'abrev' => 'AL', 'tax' => 4	),
					  array('name' => 'Alaska'			,'abrev' => 'AK', 'tax' => 0	),
					  array('name' => 'Arizona'			,'abrev' => 'AZ', 'tax' => 5.6	),
					  array('name' => 'Arkansas'			,'abrev' => 'AR', 'tax' => 5.125),
					  array('name' => 'California'			,'abrev' => 'CA', 'tax' => 7.25 ),
					  array('name' => 'Colorado'			,'abrev' => 'CO', 'tax' => 2.9	),
					  array('name' => 'Connecticut'			,'abrev' => 'CT', 'tax' => 6	),
					  array('name' => 'Delaware'			,'abrev' => 'DE', 'tax' => 0	),
					  array('name' => 'Florida'			,'abrev' => 'FL', 'tax' => 6	),
					  array('name' => 'Georgia'			,'abrev' => 'GA', 'tax' => 4	),
					  array('name' => 'Hawaii'			,'abrev' => 'HI', 'tax' => 4	),
					  array('name' => 'Idaho'			,'abrev' => 'ID', 'tax' => 6	),
					  array('name' => 'Illinois'			,'abrev' => 'IL', 'tax' => 6.25 ),
					  array('name' => 'Indiana'			,'abrev' => 'IN', 'tax' => 6	),
					  array('name' => 'Iowa'			,'abrev' => 'IA', 'tax' => 5	),
					  array('name' => 'Kansas'			,'abrev' => 'KS', 'tax' => 5.3	),
					  array('name' => 'Kentucky'			,'abrev' => 'KY', 'tax' => 6	),
					  array('name' => 'Louisiana'			,'abrev' => 'LA', 'tax' => 4	),
					  array('name' => 'Maine'			,'abrev' => 'ME', 'tax' => 5	),
					  array('name' => 'Maryland'			,'abrev' => 'MD', 'tax' => 5	),
					  array('name' => 'Massachusetts'		,'abrev' => 'MA', 'tax' => 5	),
					  array('name' => 'Michigan'			,'abrev' => 'MI', 'tax' => 6	),
					  array('name' => 'Minnesota'			,'abrev' => 'MN', 'tax' => 6.5	),
					  array('name' => 'Mississippi'			,'abrev' => 'MS', 'tax' => 7	),
					  array('name' => 'Missouri'			,'abrev' => 'MO', 'tax' => 4.225),
					  array('name' => 'Montana'			,'abrev' => 'MT', 'tax' => 0	),
					  array('name' => 'Nebraska'			,'abrev' => 'NE', 'tax' => 5.5	),
					  array('name' => 'Nevada'			,'abrev' => 'NV', 'tax' => 6.5	),
					  array('name' => 'New Hampshire'		,'abrev' => 'NH', 'tax' => 0	),
					  array('name' => 'New Jersey'			,'abrev' => 'NJ', 'tax' => 6	),
					  array('name' => 'New Mexico'			,'abrev' => 'NM', 'tax' => 5	),
					  array('name' => 'New York'			,'abrev' => 'NY', 'tax' => 4.25 ),
					  array('name' => 'North Carolina'		,'abrev' => 'NC', 'tax' => 4.5	),
					  array('name' => 'North Dakota'		,'abrev' => 'ND', 'tax' => 5	),
					  array('name' => 'Ohio'			,'abrev' => 'OH', 'tax' => 6	),
					  array('name' => 'Oklahoma'			,'abrev' => 'OK', 'tax' => 4.5	),
					  array('name' => 'Oregon'			,'abrev' => 'OR', 'tax' => 0	),
					  array('name' => 'Pennsylvania'		,'abrev' => 'PA', 'tax' => 6	),
					  array('name' => 'Rhode Island'		,'abrev' => 'RI', 'tax' => 7	),
					  array('name' => 'South Carolina'		,'abrev' => 'SC', 'tax' => 5	),
					  array('name' => 'South Dakota'		,'abrev' => 'SD', 'tax' => 4	),
					  array('name' => 'Tennessee'			,'abrev' => 'TN', 'tax' => 7	),
					  array('name' => 'Texas'			,'abrev' => 'TX', 'tax' => 6.25 ),
					  array('name' => 'Utah'			,'abrev' => 'UT', 'tax' => 4.75 ),
					  array('name' => 'Vermont'			,'abrev' => 'VT', 'tax' => 6	),
					  array('name' => 'Virginia'			,'abrev' => 'VA', 'tax' => 4.5	),
					  array('name' => 'Washington'			,'abrev' => 'WA', 'tax' => 6.5	),
					  array('name' => 'West Virginia'		,'abrev' => 'WV', 'tax' => 6	),
					  array('name' => 'Wisconsin'			,'abrev' => 'WI', 'tax' => 5	),
					  array('name' => 'Wyoming'			,'abrev' => 'WY', 'tax' => 4	),
					  array('name' => 'District of Columbia'	,'abrev' => 'DC', 'tax' => 5.75 ));
*/

?>
