/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Menus =
{
	setup : function() // loadMenus, Load.menus
	{
		if ( $('main-menu'		) ) DynarchMenu.setup('main-menu' ,{lazy:true,electric:1000,tooltips:true,scrolling:true});
		if ( $('context-menu'	) ) Context.setup();
		if ( $('search'			) ) Search.setup(1);
		if ( Filmaf.attachExplains ) Explain.attach();
	},

	cloneNav : function()
	{
		var e, f;
		if ( (e = $('nav_top')) && (f = $('nav_bop')) )
		{
			f.innerHTML = e.innerHTML.
						   replace(/<td width=.8%.><select.*\/select><\/td>/i,'').
						   replace(/sz_page_0/,'sz_page_1').
						   replace(/ex_www_titles_disks_0/,'ex_www_titles_disks_1').
						   replace(/ex_www_dvd_cart_0/,'ex_www_dvd_cart_1').
						   replace(/cart_count_0/,'cart_count_1').
						   replace(/dp_jump_0/,'dp_jump_1');
		}
	}
};

/* --------------------------------------------------------------------- */

