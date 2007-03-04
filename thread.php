<?php
/* think tank forums
 *
 * thread.php
 *
 * this script accepts the following variables:
 * 	$_GET["thread_id"]	clean
 *
 * sanity checks include:
 * 	valid thread exists
 *	includes are REQUIRED
 */
require "common.inc.php";
$thread_id = clean($_GET["thread_id"]);
$sql = "SELECT ttf_thread.forum_id, ttf_thread.title, ttf_forum.name
        FROM ttf_thread, ttf_forum
        WHERE ttf_thread.forum_id=ttf_forum.forum_id && thread_id='$thread_id'";
$result = mysql_query($sql);
$thread = mysql_fetch_array($result);
mysql_free_result($result);
if (isset($thread["title"])) {
	$sql = "UPDATE ttf_thread SET views=views+1 WHERE thread_id='$thread_id'";
	$result = mysql_query($sql);
	if (isset($ttf["uid"])) {
		$sql = "REPLACE INTO ttf_thread_new
		        SET thread_id='$thread_id', user_id='{$ttf["uid"]}', last_view=UNIX_TIMESTAMP()";
		$result = mysql_query($sql);
	};
$label = "<a href=\"forum.php?forum_id=".$thread["forum_id"]."\">".$thread["name"]."</a> » ".output($thread["title"]);
// should forum name above be run through output() ? --jlr
require "header.inc.php";
$sql = "SELECT ttf_post.post_id, ttf_post.author_id, ttf_post.date, ttf_post.body,
	ttf_user.username, ttf_user.title, ttf_user.avatar_type
	FROM ttf_post, ttf_user
	WHERE ttf_post.author_id = ttf_user.user_id
	&& ttf_post.hide='f' && ttf_post.thread_id = '$thread_id'
	ORDER BY date ASC";
$result = mysql_query($sql);
while ($post = mysql_fetch_array($result)) {
	$date = strtolower(date("g\:i a, j M y", $post["date"] + 3600*$ttf["time_zone"]));
?>
   <a name="<?php echo $post["post_id"]; ?>"><!-- new post begins here --></a>
   <div class="userbar">
    <div class="userbar_left">
<?php
	if (isset($post["avatar_type"])) {
?>
     <img src="avatars/<?php echo $post["author_id"].".".$post["avatar_type"]; ?>" alt="avatar!" width="30" height="30" />
<?php
		} else { echo "&nbsp;\n"; };
?>
    </div>
    <div class="userbar_right">
     <?php echo $date; ?><br />
     <!--<a class="link" href="editpost.php?post_id=<?php echo $post["post_id"]; ?>">edit</a>,-->
     <a class="link" href="archivepost.php?post_id=<?php echo $post["post_id"]; ?>" onclick="return confirmaction()">archive</a>
    </div>
    <a class="username" href="profile.php?user_id=<?php echo $post["author_id"]; ?>"><?php echo output($post["username"]); ?></a><br />
    <?php echo output($post["title"])."\n"; ?>
   </div>
   <div class="contentbox">
<?php
		echo outputbody($post["body"])."\n";
?>
   </div>
<?php
	};
	mysql_free_result($result);
	if (isset($ttf["uid"])) {
?>
   <form action="reply.php" method="post">
    <table border="0" cellpadding="0" cellspacing="0" width="600" class="shift">
     <tr>
      <td align="center" class="smallinv" valign="middle" width="34">
       <input class="reply" type="image" src="images/post.gif" width="25" height="65" border="0" alt="click to post!" />
      </td>
      <td align="left" class="small" valign="middle">
       <textarea class="reply" rows="7" name="body" wrap="virtual"></textarea>
      </td>
     </tr>
    </table>
    <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>" />
   </form>
<?php
	};
} else {
	message("view thread","error!","not a valid thread.",0,0);
};
require "footer.inc.php";
?>
