<?php
/* think tank forums
 *
 * logout.php
 */

require_once "include_common.php";

if (isset($_COOKIE["{$ttf_cfg["cookie_name"]}"])) {

    $expire = time() - 3600;

    setcookie($ttf_cfg["cookie_name"], "", $expire, $ttf_cfg["cookie_path"], $ttf_cfg["cookie_domain"]);

};

header("Location: $ttf_protocol://{$ttf_cfg["address"]}/");

?>
