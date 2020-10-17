/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulCountry(s) // f_echo_ul_country
{
    s.s +=
	"<li id='menu-country-no' onclick='onMenuClick(this)'>"+
	  "<ul id='help_cno'>"+
	    "<li>The Americas"+
	      "<ul>"+
			"<li id='hm_cno_us'><a><span style='color:blue'>U.S.</span></a></li>"+
			"<li id='hm_cno_ar'>Argentina</li>"+
			"<li id='hm_cno_br'>Brazil</li>"+
			"<li id='hm_cno_ca'>Canada</li>"+
			"<li id='hm_cno_cl'>Chile</li>"+
			"<li id='hm_cno_cu'>Cuba</li>"+
			"<li id='hm_cno_mx'>Mexico</li>"+
	      "</ul>"+
	    "</li>"+
	    "<li>Europe"+
	      "<ul>"+
			"<li id='hm_cno_at'>Austria</li>"+
			"<li id='hm_cno_be'>Belgium</li>"+
			"<li id='hm_cno_hr'>Croatia</li>"+
			"<li id='hm_cno_cz'>Czech Republic</li>"+
			"<li id='hm_cno_dk'>Denmark</li>"+
			"<li id='hm_cno_ee'>Estonia</li>"+
			"<li id='hm_cno_fi'>Finland</li>"+
			"<li id='hm_cno_fr'><a><span style='color:blue'>France</span></a></li>"+
			"<li id='hm_cno_de'><a><span style='color:blue'>Germany</span></a></li>"+
			"<li id='hm_cno_gr'>Greece</li>"+
			"<li id='hm_cno_hu'>Hungary</li>"+
			"<li id='hm_cno_is'>Iceland</li>"+
			"<li id='hm_cno_it'><a><span style='color:blue'>Italy</span></a></li>"+
			"<li id='hm_cno_lt'>Lithuania</li>"+
			"<li id='hm_cno_mk'>Macedonia</li>"+
			"<li id='hm_cno_nl'>Netherlands</li>"+
			"<li id='hm_cno_no'>Norway</li>"+
			"<li id='hm_cno_pl'>Poland</li>"+
			"<li id='hm_cno_pt'>Portugal</li>"+
			"<li id='hm_cno_ro'>Romania</li>"+
			"<li id='hm_cno_ru'>Russian Federation</li>"+
			"<li id='hm_cno_rs'>Serbia</li>"+
			"<li id='hm_cno_sk'>Slovakia</li>"+
			"<li id='hm_cno_si'>Slovenia</li>"+
			"<li id='hm_cno_es'>Spain</li>"+
			"<li id='hm_cno_se'>Sweden</li>"+
			"<li id='hm_cno_ch'>Switzerland</li>"+
			"<li id='hm_cno_uk'><a><span style='color:blue'>U.K.</span></a></li>"+
	      "</ul>"+
	    "</li>"+
	    "<li>Asia & Oceania"+
	      "<ul>"+
			"<li id='hm_cno_au'><a><span style='color:blue'>Australia</span></a></li>"+
			"<li id='hm_cno_cn'>China</li>"+
			"<li id='hm_cno_hk'><a><span style='color:blue'>Hong Kong</span></a></li>"+
			"<li id='hm_cno_in'>India</li>"+
			"<li id='hm_cno_id'>Indonesia</li>"+
			"<li id='hm_cno_il'>Israel</li>"+
			"<li id='hm_cno_jp'>Japan</li>"+
			"<li id='hm_cno_my'>Malaysia</li>"+
			"<li id='hm_cno_nz'>New Zealand</li>"+
			"<li id='hm_cno_ph'>Philippines</li>"+
			"<li id='hm_cno_sg'>Singapore</li>"+
			"<li id='hm_cno_kr'>South Korea</li>"+
			"<li id='hm_cno_tw'><a><span style='color:blue'>Taiwan</span></a></li>"+
			"<li id='hm_cno_th'>Thailand</li>"+
			"<li id='hm_cno_tr'>Turkey</li>"+
	      "</ul>"+
	    "</li>"+
	    "<li>Africa"+
	      "<ul>"+
			"<li id='hm_cno_za'>South Africa</li>"+
	      "</ul>"+
	    "</li>"+
	    "<li id='hm_cno_un'>Unknown Country</li>"+
	    "<li></li>"+
	    "<li id='hm_cno_xx'>None</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

