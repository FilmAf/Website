/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var UpcImport =
{
	showSort : function()
	{
		var e = $('d_sort');

		if ( CheckBox.getValBool('cb_sort') )
		{
			e.innerHTML =
				"<div style='padding:10px 0 4px 0'>"+
				  "Use this option to keep the DVDs in your collection on a specific order, like that same as in your shelves, etc. Use the prefix to segregate it into subsets."+
				"</div>"+
				"<table>"+
				  "<tr><td>Prefix:</td><td><input type='text' id='n_prefix' name='n_prefix' size='5' maxlength='5' value='"+$('o_prefix').value+"' style='text-align:right' /></td></tr>"+
				  "<tr><td>Next value:</td><td><input type='text' id='n_next' name='n_next' size='5' maxlength='5' value='"+$('o_next').value+"' style='text-align:right' /></td></tr>"+
				  "<tr><td>Increment:</td><td><input type='text' id='n_inc' name='n_inc' size='5' maxlength='5' value='"+$('o_inc').value+"' style='text-align:right' /></td></tr>"+
				"</table>";
		}
		else
		{
			if ( $('n_prefix') )
			{
				$('o_prefix').value	= $('n_prefix').value;
				$('o_next').value	= $('n_next').value;
				$('o_inc').value	= $('n_inc').value;
			}
			e.innerHTML = '';
		}

		$('n_upc').focus();
	},

	setup : function()
	{
		UpcImport.showSort();
	},

	doImport : function(e)
	{
		if ( e )
		{
			e = Dec.parse(e.id.substr(2));
			$('n_dvd_id').value = e;
			$('f_list').submit();
		}
	}
};

/* --------------------------------------------------------------------- */

