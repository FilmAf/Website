<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function getPicSubDomain($s_pic_name)
{
    $n = strpos($s_pic_name,'-');
    if ( $n <= 2 ) $n = strpos($s_pic_name,'.');
    if ( $n > 2 )
    {
	$c = intval($s_pic_name{$n - 1});
	return $c <= 1 ? '' : ($c <= 4 ? 'a.' : ($c <= 6 ? 'b.' : 'c.'));
    }
    return '';
}
function getPicLocation($s_pic_name,$b_thumbs)
{
    $n = strpos($s_pic_name,'-');
    if ( $n <= 2 ) $n = strpos($s_pic_name,'.');
    if ( $n > 2 )
    {
//	$c = intval($s_pic_name{$n - 1});
//	$c = $c <= 1 ? '' : ($c <= 4 ? 'a.' : ($c <= 6 ? 'b.' : 'c.'));
//	return "http://{$c}dv1.us/p". ($b_thumbs ? '0' : '1') ."/". substr($s_pic_name, $n - 3, 3);
	return "http://dv1.us/p". ($b_thumbs ? '0' : '1') ."/". substr($s_pic_name, $n - 3, 3);
    }
    return '';
}
function getPicDir($s_pic_name)
{
    if ( ($n = strpos($s_pic_name,'-')) > 2 ) return substr($s_pic_name, $n - 3, 3);
    if ( ($n = strpos($s_pic_name,'.')) > 2 ) return substr($s_pic_name, $n - 3, 3);
    return '';
}

function decodeTransforms($s_prefix, &$a_transform, $s_transform)
{
    $a_transform[$s_prefix.'img_treatment'] = 'B';
    $a_transform[$s_prefix.'rot_degrees'  ] = 0;
    $a_transform[$s_prefix.'rot_degrees_x'] = 0;
    $a_transform[$s_prefix.'crop_fuzz'    ] = 0;
    $a_transform[$s_prefix.'crop_x1'      ] = 0;
    $a_transform[$s_prefix.'crop_x2'      ] = 0;
    $a_transform[$s_prefix.'crop_y1'      ] = 0;
    $a_transform[$s_prefix.'crop_y2'      ] = 0;
    $a_transform[$s_prefix.'black_pt'     ] = 0;
    $a_transform[$s_prefix.'white_pt'     ] = 100;
    $a_transform[$s_prefix.'gamma'        ] = 1.0;

    if ( $s_transform && $s_transform != '-' )
    {
	$a = explode('|', $s_transform);
	for ( $i = 0 ; $i < count($a) ; $i += 2 )
	{
	    switch ( $a[$i] )
	    {
	    case 'it': $a_transform[$s_prefix.'img_treatment'] = $a[$i+1];		break;
	    case 'rd': $a_transform[$s_prefix.'rot_degrees'  ] = floatval($a[$i+1]);	break;
	    case 'ry': $a_transform[$s_prefix.'rot_degrees_x'] = intval($a[$i+1]);	break;
	    case 'fu': $a_transform[$s_prefix.'crop_fuzz'    ] = intval($a[$i+1]);	break;
	    case 'x1': $a_transform[$s_prefix.'crop_x1'      ] = intval($a[$i+1]);	break;
	    case 'x2': $a_transform[$s_prefix.'crop_x2'      ] = intval($a[$i+1]);	break;
	    case 'y1': $a_transform[$s_prefix.'crop_y1'      ] = intval($a[$i+1]);	break;
	    case 'y2': $a_transform[$s_prefix.'crop_y2'      ] = intval($a[$i+1]);	break;
	    case 'bk': $a_transform[$s_prefix.'black_pt'     ] = intval($a[$i+1]);	break;
	    case 'wh': $a_transform[$s_prefix.'white_pt'     ] = intval($a[$i+1]);	break;
	    case 'ga': $a_transform[$s_prefix.'gamma'        ] = floatval($a[$i+1]);	break;
	    }
	}
    }
}

