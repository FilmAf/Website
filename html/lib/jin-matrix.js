/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Matrix =
{
	tb_sort : function(s)
	{
	},

	tb_newrow : function(e, row_pre, sel_pre, sel_post, add, tds)
	{
		var	t = e.rows.length,
			z = t + add,
			i, j, k, r, x;

		for ( i = t+1 ; i <= z ; i++ )
		{
			r = e.insertRow(i-1);
			r.id = row_pre + i;
			for ( j = 0 ; j < tds.length ; j++ )
			{
				x = r.insertCell(j);
				x.innerHTML = tds[j].replace(/\[_\]/g,i);
			}
		}
		for ( k = 1 ; k <= z ; k++ )
		{
			r = $(sel_pre + k + sel_post);
			j = k <= t ? DropDown.getSelValue(r) : k;
			if ( r.length )
				r.remove(r.length-1);
			for ( i = r.length + 1 ; i <= z ; i++ )
				DropDown.addOption(r,i,i);
			DropDown.addOption(r,'del','del');
			DropDown.selectFromVal(r,j);
		}
	},

	tb_import : function(s)
	{
	}
};

/* --------------------------------------------------------------------- */

