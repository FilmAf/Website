/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulFriends(s) // echoFriendMenu
{
    s.s +=
	"<li id='menu-home-invite'>"+
	  "<ul id='home_invite'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0'>"+
			"<form id='mform_invite' name='mform_invite' action='javascript:void(0)'>"+
			  "<table>"+
				"<tr>"+
				  "<td colspan='2' style='padding:10px 0 12px 0'>"+
					"<h2>Inviting <span id='n_friend_1'></span></h2>"+
					"<div style='margin:4px 0 2px 2px'>Remind <span id='n_friend_2'></span> of who you are or introduce yourself:</div>"+
					"<textarea id='n_invite_text' style='width:400px;height:100px' maxlength='500' wrap='soft'></textarea>"+
				  "</td>"+
				"</tr>"+
				"<tr>"+
				  "<td width='190px'>"+
					"<img id='i_img' src='http://dv1.us/d1/1.gif' alt='Verification code' />"+
				  "</td>"+
				  "<td width='210px' style='padding-left:10px'>"+
					"Verification code: "+
					"<input id='n_seed_img' size='12' maxlength='12' type='text' value='' />"+
					"<input id='n_seed_ext' type='hidden' value='' />"+
					"<input id='n_friend_id' type='hidden' value='' />"+
					"<input id='n_friend_email' type='hidden' value='' />"+
				  "</td>"+
				"</tr>"+
				"<tr>"+
				  "<td colspan='2' style='text-align:right;padding:12px 0 4px 0'>"+
					"<input type='button' value='Send invite' onclick='FriendInvite.send(1)' style='width:96px;margin-right:10px' />"+
					"<input type='button' value='Cancel' onclick='FriendInvite.send(0)' style='width:72px' />"+
				  "</td>"+
				"</tr>"+
			  "</table>"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

