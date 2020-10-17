/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Wiki =
{
	test : function(ev)
	{
		ev || (ev = window.event);
		var s = ev.currentTarget || ev.srcElement,
			e;

		if ( ev && s && (e = $('n'+s.id.substr(1))) )
		{
			e.value = s = Str.firstLine(e.value);
			if ( s.substr(0,29) == 'http://en.wikipedia.org/wiki/' )
				Win.openStd(s, 'wiki');
			else
				alert('The Wikipedia link you provided did not start with\nhttp://en.wikipedia.org/wiki/');
		}
	},

	search : function(f,n)
	{
		var s = Str.firstLine($(f).value);
		if ( s && (s = Url.fromStr(s)) )
			Win.openStd('http://www.google.com/search?q=site:en.wikipedia.org+'+s, 'wiki');
		else
			alert('No text found in '+n+' to search for on Wikipedia.');
	}
};

/* --------------------------------------------------------------------- */

