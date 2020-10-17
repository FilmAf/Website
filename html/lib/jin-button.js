/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Button =
{
	enable : function(e,b)
	{
		if ( (e = $s(e)) )
		{
			if ( b )
			{
				e.disabled = false;
				e.style.color = '';
			}
			else
			{
				e.disabled = true;
				e.style.color = '#999999';
			}
		}
	}
};

/* --------------------------------------------------------------------- */

