<?php
/* think tank forums
 *
 * logout.php
 */

require_once "include_common.php";

if (isset($_COOKIE["{$ttf_cfg["cookie_name"]}"])) {

    $expire = time() - 3600;

    setcookie($ttf_cfg["cookie_name"], "", $expire);

};

header("Location: http://".$ttf_cfg["address"]."/");

?>
