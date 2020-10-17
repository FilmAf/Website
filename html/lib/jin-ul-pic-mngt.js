/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulPicMngt(s) // f_echo_ul_picmgt
{
    s.s +=
	"<li class='context-class-input-mp'>"+
	  "<ul id='help_pic' onclick='onMenuClick(this)'>"+
		"<li id='hm_pic_lg'>Show larger picture</li>"+
		"<li></li>"+
		"<li id='hm_pic_ed'>Edit</li>"+
		"<li id='hm_pic_de'>Delete</li>"+
		"<li id='hm_pic_be'>Replace with better quality pic"+
		  "<ul>"+
			"<li>"+
			  "<div class='pop_div' style='white-space:nowrap;text-align:center;width:288px;padding-bottom:6px'>"+
				"<form enctype='multipart/form-data' id='beform' name='beform' method='post' action='/utils/x-pic-edit.html' target='target_pic'>"+
				  "<div style='background-color:#144067;padding:4px 0 2px 0;font:bold 11px \"Trebuchet MS\";color:#fff;margin-bottom:10px'>"+
					"Replace Picture with One of Better Quality"+
				  "</div>"+
				  "Please select the picture you wish to upload:"+
				  "<div style='margin:10px 0 10px 0'><input type='file' name='file' size='30' /></div>"+
				  "<input type='hidden' id='act' name='act' value='rep' />"+
				  "<input type='hidden' id='step' name='step' value='upload' />"+
				  "<input type='hidden' id='pic' name='pic' value='' />"+
				  "<input type='hidden' id='pic_edit' name='pic_edit' value='' />"+
				  "<input type='hidden' id='obj_type_r' name='obj_type' value='' />"+
				  "<input type='hidden' id='obj_r' name='obj' value='' />"+
				  "<input type='hidden' id='obj_edit_r' name='obj_edit' value='' />"+
				  "<input type='hidden' id='seed_r' name='seed' value='' />"+
				  "<input type='hidden' id='replace_pic' name='replace_pic' value='' />"+
				  "<input type='submit' value='Upload' style='width:100px' />"+
				"</form>"+
			  "</div>"+
			"</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_pic_wi'>Withdraw request</li>"+
		"<li></li>"+
		"<li id='hm_pic_df'>Make default picture</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-pic' onclick='onMenuClick(this)'>"+
	  "<ul id='help_new'>"+
		"<li>"+
		  "<div class='pop_div' style='white-space:nowrap;text-align:center;width:288px;padding-bottom:6px'>"+
			"<form enctype='multipart/form-data' id='nwform' name='nwform' method='post' action='/utils/x-pic-edit.html' target='target_pic'>"+
			  "<div style='background-color:#144067;padding:4px 0 2px 0;font:bold 11px \"Trebuchet MS\";color:#fff;margin-bottom:10px'>"+
				"Add New Picture"+
			  "</div>"+
			  "Please select the picture you wish to upload:"+
			  "<div style='margin:10px 0 10px 0'><input type='file' name='file' size='30' /></div>"+
			  "<input type='hidden' id='act' name='act' value='new' />"+
			  "<input type='hidden' id='step' name='step' value='upload' />"+
			  "<input type='hidden' id='obj_type_n' name='obj_type' value='' />"+
			  "<input type='hidden' id='obj_n' name='obj' value='' />"+
			  "<input type='hidden' id='obj_edit_n' name='obj_edit' value='' />"+
			  "<input type='hidden' id='seed_n' name='seed' value='' />"+
			  "<input type='submit' value='Upload' style='width:100px' />"+
			"</form>"+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

