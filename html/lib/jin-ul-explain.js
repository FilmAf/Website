/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulExplain(s)
{
    s.s +=
	"<li id='menu-explain-pop' class='context-class-a-explain'>"+
	  "<ul id='explain_pop'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0;width:300px'>"+
			"<form id='mform_explain' name='mform_explain' onsubmit='return false' action=''>"+
			  "<div id='explain_div' style='margin:4px 0 4px 0'>"+
			    "<div>"+
			      "<div style='margin:4px 0 4px 0;text-align:right'><input type='button' value='close' onclick='Context.close()'></div>"+
			    "</div>"+
			  "</div>"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

