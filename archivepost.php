<?php
/* think tank forums
 *
 * archivepost.php
 *
 * this script accepts the following variables:
 * 	$_POST["post_id"]	clean
 *
 * NOTE: THIS IS A SILENT SCRIPT.
 */

require "common.inc.php";

$post_id = clean($_GET["post_id"]);

if (isset($ttf["uid"]) && $post_id != '') {

	// find out the thread_id for the given post
	$sql = "SELECT thread_id FROM ttf_post WHERE post_id=$post_id LIMIT 1";
	if (!$result = mysql_query($sql)) showerror();
	list($thread_id) = mysql_fetch_array($result);
	mysql_free_result($result);

	//echo message($thread_id, $thread_id, $thread_id, 1,1); die(); //debugging

	// hide the post if the user is either an admin or the post's author
	$sql = "UPDATE ttf_post SET hide='t' WHERE post_id=$post_id";
	if ($ttf["perm"] != 'admin') $sql .= " AND author_id='{$ttf["user_id"]}'";
	$sql .= " LIMIT 1";
	if (!$result = mysql_query($sql)) showerror();

	// update the thread table, subtracting a post from the count
	// and setting the date to the date of the most recent post in the thread
	$sql = "UPDATE ttf_thread SET posts=posts-1, ".
		"date=(SELECT date FROM ttf_post WHERE thread_id=$thread_id AND hide='f' ORDER BY date DESC LIMIT 1) ".
		"WHERE thread_id=$thread_id LIMIT 1";
	if (!$result = mysql_query($sql)) showerror();

	// update the forum table, subtracting a post from the count
	// and setting the date to the dae of the most recent post in the forum
	$sql = "UPDATE ttf_forum SET posts=posts-1, ".
		"date=(SELECT ttf_post.date FROM ttf_post, ttf_thread ".
		"	WHERE ttf_thread.thread_id=ttf_post.thread_id ".
		"	&& ttf_thread.forum_id=1 && ttf_post.hide='f' ".
		"	ORDER BY ttf_post.date DESC LIMIT 1) ".
		"WHERE forum_id=(SELECT forum_id FROM ttf_thread WHERE thread_id=$thread_id LIMIT 1) LIMIT 1";
	if (!$result = mysql_query($sql)) showerror();

	/* NOTE: this script does not revert back the author's
	 * post_date to the most recent non-hidden post date.
	 * it retains the post_date regardless of whether the
	 * latest post is hidden or not hidden. it may be determined
	 * if this is a good or bad policy in the future. at this
	 * point in time, ttf_user.post_date is only used by three
	 * scripts:
	 *   => newthread.php		UPDATE 
	 *   => reply.php		UPDATE
	 *   => admin_userinfo.php	SELECT
	*/

	header("Location: thread.php?thread_id=$thread_id");

} else {

	message("fatal error", "fatal error",
		"you must:<br />a. be logged in to use this feature<br />b. define post_id", 1, 1);

};

?>
