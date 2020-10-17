<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CWndMenu.php';

class CDvdExport extends CWndMenu
{
    function constructor() // <<--------------------<< 1.0
    {
		parent::constructor();
		CTrace::log_txt(__CLASS__ .'::'. __FUNCTION__);
//		$this->mb_show_trace		= true;
//		$this->mb_trace_environment	= true;
//		$this->mb_trace_sql			= true;
//		$this->mb_allow_redirect	= false;

		$this->ms_include_css  .=
			"<style type='text/css'>".
				".h1 {color:#3e719e; margin:12px 0 12px 0; font-size:12px; text-align:left; font-weight:bold}".
				".ho {color:#57af63}".
			"</style>";
    }

    function getHeaderJavaScript()
    {
		return	parent::getHeaderJavaScript().
			"function alldvd(c)".
			"{".
				"expCheck('a_dvd_id',1);".
				"expCheck('a_film_rel_year',c);".
				"expCheck('a_region_mask',c);".
				"expCheck('a_media_type',c);".
				"expCheck('a_publisher',c);".
			"};".
			"function allcol(c)".
			"{".
				"expCheck('b_comments',c);".
				"expCheck('b_owned_dd',c);".
				"expCheck('b_last_watched_dd',c);".
				"expCheck('b_user_film_rating',c);".
				"expCheck('b_user_dvd_rating',c);".
				"expCheck('b_sort_text',c);".
				"expCheck('b_genre_overwrite',c);".
				"expCheck('b_pic_overwrite',c);".
				"expCheck('b_custom_1',c);".
				"expCheck('b_custom_2',c);".
				"expCheck('b_custom_3',c);".
				"expCheck('b_custom_4',c);".
				"expCheck('b_custom_5',c);".
				"expCheck('b_retailer',c);".
				"expCheck('b_price_paid',c);".
				"expCheck('b_order_dd',c);".
				"expCheck('b_order_number',c);".
				"expCheck('b_trade_loan',c);".
				"expCheck('b_asking_price',c);".
				"expCheck('b_loaned_to',c);".
				"expCheck('b_loan_dd',c);".
				"expCheck('b_return_dd',c);".
				"expCheck('b_my_dvd_created_tm',c);".
			"};".
			"function expCheck(e,c)".
			"{".
				"if((e=document.getElementById(e)))".
				"e.checked=c;".
			"};";
    }

