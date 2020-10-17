/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdApproveMenuPrep =
{
	onPopup : function(el) // onMenuPopup
	{
		if ( ! this.id ) return;
		var i = this.menu.items,
			t = this.menu.target,
			z = this;

		switch ( this.id )
		{
		case 'help_pic':
			DvdApproveMenuPrep._contextPic(i,t);
			break;
		default:
			z.filmaf = ObjEdit.onPopup;
			z.filmaf(this,el);
			break;
		}
	},

	_contextPic : function(i,t)
	{
		// this only gets called from x-dvd-appr.html, not x-dvd-edit.html
		var a = (Filmaf.inputLine = t.id).split('_');
		DvdApprove.inputPicName   = a[1]; // prop (000164) or curr (020630-d0) picture. Location will be current: /p[0-1]/020630-d0.((gif)|(jpg)); proposed: /uploads/000164-prev.((gif)|(jpg))
		DvdApprove.inputPicId     = a[2]; // pic-id
		DvdApprove.inputPicEditId = a[3]; // pic-edit-id
		DvdApprove.inputPicDispo  = a[4]; // disposition code [-APRXW] ('-' if pic-edit-id == 0)
		DvdApprove.inputPicReq    = a[5]; // request-cd
		DvdApprove.inputPicMod    = a[6]; // 'M' for moderator, 'P' for proposer
		a = DvdApprove.inputPicEditId == 0 || DvdApprove.inputPicDispo != '-'; // disable if not edit or already processed
		i.hm_pic_ed.disable(a);
		i.hm_pic_rj.disable(a);
	}
};

/* --------------------------------------------------------------------- */

