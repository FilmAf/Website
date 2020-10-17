/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var FavVideos =
{
	_last_view	: '',
	_last_cat_id: -1,
	_cat_id		: -1,
	_page		:  1,

	load : function(s_target, s_category, n_parm)
	{
		var e;
		switch ( n_parm )
		{
		case -1:
			// initializing (pick up the last category for this view if it was the last otehrwise use the default category (all))
			FavVideos._cat_id = -1;
			if ( FavVideos._last_view == Filmaf.viewCollection && FavVideos._last_cat_id != -1 )
			{
				if ( (e = $('fvid_'+FavVideos._last_cat_id)) )
				{
					TblMenu.set(e,-3);
					FavVideos._cat_id = FavVideos._last_cat_id;
				}
			}
			FavVideos._page   = 1;
			break;
		case -2:
			// selection from side menu (update the new category)
			FavVideos._cat_id = s_category == 'all' ? -1 : Dec.parse(s_category);
			FavVideos._page   = 1;
			break;
		case -3:
			// called from -1 above, avaoid recursion
			return;
		default:
			FavVideos._page   = n_parm;
			if ( FavVideos._page < 1 )
				FavVideos._page = 1;
			break;
		}

		if ( FavVideos._cat_id < -1 )
			FavVideos._cat_id = -1;

		if ( FavVideos._last_view != Filmaf.viewCollection || FavVideos._last_cat_id != FavVideos._cat_id )
		{
			FavVideos._last_view   = Filmaf.viewCollection;
			FavVideos._last_cat_id = FavVideos._cat_id;
			Home.setCookies();
		}

		Ajax.asynch('home',
			'FavVideosPainter.__paint',
			'?mode=favvideos&what=get'+'&view='+Filmaf.viewCollection+'&cat_id='+FavVideos._cat_id+'&page='+FavVideos._page+'&target='+s_target);
	},

	testTub : function(s)
	{
		if ( (s = Microblog.validateYouTube(s)) )
			YouTube.openPop(s);
	},

	blog : function()
	{
		var e, n_cat_id	 = DropDown.getSelValue('n_fvid_category'),
			s_youtube_id = '',
			n_blog_id	 = 0;

		if ( (s_youtube_id = Microblog.validateYouTube('n_fvid_tub')) )
		{
			Ajax.asynch('home',
				'FavVideosPainter.__paint',
				'?mode=favvideos&what=set&user='+Filmaf.viewCollection+'&view='+Filmaf.viewCollection+'&cat_id='+n_cat_id+'&page=1&target=fvid_target',
				'version=v1&blog_id='+n_blog_id+'&youtube_id='+encodeURIComponent(s_youtube_id));
			if ( (e = $('n_fvid_tub')) ) e.value = '';
		}
	},

	del : function()
	{
		var s, s_user, n_blog_id, n_page, n_cat_id;
		
		if ( (s = $('fvid_id')) && (s = s.value.split('.')).length >= 4 && confirm("Are you sure you want to delete this videp?\n\nOK=Yes - Cancel=No") )
		{
			// user.location.blog_id.reply_num
			s_user		= s[0];
			n_blog_id	= Dec.parse(s[2]);
			n_page		= $('fvid_page').value;
			n_cat_id	= $('fvid_cat').value;

			Ajax.asynch('home',
				'FavVideosPainter.__paint',
				'?mode=favvideos&what=del&user='+s_user+'&view='+s_user+'&cat_id='+n_cat_id+'&page='+n_page+'&target=fvid_target',
				'version=v1&blog_id='+n_blog_id);
		}
	},

	jump : function (mode, page)
	{
		switch ( mode )
		{
		case 'cur':	return Dec.parse($('fvid_page').value);
		case 'max': return Dec.parse($('fvid_total').value);
		case 'page':
			if ( page > 0)
			{
				var m = Dec.parse($('fvid_total').value);
				if ( page > m ) page = m;
				FavVideos.load('fvid_target', 0, page);
			}
			break;
		}
		return 0;
	},

	getPage : function(i)
	{
		if ( i )
		{
			var e, c = (e = $('fvid_cat' )) ? Edit.getInt(e) : -1;

			if ( FavVideos._cat_id == c )
			{
				var j = ((e = $('fvid_page')) ? Edit.getInt(e) : 1),
					z = ((e = $('fvid_last')) ? Edit.getInt(e) : 0);
				
				i += j;
				if ( i <= 0 )
				{
					alert('You are already at the first page.');
					i = -1;
				}
				else
				{
					if ( z && i >= j )
					{
						alert('You are already at the last page.');
						i = -1;
					}
				}
			}
			else
			{
				i = 0;
			}
		}

		if ( i >= 0 )
			FavVideos.load('fvid_target', 0, i);
	},

	newer : function() { FavVideos.getPage(-1); },
	curr  : function() { FavVideos.getPage( 0); },
	older : function() { FavVideos.getPage( 1); }
};


