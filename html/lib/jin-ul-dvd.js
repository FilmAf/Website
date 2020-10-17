/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulDvd(s) // f_echo_ul_dvd
{
    s.s +=
	"<li class='context-class-a-dvd_gen'>"+
	  "<ul id='context_gen' onclick='onMenuClick(this)'>"+
	    "<li></li>"+
	    "<li id='cm_gen_pin1'>"+
	      "<div style='position:relative;left:-16px'>"+
	        "<strong>WARNING:</strong> Pinned criteria likely to<br />restrict contextual search results."+
	      "</div>"+
	    "</li>"+
	    "<li id='cm_gen_pin2'></li>"+
	    "<li id='cm_gen0_title'><div>Genre</div></li>"+
	    "<li></li>"+
	    "<li id='cm_gen0_filmaf'>FilmAf Database</li>"+
	    "<li id='cm_gen0_mine'>My Collection</li>"+
	    "<li id='cm_gen0_this'>This Collection</li>"+
	    "<li></li>"+
	    "<li id='cm_gen1_title'><div>Genre</div></li>"+
	    "<li id='cm_gen1_sep1'></li>"+
	    "<li id='cm_gen1_filmaf'>FilmAf Database</li>"+
	    "<li id='cm_gen1_mine'>My Collection</li>"+
	    "<li id='cm_gen1_this'>This Collection</li>"+
	    "<li id='cm_gen1_sep2'></li>"+
	    "<li id='cm_gen2_title'><div>Genre</div></li>"+
	    "<li></li>"+
	    "<li id='cm_gen2_filmaf'>FilmAf Database</li>"+
	    "<li id='cm_gen2_mine'>My Collection</li>"+
	    "<li id='cm_gen2_this'>This Collection</li>"+
	  "</ul>"+
	"</li>"+
	"<li class='context-class-a-dvd_imdb'>"+
	  "<ul id='context_imdb' onclick='onMenuClick(this)'>"+
	    "<li></li>"+
	    "<li id='cm_imdb_pin1'>"+
	      "<div>"+
	        "<strong>WARNING:</strong> Pinned criteria likely to<br />restrict contextual search results."+
	      "</div>"+
	    "</li>"+
	    "<li id='cm_imdb_pin2'></li>"+
	    "<li id='cm_imdb_title'><div>Search by &quot;<strong>IMDB id</strong>&quot;</div></li>"+
	    "<li></li>"+
//	    "<li id='cm_imdb_who'>Who&#39;s got it?</li>"+
//	    "<li></li>"+
	    "<li id='cm_imdb_filmaf'>FilmAf Database</li>"+
	    "<li id='cm_imdb_mine'>My Collection</li>"+
	    "<li id='cm_imdb_this'>This Collection</li>"+
	    "<li></li>"+
	    "<li id='cm_imdb_imdb'>IMDB site</li>"+
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
	    "<li id='cm_dir_this'>This Collection</li>"+
	    "<li id='cm_dir_xp'>Director Explorer</li>"+
	  "</ul>"+
	"</li>"+
	"<li class='context-class-a-dvd_pub'>"+
	  "<ul id='context_pub' onclick='onMenuClick(this)'>"+
	    "<li></li>"+
	    "<li id='cm_pub_pin1'>"+
	      "<div style='position:relative;left:-16px'>"+
	        "<strong>WARNING:</strong> Pinned criteria likely to<br />restrict contextual search results."+
	      "</div>"+
	    "</li>"+
	    "<li id='cm_pub_pin2'></li>"+
	    "<li id='cm_pub_title'><div>Publisher</div></li>"+
	    "<li></li>"+
	    "<li id='cm_pub_filmaf'>FilmAf Database</li>"+
	    "<li id='cm_pub_mine'>My Collection</li>"+
	    "<li id='cm_pub_this'>This Collection</li>"+
	  "</ul>"+
	"</li>";
};

