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


// NA_PDT DST SCHEME
$ttf_dst['na_pdt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_pdt'][] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 10),
                                 'end'   => ttf_conceptual_date(0, 0, 10, $year,  9),
                                 'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_pdt'][] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 10),
                                 'end'   => ttf_conceptual_date(1, 0, 11, $year,  9),
                                 'adj'   => 1*60*60);
};


// NA_MDT DST SCHEME
$ttf_dst['na_mdt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_mdt'][] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 9),
                                 'end'   => ttf_conceptual_date(0, 0, 10, $year, 8),
                                 'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_mdt'][] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 9),
                                 'end'   => ttf_conceptual_date(1, 0, 11, $year, 8),
                                 'adj'   => 1*60*60);
};


// NA_CDT DST SCHEME
$ttf_dst['na_cdt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_cdt'][] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 8),
                                 'end'   => ttf_conceptual_date(0, 0, 10, $year, 7),
                                 'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_cdt'][] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 8),
                                 'end'   => ttf_conceptual_date(1, 0, 11, $year, 7),
                                 'adj'   => 1*60*60);
};


// NA_EDT DST SCHEME
$ttf_dst['na_edt'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['na_edt'][] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 7),
                                 'end'   => ttf_conceptual_date(0, 0, 10, $year, 6),
                                 'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00 LOCAL TIME
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['na_edt'][] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 7),
                                 'end'   => ttf_conceptual_date(1, 0, 11, $year, 6),
                                 'adj'   => 1*60*60);
};


// EUROPE DST SCHEME
$ttf_dst['europe'] = array();
// 1998 to now  --- last sunday in march until last sunday in october     --- +1 hour @ 01:00:00 UTC
for ($year = $ttf_epoch_year; $year <= $dst_end_year; $year++) {
    $ttf_dst['europe'][] = array('begin' => ttf_conceptual_date(0, 0,  3, $year, 1),
                                 'end'   => ttf_conceptual_date(0, 0, 10, $year, 1),
                                 'adj'   => 1*60*60);
};


// EXPORT $ttf_dst
var_export($ttf_dst);
*************************************************************************************************/



$ttf_dst = // add the exported code below.

