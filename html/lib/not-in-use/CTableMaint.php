<?php

/* ---------------------------------------------------------------------------- *\
 *  Film Aficionado is public domain software. Promotional material images,
 *  if present, are copyrighted by the respective copyright owners and should
 *  only be used under the provisions dictated by those copyright holders.
 *  There are no warranties expressed on implied.
\* ---------------------------------------------------------------------------- */

require $gs_root.'/lib/CDataWindow.php';

class CTableMaint extends CDataWindow
{
	function constructor() // <<--------------------<< 1.0
	{
		parent::constructor();

		$ven = array(array('amz' ,'amz','def'=>'1'), array('amz.ca' ,'amz.ca'     ), array('amz.uk' ,'amz.uk'     ), array('amz.fr' ,'amz.fr'     ),
					 array('amz.de' ,'amz.de'     ), array('amz.it' ,'amz.it'     ), array('amz.es' ,'amz.es'     ), array('amz.cn' ,'amz.cn'     ),
					 array('amz.jp' ,'amz.jp'     ), array('deep'   ,'deep'       ), array('yesa'   ,'yes asia'   ),
					 array('buy'    ,'buy.com'    ), array('plan'   ,'dvd planet' ), array('emp'    ,'dvd empire' ), array('ovr'    ,'overstock'  ),
					 array('fand'   ,'fandango'   ), array('allp'   ,'all posters'), array('disc'   ,'disc card'  ), array('dell'   ,'dell comp'  ),
					 array('goda'   ,'go daddy'   ));
		$fmt = array(array('sky'	,'sky'		,'def'=>'1'),
					 array('text'	,'text'		),
					 array('banner'	,'banner'	),
					 array('rect'	,'rect'		),
					 array('square'	,'square'	));
		$lo1 = array(array('rsky'	,'right sky','def'=>'1'),
					 array('-'      ,'-'        ));
		$loN = array(array('rsky'	,'right sky'),
					 array('-'      ,'-'        ,'def'=>'1'));

		$this->ma_what = array(
		'tit'=>"Advert Maintenance",
		'sql'=>"SELECT * FROM advert ORDER BY vendor, format, width, effective_beg_tm, effective_end_tm, advert_id",
		'myt'=>"advert",
		'tbl'=>array(
					 array('col'=>'vendor'			,'upd'=>'key','lbl'=>'Vendor'			,'show'=>1,'edit1'=>1,'edit'=>0,'inp'=>'sele','mlen'=>   8,'size'=>12,'cell'=>0,'vali'=>'none','opt'=>$ven),
					 array('col'=>'advert_id'		,'upd'=>'aut','lbl'=>'Advert id'		,'show'=>1,'edit1'=>0,'edit'=>0,'inp'=>'text','mlen'=>   8,'size'=> 8,'cell'=>0,'vali'=>'none'),
					 array('col'=>'format'			,'upd'=>'upd','lbl'=>'Format'			,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'sele','mlen'=>  16,'size'=> 8,'cell'=>0,'vali'=>'str' ,'opt'=>$fmt),
					 array('col'=>'width'			,'upd'=>'upd','lbl'=>'Width'			,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'text','mlen'=>   8,'size'=> 8,'cell'=>1,'vali'=>'int' ),

					 array('col'=>'location1'		,'upd'=>'upd','lbl'=>'Location'			,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'sele','mlen'=>  16,'size'=> 8,'cell'=>0,'vali'=>'str' ,'opt'=>$lo1),
					 array('col'=>'location2'		,'upd'=>'upd','lbl'=>''					,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'sele','mlen'=>  16,'size'=> 8,'cell'=>0,'vali'=>'str' ,'opt'=>$loN),
					 array('col'=>'location3'		,'upd'=>'upd','lbl'=>''					,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'sele','mlen'=>  16,'size'=> 8,'cell'=>1,'vali'=>'str' ,'opt'=>$loN),

					 array('col'=>'ratio'			,'upd'=>'upd','lbl'=>'Weight [1-100]'	,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'text','mlen'=>   8,'size'=>12,'cell'=>0,'vali'=>'int' ),
					 array('col'=>'effective_beg_tm','upd'=>'upd','lbl'=>'Effective beg'	,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'text','mlen'=>  20,'size'=>12,'cell'=>0,'vali'=>'date'),
					 array('col'=>'effective_end_tm','upd'=>'upd','lbl'=>'Effective end'	,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'text','mlen'=>  20,'size'=>12,'cell'=>1,'vali'=>'date'),

					 array('col'=>'descr'			,'upd'=>'upd','lbl'=>'Description'		,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'text','mlen'=> 100,'size'=>50,'cell'=>0,'vali'=>'str' ),
					 array('col'=>'html'			,'upd'=>'upd','lbl'=>'HTML'				,'show'=>1,'edit1'=>1,'edit'=>1,'inp'=>'area','mlen'=>1000,'size'=>50,'cell'=>1,'vali'=>'html'),

					 array('col'=>'created_by'		,'upd'=>'usr','lbl'=>'Created by'		,'show'=>1,'edit1'=>0,'edit'=>0,'inp'=>'text','mlen'=>  32,'size'=>20,'cell'=>0,'vali'=>'none'),
					 array('col'=>'created_tm'		,'upd'=>'now','lbl'=>'Created tm'		,'show'=>1,'edit1'=>0,'edit'=>0,'inp'=>'text','mlen'=>  20,'size'=>20,'cell'=>0,'vali'=>'none'),
					 array(																	 'show'=>1,						'inp'=>'lit' ,						  'cell'=>1,
						   'txt'=>"<input type='button' value='Test' style='width:80px' ".
								  "onclick='var o=Win.openDyn(\"adverts\",\"Advert Test\",200,720,1,\"\",0,0);o.document.writeln(\"<center>\"+$(\"n_html_#row#\").value+\"</center>\");Win.endDyn(o)' />"))
		);
	}

	function validateDataSubmission()
	{
		if ( ($ret = parent::validateDataSubmission()) )
			CSql::query_and_free("call update_advert_ranges()",0,__FILE__,__LINE__);
		return $ret;
	}
}

?>
