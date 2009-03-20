<?php

/************************************************************************************************
$ttf_epoch_year = 2004;



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
for ($year = 2007, $now = gmdate('Y'); $year <= $now; $year++) {
    $ttf_dst['usa'][] = array('begin' => ttf_conceptual_date(2, 0,  3, $year, 2),
                              'end'   => ttf_conceptual_date(1, 0, 11, $year, 2),
                              'adj'   => 1*60*60);
};


// CANADA DST SCHEME
$ttf_dst['canada'] = $ttf_dst['usa'];


// EUROPE DST SCHEME
$ttf_dst['europe'] = array();
// 1998 to now  --- last sunday in march until last sunday in october     --- +1 hour @ 01:00:00
for ($year = $ttf_epoch_year, $now = gmdate('Y'); $year <= $now; $year++) {
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
  ),
)

; // add the exported code above.

?>
