/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var PicMngt =
{
	inputPicName	: '',
	inputPicDispo	: '',
	inputPicReq		: '',
	inputPicMod		: '',
	inputPicId		: 0,
	inputPicEditId	: 0,

	setup : function() // initPicMngt
	{
		var e;
		if ( (e = $('myform')) )
		{
			Menus.setup();
			Context.attach('b_add', false, 'menu-pic');
			Seed.init();
		}
	},

	onPopup : function(el) // onMenuPopup
	{
		if ( ! this.id ) return;

		var i = this.menu.items,
			t = this.menu.target,
			d = true, e, a;

		switch ( this.id )
		{
		case 'help_pic':
			a = (Filmaf.inputLine = t.id).split('_');
			PicMngt.inputPicName   = a[1]; // prop (000164) or curr (020630-d0) picture. Location will be current: /p[0-1]/020630-d0.((gif)|(jpg)); proposed: /uploads/000164-prev.((gif)|(jpg))
			PicMngt.inputPicId     = a[2]; // pic-id
			PicMngt.inputPicEditId = a[3]; // pic-edit-id
			PicMngt.inputPicDispo  = a[4]; // disposition code [-APRXW] ('-' if pic-edit-id == 0)
			PicMngt.inputPicReq    = a[5]; // request-cd
			PicMngt.inputPicMod    = a[6]; // 'M' for moderator, 'P' for proposer
			if ( (e = $('def_pic'    )) ) d = e.value == PicMngt.inputPicId;	// true if default picture
			e = PicMngt.inputPicEditId != 0;								// true if edit/proposed, false if current
			i.hm_pic_lg.disable(PicMngt.inputPicReq == 'D'		);		// Show larger picture
			i.hm_pic_ed.disable(PicMngt.inputPicReq == 'D'		);		// Edit
			i.hm_pic_de.disable(						 e || d	);		// Delete
			i.hm_pic_be.disable(						 e		);		// Replace with better quality pic
			i.hm_pic_wi.disable(PicMngt.inputPicMod == 'M' || !e	);		// Withdraw request
			i.hm_pic_df.disable(						 e || d	);		// Make default picture
			if ( (e = $('pic'        )) ) e.value = PicMngt.inputPicId;
			if ( (e = $('pic_edit'   )) ) e.value = PicMngt.inputPicEditId;
			if ( (e = $('obj_type_r' )) ) e.value = Url.getVal('obj_type');
			if ( (e = $('obj_r'      )) ) e.value = Url.getVal('obj');
			if ( (e = $('obj_edit_r' )) ) e.value = Url.getVal('obj_edit');
			if ( (e = $('seed_r'     )) ) e.value = Seed.get();
			if ( (e = $('replace_pic')) ) e.value = PicMngt.inputPicName;
			break;

		case 'help_new':
			Filmaf.inputLine = 'new';
			if ( (e = $('obj_type_n' )) ) e.value = Url.getVal('obj_type');
			if ( (e = $('obj_n'      )) ) e.value = Url.getVal('obj');
			if ( (e = $('obj_edit_n' )) ) e.value = Url.getVal('obj_edit');
			if ( (e = $('seed_n'     )) ) e.value = Seed.get();
			break;

		case 'explain_pop':
			Explain.show(t.id,true);
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
			case 'hm_pic_':
				switch ( id.substr(7) )
				{
				case 'lg': Win.showPic(PicMngt.inputPicName); break;
				case 'ed': PicMngt._picEdit(PicMngt._getpicEditUrl(    0,PicMngt.inputPicId,PicMngt.inputPicEditId), '', 0); break;
				case 'de': PicMngt._picEdit(PicMngt._getpicEditUrl('del',PicMngt.inputPicId,                   0), 'Are you sure this picture is incorrectly assigned to this title?\n\nOK=Yes - Cancel=No', 0); break;
				case 'wi': PicMngt._picEdit(PicMngt._getpicEditUrl('wdr',               0,PicMngt.inputPicEditId), 'Are you sure you want to withdraw your request?\nThere is no going back.\n\nOK=Yes - Cancel=No', 0); break;
				case 'df': PicMngt._picEdit(PicMngt._getpicEditUrl('def',PicMngt.inputPicId,PicMngt.inputPicEditId), 'Are you sure you want to make this the default picture for this listing?\n\nOK=Yes - Cancel=No', 0); break;
				}
				break;
			}
		}
	},

	_getpicEditUrl : function(s_act, n_pic, n_pic_edit) // getpicEditUrl
	{
		var n_obj_edit = Url.getDec('obj_edit'),
			n_obj	   = Url.getDec('obj'     ),
			n_pdvd	   = Url.getDec('pdvd'    ),
			s_obj_type = Url.getVal('obj_type'),
			s_parm;

		if ( typeof(n_pic     ) == 'string' ) n_pic      = Dec.parse(n_pic);
		if ( typeof(n_pic_edit) == 'string' ) n_pic_edit = Dec.parse(n_pic_edit);
		
		s_parm = (s_act      ? '&act='      + s_act      :'') +
				 (n_pic      ? '&pic='      + n_pic      :'') +
				 (n_pic_edit ? '&pic_edit=' + n_pic_edit :'') +
				 (s_obj_type ? '&obj_type=' + s_obj_type :'') +
				 (n_obj      ? '&obj='      + n_obj      :'') +
				 (n_obj_edit ? '&obj_edit=' + n_obj_edit :'') +
				 (n_pdvd     ? '&pdvd='     + n_pdvd     :'');

		return '/utils/x-pic-edit.html?' + s_parm.substr(1);
	},

	_picEdit : function(s_url, s_ask, submit) // picEdit
	{
		var y = true;
		if ( s_ask ) y = confirm(s_ask);
		if ( y )
		{
			if ( submit )
			{
				submit.action = s_url;
				submit.submit();
			}
			else
			{
				Win.openStd(s_url, 'target_pic');
			}
		}
	},

	undo : function() // f_undo_all
	{
		var e, f;

		if ( (f = $('o_p_img_treatment')) )
		if ( (e = $('n_p_img_treatment_'+f.value)) )
			e.checked = true;

		if ( (e = $('n_p_rot_degrees'  )) && (f = $('o_p_rot_degrees'  )) ) e.value = f.value;
		if ( (e = $('n_p_rot_degrees_x')) && (f = $('o_p_rot_degrees_x')) ) e.value = f.value;
		if ( (e = $('n_p_crop_fuzz'    )) && (f = $('o_p_crop_fuzz'    )) ) e.value = f.value;
		if ( (e = $('n_p_crop_x1'      )) && (f = $('o_p_crop_x1'      )) ) e.value = f.value;
		if ( (e = $('n_p_crop_x2'      )) && (f = $('o_p_crop_x2'      )) ) e.value = f.value;
		if ( (e = $('n_p_crop_y1'      )) && (f = $('o_p_crop_y1'      )) ) e.value = f.value;
		if ( (e = $('n_p_crop_y2'      )) && (f = $('o_p_crop_y2'      )) ) e.value = f.value;
		if ( (e = $('n_p_black_pt'     )) && (f = $('o_p_black_pt'     )) ) e.value = f.value;
		if ( (e = $('n_p_white_pt'     )) && (f = $('o_p_white_pt'     )) ) e.value = f.value;
		if ( (e = $('n_p_gamma'        )) && (f = $('o_p_gamma'        )) ) e.value = f.value;
	}
};

/* --------------------------------------------------------------------- */

