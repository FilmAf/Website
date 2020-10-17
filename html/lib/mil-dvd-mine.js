/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdMine =
{
	onePagerEdit : '',
	onePagerText : '',

	checkAndEdit : function()
	{
		if ( $('n_b_comments') )
			DvdMine.attacheEdit();
	},

	attacheEdit : function()
	{
		Img.attach();
		Calendar.setup({inputField:'n_b2order_dd',clearInput:'x_b2order_dd',ifFormat:'%Y-%m-%d',button:'h_b2order_dd',singleClick:true,step:1});
		Calendar.setup({inputField:'n_b2loan_dd',clearInput:'x_b2loan_dd',ifFormat:'%Y-%m-%d',button:'h_b2loan_dd',singleClick:true,step:1});
		Calendar.setup({inputField:'n_b2return_dd',clearInput:'x_b2return_dd',ifFormat:'%Y-%m-%d',button:'h_b2return_dd',singleClick:true,step:1});
		Calendar.setup({inputField:'n_b_owned_dd',clearInput:'x_b_owned_dd',ifFormat:'%Y-%m-%d',button:'h_b_owned_dd',singleClick:true,step:1});
		Calendar.setup({inputField:'n_b2last_watched_dd',clearInput:'x_b2last_watched_dd',ifFormat:'%Y-%m-%d',button:'h_b2last_watched_dd',singleClick:true,step:1});
		Context.attach('h_b_genre_overwrite', false, 'menu-genre-no');
		Context.attach('h_b_user_dvd_rating', false, 'menu-rate-dvd');
		Context.attach('h_b_user_film_rating', false, 'menu-rate-film');
		Explain.attach();
	},

	swapEdit : function(b)
	{
		var a, e;
		if ( (e = $('mydvd')) )
		{
			if ( b )
			{
				DvdMine.onePagerText = e.innerHTML;
				e.innerHTML = DvdMine.onePagerEdit;
				DvdMine.attacheEdit();
			}
			else
			{
				DvdMine.onePagerEdit = e.innerHTML;
				e.innerHTML = DvdMine.onePagerText;
			}
		}
		return false;
	},

	validate : function(b_alert_no_change)
	{
		if ( ! (f = $('mydvd')) )
			return true;

		var c = {b_changed:false}, d1 = {};

		Validate.reset('n_b_comments,n_b_sort_text,n_b2retailer,n_b2order_dd,n_b2order_number,n_b2price_paid,n_b2trade_loan,'+
					   'n_b2loaned_to,n_b2loan_dd,n_b2return_dd,n_b2asking_price,n_b2custom_1,n_b2custom_2,n_b2custom_3,'+
					   'n_b2custom_4,n_b2custom_5,n_b_owned_dd,n_b2last_watched_dd');

		if ( Str.validate		('n_b_comments'				,c,   0, 4000,1,'Comments'			,0  ) !== false )
		if ( Dec.validate		('n_b_user_dvd_rating'		,c,  -1,    9,1,'DVD rating'		,0  ) !== false )
		if ( Dec.validate		('n_b_user_film_rating'		,c,  -1,    9,1,'Film rating'		,0  ) !== false )
		if ( Str.validate		('n_b_sort_text'			,c,   0,   32,1,'Sort text'			,0  ) !== false )
		if ( Str.validate		('n_b2retailer'				,c,   0,   32,1,'Retailer'			,0  ) !== false )
		if ( DateTime.validate	('n_b2order_dd'			 ,d1,c,1990, 2030,1,'Order date'		,0  ) !== false )
		if ( Str.validate		('n_b2order_number'			,c,   0,   16,1,'Order number'		,0  ) !== false )
		if ( Dbl.validate		('n_b2price_paid'			,c,   0, 1000,1,'Price paid'		,0,1) !== false )
		if ( Dec.validate		('n_b_genre_overwrite'		,c,   0,99999,1,'Genre overwrite'	,0  ) !== false )
		if ( Str.validate		('n_b2trade_loan'			,c,   0,    1,1,'Trade loan'		,0  ) !== false )
		if ( Str.validate		('n_b2loaned_to'			,c,   0,   32,1,'Loaned to'			,0  ) !== false )
		if ( DateTime.validate	('n_b2loan_dd'			 ,d1,c,1990, 2030,1,'Loan date'			,0  ) !== false )
		if ( DateTime.validate	('n_b2return_dd'		 ,d1,c,1990, 2030,1,'Return date'		,0  ) !== false )
		if ( Dbl.validate		('n_b2asking_price'			,c,   0, 1000,1,'Asking price'		,0,1) !== false )
		if ( Str.validate		('n_b2custom_1'				,c,   0,   32,1,'Custom 1'			,0  ) !== false )
		if ( Str.validate		('n_b2custom_2'				,c,   0,   32,1,'Custom 2'			,0  ) !== false )
		if ( Str.validate		('n_b2custom_3'				,c,   0,   32,1,'Custom 3'			,0  ) !== false )
		if ( Str.validate		('n_b2custom_4'				,c,   0,   32,1,'Custom 4'			,0  ) !== false )
		if ( Str.validate		('n_b2custom_5'				,c,   0,   32,1,'Custom 5'			,0  ) !== false )
		if ( DateTime.validate	('n_b_owned_dd'			 ,d1,c,1990, 2030,1,'Owned since date'	,0  ) !== false )
		if ( DateTime.validate	('n_b2last_watched_dd'	 ,d1,c,1990, 2030,1,'Last watched date'	,0  ) !== false )
		{
			if ( c.b_changed )
				f.submit();
			else
				if ( b_alert_no_change ) alert('No changes detected.  Nothing to save.');
					return true;
		}
		return false;
	}
};

/* --------------------------------------------------------------------- */

