/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Folders =
{
	setup : function()
	{
		var i, j, e, f, a = 1, b = 0;

		if ( (e = $('myform')) )
		{
			Img.attach();

			for ( i = 0 ; (e = $('n_folder_'+i)) ; i++ )
			{
				e.onchange   = function() { this.value = this.value.toLowerCase().replace(/\x20+/g,'-').replace(/[^a-z0-9-]/g,''); Undo.change(this); };
				e.onkeypress = Folders._folderFilter;
			}
		}

		for ( i = a ; (e = $('td_'+i)) ; i++ )
			if ( $('skip_'+i) )
				a = Folders._initSort(a, i - 1);

		if ( a < i ) Folders._initSort(a, i - 1);
	},

	_initSort : function(a, b)
	{
		var i, n, e, s;

		for ( n = a ; n <= b ; n++ )
		{
			if ( (e = $('td_'+n)) )
			{
				s = "<select id='se_"+n+"' onchange='Folders._resort(this)'>";
				for ( i = a ; i <= b ; i++ )
					s += "<option value='" + i + (i == n ? "' selected='selected" : '') + "'>" + i + "</option>";
				s += "<option value='del'>DEL</option></select>";

				e.innerHTML = s;
			}
		}

		return b + 2;
	},

	resetLevels : function()
	{
		var i, e, y = false;
		
		for ( i = 0 ; !y && (e = $('n_folder_'+i)) ; i++ )
			if ( ! $('skip_'+i) && (e = $('n_levl_'+i)) )
				y = e.value != 1;

		if ( y )
		{
			if ( (y = confirm("Do you wish to reset your folder levels?\n\nOK=Yes - Cancel=No")) )
				for ( i = 0 ; (e = $('n_folder_'+i)) ; i++ )
					if ( ! $('skip_'+i) && (e = $('n_levl_'+i)) && e.value != 1 )
					{
						e.value = 1;
						if ( (e = $('ni_folder_'+i)) ) e.width = 1 + 1 * 30;
					}
		}
		else
		{
			alert("Nothing to do. All your folders are at the same level.");
		}

		return false;
	},

	_resort : function(e)
	{
		var a, b, i, e, f, n_levl, o_levl, ni_folder, n_folder, o_folder, z_folder, o_full, o_seq, o_sort, o_pub, z_pub, n_pub;

		n_levl = o_levl = ni_folder = n_folder = o_folder = z_folder = o_full = o_seq = o_sort = o_pub = z_pub = '';
		n_pub  = false;

		a = Dec.parse(e.id.substr(3));
		if ( e.value.toLowerCase() == 'del' )
		{
			if ( (e = $('n_folder_'+a)) )
			{
				e.value = '---delete-this-folder---';
				Undo.change(e);
			}
			return false;
		}

		b = Edit.getInt(e);

		if ( a != b )
		{
			for ( i = 0 ; (e = $('n_folder_'+i)) ; i++ )
				e.style.color = '';

			if ( (e = $('n_levl_'   +a)) ) n_levl    = e.value;
			if ( (e = $('o_levl_'   +a)) ) o_levl    = e.value;
			if ( (e = $('ni_folder_'+a)) ) ni_folder = e.width;
			if ( (e = $('o_folder_' +a)) ) o_folder  = e.value;
			if ( (e = $('z_folder_' +a)) ) z_folder  = e.value;
			if ( (f = $('n_folder_' +a)) ) { n_folder  = f.value; f.style.color = 'blue'; }
			if ( (e = $('o_full_'   +a)) ) o_full    = e.value;
			if ( (e = $('o_seq_'    +a)) ) o_seq     = e.value;
			if ( (e = $('o_sort_'   +a)) ) o_sort    = e.value;
			if ( (e = $('o_pub_'    +a)) ) o_pub     = e.value;
			if ( (e = $('z_pub_'    +a)) ) z_pub     = e.value;
			if ( (e = $('n_pub_'    +a)) ) n_pub     = e.checked;

			if ( b > a )
				for ( i = a ; i < b ; i++ ) Folders._moveFolder(i, i+1);
			else
				for ( i = a ; i > b ; i-- ) Folders._moveFolder(i, i-1);

			if ( (e = $('n_levl_'   +a)) ) e.value = n_levl; // set value in a
			if ( (e = $('o_levl_'   +b)) ) e.value = o_levl;
			if ( (e = $('ni_folder_'+a)) ) e.width = ni_folder; // set value in a
			if ( (e = $('o_folder_' +b)) ) e.value = o_folder;
			if ( (e = $('z_folder_' +b)) ) e.value = z_folder;
			if ( (e = $('n_folder_' +b)) ) { e.value = n_folder; e.style.color = 'red'; Undo.change(e); }
			if ( (e = $('o_full_'   +b)) ) e.value = o_full;
			if ( (e = $('o_seq_'    +b)) ) e.value = o_seq;
			if ( (e = $('o_sort_'   +b)) ) e.value = o_sort;
			if ( (e = $('o_pub_'    +b)) ) e.value = o_pub;
			if ( (e = $('z_pub_'    +b)) ) e.value = z_pub;
			if ( (e = $('n_pub_'    +b)) ) { e.checked = n_pub; Undo.change(e); }

			if ( (e = $('se_'       +a)) ) DropDown.selectFromVal(e,a);
			if ( f && f.focus) f.focus();
		}
		return false;
	},

	_moveFolder : function(a, b)
	{
		var e, f;

		if ( (e = $('n_levl_'+a)) && (f = $('n_levl_'+b)) )
		{
			if ( Edit.getInt(e) > 0 && Edit.getInt(f) > 0 )
			{
				if ( (e = $('n_levl_'   +a)) && (f = $('n_levl_'   +b)) ) e.value = f.value;
				if ( (e = $('o_levl_'   +a)) && (f = $('o_levl_'   +b)) ) e.value = f.value;
				if ( (e = $('ni_folder_'+a)) && (f = $('ni_folder_'+b)) ) e.width = f.width;
				if ( (e = $('o_folder_' +a)) && (f = $('o_folder_' +b)) ) e.value = f.value;
				if ( (e = $('z_folder_' +a)) && (f = $('z_folder_' +b)) ) e.value = f.value;
				if ( (e = $('n_folder_' +a)) && (f = $('n_folder_' +b)) ) { e.value = f.value; e.style.color = 'blue'; Undo.change(e); }
				if ( (e = $('o_full_'   +a)) && (f = $('o_full_'   +b)) ) e.value = f.value;
				if ( (e = $('o_seq_'    +a)) && (f = $('o_seq_'    +b)) ) e.value = f.value;
				if ( (e = $('o_sort_'   +a)) && (f = $('o_sort_'   +b)) ) e.value = f.value;
				if ( (e = $('o_pub_'    +a)) && (f = $('o_pub_'    +b)) ) e.value = f.value;
				if ( (e = $('z_pub_'    +a)) && (f = $('z_pub_'    +b)) ) e.value = f.value;
				if ( (e = $('n_pub_'    +a)) && (f = $('n_pub_'    +b)) ) { e.checked = f.checked; Undo.change(e); }
			}
		}
	},

	_folderFilter : function(ev)
	{
		var k, r, b = true;

		if ( window.event )
		{
			k = window.event.keyCode;
			if ( k >= 65 && k <= 90 ) window.event.keyCode = k + 97 - 65; else
			if ( k == 32            ) window.event.keyCode = 45; else
			if ( ! (k < 32 || k == 45 || (k >= 48 && k <= 57) || (k >= 97 && k <= 122)) )
			{
				window.event.returnValue = false;
				b = false;
			}
		}
		else
		{
			if ( ev )
			{
				r = 0;
				k = ev.which;
				if ( k >= 65 && k <= 90 ) r = k + 97 - 65; else
				if ( k == 32            ) r = 45; else
				if ( ! (k < 32 || k == 45 || (k >= 48 && k <= 57) || (k >= 97 && k <= 122)) )
				{
					if ( ev.preventDefault ) ev.preventDefault();
					b = false;
				}
				if ( r )
				{
					if ( ev.preventDefault ) ev.preventDefault();
					b = false;
					Edit.insertAtCursor(ev.target, String.fromCharCode(r));
				}
			}
		}
		return b;
	},

	onSpin : function(s,u)
	{
		var e, f, m, n, a = Dec.parse(s.substr(10)), b;

		switch ( s.substr(0,10) )
		{
		case 'ih_1_levl_':
			if ( u )
			{
				if ( (e = $('n_levl_'+a)) && (f = $('n_levl_'+(a-1))) )
				{
					n = Edit.getInt(e);
					m = Edit.getInt(f);
					if ( m >= n )
					{
						e.value = ++n;
						if ( (f = $('ni_folder_'+a)) ) f.width = 1 + n * 30;
						break;
					}
				}
			}
			else
			{
				if ( (e = $('n_levl_'+a)) )
				{
					n = Edit.getInt(e);
					m = (f = $('n_levl_'+(a+1))) ? Edit.getInt(f) : 1;
					if ( n > 1 && n >= m )
					{
						e.value = --n;
						if ( (f = $('ni_folder_'+a)) ) f.width = 1 + n * 30;
						break;
					}
				}
			}
			alert("Sorry, a folder and its parent must be 1 level apart.");
			break;
		}
	},

	validate : function()
	{
		var i, j, k, e, f, g, h, b=true, b_changed = false, cnt=0, n_len = 200,
		as_folder = [],
		ao_folder = [],
		as_path   = [],
		an_level  = [],
		an_beg    = [0,0,0,0,0],
		an_end    = [0,0,0,0,0];

		if ( ! (f = $('myform')) )
			return true;

		for ( i = 0 ; (e = $('n_folder_'+i)) ; i++ )
		{
			Validate.reset('n_folder_'+i);

			if ( (e = $('n_folder_'+i)) )
			{
				as_folder[i] = Str.trim(e.value).toLowerCase().replace(/\x20+/g,'-').replace(/[^a-z0-9-]/g,'');
				if ( as_folder[i] != e.value )
				{
					k = "Folders names must be composed of only lowercase letters, numbers and dashes [a-z0-9-].\n\nYou entered '"+e.value+"'. We changed it to '"+as_folder[i]+"'.";
					e.value = as_folder[i];
					return Validate.warn(e, true, true, k, false);
				}
				if ( as_folder[i] == '---delete-this-folder---' ) as_folder[i] = '';

				if ( (g = $('o_folder_'+i)) )
				{
					ao_folder[i] = g.value;
					if ( ! b_changed ) b_changed = as_folder[i] != g.value;
				}
				else
				{
					ao_folder[i] = '';
				}
				
				if (				  (h = $('n_levl_'+i))							   ) an_level[i] = Edit.getInt(h);
				if ( ! b_changed ) if ( (h				   ) && (g = $('o_levl_'  +i)) ) b_changed   = h.value != g.value;
				if ( ! b_changed ) if ( (h = $('n_pub_' +i)) && (g = $('o_pub_'   +i)) ) b_changed   = (h.checked == true) != (g.value == 'Y');
				if ( ! b_changed ) if ( (h = $('o_seq_' +i))						   ) b_changed   = Edit.getInt(h) != i;

				as_path[an_level[i]] = as_folder[i];
				if ( as_folder[i] )
				{
					for ( j = 0, k = '' ; j < an_level[i] ; j++ ) k += as_path[j] + '/';
					k += as_folder[i];
					if ( k.length > n_len )
						return Validate.warn(e, true, true, "Path length for '"+k+"' is longer than the allowed "+n_len+" characters ("+k.length+").", false);
				}
			}
		}
		cnt = i;

		i = 1;
		j = 0;
		for ( an_beg[j] = i-1 ; i < cnt && an_level[i] > 0 ; i++ ) an_end[j] = i; i++; j++; // owned
		for ( an_beg[j] = i-1 ; i < cnt && an_level[i] > 0 ; i++ ) an_end[j] = i; i++; j++; // on-order
		for ( an_beg[j] = i-1 ; i < cnt && an_level[i] > 0 ; i++ ) an_end[j] = i; i++; j++; // wish-list
		for ( an_beg[j] = i-1 ; i < cnt && an_level[i] > 0 ; i++ ) an_end[j] = i; i++; j++; // work
		for ( an_beg[j] = i-1 ; i < cnt && an_level[i] > 0 ; i++ ) an_end[j] = i;           // have-seen

		// Ensure parents have name
		// Ensure no two folders have the same name
		for ( j = 0 ; j < 5 ; j++ )
		{
			for ( i = an_beg[j] ; i <= an_end[j] ; i++ )
			{
				if ( (e = $('n_folder_'+i)) )
				{
					if ( as_folder[i] == '' )
					{
						for ( k = i + 1 ; k <= an_end[j] && an_level[k] > an_level[i] ; k++ )
							if ( as_folder[k] != '' )
								return Validate.warn(e, true, true, ao_folder[i] != '' ? 'Folder can not be deleted as it contain subfolders of its own.' : 'Parent folders must have a name.', false);
					}
					else
					{
						for ( k = i + 1 ; k <= an_end[j] && an_level[i] <= an_level[k] ; k++ )
							if ( an_level[k] == an_level[i] && an_level[k] != '' && as_folder[k] == as_folder[i] )
								return Validate.warn(e, true, true, 'Sorry, no two folders at the same level can have the same name.', false);
					}
				}
			}
		}

		// Delete old folders which have been renamed ''
		for ( i = 0 ; i < cnt ; i++ )
			if ( as_folder[i] == '' && ao_folder[i] != '' )
				if ( ! confirm("You are attempting to delete the folder currently named '"+ao_folder[i]+"'.\nThis will only succeed if you have previously emptied that folder.\n\nContinue?\n\nOK=Yes - Cancel=No") )
					return false;

		if ( b_changed )
			f.submit();
		else
			alert('No changes detected.  Nothing to save.');

		return false;
	}
};

/* --------------------------------------------------------------------- */

