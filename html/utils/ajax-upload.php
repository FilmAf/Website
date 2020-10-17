<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require 'identify.php';
require $gs_root.'/utils/ajax.php';

define('MSG_BAD_FILENAME'	,     1);
define('MSG_UPLOAD_EMPTY'	,     2);
define('MSG_UPLOAD_ERROR'	,     3);
define('MSG_UPLOAD_PARTIAL'	,     4);
define('MSG_UPLOAD_SIZE'	,     5);
define('MSG_UPLOAD_BAD_DIR'	,     6);
define('MSG_UPLOAD_BASE_DIR'	,     7);
define('MSG_FAIL_TO_PROCESS'	,     8);

class CUpload extends CAjax
{
    function main()
    {
	$this->get_requester();
	if ( $this->ms_requester == '' || $this->ms_requester == 'guest' ) return;

	$this->ms_what    = dvdaf3_getvalue('what',DVDAF3_POST|DVDAF3_LOWER);
	$this->mn_blog    = dvdaf3_getvalue('blog',DVDAF3_POST|DVDAF3_INT  );
	$this->ms_context = ( $this->ms_what != '' ? "what='{$this->ms_what}' " : '').
			    ( $this->mn_blog != '' ? "blog='{$this->mn_blog}' " : '');
	$this->mn_count   = 0;
	$this->ms_msg     = '';
	$this->ma_cmdout  = array();
	$this->mn_cmdret  = 0;

	switch ( $this->ms_what )
	{
	case 'profile': $this->processProfilePic();		break;
	case 'blog':    $this->processBlogPic($this->mn_blog);	break;
	default:	return;
	}

	$this->respond();
    }

    function processProfilePic()
    {

	$s_target_url = $this->ms_requester{0};
	if ( $s_target_url < 'a' || $s_target_url > 'z' ) $s_target_url = '-';
	$s_target_url = "/usr/{$s_target_url}/{$this->ms_requester}";

	if ( $this->processUploaded('profile', $s_target_url) )
	{
	    $this->propagate("usr=".urlencode($s_target_url));
	    if ( CSql::query_and_free("UPDATE dvdaf_user_3 SET photo = '{$s_target_url}' WHERE user_id = '{$this->ms_requester}'",0,__FILE__,__LINE__) != 1 )
		CSql::query_and_free("INSERT INTO dvdaf_user_3 (user_id, photo, created_tm, updated_tm) VALUES('{$this->ms_requester}', '{$s_target_url}', now(), now())",CSql_IGNORE_ERROR,__FILE__,__LINE__);
	    $this->ms_ajax = $s_target_url;
	}
    }

    function processBlogPic($n_blog)
    {

	if ( $n_blog )
	{
	    $s_target_url = sprintf("/blog/%03d/%08d", $n_blog % 1000, $n_blog);

	    if ( $this->processUploaded('blog', $s_target_url) )
	    {
		$this->propagate("blog=".urlencode($s_target_url));
		// update SQL update stats on how much space delete oldest (blog and blog pic tables) till quota okay 
		//$this->ms_ajax = "status\tSUCCESS\tphoto\t{$s_target_url}\n";
		$this->ms_ajax = $s_target_url;
	    }
	}
    }

    function propagate($s_parm)
    {
//	$s_temp = '/var/www/html/uploads/filmaf.'.CTime::get_time().'.tmp';
//	copy("http://dv1.us/utils/propagate.php?{$s_parm}", $s_temp);
//	dvdaf3_exec("rm -f {$s_temp}", $this->ma_cmdout, $this->mn_cmdret);
    }

