/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulMicroBlog(s) // echoPostMenu, echoDvdMenu
{
    var x = Filmaf.userCollection == Filmaf.viewCollection
            ? "<input type='button' value='Blog' onclick='DvdList.blog(3)' style='width:80px;margin:0 10px 0 190px'>"
			: "<input type='button' id='b_blog_and_home' value='Blog and go to homepage' onclick='DvdList.blog(2)' style='width:180px;margin-right:10px'>"+
			  "<input type='button' value='Blog' onclick='DvdList.blog(1)' style='width:80px;margin-right:10px'>";

    s.s +=
	"<li id='context-dvd' class='context-class-a-dvd_pic'>"+
	  "<ul id='context_dvd' onclick='onMenuClick(this)'>"+
	    "<li id='cm_dvd_large'>Show Large Pic</li>"+
	    "<li id='cm_dvd_lst'>Go to FilmAf Listing</li>"+
	    "<li id='cm_dvd_blog'>Blog it"+
	      "<ul>"+
			"<li>"+
			  ulBlogForm('wg_tdr', x)+
			"</li>"+
	      "</ul>"+
	    "</li>"+
		Facebook.ul()+
	  "</ul>"+
	"</li>"+

	"<li id='context-blog-edit' class='context-class-img-ctx1'>"+
	  "<ul id='context_blog_edit' onclick='onMenuClick(this)'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0'>"+
			"<form id='mform_blog_edit' name='mform_blog_edit' action='javascript:void(0)'>"+
			  "<div class='wg_tdr' style='margin:10px 0 4px 0;text-align:left'>"+
				"<div style='margin:0 0 6px 0'><div id='rem2' style='float:right'>500</div><span id='tit_blog_edit'>Editing:</span></div>"+
				"<textarea id='n_blog_edit' style='width:98%;height:40px' maxlength='500' wrap='soft' onkeyup='Edit.countDown(\"rem2\",500 - this.value.length)'></textarea>"+
			  "</div>"+
			  "<table width='100%'>"+
				"<tr>"+
				  "<td class='wg_tdm'>Attach... paste the YouTube URL/id <img id='ex_blog_tub_0' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td>&nbsp;</td>"+
				  "<td class='wg_tdm'>DVD id <img id='ex_blog_dvd_0' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				"</tr>"+
				"<tr>"+
				  "<td style='text-align:left'><input id='n_edit_attach_tub' size='40' maxlength='200' type='text' value='' /></td>"+
				  "<td style='padding:0 2px 0 2px'>and/or</td>"+
				  "<td style='text-align:left'><input id='n_edit_attach_dvd' size='8' maxlength='8' type='text' value='' /></td>"+
				"</tr>"+
			  "</table>"+
			  "<div style='text-align:right;margin:4px 0 4px 0'>"+
				"<input type='button' value='Save' onclick='Microblog.blogEdit(0,1)' style='width:80px;margin:0 10px 0 190px'>" +
				"<input type='button' value='Cancel' onclick='Microblog.blogEdit(0,0)' style='width:72px'>"+
			  "</div>"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='context-blog-reply' class='context-class-img-ctx2'>"+
	  "<ul id='context_blog_reply' onclick='onMenuClick(this)'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0'>"+
			"<form id='mform_blog_reply' name='mform_blog_reply' action='javascript:void(0)'>"+
			  "<div class='wg_tdr' style='margin:10px 0 4px 0;text-align:left'>"+
				"<div style='margin:0 0 6px 0'><div id='rem3' style='float:right'>500</div><span id='tit_blog_reply'>Replying:</span></div>"+
				"<textarea id='n_blog_reply' style='width:98%;height:40px' maxlength='500' wrap='soft' onkeyup='Edit.countDown(\"rem3\",500 - this.value.length)'></textarea>"+
			  "</div>"+
			  "<table width='100%'>"+
				"<tr>"+
				  "<td class='wg_tdm'>Attach... paste the YouTube URL/id <img id='ex_blog_tub_1' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td>&nbsp;</td>"+
				  "<td class='wg_tdm'>DVD id <img id='ex_blog_dvd_1' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				"</tr>"+
				"<tr>"+
				  "<td style='text-align:left'><input id='n_reply_attach_tub' size='40' maxlength='200' type='text' value='' /></td>"+
				  "<td style='padding:0 2px 0 2px'>and/or</td>"+
				  "<td style='text-align:left'><input id='n_reply_attach_dvd' size='8' maxlength='8' type='text' value='' /></td>"+
				"</tr>"+
			  "</table>"+
			  "<div style='text-align:right;margin:4px 0 4px 0'>"+
				"<input type='button' value='Save' onclick='Microblog.blogEdit(1,1)' style='width:80px;margin:0 10px 0 190px'>" +
				"<input type='button' value='Cancel' onclick='Microblog.blogEdit(1,0)' style='width:72px'>"+
			  "</div>"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>"+
 
	"<li id='menu-home-blog'>"+
	  "<ul id='home_blog'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0'>"+
			"<form id='mform_blog' name='mform_blog' action='javascript:void(0)'>"+
			  "<table width='100%'>"+
				"<tr>"+
				  "<td class='wg_tdm'>Attach... paste the YouTube URL/id <img id='ex_blog_tub' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td>&nbsp;</td>"+
				  "<td class='wg_tdm'>DVD id <img id='ex_blog_dvd' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				"</tr>"+
				"<tr>"+
				  "<td style='text-align:left'><input id='n_blog_attach_tub' size='40' maxlength='200' type='text' value='' /></td>"+
				  "<td style='padding:0 2px 0 2px'>and/or</td>"+
				  "<td style='text-align:left'><input id='n_blog_attach_dvd' size='8' maxlength='8' type='text' value='' /></td>"+
				"</tr>"+
				"<tr>"+
				  "<td style='text-align:left' colspan='3'>(clicking on a DVD and selecting blog from that screen makes it easier)</td>"+
				"</tr>"+
			  "</table>"+
			  "<div style='text-align:right;margin:4px 0 4px 0'>"+
				"<input type='button' value='Post' onclick='Microblog.blogAttached(\"blog\",0)' style='width:72px;margin-right:10px'>"+
				"<input type='button' value='Cancel' onclick='Microblog.blogAttached(0,0)' style='width:72px'>"+
			  "</div>"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-home-wall'>"+
	  "<ul id='home_wall'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0'>"+
			"<form id='mform_wall' name='mform_wall' action='javascript:void(0)'>"+
			  "<table width='100%'>"+
				"<tr>"+
				  "<td class='wg_tdm'>Attach... paste the YouTube URL/id <img id='ex_wall_tub' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td>&nbsp;</td>"+
				  "<td class='wg_tdm'>DVD id <img id='ex_wall_dvd' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				"</tr>"+
				"<tr>"+
				  "<td style='text-align:left'><input id='n_wall_attach_tub' size='40' maxlength='200' type='text' value='' /></td>"+
				  "<td style='padding:0 2px 0 2px'>and/or</td>"+
				  "<td style='text-align:left'><input id='n_wall_attach_dvd' size='8' maxlength='8' type='text' value='' /></td>"+
				"</tr>"+
			  "</table>"+
			  "<div style='text-align:right;margin:4px 0 4px 0'>"+
				"<input type='button' value='Post' onclick='Microblog.blogAttached(\"wall\",0)' style='width:72px;margin-right:10px'>"+
				"<input type='button' value='Cancel' onclick='Microblog.blogAttached(0,0)' style='width:72px'>"+
			  "</div>"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

