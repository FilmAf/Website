/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdApprove =
	{
		_fields :  ['a_dvd_title', 'a_film_rel_year', 'a_director', 'a_publisher', 'a_orig_language', 'a_country', 'a_region_mask',
			'a_genre', 'a_media_type', 'a_num_titles', 'a_num_disks', 'a_source', 'a_rel_status', 'a_film_rel_dd',
			'a_dvd_rel_dd', 'a_dvd_oop_dd', 'a_imdb_id', 'a_list_price', 'a_sku', 'a_upc', 'a_asin'],

		inputPicName	: '',
		inputPicDispo	: '',
		inputPicReq		: '',
		inputPicMod		: '',
		inputPicId		: 0,
		inputPicEditId	: 0,

		setup : function() // initDvdAppr
		{
			var e;

			if ( (e = $('myform')) )
			{
				DvdApprove._setCopyButtons();
				Calendar.setup({inputField:'n_a_film_rel_dd',clearInput:'x_a_film_rel_dd',ifFormat:'%Y-%m-%d',button:'h_a_film_rel_dd',singleClick:true,step:1});
				Calendar.setup({inputField:'n_a_dvd_rel_dd',clearInput:'x_a_dvd_rel_dd',ifFormat:'%Y-%m-%d',button:'h_a_dvd_rel_dd',singleClick:true,step:1});
				Calendar.setup({inputField:'n_a_dvd_oop_dd',clearInput:'x_a_dvd_oop_dd',ifFormat:'%Y-%m-%d',button:'h_a_dvd_oop_dd',singleClick:true,step:1});
				Menus.setup();
				DvdEdit._initSearch		('h_a_dvd_title' ,'search');
				DvdEdit._initTitleLookup('b_a_dvd_title' ,'filmaf',Title.seekInFilmaf);
				DvdEdit._initTitleLookup('b1_a_dvd_title','imdb'  ,Title.seekInImdb );
				DvdEdit._initTitleLookup('b2_a_dvd_title','amz'   ,Title.seekInAmz  );
				Context.attach('h_a_dvd_title',false      ,'menu-dvd-title');
				Context.attach('mod_txt'      ,false      ,'menu-moddvd'   );
				Context.attach('h_a_genre'    ,'g_a_genre','menu-genre-no' );
				Expand.attach('a_orig_language');
				Expand.attach('a_country'      );
				Expand.attach('a_region_mask'  );
				Expand.attach('a_director'     );
				Expand.attach('a_publisher'    );
				Expand.attach('a_imdb_id'      );
				Expand.attach('a_upc'          );

				Img.attach();
				$('b_a_amz_country').onclick = Asin.test
			}
		},

		_setCopyButtons : function() // setCopyButtons
		{
			var i, j, e, f, s, b;

			for ( i = 0 ; i < DvdApprove._fields.length ; i++ )
			{
				s = DvdApprove._fields[i];
				b = false;

				if ( (e = $('p_'+s)) )
				{
					if ( (f = $('o_'+s)) )
						b = e.value != f.value;
					else
						b = e.value != '';
				}
				else
				{
					for ( j = 0 ; ! b && (e = $('p_'+s+'_'+j)) ; j++ )
						if ( (f = $('o_'+s+'_'+j)) )
							b = e.value != f.value;
						else
							b = e.value != '';
					if ( ! b && (f = $('p_'+s+'_'+j)) )
						b = true;
				}

				j = s.substr(1);
				if ( (e = $('t1'+j)) )
				{
					e.style.textAlign = 'center';
					e.innerHTML = b ? "<input type='button' onclick='DvdApprove.copyField(\""+s+"\",false)' value='&hellip;&laquo; Rej' style='width:54px' />" : "<span class='hl'>...</span>";
				}
				if ( (e = $('t2'+j)) )
				{
					e.style.textAlign = 'center';
					e.innerHTML = b ? "<input type='button' onclick='DvdApprove.copyField(\""+s+"\",true)' value='App &raquo;&hellip;' style='width:54px' />" : "<span class='hl'>...</span>";
				}
			}
		},

		approveAll : function(n_nav) // f_approve_all
		{
			if ( ! DvdApprove._areValuesEqual(null, 'n_', 'o_') )
			{
				var y = confirm("Your changes on the right side will be lost as we overlay the member's proposed changes. Continue?\n\nOK=Yes - Cancel=No");
				if ( ! y ) return true;
			}
			DvdApprove.copyFields(true);
			return DvdApprove._validate(true, n_nav);
		},

		saveRight : function(n_nav) // f_save_right
		{
			if ( DvdApprove._areValuesEqual(null, 'n_', 'o_') )
			{
				var y = confirm('You are attempting to save the values on the right side but they have not been changed.\nWould it not be better to cancel out and reject the submission?\n\nContinue?\n\nOK=Yes - Cancel=No');
				if ( ! y ) return true;
			}
			return DvdApprove._validate(true, n_nav);
		},

		discard : function() // f_discard
		{
			var f = $('myform'),
				e = $('act'),
				c = {b_changed:false};

			if ( f && e )
			{
				Validate.reset('n_zareviewer_notes');

				if ( Str.validate('n_zareviewer_notes',c , 5, 1000, true, 'Reviewer notes', true) !== false )
				{
					if ( confirm('You are rejecting this submission.\n\nContinue?\n\nOK=Yes - Cancel=No') )
					{
						e.value = 'discard';
						f.action = location.href;
						f.submit();
						return false;
					}
				}
			}
			return true;
		},

		resurrect : function(dvd_edit, pic_edit) // f_resurrect
		{
			location.href = '/utils/resurrect.html?dvd_edit=' + dvd_edit + '&pic_edit=' + pic_edit;
		},

		_validate : function(b_alert_no_change, n_nav) // f_val_appr
		{
			var i,e,f,b,m,
				c   = {b_changed:false},
				d1	= {},
				cnt = {n_country:0,
					n_region_mask:0,
					n_orig_language:0,
					n_imdb_id:0,
					n_director:0,
					n_publisher:0,
					n_upc:0,
					n_sku:0};

			if ( ! (f = $('myform')) )
				return true;

			for ( i = 0 ; (e = $('n_a_country_'      +i)); i++ ) cnt.n_country       = i+1;
			for ( i = 0 ; (e = $('n_a_region_mask_'  +i)); i++ ) cnt.n_region_mask   = i+1;
			for ( i = 0 ; (e = $('n_a_orig_language_'+i)); i++ ) cnt.n_orig_language = i+1;
			for ( i = 0 ; (e = $('n_a_imdb_id_'      +i)); i++ ) cnt.n_imdb_id       = i+1;
			for ( i = 0 ; (e = $('n_a_director_'     +i)); i++ ) cnt.n_director      = i+1;
			for ( i = 0 ; (e = $('n_a_publisher_'    +i)); i++ ) cnt.n_publisher     = i+1;
			for ( i = 0 ; (e = $('n_a_upc_'          +i)); i++ ) cnt.n_upc           = i+1;
			for ( i = 0 ; (e = $('n_a_sku_'          +i)); i++ ) cnt.n_sku           = i+1;

			Validate.reset('n_zareviewer_notes,'+
				'n_a_dvd_title,'		+
				'n_a_film_rel_year,'	+ Validate.makeResetStr('n_a_director_'     ,cnt.n_director     ) +
				Validate.makeResetStr('n_a_publisher_'    ,cnt.n_publisher    ) +
				Validate.makeResetStr('g_a_orig_language_',cnt.n_orig_language) +
				Validate.makeResetStr('g_a_country_'      ,cnt.n_country      ) +
				Validate.makeResetStr('g_a_region_mask_'  ,cnt.n_region_mask  ) +
				'g_a_genre,'			+
				'n_a_media_type,'	+
				'n_a_num_titles,'	+
				'n_a_num_disks,'		+
				'n_a_source,'		+
				'n_a_rel_status,'	+
				'n_a_film_rel_dd,'	+
				'n_a_dvd_rel_dd,'	+
				'n_a_dvd_oop_dd,'	+ Validate.makeResetStr('n_a_imdb_id_'      ,cnt.n_imdb_id      ) +
				'n_a_list_price,'	+ Validate.makeResetStr('n_a_sku_'          ,cnt.n_sku          ) +
				Validate.makeResetStr('n_a_upc_'          ,cnt.n_upc          ) +
				'n_a_asin,'			+
				'n_a_amz_country'	);

			m = DropDown.getSelValue('n_a_media_type'); if ( m == '' ) m = 'D';

			if ( Str.validate		('n_zareviewer_notes'						,c,   0,1000,1,'Reviewer notes'			,0  ) !== false )
				if ( Str.validate		('n_a_dvd_title'							,c,   0,2000,0,'DVD Title'				,1  ) !== false )
					if ( Dec.validate		('n_a_film_rel_year'						,c,1880,new Date().getFullYear() + 1,1,'Film release year'		,0  ) !== false )
						if ( Str.validateN		('n_a_director_'		,cnt.n_director		,c,   0,   0,1,'Director '				,0  ) !== false )
							if ( Str.validateN		('n_a_publisher_'		,cnt.n_publisher	,c,   0,   0,1,'Publisher '				,0  ) !== false )
								if ( Str.validateN		('n_a_orig_language_'	,cnt.n_orig_language,c,   0,   0,0,'Original language '		,1  ) !== false ) // chosen from a list, no need to validate domain
									if ( Str.validateN		('n_a_country_'			,cnt.n_country		,c,   0,   0,0,'DVD country '			,1  ) !== false ) // chosen from a list, no need to validate domain
										if ( Str.validateN		('n_a_region_mask_'		,cnt.n_region_mask	,c,   0,   0,0,'Region '				,1  ) !== false )
											if ( Region.valMediaN	('n_a_region_mask_'		,cnt.n_region_mask	,m											) !== false )
												if ( Str.validate		('n_a_genre'								,c,   0,   0,0,'Genre'					,1  ) !== false ) // chosen from a list, no need to validate domain
													if ( Str.validate		('n_a_media_type'							,c,   0,   0,0,'Media type'				,1  ) !== false ) // chosen from a list, no need to validate domain
														if ( Dec.validate		('n_a_num_titles'							,c,   0,1000,0,'Number of titles'		,1  ) !== false )
															if ( Dec.validate		('n_a_num_disks'							,c,   1,1000,0,'Number of discs'		,1  ) !== false )
																if ( Str.validate		('n_a_source'								,c,   0,   0,0,'Source'					,1  ) !== false ) // chosen from a list, no need to validate domain
																	if ( Str.validate		('n_a_rel_status'							,c,   0,   0,0,'Release status'			,1  ) !== false ) // chosen from a list, no need to validate domain
																		if ( DateTime.validate	('n_a_film_rel_dd'		,d1					,c,1880,new Date().getFullYear() + 1,1,'Film release date'		,0  ) !== false )
																			if ( DateTime.validate	('n_a_dvd_rel_dd'		,d1					,c,1990,new Date().getFullYear() + 1,1,'DVD release date'		,0  ) !== false )
																				if ( DateTime.validate	('n_a_dvd_oop_dd'		,d1					,c,1990,new Date().getFullYear() + 1,1,'Out of print date'		,0  ) !== false )
																					if ( Imdb.validateN		('n_a_imdb_id_'			,cnt.n_imdb_id		,c          ,1,'Imdb link '				,0  ) !== false )
																						if ( Dbl.validate		('n_a_list_price'							,c,   0,1000,1,'List price'				,0,1) !== false )
																							if ( Str.validateN		('n_a_sku_'				,cnt.n_sku			,c,   0,   0,1,'Studio product code '	,0  ) !== false )
																								if ( Upc.validateN		('n_a_upc_'				,cnt.n_upc			,c,   0,   0,1,'UPC '					,0  ) !== false )
																									if ( Asin.validate		('n_a_asin'									,c,   0,   0,1,'Amazon ASIN'			,0  ) !== false )
																										if ( Str.validate		('n_a_amz_country'							,c,   0,   0,1,'Amazon country'			,0  ) !== false ) // chosen from a list, no need to validate domain
																										{
																											if ( n_nav && n_nav != 1 && n_nav != 0 )
																												f.action += (f.action.indexOf('?') >= 0 ? '&' : '?') + 'pg=' + n_nav;

																											if ( (e = $('reject')) )
																											{
																												var c = {s_changed:''};
																												DvdApprove._areValuesEqual(c, 'n_', 'p_');
																												e.value = c.s_changed ? c.s_changed : 'none';
																											}
																											f.submit();
																										}

			// must return false even if we navigate from a link otherwise the link will overwrite the submit call or the location.href setting
			return false;
		},

		diffFields : function(d) // diffFields
		{
			function _diff(p, s, d) // diffFields_
			{
				function _setFieldColor(e,b,d) // setFieldColor
				{
					if ( b )
					{
						if ( d )
						{
							e.style.color = 'red';
							e.style.background = '#ffcccc';
						}
						else
						{
							e.style.color = 'green';
							e.style.background = '#ccffcc';
						}
					}
					else
					{
						e.style.color = '';
						e.style.background = '';
					}
					return b;
				};

				var e, f, g, i, t, x,
					b  = false,
					u  = s == 'a_upc',
					p1 = 'n_'+s,
					p2 =  p  +s,
					p3 = 'g_'+s;

				e = $(p1);
				if ( ! e )
				{
					t = DvdApprove._expandField(s,d);
					for ( i = 0 ; i < t ; i++ )
					{
						e = $(p1+'_'+i);
						f = $(p2+'_'+i);
						g = $(p3+'_'+i);
						if ( e && _setFieldColor((g ? g : e), ! DvdApprove._isValueEqual(e,f,u), d) )
							b = true;
					}
				}
				else
				{
					f = $(p2);
					g = $(p3);
					if ( _setFieldColor((g ? g : e), ! DvdApprove._isValueEqual(e,f,u), d) )
						b = true;
				}
				return b;
			};

			var i, b = false,
				p = d ? 'p_' : 'o_';

			for ( i = 0 ; i < DvdApprove._fields.length ; i++ )
				if ( _diff(p, DvdApprove._fields[i],d) )
					b = true;
			if ( _diff(p, 'a_amz_country',d) )
				b = true;

			if ( ! b )
			{
				if ( d )
					alert('Right side is no different than PROPOSED values.');
				else
					alert('Right side is no different than CURRENT values.');
			}
			return false;
		},

		copyFields : function(d) // copyAll
		{
			for ( var i = 0 ; i < DvdApprove._fields.length ; i++ )
				DvdApprove.copyField(DvdApprove._fields[i],d);
			return false;
		},

		copyField : function(s,d) // copyField
		{
			function _copyIt(f,d) // copyField_
			{
				d = d ? 'p_' : 'o_';
				var n = $('n_'+f),
					o = $( d  +f),
					g = $('g_'+f);
				d = o ? o.value : '';

				if ( n )
				{
					Undo.set(n, d);
					if ( g ) Undo.set(g, Decode.field(n.id, d));
				}
			};

			var i, t;

			switch ( s )
			{
				case 'a_dvd_title':
				case 'a_film_rel_year':
				case 'a_media_type':
				case 'a_num_titles':
				case 'a_num_disks':
				case 'a_source':
				case 'a_film_rel_dd':
				case 'a_dvd_rel_dd':
				case 'a_dvd_oop_dd':
				case 'a_list_price':
					_copyIt(s,d);
					break;
				case 'a_director':
				case 'a_publisher':
				case 'a_imdb_id':
				case 'a_sku':
				case 'a_upc':
					t = DvdApprove._expandField(s,d);
					for ( i = 0 ; i < t ; i++ ) _copyIt(s+'_'+i,d);
					break;
				case 'a_genre':
					_copyIt(s,d);
					break;
				case 'a_orig_language':
				case 'a_country':
					t = DvdApprove._expandField(s,d);
					for ( i = 0 ; i < t ; i++ ) _copyIt(s+'_'+i,d);
					break;
				case 'a_rel_status':
					_copyIt(s,d);
					break;
				case 'a_region_mask':
					t = DvdApprove._expandField(s,d);
					for ( i = 0 ; i < t ; i++ ) _copyIt(s+'_'+i,d);
					break;
				case 'a_asin':
					_copyIt(s,d);
					_copyIt('a_amz_country',d);
					break;
			}
			return false;
		},

		_expandField : function(s,d) // expandField
		{
			function expand(s) // expandField_
			{
				switch ( s )
				{
					case 'a_director':	    Expand.more(s,20,20,500,'og',1,0,2); break;
					case 'a_publisher':	    Expand.more(s,10,20,128,'og',1,0,2); break;
					case 'a_orig_language': Expand.more(s,10,20,  1,'og',0,0,1); break;
					case 'a_country':	    Expand.more(s, 5,15,  1,'og',0,0,1); break;
					case 'a_region_mask':   Expand.more(s, 4, 1,  1,'og',1,0,1); break;
					case 'a_imdb_id':	    Expand.more(s,62,10,500,'og',0,1,3); break;
					case 'a_sku':		    Expand.more(s, 4,16,128,'og',0,0,2); break;
					case 'a_upc':		    Expand.more(s, 4,16,128,'og',0,1,2); break;
				}
			};

			var i, e, cs = 0, ct = 0, p = d ? 'p_' : 'o_';
			for ( i = 0 ; (e = $(p+s+'_'+i)) ; i++ ) cs = i;
			for ( i = 0 ; (e = $('n_'+s+'_'+i)) ; i++ ) ct = i;
			if ( cs > ct )
			{
				for ( i = ct ; i < cs ; i++ )
				{
					expand(s);
					ct++;
				}
			}
			return ct + 1;
		},

		managePics : function(n_dvd) // picMngt
		{
			Win.openStd('/utils/x-pic-mngt.html?mod=1&obj_type=dvd&obj='+n_dvd, 'target_pic');
		},


		_areValuesEqual : function(c, p1, p2) // areValuesEqual
		{
			function _areEqual(c, p1, p2, fd) // areValuesEqual_
			{
				var e, i, b_same = true,
					u = fd == 'a_upc';

				p1 += fd;
				p2 += fd;
				e = $(p1);
				if ( ! e )
					for ( i = 0 ; b_same && (e = $(p1+'_'+i)) ; i++ )
						b_same = DvdApprove._isValueEqual(e,$(p2+'_'+i),u);
				else
					b_same = DvdApprove._isValueEqual(e,$(p2),u);

				if ( c && ! b_same ) c.s_changed += fd + ', ';
				return b_same;
			};

			var i, b_same = true;

			for ( i = 0 ; (b_same || c) && i < DvdApprove._fields.length ; i++ )
				if ( ! _areEqual(c, p1, p2, DvdApprove._fields[i]) )
					b_same = false;

			if ( b_same || c )
				if ( ! _areEqual(c, p1, p2, 'a_amz_country') )
					b_same = false;

			if ( c && c.s_changed )
				c.s_changed = c.s_changed.substr(0,c.s_changed.length-2);

			return b_same;
		},

		_isValueEqual : function(e,f,upc) // sameValues
		{
			var v1 = e ? (upc ? e.value.replace(/[^0-9]+/g,'') : e.value) : '';
			var v2 = f ? (upc ? f.value.replace(/[^0-9]+/g,'') : f.value) : '';
			return v1 == v2;
		}
	};

