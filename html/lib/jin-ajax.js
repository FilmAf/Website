/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Ajax =
{
	_http    : false  , // http
	_uniSeed : '',

    _getHttpObj : function() // getHttpObj
	{
		var h = false;
		if ( window.XMLHttpRequest )
		{
			try	 { h = new XMLHttpRequest(); }
			catch(e) { h = false; }
		}
		else
		{
			if ( window.ActiveXObject )
			{
				try      { h = new ActiveXObject("Msxml2.XMLHTTP"); }
				catch(e) { try      { h = new ActiveXObject("Microsoft.XMLHTTP"); }
						   catch(e) { h = false; } }
			}
		}
		if ( h.overrideMimeType ) h.overrideMimeType('text/xml');
		return h;
	},

	_call : function(s_file, f_callback, s_get, s_post)
	{
		if ( ! Ajax._http ) Ajax._http = Ajax._getHttpObj();
		if ( Ajax._http )
		{
			Ajax._http.abort();
			Ajax._http.onreadystatechange = f_callback;
			if ( s_post )
			{
				Ajax._http.open('POST', '/utils/ajax-' + s_file + '.php' + s_get, true); /* Firefox dos not work with true */
				Ajax._http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				Ajax._http.setRequestHeader("Content-length", s_post.length);
				Ajax._http.setRequestHeader("Connection", "close");
				Ajax._http.send(s_post);
			}
			else
			{
				Ajax._http.open('GET', '/utils/ajax-' + s_file + '.php' + s_get, true); /* Firefox dos not work with true */
				Ajax._http.send(null);
			}
		}
	},

	_uniCall : function(s_file, f_callback, s_get, s_post, s_seed)
	{
		if ( s_seed != Ajax._uniSeed ) return;
		Ajax._call(s_file, f_callback, s_get, s_post);
	},

	asynch : function(s_file, s_callback, s_get, s_post, n_delay)
	{
		s_file  = "'"+s_file+"'";
		s_get   = typeof(s_get  ) == 'undefined' || ! s_get  ?   0 : "'"+s_get.replace(/\x27/g,"\\'") +"'";
		s_post  = typeof(s_post ) == 'undefined' || ! s_post ?   0 : "'"+s_post.replace(/\x27/g,"\\'")+"'";
		n_delay = typeof(n_delay) == 'undefined'			 ? 200 : n_delay;

		setTimeout("Ajax._call("+s_file+","+s_callback+","+s_get+","+s_post+")", n_delay);
	},

	asynchNoDup : function(s_file, s_callback, s_get, s_post, n_delay)
	{
		s_file  = "'"+s_file+"'";
		s_get   = typeof(s_get  ) == 'undefined' || ! s_get  ?   0 : "'"+s_get.replace(/\x27/g,"\\'") +"'";
		s_post  = typeof(s_post ) == 'undefined' || ! s_post ?   0 : "'"+s_post.replace(/\x27/g,"\\'")+"'";
		n_delay = typeof(n_delay) == 'undefined'			 ? 200 : n_delay;

		Ajax._uniSeed = (new Date()).getTime()+','+Math.random();
		setTimeout("Ajax._uniCall("+s_file+","+s_callback+","+s_get+","+s_post+",'"+Ajax._uniSeed+"')", n_delay);
	},

	ready : function()
	{
		return Ajax._http.readyState == 4 && Ajax._http.status == 200;
	},

	getParms : function(o)
	{
		o.sta = o.msg = o.err = '';
		if ( ! Ajax.ready()	) return false; o.lines = Ajax.getLines(); o.length = o.lines.length;
		if ( o.length < 2	) return false; o.line1 = o.lines[1];

		return Ajax.statusErr(o, o.line1);
	},

	statusErr : function(o,s)
	{
		o.sta = Ajax.statusTxt(s,'status');
		o.msg = Ajax.statusTxt(s,'msg'   );
		o.err = o.sta == 'SUCCESS' ? '' : ( o.msg ? o.msg : 'ERROR');
		return o.err == '';
	},

	statusInt : function(s,v)
	{
		var re = new RegExp(v + "='([^\x27]+)'");
		re = re.exec(s);
		return re && re.index ? Dec.parse(re[1]) : 0;
	},

	statusTxt : function(s,v)
	{
		var re = new RegExp(v + "='([^\x27]+)'");
		re = re.exec(s);
		return re && re.index ? re[1] : '';
	},

	getText : function()
	{
		return Ajax._http.responseText;
	},

	getLines : function()
	{
		return Ajax._http.responseText.split('\n');
	},

	__ignore: function(s,t)
	{
	}
};

/* --------------------------------------------------------------------- */

