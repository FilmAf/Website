/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulBirth(s)
{
    s.s +=
	"<li id='menu-birth'>"+
	  "<ul id='help_birth'>"+
	    "<li>"+
	      "<div style='margin-top:2px;width:300px'>"+
			"<div>Please select a region first...</div>"+
			"<div class='pop_sep_short' style='margin:8px 0px 8px 0px'>&nbsp;</div>"+
			"<div><img src='http://dv1.us/d1/continets.gif' width='300' height='146' border='0' usemap='#birth_map' style='img:focus:outline:none' /></div>"+
			"<map name='birth_map'>"+
			  "<area shape='poly' href='javascript:void(Birth.set(1))' alt='North America' coords='86,66,88,61,95,61,101,65,124,58,124,23,153,0,68,0,40,13,30,20,21,28,15,34,10,41,6,48,3,56,1,61,1,66' />"+
			  "<area shape='poly' href='javascript:void(Birth.set(2))' alt='South America' coords='1,82,1,66,86,66,88,61,95,61,101,65,124,58,147,126,30,126,17,114,8,102,3,91' />"+
			  "<area shape='poly' href='javascript:void(Birth.set(3))' alt='Europe' coords='153,0,124,23,124,41,150,41,154,38,159,39,164,43,172,43,170,38,174,34,181,34,189,36,188,31,186,31,185,30,185,28,186,27,191,27,192,27,192,21,190,19,190,16,193,13,190,11,193,6,193,0' />"+
			  "<area shape='poly' href='javascript:void(Birth.set(4))' alt='Africa' coords='176,44,178,48,180,53,184,59,187,62,202,62,213,78,213,126,147,126,124,58,124,41,150,41,154,38,159,39,164,43,172,43' />"+
			  "<area shape='poly' href='javascript:void(Birth.set(5))' alt='Asia' coords='232,0,256,11,270,20,278,27,287,36,293,46,298,57,299,63,299,73,258,73,258,75,260,78,255,83,213,101,213,78,202,62,187,62,184,59,180,53,178,48,176,44,172,43,170,38,174,34,181,34,189,36,188,31,186,31,185,30,185,28,186,27,192,27,192,21,190,19,190,16,193,13,190,11,193,6,193,0' />"+
			  "<area shape='poly' href='javascript:void(Birth.set(6))' alt='Oceania' coords='213,126,213,101,255,83,260,78,258,75,258,73,299,73,299,84,296,93,291,103,285,112,275,122,270,126' />"+
			  "<area shape='poly' href='javascript:void(Birth.set(7))' alt='Antartica' coords='68,146,52,139,42,134,30,126,270,126,258,134,246,140,232,146' />"+
			"</map>"+
			"<div class='pop_sep_long' />&nbsp;</div>"+
			"<div>...then click on a country to make your choice effective.</div>"+
			"<div style='margin-top:4px'>"+
			  "<iframe width='300px' heigh='50px' scrolling='yes' style='margin-bottom:4px' id='frame_birth'></iframe>"+
			"</div>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

