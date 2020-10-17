/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Facebook =
{
	_uid			: '',
	_accessToken	: '',
	_when			: '',

	post : function(act, id, med, msg, when, rate, tag)
	{
		var a_post = {'fb:explicitly_shared':true};

		switch ( med )
		{
		case 'b': a_post.bluray = 'http://www.filmaf.com/gp/'+id; break;
		case 'f': a_post.film = 'http://www.filmaf.com/gp/'+id;   break;
		default:  a_post.dvd = 'http://www.filmaf.com/gp/'+id;    break;
		}

		msg = msg.replace(/\<br \/\>/g,"\n").replace(/&lt;/g,"<").replace(/&gt;/g,">").replace(/&quot;/g,'"').replace(/&#39;/g,"'");
		if ( rate > 0 )
		{
			a_post.rating   = rate;
			msg				= rate+" out of 5 stars\n" + msg;
			// msg			= "Rated <img src='http://dv1.us/s1/s5b"+(rate * 2 - 1)+".png' alt='' /> -- "+rate+" out of 5 stars\n" + msg;
		}
		if ( msg != '' ) a_post.message		= msg;
		if ( when      ) a_post.start_time	= when;
		if ( tag != '' ) a_post.tags		= tag;

		switch ( act )
		{
		case 'see': act = 'see';		 break;
		case 'wts': act = 'want_to_see'; break;
		case 'rat': act = 'rate';		 break;
		case 'wan': act = 'want';		 break;
		case 'ord': act = 'order';		 break;
		case 'get': act = 'get';		 break;
		case 'blo': act = 'say';		 break;
		}

		var e = Facebook.getPostDiv();
		Pop.open(e, 0, 354, 0, 3);
		Facebook._login(Facebook.__post, '/me/filmafi:'+act, a_post);
	},

	__post : function(s_get, a_post, a_response, b_success)
	{
		Facebook.closePostDiv();
		if ( ! b_success ) return Facebook._error(a_response);

		var p, e,
			s = 'response='+a_response.id;
		for( p in a_post)
		{
			switch( p )
			{
			case 'message':
			case 'start_time':
			case 'rating':
			case 'tags':
				s += '&' + p + '=' + encodeURIComponent(a_post[p]);
				break;
			case 'bluray':
			case 'film':
			case 'dvd':
				s += '&media=' + p.substr(0,1);
				p  = a_post[p].split('/');
				s += '&id=' + p[p.length - 1];
				break;
			}
		}

		Ajax.asynch('facebook', 'Facebook.__logged', '?act=log', s.substr(1), 100);
//		if ( (e = $('div_fb_posted')) )
//		{
//			e.innerHTML = "Posted"+
//						  "<div style='text-align:center;padding:10px 0 10px 0'>"+
//							"<img src='http://dv1.us/d1/1.gif' width='32px' height='32px' />"+
//						  "</div>";
//			Button.enable('cb_fb_posted',1);
//		}
		return 1;
	},
	see		 : function(id,med)		{ Facebook.post('see',id,med,'' ,0,'',''); },
	want	 : function(id,med)		{ Facebook.post('wan',id,med,'' ,0,'',''); },
	order	 : function(id,med)		{ Facebook.post('ord',id,med,'' ,0,'',''); },
	get		 : function(id,med)		{ Facebook.post('get',id,med,'' ,0,'',''); },
	blog	 : function(id,med,msg)	{ Facebook.post('blo',id,med,msg,0,'',''); },
	__logged : function()
	{
		/*
		if ( Ajax.ready() )
		{
			var a = Ajax.getLines();
		}
		*/
	},
	// ----------------------------------------------------------------------------------------------

	getFriends : function()
	{
		Facebook._login(Facebook.__getFriends, '/me/friends', false);
	},

	__getFriends : function(s_get, a_post, a_response, b_success)
	{
		if ( ! b_success ) return Facebook._error(a_response);

		if(a_response.data)
			Facebook.popFriends(a_response.data);
	},

	ul : function()
	{
		return	"<li id='cm_dvd_fb'>Publish to Facebook"+
			      "<ul id='context_dvd_fb'>"+
					"<li id='cm_dvd_fb_see'>I&#39;m seeing it</li>"+
					"<li id='cm_dvd_fb_want'>I want it</li>"+
					"<li id='cm_dvd_fb_order'>I&#39;ve ordered it</li>"+
					"<li id='cm_dvd_fb_got'>I got it</li>"+
					"<li></li>"+
					"<li>More"+
					  "<ul>"+
						"<li>"+
						  Facebook.ulDiv()+
						"</li>"+
					  "</ul>"+
					"</li>"+
				  "</ul>"+
				"</li>";
	},

	ulDiv : function()
	{
		return			  "<div style='width:352px;margin:4px 0 4px 0'>"+
							"<form id='fb_form' name='mform_dvd_fb' action='javascript:Facebook.SubmitMore(0)'>"+
							  "<div class='one_lbl' style='text-align:left'>"+
								"<table style='width:100%;white-space:nowrap'>"+
								  "<tr>"+
									"<td>Select One:</td>"+
									"<td>"+
									  "<table id='tab_fb' style='border-collapse:separate;border-spacing:0 2px 0 2px;white-space:nowrap'>"+
										"<tr>"+
										  // Film + Blu-ray + DVD
										  "<td class='td_opt' id='fb_sel_see'>Seeing</td>"+
										  "<td class='td_opt' id='fb_sel_wts'>Want to see</td>"+
										  "<td class='td_opt' id='fb_sel_rat'>Rate</td>"+
										  // Blu-ray + DVD
										  "<td class='td_opt' id='fb_sel_wan'>Want it</td>"+
										  "<td class='td_opt' id='fb_sel_ord'>Ordered it</td>"+
										  "<td class='td_opt' id='fb_sel_get'>Got it</td>"+
										"</tr>"+
									  "</table>"+
									"</td>"+
								  "</tr>"+
								"</table>"+
								"<div>"+
								  "<table style='width:100%;white-space:nowrap'>"+
									"<tr>"+
									  "<td style='width:1px'>Opt msg: (<span id='fb_char'>500</span>)</td>"+
									  "<td style='width:95%'>"+
										"<img src='http://dv1.us/d1/00/fbh0.png' width='14px' height='14ps' id='fbi_home' onclick='Facebook.insTest(0)' style='padding:0 2px 0 2px' title='Insert Home URL' />"+
										"<img src='http://dv1.us/d1/00/fbf0.png' width='14px' height='14ps' id='fbi_coll' onclick='Facebook.insTest(1)' title='Insert /owned URL' />"+
									  "</td>"+
									  "<td style='width:1px'><span id='fb_lbl_when'>When</span></td>"+
									  "<td style='width:1px'><input id='fb_cb_when' type='checkbox' onclick='Facebook.ulUpdateDt()' /></td>"+
									  "<td style='width:1px'><span id='fb_text_date'>0000-00-00</td>"+
									  "<td style='width:1px;padding-right:4px'><img id='fb_spin_day' src='http://dv1.us/d1/00/pn00.gif' width='10px' height='17px' /></td>"+
									  "<td style='width:1px'><span id='fb_text_time'>0:00 AM</td>"+
									  "<td style='width:1px'><img id='fb_spin_hour' src='http://dv1.us/d1/00/pn00.gif' width='10px' height='17px' /></td>"+
									  "<td style='width:1px'><img id='fb_spin_min' src='http://dv1.us/d1/00/pn00.gif' width='10px' height='17px' /></td>"+
									  "<td style='width:1px'><img id='fb_spin_ampm' src='http://dv1.us/d1/00/pn00.gif' width='10px' height='17px' /></td>"+
									  "<td style='width:1px;padding-left:6px'><span id='fb_lbl_tag'>Tag</span></td>"+
									  "<td style='width:1px'><input id='fb_cb_tag' type='checkbox' /></td>"+
									"</tr>"+
								  "</table>"+
								"</div>"+
								"<textarea id='fb_msg' style='width:100%;height:40px' maxlength='500' wrap='soft' onkeyup='Edit.countDown(\"fb_char\",500 - this.value.length)'></textarea>"+
								"<div>"+
								  "<table style='width:100%;white-space:nowrap'>"+
									"<tr>"+
									  "<td style='text-align:right'><span id='fb_lbl_rat'>Rate it:</span></td>"+
									  "<td colspan='3'>"+
										"<span id='fb_rad1'>"+
										  "<input id='fb_med' type='hidden' />"+
										  "<input id='fb_dvd' type='hidden' />"+
										  "<input id='fb_act' type='hidden' />"+
										  "<input name='fb_rad' id='fb_rad_5' value='5' type='radio' /><img src='http://dv1.us/s1/s5b9.png' />"+
										  "<input name='fb_rad' id='fb_rad_4' value='4' type='radio' /><img src='http://dv1.us/s1/s5b7.png' />"+
										  "<input name='fb_rad' id='fb_rad_3' value='3' type='radio' /><img src='http://dv1.us/s1/s5b5.png' />"+
										"</span>"+
									  "</td>"+
									"</tr>"+
									"<tr>"+
									  "<td style='width:1px'>&nbsp;</td>"+
									  "<td style='width:1px'>"+
										"<span id='fb_rad2'>"+
										  "<input name='fb_rad' id='fb_rad_2' value='2' type='radio' /><img src='http://dv1.us/s1/s5b3.png' />"+
										  "<input name='fb_rad' id='fb_rad_1' value='1' type='radio' /><img src='http://dv1.us/s1/s5b1.png' />"+
										  "<input name='fb_rad' id='fb_rad_0' value='0' type='radio' /><span style='position:relative;top:-2px'>n/a</a>"+
										"</span>"+
									  "</td>"+
									  "<td style='width:95%;text-align:center'><a href='http://www.youtube.com/user/FilmAf' style='color:#de4141' target='yt'>yt</a></td>"+
									  "<td style='width:1px'><input type='submit' style='width:36px;text-align:center' value='Post' /></td>"+
									"</tr>"+
								  "</table>"+
								"</div>"+
							  "</div>"+
							"</form>"+
						  "</div>";
	},

	ulPre : function(i, m,n)
	{
		$('fb_med').value = m;
		$('fb_dvd').value = n;

		TblMenu.attach('tab_fb',function(id,parm){Facebook.selectAction(id.substr(7),parm)});
		TblMenu.set($('fb_sel_see'),1);

		$('fb_rad_0'  ).checked = true;
		$('fb_cb_when').checked = false;
		$('fb_cb_tag' ).checked = false;
		Facebook._when = new Date();
		Facebook._when.setSeconds(0);
		Facebook._when.setMilliseconds(0);
		Facebook._when.setMinutes(Facebook._when.getMinutes()-Facebook._when.getMinutes()%5);
		Facebook.ulUpdateDt();

		$('fb_spin_day' ).spun = Facebook.ulSpinDay;
		$('fb_spin_ampm').spun = Facebook.ulSpinAmPm;
		$('fb_spin_hour').spun = Facebook.ulSpinHour;
		$('fb_spin_min' ).spun = Facebook.ulSpinMin;
		ImgSpun.attachSpun($('fb_spin_day' ));
		ImgSpun.attachSpun($('fb_spin_ampm'));
		ImgSpun.attachSpun($('fb_spin_hour'));
		ImgSpun.attachSpun($('fb_spin_min' ));

		m = m == 'f';
		$('fb_sel_wan').style.visibility =
		$('fb_sel_ord').style.visibility =
		$('fb_sel_get').style.visibility = m ? 'hidden' : 'visible';

		if ( typeof(i.cm_dvd_fb) != 'undefined' )
		{
			if ( typeof(i.cm_dvd_fb.submenu) == 'function' )
				i.cm_dvd_fb.submenu();

			i = i.cm_dvd_fb.submenu.menu.items;
			if ( i.cm_dvd_fb_want	) i.cm_dvd_fb_want.disable(m);
			if ( i.cm_dvd_fb_order	) i.cm_dvd_fb_order.disable(m);
			if ( i.cm_dvd_fb_got	) i.cm_dvd_fb_got.disable(m);
		}

		m = $('fbi_home');
		m.onmouseover	= function(ev){Img.mouseOver(ev,this,18);};
		m.onmouseout	= function(  ){Img.mouseOut(this,18);};
		m = $('fbi_coll');
		m.onmouseover	= function(ev){Img.mouseOver(ev,this,19);};
		m.onmouseout	= function(  ){Img.mouseOut(this,19);};
	},

	insTest : function(i)
	{
		var e = $('fb_msg'),
			c = Filmaf.userCollection,
			u = 'http://' + (c ? c : 'www') + '.filmaf.com/';

		switch (i)
		{
		case 0:
			Edit.insertAtCursor(e, u);
			break;
		case 1:
			Edit.insertAtCursor(e, u + (c ? 'owned' : ''));
			break;
		}
		e.focus();
	},

	ulSpinDay : function(d)
	{
		d  = d ? 1 : -1;
		d *= 60000 * 60 * 24;
		Facebook._when = new Date(Facebook._when.getTime() + d);
		Facebook.ulUpdateDt();
	},

	_ulSpinHour : function(d,k)
	{
		var m = Facebook._when.getHours(),
			a = m >= 12 ? 12 : 0;
			i = m - a;

		switch ( k )
		{
		case 0: i = (i + (d ? 1 : -1)) % 12; break;
		case 1: a = a ? 0 : 12; break;
		}
		Facebook._when.setHours(a + i);
		Facebook.ulUpdateDt();
	},
	ulSpinHour : function(d) { Facebook._ulSpinHour(d,0); },
	ulSpinAmPm : function(d) { Facebook._ulSpinHour(d,1); },

	ulSpinMin : function(d)
	{
		var m = Facebook._when.getMinutes();
		m = (m + (d ? 5 : -5)) % 60;
		Facebook._when.setMinutes(m);
		Facebook.ulUpdateDt();
	},

/*
	ulSpinMin : function(d,k)
	{
		var m = Facebook._when.getMinutes(),
			i = m % 10;
			m = m - i;

		switch ( k )
		{
		case 0: i = (i + (d ? 1 : -1)) % 10; break;
		case 1: m = (m + (d ? 10 : -10)) % 60; break;
		}
		Facebook._when.setMinutes(m + i);
		Facebook.ulUpdateDt();
	},
	ulSpinMin1  : function(d) { Facebook.ulSpinMin (d,0); },
	ulSpinMin2  : function(d) { Facebook.ulSpinMin (d,1); },
*/

	ulUpdateDt : function()
	{
		var b = $('fb_cb_when').checked;

		$('fb_text_date').style.visibility =
		$('fb_spin_day' ).style.visibility =
		$('fb_text_time').style.visibility =
		$('fb_spin_ampm').style.visibility =
		$('fb_spin_hour').style.visibility =
		$('fb_spin_min' ).style.visibility = b ? 'visible' : 'hidden';

		if ( b )
		{
			$('fb_text_date').innerHTML = DateTime.toDateStr(Facebook._when);
			$('fb_text_time').innerHTML = DateTime.toTimeHHMM(Facebook._when,0,1);
		}
	},

	selectAction : function(id,parm)
	{
		var w = 0,
			t = 0,
			r = 0;

		$('fb_act').value = id;
		switch ( id )
		{
		case 'see': t = 1; break; // When not really useful for facebook
		case 'wts': t = 1; break; // API does not allow future time
		case 'rat':	r = 1; break;
//		case 'wan': case 'ord': case 'get':	break;
		}

		v = w ? 'visible' : 'hidden';
		t = t ? 'visible' : 'hidden';
		r = r ? 'visible' : 'hidden';

		$('fb_lbl_when' ).style.visibility = 
		$('fb_cb_when'  ).style.visibility = v;
		if ( w )
		{
			Facebook.ulUpdateDt();
		}
		else
		{
			$('fb_text_date').style.visibility = 
			$('fb_spin_day' ).style.visibility = 
			$('fb_text_time').style.visibility = 
			$('fb_spin_ampm').style.visibility = 
			$('fb_spin_hour').style.visibility = 
			$('fb_spin_min' ).style.visibility = v;
		}

		$('fb_lbl_tag'  ).style.visibility = 
		$('fb_cb_tag'   ).style.visibility = t;

		$('fb_lbl_rat'  ).style.visibility = 
		$('fb_rad1'     ).style.visibility = 
		$('fb_rad2'     ).style.visibility = r;
	},

	SubmitMore : function(i)
	{
		var a  = $('fb_act').value,
			m  = $('fb_med').value,
			n  = $('fb_dvd').value,
			w  = $('fb_lbl_when').style.visibility == 'visible',
			t  = $('fb_lbl_tag' ).style.visibility == 'visible',
			r  = $('fb_lbl_rat' ).style.visibility == 'visible',
			/* getTime() in GMT already */
			w1 = w && $('fb_cb_when').checked ? Facebook._when.getTime() / 1000 : '';
			t1 = t ? $('fb_cb_tag').checked : false,
			r1 = r ? Radio.getVal('fb_rad') : 0,
			m1 = $('fb_msg').value;
		var e;

		Context.close();
		if ( t1 )
		{
			e = Facebook.getFriendsDiv(a,m,n,w1,r1,m1);
			Pop.open(e, 0, 354, 0, 3);
		}
		else
		{
			Facebook.post(a,n,m,m1,w1,r1,'');
		}
	},

	getFriendsDiv : function(a,m,n,w1,r1,m1)
	{
		var d = $('div_fb_friends'), f;

		if ( ! d )
		{
				d = document.createElement('div');
			var s = d.style,
				b = document.getElementsByTagName('body').item(0);

			d.id			= 'div_fb_friends';
			s.visibility	= 'hidden';
			s.position		= 'absolute';
			s.top			= 0;
			s.left			= 0;
			s.zIndex		= 7;

			b.appendChild(d);
			d.innerHTML =	"<div style='background:#0059a6'>"+
							  "<div style='border:1px solid #6c8ba6;padding:3px'>"+
								"<div style='background:#ffffff;text-align:center;width:368px'>"+
								  // header
								  "<div>"+
									"<table class='img_bar' style='width:100%'>"+
									  "<tr>"+
										"<td class='img_bar' style='border-bottom:solid 2px #0059a6;text-align:center'>Facebook - Tagging</td>"+
									  "</tr>"+
									"</table>"+
								  "</div>"+
								  // picture
								  "<div style='padding:10px 0 4px 0'>"+
									"Please select which friends were/will be there with you"+
									"<div style='padding:10px 20px 6px 20px'>"+
									  "<input id='fb2_med' type='hidden' />"+
									  "<input id='fb2_dvd' type='hidden' />"+
									  "<input id='fb2_act' type='hidden' />"+
									  "<input id='fb2_msg' type='hidden' />"+
									  "<input id='fb2_whn' type='hidden' />"+
									  "<input id='fb2_rad' type='hidden' />"+
									  "<iframe width='324' height='160' scrolling='yes' id='frame_fb' style='border: 1px solid'></iframe>"+
									  "<div style='text-align:right;white-space:nowrap;padding-top:8px'>"+
										"<input type='button' value='Post without tagging' onclick='Facebook.closeFriendsDiv(1)' /> "+
										"<input type='button' value='Post' onclick='Facebook.closeFriendsDiv(2)' id='fb2_post' /> "+
										"<input type='button' value='Cancel' onclick='Facebook.closeFriendsDiv(0)' />"+
									  "</div>"+
									"</div>"+
								  "</div>"+
								"</div>"+
							  "</div>"+
							"</div>";
		}

		$('fb2_med').value = m;
		$('fb2_dvd').value = n;
		$('fb2_act').value = a;
		$('fb2_whn').value = w1;
		$('fb2_rad').value = r1;
		$('fb2_msg').value = m1;

		Button.enable('fb2_post',0);

		if ((f = Win.findFrame('frame_fb')))
		{
			f.write("<html>"+
					  "<head>"+
						"<link rel='stylesheet' type='text/css' href='/styles/00/filmaf.css' />"+
						"<body style='background:#ffffff'>"+
						  "<div style='text-align:center;padding-top:10px;color:#999999'>"+
							"Waiting on Facebook"+
							"<div style='text-align:center;padding-top:36px'>"+
							  "<img src='http://dv1.us/d1/wait.gif' width='32px' height='32px' />"+
							"</div>"+
						  "</div>"+
						"</body>"+
					"</html>");
			f.close();
		}

		Facebook.getFriends();
		return d;
	},

	popFriends : function(a)
	{
		var i, f, s = '';

		a.sort(function(a,b) { return a.name.toLocaleLowerCase().localeCompare(b.name.toLocaleLowerCase());});

		if ((f = Win.findFrame('frame_fb')))
		{
			for ( i = 0 ; i < a.length ; i++ )
				s += "<div><input type='checkbox' id='fbf_"+a[i].id+"'> "+a[i].name+"</div>";

			f.write("<html>"+
					  "<head>"+
						"<link rel='stylesheet' type='text/css' href='/styles/00/filmaf.css' />"+
						"<body style='background:#ffffff'>"+
						  "<div id='fb_list'>"+s+"</div>"+
						"</body>"+
					"</html>");
			f.close();

			Button.enable('fb2_post',1);
		}
	},

	getCheckedFriends : function()
	{
		var i, a, f, s = '';

		if ((f = Win.findFrame('frame_fb')))
		{
			a = f.getElementsByTagName('input');
			for ( i = 0 ; i < a.length ; i++ )
				if ( a[i].type == 'checkbox' && a[i].checked )
					s += ','+a[i].id.substr(4);
			s = s.substr(1);
		}
		return s;
	},

	closeFriendsDiv : function(x)
	{
		var a, n, m, s, w, r, t = '';

		switch (x)
		{
		case 2:
			t = Facebook.getCheckedFriends();
		case 1:
			a = $('fb2_act').value;
			n = $('fb2_dvd').value;
			m = $('fb2_med').value;
			s = $('fb2_msg').value;
			w = $('fb2_whn').value;
			r = $('fb2_rad').value;
			break;
		}

		Pop.close('div_fb_friends');

		if (x)
			Facebook.post(a,n,m,s,w,r,t);
	},

	getPostDiv : function()
	{
		var d = $('div_fb_post'), e, f;

		if ( ! d )
		{
				d = document.createElement('div');
			var s = d.style,
				b = document.getElementsByTagName('body').item(0);

			d.id			= 'div_fb_post';
			s.visibility	= 'hidden';
			s.position		= 'absolute';
			s.top			= 0;
			s.left			= 0;
			s.zIndex		= 7;

			b.appendChild(d);
			d.innerHTML =	"<div style='background:#0059a6'>"+
							  "<div style='border:1px solid #6c8ba6;padding:3px'>"+
								"<div style='background:#ffffff;text-align:center;width:368px'>"+
								  // header
								  "<div>"+
									"<table class='img_bar' style='width:100%'>"+
									  "<tr>"+
										"<td class='img_bar' style='border-bottom:solid 2px #0059a6;text-align:center'>Facebook - Posting</td>"+
									  "</tr>"+
									"</table>"+
								  "</div>"+
								  // picture
								  "<div style='text-align:center;padding:10px 0 10px 0'>"+
								    "<div id='div_fb_posted'>"+
									"</div>"+
//									"<input id='cb_fb_posted' type='button' value='Close' onclick='Facebook.closePostDiv()' />"+
								  "</div>"+
								"</div>"+
							  "</div>"+
							"</div>";
		}

		if ( (e = $('div_fb_posted')) )
		{
			e.innerHTML = "Waiting on Facebook"+
						  "<div style='text-align:center;padding:10px 0 10px 0'>"+
							"<img src='http://dv1.us/d1/wait.gif' width='32px' height='32px' />"+
						  "</div>";
		}

//		Button.enable('cb_fb_posted',0);
		return d;
	},

	closePostDiv : function()
	{
		Pop.close('div_fb_post');
	},

	// ----------------------------------------------------------------------------------------------

	_login : function(callback,s_get,a_post)
	{
		// callback(s_get, a_post,{id:1},1);

		FB.login(
		function (a_response)
		{
			if (a_response.authResponse)
			{
				// Facebook._uid = a_response.authResponse.userID;
				// Facebook._accessToken = a_response.authResponse.accessToken;
				if ( a_post === false )
				{
					FB.api(s_get,
					function(a_response)
					{
						callback(s_get, a_post, a_response, a_response && ! a_response.error);
					});
				}
				else
				{
					FB.api(s_get, 'post', a_post,
					function(a_response)
					{
						callback(s_get, a_post, a_response, a_response && ! a_response.error);
					});
				}
			}
		}, {scope:'publish_actions,read_friendlists'});
	},

	_error : function(a_response)
	{
		if ( a_response.error )
			alert('Error: '+a_response.error.message);
		else
			alert('Error occured');
		return 0;
	},
	// ----------------------------------------------------------------------------------------------

	parse : function(s)
	{
		var m;

		if ( (s = Str.trim(s)) != '' )
		{
			// http://www.facebook.com/profile.php?id=1166319731&ref=nf
			if ( (m = Url.getVal('id',s)) && /^[0-9]*$/.test(m) ) return m;
			// http://www.facebook.com/people/Ed-Hoo/547739148
			if ( (m = /^http:\/\/www\.facebook\.com\/people\/[^\/]*\/([0-9]*)$/.exec(s)) && m.length > 1 ) return m[1];
			// 547739148
			if ( /^[0-9]*$/.test(s) ) return s;

			return false;
		}
		return '';
	},

	validate : function(s_field, o_changed, b_allow_empty, s_name, b_must_change)
	{
		var s, e_new, e_old, b_same;

		if ( (e_new = $(s_field)) )
		{
			// update 'different from undo' flag
			if ( o_changed && ! o_changed.b_undo && (e_old = $('z_' + s_field.substr(2))) )
				o_changed.b_undo = Str.trim(e_new.value) != Str.trim(e_old.value);

			// return if the value has not changed and the user is not forced to modify it
			b_same = (e_old = $('o_' + s_field.substr(2))) && (s = Str.trim(e_new.value)) == Str.trim(e_old.value);
			if ( b_same && ! b_must_change ) return s;

			// process emptiness
			s = Validate.checkEmptiness(e_new, b_allow_empty, s_name);
			if ( s === false        ) return false;
			if ( ! e_old && s == '' ) b_same = true;
			
			// parse value
			s = Facebook.parse(s);
			if ( s === false )
			{
				Validate.warn(e_new, true, true, 
							  "Your "+s_field+" seems to be invalid.\n\n"+
							  "We expected:\n"+
							  "        A string with some 10 digits\n\n"+
							  "or a URL like one of these:\n"+
							  "        http://www.facebook.com/profile.php?id=7777777\n"+
							  "        http://www.facebook.com/people/User-Name/7777777",
							  false);
				return false;
			}

			// report
			if ( o_changed && ! o_changed.b_changed )
				o_changed.b_changed = ! b_same;
			e_new.value = s;
			return s;
		}
		return '';
	}
};

window.fbAsyncInit = function()
{
	FB.init(
	{
		appId  : '413057338766015', // App ID
		status : true, // check login status
		cookie : true, // enable cookies to allow the server to access the session
		xfbml  : true  // parse XFBML
	});
};

// Load the SDK Asynchronously
(
	function(d, debug)
	{
		var js, id = 'facebook-jssdk',
			ref = d.getElementsByTagName('script')[0];
			
		if (d.getElementById(id))
			{return;}

		js = d.createElement('script');
		js.id = id;
		js.async = true;
		js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
		ref.parentNode.insertBefore(js, ref);
	}
	(document, /*debug*/ true)
);

/* --------------------------------------------------------------------- */

