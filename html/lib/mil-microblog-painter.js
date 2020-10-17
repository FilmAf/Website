/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var MicroblogPainter =
{
	__paint : function() // blog_callback, Microblog.blogCallback
	{
		var o  = {}; if ( ! Ajax.getParms(o) ) return;
		var fb = Ajax.statusInt(o.line1,'fb'),
			b  = {};

		if ( o.length >= 3 && MicroblogPainter._parse_line(o.lines[2], b) )
		{
			switch ( b.refresh )
			{
			case 'msg':     MicroblogPainter._paintMsg(o.lines); break;
			case 'replies': MicroblogPainter._paintReplies(o.lines, b.mode); break;
			case 'widget':  MicroblogPainter._paintWidget(o.lines, b.mode, b.page, b.last); break;
			}

			if ( fb && o.length >= 4 && DvdList.parseBlogFb(o.lines[3], b) )
				Facebook.blog(b.obj_id, b.media_type, b.blog);
		}
	},

	_parse_line : function(s,t)
	{
		var i, u, v;
			s = s.split('\t');

		t.mode = '';
		for ( i = 0 ; i < s.length ; i += 2 )
		{
			u = s[i+1];
			v = Dom.decodeHtmlForInput(u);
			switch ( s[i] )
			{
			case 'mode':				t.mode				= u;			break;
			case 'refresh':				t.refresh			= u;			break;
			case 'last':				t.last				= Dec.parse(u);	break;
			case 'page':				t.page				= Dec.parse(u);	break;
			case 'user_id':				t.user_id			= u;			break;
			case 'location':			t.location			= u;			break;
			case 'blog_id':				t.blog_id			= Dec.parse(u);	break;
			case 'reply_num':			t.reply_num			= Dec.parse(u);	break;
			case 'pic_id':				t.pic_id			= Dec.parse(u);	break;
			case 'pic_source':			t.pic_source		= u;			break;
			case 'pic_name':			t.pic_name			= u;			break;
			case 'youtube_id':			t.youtube_id		= u;			break;
			case 'obj_id':				t.obj_id			= Dec.parse(u);	break;
			case 'obj_type':			t.obj_type			= u;			break;
			case 'blog':				t.blog				= u;			break;
			case 'reply_count':			t.reply_count		= Dec.parse(u);	break;
			case 'created_by':			t.created_by		= u; t.created_uc = Str.ucFirst(u); break;
			case 'created_tm':			t.created_tm		= u;			break;
			case 'updated_tm':			t.updated_tm		= u;			break;
			case 'name':				t.name				= u;			break;
			case 'post_age':			t.post_age			= Dec.parse(u);	break;
			case 'is_user_id_friend':	t.is_user_id_friend	= u;			break;
			case 'friend_reply':		t.friend_reply		= u;			break;
			case 'media_type':			t.media_type		= u;			break;
			}
		}
		return t.mode != '';
	},

	_fetch : function(a,c) // fetch
	{
		var b = {};
		return  MicroblogPainter._parse_line(a[c.pos++],b) ? b : 0;
	},

	_paintWidget : function(a, s_widget, n_page, b_last) // doWidget
	{
		var b  = {},
			c  = {pos:3},
			s  = '',
			n  = 0,
			e  = $('tbl_'+s_widget),
			rp = Cookie.get('showreplies') == 'Y',
			s_ref_id, n_curr, s_sub, s_div, f;

		if ( e )
		{
			if ( (f = $(s_widget+'_page')) ) f.value = n_page;
			if ( (f = $(s_widget+'_last')) ) f.value = b_last ? 1 : 0;

			b = MicroblogPainter._fetch(a,c);
			while ( b && n < 10 )
			{
				if ( ! n )
					s += "<table width='100%'>";

				s_ref_id = b.user_id + '.' + b.location + '.' + b.blog_id;
				s_div    = "<div id='hid."+s_ref_id+"_sav' style='visibility:hidden;position:absolute;top:0;left:0'>";
				s       += MicroblogPainter._formatMsg(b, b.mode, s_ref_id, 0);
				n_curr   = b.blog_id;
				s_sub    = '';
				n++;
				while ( (b = MicroblogPainter._fetch(a,c)) )
				{
					if ( n_curr == b.blog_id )
					s_sub += MicroblogPainter._formatMsg(b, b.mode, '', 0) + "</td></tr>";
					else
					break;
				}

				if ( s_sub )
				{
					s_sub = "<div style='margin:2px 0 0 32px;background-color:#ebf4fa'>"+
							  "<table width='100%'>"+
								s_sub+
							  "</table>"+
							"</div>";

					if ( rp )
						s_sub = s_div+"</div><div id='hid."+s_ref_id+"'>"+s_sub+"</div>";
					else
						s_sub = s_div+s_sub+"</div><div id='hid."+s_ref_id+"'></div>";
				}
				else
				{
					s_sub = s_div+"</div><div id='hid."+s_ref_id+"'></div>";
				}

				s += s_sub + "</td></tr>";
			}

			e.innerHTML = s + (n ?    "</table>"+
									"</div>"
								 :  "<div class='wg_sepa'>&nbsp;</div>"+
									  "<div>&nbsp;</div>"+
									"</div>");
			MicroblogPainter.reattachDvdMenu();
		}
	},

	_paintReplies : function (a, s_widget) // doReplies
	{
		var b = {},
			c = {pos:3},
			s = '',
			n = 0,
			s_ref_id, n_curr, s_reply;

		if ( (b = MicroblogPainter._fetch(a,c)) )
		{
			s_ref_id = b.user_id + '.' + b.location + '.' + b.blog_id;
			n_curr   = b.blog_id;

			while ( b )
			{
				if ( n_curr != b.blog_id )
				{
					break;
				}
				if ( b.reply_num )
				{
					s += MicroblogPainter._formatMsg(b, b.mode, '', 0) + "</td></tr>";
					n++;
				}
				b  = MicroblogPainter._fetch(a,c);
			}

			if ( s )
			{
				s = "<div style='margin:2px 0 0 32px;background-color:#ebf4fa'>"+
					  "<table width='100%'>"+
						s+
					  "</table>"+
					"</div>";
			}

			if ( (e = $('hid.'+s_ref_id       )) ) e.innerHTML = s;
			if ( (e = $('hid.'+s_ref_id+'_sav')) ) e.innerHTML = '';
			if ( (e = $('sho.'+s_ref_id       )) ) e.innerHTML = MicroblogPainter._formatReply(s_widget, s_ref_id, n);
		}
		MicroblogPainter.reattachDvdMenu();
	},

	_paintMsg : function(a) // doMsg
	{
		var b  = {},
			c  = {pos:3},
			e, s_ref_id, s_div;

		if ( (b = MicroblogPainter._fetch(a,c)) )
		{
			s_ref_id = b.user_id + '.' + b.location + '.' + b.blog_id;
			s_div    = 'div.' + b.user_id + '.' + b.location + '.' + b.blog_id + '.' + b.reply_num;
			if ( (e = $(s_div)) )
			{
				e.innerHTML = MicroblogPainter._formatMsg(b, b.mode, b.reply_num ? '' : s_ref_id, 1);
				MicroblogPainter.reattachDvdMenu();
			}
		}
	},

	_formatMsg : function(a, s_widget, s_ref_id, b_hb_only) // getBlogMsg
	{
		b_blog  = s_widget == 'blog';
		s_who   = a.created_uc; if ( a.name && a.name != '-') s_who = a.name+" ("+s_who+")";
		b_reply = Filmaf.userCollection == a.user_id    || (a.is_user_id_friend != '' && a.friend_reply != 'N');
		b_edit  = Filmaf.userCollection == a.created_by && a.post_age <= 1;
		b_del   = Filmaf.userCollection == a.created_by || (Filmaf.userCollection == a.user_id && (s_widget == 'blog' || s_widget == 'wall'));
		b_hide  = a.reply_num > 0;
		s_ref   = a.user_id+'.'+a.location+'.'+a.blog_id+'.'+a.reply_num;

		// --------------------------------------------------------------------------------------------------------------------------------------------------------
		s_header = a.reply_num ? 'replies' : 'says';
		s_header =		"<div class='wg_time' style='margin:0 0 4px 0'>"+
						  (b_del   ? "<img src='http://dv1.us/d1/00/ax00.gif' height='14' width='14' alt='delete' title='Delete' id='del."+s_ref+"' "+
						   				  "onmouseover='Img.mouseOver(event,this,13)' onmouseout='Img.mouseOut(this,13)' "+
										  "onclick='Microblog.del(\""+s_widget+"\",this.id)' style='float:right' />" : '')+
						  (b_reply ? "<img src='http://dv1.us/d1/00/ap00.gif' height='14' width='14' alt='reply' title='Reply' id='rep."+s_ref+"' "+
						   				  "onmouseover='Img.mouseOver(event,this,15)' onmouseout='Img.mouseOut(this,15)' "+
										  "class='ctx2' dynarch_below='div."+s_ref+"' style='float:right' />" : '')+
						  (b_edit  ? "<img src='http://dv1.us/d1/00/ak00.gif' height='14' width='14' alt='edit' title='Edit' id='edi."+s_ref+"' "+
						   				  "onmouseover='Img.mouseOver(event,this,14)' onmouseout='Img.mouseOut(this,14)' "+
										  "class='ctx1' dynarch_below='div."+s_ref+"' style='float:right' />" : '')+
						  "<div id='tim."+s_ref+"' style='float:right;margin:0 2px 0 6px'>"+a.updated_tm+"</div>"+
						  (b_blog  ? "<div>"+s_who+" "+s_header+":</div>" : "<div><a href='http://"+a.created_by+Filmaf.cookieDomain+"/'>"+s_who+"</a> "+s_header+":</div>")+
						"</div>";

		// --------------------------------------------------------------------------------------------------------------------------------------------------------
		s_blog_dvd = '';
		s_blog_tub = '';

		if ( a.obj_type == 'D' && a.obj_id > 0 && a.pic_name != '' )
		{
			s_dvd_id   = '000000'+a.obj_id;
			s_dvd_id   = s_dvd_id.substr(s_dvd_id.length-7,7);
			s_blog_dvd =  "<a id='"+a.media_type+"_"+s_dvd_id+"' class='dvd_pic' href='"+Filmaf.baseDomain+"/search.html?has="+s_dvd_id+"&init_form=str0_has_"+s_dvd_id+"'>"+
							"<img id='zo_"+s_dvd_id+"' onmouseover='ImgPop.show(this,0)' zoom_hoz='left' style='float:left;margin:0px 6px 0 0' src='"+Img.getPicLoc(a.pic_name,0)+"' />"+
							"<input id='attd."+s_ref+"' type='hidden' value='D."+a.obj_id+"'>"+
						  "</a>";
		}

		if ( a.youtube_id != '' )
		{
			s_style    = a.reply_num ? ';padding:4px 0 0 30px' : ';padding-top:4px';
			s_blog_tub =  "<div style='text-align:center"+s_style+";clear:both'>"+
							YouTube.embed(a.youtube_id, 0, 0)+
							"<input id='atty."+s_ref+"' type='hidden' value='Y."+a.youtube_id+"'>"+
						  "</div>";
		}

		s_blog = s_blog_dvd+
				 "<span id='blo."+s_ref+"'>"+a.blog+"</span>"+
				 s_blog_tub;

		// --------------------------------------------------------------------------------------------------------------------------------------------------------
		s_reply = a.reply_num ? '' : "<div id='sho."+s_ref_id+"'>"+MicroblogPainter._formatReply(s_widget,s_ref_id,a.reply_count)+"</div>";
		s_style = a.reply_num ? " style='padding:1px 0 1px 4px'" : '';

		if ( b_hb_only )
			return s_header+s_blog+s_reply;
		else
			return "<tr><td><div class='wg_sepa'>&nbsp;</div></td></tr>"+
				   "<tr><td"+s_style+"><div id='div."+s_ref+"'>"+s_header+s_blog+s_reply+"</div>";
	},

	_formatReply : function(s_widget, s_ref_id, n_replies) // getReply
	{
		if ( n_replies )
			return  "<div class='wg_repl' style='text-align:left;font-size:10px;clear:both'>"+
					  "<a href='javascript:void(Microblog.showReply(\"hid."+s_ref_id+"\"))' style='color:#bd0b0b'>"+
						  n_replies+" repl"+(n_replies > 1 ? 'ies' : 'y')+" ("+
						  "show/hide)"+
					  "</a>"+
					"</div>";
		else
			return '';
	},

	reattachDvdMenu : function()
	{
		var e = window.self.document.getElementsByTagName('a'),
		i;

		for( i = e.length  ; --i >= 0  ;  )
			if ( e[i].className == 'dvd_pic' )
				Context.attach(e[i],false,'context-dvd');

		e = window.self.document.getElementsByTagName('img');

		for( i = e.length  ; --i >= 0  ;  )
		{
			switch ( e[i].className )
			{
			case 'ctx1': Context.attach(e[i],false,'context-blog-edit' ); break;
			case 'ctx2': Context.attach(e[i],false,'context-blog-reply'); break;
			}
		}
	}
};

/* --------------------------------------------------------------------- */

