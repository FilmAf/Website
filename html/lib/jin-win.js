/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Win =
{
	openPop : function(b_refresh,s_target,s_url,n_width,n_height,b_reduced,b_scroll) // openWin
	{
		s_url    = s_url.replace(/\&amp;/g,'&');
		b_scroll = b_scroll ? '1' : '0';

		if ( b_refresh )
		{
			var t = new Date();
			s_url = s_url + (s_url.indexOf('?') < 0 ? '?' : '&') + 'refresh=' + t.getTime();
		}
		var o_wnd = window.open(s_url,
								s_target,
								(b_reduced ? 'resizable=1,menubar=0,toolbar=0,location=0,directories=0,scrollbars='+b_scroll+',status=0,titlebar=0' +
											 (n_width > 0 && n_height > 0 ? ',width=' + n_width + 'px,height=' + n_height + 'px' : '')
										   : ''));
		if ( o_wnd ) o_wnd.focus();
		return false;
	},

	openStd : function(s_url, w) // openStd
	{
		s_url = s_url.replace(/\&amp;/g,'&');

		if ( typeof(w) == 'object' )
			if ( !w || w.closed )
				w = window.open(s_url);
			else
				w.location = s_url;
		else
			w = window.open(s_url,w);

		if ( w ) w.focus();
		return true;
	},

	openDyn : function(s_target,s_title,n_width,n_height,b_reduced,s_inc,s_onload,b_scroll) // openDyn
	{
		b_scroll = b_scroll ? '1' : '0';
		var o_wnd = window.open('',
								s_target,
								(b_reduced ? 'resizable=0,menubar=0,toolbar=0,location=0,directories=0,scrollbars='+b_scroll+',status=0,titlebar=0' +
											 (n_width > 0 && n_height > 0 ? ',width=' + n_width + 'px,height=' + n_height + 'px' : '')
										   : ''));
		if ( o_wnd )
		{
			o_wnd.document.writeln(
				"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'DTD/xhtml1-transitional.dtd'>\n"+
				"<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>\n"+
				"<head>\n"+
				  "<title>"+s_title+"</title>\n"+
				  "<meta http-equiv='Content-type' content='text/html; charset=UTF-8' />\n"+
				  s_inc+
				"</head>\n"+
				"<body onload='"+(s_onload ? s_onload+';' : '')+"self.focus()'>");
		}

		return o_wnd;
	},

	endDyn : function(o_wnd) // finalizeDyn
	{
		if ( o_wnd )
		{
			o_wnd.document.writeln("</body>\n</html>");
			o_wnd.document.close();
		}
	},

	showPic : function(s_pic) // showLargePic
	{
		Win.openPop(false, 'large_pic', Filmaf.baseDomain + '/utils/lp.html?pic=' + s_pic, 354, 468, 1, 0);
	},

	reauth : function(s_redirect)
	{
		if ( ! s_redirect ) s_redirect = '/utils/close.html%3Fmsg%3D1';
		Win.openPop(0,'_blank','/utils/login.html?redirect=' + s_redirect,680,520,1,0);
	},

	findFrame : function(s_id)
	{
		var i;

		for ( i = 0 ; i < parent.frames.length ; i++ )
		{
			var elid;
			try { elid = parent.frames[i].frameElement.id }
			catch (e) { elid = 'verbotten'; }
			if ( elid == s_id )
				return parent.frames[i].document; 
		}

		return 0;
	}
};

/* --------------------------------------------------------------------- */

