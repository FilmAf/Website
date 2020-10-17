/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Asin =
{
	validate : function(s_field, o_changed, n_min_len, n_max_len, b_empty, s_name) // f_val_asin
	{
		var b = Str.validate(s_field, o_changed, n_min_len, n_max_len, b_empty, s_name) !== false,
			b_unlikelly = false,
			b_10_digits, e;

		if ( b && (e = $(s_field)) )
		{
			if ( (s = Str.trim(e.value).toLowerCase()) )
			{
				if ( (b_10_digits = s.length == 10) )
				{
					if ( s.substr(0,1) == "b" )
					{
						if ( (b_unlikelly = s.match(/^b0[0-9a-z]{8}$/) == null) )
						{
							if ( s.substr(1,1) == "o" ) s = s.substr(0,1) + '0' + s.substr(2,8);
							if ( s.substr(2,1) == "o" ) s = s.substr(0,2) + '0' + s.substr(3,7);
							if ( s.substr(3,1) == "o" ) s = s.substr(0,3) + '0' + s.substr(4,6);
							if ( s.substr(4,1) == "o" ) s = s.substr(0,4) + '0' + s.substr(5,5);
							b_unlikelly = s.match(/^b0[0-9a-z]{8}$/) == null;
						}
					}
					else
					{
						b_unlikelly = s.match(/^[016][0-9]{8}[0-9x]$/) == null;
					}
				}
				if ( ! b_10_digits || b_unlikelly )
				{
					if ( ! Validate.warn(e, true, true,
										( b_unlikelly
										  ? s_name +', is probably not valid.\n\n'
										  : ''
										)+
										( ! b_10_digits
										  ? s_name +' is not 10 characters in length.\n\n'
										  : ''
										)+
										'An ASIN is how Amazon.com and their international sites\n'+
										'identify a title. Most ASINs start with "B" followed by\n'+
										'1 or more zeros; a few are all numbers and some end with\n'+
										'an "X", but they are all 10 characters in length.\n\n'+
										'You entered "'+s+'".',
										true) )
						return false;
				}
				e.value = s = s.toUpperCase();
				return s;
			}
		}
		return b;
	},

	test : function() // testAmz
	{
		var a = $('n_a_asin'),
			b = $('n_a_amz_country');

		if ( a && b )
		{
			a = Str.trim(a.value);
			b = DropDown.getSelValue(b);
			if ( a && b )
			{
				switch ( b )
				{
				case 'C': a = 'http://www.amazon.ca/exec/obidos/ASIN/'    + a + '/dvdaficiona05-20'; break;
				case 'K': a = 'http://www.amazon.co.uk/exec/obidos/ASIN/' + a + '/dvdaficionado-21'; break;
				case 'F': a = 'http://www.amazon.fr/exec/obidos/ASIN/'    + a + '/dvdaficiona01-21'; break;
				case 'D': a = 'http://www.amazon.de/exec/obidos/ASIN/'    + a + '/dvdaficiona0e-21'; break;
				case 'I': a = 'http://www.amazon.it/exec/obidos/ASIN/'    + a + '/dvaf-21';			 break;
				case 'E': a = 'http://www.amazon.es/exec/obidos/ASIN/'    + a + '/dvaf0b-21';		 break;
				case 'J': a = 'http://www.amazon.co.jp/exec/obidos/ASIN/' + a + '/dvdaficionado-22'; break;
				default : a = 'http://www.amazon.com/exec/obidos/ASIN/'   + a + '/dvdaficionado';	 break;
				}
				Win.openStd(a, 'amz');
				return;
			}
		}
		alert('Enter an Amazon ASIN and the corresponding country and we will test it.');
	}
};

/* --------------------------------------------------------------------- */

