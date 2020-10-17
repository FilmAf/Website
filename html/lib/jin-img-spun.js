/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var ImgSpun =
{
	attachSpun : function(e,cb)
	{
		e.onmouseover = function(ev){Img.mouseOver(ev,this,17);};
		e.onmousemove = function(ev){Img.mouseOver(ev,this,17);};
		e.onmousewheel= function(ev){ImgSpun._wheel(ev,this,17,this.id,cb);};
		e.onmouseout  = function(  ){Img.mouseOut(this,17);};
		e.onclick	  = function(ev){Img.click(ev,this,17,this.id);};
		e.ondblclick  = function(ev){if ( ! ev ) Img.click(ev,this,17,this.id);}; // only trigger for ie as Firefox sends both click and cblclick
	},

	_wheel : function(ev, img, type, s_id, cb)
	{
		ev || (ev = window.event);
		if ( ev.wheelDelta && (! Img._buttonsLoaded || ! img) ) return;

		var a = Img.buttons[type], o = ev.wheelDelta;

		if ( o != 0 && a.onClick ) a.onClick(s_id, o >= 0);
		// stop event propagatinon on IE (does not get fired in FireFox)
		ev.cancelBubble = true;
		ev.returnValue = false;
	},

	click : function(s,u)
	{
		$(s).spun(u);
	}
};

/* --------------------------------------------------------------------- */

