/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Jump =
{
	_callback : null,

	attach : function(s_click, f_cb) // attachJump
	{
		Context.attach(s_click, false, 'menu-jump-page'); // needs to be after addImgHandlers()
		Jump._callback = f_cb;
	},

	attachSpin : function(t) // attachJumpSpin
	{
		var e, i,
			re = /[\&\?]pg=[0-9]*/i,
			s = location.href;

		s = re.exec(s);
		s = (s && s.index > 0) ? s[0].substr(4) : '1';

		t = t ? Dec.parse(t.getAttribute('sp_max')) : 100;
		if ( t <= 0 ) t = 100;

		if ( (e=$('n_jump')) )
			e.value = s;

		for ( i = 1 ; i <= 2 ; i++ )
			if ( (e=$('is_'+i+'_jump')) )
			{
				e.setAttribute('sp_max', t);
				ImgSpin.attachSpinV(e);
			}
	},

	setOnEnter : function(ev) // jumpToPageOnEnter
	{
		if ( Kbd.isEnter(ev) )
		{
			Jump.set(-2);
			return false;
		}
		return true;
	},

	set : function(n) // jumpToPage
	{
		if ( n < 0 && (e=$('n_jump')) )
			n = e.value;
		n = Dec.parse(Str.trim(n));

		if ( n > 0 )
		{
			if ( Jump._callback )
			{
				Jump._callback('page',n);
				Context.close();
			}
			else
			{
				var re = /[\&\?]pg=[0-9]*/i,
					s = location.href,
					p = n != 1 ? '&pg=' + n : '',
					a;

				a = re.exec(s);
				if ( a && a.index > 0 )
					s = p.substr(4) != a[0].substr(4) ? s.replace(re, p) : '';
				else
					s = p ? s + p : '';

				if ( s )
				{
					if ( s.indexOf('?') < 0 )
					if ( (a = s.indexOf('&')) > 0 )
						s = s.substr(0,a) + '?' + s.substr(a+1);
					location.href = s;
				}
				else
				{
					alert('You are already on page ' + n + '.');
				}
			}
		}
		else
		{
			alert('Please enter the page number you would like to jump to.');
		}
		return false;
	}
};

/* --------------------------------------------------------------------- */