function encodeTransforms($s_prefix, &$a_transform)
{
    $s = '';
    $a = $a_transform[$s_prefix.'img_treatment'];						  $s .= 'it|'.$a.'|';
    $a = $a_transform[$s_prefix.'rot_degrees'  ]; if ( $a >  -360.0 && $a <  360.0 && $a != 0.0 ) $s .= 'rd|'.$a.'|';
    $a = $a_transform[$s_prefix.'rot_degrees_x']; if ( $a >= -100   && $a <= 100   && $a != 0   ) $s .= 'ry|'.$a.'|';
    $a = $a_transform[$s_prefix.'crop_fuzz'    ]; if ( $a >     0   && $a <  100		) $s .= 'fu|'.$a.'|';
    $a = $a_transform[$s_prefix.'crop_x1'      ]; if ( $a >     0   && $a < 2000		) $s .= 'x1|'.$a.'|';
    $a = $a_transform[$s_prefix.'crop_x2'      ]; if ( $a >     0   && $a < 2000		) $s .= 'x2|'.$a.'|';
    $a = $a_transform[$s_prefix.'crop_y1'      ]; if ( $a >     0   && $a < 2000		) $s .= 'y1|'.$a.'|';
    $a = $a_transform[$s_prefix.'crop_y2'      ]; if ( $a >     0   && $a < 2000		) $s .= 'y2|'.$a.'|';
    $a = $a_transform[$s_prefix.'black_pt'     ]; if ( $a >     0   && $a <   90		) $s .= 'bk|'.$a.'|';
    $a = $a_transform[$s_prefix.'white_pt'     ]; if ( $a >    10   && $a <  100		) $s .= 'wh|'.$a.'|';
    $a = $a_transform[$s_prefix.'gamma'        ]; if ( $a >   0.1   && $a <  3.0   && $a != 1.0 ) $s .= 'ga|'.$a.'|';

    $s = substr($s, 0, -1);
    return $s ? $s : '-';
}

function describeTransforms($s_prefix, &$a_transform)
{
    if ( ! isset($a_transform[$s_prefix.'img_treatment']) )
	return '';

    $s = '';
    $a = $a_transform[$s_prefix.'rot_degrees'  ]; if ( $a >  -360.0 && $a <  360.0 && $a != 0.0 ) $s .= "Rotate: {$a} degrees<br />";
    $a = $a_transform[$s_prefix.'rot_degrees_x']; if ( $a >= -100   && $a <= 100   && $a != 0   ) $s .= "Rotate: {$a} X pixels<br />";
    $a = $a_transform[$s_prefix.'crop_fuzz'    ]; if ( $a >     0				) $s .= "Autocrop fuzz: {$a}<br />";
    $a = $a_transform[$s_prefix.'crop_x1'      ]; if ( ! $a ) $a = 0;
    $b = $a_transform[$s_prefix.'crop_x2'      ]; if ( ! $b ) $b = 0;
    $c = $a_transform[$s_prefix.'crop_y1'      ]; if ( ! $c ) $c = 0;
    $d = $a_transform[$s_prefix.'crop_y2'      ]; if ( ! $d ) $d = 0; if ( $a || $b || $c || $d ) $s .= "Crop: ($a, $c, $b, $d)<br />";
    $a = $a_transform[$s_prefix.'black_pt'     ]; if ( ! $a ) $a = 0;
    $b = $a_transform[$s_prefix.'white_pt'     ]; if ( ! $b ) $b = 0; if ( $a || ($b > 0 && $b != 100) ) $s .= "Levels: ($a, $b)<br />";
    $a = $a_transform[$s_prefix.'gamma'        ]; if ( $a > 0.1 && $a < 3.0 && $a != 1.0	       ) $s .= "Gamma: {$a}<br />";

    switch ( $a_transform[$s_prefix.'img_treatment'] )
    {
    case 'O': $s .= "DVD: No border<br />";			break;
    case 'B': $s .= "White cover DVDs: adds border<br />";	break;
    case 'K': $s .= "3D and posters: keeps HxV ratio<br />";	break;
    case 'H': $s .= "HD DVD: shorter size<br />";		break;
    case 'R': $s .= "Blu-ray: shorter size<br />";		break;
    case 'F': $s .= "Movie frame 16:9: adds bars<br />";	break;
    }

    return substr($s, 0, -6);
}

