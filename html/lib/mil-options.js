/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Options =
{
	validate : function()
	{
		var f;
		if ( ! (f = $('myform')) )
			return true;

		Validate.reset('blog');

		var n_finder		= CheckBox.getValBool('n_finder'		),// o_finder		= $('o_finder'		).value == 'N',
			n_longtitles	= CheckBox.getValBool('n_longtitles'	),// o_longtitles	= $('o_longtitles'	).value == 'Y',
			n_bestonwed		= CheckBox.getValBool('n_bestonwed'		),// o_bestonwed	= $('o_bestonwed'	).value == 'Y',
			n_linksonwed	= CheckBox.getValBool('n_linksonwed'	),// o_linksonwed	= $('o_linksonwed'	).value == 'Y',
			n_showrejected	= CheckBox.getValBool('n_showrejected'	),	 o_showrejected	= $('o_showrejected').value == '1',
			n_showreplies	= CheckBox.getValBool('n_showreplies'	),// o_showreplies	= $('o_showreplies'	).value == 'N',
			n_searchbig		= CheckBox.getValBool('n_searchbig'		),// o_searchbig	= $('o_searchbig'	).value == 'Y',
			n_myregion		= DropDown.getSelValue('n_myregion'		),	 o_myregion		= $('o_myregion'	).value,
			n_mymedia		= DropDown.getSelValue('n_mymedia'		),	 o_mymedia		= $('o_mymedia'		).value,
			n_noisearch		=!CheckBox.getValBool('n_noisearch'		),	 o_noisearch	= $('o_noisearch'	).value == '1',
			n_expert		= CheckBox.getValBool('n_expert'		),	 o_expert		= $('o_expert'		).value == '1',
			n_flipexcl		= CheckBox.getValBool('n_flipexcl'		),	 o_flipexcl		= $('o_flipexcl'	).value == '1',
			n_pins			= CheckBox.getValBool('n_pins'			),	 o_pins			= $('o_pins'		).value == '1',
			n_reset_more	= CheckBox.getValBool('n_reset_more'	),	 o_more			= $('o_more'		).value,
			n_allowreply	= CheckBox.getValBool('n_allowreply'	),	 o_allowreply	= $('o_allowreply'	).value == 'Y', /* 'N' */
			n_reset_pinned	= CheckBox.getValBool('n_reset_pinned'	),
			b = false, c = {b_changed:false}, u = '/?tab=options', s;

		if ( n_finder		) Cookie.set('finder'	  ,'N'); else Cookie.del('finder'	  );
		if ( n_longtitles   ) Cookie.set('longtitles' ,'Y'); else Cookie.del('longtitles' );
		if ( n_bestonwed    ) Cookie.set('bestonwed'  ,'Y'); else Cookie.del('bestonwed'  );
		if ( n_linksonwed   ) Cookie.set('linksonwed' ,'Y'); else Cookie.del('linksonwed' );
		if ( n_showreplies  ) Cookie.set('showreplies','Y'); else Cookie.del('showreplies');
		if ( n_searchbig    ) Cookie.set('search_big' ,'Y'); else Cookie.del('search_big' );

		if ( n_showrejected != o_showrejected )
		{
			s = '1|'+($('o_statsshow'  ).value ? '1|' : '0|')+
					 ($('o_statsgroup' ).value ? '1|' : '0|')+
					 ($('o_statslist'  ).value ? '1|' : '0|')+
					 ($('o_statspaid'  ).value ? '1|' : '0|')+ (n_showrejected ? '1|' : '0|')+
					 ($('o_vidlast'	   ).value ? '1|' : '0|')+
					 ($('o_vidcategory').value ? '1|' : '0|');
			Cookie.set('home',s.substr(0,s.length-1));
		}

		if ( n_myregion != o_myregion || n_mymedia  != o_mymedia  || n_noisearch != o_noisearch ||
			 n_expert   != o_expert   || n_flipexcl != o_flipexcl || n_pins      != o_pins      || n_reset_more )
		{
			s = (n_myregion				? 'myregion_'+n_myregion+'*'	:'')+
				(n_mymedia				? 'mymedia_' +n_mymedia +'*'	:'')+
				(n_noisearch			? 'noisearch_1*'				:'')+
				(n_expert				? 'expert_1*'					:'')+
				(n_flipexcl				? 'flipexcl_1*'					:'')+
				(n_pins					? 'pins_1*'						:'')+
				( n_flipexcl   && $('o_incmine').value	? 'incmine_1*'	:'')+
				(!n_reset_more && o_more == '1'			? 'more_1*'		:'');
			Cookie.set('search',s.substr(0,s.length-1));
		}

		if ( ! n_pins && o_pins )
		{
			CheckBox.setVal('n_reset_pinned',true);
			n_reset_pinned = 1;
		}
		c.b_changed = n_reset_pinned || n_allowreply != o_allowreply;

		if ( Url.validate('n_blog','http://',c,120,1,'Blog URL',0) !== false )
		{
			if ( c.b_changed )
			{
				if ( n_reset_pinned ) Cookie.del('pinned');
				f.method = 'post';
				f.action = u;
				f.submit();
			}
			else
			{
				location.href = u;
			}
		}
	}
};

/* --------------------------------------------------------------------- */

