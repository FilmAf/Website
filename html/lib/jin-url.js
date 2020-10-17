/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Url =
{
	test : function(ev)
	{
		ev || (ev = window.event);
		var s = ev.currentTarget || ev.srcElement,
			e, u;

		if ( ev && s && (e = $('n'+s.id.substr(1))) )
		{
			u = Url.parse(e.value);
			if ( u === false || u.substr(0,7) != 'http://' )
				alert('The URL you provided did not start with\nhttp://');
			else
				Win.openStd(u,'url');
		}
	},

	getVal : function(key,href) // getUrlParm
	{
		var u = (href || location.href),
			i = u.indexOf('?');

		if ( i >= 0 )
		{
			u = '&' + u.substr(i+1);
			i = u.indexOf('&' + key + '=');
			if ( i >= 0 )
			{
				u = u.substr(i + key.length + 2);
				i = u.indexOf('&');
				if ( i >= 0 ) u = u.substr(0,i);
				return decodeURIComponent(u);
			}
			else
			{
				i = u.indexOf('&amp;' + key + '=');
				if ( i >= 0 )
				{
					u = u.substr(i + key.length + 6);
					i = u.indexOf('&amp;');
					if ( i >= 0 ) u = u.substr(0,i);
					return decodeURIComponent(u);
				}
			}
		}
		return '';
	},

	setVal : function(key,val,href)
	{
		var u = (href || location.href),
			i = u.indexOf('?'),
			p = i >= 0 ? '&'+u.substr(i+1).replace('/&amp;/','&') : '',
			s = val !== '' ? '&'+key+'='+val : '';

		if ( i >= 0 ) u = u.substr(0,i);
		p = p.indexOf('&'+key+'=') >= 0 ? p.replace(new RegExp('&'+key+'=[^&]*'),s) : p + s;

		return (u + (p ? '?'+p.substr(1) : '')).replace('/&/','&amp;');
	},

	getDec : function(key,href)
	{
		return Dec.parse(Url.getVal(key,href));
	},

	fromStr : function(s) // stringToUrl
	{
		return	escape(Str.firstLine(s)).
				replace(/\+/g,'%2B').		// '+' to hex
				replace(/%20+/g,'\+').		// ' ' to '+'
				replace(/%26+/g,'\&amp;').	// '&' to '&amp;'
				replace(/\&/g,'\&amp;');	// '&' to '&amp;' (a second time)
	},

	parse : function(s)
	{
		var m;

		if ( (s = Str.trim(s)) != '' && s != 'http://' )
		{
			m = new RegExp("^http:\/\/[a-zA-Z0-9_\.,@?^=%&;:/~\+#\(\)-]*$"); // Netscape 7.2 seems to get confused if this pattern shows up directly in the replace function
			// http://dvdaf.blogspot.com/
			if ( m.test(s) )
				return s == 'http://' ? '' : s;

			return false;
		}
		return '';
	},

	validate : function(s_field, s_start, o_changed, n_max_len, b_allow_empty, s_name, b_must_change)
	{
		var s, e_new, e_old, b_same;

		if ( (e_new = $(s_field)) )
		{
			// update 'different from undo' flag
			if ( o_changed && ! o_changed.b_undo && (e_old = $('z_' + s_field.substr(2))) )
				o_changed.b_undo = Str.trim(e_new.value) != Str.trim(e_old.value);

			// return if the value has not changed and the user is not forced to modify it
			b_same = (e_old = $('o_' + s_field.substr(2))) && (s = Str.trim(e_new.value)) == Str.trim(e_old.value);
			if ( b_same && ! b_must_change ) return s;

			// process emptiness
			s = Validate.checkEmptiness(e_new, b_allow_empty, s_name);
			if ( s === false        ) return false;
			if ( ! e_old && s == '' ) b_same = true;
			
			// parse value
			s = Url.parse(s);
			if ( s === false || ( s != '' && (s_start && s.substr(0,s_start.length) != s_start )))
			{
				if ( ! s_start ) s_start = 'http://';
				Validate.warn(e_new, true, true, 
							  "Your "+s_name+" seems to be invalid.\n\n"+
							  "We expected something starting with:\n"+
							  "        "+s_start+"\n\n"+
							  "Do you want to fix it?",
							  false);
				return false;
			}

			// check length
			n = s.length;
			if ( n_max_len > 0 && n > n_max_len )
			{
				Validate.warn(e_new, true, true, s_name +' must have no more than '+ n_max_len +' characters. You entered '+ n +'.', false);
				return false;
			}

			// report
			if ( o_changed && ! o_changed.b_changed )
				o_changed.b_changed = ! b_same;
			e_new.value = s;
			return s;
		}
		return '';
	}

/*
	bookmark : function(url,title) // bookmarkUrl(url,title)
	{
		if ( navigator.appName == 'Microsoft Internet Explorer' && Dec.parse(navigator.appVersion) >= 4 )
			window.external.AddFavorite(url,title);
	}
*/
};

/* --------------------------------------------------------------------- */

