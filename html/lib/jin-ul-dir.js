/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulDir(s) // f_echo_ul_dir
{
    s.s +=
	"<li id='menu-director'>"+
	  "<ul id='help_dir'>"+
	    "<li>"+
	      "<div class='pop_div'>"+
			"Please enter a search string with at least 2 characters to locate a director."+
			"<div class='pop_sep_long' />&nbsp;</div>"+
			"<form id='mform_dir' name='mform_dir' style='margin-top:1px; margin-bottom:4px' onsubmit='return false' action=''>"+
			  "Find a director:"+
			  "<input type='text' id='dir_search' onkeyup='return MiniSearch.display(this.form,\"dir\")' size='20'>"+
			  "<br />"+
			  "<div style='margin-top:4px'>"+
				"<iframe width='200px' heigh='50px' scrolling='yes' style='margin-bottom:4px' id='frame_dir'></iframe>"+
			  "</div>"+
			"</form>"+
			"<div class='pop_sep_long' />&nbsp;</div>"+
			"Once you found who you are looking for clicking on his/her name will make your choice effective."+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