    function processUploaded($s_mode, $s_target_url)
    {
		if ( isset($_FILES['file']) && isset($_FILES['file']['tmp_name']) )
		{
			$s_base     = '/var/www/html/uploads';
			$s_load_pic = $_FILES['file']['tmp_name'];

			if ( preg_match('/[^a-zA-Z0-9\/\.-]/', $s_load_pic) )
				return $this->tellUser(__LINE__, MSG_BAD_FILENAME);

			if ( $_FILES['file']['error'] != 0 )
			{
				switch ( $_FILES['file']['error'] )
				{
				case UPLOAD_ERR_INI_SIZE:	return $this->tellUser(__LINE__, MSG_UPLOAD_SIZE);
				case UPLOAD_ERR_PARTIAL:	return $this->tellUser(__LINE__, MSG_UPLOAD_PARTIAL);
				case UPLOAD_ERR_NO_FILE:	return $this->tellUser(__LINE__, MSG_UPLOAD_EMPTY);
				default:			return $this->tellUser(__LINE__, MSG_UPLOAD_ERROR);
				}
			}

			if ( ! $s_load_pic										) return $this->tellUser(__LINE__, MSG_UPLOAD_EMPTY);
			if ( $_FILES['file']['size'] <= 0						) return $this->tellUser(__LINE__, MSG_UPLOAD_EMPTY);
			if ( get_cfg_var('upload_tmp_dir')           != $s_base ) return $this->tellUser(__LINE__, MSG_UPLOAD_BAD_DIR);
			if ( substr($s_load_pic, 0, strlen($s_base)) != $s_base ) return $this->tellUser(__LINE__, MSG_UPLOAD_BASE_DIR);

			$s_cmd = "/var/www/html/shell/process-social-pic {$s_mode} {$s_load_pic} /var/www/html{$s_target_url}";
			dvdaf3_exec($s_cmd, $this->ma_cmdout, $this->mn_cmdret);
			#echo "s_cmd = {$s_cmd}<br />". print_r($this->ma_cmdout). "<br />n_ret = {$this->mn_cmdret}<br />";

			if ( count($this->ma_cmdout) == 1 && $this->ma_cmdout[0] == 'SUCCESS' )
			{
				$this->ms_target_url = $s_target_url;
				return true;
			}
		}
		return $this->tellUser(__LINE__, MSG_FAIL_TO_PROCESS);
    }

    function tellUser($n_line, $n_what)
    {
	switch ( $n_what )
	{
	case MSG_BAD_FILENAME:		$this->ms_msg = 'Bad Uploaded filename.'; break;
	case MSG_UPLOAD_EMPTY:		$this->ms_msg = 'No file was uploaded.'; break;
	case MSG_UPLOAD_ERROR:		$this->ms_msg = 'Error uploading file.'; break;
	case MSG_UPLOAD_PARTIAL:	$this->ms_msg = 'Your file was only partially uploaded.'; break;
	case MSG_UPLOAD_SIZE:		$this->ms_msg = 'The uploaded file exceeds the maximum file size ('+get_cfg_var('upload_max_filesize')+').'; break;
	case MSG_UPLOAD_BAD_DIR:	$this->ms_msg = 'Base folder not set.'; break;
	case MSG_UPLOAD_BASE_DIR:	$this->ms_msg = 'Unrecognized base folder.'; break;
	case MSG_FAIL_TO_PROCESS:	$this->ms_msg = "Sorry the picture you uploaded seems to be invalid or corrupted."; break;
	}
	if ( $this->ms_msg ) $this->ms_msg .= " (code {$n_line})";

	return false;
    }

    function respond()
    {
	echo  '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'.
	      "<html>".
		"<head><title>404 Not Found</title><meta http-equiv='Content-type' content='text/html; charset=UTF-8' /></head>".
		"<body>".
		  "<h1>Not Found</h1>".
		  "<p>The requested URL /utils/ajax.php was not found on this server.</p>\n".
		  "<div style='visibility:hidden' status='SUCCESS' {$this->ms_context}count='{$this->mn_count}' msg='{$this->ms_msg}'></div>\n".
		  ( $this->ms_ajax ? "<script type='text/javascript'>parent.Profile.uploadPic('{$this->ms_ajax}');</script>\n" : '').
		"</body>".
	      "</html>";
    }
}

$a = new CUpload();
$a->main();

?>
