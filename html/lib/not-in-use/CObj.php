<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CObj
{
	function propagateChanges($n_obj_id) {}
	function snapHistory($n_obj_id, $n_version_id) {}
	function validate($b_new, $b_real_table, $bmod) {}

	function __construct()
	{
		$this->ms_tbl_obj				= false;
		$this->ms_tbl_direct			= false;
		$this->ms_tbl_hist				= false;
		$this->ms_tbl_pic				= false;
		$this->ms_tbl_pic_hist			= false;
		$this->ms_tbl_submit			= false;
		$this->ms_tbl_video				= false;
		//--------------------------------------------------
		// obj
		//--------------------------------------------------
		//$this->ms_obj_name			= 'DVD';
		//$this->ms_key					= 'dvd_id';
		$this->ms_version				= 'version_id';
		$this->ms_subversion			= '0';
		$this->ms_mod_flags				= 'mod_flags';
		//$this->ms_created_tm			= 'dvd_created_tm';
		//$this->ms_updated_tm			= 'dvd_updated_tm';
		//$this->ms_updated_by			= 'dvd_updated_by';
		$this->ms_justify				= 'last_justify';
		//$this->ms_verified_tm			= 'dvd_verified_tm';
		//$this->ms_verified_by			= 'dvd_verified_by';
		$this->ms_verified_version		= 'verified_version';
		//$this->ms_gen_by_edit_id		= 'dvd_edit_id';
		$this->ms_pic_status			= false;
		$this->ms_pic_name				= 'pic_name';
		$this->ms_pic_count				= 'pic_count';
		//--------------------------------------------------
		// obj_hist
		//--------------------------------------------------
		//$this->ms_id_merged				= 'dvd_id_merged';
		//--------------------------------------------------
		// obj_submit
		//--------------------------------------------------
		$this->ms_creation_seed			= 'creation_seed';
		$this->ma_key_base				= array();
		$this->ma_attributes			= array();
		$this->ms_direct_user			= 'user_id';
		$this->ms_direct_updated_tm		= 'updated_tm';
		//$this->ms_direct_seq_num		= 'seq_num';
		//$this->ms_direct_edit_obj		= 'edit_title';
		//$this->ms_direct_new_obj		= 'new_title';
		$this->ms_direct_new_picture	= 'new_picture';
		$this->ms_direct_rejected		= 'rejected';
		$this->ms_piclink_pic_id		= 'pic_id';
        $this->ms_piclink_sort			= 'sort_order';
        $this->ms_piclink_created_tm	= 'link_created_tm';
        $this->ms_piclink_created_by	= 'link_created_by';
		$this->ms_pichist_pic_id		= 'pic_id';
		$this->ms_pichist_seq_num		= 'seq_num';
		$this->ms_pichist_sort			= 'sort_order';
		$this->ms_pichist_created_tm	= 'link_created_tm';
		$this->ms_pichist_created_by	= 'link_created_by';
		$this->ms_pichist_deleted_tm	= 'link_deleted_tm';
		$this->ms_pichist_deleted_by	= 'link_deleted_by';
		//$this->ms_submit_id			= 'edit_id';
		$this->ms_submit_request_cd		= 'request_cd';
		$this->ms_submit_disposition_cd	= 'disposition_cd';
		$this->ms_submit_proposer_id	= 'proposer_id';
		$this->ms_submit_proposer_notes	= 'proposer_notes';
		$this->ms_submit_proposed_tm	= 'proposed_tm';
		$this->ms_submit_updated_tm		= 'updated_tm';
		$this->ms_submit_reviewer_id	= 'reviewer_id';
		$this->ms_submit_reviewer_notes	= 'reviewer_notes';
		$this->ms_submit_reviewed_tm	= 'reviewed_tm';
		$this->ms_submit_hist_version_id= 'hist_version_id';
		$this->ms_submit_update_justify	= 'update_justify';
		$this->ms_submit_creation_seed	= 'creation_seed';
		$this->ms_video_caption			= 'caption';
		$this->ms_video_created_tm		= 'video_created_tm';
		$this->ms_video_updated_tm		= 'video_updated_tm';
		$this->ms_video_updated_by		= 'video_updated_by';
		$this->ms_video_verified_by		= 'video_verified_by';
	}

	function initLabel()
	{
		$n_tbl = dvdaf_gettableid($this->ms_tbl_obj_);
		$n_tot = count($this->ma_attributes);

		if ( $n_tbl >= 0 )
		{
			for ( $i = 0 ; $i < $n_tot ; $i++ )
			{
				$this->ma_attributes[$i]['id'] = dvdaf_getfieldid($n_tbl, $this->ma_attributes[$i]['col']);
				if ( isset($this->ma_attributes[$i]['parh']) && $this->ma_attributes[$i]['parh'] )
					$this->ma_attributes[$i]['parh_id'] = dvdaf_getfieldid($n_tbl, $this->ma_attributes[$i]['parh']);
			}
		}
	}
}

?>
