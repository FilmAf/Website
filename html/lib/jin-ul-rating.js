/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulFilmRating(s)
{
    s.s +=
	"<li id='menu-rating-no' onclick='onMenuClick(this)'>"+
	  "<ul id='help_fro'>"+
	    "<li id='hm_fro_00001'>Not Rated</li>"+
	    "<li></li>"+
		"<li>U.S."+
		  "<ul>"+
			"<li id='hm_fro_10010'>G</li>"+
			"<li id='hm_fro_10020'>PG</li>"+
			"<li id='hm_fro_10030'>PG-13</li>"+
			"<li id='hm_fro_10040'>R</li>"+
			"<li id='hm_fro_10050'>NC-17</li>"+
			"<li></li>"+
			"<li id='hm_fro_10060'>M</li>"+
			"<li id='hm_fro_10070'>GP</li>"+
			"<li id='hm_fro_10080'>SMA</li>"+
			"<li id='hm_fro_10090'>X</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Australia"+
		  "<ul>"+
			"<li id='hm_fro_10910'>E</li>"+
			"<li id='hm_fro_10920'>G</li>"+
			"<li id='hm_fro_10930'>PG</li>"+
			"<li id='hm_fro_10940'>M</li>"+
			"<li id='hm_fro_10950'>MA15+</li>"+
			"<li id='hm_fro_10960'>R18+</li>"+
			"<li id='hm_fro_10970'>X18+</li>"+
			"<li id='hm_fro_10980'>RC</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Canada"+
		  "<ul>"+
			"<li id='hm_fro_11510'>G</li>"+
			"<li id='hm_fro_11520'>PG</li>"+
			"<li id='hm_fro_11530'>14A</li>"+
			"<li id='hm_fro_11540'>18A</li>"+
			"<li id='hm_fro_11550'>R</li>"+
			"<li id='hm_fro_11560'>A</li>"+
		  "</ul>"+
		"</li>"+
		"<li>U.K."+
		  "<ul>"+
			"<li id='hm_fro_20710'>U</li>"+
			"<li id='hm_fro_20720'>PG</li>"+
			"<li id='hm_fro_20730'>12A</li>"+
			"<li id='hm_fro_20740'>12</li>"+
			"<li id='hm_fro_20750'>15</li>"+
			"<li id='hm_fro_20760'>18</li>"+
			"<li id='hm_fro_20770'>R18</li>"+
		  "</ul>"+
		"</li>"+
	    "<li></li>"+
	    "<li id='hm_fro_00000'>Unknown</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

