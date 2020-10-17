/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var FilmGp =
{
	owned : function(id) {FilmGp._dvdAction(id,'owned');},
	wish  : function(id) {FilmGp._dvdAction(id,'wish-list');},
	order : function(id) {FilmGp._dvdAction(id,'on-order');},
	work  : function(id) {FilmGp._dvdAction(id,'work');},
	seen  : function(id) {FilmGp._dvdAction(id,'have-seen');},
	del   : function(id) {FilmGp._dvdAction(id,'del');},

	blog : function(id)
	{
		alert("Please sign in to blog.");
	},

	setup : function()
	{
		Filmaf.contextStr = Filmaf.objId;
		Filmaf.contextMed = $('obj_type').value;

		if ( Filmaf.userCollection != '' )
			Menus.setup();
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
		case 'context_fb_blog':
			break;
		case 'context_fb_more':
			Facebook.ulPre(i, Filmaf.contextMed, Filmaf.contextStr);
			break;
		}
	},


    editions : function(n)
	{
		Ajax.asynchNoDup('img-pop', 'ImgPop.__paintExpand', '?dvd='+n, 0, 250);
	},

	_dvdAction : function(id,fld)
	{
		if ( Filmaf.userCollection != '' )
			if ( fld == 'del' )
				Ajax.asynch('dvd-action','FilmGp.__dvdActioned','?dvd='+id+'&action=del',0,10);
			else
				Ajax.asynch('dvd-action','FilmGp.__dvdActioned','?dvd='+id+'&action=mov&folder='+fld,0,10);
		else
			alert("Please sign in to update your film collection.");
	},

	__dvdActioned : function()
	{
		if ( Ajax.ready() )
		{
			var o = {};

			if ( Ajax.getParms(o) )
			{
				var act = Ajax.statusTxt(o.line1,'action'),
					fld = Ajax.statusTxt(o.line1,'folder'),
					dvd = Ajax.statusTxt(o.line1,'dvd'	 );

				FilmGp._updateStatus(act == 'mov' ? fld : '', dvd);
			}
			else
			{
				alert('Oops. The update failed.');
			}
		}
	},

	_updateStatus : function(f,n)
	{
		var v = f != '' ? 'Move to ': 'Add to ';

		$('f_owned' ).innerHTML = f == 'owned'     ? "<span style='color:#de4141'>In owned</span>"     : "<a href='javascript:FilmGp.owned("+n+")'>"+v+"owned</a>"   ;
		$('f_wish'  ).innerHTML = f == 'wish-list' ? "<span style='color:#de4141'>In wish-list</span>" : "<a href='javascript:FilmGp.wish("+n+")'>"+v+"wish-list</a>";
		$('f_order' ).innerHTML = f == 'on-order'  ? "<span style='color:#de4141'>In on-order</span>"  : "<a href='javascript:FilmGp.order("+n+")'>"+v+"on-order</a>";
		$('f_work'  ).innerHTML = f == 'work'      ? "<span style='color:#de4141'>In work</span>"      : "<a href='javascript:FilmGp.work("+n+")'>"+v+"work</a>"     ;
		$('f_have'  ).innerHTML = f == 'have-seen' ? "<span style='color:#de4141'>In have-seen</span>" : "<a href='javascript:FilmGp.seen("+n+")'>"+v+"have-seen</a>";
		$('f_del'   ).innerHTML = f != ''          ? "<a href='javascript:FilmGp.del("+n+")'>Delete from collection</a>" : '&nbsp;';

		$('pop_text').innerHTML = f != ''
								  ? ImgPop.paintInColl(f.substr(0,4),'FilmGp.editions('+n+')')
								  : "<div style='padding:0 4px 4px 4px'><div style='float:right'><a href='javascript:FilmGp.editions("+n+")'>[+]</a></div>&nbsp;</div>";
	}
};

/* --------------------------------------------------------------------- */

