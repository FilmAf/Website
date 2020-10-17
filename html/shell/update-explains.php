#!/usr/local/bin/php -c /etc/httpd/conf/php-cli.ini
<?php
// vi: noet ai ts=4 sw=4

require 'identify.php';
require $gs_root.'/lib/CTrace.php';
require $gs_root.'/lib/CSqlMysql.php';

function defineExplains(&$x, &$y)
{
	$tbeg = "<div class='explain'><h1>";
	$tend = "</h1><p>";
	$sbeg = "</p><h2>";
	$send = "</h2><p>";
	$br   = "</p><p>";
	$end  = "</p></div>";

	// ===================================================================================
    $x['sponsors'] =
	"{$tbeg}Understanding Sponsors vs. Advertisers{$tend}".
		"Sponsors and advertisers help pay FilmAf's bills.".
	"{$sbeg}Advertisers{$send}".
		"We go to Google or other companies and tell them we have ad space. Advertisers ".
		"go to them and buy ad space on FilmAf. Some ads can be great deals and others ".
		"not so much.".
	  $br.
		"Advertisers pay us when you click on an ad.".
	"{$sbeg}Sponsors{$send}".
		"Sponsors pay us when you buy products or services after arriving at their site ".
		"using our links. Note that it is not only DVDs, but anything from baby clothes ".
		"and children books to power tools and espresso machines. We pick sponsors from ".
		"companies we ourselves use products or services and are happy with.".
	  $end;
	// ===================================================================================
	$x['www_titles_disks'] =
	"{$tbeg}Titles, Discs, Pages, etc.....{$tend}".
		"It is common for FilmAf to return results in multiple pages. The number of ".
		"listings in a page is configurable by each user. Members can select to see ".
		"up to 200, Supporting Members up to 500, and Sponsor Members up to 1,000 ".
		"listings in a page.".
	  $br.
		"&quot;<strong>Showing 1 - 50 of 148</strong>&quot; means that you have 148 ".
		"listings of which 1 through 50 are being shown and the rest are be in the ".
		"next pages.".
	  $br.
		"You may also see &quot;<strong>Showing 1 - 50 of 148 (213 titles, 264 discs)".
		"</strong>&quot;. This is because some listings, like box sets, may contain ".
		"multiple titles. The number of discs is simply the number of shiny round ".
		"thingies that you would have in those.".
	"{$sbeg}How are titles counted?{$send}".
		"A title is a full length feature. Multiple cuts of the same feature still ".
		"count as a single title.".
	  $br.
		"For TV series each season counts as a title.".
	  $br.
		"If a listing contains neither a full length feature nor a whole season of a ".
		"TV series it counts as one title.".
	"{$sbeg}Setting page size and navigating to page X{$send}".
		"<img src='http://dv1.us/d1/00/sz00.gif' /> Allows you to define your preferred ".
		"page size.".
	  $br.
		"<img src='http://dv1.us/d1/00/dp00.gif' /> Is shown when your results have lots ".
		"of pages and lets you jump to any particular one.".
	  $end;
	// ===================================================================================
	$x['www_dvd_cart'] =
	"{$tbeg}Price Comparison and Shopping Cart{$tend}".
		"You can get pricing information from reputable retailers in two ways.".
	  $br.
		"<img src='http://dv1.us/d1/00/bd00.png' /> Shows you prices for one DVD at a time.".
	  $br.
		"<img src='http://dv1.us/d1/00/bc00.png' /> Adds one ".
		"or more listings to your shopping cart. You can then see prices for all those ".
		"listings by hitting the &quot;Show Cart / Compare Prices&quot; link.".
	  $br.
		"<strong>Multiple carts can be saved in your browser</strong>. For example you ".
		"could make a larger list, save it, trim it, save it with a different name, load ".
		"the larger list, try a different configuration, and so forth.".
	  $end;
	// ===================================================================================
	$x['www_whos_got_it'] =
	"{$tbeg}Who&#39;s got it{$tend}".
		"Who's got it shows you who else has that title in their collection.".
	  $br.
		"Click on the DVD picture and select &quot;Who&#39;s got it?&quot; from the popup ".
		"context menu. You will see all members that own that DVD. The resulting list ".
		"contains the member&#39;s name, the folder in which the title belongs, and how ".
		"many DVDs the person has in that folder and in his/her collection.".
	  $end;
	// ===================================================================================
	$x['a_dvd_title'] =
	"<div class='explain'>".
	  "<div class='exp-nav'>".
		"<a href='javascript:void(Explain.nav(\\\"a_dvd_title:examples\\\"))'>Examples</a> ".
//		"<a href='javascript:void(Explain.nav(\\\"a_dvd_title:key\\\"))'>Key</a> ".
		"<a href='javascript:void(Explain.nav(\\\"a_dvd_title:article\\\"))'>Articles</a> ".
		"<a href='javascript:void(Explain.nav(\\\"a_dvd_title:caps\\\"))'>Capitalization</a>".
	  "</div>".
	  "<h1>DVD Titles</h1>".

	  "<h2>DVDs with a single feature film</h2>".
	  "<p>".
		"<strong>&lt;title&gt;</strong> - <strong>&lt;edition&gt;</strong>".
		" (<strong>&lt;notes&gt;</strong>) - <strong>&lt;audio&gt;</strong>".
		" (<strong>&lt;format&gt;</strong>)<br/>".
		"( <strong>&lt;title in original language&gt;</strong> )<br/>".
		"( <strong>&lt;alternate titles&gt;</strong> )<br/>".
		"+ <strong>&lt;bonus materials&gt;</strong>".
	"{$sbeg}DVDs with multiple feature films <strong>WITH</strong> a set or box name (one title per line){$send}".
		"<strong>&lt;collection name&gt;</strong><br/>".
		"- <strong>&lt;title 1&gt;</strong> ( <strong>&lt;alternates&gt;</strong> ) (<strong>&lt;year&gt;</strong>) - (<strong>&lt;notes&gt;</strong>) - <strong>&lt;audio&gt;</strong> (<strong>&lt;format&gt;</strong>)<br/>".
		"- <strong>&lt;title 2&gt;</strong> ( <strong>&lt;alternates&gt;</strong> ) (<strong>&lt;year&gt;</strong>) - (<strong>&lt;notes&gt;</strong>) - <strong>&lt;audio&gt;</strong> (<strong>&lt;format&gt;</strong>)<br/>".
		"- <strong>&lt;title 3&gt;</strong> ( <strong>&lt;alternates&gt;</strong> ) (<strong>&lt;year&gt;</strong>) - (<strong>&lt;notes&gt;</strong>) - <strong>&lt;audio&gt;</strong> (<strong>&lt;format&gt;</strong>)<br/>".
		"+ <strong>&lt;bonus materials&gt;</strong>".
	"{$sbeg}DVDs with multiple feature films <strong>WITHOUT</strong> a set or box name (all titles in the first line separated by &quot;/&quot;){$send}".
		"<strong>&lt;title 1&gt;</strong> ( <strong>&lt;alternates&gt;</strong> ) (<strong>&lt;year&gt;</strong>) - (<strong>&lt;notes&gt;</strong>) - <strong>&lt;audio&gt;</strong> (<strong>&lt;format&gt;</strong>) /<br/>".
		"<strong>&lt;title 2&gt;</strong> ( <strong>&lt;alternates&gt;</strong> ) (<strong>&lt;year&gt;</strong>) - (<strong>&lt;notes&gt;</strong>) - <strong>&lt;audio&gt;</strong> (<strong>&lt;format&gt;</strong>) -<br/>".
		"<strong>&lt;edition&gt;</strong><br/>".
		"+ <strong>&lt;bonus materials&gt;</strong>".
	  $br.
		"Please always observe <a href='javascript:void(Explain.nav(\\\"a_dvd_title:article\\\"))'>".
		"article placement</a> and <a href='javascript:void(Explain.nav(\\\"a_dvd_title:caps\\\"))'>".
		"capitalization</a> when specifying a title or its alternates.".
	  $br.
		"For a single film the year is usually not included in the title field. There".
		"&#39;s a separate area to specify the year and it&#39;s automatically added to ".
		"the end of the first line of the listing.".
	  $br.
		"FilmAf supports most character sets including the full Latin alphabet, Chinese, ".
		"Japanese, Korean, Arabic, Cyrillic, etc. Romanization (representation of a ".
		"written word or spoken speech with the Latin alphabet) is also supported and ".
		"follows the standards adopted by imdb (The <a href='http://www.imdb.com/' ".
		"target='_blank'>Internet Movie Database</a>) when possible.".
	  $end;
	// ===================================================================================
	$x['a_dvd_title:examples'] =
	"<div class='explain'>".
	  "<div class='exp-nav'><a href='javascript:void(Explain.nav(\\\"a_dvd_title\\\"))'>Back to DVD Titles</a></div>".
	  "<h1>Examples</h1>".

	  "<h2>Titles for DVDs with a single feature film</h2>".
	  "<p>".
		"<ul>".
		  "<li>".
			"Lord of the Rings: The Fellowship of the Ring, The - Special Extended DVD ".
			"Edition - New Line Platinum Series (208 Minute Version) - DTS".
		  "</li>".
		  "<li>".
			"Zombie 2: Day of the Dead (Edited) (Dubbed) (Full Screen)".
		  "</li>".
		  "<li>".
			"Alexandre (175 Minute U.S. Theatrical Version) - DTS-HD Master Audio (HD DVD)".
		  "</li>".
		  "<li>".
			"Danny the Dog - Ultimate Edition (Steelbook) (102 Minute European Version) - DTS".
		  "</li>".
		  "<li>".
			"Crouching Tiger, Hidden Dragon (2000)<br/>".
			"( 卧虎藏龙 )<br/>".
			"( Wo hu cang long )".
		  "</li>".
		  "<li>".
			"Chinese Connection, The (1972)<br/>".
			"( Jing wu men )<br/>".
			"( Fist of Fury )".
		  "</li>".
		"</ul>".
	"{$sbeg}Titles for DVDs with multiple feature films <strong>WITH</strong> a set or box name{$send}".
		"<ul>".
		  "<li>".
			"Eisenstein: The Sound Years - The Criterion Collection<br/>".
			"- Alexander Nevsky ( Александр Невский ) ( Aleksandr Nevsky ) (1938)<br/>".
			"- Ivan the Terrible, Part 1 ( Иван Грозный I ) ( Ivan Groznyj I ) (1945)<br/>".
			"- Ivan the Terrible, Part 2 ( Иван Грозный II ) ( Ivan Groznyj II ) (1958)<br/>".
			"+ Bezhin Meadow ( Бежин луг ) ( Bezhin lug ) (Short) (1937)".
		  "</li>".
		"</ul>".
	"{$sbeg}Titles for DVDs with multiple feature films <strong>WITHOUT</strong> a set or box name{$send}".
		"<ul>".
		  "<li>".
			"Fargo (1996) / Rain Man (1988) - Double Feature".
		  "</li>".
		  "<li>".
			"Fast Bullets (1936) / Brothers of the West (1937) - Tom Tyler Double Feature".
		  "</li>".
		"</ul>".
	  $end;
	// ===================================================================================
	/*
	$x['a_dvd_title:key'] =
	"<div class='explain'>".
	  "<div class='exp-nav'><a href='javascript:void(Explain.nav(\\\"a_dvd_title\\\"))'>Back to DVD Titles</a></div>".
	  "<h1>DVD Title Key</h1>".
	  "<p>".
		"<ul>".
		  "<li>".
			"<strong>&lt;title&gt;</strong> ".
			"The title as listed on the spine.".
		  "</li>".
		  "<li>".
			"<strong>&lt;edition&gt;</strong> ".
			"Collection or edition as listed on the cover (Special Edition, Collector".
			"&#39;s Edition, Criterion Collection, etc.)".
		  "</li>".
		  "<li>".
			"<strong>&lt;notes&gt;</strong> ".
			"Dubbed, Full Screen, Incorrectly Framed Transfer, Remastered, xx minutes ".
			"running time, etc. We do not specify the original aspect ratio, i.e. ".
			"widescreen, only changes to it.".
		  "</li>".
		  "<li>".
			"<strong>&lt;audio&gt;</strong> ".
			"Audio format information such as DTS or, for high definition formats, ".
			"Dolby TrueHD, DTS-HD Master Audio or Uncompressed PCM.".
		  "</li>".
		  "<li>".
			"<strong>&lt;format&gt;</strong> ".
			"DVD Audio, Blu-ray, HD DVD, HD DVD/DVD Combo. Includes notes for Bonus DVD ".
			"(DVD included with CD&#39;s or books or video games)".
		  "</li>".
		  "<li>".
			"<strong>&lt;title in original language&gt;</strong> ".
			"Should be present if the title listed in the first line was not in the ".
			"original language surrounded by &quot;(&lt;space&gt;&quot; and &quot;&lt;".
			"space&gt;)&quot;.".
		  "</li>".
		  "<li>".
			"<strong>&lt;alternate titles&gt;</strong> ".
			"Non-language based variations of a title, one per line, surrounded by ".
			"&quot;(&lt;space&gt;&quot; and &quot;&lt;space&gt;)&quot;.".
		  "</li>".
		  "<li>".
			"<strong>&lt;alternates&gt;</strong> ".
			"A sequence of alternate titles starting with the title in the original ".
			"language, if applicable, in a single line with each title surrounded by ".
			"&quot;(&lt;space&gt;&quot; and &quot;&lt;space&gt;)&quot;.".
		  "</li>".
		  "<li>".
			"<strong>&lt;bonus materials&gt;</strong> ".
			"Extra features are listed last, preceded by &quot;+&quot;. We list shorts ".
			"and documentaries which are also listed in imdb, music videos, and extras ".
			"such as toys, music CD&#39;s and artboxes. Formats for common items is ".
			"represented below.".
			"<div style='padding:3px 0 0 12px'>".
			  "+ Name of the Short, The (Short) (2009)<br/>".
			  "+ Making of &lt;film&gt;, The (Documentary) (2009)<br/>".
			  "+ Artist - Title of the Song (Music Video)<br/>".
			  "+ Group, The - The Title of the Song (Live)<br/>".
			  "+ Soundtrack CD<br/>".
			  "+ Artbox name and spec".
			"</div>".
		  "</li>".
		"</ul>".
	  $end;
	*/
	// ===================================================================================
	$x['a_dvd_title:article'] =
	"<div class='explain'>".
	  "<div class='exp-nav'><a href='javascript:void(Explain.nav(\\\"a_dvd_title\\\"))'>Back to DVD Titles</a></div>".
	  "<h1>Article Placement</h1>".
	  "<p>".
		"In all languages articles are placed at the end of a title.".
		"<ul>".
		  "<li>Vanishing, The</li>".
		  "<li>Testament des Dr. Mabuse, Das </li>".
		  "<li>Grande illusion, La</li>".
		  "<li>Strada, La</li>".
		"</ul>".
	"{$sbeg}Articles in common languages{$send}".
		"<ul>".
		  "<li><a href='javascript:void(Explain.nav(\\\"a_dvd_title:article_en\\\"))'>English</a></li>".
		  "<li><a href='javascript:void(Explain.nav(\\\"a_dvd_title:article_de\\\"))'>German</a></li>".
		  "<li><a href='javascript:void(Explain.nav(\\\"a_dvd_title:article_fr\\\"))'>French</a></li>".
		  "<li><a href='javascript:void(Explain.nav(\\\"a_dvd_title:article_it\\\"))'>Italian</a></li>".
		"</ul>".
	"{$sbeg}Tag Lines{$send}".
		"When &quot;tag lines&quot; are part of the title (usually separated by &quot;:".
		"&quot;), the article is transposed to the end of that &quot;tag line&quot;.".
		"<ul>".
		  "<li>Signore degli anelli: La compagnia dell'anello, Il</li>".
		  "<li>Lord of the Rings: The Fellowship of the Ring, The</li>".
		"</ul>".
	"{$sbeg}Exception: &quot;Borrowing&quot; articles from other languages{$send}".
		"Sometimes a title will &quot;borrow&quot; words from other languages, as ".
		"opposed to the title &quot;being&quot; in the other language. When that happens ".
		"we do not treat these as &quot;articles.&quot; For example:".
		"<ul>".
		  "<li>La Blue Girl</li>".
		  "<li>El Hazard</li>".
		  "<li>La Bamba</li>".
		  "<li>Le Mans</li>".
		  "<li>La Cucaracha</li>".
		  "<li>La Femme Nikita</li>".
		"</ul>".
	  $br.
		"Note that it can get a bit complicated as in La Femme Nikita -- the film is ".
		"actually French, but the original title is Nikita and La Femme Nikita is the ".
		"US distribution title, thus &quot;borrowing&quot; French words for a title in ".
		"English.".
	  $end;
	// ===================================================================================
	$x['a_dvd_title:article_en'] =
	"<div class='explain'>".
	  "<div class='exp-nav'><a href='javascript:void(Explain.nav(\\\"a_dvd_title:article\\\"))'>Back to Article Placement</a></div>".
	  "<h1>Articles in English</h1>".
		"<table>".
		  "<thead>".
			"<tr><td>Definite article</td></tr>".
		  "</thead>".
		  "<tbody>".
			"<tr><td>the</td></tr>".
		  "</tbody>".
		"</table>".
		"<table>".
		  "<thead>".
			"<tr><td colspan='2'>Indefinite articles</td></tr>".
			"<tr><td>Before a consonant sound</td><td>Before a vowel sound</td></tr>".
		  "</thead>".
		  "<tbody>".
			"<tr><td>a</td><td>an</td></tr>".
		  "</tbody>".
		"</table>".
	"</div>";
	// ===================================================================================
	$x['a_dvd_title:article_de'] =
	"<div class='explain'>".
	  "<div class='exp-nav'><a href='javascript:void(Explain.nav(\\\"a_dvd_title:article\\\"))'>Back to Article Placement</a></div>".
	  "<h1>Articles in German</h1>".
		"<table>".
		  "<thead>".
			"<tr><td colspan='5'>Definite articles</td></tr>".
			"<tr><td>&nbsp;</td><td>Nominative</td><td>Accusative</td><td>Dative</td><td>Genitive</td></tr>".
		  "</thead>".
		  "<tbody>".
			"<tr><th>Masculine</td><td>der</td><td>den</td><td>dem</td><td>des</td></tr>".
			"<tr><th>Feminine</td><td>die</td><td>die</td><td>der</td><td>der</td></tr>".
			"<tr><th>Neuter</td><td>das</td><td>das</td><td>dem</td><td>des</td></tr>".
			"<tr><th>Plural</td><td>die</td><td>die</td><td>den</td><td>der</td></tr>".
		  "</tbody>".
		"</table>".
		"<table>".
		  "<thead>".
			"<tr><td colspan='5'>Indefinite articles</td></tr>".
			"<tr><td>&nbsp;</td><td>Nominative</td><td>Accusative</td><td>Dative</td><td>Genitive</td></tr>".
		  "</thead>".
		  "<tbody>".
			"<tr><th>Masculine</td><td>ein</td><td>einen</td><td>einem</td><td>eines</td></tr>".
			"<tr><th>Feminine</td><td>eine</td><td>eine</td><td>einer</td><td>einer</td></tr>".
			"<tr><th>Neuter</td><td>ein</td><td>ein</td><td>einem</td><td>eines</td></tr>".
		  "</tbody>".
		"</table>".
	"</div>";
	// ===================================================================================
	$x['a_dvd_title:article_fr'] =
	"<div class='explain'>".
	  "<div class='exp-nav'><a href='javascript:void(Explain.nav(\\\"a_dvd_title:article\\\"))'>Back to Article Placement</a></div>".
	  "<h1>Articles in French</h1>".
		"<table>".
		  "<thead>".
			"<tr><td colspan='3'>Definite articles</td></tr>".
			"<tr><td>&nbsp;</td><td>Before a consonant</td><td>Before a vowel or mute h</td></tr>".
		  "</thead>".
		  "<tbody>".
			"<tr><th>Masculine</td><td>le</td><td rowspan='2'>l&#39;</td></tr>".
			"<tr><th>Feminine</td><td>la</td></tr>".
			"<tr><th>Plural</td><td colspan='2'>les</td></tr>".
		  "</tbody>".
		"</table>".
		"<table>".
		  "<thead>".
			"<tr><td colspan='2'>Indefinite articles</td></tr>".
		  "</thead>".
		  "<tbody>".
			"<tr><th>Masculine</td><td>un</td></tr>".
			"<tr><th>Feminine</td><td>une</td></tr>".
			"<tr><th>Plural</td><td>des</td></tr>".
		  "</tbody>".
		"</table>".
	  "<p>".
		"Note that we do not move to the end of a title partitive articles (du, de la) ".
		"and articles fused with prepositions (au, à la, aux, du, de la, de l&#39;, des).".
	  $end;
	// ===================================================================================
	$x['a_dvd_title:article_it'] =
	"<div class='explain'>".
	  "<div class='exp-nav'><a href='javascript:void(Explain.nav(\\\"a_dvd_title:article\\\"))'>Back to Article Placement</a></div>".
	  "<h1>Articles in Italian</h1>".
		"<table>".
		  "<thead>".
			"<tr><td colspan='4'>Definite articles</td></tr>".
			"<tr><td width='80px'>&nbsp;</td><td>Before a consonant</td><td>Before an S followed by a consonant, or Z, PS or GN</td><td>Before a vowel</td></tr>".
		  "</thead>".
		  "<tbody>".
			"<tr><th>Masculine</td><td>il</td><td>lo</td><td>l&#39;</td></tr>".
			"<tr><th>Feminine</td><td colspan=2>la</td><td>l&#39;</td></tr>".
			"<tr><th>Plural Masculine</td><td>i</td><td colspan=2>gli</td></tr>".
			"<tr><th>Plural Feminine</td><td colspan=3>le</td></tr></table>".
		  "</tbody>".
		"<table>".
		  "<thead>".
			"<tr><td colspan='4'>Indefinite articles</td></tr>".
			"<tr><td width='80px'>&nbsp;</td><td>Before a consonant</td><td>Before an S followed by a consonant, or Z, PS or GN</td><td>Before a vowel</td></tr>".
		  "</thead>".
		  "<tbody>".
			"<tr><th>Masculine</td><td>un</td><td>uno</td><td>un</td></tr>".
			"<tr><th>Feminine</td><td colspan=2>una</td><td>un&#39;</td></tr>".
		  "</tbody>".
		"</table>".
	"</div>";
	// ===================================================================================
	$x['a_dvd_title:caps'] =
	"<div class='explain'>".
	  "<div class='exp-nav'><a href='javascript:void(Explain.nav(\\\"a_dvd_title\\\"))'>Back to DVD Titles</a></div>".
	  "<h1>Title Capitalization</h1>".
	  "<p>".
		"Capitalization can get very complex. We need something relatively simple, even ".
		"if that means it will not be perfect.".
	"{$sbeg}English{$send}".
		"Sorry, not very simple.".
	  $br.
		"Do the best you can. We all make mistakes with this stuff.".
	  $br.
		"All words are capitalized with the exception of articles (a, an, the), ".
		"prepositions (regardless of length), coordinating conjunctions (and, but, for, ".
		"nor, or, so, yet), and the &quot;to&quot; in an infinitive phrase. However, ".
		"the first and last word in a title are always capitalized.".
	  $br.
		"With &quot;multipurpose words&quot; it can be hard to tell when they are being ".
		"used as a preposition, coordinating conjunction or adverb. There are a bunch ".
		"of rules, but it may be simpler to look at <a href='http://www.dictionary.com' ".
		"target='_blank'>http://www.dictionary.com</a>.".
		"<ul>".
		  "<li>Lord of the Rings, The</li>".
		"</ul>".
	"{$sbeg}German{$send}".
		"All nouns are capitalized. The pronoun Sie (the formal &quot;you&quot;) and ".
		"its derivatives (Ihnen, Ihr) are also capitalized. The other sie(s) (&quot;she".
		"&quot; and &quot;they&quot;) are not... it&#39;s downhill from there, so we ".
		"will leave it at that. The first word of a title is always capitalized.".
		"<ul>".
		  "<li>Stadt sucht einen Mörder, Eine</li>".
		"</ul>".
	"{$sbeg}French and other languages{$send}".
		"Only proper nouns and the first word of a title are capitalized. Yes, this can ".
		"get more complicated too, but we will leave it at that as well.".
		"<ul>".
		  "<li>Raisins de la mort, Les</li>".
		"</ul>".
	  $end;
	// ===================================================================================
	$x['a_film_rel_year'] =
	"{$tbeg}Film Release Year{$tend}".
		"The release year should not be specified when a DVD contains multiple feature ".
		"films as it would be automatically appended to the first line of a DVD title.".
	  $br.
		"A film is released when it becomes available for consumption, but frequently ".
		"not wide consumption. It usually occurs at a film festival or other event in ".
		"which distribution deals are made. Please see the <a href='http://en.wikipedia.".
		"org/wiki/Film_release' target='_blank'>Wikipedia</a> for a more formal ".
		"definition.".
	  $end;
	// ===================================================================================
	$x['a_film_rel_dd'] =
	"{$tbeg}Film Release Date{$tend}".
		"A film is released when it becomes available for consumption, but frequently ".
		"not wide consumption. It usually occurs at a film festival or other event in ".
		"which distribution deals are made. Please see the <a href='http://en.wikipedia.".
		"org/wiki/Film_release' target='_blank'>Wikipedia</a> for a more formal ".
		"definition.".
	  $br.
		"For DVDs with <strong>multiple feature films</strong> use the <strong>earliest ".
		"date</strong>.".
	  $end;
	// ===================================================================================
	$x['a_genre'] =
	"{$tbeg}Genre{$tend}".
		"The genre used for this DVD categorization.".
	  $br.
		"<a href='/utils/benefits.html' target='_blank'>FilmAf Star Members</a> have the ".
		"ability to overwrite the genres on titles in his/her collection.".
	  $end;
	// ===================================================================================
	$x['a_source'] =
	"{$tbeg}Source{$tend}".
		"<ul>".
		  "<li><strong>DVD Package</strong> - a retail package.</li>".
		  "<li><strong>Part of DVD Package</strong> - an individual disk of a retail package.</li>".
		  "<li><strong>DVD Package Bonus Disc</strong> - a bonus disk of a retail package.</li>".
		  "<li><strong>Audio CD Bonus Disc</strong> - a DVD or BD which comes as a bonus disk when you get the <strong>CD</strong>.</li>".
		  "<li><strong>Game Bonus Disc</strong> - a DVD or BD which comes as a bonus disk when you get the <strong>Game</strong>.</li>".
		  "<li><strong>Book Bonus Disc</strong> - a DVD or BD which comes as a bonus disk when you get the <strong>Book</strong>.</li>".
		  "<li><strong>Magazine Bonus Disc</strong> - a DVD or BD which comes as a bonus disk when you get the <strong>Magazine</strong>.</li>".
		  "<li><strong>Other Product Bonus Disc</strong> - a DVD or BD which comes as a bonus disk when you get <strong>something else</strong>.</li>".
		"</ul>".
	  $end;
	// ===================================================================================
	$x['a_media_type'] =
	"{$tbeg}Media Type{$tend}".
		"<ul>".
		  "<li><strong>DVD</strong> - This is a DVD.</li>".
		  "<li><strong>Blu-ray</strong> - This is in Blu-ray Disk format.</li>".
		  "<li><strong>Blu-ray 3D</strong> - This is in Blu-ray Disk <strong>3D</strong> format.</li>".
		  "<li><strong>Blu-ray/DVD Combo</strong> - These are editions with the Blu-ray and DVD formats on a <strong>single side of the same disk</strong>.</li>".
		  "<li><strong>BD-R</strong> - This is in Blu-ray Disc Recordable format. This is a read-only format sometimes used by smaller publishers as it can be done with a regular computer.</li>".
		  "<li><strong>DVD-R</strong> - This is in DVD-R format. This is a read-only format sometimes used by smaller publishers as it can be done with a regular computer.</li>".
		  "<li><strong>HD DVD</strong> - This is in HD DVD format.</li>".
		  "<li><strong>HD DVD/DVD Combo</strong> - This is an HD DVD on one side and regular DVD on the other.</li>".
		  "<li><strong>HD DVD/DVD TWIN Format</strong> - Like the HD DVD/DVD Combo, but both are on the same side of the disk.</li>".
		  "<li><strong>DVD Audio</strong> - This is the <strong>sound only</strong> DVD-A disk.</li>".
		  "<li><strong>Placeholder</strong> - The media type for this listing is unknown or undefined. Generally used for a film not yet released or announced.</li>".
		  "<li><strong>Other</strong> - A media type other than the ones above.</li>".
		"</ul>".
	  $end;
	// ===================================================================================
	$x['a_region_mask'] =
	"{$tbeg}Region Encoding{$tend}".
		"Region encoding is used by publishers to control the price, content, and ".
		"timing of releases in different markets. Generally you need to match the media ".
		"region to the player&#39;s unless one of them is region-free.".
	  $br.
		"<ul>".
		  "<li><strong>DVD</strong> - Regions 1 through 6 designate local markets. ".
			"Region 7 is used for screener and other restricted editions.  Region 8 is ".
			"used for international venues such as aircraft and cruise ships. Region 0 ".
			"is an informal designation meaning all regions. You can get more info on ".
			"DVD region codes on <a href='http://en.wikipedia.org/wiki/DVD_region_code' ".
			"target='_blank'>Wikipedia</a>. Note that besides having a region ".
			"compatible player you also need to be able to handle the <strong>video ".
			"mode</strong> of a DVD which can be <strong>480i (NTSC)</strong> or ".
			"<strong>576i (PAL)</strong>. <img style='position:relative;left:-20px;".
			"padding-top:4px' src='http://dv1.us/d1/region-small-dvd.gif'></li>".
		  "<li><strong>Blu-ray</strong> - Regions A through C designate local ".
			"markets. Region Free, Region 0 are informal designations meaning that the ".
			"release has no region code and plays in all regions. You can get more info ".
			"on BD regions and profiles on <a href='http://en.wikipedia.org/wiki/".
			"Blu-ray_Disc' target='_blank'>Wikipedia</a>.<img style='position:relative;".
			"left:-20px;padding-top:4px' src='http://dv1.us/d1/region-small-bd.gif'>".
			"</li>".
		  "<li><strong>HD DVD</strong> - HD DVDs do not have region restrictions</li>".
		  "<li><strong>DVD Audio</strong> - DVD Audios do not have region restrictions</li>".
		"</ul>".
	  $end;
	// ===================================================================================
	$x['a_country'] =
	"{$tbeg}DVD Country{$tend}".
		"The country or countries where this particular DVD edition is being released.".
	"{$sbeg}Notes{$send}".
		"If you are considering getting DVDs from other countries you might want to ".
		"consider:".
		"<ul>".
		  "<li><strong>Region encoding</strong> -- will my player handle it?</li>".
		  "<li><strong>Video Mode</strong> -- can my player and TV work the image? DVDs are encoded in 480i (NTSC) or 576i (PAL)? Can my player convert it?</li>".
		  "<li><strong>Presence of subtitles</strong> -- will I understand the dialog?</li>".
		  "<li><strong>Language in the bonus materials</strong> -- do bonus materials matter?</li>".
		"</ul>".
	  $end;
	// ===================================================================================
	$x['a_rel_status'] =
	"{$tbeg}DVD Release Status{$tend}".
		"<ul>".
		  "<li><strong>Current</strong> - the DVD is in production and can be easily acquired.</li>".
		  "<li><strong>Out of Print</strong> - the DVD is no longer being produced and cannot be easily acquired.</li>".
		  "<li><strong>Announced</strong> - we have a date into the future where this DVD edition is expected to come out.</li>".
		  "<li><strong>Not Announced</strong> - there is no known date for the release of this feature on DVD.</li>".
		  "<li><strong>Release Delayed</strong> - the expected release date has passed and we do not have a new one yet.</li>".
		  "<li><strong>Release Cancelled</strong> - this release has been cancelled.</li>".
		  "<li><strong>Unknown</strong> - we do not known.</li>".
		"</ul>".
	  $end;
	// ===================================================================================
	$x['a_dvd_rel_dd'] =
	"{$tbeg}DVD Release Date{$tend}".
		"Date when this DVD edition was first released. In the U.S. it tends to be a ".
		"Tuesday.".
	  $br.
		"If this DVD has not been relased yet, then this is the date in which it is ".
		"expected to be released.".
	  $end;
	// ===================================================================================
	$x['a_asin'] =
	"{$tbeg}Amazon ASIN{$tend}".
		"The Amazon Standard Identification Number is a <strong>10-character</strong> ".
		"code of letters and numbers that uniquely identifies a product in an Amazon ".
		"web site.".
	"{$sbeg}Example{$send}".
		"<a href='http://www.amazon.com/exec/obidos/ASIN/B00008976Y/dvdaficionado' ".
		"target='_blank'>http://www.amazon.com/exec/obidos/ASIN/<strong>B00008976Y".
		"</strong></a> points to the first film in Kieslowski&#39;s Trois couleurs ".
		"trilogy: &quot;Bleu.&quot;".
	  $end;
	// ===================================================================================
	$x['a_imdb_id'] =
	"{$tbeg}Imdb Links{$tend}".
		"Imdb Ids are <strong>7-digit</strong> set of numbers that uniquely identify a ".
		"film in the Internet Movie Database.".
	"{$sbeg}Example{$send}".
		"<a href='http://www.imdb.com/title/tt0108394/' target='_blank'>http://www.".
		"imdb.com/title/tt<strong>0108394</strong>/</a> points to the first film in ".
		"Kieslowski&#39;s Trois couleurs trilogy: &quot;Bleu.&quot;".
	  $end;
	// ===================================================================================
	$x['a_director'] =
	"{$tbeg}Director{$tend}".
		"The directors of this feature or features in the order the titles are listed.".
	  $br.
		"Please use the &quot;<strong>search</strong>&quot; button to avoid differences ".
		"in spelling.".
	  $end;
	// ===================================================================================
	$x['a_publisher'] =
	"{$tbeg}DVD Publisher{$tend}".
		"The publisher for this DVD edition. For newer films this is sometimes is the ".
		"same company associated with the film distribution and production, but that is ".
		"not always the case.".
	  $br.
		"Please use the &quot;<strong>search</strong>&quot; button to avoid differences ".
		"in spelling.".
	  $end;
	// ===================================================================================
	$x['a_num_titles'] =
	"{$tbeg}Number of Titles{$tend}".
		"A title is a full length feature. Multiple cuts of the same feature still ".
		"count as a single title.".
	  $br.
		"For TV series each season counts as a title.".
	  $br.
		"If a listing contains neither a full length feature nor a whole season of a ".
		"TV series it counts as one title.".
	  $end;
	// ===================================================================================
	$x['a_upc'] =
	"{$tbeg}UPC{$tend}".
		"<img src='http://dv1.us/d1/upc-sample.gif' style='float:right;margin:0 0 4px ".
		"10px'>Generally a Universal Product Code uniquely identifies a product. A UPC ".
		"for a US product is <strong>12-digit</strong> long (if you only see 10, there ".
		"are probably additional smaller digits to the right and left of the 10 you ".
		"found).".
	  $br.
		"<strong>Non-US UPCs</strong> usually have 13 or more digits.".
	  $br.
		"Sometimes <strong>ISBNs</strong> look like 10 or 13 digit UPCs, but they are ".
		"not. You can get more info on <a href='http://en.wikipedia.org/wiki/Universal_".
		"Product_Code' target='_blank'>UPCs</a> and <a href='http://en.wikipedia.org/".
		"wiki/Isbn' target='_blank'>ISBNs</a> on Wikipedia.".
	  $end;
	// ===================================================================================
	$x['a_num_disks']		= "{$tbeg}Number of Disks{$tend}The number of shiny round thingies.{$end}";
	$x['a_dvd_oop_dd']		= "{$tbeg}Out of Print Date{$tend}Date when this edition went out of print.{$end}";
	$x['a_orig_language']	= "{$tbeg}Original Language{$tend}The original language or languages of the feature.". $br. "Do not include the language in which the feature has been dubbed in this field.{$end}";
	$x['a_sku']				= "{$tbeg}SKU{$tend}The Stock-Keeping Unit we keep track of is the code in a publisher&#39;s catalog that uniquely identifies a DVD.{$end}";
	$x['a_list_price']		= "{$tbeg}List Price{$tend}The manufacturer&#39;s suggested retail price for this title. This is <strong>not</strong> the Amazon price which is usually lower.{$end}";
	// ===================================================================================
	$x['u_pub_name']		= "{$tbeg}Publisher name{$tend}Name for this publisher.{$end}";
	$x['u_official_site']	= "{$tbeg}Official Site{$tend}Publisher&#39;s official website.{$end}";
	$x['u_wikipedia']		= "{$tbeg}Wikipedia link{$tend}Wikipedia article for this publisher.{$end}";
	$x['u_pub_id']			= "{$tbeg}Publisher id{$tend}Unique internal identifier for this publisher.{$end}";
	$x['u_version_id']		= "{$tbeg}Current version{$tend}This number goes up every time the data for the publisher changes, though we skip it if the changes are less than a few minutes apart.{$end}";
	$x['u_pub_created_tm']	= "{$tbeg}Created on{$tend}Date and time when this record was first created.{$end}";
	$x['u_pub_updated_tm']	= "{$tbeg}Last updated on{$tend}Date and time when this record was last updated.{$end}";
	$x['u_pub_updated_by']	= "{$tbeg}Last Updated by{$tend}Person who last updated this record.{$end}";
	$x['u_last_justify']	= "{$tbeg}Update justification for last version{$tend}Last reason given for an update.{$end}";
	$x['u_verified_version']= "{$tbeg}Last verified version{$tend}Last version approved by a moderator.{$end}";
	$x['u_pub_verified_tm']	= "{$tbeg}Verified on{$tend}Date and time a moderator looked at and approved the last verified version.{$end}";
	$x['u_pub_verified_by']	= "{$tbeg}Verified by{$tend}Moderator who looked at and approved the last verified version.{$end}";
	$x['zaupdate_justify']	= "{$tbeg}Update justification{$tend}The reason for this request.  This you telling other members why you are making this update.{$end}";
	$x['zaproposer_notes']	= "{$tbeg}Proposer notes{$tend}Additional notes from the person proposing the change to the moderators.{$end}";

//	$x['zurequest_cd']		= "{$tbeg}Request type{$tend}One of the following: Change Request, New Publisher Proposal, or Publisher Deletion Request.{$end}";
//	$x['zuupdate_justify']	= "{$tbeg}Update justification{$tend}The reason for this request.  This you telling other members why you are making this update.{$end}";
//	$x['zudisposition_cd']	= "{$tbeg}Disposition{$tend}One of the following: Approved, Partially Approved, Declined, Withdrawn, or Expired.{$end}";
//	$x['zuproposer_id']		= "{$tbeg}Proposer id{$tend}Person proposing the change.{$end}";
//	$x['zuproposed_tm']		= "{$tbeg}Proposed on{$tend}Date and time the request was first submitted.{$end}";
//	$x['zuupdated_tm']		= "{$tbeg}Last updated on{$tend}Date and time the request was last updated by the person proposing it.{$end}";
//	$x['zuproposer_notes']	= "{$tbeg}Proposer notes{$tend}Additional notes from the person proposing the change to the moderators.{$end}";
//	$x['zureviewer_notes']	= "{$tbeg}Moderator notes{$tend}Comments by the moderator reviewing this submission or its previous version.{$end}";
	// ===================================================================================
	$x['b_user_dvd_rating']	= "{$tbeg}DVD Rating{$tend}Rate this DVD on its transfer quality (image, sound, aspect ratio) and other attributes that are specific to this edition like bonus materials.{$end}";
	$x['b_user_film_rating']= "{$tbeg}Film Rating{$tend}Rate the movie itself irrespective of technical attributes or limitations in this edition.{$end}";
	$x['b_sort_text']		=
	"{$tbeg}Sort Text{$tend}".
		"The text you enter here will be used in place of the DVD Title when sorting ".
		"your collection.".
	  $br.
		"You may use a prefix if you like, but numbers must have the same number of ".
		"digits for them to sort properly (e.g. &quot;zombie-0001&quot;&quot;).".
	  $br.
		"Together with the UPC Import feature this allows you to organize your online ".
		"collection in the same order you have in your shelves for example.".
	  $end;
	$x['b_genre_overwrite']	= "{$tbeg}Genre Override{$tend}If you are a star member you can change this DVD&#39;s genre within your collection.{$end}";
	$x['b_owned_dd']		= "{$tbeg}Date of Purchase{$tend}Since when do you own this title.{$end}";
	// ===================================================================================
	$x['b2retailer']		= "{$tbeg}Retailer{$tend}Where you got it.{$end}";
	$x['b2order_dd']		= "{$tbeg}Order Date{$tend}When you ordered it.{$end}";
	$x['b2order_number']	= "{$tbeg}Order number{$tend}Used it to keep track of what you bought together, etc.{$end}";
	$x['b2price_paid']		= "{$tbeg}Price Paid{$tend}How much you paid for it.{$end}";
	$x['b2trade_loan']		= "{$tbeg}Loaned or For Trading{$tend}Use to indicate if this DVD is out for trading or if you have loaned it to someone{$end}";
	$x['b2loaned_to']		= "{$tbeg}Loaned To{$tend}Who you lent it to.{$end}";
	$x['b2loan_dd']			= "{$tbeg}Date Loaned{$tend}When you lent it.{$end}";
	$x['b2return_dd']		= "{$tbeg}Return Date{$tend}When they are supposed to return it.{$end}";
	$x['b2asking_price']	= "{$tbeg}Asking Price{$tend}How much you want for this DVD is someone were to buy it.{$end}";
	$x['b2custom']			= "{$tbeg}Custom Fields{$tend}Use these 5 fields to store any other information you want.{$end}";
	$x['b2last_watched_dd']	= "{$tbeg}Last Date Watched{$tend}When you last watched this DVD.{$end}";

	// ===================================================================================
	$x['p_pic_type']		=
	"{$tbeg}Picture type{$tend}".
		"Please select the appropriate picture type so that the listing will display it ".
		"correctly.".
	  $br.
		"The distinction between &quot;<strong>cover art</strong>&quot; and &quot;".
		"<strong>movie poster</strong>&quot; is very important because it affects how ".
		"the title gets listed.".
	  $end;

	$x['p_copy_holder']		=
	"{$tbeg}Copyright holder{$tend}".
		"The person or entity holding the copyright to this picture.".
	  $br.
		"At some point we will use this to generate &quot;copyright by...&quot; messages.".
	  $end;

	$x['p_copy_year']		=
	"{$tbeg}Copyright year{$tend}".
		"The year of the copyright.".
	  $br.
		"At some point we will use this to generate &quot;copyright by...&quot; messages.".
	  $end;

	$x['p_suitability_cd']	=
	"{$tbeg}Suitability{$tend}".
		"This was meant to say if a picture had strong content that some people may ".
		"object to. However, the criteria and the means to make displaying these ".
		"optional is not defined yet. At some point we should come up with a better ".
		"name for this. For now please mark it as NC-17 in case you believe some folks ".
		"may object to it.".
	  $br.
		"Note that because of the wide range of people that use FilmAf no &quot;hardcore".
		"&quot; pictures are accepted. The definition of which is at sole discretion ".
		"of the moderators. If a cover art or movie poster does have an explicit image ".
		"it should be airbrushed before uploading. If you can not do that please ".
		"request help from one of the moderators in the &quot;proposer notes&quot; ".
		"field.".
	  $br.
		"Generally pictures at FilmAf should be &quot;worlplace safe.&quot;".
	  $end;

	$x['p_caption']			=
	"{$tbeg}Picture Caption{$tend}".
		"This is meant for pictures other than cover art and movie posters, although at ".
		"some point we may make use of them for those items too. It is a short text ".
		"that contextualizes the image.".
	  $end;

	$x['p_proposer_notes']	=
	"{$tbeg}Proposer notes{$tend}".
		"Additional notes from the person proposing the change to the moderators.".
	  $end;

	$x['p_reviewer_notes']	=
	"{$tbeg}Moderator notes{$tend}".
		"Comments by the moderator reviewing this submission or its previous version.".
	  $end;

	$x['p_rot_degrees']		=
	"{$tbeg}Degrees to rotate picture{$tend}".
		"A positive value will rotate the picture clockwise. A negative value counter-".
		"clockwise. Use this for large adjustments.".
	  $end;

	$x['p_rot_degrees_x']	=
	"{$tbeg}Pixels to rotate picture{$tend}".
		"A positive value will rotate the picture so to &quot;push&quot; the right top ".
		"side of the pictures that many pixels down. A negative value will do the same, ".
		"but in the opposite direction. Use this for small adjustments.".
	  $end;

	$x['p_crop_fuzz']		=
	"{$tbeg}Autocrop fuzz{$tend}".
		"Automatically trims a picture surrounded by a uniform border. A value of 0 ".
		"means do not trim.  As the value increases similar colors will be considered ".
		"for the trim. By the time you reach 100 all colors are considered the same ".
		"and the picture disappears.".
	  $br.
		"A value below 10 usually gets you the results you want.".
	  $br.
		"The left, right, top, and bottom cropping parameters are applied after the ".
		"automatic trim. You might want to set the autocrop with a low value and fine ".
		"tune it the final picture with those 4 values.".
	  $end;

	$x['p_crop_x1']			=
	"{$tbeg}Crop left{$tend}".
		"How many pixel to trim from the left.".
	  $end;

	$x['p_crop_x2']			=
	"{$tbeg}Crop right{$tend}".
		"How many pixel to trim from the right.".
	  $end;

	$x['p_crop_y1']			=
	"{$tbeg}Crop top{$tend}".
		"How many pixel to trim from the top.".
	  $end;

	$x['p_crop_y2']			=
	"{$tbeg}Crop bottom{$tend}".
		"How many pixel to trim from the bottom.".
	  $end;

	$x['p_black_pt']		=
	"{$tbeg}Black point{$tend}".
		"This is used to correct exposure problems including picture haze.".
	  $br.
		"As the value in this field increases you will get darker shadows.".
	  $br.
		"Between the &quot;black point&quot; and &quot;white point&quot; parameters you ".
		"get functionality  similar to &quot;levels&quot; in Photoshop.".
	  $end;

	$x['p_white_pt']		=
	"{$tbeg}White point{$tend}".
		"This is used to correct exposure problems including picture haze.".
	  $br.
		"As the value in this field decreases you will get brighter highlights.".
	  $br.
		"Between the &quot;black point&quot; and &quot;white point&quot; parameters you ".
		"get functionality  similar to &quot;levels&quot; in Photoshop.".
	  $end;

	$x['p_gamma']			=
	"{$tbeg}Gamma{$tend}".
		"Gamma is used to change the midpoint ballance between light and dark.".
	  $br.
		"It makes the whole picture lighter or darker.".
	  $br.
		"it can be used on its own or in combination with the &quot;black point&quot; ".
		"and &quot;white point&quot; parameters.".
	  $end;

/*
	// ===================================================================================
	$x['blog_dvd']			= "{$tbeg}{$end}";
	$x['blog_tub']			= "{$tbeg}{$end}";
	$x['fvid_category']		= "{$tbeg}{$end}";
	$x['fvid_tub']			= "{$tbeg}{$end}";
	$x['profile_about_me']	= "{$tbeg}{$end}";
	$x['profile_city']		= "{$tbeg}{$end}";
	$x['profile_country']	= "{$tbeg}{$end}";
	$x['profile_dob']		= "{$tbeg}{$end}";
	$x['profile_facebook']	= "{$tbeg}{$end}";
	$x['profile_gender']	= "{$tbeg}{$end}";
	$x['profile_homepage']	= "{$tbeg}{$end}";
	$x['profile_my_space']	= "{$tbeg}{$end}";
	$x['profile_name']		= "{$tbeg}{$end}";
	$x['profile_state']		= "{$tbeg}{$end}";
	$x['profile_status']	= "{$tbeg}{$end}";
	$x['profile_youtube']	= "{$tbeg}{$end}";
	$x['wall_dvd']			= "{$tbeg}{$end}";
	$x['wall_tub']			= "{$tbeg}{$end}";
	$x['www_isearch']		= "{$tbeg}{$end}";
	// ===================================================================================
*/
}

function deleteExpains()
{
    CSql::query_and_free("DELETE FROM explain_keyword", 0,__FILE__,__LINE__);
}

function insertExplains(&$a_define, &$a_width)
{
    foreach ( $a_define as $key => $value )
    {
	$n_width = isset($a_width[$key]) ? $a_width[$key] : 0;
	echo "INSERT {$key}\n";
	CSql::query_and_free("INSERT INTO explain_keyword (keyword, descr, width) VALUES  ('{$key}', \"{$value}\", $n_width)", 0,__FILE__,__LINE__);
    }
}

$a_define = array();
$a_width  = array();
echo "Initialize.\n";
defineExplains($a_define, $a_width);
echo "DELETE old explains.\n";
deleteExpains();
insertExplains($a_define, $a_width);
echo "done.\n";

?>
