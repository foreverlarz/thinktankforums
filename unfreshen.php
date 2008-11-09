<?php
/* think tank forums
 *
 * unfreshen.php
 */

$ttf_title = $ttf_label = "unfreshen (mark everything as read)";

require_once "include_common.php";

kill_guests();

$sql = "UPDATE ttf_user                 ".
       "SET fresh_date=UNIX_TIMESTAMP() ".
       "WHERE user_id='{$ttf["uid"]}'   ";
if (!$result = mysql_query($sql)) showerror();

header("Location: http://".$ttf_cfg["address"]."/");

?>
