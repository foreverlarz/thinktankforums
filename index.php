<?php
/* think tank forums
 *
 * index.php
 */

require_once "include_common.php";
$ttf_label = (empty($ttf_cfg["index_title"])) ? 'welcome' : output($ttf_cfg["index_title"]);
require_once "include_header.php";

?>
            <table cellspacing="1" class="content">
                <colgroup>
                    <col id="forum<?php if (!isset($ttf["uid"])) echo "_lg"; ?>" />
<?php if (isset($ttf["uid"])) echo "                    <col id=\"freshies\" />\n"; ?>
                    <col id="threads" />
                    <col id="posts" />
                </colgroup>
                <thead>
                    <tr>
                        <th>forum</th>
<?php if (isset($ttf["uid"])) echo "                        <th>freshies</th>\n"; ?>
                        <th>threads</th>
                        <th>posts</th>
                    </tr>
                </thead>
                <tbody>
<?php
$sql = "SELECT ttf_forum.*,                                                 ".
       "       COUNT( * ) - COUNT( ttf_thread_new.last_view ) AS freshies,  ".
       "       ttf_forum_new.last_view                                      ".
       "FROM ttf_forum                                                      ".
       "LEFT JOIN ttf_thread USING ( forum_id )                             ".
       "LEFT JOIN ttf_thread_new                                            ".
       "       ON (    ttf_thread.thread_id = ttf_thread_new.thread_id      ".
       "            && ttf_thread_new.user_id = '{$ttf["uid"]}'             ".
       "            && ttf_thread.date < ttf_thread_new.last_view )         ".
       "LEFT JOIN ttf_forum_new                                             ".
       "       ON (    ttf_forum.forum_id = ttf_forum_new.forum_id          ".
       "            && ttf_forum_new.user_id = '{$ttf["uid"]}'              ".
       "            && ttf_forum.date >= ttf_forum_new.last_view )          ".
       "GROUP BY ttf_forum.forum_id                                         ";

if (!$result = mysql_query($sql)) showerror();

// let's calculate total numbers of freshies, threads, and posts
$tot_freshies = 0;
$tot_threads = 0;
$tot_posts = 0;

while ($forum = mysql_fetch_array($result)) {

    // add this forum's count to the total
    $tot_freshies += $forum["freshies"];
    $tot_threads += $forum["threads"];
    $tot_posts += $forum["posts"];

?>
                    <tr>
                        <td>
                            <a href="forum.php?forum_id=<?php echo $forum["forum_id"]; ?>"><?php echo output($forum["name"]); ?></a><br />
                            <span class="small">&nbsp;&nbsp;&middot; <?php echo output($forum["description"]); ?></span>
                        </td>
<?php
    if (isset($ttf["uid"])) {
        echo "                        <td>";
        echo (empty($forum["last_view"]) ? $forum["freshies"] : "<b>".$forum["freshies"]."</b>");
        echo "</td>\n";
    };
?>
                        <td><?php echo $forum["threads"]; ?></td>
                        <td><?php echo $forum["posts"]; ?></td>
                    </tr>
<?php

};

// let's find out if anyone is online
$sql = "SELECT user_id, username, perm                                      ".
       "FROM ttf_user                                                       ".
       "WHERE visit_date > UNIX_TIMESTAMP()-{$ttf_cfg["online_timeout"]} ".
       "ORDER BY username                                                   ";
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

if (isset($ttf["uid"])) $tot_span = 3; else $tot_span = 2;

?>
                    <tr>
                        <th>online persons</th>
                        <th style="text-align: center;" colspan="<?php echo $tot_span; ?>">totals</th>
                    </tr>
                    <tr>
                        <td class="small"><?php echo $code; ?></td>
<?php if (isset($ttf["uid"])) echo "                        <td>$tot_freshies</td>\n"; ?>
                        <td><?php echo $tot_threads; ?></td>
                        <td><?php echo $tot_posts; ?></td>
                    </tr>
                </tbody>
            </table>
<?php

require_once "include_footer.php";

?>