function cmdTransforms($s_prefix, &$a_transform)
{
    $s = '';

    switch ( $a_transform[$s_prefix.'img_treatment'] )
    {
    case 'B': $s .= 'bo '; break;
    case 'H': $s .= 'hd '; break;
    case 'R': $s .= 'bd '; break;
    case 'O': $s .= 'nb '; break;
    case 'K': $s .= 'kr '; break;
    case 'F': $s .= 'sc '; break;
    default : $s .= 'bo '; break;
    }

    $a = $a_transform[$s_prefix.'rot_degrees'  ]; if ( $a >  -360.0 && $a <  360.0 && $a != 0.0 ) $s .= 'rotate '  .$a.' ';
    $a = $a_transform[$s_prefix.'rot_degrees_x']; if ( $a >= -100   && $a <= 100   && $a != 0   )
    {
	$dx = floatval(isset($a_transform['pic_dx']) ? $a_transform['pic_dx']: '0'); // isset added due to error log entries
	$dy = floatval(isset($a_transform['pic_dy']) ? $a_transform['pic_dy']: '0'); // isset added due to error log entries
	if ( $dx > 0 && $dy > 0 )
	{
	    $b_neg = false;
	    if ( $a < 0 )
	    {
		$a = -$a;
		$b_neg = true;
	    }

	    $a = ($dx / 2 - $a) / (sqrt($dx * $dx + $dy * $dy) / 2);
	    $b = atan($dy/$dx);
	    if ( $a > 0 && $a < 1 )
	    {
		$a = acos($a) - $b;
		$a = ($b_neg ? - $a : $a) * 180 / 3.14159265359;
		$s .= 'rotate '.$a.' ';
	    }
	}
    }
    $a = $a_transform[$s_prefix.'crop_fuzz'    ]; if ( $a >   0 && $a <  100		  ) $s .= 'autocrop '.$a.' ';
    $a = $a_transform[$s_prefix.'crop_x1'      ]; if ( $a >   0 && $a < 2000		  ) $s .= 'crop_x1 ' .$a.' ';
    $a = $a_transform[$s_prefix.'crop_x2'      ]; if ( $a >   0 && $a < 2000		  ) $s .= 'crop_x2 ' .$a.' ';
    $a = $a_transform[$s_prefix.'crop_y1'      ]; if ( $a >   0 && $a < 2000		  ) $s .= 'crop_y1 ' .$a.' ';
    $a = $a_transform[$s_prefix.'crop_y2'      ]; if ( $a >   0 && $a < 2000		  ) $s .= 'crop_y2 ' .$a.' ';
    $a = $a_transform[$s_prefix.'black_pt'     ]; if ( $a >   0 && $a <   90		  ) $s .= 'black '   .$a.' ';
    $a = $a_transform[$s_prefix.'white_pt'     ]; if ( $a >  10 && $a <  100		  ) $s .= 'white '   .$a.' ';
    $a = $a_transform[$s_prefix.'gamma'        ]; if ( $a > 0.1 && $a <  3.0 && $a != 1.0 ) $s .= 'gamma '   .$a.' ';

    return substr($s, 0, -1);
}

