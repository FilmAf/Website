/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var PageSize =
{
	attach : function(s_click) // attachPageSize
	{
		Context.attach(s_click, false, 'menu-page-size'); // needs to be after addImgHandlers()
	},

	attachSpin : function(t) // attachPageSizeSpin
	{
		var s = Dec.parse(Cookie.get('page')),
			e, i;

		if ( t      ) t = Dec.parse(t.getAttribute('sp_max'));
		if ( t <= 0 ) t = 200;
		if ( s <= 0 ) s = 50; else if ( s > t ) s = t;

		if ( (e=$('n_pagesize')) )
		e.value = s;

		for ( i = 1 ; i <= 3 ; i++ )
		if ( (e=$('is_'+i+'_pagesize')) )
		{
			e.setAttribute('sp_max', t);
			ImgSpin.attachSpinV(e);
		}
	},

	setOnEnter : function(ev) // setPageSizeOnEnter
	{
		if ( Kbd.isEnter(ev) )
		{
			PageSize.set(-2);
			return false;
		}
		return true;
	},

	set: function(n) // setPageSize
	{
		var re = /[\&\?]pg=[0-9]*/i,
		s = location.href,
		m = Cookie.get('page'),
		o = n,
		p, a, e;

		// get current page
		if ( n < 0 && (e=$('n_pagesize')) )
			n = e.value;

		n = Dec.parse(Str.trim(n));
		a = re.exec(s);
		if ( a && a.index > 0 ) p = Dec.parse(a[0].substr(4));
		if ( p <= 0           ) p = 1;
		if ( n <= 0           ) n = 50;
		if ( m <= 0           ) m = 50;

		if ( m == n )
		{
			alert(n + ' is already your current page size.');
		}
		else
		{
			// get future page
			p = Math.floor((((p - 1) * m + 1) - 1) / n) + 1;
			q = p > 1 ? '&pg=' + p : '';

			if ( a && a.index > 0 )
			{
				if ( p != n ) s = s.replace(re, q);
			}
			else
			{
				if ( p != 1 ) s = s + q;
			}

			if ( s.indexOf('?') < 0 )
				if ( (a = s.indexOf('&')) > 0 )
					s = s.substr(0,a) + '?' + s.substr(a+1);

			Cookie.set('page',n);

			location.href = s;
		}
		return false;
	}
};

/* --------------------------------------------------------------------- */

