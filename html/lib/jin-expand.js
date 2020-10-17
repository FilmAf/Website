/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Expand =
{
	more : function(s_field, n_max_fields, n_size, n_max_length, s_class, n_helper, n_tester, n_force_br) // f_expand
	{
		var e, f, i, n, bo = false, bz = false, bg = false, vo=[], vn=[], vz=[], vg=[], vsi=[], vsn=[], x='', sf, sn, so, sh, sz, si, sb, se = '', a, b, c, bf = false;

		if ( (f = $('s_'+s_field)) )
		{
			for ( i = 1 ; ; i++ )
			{
				if ( ! (e = $('n_'+s_field+'_'+i)) )
				break;

				if ( e.tagName.toLowerCase() == 'select' )
				{
					vsi[i] = e.selectedIndex;
					vsn[i] = e.id;
				}
				else
				{
					vsi[i] = 0;
					vsn[i] = '';
				}

				vn[i] = e.value.replace(/\x27/g, '&#39;');
				vo[i] = '';
				vz[i] = '';
				vg[i] = '';
				if ( (e = $('o_'+s_field+'_'+i)) ) { vo[i] = e.value.replace(/\x27/g, '&#39;'); bo = true; }
				if ( (e = $('z_'+s_field+'_'+i)) ) { vz[i] = e.value.replace(/\x27/g, '&#39;'); bz = true; }
				if ( (e = $('g_'+s_field+'_'+i)) ) { vg[i] = e.value.replace(/\x27/g, '&#39;'); bg = true; }
				if ( i == 1 )
				{
					a = f.innerHTML;
					b = a.toLowerCase().indexOf('<select');
					c = a.toLowerCase().indexOf('</select>');
					if ( b > 0 && c > b )
					{
						se = a.substring(b,c+9).
							   replace(/id=[\\\x22\x27]*[a-z_0-9]*[\\\x22\x27]*/i, "id='X_X_X'").
							   replace(/name=[\\\x22\x27]*[a-z_0-9]*[\\\x22\x27]*/i, "name='X_X_X'").
							   replace(/\x20selected(=[\\\x22\x27]*[a-z_0-9]*[\\\x22\x27]*)*/i,'').
							   replace(/<option\x20value=[\\\x22\x27]{2,4}><\/option>/i,'').
							   replace(/<\/select>/i,"<option value='' selected='selected'></option></select>");
					}
				}
			}

			if ( bg )
				n_helper = true;

			n = i;
			vn[i] = vo[i] = vz[i] = vg[i] = '';

			for ( i = 1 ; i <= n ; i++ )
			{
				if ( se )
					sn = se.replace(/X_X_X/ig, "n_"+s_field+"_"+i);
				else
					if ( bg )
						sn = "<input id='g_"+s_field+"_"+i+"' class='ronly' type='text' size='"+n_size+"' maxlength='"+n_max_length+"' readonly='readonly' value='"+vg[i]+"' />"+
							 "<input id='n_"+s_field+"_"+i+"' name='n_"+s_field+"_"+i+"' type='hidden' value='"+vn[i]+"' />";
					else
						sn = "<input id='n_"+s_field+"_"+i+"' name='n_"+s_field+"_"+i+ (s_class ? "' class='"+s_class : '') +"' type='text' size='"+n_size+"' maxlength='"+n_max_length+"' value='"+vn[i]+"' />";

				so = sh = sz = sb = '';

				sf = "<img src='http://dv1.us/nb/n-"+(i+1)+".gif' height='13' width='15' alt='"+(i+1)+"' />";
				if ( bo            ) so = "<input id='o_"+s_field+"_"+i+"' name='o_"+s_field+"_"+i+"' type='hidden' value='"+vo[i]+"' />";
				if ( n_helper == 1 ) sh = "<input id='h_"+s_field+"_"+i+"' type='button' value='&hellip;' class='but_ctx' />";
				if ( bz            ) sz = "<input id='z_"+s_field+"_"+i+"' type='hidden' value='"+vz[i]+"' />";
									 si =  "<img id='zi_"+s_field+"_"+i+"' src='http://dv1.us/d1/1.gif' align='top' />";
				if ( n_tester == 1 ) sb = "<input id='b_"+s_field+"_"+i+"' type='button' value='&raquo;' class='but_tst' />";

				bf = n_force_br && (i + 1) % n_force_br == 0;
				x += "<span style='white-space:nowrap'>"+ sf + sn + so + sh + sz + si + sb + "</span>" + (bf ? "<br />" : " ");
			}

			if ( n + 1 < n_max_fields )
			{
				x  = x.substr(0,x.length - (bf ? 13 : 8))+
					 "<input type='button' value='&rsaquo;' class='but_ctx' onclick='Expand.more("+'"'+s_field+'"'+","+n_max_fields+","+n_size+","+n_max_length+","+'"'+s_class+'"'+","+n_helper+","+n_tester+","+n_force_br+")' />"+
					 "</span>";
			}

			f.innerHTML = x;

			for ( i = 1 ; i < vsn.length ; i++ )
				if ( (e = $(vsn[i])) && vsi[i] >= 0 )
					e.selectedIndex = vsi[i];

			Expand.attach(s_field);

			if ( bz ) Img.attach();
		}
	},

	attach: function(s) // attachExpanded_ attachExpanded_DvdEdit
	{
		switch ( s )
		{
		case 'f_orig_language':
		case 'f_language_add':
		case 'a_orig_language':
		case 'a_language_add':
			Expand._attach('h_',s,'menu-language-no','g_' ,       0);
			break;
		case 'f_genre':
			Expand._attach('h_',s,'menu-genre-no'	,'g_' ,       0);
			break;
		case 'a_country':
			Expand._attach('h_',s,'menu-country-no'	,'g_' ,       0);
			break;
		case 'a_region_mask':
			Expand._attach('h_',s,'menu-region-no'	,false,       0);
			break;
		case 'a_director':
			Expand._attach('h_',s,'menu-director'	,false,'search');
			break;
		case 'a_publisher':
			Expand._attach('h_',s,'menu-publisher'	,false,'search');
			break;
		case 'a_imdb_id':
			Expand._attach('b_',s,Imdb.test			,false,       0);
			break;
		case 'a_upc':
			Expand._attach('b_',s,Upc.test			,false,       0);
			break;
		}
	},

	_attach : function(b,s_field,m,c,text)
	{
		var e,i,x;

		for ( i = 0 ;  ; i++ )
		{
			if ( ! (e = $(b+s_field+'_'+i)) )
				break;

			switch ( typeof(m) )
			{
			case 'string':
				x = c ? c + s_field + '_' + i : false;
				Context.attach(e,x,m);
				break;
			case 'function': e.onclick = m;
				break;
			}

			if ( text ) e.value = text;
		}
	}
};

/* --------------------------------------------------------------------- */