    function drawBodyPage() // <<-------------------------------<< 7.2
    {
		$b_windows   = stripos($_SERVER['HTTP_USER_AGENT'], 'Windows') !== false;

		echo
		"<form id='export_form' name='export_form' method='post' action='/export.txt'>".
		  "<div class='h1' style='padding:30px 0 20px 0'>Please select the fields you wish to export from your collection:</div>".
		  "<table>".
			"<tr>".
			  "<td style='vertical-align:middle'>".
				"<span class='h1'>DVD attributes:</span>&nbsp;&nbsp;&nbsp;<input type='checkbox' onclick='alldvd(this.checked)' /> select all".
			  "</td>".
			  "<td style='width:50px'>".
				"&nbsp;".
			  "</td>".
			  "<td style='vertical-align:middle'>".
				"<span class='h1'>Collection attributes:</span>&nbsp;&nbsp;&nbsp;<input type='checkbox' onclick='allcol(this.checked)' /> select all".
			  "</td>".
			"</tr>".
			"<tr>".
			  "<td style='vertical-align:top;padding:12px;white-space:nowrap'>".
				"<div><input id='a_dvd_id' name='a_dvd_id' type='checkbox' checked='checked' /> DVD id <span class='ho'>(needed for import)</span></div>".
				"<div><input id='a_dvd_title' name='a_dvd_title' type='checkbox' checked='checked' disabled='disabled' /> DVD title</div>".
				"<div><input id='a_film_rel_year' name='a_film_rel_year' type='checkbox' /> Film release year</div>".
				"<div><input id='a_region_mask' name='a_region_mask' type='checkbox' /> Region</div>".
				"<div><input id='a_media_type' name='a_media_type' type='checkbox' /> Media</div>".
				"<div><input id='a_publisher' name='a_publisher' type='checkbox' /> Publisher</div>".
			  "</td>".
			  "<td>".
				"&nbsp;".
			  "</td>".
			  "<td style='vertical-align:top;padding:12px;white-space:nowrap'>".
				"<div><input id='b_folder' name='b_folder' type='checkbox' checked='checked' disabled='disabled' /> Folder</div>".
				"<div><input id='b_comments' name='b_comments' type='checkbox' /> Comments</div>".
				"<div><input id='b_owned_dd' name='b_owned_dd' type='checkbox' /> Owned since</div>".
				"<div><input id='b_last_watched_dd' name='b_last_watched_dd' type='checkbox' /> Last watched</div>".
				"<div><input id='b_user_film_rating' name='b_user_film_rating' type='checkbox' /> Film rating</div>".
				"<div><input id='b_user_dvd_rating' name='b_user_dvd_rating' type='checkbox' /> DVD rating</div>".
				"<div>&nbsp;</div>".

				"<div><input id='b_sort_text' name='b_sort_text' type='checkbox' /> Sort overwrite</div>".
				"<div><input id='b_genre_overwrite' name='b_genre_overwrite' type='checkbox' /> Genre overwrite</div>".
				"<div><input id='b_pic_overwrite' name='b_pic_overwrite' type='checkbox' /> Picture overwrite</div>".
				"<div>&nbsp;</div>".

				"<div><input id='b_custom_1' name='b_custom_1' type='checkbox' /> Custom field #1</div>".
				"<div><input id='b_custom_2' name='b_custom_2' type='checkbox' /> Custom field #2</div>".
				"<div><input id='b_custom_3' name='b_custom_3' type='checkbox' /> Custom field #3</div>".
				"<div><input id='b_custom_4' name='b_custom_4' type='checkbox' /> Custom field #4</div>".
				"<div><input id='b_custom_5' name='b_custom_5' type='checkbox' /> Custom field #5</div>".
			  "</td>".
			  "<td style='vertical-align:top;padding:12px;white-space:nowrap'>".
				"<div><input id='b_retailer' name='b_retailer' type='checkbox' /> Retailer</div>".
				"<div><input id='b_price_paid' name='b_price_paid' type='checkbox' /> Price paid</div>".
				"<div><input id='b_order_dd' name='b_order_dd' type='checkbox' /> Order date</div>".
				"<div><input id='b_order_number' name='b_order_number' type='checkbox' /> Order number</div>".
				"<div>&nbsp;</div>".

				"<div><input id='b_trade_loan' name='b_trade_loan' type='checkbox' /> For trade or loan</div>".
				"<div><input id='b_asking_price' name='b_asking_price' type='checkbox' /> Asking price</div>".
				"<div><input id='b_loaned_to' name='b_loaned_to' type='checkbox' /> Loaned to</div>".
				"<div><input id='b_loan_dd' name='b_loan_dd' type='checkbox' /> Loaned date</div>".
				"<div><input id='b_return_dd' name='b_return_dd' type='checkbox' /> To be returned date</div>".
				"<div>&nbsp;</div>".

	//			"<div><input id='b_public_ind' name='b_public_ind' type='checkbox' /> Public indicator</div>".
				"<div><input id='b_my_dvd_created_tm' name='b_my_dvd_created_tm' type='checkbox' /> Added to collection date</div>".
	//			"<div><input id='b_my_dvd_updated_tm' name='b_my_dvd_updated_tm' type='checkbox' /> Last moved date</div>".
			  "</td>".
			"</tr>".
			"<tr>".
			  "<td colspan='3'>".
				"<table>".
				  "<tr>".
					"<td colspan='2'><span class='h1'>Your Computer:</span>&nbsp;&nbsp;&nbsp;<span class='ho'>(new line settings)</span></td>".
				  "</tr>".
				  "<tr>".
					"<td style='padding-left:20px'>".
					  "<input type='radio' name='newline' id='nl1' value='crlf' ".($b_windows ? "checked='checked' " : '')."/>".
					  "<img src='http://dv1.us/d1/oswin.gif' style='vertical-align:middle' onclick='document.getElementById(\"nl1\").checked=true' />".
					  "Windows".
					"</td>".
					"<td style='padding-left:20px'>".
					  "<input type='radio' name='newline' id='nl2' value='lf' "  .($b_windows ? '' : "checked='checked' ")."/>".
					  "<img src='http://dv1.us/d1/osmac.gif' style='vertical-align:middle' onclick='document.getElementById(\"nl2\").checked=true' />".
					  "Mac".
					"</td>".
				  "</tr>".
				  "<tr>".
					"<td style='padding-left:20px'>".
					  "<input type='radio' name='newline' id='nl3' value='LF' />".
					  "<img src='http://dv1.us/d1/oslinux.gif' style='vertical-align:middle' onclick='document.getElementById(\"nl3\").checked=true' />".
					  "Linux".
					"</td>".
					"<td style='padding-left:20px'>".
					  "<input type='radio' name='newline' id='nl4' value='cr' />".
					  "<img src='http://dv1.us/d1/osmac9.gif' style='vertical-align:middle' onclick='document.getElementById(\"nl4\").checked=true' />".
					  "Older Macs".
					"</td>".
				  "</tr>".
				  "<tr>".
					"<td colspan='2' style='padding:12px 0 8px 0'><span class='h1'>Format:</span></td>".
				  "</tr>".
				  "<tr>".
					"<td style='padding-left:20px;vertical-align:top;white-space:nowrap'>".
					  "<div style='vertical-align:middle'>".
						"<input type='radio' name='format' value='tab' checked='checked' /> Tab Separated ".
						"<span class='ho'>(backup, xls, db)</span>".
					  "</div>".
					"</td>".
					"<td style='padding-left:20px;vertical-align:top;white-space:nowrap'>".
					  "<div style='vertical-align:middle'>".
						"<input type='radio' name='format' value='fix' /> &quot;Fixed&quot; Length <span class='ho'>(best for printing)</span>".
						"<div style='padding-top:4px'>".
						  "Maximum title length: ".
						  "<select name='title_len' style='font-family:Fixed, monospace'>".
							"<option value='40'>&nbsp;40</option>".
							"<option value='50'>&nbsp;50</option>".
							"<option value='60' selected='selected'>&nbsp;60</option>".
							"<option value='70'>&nbsp;70</option>".
							"<option value='80'>&nbsp;80</option>".
							"<option value='90'>&nbsp;90</option>".
							"<option value='100'>100</option>".
							"<option value='110'>110</option>".
							"<option value='120'>120</option>".
							"<option value='-1'>All</option>".
						  "</select>".
						"</div>".
						"<div style='padding-top:5px'>".
						  "Borders and separators ".
						  "<input type='radio' name='bars' value='on' checked='checked' /> yes".
						  "&nbsp;&nbsp;&nbsp;".
						  "<input type='radio' name='bars' value='off' /> no".
						"</div>".
					  "</div>".
					"</td>".
				  "</tr>".
				"</table>".
			  "</td>".
			"</tr>".
		  "</table>".
		  "<div style='padding:20px 0 20px 0'>".
			"<input type='button' value='Cancel and go to homepage' style='width:210px' onclick='location.href=\"/\"' /> ".
			"<input type='button' value='Download file' style='width:140' onclick='this.form.submit()' ".($this->mb_view_self ? '' : "disabled='disabled' ")."/> ".
			( $this->mb_view_self ? '' :
			  "<div id='msg-err' style='padding:10px 0 0 20px'>".
				( $this->mb_logged_in
				  ? "Oops: You can only export your own collection."
				  : "Oops: In order to export your collection you must be <a href='{$this->ms_base_subdomain}/utils/login.html'>logged in</a>."
				).
			  "</div>"
			).
		  "</div>".
		"</form>";
    }
}

?>
