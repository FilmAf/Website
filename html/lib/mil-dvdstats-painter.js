/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdStatsPainter =
{
	_stat_fi_title : function(o,t)
	{
		o.str = "<div id='stat_div'><div id='stat_tit'>"+t+"</div><table id='stat_tbl'>";
	},

	_stat_fn_code : function(i,o,x,w1,w2)
	{
		if ( x[0] == '-' )
		{
			x[0] = 'Unknown';
			x[1] = 'missing';
		}
		x[0] = "<a href='"+Filmaf.baseDomain+"/search.html?"+w1+"="+x[1]+"&where="+Filmaf.viewCollection+"&init_form="+w2+"_"+x[1]+"'>"+x[0]+"</a>";
		DvdStatsPainter._add_line(o,x,1,"<td class='stat_rank'>"+i+"</td>",'-');
	},

	_stat_fn_text : function(i,o,x,w)
	{
		var d  = encodeURIComponent(Str.trim(x[0].replace(/[\s]+/g,' ').replace(/[\x00-\x1F\x7F\x27]/g, '').replace(/[\x22#$()\*\+:;\?@\[\\\]\x5E_\x60{}~]/g, ' ').replace(/^(the|an|a)[\s,]/i,''))),
			w2 = ('str0_'+w+'_' + d).replace(/(\%20)+/g,'+').replace(/(\%25)+/g,'%2525'),
			w1 = (w+'=' + d).replace(/(\%20)+/g,'+');

			x[0] = "<a href='"+Filmaf.baseDomain+"/search.html?"+w1+"&where="+Filmaf.viewCollection+"&init_form="+w2+"'>"+x[0]+"</a>";
			DvdStatsPainter._add_line(o,x,0,"<td class='stat_rank'>"+i+'</td>','-');
	},

	_add_line : function(o,x,k,prev,unkn)
	{
		var a = '';
		if ( o.list && o.paid )	a = "<td class='stat_val'>"+Dbl.toUsd(x[k+3])+"</td><td class='stat_val'>"+Dbl.toUsd(x[k+2])+'</td>'; else
		if ( o.list )			a = "<td class='stat_val'>"+Dbl.toUsd(x[k+2])+'</td>'; else
		if ( o.paid )			a = "<td class='stat_val'>"+Dbl.toUsd(x[k+2])+'</td>';
		o.str += '<tr>'+prev+'<td>'+(Str.trim(x[0]) == unkn ? 'Unknown' : x[0])+' <span>('+x[k+1]+')</span>'+a+'</td></tr>';
	},

	_stat_ff : function(o,e)
	{
		e.innerHTML = o.str + '</table></div>';
	},

	// ===================================================================================================

	___dir : function()
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o, 'Director' + (o.cnt > 50 ? ' (top 50)' : '')); o.j = 0; },
			function(i,o,x) { if ( ++(o.j) <= 50 ) DvdStatsPainter._stat_fn_text(i,o,x,'dir'); },
			DvdStatsPainter._stat_ff);
	},

	___pub : function()
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o, 'Publisher' + (o.cnt > 50 ? ' (top 50)' : '')); o.j = 0; },
			function(i,o,x) { if ( ++(o.j) <= 50 ) DvdStatsPainter._stat_fn_text(i,o,x,'pub'); },
			DvdStatsPainter._stat_ff);
	},

	___lang : function()
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o,'Original language'); },
			function(i,o,x) { DvdStatsPainter._stat_fn_code(i,o,x,'lang','str0_lang'); },
			DvdStatsPainter._stat_ff);
	},

	___pubcnt : function()
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o,'DVD country'); },
			function(i,o,x) { DvdStatsPainter._stat_fn_code(i,o,x,'pubct','str0_pubct'); },
			DvdStatsPainter._stat_ff);
	},

	___region : function()
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o,'Region'); },
			function(i,o,x) { DvdStatsPainter._stat_fn_code(i,o,x,'rgn','rgn'); },
			DvdStatsPainter._stat_ff);
	},

	___genre : function() // TO DO
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o,'Genre'); },
			function(i,o,x)
			{
				if ( x[0] == '-' )
					x[0] = "<a href='"+Filmaf.baseDomain+"/search.html?genre=missing&where="+Filmaf.viewCollection+"&init_form=str0_genre_missing'>Unknown</a>";
				else
					if ( x[1] != '' )
						x[0] = "<a href='"+Filmaf.baseDomain+"/search.html?genre="+x[1]+"&where="+Filmaf.viewCollection+"&init_form=str0_genre_"+x[1]+"'>"+x[0]+"</a>";
				DvdStatsPainter._add_line(o,x,2,"<td class='stat_rank'>"+i+"</td>",'-');
			},
			DvdStatsPainter._stat_ff);
	},

	___format : function()
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o,'Format'); },
			function(i,o,x) { DvdStatsPainter._stat_fn_code(i,o,x,'med','med'); },
			DvdStatsPainter._stat_ff);
	},

	___decade : function()
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o,'Film decade'); },
			function(i,o,x)
			{
				var m = Dec.parse(x[0]),
					n = m + 10;
				if ( m )
					x[0] = "<a href='"+Filmaf.baseDomain+"/search.html?year=.ge."+m+".lt."+n+"&where="+Filmaf.viewCollection+"&init_form=str0_year_%3E%3D+"+m+"+%3C"+n+"'>"+x[0]+"</a>";
				else
					x[0] = "<a href='"+Filmaf.baseDomain+"/search.html?year=missing&where="+Filmaf.viewCollection+"&init_form=str0_year_missing'>Unknown</a>";
				DvdStatsPainter._add_line(o,x,0,'','0');
			},
			DvdStatsPainter._stat_ff);
	},
	
	___dvd_rel : function()
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o,'DVD release year'); },
			function(i,o,x)
			{
				var m = Dec.parse(x[0]);
				if ( m )
					x[0] = "<a href='"+Filmaf.baseDomain+"/search.html?reldt="+m+"&where="+Filmaf.viewCollection+"&init_form=str0_reldt_"+m+"'>"+x[0]+"</a>";
				else
					x[0] = "<a href='"+Filmaf.baseDomain+"/search.html?reldt=missing&where="+Filmaf.viewCollection+"&init_form=str0_reldt_missing'>Unknown</a>";
				DvdStatsPainter._add_line(o,x,0,'','-');
			},
			DvdStatsPainter._stat_ff);
	},
	
	// ===================================================================================================

	___onwed_yy : function(){DvdStatsPainter.___sta_s('Owned since'			,'-');},
	___onwed_mm : function(){DvdStatsPainter.___sta_s('Owned since'			,'-');},
	___watch_yy : function(){DvdStatsPainter.___sta_s('Last watched'		,'-');},
	___watch_mm : function(){DvdStatsPainter.___sta_s('Last watched'		,'-');},
	___retailer : function(){DvdStatsPainter.___stats('Retailer'			,'-');},
	___stats : function(t,u)
	{
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o,t); },
			function(i,o,x) { DvdStatsPainter._add_line(o,x,0,"<td class='stat_rank'>"+i+'</td>', u); },
			DvdStatsPainter._stat_ff);
	},
	___sta_s : function(t,u)
   {
		DvdStatsPainter._paint(
			function(o) { DvdStatsPainter._stat_fi_title(o,t); },
			function(i,o,x) { DvdStatsPainter._add_line(o,x,0,'', u); },
			DvdStatsPainter._stat_ff);
   },

	__folder : function()
	{
		function fi(o)
		{
			DvdStatsPainter._stat_fi_title(o,'Collection Folders');
			o.z = [];
			o.j = 0;
		}
		function fn(i,o,x)
		{
			var fol = x[0],
				cnt = (o.grp == 'list' || o.grp == 'paid') ? Dbl.parse(x[1]) : Dec.parse(x[1]),
				pos = fol.indexOf('/'),
				sub = pos >= 0 ? fol.substr(pos + 1) : '',
				fol = pos >= 0 ? fol.substr(0, pos) : fol;

			if ( o.z.length <= o.j )
				o.z[o.j] = {name:fol,url:'/'+fol,tot:0,count:0,sub:[]};
			else
				if ( o.z[o.j].name != fol )
				o.z[++(o.j)] = {name:fol,url:'/'+fol,tot:0,count:0,sub:[]};

			if ( fol == 'trash-can' )
				o.z[o.j].note = 'trash can items are<br />automatically purged<br />after 7 days.';

			if ( sub )
				o.z[o.j].sub.push({name:sub,url:'/'+fol+'/'+sub,tot:cnt,count:cnt,sub:null});
			else
				o.z[o.j].count = cnt;
			o.z[o.j].tot += cnt;
		}
		function ff(o,e)
		{
			o.str += DvdStatsPainter._formatHierarchy(o,o.z);
			DvdStatsPainter._stat_ff(o,e);
		}
		DvdStatsPainter._paint(fi,fn,ff);
	},

	__genre : function()
	{
		function fi(o)
		{
			DvdStatsPainter._stat_fi_title(o,'Collection Genres');
			o.z = [];
			o.j = 0;
			o.m = 0;
			o.url_1 = Filmaf.baseDomain + '/search.html?genre=';
			o.url_2 = '&where='+Filmaf.viewCollection+'&folder=';
			o.url_3 = '&init_form=str0_genre_';
			o.url_4 = '*where_0_'+Filmaf.viewCollection+'*mode_more';
		}
		function fn(i,o,x)
		{
			if ( x.length >= 3 )
			{
				var fol      = x[0],
					n_gen    = x[1],
					cnt      = (o.grp == 'list' || o.grp == 'paid') ? Dbl.parse(x[2]) : Dec.parse(x[2]),
					s_gen    = Encode.genre(n_gen),
					s_gen_lo = Encode.genre_lower(Math.round(Math.floor(n_gen/1000)*1000+999)),
					s_gen_hi = Encode.genre_lower(n_gen),
					pos      = s_gen.indexOf('/'),
					s_subgen = pos >= 0 ? Str.trim(s_gen.substr(pos + 1)) : '',
					s_gen    = pos >= 0 ? Str.trim(s_gen.substr(0, pos)) : s_gen;

				if ( o.z.length <= o.j )
				{
					o.z[o.j] = {name:fol,url:'',tot:0,count:0,sub:[]};
					o.m = 0;
				}
				else
				{
					if ( o.z[o.j].name != fol )
					{
						o.z[++(o.j)] = {name:fol,url:'',tot:0,count:0,sub:[]};
						o.m = 0;
					}
				}

				if ( o.z[o.j].sub.length <= o.m )
					o.z[o.j].sub[o.m] = {name:s_gen,url:o.url_1+s_gen_lo+o.url_2+fol+o.url_3+s_gen_lo+o.url_4,tot:0,count:0,sub:[]};
				else
					if ( o.z[o.j].sub[o.m].name != s_gen )
						o.z[o.j].sub[++(o.m)] = {name:s_gen,url:o.url_1+s_gen_lo+o.url_2+fol+o.url_3+s_gen_lo+o.url_4,tot:0,count:0,sub:[]};

				if ( s_subgen )
					o.z[o.j].sub[o.m].sub.push({name:s_subgen,url:o.url_1+s_gen_hi+o.url_2+fol+o.url_3+s_gen_hi+o.url_4,tot:cnt,count:cnt,sub:null});
				else
					o.z[o.j].sub[o.m].count = cnt;

				o.z[o.j].tot += cnt;
				o.z[o.j].sub[o.m].tot += cnt;
			}
		}
		function ff(o,e)
		{
			o.str += DvdStatsPainter._formatHierarchy(o,o.z);
			DvdStatsPainter._stat_ff(o,e);
		}
		DvdStatsPainter._paint(fi,fn,ff);
	},

	_parseHeader : function(o)
	{
		if ( Ajax.ready() )
		{
			var e;

			if ( Ajax.getParms(o) )
			{
				o.usr  = Ajax.statusTxt(o.line1,'user'	);
				o.list = Ajax.statusTxt(o.line1,'list'	);
				o.paid = Ajax.statusTxt(o.line1,'paid'	);
				o.show = Ajax.statusTxt(o.line1,'show'	);
				o.grp  = Ajax.statusTxt(o.line1,'group' );
				o.trg  = Ajax.statusTxt(o.line1,'target');
				o.cnt  = Ajax.statusTxt(o.line1,'count'	);
			}
			else
			{
				o.usr = o.list = o.paid = o.show = o.grp = o.trg = o.cnt = '';
			}
			o.err  = (o.sta == 'SUCCESS' && o.show && o.trg ) ? '' : ( o.msg ? o.msg : 'ERROR');
			o.list = o.list == '1';
			o.paid = o.paid == '1';

			if ( o.trg && (e = $(o.trg)) )
				if ( o.err )
					e.innerHTML = o.err;
				else
					return true;
		}
		return false;
	},

	_paint : function(fi,fn,ff)
	{
		if ( Ajax.ready() )
		{
			var o = {}, e, i, x, b = DvdStatsPainter._parseHeader(o);

			if ( o && (e = $(o.trg)) )
			{
				if ( b )
				{
					if ( fi ) fi(o);
					for ( i = 2 ; i < o.length && o.lines[i].substr(0,6) != '</div>' ; i++ )
						if ( (x = o.lines[i].split('\t')) && x.length >= 2 )
							fn(i - 1, o, x);
					if ( ff ) ff(o,e);
				}
				else
				{
					e.innerHTML = '';
				}
			}
		}
	},

	_formatHierarchy : function(o, cat1) // echo_films_tab
	{
		function fmt(dbl,v)
		{
			return dbl ? Dbl.toUsd(v) : v;
		}

		var s, cat2, cat3, cat4, tot1, tot2, tot3, tot4, row1, row2, row3, row4, i1, i2, i3, i4, cnt,
			dbl = o.grp == 'list' || o.grp == 'paid';

		if ( (tot1 = cat1.length) )
		{
			s = '';
			for ( i1 = 0 ; i1 < tot1 ; i1++ )
			{
				row1 = cat1[i1];
				cnt  = (row1.count == 0 || row1.count == row1.tot) ? fmt(dbl,row1.tot) : fmt(dbl,row1.count)+"/"+fmt(dbl,row1.tot);
				s   += ( i1					 ? "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>" : '')+
					   ( (dbl || row1.count) ? "<tr><td><a href='"+row1.url+"'>"+row1.name+"</a><span> ("+cnt+")</span></td>"
											 : "<tr><td>"+row1.name+"<span> ("+cnt+")</span></td>");

				cat2 = row1.sub;
				tot2 = cat2 ? cat2.length : 0;
				for ( i2 = 0 ; i2 < tot2 ; i2++ )
				{
					row2 = cat2[i2];
					cnt  = (row2.count == 0 || row2.count == row2.tot) ? fmt(dbl,row2.tot) : fmt(dbl,row2.count)+"/"+fmt(dbl,row2.tot);
					s   += ( i2					 ? "<tr><td>&nbsp;</td>" : '' )+
						   ( (dbl || row2.count) ? "<td><a href='"+row2.url+"'>"+row2.name+"</a><span> ("+cnt+")</span></td>"
												 : "<td>"+row2.name+"<span> ("+cnt+")</span></td>");

					cat3 = row2.sub;
					tot3 = cat3 ? cat3.length : 0;
					for ( i3 = 0 ; i3 < tot3 ; i3++ )
					{
						row3 = cat3[i3];
						cnt  = (row3.count == 0 || row3.count == row3.tot) ? fmt(dbl,row3.tot) : fmt(dbl,row3.count)+"/"+fmt(dbl,row3.tot);
						s   += ( i3					 ? "<tr><td>&nbsp;</td><td>&nbsp;</td>" : '')+
							   ( (dbl || row3.count) ? "<td><a href='"+row3.url+"'>"+row3.name+"</a><span> ("+cnt+")</span></td>"
													 : "<td>"+row3.name+"<span> ("+cnt+")</span></td>");

						cat4 = row3.sub;
						tot4 = cat4 ? cat4.length : 0;
						for ( i4 = 0 ; i4 < tot4 ; i4++ )
						{
							row4 = cat4[i4];
							cnt  = (row4.count == 0 || row4.count == row4.tot) ? fmt(dbl,row4.tot) : fmt(dbl,row4.count)+"/"+fmt(dbl,row4['tot']);
							s   += ( i4					 ? "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>" : '')+
								   ( (dbl || row4.count) ? "<td><a href='"+row4.url+"'>"+row4.name+"</a><span> ("+cnt+")</span></td>"
														 : "<td>"+row4.name+"<span> ("+cnt+")</span></td>")+
								   "</tr>";
						}
						if ( ! tot4 ) s += "<td>"+(row3.note ? row3.note : "&nbsp;")+"</td></tr>";
					}
					if ( ! tot3 ) s += "<td>"+(row2.note ? row2.note : "&nbsp;")+"</td><td>&nbsp;</td></tr>";
				}
				if ( ! tot2 ) s += "<td>"+(row1.note ? row1.note : "&nbsp;")+"</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			}
		}
		else
		{
/*
   s = "<div style='text-align:center;margin:24px 8px 16px 8px'>"+
				  "<div style='width:240px;margin-bottom:12px'>"+
					"Sorry, nothing to show."+
				  "</div>"+
				  "<div style='width:240px'>"+
					o.msg+
				  "</div>"+
				"</div>";
				*/
		}

		return s;
	}
};

/* --------------------------------------------------------------------- */

