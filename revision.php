<?php
/* think tank forums
 *
 * revision.php
 */

$ttf_title = $ttf_label = "revision browser";

require_once "include_common.php";
require_once "include_finediff.php";

// pull through the variables
$ref_id = clean($_GET["ref_id"]);
$type   = clean($_GET["type"]);



// if either of the variables is empty
if (empty($ref_id) || empty($type)) {

    message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["noitemspec"]);
    die();

};



// make some nice titles/labels
switch ($type) {
case 'post':
    $ttf_label .= " &raquo; post $ref_id";
    break;
case 'profile':
    $ttf_label .= " &raquo; profile for user $ref_id";
    break;
case 'title':
    $ttf_label .= " &raquo; title for user $ref_id";
    break;
};

$ttf_title = $ttf_label;



// let's output a page to the user
require_once "include_header.php";



// start counting revisions
$revnum = 0;



// grab the revisions
$sql = "SELECT ttf_revision.*, ttf_user.username ".
       "FROM ttf_revision, ttf_user ".
       "WHERE ttf_revision.author_id = ttf_user.user_id ".
       "      && type='$type' && ref_id='$ref_id' ".
       "ORDER BY date ASC";
if (!$result = mysql_query($sql)) showerror();

while ($rev = mysql_fetch_array($result)) {


    if (isset($rev["privacy"])) {

        echo "            <div class=\"contenttitle_sm\" id=\"rev-{$rev["rev_id"]}\">\n";
        echo "                rev $revnum, rev_id {$rev["rev_id"]}";
        echo "            </div>\n";

    } else {

        $date = formatdate($rev["date"]);

        //if (isset($lastrev)) {
        //    $opcodes = FineDiff::getDiffOpcodes(output($lastrev), output($rev["body"]), FineDiff::$wordGranularity);
        //    $revbody = FineDiff::renderDiffToHTMLFromOpcodes(output($lastrev), $opcodes);
        //    $lastrev = $rev["body"];
        //} else {
            $revbody = $rev["body"];
        //    $lastrev = $rev["body"];
        //};

        echo "            <div class=\"contenttitle_sm\" id=\"rev-{$rev["rev_id"]}\">\n";
        echo "                rev $revnum, rev_id {$rev["rev_id"]} by\n";
        echo "                <a class=\"link\" href=\"profile.php?user_id={$rev["author_id"]}\">".output($rev["username"])."</a>";
        if (!empty($rev["ip"])) {
            echo "                ({$rev["ip"]})\n";
        };
        echo "                <span title=\"{$date[1]}\">{$date[0]}</span>\n";
        if ($ttf["perm"] == 'admin') {
            echo "                    <a class=\"link\" href=\"admin_privacy.php?rev_id={$rev["rev_id"]}\">privacy</a>\n";
        };
        echo "            </div>\n";
        echo "            <div class=\"contentbox_sm\">\n";
        echo nl2br($revbody)."\n";
        echo "            </div>\n";

    };

    $revnum++;

};



require_once "include_footer.php";

?>
