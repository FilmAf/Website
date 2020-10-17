/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var MiniSearch =
{
	display : function(form, what) // miniSearch
	{
		var s = form[what+'_search'] ? form[what+'_search'].value : '',
			r = Dom.getChildrenByType(form, 'div');

		if ( r.length > 0 )
		{
			s = Str.trim(s).
				replace(/</g,'%3C').
				replace(/>/g,'%3D').
				replace(/=/g,'%3E').
				replace(/\x20+/g,'+');
			// can not use Filmaf.baseDomain or we will get a permission error
			if ( s )
			{
				s_get = '?what=' + what + '&parm=' + s + '&target=' + Filmaf.inputLine;
				Ajax.asynchNoDup('search-comp', 'MiniSearch.__search', s_get, 0, 1000);
			}
		}
		return true;
	},

	__search : function() // miniCallback
	{
		if ( Ajax.ready() )
		{
			var o = {}, mod = '', trg = '', cnt = '', c = '', s = '', a, i;
			
			if ( Ajax.getParms(o) )
			{
				mod = Ajax.statusTxt(o.line1,'what'  );
				trg = Ajax.statusTxt(o.line1,'target');
				cnt = Ajax.statusTxt(o.line1,'count' );
			}
			
			s   = "<html>"+
					"<head>"+
					  "<link rel='stylesheet' type='text/css' href='/styles/00/filmaf.css' />"+
					  "<script language='javascript' type='text/javascript'>"+
						"function html_entities_decode(s)"+
						"{"+
						"var d = document.createElement('div');"+
						"d.innerHTML = s;"+
						"return document.all ? d.innerText : d.textContent;"+
						"};"+
						"function retName(s)"+
						"{"+
						"s = html_entities_decode(s.innerHTML);"+
						"if ( s ) parent.setSearchVal('"+mod+"', '"+trg+"',s);"+
						"return false;"+
						"};"+
					"</script>"+
					"</head>"+
					"<body>";

			if ( o.err )
			{
				s += o.err;
			}
			else
			{
				for ( i = 2 ; i < o.length ; i++ )
				{
					c = i % 2 ? '' : " style='background-color:#e5f6e5'";
					if ( o.lines[i].substr(0,6) == '</div>' ) break;
					s += "<div"+c+"><a href='javascript:void(0)' onclick='retName(this)'"+c+">"+o.lines[i]+"</a></div>";
				}
				if ( o.msg )
				{
					s += "<div class='dvd_cmts'"+c+">&lt;"+o.msg+"&gt;</div>";
				}
			}
			s += '</body></html>';

			if ((a = Win.findFrame('frame_' + mod)))
			{
				a.write(s);
				a.close();
			}
		}
	}
};

/* --------------------------------------------------------------------- */

