/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

function ulGenre(s) // f_echo_ul_genre
{
    s.s +=
	"<li id='menu-genre-no' onclick='onMenuClick(this)'>"+
	  "<ul id='help_gno'>"+
	    "<li>Action-Adventure"+
	      "<ul>"+
			"<li id='hm_gno_10999'><a><strong>Action-Adventure</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_10100'>Comedy</li>"+
			"<li id='hm_gno_10200'>Crime</li>"+
			"<li id='hm_gno_10300'>Disaster</li>"+
			"<li id='hm_gno_10400'>Epic</li>"+
			"<li id='hm_gno_10500'>Espionage</li>"+
			"<li id='hm_gno_10600'>Martial Arts</li>"+
			"<li id='hm_gno_10700'>Military</li>"+
			"<li id='hm_gno_10750'>Samurai</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Animation"+
		  "<ul>"+
			"<li id='hm_gno_13999'><a><strong>Animation</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_13100'>Cartoons</li>"+
			"<li id='hm_gno_13300'>Family</li>"+
			"<li id='hm_gno_13600'>Mature</li>"+
			"<li id='hm_gno_13700'>Puppetry &amp; Stop-Motion</li>"+
			"<li id='hm_gno_13800'>Sci-Fi</li>"+
			"<li id='hm_gno_13900'>Superheroes</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Anime"+
		  "<ul>"+
			"<li id='hm_gno_16999'><a><strong>Anime</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_16200'>Action</li>"+
			"<li id='hm_gno_16250'>Comedy</li>"+
			"<li id='hm_gno_16300'>Drama</li>"+
			"<li id='hm_gno_16400'>Fantasy</li>"+
			"<li id='hm_gno_16500'>Horror</li>"+
			"<li id='hm_gno_16600'>Mahou Shoujo (Magical Girls)</li>"+
			"<li id='hm_gno_16700'>Martial Arts</li>"+
			"<li id='hm_gno_16750'>Mecha (Giant Robots)</li>"+
			"<li id='hm_gno_16800'>Mo&#233; (Cute Girls, Romance)</li>"+
			"<li id='hm_gno_16850'>Romance</li>"+
			"<li id='hm_gno_16900'>Sci-Fi</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Comedy"+
		  "<ul>"+
			"<li id='hm_gno_20999'><a><strong>Comedy</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_20100'>Dark</li>"+
			"<li id='hm_gno_20200'>Farce</li>"+
			"<li id='hm_gno_20300'>Horror</li>"+
			"<li id='hm_gno_20400'>Romantic</li>"+
			"<li id='hm_gno_20600'>Satire</li>"+
			"<li id='hm_gno_20650'>Sci-Fi</li>"+
			"<li id='hm_gno_20700'>Screwball</li>"+
			"<li id='hm_gno_20750'>Sitcom</li>"+
			"<li id='hm_gno_20800'>Sketches &amp; Stand-Up</li>"+
			"<li id='hm_gno_20850'>Slapstick</li>"+
			"<li id='hm_gno_20900'>Teen</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Documentary"+
		  "<ul>"+
			"<li id='hm_gno_24999'><a><strong>Documentary</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_24100'>Biography</li>"+
			"<li id='hm_gno_24200'>Crime</li>"+
			"<li id='hm_gno_24250'>Culture</li>"+
			"<li id='hm_gno_24270'>Entertainment</li>"+
			"<li id='hm_gno_24300'>History</li>"+
			"<li id='hm_gno_24400'>Nature</li>"+
			"<li id='hm_gno_24500'>Propaganda</li>"+
			"<li id='hm_gno_24600'>Religion</li>"+
			"<li id='hm_gno_24700'>Science</li>"+
			"<li id='hm_gno_24750'>Social</li>"+
			"<li id='hm_gno_24800'>Sports</li>"+
			"<li id='hm_gno_24900'>Travel</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Drama"+
		  "<ul>"+
			"<li id='hm_gno_28999'><a><strong>Drama</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_28100'>Courtroom</li>"+
			"<li id='hm_gno_28150'>Crime</li>"+
			"<li id='hm_gno_28200'>Docudrama</li>"+
			"<li id='hm_gno_28400'>Melodrama</li>"+
			"<li id='hm_gno_28600'>Period</li>"+
			"<li id='hm_gno_28800'>Romance</li>"+
			"<li id='hm_gno_28900'>Sports</li>"+
			"<li id='hm_gno_28950'>War</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Educational"+
		  "<ul>"+
			"<li id='hm_gno_32999'><a><strong>Educational</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_32200'>Children</li>"+
			"<li id='hm_gno_32700'>School</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Erotica"+
		  "<ul>"+
			"<li id='hm_gno_36999'><a><strong>Erotica</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_36100'>Hentai</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gno_39999'>Experimental</li>"+
		"<li>Exploitation"+
		  "<ul>"+
			"<li id='hm_gno_41999'><a><strong>Exploitation</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_41100'>Blaxploitation</li>"+
			"<li id='hm_gno_41300'>Nazisploitation</li>"+
			"<li id='hm_gno_41400'>Nunsploitation</li>"+
			"<li id='hm_gno_41500'>Pinku Eiga</li>"+
			"<li id='hm_gno_41600'>Sexploitation</li>"+
			"<li id='hm_gno_41700'>Shockumentary</li>"+
			"<li id='hm_gno_41800'>WIP</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gno_43999'>Fantasy</li>"+
		"<li id='hm_gno_47999'>Film Noir</li>"+
		"<li>Horror"+
		  "<ul>"+
			"<li id='hm_gno_55999'><a><strong>Horror</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_55050'>Anthology</li>"+
			"<li id='hm_gno_55250'>Creature &amp; Animal</li>"+
			"<li id='hm_gno_55300'>ESP &amp; Ghosts</li>"+
			"<li id='hm_gno_55350'>Eurotrash</li>"+
			"<li id='hm_gno_55400'>Exploitation</li>"+
			"<li id='hm_gno_55450'>Gialli</li>"+
			"<li id='hm_gno_55500'>Gore &amp; Shock</li>"+
			"<li id='hm_gno_55550'>Gothic</li>"+
			"<li id='hm_gno_55700'>Possession &amp; Satan</li>"+
			"<li id='hm_gno_55800'>Shockumentary</li>"+
			"<li id='hm_gno_55850'>Slasher &amp; Survival</li>"+
			"<li id='hm_gno_55900'>Vampires</li>"+
			"<li id='hm_gno_55950'>Zombies &amp; Infected</li>"+
			"<li id='hm_gno_55960'>Other Undead</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Music"+
		  "<ul>"+
			"<li id='hm_gno_59999'><a><strong>Music</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_59300'>Live in Concert</li>"+
			"<li id='hm_gno_59700'>Music Videos</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gno_62999'>Musical</li>"+
		"<li>Performing Arts"+
		  "<ul>"+
			"<li id='hm_gno_66999'><a><strong>Performing Arts</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_66100'>Circus</li>"+
			"<li id='hm_gno_66300'>Concerts</li>"+
			"<li id='hm_gno_66500'>Dance</li>"+
			"<li id='hm_gno_66700'>Operas</li>"+
			"<li id='hm_gno_66900'>Theater</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Sci-Fi"+
		  "<ul>"+
			"<li id='hm_gno_70999'><a><strong>Sci-Fi</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_70100'>Alien</li>"+
			"<li id='hm_gno_70200'>Alternate Reality</li>"+
			"<li id='hm_gno_70250'>Apocalyptic</li>"+
			"<li id='hm_gno_70300'>Cyber Punk</li>"+
			"<li id='hm_gno_70400'>Kaiju (Giant Monster)</li>"+
			"<li id='hm_gno_70500'>Lost Worlds</li>"+
			"<li id='hm_gno_70550'>Military</li>"+
			"<li id='hm_gno_70600'>Other Worlds</li>"+
			"<li id='hm_gno_70800'>Space</li>"+
			"<li id='hm_gno_70850'>Space Horror</li>"+
			"<li id='hm_gno_70870'>Superheroes</li>"+
			"<li id='hm_gno_70900'>Utopia &amp; Dystopia</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gno_73999'>Short</li>"+
		"<li>Silent"+
		  "<ul>"+
			"<li id='hm_gno_76999'><a><strong>Silent</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_76100'>Animation</li>"+
			"<li id='hm_gno_76300'>Horror</li>"+
			"<li id='hm_gno_76500'>Melodrama</li>"+
			"<li id='hm_gno_76700'>Slapstick</li>"+
			"<li id='hm_gno_76800'>Western</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Sports"+
		  "<ul>"+
			"<li id='hm_gno_80999'><a><strong>Sports</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_80100'>Baseball</li>"+
			"<li id='hm_gno_80130'>Basketball</li>"+
			"<li id='hm_gno_80170'>Biking</li>"+
			"<li id='hm_gno_80200'>Fitness</li>"+
			"<li id='hm_gno_80250'>Football</li>"+
			"<li id='hm_gno_80300'>Golf</li>"+
			"<li id='hm_gno_80350'>Hockey</li>"+
			"<li id='hm_gno_80400'>Martial Arts</li>"+
			"<li id='hm_gno_80450'>Motor Sports</li>"+
			"<li id='hm_gno_80500'>Olympics</li>"+
			"<li id='hm_gno_80600'>Skateboard</li>"+
			"<li id='hm_gno_80700'>Skiing</li>"+
			"<li id='hm_gno_80800'>Soccer</li>"+
			"<li id='hm_gno_80850'>Tennis</li>"+
			"<li id='hm_gno_80900'>Wrestling</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Suspense"+
		  "<ul>"+
			"<li id='hm_gno_84999'><a><strong>Suspense</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_84400'>Mystery</li>"+
			"<li id='hm_gno_84700'>Thriller</li>"+
		  "</ul>"+
		"</li>"+
		"<li>War"+
		  "<ul>"+
			"<li id='hm_gno_88999'><a><strong>War</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_88200'>US Civil War</li>"+
			"<li id='hm_gno_88300'>World War I</li>"+
			"<li id='hm_gno_88400'>World War II</li>"+
			"<li id='hm_gno_88500'>Korea</li>"+
			"<li id='hm_gno_88600'>Vietnam</li>"+
			"<li id='hm_gno_88700'>Post-Cold War</li>"+
			"<li id='hm_gno_88900'>Other</li>"+
		  "</ul>"+
		"</li>"+
		"<li>Western"+
		  "<ul>"+
			"<li id='hm_gno_91999'><a><strong>Western</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_91400'>Epic</li>"+
			"<li id='hm_gno_91700'>Singing Cowboy</li>"+
			"<li id='hm_gno_91800'>Spaghetti</li>"+
		  "</ul>"+
		"</li>"+
		"<li id='hm_gno_95999'>DVD Audio</li>"+
		"<li>Other"+
		  "<ul>"+
			"<li id='hm_gno_98999'><a><strong>Other</strong></a></li>"+
			"<li></li>"+
			"<li id='hm_gno_98200'>Digital Comic Books</li>"+
			"<li id='hm_gno_98250'>Game Shows</li>"+
			"<li id='hm_gno_98300'>Games</li>"+
		  "</ul>"+
	    "</li>"+
	    "<li id='hm_gno_99999'><a><strong>Unspecified Genre</strong></a></li>"+
	    "<li></li>"+
	    "<li id='hm_gno_00000'>None</li>"+
	  "</ul>"+
	"</li>";
};

/* --------------------------------------------------------------------- */

