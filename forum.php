<?php
/* think tank forums
 *
 * forum.php
 */

$ttf_title = $ttf_label = "view a forum";

require_once "include_common.php";

$forum_id = clean($_GET["forum_id"]);
$offset = clean($_GET["offset"]);

$sql = "SELECT name                 ".
       "FROM ttf_forum              ".
       "WHERE forum_id='$forum_id'  ";
if (!$result = mysql_query($sql)) showerror();
list($forum_name) = mysql_fetch_array($result);

if (empty($forum_name)) {

    message($ttf_label, $ttf_msg["fatal_error"], "not a valid forum.");
    die();

};

if (isset($ttf["uid"])) {

    $sql = "REPLACE INTO ttf_forum_new      ".
           "SET forum_id='$forum_id',       ".
           "    user_id='{$ttf["uid"]}',    ".
           "    last_view=UNIX_TIMESTAMP()  ";
    if (!$result = mysql_query($sql)) showerror();

};

$ttf_title = $ttf_label = output($forum_name);

require_once "include_header.php";

if (isset($ttf["uid"])) {

?>
            <div class="sidebox">
                <strong><a href="newthread.php?forum_id=<?php echo $forum_id; ?>">create a new thread</a></strong>
            </div>
<?php

};

if (empty($offset)) $offset = 0;

$sql = "SELECT SQL_CALC_FOUND_ROWS                                                  ".
       "       ttf_thread.thread_id, ttf_thread.author_id,                          ".
       "       ttf_thread.posts, ttf_thread.views, ttf_thread.date,                 ".
       "       ttf_thread.title, ttf_user.username, ttf_thread_new.last_view        ".
       "FROM ttf_thread                                                             ".
       "LEFT JOIN ttf_user ON ttf_user.user_id=ttf_thread.author_id                 ".
       "LEFT JOIN ttf_thread_new ON ttf_thread_new.thread_id=ttf_thread.thread_id   ".
       "          && ttf_thread_new.user_id='{$ttf["uid"]}'                         ".
       "WHERE ttf_thread.forum_id='$forum_id' && ttf_thread.posts > 0               ".
       "ORDER BY ttf_thread.date DESC                                               ".
       "LIMIT $offset, {$ttf_cfg["forum_display"]}                                  ";
if (!$result = mysql_query($sql)) showerror();

$sql = "SELECT FOUND_ROWS()";
if (!$result_nested = mysql_query($sql)) showerror();
list($numrows) = mysql_fetch_array($result_nested);

if ($numrows > ($ttf_cfg["forum_display"] + $offset)) {
        
    $next = $offset + $ttf_cfg["forum_display"];
    $left = min($numrows - $offset - $ttf_cfg["forum_display"], $ttf_cfg["forum_display"]);

?>
            <div class="sidebox">
                <strong><a href="forum.php?forum_id=<?php echo $forum_id; ?>&amp;offset=<?php echo $next; ?>">next <?php echo $left; ?> threads</a></strong><br /><span class="small"><?php echo $numrows; ?> total</span>
            </div>
<?php

};

?>
            <table cellspacing="1" class="content">
                <colgroup>
                    <col id="mark" />
                    <col id="thread" />
                    <col id="author" />
                    <col id="posts" />
                    <col id="views" />
                </colgroup>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>title</th>
                        <th>author</th>
                        <th>posts</th>
                        <th>views</th>
                    </tr>
                </thead>
                <tbody>
<?php

while ($thread = mysql_fetch_array($result)) {

//  if ($thread["last_view"] <= $thread["date"] && isset($ttf["uid"])) {
    if ($thread["last_view"] <  $thread["date"] && isset($ttf["uid"])) {

        $mark = "&#9658;";
        $jump = "<span class=\"small\">&nbsp;&nbsp;&nbsp;(<a href=\"thread.php?thread_id=".
                $thread["thread_id"]."#fresh\">jump</a>)</span>";

    } else {

        $mark = "&nbsp;";
        unset($jump);

    };

?>
                    <tr>
                        <td class="center"><?php echo $mark; ?></td>
                        <td><a href="thread.php?thread_id=<?php echo $thread["thread_id"]; ?>"><?php echo output($thread["title"]); ?></a><?php echo $jump; ?></td>
                        <td><a href="profile.php?user_id=<?php echo $thread["author_id"]; ?>"><?php echo output($thread["username"]); ?></a></td>
                        <td><?php echo $thread["posts"]; ?></td>
                        <td><?php echo $thread["views"]; ?></td>
                    </tr>
<?php

};

?>
                    <tr>
                        <th colspan="5">&nbsp;</th>
                    </tr>
                </tbody>
            </table>
<?php

require_once "include_footer.php";

?>
