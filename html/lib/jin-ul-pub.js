/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulPub(s) // f_echo_ul_pub
{
    s.s +=
	"<li id='menu-publisher'>"+
	  "<ul id='help_pub'>"+
	    "<li>"+
	      "<div class='pop_div'>"+
		"Please enter a search string with at least 5 characters. You may use the search below to locate a specific publisher."+
		"<div class='pop_sep_long' />&nbsp;</div>"+
		"<form id='mform_pub' name='mform_pub' style='margin-top:1px; margin-bottom:4px' onsubmit='return false' action=''>"+
		  "Find a publisher:"+
		  "<input type='text' id='pub_search' onkeyup='return MiniSearch.display(this.form,\"pub\")' size='20'>"+
		  "<br />"+
		  "<div style='margin-top:4px'>"+
		    "<iframe width='200px' heigh='50px' scrolling='yes' style='margin-bottom:4px' id='frame_pub'></iframe>"+
		  "</div>"+
		"</form>"+
		"<div class='pop_sep_long' />&nbsp;</div>"+
		"Once you have located your publisher, clicking on his/her name will make your choice effective."+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

