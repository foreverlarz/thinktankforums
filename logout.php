<?php
/* think tank forums
 *
 * logout.php
 */

require_once "include_common.php";

cookie_smash();

header("Location: $ttf_protocol://{$ttf_cfg["address"]}/");

