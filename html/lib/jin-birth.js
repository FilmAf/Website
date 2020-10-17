/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var Birth =
{
	set : function(s)
	{
		Birth.popFrame(s);
		return false;
	},

	popFrame : function(s)
	{
		var a = Birth.getCountries(s).split(':'),
			i, c;

		s  =  "<html>"+
				"<head>"+
				  "<link rel='stylesheet' type='text/css' href='/styles/00/filmaf.css' />"+
				  "<script language='javascript' type='text/javascript'>"+
					"function retName(n,s)"+
					"{"+
					"if ( s ) parent.setSearchVal('birth','',s,n);"+
					"return false;"+
					"};"+
				  "</script>"+
				"</head>"+
				"<body style='background-color:#ffffff'>";

		for ( i = 0 ; i+1 < a.length ; i += 2 )
		{
			c  = i % 4 ? '' : " style='background-color:#e5f6e5'";
			s += "<div"+c+"><a href='javascript:void(0)' onclick='retName("+a[i]+",\""+a[i+1]+"\")'"+c+">"+a[i+1]+"</a></div>";
		}
		c  = i % 4 ? '' : " style='background-color:#e5f6e5'";
		s += "<div"+c+"><a href='javascript:void(0)' onclick='retName(29999,\"Other\")'"+c+">&lt;Other&gt;</a></div>";
		i += 2;
		c  = i % 4 ? '' : " style='background-color:#e5f6e5'";
		s += "<div"+c+"><a href='javascript:void(0)' onclick='retName(30000,\"Unknown\")'"+c+">&lt;Unknown&gt;</a></div>";

		s += '</body></html>';

		if ((a = Win.findFrame('frame_birth')))
		{
			a.write(s);
			a.close();
		}
	},

	getCountries : function(s)
	{
		switch (s)
		{
		case 1: return '18650:Anguilla:18700:Antigua and Barbuda:18750:Aruba:18800:Bahamas:18850:Barbados:18900:Belize:18950:Bermuda:19000:British Virgin Islands:19050:Canada:19100:Cayman Islands:19150:Clipperton Island:19200:Costa Rica:19250:Cuba:19300:Dominica:19350:Dominican Republic:19400:El Salvador:19450:Greenland:19500:Grenada:19550:Guadeloupe:19600:Guatemala:19650:Haiti:19700:Honduras:19750:Jamaica:19800:Martinique:19850:Mexico:19900:Montserrat:19950:Navassa Island:20000:Netherlands Antilles:20050:Nicaragua:20100:Panama:20150:Puerto Rico:20200:Saint Barthélemy:20250:Saint Kitts and Nevis:20300:Saint Lucia:20350:Saint Martin:20400:Saint Pierre and Miquelon:20450:Saint Vincent and the Grenadines:20500:Trinidad and Tobago:20550:Turks and Caicos Islands:20600:United States:20650:United States Virgin Islands:20700'; // North America
		case 2: return '22350:Argentina:22400:Bolivia:22450:Brazil:22500:Chile:22550:Colombia:22600:Ecuador:22650:Falkland Islands:22700:French Guiana:22750:Guyana:22800:Paraguay:22850:Peru:22900:Suriname:22950:Uruguay:23000:Venezuela:29999'; // South America
		case 3: return '16000:Åland Islands:16050:Albania:16100:Andorra:16150:Austria:16200:Belarus:16250:Belgium:16300:Bosnia and Herzegovina:16350:Bulgaria:16400:Croatia:16450:Czech Republic:16500:Denmark:16550:Estonia:16600:Faroe Islands:16650:Finland:16700:France:16750:Germany:16800:Gibraltar:16850:Greece:16900:Guernsey:16950:Hungary:17000:Iceland:17050:Ireland:17100:Isle of Man:17150:Italy:17200:Jersey:17250:Kosovo:17300:Latvia:17350:Liechtenstein:17400:Lithuania:17450:Luxembourg:17500:Macedonia:17550:Malta:17600:Moldova:17650:Monaco:17700:Montenegro:17750:Netherlands:17800:Norway:17850:Poland:17900:Portugal:17950:Pridnestrovie (Transnistria):18000:Romania:18050:Russia:18100:San Marino:18150:Serbia:18200:Slovakia:18250:Slovenia:18300:Spain:18350:Svalbard:18400:Sweden:18450:Switzerland:18500:Ukraine:18550:United Kingdom:18600:Vatican City'; // Europe
		case 4: return '10000:Algeria:10050:Angola:10100:Benin:10150:Botswana:10200:Burkina Faso:10250:Burundi:10300:Cameroon:10350:Cape Verde:10400:Central African Republic:10450:Chad:10500:Comoros:10550:Congo, Democratic Republic of:10600:Congo, Republic of:10650:Djibouti:10700:Egypt:10750:Equatorial Guinea:10800:Eritrea:10850:Ethiopia:10900:Gabon:10950:Gambia:11000:Ghana:11050:Guinea:11100:Guinea-Bissau:11150:Ivory Coast:11200:Kenya:11250:Lesotho:11300:Liberia:11350:Libya:11400:Madagascar:11450:Malawi:11500:Mali:11550:Mauritania:11600:Mauritius:11650:Mayotte:11700:Morocco:11750:Mozambique:11800:Namibia:11850:Niger:11900:Nigeria:11950:Réunion:12000:Rwanda:12050:Saint Helena:12100:Sao Tome and Principe:12150:Senegal:12200:Seychelles:12250:Sierra Leone:12300:Somalia:12350:Somaliland:12400:South Africa:12450:Sudan:12500:Swaziland:12550:Tanzania:12600:Togo:12650:Tunisia:12700:Uganda:12750:Zambia:12800:Zimbabwe'; // Africa
		case 5: return '13100:Abkhazia:13150:Afghanistan:13200:Armenia:13250:Azerbaijan:13300:Bahrain:13350:Bangladesh:13400:Bhutan:13450:British Indian Ocean Territory:13500:Brunei:13550:Cambodia:13600:China:13650:Christmas Island:13700:Cocos Islands:13750:Cyprus:13800:East Timor:13850:Georgia:13900:Hong Kong:13950:India:14000:Indonesia:14050:Iran:14100:Iraq:14150:Israel:14200:Japan:14250:Jordan:14300:Kazakhstan:14350:Kuwait:14400:Kyrgyzstan:14450:Laos:14500:Lebanon:14550:Macau:14600:Malaysia:14650:Maldives:14700:Mongolia:14750:Myanmar (Burma):14800:Nagorno-Karabakh:14850:Nepal:14900:North Korea:14950:Northern Cyprus:15000:Oman:15050:Pakistan:15100:Palestinian territories:15150:Philippines:15200:Qatar:18050:Russia:15250:Saudi Arabia:15300:Singapore:15350:South Korea:15400:South Ossetia:15450:Sri Lanka:15500:Syria:15550:Taiwan:15600:Tajikistan:15650:Thailand:15700:Turkey:15750:Turkmenistan:15800:United Arab Emirates:15850:Uzbekistan:15900:Vietnam:15950:Yemen:16000'; // Asia
		case 6: return '20700:American Samoa:20750:Australia:20800:Baker Island:20850:Cook Islands:20900:Fiji:20950:French Polynesia:21000:Guam:21050:Howland Island:21100:Jarvis Island:21150:Johnston Atoll:21200:Kingman Reef:21250:Kiribati:21300:Marshall Islands:21350:Micronesia:21400:Midway Atoll:21450:Nauru:21500:New Caledonia:21550:New Zealand:21600:Niue:21650:Norfolk Island:21700:Northern Mariana Islands:21750:Palau:21800:Palmyra Atoll:21850:Papua New Guinea:21900:Pitcairn Islands:21950:Samoa:22000:Solomon Islands:22050:Tokelau:22100:Tonga:22150:Tuvalu:22200:Vanuatu:22250:Wake Island:22300:Wallis and Futuna:22350'; // Oceania
		case 7: return '12850:Antarctica:12900:Bouvet Island:12950:French Southern Territories:13000:Heard Island and McDonald Islands:13050:South Georgia and the South Sandwich Islands'; // Antartica
		}
		return '';
	}
};

/* --------------------------------------------------------------------- */

