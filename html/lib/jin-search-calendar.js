/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var SearchCalendar =
{
	range : [1880,(new Date()).getFullYear()+1],
	parms : null,

	setup : function()
	{
		SearchCalendar.parms = new Array(
			{ifFormat:'%Y-%m-%d',align:'pointer',range:SearchCalendar.range,weekSelection:true,onInvoke_Ed:SearchCalendar.onInvoke,onInit_Ed:SearchCalendar.onInit,onSelect_Ed:SearchCalendar.onSelect,returnInvoker:true,refParm:0},
			{ifFormat:'%Y-%m-%d',align:'pointer',range:SearchCalendar.range,weekSelection:true,onInvoke_Ed:SearchCalendar.onInvoke,onInit_Ed:SearchCalendar.onInit,onSelect_Ed:SearchCalendar.onSelect,returnInvoker:true,refParm:1},
			{ifFormat:'%Y-%m-%d',align:'pointer',range:SearchCalendar.range,weekSelection:true,onInvoke_Ed:SearchCalendar.onInvoke,onInit_Ed:SearchCalendar.onInit,onSelect_Ed:SearchCalendar.onSelect,returnInvoker:true,refParm:2},
			{ifFormat:'%Y-%m-%d',align:'pointer',range:SearchCalendar.range,weekSelection:true,onInvoke_Ed:SearchCalendar.onInvoke,onInit_Ed:SearchCalendar.onInit,onSelect_Ed:SearchCalendar.onSelect,returnInvoker:true,refParm:3});

		for ( var i = 0 ; i < 4 ; i++ )
			Search.calendars[i] = Calendar.setup(SearchCalendar.parms[i]);
	},

	onInvoke : function(p) // calendarOnInvoke
	{
		// p.periodField - has been initialized on Calendar.setup
		// p.periodKind  - is being initialized here on f_date_on_invoke
		// p.periodForm  - will be initialized on f_date_on_init
		if ( p.refParm !== null )
		{
			p.periodField = $('str'+p.refParm);
			p.inputField = $('hid'+p.refParm);
		}

		if ( p.periodField && p.inputField )
		{
			var re, y1, y2, m1 = 1, m2 = 1, d1, d2, s_option, s_day = Str.trim(p.periodField.value);

			re = /^([0-9]{4})$/.exec(s_day); if ( re )
			{
				y1 = re[1];
				if ( y1 >= p.range[0] && y1 <= p.range[1] ) { s_option = 'year'; m1 = 1; d1 = 1; }
			} else {
			re = /^([0-9]{4})[-]([0-9]{2})$/.exec(s_day); if ( re )
			{
				y1 = re[1]; m1 = re[2];
				if ( y1 >= p.range[0] && y1 <= p.range[1] )
				{
					if ( m1 == 0	     ) { s_option = 'year'; s_day = y1; m1 = 1; d1 = 1; } else
					if ( m1 >= 1 && m1 <= 12 ) { s_option = 'month'; d1 = 1; }
				}
			} else {
			re = /^([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$/.exec(s_day); if ( re )
			{
				y1 = re[1]; m1 = re[2]; d1 = re[3];
				if ( y1 >= p.range[0] && y1 <= p.range[1] )
				{
					if ( m1 == 0 && d1 == 0  )	{ s_option = 'year'; s_day = y1; m1 = 1; d1 = 1; } else
					if ( m1 >= 1 && m1 <= 12 )
					{
						if ( d1 == 0 ) { s_option = 'month'; s_day = y1 + '-' + m1; d1 = 1;  } else
						if ( DateTime.isValid(y1, m1, d1, p.range[0], p.range[1]) ) s_option = 'day';
					}
				}
			} else {
			re = /^[<]=\x20*([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$/.exec(s_day); if ( re )
			{
				y1 = re[1]; m1 = re[2]; d1 = re[3];
				if ( DateTime.isValid(y1, m1, d1, p.range[0], p.range[1]) ) s_option = 'before';
			} else {
			re = /^[>]=\x20*([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$/.exec(s_day); if ( re )
			{
				y1 = re[1]; m1 = re[2]; d1 = re[3];
				if ( DateTime.isValid(y1, m1, d1, p.range[0], p.range[1]) ) s_option = 'after';
			} else {
			re = /^[>]=\x20*([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})\x20*[<]=\x20*([0-9]{4})[-]([0-9]{2})[-]([0-9]{2})$/.exec(s_day); if ( re )
			{
				y1 = re[1]; m1 = re[2]; d1 = re[3];
				y2 = re[4]; m2 = re[5]; d2 = re[6];
				if ( DateTime.isValid(y1, m1, d1, p.range[0], p.range[1]) && DateTime.isValid(y2, m2, d2, p.range[0], p.range[1]) ) s_option = 'week';
			}}}}}}

			// Verify if consistent with date on hidden field, otherwise update hidden field.
			m1--; // 1-12 to 0-11
			m2--; // 1-12 to 0-11
			if ( s_option )
				p.date = new Date(y1, m1, d1);
			else
				s_option = s_day ? 'invalid' : 'day';
			p.periodKind = s_option;
		}
	},

	onInit : function(cal, div)
	{
													div.style.border			= 'none';
		div = Calendar.createElement("div", div);	div.style.border			= 'inset 1px';
													div.style.borderColor		= '#52a2ff #3182e7 #52a2ff #428aef';
													div.style.color				= '#000';
													div.style.backgroundColor	= '#fff';
		div = Calendar.createElement("div", div);	div.style.borderLeft		= '1px solid #8cb6ef';
		div = Calendar.createElement("div", div);	div.style.borderLeft		= '1px solid #2979e7';
		div = Calendar.createElement("div", div);	div.style.borderLeft		= '2px solid #297de7';
		div = Calendar.createElement("div", div);	div.style.borderLeft		= '1px solid #3182ef';
													div.style.padding			= '3px 16px 6px 25px';

		var p			= cal.params;				if ( ! p.periodKind ) p.periodKind = 'day';
		var b_invalid	= p.periodKind == 'invalid',
			s_option	= b_invalid ? 'day' : p.periodKind,
			wrapper		= Calendar.createElement("div", div);

		if ( p.weekOnly )
		{
			wrapper.innerHTML =
				(b_invalid
				  ? "<div class='rp'>&nbsp;</div>"+
					"<div><span style='color:red'>Warning:</span> although the condition represented in<br />"+
					"this field may be valid it can not be expressed<br />"+
					"in this control.&nbsp; To edit it manually please click<br />"+
					"on the &quot;x&quot; button in the top right corner of the<br />"+
					"calendar to close it. Choices made from this<br />"+
					"control will override existing values.<br />"+
					"</div>"+
					"<div class='rp' style='margin-bottom:8px'>&nbsp;</div>"
				  : '');
		}
		else
		{
			wrapper.innerHTML =
				"<div style='white-space:nowrap'>Please select a time period and click on a date.</div>"+
				(b_invalid
				  ? "<div class='rp'>&nbsp;</div>"+
					"<div><span style='color:red'>Warning:</span> although the condition represented in<br />"+
					"this field may be valid it can not be expressed<br />"+
					"in this control.&nbsp; To edit it manually please click<br />"+
					"on the &quot;x&quot; button in the top right corner of the<br />"+
					"calendar to close it. Choices made from this<br />"+
					"control will override existing values.<br />"+
					"</div>"
				  : '')+
				"<div class='rp'>&nbsp;</div>"+
				"<form style='margin-top:1px; margin-bottom:4px;white-space:nowrap'>"+
					"<input type='radio' name='r' value='day'"   +(s_option == 'day'    ? ' checked' : '')+">On a given <span style='color:blue'>day</span><br />"+
					"<input type='radio' name='r' value='week'"  +(s_option == 'week'   ? ' checked' : '')+">On a given <span style='color:blue'>week</span><br />"+
					"<input type='radio' name='r' value='month'" +(s_option == 'month'  ? ' checked' : '')+">On a given <span style='color:blue'>month</span><br />"+
					"<input type='radio' name='r' value='year'"  +(s_option == 'year'   ? ' checked' : '')+">On a given <span style='color:blue'>year</span><br />"+
					"<input type='radio' name='r' value='before'"+(s_option == 'before' ? ' checked' : '')+">On or <span style='color:blue'>before</span> a given day<br />"+
					"<input type='radio' name='r' value='after'" +(s_option == 'after'  ? ' checked' : '')+">On or <span style='color:blue'>after</span> a given day"+
				"</form>"+
				"<div class='rp' style='margin-bottom:8px'>&nbsp;</div>";
		}

		p.periodForm = Dom.getFirstChildByType(wrapper,'form');
		return div;
	},

	onSelect : function(cal) // calendarOnSelect
	{
		var p = cal.params;

		if ( p.weekOnly )
		{
			// Return dates as a specific day of the chosen week: 1 = Sunday, 2 = Monday, 3 = Tuesday, etc...
			var s_day = cal.date;
			s_day = new Date(s_day.getFullYear(), s_day.getMonth(), s_day.getDate());
			s_day = new Date(s_day.getTime() - (s_day.getDay()- p.weekOnly + 1) * 3600 * 1000 * 24);
			p.periodField.value = DateTime.toDateStr(s_day);
		}
		else
		{
			if ( p.periodForm && p.inputField )
			{
				var s_choice = null;
				if ( cal.currentDateEl.week )
				{
					s_choice = 'week';
				}
				else
				{
					var f = p.periodForm;
					if ( f.r )
					{
						for ( var i = 0 ; i < f.r.length && ! s_choice ; i++ )
						if ( f.r[i].checked )
						s_choice = f.r[i].value;
					}
					if ( ! s_choice ) s_choice = 'day';
				}
				p.periodKind = s_choice;
				if ( p.periodField )
				{
					var s_day = p.inputField.value;
					switch ( s_choice )
					{
					case 'week':
						s_day = cal.date;
						s_day = new Date(s_day.getFullYear(), s_day.getMonth(), s_day.getDate());
						s_day = new Date(s_day.getTime() - s_day.getDay() * 3600 * 1000 * 24);
						s_day = '>= '  + DateTime.toDateStr(s_day) +
						' <= ' + DateTime.toDateStr(new Date(s_day.getTime() + 6 * 3600 * 1000 * 24));
						break;
					case 'day':									break;
					case 'month':	s_day = s_day.substr(0,7);	break;
					case 'year':	s_day = s_day.substr(0,4);	break;
					case 'before':	s_day = '<= '+s_day;		break;
					case 'after':	s_day = '>= '+s_day;		break;
					}
					p.periodField.value = s_day;
				}
			}
		}
	}
};

/* --------------------------------------------------------------------- */

