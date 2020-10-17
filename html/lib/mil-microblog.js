/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Microblog =
{
	_widget		: '',
	_action		: '',
	_text		: '',
	_attachDvd	: '',
	_attachTub	: '',

	onPostingPopup : function(id) // onPostMenuPopup, called from onPopup for 'home_blog' and 'home_wall'
	{
		var n    = id.substr(5),
			skip = true,
			e    = $('n_text_'+n),
			s    = e ? (e.value = Str.trim(e.value)) : '';

		if ( s.length < 1 )
		{
			alert('Sorry, did not get your message.\n\n'+
				  '        1. Enter the text of your post in the field above.\n'+
				  '        2. Chose the option to attach something if you want.\n'+
				  '        3. Press "Post."\n\n'+':-)');
		}
		else
		{
			if ( s.length > 500 )
			{
				alert('Sorry, your ' + (n == 'wall' ? n : 'microblog') + ' message must have no more than 500 characters.\n\n'+
					  'It has ' + s.length +'.');
			}
			else
			{
				Microblog._widget	 = n;
				Microblog._action	 = 'post';
				Microblog._text		 = s;
				Microblog._attachDvd = '';
				Microblog._attachTub = '';

				if ( CheckBox.getVal_1('n_'+n+'_up') )
				{
					if ( (a = $('ex_'+n+'_dvd')) )
					{
						a.onmouseover   = function(ev){Img.mouseOver(ev,this,5);};
						a.onmouseout    = function(  ){Img.mouseOut(this,5);};
						a.onclick       = function(ev){Img.click(ev,this,5,this.id.substr(3));};
					}
					if ( (a = $('ex_'+n+'_tub')) )
					{
						a.onmouseover   = function(ev){Img.mouseOver(ev,this,5);};
						a.onmouseout    = function(  ){Img.mouseOut(this,5);};
						a.onclick       = function(ev){Img.click(ev,this,5,this.id.substr(3));};
					}
					skip = false;
				}
				else
				{
					Microblog.blog();
				}
			}
		}

		return skip;
	},

	onContextPopup : function(tid) // onEditReplyMenuPopup, called from onPopup for 'context_blog_edit' and 'context_blog_reply'
	{
		var u, e, f, g, a, by, bd, cy, cd;

		Filmaf.contextStr = tid; // request_type.user_id.blog_or_wall.blog_id.reply_num => rep.ash.B.41.0
		u = $('blo'+tid.substr(3));
		e = $('tim'+tid.substr(3));
		a = tid.split('.');
		switch ( tid.substr(0,3) )
		{
		case 'edi':
			g = $('n_blog_edit');
			f = $('tit_blog_edit');
			by = $('atty'+tid.substr(3)); // obj_type.obj_id => D.99999
			bd = $('attd'+tid.substr(3)); // obj_type.obj_id => D.99999
			cy = $('n_edit_attach_tub');
			cd = $('n_edit_attach_dvd');
			if ( u && e && g && f && cy && cd )
			{
				g.value     = u.innerHTML.replace(/<br *\/?>/ig,'\n');
				f.innerHTML = 'Editing your msg from ' + e.innerHTML;
				cd.value     = bd ? bd.value.split('.')[1]: '';
				cy.value     = by ? by.value.split('.')[1]: '';
				Edit.countDown('rem2',500 - g.value.length);
			}
			break;
		case 'rep':
			g = $('n_blog_reply');
			f = $('tit_blog_reply');
			if ( u && e && g && f )
			{
				f.innerHTML = 'Replying to ' + a[1] + '&#39;s msg from ' +  e.innerHTML;
				Edit.countDown('rem3',500 - g.value.length);
			}
		break;
		}
	},

	del : function(s_widget,s) // delPost
	{
		 s = s.split('.'); // request_type.user_id.blog_or_wall.blog_id.reply_num => del.ash.B.41.0

		if ( s.length >= 5 && confirm("Are you sure you want to delete this message?\n\nOK=Yes - Cancel=No") )
		{
			var s_user      = s[1],
				s_loc       = s[2],			  //  'W' or 'B'
				n_blog_id   = Dec.parse(s[3]),
				n_reply_num = Dec.parse(s[4]),
				e			= $(s_widget+'_page'),
				n_page		= e ? Edit.getInt(e) : 0;

			s = '?mode='	  + s_widget				+ // 'wall', 'blog', 'updates'
				'&what=del'								+
				'&user='	  + s_user					+
				'&view='	  + Filmaf.viewCollection	+
				'&blog_id='	  + n_blog_id 				+
				'&reply_num=' + n_reply_num				+
				'&page='	  + n_page					;

			Ajax.asynch('home', 'MicroblogPainter.__paint', s);
		}
	},

	setShowReplies : function(x) // setBlogReplyShow
	{
		Cookie.set('showreplies', x ? 'Y' : '');

		var g = window.self.document.getElementsByTagName('div'), i, e, f, s;

		for( i = g.length  ; --i >= 0  ;  )
		{
			f = g[i];
			if ( f.id && f.id.substr(0,4) == 'hid.' && (e = $(f.id+'_sav')) && (x ? e.innerHTML : !e.innerHTML) )
			{
				s = f.innerHTML;
				f.innerHTML = e.innerHTML;
				e.innerHTML = s;
			}
		}
		if ( x ) MicroblogPainter.reattachDvdMenu();
	},

	showReply : function(e) // showReply
	{
		if ( Dom.flipHidden(e) )
		MicroblogPainter.reattachDvdMenu();
	},

	blogAttached : function(s_widget,s_action) // postAttach
	{
		var e, n, x, s;

		Microblog._attachDvd = '';
		Microblog._attachTub = '';

		if ( ! s_widget )
		{
			Context.close();
			return;
		}

		n = s_action ? s_action : s_widget;

		e = $('n_'+n+'_attach_dvd');
		if ( e && (e = e.value = Str.trim(e.value)) )
		{
			if ( /^[0-9]+$/.test(e) && (x = Dec.parse(e)) > 0 && x < 999999 )
				Microblog._attachDvd = x;
			else
				if ( confirm("We could not recognize the DVD id you provided.\nWe expected\n"+
							 "a numeric with up to 6 digits.\n\n"+
							 "Do you want to fix it?\n\n"+
							 "OK=Yes - Cancel=No") ) return;
		}

		Microblog._attachTub = Microblog.validateYouTube('n_'+n+'_attach_tub');
		if ( Microblog._attachTub === false ) return;

		Context.close();
		Microblog.blog();
	},

	validateYouTube : function(s)
	{
		Validate.reset(s);
		if ( (e = $(s)) && (e = e.value = Str.trim(e.value)) )
			if ( (e = YouTube.validate(s,null,1,'YouTube video id',0)) !== false )
				return e;
			else
				return false;
		return '';
	},

	setAllowReplies : function(e) // setBlogReply
	{
		Ajax.asynch('home', 'Ajax.__ignore', '?mode=set&what=microblog_reply_ind&val='+(e.checked ? 'Y' : 'N'));
	},

	blogEdit : function(r,n) // editBlog
	{
		var e = $(r ? 'n_blog_reply' : 'n_blog_edit'),
			b = false,
			a = Filmaf.contextStr.split('.'),	// request_type.user_id.blog_or_wall.blog_id.reply_num => rep.ash.B.41.0 edi.ash.B.41.0
			s;

		if ( n && a.length >= 5 )
		{
			if ( e )
			{
				if ( (s = Str.trim(e.value)) )
				{
					Microblog._widget    = a[2] == 'W' ? 'wall' : (a[1] == Filmaf.viewCollection ? 'blog' : 'updates');
					Microblog._action    = r ? 'reply' : 'edit';
					Microblog._text      = s;
					Microblog._attachDvd = '';
					Microblog._attachTub = '';
					Microblog.blogAttached(Microblog._widget,Microblog._action);
				}
				else
				{
					alert('Sorry, did not get your message.');
				}
			}
		}
		else
		{
			b = true;
		}

		if ( b ) Context.close();
	},

	blog : function() // blog
	{
		var n_blog_id    = 0,
			n_reply_num  = 0,
			n_pic_id     = 0,
			s_pic_source = '-',
			s_pic_name   = '-',
			s_youtube_id = '-',
			n_obj_id     = 0,
			s_obj_type   = '-',
			s_user, a, e;

		switch ( Microblog._action )
		{
		case 'edit':
		case 'reply':
			a = Filmaf.contextStr.split('.'); // request_type.user_id.blog_or_wall.blog_id.reply_num => rep.ash.B.41.0 edi.ash.B.41.0
			if ( a.length < 5 ) return;
			s_user      = a[1];
			s_pos       = a[2];		// 'W' or 'B'
			n_blog_id   = Dec.parse(a[3]);
			n_reply_num = Dec.parse(a[4]);
			break;
		case 'post':
			s_user      = Filmaf.viewCollection;
			if ( (e = $('n_text_'+Microblog._widget)) ) e.value = '';
			if ( (e = $('n_'+Microblog._widget+'_up')) ) e.checked = false;
			break;
		}

		if ( Microblog._attachDvd )
		{
			s_pic_source = 'D';
			s_obj_type   = 'D';
			n_obj_id     = Microblog._attachDvd;
		}

		if ( Microblog._attachTub )
		{
			s_youtube_id   = encodeURIComponent(Microblog._attachTub);
		}

		s_post = 'version=v1'												+
				 '&blog_id='    + n_blog_id 								+
				 '&reply_num='  + n_reply_num								+
				 '&pic_id='     + n_pic_id									+
				 '&pic_source=' + encodeURIComponent(s_pic_source)			+
				 '&pic_name='   + encodeURIComponent(s_pic_name)			+
				 '&youtube_id=' + encodeURIComponent(s_youtube_id)			+
				 '&obj_id='     + n_obj_id									+
				 '&obj_type='   + encodeURIComponent(s_obj_type)			+
				 '&blog='       + encodeURIComponent(Microblog._text)		+
				 '&reply='      + (Microblog._action == 'reply' ? 'Y' : 'N');

		Ajax.asynch('home', 'MicroblogPainter.__paint', '?mode='+Microblog._widget+'&what=set&user='+s_user+'&view='+Filmaf.viewCollection, s_post);

		if ( (e = $('n_dvd_blog'    )) ) e.value = '';
		if ( (e = $('n_blog_edit'   )) ) e.value = '';
		if ( (e = $('n_blog_reply'  )) ) e.value = '';
		if ( (e = $('n_edit_attach' )) ) e.value = '';
		if ( (e = $('n_reply_attach')) ) e.value = '';
		if ( (e = $('n_blog_attach' )) ) e.value = '';
		if ( (e = $('n_wall_attach' )) ) e.value = '';
	},

	getNextPageNumber : function(w, i) // getNextPageNumber
	{
		var e, j, k;

		if ( ! i )
		return 1;

		j = (e = $(w+'_page')) ? Edit.getInt(e) : 1;
		z = (e = $(w+'_last')) ? Edit.getInt(e) : 0;
		k = j + i;
		if ( k <= 0 )
		{
			alert('You are already at the first page.');
			return -1;
		}
		if ( z && k >= j )
		{
			alert('You are already at the last page.');
			return -1;
		}

		return k;
	},

	getPage : function(w,i) // getBlogPage
	{
		var k = Microblog.getNextPageNumber(w, i);
		if ( k > 0 ) Ajax.asynch('home', 'MicroblogPainter.__paint', '?mode='+w+'&what=get&page='+k+'&view='+Filmaf.viewCollection);
	},

	newer : function() { Microblog.getPage('blog',-1); }, // blog_newer
	curr  : function() { Microblog.getPage('blog', 0); }, // blog_curr
	older : function() { Microblog.getPage('blog', 1); }  // blog_older
};

var Wall =
{
	newer : function() { Microblog.getPage('wall',-1); }, // wall_newer
	curr  : function() { Microblog.getPage('wall', 0); }, // wall_curr
	older : function() { Microblog.getPage('wall', 1); }  // wall_older
};

var Updates =
{
	newer : function() { Microblog.getPage('updates',-1); }, // updates_newer
	curr  : function() { Microblog.getPage('updates', 0); }, // updates_curr
	older : function() { Microblog.getPage('updates', 1); }  // updates_older
};

/* --------------------------------------------------------------------- */

