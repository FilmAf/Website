/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Decode =
{
	value : function(k,s,id) // decode
	{
		var a = {s:''}, t, i, j;
		
		if ( ! s ) return '';

		switch ( k )
		{
		case 'country':
			if ( s.length == 2 )
			{
				ulCountry(a);
				if ( (s = Decode._fromUl(a, 'hm_cno_'+s)) ) return s;
			}
			break;
		case 'genre':
			if ( s.length == 5 )
			{
				if ( s == '99999' ) return id == 'b_b_genre' ? 'Use Default Genre' : 'Unspecified Genre';
				ulGenre(a);
				x = s.substr(2) == '999' ? '' : Decode._fromUl(a, 'hm_gno_' + s.substr(0,2) + '999');	// get level 1 category if this is level 2
				s = (x ? x + ' / ' : '') + Decode._fromUl(a, 'hm_gno_'+s);								// append final category
				if ( s ) return s;
			}
			break;
		case 'film_rating':
			if ( s == '0' ) return 'Unknown';
			if ( s == '1' ) return 'Not Rated';
			if ( s.length == 5 )
			{
				ulFilmRating(a);
				if ( (s = Decode._fromUl2(a, 'hm_fro_'+s)) ) return s;
			}
			break;
		case 'orig_language':
		case 'language_add':
			if ( s.length == 2 )
			{
				ulLang(a);
				if ( (t = Decode._fromUl(a, 'hm_lno_'+s      )) ) return t;
				if ( (t = Decode._fromUl(a, 'hm_lno_'+s+'_ch')) ) return 'Chinese-' + t;
				if ( (t = Decode._fromUl(a, 'hm_lno_'+s+'_in')) ) return 'Indian-' + t;
			}
			break;
		case 'country_birth':
			if ( s == 30000 ) return 'Unknown';
			if ( s == 29999 ) return 'Other';
			for ( i = 1 ; i <= 7 ; i++ )
			{
				t = '-:' + Birth.getCountries(i) + ':';
				if ( (j = t.indexOf(':'+s+':')) > 0 )
					if ( (j = t.indexOf(':',j+1)) > 0 )
						if ( (k = t.indexOf(':',j+1)) > 0 )
							return t.substr(j+1,k-j-1);
			}
			break;
		}

		alert('Missing decode "'+s+'" for "'+k+'".');
		return '';
	},

	field : function(id,s)
	{
		return Decode.value(Decode._getFieldType(id), s, id);
	},

	_getFieldType : function(id) // getFieldType
	{
		var r = /^([a-z_]+)(_[0-9]+)*$/;

		r = r.exec(id.substr(4));
		if ( r && r[1] ) return r[1];

		return '';
	},

	_fromUl : function(a,s)
	{
		// Matches for: "<li id='hm_cno_it'><a><span style='color:blue'>Italy</span></a></li>"
		//   0: "<li id='hm_cno_it'><a><span style='color:blue'>Italy</"
		//   1: "<span style='color:blue'>"
		//   2: "Italy"
		// Matches for: "<li id='hm_cno_nl'>Netherlands</li>"
		//   0: "hm_cno_nl'>Netherlands</li>"
		//   1: undefined
		//   2: "Netherlands"

		s = new RegExp(s+"'>(<.*?>)*(.+?)</");
		s = s.exec(a.s);
		return (s && s[2]) ? s[2] : '';
	},

	_fromUl2 : function(a,s)
	{
		// Matches for: "<li id='hm_cno_it'><a><span style='color:blue'>Italy</span></a></li>"
		//   0: "<li id='hm_cno_it'><a><span style='color:blue'>Italy</"
		//   1: "<span style='color:blue'>"
		//   2: "Italy"
		// Matches for: "<li id='hm_cno_nl'>Netherlands</li>"
		//   0: "hm_cno_nl'>Netherlands</li>"
		//   1: undefined
		//   2: "Netherlands"

		s = new RegExp(s+"'>(<.*?>)*(.+?)</");
		s = s.exec(a.s);
		if ( s && s[2] )
		{
			var f, b, g = s[2];

			if ( (f = a.s.lastIndexOf('<ul>',s.index)) )
				if ( (b = a.s.lastIndexOf('<li>',f)) )
					if ( (f = Dom.stripTags(a.s.substr(b + 4, f - b - 4))) )
						return f + ' / ' + g;
			return g;
		}
		return '';
	}
};

/* --------------------------------------------------------------------- */

