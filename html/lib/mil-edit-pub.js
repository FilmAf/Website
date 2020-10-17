/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var PubEdit =
{
	setup : function()
	{
		var e;

		if ( (e = $('myform')) )
		{
			Img.attach();
			Menus.setup();

			$('h_u_pub_name').onclick		= function() {Pub.search('n_u_pub_name','Publisher name')};
			$('b_u_official_site').onclick	= Url.test;
			$('b_u_wikipedia').onclick		= Wiki.test;
			$('b1_u_wikipedia').onclick		= function() {Wiki.search('n_u_pub_name','Publisher name')};
			if ( Filmaf.objId )
				$('b_showhist').onclick		= function() {ObjEdit.showHist('pub')};
			else
				$('b_showhist').disabled = true;

			if ( (e = $('mod')) && e.value == 1 )
			{
				e = $('n_zuproposer_notes');
				e.setAttribute('class','ronly');
				e.setAttribute('className','ronly');
				e.setAttribute('readOnly','readonly');
			}
		}
	},

	validate : function(b_alert_no_change, n_nav)
	{
		// mod - used to check if an update justification is needed
		// act - used to define the action type (new, edit, del_sub)
		var c   = {b_changed:false},
			b	= true,
			mod	= false,
			eid	= 0,
			e, f;

		if (   (f = $('pub_edit_id'	)) ) eid = Edit.getInt(f);
		if (   (f = $('mod'			)) ) mod = f.value == 1;
		if ( ! (f = $('myform'		)) ) return true;

		Validate.reset('n_zuupdate_justify,'	+
					   'n_zuproposer_notes,'	+
					   'n_u_pub_name,'			+
					   'n_u_official_site,'		+
					   'n_u_wikipedia'			);

		if ( Str.validate		('n_u_pub_name'										,c,   0,   100,0,'Publisher name'	,0) !== false )
		if ( Url.validate		('n_u_official_site','http://'						,c,        255,1,'Official site'	,0) !== false )
		if ( Url.validate		('n_u_wikipedia'	,'http://en.wikipedia.org/wiki/',c,        255,1,'Wikipedia link'	,0) !== false )
		{
			if  ( c.b_changed || eid > 0 )
			{
				if ( b ) b = Str.validate('n_zuproposer_notes'  ,c,   0,1000,1   ,'Proposer notes'      ,0   ) !== false;
				if ( b ) b = Str.validate('n_zuupdate_justify'  ,c,   0, 200,mod ,'Update justification',!mod) !== false;
			}
			if ( b ) Validate.save(b_alert_no_change, n_nav, c);
		}
		// must return false even if we navigate from a link otherwise the link will overwrite the submit call or the location.href setting
		return false;
	},

	onPopup : function(el)		{ return ObjEdit.onPopup(this,el); },
	onClick : function(action)	{ return ObjEdit.onClick(this,action); }
};

/* --------------------------------------------------------------------- */

