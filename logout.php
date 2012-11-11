<?php
/* think tank forums
 *
 * logout.php
 */

require_once "include_common.php";

if (isset($_COOKIE["{$ttf_cfg["cookie_name"]}-pair"]) || isset($_COOKIE["{$ttf_cfg["cookie_name"]}-user"])) {

    $expire = 1;

    setcookie($ttf_cfg["cookie_name"].'-pair', '', $expire, $ttf_cfg["cookie_path"], $ttf_cfg["cookie_domain"], $ttf_cfg["cookie_secure"]);
    setcookie($ttf_cfg["cookie_name"].'-user', '', $expire, $ttf_cfg["cookie_path"], $ttf_cfg["cookie_domain"], FALSE);

};

header("Location: $ttf_protocol://{$ttf_cfg["address"]}/");

?>
