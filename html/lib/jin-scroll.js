/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Scroll =
{
	curTimer : null,
	obj : null, begX : 0, begY : 0, dX : 0, dY : 0, steps : 0, step : 0, stop : false,

	move : function(e, xf, yf, steps, wait)
	{
		Scroll.finish();
		if ( typeof(steps) == 'undefined' ) steps = 33;
		if ( typeof(wait ) == 'undefined' ) wait  = 10;
		if ( ! Scroll._init(e) ) return;
		Scroll._move(xf, yf, steps, wait);
	},

	offset : function(e, dx, dy, steps, wait)
	{
		Scroll.finish();
		if ( typeof(steps) == 'undefined' ) steps = 33;
		if ( typeof(wait ) == 'undefined' ) wait  = 10;
		if ( ! Scroll._init(e) ) return;
		Scroll._move(Scroll.begX + dx, Scroll.begY + dy, steps, wait);
		return { x: Scroll.begX + dx, y : Scroll.begY + dy };
	},

	wnd : function(x, y, e)
	{
		if ( (e = $s(e)) && (x < 0 || y < 0) )
		{
			e = DynarchMenu._getPos(e);
			if ( x < 0 ) x = e.x;
			if ( y < 0 ) y = e.y;
		}

		Scroll.move(null, x, y, 33, 10);
	},

	_init : function(e)
	{
		if ( e == null )
		{
			Scroll.obj = null;
		}
		else
		{
			Scroll.obj = $s(e);
			if ( typeof(Scroll.obj) != 'object' )
				return false;
		}

		if ( Scroll.obj )
		{
			Scroll.begX = Scroll.obj.scrollLeft;
			Scroll.begY = Scroll.obj.scrollTop;
		}
		else
		{
			Scroll.begX = document.documentElement.scrollLeft || document.body.scrollleft || 0;
			Scroll.begY = document.documentElement.scrollTop  || document.body.scrollTop  || 0;
		}
		return true;
	},

	_move : function(xf, yf, steps, wait)
	{
		if ( xf < 0 ) xf = 0;
		if ( yf < 0 ) yf = 0;

		Scroll.dX = xf - Scroll.begX;
		Scroll.dY = yf - Scroll.begY;
		if ( Scroll.dX || Scroll.dY )
		{
			Scroll.steps = steps;
			Scroll.step  = 0;
			Scroll.stop  = false;
			Scroll.curTimer = setInterval('Scroll.scroll()', wait);
		}
	},

	scroll : function()
	{
		var c = ++Scroll.step / Scroll.steps,
			x = Scroll.begX,
			y = Scroll.begY;

		if ( Scroll.stop )
			return;

		if ( Scroll.dX )
		{
			x = ((-Math.cos( c * Math.PI) / 2) + 0.5) * Scroll.dX + Scroll.begX;
			x = Scroll.dX > 0 ? Math.ceil(x) : Math.floor(x);
			if ( Scroll.obj ) Scroll.obj.scrollLeft = x;
		}

		if ( Scroll.dY )
		{
			y = ((-Math.cos( c * Math.PI) / 2) + 0.5) * Scroll.dY + Scroll.begY;
			y = Scroll.dY > 0 ? Math.ceil(y) : Math.floor(y);
			if ( Scroll.obj ) Scroll.obj.scrollTop = y;
		}

		if ( ! Scroll.obj )
			window.scrollTo(x, y);

		if ( Scroll.step >= Scroll.steps )
		{
			clearInterval(Scroll.curTimer);
			Scroll.curTimer = null;
		}
	},

	finish : function()
	{
		if ( Scroll.curTimer )
		{
			clearInterval(Scroll.curTimer);
			Scroll.stop     = true;
			Scroll.curTimer = null;
			Scroll.obj.scrollLeft = Scroll.begX + Scroll.dX;
			Scroll.obj.scrollTop  = Scroll.begY + Scroll.dY;
		}
	}
};

/* --------------------------------------------------------------------- */

