/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var ObjEdit =
{
	onPopup : function(z,el)
	{
		if ( ! z.id ) return;
		var i = z.menu.items,
			t = z.menu.target;

		switch ( z.id )
		{
		case 'help_dir': case 'help_pub':	Filmaf.inputLine = t.id; ObjEdit._contextPubDir(t);  break;
		case 'help_rno':					Filmaf.inputLine = t.id; ObjEdit._contextRegion(t);  break;
		case 'help_gno':					Filmaf.inputLine = t.id; ObjEdit._contextGenre(i,t); break;
		case 'explain_pop':											 Explain.show(t.id,true);    break;
		default:							Filmaf.inputLine = t.id;							 break;
		}
	},

	onClick : function(z,action)
	{
		if ( ! action.info || ! action.info.id ) return;
		var id = action.info.id,
			md = id.substr(0,7),
			tg = Filmaf.inputLine.substr(2);
		
		if ( !(id = id.substr(7)) ) return;

		switch ( md )
		{
		case 'hm_gno_':					ObjEdit._setGenre      (tg,action,id);	break;
		case 'hm_fro_':					ObjEdit._setRating     (tg,action,id);	break;
		case 'hm_cno_': case 'hm_lno_':	ObjEdit._setCountryLang(tg,action,id);	break;
		case 'hm_mas_':					ObjEdit._setAspectRatio(tg,id);			break;
		}
	},

	_contextPubDir : function(t)
	{
	},

	_contextRegion : function(t)
	{
		var e = $('n_a_media_type'), t = '', i = '';

		if ( e )
		{
			switch ( DropDown.getSelValue(e) )
			{
			case 'D':
			case 'V':
				t = 'Click on the map to select the appropriate DVD Region Code.';
				i = "<img src='http://dv1.us/d1/region-dvd.gif' width='450' height='218' border='0' usemap='#region_dvd_map' style='img:focus:outline:none' />";
				break;
			case 'B':
			case '3':
			case '2':
			case 'R':
				t = 'Click on the map to select the appropriate Blu-ray Region Code.';
				i = "<img src='http://dv1.us/d1/region-bd.gif' width='450' height='218' border='0' usemap='#region_bd_map' style='img:focus:outline:none' />";
				break;
			case 'H':
			case 'C':
			case 'T':
			case 'A':
				t = 'HD DVDs and DVD Audios do not have region code restrictions.';
				i = "<img src='http://dv1.us/d1/region-sat.gif' width='450' height='218' border='0' usemap='#region_sat_map' style='img:focus:outline:none' />";
				break;
			case 'P':
			case 'O':
			default:
				t = 'Region codes are not applicable to this media type.';
				i = "<img src='http://dv1.us/d1/region-sat.gif' width='450' height='218' border='0' usemap='#region_sat_map' style='img:focus:outline:none' />";
				break;
			}
		}
		if ( (e = $('help_rno_txt')) ) e.innerHTML = t;
		if ( (e = $('help_rno_img')) ) e.innerHTML = i;
	},

	_contextGenre : function(i,t)
	{
		i.hm_gno_99999.label			 = t.id == 'b_b_genre' ? 'Use Default Genre' : 'Unspecified Genre';
		i.hm_gno_99999.labelTD.innerHTML = "<a><strong>" + i.hm_gno_99999.label + '</strong></a>';
	},

	_setGenre : function(tg,action,id)
	{
		var e, i, j;

		if ( id == '00000' )
			i = id = '';
		else
		{
			i = action.info.parent && action.info.parent.parent_item && action.info.parent.parent_item.label;
			j = action.info.label.substr(0,1) == '<' ? false : action.info.label;
			i = j ? ( i ? i + ' / ' + j : j ) : i;
		}

		if ( (e = $('g_'+tg)) ) e.value = i;
		if ( (e = $('n_'+tg)) ) { e.value = id; Undo.change(e); }
	},

	_setRating : function(tg,action,id)
	{
		var e, i, j;

		i = action.info.parent && action.info.parent.parent_item && action.info.parent.parent_item.label;
		j = action.info.label.substr(0,1) == '<' ? false : action.info.label;
		i = j ? ( i ? i + ' / ' + j : j ) : i;

		if ( (e = $('g_'+tg)) ) e.value = i;
		if ( (e = $('n_'+tg)) ) { e.value = id; Undo.change(e); }
	},

	_setCountryLang : function(tg,action, id)
	{
		var i, e;

		if ( id == 'xx' )
			i = id = '';
		else
		{
			i = Dom.stripTags(action.info.label);
			switch ( id.substr(3) )
			{
			case 'ch': i = 'Chinese-' + i; break;
			case 'in': i = 'Indian-'  + i; break;
			}
			id = id.substr(0,2);
		}

		if ( (e = $('g_'+tg)) ) e.value = i;
		if ( (e = $('n_'+tg)) ) { e.value = id; Undo.change(e); }
	},

	_setAspectRatio : function(tg,id)
	{
		if ( (tg = $('n_'+tg)) )
			tg.value = id.substr(0,1)+'.'+id.substr(1);
	},

	discard : function(msg,url)
	{
		if ( !msg || confirm(msg + '\n\nOK=Yes - Cancel=No') )
			location.href = url ? url : location.href;
	},

	withdraw : function(msg)
	{
		var e = $('act'), f = $('myform');
		if ( e && f && confirm(msg+'\n\nOK=Yes - Cancel=No') )
		{
			e.value = 'del_sub';
			f.action = location.href;
			f.submit();
			return 1;
		}
		return 0;
	},

	showHist : function(s_trg)
	{
		var e = $('hist');
		if ( e.style.visibility == 'hidden' )
		{
			e.innerHTML = "<div style='margin:2px 6px 2px 2px'>"+
							"<iframe scrolling='yes' frameborder='0' src='"+Filmaf.baseDomain+"/utils/hist-"+s_trg+".html?frm=1&obj="+Filmaf.objId+"' style='width:100%;height:260px;padding:1px;border:solid 1px #abadb3'></iframe>"+
						  "</div";
			e.style.visibility = 'visible';
			e = 'Hide history';
		}
		else
		{
			e.innerHTML = '';
			e.style.visibility = 'hidden';
			e = 'Show history';
		}
		$('b_showhist').value = e;
	}
};

/* --------------------------------------------------------------------- */

