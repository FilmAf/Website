/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulHome(s)
{
	ulProfile(s);
	ulMicroBlog(s);
	ulFriends(s);
};

var Home =
{
	_version : 1,

	onPopup : function(el)
	{
		if ( ! this.id ) return;

		var i = this.menu.items,
			t = this.menu.target,
			z = this;

		switch ( this.id )
		{
		case 'home_profile':		Profile.load();										break;
		case 'home_blog':
		case 'home_wall':			this.doNotOpen = Microblog.onPostingPopup(this.id);	break;
		case 'home_invite':			FriendInvite.onPopup(this.id);						break;
		case 'home_divorce':		Friends.divorceLoad();								break;
		case 'context_dvd':			Home._contextDvd(t,i);								break;
		case 'context_blog_edit':
		case 'context_blog_reply':	Microblog.onContextPopup(t.id);						break;

		default:
			z.filmaf = SearchMenuPrep.onPopup;
			z.filmaf(el);
			break;
		}
	},

	_contextDvd : function(t,i)
	{
		var g = Filmaf.userCollection == '',
			b = DvdList.isDvdInCart(Filmaf.contextStr),
			e, f, u, a, c;

		Filmaf.contextStr = t.id.substr(2);
		Filmaf.contextMed = t.id.substr(0,1);

		ImgPop.setMenuText(i.cm_dvd_large.labelTD);

		i.cm_dvd_blog.disable(g);
//		i.cm_dvd_cart_add.disable(b);
//		i.cm_dvd_cart_del.disable(!b);
//		i.cm_dvd_edit.disable(g);
//		i.cm_dvd_pic.disable(g);

		Facebook.ulPre(i, Filmaf.contextMed, Filmaf.contextStr);
	},

	setup : function()
	{
		Img.attach();
		Context.attach('edit_profile'  ,false,'menu-home-profile');
		Context.attach('edit_photo'    ,false,'menu-home-profile-pic');
		Context.attach('post_blog'     ,false,'menu-home-blog');
		Context.attach('post_wall'     ,false,'menu-home-wall');
		DateTime.attachLiveParser('n_dob');

		Home.getCookies();
		if ( $('stat_menu') )
		{
			TblMenu.attach('stat_menu',function(id,parm){DvdStats.load('stat_target',id.substr(5),parm)});
			TblMenu.set($(DvdStats._statsShow ? 'stat_' + DvdStats._statsShow : 'stat_folder'),1);
		}
		if ( $('fvid_menu') )
		{
			TblMenu.attach('fvid_menu',function(id,parm){FavVideos.load('fvid_target',id.substr(5),parm)});
			TblMenu.set($('fvid_all'),-1);
			Jump.attach('fvid_jump',FavVideos.jump);
		}
		if ( $('friend_by_name') )
			Friends.attachByName();
		else
			Context.attach('friend_invite',false,'menu-home-invite');
	},

	getCookies : function()
	{
		var s = Cookie.get('home');
		s = s.split('|');
		if ( s.length >= 1 && Home._version == s[0] )
		{
			if ( s.length >= 2 ) DvdStats._statsShow		= s[1];
			if ( s.length >= 3 ) DvdStats._statsGroup		= s[2];
			if ( s.length >= 4 ) DvdStats._statsList		= s[3];
			if ( s.length >= 5 ) DvdStats._statsPaid		= s[4];
			if ( s.length >= 6 ) FriendInvite.showRejected	= s[5];
			if ( s.length >= 7 ) FavVideos._last_view		= s[6];
			if ( s.length >= 8 ) FavVideos._last_cat_id		= s[7];
		}
	},

	setCookies : function()
	{
		var a = Cookie.get('home'),
			b = Home._version
				+'|'+DvdStats._statsShow
				+'|'+DvdStats._statsGroup
				+'|'+DvdStats._statsList
				+'|'+DvdStats._statsPaid
				+'|'+FriendInvite.showRejected // also accessed from CHome.php
				+'|'+FavVideos._last_view
				+'|'+FavVideos._last_cat_id;
		if ( a != b ) Cookie.set('home', b);
	}
};

/* --------------------------------------------------------------------- */

