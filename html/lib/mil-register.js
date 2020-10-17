/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Register =
{
	validate : function() // f_val_register
	{
		if ( ! (f = $('fname')) )
 			return true;

		var c = {b_changed:false};

		Validate.reset('name,pass_1,pass_2,email,code_int');

		if ( Str.validate  ('name'		,c,3,30,0,'User name'					,0) !== false )
		if ( Str.validate  ('pass_1'	,c,6,30,0,'Password'					,0) !== false )
		if ( Str.validate  ('pass_2'	,c,6,30,0,'Password confirmation'		,0) !== false )
		if ( Str.hasSameVal('pass_1', 'pass_2'									,0)			  )
		if ( Email.validate('email'		,c,     0,'Your email address'			,0) !== false )
		if ( Str.validate  ('code_int'	,c,6, 6,0,'Security code confirmation'	,0) !== false )
			return true;

		return false;
	}
};

var Login =
{
	validate : function() // f_val_login
	{
		if ( ! (f = $('fname')) )
			return true;

		var c = {b_changed:false};

		Validate.reset('name,pass');

		if ( Str.validate('name',c,3,30,0,'User name',0) !== false )
		if ( Str.validate('pass',c,0, 0,0,'Password' ,0) !== false )
			return true;

		return false;
	}
};

var EmailValidation =
{
	validate : function() // f_val_validation
	{
		if ( ! (f = $('fname')) )
			return true;

		var c = {b_changed:false};

		Validate.reset('name,val_cd,code_int,');

		if ( Str.validate('name'	,c,3,30,0,'User name'					,0) !== false )
		if ( Str.validate('val_cd'	,c,0, 0,0,'Validation code'				,0) !== false )
		if ( Str.validate('code_int',c,6, 6,0,'Security code confirmation'	,0) !== false )
			return true;

		return false;
	}
};

var DeleteAccount =
{
	validate : function() // f_val_del_account
	{
		if ( ! (f = $('fname')) )
			return true;

		var c = {b_changed:false};

		Validate.reset('name,pass,reason');

		if ( Str.validate ('name'  ,c,3,  30,0,'User name',0) !== false )
		if ( Str.validate ('pass'  ,c,0,   0,0,'Password' ,0) !== false )
		if ( Str.validate ('reason',c,0,4000,0,'Reason'   ,0) !== false )
			return true;

		return false;
	}
};

var FindAccount =
{
	validate : function() // f_val_find_account
	{
		if ( ! (f = $('fname')) )
			return true;

		var c = {b_changed:false}, e, o;

		Validate.reset('email,code_int,old_name');

		e = $('email').value != '';
		o = $('old_name').value != '';
		if ( (e && o) || (!e && !o) )
		{
			Validate.warn($('email'), true, true, 'Please specify either your email address or your old FilmAf user name, but not both.', false);
		}
		else
		{
			if ( !e || Email.validate('email'	,c,     1,'Your email address'			,0) !== false )
			if ( !o || Str.validate  ('old_name',c,3,32,1,'Old FilmAf user name'		,0) !== false )
			if (	   Str.validate  ('code_int',c,6, 6,0,'Security code confirmation'	,0) !== false )
				return true;
		}

		return false;
	}
};

var ResetPass =
{
	validate : function() // f_val_reset_pass
	{
		if ( ! (f = $('fname')) )
			return true;

		var c = {b_changed:false};

		Validate.reset('name,email,code_int');

		if ( Str.validate	('name'    ,c,3,30,0,'User name'					,0) !== false )
		if ( Email.validate	('email'   ,c,     0,'Your email address'			,0) !== false )
		if ( Str.validate	('code_int',c,6, 6,0,'Security code confirmation'	,0) !== false )
			return true;

		return false;
	}
};

var ChangePass =
{
	validate : function() // f_val_change_pass
	{
		if ( ! (f = $('fname')) )
			return true;

		var c = {b_changed:false};

		Validate.reset('pass_curr,pass_new1,pass_new2,code_int');

		if ( Str.validate	('pass_curr',c,0,0,0,'Current passord'           ,0) !== false )
		if ( Str.validate	('pass_new1',c,0,0,0,'New password'              ,0) !== false )
		if ( Str.validate	('pass_new2',c,0,0,0,'Confirm password'          ,0) !== false )
		if ( Str.hasSameVal	('pass_new1','pass_new2'                         ,0) )
		if ( Str.validate	('code_int' ,c,6,6,0,'Security code confirmation',0) !== false )
			return true;

		return false;
	}
};

var ChangeEmail =
{
	validate : function() // f_val_change_email
	{
		if ( ! (f = $('fname')) )
			return true;

		var c = {b_changed:false};

		Validate.reset('email,pass,code_int');

		if ( Email.validate	('email'    ,c,    0,'Your email address'        ,0) !== false )
		if ( Str.validate	('pass'     ,c,0,0,0,'Password'                  ,0) !== false )
		if ( Str.validate	('code_int' ,c,6,6,0,'Security code confirmation',0) !== false )
			return true;

		return false;
	}
};

var CloneDvd =
{
	validate : function() // f_val_clone_dvd
	{
		if ( ! (f = $('fname')) )
			return true;

		var c = {b_changed:false};

		Validate.reset('dvd_id');

		if ( Dec.validate  ('dvd_id',c,1,999999,0,'DVD id',0) !== false )
			return true;

		return false;
	}
};

var FeaturetteDir =
{
	validate : function()
	{
		if ( ! (f = $('fname')) )
			return true;

		var c = {b_changed:false};

		Validate.reset('director');

		if ( Str.validate('director',c,2,100,0,'Director',0) !== false )
			return true;

		return false;
	}
};


/* --------------------------------------------------------------------- */

