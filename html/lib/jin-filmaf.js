/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function $(e)
{
	return document.getElementById(e);
};

function $s(e)
{
	return typeof(e) == 'string' ? document.getElementById(e) : ( typeof(e) == 'object' ? e : null);
}

var Filmaf =
{
	baseDomain			: '',
	cookieDomain		: '',
	userCollection		: '',
	viewCollection		: '',
	presentationMode	: '',
	reqPageSize			: 50,
	firstRowNo			: 1,
	rowsShown			: 0,
	onMenuPopup			: null,
	objId				: 0,
	lastMoveFolder		: '',
	hasRelWeek			: 0,
	loadMainFloat		: 0,
	loadContextMenu		: 0,
	loadSearchMenu		: 0,
	attachExplains		: 0,
	contextStr			: '',
	contextMed			: '', // b for bluray, d for dvd
	contextForm			: '',
	inputLine			: 0,
	contextRow			: 0,
	updatePriceComp		: 0,

	config : function(c) // loadSupplement, f_echo_context_ul, Load.supplement
	{
		var s = {s:''},
			a = $('s_alert'),
			e = $('context-menu'),
			r = $('search');

		if ( ! c.baseDomain )
		{
			alert('Missing baseDomain on Filmaf.config');
			return;
		}
												  Filmaf.baseDomain			= c.baseDomain;
		if ( c.userCollection					) Filmaf.userCollection		= c.userCollection == 'guest' ? '' : c.userCollection;
		if ( c.viewCollection					) Filmaf.viewCollection		= c.viewCollection == 'www'   ? '' : c.viewCollection;
		if ( c.presentationMode					) Filmaf.presentationMode	= c.presentationMode;
		if ( c.reqPageSize						) Filmaf.reqPageSize		= c.reqPageSize;
		if ( c.firstRowNo						) Filmaf.firstRowNo			= c.firstRowNo;
		if ( c.rowsShown						) Filmaf.rowsShown			= c.rowsShown;
												  Filmaf.onPopup			= c.onPopup ? c.onPopup : null;
		if ( c.objId							) Filmaf.objId				= c.objId;
												  Filmaf.lastMoveFolder		= decodeURIComponent(Cookie.get('move'));
		if ( c.hasRelWeek						) Filmaf.hasRelWeek			= 1;
		if ( c.ulExplain						) Filmaf.attachExplains		= 1;

		if ( c.imgPreLoad 						) Img.preLoad(c.imgPreLoad);
		if ( r									) Search.create(0,0);
		if ( c.optionsTag						) DropDown.replaceOptions('sel_folder',c.optionsTag);
		if ( $('nav_bop')						) Menus.cloneNav();
		if ( c.imgHandlers || c.cartHandlers	) Img.attach();
		if ( c.cartHandlers						) Price.attach();
		if ( c.cartHandlers						) Cart.highlight(false);
		if ( c.preloadImgPop					) setTimeout('ImgPop.getImgDiv();',200);

		if ( e )
		{
			if ( c.ulAspect			) ulAspect(s);
			if ( c.ulBlog			) ulBlog(s);
			if ( c.ulBirth			) ulBirth(s);
			if ( c.ulCountry		) ulCountry(s);
			if ( c.ulDir			) ulDir(s);
			if ( c.ulDvd			) ulDvd(s);
			if ( c.ulDvdComments	) ulDvdComments(s);		// c.ulModDvd
			if ( c.ulDvdMany		) ulDvdMany(s);
			if ( c.ulDvdOne			) ulDvdOne(s);
			if ( c.ulDvdPic			) ulDvdPic(s);
			if ( c.ulDvdTitle		) ulDvdTitle(s);
			if ( c.ulExplain		) ulExplain(s);
			if ( c.ulFilmRating		) ulFilmRating(s);
			if ( c.ulGenre			) ulGenre(s);
			if ( c.ulHome			) ulHome(s);
			if ( c.ulJump			) ulJump(s);
			if ( c.ulLang			) ulLang(s);
			if ( c.ulPageSize		) ulPageSize(s);
			if ( c.ulPerson			) ulPerson(s);
			if ( c.ulPicComments	) ulPicComments(s);		// c.ulModPic
			if ( c.ulPicMngt		) ulPicMngt(s);
			if ( c.ulPub			) ulPub(s);
			if ( c.ulRegion			) ulRegion(s);
			if ( r					) ulSearch(s);
			if ( c.ulStars			) ulStars(s);
			e.innerHTML = s.s;
		}

		if ( a									) alert(a.innerHTML);
		if ( c.splash							) splash_me(1);
	},

	_getCookieDomain : function()
	{
		var c = document.domain ? document.domain.match(/\..*\.com$/) ||
								  document.domain.match(/\..*\.net$/) ||
								  document.domain.match(/\..*\.mil$/) ||
								  document.domain.match(/\..*\.edu$/) ||
								  document.domain.match(/\..*\.gov$/) : '';

		return typeof(c) == 'object' && c[0] ? c[0] : c;
	}
};

Filmaf.cookieDomain = Filmaf._getCookieDomain();

/* --------------------------------------------------------------------- */

