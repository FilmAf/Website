/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Str =
{
	trim : function(s)
	{
		return  s.replace(/^[\s]+/,'').			// trim left
				  replace(/[\s]+$/,'').			// trim right
				  replace(/[\t\r\x20]+/g,' ').	// multiple spaces to single spaces
				  replace(/\x20\n/g,'\n').
				  replace(/\n\x20/g,'\n');
	},

	trimR : function(s)
	{
		return  s.replace(/[\s]+$/,'').			// trim right
				  replace(/[\t\r\x20]+/g,' ').	// multiple spaces to single spaces
				  replace(/\x20\n/g,'\n').
				  replace(/\n\x20/g,'\n');
	},

	trimL : function(s)
	{
		return  s.replace(/^[\s]+/,'').			// trim left
				  replace(/[\t\r\x20]+/g,' ').	// multiple spaces to single spaces
				  replace(/\x20\n/g,'\n').
				  replace(/\n\x20/g,'\n');
	},

	firstLine : function(s)
	{
		var n;
		s = s.replace(/^[\s]+/,'');							// trim left
		if ( (n = s.indexOf('\n')) > 1 ) s = s.substr(0,n);	// take only the 1st line
		return  s.replace(/[\s]+$/,'').						// trim right
				  replace(/[\s]+/g,' ');					// multiple spaces to single spaces
	},

	ucFirst : function(s)
	{
		return s.substr(0,1).toUpperCase()+s.substr(1);
	},

	ucWords : function(s)
	{
		return (s).replace(/^(.)|\s(.)/g, function($1) { return $1.toUpperCase(); } );
	},

	ucTitle : function(s)
	{
		var x = s.split(' '),
			t = x.length,
			i, w;
		for ( i = 0, s = '' ; i < t ; i++ )
		{
			w  = x[i];
			s += ' ' + (i && ',the,a,an,aboard,about,above,across,after,against,along,amid,among,anti,around,as,at,before,behind,below,beneath,beside,besides,between,beyond,but,by,concerning,considering,despite,down,during,except,excepting,excluding,following,for,from,in,inside,into,like,minus,near,of,off,on,onto,opposite,outside,over,past,per,plus,regarding,round,save,since,than,through,to,toward,towards,under,underneath,unlike,until,up,upon,vs,versus,via,with,within,without,and,but,for,nor,or,so,yet,to,'.indexOf(','+w+',') >= 0 ? w : w.substr(0,1).toUpperCase()+w.substr(1));
		}
		return s.substr(1);
	},

	hasSameVal : function(e,f)
	{
		if ( (e = $s(e)) && (f = $s(f)) && Str.trim(e.value) != Str.trim(f.value) )
		{
			f.style.color = 'red';
			f.style.background = '#ffcccc';
			Validate.warn(e, true, true, 'The password and the password confirmation fields do not match.', false);
			return false;
		}
		return true;
	},

	validateN : function(s_field, n_cnt, o_changed, n_min_len, n_max_len, b_allow_empty, s_name, b_must_change)
	{
		var i, b;
		for ( i = 0, b = true  ;  b !== false && i < n_cnt  ;  i++ )
			b = Str.validate(s_field + i, o_changed, n_min_len, n_max_len,
							 i ? 1 : b_allow_empty,
							 i ? s_name+(i+1) : Str.trim(s_name),
							 i ? 0 : b_must_change);
		return  b !== false;
	},

	validate : function(s_field, o_changed, n_min_len, n_max_len, b_allow_empty, s_name, b_must_change)
	{
		var s, n, e_new, e_old, b_same;

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
			if ( s === false ) return false;
			if ( ! e_old && s == '' ) b_same = true;

			// check length
			n = s.length;
			if ( n_max_len > 0 && n > n_max_len )
			{
				Validate.warn(e_new, true, true, s_name +' must have no more than '+ n_max_len +' characters. You entered '+ n +'.', false);
				return false;
			}
			if ( n_min_len > 0 && n < n_min_len )
			{
				Validate.warn(e_new, true, true, s_name +' must have at least '+ n_min_len +' characters. You entered '+ n +'.', false);
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

