/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdStats =
{
	_statsList  : 0,
	_statsPaid  : 0,
	_statsShow  : 'folder',
	_statsGroup : 'titles',

	load : function(s_target, s_show, n_parm)
	{
		switch ( n_parm )
		{
		case -1:
			// initializing (read nothing, cookies already have been read)
			break;

		case -2:
			// selection from side menu (update the new category, read screen selections, and update cookies)
			if ( ! s_show ) return;
			DvdStats._statsShow = s_show;

		case -3:
			// "go" pressed with new parms (read screen selections and update cookies)
			if ( $('stats_mode') )
			{
				DvdStats._statsGroup = DropDown.getSelValue('stats_mode');
			}
			else
			{
				DvdStats._statsList  = CheckBox.getValBool('stats_list') ? '1' : '0';
				DvdStats._statsPaid  = CheckBox.getValBool('stats_paid') ? '1' : '0';
			}
			Home.setCookies();
			break;
		}
		DvdStats.setOptions();

		s_get = '?mode=countdvds&user='+Filmaf.viewCollection+
							   '&list='+DvdStats._statsList+
							   '&paid='+DvdStats._statsPaid+
							   '&show='+DvdStats._statsShow+
							  '&group='+DvdStats._statsGroup+
							 '&target='+s_target;

		Ajax.asynch('home', 'DvdStatsPainter.__'+DvdStats._statsShow, s_get);
	},

	setOptions : function()
	{
		if ( DvdStats._statsShow == 'folder' || DvdStats._statsShow == 'genre' )
		{
			if ( ! $('stats_mode') )
			{
				$('stats_mode_span').innerHTML = 
					"<select id='stats_mode' style='color:#072b4b;font-size:11px; font-weight:bold'>"+
						"<option value='titles'>Films/Titles</option>"+
						"<option value='dvds'>BDs/DVDs</option>"+
						"<option value='disks'>Discs</option>"+
						"<option value='list'>List Price</option>"+
						"<option value='paid'>Price Paid</option>"+
					"</select> ";
			}
			DropDown.selectFromVal('stats_mode', DvdStats._statsGroup);
		}
		else
		{
			if ( ! $('stats_fix_mode') )
			{
				$('stats_mode_span').innerHTML = 
					"<input type='checkbox' id='stats_paid' />Price paid &nbsp; &nbsp;"+
					"<input type='checkbox' id='stats_list' />List price &nbsp; &nbsp;"+
					"<input id='stats_fix_mode' type='hidden' value='dvds' />"+
					"Counting BDs/DVDs &nbsp;";
			}
			CheckBox.setVal('stats_list', DvdStats._statsList);
			CheckBox.setVal('stats_paid', DvdStats._statsPaid);
		}
	}
};

/* --------------------------------------------------------------------- */

