<?php

/************************************************************************************************
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
for ($year = 1987; $year <= 2006; $year++) {
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
      'begin' => 544586400,
      'end' => 562125600,
      'adj' => 3600,
    ),
    1 => 
    array (
      'begin' => 576036000,
      'end' => 594180000,
      'adj' => 3600,
    ),
    2 => 
    array (
      'begin' => 607485600,
      'end' => 625629600,
      'adj' => 3600,
    ),
    3 => 
    array (
      'begin' => 638935200,
      'end' => 657079200,
      'adj' => 3600,
    ),
    4 => 
    array (
      'begin' => 670989600,
      'end' => 688528800,
      'adj' => 3600,
    ),
    5 => 
    array (
      'begin' => 702439200,
      'end' => 719978400,
      'adj' => 3600,
    ),
    6 => 
    array (
      'begin' => 733888800,
      'end' => 751428000,
      'adj' => 3600,
    ),
    7 => 
    array (
      'begin' => 765338400,
      'end' => 783482400,
      'adj' => 3600,
    ),
    8 => 
    array (
      'begin' => 796788000,
      'end' => 814932000,
      'adj' => 3600,
    ),
    9 => 
    array (
      'begin' => 828842400,
      'end' => 846381600,
      'adj' => 3600,
    ),
    10 => 
    array (
      'begin' => 860292000,
      'end' => 877831200,
      'adj' => 3600,
    ),
    11 => 
    array (
      'begin' => 891741600,
      'end' => 909280800,
      'adj' => 3600,
    ),
    12 => 
    array (
      'begin' => 923191200,
      'end' => 940730400,
      'adj' => 3600,
    ),
    13 => 
    array (
      'begin' => 954640800,
      'end' => 972784800,
      'adj' => 3600,
    ),
    14 => 
    array (
      'begin' => 986090400,
      'end' => 1004234400,
      'adj' => 3600,
    ),
    15 => 
    array (
      'begin' => 1018144800,
      'end' => 1035684000,
      'adj' => 3600,
    ),
    16 => 
    array (
      'begin' => 1049594400,
      'end' => 1067133600,
      'adj' => 3600,
    ),
    17 => 
    array (
      'begin' => 1081044000,
      'end' => 1098583200,
      'adj' => 3600,
    ),
    18 => 
    array (
      'begin' => 1112493600,
      'end' => 1130637600,
      'adj' => 3600,
    ),
    19 => 
    array (
      'begin' => 1143943200,
      'end' => 1162087200,
      'adj' => 3600,
    ),
    20 => 
    array (
      'begin' => 1173578400,
      'end' => 1194141600,
      'adj' => 3600,
    ),
    21 => 
    array (
      'begin' => 1205028000,
      'end' => 1225591200,
      'adj' => 3600,
    ),
    22 => 
    array (
      'begin' => 1236477600,
      'end' => 1257040800,
      'adj' => 3600,
    ),
  ),
)

; // add the exported code above.

?>
