/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdApproveMenuAction =
{
	onClick : function(action) // onMenuClick2
	{
		if ( action.info && action.info.id )
		{
			var id = action.info.id,
				z  = this;

			switch ( id.substr(0,7) )
			{
			case 'hm_pic_':
				switch ( id.substr(7) )
				{
				case 'lg': Win.showPic(DvdApprove.inputPicName);				break;
				case 'up': Win.showPic(DvdApprove.inputPicName + '&bord=1');	break;
				default:   DvdApproveMenuAction._selPic(id);			break;
				}
				break;
			case 'hm_mdv_':
				DvdApproveMenuAction._prependReviwerNotes(id.substr(7));
				break;
			default:
				z.filmaf = ObjEdit.onClick;
				z.filmaf(this,action);
				break;
			}
		}
	},

	_selPic : function(id)
	{
		if ( Filmaf.objId )
		{
			var e = $('edit_id'),
				s = '&obj_type=dvd&obj=' + Filmaf.objId + '&obj_edit='+ (e ? e.value : Url.getVal('edit')); // obj=dvd&id=103629&edit=0

			switch ( id.substr(7) )
			{
			case 'ed': DvdApproveMenuAction.picEdit('?pic='   +DvdApprove.inputPicId+'&mod=1&pic_edit='+DvdApprove.inputPicEditId+s, ''); break;
			case 'rj': DvdApproveMenuAction.picEdit('?act=rej'                      +'&mod=1&pic_edit='+DvdApprove.inputPicEditId+s, 'Are you sure you want to reject this request?\nThere is no going back.\n\nOK=Yes - Cancel=No'); break;
			}
		}
		else
		{
			alert('Please approve the submission before editing or approving the corresponding pictures.');
		}
	},

	_prependReviwerNotes : function(e) // prependReviwerNotes
	{
		var f = $('n_zareviewer_notes');

		if ( f && (e = $('mdv'+e)) )
			f.value = e.innerHTML;
	},

	picEdit : function(s_parm, s_ask) // picEdit
	{
		var y = true;
		if ( s_ask ) y = confirm(s_ask);
		if ( y ) Win.openStd('/utils/x-pic-edit.html' + s_parm, 'target_pic');
	}
};

/* --------------------------------------------------------------------- */

