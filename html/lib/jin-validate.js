/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Validate =
{
	checkEmptiness : function(e_new, b_allow_empty, s_name) // f_val_empty
	{
		var s = Str.trim(e_new.value), a;

		if ( s == '' && ! b_allow_empty )
		{
			a = 'aeiou'.indexOf(s.substr(0,1).toLowerCase()) >= 0 ? 'an' : 'a';
			Validate.warn(e_new, true, true, 'Please enter '+a+' ' + s_name.toLowerCase() +'.', false);
			return false;
		}
		return s;
	},

	reset : function(s_field) // f_val_clear
	{
		var a = s_field.split(','),
			e, i;

		for ( i = 0  ;  i < a.length  ;  i++ )
		{
			if ( (e = $(a[i])) )
			{
				e.style.color = '';
				e.style.background = '';
			}
		}
	},

	makeResetStr : function(s_field,n)
	{
		var s = '', i;
		for ( i = 0 ; i < n ; i++ ) s += s_field + n + ',';
		return s;
	},

	warn : function(o_focus, b_focus, b_fatal, s_alert, b_allow_overwrite) // f_alert
	{
		var y = false; // returns true if we want ot ignore the alert

		if ( o_focus && o_focus.type == 'hidden' )
		{
			var e = $('g'+o_focus.id.substr(1));
			o_focus = (e && e.focus && e.type != 'hidden') ? e : null;
		}

		if ( b_focus && o_focus != null )
		{
			if ( b_fatal )
			{
				o_focus.style.color = 'red';
				o_focus.style.background = '#ffcccc';
			}
			else
			{
				if ( o_focus.style.color != 'red' )
				{
					o_focus.style.color = 'green';
					o_focus.style.background = '#ccffcc';
				}
			}
		}

		if ( b_allow_overwrite )
			y = confirm(s_alert + '\n\nDo you wish to ignore this error?\n\nOK=Yes - Cancel=No');
		else
			alert(s_alert);

		// This needs to go here or firefox displays an error
		if ( o_focus != null && o_focus.focus )
		{
			if ( b_focus         ) o_focus.focus();
			if ( o_focus.select  ) o_focus.select();
		}

		return y;
	},

	reload : function(s_url) // f_discard_reload
	{
		var y = confirm('Discard changes and reaload data?\n\nOK=Yes - Cancel=No');
		if ( y )
		{
			location.href = s_url;
			return true;
		}
		return false;
	},

	save : function(b_alert_no_change, n_nav, c)
	{
		var f = $('myform'), b, e;

		if ( f )
		{
			if ( n_nav && n_nav != 0 )
				f.action += (f.action.indexOf('?') >= 0 ? '&' : '?') + 'pg=' + n_nav;

			if ( c.b_changed )
			{
				if ( ! c.b_undo )
				{
					b = confirm('The fields in this submission are the same as the current information for this title.\n\nDo you wish to withdraw this submission request?\n\nOK=Yes - Cancel=No');
					if ( b && (e = $('act')) )
					{
						e.value = 'del_sub';
						f.action = location.href;
					}
				}
				f.submit();
			}
			else
			{
				location.href = f.action;
				if ( b_alert_no_change ) alert('No changes detected.  Nothing to save.');
			}
		}
	}
};

/* --------------------------------------------------------------------- */