function getPicEditListSql($n_dvd_id, $n_edit_id, $s_user_id)
{
    $s_where = $n_edit_id ? ( $n_dvd_id ? "(s.obj_edit_id = {$n_edit_id} or (s.obj_id = {$n_dvd_id} and s.disposition_cd = '-'))"
					: "s.obj_edit_id = {$n_edit_id}"
			    )
			  : "s.obj_id = {$n_dvd_id} and s.disposition_cd = '-'". ($s_user_id ? " and s.proposer_id = '{$s_user_id}'" : '');

    $s_union = "SELECT s.pic_id, p.version_id, p.sub_version_id, s.pic_name, s.pic_type, p.transforms transforms_old, p.pic_uploaded_tm, p.pic_uploaded_by, ".
		      "p.pic_edited_tm, p.pic_edited_by, p.pic_verified_tm, p.pic_verified_by, s.pic_edit_id, s.obj_edit_id, s.request_cd, ".
		      "s.proposer_id, s.proposed_tm, s.updated_tm, s.uploaded_pic, s.transforms transforms_new, s.disposition_cd, ".
		      "s.updated_tm pic_refresh_tm, s.pic_edit_id p_sort, ".
		      "if(s.pic_id, CONCAT_WS(', ', if(p.pic_type = s.pic_type, NULL, 'picture type'), ".
						   "if(p.transforms = s.transforms, NULL, 'transforms'), ".
						   "if(p.caption = s.caption, NULL, 'caption'), ".
						   "if(p.copy_holder = s.copy_holder, NULL, 'copyright holder'), ".
						   "if(p.copy_year = s.copy_year, NULL, 'copyright year'), ".
						   "if(p.suitability_cd = s.suitability_cd, NULL, 'suitability')), ".
				   "'') diff ".
		 "FROM pic_submit s ".
		 "LEFT JOIN pic p ON s.pic_id = p.pic_id ".
		"WHERE s.obj_type = 'D' and {$s_where}";

    if ( $n_dvd_id )
    {
	$s_where  = $n_edit_id ? "(r.obj_edit_id = {$n_edit_id} or (r.obj_id = {$n_dvd_id} and r.disposition_cd = '-'))"
			       : "r.obj_id = {$n_dvd_id} and r.disposition_cd = '-'". ($s_user_id ? " and r.proposer_id = '{$s_user_id}'" : '');
	$s_union .= " UNION ".
		    "SELECT p.pic_id, p.version_id, p.sub_version_id, p.pic_name, p.pic_type, p.transforms transforms_old, p.pic_uploaded_tm, p.pic_uploaded_by, ".
			   "p.pic_edited_tm, p.pic_edited_by, p.pic_verified_tm, p.pic_verified_by, 0 pic_edit_id, 0 obj_edit_id, '-' request_cd, ".
			   "'-' proposer_id, '' proposed_tm, '' updated_tm, '' uploaded_pic, '-' transforms_new, '-' disposition_cd, ".
			   "coalesce(pic_edited_tm, pic_verified_tm, pic_uploaded_tm) pic_refresh_tm, (a.sort_order + 999999) p_sort, '' diff ".
		      "FROM dvd_pic a JOIN pic p ON a.pic_id = p.pic_id ".
		     "WHERE a.dvd_id = {$n_dvd_id} ".
		       "and not exists (SELECT 1 ".
					 "FROM pic_submit r ".
					"WHERE r.obj_type = 'D' and {$s_where} ".
					  "and a.pic_id = r.pic_id)";
    }

    return  "SELECT a.pic_id, a.version_id, a.sub_version_id, a.pic_name, d.descr pic_type_txt, a.transforms_old, a.pic_uploaded_tm, a.pic_uploaded_by, ".
		   "a.pic_edited_tm, a.pic_edited_by, a.pic_verified_tm, a.pic_verified_by, a.pic_edit_id, a.obj_edit_id, request_cd, e.descr request_txt, ".
		   "a.proposer_id, a.proposed_tm, a.updated_tm, a.uploaded_pic, a.transforms_new, a.disposition_cd, f.descr disposition_txt, ".
		   "a.pic_refresh_tm, a.diff ".
	      "FROM ({$s_union}) a ".
		   "LEFT JOIN pic_category_sort s ON s.obj_type = 'D' and a.pic_type = s.pic_type ".
		   "LEFT JOIN decodes d ON d.domain_type = 'pic_type' and a.pic_type = d.code_char ".
		   "LEFT JOIN decodes e ON e.domain_type = 'request_cd (pic)' and a.request_cd = e.code_char ".
		   "LEFT JOIN decodes f ON f.domain_type = 'disposition_cd' and a.disposition_cd = f.code_char ".
	     "ORDER BY s.sort_order, a.pic_id, a.p_sort, a.pic_edit_id";
}

function drawPicSubCurr(&$ln, $s_def_pic, $b_options)
{
    if ( $ln['pic_name'] && $ln['pic_name'] != '-' )
    {
	$s_rand          = str_replace(' ','', str_replace(':','', str_replace('-','', $ln['pic_refresh_tm'])));
	$s_curr_pic_name = $ln['pic_name']; // 103629-d1
	$s_curr_id	 = "{$s_curr_pic_name}_{$ln['pic_id']}_0_-_-_-";
//	$s_curr_pic_name = "<img src='http://dv1.us/p0/". getPicDir($s_curr_pic_name) ."/{$s_curr_pic_name}.gif?tm={$s_rand}'>".
	$s_curr_pic_name = "<img src='". getPicLocation($s_curr_pic_name,true) ."/{$s_curr_pic_name}.gif?tm={$s_rand}'>".
			   ($b_options ? "<br /><input id='picp_{$s_curr_id}' type='button' value='Options' class='mp'>" : '');
    }
    else
    {
	$s_curr_pic_name = "<img src='http://dv1.us/di/1.gif' width='63' height='90' />";
    }

    return "<td class='sg' style='text-align:center;vertical-align:top'>{$s_curr_pic_name}<input type='hidden' id='def_pic' value='{$s_def_pic}'></td>";
}

