<?php
/* think tank forums
 *
 * newthread.php
 */

require "include_common.php";

// if the agent is logged in as a valid user
if (isset($ttf["uid"])) {

	$forum_id = clean($_REQUEST["forum_id"]);

	// grab the name of the specified forum
	$sql = "SELECT name FROM ttf_forum WHERE forum_id='$forum_id'";
	if (!$result = mysql_query($sql)) showerror();
	list($forum_name) = mysql_fetch_array($result);
	mysql_free_result($result);

	// if a valid forum_id was supplied
	if (isset($forum_name)) {

		// if the form was submitted
		if (isset($_POST["body"])) {

			$title = clean($_POST["title"]);
			$body = clean($_POST["body"]);

			// if both fields aren't blank
			if ($title != "" && $body != "") {

				// insert the new thread into ttf_thread
				$sql = "INSERT INTO ttf_thread SET forum_id='$forum_id', ".
					"author_id='{$ttf["uid"]}', date=UNIX_TIMESTAMP(), title='$title'";
				if (!$result = mysql_query($sql)) showerror();
				$thread_id = mysql_insert_id();
				
				// insert the associated post into ttf_post
				$sql = "INSERT INTO ttf_post SET thread_id='$thread_id', ".
					"author_id='{$ttf["uid"]}', date=UNIX_TIMESTAMP(), ".
					"ip='{$_SERVER["REMOTE_ADDR"]}', body='$body'";
				if (!$result = mysql_query($sql)) showerror();
				
				// update the date, thread count, and post count of the forum
				$sql = "UPDATE ttf_forum SET date=UNIX_TIMESTAMP(), threads=threads+1, ".
					"posts=posts+1 WHERE forum_id='$forum_id'";
				if (!$result = mysql_query($sql)) showerror();

				// mark the new thread as read for the author
				$sql = "REPLACE INTO ttf_thread_new SET thread_id='$thread_id', ".
					"user_id='{$ttf["uid"]}', last_view=UNIX_TIMESTAMP()";
				if (!$result = mysql_query($sql)) showerror();

				// update the last post date for the author
				$sql = "UPDATE ttf_user SET post_date=UNIX_TIMESTAMP() ".
					"WHERE user_id='{$ttf["uid"]}'";
				if (!$result = mysql_query($sql)) showerror();

				// redirect to the new thread
				header("Location: thread.php?thread_id=".$thread_id);
			
			} else {

				message("create a new thread", "fatal error", "you left a field blank.", 1, 1);
			
			};
		
		} else {
			
			$label = "<a href=\"forum.php?forum_id=".$forum_id."\">$forum_name</a> » create a new thread";
			
			require "include_header.php";

?>
  <form action="newthread.php" method="post">
   <table border="0" cellpadding="2" cellspacing="1" class="shift">
    <tr class="mediuminv"><td colspan="2"><b>punch in a new thread</b></td></tr>
    <tr class="medium">
     <td valign="top" width="50">title:</td>
     <td valign="top"><input type="text" name="title" maxlength="128" size="48" /></td>
    </tr>
    <tr class="medium">
     <td valign="top" width="50">body:</td>
     <td valign="top"><textarea class="medium" cols="64" rows="12" name="body" wrap="virtual"></textarea></td>
    </tr>
    <tr class="medium"><td align="center" colspan="2"><input type="submit" value="post!" /></td></tr>
   </table>
   <input type="hidden" name="forum_id" value="<?php echo $forum_id; ?>" />
  </form>
<?php
			require "include_footer.php";
		
		};
		
	} else {
		
		message("create a new thread", "fatal error", "you must specify a valid forum.", 1, 1);

	};

} else {
	
	message("create a new thread", "fatal error", "you must login before you may create a new thread.", 1, 1);

};

?>