var FavVideosCat =
{
	resort : function(e)
	{
		var a, b, e, n_del, o_cat_id, o_cat_name, n_cat_name, o_sort, x_sort;

		a = Dec.parse(e.id.substr(7));
		b = Dec.parse(DropDown.getSelValue(e));
		if ( a != b )
		{
			if ( (e = $('n_del_'		+a)) ) n_del		= e.checked;
			if ( (e = $('o_cat_id_'		+a)) ) o_cat_id		= e.value;
			if ( (e = $('o_cat_name_'	+a)) ) o_cat_name	= e.value;
			if ( (e = $('n_cat_name_'	+a)) ) n_cat_name	= e.value;
			if ( (e = $('o_sort_'		+a)) ) o_sort		= e.value;
			if ( (e = $('x_sort_'		+a)) ) x_sort		= e.value;

			if ( b > a )
				for ( i = a ; i < b ; i++ ) FavVideosCat._moveFolder(i, i+1);
			else
				for ( i = a ; i > b ; i-- ) FavVideosCat._moveFolder(i, i-1);

			if ( (e = $('n_del_'		+b)) ) e.checked	= n_del;
			if ( (e = $('o_cat_id_'		+b)) ) e.value		= o_cat_id;
			if ( (e = $('o_cat_name_'	+b)) ) e.value		= o_cat_name;
			if ( (e = $('n_cat_name_'	+b)) ) e.value		= n_cat_name;
			if ( (e = $('o_sort_'		+b)) ) e.value		= o_sort;
			if ( (e = $('x_sort_'		+b)) ) e.value		= x_sort;

			DropDown.selectFromVal('n_sort_' + a, a);
		}
		return false;
	},

	_moveFolder : function(a, b)
	{
		var e, f;

		if ( (e = $('o_cat_id_'+a)) && (f = $('o_cat_id_'+b)) )
		{
			if ( (e = $('n_del_'		+a)) && (f = $('n_del_'			+b)) ) e.checked = f.checked;
			if ( (e = $('o_cat_id_'		+a)) && (f = $('o_cat_id_'		+b)) ) e.value = f.value;
			if ( (e = $('o_cat_name_'	+a)) && (f = $('o_cat_name_'	+b)) ) e.value = f.value;
			if ( (e = $('n_cat_name_'	+a)) && (f = $('n_cat_name_'	+b)) ) e.value = f.value;
			if ( (e = $('o_sort_'		+a)) && (f = $('o_sort_'		+b)) ) e.value = f.value;
			if ( (e = $('x_sort_'		+a)) && (f = $('x_sort_'		+b)) ) e.value = f.value;
		}
	},

	validate : function()
	{
		var f, i, a, b, e, c = false;

		for ( i = 1 ; !c && $('o_sort_'+i) ; i++ )
		{
			if ( !c && (a = $('n_del_'		+i)) && (b = $('o_cat_id_'	+i)) ) c = a.checked && b.value != '0';
			if ( !c && (a = $('n_cat_name_'	+i)) && (b = $('o_cat_name_'+i)) ) c = b.value != a.value;
			if ( !c &&								(b = $('o_sort_'	+i)) ) c = b.value != DropDown.getSelValue('n_sort_'+i);
		}

		if ( c )
		{
			if ( (f = $('myform')) )
			{
				f.method = 'post';
				f.action = '/?tab=favvideos&act=cat';
				f.submit();
			}
		}
		else
		{
			alert('Nothing to save.');
		}
	}
};

