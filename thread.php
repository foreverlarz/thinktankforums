<?php
/* think tank forums
 *
 * thread.php
 */

$ttf_title = $ttf_label = "view a thread";

require_once "include_common.php";

$thread_id = clean($_GET["thread_id"]);



// get basic information about this thread
$sql = "SELECT ttf_thread.forum_id,                     ".
       "       ttf_thread.title,                        ".
       "       ttf_forum.name                           ".
       "FROM ttf_thread, ttf_forum                      ".
       "WHERE ttf_thread.forum_id=ttf_forum.forum_id    ".
       "   && thread_id='$thread_id'                    ";
if (!$result = mysql_query($sql)) showerror();



// kill the agent if the thread doesn't exist
if (mysql_num_rows($result) !== 1) {

    message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["thread_dne"]);
    die();

};



// grab the row and stick it into awesomely-named variables
list($forum_id, $thread_title, $forum_name) = mysql_fetch_array($result);



// increment thread views by one
$sql = "UPDATE ttf_thread               ".
       "SET views=views+1               ".
       "WHERE thread_id='$thread_id'    ";
if (!$result = mysql_query($sql)) showerror();



// if user is logged in...
if (isset($ttf["uid"])) {

    // mark this thread as read
    $sql = "REPLACE INTO ttf_thread_new     ".
           "SET thread_id='$thread_id',     ".
           "    user_id='{$ttf["uid"]}',    ".
           "    last_view=UNIX_TIMESTAMP()  ";
    if (!$result = mysql_query($sql)) showerror();

};



// create the header label
$ttf_label = "<a href=\"forum.php?forum_id=$forum_id\">".output($forum_name)."</a> &raquo; ".output($thread_title);
$ttf_title = output($forum_name)." &raquo; ".output($thread_title);



// let's output a page to the user
require_once "include_header.php";



// select the posts in this thread
$sql = "SELECT ttf_post.post_id,                    ".
       "       ttf_post.author_id,                  ".
       "       ttf_post.date,                       ".
       "       ttf_post.rev,                        ".
       "       ttf_post.body,                       ".
       "       ttf_user.username,                   ".
       "       ttf_user.title,                      ".
       "       ttf_user.avatar_type                 ".
       "FROM ttf_post, ttf_user                     ".
       "WHERE ttf_post.author_id=ttf_user.user_id   ".
       "   && ttf_post.thread_id='$thread_id'       ".
       "   && ttf_post.archive IS NULL              ".
       "ORDER BY date ASC                           ";
if (!$result = mysql_query($sql)) showerror();



// for each post...
while ($post = mysql_fetch_array($result)) {

    $date = formatdate($post["date"], "g\:i a, j M y");

    $hasperm = ($ttf["perm"] == 'admin' || $ttf["uid"] == $post["author_id"]) ? TRUE : FALSE;

?>
            <div class="userbar" id="<?php echo $post["post_id"]; ?>">
                <div class="userbar_left">
<?php

    if (!empty($post["avatar_type"])) {

?>
                    <img src="avatars/<?php echo $post["author_id"].".".$post["avatar_type"]; ?>" alt="<?php echo output($post["username"]); ?>'s avatar" width="30" height="30" />
<?php

    } else {

        echo "                    &nbsp;\n";

    };

?>
                </div>
                <div class="userbar_right"><span title="<?php echo $date[1]; ?>"><?php echo $date[0]; ?></span><br />
<?php

    if ($post["rev"] > 0) {

?>
                    <a class="link" href="revision.php?ref_id=<?php echo $post["post_id"]; ?>&amp;type=post">rev <?php echo $post["rev"]; ?></a><?php if ($hasperm) echo ",\n"; ?>
<?php

    };

    if ($hasperm) {

?>
                    <a class="link" href="editpost.php?post_id=<?php echo $post["post_id"]; ?>">edit</a>,
                    <a class="link" href="archivepost.php?post_id=<?php echo $post["post_id"]; ?>" onclick="return confirmaction()">archive</a>
<?php

    };

?>
                </div>
                <a class="username" href="profile.php?user_id=<?php echo $post["author_id"]; ?>"><?php echo output($post["username"]); ?></a><br />
                <?php echo $post["title"]."\n"; ?>
            </div>
            <div class="contentbox_sm">
<?php echo $post["body"]."\n"; ?>
            </div>
<?php

};



// if user is logged in, print a reply box
if (isset($ttf["uid"])) {

?>
            <form action="reply.php" method="post">
                <div id="reply_textarea">
                    <textarea class="reply" rows="5" name="body" wrap="virtual"></textarea>
                </div>
                <div id="reply_button">
                    <input class="reply" type="submit" value="<?php echo $ttf_msg["btnpost"]; ?>" />
                </div>
                <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>" />
            </form>
<?php

};



require_once "include_footer.php";

?>
