/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Kbd =
{
	isEnter : function(ev) // isKeyEnter
	{
		return (window.event ? window.event.keyCode : ( ev ? ev.which : null)) == 13;
	},

	isEditKey : function(c) // isEditKey
	{
		switch (c)
		{
		case 8:   // backspace
		case 9:   // tab
		case 33:  // page-up
		case 34:  // page-down
		case 35:  // end
		case 36:  // home
		case 37:  // left-arrow
		case 38:  // up-arrow
		case 39:  // right-arrow
		case 40:  // down-arrow
		case 45:  // insert
		case 46:  // deleteKey
		case 127: // deleteKey
			return true;
		}
		return false;
	}
/*
	submitOnEnter : function(ev,f) // submitOnEnter
	{
		if ( Kbd.isEnter(ev) && f.onsubmit() )
			f.submit();
		return true;
	},
*/
};

/* --------------------------------------------------------------------- */

