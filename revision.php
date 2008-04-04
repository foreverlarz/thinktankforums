<?php
/* think tank forums
 *
 * revision.php
 */

require_once "include_common.php";

// pull through the variables
$ref_id = clean($_GET["ref_id"]);
$type   = clean($_GET["type"]);

// if either of the variables is empty
if (empty($ref_id) || empty($type)) {

    message("view revisions", "fatal error",
            "you must specify an item to view.");

    die();

};

$title = "revision browser";
switch ($type) {
case 'post':
    $title .= " &raquo; post_id $ref_id";
    break;
case 'profile':
    $title .= " &raquo; profile for user_id $ref_id";
    break;
case 'title':
    $title .= " &raquo; title for user_id $ref_id";
    break;
};
$label = $title;
require_once "include_header.php";

$revnum = 0;

$sql = "SELECT ttf_revision.*, ttf_user.username ".
       "FROM ttf_revision, ttf_user ".
       "WHERE ttf_revision.author_id = ttf_user.user_id ".
       "      && type='$type' && ref_id='$ref_id' ".
       "ORDER BY date ASC";
if (!$result = mysql_query($sql)) showerror();

while ($rev = mysql_fetch_array($result)) {

    echo "            <div class=\"contenttitle_sm\">\n";
    echo "                rev $revnum, rev_id {$rev["rev_id"]} by\n";
    echo "                <a class=\"link\" href=\"profile.php?user_id={$rev["author_id"]}\">";
    echo output($rev["username"])."</a> ({$rev["ip"]})\n";
    echo "                ".formatdate($rev["date"])."\n";
    echo "            </div>\n";
    echo "            <div class=\"contentbox_sm\">\n";
    echo nl2br(output($rev["body"]))."\n";
    echo "            </div>\n";

    $revnum++;

};

require_once "include_footer.php";

?>
