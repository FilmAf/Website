/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var FriendInvite =
{
	showRejected : 0,

	onPopup : function(id)
	{
		var e, u = '', m = '', t, v;

		if ( (e = $('friend_invite'	)) )
		{
			u = e.getAttribute('n_user');
			m = e.getAttribute('n_email');
		}
		if ( u == 'NONE' )
			v = (t = m) + ' to join FilmAf';
		else
			v = (t = Str.ucFirst(u)) + ' to be your friend';

		if ( (e = $('i_img'			)) ) e.src   = 'http://dv1.us/d1/1.gif';
		if ( (e = $('n_seed_img'	)) ) e.value = '';
		if ( (e = $('n_seed_ext'	)) ) e.value = '';
		if ( (e = $('n_invite_text'	)) ) e.value = '';
		if ( (e = $('n_friend_1'	)) ) e.innerHTML = v;
		if ( (e = $('n_friend_2'	)) ) e.innerHTML = t;
		if ( (e = $('n_friend_id'	)) ) e.value = u;
		if ( (e = $('n_friend_email')) ) e.value = m;

		Ajax.asynch('secure', 'FriendInvite.__getSecCode', '?what=get');
	},

	__getSecCode: function()
	{
		if ( Ajax.ready() )
		{
			var e = $('i_img'),
				f = $('n_seed_ext'),
				a = Ajax.getLines(),
				b = {};

			if ( e && f && a.length >= 3 && FriendInvite._parseStatus(a[2], b) && b.seed )
			{
				var i = new Image();
				i.src	= Filmaf.baseDomain + "/icons/security-code.jpg?id=" + b.seed;
				e.src	= i.src;
				f.value = b.seed;
			}
		}
	},

	send : function(b)
	{
		var e   = $('n_seed_img'),
			f   = $('n_seed_ext'),
			g   = $('n_invite_text'),
			u   = $('n_friend_id'),
			err = '';

		if ( b && e && f && g )
		{
			if ( ! (g.value = Str.trim(g.value)) ) err += 'the intro to ' + Str.ucFirst(u);
			if ( ! (e.value = Str.trim(e.value)) ) err += (err ? ' and ' : '') + 'the verification code';
			if ( err )
			{
				alert('Oops, you forgot ' + err + '.');
				return;
			}
			Ajax.asynch('secure', 'FriendInvite.__verifyHuman', '?what=check&int='+e.value+'&ext='+f.value);
		}
		else
		{
			Context.close();
		}
	},

	__verifyHuman : function()
	{
		if ( Ajax.ready() )
		{
			var e   = $('i_img'),
				f   = $('n_seed_ext'),
				g   = $('n_seed_img'),
				h   = $('n_invite_text'),
				u   = $('n_friend_id'),
				v	= $('n_friend_email'),
				a   = Ajax.getLines(),
				err = '',
				b   = {};

			if ( e && f && g && h && a.length >= 3 && FriendInvite._parseStatus(a[2], b) )
			{
				switch ( b.status )
				{
				case 'good':
					Context.close();
					s_post = 'version=v1'+
							 '&ext='   +encodeURIComponent(f.value)+
							 '&int='   +encodeURIComponent(g.value)+
							 '&invite='+encodeURIComponent(h.value);
					if ( u && v )
						Ajax.asynch('friend', 'FriendInvite.__invite', '?what=invite&user='+u.value+'&email='+v.value, s_post);
					break;
				case 'seed':
					var i   = new Image();
					i.src   = Filmaf.baseDomain + "/icons/security-code.jpg?id=" + b.seed;
					e.src   = i.src;
					f.value = b.seed;
					g.value = '';
					alert('Oops, the verification code you entered did not match the picture\n\nPlease try again.\n\nThanks.');
					break;
				}
			}
		}
	},

	__invite : function()
	{
		if ( Ajax.ready() )
		{
			var a  = Ajax.getLines(),
				b = {},
				e = $('invite_div');

			if ( e )
				e.outerHTML = '';

			if ( a.length >= 3 && FriendInvite._parseStatus(a[2], b) && b.invited )
			{
				switch ( b.status )
				{
				case 'sent':
					alert('Invitation sent.');
					location.href = location.href;
					break;
				case 'already sent':
					alert('Sorry, an invitation from you to '+b.invited+' was sent recently.\n\nWe can only send another one after a week has passed.');
					break;
				case 'sent again':
					alert('Invitation resent.');
					break;
				case 'accepted mutual':
					alert('You and '+b.invited+' had matching invitations.\n\n'+b.invited+' has been added to your list of friends.');
					location.href = location.href;
					break;
				case 'not human':
				default:
					alert('Oops something went wrong. Please wait\na few minutes and try again. Sorry.');
					break;
				}
			}
		}
	},

    _parseStatus : function(s,t)
    {
		var i, u, v;
		s = s.split('\t');

		t.status = '';
		for ( i = 0 ; i < s.length ; i += 2 )
		{
			u = s[i+1];
			v = Dom.decodeHtmlForInput(u);
			switch ( s[i] )
			{
			case 'invited': t.invited = u;			  break;
			case 'status':  t.status  = u;			  break;
			case 'seed':    t.status  = 'seed'; t.seed   = u; break;
			}
		}
		return t.status != '';
	},

	accept : function(i,s)
	{
		var e;
		if ( (e = $('ba_'+i)) ) e.disabled = true;
		if ( (e = $('br_'+i)) ) e.disabled = true;
		Ajax.asynch('friend', 'FriendInvite.__accept', '?what=accept&user='+s);
	},

	decline : function(i,s)
	{
		var e;
		if ( (e = $('ba_'+i)) ) e.disabled = true;
		if ( (e = $('br_'+i)) ) e.disabled = true;
		Ajax.asynch('friend', 'FriendInvite.__decline', '?what=reject&user='+s);
	},

	__accept : function()
	{
		if ( Ajax.ready() ) location.href = location.href;
	},

	__decline : function()
	{
		if ( Ajax.ready() ) FriendInvite._show();
	},

	setShowDeclined : function(b)
	{
		FriendInvite.showRejected = b ? 1 : 0;
		Home.setCookies();
		FriendInvite._show();
	},

	_show: function()
	{
		Ajax.asynch('friend', 'FriendInvite.__show', '?what=showinvites&rejected='+FriendInvite.showRejected);
	},

	__show : function()
	{
		if ( Ajax.ready() )
		{
			var e = $('invites'),
				a = Ajax.getLines(),
				b = {},
				s, n, i;

			if ( e && a.length >= 3 && FriendInvite._parseShow(a[2], b) )
			{
				n = b.count;
				s =	  "<table width='100%'>"+
						"<thead>"+
						  "<tr>"+
							"<td colspan='4'>"+
							  "<div style='float:right;margin:0 2px 0 6px'>"+
								( FriendInvite.showRejected ? "<a href='javascript:void(FriendInvite.setShowDeclined(0))'>Hide previously rejected...</a>"
															: "<a href='javascript:void(FriendInvite.setShowDeclined(1))'>Show previously rejected...</a>"
								)+
							  "</div>"+
							  (n ? ( n > 1 ? "You have "+n+" new people who want to be your friend"
										   : "You have one new person who wants to be your friend"
								   )
								 : "You have no new friend invitations"
							  )+
							"</td>"+
						  "</tr>";

				if ( n > 0 )
				{
					s +=  "<tr>"+
							"<td width='1%'>Invite from</td>"+
							"<td>Message to you</td>"+
							"<td width='1%' style='text-align:center'>Created on</td>"+
							"<td width='1%' style='text-align:center'>Your reponse</td>"+
						  "</tr>";
				}

				s +=	"</thead>"+
						"<tbody>";

				for ( i = 0 ; i < n ; i++ )
				{
					b    = {};
					FriendInvite._parseShow(a[i+3], b);
					if ( b.name ) b.uc = b.name + ' (' + b.uc + ')';
					s +=  "<tr>"+
							"<td style='white-space:nowrap'><a href='http://"+b.invitee_id+Filmaf.cookieDomain+"' target='invitee'>"+b.uc+"</a></td>"+
							"<td>"+b.invite+"</td>"+
							"<td style='white-space:nowrap'>"+b.created_tm+"</td>"+
							"<td style='white-space:nowrap;text-align:center'>"+
							  "<input type='button' id='ba_"+i+"' onclick='FriendInvite.accept("+i+",\""+b.invitee_id+"\")' value='Accept' style='width:72px;margin:0 4px 0 8px' />"+
							  ( b.rejected_ind == 'Y'
								? "<span style='width:72px;margin:0 4px 0 8px;position:relative;top:-2px'>Declined</span>"
								: "<input type='button' id='br_"+i+"' onclick='FriendInvite.decline("+i+",\""+b.invitee_id+"\")' value='Decline' style='width:72px;margin:0 4px 0 8px' />")+
							"</td>"+
						  "</tr>";
				}
				s +=	"<tbody>"+
					  "</table>";
				e.innerHTML = s;
			}
		}
	},

	_parseShow : function(s,t)
	{
		var i, u, v;
		s = s.split('\t');

		t.invitee_id = '';
		t.count		 = '';
		for ( i = 0 ; i < s.length ; i += 2 )
		{
			u = s[i+1];
			v = Dom.decodeHtmlForInput(u);
			switch ( s[i] )
			{
			case 'count':			 t.count			= u; break;
			case 'has_rejected':	 t.has_rejected		= u; break;
			case 'showing_rejected': t.showing_rejected	= u; break;
			case 'invitee_id':		 t.invitee_id		= u; break;
			case 'name':			 t.name				= u; break;
			case 'uc':				 t.uc				= u; break;
			case 'invite':			 t.invite			= u; break;
			case 'created_tm':		 t.created_tm		= u; break;
			case 'rejected_ind':	 t.rejected_ind		= u; break;
			}
		}
		return t.invitee_id != '' || t.count != '';
	}
};

/* --------------------------------------------------------------------- */

