/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var = Stack
{
	trace : function() // stackTrace
	{
		var c = arguments ? (arguments.callee ? arguments.callee.caller : false) : false,
		s = '';

		while ( c )
		{
			s += Stack.traceFunction(c) + "\n";
			c  = c.caller;
		}
		return s;
	},

	traceFunction : function(f) // traceFunction
	{
		var x = f.arguments,
		s = '',
		n, d;

		if ( f.name )
		{
			n = f.name;
		}
		else
		{
			d = f.toString();
			n = d.substring(d.indexOf('function') + 8, d.indexOf('('));
			if ( n ) n = Str.trim(n);
		}

		if ( ! n || n == 'anonymous' )
		{
			x = new RegExp("^function anonymous ?\(\) ?{?"); // Netscape 7.2 seems to get confused if this pattern shows up directly in the replace function
			return 'anonymous: ' + Str.trim(Str.trim(d).
									   replace(/[\n\r\t ]+/g, ' ').
									   replace(x,'').
									   replace(/}$/,''));
		}

		if ( x )
			for ( i = 0  ;  i < x.length  ;  i++ )
				s += (x[i]  == '[object]' || typeof(x[i]) != 'string' ? x[i] : "'" + x[i] + "'") + ', ';

		return n + '(' + s.substring(0, s.length - 2) + ')';
	}
};

/* --------------------------------------------------------------------- */

