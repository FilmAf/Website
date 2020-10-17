/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Title =
{
	_test : function(w)
	{
		var e, s;

		if ( (e = $('n_a_dvd_title' )) )
		{
			s = Url.fromStr(Edit.getSel(e));
			if ( s )
			{
				switch ( w )
				{
				case 'filmaf': s = Filmaf.baseDomain+'/search.html?has='+s+'&init_form=str0_has_'+s; break;
				case 'imdb':   s = 'https://www.imdb.com/find?q='+s; break;
				case 'amz':    s = 'http://www.amazon.com/exec/obidos/external-search?mode=dvd&tag=dvdaficionado&keyword='+s; break;
				default: return;
				}
				Win.openStd(s, w);
			}
			else
			{
				switch ( w )
				{
				case 'filmaf': s = 'Film Aficionado';	break;
				case 'imdb':   s = 'Imdb';				break;
				case 'amz':    s = 'Amazon';			break;
				default: return;
				}
				alert('Please highlight the text you would like to\n'+
					  'search for at '+s+'.  Thanks!');
			}
		}
	},

	seekInFilmaf : function() { Title._test('filmaf'); },
	seekInImdb   : function() { Title._test('imdb'  ); },
	seekInAmz    : function() { Title._test('amz'   ); }
};

/* --------------------------------------------------------------------- */

