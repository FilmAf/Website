/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Search =
	{
		_categories	: ['has','pricele','pricege','asin','imdb','upc','dir','pub','pubct','genre','rel','reldt','year','lang','pic','src','created'],
		_context	: { has		  :'menu-title-has'		,
			pricele	  :'menu-priced-below'	,
			pricege	  :'menu-priced-above'	,
			asin	  :'menu-amz-asin'		,
			imdb	  :'menu-imdb'			,
			upc		  :'menu-upc'			,
			dir		  :'menu-director'		,
			pub		  :'menu-publisher'		,
			pubct	  :'menu-country'		,
			genre	  :'menu-genre'			,
			med		  :'menu-media'			,
			rel		  :'menu-released'		,
			reldt	  :'calendar'			,
			year	  :'menu-year'			,
			lang	  :'menu-language'		,
			pic		  :'menu-picture'		,
			src		  :'menu-source'		,
			created	  :'calendar'			},
		appending	: { cnt: false, gen: false, rel: false, lan: false, src: false, pic: false}, // used for helpers to populate search text control
		criteria	: { cnt: 'Publisher\'s Country', gen: 'Genre', rel: 'Release Status', lan: 'Language', src: 'Source', pic: 'Picture'}, // used for helpers to populate search text control
		calendars	: {},
		form		: 'q1',
		lines		: 4,
		// options loaded from cookies
		_myregion	: '',
		_mymedia	: '',
		_pins		: 0,
		_noisearch	: 0,
		_expert		: 0,
		_flipexcl	: 0,
		_more		: 0,
		_incmine	: 0,

		_getMore	: function()    { var m = $('more'); return m ? Dec.parse(m.value) : 0; },
		_getLines	: function()    { var n = Search._getMore(); return n ? Search.lines : 1; },
		_getLinesH	: function()    { return Search._expert ? Search.lines : 1; },
		getCatNdx   : function(v)   { return Arrays.indexFromVal(Search._categories, v); },

		isLinPined	: function(i)   { return Search._pins && Img.getPin('pin'+i); },
		isRegPined	: function()    { return Search._pins && Img.getPin('pinR' ); },
		isMedPined	: function()    { return Search._pins && Img.getPin('pinM' ); },
		isXmyPined	: function()    { return Search._pins && Img.getPin('pinX' ); },
		getRegOpt   : function()    { return DropDown.getSelValue('optR' ); },
		getMedOpt   : function()    { return DropDown.getSelValue('optM' ); },
		getLinOpt   : function(i)   { return DropDown.getSelValue('opt'+i); },

		setLinTxt   : function(i,s) { Edit.setStr           ('str'+i  ,s); },
		setLinOpt   : function(i,s) { DropDown.selectFromVal('opt'+i  ,s); },

		create : function()
		{
			Search.loadPreferences();
			Search.paint(0,1);
		},

		loadPreferences : function()
		{
			var c = Cookie.explode('search',0);

			Search._myregion  = c.myregion ? c.myregion : 'us';
			Search._mymedia   = c.mymedia ? c.mymedia : 'all';
			Search._pins      = c.pins || Cookie.get('pinned') ? 1 : 0;
			Search._noisearch = c.noisearch ? 1 : 0;
			Search._expert    = c.expert ? 1 : 0;
			Search._flipexcl  = c.flipexcl ? 1 : 0;
			Search._more      = c.more ? 1 : 0;
			Search._incmine   = c.incmine ? 1 : 0;
		},

		paint : function(b_ignore_parms, b_create)
		{
			var s_reg		= " Region "+
				"<select id='optR' onchange='Search.selectorChange(this)'>"+
				"<option value='us'>us</option>"+			"<option value='uk'>uk</option>"+		"<option value='eu'>eu,af</option>"+
				"<option value='la'>lam</option>"+			"<option value='as'>asia</option>"+		"<option value='sa'>sea</option>"+
				"<option value='jp'>jp</option>"+			"<option value='au'>au,nz</option>"+	"<option value='z'>0</option>"+
				"<option value='1'>1</option>"+				"<option value='1,a,0'>1,A,0</option>"+	"<option value='2'>2</option>"+
				"<option value='2,b,0'>2,B,0</option>"+		"<option value='3'>3</option>"+			"<option value='4'>4</option>"+
				"<option value='5'>5</option>"+				"<option value='6'>6</option>"+			"<option value='a'>A</option>"+
				"<option value='b'>B</option>"+				"<option value='c'>C</option>"+			"<option value='all'>all</option>"+
				"</select>"+
				Search._getPinCtrl('pinR',Search._pins),
				s_med		= " Media "+
					"<select id='optM' onchange='Search.selectorChange(this)'>"+
					"<option value='all'>all</option>"+
					"<option value='d,v'>dvd</option>"+
					"<option value='b,3,2,r'>blu-ray</option>"+
					"<option value='3'>blu-ray 3D</option>"+
					"<option value='h,c,t'>hd dvd</option>"+
					"<option value='a,p,o'>other</option>"+
					"<option value='f,s,l,e,n'>film</option>"+
					"</select>"+
					Search._getPinCtrl('pinM',Search._pins),
				s_mine		= (Filmaf.userCollection ? ' '+
					"<input type='checkbox' id='xcmy' />"+
					"<a href='javascript:void(Search.flipExclMine())'>"+
					"<img src='http://dv1.us/d1/00/"+(Filmaf.userCollection && Search._incmine ? 'be00' : 'be01')+".png' height='20' width='32' id='xcmyp' />"+
					"</a>"+
					Search._getPinCtrl('pinX',Search._pins) : ''),
				s_go		= "<a href='javascript:void(Search.submit())' title='Click here to perform the search'>Go</a>",
				s_clear		= "<a href='javascript:void(Search.clear())' title='Reset search to member preferences'>Clear</a>",
				s_options	= "<a href='javascript:void(0)' title='Present search preferences' id='edit_search'>Options</a>",
				e			= $('search'),
				b_expand	= Search._more && Search._expert,
				x = {}, s, td, b;

			if ( ! e ) return;

			b = Search._getParms(x, b_ignore_parms);
			if ( b && ! b_expand ) b_expand = 1;

			s =			"<form id='"+Search.form+"' name='"+Search.form+"' action='javascript:void(0)' method='get' onsubmit='return Search.submit()'>"+
				"<ul>"+
				"<li>"+
				Search._getSelCtrl(0,x.str[0],0)+' '+s_go+" / "+s_clear+" / "+s_options+s_reg+s_med+s_mine;

			if ( Search._expert || b_expand )
			{
				if ( b_expand )
				{
					s += 	  "<a href='javascript:void(Search._expand(0))' title='Show fewer search parameters'>&lt;&lt;Less</a>"+
						"<input type='hidden' id='more' value='1' />"+
						"</li>"+
						"<li>"+
						Search._getSelCtrl(1,x.str[1],0)+" / "+
						Search._getSelCtrl(2,x.str[2],0)+" / "+
						Search._getSelCtrl(3,x.str[3],0);
				}
				else
				{
					s +=	  "<a href='javascript:void(Search._expand(1))' title='Show more search parameters'>More&gt;&gt;</a>"+
						"<input type='hidden' id='more' value='0' />"+
						Search._getSelHidden(1,x.opt[1],x.str[1],x.pin[1])+
						Search._getSelHidden(2,x.opt[2],x.str[2],x.pin[2])+
						Search._getSelHidden(3,x.opt[3],x.str[3],x.pin[3]);
				}
			}
			e.innerHTML = s +
				"</li>"+
				"</ul>"+
				"</form>";

			if ( $(Search.form) )
			{
				DropDown.selectFromVal('optR',x.optR );
				DropDown.selectFromVal('optM',x.optM );
				DropDown.selectFromVal('opt0',x.opt[0] );
				if ( b_expand )
				{
					DropDown.selectFromVal('opt1',x.opt[1] );
					DropDown.selectFromVal('opt2',x.opt[2] );
					DropDown.selectFromVal('opt3',x.opt[3] );
				}
				if ( Search._pins )
				{
					Search._setPin('R',x.pinR,0);
					Search._setPin('M',x.pinM,0);
					Search._setPin('X',x.pinX,0);
					Search._setPin( 0 ,x.pin[0],1);
					if ( b_expand )
					{
						Search._setPin(1,x.pin[1],1);
						Search._setPin(2,x.pin[2],1);
						Search._setPin(3,x.pin[3],1);
					}
				}
				CheckBox.setVal('xcmy',x.xcmy);
				Search._disableXcmy(x.pinX);

				if ( ! b_create )
					setTimeout("Search.setup(0)",200);
			}
		},

		_setPin : function(e,v,b_txt)
		{
			var tx, bk, f;

			Img.setPin('pin'+e,v);

			v  = v != false;
			tx = v ? '#999999' : '#0066b2';
			bk = v ? '#eeeeee' : '#ffffff';

			if ( (f = $('opt'+e)) )
			{
				f.disabled = v;
				f.style.color = tx;
				f.style.background = bk;
			}
			if ( b_txt && (f = $('str'+e)) )
			{
				f.readOnly = v;
				f.style.color = tx;
				f.style.background = bk;
			}
		},

		_disableXcmy : function(v)
		{
			if ( (e = $('xcmy')) )
			{
				e.disabled = v = v != false;
				e.style.color = v ? '#999999' : '#0066b2';
			}
		},

		_getSelCtrl : function(i,s,pin)
		{
			return  "<select onchange='Search.selectorChange(this)' id='opt"+i+"'>"+
				"<option value='has'>title</option>"+
				"<option value='pricele'>priced below</option>"+
				"<option value='pricege'>priced above</option>"+
				"<option value='asin'>Amz ASIN</option>"+
				"<option value='imdb'>imdb</option>"+
				"<option value='upc'>UPC</option>"+
				"<option value='dir'>director</option>"+
				"<option value='pub'>publisher</option>"+
				"<option value='pubct'>pub country</option>"+
				"<option value='genre'>genre</option>"+
				"<option value='med'>media type</option>"+
				"<option value='rel'>release status</option>"+
				"<option value='reldt'>release dt</option>"+
				"<option value='year'>year</option>"+
				"<option value='lang'>language</option>"+
				"<option value='pic'>picture status</option>"+
				"<option value='src'>source</option>"+
				"<option value='created'>created dt</option>"+
				"</select> "+
				"<input type='text' size='12' title='Type your search string here' autocomplete='off' onkeypress='return Search.submitOnEnter(event)' zoom_ver='down' id='str"+i+"' value='"+s+"' />"+
				"<input type='hidden' value='' id='hid"+i+"' />"+ // used at least for calendar input
				"<img src='http://dv1.us/d1/00/bm00.gif' onmouseover='Img.mouseOver(event,this,3)' onmouseout='Img.mouseOut(this,3)' height='14' width='14' alt='help' id='hp"+i+"' />"+
				Search._getPinCtrl('pin'+i, pin);
		},

		_getSelHidden : function(i,opt,str,pin)
		{
			return "<input type='hidden' id='opt"+i+"' value='"+opt+"' /><input type='hidden' id='str"+i+"' value='"+str+"' />";
		},

		_getPinCtrl : function(id,b)
		{
			return b ? "<img src='http://dv1.us/d1/00/bp00.gif' onmouseover='Img.mouseOver(event,this,0)' onmouseout='Img.mouseOut(this,0)' onclick='Img.click(event,this,0,0)' height='15' width='17' alt='pin' id='"+id+"' />" : '';
		},

		setup : function(b_calendar)
		{
			if ( b_calendar ) SearchCalendar.setup();
			Search._attachHelpers();
			Search.focus();
		},

		_attachHelpers : function()
		{
			for ( var i = 0 ; i < Search._getLines() ; i++ )
				Search._attachHelper(i, Search.getLinOpt(i));
			Context.attach('edit_search', false, 'menu-search-options');
		},

		_attachHelper : function(i,c)
		{
			Context.attach('hp' +i, false, Search._context[c], i);
			if ( ! Search._noisearch && i == 0 )
				iSearch.attach('str'+i, c);
		},

		_expand : function(b_expand)
		{
			Search._more = b_expand ? 1 : '';
			Cookie.amend('search','more', Search._more);
			Search.paint(1,0);
		},

		_getParms : function(x, b_ignore_parms)
		{
			if ( b_ignore_parms )
			{
				Search._loadCtrlParms(x,1);
			}
			else
			{
				Search._clearParms(x);

				var s = Url.getVal('init_form');
				if ( s )
				{
					x.optR = 'all';
					Search._loadStrParms(x, Url.getVal('init_form'),0);
				}
				if ( Search._pins )
					Search._loadStrParms(x, Cookie.get('pinned'),1);
				if ( x.str[1] || x.str[2] || x.str[3] )
					return true; // force expansion
			}
			return false;
		},

		_clearParms : function(x)
		{
			x.optR = Search._myregion;
			x.optM = Search._mymedia;
			x.xcmy = 0;
			x.pinR = 0;
			x.pinM = 0;
			x.pinX = 0;
			x.opt  = [0,0,0,0];
			x.str  = ['','','',''];
			x.pin  = [0,0,0,0];
		},

		_loadStrParms : function(x, c, b_pinned)
		{
			var i, a;
			// decodeURIComponent
			// s = s.replace(/\+/g,' ').replace(/\%3[Cc]/g,'<').replace(/\%3[Dd]/g,'>').replace(/\%3[Ee]/g,'=');

			c = Cookie.explodeStr(c,1);

			for ( i = 0 ; i < c.length ; i++ )
			{
				a = c[i].val.split('_');
				switch ( c[i].key )
				{
					case 'rgn':  Search._prepParm(a,0); x.optR   = a[0];				  if ( b_pinned ) x.pinR   = 1; break;
					case 'med':  Search._prepParm(a,0); x.optM   = a[0];				  if ( b_pinned ) x.pinM   = 1; break;

					case 'xcmy': Search._prepParm(a,0);
						switch ( a[0] )
						{
							case '1': x.xcmy = 1; Search._incmine = 0; break;
							case '2': x.xcmy = 1; Search._incmine = 1; break;
							case '0': x.xcmy = 0; break;
						}
						if ( b_pinned ) x.pinX   = 1;
						break;

					case 'str0': Search._prepParm(a,1); x.opt[0] = a[0]; x.str[0] = a[1]; if ( b_pinned ) x.pin[0] = 1; break;
					case 'str1': Search._prepParm(a,1); x.opt[1] = a[0]; x.str[1] = a[1]; if ( b_pinned ) x.pin[1] = 1; break;
					case 'str2': Search._prepParm(a,1); x.opt[2] = a[0]; x.str[2] = a[1]; if ( b_pinned ) x.pin[2] = 1; break;
					case 'str3': Search._prepParm(a,1); x.opt[3] = a[0]; x.str[3] = a[1]; if ( b_pinned ) x.pin[3] = 1; break;
				}
			}
		},

		_prepParm : function(a, b_opt)
		{
			var i = a.length > (b_opt ? 2 : 1) ? 1: 0; // accounts for older parms which had a pinned indication in the str
			if ( b_opt )
			{
				a[0] = a[i];
				a[1] = decodeURIComponent(a[i+1]).replace(/\+/g,' ').replace(/\%3[Cc]/g,'<').replace(/\%3[Dd]/g,'>').replace(/\%3[Ee]/g,'=');
			}
			else
			{
				a[0] = a[i];
			}
		},

		_loadCtrlParms : function(x,b_hidden)
		{
			var n = Search._getLines(),
				t = Search._getLinesH();

			Search._clearParms(x);

			x.optR = DropDown.getSelValue('optR');
			x.optM = DropDown.getSelValue('optM');
			x.xcmy = CheckBox.getVal_1   ('xcmy');
			for ( i = 0 ; i < n ; i++ )
			{
				x.opt[i] = DropDown.getSelValue('opt'+i);
				x.str[i] = Edit.getStr('str'+i);
				if ( Search._pins ) x.pin[i] = Search.isLinPined(i);
			}
			if ( b_hidden )
			{
				for (  ; i < t ; i++ )
				{
					x.opt[i] = Edit.getStr('opt'+i);
					x.str[i] = Edit.getStr('str'+i);
					x.pin[i] = 0;
				}
			}
			if ( Search._pins )
			{
				x.pinR = Search.isRegPined();
				x.pinM = Search.isMedPined();
				x.pinX = Search.isXmyPined();
			}
		},

		focus : function()
		{
			var e = $('str0');
			if ( e )
			{
				if ( e.focus  ) e.focus();
				if ( e.select ) e.select();
			}
		},

		submitOnEnter : function(ev)
		{
			var key = window.event ? window.event.keyCode : ( ev ? ev.which : null);
			if ( key == 13 )
			{
				Search.submit();
				return false;
			}
			return true;
		},

		setAppend : function(mode, s)
		{
			Search.appending[mode] = s;
		},

		isPinned : function()
		{
			if ( Search._pins )
			{
				if ( Search.isRegPined() || Search.isMedPined() || Search.isXmyPined() )
					return true;
				var n = Search._getLines();
				for ( i = 0 ; i < n ; i++ )
					if ( Search.isLinPined(i) && Edit.getStr('str'+i) != '' )
						return true;
			}
			return false;
		},

		clear : function()
		{
			var n = Search._getLines(),
				t = Search._getLinesH(),
				i;

			if ( !Search.isRegPined() ) DropDown.selectFromVal('optR',Search._myregion);
			if ( !Search.isMedPined() ) DropDown.selectFromVal('optM',Search._mymedia);
			if ( !Search.isXmyPined() ) CheckBox.setVal('xcmy',0);

			for ( i = 0 ; i < n ; i++ )
			{
				if ( !Search.isLinPined(i) )
				{
					DropDown.selectFromIndex('opt'+i,0);
					Edit.setStr('str'+i,'');
				}
			}
			for (  ; i < t ; i++ )
			{
				Edit.setStr('opt'+i,'has');
				Edit.setStr('str'+i,'');
				Edit.setStr('pin'+i,'0');
			}

			Search._attachHelpers();
			iSearch.close();
			setTimeout('Search.focus();',100);
//		setTimeout("$('str0').focus();",100);
		},

		flipExclMine : function()
		{
			if ( ! Search.isXmyPined() && Filmaf.userCollection )
			{
				if ( Search._flipexcl )
				{
					Search._incmine = Search._incmine ? 0 : 1;
					Cookie.amend('search','incmine', Search._incmine);
					$('xcmyp').src = Search._incmine ? 'http://dv1.us/d1/00/be00.png' : 'http://dv1.us/d1/00/be01.png';
				}
				else
				{
					CheckBox.setVal('xcmy', ! CheckBox.getVal_1('xcmy'));
				}
			}
		},

		selectorChange : function(e)
		{
			var s, i, o;
			switch ( e.id )
			{
				case 'optR': iSearch.selectorChange('reg' ); break;
				case 'optM': iSearch.selectorChange('med' ); break;
//		case 'xcmy': iSearch.selectorChange('xcmy'); break;
//		if we consider exclude, we should also consider incluses as well and the corresponding clicking of the img

				case 'opt0':
				case 'opt1':
				case 'opt2':
				case 'opt3':
					i = Dec.parse(e.id.substr(3,1));
					o = Search.getLinOpt(i);
					Search._attachHelper(i,o);

					if ( (s = iSearch.getLastPopRef()) && s.substr(3,1) == i )
						iSearch.selectorChange(o);
					break;
			}
		},

		setSearch : function(ctx, opt, val)
		{
			var n = Search._getLines(),
				i;

			Search.clear();
			for ( i = 0 ; val != '' && i < n ; i++ )
			{
				if ( !Search.isLinPined(i) )
				{
					DropDown.selectFromVal('opt'+i,opt);
					Edit.setStr('str'+i,val);
					val = '';
				}
			}
			if ( val != '' )
			{
				alert('Please make sure that at least one search strings remains\nunpinned when performing a contextual search.');
			}
			else
			{
				switch ( ctx )
				{
					case 'filmaf': Search.submit(); break;
					case   'mine': Search.submit(Filmaf.userCollection); break;
					case   'this': Search.submit(Filmaf.viewCollection); break;
				}
			}
		},

		savePin : function(id)
		{
			var t = Search._getLines(),
				s = '',
				b, c, x, i;

			if ( (b = Search.isRegPined()) ) s += '*rgn_'  + DropDown.getSelValue('optR');
			Search._setPin('R',b,0);

			if ( (b = Search.isMedPined()) ) s += '*med_'  + DropDown.getSelValue('optM');
			Search._setPin('M',b,0);

			if ( (b = Search.isXmyPined()) ) s += '*xcmy_' + (CheckBox.getVal_1('xcmy') ? (Search._incmine ? '2' : '1') : '0');
			Search._disableXcmy(b);

			for ( i = 0 ; i < t ; i++ )
			{
				if ( (b = c = Search.isLinPined(i)) )
				{
					x = Str.trim(Edit.getStr('str'+i).replace(/[\s]+/g,' ').replace(/[\x00-\x1F\x7F\x27]/g, '').replace(/[\x22#$()\*\+:;\?@\[\\\]\x5E_\x60{}~]/g, ' '));
					if ( x != null && x != '' )
					{
						x = s + '*str'+i + '_' + DropDown.getSelValue('opt'+i) + '_' + x;
						if ( x.length > 101 )
							b = 0;
						else
							s = x;
					}
					else
					{
						b = 0;
					}
					if ( ! b )
					{
						// unpin it
					}
				}
				Search._setPin(i,b,1);
			}

			s = s.substr(1);
			if ( s )
				Cookie.set('pinned', s);
			else
				Cookie.del('pinned');

			Ajax.asynch('pin', 'Ajax.__ignore', '?pin='+s); // review defaults
		},

		/* --------------------------------------------------------------------- *\
           construct the query string from a series of fields and pass it as a
           single GET parameter to the search page.
           Usage of non-encoded characters
           (0-9A-Za-z.=<> ) search strings in lower case
           (*) used for sub-field separator in init_form parameter
           (_) used for sub-field separator in init_form parameter
           (.) sub-field separator, operator delimiter and decimal in search string
           (|) to override AND with an OR
           (-) reserved
        \* --------------------------------------------------------------------- */

		submit : function(s_extra_where)
		{
			var n		 = Search._getLines(),
				s_week   = '',
				a_form	 = {},
				a_search = {},
				o_focus	 = null,
				o_value	 = null,
				b_fatal	 = false,
				s, i, a, b;

			if ( ! $(Search.form) ) return;

			// reset colors
			for ( i = 0 ; i < n ; i++ )
			{
				b = Search.isLinPined(i);
				a = $('str'+i);
				a.color      = b ? '#999999' : '#0066b2';
				a.background = b ? '#eeeeee' : '#ffffff';
			}

			// parse search strings
			for ( i = 0 ; i < n ; i++ )
				if ( ! Search.isLinPined(i) )
					ff_line_option('str'+i, Search.getLinOpt(i), $('str'+i));

			// parse where and excl mine
			if ( ! Search.isXmyPined() && (b = CheckBox.getVal_1('xcmy')) )
			{
				a_form['xcmy'] = Search._incmine ? '2' : '1';
				ff_queue('where', Search._incmine ? '=' : '!=',Filmaf.userCollection);
			}
			else
			{
				if ( s_extra_where )
					ff_queue('where','=',s_extra_where);
			}

			// parse region and media
			if ( ! Search.isRegPined() && (a = Search.getRegOpt()) && a != 'all' ) { a_form['rgn'] = a; ff_queue('rgn', '=', a); }
			if ( ! Search.isMedPined() && (a = Search.getMedOpt()) && a != 'all' ) { a_form['med'] = a; ff_queue('med', '=', a); }

			// do the search
			if ( ! b_fatal )
			{
				var s_search = '',
					s_form   = '',
					s_key;

				for ( s_key in a_form   ) s_form   += s_key + '_' + encodeURIComponent(a_form[s_key].replace(/\t/g,'_').replace(/\n/g,'*')) + '*';
				for ( s_key in a_search ) s_search += s_key + '=' + encodeURIComponent(a_search[s_key]) + '&';

				if ( s_search != '' )
				{
					s_form   = s_form.substr(0,s_form.length - 1).replace(/(\%20)+/g,'+').replace(/(\%25)+/g,'%2525'); // need to double escape % signs
					s_search = s_search.substr(0,s_search.length - 1).replace(/(\%20)+/g,'+');	// .replace(/&/g,'&amp;');
					s_search = s_search + '&init_form=' + s_form;
				}

				if ( Filmaf.hasRelWeek && s_search )
				{
					s = Url.getVal('week');
					if ( DateTime.isStrValid(s,1980,new Date().getFullYear() + 1 ) ) s_week = s + '&' + s_search; else
					if ( s == ''						  ) s_week = s_search;
				}

				if ( s_week   ) location.href = Filmaf.baseDomain + '/releases.html' + (s_week ? '?' + s_week : ''); else
				if ( s_search ) location.href = Filmaf.baseDomain + '/search.html?' + s_search;
			}
			return false;

			function ff_line_option(s_field, s_key, o_value)
			{
				var s_value = Str.trim(o_value.value.replace(/[\s]+/g,' ').replace(/[\x00-\x1F\x7F\x27]/g, '').replace(/[\x22#$()\*\+:;\?@\[\\\]\x5E_\x60{}~]/g, ' '));
				if ( s_value == null || s_value == '' ) return;
				o_value.value	= s_value;
				a_form[s_field]	= s_key + '\t' + s_value;
				o_focus = o_value;
				{
					var o_token = new Token(/([a-z_]*)\x20*([!=<>][!=<>]*)\x20*/, s_value, ff_valid_key, ff_norm_oper, s_key);
					while ( o_token.f_next() ) ff_queue(o_token.s_key, o_token.s_oper, o_token.s_token);
				}
				o_focus = null;
			};

			function ff_valid_key(a_keys, s_default_key)
			{
				if ( a_keys )
				{
					if ( a_keys[1] == '' )
					{
						switch ( s_default_key )
						{
							case 'pricege': case 'pricele': a_keys[1] = 'price';		break;
							default:						a_keys[1] = s_default_key;	break;
						}
					}
					switch ( a_keys[1] )
					{
						case 'has': case 'price': case 'asin': case 'imdb':
						case 'upc': case 'dir': case 'pub': case 'pubct': case 'genre':
						case 'rel': case 'reldt': case 'year': case 'lang': case 'pic':
						case 'src': case 'created': case 'rgn': case 'med':

						case 'ord':
							a_keys[2] = ff_norm_oper(a_keys[2]);
							if ( a_keys[2] ) return true;
							break;
					}
				}
				return false;
			};

			function ff_norm_oper(s)
			{
				switch ( s )
				{
					case '':   case '==': case '=' : return '=';
					case '=>': case '>=':		 return '>=';
					case '>' :			 return '>';
					case '=<': case '<=':		 return '<=';
					case '<' :			 return '<';
					case '<>': case '!=':		 return '!=';
				}
				return false;
			};

			function ff_alert(o_value, s_alert)
			{
				if ( o_value.b_warn && s_alert )
					Validate.warn(o_focus, o_value.b_focus, o_value.b_fatal, s_alert, false);
			};

			function ff_valid_tit(o_value, re_delimiter, f_validate)
			{
				var s = o_value.s_value, re = /([0-9])[,\.]([0-9]{3}([^0-9]|$))/, m;
				while ( (m = re.exec(s)) )
				{
					m = m.index;
					s = s.substr(0, m + 1) + s.substr(m + 2, s.length - m - 2);
				}
				o_value.s_value = s;
				return ff_valid_multi(o_value, re_delimiter, f_validate);
			};

			function ff_valid_multi(o_value, re_delimiter, f_validate)
			{
				var s_value_ori		= o_value.s_value,
					o_token			= new Token(re_delimiter, o_value.s_value, null, null, ''),
					b_no_candidates	= true,
					s_value			= '';
				while ( o_token.f_next() && ! o_value.b_fatal )
				{
					o_value.s_value = o_token.s_token;
					f_validate(o_value);
					s_value += o_value.s_value + ',';
					b_no_candidates = false;
				}
				if ( b_no_candidates )
				{
					o_value.s_value = s_value_ori;
					f_validate(o_value);
					o_value.b_fatal = true;
				}
				o_value.s_value = s_value != '' ? s_value.substr(0, s_value.length - 1) : '';
			};

			function ff_valid_one(o_value, s_kind)
			{
				var s_value = o_value.s_value.replace(/[^A-Za-z0-9-]/g, ''), // only a-z, 0-9 and -
					b_valid = false,
					s_match = '';

				if ( s_value != '' )
				{
					switch ( s_kind )
					{
						case 'genre':
							s_match = '-:action:action-comedy:action-crime:action-disaster:action-epic:action-espionage:action-martialarts:action-military:action-samurai:action-nosub:animation:animation-cartoons:animation-family:animation-mature:animation-puppetrystopmotion:animation-scifi:animation-superheroes:animation-nosub:anime:anime-action:anime-comedy:anime-drama:anime-fantasy:anime-horror:anime-mahoushoujo:anime-martialarts:anime-mecha:anime-moe:anime-romance:anime-scifi:anime-nosub:comedy:comedy-dark:comedy-farce:comedy-horror:comedy-romantic:comedy-satire:comedy-scifi:comedy-screwball:comedy-sitcom:comedy-sketchesstandup:comedy-slapstick:comedy-teen:comedy-nosub:documentary:documentary-biography:documentary-crime:documentary-culture:documentary-entertainment:documentary-history:documentary-nature:documentary-propaganda:documentary-religion:documentary-science:documentary-social:documentary-sports:documentary-travel:documentary-nosub:drama:drama-courtroom:drama-crime:drama-docudrama:drama-melodrama:drama-period:drama-romance:drama-sports:drama-war:drama-nosub:educational:educational-children:educational-school:educational-nosub:erotica:erotica-hentai:erotica-nosub:experimental:exploitation:exploitation-blaxploitation:exploitation-nazisploitation:exploitation-nunsploitation:exploitation-pinkueiga:exploitation-sexploitation:exploitation-shockumentary:exploitation-wip:exploitation-nosub:fantasy:filmnoir:'+
								'horror:horror-anthology:horror-creatureanimal:horror-espghosts:horror-eurotrash:horror-exploitation:horror-gialli:horror-goreshock:horror-gothic:horror-possessionsatan:horror-shockumentary:horror-slashersurvival:horror-vampires:horror-zombiesinfected:horror-otherundead:horror-nosub:music:music-liveinconcert:music-musicvideos:music-nosub:musical:performing:performing-circus:performing-concerts:performing-dance:performing-operas:performing-theater:performing-nosub:scifi:scifi-alien:scifi-alternatereality:scifi-apocalyptic:scifi-cyberpunk:scifi-kaiju:scifi-lostworlds:scifi-military:scifi-otherworlds:scifi-space:scifi-spacehorror:scifi-superheroes:scifi-utopiadystopia:scifi-nosub:short:silent:silent-animation:silent-horror:silent-melodrama:silent-slapstick:76800:silent-western:silent-nosub:sports:sports-baseball:sports-basketball:sports-biking:sports-fitness:sports-football:sports-golf:sports-hockey:sports-martialarts:sports-motorsports:sports-olympics:sports-skateboard:sports-skiing:sports-soccer:sports-tennis:sports-wrestling:sports-nosub:suspense:suspense-mystery:suspense-thriller:suspense-nosub:war:war-uscivilwar:war-wwi:war-wwii:war-korea:war-vietnam:war-postcoldwar:war-other:war-nosub:western:western-epic:western-singingcowboy:western-spaghetti:western-nosub:dvdaudio:other:other-digitalcomicbooks:other-gameshows:other-games:other-nosub:unspecifiedgenre:';
							break;
					}
					b_valid = s_match.indexOf(':'+s_value+':') > 0;
				}
				o_value.b_fatal = o_value.b_fatal || ! b_valid;
				o_value.s_value = s_value;
				return ! o_value.b_fatal;
			};

			function ff_valid_match(o_value, s_kind)
			{
				var s_value = o_value.s_value.replace(/[^A-Za-z0-9]/g, ''), // only a-z, 0-9
					s_match = '',
					n_count = 0;

				if ( s_value != '' )
				{
					switch ( s_kind )
					{
						case 'language':
							if ( s_value == 'missing' ) break;
							n_count = 2;
							s_match = '-:en:_english:am:_armenian:ar:_arabic:bg:_bulgarian:bn:_indianbengali:br:_portuguesebrazilian:br:_brazilian:ca:_frenchcanadian:cs:_chineseshanghainese:ct:_chinesecantonese:ct:_cantonese:cz:_czech:de:_german:de:_ge:dk:_danish:eo:_esperanto:es:_spanish:es:_sp:et:_estonian:fa:_farsi:fa:_persian:fi:_finnish:fr:_french:ge:_georgian:gr:_greek:he:_hebrew:hi:_indianhindi:hi:_hindi:ho:_chinesehokkien:ho:_hokkien:hu:_hungarian:id:_bahasaindonesia:id:_indonesia:il:_yiddish:in:_indianothers:is:_icelandic:it:_italian:jp:_japanese:kh:_khmer:kl:_klingon:kr:_korean:ku:_kurdish:kz:_kazakh:la:_latin:lt:_lithuanian:lv:_latvian:ma:_chinesemandarin:ma:_mandarin:mk:_macedonian:ml:_indianmalayalam:ml:_malayalam:mn:_mongolian:my:_bahasamalaysia:my:_malaysian:nl:_dutch:no:_norwegian:nz:_aramaic:ot:_other:ph:_filipino:pl:_polish:pt:_portuguese:pu:_indianpunjabi:pu:_punjabi:rm:_romani:ro:_romanian:ru:_russian:sc:_serbocroatian:se:_swedish:si:_slovenian:sk:_slovak:sl:_silent:ta:_indiantamil:ta:_tamil:te:_indiantelugu:te:_telugu:th:_thai:tr:_turkish:tw:_chinesetaiwanese:tw:_taiwanese:uk:_ukrainian:un:_unknown:ur:_indianurdu:ur:_urdu:ve:_catalan:vi:_vietnamese:';
							break;
						case 'country':
							if ( s_value == 'missing' ) break;
							n_count = 2;
							s_match = '-:us:_unitedstates:ar:_argentina:at:_austria:au:_australia:be:_belgium:br:_brazil:ca:_canada:ch:_switzerland:cl:_chile:cn:_china:cu:_cuba:cz:_czechrepublic:cz:_czech:de:_germany:de:_ge:dk:_denmark:ee:_estonia:es:_spain:es:_sp:fi:_finland:fr:_france:gr:_greece:hk:_hongkong:hr:_croatia:hu:_hungary:id:_indonesia:il:_israel:in:_india:is:_iceland:it:_italy:jp:_japan:kr:_southkorea:kr:_korea:lt:_lithuania:mk:_macedonia:mx:_mexico:my:_malaysia:nl:_netherlands:no:_norway:nz:_newzealand:ph:_philippines:pl:_poland:pt:_portugal:ro:_romania:rs:_serbia:ru:_russianfederation:ru:_russia:se:_sweden:sg:_singapore:si:_slovenia:sk:_slovakia:th:_thailand:tr:_turkey:tw:_taiwan:uk:_unitedkingdom:un:_unknown:za:_southafrica:';
							break;
						default:
							s_value = '';
							break;
					}

					if ( n_count > 0 )
					{
						var n_index = s_match.indexOf(':'+s_value+':');
						if ( ! (n_index > 0) )
						{
							n_index = s_match.indexOf(':_'+s_value+':');
							s_value = n_index > 0 ? s_match.substr(n_index - n_count, n_count) : '';
						}
					}
				}
				o_value.b_fatal = o_value.b_fatal || s_value == '';
				o_value.s_value = s_value;
				return ! o_value.b_fatal;
			};

			function ff_valid_title(o_value)
			{
				var s_value = o_value.s_value.replace(/^(the|an|a)[\s,]/,'').replace(/^[\s]+/,''), // eliminate the first English article and trim left
					b_article_stripped	= s_value != o_value.s_value,
					b_changed_to_begin	= false,
					b_too_small			= s_value.length < 3;

				if ( b_article_stripped || b_changed_to_begin || b_too_small )
				{
					o_value.b_fatal = o_value.b_fatal || b_too_small;
					ff_alert(o_value,
						(
							( b_too_small
									? 'You need to specify at least 3 characters for a Title Has search. We only got '+s_value.length+' ("'+s_value+'").\n\n'
									: ''
							)+
							( b_article_stripped
									? 'Your article ("'+Str.trim(o_value.s_value.substr(0, o_value.s_value.length - s_value.length))+'") was stripped'+
									( b_too_small
											? '.\n\n'
											: ( b_changed_to_begin
												? ' and your "title has" condition changed to a "title begins"'
												: ''
											)+
											'.\nYou are now searching for "'+s_value+'".\n\n'
									)+
									'Articles (la, le, l\', les, un, une, die, der, das...) at the beginning of a title are\n'+
									'usually not searchable. English articles (a, an, the) are automatically stripped.\n\n'
									: ''
							)+
							'You can also:\n'+
							'   - Specify a Director by typing "dir=Renoir"\n'+
							'   - Specify a Publisher by typing "pub=Criterion"\n'+
							'   - OR values by separating them with commas (",")\n'+
							'   - AND values by specifying multiple search strings with the button "More>>"'
						));
				}
				o_value.s_value = s_value;
			};

			function ff_valid_price(o_value)
			{
				var s_value = Dbl.parseBounded(o_value.s_value, 0, 1000, false, true);
				if ( s_value === false )
				{
					o_value.b_fatal = true;
					ff_alert(o_value,
						'A price must be a non-negative number no larger than 1,000 with up to two decimal digits.\n'+
						'You entered "'+o_value.s_value+'".\n\n'+
						'You can also precede the price value by an operator (>, >=, <, <=, =, !=).\n'+
						'    - Step 1: select either "priced below" or "priced above" and...\n'+
						'    - Step 2: enter ">= 5 <= 10" to get DVDs priced between 5 and 10 US$.');
				}
				o_value.s_value = s_value;
			};

			function ff_valid_asin(o_value)
			{
				var s_value	= o_value.s_value.toLowerCase(),
					b_10_digits	= s_value.length == 10,
					b_changed_o	= false,
					b_unlikelly	= false;

				if ( b_10_digits )
				{
					if ( s_value.substr(0,1) == "b" )
					{
						b_unlikelly = s_value.match(/^b00[0-9a-z]{7}$/) == null;
						if ( b_unlikelly )
						{
							b_changed_o = s_value;
							if ( s_value.substr(1,1) == "o" ) s_value = s_value.substr(0,1) + '0' + s_value.substr(2,8);
							if ( s_value.substr(2,1) == "o" ) s_value = s_value.substr(0,2) + '0' + s_value.substr(3,7);
							if ( s_value.substr(3,1) == "o" ) s_value = s_value.substr(0,3) + '0' + s_value.substr(4,6);
							if ( s_value.substr(4,1) == "o" ) s_value = s_value.substr(0,4) + '0' + s_value.substr(5,5);

							b_unlikelly = s_value.match(/^b00[0-9a-z]{7}$/) == null;
							if ( b_unlikelly ) s_value = b_changed_o;
							b_changed_o = b_changed_o != s_value;
						}
					}
					else
						b_unlikelly = s_value.match(/^[016][0-9]{8}[0-9x]$/) == null;
				}
				if ( ! b_10_digits || b_unlikelly || b_changed_o )
				{
					o_value.b_fatal = o_value.b_fatal || s_value.length < 5;
					ff_alert(o_value,
						( b_unlikelly
								? 'The value you entered, "'+s_value+'", is probably not a\n'+
								'valid ASIN. If you do not find the DVD you are looking\n'+
								'for please check it to see if there is a typo.\n\n'
								: ( b_changed_o
										? 'We have replaced one or more letters "o" in your search\n'+
										'string with the number zero.\n\n'
										: ''
								)
						)+
						( ! b_10_digits
								? 'The value you entered, "'+s_value+'", is not 10 characters\n'+
								'in length. A partial match with a string of 5 or more\n'+
								'characters is allowed in which case the search will proceed.\n\n'
								: ''
						)+
						'An ASIN is how Amazon.com and their international sites\n'+
						'identify a title. Most ASINs start with "B" followed by\n'+
						'2 or more zeros; a few are all numbers and some end with\n'+
						'an "X", but they are all 10 characters in length.');
				}
				o_value.s_value = s_value;
			};

			function ff_valid_imdb(o_value)
			{
				// http://www.imdb.com/title/tt0028950/
				var s_value = o_value.s_value.replace(/[^0-9-]+/g,',').	// only numbers, a dash means that the number before it is a dvd_id and the number after a zero-based index of the imdb id on file for that title
				replace(/,0+/g,',').	// left trim zeros
				replace(/,+/g,',').	// get rid of double commas
				replace(/^,/,'').		// remove left-most comma
				replace(/,$/,'');		// remove right-most comma
				if ( s_value == '' )
				{
					o_value.b_fatal = true;
					ff_alert(o_value,
						'An imbd id is the 7-digit number for a film in the Internet Movie DataBase.\n'+
						'For example, "http://www.imdb.com/title/tt0017136/" points to Fritz Lang\'s\n'+
						'Metropolis, where "0017136" is the imdb id.\n\n'+
						'You can also list DVDs (or box sets) that have:\n'+
						'    - Any films of a list by separating the imdb ids with commas.\n'+
						'    - All films by using the "More>>" button and entering them in multiple boxes.\n\n'+
						'Both of this techniques can also be used with the "title has" based search.');
				}
				o_value.s_value = s_value;
			};

			function ff_valid_upc(o_value)
			{
				var r = {},
					s = Upc.getErrors(o_value.s_value, r);

				Upc.getErrorMsg(s, r, o_value.s_value, true);

				if ( r.b_fatal || r.b_warn )
				{
					o_value.b_fatal = o_value.b_fatal || r.b_fatal;
					ff_alert(o_value, r.s_msg);
				}
				o_value.s_value = Upc.toStr(s).replace(/-/g,'');
			};

			function ff_valid_dir(o_value)
			{
				var b_too_small = o_value.s_value.length < 3;
				if ( b_too_small )
				{
					o_value.b_fatal = true;
					ff_alert(o_value,
						'Please specify at least 3 characters for a Director search.\n'+
						'We only got '+o_value.s_value.length+' ("'+o_value.s_value+'").');
				}
			};

			function ff_valid_pub(o_value)
			{
				var b_too_small = o_value.s_value.length < 3;
				if ( b_too_small )
				{
					o_value.b_fatal = true;
					ff_alert(o_value,
						'Please specify at least 3 characters for a Publisher search.\n'+
						'We only got '+o_value.s_value.length+' ("'+o_value.s_value+'").');
				}
			};

			function ff_valid_country(o_value)
			{
				if ( ! ff_valid_match(o_value, 'country') )
				{
					ff_alert(o_value,
						'You entered "'+o_value.s_value+'" which is not a country we know.\n\n'+
						'Please use the [+] icon to select a language from a list. You can also\n'+
						'use the following abbreviations for common countries if you do not want\n'+
						'to type them:\n\n'+
						'us: United States, uk: United Kingdom, au: Australia, ca: Canada,\n'+
						'dk: Denmark, fr: France, de + ge: Germany, hk: Hong Kong, it: Italy, jp: Japan,\n'+
						'ru: Russian Federation, es + sp: Spain, kr: South Korea, se: Sweden.\n');
				}
			};

			function ff_valid_genre(o_value)
			{
				if ( ! ff_valid_one(o_value, 'genre') )
				{
					ff_alert(o_value,
						'You entered "'+o_value.s_value+'" which is not a genre we recognize.\n\n'+
						'Please use the [+] icon to select a genre from a list.');
				}
			};

			function ff_valid_rel(o_value)
			{
				switch ( o_value.s_value )
				{
					case 'C': case 'O': case 'A': case 'N': case 'D': case 'X': case 'U': o_value.s_value = o_value.s_value.toLowerCase();
					case 'c': case 'o': case 'a': case 'n': case 'd': case 'x': case 'u':
					break;
					default:
						if ( o_value.s_value.match(/^-*[0-9]$/) != null )
						{
							o_value.s_value = ''+ Dec.parse(o_value.s_value);
						}
						else
						{
							o_value.b_fatal = true;
							ff_alert(o_value,
								'You entered "'+o_value.s_value+'" which is not a valid option.\n\n'+
								'Please enter:\n'+
								'    - "C" for current titles,\n'+
								'    - "O" for out of print titles,\n'+
								'    - "A" for announced titles,\n'+
								'    - "N" for not announced,\n'+
								'    - "D" for release delayed,\n'+
								'    - "X" for release cancelled,\n'+
								'    - "U" for unknown release status.\n\n'+
								'You may also enter "-W" for recently released titles or "W" for titles\n'+
								'soon to be released. Where "W" is a number between 0 and 9 representing\n'+
								'the number of weeks. Zero ("0") means being released this week.');
						}
						break;
				}
			};

			function ff_valid_dt(o_value)
			{
				if ( DateTime.parseBounded(o_value.s_value, 1880, new Date().getFullYear() + 1, false) === false )
				{
					o_value.b_fatal = true;
					ff_alert(o_value,
						'Dates must be represented in one of the following formats: YYYY-MM-DD, \n'+
						'YYYY-MM or YYYY. You entered "'+o_value.s_value+'". Use the helper button on date \n'+
						'fields to popup a calendar. It makes entering dates much simpler.'+
						(o_value.b_no_dt_range ? ''
							: '\n\nYou can also precede the date by an operator (>, >=, <, <=, =, !=).')
					);
				}
			};

			function ff_valid_year(o_value)
			{
				var s_value     = o_value.s_value,
					b_not_num   = s_value.match(/^[0-9]{4}$/) == null && s_value.match(/^[0-9]{1,2}$/) == null,
					b_bad_range = false;

				if ( ! b_not_num )
				{
					var n_year  = Dec.parse(s_value);		  if ( n_year < 100  ) n_year = n_year > 10 ? n_year + 1900 : n_year + 2000;
					b_bad_range = n_year < 1880 || n_year > (new Date().getFullYear() + 1); if ( ! b_bad_range ) s_value = '' + n_year;
				}
				if ( b_not_num || b_bad_range )
				{
					o_value.b_fatal = true;
					ff_alert(o_value,
						'A year must be a number between 1880 and '+ (new Date().getFullYear() + 1) +'. You entered "'+s_value+'".\n\n'+
						'You can also precede the year by an operator (>, >=, <, <=, =, !=).\n'+
						'    - Enter ">=20 <30" to get DVDs where the main feature was produced in the 20\'s.');
				}
				o_value.s_value = s_value;
			};

			function ff_valid_language(o_value)
			{
				if ( ! ff_valid_match(o_value, 'language') )
				{
					ff_alert(o_value,
						'You entered "'+o_value.s_value+'" which is not a language we understand.\n\n'+
						'Please use the [+] icon to select a language from a list. You can use the\n'+
						'following abbreviations for common languages if you do not want to type them:\n\n'+
						'en: English, fr: French, ca: French-Canadian, ge + de: German, it: Italian,\n'+
						'jp: Japanese, sl: silent, sp + es: Spanish.');
				}
			};

			function ff_valid_pic(o_value)
			{
				switch ( o_value.s_value )
				{
					case 'Y': case 'P': case 'N': o_value.s_value = o_value.s_value.toLowerCase();
					case 'y': case 'p': case 'n':
					break;
					default:
						o_value.b_fatal = true;
						ff_alert(o_value,
							'You entered "'+o_value.s_value+'" which is not a valid option.\n\n'+
							'Please enter\n'+
							'    - "Y" where the default picture is the DVD cover art,\n'+
							'    - "P" where the default picture is a film poster,\n'+
							'    - "N" for titles with no picture.');
						break;
				}
			};

			function ff_valid_src(o_value)
			{
				switch ( o_value.s_value )
				{
					case 'A': case 'I': case 'E': case 'C': case 'G': case 'B': case 'M': case 'O': case 'T': o_value.s_value = o_value.s_value.toLowerCase();
					case 'a': case 'i': case 'e': case 'c': case 'g': case 'b': case 'm': case 'o': case 't':
					break;
					default:
						o_value.b_fatal = true;
						ff_alert(o_value,
							'You entered "'+o_value.s_value+'" which is not a valid option.\n\n'+
							'Please enter\n'+
							'    - "A" for DVD Package,\n'+
							'    - "I" for Part of DVD Package,\n'+
							'    - "E" for DVD Package Bonus Disc,\n'+
							'    - "C" for Audio CD Bonus Disc,\n'+
							'    - "G" for Game Bonus Disc,\n'+
							'    - "B" for Book Bonus Disc,\n'+
							'    - "M" for Magazine Bonus Disc,\n'+
							'    - "O" for Other Product Bonus Disc,\n'+
							'    - "T" for Theatrical or Broadcast.');
						break;
				}
			};

			function ff_valid_rgn(o_value)
			{
				switch ( o_value.s_value )
				{
					case '0': o_value.s_value = 'z';
					case 'Z': case 'A': case 'B': case 'C': case 'US': case 'UK': case 'EU': case 'LA': case 'AS': case 'SA': case 'JP': case 'AU': case 'ALL': o_value.s_value = o_value.s_value.toLowerCase();
					case 'z': case 'a': case 'b': case 'c': case 'us': case 'uk': case 'eu': case 'la': case 'as': case 'sa': case 'jp': case 'au': case 'all':
					case '1': case '2': case '3': case '4': case '5': case '6':
					break;
					default:
						o_value.b_fatal = true;
						ff_alert(o_value,
							'You entered "'+o_value.s_value+'" which is not a valid option.\n\n'+
							'Please enter\n'+
							'    - "0" for region-free disks,\n'+
							'    - "1" for DVD: US and Canada,\n'+
							'    - "2" for DVD: Europe, Middle East, Japan and South Africa,\n'+
							'    - "3" for DVD: Southeast Asia,\n'+
							'    - "4" for DVD: Australia, New Zealand and Latin America,\n'+
							'    - "5" for DVD: Africa, Eastern Europe and the rest of Asia,\n'+
							'    - "6" for DVD: China and Hong Kong,\n'+
							'    - "A" for Blu-ray: Americas, Japan, Korea and Southeast Asia,\n'+
							'    - "B" for Blu-ray: Europe, Australia, New Zealand and Africa,\n'+
							'    - "C" for Blu-ray: Eastern Europe and the rest of Asia.');
						break;
				}
			};

			function ff_valid_med(o_value)
			{
				switch ( o_value.s_value )
				{
					case 'D': case 'B': case 'R': case 'V': case 'H': case 'C': case 'T': case 'A': case 'P': case 'O': case 'F': case 'S': case 'L': case 'E': case 'N': o_value.s_value = o_value.s_value.toLowerCase();
					case 'd': case 'b': case 'r': case 'v': case 'h': case 'c': case 't': case 'a': case 'p': case 'o': case 'f': case 's': case 'l': case 'e': case 'n': case '3': case '2':
					break;
					default:
						o_value.b_fatal = true;
						ff_alert(o_value,
							'You entered "'+o_value.s_value+'" which is not a valid option.\n\n'+
							'For home media\n'+
							'    - "D" for DVD,\n'+
							'    - "B" for Blu-ray,\n'+
							'    - "3" for Blu-ray 3D,\n'+
							'    - "2" for Blu-ray / DVD Combo,\n'+
							'    - "R" for BD-R,\n'+
							'    - "V" for DVD-R,\n'+
							'    - "H" for HD DVD,\n'+
							'    - "C" for HD DVD/DVD Combo,\n'+
							'    - "T" for HD DVD/DVD TWIN Format,\n'+
							'    - "A" for DVD Audio,\n'+
							'    - "P" for Placeholder,\n'+
							'    - "O" for Other,\n'+
							'For theatrical & broadcast\n'+
							'    - "F" for Film,\n'+
							'    - "S" for Short,\n'+
							'    - "L" for Television,\n'+
							'    - "E" for Featurette,\n'+
							'    - "N" for Events & Performances');
						break;
				}
			};

			function ff_valid_where(o_value)
			{
				var b_too_small = o_value.s_value.length < 3 && o_value.s_value != 'db';
				if ( b_too_small )
				{
					o_value.b_fatal = true;
					ff_alert(o_value,
						'Please specify at least 3 characters for a collection owner.\n'+
						'We only got '+o_value.s_value.length+' ("'+o_value.s_value+'").\n\n'+
						'You may use the notation "member/wishlist" or "member/owned/subfolder"\n'+
						'to specify a folder and subfolder within a collection.');
				}
			};

			function ff_valid_ord(o_value)
			{
				switch ( o_value.s_value )
				{
					case 'created':
						break;
					default:
						o_value.b_fatal = true;
						ff_alert(o_value, '"'+o_value.s_value+'" is not a supported parameter for sorting.\n');
						break;
				}
			};

			function ff_valid_val(o_value, b_warn, b_focus)
			{
				o_value.b_warn  = b_warn;
				o_value.b_focus = b_focus;
				o_value.b_fatal = false;

				if ( o_value.s_value == 'missing' )
				{
					o_value.s_oper = '=';
					return true;
				}

				switch ( o_value.s_key )
				{
					case 'has':		ff_valid_tit  (o_value, /,+/	    ,ff_valid_title		); break;
					case 'price':	ff_valid_price(o_value									); break;
					case 'asin':	ff_valid_multi(o_value, /[\x20,]+/  ,ff_valid_asin		); break;
					case 'imdb':	ff_valid_imdb (o_value									); break;
					case 'upc':		ff_valid_multi(o_value, /,+/	    ,ff_valid_upc		); break;
					case 'dir':		ff_valid_multi(o_value, /,+/	    ,ff_valid_dir		); break;
					case 'pub':		ff_valid_multi(o_value, /,+/	    ,ff_valid_pub		); break;
					case 'pubct':	ff_valid_multi(o_value, /,+/	    ,ff_valid_country	); break;
					case 'genre':	ff_valid_multi(o_value, /,+/	    ,ff_valid_genre		); break;
					case 'rel':		ff_valid_multi(o_value, /,+/	    ,ff_valid_rel		); break;
					case 'reldt':	ff_valid_multi(o_value, /,+/	    ,ff_valid_dt		); break;
					case 'year':	ff_valid_year (o_value									); break;
					case 'lang':	ff_valid_multi(o_value, /,+/	    ,ff_valid_language	); break;
					case 'pic':		ff_valid_multi(o_value, /,+/        ,ff_valid_pic		); break;
					case 'src':		ff_valid_multi(o_value, /,+/        ,ff_valid_src		); break;
					case 'rgn':		ff_valid_multi(o_value, /,+/        ,ff_valid_rgn		); break;
					case 'med':		ff_valid_multi(o_value, /,+/        ,ff_valid_med		); break;
					case 'created':	ff_valid_multi(o_value, /,+/	    ,ff_valid_dt		); break;
					case 'where':	ff_valid_multi(o_value, /,+/	    ,ff_valid_where		); break;

					case 'ord':		ff_valid_ord  (o_value, /,+/	    ,ff_valid_ord		); break;
				}

				if ( o_value.b_fatal )
					b_fatal = true;

				return ! o_value.b_fatal;
			};

			function ff_queue(s_key, s_oper, s_value)
			{
				if ( ! s_key || ! s_value ) return;

				switch ( s_key )
				{
					case 'pricele': s_key = 'price'; if ( s_oper == '' ) s_oper = '<='; break;
					case 'pricege': s_key = 'price'; if ( s_oper == '' ) s_oper = '>='; break;
				}

				if ( s_oper == '' ) s_oper = '=';

				var o_value = {};
				o_value.s_key	= s_key;
				o_value.s_oper	= s_oper;
				o_value.s_value = s_value;

				if ( ff_valid_val(o_value, true, true) )
				{
					switch ( o_value.s_oper )
					{
						case '>' :			s_oper = '.gt.'; break;
						case '>=':			s_oper = '.ge.'; break;
						case '<' :			s_oper = '.lt.'; break;
						case '<=':			s_oper = '.le.'; break;
						case '!=':			s_oper = '.ne.'; break;
						case '=' : default: s_oper = '.eq.'; break;
					}
					a_search[o_value.s_key] = (a_search[o_value.s_key] ? a_search[o_value.s_key] + s_oper : (s_oper != '.eq.' ? s_oper : '')) + o_value.s_value;
				}
			}
		}
	};

function Token(re_key, s_value, f_valid, f_norm_oper, s_def_key)
{
	function next()
	{
		while ( this.s_value )
		{
			if ( this.a_keys )
			{
				this.s_key  = this.s_separator = this.a_keys[1];
				this.s_oper = this.a_keys[2];
				if ( this.f_norm_oper ) this.s_oper = this.f_norm_oper(this.s_oper);
			}
			this.re_key.last_index = 0;
			this.a_keys = re_key.exec(this.s_value);

			var b_valid = this.a_keys != null;
			if ( b_valid && this.f_valid )
			{
				b_valid = this.f_valid(this.a_keys, this.s_def_key);
				if ( ! b_valid && this.f_norm_oper && this.a_keys[2] )
				{
					var s_oper = this.f_norm_oper(this.a_keys[2]);
					if ( s_oper )
					{
						// cook it so that it looks like it was the regular syntax "joan has <> arc" instead of "joan <> arc"
						this.a_keys.index = this.s_value.indexOf(this.a_keys[2]);
						this.a_keys[0] = this.a_keys[2];
						this.a_keys[1] = this.s_def_key;
						this.a_keys[2] = s_oper;
						b_valid = true;
					}
				}
			}
			/*
			if ( this.f_valid )
			{
				if ( this.a_keys )
					alert(
					'b_valid=['+b_valid+']\n'+
					'this.s_value=['+this.s_value+']\n'+
					'this.a_keys=['+this.a_keys+']\n'+
					'this.s_def_key=['+this.s_def_key+']\n'+
					'this.a_keys[0]=['+this.a_keys[0]+']\n'+
					'this.a_keys[1]=['+this.a_keys[1]+']\n'+
					'this.a_keys[2]=['+this.a_keys[2]+']\n'+
					'this.a_keys.index=['+this.a_keys.index+']\n'+
					'');
				  else
					alert(
					'b_valid=['+b_valid+']\n'+
					'this.s_value=['+this.s_value+']\n'+
					'this.a_keys=['+this.a_keys+']\n'+
					'this.s_def_key=['+this.s_def_key+']\n'+
					'');
			}
			*/
			if ( b_valid )
			{
				this.s_token = this.a_keys.index > 0 ? Str.trim(this.s_value.substr(0,this.a_keys.index)) : '';
				this.s_value = Str.trim(this.s_value.substr(this.a_keys.index + this.a_keys[0].length));
			}
			else
			{
				this.s_token = this.s_value;
				this.s_value = '';
			}

			if ( this.s_token )
			{
				if ( this.s_key )
					this.s_def_key = this.s_key;
				else
					this.s_key = this.s_def_key;
				/*
				if ( this.f_valid )
				{
					alert(
						'this.s_key=['+this.s_key+']\n'+
						'this.s_oper=['+this.s_oper+']\n'+
						'this.s_token=['+this.s_token+']\n'+
						'this.s_value=['+this.s_value+']\n'+
						'');
				}
				*/
				return true;
			}
		}
		return false;
	};

	this.re_key			= re_key;
	this.s_value		= s_value;
	this.a_keys			= null;
	this.s_key			= '';
	this.s_separator	= '';
	this.s_oper			= '';
	this.s_token		= '';
	this.f_next			= next;
	this.f_valid		= f_valid;
	this.f_norm_oper	= f_norm_oper;
	this.s_def_key		= s_def_key;
};

/* --------------------------------------------------------------------- */

