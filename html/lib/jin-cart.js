/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Price =
{
	attach : function() // addPriceHandlers
	{
		var e = window.self.document.getElementsByTagName('a');

		for( var i = e.length  ; --i>=0  ;  )
			if ( e[i].id && e[i].id.substr(0,3) == 'pc_' )
				e[i].onclick = function(){Price.click(this.id.substr(3),0);};
	},

	click : function(dvd_id, b_set) // onClickPrice, priceClick
	{
		Win.openPop(false, 'price', Filmaf.baseDomain + '/price-one.html?dvd=' + dvd_id, 660, 660, 1, 1);
		return true;
	}
};

var Cart =
{
	has : function (n)
	{
		var c = ',' + Cookie.get('cart') + ',';
		return c.indexOf(',' + Dec.parse(n) + '-') >= 0;
	},

	highlight : function(b_all) // highlightCart
	{
		var c = ',' + Cookie.get('cart') + ',',
			a = document.getElementsByTagName('img'),
			i, e, s;

		if ( b_all )
			for ( i = 0 ; i < a.length ; i++ )
			{
				e = a[i];
				if ( e.id.substr(0,3) == 'ic_' )
				{
					s = ',' + Dec.parse(e.id.substr(3).replace(/^0+/g,'')) + '-';
					Img.check(e,1, (c && c.indexOf(s) >= 0) ? 1 : 0);
				}
			}
		else
			if ( c )
				for ( i = 0 ; i < a.length ; i++ )
				{
					e = a[i];
					if ( e.id.substr(0,3) == 'ic_' )
					{
						s = ',' + Dec.parse(e.id.substr(3).replace(/^0+/g,'')) + '-';
						if ( c.indexOf(s) >= 0 ) Img.check(e,1,1);
					}
				}
		Cart._count(c);
	},

	count : function()
	{
		Cart._count(',' + Cookie.get('cart') + ',');
	},

	click : function(dvd_id, b_set, n_vendor) // onClickCart, cartClick
	{
		var c = Cookie.get('cart'),
			z = Cookie.get('saved'),
			b_vendor = typeof(n_vendor) != 'undefined',
			b, d, x;

		dvd_id = Dec.parse(dvd_id.replace(/^0+/g,''));
		c      = c ? ',' + c + ',' : '';

		if ( dvd_id > 0 )
		{
			x = ',' + dvd_id + '-';
			b = c.indexOf(x);
			if ( b_vendor && n_vendor >= 0 ) x += n_vendor; // n_vendor == -1 => default
			d = c.indexOf(x+',');

			if ( b_vendor && b < 0 )
			{
				// trying to save the vendor for someone who is not in the cart
				return true;
			}

			if ( (b >= 0 && ! b_set) || (b < 0 && b_set) )
			{
				if  ( b_set )
					c = x + (c ? c : ',');
				else
					c = c.replace(new RegExp(','+dvd_id+'-[a-z0-9]*,','g'), ',');
			}
			else
			{
				if ( b_vendor && d < 0 )
				{
					b = c.indexOf('-',b);
					if ( b >= 0 )
					{
						d = c.indexOf(',',b);
						c = c.substr(0,b+1)+ (n_vendor >= 0 ? n_vendor : '') + (d ? c.substr(d) : '');
					}
				}
				else
				{
					Cart._count(c);
					return true;
				}
			}

			if ( ! z ) z = '';
			if ( z.lenght + c.lenght > 2300 )
				alert('Sorry, your saved carts plus your current cart would be larger\nthan 2300 bytes. We can not complete this operation.');
			else
				Cookie.set('cart',c);
			Cart._count(c);
		}
		return true;
	},

	_count : function(c) // countCart
	{
		var t = $('cart_count_0'),
			u = $('cart_count_1'),
			n = 0;

		if ( t || u )
		{
			c = c.replace(/^,+/,'').replace(/,+$/,''); if ( c ) n = c.split(',').length;
			n = n > 0 ? n + ' item' + (n > 1 ? 's' : '') + ' in your cart' : 'your cart is empty';
			if ( t ) t.innerHTML = n;
			if ( u ) u.innerHTML = n;
		}
	}
/*
	isEmpty : function() // isCartEmpty
	{
		var t = $('cart_count_0');
		if ( t ) return t.innerHTML.indexOf('empty') != -1;
		return true;
	},
*/
};

/* --------------------------------------------------------------------- */

