/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdPrice =
{
	// Make sure to change Vendor.cpp, UnitTest_SnippetPriceOne.cpp, vd.php, CVendor.php, CAdvert.php, mil-dvd-price.js if the below changes

	_calcMany			: 1,
	_totVendors			: 6,
	_vendor				: [{key :'amz',
							name:'Amazon',
							tax :{'KS':5.3, 'KY':6, 'NYC':8.75, 'NY':4, 'ND':5, 'WA':6.5},
							ship:function(n,v){return v >= 25 || ! n ? 0 : 1.99 + n * 0.99; }},

						   {key :'buy',
							name:'Buy.com',
							tax :{'CA':7.25, 'MA':5, 'TN':9.25, 'NYC':8.75, 'NY':4},
							ship:function(n,v){return v >= 25  || ! n? 0 : 1.45 + n * 0.45; }},

						   {key :'pla',
							name:'DVD Planet',
							tax :{'CA':7.25, 'IL':7.75},
							ship:function(n,v){return v >= 35  || ! n? 0 : 1.50; }},

						   {key :'ddd',
							name:'Deep Discount',
							tax :{'IL':7.75},
							ship:function(n,v){return 0; }},

						   {key :'ovr',
							name:'Overstock',
							tax :{'UT':4.65},
							ship:function(n,v){return n ? 2.95 : 0; }},

						   {key :'exp',
							name:'DVD Empire',
							tax :{'PA':6},
							ship:function(n,v){return 0;}}],

	onPopup : function(el)
	{
		if ( ! this.id ) return;

		var i = this.menu.items,
			z = this;

		z.filmaf = DvdListMenuPrep.onPopup;
		z.filmaf(el);

		switch ( this.id )
		{
		case 'context_dvd':
			i.cm_dvd_one.display(0);
			i.cm_dvd_mine.display(0);
			break;
		}
	},

	_getState : function()
	{
		var s = DropDown.getSelValue('sel_state'),
			e = $('sp_state');

		if ( e ) e.innerHTML = s && s != '0' ? s : 'none';
		return s;
	},

	calcPricesMany : function()
	{
		DvdPrice._calcMany = 1;

		var a_sel_prc = [], a_sel_cnt = [],
			a_all_prc = [], a_all_cnt = [],
			a_low_prc = [], a_low_cnt = [],
			state     = DvdPrice._getState(),
			i, k, e, v;

		// subtotals
		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
		{
			a_sel_prc[i] = 0; a_sel_cnt[i] = 0;
			a_low_prc[i] = 0; a_low_cnt[i] = 0;
			a_all_prc[i] = 0; a_all_cnt[i] = 0;
		}
		for ( k = 0 ; k < compare.totRows ; k++ )
		{
			for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
			{
				if ( (v = Dbl.parse(compare.prices[k][i])) > 0 )
				{
					if ( (e = $('cx_'+k+'_'+i)) && e.checked )
					{
						a_sel_prc[i] += v;
						a_sel_cnt[i]++;
					}
					if ( compare.lowest[k] == i )
					{
						a_low_prc[i] += v;
						a_low_cnt[i]++;
					}
					a_all_prc[i] += v;
					a_all_cnt[i]++;
				}
			}
		}

		// total by vendors
		var e_sel_prc = 0,
			e_sel_cnt = 0,
			e_low_prc = 0,
			e_low_cnt = 0;

		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
		{
			e_sel_prc += a_sel_prc[i];
			e_sel_cnt += a_sel_cnt[i];
			e_low_prc += a_low_prc[i];
			e_low_cnt += a_low_cnt[i];
			if ( (e = $('td_sub'+i)) )
				e.innerHTML = "<div>" + Dbl.toStr(a_sel_prc[i],'-') + '<br />(' + DvdPrice._strItems(a_sel_cnt[i]) + ')</div>'+
							  "<div>" + Dbl.toStr(a_all_prc[i],'-') + '<br />(' + DvdPrice._strItems(a_all_cnt[i]) + ')</div>';
		}
		if ( (e = $('td_subsel')) ) e.innerHTML = "<div>" + Dbl.toStr(e_sel_prc,'-') + '<br />(' + DvdPrice._strItems(e_sel_cnt) + ')</div>';
		if ( (e = $('td_sublow')) ) e.innerHTML = Dbl.toStr(e_low_prc,'-') + '<br />(' + DvdPrice._strItems(e_low_cnt) + ')';

		// add shipping and tax
		var a_sel_shp = DvdPrice._calcShipping(a_sel_prc, a_sel_cnt),
			a_low_shp = DvdPrice._calcShipping(a_low_prc, a_low_cnt),
			a_sel_tax = DvdPrice._calcTaxes(a_sel_prc, state),
			a_low_tax = DvdPrice._calcTaxes(a_low_prc, state),
			e_sel_shp = 0,
			e_low_shp = 0,
			e_sel_tax = 0,
			e_low_tax = 0;

			e_sel_prc = 0,
			e_low_prc = 0;

		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
		{
			e_sel_shp += a_sel_shp[i];
			e_low_shp += a_low_shp[i];
			e_sel_tax += a_sel_tax[i];
			e_low_tax += a_low_tax[i];
			e_sel_prc += a_sel_prc[i];
			e_low_prc += a_low_prc[i];
			if ( (e = $('td_shp'+i)) ) e.innerHTML = Dbl.toStr(a_sel_shp[i],'-');
			if ( (e = $('td_tax'+i)) ) e.innerHTML = Dbl.toStr(a_sel_tax[i],'-');
			if ( (e = $('td_tot'+i)) ) e.innerHTML = Dbl.toStr(a_sel_prc[i] + a_sel_shp[i] + a_sel_tax[i],'-');
		}
		if ( (e = $('td_shpsel')) ) e.innerHTML = "<div>" + Dbl.toStr(e_sel_shp,'-') + '</div>';
		if ( (e = $('td_shplow')) ) e.innerHTML = Dbl.toStr(e_low_shp,'-');
		if ( (e = $('td_taxsel')) ) e.innerHTML = "<div>" + Dbl.toStr(e_sel_tax,'-') + '</div>';
		if ( (e = $('td_taxlow')) ) e.innerHTML = Dbl.toStr(e_low_tax,'-');
		if ( (e = $('td_totsel')) ) e.innerHTML = "<div>" + Dbl.toStr(e_sel_prc + e_sel_shp + e_sel_tax,'-') + '</div>';
		if ( (e = $('td_totlow')) ) e.innerHTML = Dbl.toStr(e_low_prc + e_low_shp + e_low_tax,'-');
	},

	populateVendors : function()
	{
		var e, k;

		for ( k = 0 ; k < compare.totRows ; k++ )
		{
			if ( (e = $('td_lo'+k)) ) e.innerHTML = DvdPrice._vendor[compare.lowest[k]].name;
			if ( (e = $('td_vd'+k)) ) e.innerHTML = DvdPrice._vendor[compare.vendorSel[k]].name;
		}
	},

	calcPricesOne : function()
	{
		DvdPrice._calcMany = 0;

		var a_all_prc = [],
			a_all_cnt = [],
			a_all_shp = [],
			a_all_tax = [],
			state     = DvdPrice._getState(),
			i, e, v;

		// subtotals
		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
		{
			a_all_prc[i] = 0;
			a_all_cnt[i] = 0;
		}
		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
		{
			if ( (v = Dbl.parse(compare.prices[i])) > 0 )
			{
				a_all_prc[i] = v;
				a_all_cnt[i] = 1;
			}
			else
			{
				a_all_prc[i] = 0;
				a_all_cnt[i] = 0;
			}
			a_all_shp[i] = 0;
		}

		// shipping and taxes
		a_all_shp = DvdPrice._calcShipping(a_all_prc, a_all_cnt);
		a_all_tax = DvdPrice._calcTaxes(a_all_prc, state);

		// add up totals and find the best price
		var best = 99999;
		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
		{
			if ( a_all_prc[i] > 0 )
			{
				a_all_prc[i] += a_all_shp[i] + a_all_cnt[i];
				if ( best > a_all_prc[i] ) best = a_all_prc[i];
			}
			if ( (e = $('td_shp'+i)) ) e.innerHTML = Dbl.toStr(a_all_shp[i],'-');
			if ( (e = $('td_tax'+i)) ) e.innerHTML = Dbl.toStr(a_all_tax[i],'-');
			if ( (e = $('td_tot'+i)) ) e.innerHTML = Dbl.toStr(a_all_prc[i],'-');
		}
		i     = Dec.parse(Cookie.get('zero'));
		best += (i > 0 ? i : 0.1) / 100;

		// highlight best prices
		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
		{
			c = (a_all_prc[i] > 0 && a_all_prc[i] <= best) ? 'pr1_lowest' : '';
			if ( (e = $('td_sub'+i)) ) e.className = c;
			if ( (e = $('td_shp'+i)) ) e.className = c;
			if ( (e = $('td_tax'+i)) ) e.className = c;
			if ( (e = $('td_tot'+i)) ) e.className = c;
			if ( (e = $('td_off'+i)) ) e.className = c;
		}
	},

	_calcPrices : function()
	{
		if ( DvdPrice._calcMany )
			DvdPrice.calcPricesMany();
		else
			DvdPrice.calcPricesOne();
	},

	_calcShipping : function(a_tot, a_cnt)
	{
		var a_shp = [], i, b = Cookie.get('ship') == 'paid';
		
		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
			a_shp[i] = b ? DvdPrice._vendor[i].ship(a_cnt[i],a_tot[i]) : 0;

		return a_shp;
	},

	_calcTaxes : function(a_tot,s_state)
	{
		var a_tax = [], i;
		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
			a_tax[i] =  typeof(DvdPrice._vendor[i].tax[s_state]) == 'undefined' ? 0 : DvdPrice._vendor[i].tax[s_state] * a_tot[i] /100;
		return a_tax;
	},

	_strItems : function(n)
	{
		return (n ? (n == 1 ? '1&nbsp;item' : n + '&nbsp;items') : 'no&nbsp;items');
	},

	routeToVendor : function(n_vendor, n_dvd_id)
	{
		Win.openStd('pc.php?tg='+n_vendor+'&amp;id='+n_dvd_id+'&amp;qc=1', DvdPrice._vendor[n_vendor].key);
	},

	selLowestPrice : function()
	{
		var i, k, e;

		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
			if ( (e = $('cx_vendor'+i)) )
				e.checked = false;
				
		for ( k = 0 ; k < compare.totRows ; k++ )
		{
			i = compare.lowest[k];
			if ( i >= 0 )
			{
				e = $('cx_'+k+'_'+i);
				if ( e ) { e.checked = true; DvdPrice.selVendorForItem(e, k, i, false); }
			}
		}
		DvdPrice.calcPricesMany();
	},

	selVendor : function(n_ven)
	{
		var i, k, e;
		
		if ( (e = $('cx_lowest')) )
			e.checked = false;

		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
			if ( i != n_ven && (e = $('cx_vendor'+i)) )
				e.checked = false;

		for ( k = 0, i = n_ven ; k < compare.totRows ; k++ )
		{
			if ( compare.prices[k][i] > 0 )
			{
				e = $('cx_'+k+'_'+i);
				if ( e ) { e.checked = true; DvdPrice.selVendorForItem(e, k, i, false); }
			}
		}
		DvdPrice.calcPricesMany();
	},

	selVendorForItem : function(o_cb, n_row, n_ven, b_calc)
	{
		var i, e, f, a, b;
		if ( b_calc )
		{
			if ( (e = $('cx_lowest')) )
				e.checked = false;

			for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
				if ( (e = $('cx_vendor'+i)) )
					e.checked = false;
		}

		if ( (e = $('td_vd'+n_row)) )
			e.innerHTML = o_cb.checked ? DvdPrice._vendor[n_ven].name : '-';

		if ( (e = $('td_pc'+n_row)) )
			e.innerHTML = o_cb.checked ? compare.prices[n_row][n_ven]+"<div>"+(compare.diff[n_row][n_ven] ? compare.diff[n_row][n_ven] : '0.00')+'</div>' : '-';

		for ( i = 0 ; i < DvdPrice._totVendors ; i++ )
		{
			if ( (e = $('td_'+n_row+'_'+i)) && (f = $('cx_'+n_row+'_'+i)) )
			{
				if ( i != n_ven ) f.checked = false;
				e.className = f.checked ? (compare.lowest[n_row] == i ? 'pri_def' : 'pri_ndf') : '';
			}
		}
		Cart.click(''+compare.dvdid[n_row], true, compare.lowest[n_row] == n_ven ? -1 : n_ven);
		
		if ( b_calc ) DvdPrice.calcPricesMany();
	},

	setState : function(e)
	{
		var i = DropDown.getSelValue(e);
		if ( ! i ) i = 0;
		Cookie.set('state',i);
		DvdPrice._calcPrices();
		return true;
	},

	setFreeShipping : function(e)
	{
		Cookie.set('ship', e.checked ? '' : 'paid');
		DvdPrice._calcPrices();
	},

	setZero : function(e)
	{
		var i = DropDown.getSelValue(e);
		if ( ! i ) i = 10;
		Cookie.set('zero',i);
		DvdPrice._calcPrices();
		return true;
	},

	showCookies : function()
	{
		Win.openPop(true, 'cookies', Filmaf.baseDomain + '/utils/show-cookies.html?pop=1', 460, 310, 1, 1);
	},

	cleanCookies : function(b_all)
	{
		if ( Cookie.clean(b_all) )
		{
			Cart.highlight(true);
			DvdPrice.getState();
			DvdPrice.updateCartSelect();
			DvdPrice._resetVendorSelection();
			DvdPrice.showCookies();
		}
	},

	getState : function()
	{
		var s, e, i;

		if ( (s = Cookie.get('state')) == '' )
			s = '0';

		if ( (e = $('sel_state')) )
			if ( ! DropDown.selectFromVal(e, s) )
				DropDown.selectFromVal(e, (s = '0'));

		if ( (e = $('sp_state')) )
			e.innerHTML = s && s != '0' ? s : 'none';

		if ( (e = $('cx_shipping')) )
			e.checked = Cookie.get('ship') != 'paid';

		if ( (e = $('sel_zero')) )
			if ( ! DropDown.selectFromVal(e, Dec.parse(Cookie.get('zero'))) )
				DropDown.selectFromVal(e, '10');
	},

	saveCart : function()
	{
		var e, s, c, z, y, n;

		if ( ! (c = Cookie.get('cart')) )
		{
			alert('Sorry, your cart is empty. There is nothing to save.');
			return false;
		}

		if ( (e = $('cartname')) )
		{
			s = Str.trim(e.value.replace(/[^a-zA-Z0-9\x20-]/g,''));
			e.value = s;
			if ( ! s )
			{
				alert("Sorry, you must enter a valid cart name. You may use letters\nand numbers as well as spaces and the dash ('-'). Other\ncharacters are not supported and are automatically removed.");
				return false;
			}

			if ( (z = Cookie.get('saved')) )
				if ( (z = DvdPrice._removeCartFromString(z, s, "We already hava cart named '"+s+"'. Would you like to replace it?\n\nOK=Yes - Cancel=No")) === false )
					return false;
			z =  s + ':' + c + ( z ? '|' + z : '');
			if ( z.lenght > 1500 )
			{
				alert('Sorry, your saved carts would be larger than 1500 bytes.\nWe can not complete this operation.');
				return false;
			}
			if ( z.lenght + c.lenght > 2300 )
			{
				alert('Sorry, your saved carts plus your current cart would be larger\nthan 2300 bytes. We can not complete this operation.');
				return false;
			}
			Cookie.set('saved',z);
			DvdPrice.updateCartSelect();
			alert("Your cart has been saved as '"+s+"'");
		}
		return false;
	},

	loadCart : function()
	{
		var e, s, z, n;
		if ( (e = $('sel_cart')) )
		{
			s = DropDown.getSelValue(e);
			if ( s && (z = Cookie.get('saved')) )
			{
				z = '|' + z;
				n = z.indexOf('|'+s+':');
				if ( n >= 0 )
				{
					z = z.substr(n+1);
					n = z.indexOf(':');
					z = z.substr(n+1);
					n = z.indexOf('|');
					if ( n >= 0 ) z = z.substr(0,n);
					Cookie.set('cart',z);
					location.href = location.href;
				}
			}
		}
	},

	deleteCart : function(s)
	{
		var e, s, z, n;
		if ( (e = $('sel_cart')) )
		{
			s = DropDown.getSelValue(e);
			if ( s && (z = Cookie.get('saved')) )
			{
				if ( (z = DvdPrice._removeCartFromString(z, s, "Are you sure you want to remove the cart named '"+s+"'?\n\nOK=Yes - Cancel=No")) !== false )
				{
					Cookie.set('saved',z);
					DvdPrice.updateCartSelect();
					alert("The cart named '"+s+"' has been deleted.");
				}
			}
		}
		return false;
	},

	updateCartSelect : function()
	{
		var e, i, z, n, s;
		if ( (e = $('sel_cart')) )
		{
			DropDown.removeOptions(e);
			if ( (z = Cookie.get('saved')) )
			{
				z = z.split('|');
				for ( i = 0 ; i < z.length ; i++ )
				{
					n = z[i].indexOf(':');
					s = z[i].substr(0,n);
					DropDown.addOption(e, s, s);
				}
			}
			else
			{
				DropDown.addOption(e, '-', '...you have no saved carts...');
			}
		}
	},

	_resetVendorSelection : function()
	{
		var i, e;
		for ( var i = 0 ; i < DvdPrice._totVendors ; i++ )
			if ( (e = $('cx_exclude'+i)) )
				e.checked = true;
	},

	_removeCartFromString : function(s_saved, s_remove, s_confirm)
	{
		var y, z, n;

		if ( s_saved )
		{
			z = '|' + s_saved;
			n = z.indexOf('|' + s_remove + ':');
			if ( n >= 0 )
			{
				if ( s_confirm )
				{
					y = confirm(s_confirm);
					if ( ! y ) return false;
				}
				y = z.substr(0,n);
				z = z.substr(n+1);
				n = z.indexOf('|');
				if ( n >= 0 ) y += z.substr(n);
				return y.substr(1);
			}
		}
		return s_saved;
	},

	excludeVendor : function()
	{
		var i, k, e, s, c = Cookie.get('excl');
		c = c ? ',' + c + ',' : '';

		for ( i = 0, k = 0 ; i < DvdPrice._totVendors ; i++ )
		{
			if ( (e = $('cx_exclude'+i)) )
			{
				s = DvdPrice._vendor[i].key;
				if ( e.checked )
				{
					c = c.replace(new RegExp(','+s+',','g'), ',');
					k++;
				}
				else
				{
					if ( c.indexOf(','+s+',') < 0 ) c += s + ',';
				}
			}
		}

		if ( k < 2 )
		{
			alert('Sorry, at least 2 vendors must be selected.');
		}
		else
		{
			Cookie.set('excl',c);
			location.href = location.href;
		}

		return true;
	}
};

/* --------------------------------------------------------------------- */

