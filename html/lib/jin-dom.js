/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Dom =
{
	getParentByType : function(e,t) // getParentByType
	{
		if ( ! t ) return null;
		t = t.toLowerCase();
		while  ( e && e.tagName && e.tagName.toLowerCase() != t ) e = e.parentNode;
		return ( e && e.tagName && e.tagName.toLowerCase() == t) ? e : null;
	},

	getChildrenByType : function(e,t) // getChildrenByTagName
	{
		var a = [], i;
		if ( t ) t = t.toLowerCase();
		if ( t && e && e.firstChild )
		{
			for( i = e.firstChild  ;  i  ;  i = i.nextSibling )
			{
				if ( i.nodeType != 1 ) continue;
				if ( !t || t == i.tagName.toLowerCase() ) a[a.length] = i;
			}
		}
		return a;
	},

	getFirstChildByType : function(e,t) // getFirstChildByTagName
	{
		if ( t ) t = t.toLowerCase();
		if ( t && e && e.firstChild )
		{
			for( var i = e.firstChild ; i ; i = i.nextSibling )
			{
				if ( i.nodeType != 1 ) continue;
				if ( !t || t == i.tagName.toLowerCase() ) return i;
			}
		}
		return null;
	},

    decodeHtmlForInput : function(s)
	{
		// Done to address disapearing new line for Chrome
		if ( ! s ) return '';
		var d = document.createElement('div');
		d.innerHTML = s.replace(/<br\x20?\/?>/g, '_ed_magick_');
		s = document.all ? d.innerText : d.textContent;
		return s.replace(/_ed_magick_/g, '\n');
	},

    decodeHtmlEntities : function(s) // html_entities_decode
	{
		if ( ! s ) return '';
		var d = document.createElement('div');
		d.innerHTML = s;
		return document.all ? d.innerText : d.textContent;
	},

	stripTags : function(s) // stripTags
	{
		return s.replace(/[<].*?[>]/g, '');
	},

	flipHidden : function(s) // tflip
	{
		var e = $(s),
			f = $(s+'_sav');

		if ( e && f )
		{
			s = f.innerHTML;
			f.innerHTML = e.innerHTML;
			e.innerHTML = s;
			return s != ''; // returns true if display element is not empty
		}
		return false;
	},

	getElementWidth : function(e)
	{
		if ( typeof e.clip != 'undefined' ) return e.clip.width;
		if ( e.style.pixelWidth ) return e.style.pixelWidth;
		return e.offsetWidth;
	},

	getElementHeight : function(e)
	{
		if ( typeof this.clip != 'undefined' ) return this.clip.height;
		if ( this.style.pixelHeight ) return this.style.pixelHeight;
		return this.offsetHeight;
	}
};

/* --------------------------------------------------------------------- */

