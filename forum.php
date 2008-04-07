<?php
/* think tank forums
 *
 * forum.php
 */

$ttf_label = "view a forum";
$ttf_title = $ttf_label;

require_once "include_common.php";

$forum_id = clean($_GET["forum_id"]);
$offset = clean($_GET["offset"]);

$sql = "SELECT name FROM ttf_forum WHERE forum_id='$forum_id'";
if (!$result = mysql_query($sql)) showerror();
list($forum_name) = mysql_fetch_array($result);

if (empty($forum_name)) {

    message($ttf_label, $error_die_text, "not a valid forum.");
    die();

};

if (isset($ttf["uid"])) {

    $sql = "REPLACE INTO ttf_forum_new      ".
           "SET forum_id='$forum_id',       ".
           "    user_id='{$ttf["uid"]}',    ".
           "    last_view=UNIX_TIMESTAMP()  ";
    if (!$result = mysql_query($sql)) showerror();

};

$ttf_label = output($forum_name);
$ttf_title = $ttf_label;

require_once "include_header.php";

?>
            <div class="sidebox">
                <a href="newthread.php?forum_id=<?php echo $forum_id; ?>">create a new thread</a>
            </div>
<?php

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
       "LIMIT $offset, {$ttf_config["forum_display"]}                               ";
if (!$result = mysql_query($sql)) showerror();

$sql = "SELECT FOUND_ROWS()";
if (!$result_nested = mysql_query($sql)) showerror();
list($numrows) = mysql_fetch_array($result_nested);

if ($numrows > ($ttf_config["forum_display"] + $offset)) {
        
    $next = $offset + $ttf_config["forum_display"];
    $left = min($numrows - $offset - $ttf_config["forum_display"], $ttf_config["forum_display"]);

?>
            <div class="sidebox">
                <a href="forum.php?forum_id=<?php echo $forum_id; ?>&amp;offset=<?php echo $next; ?>"><strong>next <?php echo $left; ?> threads</strong></a><br />(<?php echo $numrows; ?> total)
            </div>
<?php

};

?>
            <table cellspacing="1" class="content">
                <colgroup>
                    <col id="mark" align="center" />
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
    /* THERE IS SURELY A MORE EFFICIENT WAY
     * TO PRINT A JUMP LINK RATHER THAN TO
	 * QUERY FOR EACH THREAD!! --JLR
	 * here's an idea! instead of using dates in the new_thread table,
	 * use the number of the reply. so reply_id=5 means that they read
	 * the fifth reply but nothing after that. maybe this would work
	 * pretty slick! --jlr
	 */

    // initialize variables
    $mark = "&nbsp;";
    unset($jump);

    if ($thread["last_view"] < $thread["date"] && isset($ttf["uid"])) {

        $mark = "&#9658;";

        $sql = "SELECT ttf_post.post_id ".
               "FROM ttf_post ".
               "WHERE ttf_post.thread_id='{$thread["thread_id"]}' ".
               "ORDER BY ttf_post.date DESC ".
               "LIMIT 0, 1";
        if (!$result_nested = mysql_query($sql)) showerror();
        list($newpost) = mysql_fetch_array($result_nested);
        $jump = "<span class=\"small\">&nbsp;&nbsp;&nbsp;(<a href=\"thread.php?thread_id=".
                $thread["thread_id"]."#$newpost\">jump</a>)</span>";

    };

?>
                    <tr>
                        <td><?php echo $mark; ?></td>
                        <td><a href="thread.php?thread_id=<?php echo $thread["thread_id"]; ?>"><?php echo output($thread["title"]); ?></a><?php echo $jump; ?></td>
                        <td><a href="profile.php?user_id=<?php echo $thread["author_id"]; ?>"><?php echo output($thread["username"]); ?></a></td>
                        <td><?php echo $thread["posts"]; ?></td>
                        <td><?php echo $thread["views"]; ?></td>
                    </tr>
<?php

};

?>
                </tbody>
            </table>
<?php

require_once "include_footer.php";

?>
