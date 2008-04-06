<?php
/* think tank forums
 *
 * newthread.php
 */

$ttf_label = "create a new thread";
$ttf_title = $ttf_label;

require_once "include_common.php";

// if the agent is not logged in
if (empty($ttf["uid"])) {

    message($ttf_label, $error_die_text, "you must login before you may create a new thread.");
    die();

};

$forum_id = clean($_REQUEST["forum_id"]);
// don't clean $title and $body, we might output them!
$title = $_POST["title"];
$body = $_POST["body"];

// grab the name of the specified forum
$sql = "SELECT name FROM ttf_forum WHERE forum_id='$forum_id'";
if (!$result = mysql_query($sql)) showerror();
list($forum_name) = mysql_fetch_array($result);

// if a valid forum_id was supplied
if (empty($forum_name)) {

    message($ttf_label, $error_die_text, "you must specify a valid forum.");
    die();

};

// now that we have the name of the forum, make a nicer $ttf_label and $ttf_title
$ttf_label = "<a href=\"forum.php?forum_id=$forum_id\">".output($forum_name)."</a> &raquo; create a new thread";
$ttf_title = output($forum_name)." &raquo; create a new thread";

// if each field isn't empty, silently and patiently let them fill them! :D
if (empty($title) || empty($body)) {

    require_once "include_header.php";

?>
            <form action="newthread.php" method="post">
                <div class="contenttitle">punch in a new thread</div>
                <div class="contentbox">
                    title:<br />
                    <input type="text" name="title" maxlength="128" size="48" value="<?php echo output($title); ?>" /><br /><br />
                    body:<br />
                    <textarea class="medium" cols="70" rows="15" name="body" wrap="virtual"><?php echo output($body); ?></textarea><br /><br />
                    <input type="submit" value="create" />
                </div>
                <input type="hidden" name="forum_id" value="<?php echo $forum_id; ?>" />
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

// redirect to the new thread
header("Location: thread.php?thread_id=".$thread_id);

?>
