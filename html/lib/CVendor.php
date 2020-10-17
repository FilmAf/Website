<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

class CVendor
{
	// Make sure to change Vendor.cpp, UnitTest_SnippetPriceOne.cpp, vd.php, CVendor.php, CAdvert.php, mil-dvd-price.js if the below changes
	// 'amz.cn' => array('disp'=>'Amz.cn','pixe'=>''),

	public		  $ma_price		   = array();
	public		  $mn_price		   = 0;
	public		  $mn_vendor_flags = 0;
	public static $ma_pcmp		   = array('amz', 'deep', 'emp', 'ovr');
	public static $ma_vendors	   = array(
		'amz'	 => array(
			'disp'=>'Amazon',
			'pcmp'=>'amz',
			'spic'=>'http://dv1.us/rt/l1-amz-us.gif',
			'lnk0'=>'http://www.amazon.com/exec/obidos/redirect?path=tg/browse/-/130',
			'lnk1'=>'http://www.amazon.com/exec/obidos/ASIN/',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'price_00',
			'cndx'=>0,
			'pixe'=>''),
		'amz.ca' => array(
			'disp'=>'Amz.ca',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'http://www.amazon.ca/',
			'lnk1'=>'http://www.amazon.ca/exec/obidos/ASIN/',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''),
		'amz.uk' => array(
			'disp'=>'Amz.uk',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'http://www.amazon.co.uk/',
			'lnk1'=>'http://www.amazon.co.uk/exec/obidos/ASIN/',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''),
		'amz.fr' => array(
			'disp'=>'Amz.fr',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'http://www.amazon.fr/',
			'lnk1'=>'http://www.amazon.fr/exec/obidos/ASIN/',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''),
		'amz.de' => array(
			'disp'=>'Amz.de',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'http://www.amazon.de/',
			'lnk1'=>'http://www.amazon.de/exec/obidos/ASIN/',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''),
		'amz.it' => array(
			'disp'=>'Amz.it',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'http://www.amazon.it/',
			'lnk1'=>'http://www.amazon.it/exec/obidos/ASIN/',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''),
		'amz.es' => array(
			'disp'=>'Amz.es',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'http://www.amazon.es/',
			'lnk1'=>'http://www.amazon.es/exec/obidos/ASIN/',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''),
		'amz.jp' => array(
			'disp'=>'Amz.jp',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'http://www.amazon.co.jp/',
			'lnk1'=>'http://www.amazon.co.jp/exec/obidos/ASIN/',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''),
		'deep'	 => array(
			'disp'=>'Deep Discount',
			'pcmp'=>'ddd',
			'spic'=>'http://dv1.us/rt/l1-ddd.gif',
			'lnk0'=>'http://www.deepdiscount.com',
			'lnk1'=>'',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'price_01',
			'cndx'=>1,
			'pixe'=>''),
		'emp'	 => array(
			'disp'=>'DVD Empire',
			'pcmp'=>'exp',
			'spic'=>'http://dv1.us/rt/l1-empire.gif',
			'lnk0'=>'http://www.dvdempire.com/',
			'lnk1'=>'',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'price_02',
			'cndx'=>2,
			'pixe'=>''),
		'ovr'	 => array(
			'disp'=>'Overstock',
			'pcmp'=>'ovr',
			'spic'=>'http://dv1.us/rt/l1-overstock.gif',
			'lnk0'=>'http://www.overstock.com/',
			'lnk1'=>'',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'price_03',
			'cndx'=>3,
			'pixe'=>''),
		'ebay'	 => array(
			'disp'=>'eBay',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'http://www.ebay.com/',
			'lnk1'=>'',
			'lnk2'=>'.',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''),
		'bno'	 => array(
			'disp'=>'Barnes&amp;Noble',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'https://www.barnesandnoble.com/b/movies-tv/_/N-8qh',
			'lnk1'=>'',
			'lnk2'=>'.',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''),
		'imd'	 => array(
			'disp'=>'Imdb',
			'pcmp'=>'',
			'spic'=>'',
			'lnk0'=>'',
			'lnk1'=>'http://www.imdb.com/title/tt',
			'lnk2'=>'',
			'ship'=>'',
			'colu'=>'',
			'cndx'=>-1,
			'pixe'=>''));

	function getIndex($vd)
	{
		return CVendor::$ma_vendors[$vd]['cndx'];
	}

	function getHome($vd)
	{
		return CVendor::$ma_vendors[$vd]['lnk0'];
	}

	function getLink($vd, $sku)
	{
		if ( CVendor::$ma_vendors[$vd]['lnk1'] )
			return CVendor::$ma_vendors[$vd]['lnk1'] . $sku . CVendor::$ma_vendors[$i]['lnk2'];
		return '';
	}

	function __construct()
	{
		$this->mn_price = count(CVendor::$ma_pcmp);
		$this->ma_price = array();
		for ( $i = 0 ; $i < $this->mn_price ; $i++ )
		{
			$s_key = CVendor::$ma_pcmp[$i];
			$this->ma_price[$i] = array(
				'key' =>$s_key,
				'disp'=>CVendor::$ma_vendors[$s_key]['disp'],
				'pcmp'=>CVendor::$ma_vendors[$s_key]['pcmp'],
				'spic'=>CVendor::$ma_vendors[$s_key]['spic'],
				'pixe'=>CVendor::$ma_vendors[$s_key]['pixe'],
				'ship'=>CVendor::$ma_vendors[$s_key]['ship'],
				'colu'=>CVendor::$ma_vendors[$s_key]['colu'],
				'link'=>"/vd.php?vd={$s_key}",
				'in'  =>true);
		}

		$s_exclude = dvdaf3_getvalue('excl', DVDAF3_COOKIE|DVDAF3_LOWER);
		$n_flags   = 0;
		for ( $i = $this->mn_price - 1 ; $i >= 0 ; $i-- )
		{
			$n_flags <<= 1;
			if ( strpos($s_exclude, $this->ma_price[$i]['pcmp']) !== false )
				$this->ma_price[$i]['in'] = false;
			else
				$n_flags |= 1;
		}
		$this->mn_vendor_flags = $n_flags;
    }

    function addVendorsOne(&$res)
    {
		$res['vd-total'] = $this->mn_price;
		for ( $i = 0 ; $i < $this->mn_price ; $i++ )
		{
			$res['vd-'.$i.'-key']  = $this->ma_price[$i]['key'];
			$res['vd-'.$i.'-disp'] = $this->ma_price[$i]['disp'];
			$res['vd-'.$i.'-spic'] = $this->ma_price[$i]['spic'];
			$res['vd-'.$i.'-ship'] = $this->ma_price[$i]['ship'];
			$res['vd-'.$i.'-colu'] = $this->ma_price[$i]['colu'];
			$res['vd-'.$i.'-pixe'] = $this->ma_price[$i]['pixe'];
		}
		$res['vd-flags'] = $this->mn_vendor_flags;
	}

	function addVendorsMany(&$res)
	{
		for ( $i = 0 ; $i < $this->mn_price ; $i++ )
			$res['vd-'.$i.'-colu'] = $this->ma_price[$i]['colu'];

		$res['vd-total'] = $this->mn_price;
		$res['vd-flags'] = $this->mn_vendor_flags;
	}
}

?>
