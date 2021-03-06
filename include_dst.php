<?php

/************************************************************************************************
$ttf_epoch_year = 2004;
$dst_end_year   = 2020;



function ttf_conceptual_date($ordinal, $day_of_week, $month, $year, $h=0, $m=0, $s=0) {
    if ($ordinal === 0) {
        for ($day = gmdate('t', gmmktime($h,$m,$s,$month,0,$year));
             $day_of_week != gmdate('w', $stamp = gmmktime($h,$m,$s,$month,$day,$year));
             $day--);
    } else {
        $day = 0;
        $i = 0;
        do {
            $day++;
            if ($day_of_week == gmdate('w', $stamp = gmmktime($h,$m,$s,$month,$day,$year))) $i++;
        } while ($ordinal > $i);
    };
    return $stamp;
};



// CREATE DST SCHEMES
$ttf_dst = array();


// NA_AKDT DST SCHEME
$ttf_dst['na_akdt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_akdt'][$year] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 11),
                                       'end'   => ttf_conceptual_date(0, 0, 10, $year, 10),
                                       'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_akdt'][$year] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 11),
                                       'end'   => ttf_conceptual_date(1, 0, 11, $year, 10),
                                       'adj'   => 1*60*60);
};


// NA_PDT DST SCHEME
$ttf_dst['na_pdt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_pdt'][$year] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 10),
                                      'end'   => ttf_conceptual_date(0, 0, 10, $year,  9),
                                      'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_pdt'][$year] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 10),
                                      'end'   => ttf_conceptual_date(1, 0, 11, $year,  9),
                                      'adj'   => 1*60*60);
};


// NA_MDT DST SCHEME
$ttf_dst['na_mdt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_mdt'][$year] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 9),
                                      'end'   => ttf_conceptual_date(0, 0, 10, $year, 8),
                                      'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_mdt'][$year] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 9),
                                      'end'   => ttf_conceptual_date(1, 0, 11, $year, 8),
                                      'adj'   => 1*60*60);
};


// NA_CDT DST SCHEME
$ttf_dst['na_cdt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_cdt'][$year] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 8),
                                      'end'   => ttf_conceptual_date(0, 0, 10, $year, 7),
                                      'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_cdt'][$year] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 8),
                                      'end'   => ttf_conceptual_date(1, 0, 11, $year, 7),
                                      'adj'   => 1*60*60);
};


// NA_EDT DST SCHEME
$ttf_dst['na_edt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_edt'][$year] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 7),
                                      'end'   => ttf_conceptual_date(0, 0, 10, $year, 6),
                                      'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_edt'][$year] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 7),
                                      'end'   => ttf_conceptual_date(1, 0, 11, $year, 6),
                                      'adj'   => 1*60*60);
};


// NA_ADT DST SCHEME
$ttf_dst['na_adt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_adt'][$year] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 6),
                                      'end'   => ttf_conceptual_date(0, 0, 10, $year, 5),
                                      'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_adt'][$year] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 6),
                                      'end'   => ttf_conceptual_date(1, 0, 11, $year, 5),
                                      'adj'   => 1*60*60);
};


// NA_MX3 DST SCHEME
$ttf_dst['na_mx3'] = array();
// 1996 to now  --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_mx3'][$year] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 10),
                                      'end'   => ttf_conceptual_date(0, 0, 10, $year,  9),
                                      'adj'   => 1*60*60);
};


// NA_MX2 DST SCHEME
$ttf_dst['na_mx2'] = array();
// 1996 to now  --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_mx2'][$year] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 9),
                                      'end'   => ttf_conceptual_date(0, 0, 10, $year, 8),
                                      'adj'   => 1*60*60);
};


// NA_MX1 DST SCHEME
$ttf_dst['na_mx1'] = array();
// 1996 to now  --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_mx1'][$year] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 8),
                                      'end'   => ttf_conceptual_date(0, 0, 10, $year, 7),
                                      'adj'   => 1*60*60);
};


// EUROPE DST SCHEME
$ttf_dst['eu'] = array();
// 1998 to now  --- last sunday in march until last sunday in october     --- +1 hour @ 01:00:00 UTC
for ($year = $ttf_epoch_year; $year <= $dst_end_year; $year++) {
    $ttf_dst['eu'][$year] = array('begin' => ttf_conceptual_date(0, 0,  3, $year, 1),
                                  'end'   => ttf_conceptual_date(0, 0, 10, $year, 1),
                                  'adj'   => 1*60*60);
};


// EXPORT $ttf_dst
var_export($ttf_dst);
*************************************************************************************************/



$ttf_dst = // add the exported code below.

