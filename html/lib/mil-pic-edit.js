/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var PicEdit =
	{
		setup : function() // initDvdAppr
		{
			if ( (e = $('myform')) )
			{
				Img.attach();
				Menus.setup();
				Context.attach('mod_txt', false, 'menu-modpic');
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

		onClick : function(action) // onMenuClick
		{
			if ( action.info && action.info.id )
			{
				var id = action.info.id;

				switch ( id.substr(0,7) )
				{
					case 'hm_mpi_':
						PicEdit._prependReviwerNotes(id.substr(7));
						break;
				}
			}
		},

		_prependReviwerNotes : function(e) // prependReviwerNotes
		{
			var f = $('n_p_reviewer_notes');
			if ( f && (e = $('mpi'+e)) )
				f.value = e.innerHTML;
		},

		reject : function() // picReject
		{
			var b = true,
				c = {b_changed:false},
				e = $('n_p_reviewer_notes'),
				f = $('modjust'),
				g = $('rjform');

			if ( !e || !f || !g )
				return true;

			Validate.reset('n_p_reviewer_notes');
			if ( Str.validate('n_p_reviewer_notes',c , 0, 1000, false, 'Reviewer notes', true) !== false )
			{
				f.value = e.value;
				PicMngt._picEdit(PicMngt._getpicEditUrl('rej',0,Url.getVal('pic_edit')), '', g);
			}
		},

		deleteApproval : function() // picDeleteApproval
		{
			PicMngt._picEdit(PicMngt._getpicEditUrl('dap',Url.getVal('pic'),Url.getVal('pic_edit')), '', 0);
		},

		reset : function() // f_reset
		{
			var e;
			if ( (e = $('n_p_img_treatment_B')) ) e.checked = true;
			if ( (e = $('n_p_rot_degrees'	   )) ) e.value = '0';
			if ( (e = $('n_p_rot_degrees_x'  )) ) e.value = '0';
			if ( (e = $('n_p_crop_fuzz'	   )) ) e.value = '0';
			if ( (e = $('n_p_crop_x1'	   )) ) e.value = '0';
			if ( (e = $('n_p_crop_x2'	   )) ) e.value = '0';
			if ( (e = $('n_p_crop_y1'	   )) ) e.value = '0';
			if ( (e = $('n_p_crop_y2'	   )) ) e.value = '0';
			if ( (e = $('n_p_black_pt'	   )) ) e.value = '0';
			if ( (e = $('n_p_white_pt'	   )) ) e.value = '100';
			if ( (e = $('n_p_gamma'	   )) ) e.value = '1.0';
		},

		validate : function(b_save,b_as_new) // f_val_pic
		{
			var b = true,
				c = {b_changed:false},
				d = {b_changed:false},
				e, f, pn, rn, mod;

			if ( ! (f = $('myform')) )
				return true;

			pn  = (e = $('n_p_proposer_notes')) && ! e.readOnly;
			rn  = (e = $('n_p_reviewer_notes')) && ! e.readOnly;
			mod = (e = $('mod')) && e.value == '1';

			Validate.reset(
				( pn ? 'n_p_proposer_notes,'	: '')+
				( rn ? 'n_p_reviewer_notes,'	: '')+
				'n_p_copy_holder,'		+
				'n_p_copy_year,'			+
				'n_p_caption,'			+
				'n_p_rot_degrees,'		+
				'n_p_rot_degrees_x,'		+
				'n_p_crop_fuzz,'			+
				'n_p_crop_x1,'			+
				'n_p_crop_x2,'			+
				'n_p_crop_y1,'			+
				'n_p_crop_y2,'			+
				'n_p_black_pt,'			+
				'n_p_white_pt,'			+
				'n_p_gamma'				);

			if (		Str.validate('n_p_copy_holder'		,c,   0, 100,1  ,'Copyright holder'		,1   ) !== false )
				if (		Dec.validate('n_p_copy_year'		,c,1880, new Date().getFullYear(),1  ,'Copyright year'		,1   ) !== false )
					if (		Str.validate('n_p_caption'			,c,   0,1000,1  ,'Picture caption text'	,1   ) !== false )
						if (		Dbl.validate('n_p_rot_degrees'		,d,-360, 360,1  ,'Rotation in degrees'	,1 ,0) !== false )
							if (		Dec.validate('n_p_rot_degrees_x'	,d,-100, 100,1  ,'Rotation in Y pixels'	,1   ) !== false )
								if (		Dbl.validate('n_p_crop_fuzz'		,d,   0, 100,1  ,'Autocrop fuzz'		,1 ,0) !== false )
									if (		Dec.validate('n_p_crop_x1'			,d,   0,2000,1  ,'Left margin'			,1   ) !== false )
										if (		Dec.validate('n_p_crop_x2'			,d,   0,2000,1  ,'Right margin'			,1   ) !== false )
											if (		Dec.validate('n_p_crop_y1'			,d,   0,2000,1  ,'Top margin'			,1   ) !== false )
												if (		Dec.validate('n_p_crop_y2'			,d,   0,2000,1  ,'Bottom margin'		,1   ) !== false )
													if (		Dec.validate('n_p_black_pt'			,d,   0,  90,1  ,'Levels black point'	,1   ) !== false )
														if (		Dec.validate('n_p_white_pt'			,d,  10, 100,1  ,'Levels white point'	,1   ) !== false )
															if (		Dbl.validate('n_p_gamma'			,d,0.10,   3,1  ,'Gamma adjustment'		,1 ,0) !== false )
																if ( !pn || Str.validate('n_p_proposer_notes'	,c,   0,1000,mod,'Justification'		,!mod) !== false )
																	if ( !rn || Str.validate('n_p_reviewer_notes'	,c,   0,1000,1  ,'Reviewer notes'		,1   ) !== false )
																	{
																		// check if radion buttons were changed
																		if ( (e = $('z_p_pic_type')) && (e = $('n_p_pic_type_'+e.value)) && ! e.checked )
																			c.b_changed = true;
																		if ( (e = $('z_p_img_treatment')) && (e = $('n_p_img_treatment_'+e.value)) && ! e.checked )
																			d.b_changed = true;

																		var b_text = c.b_changed,
																			b_img  = d.b_changed;

																		if ( (e = $('step')) )
																			e.value = b_save ? 'save' : 'preview';

																		if ( b_save )
																		{
																			b_save = b_text || b_img;

																			if ( ! b_save && (e = $('preview')) )
																				b_save = e.value == '1';

																			if ( ! b_save && (e = $('act')) )
																				b_save = e.value == 'rep' || e.value == 'new';

																			if ( ! b_save )
																				b_save = mod;

																			if ( b_save )
																			{
																				if ( b_as_new )
																				{
																					if ( (e = $('act')) ) e.value = 'asnew';
																					if ( (e = $('pic')) ) e.value = '0';
																				}
																				f.submit();
																			}
																			else
																			{
																				alert('No changes detected.  Nothing to save.');
																			}
																		}
																		else
																		{
																			if ( b_img )
																				f.submit();
																			else
																				alert('No image changes detected.  Preview already reflects chosen transforms.');
																		}
																	}

			return false;
			// must return false even if we navigate from a link otherwise the link will overwrite the submit call or the location.href setting
		}
	};

/* --------------------------------------------------------------------- */

