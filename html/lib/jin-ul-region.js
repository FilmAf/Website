/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulRegion(s) // f_echo_ul_region
{
    s.s +=
	"<li id='menu-region-no' onclick='onMenuClick(this)'>"+
	  "<ul id='help_rno'>"+
	    "<li>"+
	      "<div style='margin-top:2px;width:450px'>"+
			"<div id='help_rno_txt'>Click on the map to select the appropriate DVD Region Code.</div>"+
			"<div class='pop_sep_short' style='margin:8px 0px 8px 0px'>&nbsp;</div>"+
			"<div style='display:none'>"+
			  "<img src='http://dv1.us/d1/region-dvd.gif' />"+
			  "<img src='http://dv1.us/d1/region-bd.gif' />"+
			  "<img src='http://dv1.us/d1/region-sat.gif' />"+
			"</div>"+
			"<div id='help_rno_img'><img src='http://dv1.us/d1/region-sat.gif' width='450' height='218' border='0' style='img:focus:outline:none' /></div>"+
			"<map name='region_dvd_map'>"+
			  "<area shape='rect' href='javascript:void(Region.set(-1))' alt='Unknwown Region' coords='0,202,125,217' />"+
			  "<area shape='rect' href='javascript:void(Region.set(0))' alt='Region 0: Plays on any DVD Player' coords='0,187,125,201' />"+
			  "<area shape='poly' href='javascript:void(Region.set(1))' alt='Region 1: US and Canada' coords='101,0,183,0,168,10,185,82,147,82,143,87,140,86,142,79,130,70,128,72,128,76,107,74,104,72,104,70,103,69,100,69,98,66,95,66,92,66,90,65,86,65,45,87,3,87,10,68,20,53,35,37,54,22,75,11' />"+
			  "<area shape='poly' href='javascript:void(Region.set(2))' alt='Region 2: Europe, Middle East, Japan and South Africa' coords='424,131,421,138,429,145,432,139' />"+
			  "<area shape='poly' href='javascript:void(Region.set(2))' alt='Region 2: Europe, Middle East, Japan and South Africa' coords='381,49,388,46,392,56,400,75,376,75,379,60' />"+
			  "<area shape='poly' href='javascript:void(Region.set(2))' alt='Region 2: Europe, Middle East, Japan and South Africa' coords='156,101,156,107,159,106,160,104' />"+
			  "<area shape='poly' href='javascript:void(Region.set(2))' alt='Region 2: Europe, Middle East, Japan and South Africa' coords='244,148,246,148,246,149,248,149,248,148,249,148,249,143,250,143,250,146,251,146,251,145,253,144,256,144,260,139,263,139,265,146,265,148,257,156,247,156,244,150' />"+
			  "<area shape='poly' href='javascript:void(Region.set(2))' alt='Region 2: Europe, Middle East, Japan and South Africa' coords='183,0,256,0,254,17,256,26,254,28,256,33,251,38,251,44,256,43,259,45,258,48,264,52,275,50,289,59,291,57,297,59,297,66,300,71,301,94,289,94,288,91,280,94,271,78,267,78,267,79,255,79,255,64,245,63,237,57,226,58,216,61,180,61,168,10' />"+
			  "<area shape='poly' href='javascript:void(Region.set(3))' alt='Region 3: Southeast Asia' coords='337,80,339,80,339,75,340,73,342,71,344,72,344,77,347,79,351,79,354,77,356,79,355,83,358,86,368,82,374,66,371,59,374,56,379,60,376,75,400,75,400,123,387,123,354,135,333,126,333,85' />"+
			  "<area shape='poly' href='javascript:void(Region.set(4))' alt='Region 4: Australia, New Zealand and Latin America' coords='333,126,333,218,349,218,369,209,389,199,407,187,423,172,435,158,445,139,450,120,450,97,445,80,440,69,435,61,394,61,400,75,400,123,387,123,354,135' />"+
			  "<area shape='poly' href='javascript:void(Region.set(4))' alt='Region 4: Australia, New Zealand and Latin America' coords='3,87,45,87,86,65,90,65,92,66,98,66,100,69,103,69,104,70,104,72,107,74,128,76,128,72,130,70,142,79,140,86,143,87,147,82,185,82,187,82,193,82,193,217,126,217,126,172,27,172,17,159,9,147,3,131,0,118,0,97' />"+
			  "<area shape='poly' href='javascript:void(Region.set(5))' alt='Region 5: Africa, Eastern Europe and the rest of Asia' coords='193,61,216,61,226,58,237,57,245,63,255,64,255,79,267,79,267,78,271,78,280,94,288,91,289,94,301,94,300,71,297,66,297,59,291,57,289,59,275,50,264,52,258,48,259,45,256,43,251,44,251,38,256,33,254,28,256,26,254,17,256,0,348,0,371,9,390,19,403,28,418,40,435,61,394,61,388,46,381,49,379,60,374,56,371,59,369,55,373,51,373,49,375,47,375,45,372,43,372,45,370,45,369,43,365,43,359,37,354,37,355,38,355,42,353,42,353,43,358,45,358,46,354,48,351,48,351,52,344,53,336,52,331,48,328,48,325,45,322,43,320,43,320,46,318,46,316,48,316,50,317,50,317,52,311,55,311,57,315,61,319,61,319,67,321,68,325,68,331,71,336,71,338,69,341,69,342,70,342,71,340,73,339,75,339,80,337,80,333,85,333,158,234,158,193,158' />"+
			  "<area shape='poly' href='javascript:void(Region.set(6))' alt='Region 6: China and Hong Kong' coords='311,55,317,52,317,50,316,50,316,48,318,46,320,46,320,43,322,43,325,45,328,48,331,48,336,52,344,53,351,52,351,48,354,48,358,46,358,45,353,43,353,42,355,42,355,40,355,38,354,37,359,37,365,43,369,43,370,45,372,45,372,43,375,45,375,47,373,49,373,51,369,55,371,59,374,66,368,82,358,86,355,83,356,79,354,77,351,79,347,79,344,77,344,72,342,71,342,70,341,69,338,69,336,71,331,71,325,68,321,68,319,67,319,65,319,61,315,61,311,57' />"+
			"</map>"+
			"<map name='region_bd_map'>"+
			  "<area shape='rect' href='javascript:void(Region.set(-1))' alt='Unknwown Region' coords='0,202,125,217' />"+
			  "<area shape='rect' href='javascript:void(Region.set(0))' alt='Region Free: Plays on any Blu-ray DVD Player' coords='0,187,125,201' />"+
			  "<area shape='poly' href='javascript:void(Region.set(9))' alt='Region C: Eastern Europe and the rest of Asia' coords='348,0,377,12,399,25,418,40,431,54,443,73,448,89,450,108,400,108,402,59,392,54,388,47,383,48,376,50,369,54,369,59,374,66,368,82,358,86,355,83,356,79,354,77,351,79,347,79,344,77,344,72,342,71,340,73,339,76,339,80,337,80,333,85,333,126,302,113,300,71,297,65,297,59,291,57,289,59,282,54,275,50,264,52,258,48,259,45,256,43,251,44,251,38,256,33,254,28,256,26,254,17,256,0' />"+
			  "<area shape='poly' href='javascript:void(Region.set(8))' alt='Region B: Europe, Australia, New Zealand and Africa' coords='156,101,156,107,159,106,160,104' />"+
			  "<area shape='poly' href='javascript:void(Region.set(8))' alt='Region B: Europe, Australia, New Zealand and Africa' coords='183,0,256,0,254,17,256,26,254,28,256,33,251,38,251,44,256,43,259,45,258,48,264,52,275,50,289,59,291,57,297,59,297,65,300,71,302,113,354,135,387,123,400,123,400,108,450,108,448,127,442,145,432,162,416,179,402,190,386,201,365,211,347,218,217,218,168,10' />"+
			  "<area shape='poly' href='javascript:void(Region.set(7))' alt='Region A: Americas, Japan, Korea and Southeast Asia' coords='374,51,383,48,388,47,392,54,402,59,400,109,400,123,387,123,354,135,333,126,333,85,337,80,339,80,339,76,340,73,342,71,344,72,344,77,347,79,351,79,354,77,356,79,355,83,358,86,368,82,374,66,369,59,369,54' />"+
			  "<area shape='poly' href='javascript:void(Region.set(7))' alt='Region A: Americas, Japan, Korea and Southeast Asia' coords='168,10,183,0,101,0,70,13,44,30,23,50,9,71,2,91,0,111,4,138,15,158,27,172,126,172,126,218,217,218' />"+
			"</map>"+
			"<map name='region_sat_map'>"+
			  "<area shape='poly' href='javascript:void(Region.set(0))' alt='Region Free: Plays on any Player' coords='345,0,376,11,405,29,426,48,440,69,447,88,450,109,447,130,439,150,425,170,402,191,371,209,345,218,102,218,76,207,47,190,26,171,11,150,3,130,0,110,2,89,10,69,24,48,45,29,74,11,104,0' />"+
			"</map>"+
			"<div class='pop_sep_short' style='margin:8px 0px 8px 0px'>&nbsp;</div>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

