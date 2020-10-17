/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdListMenuPrep =
{
	dvdImgSel : 0,

	onPopup : function(el) // onMenuPopup
	{
		if ( ! this.id ) return;
		var i = this.menu.items,
			t = this.menu.target,
			u = !(Filmaf.userCollection != ''), // false if viewing collection
			v = !(Filmaf.viewCollection != '' && Filmaf.viewCollection != Filmaf.userCollection), // false if viewing collections that is not the user's
			z = this;

		switch ( this.id )
		{
		case 'context_dvd':			DvdListMenuPrep._contextDvd(i,t,u,v,el);			break;
		case 'context_dvd_img':		DvdListMenuPrep._contextDvdImg(Filmaf.contextStr);	break;
		case 'context_dvd_copy':	DvdListMenuPrep._contextDvdCopy(i);					break;
		case 'context_sel':			DvdListMenuPrep._contextSel(i,t);					break;
		case 'context_sel_copy':	DvdListMenuPrep._contextSelCopy(i);					break;
		case 'context-float': break;
		case 'context_gen':			DvdListMenuPrep._contextGen(i,t,u,v);				break;
		case 'context_imdb':		DvdListMenuPrep._contextImdb(i,t,u,v);				break;
		case 'context_dir':			DvdListMenuPrep._contextDir(i,t,u,v);				break;
		case 'context_pub':			DvdListMenuPrep._contextPub(i,t,u,v);				break;

		case 'jump_page':			Jump.attachSpin(t,null);							break;
		case 'page_size':			PageSize.attachSpin(t);								break;
		case 'explain_pop':			Explain.show(t.id,true);							break;

		default:
			z.filmaf = SearchMenuPrep.onPopup;
			z.filmaf(el);
			break;
		}
	},

	_contextDvd : function(i,t,u,v,el)
	{
		var b = Dom.getParentByType(t,'form'),
			g, h;

		Filmaf.contextForm	= b ? b.name : '';
		Filmaf.contextStr	= t.id.substr(2);
		Filmaf.contextMed	= t.id.substr(0,1);
		Filmaf.contextRow	= 0;
		t					= Dom.getParentByType(t,'tr');

		if ( t && t.id && t.id.substr(0,2) == 'r_' )
		{
			t = Dec.parse(t.id.substr(2));
			if ( t > 0 ) Filmaf.contextRow = t;
		}

		b = Img.isChecked($('ic_' + Filmaf.contextStr), 1);
		g = Filmaf.userCollection == '';
		h = location.href.indexOf('/search.html?') > 0;

		i.cm_dvd_title.labelTD.innerHTML = "<span style='position:relative;left:-16px'>Action on DVD <strong>" + Filmaf.contextStr + '</strong></span>';
		i.cm_dvd_one.labelTD.innerHTML = Filmaf.presentationMode == 'one' ? "&quot;Standard Listing&quot; mode" : "&quot;One Pager&quot; mode";
		i.cm_dvd_one.disable(typeof(DvdPricePrep) != 'undefined');

		if ( i.cm_dvd_copy   ) i.cm_dvd_copy.disable(g);
		if ( i.cm_dvd_delete ) i.cm_dvd_delete.disable(g);
		i.cm_dvd_cart_add.disable(b);
		i.cm_dvd_cart_del.disable(!b);
		if ( i.cm_dvd_edit   ) i.cm_dvd_edit.disable(g);
		if ( i.cm_dvd_pic    ) i.cm_dvd_pic.disable(g);
		if ( i.cm_dvd_mine   ) i.cm_dvd_mine.disable(g || h);
		if ( i.cm_dvd_spi    ) i.cm_dvd_spi.disable(u || ! v || h || el.getAttribute('imgsel') != '1');
		i.cm_dvd_blog.disable(g);

		if ( $('div_zoom') )
		{
			ImgPop.setMenuText(i.cm_dvd_large.labelTD);
		}
		else
		{
			i.cm_dvd_large.labelTD.innerHTML = 'Show Large Pic';
			ImgPop.menuContext = 'show large';
		}

		Facebook.ulPre(i, Filmaf.contextMed, Filmaf.contextStr);
	},

	_contextDvdImg : function(i)
	{
		var e;
		i = Dec.parse(i);

		if ( DvdListMenuPrep.dvdImgSel != i && (e = $('cm_dvd_spi_div')) )
		{
			DvdListMenuPrep.dvdImgSel = i;
			e.innerHTML		= "<div>Retrieving Options...</div>"+
							  "<div><img src='http://dv1.us/d1/wait.gif' /></div>";
			Ajax.asynch('pics', 'DvdList.__dvdPicSelect', '?what=listpics&dvd='+i);
		}
	},

	_contextDvdCopy : function(i)
	{
		var b = Filmaf.lastMoveFolder && Filmaf.lastMoveFolder.indexOf('/') > 0;

		i.cm_dvd_copy_last.labelTD.innerHTML = b ? Filmaf.lastMoveFolder : '&lt;no previous subfolder&gt;';
		i.cm_dvd_copy_last.disable(!b);
	},

	_contextSel : function(i,t)
	{
		var b = Dom.getParentByType(t,'form'),
			s = DvdList.isAnyAllSelected(b),
			g = Filmaf.userCollection == '',
			h = location.href.indexOf('/search.html?') > 0;

		i.cm_sel_who.disable(!s);
		i.cm_sel_copy.disable(g || !s);
		i.cm_sel_delete.disable(g || !s);
		i.cm_sel_cart_add.disable(!s);
		i.cm_sel_cart_del.disable(!s);
		i.cm_sel_prices.disable(!s || typeof(DvdPricePrep) != 'undefined');
		if ( i.cm_sel_edit ) i.cm_sel_edit.disable(g || !s);
		if ( i.cm_sel_mine ) i.cm_sel_mine.disable(g || !s || h);
		i.cm_sel_inv.disable(s != 1);
		i.cm_sel_all.disable(s == 2);
		i.cm_sel_none.disable(s == 0);
	},

	_contextSelCopy : function(i)
	{
		var b = Filmaf.lastMoveFolder && Filmaf.lastMoveFolder.indexOf('/') > 0;

		if ( b ) i.cm_sel_copy_last.labelTD.innerHTML = Filmaf.lastMoveFolder;
		i.cm_sel_copy_last.display(b);
		i.cm_sel_copy_last_.display(b);
	},

	_contextGen : function(i,t,u,v)
	{
		Filmaf.contextStr = Url.getVal('genre', t.href);

		var s_genre = t.innerHTML,
			n_slash = s_genre.indexOf(' / '),
			b;

		i.cm_gen0_title.labelTD.innerHTML = "<span style='position:relative;left:-16px'>Search &quot;<strong>" + s_genre + '</strong>&quot; in:</span>';

		if ( n_slash >= 0 )
		{
			s_genre = s_genre.substr(0,n_slash);
			i.cm_gen1_title.labelTD.innerHTML = "<span style='position:relative;left:-16px'>Search &quot;<strong>" + s_genre + '</strong>&quot; in:</span>';
			if ( !i.cm_gen1_title.visible	) i.cm_gen1_title.display(true);
			if ( !i.cm_gen1_sep1.visible	) i.cm_gen1_sep1.display(true);
			if ( !i.cm_gen1_filmaf.visible	) i.cm_gen1_filmaf.display(true);
			if ( !i.cm_gen1_mine.visible	) i.cm_gen1_mine.display(true);
			if ( !i.cm_gen1_this.visible	) i.cm_gen1_this.display(true);
			if ( !i.cm_gen1_sep2.visible	) i.cm_gen1_sep2.display(true);
		}
		else
		{
			if ( i.cm_gen1_title.visible	) i.cm_gen1_title.display(false);
			if ( i.cm_gen1_sep1.visible		) i.cm_gen1_sep1.display(false);
			if ( i.cm_gen1_filmaf.visible	) i.cm_gen1_filmaf.display(false);
			if ( i.cm_gen1_mine.visible		) i.cm_gen1_mine.display(false);
			if ( i.cm_gen1_this.visible		) i.cm_gen1_this.display(false);
			if ( i.cm_gen1_sep2.visible		) i.cm_gen1_sep2.display(false);
		}
		i.cm_gen2_title.labelTD.innerHTML = "<span style='position:relative;left:-16px'>Search &quot;<strong>" + s_genre + ' + Subgenres</strong>&quot; in:</span>';
		if ( i.cm_gen0_mine.visible ) i.cm_gen0_mine.disable(u);
		if ( i.cm_gen0_this.visible ) i.cm_gen0_this.disable(v);
		if ( i.cm_gen1_mine.visible ) i.cm_gen1_mine.disable(u);
		if ( i.cm_gen1_this.visible ) i.cm_gen1_this.disable(v);
		if ( i.cm_gen2_mine.visible ) i.cm_gen2_mine.disable(u);
		if ( i.cm_gen2_this.visible ) i.cm_gen2_this.disable(v);
		b = Search.isPinned();
		if ( b != i.cm_gen_pin1.visible	) i.cm_gen_pin1.display(b);
		if ( b != i.cm_gen_pin2.visible	) i.cm_gen_pin2.display(b);
	},

	_contextImdb : function(i,t,u,v)
	{
		Filmaf.contextStr = Url.getVal('vd', t.href).substr(3);
		i.cm_imdb_mine.disable(u);
		i.cm_imdb_this.disable(v);
		var b = Search.isPinned();
		if ( b != i.cm_imdb_pin1.visible	) i.cm_imdb_pin1.display(b);
		if ( b != i.cm_imdb_pin2.visible	) i.cm_imdb_pin2.display(b);
	},

	_contextDir : function(i,t,u,v)
	{
		var s = t.href,
			n = s.indexOf('/gd/');

		Filmaf.contextStr = decodeURIComponent(s.substr(n+4));
		i.cm_dir_title.labelTD.innerHTML = "<span style='position:relative;left:-16px'>Search &quot;<strong>" + t.innerHTML + '</strong>&quot; in:</span>';
		i.cm_dir_mine.disable(u);
		i.cm_dir_this.disable(v);
		var b = Search.isPinned();
		if ( b != i.cm_dir_pin1.visible	) i.cm_dir_pin1.display(b);
		if ( b != i.cm_dir_pin2.visible	) i.cm_dir_pin2.display(b);
	},

	_contextPub : function(i,t,u,v)
	{
		var s = t.href,
			n = s.indexOf('pub=');

		Filmaf.contextStr = decodeURIComponent(s.substr(n+4));
		i.cm_pub_title.labelTD.innerHTML = "<span style='position:relative;left:-16px'>Search &quot;<strong>" + t.innerHTML + '</strong>&quot; in:</span>';
		i.cm_pub_mine.disable(u);
		i.cm_pub_this.disable(v);
		var b = Search.isPinned();
		if ( b != i.cm_pub_pin1.visible	) i.cm_pub_pin1.display(b);
		if ( b != i.cm_pub_pin2.visible	) i.cm_pub_pin2.display(b);
	}
};

/* --------------------------------------------------------------------- */

