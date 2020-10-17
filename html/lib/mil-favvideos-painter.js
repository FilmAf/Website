/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var FavVideosPainter =
{
	__paint : function()
	{
		if ( Ajax.ready() )
		{
			var o = {}, e, c, a, s, x = {}, b = FavVideosPainter._parseHeader(o);

			if ( o && (e = $(o.trg)) )
			{
				switch ( o.what )
				{
				case 'set':
					if ( (c = $('fvid_'+o.cat)) )
						TblMenu.set(c,-3);
					break;
				}

				if ( b && o.length > 2 && FavVideosPainter._parse_line(o.lines[2],x) )
				{
					if ( x.total > 0 )
					{
						if ( x.cat_name === '' )
						{
							switch ( x.cat_id )
							{
							case 0:	 x.cat_name = 'Uncategorized'; break;
							case -1: x.cat_name = 'All'; break;
							default: x.cat_name = '&lt;Unknown category&gt;'; break;
							}
						}

						e.innerHTML = YouTube.embed(x.youtube_id, 1, 0);
						// Firefox gets confused if we do not assigne the element to an intermediate variable
						if ( (a = $('fvid_ref')) ) a.innerHTML = x.cat_name+": "+x.page+" of "+x.total;
						if ( (a = $('fvid_nav')) ) a.style.visibility='visible';
						if ( (a = $('fvid_ctr')) ) a.style.visibility='visible';
					}
					else
					{
						if ( x.cat_name === '' )
						{
							switch ( x.cat_id )
							{
							case 0:	 s = 'There are no uncategorized videos.'; break;
							case -1: s = Str.ucFirst(x.user_id) + ' has not linked to any videos yet.'; break;
							default: s = 'There are no videos for the selected category.'; break;
							}
						}
						else
						{
							s = "There are no videos for "+x.cat_name+".";
						}

						e.innerHTML = "<div style='text-align:center;height:395px;width:640px'>"+s+"</div>";
						if ( (a = $('fvid_ref')) ) a.innerHTML = '&nbsp;';
						if ( (a = $('fvid_nav')) ) a.style.visibility='hidden';
						if ( (a = $('fvid_ctr')) ) a.style.visibility='hidden';
					}
					if ( (a = $('fvid_page' )) ) a.value = x.page;
					if ( (a = $('fvid_total')) ) a.value = x.total;
					if ( (a = $('fvid_last' )) ) a.value = x.page >= x.total ? '1' : '0';
					if ( (a = $('fvid_cat'  )) ) a.value = x.cat_id;
					if ( (a = $('fvid_id'   )) ) a.value = x.user_id+'.V.'+x.blog_id+'.0'; // user.location.blog_id.reply_num
					if ( (a = $('is_1_jump' )) ) a.setAttribute('sp_max',x.total);
					if ( (a = $('is_2_jump' )) ) a.setAttribute('sp_max',x.total);
					if ( (a = $('n_jump'    )) ) a.value = x.page;
				}
				else
				{
					e.innerHTML = '';
				}
			}
		}
	},

	_parseHeader : function(o)
	{
		if ( Ajax.ready() )
		{
			var e;
		
			if ( Ajax.getParms(o) )
			{
				o.page = Ajax.statusTxt(o.line1,'page'	);
				o.cat  = Ajax.statusTxt(o.line1,'cat_id');
				o.what = Ajax.statusTxt(o.line1,'what'	);
				o.trg  = Ajax.statusTxt(o.line1,'target');
				o.usr  = Ajax.statusTxt(o.line1,'user'	);
				o.view = Ajax.statusTxt(o.line1,'view'	);
				o.cnt  = Ajax.statusTxt(o.line1,'count'	);
			}
			else
			{
				o.page = o.cat = o.what = o.trg = o.usr = o.view = o.cnt = '';
			}
			o.err = (o.sta == 'SUCCESS' && o.trg ) ? '' : ( o.msg ? o.msg : 'ERROR');

			if ( o.trg && (e = $(o.trg)) )
				if ( o.err )
					e.innerHTML = o.err;
				else
					return true;
		}
		return false;
	},

	_parse_line : function(s,t)
	{
		var i, u, v;
		s = s.split('\t');
		t.total = false;
		for ( i = 0 ; i < s.length ; i += 2 )
		{
			u = s[i+1];
			v = Dom.decodeHtmlForInput(u);
			switch ( s[i] )
			{
			case 'user_id':			t.user_id		= u; break;
			case 'blog_id':			t.blog_id		= Dec.parse(u); break;
			case 'cat_id':			t.cat_id		= Dec.parse(u); break;
			case 'cat_name':		t.cat_name		= u; break;
			case 'youtube_id':		t.youtube_id	= u; break;
			case 'created_tm':		t.created_tm	= u; break;
			case 'updated_tm':		t.updated_tm	= u; break;
			case 'page':			t.page			= Dec.parse(u); break;
			case 'total':			t.total			= Dec.parse(u); break;
			}
		}
		return t.total !== false;
	}
};

/* --------------------------------------------------------------------- */

