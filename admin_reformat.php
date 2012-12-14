<?php
/* think tank forums
 *
 * admin_reformat.php
 */

require_once "include_common.php";

$ttf_title = $ttf_label = "administration &raquo; reformat posts, profiles, and user titles";

// this is an admin-only script--kill everyone else
kill_nonadmin();

require_once "include_header.php";

echo "<div class=\"contenttitle\">reformatting the cache...</div>\n";
echo "<div class=\"contentbox\">\n";

reformat_caches();

echo "</div>\n";

require_once "include_footer.php";

