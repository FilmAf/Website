/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Imdb =
{
	test       : function(ev) { Imdb._test(ev,'t'); },
	testPerson : function(ev) { Imdb._test(ev,'p'); },
	_test : function(ev,w)
	{
		ev || (ev = window.event);
		var s = ev.currentTarget || ev.srcElement,
			e;

		if ( ev && s && (e = $('n'+s.id.substr(1))) )
		{
			s = e.value.replace(/[^0-9]+/g,',').	// only numbers
			replace(/,+/g,',').			// get rid of double commas
			replace(/^,/,'').			// remove left-most comma
			replace(/,$/,'');			// remove right-most comma
			s = + s.split(',',1)[0];
			if ( s )
			{
				s = '0000000' + s;
				s = s.substr(s.length - 7, 7);
				e.value = s;
				switch (w)
				{
				case 't': Win.openStd('http://www.imdb.com/title/tt' + s + '/', 'imdb'); break;
				case 'p': Win.openStd('http://www.imdb.com/name/nm' + s + '/', 'imdb'); break;
				}
			}
			else
			{
				switch (w)
				{
				case 't': w = 'film'; break;
				case 'p': w = 'person'; break;
				}
				alert('Enter an imdb id and we will test it to see what '+w+' it points to.');
			}
		}
	},

	search       : function(f,n) { Imdb._search(f,n,'t'); },
	searchPerson : function(f,n) { Imdb._search(f,n,'p'); },
	_search : function(f,n,w)
	{
		var s = Str.firstLine($(f).value);
		if ( s && (s = Url.fromStr(s)) )
		{
			switch (w)
			{
			case 't': Win.openStd('https://www.imdb.com/find?q=?'+s, 'imdb'); break;
			case 'p': Win.openStd('https://www.imdb.com/find?q=?'+s, 'imdb'); break;
			}
		}
		else
		{
			switch (w)
			{
			case 't': w = 'some text'; break;
			case 'p': w = "a person's name"; break;
			}
			alert('No text found in '+n+' to search for on imdb.');
		}
	},

	validateN : function(s_field, n_cnt, o_changed, b_allow_empty, s_name, b_must_change)
	{
		var i, b;
		for ( i = 0, b = true  ;  b !== false && i < n_cnt  ;  i++ )
			b = Imdb.validate(s_field + i, o_changed,
							  i ? 1 : b_allow_empty,
							  i ? s_name+(i+1) : Str.trim(s_name),
							  i ? 0 : b_must_change);
		return  b !== false;
	},

	validate : function(s_field, o_changed, b_allow_empty, s_name, b_must_change)
	{
		var s = Dec.validate(s_field, o_changed, 1, 9999999, b_allow_empty, s_name, b_must_change);

		if ( s && s.length != 7 )
		{
			if ( (e = $(s_field)) )
			{
				s = '0000000' + s;
				s = s.substr(s.length - 7, 7);
				e.value = s;
			}
		}

		return s;
	}
};

/* --------------------------------------------------------------------- */

