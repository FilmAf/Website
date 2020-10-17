/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var MySpace =
{
	parse : function(s)
	{
		var m;

		if ( (s = Str.trim(s)) != '' )
		{
			// http://profile.myspace.com/index.cfm?fuseaction=user.viewprofile&friendid=378257897
			if ( (m = Url.getVal('friendid',s)) && /^[0-9]*$/.test(m) ) return m;
			// 378257897
			if ( /^[0-9]*$/.test(s) ) return s;

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
			s = MySpace.parse(s);
			if ( s === false )
			{
				Validate.warn(e_new, true, true, 
							  "Your "+s_field+" seems to be invalid.\n\n"+
							  "We expected:\n"+
							  "        A string with some 10 digits\n\n"+
							  "or a URL like this one:\n"+
							  "        http://profile.myspace.com/... &friendid=7777777",
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
	}

};

/* --------------------------------------------------------------------- */

