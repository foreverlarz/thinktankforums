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
for ($year = 1987, $now = gmdate('Y'); $year <= $now; $year++) {
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
      'begin' => 542167200,
      'end' => 562730400,
      'adj' => 3600,
    ),
    21 => 
    array (
      'begin' => 574221600,
      'end' => 594784800,
      'adj' => 3600,
    ),
    22 => 
    array (
      'begin' => 605671200,
      'end' => 626234400,
      'adj' => 3600,
    ),
    23 => 
    array (
      'begin' => 637120800,
      'end' => 657684000,
      'adj' => 3600,
    ),
    24 => 
    array (
      'begin' => 668570400,
      'end' => 689133600,
      'adj' => 3600,
    ),
    25 => 
    array (
      'begin' => 700020000,
      'end' => 720583200,
      'adj' => 3600,
    ),
    26 => 
    array (
      'begin' => 732074400,
      'end' => 752637600,
      'adj' => 3600,
    ),
    27 => 
    array (
      'begin' => 763524000,
      'end' => 784087200,
      'adj' => 3600,
    ),
    28 => 
    array (
      'begin' => 794973600,
      'end' => 815536800,
      'adj' => 3600,
    ),
    29 => 
    array (
      'begin' => 826423200,
      'end' => 846986400,
      'adj' => 3600,
    ),
    30 => 
    array (
      'begin' => 857872800,
      'end' => 878436000,
      'adj' => 3600,
    ),
    31 => 
    array (
      'begin' => 889322400,
      'end' => 909885600,
      'adj' => 3600,
    ),
    32 => 
    array (
      'begin' => 921376800,
      'end' => 941940000,
      'adj' => 3600,
    ),
    33 => 
    array (
      'begin' => 952826400,
      'end' => 973389600,
      'adj' => 3600,
    ),
    34 => 
    array (
      'begin' => 984276000,
      'end' => 1004839200,
      'adj' => 3600,
    ),
    35 => 
    array (
      'begin' => 1015725600,
      'end' => 1036288800,
      'adj' => 3600,
    ),
    36 => 
    array (
      'begin' => 1047175200,
      'end' => 1067738400,
      'adj' => 3600,
    ),
    37 => 
    array (
      'begin' => 1079229600,
      'end' => 1099792800,
      'adj' => 3600,
    ),
    38 => 
    array (
      'begin' => 1110679200,
      'end' => 1131242400,
      'adj' => 3600,
    ),
    39 => 
    array (
      'begin' => 1142128800,
      'end' => 1162692000,
      'adj' => 3600,
    ),
    40 => 
    array (
      'begin' => 1173578400,
      'end' => 1194141600,
      'adj' => 3600,
    ),
    41 => 
    array (
      'begin' => 1205028000,
      'end' => 1225591200,
      'adj' => 3600,
    ),
    42 => 
    array (
      'begin' => 1236477600,
      'end' => 1257040800,
      'adj' => 3600,
    ),
  ),
)

; // add the exported code above.

?>
