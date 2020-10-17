/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulBlog(s)
{
    s.s +=
	"<li class='context-class-a-blog_pop'>"+
      "<ul id='context_fb_blog'>"+
		"<li>"+
		  ulBlogForm('','')+
		"</li>"+
      "</ul>"+
	"</li>"+
	"<li class='context-class-a-more_pop'>"+
      "<ul id='context_fb_more'>"+
		"<li>"+
		  Facebook.ulDiv()+
		"</li>"+
      "</ul>"+
	"</li>";
};

function ulBlogForm(cls,but)
{
	if ( cls == '' )
		cls = 'one_lbl';

	if ( but == '' )
		but = "<input type='button' id='b_blog_and_home' value='Blog and go to homepage' onclick='DvdList.blog(2)' style='width:180px;margin-right:10px'>"+
			  "<input type='button' value='Blog' onclick='DvdList.blog(1)' style='width:80px;margin-right:10px'>";

	// some sort of bug returns 'undefined' if this is not assigned to an intermediate variable
	cls = "<div style='margin:4px 0 4px 0'>"+
			"<form id='mform_dvd_blog' name='mform_dvd_blog' action='javascript:void(0)'>"+
			  "<div class='"+cls+"' style='margin:10px 0 4px 0;text-align:left'>"+
				"<div>"+
				  "<table style='width:100%;white-space:nowrap'>"+
					"<tr>"+
					  "<td style='width:97%'>Blog it: (<span id='rem1'>500</span>)</td>"+
					  "<td style='width:1%'>Publish to Facebook:</td>"+
					  "<td style='width:1%'><input id='blogfb_cb' type='checkbox' onclick='DvdList.blogfb()' /><img id='blogfb_img' src='http://dv1.us/d1/fb0.png' /></td>"+
					"</tr>"+
			      "</table>"+
				"</div>"+
				"<textarea id='n_dvd_blog' style='width:100%;height:40px' maxlength='500' wrap='soft' onkeyup='Edit.countDown(\"rem1\",500 - this.value.length)'></textarea>"+
			  "</div>"+
			  "<div style='text-align:right;margin:4px 0 4px 0;white-space:nowrap'>"+
				but+
				"<input type='button' value='Cancel' onclick='DvdList.blog(0)' style='width:72px'>"+
			  "</div>"+
			"</form>"+
		  "</div>";

	return cls;
};

/* --------------------------------------------------------------------- */

