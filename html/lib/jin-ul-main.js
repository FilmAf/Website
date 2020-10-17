/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulDvd(s)
{
    s.s +=
	"<li id='menu-release-img' class='context-class-a-dvd_pic'>"+
	  "<ul id='context_dvd' onclick='onMenuClick(this)'>"+
	    "<li id='cm_dvd_large'>Show Large Pic</li>"+
	    "<li id='cm_dvd_list'>Go to DVD listing</li>"+

	    "<li id='cm_dvd_blog'>Blog it"+
	      "<ul>"+
			"<li>"+
			  ulBlogForm('','')+
			"</li>"+
	      "</ul>"+
	    "</li>"+
		Facebook.ul()+
	    "<li></li>"+
	    "<li id='cm_dvd_copy'>Move to"+
	      "<ul id='context_dvd_copy'>"+
			"<li id='cm_dvd_copy_owned'>owned</li>"+
			"<li id='cm_dvd_copy_order'>on-order</li>"+
			"<li id='cm_dvd_copy_wish'>wish-list</li>"+
			"<li id='cm_dvd_copy_work'>work</li>"+
			"<li id='cm_dvd_copy_seen'>have-seen</li>"+
		  "</ul>"+
		"</li>"+
	    "<li id='cm_dvd_delete'>Delete from My Collection</li>"+
	    "<li></li>"+
	    "<li id='cm_dvd_cart_add'>Add to Cart</li>"+
	    "<li id='cm_dvd_cart_del'>Remove from Cart</li>"+
	    "<li id='cm_dvd_prices'>Compare Prices</li>"+
		"<li></li>"+
	    "<li id='cm_dvd_edit'>Correct Listing Info</li>"+
	    "<li id='cm_dvd_pic'>Upload or Edit Pictures</li>"+

	  "</ul>"+
	"</li>"+
	"<li class='context-class-a-dvd_dir'>"+
	  "<ul id='context_dir' onclick='onMenuClick(this)'>"+
		"<li></li>"+
		"<li id='cm_dir_pin1'>"+
		  "<div style='position:relative;left:-16px'>"+
			"<strong>WARNING:</strong> Pinned criteria likely to<br />restrict contextual search results."+
		  "</div>"+
		"</li>"+
		"<li id='cm_dir_pin2'></li>"+
		"<li id='cm_dir_title'><div>Director</div></li>"+
		"<li></li>"+
		"<li id='cm_dir_filmaf'>FilmAf Database</li>"+
		"<li id='cm_dir_mine'>My Collection</li>"+
	    "<li id='cm_dir_xp'>Director Explorer</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

