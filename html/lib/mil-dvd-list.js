/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdList =
{
	setListFormat : function(prop,row,edit)
	{
		if ( row <= 0 ) row = Filmaf.firstRowNo;

		if ( ! prop || prop == Filmaf.presentationMode ) return;

		switch ( prop )
		{
		case 'one':
			break;
		case 'prn':
			row = 1;
			break;
		case 'src':
		default:
			row = Math.floor((row + Filmaf.reqPageSize - 1) / Filmaf.reqPageSize);
			prop = '';
			break;
		}
		if ( row < 1 ) row = 1;

		Cookie.set('pm',prop);

		location.href =	Url.setVal('edit', edit ? '1' : '',
						Url.setVal('pg'  , row > 1 ? row : ''));
	},

	showLargePic : function(s)
	{
		var i, e = $('a_'+s);
		if ( e && (e = Dom.getFirstChildByType(e,'img')) )
			if ( (i = e.src.indexOf(s.substr(1) + '-')) >= 0 )
				if ( (e = e.src.substr(i)) )
					if ( (e = e.substr(0, e.length - 4)) )
						Win.showPic(e);
	},

	picMngt : function(s)
	{
		if ( s )
			Win.openPop(false, 'target_pic', Filmaf.baseDomain + '/utils/x-pic-mngt.html?obj_type=dvd&obj='+s, 0, 0, 1, 1);
		else
			alert('Sorry, we did not find any titles selected.');
	},

	onePagerFlip : function(edit)
	{
		if ( Filmaf.presentationMode == 'one' )
			DvdList.setListFormat('src',Filmaf.contextRow,0);
		else
			DvdList.setListFormat('one',Filmaf.contextRow,edit);
	},

	searchByDvdId : function(s)
	{
		s = encodeURIComponent(s).replace(/(\%20)+/g,'+');  // .replace(/&/g,'&amp;');
		if ( s ) location.href = Filmaf.baseDomain+"/search.html?has="+s+"&init_form=str0_has_"+s;
	},

	folderToMove : function(id)
	{
		switch ( id )
		{
		case 'copy_last':  return Filmaf.lastMoveFolder;
		case 'copy_owned': return 'owned';
		case 'copy_order': return 'on-order';
		case 'copy_wish':  return 'wish-list';
		case 'copy_work':  return 'work';
		case 'copy_seen':  return 'have-seen';
		}
		return 0;
	},

	dvdAction : function(s_form, s_id, s_action, s_where)
	{
		function __dvdListEdit(s)
		{
			if ( s )
				Win.openPop(false, 'targetedit', Filmaf.baseDomain + '/utils/x-dvd-edit.html?dvd=' + s, 0, 0, 1, 1);
			else
				alert('Sorry, we did not find any titles selected.');
		};

		function __dvdListDel(s)
		{
			var f;
			if ( s && (f = $('f_act')) )
			{
				f.act.value = 'del';
				f.sub.value = s;
				f.tar.value = '';
				f.action = location.href;
				f.submit();
			}
		};

		function __dvdListWho(s)
		{
			location.href = Filmaf.baseDomain + '/who.html?dvd=' + s;
		};

		function __dvdListMove(s,d)
		{
			var f;
			if ( s && d && (f = $('f_act')) )
			{
				f.act.value = 'move';
				f.sub.value = s;
				f.tar.value = d;
				f.action = location.href;
				f.submit();
			}
			return true;
		};

		var a, f, i, s = '';

		if ( s_form )
		{
			if ( (f = $(s_form)) )
			{
				a = DvdList.getSelListings(1,f);

				for ( i = 0 ; i < a.length ; i++ ) s += a[i] + ',';
					s = s.substr(0,s.length-1);

				if ( s_action == 'move' )
				{
					if ( ! f.sel_folder ) return;
					if ( ! s_where ) s_where = DropDown.getSelValue(f.sel_folder);
				}
			}
		}
		else
		{
			s = Dec.parse(s_id.replace(/^0+/g,''));
		}

		if ( s )
		{
			switch ( s_action )
			{
			case 'edit':	__dvdListEdit(s);			break;
			case 'delete':	__dvdListDel(s);			break;
			case 'who':		__dvdListWho(s);			break;
			case 'move':	__dvdListMove(s,s_where);	break;
			}
		}
		return true;
	},

	cartAdd : function(child)
	{
		var f = Dom.getParentByType(child,'form'),
			c = Cookie.get('cart'),
			a = DvdList.getSelListings(1,f),
			i, h;
			
		c = c ? ',' + c + ',' : '';

		if ( a.length )
		{
			for ( i = a.length-1 ; i >= 0 ; i-- )
			{
				if ( a[i] )
				{
					h = c.match(new RegExp(','+a[i]+'-[a-z0-9]*,','g'));
					if ( h )
						c = h[0] + c.replace(new RegExp(','+a[i]+'-[a-z0-9]*,','g'), ',').substr(1);
					else
						c = ',' + a[i] + '-' + (c ? c : ',');
				}
			}
			Cookie.set('cart',c);
			Cart.highlight(true);
		}

		return false;
	},

	cartDel : function(child)
	{
		var f = Dom.getParentByType(child,'form'),
		c = Cookie.get('cart'),
		a = DvdList.getSelListings(1,f),
		i, h, b;
		c = c ? ',' + c + ',' : '';

		if ( a.length )
		{
			for ( i = a.length-1 ; i >= 0 ; i-- )
				if (  a[i] )
					c = c.replace(new RegExp(','+a[i]+'-[a-z0-9]*,','g'), ',');
			Cookie.set('cart',c);
			Cart.highlight(true);
		}

		return false;
	},

	dvdSelect : function(f,n)
	{
		var e, i, b, x;
		if ( f && f.elements )
		{
		e = f.elements;
		b = 1;
		for ( i = 0 ; i < e.length ; i++ )
		{
			x = e[i];
			if ( x.type == 'checkbox' && x.id.substr(0,3) == 'cb_' && x.id != 'cb_all' )
			{
			x.checked = n == 2 ? ! x.checked : n != 0;
			if ( ! x.checked ) b = 0;
			}
		}
		if ( (x = $('cb_all')) ) x.checked = b;
		}
	},

	blog : function(n)
	{
		var e = $('n_dvd_blog'), f = $('blogfb_cb'), s, b = false;

		if ( n )
		{
			if ( e )
			{
				if ( (s = Str.trim(e.value)) )
				{
					f = f && f.checked ? '1' : '0';
					s_post = 'version=v1'   +
							 '&blog='       + encodeURIComponent(s) +
							 '&pic_source=D'+
							 '&pic_name=-'  +
							 '&pic_id=0'    +
							 '&obj_id='     + Filmaf.contextStr +
							 '&obj_type=D'  +
							 '&fb='			+ f;

					switch ( n )
					{
					case 1: // just blog and remain on dvd listing
					case 2: // blog and go from dvd listing to homepage
						Ajax.asynch('home', 'DvdList.__blog'+(n == 1 ? '' : 'Home'), '?mode=blog&what=set&user='+Filmaf.userCollection, s_post);
						break;
					case 3: // blogging from one's own homepage
						Ajax.asynch('home', 'MicroblogPainter.__paint', '?mode=blog&what=set&user='+Filmaf.userCollection+'&view='+Filmaf.viewCollection, s_post);
						break;
					}
					b = true;
				}
				else
				{
					alert('Sorry, did not get your message.');
				}
				e.value = '';
			}
		}
		else
		{
			b = true;
		}

		if ( b ) Context.close();
	},

	blogfb : function()
	{
		var e = $('blogfb_cb'), f = $('blogfb_img');
		if ( e && f )
		{
			f.src = 'http://dv1.us/d1/fb'+(e.checked ? '1' : '0')+'.png';
			Button.enable('b_blog_and_home',! e.checked);
		}
	},

	__blog     : function() { DvdList._blogMe(''); },
//	__blogHome : function() { DvdList._blogMe('http://'+Filmaf.userCollection+Filmaf.cookieDomain+'/'); },

	_blogMe : function(loc)
	{
		var o  = {}; if ( ! Ajax.getParms(o) ) return;
		var fb = Ajax.statusInt(o.line1,'fb'),
			b  = {};

		if ( fb && o.length >= 4 && DvdList.parseBlogFb(o.lines[3], b) )
			Facebook.blog(b.obj_id, b.media_type, b.blog);

		if (loc)
			location.href = loc;
		else
			if ( ! fb )
				alert('Posted!');
	},

	parseBlogFb : function(s, t)
	{
		var i, u, v;
		s = s.split('\t');

		t.location = '';
		for ( i = 0 ; i < s.length ; i += 2 )
		{
			u = s[i+1];
			v = Dom.decodeHtmlForInput(u);
			switch ( s[i] )
			{
			case 'user_id':		t.user_id    = u; break;
			case 'location':	t.location   = u; break;
//			case 'name':		t.media_type = "23BR".indexOf(u) >= 0 ? 'b' : ("EFLNS".indexOf(u) >= 0 ? 'f' : 'd'); break;
			case 'media_type':	t.media_type = u; break;
			case 'blog_id':		t.blog_id    = Dec.parse(u); break;
			case 'reply_num':	t.reply_num  = Dec.parse(u); break;
			case 'obj_id':		t.obj_id     = Dec.parse(u); break;
			case 'blog':		t.blog       = u; break;
			}
		}
		return t.location == 'B';
	},

	__dvdPicSelect : function()
	{
		function parse(s, t)
		{
			var i, u, v;
			s = s.split('\t');

			t.images = '';
			for ( i = 0 ; i < s.length ; i += 2 )
			{
				u = s[i+1];
				v = Dom.decodeHtmlForInput(u);
				switch ( s[i] )
				{
				case 'dvd_id': t.dvd_id  = Dec.parse(u); break;
				case 'images': t.images  = u; break;
				}
			}
			return t.images != '';
		};

		var o  = {}; if ( ! Ajax.getParms(o) ) return;
		var	x = DvdListMenuPrep.dvdImgSel,
			b = {},
			s = '',
			a, e, i; 

		if ( o.length >= 3 && parse(o.lines[2], b) && (e = $('cm_dvd_spi_div')) && b.dvd_id == x )
		{
			a = b.images.split(',');
			for ( i = 0 ; i < a.length ; i++ )
			{
				if ( i && i % 5 == 0 ) s += '</div><div>';
				b = a[i];
				s += "<a href='javascript:void(DvdList.dvdPicSelected("+x+",\""+b+"\"))' style='padding:10px 4px 10px 4px'>"+
					   "<img src='"+Img.getPicLoc(b,1)+"' style='padding:4px;border: 1px solid #b1d3ec' />"+
					 "</a>";
			}
			e.innerHTML = "<div>Please click on one of the pictures below for it to be displayed in your collection.</div>"+
						  "<div style='padding:4px 0 4px 0'>"+s+"</div>"+
						  "<div style='float:right;margin-bottom:4px'><input type='button' value='Use FilmAf&#34;s default' onclick='DvdList.dvdPicSelected("+x+",0)' /></div>";
		}
	},

	dvdPicSelected : function(dvd, pic)
	{
		Ajax.asynch('pics', 'DvdList.__dvdPicSelected', '?what=selcollectionpic&dvd='+dvd+'&pic='+pic);
	},

	__dvdPicSelected : function()
	{
		function parse(s, t)
		{
			var i, u, v;
				s = s.split('\t');

			t.pic_name = '';
			for ( i = 0 ; i < s.length ; i += 2 )
			{
				u = s[i+1];
				v = Dom.decodeHtmlForInput(u);
				switch ( s[i] )
				{
				case 'dvd_id':   t.dvd_id   = Dec.parse(u); break;
				case 'pic_name': t.pic_name = u; break;
				}
			}
			return t.pic_name != '';
		};

		var o = {}; if ( ! Ajax.getParms(o) ) return;
		var b = {}, e;

		if ( o.length >= 3 && parse(o.lines[2], b) )
		{
			e = '000000' + b.dvd_id;
			if ( (e = $('zo_'+e.substr(e.length - 7,7))) )
			{
				e.src = /jpg$/.test(e.src) ? Img.getPicLoc(b.pic_name,0) : Img.getPicLoc(b.pic_name,1);
				Context.close();
				ImgPop.close(0);
			}
		}
	},

	getSelListings : function(b_warn_on_none, f)
	{
		var x = [], i, k, e;

		if ( f && f.elements )
		{
			f = f.elements;
			for ( i = 0, k = 0 ; i < f.length ; i++ )
			{
				e = f[i].id;
				if ( e.substr(0,3) == 'cb_' && e != 'cb_all' )
				if ( f[i].checked ) x[k++] = Dec.parse(e.substr(3).replace(/^0+/g,''));
			}

			if ( k )
				return x;
			else
				if ( b_warn_on_none )
			alert('Sorry, there are no listings selected. In order\nto select a listing click on the check box next to it.');
		}
		return false;
	},

	isAnyAllSelected : function(f)
	{
		var e, i, n = 1, a = 1;

		if ( f && f.elements )
		{
			f = f.elements;
			for ( i = 0 ; i < f.length ; i++ )
			{
				e = f[i].id;
				if ( e.substr(0,3) == 'cb_' && e != 'cb_all' )
				if ( f[i].checked )
					n = 0;
				else
					a = 0;
			}
		}
		return n ? 0 : (a ? 2 : 1); // 0: none, 1: some, 2: all
	},

	check : function(cb)
	{
		if ( cb && cb.form && cb.form.cb_all )
			cb.form.cb_all.checked = false;
		return false;
	},

	uncheckAll : function(child)
	{
		var f = Dom.getParentByType(child,'form'), e, i, x;
		if ( f && f.elements )
		{
			e = f.elements;
			for ( i = 0 ; i < e.length ; i++ )
			{
				x = e[i];
				if ( x.type == 'checkbox' && x.id.substr(0,3) == 'cb_' && x.id != 'cb_longtitles' ) x.checked = false;
			}
		}
		return false;
	},

	checkAll : function(f)
	{
		var e, c, i, x;
		if ( f && f.elements )
		{
			e = f.elements;
			c = f.cb_all ? f.cb_all.checked : true;
			for ( i = 0 ; i < e.length ; i++ )
			{
				x = e[i];
				if ( x.type == 'checkbox' && x.id.substr(0,3) == 'cb_' && x.id != 'cb_all' && x.id != 'cb_longtitles' ) x.checked = c;
			}
		}
		return false;
	},

	isDvdInCart : function(i)
	{
		if ( typeof(i) == 'string' )
		i = Dec.parse(i);

		if ( i )
		{
			var r = new RegExp(','+i+'-[0-9]*,'),
				c = ',' + Cookie.get('cart') + ','; // '47751-0,108568-'

			return r.test(c);
		}
		return false;
	},

	setLongTitles : function(x)
	{
		Cookie.set('longtitles', x ? 'Y' : '');

		var g = window.self.document.getElementsByTagName('div'), i, e, f, s;

		for( i = g.length  ; --i >= 0  ;  )
		{
			e = g[i];
			if ( e.id && e.id.substr(0,3) == 'tf_' )
			{
				if( (f = $(e.id+'_sav')) )
				{
					if ( (e.innerHTML.length > f.innerHTML.length) == (! x ? true : false) )
					{
						s = f.innerHTML;
						f.innerHTML = e.innerHTML;
						e.innerHTML = s;
					}
				}
			}
		}
	},

	synchLongTitles : function()
	{
		var e = $('cb_longtitles');
		if ( e )
			e.checked = Cookie.get('longtitles') == 'Y';
	},

	selectIfOne : function()
	{
		if ( Filmaf.rowsShown == 1 )
		{
			var f = $('f_list'),
				e = $('cb_all');
			if ( f && e )
			{
				e.checked = true;
				DvdList.checkAll(f);
			}
		}
	}
};

/* --------------------------------------------------------------------- */

