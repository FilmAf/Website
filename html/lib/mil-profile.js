/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Profile =
{
	uploadPic : function(s) // refreshProfilePic
	{
		Context.close();

		var e = $('profile_1'),
			b = Filmaf.viewCollection == Filmaf.userCollection,
			u = s ? "<img src='http://dv1.us"+s+"_p.jpg' >"   +(b ? "<br /><a class='wga' href='javascript:void(0)' id='edit_photo'>Replace Pic</a>" : '')
				  : "<img src='http://dv1.us/d1/md100.png' />"+(b ? "<br /><a class='wga' href='javascript:void(0)' id='edit_photo'>Upload Pic</a>"  : '');

		if ( e )
		{
			e.innerHTML = u;
			if ( s ) alert("Success!\n\nYour picture has been updated.");
		}
	},

	validate : function() // setProfile
	{
		var i, e, s_gender, s_status, s_name, s_dob, s_city, s_state, s_country, s_my_space, s_facebook, s_homepage, s_about_me, s_youtube,
			s_youtube_auto_ind, c = {b_changed:false,b_undo:true}, d1 = {}; // b_undo:true will skip 'something to undo' detection

		Validate.reset('n_youtube_auto_ind,n_gender,n_status,n_name,n_dob,n_city,n_state,n_country,n_my_space,n_facebook,n_homepage,n_about_me,n_youtube');

		if ( (s_youtube_auto_ind	= CheckBox.validate	('n_youtube_auto_ind'	,c									,0)) !== false )
		if ( (s_gender				= Radio.validate	('n_gender'				,c									,0)) !== false )
		if ( (s_status				= Radio.validate	('n_status'				,c									,0)) !== false )
		if ( (s_name				= Str.validate		('n_name'				,c,   0,  64,1,'Name'				,0)) !== false )
		if ( (s_dob					= DateTime.validate	('n_dob'			 ,d1,c,1880,2008,1,'date of birth'		,0)) !== false )
		if ( (s_city				= Str.validate		('n_city'				,c,   0,  64,1,'City'				,0)) !== false )
		if ( (s_state				= Str.validate		('n_state'				,c,   0,  32,1,'State'				,0)) !== false )
		if ( (s_country				= Str.validate		('n_country'			,c,   0,  32,1,'Country'			,0)) !== false )
		if ( (s_my_space			= MySpace.validate	('n_my_space'			,c          ,1,'MySpace id'			,0)) !== false )
		if ( (s_facebook			= Facebook.validate	('n_facebook'			,c          ,1,'Facebook id'		,0)) !== false )
		if ( (s_homepage			= Url.validate		('n_homepage'		 ,'',c,      128,1,'Homepage address'	,0)) !== false )
		if ( (s_about_me			= Str.validate		('n_about_me'			,c,   0, 600,1,'About me'			,0)) !== false )
		if ( (s_youtube				= YouTube.validate	('n_youtube'			,c          ,1,'YouTube video id'	,0)) !== false )
		{
			if ( c.b_changed )
			{
				s_post = 'version=v1'												 +
						 '&youtube_auto_ind='+encodeURIComponent(s_youtube_auto_ind	)+
						 '&gender='			 +encodeURIComponent(s_gender			)+
						 '&status='			 +encodeURIComponent(s_status			)+
						 '&name='			 +encodeURIComponent(s_name				)+
						 '&dob='			 +encodeURIComponent(s_dob				)+
						 '&city='			 +encodeURIComponent(s_city				)+
						 '&state='			 +encodeURIComponent(s_state			)+
						 '&country='		 +encodeURIComponent(s_country			)+
						 '&my_space='		 +encodeURIComponent(s_my_space			)+
						 '&facebook='		 +encodeURIComponent(s_facebook			)+
						 '&homepage='		 +encodeURIComponent(s_homepage			)+
						 '&about_me='		 +encodeURIComponent(s_about_me			)+
						 '&youtube='		 +encodeURIComponent(s_youtube			);

				Ajax.asynch('home', 'Profile.__save', '?mode=profile&what=set&user='+Filmaf.viewCollection, s_post);
			}
			else
			{
				alert('No changes detected.  Nothing to save.');
			}

			Context.close();
		}
	},

	load : function()
	{
		Ajax.asynch('home', 'Profile.__load', '?mode=profile&what=get&user='+Filmaf.viewCollection);
	},

	__load : function() // profile_callback_load
	{
		if ( Ajax.ready() ) Profile._paint(false);
	},

	__save : function() // profile_callback_save
	{
		if ( Ajax.ready() ) Profile._paint(true);
	},

	_paint : function(b_save) // profile_callback
	{
		// b_save true is called from widget > load > ajax > __load > to refresh the popup dialog
		// b_save false is called from dialog > ajax > __save > to refresh the widget
		if ( Ajax.ready() )
		{
			var a      = Ajax.getLines(),
				b_self = Filmaf.viewCollection == Filmaf.userCollection;

			if ( a.length >= 3 )
			{
				a = a[2].split('\t');
				if ( a.length > 2 )
				{
					var s_photo = '', s_name = '', s_gender = '', s_city = '', s_state = '', s_country = '', s_status = '', s_my_space = '',
						s_facebook = '', s_homepage = '', s_about_me = '', s_youtube = '', s_youtube_auto_ind = '', s_age = '', s_updated_tm = '',
						s_last_visit_tm = '', i, u, v;

					for ( i = 0 ; i < a.length ; i += 2 )
					{
						u = a[i+1];
						v = Dom.decodeHtmlForInput(u);
						switch ( a[i] )
						{
						case 'photo':													s_photo				= u; break;
						case 'name':				Edit.setPrefixed    (a[i],v);		s_name				= u; break;
						case 'dob':					Edit.setPrefixed    (a[i],v);								 break;
						case 'gender':				Radio.setPrefixed   (a[i],v,'MFP');	s_gender			= u; break;
						case 'city':				Edit.setPrefixed    (a[i],v);		s_city				= u; break;
						case 'state':				Edit.setPrefixed    (a[i],v);		s_state				= u; break;
						case 'country':				Edit.setPrefixed    (a[i],v);		s_country			= u; break;
						case 'status':				Radio.setPrefixed   (a[i],v,'SMRP');s_status			= u; break;
						case 'my_space':			Edit.setPrefixed    (a[i],v);		s_my_space			= u; break;
						case 'facebook':			Edit.setPrefixed    (a[i],v);		s_facebook			= u; break;
						case 'homepage':			if ( v == '' ) v = 'http://'; Edit.setPrefixed(a[i],v); s_homepage = u; break;
						case 'about_me':			Edit.setPrefixed    (a[i],v);		s_about_me			= u; break;
						case 'youtube':				Edit.setPrefixed    (a[i],v);		s_youtube			= u; break;
						case 'youtube_auto_ind':	CheckBox.setPrefixed(a[i],v);		s_youtube_auto_ind	= u; break;
						case 'age':														s_age				= u; break;
						case 'updated_tm':												s_updated_tm		= u; break;
						case 'last_visit_tm':											s_last_visit_tm		= u; break;
						}
					}

					if ( b_save )
					{
						if ( s_about_me )
						{
							v			 = Str.ucFirst(Filmaf.viewCollection);
							s_about_me   =  "<div style='margin:0 0 4px 0'>"+
											  "<div class='wg_sepa'>&nbsp;</div>"+
											  "<div style='float:right;margin:0 2px 0 6px'>"+s_updated_tm+"</div>"+
											  "<div>"+(s_name ? s_name + ' (' + v + ')' : v)+" says:</div>"+
											"</div>"+
											s_about_me;
						}

						if ( (u = $('profile_1')) )
						{
							s_photo		= s_photo ? "<img src='http://dv1.us"+s_photo+"_p.jpg' />"+(b_self ? "<br /><a class='wga' href='javascript:void(0)' id='edit_photo'>Replace Pic</a>" : '')
												  : "<img src='http://dv1.us/d1/md100.png' />" +(b_self ? "<br /><a class='wga' href='javascript:void(0)' id='edit_photo'>Upload Pic</a>"  : '');
							u.innerHTML = s_photo;
						}
						if ( (u = $('profile_2')) )
						{
							s_gender		= s_gender == 'M' ? 'Male'    : (s_gender == 'F' ? 'Female' : '');
							s_status		= s_status == 'M' ? 'Married' : (s_status == 'S' ? 'Single' : (s_status == 'R' ? 'In a relationship' : ''));
							s_status		= ((s_status ? s_status + ', ' : '') + (s_gender ? s_gender + ', ' : ''));
							s_status		= s_status.substr(0,s_status.length - 2);
							s_age			= s_age > 0 ? (s_age + ' year' + (s_age == 1 ? '' : 's') + ' old') : '';
							s_location		= ((s_city ? s_city + ', ' : '') + (s_state ? s_state + ', ' : ''));
							s_location		= s_location.substr(0,s_location.length - 2);
							s_last_visit_tm	= s_last_visit_tm ? 'Last visit: ' + s_last_visit_tm : '';
							s_my_space		= s_my_space ? "<a class='wga' href='http://profile.myspace.com/index.cfm?fuseaction=user.viewprofile&friendid=" + s_my_space + "' target='myspace'>MySpace</a>" : '';
							s_facebook		= s_facebook ? "<a class='wga' href='http://www.facebook.com/profile.php?id=" + s_facebook + "' target='facebook'>Facebook</a>" : '';
							s_homepage		= s_homepage ? "<a class='wga' href='" + s_homepage + "' target='homepage'>Homepage</a>" : '';
							s_links			= ((s_my_space ? s_my_space + ', ' : '') + (s_facebook ? s_facebook + ', ' : '') + (s_homepage ? s_homepage + ', ' : ''));
							s_links			= s_links.substr(0,s_links.length - 2);
							s_filmaf			= "http://"+s_name+Filmaf.cookieDomain+'/';

							u.innerHTML		= (s_name			? "<div>" + s_name			+ '</div>' : '')+
											  (s_status			? "<div>" + s_status		+ '</div>' : '')+
											  (s_age			? "<div>" + s_age			+ '</div>' : '')+
											  (s_location		? "<div>" + s_location		+ '</div>' : '')+
											  (s_country		? "<div>" + s_country		+ '</div>' : '')+
											  (s_last_visit_tm	? "<div>" + s_last_visit_tm	+ '</div>' : '')+
											  (s_links			? "<div>" + s_links			+ '</div>' : '')+
											  "<div style='white-space:nowrap;margin-top:10px'>"+s_filmaf+"</div>";
						}
						if ( (u = $('profile_3')) )
						{
							s_youtube		= YouTube.embed(s_youtube, 0, s_youtube_auto_ind == 'Y');
							u.innerHTML		= (s_about_me     ? "<div style='padding-top:8px'>"   + s_about_me + '</div>' : '')+
											  (s_youtube      ? "<div style='text-align:center;padding-top:8px'>" + s_youtube  + '</div>' : '');
						}
					}
				}
			}
		}
	}
};

/* --------------------------------------------------------------------- */

