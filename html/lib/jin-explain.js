/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Explain =
{
	_asAlert : 0,

	attach: function()
	{
		var e = window.self.document.getElementsByTagName('img'),
			i, x;

		for( i = e.length  ; --i >= 0  ;  )
		{
			x = e[i];
			if ( x.id && x.id.substr(0,3) == 'ex_' )
			{
				Context.attach(x, false, 'menu-explain-pop');
				e.onmouseover	= function(ev){Img.mouseOver(ev,this,5);};
				e.onmouseout	= function(  ){Img.mouseOut(this,5);};
//				e.onclick		= function(ev){Img.click(ev,this,5,this.id.substr(3));};
			}
		}
	},

	show : function(s,b_popup) // explain
	{
		Explain._asAlert = b_popup ? 0 : 1;

		s = s.replace(/_[0-9]+$/,'').replace(/^ex_/,'');
		Ajax.asynch('explain', 'Explain.__paint', '?s='+s, 0, 100);

		var e = $('explain_div');
		if ( e ) e.innerHTML = '&nbsp;';
	},

	__paint : function()
	{
		function parse(s, t)
		{
			var i, u, v;
			s = s.split('\t');
			for ( i = 0 ; i < s.length ; i += 2 )
			{
				u = s[i+1];
				v = Dom.decodeHtmlForInput(u);
				switch ( s[i] )
				{
				case 'keyword': t.keyword = u; break;
				case 'width':   t.width   = Dec.parse(u); break;
				case 'explain': t.explain = u; break;
				}
			}
			return typeof(t.explain) == 'string';
		};

		if ( Ajax.ready() )
		{
			var a  = Ajax.getLines(),
				b  = {}, e, s, w;

			if ( a.length >= 3 && parse(a[2], b) )
			{
				if ( b.explain == 'Not found' )
					s = 'Need to write "Explain" for ' + b.keyword + '.';
				else
					s = b.explain;
			}
			else
			{
				s = '"Explain" is experiencing problems.\nPlease try again soon or report the issue at http://dvdaf.net.\n\nSorry for the inconvenience.';
			}

			if ( ! Explain._asAlert && (e = $('explain_div')) )
			{
				e.innerHTML = "<div>"+s+
								"<div style='margin:4px 0 4px 0;text-align:right'><input type='button' value='close' onclick='Context.close()'></div>"+
							  "</div>";
			}
			else
			{
				alert(s);
			}
		}
	},

	nav : function(s) // explainNav
	{
		Explain.show(s,true);
	}
};

/* --------------------------------------------------------------------- */

