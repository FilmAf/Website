/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Dbl =
{
	parse : function(s) // parseDbl, Parse.dbl
	{
		s = parseFloat(s);
		return isNaN(s) ? 0 : s;
	},

	parseBounded : function(s, min, max, b_allow_empty, b_money) // parseDouble
	{
		if ( typeof(b_money) == 'undefined' ) b_money = false;
		var re_1, re_2, b_not_num, b_bounds, b_neg = false;

		s = Str.trim(s);
		if ( s == '' ) return b_allow_empty ? '' : false;

		if ( s.substr(0,1) == '-' )
		{
			b_neg = true;
			s     = s.substr(1);
		}

		if ( b_money )
		{
			re_1 = /^([0-9]+)[,.]*([0-9]{0,2})$/;
			re_2 = /^[0-9]+[\.]*[0-9]{0,2}%?$/;
		}
		else
		{
			re_1 = /^([0-9]+)[,.]*([0-9]{0,5})$/;
			re_2 = /^[0-9]+[\.]*[0-9]{0,5}%?$/;
		}

	    s = s.replace(re_1             ,'$1.$2'). // non-US members may be using a comma instead of a dot
			  replace(/^([0-9]+)[,.]*$/,'$1'   ); // get rid of the '.' if that is the last character
	    b_not_num = s.match(re_2) == null;
	    b_bounds  = b_not_num ? false : (max > min ? (Dbl.parse(s) > max || Dbl.parse(s) < min) : false);
	    if ( b_not_num || b_bounds ) return false;
	    return b_neg ? '-' + s : s;
	},

    toStr : function(v,s_on_zero) // formatDec
	{
		if ( v > -0.005 && v < 0.005 ) return s_on_zero;
		v += v > 0 ? 0.005 : -0.005;
		v  = '' + v;
		var n = v.indexOf('.');
		if ( n > 0 )
			return v.substr(0,n) + '.' + (v.substr(n+1) + '00').substr(0,2);
		return v + '.00';
	},

	toUsd : function(v)
	{
		v = Dbl.toStr(v,'0.00');

		var m = v.indexOf('-') >= 0 ? 1 : 0,
			n = v.indexOf('.');
		if (  n - m > 3 )
			v = v.substr(0,n-3) + ',' + v.substr(n-3);

		return v;
	},

	validate : function(s_field, o_changed, e_min, e_max, b_allow_empty, s_name, b_must_change, b_money) // f_val_float_, f_val_float, f_val_money
	{
		if ( typeof(b_money) == 'undefined' ) b_money = false;
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
			s = Dbl.parseBounded(s, e_min, e_max, b_allow_empty, b_money);
			if ( s === false )
			{
				Validate.warn(e_new, true, true, s_name +' must be a number between '+ e_min +' and '+ e_max +' with up to '+ (b_money ? 'two' : 'five') +' decimal digits.\nYou entered "'+ e_new.value +'".', false);
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

