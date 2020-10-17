/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Main =
{
	_tabMed : '',
	_tabReg : '',
	_tabCol : '',
	_tabCat : '',
	_tabSub : 0,
	_selGen : 0,
	_cookie : '',

	setup : function(parms)
	{
		Main._cookie = parms.cookie;
		Main.attachZoom();
		Main.attachTabs(parms);
	},

	load : function(tbl,opt,parm)
	{
		// do nothing on the initial load, otherwise set cookies and reload page
		if ( parm != 1 )
		{
			switch ( tbl )
			{
			case 'med': Main._tabMed = opt; break;
			case 'reg': Main._tabReg = opt; break;
			case 'col': Main._tabCol = opt; break;
			case 'cat': Main._tabCat = opt; break;
			case 'sbg': Main._tabSub = opt; break;
			}

			Cookie.set(Main._cookie, Main._tabMed+'|'+Main._tabReg+'|'+Main._tabCol+'|'+Main._tabCat+'|'+Main._tabSub, 0);

			location.href = location.href;
		}
	},

	attachTabs : function(parms)
	{
		if ( parms.med )
		{
			Main._tabMed = parms.med;
			Main._tabReg = parms.reg;
			Main._tabCol = parms.col;
			Main._tabCat = parms.cat;
			Main._tabSub = parms.sbg;
			Main._selGen = parms.gen;

			TblMenu.attach('tab_med',function(id,parm){Main.load('med',id.substr(5),parm)});
			TblMenu.set($('stat_'+Main._tabMed),1);
			TblMenu.attach('tab_reg',function(id,parm){Main.load('reg',id.substr(5),parm)});
			TblMenu.set($('stat_'+Main._tabReg),1);
			TblMenu.attach('tab_col',function(id,parm){Main.load('col',id.substr(5),parm)});
			TblMenu.set($('stat_'+Main._tabCol),1);
			TblMenu.attach('tab_cat',function(id,parm){Main.load('cat',id.substr(5),parm)});
			TblMenu.set($('stat_'+Main._tabCat),1);

			if ( $('tab_sbg') )
			{
				TblMenu.attach('tab_sbg',function(id,parm){Main.load('sbg',id.substr(4),parm)});
				TblMenu.set($('gno_'+(Main._tabSub ? Main._tabSub : Main._selGen)),1);
			}
		}
	},

	attachZoom : function()
	{
		var e = window.self.document.getElementsByTagName('img'), i, x;

		for ( i = e.length  ; --i >= 0  ;  )
			if ( e[i].id && e[i].id.substr(0,3) == 'zo_' )
				e[i].onmouseover = function(ev){ImgPop.show(this,0);};
	},

	onPopup : function(el)
	{
		if ( ! this.id ) return;
		var i = this.menu.items,
			t = this.menu.target,
			g = ! (Filmaf.userCollection != ''), // true if guest
			z = this,
			b;

		switch ( this.id )
		{
		case 'context_dvd': Main._contextDvd(i,t,g,el); break;
		case 'context_dir': Main._contextDir(i,t,g); break;

		default:
			z.filmaf = SearchMenuPrep.onPopup;
			z.filmaf(el);
			break;
		}
	},

	_contextDvd : function(i,t,g,el)
	{
		Filmaf.contextStr = t.id.substr(2);
		Filmaf.contextMed = t.id.substr(0,1);

		if ( ! i.cm_dvd_large.visible )
			i.cm_dvd_large.display(true);
	
		ImgPop.setMenuText(i.cm_dvd_large.labelTD);

		i.cm_dvd_blog.disable(g);
		i.cm_dvd_copy.disable(g);
		i.cm_dvd_delete.disable(g);
		i.cm_dvd_edit.disable(g);
		i.cm_dvd_pic.disable(g);

		b = Cart.has(Filmaf.contextStr);
		i.cm_dvd_cart_add.disable(b);
		i.cm_dvd_cart_del.disable(!b);

		Facebook.ulPre(i, Filmaf.contextMed, Filmaf.contextStr);
	},

	_contextDir : function(i,t,g)
	{
		var s = t.href,
			n = s.indexOf('/gd/');

		Filmaf.contextStr = decodeURIComponent(s.substr(n+4));
		i.cm_dir_title.labelTD.innerHTML = "<span style='position:relative;left:-16px'>Search &quot;<strong>" + t.innerHTML + '</strong>&quot; in:</span>';
		i.cm_dir_mine.disable(g);
		var b = Search.isPinned();
		if ( b != i.cm_dir_pin1.visible	) i.cm_dir_pin1.display(b);
		if ( b != i.cm_dir_pin2.visible	) i.cm_dir_pin2.display(b);
	},

	onClick : function(action)
	{
		if ( ! action.info || ! action.info.id ) return;
		var id = action.info.id,
			md = id.substr(0,7),
			z  = this;
		
		if ( !(id = id.substr(7)) ) retutn;

		switch ( md )
		{
		case 'cm_dvd_':
			Main._clickDvd(id);
			break;
		case 'cm_dir_':
			Main._clickText(id, md.substr(3,3));
			break;
		default:
			z.filmaf = SearchMenuAction.onClick;
			z.filmaf(action);
			break;
		}
	},

	_clickDvd : function(id)
	{
		switch ( id )
		{
		case 'large':
			switch ( ImgPop.menuContext )
			{
			case 'show':		 ImgPop.show($('zo_'+Filmaf.contextStr), 1); break;
			case 'stop showing': ImgPop.close(1); break;
			}
			break;
		case 'list':		DvdList.searchByDvdId(Filmaf.contextStr);			break;	
		case 'blog':															break;
		case 'cart_add':	Cart.click (Filmaf.contextStr, true);				break;
		case 'cart_del':	Cart.click (Filmaf.contextStr, false);				break;
		case 'prices':		Price.click(Filmaf.contextStr, 0);					break;
		case 'pic':			DvdList.picMngt(Filmaf.contextStr);					break;
		case 'edit':		DvdList.dvdAction('',Filmaf.contextStr,id,0);		break;
		case 'delete':		Main._dvdAction(Filmaf.contextStr,id,0);			break;
		case 'fb_see':		Facebook.see  (Filmaf.contextStr,Filmaf.contextMed);break;
		case 'fb_want':		Facebook.want (Filmaf.contextStr,Filmaf.contextMed);break;
		case 'fb_order':	Facebook.order(Filmaf.contextStr,Filmaf.contextMed);break;
		case 'fb_got':		Facebook.get  (Filmaf.contextStr,Filmaf.contextMed);break;
		default:
			if ( (id = DvdList.folderToMove(id)) )
				Main._dvdAction(Filmaf.contextStr, 'move', id);
			break;
		}
	},

	_clickText : function(ctx, opt)
	{
		if ( ! Filmaf.contextStr ) return;

		switch ( ctx )
		{
		case 'xp':	DirXp.open(Filmaf.contextStr); break;
		default:	Search.setSearch(ctx, opt, Filmaf.contextStr); break;
		}
	},

	_dvdAction : function(s_id,s_action,s_where)
	{
		// prevent popups until collection has been updated
		ImgPop.skipShowOnWait = true;
		switch (s_action)
		{
		case 'delete':
			Ajax.asynch('dvd-action', 'Main.__dvdActioned', '?dvd='+s_id+'&action=del', 0, 10);
			break;
		case 'move':
			Ajax.asynch('dvd-action', 'Main.__dvdActioned', '?dvd='+s_id+'&action=mov&folder='+s_where, 0, 10);
			break;
		}
		ImgPop.close(0);
	},

	__dvdActioned : function()
	{
		if ( Ajax.ready() )
		{
			var o = {};

			if ( Ajax.getParms(o) )
				alert(o.msg.replace('.<br />','').replace('1 title','Title').replace('inserted','Added').replace('updated','Moved').replace('delete','Delete'));
			else
				alert('Oops. The update failed.');

			ImgPop.skipShowOnWait = false;
		}
	}
};

/* --------------------------------------------------------------------- */

