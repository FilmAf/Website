/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Arrays =
{
	indexFromVal : function(a,v) // indexFromValue
	{
		for ( var i = 0 ; i < a.length ; i++ )
		if ( a[i] == v )
			return i;
		return -1;
	}
};

/* --------------------------------------------------------------------- */

