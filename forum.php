<?php
/* think tank forums
 *
 * forum.php
 */
 require "include_common.php";
 $forum_id = clean($_GET["forum_id"]);
 $offset   = clean($_GET["offset"]);
 $sql = "SELECT name FROM ttf_forum WHERE forum_id='$forum_id'";
 $result = mysql_query($sql);
 $forum  = mysql_fetch_array($result);
 mysql_free_result($result);
 if (isset($forum["name"])) {
  if (isset($ttf["uid"])) {
   $result = mysql_query("REPLACE INTO ttf_forum_new SET forum_id='$forum_id', user_id='{$ttf["uid"]}', last_view=UNIX_TIMESTAMP()");
  }; 
  $label = $forum["name"]; // should this be run through output() ? --jlr
  require "include_header.php";
  $sql = "SELECT COUNT(thread_id) FROM ttf_thread WHERE forum_id='$forum_id'";
  $result = mysql_query($sql);
  $count = mysql_fetch_array($result);
  $numrows = $count[0];
  mysql_free_result($result);
  if ($numrows > ($ttf_config["forum_display"] + $offset)) {
   $next = $offset + $ttf_config["forum_display"];
   $left = min($numrows - $offset - $ttf_config["forum_display"], $ttf_config["forum_display"]);
?>
            <div class="sidebox"><a href="forum.php?forum_id=<?php echo $forum_id; ?>&amp;offset=<?php echo $next; ?>"><strong>next <?php echo $left; ?> threads</strong></a><br />(<?php echo $numrows; ?> total)</div>
<?php
  };
?>
            <table cellspacing="1">
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
  if ($offset == "") $offset = 0;
  $sql = "SELECT ttf_thread.thread_id, ttf_thread.author_id,
                 ttf_thread.posts, ttf_thread.views, ttf_thread.date,
                 ttf_thread.title, ttf_user.username, ttf_thread_new.last_view
          FROM ttf_thread
          LEFT JOIN ttf_user ON ttf_user.user_id=ttf_thread.author_id
	  LEFT JOIN ttf_thread_new ON ttf_thread_new.thread_id=ttf_thread.thread_id
	                           && ttf_thread_new.user_id='{$ttf["uid"]}'
          WHERE ttf_thread.forum_id='$forum_id'
          ORDER BY ttf_thread.date DESC
          LIMIT $offset, {$ttf_config["forum_display"]}";
  $result = mysql_query($sql);
  while ($thread = mysql_fetch_array($result)) {
	  /* THERE IS SURELY A MORE EFFICIENT WAY
	   * TO PRINT A JUMP LINK RATHER THAN TO
	   * QUERY FOR EACH THREAD!! --JLR
	   * here's an idea! instead of using dates in the new_thread table,
	   * use the number of the reply. so reply_id=5 means that they read
	   * the fifth reply but nothing after that. maybe this would work
	   * pretty slick! --jlr
	   */
   $code = "&nbsp;";
   unset ($code2);
   if ($thread["last_view"] < $thread["date"] && isset($ttf["uid"])) {
    $code = "<img src=\"images/arrow.gif\" width=\"11\" height=\"11\" alt=\"new post!\" />";
    $sql = "SELECT ttf_post.post_id ".
           "FROM ttf_post ".	 
           "WHERE ttf_post.thread_id='{$thread["thread_id"]}' ".
           "ORDER BY ttf_post.date DESC ".
           "LIMIT 0, 1";
    $resulta = mysql_query($sql);
    $newpost = mysql_fetch_array($resulta);
    mysql_free_result($resulta);
    $code2 = "<span class=\"small\">&nbsp;&nbsp;&nbsp;(<a href=\"thread.php?thread_id=".$thread["thread_id"]."#".$newpost["post_id"]."\">jump</a>)</span>";
   };
?>
                    <tr>
                        <td><?php echo $code; ?></td>
                        <td><a href="thread.php?thread_id=<?php echo $thread["thread_id"]; ?>"><?php echo output($thread["title"]); ?></a><?php echo $code2; ?></td>
                        <td><a href="profile.php?user_id=<?php echo $thread["author_id"]; ?>"><?php echo output($thread["username"]); ?></a></td>
                        <td><?php echo $thread["posts"]; ?></td>
                        <td><?php echo $thread["views"]; ?></td>
                    </tr>
<?php
  };
  mysql_free_result($result);
?>
                </tbody>
            </table>
            <div class="contenttitle">start a new thread</div>
            <div class="contentbox" style="text-align: center;">
                <form action="newthread.php" method="post">
                    <input type="text" name="title" maxlength="64" size="64" />
                    <input type="submit" value="insert" />
                    <input type="hidden" name="forum_id" value="<?php echo $forum_id; ?>" />
                </form>
            </div>
<?php
 } else { message("view forum","error!","not a valid forum.",1,0); };
 require "include_footer.php";
?>
