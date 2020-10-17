/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdEdit =
	{
		setup : function() // initDvdEdit
		{
			var e;

			if ( (e = $('myform')) )
			{
				Img.attach();
				Calendar.setup({inputField:'n_a_film_rel_dd',clearInput:'x_a_film_rel_dd',ifFormat:'%Y-%m-%d',button:'h_a_film_rel_dd',singleClick:true,step:1});
				Calendar.setup({inputField:'n_a_dvd_rel_dd',clearInput:'x_a_dvd_rel_dd',ifFormat:'%Y-%m-%d',button:'h_a_dvd_rel_dd',singleClick:true,step:1});
				Calendar.setup({inputField:'n_a_dvd_oop_dd',clearInput:'x_a_dvd_oop_dd',ifFormat:'%Y-%m-%d',button:'h_a_dvd_oop_dd',singleClick:true,step:1});
				Menus.setup();

				DvdEdit._initSearch		('h_a_dvd_title' ,'search');
				DvdEdit._initTitleLookup('b_a_dvd_title' ,'filmaf',Title.seekInFilmaf);
				DvdEdit._initTitleLookup('b1_a_dvd_title','imdb'  ,Title.seekInImdb  );
				DvdEdit._initTitleLookup('b2_a_dvd_title','amz'   ,Title.seekInAmz   );
				Context.attach('h_a_dvd_title' ,false      ,'menu-dvd-title');
				Context.attach('h_a_genre'     ,'g_a_genre','menu-genre-no' );
				Expand.attach('a_orig_language');
				Expand.attach('a_country'      );
				Expand.attach('a_region_mask'  );
				Expand.attach('a_director'     );
				Expand.attach('a_publisher'    );
				Expand.attach('a_imdb_id'      );
				Expand.attach('a_upc'          );
				Expand.attach('a_amz'          );
				DvdEdit._initOutOfPrint();

				$('b_a_amz_country').onclick = Asin.test
			}
		},

		onPopup : function(el)
		{
			if ( ! this.id ) return;
			var i = this.menu.items,
				t = this.menu.target,
				z = this;

			switch ( this.id )
			{
				default:
					z.filmaf = ObjEdit.onPopup;
					z.filmaf(this,el);
					break;
			}
		},

		onClick : function(action) // onMenuClick2
		{
			if ( action.info && action.info.id )
			{
				var id = action.info.id,
					z  = this;

				switch ( id.substr(0,7) )
				{
					default:
						z.filmaf = ObjEdit.onClick;
						z.filmaf(this,action);
						break;
				}
			}
		},

		_initSearch : function(n,s)
		{
			var e;

			if ( (e = $(n)) )
			{
				e.value = s;
				e.style.width= '60px';
			}
		},

		_initTitleLookup : function(n,s,f) // initTitleLookup
		{
			var e;

			if ( (e = $(n)) )
			{
				e.value = s + ' ' + e.value;
				e.onclick = f;
				e.style.width= '60px';
			}
		},

		_initOutOfPrint : function() // initOutOfPrint
		{
			var e = $('cb_out_of_print'),
				f = $('n_a_rel_status' );

			if ( e )
			{
				e.checked = DropDown.getSelValue(f) == 'O';
				e.onclick =
					function()
					{
						var a, b;
						if ( (a = $('n_a_rel_status')) )
						{
							if ( this.checked )
								b = 'O';
							else
							if ( (b = $('o_a_rel_status')) )
								b = b.value == 'O' ? 'C' : b.value;
							else
								b = 'C';
							DropDown.selectFromVal(a,b);
							Undo.change(a);
						}
					};
			}

			if ( f )
			{
				f.onchange =
					function()
					{
						var a = $('cb_out_of_print');
						if ( a )
						{
							a.checked = DropDown.getSelValue(this) == 'O';
							Undo.change(this);
						}
					};

				f.notifySet =
					function()
					{
						var a = $('cb_out_of_print');
						if ( a ) a.checked = DropDown.getSelValue(this) == 'O';
					};
			}
		},

		validate : function(b_alert_no_change, n_nav) // f_val_dvd
		{
			var cnt = {n_country:0, n_region_mask:0, n_orig_language:0, n_imdb_id:0, n_director:0, n_publisher:0, n_upc:0, n_sku:0},
				c   = {b_changed:false},
				d1	= {},
				b	= true,
				mod	= false,
				eid	= 0,
				i, e, f, m;

			if (   (f = $('edit_id'  )) ) eid = Edit.getInt(f);
			if (   (f = $('moderator')) ) mod = f.value == 1;
			if ( ! (f = $('myform'   )) ) return true;

			for ( i = 0 ; (e = $('n_a_country_'      +i)); i++ ) cnt.n_country       = i+1;
			for ( i = 0 ; (e = $('n_a_region_mask_'  +i)); i++ ) cnt.n_region_mask   = i+1;
			for ( i = 0 ; (e = $('n_a_orig_language_'+i)); i++ ) cnt.n_orig_language = i+1;
			for ( i = 0 ; (e = $('n_a_imdb_id_'      +i)); i++ ) cnt.n_imdb_id       = i+1;
			for ( i = 0 ; (e = $('n_a_director_'     +i)); i++ ) cnt.n_director      = i+1;
			for ( i = 0 ; (e = $('n_a_publisher_'    +i)); i++ ) cnt.n_publisher     = i+1;
			for ( i = 0 ; (e = $('n_a_upc_'          +i)); i++ ) cnt.n_upc           = i+1;
			for ( i = 0 ; (e = $('n_a_sku_'          +i)); i++ ) cnt.n_sku           = i+1;

			Validate.reset('n_zaupdate_justify,'	+
				'n_zaproposer_notes,'	+
				'n_a_dvd_title,'			+
				'n_a_film_rel_year,'		+
				'n_a_film_rel_dd,'		+ Validate.makeResetStr('g_a_orig_language_',cnt.n_orig_language)+
				'g_a_genre,'				+
				'n_a_source,'			+
				'n_a_media_type,'		+ Validate.makeResetStr('g_a_region_mask_'  ,cnt.n_region_mask  )+
				Validate.makeResetStr('g_a_country_'      ,cnt.n_country      )+
				'n_a_rel_status,'		+
				'n_a_dvd_rel_dd,'		+
				'n_a_dvd_oop_dd,'		+
				'n_a_asin,'				+
				'n_a_amz_country,'		+ Validate.makeResetStr('n_a_imdb_id_'      ,cnt.n_imdb_id      )+
				Validate.makeResetStr('n_a_director_'     ,cnt.n_director     )+
				Validate.makeResetStr('n_a_publisher_'    ,cnt.n_publisher    )+
				'n_a_num_titles,'		+
				'n_a_num_disks,'			+ Validate.makeResetStr('n_a_upc_'          ,cnt.n_upc          )+
				Validate.makeResetStr('n_a_sku_'          ,cnt.n_sku          )+
				'n_a_list_price');

			m  = DropDown.getSelValue('n_a_media_type'); if ( m == '' ) m = 'D';

			if ( Str.validate		('n_a_dvd_title'							,c,   0,2000,0,'DVD Title'				,1  ) !== false )
				if ( Dec.validate		('n_a_film_rel_year'						,c,1880,new Date().getFullYear() + 1,1,'Film release year'		,0  ) !== false )
					if ( DateTime.validate	('n_a_film_rel_dd'		,d1					,c,1880,new Date().getFullYear() + 1,1,'Film release date'		,0  ) !== false )
						if ( Str.validateN		('n_a_orig_language_'	,cnt.n_orig_language,c,   0,   0,0,'Original language '		,1  ) !== false ) // chosen from a list, no need to validate domain
							if ( Str.validate		('n_a_genre'								,c,   0,   0,0,'Genre'					,1  ) !== false ) // chosen from a list, no need to validate domain
								if ( Str.validate		('n_a_source'								,c,   0,   0,0,'Source'					,1  ) !== false ) // chosen from a list, no need to validate domain
									if ( Str.validate		('n_a_media_type'							,c,   0,   0,0,'Media type'				,1  ) !== false ) // chosen from a list, no need to validate domain
										if ( Str.validateN		('n_a_region_mask_'		,cnt.n_region_mask	,c,   0,   0,0,'Region '				,1  ) !== false )
											if ( Region.valMediaN	('n_a_region_mask_'		,cnt.n_region_mask	,m											) !== false )
												if ( Str.validateN		('n_a_country_'			,cnt.n_country		,c,   0,   0,0,'DVD country '			,1  ) !== false ) // chosen from a list, no need to validate domain
													if ( Str.validate		('n_a_rel_status'							,c,   0,   0,0,'Release status'			,1  ) !== false ) // chosen from a list, no need to validate domain
														if ( DateTime.validate	('n_a_dvd_rel_dd'		,d1					,c,1990,new Date().getFullYear() + 1,1,'DVD release date'		,0  ) !== false )
															if ( DateTime.validate	('n_a_dvd_oop_dd'		,d1					,c,1990,new Date().getFullYear() + 1,1,'Out of print date'		,0  ) !== false )
																if ( Asin.validate		('n_a_asin'									,c,   0,   0,1,'Amazon ASIN'			,0  ) !== false )
																	if ( Str.validate		('n_a_amz_country'							,c,   0,   0,1,'Amazon country'			,0  ) !== false ) // chosen from a list, no need to validate domain
																		if ( Imdb.validateN		('n_a_imdb_id_'			,cnt.n_imdb_id		,c          ,1,'Imdb link '				,0  ) !== false )
																			if ( Str.validateN		('n_a_director_'		,cnt.n_director		,c,   0,   0,1,'Director '				,0  ) !== false )
																				if ( Str.validateN		('n_a_publisher_'		,cnt.n_publisher	,c,   0,   0,1,'Publisher '				,0  ) !== false )
																					if ( Dec.validate		('n_a_num_titles'							,c,   0,1000,0,'Number of titles'		,1  ) !== false )
																						if ( Dec.validate		('n_a_num_disks'							,c,   1,1000,0,'Number of discs'		,1  ) !== false )
																							if ( Upc.validateN		('n_a_upc_'				,cnt.n_upc			,c,   0,   0,1,'UPC '					,0  ) !== false )
																								if ( Str.validateN		('n_a_sku_'				,cnt.n_sku			,c,   0,   0,1,'Studio product code '	,0  ) !== false )
																									if ( Dbl.validate		('n_a_list_price'							,c,   0,1000,1,'List price'				,0,1) !== false )
																									{
																										if  ( c.b_changed || eid > 0 )
																										{
																											if ( b ) b = Str.validate('n_zaproposer_notes'  ,c,   0,1000,1   ,'Proposer notes'      ,0   ) !== false;
																											if ( b ) b = Str.validate('n_zaupdate_justify'  ,c,   0, 200,mod ,'Update justification',!mod) !== false;
																										}
																										if ( b )
																										{
																											if ( n_nav && n_nav != 1 && n_nav != 0 )
																												f.action += (f.action.indexOf('?') >= 0 ? '&' : '?') + 'pg=' + n_nav;

																											if ( c.b_changed )
																											{
																												if ( ! c.b_undo )
																												{
																													b = confirm('The fields in this submission are the same as the current information for this title.\n\nDo you wish to withdraw this submission request?\n\nOK=Yes - Cancel=No');
																													if ( b && (e = $('act')) )
																													{
																														e.value = 'del_sub';
																														f.action = location.href;
																													}
																												}
																												f.submit();
																											}
																											else
																											{
																												location.href = f.action;
																												if ( b_alert_no_change ) alert('No changes detected.  Nothing to save.');
																											}
																										}
																									}

			// must return false even if we navigate from a link otherwise the link will overwrite the submit call or the location.href setting
			return false;
		},

		removeUpcDashes : function()
		{
			var e, i;

			for ( i = 0 ; (e = $('n_a_upc_'+i)); i++ )
				e.value = e.value.replace(/-/g,'');
		},

		withdrawSub : function() // f_del_sub
		{
			var y, e;
			if ( (y = confirm('Do you wish to withdraw this submission request?\n\nOK=Yes - Cancel=No')) && (e = $('act')) )
			{
				e.value = 'del_sub';
				if ( (e = $('myform')) )
				{
					e.action = location.href;
					e.submit();
				}
			}
			return false;
		},

		managePics : function() // f_pic_manage
		{
			var e, f, y;
			if ( (e = $('edit_id')) && (f = $('dvd_id')) )
			{
				e = Edit.getInt(e);
				f = Edit.getInt(f);
				if ( e <= 0 && f <= 0 )
				{
					if ( confirm('In order to upload a picture for a new title you must first save your request.\nDo you wish to save it now?\n\nOK=Yes - Cancel=No') )
						if ( (e = $('b_submit')) )
							e.onclick();
				}
				else
				{
					Win.openPop(false, 'target_pic', '/utils/x-pic-mngt.html?obj_type=dvd&obj='+f+'&obj_edit='+e, 1080, 900, 1, 1)
				}
			}
			return false;
		},

		setSearchVal : function(mode, target, val)
		{
			var e, s;

			switch( mode )
			{
				case 'dvd': if ( (e = $('n_a_dvd_title'					 )) ) Edit.insertAtCursor(e,val); break;
				case 'dir': if ( (e = $('n'+Filmaf.inputLine.substr(1,100))) ) e.value = val; break;
				case 'pub': if ( (e = $('n'+Filmaf.inputLine.substr(1,100))) ) e.value = val; break;
				default: return;
			}

			Context.close();
		}
	};

/* --------------------------------------------------------------------- */

