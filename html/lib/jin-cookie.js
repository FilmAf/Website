/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

/* --------------------------------------------------------------------- *\
	cookies

	// price comparison
	cart		= '3270-2,3252-'				comma-separated list of "{dvd_id}-{retailer_preference}"
	excl		= 'exp,pla,ddd,ovr'				comma-separated list of vendors to exclude from price comparison
	saved		= 'test2:327-,352-1|test:327-]'	pipe-separeated list of saved carts
	state		= '30'							state for tax calculation
	ship		= 'paid'						consider shipping when doing the price-one comparison
	zero		= '25'							cents to to be considered zero when doing the price-one comparison

	// session
	user		= 'ash'							user name
	parm		= md5(32) + '2'					(0:guest,1:remembered,2:authenticated)|tbd|tbd|tbd
	sess		= '2'							0:none,1:temporary,2:permanent
	orig		= '67.85.97.77|1283992879.5957'	unique value for browser
.	charm		= md5(32) + 'H/M' + '0.49433' + unixtime	humna/machine + exponentially smoothed moving average used to detect robots + unix time of when this was written
if invalid, non-existant, or too old -- generate a new one and log it.

	// list presentation
	pm			= 'one','prn'					presentation mode
	page		= '50'							page size for list display
	finder		= 'N'							disable auto image pop up
	longtitles	= 'Y'							show expanded titles
	move		= 'owned/hd-dvd'				default folder where to move titles to

	// my home
.	home		= '1|folder|titles|0'			Home._version+'|'+FilmTab.filmTab+'|'+FilmTab.filmCount+'|'+FriendInvite.showRejected;
.	showreplies	= 'Y'							Show microblog replies
	hometab		= 'bd|us|rnk'					Selectors for site main page tabs

	// filmaf home
	homeshow	= 'bd*dvd*crit'					define what elements to show in home page

	// search
	pinned		= 'rgn_1_us'					pinned search values
	search		= 'myregion_us*mymedia_all*pins_1*expert_1*flipexcl_1'		Search options: 'noisearch','more','incmine','myregion','mymedia','save','expert','flipexcl','pins'
	search_big	= 'Y'							show big picture instead of thumbnails in the iterative search

	// others
	lastupd		= time() - 1317000000			last time an action caused a database change
.	lastvis		= time() - 1317000000			last visit
	splash		= '30'							number of days not to display the splash_{n} splash screen

\* --------------------------------------------------------------------- */

var Cookie =
	{
		get : function(n) // getCookie(n)
		{
			var c = ('; ' + document.cookie).replace(/;\s*([A-Za-z0-9_]+\=)/g,';$1'),
				i = c.indexOf(';'+n+'=');
			if ( i >= 0 )
			{
				i += n.length + 2;
				n  = c.indexOf(';',i);
				return n > i ? c.substr(i,n-i) : c.substr(i);
			}
			return '';
		},

		del : function(n) // deleteCookie
		{
			var c = new Date('January 1, 2001');
			c = '; expires=' + c.toGMTString() + '; path=/; domain=' + Filmaf.cookieDomain;
			document.cookie = n + '=' + c;
		},

		set : function(n,v,d) // setCookie
		{
			if ( typeof(v) == 'string' && ! (v = Str.trim(v).replace(/^,+/,'').replace(/,+$/,'')) )
			{
				Cookie.del(n);
				return;
			}

			if ( d )
				if ( d < 0 )
					d = 0;
				else
				{
					var x = d;
					d = new Date();
					d.setDate(d.getDate() + x);
				}
			else
				d = new Date(new Date().setFullYear(new Date().getFullYear() + 1));
			d = ( d ? '; expires=' + d.toGMTString() : '') + '; path=/; domain=' + Filmaf.cookieDomain;

			document.cookie = n + '=' + v + d;
		},

		reset : function(keepers) // resetCookies
		{
			var z = document.cookie.split(';'),
				i, s, n;

			keepers = '-,'+keepers.toLowerCase()+',';

			for ( i = 0 ; i < z.length ; i++ )
			{
				s = Str.trim(z[i]);
				n = s.indexOf('=');
				if ( n >= 0 )
				{
					s = s.substr(0,n);
					if ( keepers.indexOf(','+s.toLowerCase()+',') < 0 ) Cookie.del(s);
				}
			}
		},

		amend : function(n,k,v) // amends a *_ cookie with key and value
		{
			var c = Cookie.get(n).split('*'),
				s = '',
				b = 0,
				m, i;

			if ( typeof(v) == 'string' )
			{
				if ( v.indexOf('*') > 0 )
				{
					v = v.replace(/\*/g,'');
					alert("Illegal character '*' removed from ammended cookie value");
				}
				if ( v.indexOf('*') > 0 || v.indexOf('_') > 0 )
				{
					k = k.replace(/\*/g,'').replace(/_/g,'');
					alert("Illegal characters '*' and/or '_' removed from ammended cookie value");
				}
			}

			for ( i = 0 ; i < c.length ; i++ )
			{
				m = c[i].split('_',1)[0];
				if ( k == m )
				{
					if ( v != '' ) s += '*'+k+'_'+v;
					b  = 1;
				}
				else
				{
					if ( m != '' ) s += '*'+c[i];
				}
			}

			if ( ! b && v != '' )
				s += '*'+k+'_'+v;

			if ( s == '' )
				Cookie.del(n);
			else
				Cookie.set(n,s.slice(1));
		},

		extract : function(n,k,def) // returns the value for key in a *_ cookie
		{
			var c = Cookie.get(n).split('*'),
				i;

			for ( i = 0 ; i < c.length ; i++ )
			{
				if ( k == c[i].split('_',1)[0] )
					return c[i].slice(k.length+1);
			}

			return typeof(def) == 'undefined' ? '' : def;
		},

		extractFromStr : function(c,k,def) // same as extract, but c is the cookie string already
		{
			var i;

			c = c.split('*');
			for ( i = 0 ; i < c.length ; i++ )
			{
				if ( k == c[i].split('_',1)[0] )
					return c[i].slice(k.length+1);
			}

			return typeof(def) == 'undefined' ? '' : def;
		},

		explode : function(n,ints) // explode a *_ cookie into an array of (key,val) pairs
		{
			var c = Cookie.get(n).split('*'),
				a = [],
				i;

			if ( c == '' ) return '';

			for ( i = 0 ; i < c.length ; i++ )
			{
				k = c[i].split('_',1)[0];
				v = c[i].slice(k.length+1);
				if ( ints )
					a[i] = {key:k, val:v};
				else
					a[k] = v;
			}
			return a;
		},

		explodeStr : function(c,ints) // explode a *_ cookie into an array of (key,val) pairs
		{
			var c = c.split('*'),
				a = [],
				i;

			if ( c == '' ) return '';

			for ( i = 0 ; i < c.length ; i++ )
			{
				k = c[i].split('_',1)[0];
				v = c[i].slice(k.length+1);
				if ( ints )
					a[i] = {key:k, val:v};
				else
					a[k] = v;
			}
			return a;
		},

		clean : function(b_all)
		{
			var b = confirm(b_all
				? 'This action will log you off FilmAf, delete your shopping\ncart and a few other options. Do you wish to proceed?\n\nOK=Yes - Cancel=No'
				: 'This action will delete your shopping cart and\na few other options. Do you wish to proceed?\n\nOK=Yes - Cancel=No');
			if ( b ) Cookie.reset(b_all ? 'orig' : 'user,parm,orig');
			return b;
		}
	};

/* --------------------------------------------------------------------- */

