/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var ImgSpin =
{
	attachSpinV : function(e) // addImgHandlersSpinV
	{
		e.onmouseover = function(ev){Img.mouseOver(ev,this,4);};
		e.onmousemove = function(ev){Img.mouseOver(ev,this,4);};
		e.onmousewheel= function(ev){ImgSpin._wheel(ev,this,4,this.id);};
		e.onmouseout  = function(  ){Img.mouseOut(this,4);};
		e.onclick	  = function(ev){Img.click(ev,this,4,this.id);};
		e.ondblclick  = function(ev){if ( ! ev ) Img.click(ev,this,4,this.id);}; // only trigger for ie as Firefox sends both click and cblclick
	},

	attachSpinH : function(e) // addImgHandlersSpinH
	{
		e.onmouseover = function(ev){Img.mouseOver(ev,this,8);};
		e.onmousemove = function(ev){Img.mouseOver(ev,this,8);};
		e.onmousewheel= function(ev){ImgSpin._wheel(ev,this,8,this.id);};
		e.onmouseout  = function(  ){Img.mouseOut(this,8);};
		e.onclick	  = function(ev){Img.click(ev,this,8,this.id);};
		e.ondblclick  = function(ev){if ( ! ev ) Img.click(ev,this,8,this.id);}; // only trigger for ie as Firefox sends both click and cblclick
	},

	_wheel : function(ev, img, type, s_id) // imgOnMouseWheel
	{
		ev || (ev = window.event);
		if ( ev.wheelDelta && (! Img._buttonsLoaded || ! img) ) return;
		if ( ev.srcElement && ev.srcElement.getAttribute('sp_inc') == 'F' ) return;

		var a = Img.buttons[type], o = ev.wheelDelta;

		if ( o != 0 && a.onClick ) a.onClick(s_id, o >= 0);
		// stop event propagatinon on IE (does not get fired in FireFox)
		ev.cancelBubble = true;
		ev.returnValue = false;
	},

	click : function(s,u) // onClickSpin
	{
		function ff_bounds(n, p, u)
		{
			var a = p.getAttribute('sp_min'),
				b = p.getAttribute('sp_max');

			if ( a && b )
			{
				switch ( u )
				{
				case 'int':
					a = Dec.parse(a); if ( n < a ) return a;
					b = Dec.parse(b); if ( n > b ) return b;
					break;
				case 'float':
					a = Dbl.parse(a); if ( n < a ) n = a;
					b = Dbl.parse(b); if ( n > b ) n = b;
					n = Dbl.toStr(n,'');
					break;
				}
			}
			return n;
		};

		var k, e, p = $(s);

		if ( p )
		{
			k = p.getAttribute('sp_inc');
			if ( k == 'F' )
			{
				Folders.onSpin(s,u);
			}
			else
			{
				if ( (e = $('n_'+s.substr(5))) )
				{
					switch ( k )
					{
					case '0': s = Edit.getInt(e) + (u ?     1 :     -1); u = 'int';   break;
					case '1': s = Edit.getInt(e) + (u ?    10 :    -10); u = 'int';   break;
					case '2': s = Edit.getInt(e) + (u ?   100 :   -100); u = 'int';   break;
					case '3': s = Edit.getInt(e) + (u ?  1000 :  -1000); u = 'int';   break;
					case '4': s = Edit.getInt(e) + (u ? 10000 : -10000); u = 'int';   break;

					case 'a': s = Math.floor(Edit.getInt(e)/    10) *     10 + (u ?     10 :     -10); u = 'int';   break;
					case 'b': s = Math.floor(Edit.getInt(e)/   100) *    100 + (u ?    100 :    -100); u = 'int';   break;
					case 'c': s = Math.floor(Edit.getInt(e)/  1000) *   1000 + (u ?   1000 :   -1000); u = 'int';   break;
					case 'd': s = Math.floor(Edit.getInt(e)/ 10000) *  10000 + (u ?  10000 :  -10000); u = 'int';   break;
					case 'e': s = Math.floor(Edit.getInt(e)/100000) * 100000 + (u ? 100000 : -100000); u = 'int';   break;

					case 'A': s = Edit.getDbl(e) + (u ?   0.01 :   -0.01); u = 'float'; break;
					case 'B': s = Edit.getDbl(e) + (u ?   0.1  :   -0.1 ); u = 'float'; break;
					case 'C': s = Edit.getDbl(e) + (u ?   1    :   -1   ); u = 'float'; break;
					case 'D': s = Edit.getDbl(e) + (u ?  10    :  -10   ); u = 'float'; break;
					case 'E': s = Edit.getDbl(e) + (u ? 100    : -100   ); u = 'float'; break;
					default: return;
					}
					e.value = ff_bounds(s, p, u);
					Undo.change(e);
				}
			}
		}
	}
};

/* --------------------------------------------------------------------- */

