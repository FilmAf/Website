/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var TblMenu =
{
	attach : function(e,callback)
	{
		if ( (e = $(e)) )
		{
			e._TblMenuSel = null;
			e._TblMenuCall = callback;

			var y = Dom.getFirstChildByType(e, 'tbody'), x, i, j;
			if ( (y = Dom.getChildrenByType(y ? y : e, 'tr')) )
			{
				for( j = y.length  ; --j >= 0  ;  )
				{
					if ( (x = Dom.getChildrenByType(y[j], 'td')) )
					{
						for( i = x.length  ; --i >= 0  ;  )
						{
							a = x[i];
							if ( a.id )
							{
								a._TblMenuCont = e;
								a.onmouseover	= function(ev){TblMenu.mouseOver(ev,this);};
								a.onmouseout	= function(  ){TblMenu.mouseOut(this);};
								a.onclick		= function(ev){TblMenu.click(ev,this);};
								a.className		= 'td_opt';
							}
						}
					}
				}
			}
		}
		
	},
	mouseOver : function(ev, td)
	{
		if ( td._TblMenuCont )
			if ( td._TblMenuCont._TblMenuSel && td._TblMenuCont._TblMenuSel.id != td.id )
				td.className = 'td_hov';
	},
	mouseOut : function(td)
	{
		if ( td._TblMenuCont )
			if ( td._TblMenuCont._TblMenuSel && td._TblMenuCont._TblMenuSel.id != td.id )
				td.className = 'td_opt';
	},
	click : function(ev, td)
	{
		TblMenu.set(td,-2);
	},
	set : function(e,parm)
	{
		if ( e && e._TblMenuCont )
		{
			if ( e._TblMenuCont._TblMenuSel && e._TblMenuCont._TblMenuSel.id != e.id )
				e._TblMenuCont._TblMenuSel.className = 'td_opt';
			(e._TblMenuCont._TblMenuSel = e).className = 'td_sel';
			e._TblMenuCont._TblMenuCall(e.id,parm);
		}
	}
};

/* --------------------------------------------------------------------- */

