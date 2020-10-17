/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var ImgPop =
{
	_zoomImg		: null,
	_zoomDvd		: 0,
	_zoomSrc		: '',
	_related		: {},
	_onwership		: false,
	_footer			: false,
	skipShowOnWait	: false,
	menuContext		: '',

	setMenuText : function(e)
	{
		if ( Cookie.get('finder') == 'N' )
		{
			e.innerHTML = 'Edition Finder';
			ImgPop.menuContext = 'show';
		}
		else
		{
			e.innerHTML = 'Disable Edition Finder';
			ImgPop.menuContext = 'stop showing';
		}
	},

	show : function(e,ovr)
	{
		if (ImgPop.skipShowOnWait)
			return;

		var auto = Cookie.get('finder') != 'N';

		if ( e && (ovr || auto) )
		{
			var f = ImgPop.getImgDiv(),
				g = $('img_zoom1'),
				h = $('img_zoom2'),
				s = e.src,
				z = '',
				r, x, y, tx, ty, d, o, a, b;

			if ( f && g && h )
			{
				if ( /^http:\/\/([a-z]\.)?dv1.us\/p0\/[0-9]{3}\/[0-9]{6}-d[0-9]+\.gif$/.test(s) )
				{
					z  = s.replace(/p0/,'p1').replace(/gif/,'jpg');
					y  = 'http://dv1.us/d1/wait-big.gif';
					tx = 356;
					ty = 623;
					d  = 1;
					ImgPop._onwership = Filmaf.userCollection != '' && 
										(Filmaf.userCollection != Filmaf.viewCollection ||
										 typeof(Home) != "undefined");
				}
				if ( /^http:\/\/dv1.us\/usr\/[a-z-]\/[^\._]+_t\.jpg$/.test(s) ) //http://dv1.us/usr/a/ash_p.jpg
				{
					z  = s.replace(/_t\./,'_p.');
					y  = 'http://dv1.us/d1/wait.gif';
					tx = 356;
					ty = 289;
					d  = 0;	// not a dvd
					ImgPop._onwership = false;
				}

				if ( z && ImgPop._zoomSrc != z )
				{
					if ( (r = $('pop_enable')) )
					{
						if ( auto )
						{
							r.innerHTML = 'Disable Edition Finder';
							r.href = 'javascript:ImgPop.close(1)';
						}
						else
						{
							r.innerHTML = 'Enable Edition Finder';
							r.href = 'javascript:ImgPop.close(2)';
						}
					}

					ImgPop._zoomSrc = z; // loading begins (1 to display on 2nd call with a 2 amd 0 to show immediately)
					g.src = y;
					h.src = 'http://dv1.us/d1/1.gif';

					$('pop_text').innerHTML = '';
					Pop.open(f, e, tx, ty, d);

					ImgPop._zoomImg = new Image();
					ImgPop._zoomImg.onload = function(){ ImgPop._doneLoading(); };
					ImgPop._zoomImg.src = z;

					if ( d )
					{
						ImgPop._zoomDvd = Dec.parse(e.id.substr(3));
						Ajax.asynchNoDup('img-pop', 'ImgPop.__paint', '?dvd='+ImgPop._zoomDvd, 0, 250);
					}
				}
			}
		}
	},

	__paint : function()
	{
		var o   = {}; if ( ! Ajax.getParms(o) ) return;
		var dvd = Ajax.statusInt(o.line1,'dvd'),
			y   = {has:false,amz:'&nbsp;',price:'&nbsp;',imdb:'&nbsp;',media:'&nbsp;',cnt:'&nbsp;',pub:'&nbsp;',rel:'&nbsp;'}, s = '', x = {}, i, same = 0;

		if ( ImgPop._zoomDvd != dvd	) return;

		ImgPop._related.same		= '';
		ImgPop._related.mine		= '';
		ImgPop._related.mine_count	= 0;
		ImgPop._related.other		= '';
		ImgPop._related.other_count	= 0;
		ImgPop._related.dvd			= [];

		for ( i = 2 ; i < o.length && ImgPop._parsePaint(o.lines[i],x) ; i++ )
		{
			ImgPop._related.dvd[i-1] = x.dvd;
			if ( x.dvd == dvd )
			{
				y.dvd	 = x.dvd;
				y.pic	 = x.pic;
				y.folder = x.folder;
				y.has    = x.folder != '';
				y.amz	 = x.amz;
				y.price	 = x.price;
				y.imdb	 = x.imdb;
				y.media	 = x.media;
				y.cnt	 = x.cnt;
				y.pub	 = x.pub;
				y.rel	 = x.rel;
				ImgPop._paintGenAlternates(x,ImgPop._related,y.has ? 2 : 1);
			}
			else
			{
				ImgPop._paintGenAlternates(x,ImgPop._related, 0);
			}
		}

		s +=  "<div id='pop_text2'>";

		if ( ImgPop._onwership )
			if ( y.has )
				s += ImgPop.paintInColl(y.folder,'ImgPop.showAll()');
			else
				s += ImgPop._paintAlternates(ImgPop._related, ImgPop._related.mine_count > 0 ? 1 : 0);
		else
			s += ImgPop._paintAlternates(ImgPop._related, 0);

		s +=  "</div>"+
			  "<div style='overflow:hidden'>"+
				"<table class='img_bar' style='width:100%'>"+
				  "<tr>"+
					"<td class='img_bar' style='border-right:solid 2px #0059a6;border-top:solid 2px #0059a6'>"+y.media+"</td>"+
					"<td class='img_bar' style='border-left:solid 2px #0059a6;border-right:solid 2px #0059a6;border-top:solid 2px #0059a6'>"+y.price+"</td>"+
					"<td class='img_bar' style='border-left:solid 2px #0059a6;border-right:solid 2px #0059a6;border-top:solid 2px #0059a6'>"+y.rel+"</td>"+
					"<td class='img_bar' style='border-left:solid 2px #0059a6;border-right:solid 2px #0059a6;border-top:solid 2px #0059a6'>"+y.amz+"</td>"+
					"<td class='img_bar' style='border-left:solid 2px #0059a6;border-top:solid 2px #0059a6'>"+y.imdb+"</td>"+
				  "</tr>"+
				  "<tr>"+
					"<td class='img_bar' style='border-right:solid 2px #0059a6;border-top:solid 2px #0059a6'>"+y.cnt+"</td>"+
					"<td class='img_bar' style='border-left:solid 2px #0059a6;border-top:solid 2px #0059a6;overflow:hidden' colspan='4'>"+y.pub+"</td>"+
				  "</tr>"+
				"</table>"+
			  "</div>";
		
		$('pop_text').innerHTML = s;
		Pop.open('div_zoom', '', 0, 0, 2);
	},

	__paintExpand : function()
	{
		var o   = {}; if ( ! Ajax.getParms(o) ) return;
		var dvd = Ajax.statusInt(o.line1,'dvd'),
			x   = {}, i;

		ImgPop._related.same		= '';
		ImgPop._related.mine		= '';
		ImgPop._related.mine_count	= 0;
		ImgPop._related.other		= '';
		ImgPop._related.other_count	= 0;
		ImgPop._related.dvd			= [];

		for ( i = 2 ; i < o.length && ImgPop._parsePaint(o.lines[i],x) ; i++ )
		{
			ImgPop._related.dvd[i-1] = x.dvd;
			ImgPop._paintGenAlternates(x,ImgPop._related, x.dvd == dvd ? (x.folder != '' ? 2 : 1) : 0);		
		}

		$('pop_text').innerHTML = ImgPop._paintAlternates(ImgPop._related,2);
	},

	showAll : function()
	{
		$('pop_text2').innerHTML = ImgPop._paintAlternates(ImgPop._related,2);
		Pop.open('div_zoom', '', 0, 0, 2);
	},

	_paintGenAlternates : function(x,h,same)
	{
		var z = same ? '*' : '',
		f = '0000000' + x.dvd;
		f = f.substr(f.length-6,6);
		f = Filmaf.baseDomain+"/search.html?has="+f+"&init_form=str0_has_"+f;
		f = "<div class='img_float2'>"+
			  "<a id='b_"+x.dvd+"' class='dvd_pic' href='"+f+"'>"+
				"<img src='" + (x.pic == '-' ? 'http://dv1.us/d1/00/pic-no.gif' : Img.getPicLoc(x.pic,1)) + "' width='63' height='90' />"+
			  "</a> "+
			  "<div class='img_text'>" + z;
		z +=  "</div>"+
			"</div>";

		if (x.folder != '')
		{
			f += ImgPop._getFolderName(x.folder) + z;
			if (! same)
			{
				h.mine += f;
				h.mine_count++;
			}
		}
		else
		{
			f += x.mediaSm + '/' + x.cntSm + z;
			if (! same)
			{
				h.other += f;
				h.other_count++;
			}
		}
		if (same) h.same = f;
	},

	paintInColl : function(f,s)
	{
		return  "<div>"+
				  "<div><div style='float:right'><a href='javascript:"+s+"'>[+]</a></div>In your collection</div>"+
				  "<img src='http://dv1.us/d1/"+ImgPop._getFolderImg(f)+"' width='90px' height='90px' />"+
				  "<div class='img_text'>"+ImgPop._getFolderName(f)+"</div>"+
				"</div>";
	},

	_paintAlternates : function(h,mine)
	{
		var tit, cnt, str;

		switch (mine)
		{
		case 0:
			return "<div style='padding:0 4px 4px 4px'><div style='float:right'><a href='javascript:ImgPop.showAll()'>[+]</a></div>&nbsp;</div>";
			break;
		case 1:
			tit = "<div style='float:right'><a href='javascript:ImgPop.showAll()'>[+]</a></div>Alternate editions in your collection";
			cnt = h.mine_count;
			str = h.mine;
			break;
		case 2:
			tit = "Alternate editions";
			cnt = 1 + h.mine_count + h.other_count;
			str = h.same + h.mine + h.other;
			break;
		}

		if (! cnt) return '';

		var w = cnt * (63 + 22),
			t = cnt > 4 ? " style='overflow-x:scroll'" : '',
			c = cnt > 4 ? " ("+cnt+")" : '';

		return  "<div style='padding:0 4px 2px 4px'>"+
				  "<div style='padding-bottom:2px'>"+tit+c+"</div>"+
				  "<div"+t+">"+
					"<div style='height:104px;width:"+w+"px'>"+str+"</div>"+
				  "</div>"+
				"</div>";
	},

	_getFolderName : function(s)
	{
		switch ( s )
		{
		case 'owne': return 'owned';
		case 'wish': return 'wish&nbsp;list';
		case 'on-o': return 'on&nbsp;order';
		case 'work': return 'work';	
		case 'have': return 'have&nbsp;seen';
		}
		return '';
	},

	_getFolderImg : function(s)
	{
		switch ( s )
		{
		case 'owne': return 'chk-g.gif';
		case 'wish': return 'chk-r.gif';
		case 'on-o': return 'chk-b.gif';
		case 'work': return 'chk-y.gif';
		case 'have': return 'chk-z.gif';
		}
		return '1.gif';
	},

	_getMediaType : function(s,z)
	{
		switch ( s )
		{
		case '2': return z ? 'BD/D' : 'Blu-ray/DVD';
		case '3': return z ? 'BD3d' : 'Blu-ray 3D';
		case 'A': return z ? 'Aud'  : 'DVD Audio';
		case 'B': return z ? 'BD'   : 'Blu-Ray';
		case 'C': return z ? 'HD/D' : 'HD DVD/DVD';
		case 'D': return z ? 'D'    : 'DVD';
		case 'H': return z ? 'HD'   : 'HD DVD';
		case 'O': return z ? 'Ot'   : 'Other';
		case 'P': return z ? 'Hold' : 'Placeholder';
		case 'R': return z ? 'BD-R' : 'BD-R';
		case 'T': return z ? 'HD/D' : 'HD DVD/DVD';
		case 'V': return z ? 'D-R'  : 'DVD-R';
		}
		return '&nbsp;';
	},

	_parsePaint : function(s,t)
	{
		var i, n;

		s = s.split('\t');
		if ( s.length >= 10 )
		{
			t.dvd		= s[0];
			t.pic		= s[1];
			t.folder	= s[2];
			t.amz		= s[3];
			t.price		= s[4];
			t.imdb		= s[5];
			t.media		= s[6];
			t.cnt		= s[7];
			t.pub		= s[8];
			t.rel		= s[9].trim();

			t.amz		= t.amz != 0 ? "amz: <a href='"+Filmaf.baseDomain+"/rt.php?vd=amz"+t.dvd+"'>x</a>" : '&nbsp;';
			t.price		= t.price > 0 ? "<a href='"+Filmaf.baseDomain+"/rt.php?vd=amz"+t.dvd+"'>$"+t.price+"</a>" : '&nbsp;';
			t.rel		= t.rel != '-' ? t.rel.substr(0,4)+'-'+t.rel.substr(4,2)+'-'+t.rel.substr(6,2) : '&nbsp;';
			t.cnt		= t.cnt != '-' ? t.cnt.substr(1,t.cnt.length-2).toUpperCase() : '&nbsp;';
			t.cntSm		= t.cnt.substr(0,2);

			n			= '0000000'+t.dvd;
			n			= n.substr(n.length-7,7);
			t.mediaSm	= ImgPop._getMediaType(t.media,1);
			t.media		= "<a href='"+Filmaf.baseDomain+"/search.html?has="+n+"&init_form=str0_has_"+n+"'>"+ImgPop._getMediaType(t.media,0)+"</a>";

			if ( t.imdb )
			{
				n = t.imdb;
				t.imdb = 'imdb: ';
				for ( i = 0 ; i < n ; i++ )
					t.imdb += "<a href='"+Filmaf.baseDomain+"/rt.php?vd=imd"+t.dvd+"-"+i+"'>x</a> ";
			}
			else
			{
				t.imdb	= '&nbsp;';
			}
			return true;
		}
		return false;
	},

	getImgDiv : function()
	{
		var e= $('div_zoom');
		if ( e ) return e;

		var d = document.createElement('div'),
			s = d.style,
			b = document.getElementsByTagName('body').item(0);

		d.id			= 'div_zoom';
		s.visibility	= 'hidden';
		s.position		= 'absolute';
		s.top			= 0;
		s.left			= 0;
		s.zIndex		= 7;

		b.appendChild(d);
		d.innerHTML =
						"<div style='background:#0059a6'>"+
						  "<div style='border:1px solid #6c8ba6;padding:3px'>"+
							"<div style='background:#ffffff;text-align:center;width:348px'>"+
							  // header
							  "<div>"+
								"<table class='img_bar' style='width:100%'>"+
								  "<tr>"+
									"<td class='img_bar' style='border-bottom:solid 2px #0059a6;text-align:left'>"+
									  "<div style='float:right'><a href='javascript:ImgPop.close(0)'>Close</a></div>"+
									  "<a id='pop_enable' href='javascript:ImgPop.close(1)'>Disable Edition Finder</a>"+
									"</td>"+
								  "</tr>"+
								"</table>"+
							  "</div>"+
							  // picture
							  "<div style='padding:8px 0 4px 0'>"+
								"<img id='img_zoom1' src='http://dv1.us/d1/1.gif' onclick='ImgPop.close(0)' />"+
								"<img id='img_zoom2' src='http://dv1.us/d1/1.gif' onclick='ImgPop.close(0)' />"+
							  "</div>"+
							  // collection match and footer
							  "<div id='pop_text'></div>"+
							"</div>"+
						  "</div>"+
						"</div>";
		return d;
	},

	close : function(p) // closeImgPop
	{
		//  p = 0 just close
		//  p = 1 close and set cookies not to open
		//  p = 2 close and set cookies to open

		if ( Pop.close('div_zoom') )
		{
			ImgPop._zoomImg = null;
			ImgPop._zoomSrc = '';
		}

		switch ( p )
		{
		case 1: Cookie.set('finder', 'N'); break;
		case 2: Cookie.del('finder');      break;
		}
	},

	_doneLoading : function()
	{
		var g = $('img_zoom1'),
			h = $('img_zoom2');;

		if ( g && h )
		{
			g.src = 'http://dv1.us/d1/1.gif';
			h.src = ImgPop._zoomImg.src;
		}
	}
};

/* --------------------------------------------------------------------- */

