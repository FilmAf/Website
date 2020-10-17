/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Tab =
{
	_length   : [],
	_options  : [],
	_wholepage: false,
	targetDiv : '',

	setup : function(tab_id, options)
	{
		var el = $(tab_id);
		if ( !el ) { alert('Could not find a tab control by the name "'+ tab_id + '".'); return; }

		var a = Dom.getChildrenByType(el,'ul');
		a = Dom.getChildrenByType(a.length > 0 ? a[0] : el,'li');
		if ( !(a.length > 0) ) { alert('No tab definitions found for "'+ tab_id + '".'); return; }

		var k = Tab._length.length;
		Tab._length[k]  = a.length;
		Tab._options[k] = {};
		Tab._wholepage = ( options && typeof(options['wholepage']) != 'undefined' ) ? options['wholepage'] == '1' : false;

		Tab.getCmdOptions(Tab._options[k], options, 'loadtab', 0, 0, a.length -1);
		Tab.getCmdOptions(Tab._options[k], options, 'width');
		Tab.getCmdOptions(Tab._options[k], options, 'background');
		Tab.getCmdOptions(Tab._options[k], options, 'padding', '8px');
		Tab.getCmdOptions(Tab._options[k], options, 'populate', true);
		var op = Tab._options[k];

		var s = "<table cellpadding='0' style='position:relative;"+
				( op.width		? " width:"		 + op.width		 + ";" : '')+
				( op.background	? " background:" + op.background + ";" : '')+
				( op.padding	? " padding:"	 + op.padding	 + ";" : '')+
				"'><tr><td>"+
				"<table cellpadding='0' width='100%'><tr><td>"+
				"<table cellpadding='0'><tr style='position:relative;z-index:2'><tr>";
		var href = [];
		var hdiv = [];
		for ( var i = 0 ; i < a.length ; i++ )
		{
			var b = Dom.getChildrenByType(a[i],'a');
			var label, dx;
			if ( b.length > 0 )
			{
				label   = b[0].innerHTML;
				dx      = b[0].offsetWidth + 20 + 'px';
				href[i] = b[0].href;
			}
			else
			{
				b = Dom.getChildrenByType(a[i],'div');
				if ( b.length > 0 )
				{
					hdiv[i] = b[0].innerHTML;
					a[i].removeChild(a[i].lastChild);
				}
				label = a[i].innerHTML;
				a[i].innerHTML = '<span>'+label+'</span>';
				b = Dom.getChildrenByType(a[i],'span');
				dx = b.length > 0 ? b[0].offsetWidth + 20 + 'px' : '10em';
			}
			s += "<td>"+
				 "<div class='tab-00' onmouseover='Tab.set("+k+","+i+",1,false);' onmouseout='Tab.set("+k+","+i+",0,false);' onclick='Tab.set("+k+","+i+",2,true);'>"+
				 "<div class='tab-01'>"+
				 "<div class='tab-10'>"+
				 "<div class='tab-11'>"+
				 "<div class='tab-xx'>"+
				 "<div id='tab"+k+"_"+i+"' unselectable='on' style='width: "+dx+";padding:5px 0 4px 0'>"+label+"</div>"+
				 "</div></div></div></div></div>"+
				 "</td>";
		}
		s += "</tr></table></td></tr>"+
			 "<tr><td><table cellpadding='0' width='100%'><tr style='position:relative;z-index:0'><tr><td>"+
			 "<div class='cab-t'><div class='cab-b'><div class='cab-r'><div class='cab-l' id='tab"+k+"_div' >"+
			 "&nbsp;"+
			 "</div></div></div></div>"+
			 "</td></tr></table></td></tr></table></td></tr></table>";
		el.innerHTML = s;
		for ( var i = 0 ; i < a.length ; i++ )
		{
			el = $('tab'+k+'_'+i);
			if ( el )
			{
				if ( href[i] ) el['tab_href'] = href[i];
				if ( hdiv[i] ) el['tab_hdiv'] = hdiv[i];
			}
		}

		Tab.set(k, op.loadtab, 2, op.populate);
	},

	set : function(n_tab, n_id, n_state, b_populate)
	{
		var el = $('tab'+n_tab+'_'+n_id);
		var ch = n_state == 2 ? 's' : (n_state == 1 ? 'h' : 't');
		var ol;
		if ( !el ) return;

		if ( n_state == 2 )
		{
			el['tab_selected'] = 1;
			for ( var i = 0 ; i < Tab._length[n_tab] ; i++ )
			{
				if ( i == n_id ) continue;
				ol = $('tab'+n_tab+'_'+i);
				if ( ol && ol.tab_selected == 1 )
				{
					ol.tab_selected = 0;
					Tab.set(n_tab, i, 0, false);
				}
			}

			Tab.targetDiv = 'tab'+n_tab+'_div';
			if ( b_populate )
			{
				ol = $(Tab.targetDiv);
				if (ol)
				{
					if ( el.tab_hdiv )
					{
						ol.innerHTML = el.tab_hdiv;
					} else
					{
						// adding replace(/%22/g,'"') because of Safari which replaces '"' by '%22'
						var href = el.tab_href ? el.tab_href.replace(/%22/g,'"') : 'about:blank';
						if ( href.substr(0,11) == 'javascript:' )
						{
							eval(href.substr(11));
						}
						else
						{
							if ( Tab._wholepage )
							{
								location.href = href;
							}
							else
							{
								ol.innerHTML = "<iframe id='tab"+n_tab+"_frame' scrolling='no' width='99%' frameborder='0' marginwidth='0' src='"+
											   href+
											   "'>Oops, your browser does not support iframes, please follow <a href='"+
											   href+
											   "'>this link</a>.</iframe>";
								// setTimeout("Tab.iframeResize('tab"+n_tab+"_frame')", 2000);
							}
						}
					}
				}
			}
		}
		else
		{
			if ( el['tab_selected'] == 1 ) ch = 's';
		}

		el = el.parentNode; el.className = ch + 'ab-xx';
		el = el.parentNode; el.className = ch + 'ab-11';
		el = el.parentNode; el.className = ch + 'ab-10';
		el = el.parentNode; el.className = ch + 'ab-01';
		el = el.parentNode; el.className = ch + 'ab-00';
	},

	iframeResize : function(s_id)
	{
		var el = $(s_id);
		if ( el && el.contentWindow && el.contentWindow.document && el.contentWindow.document.body && el.contentWindow.document.body.scrollHeight )
		{
			if ( el.scrollHeight < el.contentWindow.document.body.scrollHeight + 20 )
				el.style.height = Dec.parse(el.contentWindow.document.body.scrollHeight) + 20 + 'px';
		}
	},

	getCmdOptions : function(a, o, s_item, s_default, n_min, n_max)
	{
		if ( o && typeof(o[s_item]) != 'undefined' )
		{
			a[s_item] = o[s_item];
			if ( n_min && n_max && (a[s_item] < n_min || a[s_item] > n_max) )
				a[s_item] = s_default;
		}
		else
		{
			a[s_item] = s_default;
		}
	}
};

/* --------------------------------------------------------------------- */

