/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Upc =
{
    toStr : function(s) // formatUpc
	{
		s = s.replace(/[^0-9]+/g,'');

		switch ( s.length )
		{
		case 11:
		case 12: return s.substr(0,1) +'-'+ s.substr(1,5) +'-'+ s.substr(6,5) +'-'+ s.substr(11);
		case 13: return s.substr(0,1) +'-'+ s.substr(1,6) +'-'+ s.substr(7);
		}

		return s;
	},

	validateN : function(s_field, n_cnt, o_changed, n_min_len, n_max_len, b_allow_empty, s_name)
	{
		var i, b;
		for ( i = 0, b = true  ;  b !== false && i < n_cnt  ;  i++ )
			b = Upc.validate(s_field + i, o_changed, n_min_len, n_max_len,
							 i ? 1 : b_allow_empty,
							 i ? s_name+(i+1) : Str.trim(s_name));
		return  b !== false;
	},

	validate : function(s_field, o_changed, n_min_len, n_max_len, b_empty, s_name) // f_val_upc
	{
		var b = Str.validate(s_field, o_changed, n_min_len, n_max_len, b_empty, s_name) !== false,
			r = {},
			e, s;

		if ( b && (e = $(s_field)) )
		{
			s = Str.trim(e.value);
			if ( s )
			{
				s = Upc.getErrors(e.value, r);
				Upc.getErrorMsg(s, r, e.value, false);
				if ( r.b_fatal )
					if ( ! Validate.warn(e, true, true, r.s_msg.substr(2), true) )
						return false;
					else
						return s;
				e.value = s;
				return s;
			}
		}
		return b;
	},

	getErrors : function(s,r) // validateUpc
	{
		r.b_10_digits = s.match(/^-*[0-9]{5}-*[0-9]{5}-*$/) != null;					// user forgot first and last digit
		r.b_11_digits = s.match(/^[0-9]-*[0-9]{5}-*[0-9]{5}-*$/) != null;				// user did not enter check digit
		r.b_12_digits = s.match(/^[0-9]-*[0-9]{5}-*[0-9]{5}-*[0-9]$/) != null;			// complete US UPC
		r.b_1b_digits = ! r.b_12_digits && s.match(/^-*[0-9]{6}-*[0-9]{6}$/) != null;	// international UPC without check digit
		r.b_13_digits = s.match(/^[0-9]-*[0-9]{6}-*[0-9]{6}$/) != null;					// complete international UPC
		r.b_inv_digit = false;
		s			  = s.replace(/[^0-9]+/g,'');

		if ( r.b_11_digits || r.b_1b_digits ) s += r.s_check = Upc._getCheckDigit(s);
		if ( r.b_12_digits || r.b_13_digits ) r.b_inv_digit  = Upc._isCheckDigitInvalid(s);

		return s;
	},

	_catMsg : function(r, b_fatal, s)
	{
		r.s_msg += (r.s_msg ? '\n\n' : '') + s;
		
		if ( b_fatal )
			r.b_fatal = true;
		else
			r.b_warn  = true;
	},

	getErrorMsg : function(s,r,o,b_search) // getUpcInvMsg
	{
		//    r.s_msg = 'An UPC is a 12-digit sequence next to a bar code.  It is usually represented as\n'+
		//	      '9-99999-99999-9. For international titles this is sometimes a 13-digit sequence\n'+
		//	      'as in 9-999999-999999. The dashes are optional.\n\n'+
		//	      'You entered "'+ o +'".';
		r.s_msg   = '';
		r.b_fatal = false;
		r.b_warn  = false;

		if ( r.b_10_digits ) Upc._catMsg(r,1,'UPCs have at least 12 digits.  You entered only 10. Perhaps you forgot the first and last digits.');
		if ( r.b_11_digits ) Upc._catMsg(r,0,'You did not enter the check digit. We calculated it for you: '+r.s_check+'.');
		if ( r.b_1b_digits ) Upc._catMsg(r,0,'You seem to have entered an international UPC without a check digit. We calculated it for you: '+r.s_check+'.');
		if ( r.b_inv_digit ) Upc._catMsg(r,1,'Your '+s.length+'-digit UPC has an incorrect check digit. Please check it for a typo. Sometimes ISBNs look like 10 or 13 digit UPCs, but they are not.');
		if ( s.length > 15 ) Upc._catMsg(r,1,'Your UPC has too many digits: '+s.length+ '.');

		if ( s.length < 12 && ! r.b_10_digits && ! r.b_11_digits && ! r.b_1b_digits )
		{
			if ( b_search )
				if ( s.length < 5 )
					 Upc._catMsg(r,1,'To do a partial UPC search you need to enter at least 5 digits, but no more than 9.');
				else
					 Upc._catMsg(r,0,'Note that when you enter 5 to 9 digits we will do a partial search.');
			else
				 Upc._catMsg(r,1,'UPCs have at least 12 digits. You entered only '+s.length+'.');
		}
	},

	_getCheckDigit : function(s) // getUpcCheckDigit
	{
		s = '00000000000000' + s;
		s  = s.substr(s.length - 15,15);

		var o = (Dec.parse(s.substr( 0,1))+
				 Dec.parse(s.substr( 2,1))+
				 Dec.parse(s.substr( 4,1))+
				 Dec.parse(s.substr( 6,1))+
				 Dec.parse(s.substr( 8,1))+
				 Dec.parse(s.substr(10,1))+
				 Dec.parse(s.substr(12,1))+
				 Dec.parse(s.substr(14,1)))*3;
		var e =  Dec.parse(s.substr( 1,1))+
				 Dec.parse(s.substr( 3,1))+
				 Dec.parse(s.substr( 5,1))+
				 Dec.parse(s.substr( 7,1))+
				 Dec.parse(s.substr( 9,1))+
				 Dec.parse(s.substr(11,1))+
				 Dec.parse(s.substr(13,1));

		return '' + ((10-Math.floor((o + e) % 10)) % 10);
	},

	_isCheckDigitInvalid : function(s) // invalidUpc
	{
		var c = '';
		if ( s.length >= 12 && s.length <= 15 )
		{
			c = Upc._getCheckDigit(s.substr(0,s.length - 1));
			return s.substr(s.length - 1, 1) != c ? c : false;
		}
		return true;
	},

	test : function(ev) // testUpc
	{
		ev || (ev = window.event);
		var s = ev.currentTarget || ev.srcElement,
			r = {}, 
			e, o;

		if ( ev && s && (e = $('n'+s.id.substr(1))) )
		{
			if ( (o = Str.trim(e.value)) )
			{
				s = Upc.getErrors(e.value, r);
				Upc.getErrorMsg(s, r, e.value, false);
				if ( r.b_fatal )
				{
					alert(r.s_msg);
				}
				else
				{
					e.value = Upc.toStr(s);
					if ( ! r.b_warn								 ) r.s_msg  = 'This looks like a valid UPC.' + (r.s_msg ? '\n\n'+r.s_msg : '');
					if ( e.id.substr(e.id.length - 2, 2) != '_0' ) r.s_msg += '\n\nPlease note that only the UPC in the first position is used for finding online prices.';
					alert(r.s_msg);
				}
			}
			else
			{
				alert('Enter an UPC and we will try to validate it.');
			}
		}
	}
};

/* --------------------------------------------------------------------- */

