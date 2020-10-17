/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var SearchMenuAction =
{
	onClick : function(action) // onMenuClick
	{
		if ( ! action.info || ! action.info.id ) return;
		var id = action.info.id,
			md = id.substr(0,7),
			m  = md.substr(3,3);
		
		if ( !(id = id.substr(7)) ) retutn;

		switch ( md )
		{
		case 'hm_gen_':
		case 'hm_rel_':
			if ( id == 'ca' ) id = 'c, a';
			// let it fall
		case 'hm_med_':
		case 'hm_cnt_':
		case 'hm_lan_':
		case 'hm_src_':
		case 'hm_pic_':
			SearchMenuAction._selSearchCode(id,md,m);
			break;
		case 'hm_rtd_':
		case 'hm_rtf_':
			SearchMenuAction._selStars(id);
			break;
		case 'hm_gno_':
			SearchMenuAction._selGenre(id,action);
			break;
		}
	},

	_selSearchCode : function(id,md,m)
	{
		var e = $('str'+Filmaf.inputLine);

		if ( e )
		{
			var r = Str.trim(e.value),
				a = Search.appending[m] && r != '';

			if ( id == 'xx' && (md == 'hm_cnt_' || md == 'hm_lan_') )
				id = 'missing';

			e.value = a = (a ? r + ', ' : '') + id;
			if ( a.match(/[<>!=,]/) && a.length > 16 )
				alert('The '+Search.criteria[m]+' criteria is set to: \n"'+a+'"');
		}
	},

	_selStars : function(id)
	{
		var m  = Filmaf.inputLine.substr(2),
			md = /dvd/.test(Filmaf.inputLine) ? 'y' : 'r',
			f;

		if ( (f = $('n_'+m)) )
		{
			f.value = id;
		}
		if ( (f = $('g_'+m)) )
		{
			if ( id < 0 )
			{
				f.src = 'http://dv1.us/d1/1.gif';
				f.alt = '';
			}
			else
			{
				f.src = 'http://dv1.us/s1/s5'+ md + id +'.png';
				id    = Dec.parse(id);
				f.alt = ((id + 1) / 2) + ' stars';
			}
		}
	},

	_selGenre : function(id,action)
	{
		var m = Filmaf.inputLine.substr(2),
			e, i, j;

		if ( (e = $('g_'+m)) )
		{
			i = action.info.parent && action.info.parent.parent_item && action.info.parent.parent_item.label;
			j = action.info.label.substr(0,1) == '<' ? false : action.info.label;
			e.value = j ? ( i ? i + ' / ' + j : j ) : i;
		}

		if ( (e = $('n_'+m)) )
			e.value = id;
	}
};

/* --------------------------------------------------------------------- */

