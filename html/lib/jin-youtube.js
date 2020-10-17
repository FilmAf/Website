/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var YouTube =
{
	parse : function(s)
	{
		var m;

		if ( (s = Str.trim(s)) != '' )
		{
			// http://www.youtube.com/watch?v=svR3iXKTJvc&eurl=http://ash.filmaf.com/
			if ( (m = Url.getVal('v',s)) && /^[0-9a-zA-Z_-]{9,12}$/.test(m) ) return m;
			// svR3iXKTJvc
			if ( /^[0-9a-zA-Z_-]{9,12}$/.test(s) ) return s;

			return false;
		}
		return '';
	},

	validate : function(s_field, o_changed, b_allow_empty, s_name, b_must_change)
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
			s = YouTube.parse(s);
			if ( s === false )
			{
				Validate.warn(e_new, true, true, 
							  "Your "+s_name+" seems to be invalid.\n\n"+
							  "We expected:\n"+
							  "        A string with some 10 letters\n\n"+
							  "or a URL like one of these:\n"+
							  "        http://www.youtube.com/... &v=77777777",
							  false);
				return false;
			}

			// report
			if ( o_changed && ! o_changed.b_changed )
				o_changed.b_changed = ! b_same;
			e_new.value = s;
			return s;
		}
		return '';
	},

	embed : function(id, big, auto)
	{
		if ( id && id.length > 1 )
		{
			var parm = (auto == 'Y' ? '&autoplay=1' : ''),
				size = big ? "width='640' height='395'" : "width='380' height='251'";

			return Agent.is_ie
				? "<embed src='http://www.youtube.com/v/"+id+"?hl=en&fs=1&fmt=18"+parm+"' type='application/x-shockwave-flash' wmode='transparent' allowfullscreen='true' "+size+" />"
				: "<iframe src='http://www.youtube.com/embed/"+id+"?hd=1&wmode=opaque&rel=0"+parm+"' "+size+" frameborder='0' allowfullscreen></iframe>";
		}
		return '';
	},

	openPop : function(s)
	{
		Win.openPop(true,'tub','http://www.youtube.com/watch?v='+s,0,0,0,0);
	}
};

/* --------------------------------------------------------------------- */