var FavVideosVid =
{
	resort : function(e)
	{
		var a, b, e, n_del, o_blog_id, o_cat_id, n_cat_id, o_youtube_id, n_youtube_id, o_sort, x_sort;

		a = Dec.parse(e.id.substr(7));
		b = Dec.parse(DropDown.getSelValue(e));
		if ( a != b )
		{
			if ( (e = $('n_del_'		+a)) ) n_del		= e.checked;
			if ( (e = $('o_blog_id_'	+a)) ) o_blog_id	= e.value;
			if ( (e = $('o_cat_id_'		+a)) ) o_cat_id		= e.value;
			if ( (e = $('o_youtube_id_'	+a)) ) o_youtube_id	= e.value;
			if ( (e = $('n_youtube_id_'	+a)) ) n_youtube_id	= e.value;
			if ( (e = $('o_sort_'		+a)) ) o_sort		= e.value;
			if ( (e = $('x_sort_'		+a)) ) x_sort		= e.value;
			n_cat_id = DropDown.getSelValue('n_cat_id_' + a);

			if ( b > a )
				for ( i = a ; i < b ; i++ ) FavVideosVid._moveFolder(i, i+1);
			else
				for ( i = a ; i > b ; i-- ) FavVideosVid._moveFolder(i, i-1);

			if ( (e = $('n_del_'		+b)) ) e.checked	= n_del;
			if ( (e = $('o_blog_id_'	+b)) ) e.value		= o_blog_id;
			if ( (e = $('o_cat_id_'		+b)) ) e.value		= o_cat_id;
			if ( (e = $('o_youtube_id_'	+b)) ) e.value		= o_youtube_id;
			if ( (e = $('n_youtube_id_'	+b)) ) e.value		= n_youtube_id;
			if ( (e = $('o_sort_'		+b)) ) e.value		= o_sort;
			if ( (e = $('x_sort_'		+b)) ) e.value		= x_sort;
			DropDown.selectFromVal('n_cat_id_' + b, n_cat_id);

			DropDown.selectFromVal('n_sort_'   + a, a);
		}
		return false;
	},

	_moveFolder : function(a, b)
	{
		var e, f;

		if ( (e = $('o_blog_id_'+a)) && (f = $('o_blog_id_'+b)) )
		{
			if ( (e = $('n_del_'		+a)) && (f = $('n_del_'			+b)) ) e.checked = f.checked;
			if ( (e = $('o_blog_id_'	+a)) && (f = $('o_blog_id_'		+b)) ) e.value = f.value;
			if ( (e = $('o_cat_id_'		+a)) && (f = $('o_cat_id_'		+b)) ) e.value = f.value;
			if ( (e = $('o_youtube_id_'	+a)) && (f = $('o_youtube_id_'	+b)) ) e.value = f.value;
			if ( (e = $('n_youtube_id_'	+a)) && (f = $('n_youtube_id_'	+b)) ) e.value = f.value;
			if ( (e = $('o_sort_'		+a)) && (f = $('o_sort_'		+b)) ) e.value = f.value;
			if ( (e = $('x_sort_'		+a)) && (f = $('x_sort_'		+b)) ) e.value = f.value;
			DropDown.selectFromVal('n_cat_id_' + a, DropDown.getSelValue('n_cat_id_' + b));
		}
	},

	validate : function()
	{
		var i, e, f, t, s, v, a, b, c = false, d = true;

		if ( ! (f = $('myform')) )
			return true;

		for ( i = 1 ; (e = $(s = 'n_youtube_id_'+i)) ; i++ )
			Validate.reset(s);
		t = i;

		for ( i = 1 ; !c && i < t ; i++ )
		{
			if ( !c && (a = $('n_del_'			+i)) && (b = $('o_blog_id_'		+i)) ) c = a.checked && b.value != '0';
			if ( !c && (a = $('n_youtube_id_'	+i)) && (b = $('o_youtube_id_'	+i)) ) c = b.value != a.value;
			if ( !c &&									(b = $('o_cat_id_'		+i)) ) c = b.value != DropDown.getSelValue('n_cat_id_'+i);
			if ( !c &&									(b = $('o_sort_'		+i)) ) c = b.value != DropDown.getSelValue('n_sort_'  +i);
		}

		if ( c )
		{
			for ( i = 1 ; d && i < t ; i++ )
			{
				s = 'n_youtube_id_'+i;
				e = $(s);
				if ( (e.value = Str.trim(e.value)) )
					d = YouTube.validate(s,null,1,'YouTube video id in row #'+i,0) !== false;
			}

			if ( d )
			{
				f.method = 'post';
				f.action = '/?tab=favvideos&act=vid';
				f.submit();
			}
		}
		else
		{
			alert('Nothing to save.');
		}
	}
};

/* --------------------------------------------------------------------- */

