/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Radio =
{
	getVal : function(e) // getRadioValue
	{
		if ( typeof(e) == 'string' ) e = document.getElementsByName(e);

		if ( e )
			for ( var i = 0 ; i < e.length ; i++ )
				if ( e[i].checked ) return e[i].value;
		return '';
	},

	getValCmp : function(e, o_changed)
	{
		var s = Radio.getVal(e);
		if ( ! o_changed.b_changed && (e = $('o_' + e.substr(2))) )
			o_changed.b_changed = s == e.value;
		return s;
	},

	setPrefixed : function(name,s,options) // setAjaxRadio
	{
		var e;
		if ( options.indexOf(s) >= 0 )
		{
			if ( (e = $('n_'+name+'_'+s)) ) e.checked = true;
			if ( (e = $('o_'+name      )) ) e.value = s;
			return s;
		}
		return '';
	},

	validate : function(s_field, o_changed, b_must_change)
	{
		var s, e_old, b_same;

		if ( (s = Radio.getVal(s_field)) != '' )
		{
			// update 'different from undo' flag
			if ( o_changed && ! o_changed.b_undo && (e_old = $('z_' + s_field.substr(2))) )
				o_changed.b_undo = s != Str.trim(e_old.value);

			// return if the value has not changed and the user is not forced to modify it
			b_same = (e_old = $('o_' + s_field.substr(2))) && s == Str.trim(e_old.value);
			if ( b_same && ! b_must_change ) return s;

			// report
			if ( o_changed && ! o_changed.b_changed )
				o_changed.b_changed = ! b_same;
			return s;
		}
		return '';
	}
};

/* --------------------------------------------------------------------- */

