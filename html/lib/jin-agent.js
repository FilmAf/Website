/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Agent =
{
	is_gecko	: 0,
	is_opera	: 0,
	is_ie		: 0,
	is_ie5		: 0,
	is_mac_ie	: 0,
	is_khtml	: 0,

	init : function()
	{
		var ua = navigator.userAgent;

		Agent.is_gecko	= /gecko/i.test(ua);
		Agent.is_opera	= /opera/i.test(ua);
		Agent.is_ie		= /msie/i.test(ua)&&!Agent.is_opera&&!(/mac_powerpc/i.test(ua));
		Agent.is_ie5	= Agent.is_ie&&/msie 5\.[^5]/i.test(ua);
		Agent.is_mac_ie	= /msie.*mac/i.test(ua);
		Agent.is_khtml	= /Konqueror|Safari|KHTML/i.test(ua);
	}
};

Agent.init();

/* --------------------------------------------------------------------- */