/* --------------------------------------------------------------------- */

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdApprove =
{
	_fields :  ['a_dvd_title', 'a_film_rel_year', 'a_director', 'a_publisher', 'a_orig_language', 'a_country', 'a_region_mask',
				'a_genre', 'a_media_type', 'a_num_titles', 'a_num_disks', 'a_source', 'a_rel_status', 'a_film_rel_dd',
				'a_dvd_rel_dd', 'a_dvd_oop_dd', 'a_imdb_id', 'a_list_price', 'a_sku', 'a_upc', 'a_asin'],

	inputPicName	: '',
	inputPicDispo	: '',
	inputPicReq		: '',
	inputPicMod		: '',
	inputPicId		: 0,
	inputPicEditId	: 0,

	setup : function() // initDvdAppr
	{
		var e;

		if ( (e = $('myform')) )
		{
			DvdApprove._setCopyButtons();
			Calendar.setup({inputField:'n_a_film_rel_dd',clearInput:'x_a_film_rel_dd',ifFormat:'%Y-%m-%d',button:'h_a_film_rel_dd',singleClick:true,step:1});
			Calendar.setup({inputField:'n_a_dvd_rel_dd',clearInput:'x_a_dvd_rel_dd',ifFormat:'%Y-%m-%d',button:'h_a_dvd_rel_dd',singleClick:true,step:1});
			Calendar.setup({inputField:'n_a_dvd_oop_dd',clearInput:'x_a_dvd_oop_dd',ifFormat:'%Y-%m-%d',button:'h_a_dvd_oop_dd',singleClick:true,step:1});
			Menus.setup();
			DvdEdit._initSearch		('h_a_dvd_title' ,'search');
			DvdEdit._initTitleLookup('b_a_dvd_title' ,'filmaf',Title.seekInFilmaf);
			DvdEdit._initTitleLookup('b1_a_dvd_title','imdb'  ,Title.seekInImdb );
			DvdEdit._initTitleLookup('b2_a_dvd_title','amz'   ,Title.seekInAmz  );
			Context.attach('h_a_dvd_title',false      ,'menu-dvd-title');
			Context.attach('mod_txt'      ,false      ,'menu-moddvd'   );
			Context.attach('h_a_genre'    ,'g_a_genre','menu-genre-no' );
			Expand.attach('a_orig_language');
			Expand.attach('a_country'      );
			Expand.attach('a_region_mask'  );
			Expand.attach('a_director'     );
			Expand.attach('a_publisher'    );
			Expand.attach('a_imdb_id'      );
			Expand.attach('a_upc'          );
			
			Img.attach();
			$('b_a_amz_country').onclick = Asin.test
		}
	},

	_setCopyButtons : function() // setCopyButtons
	{
		var i, j, e, f, s, b;

		for ( i = 0 ; i < DvdApprove._fields.length ; i++ )
		{
			s = DvdApprove._fields[i];
			b = false;

			if ( (e = $('p_'+s)) )
			{
				if ( (f = $('o_'+s)) )
					b = e.value != f.value;
				else
					b = e.value != '';
			}
			else
			{
				for ( j = 0 ; ! b && (e = $('p_'+s+'_'+j)) ; j++ )
					if ( (f = $('o_'+s+'_'+j)) )
						b = e.value != f.value;
					else
						b = e.value != '';
				if ( ! b && (f = $('p_'+s+'_'+j)) )
					b = true;
			}

			j = s.substr(1);
			if ( (e = $('t1'+j)) )
			{
				e.style.textAlign = 'center';
				e.innerHTML = b ? "<input type='button' onclick='DvdApprove.copyField(\""+s+"\",false)' value='&hellip;&laquo; Rej' style='width:54px' />" : "<span class='hl'>...</span>";
			}
			if ( (e = $('t2'+j)) )
			{
				e.style.textAlign = 'center';
				e.innerHTML = b ? "<input type='button' onclick='DvdApprove.copyField(\""+s+"\",true)' value='App &raquo;&hellip;' style='width:54px' />" : "<span class='hl'>...</span>";
			}
		}
	},

	approveAll : function(n_nav) // f_approve_all
	{
		if ( ! DvdApprove._areValuesEqual(null, 'n_', 'o_') )
		{
			var y = confirm("Your changes on the right side will be lost as we overlay the member's proposed changes. Continue?\n\nOK=Yes - Cancel=No");
			if ( ! y ) return true;
		}
		DvdApprove.copyFields(true);
		return DvdApprove._validate(true, n_nav);
	},

	saveRight : function(n_nav) // f_save_right
	{
		if ( DvdApprove._areValuesEqual(null, 'n_', 'o_') )
		{
			var y = confirm('You are attempting to save the values on the right side but they have not been changed.\nWould it not be better to cancel out and reject the submission?\n\nContinue?\n\nOK=Yes - Cancel=No');
			if ( ! y ) return true;
		}
		return DvdApprove._validate(true, n_nav);
	},

	discard : function() // f_discard
	{
		var f = $('myform'),
			e = $('act'),
			c = {b_changed:false};

		if ( f && e )
		{
			Validate.reset('n_zareviewer_notes');

			if ( Str.validate('n_zareviewer_notes',c , 5, 1000, true, 'Reviewer notes', true) !== false )
			{
				if ( confirm('You are rejecting this submission.\n\nContinue?\n\nOK=Yes - Cancel=No') )
				{
					e.value = 'discard';
					f.action = location.href;
					f.submit();
					return false;
				}
			}
		}
		return true;
	},

	resurrect : function(dvd_edit, pic_edit) // f_resurrect
	{
		location.href = '/utils/resurrect.html?dvd_edit=' + dvd_edit + '&pic_edit=' + pic_edit;
	},

	_validate : function(b_alert_no_change, n_nav) // f_val_appr
	{
		var i,e,f,b,m,
			c   = {b_changed:false},
			d1	= {},
			cnt = {n_country:0,
				   n_region_mask:0,
				   n_orig_language:0,
				   n_imdb_id:0,
				   n_director:0,
				   n_publisher:0,
				   n_upc:0,
				   n_sku:0};

		if ( ! (f = $('myform')) )
			return true;

		for ( i = 0 ; (e = $('n_a_country_'      +i)); i++ ) cnt.n_country       = i+1;
		for ( i = 0 ; (e = $('n_a_region_mask_'  +i)); i++ ) cnt.n_region_mask   = i+1;
		for ( i = 0 ; (e = $('n_a_orig_language_'+i)); i++ ) cnt.n_orig_language = i+1;
		for ( i = 0 ; (e = $('n_a_imdb_id_'      +i)); i++ ) cnt.n_imdb_id       = i+1;
		for ( i = 0 ; (e = $('n_a_director_'     +i)); i++ ) cnt.n_director      = i+1;
		for ( i = 0 ; (e = $('n_a_publisher_'    +i)); i++ ) cnt.n_publisher     = i+1;
		for ( i = 0 ; (e = $('n_a_upc_'          +i)); i++ ) cnt.n_upc           = i+1;
		for ( i = 0 ; (e = $('n_a_sku_'          +i)); i++ ) cnt.n_sku           = i+1;

		Validate.reset('n_zareviewer_notes,'+
					   'n_a_dvd_title,'		+
					   'n_a_film_rel_year,'	+ Validate.makeResetStr('n_a_director_'     ,cnt.n_director     ) +
											  Validate.makeResetStr('n_a_publisher_'    ,cnt.n_publisher    ) +
											  Validate.makeResetStr('g_a_orig_language_',cnt.n_orig_language) +
											  Validate.makeResetStr('g_a_country_'      ,cnt.n_country      ) +
											  Validate.makeResetStr('g_a_region_mask_'  ,cnt.n_region_mask  ) +
					   'g_a_genre,'			+
					   'n_a_media_type,'	+
					   'n_a_num_titles,'	+
					   'n_a_num_disks,'		+
					   'n_a_source,'		+
					   'n_a_rel_status,'	+
					   'n_a_film_rel_dd,'	+
					   'n_a_dvd_rel_dd,'	+
					   'n_a_dvd_oop_dd,'	+ Validate.makeResetStr('n_a_imdb_id_'      ,cnt.n_imdb_id      ) +
					   'n_a_list_price,'	+ Validate.makeResetStr('n_a_sku_'          ,cnt.n_sku          ) +
											  Validate.makeResetStr('n_a_upc_'          ,cnt.n_upc          ) +
					   'n_a_asin,'			+
					   'n_a_amz_country'	);

		m = DropDown.getSelValue('n_a_media_type'); if ( m == '' ) m = 'D';

		if ( Str.validate		('n_zareviewer_notes'						,c,   0,1000,1,'Reviewer notes'			,0  ) !== false )
		if ( Str.validate		('n_a_dvd_title'							,c,   0,2000,0,'DVD Title'				,1  ) !== false )
		if ( Dec.validate		('n_a_film_rel_year'						,c,1880,2030,1,'Film release year'		,0  ) !== false )
		if ( Str.validateN		('n_a_director_'		,cnt.n_director		,c,   0,   0,1,'Director '				,0  ) !== false )
		if ( Str.validateN		('n_a_publisher_'		,cnt.n_publisher	,c,   0,   0,1,'Publisher '				,0  ) !== false )
		if ( Str.validateN		('n_a_orig_language_'	,cnt.n_orig_language,c,   0,   0,0,'Original language '		,1  ) !== false ) // chosen from a list, no need to validate domain
		if ( Str.validateN		('n_a_country_'			,cnt.n_country		,c,   0,   0,0,'DVD country '			,1  ) !== false ) // chosen from a list, no need to validate domain
		if ( Str.validateN		('n_a_region_mask_'		,cnt.n_region_mask	,c,   0,   0,0,'Region '				,1  ) !== false )
		if ( Region.valMediaN	('n_a_region_mask_'		,cnt.n_region_mask	,m											) !== false )
		if ( Str.validate		('n_a_genre'								,c,   0,   0,0,'Genre'					,1  ) !== false ) // chosen from a list, no need to validate domain
		if ( Str.validate		('n_a_media_type'							,c,   0,   0,0,'Media type'				,1  ) !== false ) // chosen from a list, no need to validate domain
		if ( Dec.validate		('n_a_num_titles'							,c,   0,1000,0,'Number of titles'		,1  ) !== false )
		if ( Dec.validate		('n_a_num_disks'							,c,   1,1000,0,'Number of discs'		,1  ) !== false )
		if ( Str.validate		('n_a_source'								,c,   0,   0,0,'Source'					,1  ) !== false ) // chosen from a list, no need to validate domain
		if ( Str.validate		('n_a_rel_status'							,c,   0,   0,0,'Release status'			,1  ) !== false ) // chosen from a list, no need to validate domain
		if ( DateTime.validate	('n_a_film_rel_dd'		,d1					,c,1880,2030,1,'Film release date'		,0  ) !== false )
		if ( DateTime.validate	('n_a_dvd_rel_dd'		,d1					,c,1990,2030,1,'DVD release date'		,0  ) !== false )
		if ( DateTime.validate	('n_a_dvd_oop_dd'		,d1					,c,1990,2030,1,'Out of print date'		,0  ) !== false )
		if ( Imdb.validateN		('n_a_imdb_id_'			,cnt.n_imdb_id		,c          ,1,'Imdb link '				,0  ) !== false )
		if ( Dbl.validate		('n_a_list_price'							,c,   0,1000,1,'List price'				,0,1) !== false )
		if ( Str.validateN		('n_a_sku_'				,cnt.n_sku			,c,   0,   0,1,'Studio product code '	,0  ) !== false )
		if ( Upc.validateN		('n_a_upc_'				,cnt.n_upc			,c,   0,   0,1,'UPC '					,0  ) !== false )
		if ( Asin.validate		('n_a_asin'									,c,   0,   0,1,'Amazon ASIN'			,0  ) !== false )
		if ( Str.validate		('n_a_amz_country'							,c,   0,   0,1,'Amazon country'			,0  ) !== false ) // chosen from a list, no need to validate domain
		{
			if ( n_nav && n_nav != 1 && n_nav != 0 )
				f.action += (f.action.indexOf('?') >= 0 ? '&' : '?') + 'pg=' + n_nav;

			if ( (e = $('reject')) )
			{
				var c = {s_changed:''};
				DvdApprove._areValuesEqual(c, 'n_', 'p_');
				e.value = c.s_changed ? c.s_changed : 'none';
			}
			f.submit();
		}

		// must return false even if we navigate from a link otherwise the link will overwrite the submit call or the location.href setting
		return false;
	},

	diffFields : function(d) // diffFields
	{
		function _diff(p, s, d) // diffFields_
		{
			function _setFieldColor(e,b,d) // setFieldColor
			{
				if ( b )
				{
					if ( d )
					{
						e.style.color = 'red';
						e.style.background = '#ffcccc';
					}
					else
					{
						e.style.color = 'green';
						e.style.background = '#ccffcc';
					}
				}
				else
				{
					e.style.color = '';
					e.style.background = '';
				}
				return b;
			};

			var e, f, g, i, t, x,
				b  = false,
				u  = s == 'a_upc',
				p1 = 'n_'+s,
				p2 =  p  +s,
				p3 = 'g_'+s;

			e = $(p1);
			if ( ! e )
			{
				t = DvdApprove._expandField(s,d);
				for ( i = 0 ; i < t ; i++ )
				{
					e = $(p1+'_'+i);
					f = $(p2+'_'+i);
					g = $(p3+'_'+i);
					if ( e && _setFieldColor((g ? g : e), ! DvdApprove._isValueEqual(e,f,u), d) )
						b = true;
				}
			}
			else
			{
				f = $(p2);
				g = $(p3);
				if ( _setFieldColor((g ? g : e), ! DvdApprove._isValueEqual(e,f,u), d) )
					b = true;
			}
			return b;
		};

		var i, b = false,
			p = d ? 'p_' : 'o_';

		for ( i = 0 ; i < DvdApprove._fields.length ; i++ )
			if ( _diff(p, DvdApprove._fields[i],d) )
				b = true;
		if ( _diff(p, 'a_amz_country',d) )
			b = true;

		if ( ! b )
		{
			if ( d )
			   alert('Right side is no different than PROPOSED values.');
			else
			   alert('Right side is no different than CURRENT values.');
		}
		return false;
	},

	copyFields : function(d) // copyAll
	{
		for ( var i = 0 ; i < DvdApprove._fields.length ; i++ )
			DvdApprove.copyField(DvdApprove._fields[i],d);
		return false;
	},

	copyField : function(s,d) // copyField
	{
		function _copyIt(f,d) // copyField_
		{
			d = d ? 'p_' : 'o_';
			var n = $('n_'+f),
				o = $( d  +f),
				g = $('g_'+f);
				d = o ? o.value : '';

			if ( n )
			{
				Undo.set(n, d);
				if ( g ) Undo.set(g, Decode.field(n.id, d));
			}
		};

		var i, t;

		switch ( s )
		{
		case 'a_dvd_title':
		case 'a_film_rel_year':
		case 'a_media_type':
		case 'a_num_titles':
		case 'a_num_disks':
		case 'a_source':
		case 'a_film_rel_dd':
		case 'a_dvd_rel_dd':
		case 'a_dvd_oop_dd':
		case 'a_list_price':
			_copyIt(s,d);
			break;
		case 'a_director':
		case 'a_publisher':
		case 'a_imdb_id':
		case 'a_sku':
		case 'a_upc':
			t = DvdApprove._expandField(s,d);
			for ( i = 0 ; i < t ; i++ ) _copyIt(s+'_'+i,d);
			break;
		case 'a_genre':
			_copyIt(s,d);
			break;
		case 'a_orig_language':
		case 'a_country':
			t = DvdApprove._expandField(s,d);
			for ( i = 0 ; i < t ; i++ ) _copyIt(s+'_'+i,d);
			break;
		case 'a_rel_status':
			_copyIt(s,d);
			break;
		case 'a_region_mask':
			t = DvdApprove._expandField(s,d);
			for ( i = 0 ; i < t ; i++ ) _copyIt(s+'_'+i,d);
			break;
		case 'a_asin':
			_copyIt(s,d);
			_copyIt('a_amz_country',d);
			break;
		}
		return false;
	},

	_expandField : function(s,d) // expandField
	{
		function expand(s) // expandField_
		{
			switch ( s )
			{
			case 'a_director':	    Expand.more(s,20,20,500,'og',1,0,2); break;
			case 'a_publisher':	    Expand.more(s,10,20,128,'og',1,0,2); break;
			case 'a_orig_language': Expand.more(s,10,20,  1,'og',0,0,1); break;
			case 'a_country':	    Expand.more(s, 5,15,  1,'og',0,0,1); break;
			case 'a_region_mask':   Expand.more(s, 4, 1,  1,'og',1,0,1); break;
			case 'a_imdb_id':	    Expand.more(s,62,10,500,'og',0,1,3); break;
			case 'a_sku':		    Expand.more(s, 4,16,128,'og',0,0,2); break;
			case 'a_upc':		    Expand.more(s, 4,16,128,'og',0,1,2); break;
			}
		};

		var i, e, cs = 0, ct = 0, p = d ? 'p_' : 'o_';
		for ( i = 0 ; (e = $(p+s+'_'+i)) ; i++ ) cs = i;
		for ( i = 0 ; (e = $('n_'+s+'_'+i)) ; i++ ) ct = i;
		if ( cs > ct )
		{
			for ( i = ct ; i < cs ; i++ )
			{
				expand(s);
				ct++;
			}
		}
		return ct + 1;
	},

	managePics : function(n_dvd) // picMngt
	{
		Win.openStd('/utils/x-pic-mngt.html?mod=1&obj_type=dvd&obj='+n_dvd, 'target_pic');
	},


	_areValuesEqual : function(c, p1, p2) // areValuesEqual
	{
		function _areEqual(c, p1, p2, fd) // areValuesEqual_
		{
			var e, i, b_same = true,
			u = fd == 'a_upc';

			p1 += fd;
			p2 += fd;
			e = $(p1);
			if ( ! e )
				for ( i = 0 ; b_same && (e = $(p1+'_'+i)) ; i++ )
					b_same = DvdApprove._isValueEqual(e,$(p2+'_'+i),u);
			else
				b_same = DvdApprove._isValueEqual(e,$(p2),u);

			if ( c && ! b_same ) c.s_changed += fd + ', ';
			return b_same;
		};

		var i, b_same = true;

		for ( i = 0 ; (b_same || c) && i < DvdApprove._fields.length ; i++ )
			if ( ! _areEqual(c, p1, p2, DvdApprove._fields[i]) )
				b_same = false;

		if ( b_same || c )
			if ( ! _areEqual(c, p1, p2, 'a_amz_country') )
				b_same = false;

		if ( c && c.s_changed )
			c.s_changed = c.s_changed.substr(0,c.s_changed.length-2);

		return b_same;
	},

	_isValueEqual : function(e,f,upc) // sameValues
	{
		var v1 = e ? (upc ? e.value.replace(/[^0-9]+/g,'') : e.value) : '';
		var v2 = f ? (upc ? f.value.replace(/[^0-9]+/g,'') : f.value) : '';
		return v1 == v2;
	}
};

/* --------------------------------------------------------------------- */

