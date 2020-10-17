/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var PersonEdit =
{
	setup : function()
	{
		var e;

		if ( (e = $('myform')) )
		{
			Img.attach();
			Calendar.setup({inputField:'n_c_date_of_birth',clearInput:'x_c_date_of_birth',ifFormat:'%Y-%m-%d',button:'h_c_date_of_birth',singleClick:true,step:1});
			Calendar.setup({inputField:'n_c_date_of_death',clearInput:'x_c_date_of_death',ifFormat:'%Y-%m-%d',button:'h_c_date_of_death',singleClick:true,step:1});
			Menus.setup();
			Context.attach('h_c_country_birth'	,'g_c_country_birth','menu-birth');
			Context.attach('search_c_alias_of'	,false,'menu-person');
			Context.attach('search_c_father_id'	,false,'menu-person');
			Context.attach('search_c_mother_id'	,false,'menu-person');

			$('h_c_given_name').onclick		= function() {Person.search('n_c_surname','Person surname')};
			$('b_c_imdb_id').onclick		= Imdb.testPerson;
			$('b1_c_imdb_id').onclick		= function() {Imdb.searchPerson('n_c_surname','Surname')};
			$('b_c_official_site').onclick	= Url.test;
			$('b_c_wikipedia').onclick		= Wiki.test;
			$('b1_c_wikipedia').onclick		= function() {Wiki.search('n_c_surname','Surname')};
			if ( Filmaf.objId )
				$('b_showhist').onclick		= function() {ObjEdit.showHist('person')};
			else
				$('b_showhist').disabled = true;

			e = $('has_alias');
			e.checked  = $('n_c_alias_of').value ? true: false;
			e.onclick = function() {PersonEdit.onChangeHasAlias(this)};
			PersonEdit.onChangeHasAlias(e);

			if ( (e = $('mod')) && e.value == 1 )
			{
				e = $('n_zcproposer_notes');
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
			d0	= new Date(),
			d1  = {},
			d2  = {},
			b	= true,
			mod	= false,
			eid	= 0,
			noa = $('has_alias').checked,
			ysa = ! noa,
			e, f, x, y;

		if (   (f = $('person_edit_id')) ) eid = Edit.getInt(f);
		if (   (f = $('mod'			  )) ) mod = f.value == 1;
		if ( ! (f = $('myform'		  )) ) return true;

		Validate.reset('n_zcupdate_justify,'	+
					   'n_zcproposer_notes,'	+
					   'n_c_surname,'			+
					   'n_c_given_name,'		+
					   'n_c_imdb_id,'			+
					   'n_c_official_site,'		+
					   'n_c_wikipedia,'			+
					   'n_c_alias_of,'			+
					   'n_c_father_id,'			+
					   'n_c_mother_id,'			+
					   'n_c_country_birth,'		+
					   'n_c_state_birth,'		+
					   'n_c_city_birth,'		+
					   'n_c_date_of_birth,'		+
					   'n_c_date_of_death'		);

		if ( Str.validate				('n_c_surname'										,c,   0,   100,1,'Surname'					 ,0) !== false )
		if ( Str.validate				('n_c_given_name'									,c,   0,   100,1,'Given name'				 ,0) !== false )
		if ( Imdb.validate				('n_c_imdb_id'										,c,            1,'Imdb link'				 ,0) !== false )
		if ( Url.validate				('n_c_official_site','http://'						,c,        255,1,'Official Site'			 ,0) !== false )
		if ( Url.validate				('n_c_wikipedia'	,'http://en.wikipedia.org/wiki/',c,        255,1,'Wikipedia link'			 ,0) !== false )
		if ( ysa || Dec.validate		('n_c_alias_of'										,c,   1,999999,0,'Alias'					 ,0) !== false ) // do not validate if alias is not checked
		if ( noa || Dec.validate		('n_c_father_id'									,c,   0,999999,1,'Father'					 ,0) !== false ) // do not validate if alias is checked
		if ( noa || Dec.validate		('n_c_mother_id'									,c,   0,999999,1,'Mother'					 ,0) !== false ) // do not validate if alias is checked
		if ( noa || Dec.validate		('n_c_country_birth'								,c,   0, 30000,1,'Country of birth'			 ,0) !== false ) // chosen from a list, no need to validate domain
		if ( noa || Str.validate		('n_c_state_birth'									,c,   0,    32,1,'State or province of birth',0) !== false )
		if ( noa || Str.validate		('n_c_city_birth'									,c,   0,    64,1,'City of birth'			 ,0) !== false )
		if ( noa || DateTime.validate	('n_c_date_of_birth',d1								,c,1800,  2030,1,'Date of birth'			 ,0) !== false )
		if ( noa || DateTime.validate	('n_c_date_of_death',d2								,c,1890,  2030,1,'Date of death'			 ,0) !== false )
		{
			d0 = (d0.getFullYear() * 100 + d0.getMonth()) * 100 + d0.getDate();
			d1 = (d1.year          * 100 + d1.month     ) * 100 + d1.day;
			d2 = (d2.year          * 100 + d2.month     ) * 100 + d2.day;

			if ( b && ! (b = Str.trim($('n_c_surname').value) != '' || Str.trim($('n_c_given_name').value) != '') )
				Validate.warn($('n_c_surname'), true, true, 'Please specify at least a Surname or a Given Name.', false);
			if ( b && d1 && ! (b = d1 < d0) )
				Validate.warn($('n_c_country_birth'), true, true, 'Sorry, the date of birth must be earlier than today.', false);
			if ( b && d2 && ! (b = d2 < d0) )
				Validate.warn($('n_c_date_of_death'), true, true, 'Sorry, the date of death must be earlier than today.', false);
			if ( b && d1 && d2 && ! (b = d1 < d2) )
				Validate.warn($('n_c_date_of_death'), true, true, 'Sorry, the date of birth must be earlier than the date of death.', false);
			if ( ! c.b_changed )
			{
				c.b_changed = ($('n_c_surname_first_ind').checked ? 'Y' : 'N') != $('o_c_surname_first_ind').value;
				if ( c.b_changed ) c.b_undo = true;
			}

			if  ( c.b_changed || eid > 0 )
			{
				if ( b ) b = Str.validate('n_zcproposer_notes'  ,c,   0,1000,1   ,'Proposer notes'      ,0   ) !== false;
				if ( b ) b = Str.validate('n_zcupdate_justify'  ,c,   0, 200,mod ,'Update justification',!mod) !== false;
			}

			if ( ! c.b_changed && ysa && $('n_c_alias_of').value != '' )
			{
				c.b_changed = true;
				c.b_undo = true;
			}

			if ( c.b_changed && c.b_undo && ysa )
				$('n_c_alias_of').value = '';

			if ( b ) Validate.save(b_alert_no_change, n_nav, c);
		}
		// must return false even if we navigate from a link otherwise the link will overwrite the submit call or the location.href setting
		return false;
	},
/*
n_c_surname_first_ind = [on]
o_c_surname_first_ind = [Y]
has_alias = [on]
*/

	onChangeHasAlias : function(e)
	{
		$('sp_alias').style.visibility			= e.checked ? 'visible' : 'hidden';
		$('tr_father').style.visibility			=
		$('tr_mother').style.visibility			=
		$('tr_country_birth').style.visibility	=
		$('tr_state_birth').style.visibility	=
		$('tr_city_birth').style.visibility		=
		$('tr_date_of_birth').style.visibility	=
		$('tr_date_of_death').style.visibility	= e.checked ? 'hidden' : 'visible';
	},

	setSearchVal : function(mode, trg, s, n)
	{
		switch( mode )
		{
		case 'birth':
			$('g_c_country_birth').value = s;
			$('n_c_country_birth').value = n;
			Undo.change($('n_c_country_birth'));
			break;
		default: return;
		}

		Context.close();
	},

	onPopup : function(el)		{ return ObjEdit.onPopup(this,el); },
	onClick : function(action)	{ return ObjEdit.onClick(this,action); }
};

/* --------------------------------------------------------------------- */

