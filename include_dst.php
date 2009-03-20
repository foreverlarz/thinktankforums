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


// NULL DST SCHEME
$ttf_dst['null'] = array();


// USA DST SCHEME
$ttf_dst['usa'] = array();
// 1987 to 2006 --- first sunday in april until last sunday in october    --- +1 hour @ 02:00:00
for ($year = $ttf_epoch_year; $year <= 2006; $year++) {
    $ttf_dst['usa'][] = array('begin' => ttf_conceptual_date(1, 0,  4, $year, 2),
                              'end'   => ttf_conceptual_date(0, 0, 10, $year, 2),
                              'adj'   => 1*60*60);
};
// 2007 to now  --- second sunday in march until first sunday in november --- +1 hour @ 02:00:00
for ($year = 2007; $year <= $dst_end_year; $year++) {
    $ttf_dst['usa'][] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 2),
                              'end'   => ttf_conceptual_date(1, 0, 11, $year, 2),
                              'adj'   => 1*60*60);
};


// CANADA DST SCHEME
$ttf_dst['canada'] = $ttf_dst['usa'];


// EUROPE DST SCHEME
$ttf_dst['europe'] = array();
// 1998 to now  --- last sunday in march until last sunday in october     --- +1 hour @ 01:00:00
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
  'null' => 
  array (
  ),
  'usa' => 
  array (
    0 => 
    array (
      'begin' => 1081044000,
      'end' => 1098583200,
      'adj' => 3600,
    ),
    1 => 
    array (
      'begin' => 1112493600,
      'end' => 1130637600,
      'adj' => 3600,
    ),
    2 => 
    array (
      'begin' => 1143943200,
      'end' => 1162087200,
      'adj' => 3600,
    ),
    3 => 
    array (
      'begin' => 1173578400,
      'end' => 1194141600,
      'adj' => 3600,
    ),
    4 => 
    array (
      'begin' => 1205028000,
      'end' => 1225591200,
      'adj' => 3600,
    ),
    5 => 
    array (
      'begin' => 1236477600,
      'end' => 1257040800,
      'adj' => 3600,
    ),
    6 => 
    array (
      'begin' => 1268532000,
      'end' => 1289095200,
      'adj' => 3600,
    ),
    7 => 
    array (
      'begin' => 1299981600,
      'end' => 1320544800,
      'adj' => 3600,
    ),
    8 => 
    array (
      'begin' => 1331431200,
      'end' => 1351994400,
      'adj' => 3600,
    ),
    9 => 
    array (
      'begin' => 1362880800,
      'end' => 1383444000,
      'adj' => 3600,
    ),
    10 => 
    array (
      'begin' => 1394330400,
      'end' => 1414893600,
      'adj' => 3600,
    ),
    11 => 
    array (
      'begin' => 1425780000,
      'end' => 1446343200,
      'adj' => 3600,
    ),
    12 => 
    array (
      'begin' => 1457834400,
      'end' => 1478397600,
      'adj' => 3600,
    ),
    13 => 
    array (
      'begin' => 1489284000,
      'end' => 1509847200,
      'adj' => 3600,
    ),
    14 => 
    array (
      'begin' => 1520733600,
      'end' => 1541296800,
      'adj' => 3600,
    ),
    15 => 
    array (
      'begin' => 1552183200,
      'end' => 1572746400,
      'adj' => 3600,
    ),
    16 => 
    array (
      'begin' => 1583632800,
      'end' => 1604196000,
      'adj' => 3600,
    ),
  ),
  'canada' => 
  array (
    0 => 
    array (
      'begin' => 1081044000,
      'end' => 1098583200,
      'adj' => 3600,
    ),
    1 => 
    array (
      'begin' => 1112493600,
      'end' => 1130637600,
      'adj' => 3600,
    ),
    2 => 
    array (
      'begin' => 1143943200,
      'end' => 1162087200,
      'adj' => 3600,
    ),
    3 => 
    array (
      'begin' => 1173578400,
      'end' => 1194141600,
      'adj' => 3600,
    ),
    4 => 
    array (
      'begin' => 1205028000,
      'end' => 1225591200,
      'adj' => 3600,
    ),
    5 => 
    array (
      'begin' => 1236477600,
      'end' => 1257040800,
      'adj' => 3600,
    ),
    6 => 
    array (
      'begin' => 1268532000,
      'end' => 1289095200,
      'adj' => 3600,
    ),
    7 => 
    array (
      'begin' => 1299981600,
      'end' => 1320544800,
      'adj' => 3600,
    ),
    8 => 
    array (
      'begin' => 1331431200,
      'end' => 1351994400,
      'adj' => 3600,
    ),
    9 => 
    array (
      'begin' => 1362880800,
      'end' => 1383444000,
      'adj' => 3600,
    ),
    10 => 
    array (
      'begin' => 1394330400,
      'end' => 1414893600,
      'adj' => 3600,
    ),
    11 => 
    array (
      'begin' => 1425780000,
      'end' => 1446343200,
      'adj' => 3600,
    ),
    12 => 
    array (
      'begin' => 1457834400,
      'end' => 1478397600,
      'adj' => 3600,
    ),
    13 => 
    array (
      'begin' => 1489284000,
      'end' => 1509847200,
      'adj' => 3600,
    ),
    14 => 
    array (
      'begin' => 1520733600,
      'end' => 1541296800,
      'adj' => 3600,
    ),
    15 => 
    array (
      'begin' => 1552183200,
      'end' => 1572746400,
      'adj' => 3600,
    ),
    16 => 
    array (
      'begin' => 1583632800,
      'end' => 1604196000,
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