function drawPicSubProp(&$ln, $s_pic_dispo_cd, $s_request_cd, $s_base_subdomain, &$s_prop_id, $b_options, $b_is_proposer)
{
    if ( $ln['pic_edit_id'] )
    {
	$s_rand		 = str_replace(' ','', str_replace(':','', str_replace('-','', $ln['pic_refresh_tm'])));
	$s_prop_pic_name = $ln['uploaded_pic'  ]; if ( !$s_prop_pic_name || $s_prop_pic_name == '-' ) $s_prop_pic_name = sprintf('%06d',$ln['pic_edit_id']);
	$s_prop_id       = "{$s_prop_pic_name}_{$ln['pic_id']}_{$ln['pic_edit_id']}_{$s_pic_dispo_cd}_{$s_request_cd}_" . ($b_is_proposer ? 'P' : 'M');

	if ( $ln['disposition_cd'] == 'A' && $ln['pic_name'] != '-' )
//	    $s_src = "http://dv1.us/p0/".getPicDir($ln['pic_name'])."/{$ln['pic_name']}.gif";
	    $s_src = getPicLocation($ln['pic_name'],true)."/{$ln['pic_name']}.gif";
	else
	    $s_src = "{$s_base_subdomain}/uploads/{$s_prop_pic_name}-prev.gif";

	$s_prop_pic_name = "<img src='{$s_src}?tm={$s_rand}'>".
			   ($b_options ? "<br /><input id='picp_{$s_prop_id}' type='button' value='Options' class='mp'>" : '');
    }
    else
    {
	$s_prop_pic_name = "<img src='http://dv1.us/di/1.gif' width='63' height='90' />";
	$s_prop_id	 = '';
    }

    return "<td class='sg' style='text-align:center;vertical-align:top'>{$s_prop_pic_name}</td>";
}

