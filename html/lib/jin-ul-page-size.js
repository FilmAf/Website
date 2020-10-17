/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulPageSize(s) // f_echo_ul_pagesize
{
    s.s +=
	"<li id='menu-page-size'>"+
	  "<ul id='page_size'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0'>"+
			"<form id='mform_pagesize' name='mform_pagesize' action=''>"+
			  "<div style='white-space:nowrap'>"+
				"<div style='margin:4px 0 4px 0'>Preferred page size:</div>"+
				"<table style='white-space:nowrap'>"+
				  "<tr>"+
					"<td><input type='text' id='n_pagesize' onkeypress='return PageSize.setOnEnter(event)' size='4'></td>"+
					"<td style='padding:0 12px 0 1px'>"+
					  "<img src='http://dv1.us/d1/00/pn00.gif' id='is_3_pagesize' height='17' width='10' sp_min='1' sp_max='200' sp_inc='b' alt='Spin' />"+
					  "<img src='http://dv1.us/d1/00/pn00.gif' id='is_2_pagesize' height='17' width='10' sp_min='1' sp_max='200' sp_inc='a' alt='Spin' />"+
					  "<img src='http://dv1.us/d1/00/pn00.gif' id='is_1_pagesize' height='17' width='10' sp_min='1' sp_max='200' sp_inc='0' alt='Spin' />"+
					"</td>"+
					"<td style='text-align:right'><input type='button' value='set' onclick='PageSize.set(-1)'></td>"+
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

