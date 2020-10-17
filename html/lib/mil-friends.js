/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Friends =
{
	getPage : function(i) // getFriendsPage
	{
		var k = Microblog.getNextPageNumber('friends', i);
		if ( k > 0 ) Ajax.asynch('friend', 'FriendPainter.__paint', '?what=list&page='+k+'&user='+Filmaf.viewCollection);
	},

	divorceLoad : function()
	{
		Ajax.asynch('friend', 'Friends.__divorceLoad', '?what=listall');
	},

	__divorceLoad : function() // divorce_callback_load
	{
		if ( Ajax.ready() )
		{
			var a	    = Ajax.getLines(),
				n_cols  = 5,
				n_width = '20%',
				b	    = {},
				j	    = 0,
				s	    = "<table width='100%'>",
				e, i, k;

			if ( a.length >= 3 && (e = $('div_divorce')) )
			{
				for ( i = 2 ; i < a.length ; i++ )
				{
					b = {};
					if ( FriendPainter.parse_line(a[i],b) )
					{
						t  = b.top_friend_ind == 'Y' ? '*' : '';
						w  = "<div>" + (b.name  == '' ? b.friend_uc + t			      : b.name + t + "<div>(" + b.friend_uc + ")</div>"   ) + "</div>";
						p  = "<div>" + (b.photo == '' ? "<img src='http://dv1.us/d1/m_64.png' />" : "<img src='http://dv1.us" + b.photo + "_t.jpg' />") + "</div>";
						s += (j % n_cols == 0 ? (j ? '<tr>' : '</tr><tr>') : '')+
								"<td style='text-align:center;vertical-align:top' width='"+n_width+"'>"+
								  "<a href='javascript:void(Friends.divorce(\""+b.friend_id+"\",\""+b.friend_uc+"\"))'>"+
									p + w +
								  "</a>"+
								"</td>";
						j++;
					}
				}

				if ( (k = j % n_cols) )
				{
					for (  ;  k % n_cols  ;  k++ ) s += "<td>&nbsp;</td>";
					s += '</tr>';
				}
				else
				{
					s = s.substr(0, s.length - 5);
				}

				e.innerHTML = j ? s + '</table>' : '&nbsp;';
			}
		}
	},

	divorce : function(n,uc) // friend_del
	{
		if ( confirm("Are you sure you no longer want to be friends with "+uc+"?\n\nOK=Yes - Cancel=No") )
		{
			Context.close();
			Ajax.asynch('friend', 'Friends.__divorce', '?what=divorce&user='+n);
		}
	},

	__divorce : function() // divorce_callback_refresh
	{
		if ( Ajax.ready() )
		{
			location.href = location.href;
		}
	},

	validateEmail : function()
	{
		var c = {b_changed:false}, f;

		if ( ! (f = $('myform')) )
			return true;

		Validate.reset('email');

		if ( Email.validate('email',c,0,'Email address') !== false )
		{
			f.method = 'post';
			f.action = '/?tab=friends&act=email';
			f.submit();
		}
	},

	validateName : function()
	{
		var c = {b_changed:false}, f;

		if ( ! (f = $('myform')) )
			return true;

		Validate.reset('name');

		if ( Str.validate('name',c,3,32,0,'Name',0) !== false )
		{
			f.method = 'post';
			f.action = '/?tab=friends&act=name';
			f.submit();
		}
	},

	validateEdit : function()
	{
	},

	attachByName : function()
	{
		var e = window.self.document.getElementsByTagName('input'), i, x;
		for( i = e.length  ; --i >= 0  ;  )
		{
			x = e[i];
			if ( x.id.substr(0,4) == 'cbi_' )
				x.onclick = function(ev){Friends.onClickName(ev,this.id);};
		}
	},

	onClickName : function(ev, id)
	{
		var e = window.self.document.getElementsByTagName('input'), i, x, b = 0;
		for( i = e.length  ; --i >= 0  ;  )
		{
			x = e[i];
			if ( x.id && x.id.substr(0,4) == 'cbi_' && x.id != id && x.checked )
			{
				x.checked = false;
				b = 1;
			}
		}
		$('friend_invite').setAttribute('n_user', id.substr(4).replace(/_/,'-'));
		if ( ! b ) Context.attach('friend_invite' ,false,'menu-home-invite');
	},

	newer : function() { Friends.getPage(-1); }, // friend_prev
	curr  : function() { Friends.getPage( 0); }, // friend_top
	older : function() { Friends.getPage( 1); }  // friend_next
};

/* --------------------------------------------------------------------- */

