/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Context =
{
	_isFireFox : navigator.userAgent.match(/gecko/i),
	_menuHandle : null,

	setup : function()
	{
		if ( Filmaf.onPopup )
			Context._menuHandle = DynarchMenu.setup('context-menu',{context:true,lazy:true,onPopup:Filmaf.onPopup,ctxbutton:1});
		else
			Context._menuHandle = DynarchMenu.setup('context-menu',{context:true,lazy:true,ctxbutton:1});
	},

	attach : function(click,edit,s_menu,n_calendar_id)
	{
		var e = $s(click),
			f = $s(edit);

		if ( Context._menuHandle && s_menu && (e || f) )
		{
			if ( s_menu == 'calendar' )
			{
				if ( f ) f.onclick = Search.calendars[n_calendar_id];
				if ( e ) e.onclick = Search.calendars[n_calendar_id];
			}
			else
			{
				if ( f ) f.onclick = f.onkeydown = Context._pressButton;
				if ( e ) DynarchMenu.setupContext(e, Context._menuHandle.items[s_menu]);
			}
		}
	},

	_pressButton : function(ev)
	{
		ev||(ev=window.event);
		if ( ev.keyCode != 9 )
		{
			// This was supposed to get rid of "Permission denied to get property XULElement.selectedIndex.", but it did not work.
			// trying again with ev.currentTarget || ev.srcElement
			var e = ev.currentTarget || ev.srcElement;
			if ( e && e.tagName == 'INPUT' )
				e.setAttribute('autocomplete','off');

			if ( e && e.id && (e = $('h'+e.id.substr(1))) )
				e.onclick(ev);
			else
				alert('Please use the [...] button next to this field to select an option.');
		}
	},

	getPopupPos : function(e,dx,dy) // getPopupPos
	{
		var r   = DynarchMenu.getWinSize(),
			xi  = DynarchMenu.psLeft(),
			yi  = DynarchMenu.psTop(),
			xf  = xi + r.x,
			yf  = yi + r.y;

//			r   = DynarchMenu._getPos(e);
			r   = Context.getPos(e);
		var xe  = r.x,
			ye  = r.y,
			zh  = e.getAttribute('zoom_hoz'),
			dn  = e.getAttribute('zoom_ver') == 'down',
			lf  = zh == 'left',
			lo  = zh == 'leftoffset';

		r.beg_y = yi;
		r.end_y = yf;
		r.bot_y = 0;

		// if lf then to the left, otherwise to the right
		r.x  = lf ? xe - dx : xe + (lo ? 0 : e.offsetWidth);
		// top right
		r.y  = ye - 1;
		//alert('r.x='+r.x+' '+'r.y='+r.y+'\n'+'xe='+xe+' '+'ye='+ye+'\n'+'xf='+xf+' '+'yf='+yf+'\n'+'xi='+xi+' '+'yi='+yi+'\n'+'dx='+dx+' '+'dy='+dy);

		// if dn then place it at the bottom, otherwise just offset 85% down
		r.y += dn ? e.offsetHeight : e.offsetHeight * 85 / 100;

		// Does it fit horizontally?
		if (! lf && r.x + dx > xf) r.x = lo ? r.x = xf - dx -1 : r.x - e.offsetWidth - dx;
		// Do not let it go to the left of the window
		if (r.x < xi) r.x = xi;

		// Does it fit vertically?
		if (r.y + dy > yf)
		{
			r.bot_y = r.y - (dn ? e.offsetHeight : e.offsetHeight * 70 / 100);
			r.y = r.bot_y - dy;
			r.bot_y = r.end_y - r.bot_y;
		}
		// Do not let it go to the top of the window
		if (r.y < yi) r.y = yi;

		r.x = Math.floor(r.x);
		r.y = Math.floor(r.y);
		return r;
	},

	getPos : function(e)
	{
		var r = { x:0, y:0 },
			op, pn;

		if ( e )
		{
			r.x = e.offsetLeft;
			r.y = e.offsetTop;
			op  = e.offsetParent;
			pn  = e.parentNode;
			while ( op )
			{
				r.x += op.offsetLeft;
				r.y += op.offsetTop;
				if ( op != document.body && op != document.documentElement )
				{
					r.x -= op.scrollLeft;
					r.y -= op.scrollTop;
				}
				//next lines are necessary to support FireFox problem with offsetParent
				if ( Context._isFireFox )
				{
					while ( pn && op != pn )
					{
						r.x -= pn.scrollLeft;
						r.y -= pn.scrollTop;
						pn = pn.parentNode;
					}
				}
				pn = op.parentNode;
				op = op.offsetParent;
			}
		}
		return r;
	},

	close : function()
	{
		if ( window.top && window.top.DynarchMenu )
			window.top.DynarchMenu._closeOtherMenus(null);
	}
};

/* --------------------------------------------------------------------- */

