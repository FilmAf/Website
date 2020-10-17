<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CWidgetOptions extends CWidget
{
	function validateDataSubmission(&$wnd)
	{
		$b_reset_pinned	= dvdaf3_getvalue('n_reset_pinned'	,DVDAF3_POST|DVDAF3_BOOLEAN);
		$n_allowreply	= dvdaf3_getvalue('n_allowreply'	,DVDAF3_POST|DVDAF3_BOOLEAN);
		$o_allowreply	= dvdaf3_getvalue('o_allowreply'	,DVDAF3_POST|DVDAF3_BOOLEAN);
		$n_blog			= dvdaf3_getvalue('n_blog'			,DVDAF3_POST);
		$o_blog			= dvdaf3_getvalue('o_blog'			,DVDAF3_POST);

		if ( $b_reset_pinned )
		{
			CSql::query_and_free("UPDATE dvdaf_user SET pinned = '-' WHERE user_id = '{$this->ms_user_id}'",0,__FILE__,__LINE__);
		}
		if ( $n_allowreply != $o_allowreply || $n_blog != $o_blog )
		{
			$n_allowreply = $n_allowreply ? 'Y' : 'N';
			if ( strtolower(substr($n_blog,0,7)) != 'http://' || strlen($n_blog) < 10 ) $n_blog = '-';

			$ss = "UPDATE dvdaf_user_3 SET blog = '{$n_blog}', microblog_reply_ind = '{$n_allowreply}' WHERE user_id = '{$this->ms_user_id}'";
			if ( ! CSql::query_and_free($ss,0,__FILE__,__LINE__) )
			{
				$ss = "INSERT INTO dvdaf_user_3 (user_id, blog, microblog_reply_ind) VALUES ('{$this->ms_user_id}', '{$n_blog}', '{$n_allowreply}')";
				CSql::query_and_free($ss,CSql_IGNORE_ERROR,__FILE__,__LINE__);
			}
		}
	}

	function draw(&$wnd)
	{
		CWidget::drawHeader("Options", "<a class='wga' href='javascript:void(Options.validate())'>Save Changes</a>", '');

		$s_finder		= dvdaf3_getvalue('finder'		,DVDAF3_COOKIE);	//	'N' or empty	disable auto image pop up
		$s_longtitles	= dvdaf3_getvalue('longtitles'	,DVDAF3_COOKIE);	//	'Y' or empty	show expanded titles
		$s_bestonwed	= dvdaf3_getvalue('bestonwed'	,DVDAF3_COOKIE);	//	'Y' or empty	show best prices in one's owned folder
		$s_linksonwed	= dvdaf3_getvalue('linksonwed'	,DVDAF3_COOKIE);	//	'Y' or empty	hide text link navigation for one's own collection
		$s_home			= dvdaf3_getvalue('home'		,DVDAF3_COOKIE);
		$a_home			= explode('|',$s_home);
		$n_home			= count($a_home);
		$s_statsshow	= '';
		$s_statsgroup	= '';
		$s_statslist	= '';
		$s_statspaid	= '';
		$s_showrejected	= '';
		$s_vidlast		= '';
		$s_vidcategory	= '';
		if ( $n_home > 1 && $a_home[0] == '1' )
		{
			if ( $n_home > 2 ) $s_statsshow		= $a_home[1];
			if ( $n_home > 3 ) $s_statsgroup	= $a_home[2];
			if ( $n_home > 4 ) $s_statslist		= $a_home[3];
			if ( $n_home > 5 ) $s_statspaid		= $a_home[4];
			if ( $n_home > 6 ) $s_showrejected	= $a_home[5];
			if ( $n_home > 7 ) $s_vidlast		= $a_home[6];
			if ( $n_home > 8 ) $s_vidcategory	= $a_home[7];
		}
		$s_showreplies	= dvdaf3_getvalue('showreplies'	,DVDAF3_COOKIE);	//	'Y' or empty	Show microblog replies
		$s_pinned		= dvdaf3_getvalue('pinned'		,DVDAF3_COOKIE);	//	rgn_1_us		pinned search values
		$s_searchbig	= dvdaf3_getvalue('search_big'	,DVDAF3_COOKIE);	//	'Y' or empty	show big picture instead of thumbnails in the iterative search

		$s_search		= dvdaf3_getvalue('search'		,DVDAF3_COOKIE);	//	myregion_us*mymedia_all*pins_1*expert_1*flipexcl_1'
		$a_search		= explode('*',$s_search);							//	Search options: 'noisearch','more','incmine','myregion','mymedia','save','expert','flipexcl','pins'
		$n_search		= count($a_search);
		$s_myregion		= '';
		$s_mymedia		= '';
		$s_noisearch	= '';
		$s_expert		= '';
		$s_flipexcl		= '';
		$s_pins			= '';
		$s_incmine		= '';
		$s_more			= '';
		for ( $i = 0 ; $i < $n_search ; $i++ )
		{
			$a_opt = explode('_',$a_search[$i]);
			if ( count($a_opt) >= 2 )
			{
				$s_opt = $a_opt[1];
				switch ( $a_opt[0] )
				{
				case 'myregion':	$s_myregion	 = $s_opt; break;	// default to 'us'
				case 'mymedia':		$s_mymedia	 = $s_opt; break;	// default to 'all'
				case 'noisearch':	$s_noisearch = $s_opt; break;	// '1' or empty
				case 'expert':		$s_expert	 = $s_opt; break;	// '1' or empty
				case 'flipexcl':	$s_flipexcl	 = $s_opt; break;	// '1' or empty
				case 'pins':		$s_pins		 = $s_opt; break;	// '1' or empty
				case 'incmine':		$s_incmine	 = $s_opt; break;	// '1' or empty
				case 'more':		$s_more		 = $s_opt; break;	// '1' or empty
				}
			}
		}

		$s_pinned_sql = '';
		$s_blog		  = '';
		$s_allowreply = '';
		$ss = "SELECT a.pinned, c.blog, c.microblog_reply_ind ".
				"FROM dvdaf_user a ".
				"LEFT JOIN dvdaf_user_3 c ON a.user_id = c.user_id ".
			   "WHERE a.user_id = '{$this->ms_user_id}'";
		if ( ($rr = CSql::query_and_fetch($ss, 0,__FILE__,__LINE__)) )
		{
			$s_pinned_sql = $rr['pinned'];
			$s_blog		  = $rr['blog'];
			$s_allowreply = $rr['microblog_reply_ind'];
		}

		$a_region_opt = array(
						array('us'		,'U.S. and Canada'					),
						array('uk'		,'U.K.'								),
						array('eu'		,'Europe and Africa'				),
						array('la'		,'Latin America'					),
						array('as'		,'Russia, China, and most of Asia'	),
						array('se'		,'Southeast Asia'					),
						array('jp'		,'Japan'							),
						array('au'		,'Australia and New Zealand'		),
						array('z'		,'Region 0'							),
						array('1'		,'Region 1'							),
						array('1,A,0'	,'Regions 1, A, and 0'				),
						array('2'		,'Region 2'							),
						array('2,B,0'	,'Regions 2, B, and 0'				),
						array('3'		,'Region 3'							),
						array('4'		,'Region 4'							),
						array('5'		,'Region 5'							),
						array('6'		,'Region 6'							),
						array('A'		,'Region A'							),
						array('B'		,'Region B'							),
						array('C'		,'Region C'							),
						array('all'		,'All regions and countries'		));

		$a_media_opt  = array(
						array('all'		,'All'								),
						array('d'		,'DVD'								),
						array('b'		,'Blu-ray'							),
						array('h,c,t'	,'HD DVD'							),
						array('a,p,o'	,'Not announced + others'			));

		echo		"<fieldset>".
					  "<legend>Interactive Search:</legend>".
					  "<table>";
		CWidgetOptions::checkbox('noisearch'	,$s_noisearch	,'','1'				,'Autocomplete and search preview (1 second delay)');
		CWidgetOptions::checkbox('searchbig'	,$s_searchbig	,'Y',''				,'Show big picture instead of thumbnails in the iterative search');
		echo		  "</table>".
					"</fieldset>".
					"<fieldset style='margin-top:20px'>".
					  "<legend>Search Options:</legend>".
					  "<table>";
		CWidgetOptions::checkbox('expert'		,$s_expert		,'1',''				,'Enable multiple search criteria, the &quot;More &gt;&gt;&quot; option');
		CWidgetOptions::checkbox('flipexcl'		,$s_flipexcl	,'1',''				,'Flip &quot;exclude mine&quot; / &quot;include mine&quot; when clicking on it');
		CWidgetOptions::checkbox('pins'			,$s_pins		,'1',''				,'Allow pinning, the &quot;sticky criteria&quot;');
		CWidgetOptions::active	('reset_more'	,$s_more							,'More &gt;&gt; is active');
		echo		  "</table>".
					"</fieldset>".
					"<fieldset style='margin-top:20px'>".
					  "<legend>Search Restrictions:</legend>".
					  "<table>";
		CWidgetOptions::select  ('myregion'		,$s_myregion	,'us',$a_region_opt	,'Region');
		CWidgetOptions::select  ('mymedia'		,$s_mymedia		,'all',$a_media_opt ,'Media');
		CWidgetOptions::active	('reset_pinned'	,$s_pinned || $s_pinned_sql != '-'	,'You currently have a pinned criteria restricting your search results');
		echo		  "</table>".
					"</fieldset>".
					"<fieldset style='margin-top:20px'>".
					  "<legend>Blog:</legend>".
					  "<table>";
		CWidgetOptions::edit    ('blog'			,$s_blog		,'-',40,120			,'Blog URL');
		echo		  "</table>".
					"</fieldset>".
					"<fieldset style='margin-top:20px'>".
					  "<legend>Microblog / Updates:</legend>".
					  "<table>";
		CWidgetOptions::checkbox('showrejected'	,$s_showrejected,'1',''				,'Show friend invitations you previously declined');
		CWidgetOptions::checkbox('showreplies'	,$s_showreplies	,'Y',''				,'Show microblog/updates replies');
		CWidgetOptions::checkbox('allowreply'	,$s_allowreply	,'Y','N'			,'Allow friends to post replies in microblog');
		echo		  "</table>".
					"</fieldset>".
					"<fieldset style='margin-top:20px'>".
					  "<legend>Helpers:</legend>".
					  "<table>";
		CWidgetOptions::checkbox('finder'		,$s_finder		,'N',''				,'Disable Edition Finder');
		echo		  "</table>".
					"</fieldset>".
					"<fieldset style='margin-top:20px'>".
					  "<legend>Listings:</legend>".
					  "<table>";
		CWidgetOptions::checkbox('longtitles'	,$s_longtitles	,'Y',''				,'Show expanded titles');
		CWidgetOptions::checkbox('bestonwed'	,$s_bestonwed	,'Y',''				,'Show &quot;best prices&quot; in one&#39;s owned folder');
		CWidgetOptions::checkbox('linksonwed'	,$s_linksonwed	,'Y',''				,'Hide text link navigation for one&#39;s own collection');
		echo		  "</table>".
					"</fieldset>".
					"<input type='hidden' id='o_more' value='{$s_more}' />".
					"<input type='hidden' id='o_incmine' value='{$s_incmine}' />".
					"<input type='hidden' id='o_statsshow' value='{$s_statsshow}' />".
					"<input type='hidden' id='o_statsgroup' value='{$s_statsgroup}' />".
					"<input type='hidden' id='o_statslist' value='{$s_statslist}' />".
					"<input type='hidden' id='o_statspaid' value='{$s_statspaid}' />".
					"<input type='hidden' id='o_vidlast' value='{$s_vidlast}' />".
					"<input type='hidden' id='o_vidcategory' value='{$s_vidcategory}' />";
	}

	function checkbox($s_id, $s_val, $s_on, $s_off, $s_desc)
	{
		echo  "<tr><td class='opt_tbl' colspan='2'>".
				"<input id='n_{$s_id}' name='n_{$s_id}' type='checkbox' ".($s_val == $s_on ? "checked='checked' " : '')."/>".
				"<input id='o_{$s_id}' name='o_{$s_id}' type='hidden' value='{$s_val}' />".
				" {$s_desc}".
			  "</td></tr>";
	}

	function edit($s_id, $s_val, $s_empty, $n_size, $n_maxlength, $s_desc)
	{
		if ( $s_val == $s_empty ) $s_val = '';
		echo  "<tr><td class='opt_tbl' colspan='2'>".
				"{$s_desc} ".
				"<input id='n_{$s_id}' name='n_{$s_id}' type='text' size='{$n_size}' maxlength='{$n_maxlength}' value='{$s_val}' />".
				"<input id='o_{$s_id}' name='o_{$s_id}' type='hidden' value='{$s_val}' />".
			  "</td></tr>";
	}

	function select($s_id, $s_val, $s_default, &$a_opt, $s_desc)
	{
		if ( $s_val == '' ) $s_val = $s_default;

		$s_str = "<select id='n_{$s_id}' name='n_{$s_id}'>";
		for ( $i = 0 ; $i < count($a_opt) ; $i++ )
			$s_str .= "<option value='{$a_opt[$i][0]}'".($s_val == $a_opt[$i][0] ? " selected='selected'" : '').">{$a_opt[$i][1]}</option>";
		$s_str .= "</select>";

		echo  "<tr><td class='opt_tbl'>{$s_desc}</td><td class='opt_tbl'>{$s_str}".
				"<input id='o_{$s_id}' name='o_{$s_id}' type='hidden' value='{$s_val}' />".
			  "</td></tr>";
	}

	function active($s_id, $b_enabled, $s_desc)
	{
		if ( $b_enabled )
		{
			echo  "<tr><td class='opt_tbl' colspan='2'>".
					"{$s_desc} -- <span class='highkey'>Reset it</span> ".
					"<input id='n_{$s_id}' name='n_{$s_id}' type='checkbox' />".
				  "</td></tr>";
		}
	}
}

?>
