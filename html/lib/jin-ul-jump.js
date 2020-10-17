/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulJump(s) // f_echo_ul_jump
{
    s.s +=
	"<li id='menu-jump-page'>"+
	  "<ul id='jump_page'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0'>"+
			"<form id='mform_jump' name='mform_jump' action=''>"+
			  "<div style='white-space:nowrap'>"+
				"<div style='margin:4px 0 4px 0'>Jump to page:</div>"+
				"<table style='white-space:nowrap'>"+
				  "<tr>"+
					"<td><input type='text' id='n_jump' onkeypress='return Jump.setOnEnter(event)' size='4'></td>"+
					"<td style='padding:0 12px 0 1px'>"+
					  "<img src='http://dv1.us/d1/00/pn00.gif' id='is_2_jump' height='17' width='10' sp_min='1' sp_max='100' sp_inc='1' alt='Spin' />"+
					  "<img src='http://dv1.us/d1/00/pn00.gif' id='is_1_jump' height='17' width='10' sp_min='1' sp_max='100' sp_inc='0' alt='Spin' />"+
					"</td>"+
					"<td style='text-align:right'><input type='button' value='go' onclick='Jump.set(-1)'></td>"+
				  "</tr>"+
				"</table>"+
		      "</div>"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

