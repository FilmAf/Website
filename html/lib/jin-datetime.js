/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DateTime =
{
	parseBounded : function(s, yy_min, yy_max, empty) // parseDate
	{
		var re, y, m = 1, d = 1, o = 'invalid';

		s = Str.trim(s);
		if ( s == '' ) return empty ? '' : false;

		re = /^([0-9]{4})$/.exec(s);
		if ( re )
		{
			y = Dec.parse(re[1]);
			if ( y >= yy_min && y <= yy_max ) o = 'year';
		}
		else
		{
			re = /^([0-9]{4})[-]([0-9]{2})$/.exec(s);
			if ( re )
			{
				y = Dec.parse(re[1]);
				m = Dec.parse(re[2]);
				if ( y >= yy_min && y <= yy_max )
				{
					if ( m == 0            ) o = 'year'; else
					if ( m >= 1 && m <= 12 ) o = 'month';
				}
			}
			else
			{
				re = /^([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$/.exec(s);
				if ( re )
				{
					y = Dec.parse(re[1]);
					m = Dec.parse(re[2]);
					d = Dec.parse(re[3]);
					if ( y >= yy_min && y <= yy_max )
					{
						if ( m == 0 && d == 0  ) o = 'year'; else
						if ( m >= 1 && m <= 12 )
						{
							if ( d == 0 ) o = 'month'; else
							if ( DateTime.isValid(y, m, d, yy_min, yy_max) ) o = 'day';
						}
					}
				}
				else
				{
					return false;
				}
			}
		}
		return { day: d, month: m - 1, year: y, option: o };
	},

	isLeapYear : function(y) // isLeapYear
	{
		return y % 4 == 0 && (y % 100 != 0 || y % 400 == 0);
	},

	daysInMonth : function(m,y) // daysInMonth
	{
		var dm = [0, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

		if ( m == 2 && y > 0 )
		return DateTime.isLeapYear(y) ? 29 : 28;

		return ( m >= 1 && m <= 12 ) ? dm[m] : false;
	},

	isStrValid : function(s, yy_min, yy_max)
	{
		var re = /^([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$/.exec(s);
		if ( re )
		{
			y = Dec.parse(re[1]);
			m = Dec.parse(re[2]);
			d = Dec.parse(re[3]);
			return DateTime.isValid(y, m, d, yy_min, yy_max);
		}
		return false;
	},

	isValid : function(y, m, d, yy_min, yy_max) // isDateValid
	{
		y = Dec.parse(y);
		m = Dec.parse(m);
		d = Dec.parse(d);

		return y >= yy_min && y <= yy_max && m >= 1 && m <= 12 && d >= 1 && d <= DateTime.daysInMonth(m,y);
	},

	IntToDateStr : function(y,m,d) // format_yyyy_mm_dd_(o_date) {.year, .month, .day}
	{
		m = m + 1;
		if ( m < 10 ) m = '0' + m;
		if ( d < 10 ) d = '0' + d;
		return y + '-' + m + '-' + d;
	},

	toDateStr : function(dt) // format_yyyy_mm_dd
	{
		var m = dt.getMonth() + 1,
			d = dt.getDate();
		if ( m < 10 ) m = '0' + m;
		if ( d < 10 ) d = '0' + d;

		return dt.getFullYear() + '-' + m + '-' + d;
	},

	toTimeStr : function(dt) // format_yyyy_mm_dd_hh_mm_ss
	{
		var m = dt.getMonth() + 1,
			d = dt.getDate(),
			h = dt.getHours(),
			i = dt.getMinutes(),
			s = dt.getSeconds();
		if ( m < 10 ) m = '0' + m;
		if ( d < 10 ) d = '0' + d;
		if ( h < 10 ) h = '0' + h;
		if ( i < 10 ) i = '0' + i;
		if ( s < 10 ) s = '0' + s;

		return dt.getFullYear() + '-' + m + '-' + d + ' ' + h + ':' + i + ':' + s;
	},

	toTimeHHMM : function(dt, zeroPad, ampm)
	{
		var h = dt.getHours(),
			i = dt.getMinutes(),
			b = h > 11,
			m = ampm ? (b ? ' PM' : ' AM') : '';
		
		if ( m && b            ) h -= 12;
		if ( h < 10 && zeroPad ) h = '0' + h;
		if ( i < 10            ) i = '0' + i;
		return h + ':' + i + m;
	},

	getMonth : function(n)
	{
		 var a = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		 if ( n >= 1 && n <= 12 ) return a[n-1];
	},

	validate : function(s_field, o_date, o_changed, yy_min, yy_max, b_allow_empty, s_name, b_must_change) // f_val_date
	{
		var s, e_new, e_old, b_same;

		o_date.day   = 0;
		o_date.month = 0;
		o_date.year  = 0;
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
			s = DateTime.parseBounded(s, yy_min, yy_max, b_allow_empty);
			if ( s === false || (s.option && s.option != 'day') )
			{
				Validate.warn(e_new, true, true, s_name +' must be a valid date between '+ yy_min +' and '+ yy_max +' expressed as YYYY-MM-DD.\nYou entered "'+ e_new.value +'".', false);
				return false;
			}
			o_date.day   = s.day;
			o_date.month = s.month;
			o_date.year  = s.year;

			// report
			if ( o_changed && ! o_changed.b_changed )
				o_changed.b_changed = ! b_same;
			if ( s != '' ) s = DateTime.IntToDateStr(s.year, s.month, s.day);
			e_new.value = s;
			return s;
		}
		return '';
	},

	attachLiveParser : function(s) // attachDateKeypress
	{
		if ( (s = $(s)) )
		s.onkeypress = DateTime._liveParser;
	},

	_liveParser : function(ev) // dateKeypress
	{
		function has31d(s)
		{
			s = Dec.parse(s);
			a = [0,31,29,31,30,31,30,31,31,30,31,30,31];
			return ( s >= 1 && s <= 12 ) ? a[s] == 31 : false;
		};

		ev||(ev=window.event);

		var c = ev.keyCode || ev.charCode,
			d = c ? String.fromCharCode(c) : 'not',
			e = ev.currentTarget || ev.srcElement,
			b = e.value,
			f = b.length,
			g = d == '-';

		if ( (d == '-' || (d >= '0' && d <= '9')) && e )
		{
			c = true;

			switch ( f )
			{
			case 0:
				c = /[1-2]/.test(d);
				break;

			case 1: if ( ! /[1-2]/.test(b) ) c = 0; else
				{
					c = (b == '1'  ? /[8-9]/ : /[0]/  ).test(d);
				}
				break;

			case 2: if ( ! /(19)|(20)/.test(b) ) c = 0; else
				{
					c = (b == '20' ? /[0]/   : /[0-9]/).test(d);
				}
				break;

			case 3: if ( ! /(19[0-9])|(200)/.test(b) ) c = 0; else
				{
					c = /[0-9]/.test(d);
				}
				break;

			case 4: if ( ! /(19[0-9][0-9])|(200[0-9])/.test(b) ) c = 0; else
				{
					if ( ! g )
					{
						e.value += '-';
						if ( ! /[0-1]/.test(d) ) e.value += '0';
						c = /[0-9]/.test(d);
					}
				}
				break;

			case 5: if ( ! /(19[0-9][0-9]-)|(200[0-9]-)/.test(b) ) c = 0; else
				{
					if ( ! /[0-1]/.test(d) ) e.value += '0';
					c = /[0-9]/.test(d);
				}
			break;

			case 6: if ( ! /(19[0-9][0-9]-[0-1])|(200[0-9]-[0-1])/.test(b) ) c = 0; else
				{
					if ( g && b.substr(5,1) != '0' )
						e.value = b.substr(0,5) + '0' + b.substr(5,1);
					else
						c = (b.substr(5,1) == '1' ? /[0-2]/ : /[1-9]/).test(d);
				}
				break;

			case 7: if ( ! /(19[0-9][0-9]-[0-1][0-9])|(200[0-9]-[0-1][0-9])/.test(b) ) c = 0; else
				{
					if ( ! g )
					{
						e.value += '-';
						if ( ! (b.substr(5,2) == '02' ? /[0-2]/ : /[0-3]/).test(d) )
						e.value += '0';
						c = /[0-9]/.test(d); /* Feb does not allow '3' */
					}
				}
				break;

			case 8: if ( ! /(19[0-9][0-9]-[0-1][0-9]-)|(200[0-9]-[0-1][0-9]-)/.test(b) ) c = 0; else
				{
					if ( ! (b.substr(5,2) == '02' ? /[0-2]/ : /[0-3]/).test(d) ) e.value += '0';
					c = /[0-9]/.test(d); /* Feb does not allow '3' */
				}
				break;

			case 9: if ( ! /(19[0-9][0-9]-[0-1][0-9]-[0-3])|(200[0-9]-[0-1][0-9]-[0-3])/.test(b) ) c = 0; else
				{
					// check day of month
					var y = Dec.parse(b.substr(0,4)),
						m = Dec.parse(b.substr(5,2));

					if ( m == 2 )
						c = (b.substr(8,1) == '2' ? (DateTime.isLeapYear(y) ? /[0-9]/ : /[0-8]/) : /[0-9]/).test(d);
					else
						c = (b.substr(8,1) == '3' ? (DateTime.daysInMonth(m,y) == 31 ? /[0-1]/ : /[0]/  ) : /[0-9]/).test(d);
				}
				break;

			default:
				// just ignore, no message
				return false;
				break;
			}
		}
		else
		{
			c = (d >= '0' && d <= '9') || d == '-' || d == 'not' || Kbd.isEditKey(c);
		}

		if ( ! c )
			alert("Oops, please enter a valid date 'YYYY-MM-DD'" );

		return c;
	}
};

/* --------------------------------------------------------------------- */

