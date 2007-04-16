<html>
<body>


<?php
include "include_diff.php";

$string_a = "my name is larz\r\ni am for reelz\r\ni like the girls with the boom\r\ni once got busy in a burger king bathroom!";
$string_b = "my name is larz\r\ni am for tru\r\n\r\ni like the girls with the boom\r\ni once got busy in a burger king drivethru!";
$diff = serialize(diff($string_a, $string_b));
$patched = patch($string_a, unserialize($diff));
$unpatched = unpatch($string_b, unserialize($diff));

echo "\n\n<br /><br /><b>string a:</b><br /><br />\n\n";
echo $string_a;
echo "\n\n<br /><br /><b>string b:</b><br /><br />\n\n";
echo $string_b;
echo "\n\n<br /><br /><b>diff</b><br /><br />\n\n";
echo $diff;
echo "\n\n<br /><br /><b>patched string a:</b><br /><br />\n\n";
echo $patched;
echo "\n\n<br /><br /><b>unpatched string b:</b><br /><br />\n\n";
echo $unpatched;
?>


</body>
</html>
