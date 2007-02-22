<?php
/* think tank forums
 *
 * newthread.php
 *
 * this script accepts the following variables:
 * 	$_GET["forum_id"]	clean
 * 	$_POST["newthread"]	clean
 * 	$_POST["title"]		clean
 * 	$_POST["body"] 		clean
 * 
 * sanity checks include:
 * 	logged in as valid user
 * 	adding to a valid forum
 * 	neither title nor body are blank
 * 	includes are REQUIRED
 */
 require "common.inc.php";
 if (isset($ttf["uid"])) {
  if (isset($_POST["newthread"])) $forum_id = clean($_POST["newthread"]);
  else $forum_id = clean($_GET["forum_id"]);
  $sql = "SELECT name FROM ttf_forum WHERE forum_id='$forum_id'";
  $result = mysql_query($sql);
  $forum = mysql_fetch_array($result);
  mysql_free_result($result);
  if (isset($forum["name"])) {
   if (isset($_POST["newthread"])) {
    $title = clean($_POST["title"]);
    $body = clean($_POST["body"]);
    if ($title != "" && $body != "") {
     $resulta = mysql_query("INSERT INTO ttf_thread VALUES ('$forum_id', '', '{$ttf["uid"]}', '1', '0', UNIX_TIMESTAMP(), '$title')");
     $thread_id = mysql_insert_id();
     $resultb = mysql_query("INSERT INTO ttf_post VALUES ('', '$thread_id', '{$ttf["uid"]}', UNIX_TIMESTAMP(), '{$_SERVER["REMOTE_ADDR"]}', '$body')");
     $resultc = mysql_query("UPDATE ttf_forum SET date=UNIX_TIMESTAMP(), threads=threads+1, posts=posts+1 WHERE forum_id='$forum_id'");
     $resultd = mysql_query("REPLACE INTO ttf_thread_new SET thread_id='$thread_id', user_id='{$ttf["uid"]}', last_view=UNIX_TIMESTAMP()");
     $resulte = mysql_query("UPDATE ttf_user SET post_date=UNIX_TIMESTAMP() WHERE user_id='{$ttf["uid"]}'");
     header("Location: thread.php?thread_id=".$thread_id);
    } else { message("create a new thread","error!","you left a field blank.",1,1); };
   } else {	
    $label = "<a href=\"forum.php?forum_id=".$forum_id."\">".$forum["name"]."</a> » create a new thread";
    require "header.inc.php";  
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
   <input type="hidden" name="newthread" value="<?php echo $forum_id; ?>" />
  </form>
<?php
    require "footer.inc.php";
   };
  } else { message("create a new thread","error!","invalid forum.",1,1); };
 } else { message("create a new thread","error!","you must login before you may post a new thread.",1,1); };
?>