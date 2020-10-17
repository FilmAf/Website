/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

var DvdListMenuAction =
{
	onClick : function(action) // onMenuClick
	{
		if ( ! action.info || ! action.info.id ) return;
		var id = action.info.id,
			md = id.substr(0,7),
			z  = this;
		
		if ( !(id = id.substr(7)) ) retutn;

		switch ( md )
		{
		case 'cm_imdb':
			DvdListMenuAction._clickSearchImdb(id.substr(1));
			break;
		case 'cm_gen0':
		case 'cm_gen1':
		case 'cm_gen2':
			DvdListMenuAction._clickSearchGenre(id.substr(1),md);
			break;
		case 'cm_dir_':
		case 'cm_pub_':
			DvdListMenuAction._clickSearchText(id, md.substr(3,3));
			break;
		case 'cm_dvd_':
			DvdListMenuAction._clickOneDvd(id);
			break;
		case 'cm_sel_':
			DvdListMenuAction._clickManyDvd(id);
			break;
		default:
			z.filmaf = SearchMenuAction.onClick;
			z.filmaf(action);
			break;
		}
	},

	_clickSearchText : function(ctx, opt)
	{
		if ( ! Filmaf.contextStr ) return;

		switch ( ctx )
		{
		case 'imdb':	Win.openPop(false, 'imdb', Filmaf.baseDomain + '/rt.php?vd=imd' + Filmaf.contextStr, 0, 0, 0, 1); break;
		case 'xp':		DirXp.open(Filmaf.contextStr); break;
		default:		Search.setSearch(ctx, opt, Filmaf.contextStr); break;
		}
	},

	_clickSearchImdb : function(ctx)
	{
		if ( ! Filmaf.contextStr ) return;

//		if ( ctx == 'who' )
//			location.href = Filmaf.baseDomain + '/who.html?dvd=' + Filmaf.contextStr.replace(/^0+/g,'');
//		else
			DvdListMenuAction._clickSearchText(ctx,'imdb');
	},

	_clickSearchGenre : function(ctx,md)
	{
		if ( ! Filmaf.contextStr ) return;

		var m = Filmaf.contextStr.indexOf('-');
		if ( m && md != 'cm_gen0' )
		{
			Filmaf.contextStr = Filmaf.contextStr.substr(0,m);
			if ( md == 'cm_gen1' ) Filmaf.contextStr += '-nosub';
		}
		Search.setSearch(ctx,'genre', Filmaf.contextStr);
	},

	_clickOneDvd : function(id)
	{
		var df = true,
			mv = '';

		switch ( id )
		{
		case 'large':
			switch ( ImgPop.menuContext )
			{
			case 'show':
				ImgPop.show($('zo_'+Filmaf.contextStr), 1);
				break;
			case 'stop showing':
				ImgPop.close(1);
				break;
			case 'show large':
				DvdList.showLargePic(Filmaf.contextStr);
				break;
			}
			break;

		case 'blog':		break;
		case 'pic':			DvdList.picMngt(Filmaf.contextStr);					break;
		case 'prices':		Price.click(Filmaf.contextStr, 0);					break;
		case 'cart_add':	Img.check($('ic_' + Filmaf.contextStr), 1, 1); Cart.click(Filmaf.contextStr, true);  break;
		case 'cart_del':	Img.check($('ic_' + Filmaf.contextStr), 1, 0); Cart.click(Filmaf.contextStr, false); break;
		case 'mine':		DvdList.onePagerFlip(1);							break;
		case 'one':			DvdList.onePagerFlip(0);							break;
		case 'lst':			DvdList.searchByDvdId(Filmaf.contextStr);			break;
		case 'fb_see':		Facebook.see  (Filmaf.contextStr,Filmaf.contextMed);break;
		case 'fb_want':		Facebook.want (Filmaf.contextStr,Filmaf.contextMed);break;
		case 'fb_order':	Facebook.order(Filmaf.contextStr,Filmaf.contextMed);break;
		case 'fb_got':		Facebook.get  (Filmaf.contextStr,Filmaf.contextMed);break;

		case 'delete':
		case 'edit':
		case 'details':
		case 'who':
			df = false;
			// let it fall
		default:
			if ( df )
			{
				if ( (mv = DvdList.folderToMove(id)) )
					id = 'move';
				else
					break;
			}
			DvdList.dvdAction('', Filmaf.contextStr, id, mv);
			break;
		}
	},

	_clickManyDvd : function(id)
	{
		var f  = $(Filmaf.contextForm),
			df = true,
			mv = '';

		if ( ! f ) return;

		switch ( id )
		{
		case 'cart_add':	DvdList.cartAdd(f);			break;
		case 'cart_del':	DvdList.cartDel(f);			break;
		case 'inv':			DvdList.dvdSelect(f,2);		break;
		case 'all':			DvdList.dvdSelect(f,1);		break;
		case 'none':		DvdList.dvdSelect(f,0);		break;
		case 'prices':		DvdList.cartAdd(f); location.href = Filmaf.baseDomain + '/price.html'; break;
		case 'mine':		DvdList.onePagerFlip(1);	break;
		case 'delete':
		case 'edit':
		case 'details':
		case 'who':
			df = false;
			// let it fall
		default:
			if ( df )
			{
				if ( (mv = DvdList.folderToMove(id)) )
					id = 'move';
				else
					break;
			}
			DvdList.dvdAction(Filmaf.contextForm, '', id, mv);
			break;
		}
	}
};

/* --------------------------------------------------------------------- */

