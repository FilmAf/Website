/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Pub =
{
	search : function(f,n)
	{
		var s = Str.firstLine($(f).value);
		if ( s && (s = Url.fromStr(s)) )
			Win.openStd(Filmaf.baseDomain+'/pub/search?s='+s, 'pub');
		else
			alert('No text found in '+n+' to search for publisher.');
	}
};

/* --------------------------------------------------------------------- */

