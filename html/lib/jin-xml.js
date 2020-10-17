/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Xml =
{
	parse : function(txt)
	{
		if ( window.DOMParser )
		{
			parser=new DOMParser();
			xmlDoc=parser.parseFromString(txt,"text/xml");
		}
		else
		{
			xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
			xmlDoc.async="false";
			xmlDoc.loadXML(txt);
		}
		return xmlDoc;
	}
/*
mil-edit-obj.js
	showXml : function(s_trg,s_tag,s_txt)
	{
		var e = $('xml');
		if ( e.style.visibility == 'hidden' )
		{
			$('b_impxml').style.visibility = 'hidden';
			$('b_xmlguide').style.visibility = 'hidden';
			e.innerHTML = "<div style='margin:2px 6px 2px 2px'>"+
							"<textarea id='xmltext' style='width:100%;padding:1px' wrap='soft' rows='15'>"+s_txt+"</textarea>"+
							"<div>"+
							  "<input type='button' onclick='"+s_trg+"Edit.xmlImp(ObjEdit.xmlImp(\""+s_tag+"\"))' value='Import XML' /> "+
							  "<a href='javascript:void(0)' id='b_xmlguide2' class='but'>XML guide</a></div>"+
						  "</div";
			e.style.visibility = 'visible';
			$('b_xmlguide2').onclick = $('b_xmlguide').onclick;
		}
	},
	xmlImp : function(s_top)
	{
		var x = Xml.parse($('xmltext').value), c = null;
		if ( x )
			if ( x.errorCode )
				alert('XML Parsing error '+x.errorCode+' on line '+x.line+'\n'+x.reason);
			else
				if ( (c = x.firstChild) && c.tagName == s_top )
					return c.firstChild;
		return c;
	},

mil-edit-pub.js.setup
			$('b_impxml').onclick			= function() {ObjEdit.showXml('Pub','publisher',PubEdit.xmlSample())};
			$('b_xmlguide').onclick			= PubEdit.xmlGuide;

mil-edit-pub.js
	xmlSample : function()
	{
		return ""+
		"<publisher>\n"+
		"    <pub_name></pub_name>\n"+
		"    <official_site></official_site>\n"+
		"    <wikipedia></wikipedia>\n"+
		"</publisher>\n";
	},
	xmlImp : function(c)
	{
		while ( c )
		{
			switch ( c.tagName )
			{
			case 'pub_name':
			case 'official_site':
			case 'wikipedia':
				$('n_u_'+c.tagName).value = c.text;
			}
			c = c.nextSibling;
		}
	},
	xmlGuide : function()
	{
	},
*/

};

/* --------------------------------------------------------------------- */

