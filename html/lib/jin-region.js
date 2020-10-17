/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Region =
{
	set : function(r) // f_sel_region
	{
		var e;
		if ( (e = $('n'+Filmaf.inputLine.substr(1))) )
		{
			if ( e.options )
			{
				switch ( r )
				{
				case -1: r = '-'; break;
				case  7: r = 'A'; break;
				case  8: r = 'B'; break;
				case  9: r = 'C'; break;
				}
				DropDown.selectFromVal(e,r);
				Undo.change(e);
			}
		}

		Context.close();

		return false;
	},

	valMediaN : function(s_field, n_cnt, s_media_cd) // valRegionMediaTypeN
	{
		var i, b;
		for ( i = 0, b = true  ;  b !== false && i < n_cnt  ;  i++ )
			b = Region.valMedia(s_field + i, s_media_cd);
		return b !== false;
	},

	valMedia : function(s_field, s_media_cd) // valRegionMediaType
	{
		var e = $(s_field);
		if ( e )
		{
			var v = DropDown.getSelValue(e),
				w = false;

			switch ( s_media_cd )
			{
			case 'D': case 'V':						if ( /[ABC]/.test(v)       ) w = 'DVDs';            break;
			case 'B': case '3': case '2': case 'R':	if ( /[123456]/.test(v)    ) w = 'Blu-rays';        break;
			case 'H': case 'C': case 'T':			if ( /[123456ABC]/.test(v) ) w = 'HD DVDs';         break;
			case 'A':								if ( /[123456ABC]/.test(v) ) w = 'DVD Audios';      break;
			case 'P': case 'O': default:			if ( /[123456ABC]/.test(v) ) w = 'this media type'; break;
			}

			if ( w )
				return Validate.warn(e, true, true, 'Region ' + v + ' is not a valid option for ' + w + '.', false);

			if ( s_media_cd != 'D' && s_media_cd != 'B' && v == '-' )
				DropDown.selectFromVal(e,'0');
		}
		return true;
	}
};

/* --------------------------------------------------------------------- */

