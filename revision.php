<?php
/* think tank forums
 *
 * editpost.php
 */

require_once "include_common.php";
require_once "include_diff.php";

// pull through the variables
$ref_id = clean($_GET["ref_id"]);
$type   = clean($_GET["type"]);

// if one of the variables isn't defined
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

unset($revbody);

$sql = "SELECT ttf_revision.*, ttf_user.username ".
       "FROM ttf_revision, ttf_user ".
       "WHERE ttf_revision.author_id = ttf_user.user_id ".
       "      && type='$type' && ref_id='$ref_id' ".
       "ORDER BY date ASC";
if (!$result = mysql_query($sql)) showerror();

while ($rev = mysql_fetch_array($result)) {

    if (!isset($revbody)) {

        $revbody = $rev["body"];
        $revnum = 0;

    } else {

        $diffarray = unserialize($rev["body"]);
        if (is_array($diffarray)) {

            $revbody = patch($revbody, $diffarray);
            $revnum++;

        } else {

            message("think tank forums",
                    "fatal error",
                    "there was a patching problem.");
            die();

        };

    };

    echo "            <div class=\"contenttitle_sm\">\n";
    echo "                rev $revnum, rev_id {$rev["rev_id"]} by\n";
    echo "                <a class=\"link\" href=\"profile.php?user_id={$rev["author_id"]}\">";
    echo output($rev["username"])."</a> ({$rev["ip"]})\n";
    echo "                ".formatdate($rev["date"])."\n";
    echo "            </div>\n";
    echo "            <div class=\"contentbox_sm\">\n";
    echo nl2br(output($revbody))."\n";
    echo "            </div>\n";

};

require_once "include_footer.php";

?>
