<?php
/* think tank forums
 *
 * latest.php
 */

$ttf_title = $ttf_label = "latest revisions";

require_once "include_common.php";

// let's output a page to the user
require_once "include_header.php";

$sql = "SELECT `ttf_revision`.*, `ttf_user`.`username`          ".
       "FROM `ttf_revision`                                     ".
       "LEFT JOIN `ttf_user`                                    ".
       "  ON `ttf_revision`.`author_id`=`ttf_user`.`user_id`    ".
       "ORDER BY `date` DESC                                    ".
       "LIMIT 20                                                ";
if (!$result = mysql_query($sql)) showerror();

while ($rev = mysql_fetch_array($result)) {

    $date = formatdate($rev["date"]);

    echo "            <div class=\"contenttitle_sm\">\n                <b>";

    switch ($rev["type"]) {
    case 'post':
        echo "post ".$rev["ref_id"];
        break;
    case 'profile':
        echo "user profile ".$rev["ref_id"];
        break;
    case 'title':
        echo "user title ".$rev["ref_id"];
        break;
    };

    echo "</b>, rev_id {$rev["rev_id"]} by\n";
    echo "                <a class=\"link\" href=\"profile.php?user_id={$rev["author_id"]}\">".output($rev["username"])."</a>";
    if (!empty($rev["ip"])) {
        echo "                ({$rev["ip"]})\n";
    };
    echo "                <span title=\"{$date[1]}\">{$date[0]}</span>\n";
    echo "            </div>\n";
    echo "            <div class=\"contentbox_sm\">\n";
    echo nl2br(output($rev["body"]))."\n";
    echo "            </div>\n";

};

require_once "include_footer.php";