function drawPicSubNotes(&$ln, $n_req_def_edit_id, &$n_req_def_pic_id, $n_req_def_pic_edit_id, $s_pic_dispo_txt, $s_def_dispo_txt, $b_curr, $s_prop_id)
{
    $s_orig = $a_orig = '';
    $s_prop = $a_prop = '';

    if ( $ln['pic_id'] && $b_curr )
    {
	if ( $ln['transforms_old' ] != '-' ) { $a_orig = array(); decodeTransforms('', $a_orig, $ln['transforms_old']); $a_orig = describeTransforms('', $a_orig); }
					     $a_orig  = $a_orig ? "Actions:<div style='margin-left:20px'>{$a_orig}</div>" : 'No actions were applied to this image';
	if ( $ln['pic_uploaded_by'] != '-' ) $s_orig .= "<tr class='qe'><td style='text-align:right'>Uploaded by</td><td class='qd'>{$ln['pic_uploaded_by']}</td><td>on {$ln['pic_uploaded_tm']}</td></tr>";
	if ( $ln['pic_edited_by'  ] != '-' ) $s_orig .= "<tr class='qe'><td style='text-align:right'>Edited by </td><td class='qd'>{$ln['pic_edited_by']}</td><td>on {$ln['pic_edited_tm']}</td></tr>";
	if ( $ln['pic_verified_by'] != '-' ) $s_orig .= "<tr class='qe'><td style='text-align:right'>Verified by </td><td class='qd'>{$ln['pic_verified_by']}</td><td>on {$ln['pic_verified_tm']}</td></tr>";
	$s_orig = "<div class='qa'>".
		    "<div>Current picture: <span class='qe'>{$ln['pic_id']}</span> &nbsp; Version <span class='qe'>{$ln['version_id']}.{$ln['sub_version_id']}</span></div>".
		    "<div style='margin-left:20px'>".
		       ( $a_orig ? "<div>{$a_orig}</div>"     : '' ).
		       ( $s_orig ? "<table>{$s_orig}</table>" : '' ).
		    "</div>".
		  "</div>";
    }

    if ( $ln['pic_edit_id'] )
    {
	if ( $s_pic_dispo_txt ) $s_pic_dispo_txt = " ({$s_pic_dispo_txt})";
	if ( $s_def_dispo_txt ) $s_def_dispo_txt = " ({$s_def_dispo_txt})";

	$s_audit_id = $ln['pic_edit_id'];
	$s_request  = $ln['request_txt'];
//	if ( $n_req_def_pic_edit_id )
//	{
//	    if ( $ln['pic_edit_id'] == $n_req_def_pic_edit_id )
//	    {
//		$s_request  .= ", default for listing{$s_def_dispo_txt}";
//		$s_audit_id .= "-{$n_req_def_edit_id}";
//	    }
//	}
//	else
//	{
	    if ( $n_req_def_pic_id && $ln['pic_id'] == $n_req_def_pic_id )
	    {
		$s_request  .= ", default for listing{$s_def_dispo_txt}";
		$s_audit_id .= "-{$n_req_def_edit_id}";
//		$s_request	  = "Default for listing{$s_def_dispo_txt}";
//		$s_audit_id       = "0-{$n_req_def_edit_id}";
		$n_req_def_pic_id = 0;
	    }
//	}
	$s_request .= "<span id='pics_{$s_prop_id}'>{$s_pic_dispo_txt}</span>";

	if ( $ln['transforms_new' ] != '-' ) { $a_prop = array(); decodeTransforms('', $a_prop, $ln['transforms_new']); $a_prop = describeTransforms('', $a_prop); }
					     $a_prop  = $a_prop ? "Actions:<div style='margin-left:20px'>{$a_prop}</div>" : 'No actions';
	if ( $ln['proposer_id'    ] != '-' ) $s_prop .= "<tr class='qe'><td style='text-align:right'>Proposed by </td><td class='qd'>{$ln['proposer_id']}</td><td>on {$ln['proposed_tm']}</td></tr>".
							($ln['proposed_tm'] != $ln['updated_tm'] ? "<tr class='qe'><td colspan='2' style='text-align:right'>Last updated</td><td>on {$ln['updated_tm']}</td></tr>" : '');
	$s_prop = ( $ln['disposition_cd'] == '-'
		    ? "<div class='qc'><div>Pending request: "
		    : "<div class='qb'><div>Processed request: "
		  ).
		      "<span class='qd'>{$s_request}</span>".($s_audit_id ? "<span class='qe'> (audit id {$s_audit_id})</span>" : '')."</div>".
		    ( $ln['obj_edit_id'] ? "<div>Part of obj submission: <span class='qe'>{$ln['obj_edit_id']}</span></div>" : '').
		    "<div style='margin-left:20px'>".
		      ( $ln['diff'] ? "<div>Attribute edits: {$ln['diff']}</div>" : '' ).
		      ( $a_prop ? "<div>{$a_prop}</div>"     : '' ).
		      ( $s_prop ? "<table>{$s_prop}</table>" : '' ).
		    "</div>".
		  "</div>";
    }

    $s_notes = $s_orig . $s_prop;
   
    return "<td class='sg' style='vertical-align:top;padding-top:4px'>".
	     ($s_notes ? "<img src='http://dv1.us/di/1.gif' height='1' width='380px' />". $s_notes : '&nbsp;').
	   "</td>";
}

function drawPicSub(&$ln, $s_def_pic, $n_req_def_edit_id, &$n_req_def_pic_id, $n_req_def_pic_edit_id, $s_pic_dispo_cd, $s_pic_dispo_txt, $s_def_dispo_cd, $s_def_dispo_txt, $s_request_cd, $s_base_subdomain, $b_curr, $b_options, $b_is_proposer)
{
    $s_prop_id = '';
		   $str  = '';
    if ( $b_curr ) $str .= drawPicSubCurr ($ln, $s_def_pic, $b_options);
		   $str .= drawPicSubProp ($ln, $s_pic_dispo_cd, $s_request_cd, $s_base_subdomain, $s_prop_id, $b_options, $b_is_proposer);
		   $str .= drawPicSubNotes($ln, $n_req_def_edit_id, $n_req_def_pic_id, $n_req_def_pic_edit_id, $s_pic_dispo_txt, $s_def_dispo_txt, $b_curr, $s_prop_id);
    return $str;
}

?>
