/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulSearch(s) // f_echo_ul_search
{
    s.s +=
	"<li id='menu-title-has'>"+
	  "<ul id='help_dvd'>"+
		"<li>"+
		  "<div class='pop_div'>"+
			"<strong>Commas</strong> (&quot;,&quot;) may be used to separate multiple values. "+
			"Searching for &quot;Lady, Duke&quot; will return titles with both Lady and Duke. Searching for "+
			"&quot;<> Lady, Duke&quot; will return titles that nave neither Lady nor Duke. In the odd case that "+
			"you want to return listings with either Lady or Duke, replace the comma with a <strong>"+
			"pipe</strong> (&quot;|&quot;) as in &quot;Lady | Duke&quot;."+
			"<div class='pop_sep_long'>&nbsp;</div>"+

			"<strong>Articles</strong> at the beginnig of a title (&quot;A&quot;, &quot;An&quot;, "+
			"&quot;The&quot;, &quot;Le&quot;,&quot;La&quot;, &quot;Les&quot;, &quot;L&#039;&quot;, &quot;Die&quot;, "+
			"&quot;Das&quot;, etc.) are generaly placed at the end and should not be searched."+
			"<div class='pop_sep_long'>&nbsp;</div>"+

			"<strong>A slash</strong> (&quot;/&quot;) may be used to represent the end of a word. "+
			"Therefore searching for &quot;<strong>Lady/</strong>&quot; will now only match:"+
			"<div style='padding-left:2em'>"+
			  "- My Fair <strong>Lady</strong> (1964)<br />"+
			  "- <strong>Lady</strong> Vanishes, The (1938)"+
			"</div>"+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-priced-below'>"+
	  "<ul>"+
		"<li>"+
		  "<div class='pop_div'>"+
			"You may enter a price in US dollars."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"Searching for &quot;<strong>10</strong>&quot; will match:"+
			"<div style='padding-left:2em'>"+
			  "- DVDs with a price is <strong>smaller</strong> than or equal to $10"+
			"</div>"+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"<strong>A percent sign</strong> (&quot;%&quot;) can be used to express the retail price in relation to the list price (MSRP). "+
			"Searching for &quot;<strong>70%</strong>&quot; will match:"+
			"<div style='padding-left:2em'>"+
			  "- DVDs discounted at 30% or more (&lt;= 70% MSRP)"+
			"</div>"+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"<strong>The operators &quot;&lt;&quot;, &quot;&lt;=&quot;, &quot;=&quot;, &quot;&lt;&gt;&quot;, &quot;&gt;=&quot;, "+
			"and &quot;&gt;&quot;</strong> can be used to create more complex conditions. Searching for &quot;<strong>&gt;=10 "+
			"&lt=20 &lt=70%</strong>&quot; will match:"+
			"<div style='padding-left:2em'>"+
			  "- DVDs priced between $10 and $20, "+
			  "discounted at 30% or more (&lt;= 70% MSRP)"+
			"</div>"+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"The &quot;price&quot; of a DVD refers to the best offer amongst all vendors participating in the "+
			"<strong>FilmAf Price Comparison</strong> tool."+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-priced-above'>"+
	  "<ul>"+
		"<li>"+
		  "<div class='pop_div'>"+
			"You may enter a price in US dollars."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"Searching for &quot;<strong>5</strong>&quot; will match:"+
			"<div style='padding-left:2em'>"+
			  "- DVDs with a price is <strong>greater</strong> than or equal to $5"+
			"</div>"+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"<strong>A percent sign</strong> (&quot;%&quot;) can be used to express the retail price in relation to the list price (MSRP). "+
			"Searching for &quot;<strong>70%</strong>&quot; will match:"+
			"<div style='padding-left:2em'>"+
			  "- DVDs discounted at 30% or less (&lt;= 70% MSRP)"+
			"</div>"+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"<strong>The operators &quot;&lt;&quot;, &quot;&lt;=&quot;, &quot;=&quot;, &quot;&lt;&gt;&quot;, &quot;&gt;=&quot;, "+
			"and &quot;&gt;&quot;</strong> can be used to create more complex conditions. Searching for &quot;<strong>&gt;=10 "+
			"&lt=20 &lt=70%</strong>&quot; will match:"+
			"<div style='padding-left:2em'>"+
			  "- DVDs priced between $10 and $20, "+
			  "discounted at 30% or more (&lt;= 70% MSRP)"+
			"</div>"+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"The &quot;price&quot; of a DVD refers to the best offer amongst all vendors participating in the "+
			"<strong>FilmAf Price Comparison</strong> tool."+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-amz-asin'>"+
	  "<ul>"+
		"<li>"+
		  "<div class='pop_div'>"+
			"Please enter a search string with at least 5 characters."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"The Amazon ASIN is a <strong>10-character</strong> code of letters and numbers that "+
			"uniquelly identifies a product in an Amazon web site. This search is a convenience in case you have "+
			"the ASIN on hand and want to quickly find that listing at FilmAf."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"<strong>Commas</strong> (&quot;,&quot;) can be used to separate multiple values, in "+
			"which case we will search for titles matching any of them."+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-imdb'>"+
	  "<ul>"+
		"<li>"+
		  "<div class='pop_div'>"+
			"Please enter a search string with 7 digits."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"The IMDB id is a 7-digit numeric code that identifies a film in the <a href='http://www.imdb.com/' target="+
			"'_blank'>Internet Movie Database (imbd.com)</a>. This search is a convenience in case you have the IMDB "+
			"id on hand and want to quickly find DVDs that contain that film in FilmAf."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"For example Jean Renoir&#039;s La grande illusion IMDB id is &quot;0028950&quot; and it can be accessed "+
			"at <a href='http://www.imdb.com/title/tt0028950' target='_blank'>http://www.imdb.com/title/tt<strong>"+
			"0028950</strong></a>."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"<strong>Commas</strong> (&quot;,&quot;) can be used to separate multiple values, in "+
			"which case we will search for titles matching any of them."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"<strong>The operator &lt;&gt;</strong> can be used to exlude a film. &quot;0378194 &lt;&gt;"+
			"0266697&quot; will give you all editons of Kill Bill Vol. 2, that do not have Vol. 1."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"<strong>The operator =</strong> can be used to look for sets with multiple films. &quot;"+
			"0378194 =0266697&quot; will search for a DVD set with both films."+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-upc'>"+
	  "<ul>"+
		"<li>"+
		  "<div class='pop_div'>"+
			"Please enter a search string with at least 5 digits."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"Generally a UPC uniquely identifies a product.  A UPC for a US product is <strong>12-digit"+
			"</strong> long (if you only see 10, there are probably additional smaller digits to the right and left of the "+
			"10 you found). <strong>Non-US UPCs</strong> usually have 13 or more digits."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"Dashes (&quot;-&quot;) are ignored."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"<strong>Commas</strong> (&quot;,&quot;) can be used to separate multiple values, in "+
			"which case we will search for titles matching any of them."+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-director'>"+
	  "<ul id='help_dir'>"+
		"<li>"+
		  "<div class='pop_div'>"+
			"<strong>Commas</strong> (&quot;,&quot;) may be used to specify multiple values."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"You can type in &quot;<strong>missing</strong>&quot; to find titles where the director information is not present."+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-publisher'>"+
	  "<ul id='help_pub'>"+
		"<li>"+
		  "<div class='pop_div'>"+
			"<strong>Commas</strong> (&quot;,&quot;) may be used to specify multiple values."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"You can type in &quot;<strong>missing</strong>&quot; to find titles where the publisher information is not present."+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-country' onclick='onMenuClick(this)'>"+
	  "<ul id='help_cnt'>"+
		"<li>"+
		  "<div style='margin-top:2px'>"+
			"Please select a country<br />from the list below."+
			"<div class='pop_sep_short'>&nbsp;</div>"+
			"<form id='mform_cnt' name='mform_cnt' style='margin-top:1px; margin-bottom:4px'>"+
			  "<input type='radio' name='ropt' value='r' onclick='Search.setAppend(\"cnt\",false)'>Replace"+
			  "<input type='radio' name='ropt' value='a' onclick='Search.setAppend(\"cnt\",true)'>Append<br />"+
			"</form>"+
			"<div class='pop_sep_short'>&nbsp;</div>"+
		  "</div>"+
		"</li>"+
		"<li>The Americas"+
		  "<ul>"+
			"<li id='hm_cnt_us'><a><strong>U.S.</strong></a></li>"+
			"<li id='hm_cnt_ar'>Argentina</li>"+
			"<li id='hm_cnt_br'>Brazil</li>"+
			"<li id='hm_cnt_ca'>Canada</li>"+
			"<li id='hm_cnt_cl'>Chile</li>"+
			"<li id='hm_cnt_cu'>Cuba</li>"+
			"<li id='hm_cnt_mx'>Mexico</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Europe"+
		  "<ul>"+
			"<li id='hm_cnt_at'>Austria</li>"+
			"<li id='hm_cnt_be'>Belgium</li>"+
			"<li id='hm_cnt_hr'>Croatia</li>"+
			"<li id='hm_cnt_cz'>Czech Republic</li>"+
			"<li id='hm_cnt_dk'>Denmark</li>"+
			"<li id='hm_cnt_ee'>Estonia</li>"+
			"<li id='hm_cnt_fi'>Finland</li>"+
			"<li id='hm_cnt_fr'><a><strong>France</strong></a></li>"+
			"<li id='hm_cnt_de'><a><strong>Germany</strong></a></li>"+
			"<li id='hm_cnt_gr'>Greece</li>"+
			"<li id='hm_cnt_hu'>Hungary</li>"+
			"<li id='hm_cnt_is'>Iceland</li>"+
			"<li id='hm_cnt_it'><a><strong>Italy</strong></a></li>"+
			"<li id='hm_cnt_lt'>Lithuania</li>"+
			"<li id='hm_cnt_mk'>Macedonia</li>"+
			"<li id='hm_cnt_nl'>Netherlands</li>"+
			"<li id='hm_cnt_no'>Norway</li>"+
			"<li id='hm_cnt_pl'>Poland</li>"+
			"<li id='hm_cnt_pt'>Portugal</li>"+
			"<li id='hm_cnt_ro'>Romania</li>"+
			"<li id='hm_cnt_ru'>Russian Federation</li>"+
			"<li id='hm_cnt_rs'>Serbia</li>"+
			"<li id='hm_cnt_sk'>Slovakia</li>"+
			"<li id='hm_cnt_si'>Slovenia</li>"+
			"<li id='hm_cnt_es'>Spain</li>"+
			"<li id='hm_cnt_se'>Sweden</li>"+
			"<li id='hm_cnt_ch'>Switzerland</li>"+
			"<li id='hm_cnt_uk'><a><strong>U.K.</strong></a></li>"+
		  "</ul>"+
		"</li>"+
		"<li>Asia & Oceania"+
		  "<ul>"+
			"<li id='hm_cnt_au'><a><strong>Australia</strong></a></li>"+
			"<li id='hm_cnt_cn'>China</li>"+
			"<li id='hm_cnt_hk'><a><strong>Hong Kong</strong></a></li>"+
			"<li id='hm_cnt_in'>India</li>"+
			"<li id='hm_cnt_id'>Indonesia</li>"+
			"<li id='hm_cnt_il'>Israel</li>"+
			"<li id='hm_cnt_jp'>Japan</li>"+
			"<li id='hm_cnt_my'>Malaysia</li>"+
			"<li id='hm_cnt_nz'>New Zealand</li>"+
			"<li id='hm_cnt_ph'>Philippines</li>"+
			"<li id='hm_cnt_sg'>Singapore</li>"+
			"<li id='hm_cnt_kr'>South Korea</li>"+
			"<li id='hm_cnt_tw'><a><strong>Taiwan</strong></a></li>"+
			"<li id='hm_cnt_th'>Thailand</li>"+
			"<li id='hm_cnt_tr'>Turkey</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Africa"+
		  "<ul>"+
			"<li id='hm_cnt_za'>South Africa</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_cnt_un'>Unknown Country</li>"+
		"<li></li>"+
		"<li id='hm_cnt_xx'>None</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-genre' onclick='onMenuClick(this)'>"+
	  "<ul id='help_gen'>"+
		"<li>"+
		  "<div style='margin-top:2px'>"+
			"Please select a genre<br />from the list below."+
			"<div class='pop_sep_short'>&nbsp;</div>"+
			"<form id='mform_gen' name='mform_gen' style='margin-top:1px; margin-bottom:4px'>"+
			  "<input type='radio' name='ropt' value='r' onclick='Search.setAppend(\"gen\",false)'>Replace"+
			  "<input type='radio' name='ropt' value='a' onclick='Search.setAppend(\"gen\",true)'>Append<br />"+
			"</form>"+
			"<div class='pop_sep_short'>&nbsp;</div>"+
		  "</div>"+
		"</li>"+
		"<li>Action-Adventure"+
		  "<ul>"+
			"<li id='hm_gen_action-nosub'><a><strong>Action-Adventure</strong></a></li>"+
			"<li id='hm_gen_action'><a><strong>Action-Adventure + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_action-comedy'>Comedy</li>"+
			"<li id='hm_gen_action-crime'>Crime</li>"+
			"<li id='hm_gen_action-disaster'>Disaster</li>"+
			"<li id='hm_gen_action-epic'>Epic</li>"+
			"<li id='hm_gen_action-espionage'>Espionage</li>"+
			"<li id='hm_gen_action-martialarts'>Martial Arts</li>"+
			"<li id='hm_gen_action-military'>Military</li>"+
			"<li id='hm_gen_action-samurai'>Samurai</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Animation"+
		  "<ul>"+
			"<li id='hm_gen_animation-nosub'><a><strong>Animation</strong></a></li>"+
			"<li id='hm_gen_animation'><a><strong>Animation + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_animation-cartoons'>Cartoons</li>"+
			"<li id='hm_gen_animation-family'>Family</li>"+
			"<li id='hm_gen_animation-mature'>Mature</li>"+
			"<li id='hm_gen_animation-puppetrystopmotion'>Puppetry &amp; Stop-Motion</li>"+
			"<li id='hm_gen_animation-scifi'>Sci-Fi</li>"+
			"<li id='hm_gen_animation-superheroes'>Superheroes</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Anime"+
		  "<ul>"+
			"<li id='hm_gen_anime-nosub'><a><strong>Anime</strong></a></li>"+
			"<li id='hm_gen_anime'><a><strong>Anime + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_anime-action'>Action</li>"+
			"<li id='hm_gen_anime-comedy'>Comedy</li>"+
			"<li id='hm_gen_anime-drama'>Drama</li>"+
			"<li id='hm_gen_anime-fantasy'>Fantasy</li>"+
			"<li id='hm_gen_anime-horror'>Horror</li>"+
			"<li id='hm_gen_anime-mahoushoujo'>Mahou Shoujo (Magical Girls)</li>"+
			"<li id='hm_gen_anime-martialarts'>Martial Arts</li>"+
			"<li id='hm_gen_anime-mecha'>Mecha (Giant Robots)</li>"+
			"<li id='hm_gen_anime-moe'>Mo&#233; (Cute Girls, Romance)</li>"+
			"<li id='hm_gen_anime-romance'>Romance</li>"+
			"<li id='hm_gen_anime-scifi'>Sci-Fi</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Comedy"+
		  "<ul>"+
			"<li id='hm_gen_comedy-nosub'><a><strong>Comedy</strong></a></li>"+
			"<li id='hm_gen_comedy'><a><strong>Comedy + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_comedy-dark'>Dark</li>"+
			"<li id='hm_gen_comedy-farce'>Farce</li>"+
			"<li id='hm_gen_comedy-horror'>Horror</li>"+
			"<li id='hm_gen_comedy-romantic'>Romantic</li>"+
			"<li id='hm_gen_comedy-satire'>Satire</li>"+
			"<li id='hm_gen_comedy-scifi'>Sci-Fi</li>"+
			"<li id='hm_gen_comedy-screwball'>Screwball</li>"+
			"<li id='hm_gen_comedy-sitcom'>Sitcom</li>"+
			"<li id='hm_gen_comedy-sketchesstandup'>Sketches &amp; Stand-Up</li>"+
			"<li id='hm_gen_comedy-slapstick'>Slapstick</li>"+
			"<li id='hm_gen_comedy-teen'>Teen</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Documentary"+
		  "<ul>"+
			"<li id='hm_gen_documentary-nosub'><a><strong>Documentary</strong></a></li>"+
			"<li id='hm_gen_documentary'><a><strong>Documentary + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_documentary-biography'>Biography</li>"+
			"<li id='hm_gen_documentary-crime'>Crime</li>"+
			"<li id='hm_gen_documentary-culture'>Culture</li>"+
			"<li id='hm_gen_documentary-entertainment'>Entertainment</li>"+
			"<li id='hm_gen_documentary-history'>History</li>"+
			"<li id='hm_gen_documentary-nature'>Nature</li>"+
			"<li id='hm_gen_documentary-propaganda'>Propaganda</li>"+
			"<li id='hm_gen_documentary-religion'>Religion</li>"+
			"<li id='hm_gen_documentary-science'>Science</li>"+
			"<li id='hm_gen_documentary-social'>Social</li>"+
			"<li id='hm_gen_documentary-sports'>Sports</li>"+
			"<li id='hm_gen_documentary-travel'>Travel</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Drama"+
		  "<ul>"+
			"<li id='hm_gen_drama-nosub'><a><strong>Drama</strong></a></li>"+
			"<li id='hm_gen_drama'><a><strong>Drama + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_drama-courtroom'>Courtroom</li>"+
			"<li id='hm_gen_drama-crime'>Crime</li>"+
			"<li id='hm_gen_drama-docudrama'>Docudrama</li>"+
			"<li id='hm_gen_drama-melodrama'>Melodrama</li>"+
			"<li id='hm_gen_drama-period'>Period</li>"+
			"<li id='hm_gen_drama-romance'>Romance</li>"+
			"<li id='hm_gen_drama-sports'>Sports</li>"+
			"<li id='hm_gen_drama-war'>War</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Educational"+
		  "<ul>"+
			"<li id='hm_gen_educational-nosub'><a><strong>Educational</strong></a></li>"+
			"<li id='hm_gen_educational'><a><strong>Educational + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_educational-children'>Children</li>"+
			"<li id='hm_gen_educational-school'>School</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Erotica"+
		  "<ul>"+
			"<li id='hm_gen_erotica-nosub'><a><strong>Erotica</strong></a></li>"+
			"<li id='hm_gen_erotica'><a><strong>Erotica + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_erotica-hentai'>Hentai</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gen_experimental'>Experimental</li>"+
		"<li>Exploitation"+
		  "<ul>"+
			"<li id='hm_gen_exploitation-nosub'><a><strong>Exploitation</strong></a></li>"+
			"<li id='hm_gen_exploitation'><a><strong>Exploitation + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_exploitation-blaxploitation'>Blaxploitation</li>"+
			"<li id='hm_gen_exploitation-nazisploitation'>Nazisploitation</li>"+
			"<li id='hm_gen_exploitation-nunsploitation'>Nunsploitation</li>"+
			"<li id='hm_gen_exploitation-pinkueiga'>Pinku Eiga</li>"+
			"<li id='hm_gen_exploitation-sexploitation'>Sexploitation</li>"+
			"<li id='hm_gen_exploitation-shockumentary'>Shockumentary</li>"+
			"<li id='hm_gen_exploitation-wip'>WIP</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gen_fantasy'>Fantasy</li>"+
		"<li id='hm_gen_filmnoir'>Film Noir</li>"+
		"<li>Horror"+
		  "<ul>"+
			"<li id='hm_gen_horror-nosub'><a><strong>Horror</strong></a></li>"+
			"<li id='hm_gen_horror'><a><strong>Horror + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_horror-anthology'>Anthology</li>"+
			"<li id='hm_gen_horror-creatureanimal'>Creature &amp; Animal</li>"+
			"<li id='hm_gen_horror-espghosts'>ESP &amp; Ghosts</li>"+
			"<li id='hm_gen_horror-eurotrash'>Eurotrash</li>"+
			"<li id='hm_gen_horror-exploitation'>Exploitation</li>"+
			"<li id='hm_gen_horror-gialli'>Gialli</li>"+
			"<li id='hm_gen_horror-goreshock'>Gore &amp; Shock</li>"+
			"<li id='hm_gen_horror-gothic'>Gothic</li>"+
			"<li id='hm_gen_horror-possessionsatan'>Possession &amp; Satan</li>"+
			"<li id='hm_gen_horror-shockumentary'>Shockumentary</li>"+
			"<li id='hm_gen_horror-slashersurvival'>Slasher &amp; Survival</li>"+
			"<li id='hm_gen_horror-vampires'>Vampires</li>"+
			"<li id='hm_gen_horror-zombiesinfected'>Zombies &amp; Infected</li>"+
			"<li id='hm_gen_horror-otherundead'>Other Undead</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Music"+
		  "<ul>"+
			"<li id='hm_gen_music-nosub'><a><strong>Music</strong></a></li>"+
			"<li id='hm_gen_music'><a><strong>Music + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_music-liveinconcert'>Live in Concert</li>"+
			"<li id='hm_gen_music-musicvideos'>Music Videos</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gen_musical'>Musical</li>"+
		"<li>Performing Arts"+
		  "<ul>"+
			"<li id='hm_gen_performing-nosub'><a><strong>Performing Arts</strong></a></li>"+
			"<li id='hm_gen_performing'><a><strong>Performing Arts + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_performing-circus'>Circus</li>"+
			"<li id='hm_gen_performing-concerts'>Concerts</li>"+
			"<li id='hm_gen_performing-dance'>Dance</li>"+
			"<li id='hm_gen_performing-operas'>Operas</li>"+
			"<li id='hm_gen_performing-theater'>Theater</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Sci-Fi"+
		  "<ul>"+
			"<li id='hm_gen_scifi-nosub'><a><strong>Sci-Fi</strong></a></li>"+
			"<li id='hm_gen_scifi'><a><strong>Sci-Fi + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_scifi-alien'>Alien</li>"+
			"<li id='hm_gen_scifi-alternatereality'>Alternate Reality</li>"+
			"<li id='hm_gen_scifi-apocalyptic'>Apocalyptic</li>"+
			"<li id='hm_gen_scifi-cyberpunk'>Cyber Punk</li>"+
			"<li id='hm_gen_scifi-kaiju'>Kaiju (Giant Monster)</li>"+
			"<li id='hm_gen_scifi-lostworlds'>Lost Worlds</li>"+
			"<li id='hm_gen_scifi-military'>Military</li>"+
			"<li id='hm_gen_scifi-otherworlds'>Other Worlds</li>"+
			"<li id='hm_gen_scifi-space'>Space</li>"+
			"<li id='hm_gen_scifi-spacehorror'>Space Horror</li>"+
			"<li id='hm_gen_scifi-superheroes'>Superheroes</li>"+
			"<li id='hm_gen_scifi-utopiadystopia'>Utopia &amp; Dystopia</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gen_short'>Short</li>"+
		"<li>Silent"+
		  "<ul>"+
			"<li id='hm_gen_silent-nosub'><a><strong>Silent</strong></a></li>"+
			"<li id='hm_gen_silent'><a><strong>Silent + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_silent-animation'>Animation</li>"+
			"<li id='hm_gen_silent-horror'>Horror</li>"+
			"<li id='hm_gen_silent-melodrama'>Melodrama</li>"+
			"<li id='hm_gen_silent-slapstick'>Slapstick</li>"+
			"<li id='hm_gen_silent-western'>Western</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Sports"+
		  "<ul>"+
			"<li id='hm_gen_sports-nosub'><a><strong>Sports</strong></a></li>"+
			"<li id='hm_gen_sports'><a><strong>Sports + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_sports-baseball'>Baseball</li>"+
			"<li id='hm_gen_sports-basketball'>Basketball</li>"+
			"<li id='hm_gen_sports-biking'>Biking</li>"+
			"<li id='hm_gen_sports-fitness'>Fitness</li>"+
			"<li id='hm_gen_sports-football'>Football</li>"+
			"<li id='hm_gen_sports-golf'>Golf</li>"+
			"<li id='hm_gen_sports-hockey'>Hockey</li>"+
			"<li id='hm_gen_sports-martialarts'>Martial Arts</li>"+
			"<li id='hm_gen_sports-motorsports'>Motor Sports</li>"+
			"<li id='hm_gen_sports-olympics'>Olympics</li>"+
			"<li id='hm_gen_sports-skateboard'>Skateboard</li>"+
			"<li id='hm_gen_sports-skiing'>Skiing</li>"+
			"<li id='hm_gen_sports-soccer'>Soccer</li>"+
			"<li id='hm_gen_sports-tennis'>Tennis</li>"+
			"<li id='hm_gen_sports-wrestling'>Wrestling</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Suspense"+
		  "<ul>"+
			"<li id='hm_gen_suspense-nosub'><a><strong>Suspense</strong></a></li>"+
			"<li id='hm_gen_suspense'><a><strong>Suspense + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_suspense-mystery'>Mystery</li>"+
			"<li id='hm_gen_suspense-thriller'>Thriller</li>"+
		  "</ul>"+
		"</li>"+
		"<li>War"+
		  "<ul>"+
			"<li id='hm_gen_war-nosub'><a><strong>War</strong></a></li>"+
			"<li id='hm_gen_war'><a><strong>War + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_war-uscivilwar'>US Civil War</li>"+
			"<li id='hm_gen_war-wwi'>World War I</li>"+
			"<li id='hm_gen_war-wwii'>World War II</li>"+
			"<li id='hm_gen_war-korea'>Korea</li>"+
			"<li id='hm_gen_war-vietnam'>Vietnam</li>"+
			"<li id='hm_gen_war-postcoldwar'>Post-Cold War</li>"+
			"<li id='hm_gen_war-other'>Other</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Western"+
		  "<ul>"+
			"<li id='hm_gen_western-nosub'><a><strong>Western</strong></a></li>"+
			"<li id='hm_gen_western'><a><strong>Western + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_western-epic'>Epic</li>"+
			"<li id='hm_gen_western-singingcowboy'>Singing Cowboy</li>"+
			"<li id='hm_gen_western-spaghetti'>Spaghetti</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gen_dvdaudio'>DVD Audio</li>"+
		"<li>Other"+
		  "<ul>"+
			"<li id='hm_gen_other-nosub'><a><strong>Other</strong></a></li>"+
			"<li id='hm_gen_other'><a><strong>Other + Subgenres</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gen_other-digitalcomicbooks'>Digital Comic Books</li>"+
			"<li id='hm_gen_other-gameshows'>Game Shows</li>"+
			"<li id='hm_gen_other-games'>Games</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gen_unspecifiedgenre'>Unspecified Genre</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-media' onclick='onMenuClick(this)'>"+
	  "<ul id='help_med'>"+
		"<li>"+
		  "<div style='margin-top:2px'>"+
			"Please select a media type<br />from the list below."+
			"<div class='pop_sep_short'>&nbsp;</div>"+
			"<form id='mform_med' name='mform_med' style='margin-top:1px; margin-bottom:4px'>"+
			  "<input type='radio' name='ropt' value='r' onclick='Search.setAppend(\"med\",false)'>Replace"+
			  "<input type='radio' name='ropt' value='a' onclick='Search.setAppend(\"med\",true)'>Append<br />"+
			"</form>"+
			"<div class='pop_sep_short'>&nbsp;</div>"+
		  "</div>"+
		"</li>"+
		"<li>Home media"+
		  "<ul>"+
			"<li id='hm_med_d'>DVD</li>"+
			"<li id='hm_med_b'>Blu-ray</li>"+
			"<li id='hm_med_3'>Blu-ray 3D</li>"+
			"<li id='hm_med_2'>Blu-ray/DVD Combo</li>"+
			"<li id='hm_med_r'>BD-R</li>"+
			"<li id='hm_med_v'>DVD-R</li>"+
			"<li id='hm_med_h'>HD DVD</li>"+
			"<li id='hm_med_c'>HD DVD/DVD Combo</li>"+
			"<li id='hm_med_t'>HD DVD/DVD TWIN Format</li>"+
			"<li id='hm_med_a'>DVD Audio</li>"+
			"<li id='hm_med_p'>Placeholder</li>"+
			"<li id='hm_med_o'>Other</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Theatrical &amp; broadcast"+
		  "<ul>"+
			"<li id='hm_med_f'>Film</li>"+
			"<li id='hm_med_s'>Short</li>"+
			"<li id='hm_med_l'>Television</li>"+
			"<li id='hm_med_e'>Featurette</li>"+
			"<li id='hm_med_n'>Events &amp; Performances</li>"+
		  "</ul>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-released' onclick='onMenuClick(this)'>"+
	  "<ul id='help_rel'>"+
		"<li>"+
		  "<div style='margin-top:2px'>"+
			"Please select a release status<br />from the list below."+
			"<div class='pop_sep_short'>&nbsp;</div>"+
			"<form id='mform_rel' name='mform_rel' style='margin-top:1px; margin-bottom:4px'>"+
			  "<input type='radio' name='ropt' value='r' onclick='Search.setAppend(\"rel\",false)'>Replace"+
			  "<input type='radio' name='ropt' value='a' onclick='Search.setAppend(\"rel\",true)'>Append<br />"+
			"</form>"+
			"<div class='pop_sep_short'>&nbsp;</div>"+
		  "</div>"+
		"</li>"+
		"<li id='hm_rel_ca'>Released (current &amp; out of print)</li>"+
		"<li id='hm_rel_c'>Current</li>"+
		"<li id='hm_rel_o'>Out of Print</li>"+
		"<li id='hm_rel_a'>Announced</li>"+
		"<li id='hm_rel_n'>Not Announced</li>"+
		"<li id='hm_rel_d'>Release delayed</li>"+
		"<li id='hm_rel_x'>Release cancelled</li>"+
		"<li id='hm_rel_u'>Unknown</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-year'>"+
	  "<ul>"+
		"<li>"+
		  "<div class='pop_div'>"+
			"Please enter a 4-digit year."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"2-digit years will be interpreted as being 19xx or 20xx."+
			"<div class='pop_sep_long'>&nbsp;</div>"+
			"If you enter multiple values separated by spaces or commas (&quot;,&quot;) we will seach for titles"+
			"matching any of them."+
		  "</div>"+
		"</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-language' onclick='onMenuClick(this)'>"+
	  "<ul id='help_lan'>"+
		"<li>"+
		  "<div style='margin-top:2px'>"+
			"Please select a language<br />associated with:"+
			"<div class='pop_sep_short'>&nbsp;</div>"+
			"<form id='mform_lan' name='mform_lan' style='margin-top:1px; margin-bottom:4px'>"+
			  "<input type='radio' name='ropt' value='r' onclick='Search.setAppend(\"lan\",false)'>Replace"+
			  "<input type='radio' name='ropt' value='a' onclick='Search.setAppend(\"lan\",true)'>Append<br />"+
			"</form>"+
			"<div class='pop_sep_short'>&nbsp;</div>"+
		  "</div>"+
		"</li>"+
		"<li>The Americas"+
		  "<ul>"+
			"<li id='hm_lan_en'><a><strong>English</strong></a></li>"+
			"<li id='hm_lan_fr'>French</li>"+
			"<li id='hm_lan_br'>Portuguese-Brazilian</li>"+
			"<li id='hm_lan_es'><a><strong>Spanish</strong></a></li>"+
		  "</ul>"+
		"</li>"+
		"<li>Europe"+
		  "<ul>"+
			"<li id='hm_lan_bg'>Bulgarian</li>"+
			"<li id='hm_lan_ve'>Catalan</li>"+
			"<li id='hm_lan_cz'>Czech</li>"+
			"<li id='hm_lan_dk'>Danish</li>"+
			"<li id='hm_lan_nl'>Dutch</li>"+
			"<li id='hm_lan_en'><a><strong>English</strong></a></li>"+
			"<li id='hm_lan_et'>Estonian</li>"+
			"<li id='hm_lan_fi'>Finnish</li>"+
			"<li id='hm_lan_fr'><a><strong>French</strong></a></li>"+
			"<li id='hm_lan_ge'>Georgian</li>"+
			"<li id='hm_lan_de'><a><strong>German</strong></a></li>"+
			"<li id='hm_lan_gr'>Greek</li>"+
			"<li id='hm_lan_hu'>Hungarian</li>"+
			"<li id='hm_lan_is'>Icelandic</li>"+
			"<li id='hm_lan_it'><a><strong>Italian</strong></a></li>"+
			"<li id='hm_lan_lv'>Latvian</li>"+
			"<li id='hm_lan_lt'>Lithuanian</li>"+
			"<li id='hm_lan_mk'>Macedonian</li>"+
			"<li id='hm_lan_no'>Norwegian</li>"+
			"<li id='hm_lan_pl'>Polish</li>"+
			"<li id='hm_lan_pt'>Portuguese</li>"+
			"<li id='hm_lan_rm'>Romani</li>"+
			"<li id='hm_lan_ro'>Romanian</li>"+
			"<li id='hm_lan_ru'>Russian</li>"+
			"<li id='hm_lan_sc'>Serbo-Croatian</li>"+
			"<li id='hm_lan_sk'>Slovak</li>"+
			"<li id='hm_lan_si'>Slovenian</li>"+
			"<li id='hm_lan_es'><a><strong>Spanish</strong></a></li>"+
			"<li id='hm_lan_se'>Swedish</li>"+
			"<li id='hm_lan_uk'>Ukrainian</li>"+
			"<li id='hm_lan_il'>Yiddish</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Asia"+
		  "<ul>"+
			"<li id='hm_lan_ar'>Arabic</li>"+
			"<li id='hm_lan_am'>Armenian</li>"+
			"<li id='hm_lan_id'>Bahasa-Indonesia</li>"+
			"<li id='hm_lan_my'>Bahasa-Malaysia</li>"+
			"<li>Chinese"+
			  "<ul>"+
				"<li id='hm_lan_ct'>Cantonese</li>"+
				"<li id='hm_lan_ho'>Hokkien</li>"+
				"<li id='hm_lan_ma'>Mandarin</li>"+
				"<li id='hm_lan_cs'>Shanghainese</li>"+
				"<li id='hm_lan_tw'>Taiwanese</li>"+
			  "</ul>"+
			"</li>"+
			"<li id='hm_lan_fa'>Farsi</li>"+
			"<li id='hm_lan_ph'>Filipino</li>"+
			"<li id='hm_lan_he'>Hebrew</li>"+
			"<li>Associated with India"+
			  "<ul>"+
				"<li id='hm_lan_hi'>Hindi</li>"+
				"<li id='hm_lan_bn'>Bengali</li>"+
				"<li id='hm_lan_ml'>Malayalam</li>"+
				"<li id='hm_lan_pu'>Punjabi</li>"+
				"<li id='hm_lan_ta'>Tamil</li>"+
				"<li id='hm_lan_te'>Telugu</li>"+
				"<li id='hm_lan_ur'>Urdu</li>"+
				"<li id='hm_lan_in'>Others</li>"+
			  "</ul>"+
			"</li>"+
			"<li id='hm_lan_jp'><a><strong>Japanese</strong></a></li>"+
			"<li id='hm_lan_kz'>Kazakh</li>"+
			"<li id='hm_lan_kh'>Khmer</li>"+
			"<li id='hm_lan_kr'>Korean</li>"+
			"<li id='hm_lan_ku'>Kurdish</li>"+
			"<li id='hm_lan_mn'>Mongolian</li>"+
			"<li id='hm_lan_th'>Thai</li>"+
			"<li id='hm_lan_tr'>Turkish</li>"+
			"<li id='hm_lan_vi'>Vietnamese</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Others"+
		  "<ul>"+
			"<li id='hm_lan_nz'>Aramaic</li>"+
			"<li id='hm_lan_eo'>Esperanto</li>"+
			"<li id='hm_lan_la'>Latin</li>"+
			"<li id='hm_lan_sl'><a><strong>Silent</strong></a></li>"+
			"<li id='hm_lan_ot'>Others</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_lan_un'>Unknown Language</li>"+
		"<li></li>"+
		"<li id='hm_lan_xx'>None</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-picture' onclick='onMenuClick(this)'>"+
	  "<ul id='help_pic'>"+
		"<li>"+
		  "<div style='margin-top:2px'>"+
			"Please select a picture status<br />from the list below."+
			"<div class='pop_sep_short'>&nbsp;</div>"+
			"<form id='mform_pic' name='mform_pic' style='margin-top:1px; margin-bottom:4px'>"+
			  "<input type='radio' name='ropt' value='r' onclick='Search.setAppend(\"pic\",false)'>Replace"+
			  "<input type='radio' name='ropt' value='a' onclick='Search.setAppend(\"pic\",true)'>Append<br />"+
			"</form>"+
			"<div class='pop_sep_short'>&nbsp;</div>"+
		  "</div>"+
		"</li>"+
		"<li id='hm_pic_y'>DVD Cover Art</li>"+
		"<li id='hm_pic_p'>Film Poster</li>"+
		"<li id='hm_pic_n'>No Picture</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-source' onclick='onMenuClick(this)'>"+
	  "<ul id='help_src'>"+
		"<li>"+
		  "<div style='margin-top:2px'>"+
			"Please select a source<br />from the list below."+
			"<div class='pop_sep_short'>&nbsp;</div>"+
			"<form id='mform_src' name='mform_src' style='margin-top:1px; margin-bottom:4px'>"+
			  "<input type='radio' name='ropt' value='r' onclick='Search.setAppend(\"src\",false)'>Replace"+
			  "<input type='radio' name='ropt' value='a' onclick='Search.setAppend(\"src\",true)'>Append<br />"+
			"</form>"+
			"<div class='pop_sep_short'>&nbsp;</div>"+
		  "</div>"+
		"</li>"+
		"<li id='hm_src_a'>DVD Package</li>"+
		"<li id='hm_src_i'>Part of DVD Package</li>"+
		"<li id='hm_src_e'>DVD Package Bonus Disc</li>"+
		"<li id='hm_src_c'>Audio CD Bonus Disc</li>"+
		"<li id='hm_src_g'>Game Bonus Disc</li>"+
		"<li id='hm_src_b'>Book Bonus Disc</li>"+
		"<li id='hm_src_m'>Magazine Bonus Disc</li>"+
		"<li id='hm_src_o'>Other Product Bonus Disc</li>"+
		"<li id='hm_src_t'>Theatrical or Broadcast</li>"+
	  "</ul>"+
	"</li>"+

	"<li id='menu-search-options'>"+
	  "<ul id='search_options'>"+
	    "<li>"+
	      "<div style='margin:4px 0 4px 0;width:320px'>"+
			"<form id='mform_search' name='mform_search' action='javascript:void(0)'>"+
			  "<div class='h1' style='color:#0066b2;font-size:12px;font-weight:bold'>Search Preferences</div>"+
			  "<table>"+
				"<tr>"+
				  "<td style='text-align:right;white-space:nowrap;color:#0066b2;padding:10px 4px 0 10px'>"+
					"Region:"+
				  "</td>"+
				  "<td style='padding:10px 0 0 0'>"+
					"<select id='myregion'>"+
					  "<option value='us'>U.S. and Canada</option>"+
					  "<option value='uk'>U.K.</option>"+
					  "<option value='eu'>Europe and Africa</option>"+
					  "<option value='la'>Latin America</option>"+
					  "<option value='as'>Russia, China, and most of Asia</option>"+
					  "<option value='se'>Southeast Asia</option>"+
					  "<option value='jp'>Japan</option>"+
					  "<option value='au'>Australia and New Zealand</option>"+
					  "<option value='z'>Region 0</option>"+
					  "<option value='1'>Region 1</option>"+
					  "<option value='1,A,0'>Regions 1, A, and 0</option>"+
					  "<option value='2'>Region 2</option>"+
					  "<option value='2,B,0'>Regions 2, B, and 0</option>"+
					  "<option value='3'>Region 3</option>"+
					  "<option value='4'>Region 4</option>"+
					  "<option value='5'>Region 5</option>"+
					  "<option value='6'>Region 6</option>"+
					  "<option value='A'>Region A</option>"+
					  "<option value='B'>Region B</option>"+
					  "<option value='C'>Region C</option>"+
					  "<option value='all'>All regions and countries</option>"+
					"</select>"+
				  "</td>"+
				"</tr>"+
				"<tr>"+
				  "<td style='text-align:right;white-space:nowrap;color:#0066b2;padding:4px 4px 0 10px'>"+
					"Media:"+
				  "</td>"+
				  "<td style='padding:4px 0 0 0'>"+
					"<select id='mymedia'>"+
					  "<option value='all'>All</option>"+
					  "<option value='d'>DVD</option>"+
					  "<option value='b'>Blu-ray</option>"+
					  "<option value='h,c,t'>HD DVD</option>"+
					  "<option value='a,p,o'>Not announced + others</option>"+
					"</select>"+
				  "</td>"+
				"</tr>"+
			  "</table>"+

			  "<div style='white-space:nowrap;color:#0066b2;padding:6px 0 2px 6px'>"+
				"<input type='checkbox' id='isearch' checked='checked' /> "+
				"Autocomplete and search preview (1 second delay)"+
			  "</div>"+
			  "<div style='white-space:nowrap;color:#0066b2;padding:0 0 2px 6px'>"+
				"<input type='checkbox' id='expert' /> "+
				"Multiple search criteria: the &quot;More &gt;&gt;&quot; option"+
			  "</div>"+
			  "<div id='flipexclpins'>"+
				"<div style='white-space:nowrap;color:#0066b2;padding:0 0 2px 6px'>"+
				  "<input type='checkbox' id='flipexcl' /> "+
				  "Flip &quot;exclude mine&quot; / &quot;include mine&quot; when clicking on it"+
				"</div>"+
				"<div style='white-space:nowrap;color:#0066b2;padding:0 0 8px 6px'>"+
				  "<input type='checkbox' id='pins' /> "+
				  "Allow pinning: sticky criteria"+
				"</div>"+
			  "</div>"+
			  "<div style='text-align:right;padding:0 0 4px 0'>"+
				"<input type='button' value='Restore defaults' onclick='SearchMenuPrep.restoreDefaults()' style='width:128px;margin-right:10px'>"+
				"<input type='button' value='Save' onclick='SearchMenuPrep.saveOptions()' style='width:72px;margin-right:10px'>"+
				"<input type='button' value='Cancel' onclick='Context.close()' style='width:72px'>"+
			  "</div>"+
			"</form>"+
	      "</div>"+
	    "</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