array (
  'na_akdt' => 
  array (
    2004 => 
    array (
      'begin' => 1081076400,
      'end' => 1098612000,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1112526000,
      'end' => 1130666400,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143975600,
      'end' => 1162116000,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1173610800,
      'end' => 1194170400,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1205060400,
      'end' => 1225620000,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1236510000,
      'end' => 1257069600,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1268564400,
      'end' => 1289124000,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1300014000,
      'end' => 1320573600,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1331463600,
      'end' => 1352023200,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1362913200,
      'end' => 1383472800,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1394362800,
      'end' => 1414922400,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1425812400,
      'end' => 1446372000,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1457866800,
      'end' => 1478426400,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1489316400,
      'end' => 1509876000,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1520766000,
      'end' => 1541325600,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1552215600,
      'end' => 1572775200,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1583665200,
      'end' => 1604224800,
      'adj' => 3600,
    ),
  ),
  'na_pdt' => 
  array (
    2004 => 
    array (
      'begin' => 1081072800,
      'end' => 1098608400,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1112522400,
      'end' => 1130662800,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143972000,
      'end' => 1162112400,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1173607200,
      'end' => 1194166800,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1205056800,
      'end' => 1225616400,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1236506400,
      'end' => 1257066000,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1268560800,
      'end' => 1289120400,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1300010400,
      'end' => 1320570000,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1331460000,
      'end' => 1352019600,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1362909600,
      'end' => 1383469200,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1394359200,
      'end' => 1414918800,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1425808800,
      'end' => 1446368400,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1457863200,
      'end' => 1478422800,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1489312800,
      'end' => 1509872400,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1520762400,
      'end' => 1541322000,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1552212000,
      'end' => 1572771600,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1583661600,
      'end' => 1604221200,
      'adj' => 3600,
    ),
  ),
  'na_mdt' => 
  array (
    2004 => 
    array (
      'begin' => 1081069200,
      'end' => 1098604800,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1112518800,
      'end' => 1130659200,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143968400,
      'end' => 1162108800,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1173603600,
      'end' => 1194163200,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1205053200,
      'end' => 1225612800,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1236502800,
      'end' => 1257062400,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1268557200,
      'end' => 1289116800,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1300006800,
      'end' => 1320566400,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1331456400,
      'end' => 1352016000,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1362906000,
      'end' => 1383465600,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1394355600,
      'end' => 1414915200,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1425805200,
      'end' => 1446364800,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1457859600,
      'end' => 1478419200,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1489309200,
      'end' => 1509868800,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1520758800,
      'end' => 1541318400,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1552208400,
      'end' => 1572768000,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1583658000,
      'end' => 1604217600,
      'adj' => 3600,
    ),
  ),
  'na_cdt' => 
  array (
    2004 => 
    array (
      'begin' => 1081065600,
      'end' => 1098601200,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1112515200,
      'end' => 1130655600,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143964800,
      'end' => 1162105200,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1173600000,
      'end' => 1194159600,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1205049600,
      'end' => 1225609200,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1236499200,
      'end' => 1257058800,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1268553600,
      'end' => 1289113200,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1300003200,
      'end' => 1320562800,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1331452800,
      'end' => 1352012400,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1362902400,
      'end' => 1383462000,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1394352000,
      'end' => 1414911600,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1425801600,
      'end' => 1446361200,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1457856000,
      'end' => 1478415600,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1489305600,
      'end' => 1509865200,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1520755200,
      'end' => 1541314800,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1552204800,
      'end' => 1572764400,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1583654400,
      'end' => 1604214000,
      'adj' => 3600,
    ),
  ),
  'na_edt' => 
  array (
    2004 => 
    array (
      'begin' => 1081062000,
      'end' => 1098597600,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1112511600,
      'end' => 1130652000,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143961200,
      'end' => 1162101600,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1173596400,
      'end' => 1194156000,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1205046000,
      'end' => 1225605600,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1236495600,
      'end' => 1257055200,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1268550000,
      'end' => 1289109600,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1299999600,
      'end' => 1320559200,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1331449200,
      'end' => 1352008800,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1362898800,
      'end' => 1383458400,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1394348400,
      'end' => 1414908000,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1425798000,
      'end' => 1446357600,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1457852400,
      'end' => 1478412000,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1489302000,
      'end' => 1509861600,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1520751600,
      'end' => 1541311200,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1552201200,
      'end' => 1572760800,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1583650800,
      'end' => 1604210400,
      'adj' => 3600,
    ),
  ),
  'na_adt' => 
  array (
    2004 => 
    array (
      'begin' => 1081058400,
      'end' => 1098594000,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1112508000,
      'end' => 1130648400,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143957600,
      'end' => 1162098000,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1173592800,
      'end' => 1194152400,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1205042400,
      'end' => 1225602000,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1236492000,
      'end' => 1257051600,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1268546400,
      'end' => 1289106000,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1299996000,
      'end' => 1320555600,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1331445600,
      'end' => 1352005200,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1362895200,
      'end' => 1383454800,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1394344800,
      'end' => 1414904400,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1425794400,
      'end' => 1446354000,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1457848800,
      'end' => 1478408400,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1489298400,
      'end' => 1509858000,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1520748000,
      'end' => 1541307600,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1552197600,
      'end' => 1572757200,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1583647200,
      'end' => 1604206800,
      'adj' => 3600,
    ),
  ),
  'na_mx3' => 
  array (
    2004 => 
    array (
      'begin' => 1081072800,
      'end' => 1098608400,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1112522400,
      'end' => 1130662800,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143972000,
      'end' => 1162112400,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1175421600,
      'end' => 1193562000,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1207476000,
      'end' => 1225011600,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1238925600,
      'end' => 1256461200,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1270375200,
      'end' => 1287910800,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1301824800,
      'end' => 1319965200,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1333274400,
      'end' => 1351414800,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1365328800,
      'end' => 1382864400,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1396778400,
      'end' => 1414314000,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1428228000,
      'end' => 1445763600,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1459677600,
      'end' => 1477818000,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1491127200,
      'end' => 1509267600,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1522576800,
      'end' => 1540717200,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1554631200,
      'end' => 1572166800,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1586080800,
      'end' => 1603616400,
      'adj' => 3600,
    ),
  ),
  'na_mx2' => 
  array (
    2004 => 
    array (
      'begin' => 1081069200,
      'end' => 1098604800,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1112518800,
      'end' => 1130659200,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143968400,
      'end' => 1162108800,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1175418000,
      'end' => 1193558400,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1207472400,
      'end' => 1225008000,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1238922000,
      'end' => 1256457600,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1270371600,
      'end' => 1287907200,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1301821200,
      'end' => 1319961600,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1333270800,
      'end' => 1351411200,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1365325200,
      'end' => 1382860800,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1396774800,
      'end' => 1414310400,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1428224400,
      'end' => 1445760000,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1459674000,
      'end' => 1477814400,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1491123600,
      'end' => 1509264000,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1522573200,
      'end' => 1540713600,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1554627600,
      'end' => 1572163200,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1586077200,
      'end' => 1603612800,
      'adj' => 3600,
    ),
  ),
  'na_mx1' => 
  array (
    2004 => 
    array (
      'begin' => 1081065600,
      'end' => 1098601200,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1112515200,
      'end' => 1130655600,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143964800,
      'end' => 1162105200,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1175414400,
      'end' => 1193554800,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1207468800,
      'end' => 1225004400,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1238918400,
      'end' => 1256454000,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1270368000,
      'end' => 1287903600,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1301817600,
      'end' => 1319958000,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1333267200,
      'end' => 1351407600,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1365321600,
      'end' => 1382857200,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1396771200,
      'end' => 1414306800,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1428220800,
      'end' => 1445756400,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1459670400,
      'end' => 1477810800,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1491120000,
      'end' => 1509260400,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1522569600,
      'end' => 1540710000,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1554624000,
      'end' => 1572159600,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1586073600,
      'end' => 1603609200,
      'adj' => 3600,
    ),
  ),
  'eu' => 
  array (
    2004 => 
    array (
      'begin' => 1080435600,
      'end' => 1098579600,
      'adj' => 3600,
    ),
    2005 => 
    array (
      'begin' => 1111885200,
      'end' => 1130634000,
      'adj' => 3600,
    ),
    2006 => 
    array (
      'begin' => 1143334800,
      'end' => 1162083600,
      'adj' => 3600,
    ),
    2007 => 
    array (
      'begin' => 1174784400,
      'end' => 1193533200,
      'adj' => 3600,
    ),
    2008 => 
    array (
      'begin' => 1206234000,
      'end' => 1224982800,
      'adj' => 3600,
    ),
    2009 => 
    array (
      'begin' => 1237683600,
      'end' => 1256432400,
      'adj' => 3600,
    ),
    2010 => 
    array (
      'begin' => 1269738000,
      'end' => 1287882000,
      'adj' => 3600,
    ),
    2011 => 
    array (
      'begin' => 1301187600,
      'end' => 1319936400,
      'adj' => 3600,
    ),
    2012 => 
    array (
      'begin' => 1332637200,
      'end' => 1351386000,
      'adj' => 3600,
    ),
    2013 => 
    array (
      'begin' => 1364086800,
      'end' => 1382835600,
      'adj' => 3600,
    ),
    2014 => 
    array (
      'begin' => 1395536400,
      'end' => 1414285200,
      'adj' => 3600,
    ),
    2015 => 
    array (
      'begin' => 1426986000,
      'end' => 1445734800,
      'adj' => 3600,
    ),
    2016 => 
    array (
      'begin' => 1459040400,
      'end' => 1477789200,
      'adj' => 3600,
    ),
    2017 => 
    array (
      'begin' => 1490490000,
      'end' => 1509238800,
      'adj' => 3600,
    ),
    2018 => 
    array (
      'begin' => 1521939600,
      'end' => 1540688400,
      'adj' => 3600,
    ),
    2019 => 
    array (
      'begin' => 1553389200,
      'end' => 1572138000,
      'adj' => 3600,
    ),
    2020 => 
    array (
      'begin' => 1585443600,
      'end' => 1603587600,
      'adj' => 3600,
    ),
  ),
)

; // add the exported code above.

