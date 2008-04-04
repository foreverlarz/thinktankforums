<?php
/* think tank forums
 *
 * archivepost.php
 */

$label = "archive post";

require_once "include_common.php";

$post_id = clean($_GET["post_id"]);

if (!isset($ttf["uid"])) {

    message($label, $error_die_text, "you must be logged in.");
    die();

};

if (empty($post_id)) {

    message($label, $error_die_text, "you must specify a post.");
    die();

};

// archive the post if the user is either an admin or the post's author
$sql = "UPDATE ttf_post SET archive=UNIX_TIMESTAMP() WHERE post_id=$post_id";
if ($ttf["perm"] != 'admin') $sql .= " AND author_id='{$ttf["uid"]}'";
if (!$result = mysql_query($sql)) showerror();

if (mysql_affected_rows() != 1) {

    message($label, $error_die_text, "you don't have permission to do this.");
    die();

};

// find out the thread_id for the given post
$sql = "SELECT thread_id FROM ttf_post WHERE post_id=$post_id";
if (!$result = mysql_query($sql)) showerror();
list($thread_id) = mysql_fetch_array($result);
    
// update the thread table, subtracting a post from the count
// and setting the date to the date of the most recent post in the thread
// WORD UP ==> IF THE THREAD HAS NO OTHER POSTS, date->0, listing it last. --jlr *********************
$sql = "UPDATE ttf_thread SET posts=posts-1, ".
    "date=(SELECT date FROM ttf_post ".
    "      WHERE thread_id=$thread_id AND hide='f' ".
    "      ORDER BY date DESC LIMIT 1) ".
    "WHERE thread_id=$thread_id LIMIT 1";
if (!$result = mysql_query($sql)) showerror();

// update the forum table, subtracting a post from the count
// and setting the date to the date of the most recent post in the forum
// WORD UP ==> IF THE FORUM HAS NO OTHER POSTS, date->0, listing it last. --jlr **********************
$sql = "UPDATE ttf_forum SET posts=posts-1, ".
    "date=(SELECT ttf_post.date FROM ttf_post, ttf_thread ".
    "      WHERE ttf_thread.thread_id=ttf_post.thread_id ".
    "      && ttf_thread.forum_id=1 && ttf_post.hide='f' ".
    "      ORDER BY ttf_post.date DESC LIMIT 1) ".
    "WHERE forum_id=(SELECT forum_id FROM ttf_thread ".
    "                WHERE thread_id=$thread_id)";
if (!$result = mysql_query($sql)) showerror();

/* NOTE: this script does not revert back the author's
 * post_date to the most recent non-hidden post date.
 * it retains the post_date regardless of whether the
 * latest post is hidden or not hidden. it may be determined
 * if this is a good or bad policy in the future. at this
 * point in time, ttf_user.post_date is only used by two
 * scripts:
 *   => reply.php           UPDATE
 *   => admin_userinfo.php  SELECT
 *   => profile.php         SELECT
 */
  
header("Location: thread.php?thread_id=$thread_id");

?>
