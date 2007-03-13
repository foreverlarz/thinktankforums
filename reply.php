<?php
/* think tank forums
 *
 * reply.php
 */

require "include_common.php";

$thread_id = clean($_POST["thread_id"]);
$body = clean($_POST["body"]);

if (isset($ttf["uid"])) {

	if (!empty($thread_id) && !empty($body)) {

		// select the $forum_id of the forum that the post is being inserted into
		$sql = "SELECT forum_id FROM ttf_thread WHERE thread_id='$thread_id' LIMIT 1";
		$result = mysql_query($sql);
		list($forum_id) = mysql_fetch_array($result);
		mysql_free_result($result);
		if (!$forum_id) {
			message("fatal error", "fatal error", "the thread specified does not exist.", 1, 1);
			die();
		};

		// insert the post into the respective thread
		$sql = "INSERT INTO ttf_post SET thread_id='$thread_id', author_id='{$ttf["uid"]}', ". 
			"date=UNIX_TIMESTAMP(), ip='{$_SERVER["REMOTE_ADDR"]}', body='$body'";
         	if (!$result = mysql_query($sql)) showerror();

		// update the thread's post count and date
		$sql = "UPDATE ttf_thread SET date=UNIX_TIMESTAMP(), posts=posts+1 WHERE thread_id='$thread_id'";
         	if (!$result = mysql_query($sql)) showerror();

		// update the forum's post count and date
		$sql = "UPDATE ttf_forum SET date=UNIX_TIMESTAMP(), posts=posts+1 WHERE forum_id='$forum_id'";
         	if (!$result = mysql_query($sql)) showerror();

		// mark the thread as read for the author
		$sql = "REPLACE INTO ttf_thread_new SET thread_id='$thread_id', user_id='{$ttf["uid"]}', ".
			"last_view=UNIX_TIMESTAMP()";
         	if (!$result = mysql_query($sql)) showerror();

		// upate the user's last post date
		$sql = "UPDATE ttf_user SET post_date=UNIX_TIMESTAMP() WHERE user_id='{$ttf["uid"]}'";
         	if (!$result = mysql_query($sql)) showerror();

		header("Location: thread.php?thread_id=$thread_id");

	} else {
		message("post a reply", "fatal error", "either the thread_id or body fields were left empty.", 1, 1);
	};

} else {
	message("post a reply", "fatal error", "you must be logged in to post.", 1, 1);
};

?>
