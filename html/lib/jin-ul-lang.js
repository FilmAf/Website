/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulLang(s) // f_echo_ul_lang
{
    s.s +=
	"<li id='menu-language-no' onclick='onMenuClick(this)'>"+
	  "<ul id='help_lno'>"+
	    "<li>The Americas"+
	      "<ul>"+
		"<li id='hm_lno_en'><a><span style='color:blue'>English</span></a></li>"+
		"<li id='hm_lno_fr'>French</li>"+
		"<li id='hm_lno_br'>Portuguese-Brazilian</li>"+
		"<li id='hm_lno_es'><a><span style='color:blue'>Spanish</span></a></li>"+
	      "</ul>"+
	    "</li>"+
	    "<li>Europe"+
	      "<ul>"+
		"<li id='hm_lno_bg'>Bulgarian</li>"+
		"<li id='hm_lno_ve'>Catalan</li>"+
		"<li id='hm_lno_cz'>Czech</li>"+
		"<li id='hm_lno_dk'>Danish</li>"+
		"<li id='hm_lno_nl'>Dutch</li>"+
		"<li id='hm_lno_en'><a><span style='color:blue'>English</span></a></li>"+
		"<li id='hm_lno_et'>Estonian</li>"+
		"<li id='hm_lno_fi'>Finnish</li>"+
		"<li id='hm_lno_fr'><a><span style='color:blue'>French</span></a></li>"+
		"<li id='hm_lno_ge'>Georgian</li>"+
		"<li id='hm_lno_de'><a><span style='color:blue'>German</span></a></li>"+
		"<li id='hm_lno_gr'>Greek</li>"+
		"<li id='hm_lno_hu'>Hungarian</li>"+
		"<li id='hm_lno_is'>Icelandic</li>"+
		"<li id='hm_lno_it'><a><span style='color:blue'>Italian</span></a></li>"+
		"<li id='hm_lno_lv'>Latvian</li>"+
		"<li id='hm_lno_lt'>Lithuanian</li>"+
		"<li id='hm_lno_mk'>Macedonian</li>"+
		"<li id='hm_lno_no'>Norwegian</li>"+
		"<li id='hm_lno_pl'>Polish</li>"+
		"<li id='hm_lno_pt'>Portuguese</li>"+
		"<li id='hm_lno_rm'>Romani</li>"+
		"<li id='hm_lno_ro'>Romanian</li>"+
		"<li id='hm_lno_ru'>Russian</li>"+
		"<li id='hm_lno_sc'>Serbo-Croatian</li>"+
		"<li id='hm_lno_sk'>Slovak</li>"+
		"<li id='hm_lno_si'>Slovenian</li>"+
		"<li id='hm_lno_es'><a><span style='color:blue'>Spanish</span></a></li>"+
		"<li id='hm_lno_se'>Swedish</li>"+
		"<li id='hm_lno_uk'>Ukrainian</li>"+
		"<li id='hm_lno_il'>Yiddish</li>"+
	      "</ul>"+
	    "</li>"+
	    "<li>Asia"+
	      "<ul>"+
		"<li id='hm_lno_ar'>Arabic</li>"+
		"<li id='hm_lno_am'>Armenian</li>"+
		"<li id='hm_lno_id'>Bahasa-Indonesia</li>"+
		"<li id='hm_lno_my'>Bahasa-Malaysia</li>"+
		"<li>Chinese"+
		  "<ul>"+
		    "<li id='hm_lno_ct_ch'>Cantonese</li>"+
		    "<li id='hm_lno_ho_ch'>Hokkien</li>"+
		    "<li id='hm_lno_ma_ch'>Mandarin</li>"+
			"<li id='hm_lno_cs_ch'>Shanghainese</li>"+
		    "<li id='hm_lno_tw_ch'>Taiwanese</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_lno_fa'>Farsi</li>"+
		"<li id='hm_lno_ph'>Filipino</li>"+
		"<li id='hm_lno_he'>Hebrew</li>"+
		"<li>Associated with India"+
		  "<ul>"+
		    "<li id='hm_lno_hi_in'>Hindi</li>"+
			"<li id='hm_lno_bn_in'>Bengali</li>"+
		    "<li id='hm_lno_ml_in'>Malayalam</li>"+
		    "<li id='hm_lno_pu_in'>Punjabi</li>"+
		    "<li id='hm_lno_ta_in'>Tamil</li>"+
		    "<li id='hm_lno_te_in'>Telugu</li>"+
		    "<li id='hm_lno_ur_in'>Urdu</li>"+
		    "<li id='hm_lno_in_in'>Others</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_lno_jp'><a><span style='color:blue'>Japanese</span></a></li>"+
		"<li id='hm_lno_kz'>Kazakh</li>"+
		"<li id='hm_lno_kh'>Khmer</li>"+
		"<li id='hm_lno_kr'>Korean</li>"+
		"<li id='hm_lno_ku'>Kurdish</li>"+
		"<li id='hm_lno_mn'>Mongolian</li>"+
		"<li id='hm_lno_th'>Thai</li>"+
		"<li id='hm_lno_tr'>Turkish</li>"+
		"<li id='hm_lno_vi'>Vietnamese</li>"+
	      "</ul>"+
	    "</li>"+
	    "<li>Others"+
	      "<ul>"+
		"<li id='hm_lno_nz'>Aramaic</li>"+
		"<li id='hm_lno_eo'>Esperanto</li>"+
		"<li id='hm_lno_la'>Latin</li>"+
/*		"<li id='hm_lno_kl'>Klingon</li>"+ */
		"<li id='hm_lno_sl'><a><span style='color:blue'>Silent</span></a></li>"+
		"<li id='hm_lno_ot'>Others</li>"+
	      "</ul>"+
	    "</li>"+
	    "<li id='hm_lno_un'>Unknown Language</li>"+
	    "<li></li>"+
	    "<li id='hm_lno_xx'>None</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

