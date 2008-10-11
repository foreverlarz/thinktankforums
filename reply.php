<?php
/* think tank forums
 *
 * reply.php
 */

$ttf_title = $ttf_label = "post a reply";

require_once "include_common.php";

$thread_id = $_POST["thread_id"];
$body = $_POST["body"];

kill_guests();

if (empty($thread_id) || empty($body)) {

    message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["field_empty"]);
    die();

};

// select the $forum_id of the forum that the post is being inserted into
$sql = "SELECT forum_id FROM ttf_thread WHERE thread_id='".clean($thread_id)."' LIMIT 1";
$result = mysql_query($sql);
list($forum_id) = mysql_fetch_array($result);
mysql_free_result($result);
if (empty($forum_id)) {
    
    message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["thread_dne"]);
    die();

};

// insert the post into the respective thread
$sql = "INSERT INTO ttf_post SET thread_id='".clean($thread_id)."', author_id='{$ttf["uid"]}', ". 
       "date=UNIX_TIMESTAMP(), body='".clean(outputbody($body))."'";
if (!$result = mysql_query($sql)) showerror();
$post_id = mysql_insert_id();

// insert the post as a base revision
$sql = "INSERT INTO ttf_revision SET ref_id='$post_id', type='post', author_id='{$ttf["uid"]}', ". 
       "date=UNIX_TIMESTAMP(), ip='{$_SERVER["REMOTE_ADDR"]}', body='".clean($body)."'";
if (!$result = mysql_query($sql)) showerror();

// update the thread's post count and date
$sql = "UPDATE ttf_thread SET date=UNIX_TIMESTAMP(), posts=posts+1 WHERE thread_id='".clean($thread_id)."'";
if (!$result = mysql_query($sql)) showerror();

// update the forum's post count and date
$sql = "UPDATE ttf_forum SET date=UNIX_TIMESTAMP(), posts=posts+1 WHERE forum_id='$forum_id'";
if (!$result = mysql_query($sql)) showerror();

// mark the thread as read for the author
$sql = "REPLACE INTO ttf_thread_new SET thread_id='".clean($thread_id)."', user_id='{$ttf["uid"]}', ".
       "last_view=UNIX_TIMESTAMP()";
if (!$result = mysql_query($sql)) showerror();

// update the user's last post date
$sql = "UPDATE ttf_user SET post_date=UNIX_TIMESTAMP() WHERE user_id='{$ttf["uid"]}'";
if (!$result = mysql_query($sql)) showerror();

// update the user's last rev date
$sql = "UPDATE ttf_user                 ".
       "SET rev_date=UNIX_TIMESTAMP()  ".
       "WHERE user_id={$ttf["uid"]}     ";
if (!$result = mysql_query($sql)) showerror();

header("Location: thread.php?thread_id=$thread_id#$post_id");

?>
