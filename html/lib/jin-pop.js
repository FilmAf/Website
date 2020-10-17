/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Pop =
{
	open : function(e,f,dx,dy,hid)
	{
		// hid = 0 - place and show used for invariant picture sizes
		// hid = 1 - calculate initial placement but keep it hidden
		// hid = 2 - double check final height, move if needed and make it visible
		var r = {}, yi, yf;

		e = $s(e);

		if (hid == 1)
			if ( e.style.visibility != 'hidden' )
				e.style.visibility = 'hidden';

		switch (hid)
		{
		case 0:
		case 1:
			f = $s(f);
			r = Context.getPopupPos(f, dx, dy);
			e.style.right = '';
			e.style.left = r.x + 'px';

			if (hid == 1 && r.bot_y)
			{
				e.style.top = '';
				e.style.bottom = (r.bot_y - r.beg_y) + 'px';
			}
			else
			{
				e.style.bottom = '';
				e.style.top = r.y + 'px';
			}
			break;

		case 2:
			yi  = DynarchMenu.psTop();
			r   = Context.getPos(e);
			if (r.y < yi)
			{
				e.style.bottom = '';
				e.style.top = yi + 'px';
			}
			break;
		case 3:
			r.y = DynarchMenu.psTop() + 10;
			r.x = DynarchMenu.psLeft() + (DynarchMenu.getWinSize().x - dx) / 2;
			e.style.right = '';
			e.style.left = r.x + 'px';
			e.style.bottom = '';
			e.style.top = r.y + 'px';
			break;
		}

		if (hid != 1)
			if ( e.style.visibility != 'visible' )
				e.style.visibility = 'visible';
	},

	close : function(e)
	{
		if ( (e = $s(e)) && e.style.visibility != 'hidden' )
		{
			e.style.visibility = 'hidden';
			return true;
		}
		return false;
	}
};

/* --------------------------------------------------------------------- */

