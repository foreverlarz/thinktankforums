<?php
/* think tank forums
 *
 * newthread.php
 */

$ttf_title = $ttf_label = "create a new thread";

require_once "include_common.php";

// guests cannot create threads
kill_guests();

$forum_id = clean($_REQUEST["forum_id"]);
// don't clean $title and $body, we might output them!
$title = $_POST["title"];
$body = $_POST["body"];

// grab the name of the specified forum
$sql = "SELECT name FROM ttf_forum WHERE forum_id='$forum_id'";
if (!$result = mysql_query($sql)) showerror();

// if a valid forum_id was supplied
if (mysql_num_rows($result) !== 1) {

    message($ttf_label, $ttf_msg["fatal_error"], "you must specify a valid forum.");
    die();

};

list($forum_name) = mysql_fetch_array($result);

// now that we have the name of the forum, make a nicer $ttf_label and $ttf_title
$ttf_label = "<a href=\"forum.php?forum_id=$forum_id\">".output($forum_name)."</a> &raquo; create a new thread";
$ttf_title = output($forum_name)." &raquo; create a new thread";

// if any field is empty, silently and patiently let them fill them! :D
if (trim($title) == "" || trim($body) == "") {

    require_once "include_header.php";

?>
            <form action="newthread.php" method="post">
                <div class="contenttitle">title your thread</div>
                <div id="newthread_title">
                    <input class="newthread_title" type="text" name="title" value="<?php echo output($title); ?>" />
                </div>
                <div id="newthread_textarea">
                    <textarea class="newthread" cols="72" rows="20" name="body"><?php echo output($body); ?></textarea>
                </div>
                <div id="newthread_button">
                    <input class="newthread_button" type="submit" value="create thread" />
                </div>
                <div>
                    <input type="hidden" name="forum_id" value="<?php echo $forum_id; ?>" />
                </div>
            </form>
<?php

    require_once "include_footer.php";
    die();

};

// insert the new thread into ttf_thread
$sql = "INSERT INTO ttf_thread          ".
       "SET forum_id=$forum_id,         ".
       "    author_id={$ttf["uid"]},    ".
       "    posts=1,                    ".
       "    date=UNIX_TIMESTAMP(),      ".
       "    title='".clean($title)."'   ";
if (!$result = mysql_query($sql)) showerror();
$thread_id = mysql_insert_id();

// insert the post into the respective thread
$sql = "INSERT INTO ttf_post                    ".
       "SET thread_id=$thread_id,               ".
       "    author_id={$ttf["uid"]},            ".
       "    date=UNIX_TIMESTAMP(),              ".
       "    body='".clean(outputbody($body))."' ";
if (!$result = mysql_query($sql)) showerror();
$post_id = mysql_insert_id();

// insert the thread as a base revision
$sql = "INSERT INTO ttf_revision            ".
       "SET ref_id=$thread_id,              ".
       "    type='thread',                  ".
       "    author_id={$ttf["uid"]},        ".
       "    date=UNIX_TIMESTAMP(),          ".
       "    ip='{$_SERVER["REMOTE_ADDR"]}', ".
       "    body='".clean($title)."'        ";
if (!$result = mysql_query($sql)) showerror();

// insert the post as a base revision
$sql = "INSERT INTO ttf_revision            ".
       "SET ref_id=$post_id,                ".
       "    type='post',                    ".
       "    author_id={$ttf["uid"]},        ".
       "    date=UNIX_TIMESTAMP(),          ".
       "    ip='{$_SERVER["REMOTE_ADDR"]}', ".
       "    body='".clean($body)."'         ";
if (!$result = mysql_query($sql)) showerror();

// update the date, thread count, and post count of the forum
$sql = "UPDATE ttf_forum            ".
       "SET threads=threads+1,      ".
       "    posts=posts+1,          ".
       "    date=UNIX_TIMESTAMP()   ".
       "WHERE forum_id=$forum_id    ";
if (!$result = mysql_query($sql)) showerror();

// mark the new thread as read for the author
$sql = "REPLACE INTO ttf_thread_new     ".
       "SET thread_id=$thread_id,       ".
       "    user_id={$ttf["uid"]},      ".
       "    last_view=UNIX_TIMESTAMP()  ";
if (!$result = mysql_query($sql)) showerror();

// update the user's last post date
$sql = "UPDATE ttf_user                 ".
       "SET post_date=UNIX_TIMESTAMP()  ".
       "WHERE user_id={$ttf["uid"]}     ";
if (!$result = mysql_query($sql)) showerror();

// update the user's last rev date
$sql = "UPDATE ttf_user                 ".
       "SET rev_date=UNIX_TIMESTAMP()   ".
       "WHERE user_id={$ttf["uid"]}     ";
if (!$result = mysql_query($sql)) showerror();

// redirect to the new thread
header("Location: $ttf_protocol://{$ttf_cfg["address"]}/thread.php?thread_id=$thread_id");

?>
