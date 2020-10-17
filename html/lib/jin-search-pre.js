/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var SearchMenuPrep =
{
	onPopup : function(el) // onMenuPopup
	{
		if ( ! this.id ) return;
		var i = this.menu.items,
			t = this.menu.target;

		switch ( this.id )
		{
		case 'help_dvd':
		case 'help_dir':
		case 'help_pub':
		case 'help_cnt':
		case 'help_gen':
		case 'help_rel':
		case 'help_lan':
		case 'help_src':
		case 'help_pic':
		case 'help_med':
			SearchMenuPrep._helpSearch(t,this.id.substr(5));
			break;
		case 'help_rdv':
		case 'help_rfm':
			SearchMenuPrep._helpStars(t);
			break;
		case 'help_gno':
			SearchMenuPrep._helpGenre(i,t);
			break;
		case 'explain_pop':
			Explain.show(t.id,true);
			break;
		case 'search_options':
			SearchMenuPrep._loadOptions();
			break;
		}
	},

	_loadOptions : function()
	{
		var c = Cookie.get('search'), e = $s('flipexclpins'), b = Filmaf.userCollection == '';

		DropDown.selectFromVal('myregion', Cookie.extractFromStr(c,'myregion','us'));
		DropDown.selectFromVal('mymedia' , Cookie.extractFromStr(c,'mymedia','all'));
		CheckBox.setVal       ('isearch' ,!Cookie.extractFromStr(c,'noisearch'    ));
		CheckBox.setVal       ('save'    , Cookie.extractFromStr(c,'save'         ));
		CheckBox.setVal       ('expert'  , Cookie.extractFromStr(c,'expert'       ));
		CheckBox.setVal       ('flipexcl', b ? 'N' : Cookie.extractFromStr(c,'flipexcl'));
		CheckBox.setVal       ('pins'    , b ? 'N' : Cookie.extractFromStr(c,'pins'    ));
		if (e) e.style.visibility = b ? 'hidden' : 'visible';
		iSearch.close();
	},

	saveOptions : function()
	{
		if ( ! CheckBox.getVal_1('flipexcl') && ! Search.isXmyPined() )
			Cookie.amend('search','incmine','');
		Cookie.amend('search','myregion' , DropDown.getSelValue('myregion'));
		Cookie.amend('search','mymedia'  , DropDown.getSelValue('mymedia' ));
		Cookie.amend('search','noisearch',!CheckBox.getVal_1   ('isearch' ) ? '1' : '');
		Cookie.amend('search','save'     , CheckBox.getVal_1   ('save'    ) ? '1' : '');
		Cookie.amend('search','expert'   , CheckBox.getVal_1   ('expert'  ) ? '1' : '');
		Cookie.amend('search','flipexcl' , CheckBox.getVal_1   ('flipexcl') ? '1' : '');
		Cookie.amend('search','pins'     , CheckBox.getVal_1   ('pins'    ) ? '1' : '');
		Context.close();
		Search.loadPreferences();
		if ( !Search._pins || !Search.isRegPined() ) DropDown.selectFromVal('optR',Search._myregion);
		if ( !Search._pins || !Search.isMedPined() ) DropDown.selectFromVal('optM',Search._mymedia);
		Search.paint(1,0);
		if ( !CheckBox.getVal_1('pins') )
			 Ajax.asynch('pin', 'Ajax.__ignore', '?pin=cls');
	},

	restoreDefaults : function()
	{
		DropDown.selectFromVal('myregion', 'us' );
		DropDown.selectFromVal('mymedia' , 'all');
		CheckBox.setVal       ('isearch' , 1);
		CheckBox.setVal       ('save'    , 0);
		CheckBox.setVal       ('expert'  , 0);
		CheckBox.setVal       ('flipexcl', 0);
		CheckBox.setVal       ('pins'    , 0);
	},

	_helpSearch : function(t,f)
	{
		Filmaf.inputLine = t.id.substr(2,1);
		var g = $('mform_'+f);
		if ( g && g.ropt && g.ropt[1] ) g.ropt[0].checked = ! (g.ropt[1].checked = Search.appending[f]);
	},

	_helpStars : function(t)
	{
		Filmaf.inputLine = t.id;
	},

	_helpGenre : function(i,t)
	{
		Filmaf.inputLine = t.id;
		switch ( t.id )
		{
		case 'b_b_genre':
		case 'b_a_genre':
			i.hm_gno_99999.label		     = t.id == 'b_b_genre' ? 'Use Default Genre' : 'Unspecified Genre';
			i.hm_gno_99999.labelTD.innerHTML = "<a><strong>" + i.hm_gno_99999.label + '</strong></a>';
			break;
		}
	}
};

/* --------------------------------------------------------------------- */

