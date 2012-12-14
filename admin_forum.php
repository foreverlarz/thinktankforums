<?php
/* think tank forums
 *
 * admin_forum.php
 */

$ttf_title = $ttf_label = "administration &raquo; forums";

require_once "include_common.php";

// this is an admin-only script--kill everyone else
kill_nonadmin();

$forum_id = clean($_REQUEST["forum_id"]);



if (isset($_POST["forum_id"])) {

    $forum_name = clean($_POST["forum_name"]);
    $forum_description = clean($_POST["forum_description"]);

    $sql = "UPDATE ttf_forum                        ".
           "SET name='$forum_name',                 ".
           "    description='$forum_description'    ".
           "WHERE forum_id='$forum_id'              ";

    if (!$result = mysql_query($sql)) {

        showerror();

    } else {

        set_msg("<strong>success:</strong> the forum has been updated successfully.");

        header("Location: $ttf_protocol://{$ttf_cfg["address"]}/admin_forum.php?forum_id=".$forum_id);

    };

    die();

} else if (isset($_GET["forum_id"])) {

    $ttf_title = $ttf_label = "altering forum $forum_id";

    // select forum info
    $sql = "SELECT * FROM ttf_forum WHERE forum_id='{$forum_id}'";
    if (!$result = mysql_query($sql)) showerror();
    $forum = mysql_fetch_array($result);

    $output_forum_name = output($forum["name"]);
    $output_forum_desc = output($forum["description"]);

    require_once "include_header.php";

echo <<<EOF
            <form action="admin_forum.php" method="post">
                <table cellspacing="1" class="content">
                    <tr>
                        <th colspan="2">modify the forum information</th>
                    </tr>
                    <tr>
                        <td>name</td>
                        <td><input type="text" name="forum_name" size="64" value="{$output_forum_name}" /></td>
                    </tr>
                    <tr>
                        <td>description</td>
                        <td><input type="text" name="forum_description" size="64" value="{$output_forum_desc}" /></td>
                    </tr>
                </table>
                <div class="contentbox-orange">
                    <strong>notice:</strong> these changes are not versioned.
                </div>
                <div id="box_submit-button">
                    <input class="submit-button" type="submit" value="apply changes" />
                </div>
                <div><input type="hidden" name="forum_id" value="{$forum_id}" /></div>
            </form>

EOF;

    require_once "include_footer.php";
    die();

};

require_once "include_header.php";

$sql = "SELECT * FROM ttf_forum";
if (!$result = mysql_query($sql)) showerror();

?>
            <table cellspacing="1" class="content">
                <tr>
                    <th>name</th>
                    <th>thrd</th>
                    <th>psts</th>
                    <th>date</th>
                    <th>actn</th>
                </tr>
<?php

while($forum = mysql_fetch_array($result)) {

    $output_forum_name = output($forum["name"]);
    $output_forum_desc = output($forum["description"]);
    $date = formatdate($forum["date"]);

    echo <<<EOF
                    <tr>
                        <td><span title="{$output_forum_desc}" class="pro-tip">{$output_forum_name}</span></td>
                        <td>{$forum["threads"]}</td>
                        <td>{$forum["posts"]}</td>
                        <td><span title="{$date[1]}" class="pro-tip">{$date[0]}</span></td>
                        <td><a href="admin_forum.php?forum_id={$forum["forum_id"]}">modify</a></td>
                    </tr>

EOF;

};

?>
            </table>    
<?php

require_once "include_footer.php";

