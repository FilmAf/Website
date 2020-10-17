/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Undo =
{
	_redo : {},

	setImg : function(x,b)
	{
		var e, f, n, z, s, t = '';

		s = x.id.substr(3);
		e = $('n_'+s);
		f = $('z_'+s);
		if ( ! e || ! f ) return;
		if ( e && e.type == 'radio' ) return;

		n = e ? (e.type == 'checkbox' ? (e.checked ? 'Y' : 'N') : e.value) : '';
		z = f ? f.value : '';
		// alert(e.id + '=[' + n + '] -- ' + f.id + '=[' + z + ']');

		if ( n != z )
		{
			s = b ? Img.buttons[7].b11.src : Img.buttons[7].b10.src;
			t = 'Undo';
		}
		else
		{
			if ( n == Undo._getRedo(s) )
			{
				s = Img.buttons[7].b00.src;
			}
			else
			{
				s = b ? Img.buttons[7].b21.src : Img.buttons[7].b20.src;
				t = 'Redo';
			}
		}

		if ( s != x.src ) x.src = s;
		x.title = t;
	},

	attach : function(x) // attachUndo
	{
		var e, i, n = 'n' + x.id.substr(2);

		if ( (e = $(n)) && e.id == n )
		{
			switch ( e.tagName )
			{
			case 'INPUT':
				switch ( e.type )
				{
				case 'text'    : e.onchange = e.onkeyup = e.onpaste = e.onblur = e.onmouseout = function(){Undo.change(this);}; break;
				case 'checkbox': e.onclick =						    function(){Undo.change(this);};							break;
				case 'hidden'  : 																							    break;
				default        : alert('Missing attachUndo:1('+e.type+') for ' + e.id);										    break;
				}
				break;

			case 'TEXTAREA'	 : e.onchange = e.onkeyup = e.onpaste = e.onblur = e.onmouseout = function(){Undo.change(this);};	break;
			case 'SELECT'	 : e.onchange =							  function(){Undo.change(this);};							break;
			default		 : alert('Missing attachUndo:2('+e.tagName+') for ' + e.id);											break;
			}
		}
	},

	set : function(e,v)
	{
		if ( (e = $s(e)) )
		{
			switch ( e.tagName )
			{
			case 'INPUT':
				switch ( e.type )
				{
				case 'text'	   : e.value = v; Undo.change(e);												break;
				case 'hidden'  : e.value = v; Undo.change(e); Undo._updateDecode('g'+e.id.substr(1), v);	break;
				case 'checkbox': e.checked = v != 'N'; Undo.change(e);										break;
				default	   : alert('Missing Undo.set('+e.type+') for ' + e.id);								break;
				}
				break;
			case 'TEXTAREA'	   : e.value = v; Undo.change(e);												break;
			case 'SELECT'	   : DropDown.selectFromVal(e,v); Undo.change(e);								break;
			default		   : alert('Missing Undo.set('+e.tagName+') for ' + e.id);							break;
			}

			if ( typeof(e.notifySet) == 'function' )
				e.notifySet();
		}
	},

	change : function(e)
	{
		e = e.type == 'radio' ? e.id.substr(1, e.id.length - 3) : e.id.substr(1);
		e = $('zi' + e);
		if ( e ) Undo.setImg(e, false);
	},

	click : function(s,u)
	{
		var n, z, g, nv, zv, ov;

		n = $('n_'+s);
		
		nv = n ? (n.type == 'checkbox' ? (n.checked ? 'Y' : 'N') : n.value) : '';
		z = $('z_'+s); zv = z ? z.value : '';
		g = $('g_'+s);
						 ov = Undo._getRedo(s);
		if ( n && z )
		{
			if ( nv != zv )
			{
				Undo._redo[s] = nv;
				Undo.set(n, zv);
				if ( g ) Undo.set(g, Decode.field(n.id, zv));
			}
			else
			{
				if ( nv != ov )
				{
					Undo.set(n, ov);
					if ( g ) Undo.set(g, Decode.field(n.id, ov));
				}
			}
		}
	},

	_getRedo : function(s)
	{
		if ( typeof(Undo._redo[s]) != "undefined" )
			return Undo._redo[s];

		s = $('o_'+s);
		return s ? (s.type == 'checkbox' ? (s.checked ? 'Y' : 'N') : s.value) : '';
	},

	_updateDecode : function(e,s)
	{
		if ( (e = $s(e)) )
			if ( (s = Decode.field(e.id,s)) )
				e.value = s;
	}
};

/* --------------------------------------------------------------------- */

