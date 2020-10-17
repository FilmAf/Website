/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var FriendPainter =
{
	__paint : function() // friends_callback
	{
		if ( Ajax.ready() )
		{
			var a	    = Ajax.getLines(),
				n_cols  = 5,
				n_width = '20%',
				b	    = {},
				s	    = "<table width='100%'><tr>",
				j	    = 0,
				k	    = 0, e, f, i, t, w, p, tot;

			if ( a.length >= 3 && FriendPainter.parse_line(a[2],b) && (e = $('tbl_friends')) )
			{
				if ( (f = $('friends_page')) ) f.value = b.page;
				if ( (f = $('friends_last')) ) f.value = b.last ? 1 : 0;
				tot = b.total;

				for ( i = 3 ; i < a.length ; i++ )
				{
					b = {};
					if ( FriendPainter.parse_line(a[i],b) )
					{
						if ( j && (j % n_cols) == 0 )
							s += "</tr><tr>";

						t  = b.top_friend_ind == 'Y' ? '*' : '';

						s +=  "<td style='text-align:center;vertical-align:top;padding:10px 0 10px 0' width='"+n_width+"'>"+
								"<a href='http://"+b.friend_id+Filmaf.cookieDomain+"'>"+
								  (b.photo == ''
									? "<img src='http://dv1.us/d1/m_64.png' />"
									: "<img onmouseover='ImgPop.show(this,0)' src='http://dv1.us" + b.photo + "_t.jpg' />")+
								  "<br />"+
								  (b.name == ''
									? b.friend_uc + t
									: b.name + t + "<br />(" + b.friend_uc + ")") +
								"</a>"+
							  "</td>";
						j++;
					}
				}

				if ( (k = j % n_cols) )
					for (  ;  k % n_cols  ;  k++ ) s += "<td>&nbsp;</td>";

				e.innerHTML = j ? s + '</tr></table>' : '&nbsp;';

				if ( (e = $('tot_friends')) )
					e.innerHTML = tot > 10 ? '('+tot+')' : '';
			}
		}
	},

	parse_line : function(s,t)
	{
		var i, u, v;
		s = s.split('\t');

		t.friend_id = '';
		t.page      = 0;
		for ( i = 0 ; i < s.length ; i += 2 )
		{
			u = s[i+1];
			v = Dom.decodeHtmlForInput(u);
			switch ( s[i] )
			{
			case 'last':			t.last			 = Dec.parse(u); break;
			case 'page':			t.page			 = Dec.parse(u); break;
			case 'total':			t.total			 = Dec.parse(u); break;
			case 'friend_id':		t.friend_id		 = u; t.friend_uc = Str.ucFirst(u); break;
			case 'top_friend_ind':	t.top_friend_ind = u; break;
			case 'name':			t.name			 = u; break;
			case 'photo':			t.photo			 = u; break;
			case 'gender':			t.gender		 = u; break;
			}
		}
		return t.friend_id != '' || t.page;
	}
};

/* --------------------------------------------------------------------- */

