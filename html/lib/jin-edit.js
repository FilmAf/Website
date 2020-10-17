/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Edit =
{
	getStr : function(e)
	{
		return (e = $s(e)) ? e.value : '';
	},

	getStrCmp : function(e, o_changed)
	{
		var s = Edit.getStr(e);
		if ( ! o_changed.b_changed && (e = $('o_' + e.substr(2))) )
			o_changed.b_changed = s == e.value;
		return s;
	},

	getInt : function(e)
	{
		return Dec.parse(Edit.getStr(e));
	},

	getDbl : function(e)
	{
		return Dbl.parse(Edit.getStr(e));
	},

	setStr : function(e,s)
	{
		if ( (e = $s(e)) ) e.value = s;
	},


	setPrefixed : function(name,s)
	{
		var e;
		if ( (e = $('n_'+name)) ) e.value = s;
		if ( (e = $('o_'+name)) ) e.value = s;
		if ( (e = $('d_'+name)) ) e.innerHTML = s;
		return s;
	},

	countDown : function(e,n)
	{
		if ( (e = $(e)) )
		{
			if ( n > 50 )
				e.innerHTML = n;
			else
				if ( n < 0 )
					e.innerHTML = "<span style='color:#bd0b0b;font-weight:bold'>"+n+"</span>";
				else
					e.innerHTML = "<span style='font-weight:bold'>"+n+"</span>";
		}
	},

	insertAtCursor : function(e,v)
	{
		var a, b, s;

		if ( document.selection )
		{
			e.focus();
			s = document.selection.createRange();
			s.text = v;
		}
		else if ( e.selectionStart || e.selectionStart == '0' )
		{
			a = e.selectionStart;
			b = e.selectionEnd;
			e.value = e.value.substring(0,a) + v + e.value.substring(b, e.value.length);
		}
		else
		{
			e.value += v;
		}
	},

	getSel : function(e)
	{
		var s = '';

		if (window.getSelection)
			s = window.getSelection().toString();
		else if (document.getSelection)
			s = document.getSelection().toString();
		else if (document.selection)
			s = document.selection.createRange().text;

		if ( ! s && e )
		{
			e.focus();
			s = Edit.getSel(null);
		}
		return s;
	}
};

/* --------------------------------------------------------------------- */

