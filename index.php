<?php
/* think tank forums
 *
 * index.php
 */

require "include_common.php";
$label = $ttf_config["index_title"];
require "include_header.php";

?>
            <table cellspacing="1">
                <colgroup>
                    <col id="mark" />
                    <col id="forum" />
                    <col id="threads" />
                    <col id="posts" />
                </colgroup>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>forum</th>
                        <th>threads</th>
                        <th>posts</th>
                    </tr>
                </thead>
                <tbody>
<?php
$sql = "SELECT ttf_forum.*, ttf_forum_new.last_view FROM ttf_forum ".
       "LEFT JOIN ttf_forum_new ON ttf_forum_new.forum_id=ttf_forum.forum_id ".
       "AND ttf_forum_new.user_id='{$ttf["uid"]}'";
if (!$result = mysql_query($sql)) showerror();

// let's calculate total numbers of threads and posts
$tot_threads = 0;
$tot_posts = 0;

while ($forum = mysql_fetch_array($result)) {

    // reset $code from last time
    unset($mark);

    // if user is logged in and hasn't read the forum since the last post
    if ($forum["last_view"] < $forum["date"] && isset($ttf["uid"])) {

        // make $mark an arrow
        $mark = "<img src=\"images/arrow.gif\" width=\"11\" height=\"11\" alt=\"new posts\" />";
    
    } else {

        // or make it a space
        $mark = "&nbsp;";
    
    };

    // add the forum's count to the total
    $tot_threads += $forum["threads"];
    $tot_posts += $forum["posts"];
    
    // ** should forum name and description be run through output() ? --jlr **

?>
                    <tr>
                        <td><?php echo $mark; ?></td>
                        <td>
                            <a href="forum.php?forum_id=<?php echo $forum["forum_id"]; ?>"><?php echo $forum["name"]; ?></a><br />
                            <span class="small">&nbsp;&nbsp;&middot; <?php echo $forum["description"]; ?></span>
                        </td>
                        <td><?php echo $forum["threads"]; ?></td>
                        <td><?php echo $forum["posts"]; ?></td>
                    </tr>
<?php

};

// let's find out if anyone is online
$sql = "SELECT user_id, username, perm FROM ttf_user ".
       "WHERE visit_date > UNIX_TIMESTAMP()-{$ttf_config["online_timeout"]} ".
       "ORDER BY username";
if (!$result = mysql_query($sql)) showerror();

// initialize $i
$i = 0;

while ($user = mysql_fetch_array($result)) {

    // if there has been a previous printed, use a comma
    if ($i > 0) $code .= ", ";

    // if the user is an admin, make the name bold
    if ($user["perm"] == 'admin') {
        
        $code .= "<a href=\"profile.php?user_id={$user["user_id"]}\"><strong>".output($user["username"])."</strong></a>";
    
    } else {
        
        $code .= "<a href=\"profile.php?user_id={$user["user_id"]}\">".output($user["username"])."</a>";
    
    };

    // we printed a user
    $i = 1;

};

// if no users were printed, say so
if ($i == 0) $code = "noone is online.";

?>
                </tbody>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>online persons</th>
                        <th style="text-align: center;" colspan="2">totals</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>&nbsp;</td>
                        <td class="small"><?php echo $code; ?></td>
                        <td><?php echo $tot_threads; ?></td>
                        <td><?php echo $tot_posts; ?></td>
                    </tr>
                </tbody>
            </table>
<?php

require "include_footer.php";

?>
