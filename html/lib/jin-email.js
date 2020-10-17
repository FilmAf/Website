/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Email =
{
	validate : function(s_field, o_changed, b_empty, s_name) // f_val_email
	{
		var s, n, e_new, e_old, b_same = false;

		if ( (e_new = $(s_field)) )
		{
			// update 'different from undo' flag
			if ( o_changed && ! o_changed.b_undo && (e_old = $('z_' + s_field.substr(2))) )
				o_changed.b_undo = Str.trim(e_new.value) != Str.trim(e_old.value);

			// return if the value has not changed and the user is not forced to modify it
			b_same = (e_old = $('o_' + s_field.substr(2))) && (s = Str.trim(e_new.value)) == Str.trim(e_old.value);
			if ( b_same && ! b_must_change ) return s;

			// process emptiness
			var s = Validate.checkEmptiness(e_new, b_empty, s_name);
			if ( s === false        ) return false;
			if ( ! e_old && s == '' ) b_same = true;

			// parse value
			// if ( s.match(/^[^@]+@[^@\.]+\.[A-Za-z]+$/) == null )
			// if ( s.match(/^[^@]+@[^@\.]+\.(([A-Za-z]{2,3})|([^@\.]+\.[A-Za-z]{2}\.[A-Za-z]{2}))$/) == null )
			if ( s.match(/^[^@]+@([^@\.]+\.)+[A-Za-z]{2,4}$/) == null )
			{
				Validate.warn(e_new, true, true, s_name +' does look like a valid email address.', false);
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

