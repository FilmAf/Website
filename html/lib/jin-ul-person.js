/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulPerson(s)
{
    s.s +=
	"<li id='menu-person'>"+
	  "<ul id='help_person'>"+
	    "<li>"+
	      "<div class='pop_div'>"+
			"Please enter a search string with at least 2 characters to locate a person."+
			"<div class='pop_sep_long' />&nbsp;</div>"+
			"<form id='mform_person' name='mform_person' style='margin-top:1px; margin-bottom:4px' onsubmit='return false' action=''>"+
			  "Find a person:"+
			  "<input type='text' id='person_search' onkeyup='return MiniSearch.display(this.form,\"person\")' size='20'>"+
			  "<br />"+
			  "<div style='margin-top:4px'>"+
				"<iframe width='200px' heigh='50px' scrolling='yes' style='margin-bottom:4px' id='frame_person'></iframe>"+
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

