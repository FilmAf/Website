/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulDvdPic(s) // f_echo_ul_picmgt
{
    s.s +=
	"<li class='context-class-input-mp'>"+
	  "<ul id='help_pic' onclick='onMenuClick(this)'>"+
	    "<li id='hm_pic_lg'>Show larger picture</li>"+
	    "<li id='hm_pic_up'>Show uploaded picture</li>"+
	    "<li></li>"+
	    "<li id='hm_pic_ed'>Edit and Approve picture</li>"+
	    "<li id='hm_pic_rj'>Reject picture submission</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

