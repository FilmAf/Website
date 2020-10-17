/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Dec =
{
	parse : function(s) // parseDec, Parse.dec
	{
	    s = parseInt(s,10);
		return isNaN(s) ? 0 : s;
	},

	parseBounded : function(s, min, max, b_allow_empty) // parseInteger
	{
		s = Str.trim(s);
		if ( s == '' ) return b_allow_empty ? '' : false;

		var b_not_num = s.match(/^[+-]?[0-9]+$/) == null;
		var b_bounds  = b_not_num ? false : (max > min ? (Dec.parse(s) > max || Dec.parse(s) < min) : false);
		if ( b_not_num || b_bounds ) return false;
		return s;
	},

	validateN : function(s_field, n_cnt, o_changed, e_min, e_max, b_allow_empty, s_name, b_must_change)
	{
		var i, b;
		for ( i = 0, b = true  ;  b !== false && i < n_cnt  ;  i++ )
			b = Dec.validate(s_field + i, o_changed, e_min, e_max,
							 i ? 1 : b_allow_empty,
							 i ? s_name+(i+1) : Str.trim(s_name),
							 i ? 0 : b_must_change);
		return  b !== false;
	},

	validate : function(s_field, o_changed, e_min, e_max, b_allow_empty, s_name, b_must_change) // f_val_int
	{
		var s, e_new, e_old, b_same;

		if ( (e_new = $(s_field)) )
		{
			// update 'different from undo' flag
			if ( o_changed && ! o_changed.b_undo && (e_old = $('z_' + s_field.substr(2))) )
				o_changed.b_undo = Str.trim(e_new.value) != Str.trim(e_old.value);

			// return if the value has not changed and the user is not forced to modify it, but not if it is empty or zero and we must have a value
			b_same = (e_old = $('o_' + s_field.substr(2))) && (s = Str.trim(e_new.value)) == Str.trim(e_old.value);
			if ( ((s != '' && s != '0') || b_allow_empty) && b_same && ! b_must_change ) return s;

			// process emptiness
			s = Validate.checkEmptiness(e_new, b_allow_empty, s_name);
			if ( s === false        ) return false;
			if ( ! e_old && s == '' ) b_same = true;
			
			// parse value
			s = Dec.parseBounded(s, e_min, e_max, b_allow_empty);
			if ( s === false )
			{
				Validate.warn(e_new, true, true, s_name +' must be an integer'+ (e_max > e_min ? (' between '+ e_min +' and '+ e_max) : '' )+ '.\nYou entered "'+ e_new.value +'".', false);
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

