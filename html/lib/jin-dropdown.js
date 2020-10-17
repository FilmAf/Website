/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DropDown =
{
	_option_syntax : 0, // gn_option_syntax

	getSelIndex : function(e)
	{
		return (e = $s(e)) ? e.selectedIndex : -1;
	},

	getSelValue : function(e)
	{
		return (e = $s(e)) ? e.options[e.selectedIndex].value : '';
	},

	selectFromIndex : function(e,i)
	{
		if ( (e = $s(e)) && e.options && i >= 0 && i < e.options.length )
			return e.options[i].selected = true;
		return false;
	},

	selectFromVal : function(e,v)
	{
		if ( (e = $s(e)) && e.options && (v = DropDown.indexFromVal(e,v)) >= 0 )
			return e.options[v].selected = true;
		return false;
	},

	indexFromVal : function(e,v) // indexFromSelect
	{
		if ( (e = $s(e)) && (e = e.options) )
			for ( var i = 0 ; i < e.length ; i++ )
				if ( e[i].value == v )
					return i;
		return -1;
	},

	replaceOptions : function(trg,src) // createOptions
	{
		function addChildren(t,s)
		{
			for( s = s.firstChild  ;  s  ;  s = s.nextSibling )
			{
				switch ( s.tagName.toLowerCase() )
				{
				case 'a':
					h = s.href.match(/^http\:\/\/([^\/]+)\/([^?&]+)/i);
					if ( h && h[2] && h[2] != 'trash-can' && h[1].substr(0,4) != 'www.' )
					{
						DropDown.addOption(t, h[2], h[2]);
						if ( h[2] == Filmaf.lastMoveFolder ) t.selectedIndex = n;
						n++;
					}
					break;
				case 'li':
				case 'ul':
					addChildren(t,s);
					break;
				}
			}
		};

		var t = $s(trg),
		    s = $s(src),
			n = 0,
			h;

		if ( t && s )
		{
			DropDown.removeOptions(t);
			addChildren(t,s);
		}
	},

	removeOptions : function(e) // removeAllOptions
	{
		e = $s(e);
		while ( e.length > 0 ) e.remove(0);
	},

	addOption : function(sel,val,txt) // addOption
	{
		if ( sel && sel.add )
		{
			var f = document.createElement('option');

			f.value = val;
			f.text  = txt;
			if ( DropDown._option_syntax == 1 ) sel.add(f,null); else
			if ( DropDown._option_syntax == 2 ) sel.add(f); else
			try
				{ sel.add(f,null); DropDown._option_syntax = 1; }
			catch (ex)
				{ sel.add(f); DropDown._option_syntax = 2; }
		}
	}
};

/* --------------------------------------------------------------------- */

