/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulPicComments(s) // f_echo_ul_modpic
{
    s.s +=
	"<li id='menu-modpic' onclick='onMenuClick(this)'>"+
	  "<ul id='help_mpi'>"+
	    "<li id='hm_mpi_11'><a>"+
	      "<div style='color:blue'>THANKS:</div>"+
	      "<div style='width:300px;font-size:9px' id='mpi11'>Thanks!</div>"+
	    "</a></li>"+
	    "<li></li>"+
	    "<li id='hm_mpi_21'><a>"+
	      "<div style='color:orange'>PLS UP BETTER PIC SOON:</div>"+
	      "<div style='width:300px;font-size:9px' id='mpi21'>Okay for now. Please upload a better version of the artwork as soon as you can. Thanks.</div>"+
	    "</a></li>"+
	    "<li></li>"+
	    "<li id='hm_mpi_31'><a>"+
	      "<div style='color:red'>PIC WATERMARKED:</div>"+
	      "<div style='width:300px;font-size:9px' id='mpi31'>Sorry, the uploaded picture is watermarked.</div>"+
	    "</a></li>"+
	    "<li></li>"+
	    "<li id='hm_mpi_32'><a>"+
	      "<div style='color:red'>PIC TOO POOR:</div>"+
	      "<div style='width:300px;font-size:9px' id='mpi32'>Sorry, the uploaded picture is too poor.</div>"+
	    "</a></li>"+
	    "<li></li>"+
	    "<li id='hm_mpi_33'><a>"+
	      "<div style='color:red'>PIC TOO SMALL:</div>"+
	      "<div style='width:300px;font-size:9px' id='mpi33'>Sorry, the uploaded picture is too small.</div>"+
	    "</a></li>"+
	    "<li></li>"+
	    "<li id='hm_mpi_34'><a>"+
	      "<div style='color:red'>ALREADY IN - RECENT:</div>"+
	      "<div style='width:300px;font-size:9px' id='mpi34'>Sorry, we just approved another submission for this picture.</div>"+
	    "</a></li>"+
	    "<li></li>"+
	    "<li id='hm_mpi_35'><a>"+
	      "<div style='color:red'>DIFFERENT EDITION:</div>"+
	      "<div style='width:300px;font-size:9px' id='mpi35'>The picture you uploaded seems to belong to a different edition. If you can not find that edition in the database please submit it as a new title. Thanks!</div>"+
	    "</a></li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

