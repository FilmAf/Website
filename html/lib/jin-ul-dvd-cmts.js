/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulDvdComments(s) // f_echo_ul_moddvd
{
    s.s +=
	"<li id='menu-moddvd' onclick='onMenuClick(this)'>"+
	  "<ul id='help_mdv'>"+
	    "<li>Good"+
	      "<ul>"+
		"<li id='hm_mdv_11'><a>"+
		  "<div style='color:blue'>THANKS:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv11'>Thanks!</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_12'><a>"+
		  "<div style='color:blue'>FIXED:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv12'>Fixed.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_13'><a>"+
		  "<div style='color:blue'>MADE IT A PLACEHOLDER:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv13'>Since we do not have an official announcement we will make this submission into a placeholder for now.</div>"+
		"</a></li>"+
	      "</ul>"+
	    "</li>"+
	    "<li>Okay"+
	      "<ul>"+
		"<li id='hm_mdv_21'><a>"+
		  "<div style='color:orange'>PLS UP BETTER PIC SOON:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv21'>Okay for now. Please upload a better version of the artwork as soon as you can. Thanks.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_22'><a>"+
		  "<div style='color:orange'>PLS UP PIC SOON:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv22'>Okay for now. Please upload artwork as soon as you can. Thanks.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_23'><a>"+
		  "<div style='color:orange'>PLS MORE IN THE FUTURE:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv23'>Okay for now. In the future please ensure that your submission has directors, publishers, imdb ids, UPC, release dates, good quality artwork, etc. or it is likely not to be approved.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_24'><a>"+
		  "<div style='color:orange'>DOCS NOT IN IMDB:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv24'>Currently documentaries must be in imdb in order to be listed.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_25'><a>"+
		  "<div style='color:orange'>NON-US MSRP:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv25'>We currently do not list MSRP for non-US releases.</div>"+
		"</a></li>"+
	      "</ul>"+
	    "</li>"+
	    "<li>Bad"+
	      "<ul>"+
		"<li id='hm_mdv_31'><a>"+
		  "<div style='color:red'>ALREADY IN - RECENT:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv31'>Sorry, we just approved another submission for this title.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_32'><a>"+
		  "<div style='color:red'>ALREADY IN - OLD:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv32'>Sorry, title already in database.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_33'><a>"+
		  "<div style='color:red'>PIC WATERMARKED:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv33'>Sorry, the uploaded picture is watermarked.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_34'><a>"+
		  "<div style='color:red'>PIC TOO POOR:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv34'>Sorry, the uploaded picture is too poor.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_35'><a>"+
		  "<div style='color:red'>PIC TOO SMALL:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv35'>Sorry, the uploaded picture is too small.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_36'><a>"+
		  "<div style='color:red'>STOP RESUBMITS:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv36'>Please stop re-submitting rejected changes or your ability to update listings will be taken away. Instead try to understand the reasoning behind the rejection. You may try to make your point at http://dvdaf.net/. Thanks.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_37'><a>"+
		  "<div style='color:red'>NO PORN:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv37'>Sorry, FilmAf does not list porn unless the title has special social or historical significance.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_38'><a>"+
		  "<div style='color:red'>VERIFY EXISTENCE:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv38'>Please provide a link so that we can verify the existence of this title.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_39'><a>"+
		  "<div style='color:red'>INCOMPLETE:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv39'>Sorry, your submission is incomplete. Please ensure it has directors, publishers, imdb ids, UPC, release dates, good quality artwork (if you have not uploaded it already), etc.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_40'><a>"+
		  "<div style='color:red'>ALL CAPS:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv40'>Please do not user ALL CAPS because we have to retype everything. Sorry.</div>"+
		"</a></li>"+
		"<li></li>"+
		"<li id='hm_mdv_41'><a>"+
		  "<div style='color:red'>PLS USE PLACEHOLDER:</div>"+
		  "<div style='width:300px;font-size:9px' id='mdv41'>We will wait for an official announcement. Meanwhile please use the placeholder listing with the &quot;To be Determined&quot; publisher.</div>"+
		"</a></li>"+
	      "</ul>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