function ulDvdOne(s) // f_echo_ul_dvdone
{
    s.s +=
	"<li class='context-class-a-dvd_pic'>"+
	  "<ul id='context_dvd' onclick='onMenuClick(this)'>"+
	    "<li id='cm_dvd_title'><div>DVD</div></li>"+
	    "<li></li>"+
	    "<li id='cm_dvd_large'>Show Large Pic</li>"+
	    "<li id='cm_dvd_blog'>Blog it"+
	      "<ul>"+
			"<li>"+
			  ulBlogForm('','')+
			"</li>"+
	      "</ul>"+
	    "</li>"+
		Facebook.ul()+
	    "<li></li>"+
	    "<li id='cm_dvd_one'>One Pager</li>"+
	    "<li id='cm_dvd_who'>Who&#39;s got it?</li>"+
	    "<li></li>"+
	    "<li id='cm_dvd_cart_add'>Add to Cart</li>"+
	    "<li id='cm_dvd_cart_del'>Remove from Cart</li>"+
	    "<li id='cm_dvd_prices'>Compare Prices</li>"+
		"<li></li>"+
	    "<li id='cm_dvd_edit'>Correct Listing Info</li>"+
	    "<li id='cm_dvd_pic'>Upload or Edit Pictures</li>"+
	    "<li id='cm_dvd_spi'>Select Display Picture"+
	      "<ul id='context_dvd_img'>"+
			"<li></li>"+
			"<li>"+
			  "<div id='cm_dvd_spi_div'>"+
				"Hello."+
			  "</div>"+
			"</li>"+
	      "</ul>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

function ulDvdMany(s) // f_echo_ul_dvdmany
{
	var b = typeof(DvdPricePrep) == 'undefined';
    s.s +=
	"<li class='context-class-a-dvd_pic'>"+
	  "<ul id='context_dvd' onclick='onMenuClick(this)'>"+
	    "<li id='cm_dvd_title'><div>DVD</div></li>"+
	    "<li></li>"+
	    "<li id='cm_dvd_large'>Show Large Pic</li>"+
	    "<li id='cm_dvd_blog'>Blog it"+
	      "<ul>"+
			"<li>"+
			  ulBlogForm('','')+
			"</li>"+
	      "</ul>"+
	    "</li>"+
		Facebook.ul()+
	    "<li></li>"+
	    "<li id='cm_dvd_one'>One Pager</li>"+
	    "<li id='cm_dvd_who'>Who&#39;s got it?</li>"+
	    "<li></li>"+
	    "<li id='cm_dvd_copy'>Move to"+
	      "<ul id='context_dvd_copy'>"+
			"<li id='cm_dvd_copy_last'>last</li>"+
			"<li></li>"+
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

( b ?   "<li></li>"+
	    "<li id='cm_dvd_edit'>Correct Listing Info</li>"+
	    "<li id='cm_dvd_pic'>Upload or Edit Pictures</li>"+
	    "<li id='cm_dvd_mine'>Edit Custom Info</li>"+
	    "<li id='cm_dvd_spi'>Select Display Picture"+
	      "<ul id='context_dvd_img'>"+
			"<li></li>"+
			"<li>"+
			  "<div id='cm_dvd_spi_div'>"+
				"Hello."+
			  "</div>"+
			"</li>"+
	      "</ul>"+
	    "</li>" : '')+

	    "<li></li>"+
	    "<li id='cm_sel'><a><span style='position:relative;left:-16px'>Action on <strong>Selected Titles</strong></span></a>"+
	      "<ul id='context_sel'>"+
			"<li id='cm_sel_who'>Who&#39;s got them?</li>"+
			"<li></li>"+
			"<li id='cm_sel_copy'>Move to"+
			  "<ul id='context_sel_copy'>"+
			    "<li></li>"+
			    "<li id='cm_sel_copy_last'>last</li>"+
			    "<li id='cm_sel_copy_last_'></li>"+
			    "<li id='cm_sel_copy_owned'>owned</li>"+
			    "<li id='cm_sel_copy_order'>on-order</li>"+
			    "<li id='cm_sel_copy_wish'>wish-list</li>"+
			    "<li id='cm_sel_copy_work'>work</li>"+
			    "<li id='cm_sel_copy_seen'>have-seen</li>"+
			  "</ul>"+
			"</li>"+
			"<li id='cm_sel_delete'>Delete from My Collection</li>"+
			"<li></li>"+
			"<li id='cm_sel_cart_add'>Add to Cart</li>"+
			"<li id='cm_sel_cart_del'>Remove from Cart</li>"+
			"<li id='cm_sel_prices'>Compare Prices</li>"+

( b ?		"<li></li>"+
			"<li id='cm_sel_edit'>Correct Listing Info</li>"+
			"<li id='cm_sel_mine'>Edit Custom Info</li>" : '')+

			"<li></li>"+
			"<li id='cm_sel_inv'>Toggle Selection</li>"+
			"<li id='cm_sel_all'>Select All</li>"+
			"<li id='cm_sel_none'>Unselect All</li>"+
	      "</ul>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

