/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Seed =
{
	_ip			 : '',
	_time_offset : 0,

	init : function()
	{
		var e;

		if ( (e = $('h_ip')) )
			Seed._ip = e.value;
		else
			alert('ERROR on Seed.init(): Missing h_ip reference value');

		if ( (e = $('h_time')) )
			Seed._time_offset = Edit.getInt(e) - (new Date()).getTime();
		else
			alert('ERROR on Seed.init(): Missing h_time reference value');
	},

	get : function()
	{
		var d = new Date();
		d.setTime(d.getTime() + Seed._time_offset);
		return DateTime.toTimeStr(d) + ' ' + Seed._ip;
	}
};

/* --------------------------------------------------------------------- */

