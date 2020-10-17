/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DragHandler =
{
	_oElem : null,

	attach : function(oElem)
	{
		oElem.onmousedown = DragHandler._dragBegin;
		oElem.dragBegin = new Function();
		oElem.drag = new Function();
		oElem.dragEnd = new Function();
		return oElem;
	},

	_dragBegin : function(e)
	{
		var oElem = DragHandler._oElem = this;
		if (isNaN(parseInt(oElem.style.left))) { oElem.style.left = '0px'; }
		if (isNaN(parseInt(oElem.style.top))) { oElem.style.top = '0px'; }
	
		var x = parseInt(oElem.style.left);
		var y = parseInt(oElem.style.top);
	
		e = e ? e : window.event;
		oElem.mouseX = e.clientX;
		oElem.mouseY = e.clientY;
	
		oElem.dragBegin(oElem, x, y);
	
		document.onmousemove = DragHandler._drag;
		document.onmouseup = DragHandler._dragEnd;
		return false;
	},
	
	_drag : function(e)
	{
		var oElem = DragHandler._oElem;
		var x = parseInt(oElem.style.left);
		var y = parseInt(oElem.style.top);
	
		e = e ? e : window.event;
		oElem.style.left = x + (e.clientX - oElem.mouseX) + 'px';
		oElem.style.top = y + (e.clientY - oElem.mouseY) + 'px';
	
		oElem.mouseX = e.clientX;
		oElem.mouseY = e.clientY;
	
		oElem.drag(oElem, x, y);
		return false;
	},
	
	_dragEnd : function()
	{
		var oElem = DragHandler._oElem;
		var x = parseInt(oElem.style.left);
		var y = parseInt(oElem.style.top);
	
		oElem.dragEnd(oElem, x, y);
	
		document.onmousemove = null;
		document.onmouseup = null;
		DragHandler._oElem = null;
	}
}

/* --------------------------------------------------------------------- */