array (
  'na_pdt' => 
  array (
    0 => 
    array (
      'begin' => 1081072800,
      'end' => 1098608400,
      'adj' => 3600,
    ),
    1 => 
    array (
      'begin' => 1112522400,
      'end' => 1130662800,
      'adj' => 3600,
    ),
    2 => 
    array (
      'begin' => 1143972000,
      'end' => 1162112400,
      'adj' => 3600,
    ),
    3 => 
    array (
      'begin' => 1173607200,
      'end' => 1194166800,
      'adj' => 3600,
    ),
    4 => 
    array (
      'begin' => 1205056800,
      'end' => 1225616400,
      'adj' => 3600,
    ),
    5 => 
    array (
      'begin' => 1236506400,
      'end' => 1257066000,
      'adj' => 3600,
    ),
    6 => 
    array (
      'begin' => 1268560800,
      'end' => 1289120400,
      'adj' => 3600,
    ),
    7 => 
    array (
      'begin' => 1300010400,
      'end' => 1320570000,
      'adj' => 3600,
    ),
    8 => 
    array (
      'begin' => 1331460000,
      'end' => 1352019600,
      'adj' => 3600,
    ),
    9 => 
    array (
      'begin' => 1362909600,
      'end' => 1383469200,
      'adj' => 3600,
    ),
    10 => 
    array (
      'begin' => 1394359200,
      'end' => 1414918800,
      'adj' => 3600,
    ),
    11 => 
    array (
      'begin' => 1425808800,
      'end' => 1446368400,
      'adj' => 3600,
    ),
    12 => 
    array (
      'begin' => 1457863200,
      'end' => 1478422800,
      'adj' => 3600,
    ),
    13 => 
    array (
      'begin' => 1489312800,
      'end' => 1509872400,
      'adj' => 3600,
    ),
    14 => 
    array (
      'begin' => 1520762400,
      'end' => 1541322000,
      'adj' => 3600,
    ),
    15 => 
    array (
      'begin' => 1552212000,
      'end' => 1572771600,
      'adj' => 3600,
    ),
    16 => 
    array (
      'begin' => 1583661600,
      'end' => 1604221200,
      'adj' => 3600,
    ),
  ),
  'na_mdt' => 
  array (
    0 => 
    array (
      'begin' => 1081069200,
      'end' => 1098604800,
      'adj' => 3600,
    ),
    1 => 
    array (
      'begin' => 1112518800,
      'end' => 1130659200,
      'adj' => 3600,
    ),
    2 => 
    array (
      'begin' => 1143968400,
      'end' => 1162108800,
      'adj' => 3600,
    ),
    3 => 
    array (
      'begin' => 1173603600,
      'end' => 1194163200,
      'adj' => 3600,
    ),
    4 => 
    array (
      'begin' => 1205053200,
      'end' => 1225612800,
      'adj' => 3600,
    ),
    5 => 
    array (
      'begin' => 1236502800,
      'end' => 1257062400,
      'adj' => 3600,
    ),
    6 => 
    array (
      'begin' => 1268557200,
      'end' => 1289116800,
      'adj' => 3600,
    ),
    7 => 
    array (
      'begin' => 1300006800,
      'end' => 1320566400,
      'adj' => 3600,
    ),
    8 => 
    array (
      'begin' => 1331456400,
      'end' => 1352016000,
      'adj' => 3600,
    ),
    9 => 
    array (
      'begin' => 1362906000,
      'end' => 1383465600,
      'adj' => 3600,
    ),
    10 => 
    array (
      'begin' => 1394355600,
      'end' => 1414915200,
      'adj' => 3600,
    ),
    11 => 
    array (
      'begin' => 1425805200,
      'end' => 1446364800,
      'adj' => 3600,
    ),
    12 => 
    array (
      'begin' => 1457859600,
      'end' => 1478419200,
      'adj' => 3600,
    ),
    13 => 
    array (
      'begin' => 1489309200,
      'end' => 1509868800,
      'adj' => 3600,
    ),
    14 => 
    array (
      'begin' => 1520758800,
      'end' => 1541318400,
      'adj' => 3600,
    ),
    15 => 
    array (
      'begin' => 1552208400,
      'end' => 1572768000,
      'adj' => 3600,
    ),
    16 => 
    array (
      'begin' => 1583658000,
      'end' => 1604217600,
      'adj' => 3600,
    ),
  ),
  'na_cdt' => 
  array (
    0 => 
    array (
      'begin' => 1081065600,
      'end' => 1098601200,
      'adj' => 3600,
    ),
    1 => 
    array (
      'begin' => 1112515200,
      'end' => 1130655600,
      'adj' => 3600,
    ),
    2 => 
    array (
      'begin' => 1143964800,
      'end' => 1162105200,
      'adj' => 3600,
    ),
    3 => 
    array (
      'begin' => 1173600000,
      'end' => 1194159600,
      'adj' => 3600,
    ),
    4 => 
    array (
      'begin' => 1205049600,
      'end' => 1225609200,
      'adj' => 3600,
    ),
    5 => 
    array (
      'begin' => 1236499200,
      'end' => 1257058800,
      'adj' => 3600,
    ),
    6 => 
    array (
      'begin' => 1268553600,
      'end' => 1289113200,
      'adj' => 3600,
    ),
    7 => 
    array (
      'begin' => 1300003200,
      'end' => 1320562800,
      'adj' => 3600,
    ),
    8 => 
    array (
      'begin' => 1331452800,
      'end' => 1352012400,
      'adj' => 3600,
    ),
    9 => 
    array (
      'begin' => 1362902400,
      'end' => 1383462000,
      'adj' => 3600,
    ),
    10 => 
    array (
      'begin' => 1394352000,
      'end' => 1414911600,
      'adj' => 3600,
    ),
    11 => 
    array (
      'begin' => 1425801600,
      'end' => 1446361200,
      'adj' => 3600,
    ),
    12 => 
    array (
      'begin' => 1457856000,
      'end' => 1478415600,
      'adj' => 3600,
    ),
    13 => 
    array (
      'begin' => 1489305600,
      'end' => 1509865200,
      'adj' => 3600,
    ),
    14 => 
    array (
      'begin' => 1520755200,
      'end' => 1541314800,
      'adj' => 3600,
    ),
    15 => 
    array (
      'begin' => 1552204800,
      'end' => 1572764400,
      'adj' => 3600,
    ),
    16 => 
    array (
      'begin' => 1583654400,
      'end' => 1604214000,
      'adj' => 3600,
    ),
  ),
  'na_edt' => 
  array (
    0 => 
    array (
      'begin' => 1081062000,
      'end' => 1098597600,
      'adj' => 3600,
    ),
    1 => 
    array (
      'begin' => 1112511600,
      'end' => 1130652000,
      'adj' => 3600,
    ),
    2 => 
    array (
      'begin' => 1143961200,
      'end' => 1162101600,
      'adj' => 3600,
    ),
    3 => 
    array (
      'begin' => 1173596400,
      'end' => 1194156000,
      'adj' => 3600,
    ),
    4 => 
    array (
      'begin' => 1205046000,
      'end' => 1225605600,
      'adj' => 3600,
    ),
    5 => 
    array (
      'begin' => 1236495600,
      'end' => 1257055200,
      'adj' => 3600,
    ),
    6 => 
    array (
      'begin' => 1268550000,
      'end' => 1289109600,
      'adj' => 3600,
    ),
    7 => 
    array (
      'begin' => 1299999600,
      'end' => 1320559200,
      'adj' => 3600,
    ),
    8 => 
    array (
      'begin' => 1331449200,
      'end' => 1352008800,
      'adj' => 3600,
    ),
    9 => 
    array (
      'begin' => 1362898800,
      'end' => 1383458400,
      'adj' => 3600,
    ),
    10 => 
    array (
      'begin' => 1394348400,
      'end' => 1414908000,
      'adj' => 3600,
    ),
    11 => 
    array (
      'begin' => 1425798000,
      'end' => 1446357600,
      'adj' => 3600,
    ),
    12 => 
    array (
      'begin' => 1457852400,
      'end' => 1478412000,
      'adj' => 3600,
    ),
    13 => 
    array (
      'begin' => 1489302000,
      'end' => 1509861600,
      'adj' => 3600,
    ),
    14 => 
    array (
      'begin' => 1520751600,
      'end' => 1541311200,
      'adj' => 3600,
    ),
    15 => 
    array (
      'begin' => 1552201200,
      'end' => 1572760800,
      'adj' => 3600,
    ),
    16 => 
    array (
      'begin' => 1583650800,
      'end' => 1604210400,
      'adj' => 3600,
    ),
  ),
  'europe' => 
  array (
    0 => 
    array (
      'begin' => 1080435600,
      'end' => 1098579600,
      'adj' => 3600,
    ),
    1 => 
    array (
      'begin' => 1111885200,
      'end' => 1130634000,
      'adj' => 3600,
    ),
    2 => 
    array (
      'begin' => 1143334800,
      'end' => 1162083600,
      'adj' => 3600,
    ),
    3 => 
    array (
      'begin' => 1174784400,
      'end' => 1193533200,
      'adj' => 3600,
    ),
    4 => 
    array (
      'begin' => 1206234000,
      'end' => 1224982800,
      'adj' => 3600,
    ),
    5 => 
    array (
      'begin' => 1237683600,
      'end' => 1256432400,
      'adj' => 3600,
    ),
    6 => 
    array (
      'begin' => 1269738000,
      'end' => 1287882000,
      'adj' => 3600,
    ),
    7 => 
    array (
      'begin' => 1301187600,
      'end' => 1319936400,
      'adj' => 3600,
    ),
    8 => 
    array (
      'begin' => 1332637200,
      'end' => 1351386000,
      'adj' => 3600,
    ),
    9 => 
    array (
      'begin' => 1364086800,
      'end' => 1382835600,
      'adj' => 3600,
    ),
    10 => 
    array (
      'begin' => 1395536400,
      'end' => 1414285200,
      'adj' => 3600,
    ),
    11 => 
    array (
      'begin' => 1426986000,
      'end' => 1445734800,
      'adj' => 3600,
    ),
    12 => 
    array (
      'begin' => 1459040400,
      'end' => 1477789200,
      'adj' => 3600,
    ),
    13 => 
    array (
      'begin' => 1490490000,
      'end' => 1509238800,
      'adj' => 3600,
    ),
    14 => 
    array (
      'begin' => 1521939600,
      'end' => 1540688400,
      'adj' => 3600,
    ),
    15 => 
    array (
      'begin' => 1553389200,
      'end' => 1572138000,
      'adj' => 3600,
    ),
    16 => 
    array (
      'begin' => 1585443600,
      'end' => 1603587600,
      'adj' => 3600,
    ),
  ),
)

; // add the exported code above.

?>
