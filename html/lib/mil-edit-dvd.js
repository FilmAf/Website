/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdEdit =
{
	setup : function()
	{
		var e;

		if ( (e = $('myform')) )
		{
			Img.attach();
			Calendar.setup({inputField:'n_a_dvd_rel_dd',clearInput:'x_a_dvd_rel_dd',ifFormat:'%Y-%m-%d',button:'h_a_dvd_rel_dd',singleClick:true,step:1});
			Calendar.setup({inputField:'n_a_dvd_oop_dd',clearInput:'x_a_dvd_oop_dd',ifFormat:'%Y-%m-%d',button:'h_a_dvd_oop_dd',singleClick:true,step:1});
			Calendar.setup({inputField:'n_a_film_rel_dd',clearInput:'x_a_film_rel_dd',ifFormat:'%Y-%m-%d',button:'h_a_film_rel_dd',singleClick:true,step:1});
			Menus.setup();
			Context.attach('h_a_film_rating','g_a_film_rating','menu-rating-no');
			Context.attach('h_a_genre'      ,'g_a_genre'      ,'menu-genre-no' );
			Expand.attach('a_orig_language');
			Expand.attach('a_language_add' );
			Expand.attach('a_country'      );
			Expand.attach('a_region_mask'  );
			Expand.attach('a_imdb_id'      );
			Expand.attach('a_director'     );
			Expand.attach('a_publisher'    );
			Expand.attach('a_upc'          );

			$('h_a_rel_status' ).onclick = DvdEdit._setOop;
			$('b_a_amz_country').onclick = Asin.test

//			$('b_c_imdb_id').onclick	 = Imdb.testPerson;
//			$('b1_c_imdb_id').onclick	 = function() {Imdb.searchPerson('n_c_surname','Surname')};
//			$('b_c_wikipedia').onclick	 = Wiki.test;
//			$('b1_c_wikipedia').onclick	 = function() {Wiki.search('n_c_surname','Surname')};

//			e = $('has_alias');
//			e.checked  = $('n_c_alias_of').value ? true: false;
//			e.onclick = function() {PersonEdit.onChangeHasAlias(this)};
//			PersonEdit.onChangeHasAlias(e);

			if ( (e = $('mod')) && e.value == 1 )
			{
//				e = $('n_zcproposer_notes');
//				e.setAttribute('class','ronly');
//				e.setAttribute('className','ronly');
//				e.setAttribute('readOnly','readonly');
			}
			else
			{
				$('div_upc_dash').style.visibility  = 'hidden';
			}
		}
	},

	validate : function(b_alert_no_change, n_nav)
	{
		return false;
	},

	_setOop : function()
	{
		var e = $('n_a_rel_status');
		if ( DropDown.getSelValue(e) == 'O' )
		{
			alert('The DVD release status has already\nbeen set to Out of Print.');
		}
		else
		{
			DropDown.selectFromVal(e,'O');
			Undo.change(e);
		}
	},

	setSearchVal : function(mode, trg, s, n)
	{
		switch( mode )
		{
//		case 'dvd': Edit.insertAtCursor($('n_a_dvd_title'),val); break;
		case 'dir': e = $('n'+Filmaf.inputLine.substr(1,100)); e.value = s; Undo.change(e); break;
		case 'pub': e = $('n'+Filmaf.inputLine.substr(1,100)); e.value = s; Undo.change(e); break;
		default: return;
		}
		Context.close();
	},

	removeUpcDashes : function()
	{
		var e, i;

		for ( i = 0 ; (e = $('n_a_upc_'+i)); i++ )
			e.value = e.value.replace(/-/g,'');
	},

	onPopup : function(el)		{ return ObjEdit.onPopup(this,el); },
	onClick : function(action)	{ return ObjEdit.onClick(this,action); }
};

/* --------------------------------------------------------------------- */

