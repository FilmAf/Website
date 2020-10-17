/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Img =
{
	buttons			: {},
	_buttonsLoaded	:  0,
	_buttonsInd		: {},

	preLoad : function(s)
	{
		if ( ! document.images ) return;

		var s = s.split('.'),
			i, a;

		for ( i = 0  ;  i < s.length  ;  i++ )
		{
			switch ( s[i] )
			{
			case 'pin':
				// gray pins
				a = Img.buttons[0] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/bp00.gif';
				a.b01 = new Image(); a.b01.src = 'http://dv1.us/d1/00/bp01.gif';
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/bp10.gif';
				a.b11 = new Image(); a.b11.src = 'http://dv1.us/d1/00/bp11.gif';
				a.onClick = null;
				a.afterClick = Search.savePin;
				break;
			case 'cart':
				a = Img.buttons[1] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/bc00.png';
				a.b01 = new Image(); a.b01.src = 'http://dv1.us/d1/00/bc01.png';
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/bc10.png';
				a.b11 = new Image(); a.b11.src = 'http://dv1.us/d1/00/bc11.png';
				a.onClick = Cart.click;
				a.afterClick = null;
				break;
			case 'price':
				a = Img.buttons[2] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/bd00.png';
				a.b01 = new Image(); a.b01.src = 'http://dv1.us/d1/00/bd01.png';
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/bd10.png';
				a.b11 = new Image(); a.b11.src = 'http://dv1.us/d1/00/bd11.png';
				a.onClick = Price.click;
				a.afterClick = null;
				break;
			case 'help':
				a = Img.buttons[3] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/bm00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/bm10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'spin':
				a = Img.buttons[4] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/pn00.gif';
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/pn10.gif';
				a.b11 = new Image(); a.b11.src = 'http://dv1.us/d1/00/pn11.gif';
				a.onClick = ImgSpin.click;
				a.afterClick = null;
				break;
			case 'spin_hoz':
				a = Img.buttons[8] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/ph00.gif';
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/ph10.gif';
				a.b11 = new Image(); a.b11.src = 'http://dv1.us/d1/00/ph11.gif';
				a.onClick = ImgSpin.click;
				a.afterClick = null;
				break;
			case 'explain':
				a = Img.buttons[5] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/bq00.png';
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/bq10.png';
				a.onClick = Explain.show;
				a.afterClick = null;
				break;
			case 'drop':
				a = Img.buttons[6] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/dp00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/dp10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'undo':
				a = Img.buttons[7] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/1.gif';
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/ud01.gif';
				a.b11 = new Image(); a.b11.src = 'http://dv1.us/d1/00/ud11.gif';
				a.b20 = new Image(); a.b20.src = 'http://dv1.us/d1/00/rd01.gif';
				a.b21 = new Image(); a.b21.src = 'http://dv1.us/d1/00/rd11.gif';
				a.onClick = Undo.click;
				a.afterClick = null;
				break;
			case 'pagesize':
				a = Img.buttons[9] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/sz00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/sz10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'aleft':
				a = Img.buttons[10] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/al00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/al10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'aright':
				a = Img.buttons[11] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/ar00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/ar10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'astop':
				a = Img.buttons[12] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/as00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/as10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'across':
				a = Img.buttons[13] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/ax00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/ax10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'acheck':
				a = Img.buttons[14] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/ak00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/ak10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'aplus':
				a = Img.buttons[15] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/ap00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/ap10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'adown':
				a = Img.buttons[16] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/ad00.gif'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/ad10.gif'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'tabbk':
				Img._buttonsInd[0] = new Image();
				Img._buttonsInd[0].src = 'http://dv1.us/d1/00/header-back-small.jpg';
				break;
			case 'spun':
				a = Img.buttons[17] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/pn00.gif';
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/pn10.gif';
				a.b11 = new Image(); a.b11.src = 'http://dv1.us/d1/00/pn11.gif';
				a.onClick = ImgSpun.click;
				a.afterClick = null;
				break;
			case 'home':
				a = Img.buttons[18] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/fbh0.png'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/fbh1.png'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;
			case 'coll':
				a = Img.buttons[19] = {};
				a.b00 = new Image(); a.b00.src = 'http://dv1.us/d1/00/fbf0.png'; a.b01 = a.b00;
				a.b10 = new Image(); a.b10.src = 'http://dv1.us/d1/00/fbf1.png'; a.b11 = a.b10;
				a.onClick = null;
				a.afterClick = null;
				break;

			}
		}
		Img._buttonsLoaded = true;
	},

	getMouseY : function(ev,e)
	{
		return Img.getCoord(ev,e).y;
/*		
		var s = 'ev.y = '+(typeof(ev.y)=='undefined' ? 'undefined' : ev.y)+'\n'+
				'ev.layerY = '+(typeof(ev.layerY)=='undefined' ? 'undefined' : ev.layerY)+'\n'+
				'ev.pageY = '+(typeof(ev.pageY)=='undefined' ? 'undefined' : ev.pageY)+'\n'+
				'ev.currentTarget.y = '+(typeof(ev.currentTarget.y)=='undefined' ? 'undefined' : ev.currentTarget.y)+'\n'+
				'window.event.offsetY = '+((typeof(window.event)!='undefined' && typeof(window.event.offsetY)!='undefined') ? window.event.offsetY : 'undefined')+'\n'+
				'ev.currentTarget.offsetTop = '+((typeof(ev.currentTarget)!='undefined' && typeof(ev.currentTarget.offsetTop)!='undefined') ? ev.currentTarget.offsetTop : 'undefined')+';';
		alert(s);
		if ( window.event && typeof(window.event.offsetY)!='undefined' )
			return window.event.offsetY;
		if ( ev && ev.currentTarget && typeof(ev.layerY)!='undefined' && typeof(ev.currentTarget.offsetTop)!='undefined' )
			return ev.layerY - ev.currentTarget.offsetTop;
		return 0;
*/
	},

	getMouseX : function(ev,e)
	{
		return Img.getCoord(ev,e).x;
/*
		if ( window.event && typeof(window.event.offsetX)!='undefined' )
			return window.event.offsetX;
		if ( ev && ev.currentTarget && typeof(ev.layerX)!='undefined' && typeof(ev.currentTarget.offsetLeft)!='undefined' )
			return ev.layerX - ev.currentTarget.offsetLeft;
		return 0;
*/
	},

	setPin : function(e,b)
	{
		Img.check(e,0,b);
	},

	getPin : function(e)
	{
		return Img.isChecked(e,0);
	},

	attach : function() // addImgHandlers
	{
		var e = window.self.document.getElementsByTagName('img'), i, x;

		for( i = e.length  ; --i >= 0  ;  )
		{
			x = e[i];
			if ( x.id )
			{
				switch ( x.id.substr(0,3) )
				{
				case 'ic_': // cart     (type = 1)
					x.onmouseover	= function(ev){Img.mouseOver(ev,this,1);};
					x.onmouseout	= function(  ){Img.mouseOut(this,1);};
					x.onclick		= function(ev){Img.click(ev,this,1,this.id.substr(3));};
					break;
				case 'id_': // prices   (type = 2)
					x.onmouseover	= function(ev){Img.mouseOver(ev,this,2);};
					x.onmouseout	= function(  ){Img.mouseOut(this,2);};
					x.onclick		= function(ev){Img.click(ev,this,2,this.id.substr(3));};
					break;
				case 'is_': // V spin   (type = 4)
					ImgSpin.attachSpinV(x);
					break;
				case 'ex_': // explain  (type = 5)
					x.onmouseover	= function(ev){Img.mouseOver(ev,this,5);};
					x.onmouseout	= function(  ){Img.mouseOut(this,5);};
					x.onclick		= function(ev){Img.click(ev,this,5,this.id.substr(3));};
				break;
					case 'dp_': // drop     (type = 6)
					x.onmouseover	= function(ev){Img.mouseOver(ev,this,6);};
					x.onmouseout	= function(  ){Img.mouseOut(this,6);};
					x.onclick		= function(ev){Img.click(ev,this,6,this.id.substr(3));};
					break;
				case 'zi_': // undo     (type = 7)
					x.onmouseover	= function(ev){Img.mouseOver(ev,this,7);};
					x.onmouseout	= function(  ){Img.mouseOut(this,7);};
					x.onclick		= function(ev){Img.click(ev,this,7,this.id.substr(3));};
					Undo.setImg(x, false);
					Undo.attach(x);
					break;
				case 'ih_': // H spin   (type = 8)
					ImgSpin.attachSpinH(x);
					break;
				case 'sz_': // pagesize (type = 9)
					x.onmouseover	= function(ev){Img.mouseOver(ev,this,9);};
					x.onmouseout	= function(  ){Img.mouseOut(this,9);};
					x.onclick		= function(ev){Img.click(ev,this,9,this.id.substr(3));};
					break;
				case 'zo_':
					x.onmouseover	= function(ev){ImgPop.show(this,0);};
					break;
				}
			}
		}
	},

	mouseOver : function(ev, img, type)
	{
		if ( ! Img._buttonsLoaded ) return;

		var a = Img.buttons[type], o;

		switch ( type )
		{
		case  0: // pin
		case  1: // cart
		case  2: // price
		case  3: // help
		case 10: // aleft
		case 11: // aright
		case 12: // astop
		case 13: // across
		case 14: // acheck
		case 15: // aplus
		case 16: // adown
		case 18: // home
		case 19: // coll
			o = Dec.parse(img.src.substr(img.src.length-5,1));
			img.src = o == 2 ? a.b12.src : (o == 1 ? a.b11.src : a.b10.src);
			img.style.cursor = 'pointer';
			break;
		case  4: // spin
		case 17: // spun
			o = Img.getMouseY(ev,img);
			if ( o >= 1 && o <= 20 )
			{
				img.style.cursor = 'pointer';
				a = o >= 11 ? a.b10.src : a.b11.src;
				if ( a != img.src ) img.src = a;
			}
			break;
		case  5: // explain
		case  6: // drop
		case  9: // pagesize
			img.style.cursor = 'pointer';
			img.src = a.b10.src;
			break;
		case  7: // drop
			Undo.setImg(img, true);
			break;
		case  8: // horiz spin
			o = Img.getMouseX(ev,img);
			if ( o >= 1 && o <= 20 )
			{
				img.style.cursor = 'pointer';
				a = o < 10 ? a.b10.src : a.b11.src;
				if ( a != img.src ) img.src = a;
			}
			break;
		}
	},

	mouseOut : function(img, type)
	{
		if ( ! Img._buttonsLoaded ) return;

		var a = Img.buttons[type], o;

		switch ( type )
		{
		case  0: // pin
		case  1: // cart
		case  2: // price
		case  3: // help
		case 10: // aleft
		case 11: // aright
		case 12: // astop
		case 13: // across
		case 14: // acheck
		case 15: // aplus
		case 16: // adown
		case 18: // home
		case 19: // coll
			o = Dec.parse(img.src.substr(img.src.length-5,1));
			img.src = o == 2 ? a.b02.src : (o == 1 ? a.b01.src : a.b00.src);
			img.style.cursor = 'pointer';
			break;
		case  4: // spin
		case  8: // horiz spin
		case 17: // spun
			img.src = a.b00.src;
			break;
		case  5: // explain
		case  6: // drop
		case  9: // pagesize
			img.src = a.b00.src;
			break;
		case  7: // drop
			Undo.setImg(img, false);
			break;
		}
	},

	click : function(ev, img, type, s_id)
	{
		if ( ! Img._buttonsLoaded || ! img ) return;

		var a = Img.buttons[type], o, m, e;

		switch ( type )
		{
		case  0: // pin
			if ( (e = ev.currentTarget || ev.srcElement) ) s_id = e.id;
		case  1: // cart
		case  2: // price
			o = Dec.parse(img.src.substr(img.src.length-5,1));
			m = 2;
			if ( ! a.onClick || a.onClick(s_id, (o + 1) % m) )
				o = (o + 1) % m;
			if ( type != 2 )
				img.src = o == 2 ? a.b12.src : (o == 1 ? a.b11.src : a.b10.src);
			if ( a.afterClick )
				a.afterClick(s_id);
			break;
		case  4: // spin
		case 17: // spun
			o = Img.getMouseY(ev,img);
			if ( o >= 1 && o <= 20 && a.onClick )
				a.onClick(s_id, o <= 10);
			break;
		case  5: // explain
		case  6: // drop
		case  7: // undo
		case  9: // pagesize
			if ( a.onClick )
				a.onClick(s_id);
			break;
		case  8: // horiz spin
			o = Img.getMouseX(ev,img);
			if ( o >= 1 && o <= 20 && a.onClick )
				a.onClick(s_id, o > 10);
			break;
		}
	},

	isChecked : function(e, type)
	{
		if ( ! (e = $s(e)) ) return 0;
		switch ( type )
		{
		case  0: // pin
		case  1: // cart
		case  2: // price
			// pressed image names must end with '[0-9].gif'
			return Dec.parse(e.src.substr(e.src.length-5,1));
		}
		return 0;
	},

	check : function(e, type, n_set)
	{
		e = $s(e);
		if ( ! Img._buttonsLoaded || ! e ) return;
		switch ( type )
		{
		case  0: // pin
		case  1: // cart
		case  2: // price
			e.src = n_set == 2 ? Img.buttons[type].b02.src : (n_set == 1 ? Img.buttons[type].b01.src : Img.buttons[type].b00.src);
			break;
		}
	},

	getPicLoc : function(s,b_thumb)
	{
		var c, p, k = s.indexOf('-');
		if ( k > 3 )
		{
			c = Dec.parse(s.substr(k-1,1));
//			c = 'http://' + (c <= 1 ? '' : (c <= 4 ? 'a.' : (c <= 6 ? 'b.' : 'c.'))) + 'dv1.us';
			c = 'http://dv1.us';
			p = s.indexOf('/');
			if ( p >= 0 )
				return c + (p > 0 ? '/' : '') + s;
			else
				if ( b_thumb )
					return c+'/p0/'+s.substr(k-3,3)+'/'+s+'.gif';
				else
					return c+'/p1/'+s.substr(k-3,3)+'/'+s+'.jpg';
		}
		return '';
	},

	getCoord : function(ev,e)
	{
		var a = {x:0,y:0};
		if ( typeof ev.pageY == 'undefined' && typeof ev.clientX == 'number' && document.documentElement )
		{
			a.x = ev.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
			a.y = ev.clientY + document.body.scrollTop + document.documentElement.scrollTop;
		}
		else
		{
			a.x = ev.pageX;
			a.y = ev.pageY;
		}

		if ( e.offsetParent )
		{
			a.x -= e.offsetLeft;
			a.y -= e.offsetTop;
			while ( e = e.offsetParent )
			{
				a.x -= e.offsetLeft;
				a.y -= e.offsetTop;
			}
		}
		
		return a;
	}
};

/* --------------------------------------------------------------------- */

