/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Splash =
{
	show : function(a,c,href) // splash
	{
		var e, s='splash_div', z;

		if      (document.getElementById) e = $(s).style;
		else if (document.all           ) e = document.all[s].style;
		else if (document.layers        ) e = document.layers[s];
		else return;

		switch ( a )
		{
		case 1:					// show
			z = Dec.parse(Cookie.get('splash'));
			if ( c > z )
			{
				if ( document.getElementById )
				e.left = Math.max(30,Math.floor((document.body.clientWidth - Dec.parse(e.width))/2)) + 'px';
				Splash._fadeIn(s,0,10,50);
			}
			break;

		case 0:					// hide
		case 2: href = false;	// hide and do not show again
		case 3:					// hide and action
			Splash._fadeOut(s,100,20,50);
			Cookie.set('splash', c);	// make always 'do not show again'
			if ( href ) location.href = href;
			break;
		}
	},

	_fadeIn : function(s,i,n,t) // fadeIn
	{
		var e = $(s);
		if ( e )
		{
			i += n; if ( i > 100 ) i = 100;
			if ( document.all && document.all[s].filters )
				document.all[s].filters['alpha'].opacity = i;
			else
				if ( e.style.MozOpacity )
					e.style.MozOpacity = i / 100;
				else
					return e.style.visibility = 'visible';

			if ( e.style.visibility != 'visible' )
				e.style.visibility = 'visible';

			if ( i < 100 )
				setTimeout('Splash._fadeIn("'+s+'",'+i+','+n+','+t+')',t);
		}
	},

	_fadeOut : function(s,i,n,t) // fadeOut
	{
		var e = $(s);
		if ( e )
		{
			i -= n; if ( i < 0 ) i = 0;
			if ( document.all && document.all[s].filters )
				document.all[s].filters['alpha'].opacity = i;
			else
				if ( e.style.MozOpacity )
					e.style.MozOpacity = i / 100;
				else
					return (e.style.visibility = 'hidden');

			if ( i > 0 )
				setTimeout('Splash._fadeOut("'+s+'",'+i+','+n+','+t+')',t);
			else
				e.style.visibility = 'hidden';
		}
	}
};

/* --------------------------------------------------------------------- */

