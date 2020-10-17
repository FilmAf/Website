/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulProfile(s) // echoProfileMenu
{
    s.s +=
	"<li id='menu-home-profile'>"+
	  "<ul id='home_profile'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0'>"+
			"<form id='mform_profile' name='mform_profile' action='javascript:void(0)'>"+
			  "<h2 style='padding:0 0 8px 0;margin:0'>"+Str.ucFirst(Filmaf.viewCollection)+"&#39;s Profile</h2>"+
			  "<table>"+

				"<tr>"+
				  "<td class='wg_tdl'>Name:<img id='ex_profile_name' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr' style='padding-right:20px'><input id='n_name' size='24' maxlength='64' type='text' value='' /><input id='o_name' type='hidden' value='' /></td>"+
//				  "<td rowspan='7' style='vertical-align:top'>"+

//				"<table border='1' style='border-collapse:separate'>"+
//				  "<tr><td colspan='2' style='text-align:center;white-space:nowrap'>In your FilmAf homepage show:</td></tr>"+
//				  "<tr><td style='width:50%;white-space:nowrap'><input id='n_show_profile_ind' type='checkbox' />Personal&nbsp;info</td>"+
//					  "<td style='width:50%;white-space:nowrap'><input id='n_show_blog_ind' type='checkbox' />Film&nbsp;Blog</td></tr>"+
//				  "<tr><td rowspan='3' style='vertical-align:top'><input id='n_show_stats_ind' type='checkbox' />Stats</td>"+
//					  "<td style='white-space:nowrap'><input id='n_show_friends_ind' type='checkbox' />Friends</td></tr>"+
//				  "<tr><td style='white-space:nowrap'><input id='n_show_updates_ind' type='checkbox' />Updates</td></tr>"+
//				  "<tr><td style='white-space:nowrap'><input id='n_show_wall_ind' type='checkbox' />Wall</td></tr>"+
//				"</table>"+

//				  "</td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>Date of Birth:<img id='ex_profile_dob' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr'><input id='n_dob' size='10' maxlength='10' type='text' value='' /><input id='o_dob' type='hidden' value='' /><span class='lowkey'> YYYY-MM-DD</span></td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>Sex:<img id='ex_profile_gender' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr'>"+
					"<input id='n_gender_M' name='n_gender' type='radio' value='M' />Male "+
					"<input id='n_gender_F' name='n_gender' type='radio' value='F' />Female "+
					"<input id='n_gender_P' name='n_gender' type='radio' value='P' />Private"+
					"<input id='o_gender' type='hidden' value='' />"+
				  "</td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>City:<img id='ex_profile_city' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr'><input id='n_city' size='24' maxlength='64' type='text' value='' /><input id='o_city' type='hidden' value='' /></td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>State:<img id='ex_profile_state' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr'><input id='n_state' size='16' maxlength='32' type='text' value='' /><input id='o_state' type='hidden' value='' /></td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>Country:<img id='ex_profile_country' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr'><input id='n_country' size='16' maxlength='32' type='text' value='' /><input id='o_country' type='hidden' value='' /></td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>Status:<img id='ex_profile_status' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr'>"+
					"<input id='n_status_S' name='n_status' type='radio' value='S' />Single "+
					"<input id='n_status_M' name='n_status' type='radio' value='M' />Married "+
					"<input id='n_status_R' name='n_status' type='radio' value='R' />In a relationship "+
					"<input id='n_status_P' name='n_status' type='radio' value='P' />Private"+
					"<input id='o_status' type='hidden' value='' />"+
				  "</td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>MySpace id:<img id='ex_profile_my_space' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr' colspan='2'><input id='n_my_space' size='54' maxlength='200' type='text' value='' /><input id='o_my_space' type='hidden' value='' /></td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>Facebook id:<img id='ex_profile_facebook' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr'><input id='n_facebook' size='54' maxlength='200' type='text' value='' /><input id='o_facebook' type='hidden' value='' /></td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>Homepage:<img id='ex_profile_homepage' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr'><input id='n_homepage' size='36' maxlength='128' type='text' value='' /><input id='o_homepage' type='hidden' value='' /></td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>About me:<img id='ex_profile_about_me' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr'><textarea id='n_about_me' style='width:98%;height:100px' maxlength='500' wrap='soft'></textarea><input id='o_about_me' type='hidden' value='' /></td>"+
				"</tr>"+

				"<tr>"+
				  "<td class='wg_tdl'>Video Intro:<img id='ex_profile_youtube' src='http://dv1.us/d1/00/bq00.png' width='17' height='17' align='absbottom' alt='Explain' /></td>"+
				  "<td class='wg_tdr' style='width:272px'>"+
//					"<input id='n_youtube_loop_ind' type='checkbox' />loop video<input id='o_youtube_loop_ind' type='hidden' value='' /> "+
					"YouTube id or URL <input id='n_youtube' size='24' maxlength='200' type='text' value='' /><input id='o_youtube' type='hidden' value='' />"+
					" <input id='n_youtube_auto_ind' type='checkbox' />auto play<input id='o_youtube_auto_ind' type='hidden' value='' />"+
				  "</td>"+
				"</tr>"+

			  "</table>"+
			  "<div style='text-align:right;margin:4px 0 4px 0'>"+
				   "<input type='button' value='Save' onclick='Profile.validate()' style='width:72px;margin-right:10px'>"+
				   "<input type='button' value='Cancel' onclick='Context.close()' style='width:72px'>"+
			  "</div>"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-home-profile-pic'>"+
	  "<ul id='home_profile_pic'>"+
	    "<li>"+
	      "<div class='pop_div' style='white-space:nowrap;text-align:center;width:288px;padding-bottom:6px'>"+
			"<form enctype='multipart/form-data' id='nwform' name='nwform' method='post' action='/utils/ajax-upload.php' target='iframe_pic'>"+
			  "<div style='background-color:#144067;padding:4px 0 2px 0;font:bold 11px \"Trebuchet MS\";color:#fff;margin-bottom:10px'>Upload Profile Picture</div>"+
			  "<iframe id='iframe_pic' name='iframe_pic' src='' style='width:1px;height:1px;border:0'></iframe>"+
			  "Please select your picture:"+
			  "<div style='margin:10px 0 10px 0'><input type='file' name='file' size='30' /></div>"+
			  "<input type='hidden' id='what' name='what' value='profile' />"+
			  "<input type='submit' value='Upload' style='width:100px' />"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

