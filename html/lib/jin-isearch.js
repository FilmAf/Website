/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var iSearch =
{
	_lastPop : '',
	_lastPreviewReq : '',
	_lastLoaded : '',

	attach : function(e)
	{
		if ( (e = $s(e)) ) e.onkeyup = function(ev) { return iSearch.onkeyup(ev,this); };

		if ( ! $('div_isearch') )
			iSearch._createDiv();
	},

	getLastPopRef : function()
	{
		return iSearch._lastPop;
	},

	selectorChange : function(key)
	{
		if ( iSearch._lastPop )
		{
			switch ( key )
			{
			case 'has':
			case 'dir':
			case 'pub':
			case 'xcmy':
			case 'reg':
			case 'med':
				iSearch._lastLoaded = '';
				iSearch._lastPreviewReq = '';
				iSearch.onkeyup(null, $(iSearch._lastPop));
				break;
			default:
				iSearch.close();
			}
		}	
	},

	_createDiv : function()
	{
		var d = document.createElement('div'),
			s = d.style,
			b = document.getElementsByTagName('body').item(0);

		d.id			= 'div_isearch';
		s.visibility	= 'hidden';
		s.position		= 'absolute';
		s.top			= 0;
		s.left			= 0;
		s.zIndex		= 5;
		s.padding		= '0 10px 10px 0';
		s.filter		= 'shadow(color:gray,strength:10,direction:135)';

		b.appendChild(d);
		d.innerHTML =	"<div style='background:#ffffff'>"+
						  "<div style='border:1px solid #b1d3ec'>"+
						    "<table width='540'>"+
							  "<tr>"+
								"<td style='width:220px;vertical-align:top'>"+
								  "<iframe width='220' height='486' scrolling='yes' id='frame_isearch'></iframe>"+
								"</td>"+
								"<td style='vertical-align:top;text-align:center'>"+
								  "<img id='ex_www_isearch' src='http://dv1.us/d1/00/bq00.png' alt='Explain' style='float:right;position:relative;top:2px;left:2px;width:17px;height:17px;margin-right:2px' />"+
								  "<div style='padding-top:4px'>"+
									"<a href='javascript:void(iSearch.close())' style='padding-left:6px'>close</a>"+
									"<a href='javascript:void(iSearch.disable())' style='padding-left:8px'>disable</a>"+
									"<a href='javascript:void(iSearch.previewPage(-2))' style='padding-left:36px'>first</a>"+
									"<a href='javascript:void(iSearch.previewPage(-1))' style='padding-left:8px'>prev</a>"+
									"<a href='javascript:void(iSearch.previewPage(1))' style='padding-left:8px'>next</a>"+
									"<a href='javascript:void(iSearch.cookBig(0))' style='padding-left:36px'>[o]</a>"+
									"<a href='javascript:void(iSearch.cookBig(1))' style='padding-left:4px'>[O]</a>"+
								  "</div>"+
								  "<div id='crit_isearch'>"+
								  "</div>"+
								"</td>"+
							  "</tr>"+
						    "<table>"+
						  "</div>"+
						"</div>";
	},

	onkeyup : function(ev,e)
	{
		var n = e.id.substr(0,3) == 'str' ? Dec.parse(e.id.substr(3)) : -1,
			s = Str.trimL(e.value),
			c;

		if ( /[<>,|]/.test(s) )
		{
			iSearch.close();
		}
		else
		{
			if ( n >= 0 && s != '' && (s = s.replace(/</g,'%3C').replace(/>/g,'%3D').replace(/=/g,'%3E').replace(/\x20$/,'/').replace(/\x20+/g,'+')) != '' )
			{
				c = Search.getLinOpt(n);
				r = Search.getRegOpt();
				m = Search.getMedOpt();
				switch ( c )
				{
				case 'has':
				case 'dir':
				case 'pub':
					r = r == 'all' || r == '' ? '' : '&reg='+r;
					m = m == 'all' || m == '' ? '' : '&med='+m;
					s = '?what=counts&key='+c+'&val='+s+r+m+'&target='+n;

					Ajax.asynchNoDup('isearch', 'iSearch.__paint', s, 0, 500);
					break;
				}
			}
		}
		return true;
	},

	_getPrevIframe : function()
	{
		return Win.findFrame('frame_isearch');
	},

	_getUniq : function(obj,nocase)
	{
		return (obj ? obj : 'V') + '|' + nocase;
	},

	_parseHeader : function(s,t)
	{
		t.key  = Ajax.statusTxt(s,'key'	  );
		t.val  = Ajax.statusTxt(s,'val'	  );
		t.reg  = Ajax.statusTxt(s,'reg'	  );
		t.med  = Ajax.statusTxt(s,'med'	  );
		t.obj  = Ajax.statusTxt(s,'obj'	  );
		t.pg   = Ajax.statusTxt(s,'pg'	  ); if ( t.pg == '' ) t.pg = 1;
		t.row  = Ajax.statusTxt(s,'row'	  );
		t.last = Ajax.statusTxt(s,'last'  );
		t.trg  = Ajax.statusTxt(s,'target');
		t.cnt  = Ajax.statusTxt(s,'count' );
		return Ajax.statusErr(t,s);
	},

	_parsePaint : function(s,t)
	{
		var i;
		s = s.split('\t');
		if ( s.length == 4 )
		{
			t.obj     = s[1];
			t.matches = s[2];
			t.expand  = ((t.obj == 'D' || t.obj == 'P') && s[3] == 'N') ? 1 : '';
			s		  = s[0];
			i		  = s.substr(0,2) == '/ ' ? 2 : 0;
			t.nocase  = s.substr(i, s.length - 2 - i);
			t.title   = Str.ucTitle(t.nocase);
			return true;
		}
		return false;
	},

	_parseIframeTag : function(s,t)
	{
		if ( (s = s.split('|')).length == 8 )
		{
			t.key	  = s[0];
			t.reg	  = s[1];
			t.med	  = s[2];
			t.nocase  = s[3];
			t.obj	  = s[4];
			t.matches = s[5];
			t.expand  = s[6];
			t.row	  = s[7];
			return t.key != '' && t.nocase != '';
		}
		return false;
	},

	_getObjTypeSpan : function(s,b_long)
	{
		switch ( s )
		{
		case 'A': s = b_long ? 'ASIN '      : "<span class='ig'>ASIN: </span>"; break;
		case 'I': s = b_long ? 'imdb '      : "<span class='ig'>imdb: </span>"; break;
		case 'U': s = b_long ? 'UPC '       : "<span class='ig'>UPC: </span>"; break;
		case 'P': s = b_long ? 'Publisher ' : "<span class='ig'>Pub: </span>"; break;
		case 'D': s = b_long ? 'Director '  : "<span class='ig'>Dir: </span>"; break;
		default:  s = ''; break; // do nothing for 'V'
		}
		return s;
	},

	__paint : function()
	{
		if ( Ajax.ready() )
		{
			var a = Ajax.getLines(),
				t = a.length,
				b = {},
				x = {},
				s = '',
				f = 0,
				e, i, c, z, y, r;

			if ( iSearch._parseHeader(a[1],b) )
			{
				iSearch._lastPop = 'str' + b.trg;
				$(iSearch._lastPop).setAttribute('zoom_hoz','leftoffset');
				Pop.open('div_isearch', iSearch._lastPop, 542, 492, 0);

				if ( (f = iSearch._getPrevIframe()) )
				{
					if ( t >= 2 && iSearch._parseHeader(a[1],b) )
					{
						for ( i = 2, r = 1 ; i < t && iSearch._parsePaint(a[i],x) ; i++, r++ )
						{
							c = i % 2 ? 'id' : 'ie';
							z = iSearch._getObjTypeSpan(x.obj,0);
							y = b.key+'|'+b.reg+'|'+b.med+'|'+x.nocase+'|'+x.obj+'|'+x.matches+'|'+x.expand+'|pr'+r;
							s +=  "<div id='pr"+r+"' class='"+c+"'>"+
									"<a href='javascript:void(0)' onclick='select(this)' onmouseover='preview(this)' onmouseout='reset()' isearch='"+y+"' class='if'>"+
									  z + x.title + " <span class='ih'> (" + x.matches + ")</span>"+
									"</a>"+
								  "</div>";
						}
						if ( (z = b.msg) )
						{
							x = '';
							if ( z.indexOf('stopping at') >= 0 )
								x = z.replace(/ matches/,' lines');
							else
								if ( z == 'no matches found' )
									x = 'no results found';
							if ( x )
							{
								c = i % 2 ? 'id' : 'ie';
								s +=  "<div class='"+c+"' style='text-align:center'><span class='ii'>... "+x+" ...</span></div>";
							}
						}

						var trg = Filmaf.inputLine;

						f.write("<html>"+
								  "<head>"+
									"<link rel='stylesheet' type='text/css' href='/styles/00/filmaf.css' />"+
									"<script language='javascript' type='text/javascript'>"+
									  "function select(e) { if ( e && (e = e.getAttribute('isearch')) && e ) parent.iSearch.select(e); };"+
									  "function preview(e) { if ( e && (e = e.getAttribute('isearch')) && e ) parent.iSearch.preview(e,0); };"+
									  "function reset() { parent.iSearch.resetPreview(); };"+
									"</script>"+
								  "</head>"+
	//							  "<body style='background:#f0f8ff'>"+s+"</body>"+
								  "<body style='background:#ffffff'>"+s+"</body>"+
								"</html>");
						f.close();
					}
				}
				if ( e = $('crit_isearch') )
				{
					e.innerHTML = "<div class='hl' style='padding:60px 20px 0 20px'>"+
									"<div style='padding-bottom:30px'>Move your mouse over a keyword on the left for a preview in this window.</div>"+
									"<div>Make your selection by clicking either on the keyword or on a picture.</div>"+
								  "</div>";
					iSearch._lastLoaded = '';
				}
			}
		}
	},

	__expand : function()
	{
		if ( Ajax.ready() )
		{
			var a = Ajax.getLines(),
				t = a.length,
				b = {},
				x = {},
				o, i, f, e, s, y, z, id;

			if ( t >= 2 && iSearch._parseHeader(a[1],b) && b.row && (id = iSearch._getUniq(b.obj,b.val)) == iSearch._lastPreviewReq )
			{
				if ( (f = iSearch._getPrevIframe()) && (e = f.getElementById(b.row)) )
				{
					iSearch._lastLoaded = id;

					s = "<div class='lowkey'>"+Dom.decodeHtmlEntities(e.innerHTML)+"</div>"+
						"<div style='padding-left:10px'>";

					for ( i = 2 ; i < t && iSearch._parsePaint(a[i],x) ; i++ )
					{
						z = iSearch._getObjTypeSpan(x.obj,0);
						y = b.key+'|'+b.reg+'|'+b.med+'|'+x.nocase+'|'+x.obj+'|'+x.matches+'||';
						s +=  "<div>"+
								"<a href='javascript:void(0)' onclick='select(this)' onmouseover='preview(this)' onmouseout='reset()' isearch='"+y+"' class='if'>"+
								  z + x.title + " <span class='ih'> (" + x.matches + ")</span>"+
								"</a>"+
							  "</div>";
					}

					e.innerHTML = s + "</div>";
					e.id = '';
				}
			}
		}
	},

	cookBig : function(b)
	{
		var e, p;

		if ( (b ? 'Y' : '') != Cookie.get('search_big') )
		{
			if ( (e = $('isearch_pg'  )) ) p = Dec.parse(e.value); else return;
			if ( b )
			{
				Cookie.set('search_big', 'Y');
				p = (p - 1) * 16 + 1;
			}
			else
			{
				Cookie.del('search_big');
				p = Math.floor((p - 1) / 16) + 1;
			}
			iSearch.preview(0,p);
		}
	},

	previewPage : function(add)
	{
		var e, p, b;
		
		if ( (e = $('isearch_pg'  )) ) p = Dec.parse(e.value);		else return;
		if ( (e = $('isearch_last')) ) b = Dec.parse(e.value) != 0;	else return;
		
		switch ( add )
		{
		case -2: if ( p > 1 ) { p = 1; iSearch.preview(0,p); } break;
		case -1: if ( p > 1 ) { p--;   iSearch.preview(0,p); } break;
		case 0:               {        iSearch.preview(0,p); } break;
		case 1:  if ( ! b   ) { p++;   iSearch.preview(0,p); } break;
		}
	},

	select : function(s)
	{
		var x = {},
			o = '',
			u = '',
			e;

		if ( iSearch._parseIframeTag(s,x) && x.nocase ) // y = b.key+'|'+b.reg+'|'+b.med+'|'+x.nocase+'|'+x.obj+'|'+x.matches+'|'+x.expand+'|pr'+r;
		{
			switch ( x.key )
			{
			case 'dir':
	        case 'pub':   o = x.key;  u = '//'; break;

			case 'has':
				switch ( x.obj )
				{
				case 'A': o = 'asin';           break;
				case 'I': o = 'imdb';           break;
				case 'U': o = 'upc';            break;
				case 'P': o = 'pub';  u = '//'; break;
				case 'D': o = 'dir';  u = '//'; break;
				default:  o = 'has';  u = '//'; break;
				}
				break;
			}

			if ( o )
			{
				Search.setLinTxt(0,x.nocase + u);
				Search.setLinOpt(0,o);
				if ( ! Search.isLinPined(1) ) Search.setLinTxt(1,'');
				if ( ! Search.isLinPined(2) ) Search.setLinTxt(2,'');
				if ( ! Search.isLinPined(3) ) Search.setLinTxt(3,'');
				iSearch.close();
				Search.submit();
			}
		}
	},

	selectPic : function(n)
	{
		if ( (n = Dec.parse(n)) )
		{
			n = '0000000' + n;
			n = n.substr(n.length - 7,7);
			Search.setLinTxt(0,n);
			Search.setLinOpt(0,'has');
			if ( ! Search.isLinPined(1) ) Search.setLinTxt(1,'');
			if ( ! Search.isLinPined(2) ) Search.setLinTxt(2,'');
			if ( ! Search.isLinPined(3) ) Search.setLinTxt(3,'');
			iSearch.close();
			Search.submit();
		}
	},

	preview : function(s,pg)
	{
		var d = 1000,
			x = {},
			id;

		if ( ! s )
		{
			// arriving from a pic size change or a request for a new page,
			// need to load s =	b.key+'|'+b.reg+'|'+b.med+'|'+x.nocase+'|'+x.obj+'|'+x.matches+'|'+x.expand+'|pr'+r;
			if ( ! (s = $('isearch_val' )) || ! (s = s.value) ) return;
			iSearch._lastLoaded = '';
			d = 200;
		}

		if ( iSearch._parseIframeTag(s,x) )
		{
			s = (x.expand						 ? '?what=expand'	: '?what=dvds'	)+
												   '&key='+x.key+
												   '&val='+x.nocase+
				(x.obj							 ? '&obj='+x.obj	: '&obj=V'		)+
				(x.reg							 ? '&reg='+x.reg	: ''			)+
				(x.med							 ? '&med='+x.med	: ''			)+
				(pg > 0							 ? '&pg=' +pg		: ''			)+
				(Cookie.get('search_big') == 'Y' ? '&single=1'		: ''			)+
				(x.expand						 ? '&row='+x.row	: ''			);

			id = iSearch._getUniq(x.obj,x.nocase);

			if ( iSearch._lastLoaded != id )
			{
				iSearch._lastPreviewReq = id;
				if ( x.expand )
					Ajax.asynchNoDup('isearch', 'iSearch.__expand' , s, 0, 200);
				else
					Ajax.asynchNoDup('isearch', 'iSearch.__preview', s, 0, d);
			}
		}
	},

	resetPreview : function()
	{
		iSearch._lastPreviewReq = '';
	},

	__preview : function()
	{
		function parse(s, t)
		{
			if ( (s = s.split('\t')).length == 9 )
			{
				//  a.dvd_id, x.pic_name, x.dvd_title, x.director, x.publisher, x.country, x.region_mask, x.film_rel_year, x.media_type
				t.dvd		= s[0];
				t.pic		= s[1] == '-' ? '' : s[1];
				t.title		= s[2];
				t.dir		= s[3] == '-' ? '' : s[3];
				t.pub		= s[4] == '-' ? '' : s[4];
				t.country	= s[5].substr(1,s[5].length-2);
				t.region	= s[6];
				t.year		= s[7];
				t.media		= s[8];
				return true;
			}
			return false;
		};

		function getCell(s,b)
		{
			var t = {}, i, x, p;

			if ( s && parse(s,t) )
			{
				p = t.pic ? ( b ? Img.getPicLoc(t.pic,0) : Img.getPicLoc(t.pic,1) ) : "http://dv1.us/d1/00/pic-no.gif";

				x = t.title.replace(/<br\x20?\/?>/g,'\n').split('\n');
				s = x[0];

				if  ( t.year > 0				 ) s += ' ('+t.year+')';
				for ( i = 1 ; i < x.length ; i++ ) s += '\n' + x[i];
				if  ( t.dir						 ) s += '\n' + t.dir;
				s = "<a href='javascript:void(parent.iSearch.selectPic("+t.dvd+"))'><img src='"+p+"' title='"+s+"' /></a>";
			}
			else
			{
				s = "<img src='http://dv1.us/d1/1.gif' style='height:90px;width:63px' />";
			}
			return "<td>"+s+"</td>";
		};

		if ( Ajax.ready() )
		{
			var a = Ajax.getLines(),
				t = a.length,
				e = $('crit_isearch'),
				s = '',
				b = {},
				i, y, id, z;

			if ( e && e.style.visibility != 'hidden' && t >= 1 )
			{
				if ( ! iSearch._parseHeader(a[1],b)   ) return;

				id = iSearch._getUniq(b.obj,b.val);
				if ( id != iSearch._lastPreviewReq ) return;
				iSearch._lastLoaded = id;

				if ( t >= 2 )
				{
					if ( Cookie.get('search_big') == 'Y' )
					{
						s += "<tr>"+getCell(a[2],1)+"</tr>";
						s  = "<table width='100%'>"+s+"</table>";
					}
					else
					{
						for ( i = 0 ; i < 16 ; i += 4 )
						{
							s += "<tr>"+
									getCell(i+2 < t ? a[i+2] : '',0)+
									getCell(i+3 < t ? a[i+3] : '',0)+
									getCell(i+4 < t ? a[i+4] : '',0)+
									getCell(i+5 < t ? a[i+5] : '',0)+
								 "</tr>";
						}
						s = "<table width='100%'>"+s+"</table>";
					}
					z = 'Matches';
				}
				else
				{
					s = '';
					z = b.pg > 1 ? 'No more matches' : 'No matches';
				}

				// y = b.key+'|'+b.reg+'|'+b.med+'|'+x.nocase+'|'+x.obj+'|'+x.matches+'|'+x.expand+'|pr'+r;
				y = b.key+'|'+b.reg+'|'+b.med+'|'+b.val+'|'+b.obj+'|||'; // nothing after obj from pic preview
				e.innerHTML = "<div class='ig' style='text-align:left;padding:10px 2px 4px 2px'>"+
								z + " for &quot;"+iSearch._getObjTypeSpan(b.obj,1)+Str.ucTitle(b.val)+"&quot;"+
								"<input type='hidden' id='isearch_val' value='"+y+"'>"+
								"<input type='hidden' id='isearch_pg' value='"+b.pg+"'>"+
								"<input type='hidden' id='isearch_last' value='"+b.last+"'>"+
							  "</div>"+s;
			}
		}
	},

	close : function()
	{
		Pop.close('div_isearch');
		iSearch._lastPop = '';
	},

	disable : function()
	{
		Cookie.amend('search','noisearch','1');
		iSearch.close();
		Search.loadPreferences();
		Search.paint(1,0);
	}
};

/* --------------------------------------------------------------------- */

