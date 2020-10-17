/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulDvdTitle(s) // f_echo_ul_dvd_title
{
    s.s +=
	"<li id='menu-dvd-title'>"+
	  "<ul id='help_dvd_search'>"+
	    "<li>"+
	      "<div class='pop_div'>"+
		"Please enter a search string with at least 3 characters. You may use the search below to locate a specific title."+
		"<div class='pop_sep_long' />&nbsp;</div>"+
		"<form id='mform_dvd' name='mform_dvd' style='margin-top:1px; margin-bottom:4px' onsubmit='return false' action=''>"+
		  "Find a title:"+
		  "<input type='text' id='dvd_search' onkeyup='return MiniSearch.display(this.form,\"dvd\")' size='20'>"+
		  "<br />"+
		  "<div style='margin-top:4px'>"+
		    "<iframe width='200px' heigh='50px' scrolling='yes' style='margin-bottom:4px' id='frame_dvd'></iframe>"+
		  "</div>"+
		"</form>"+
		"<div class='pop_sep_long' />&nbsp;</div>"+
		"Once you have located your title, clicking on it will make your choice effective."+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

