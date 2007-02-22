<?php
/* think tank forums
 *
 * reply.php
 *
 * this script accepts the following variables:
 * 	$_POST["thread_id"]	clean
 *	$_POST["body"]		clean
 *
 * sanity checks include:
 * 	valid user logged in
 * 	neither thread_id nor body are blank
 * 	valid forum and thread specified
 * 	includes are REQUIRED
 *
 * NOTE: THIS IS A SILENT SCRIPT.
 */
require "common.inc.php";
$thread_id = clean($_POST["thread_id"]);
$body = clean($_POST["body"]);
if (isset($ttf["uid"])) {
   if ($thread_id != "" && $body != "") {
      $resulta = mysql_query("SELECT forum_id FROM ttf_thread WHERE thread_id='$thread_id' LIMIT 1");
      $thread = mysql_fetch_array($resulta);
      mysql_free_result($resulta);
      if (isset($thread["forum_id"])) {
         $resultb = mysql_query("INSERT INTO ttf_post VALUES ('', '$thread_id', '{$ttf["uid"]}', UNIX_TIMESTAMP(), '{$_SERVER["REMOTE_ADDR"]}', '$body')");
         $resultc = mysql_query("UPDATE ttf_thread SET date=UNIX_TIMESTAMP(), posts=posts+1 WHERE thread_id='$thread_id'");
         $resultd = mysql_query("UPDATE ttf_forum SET date=UNIX_TIMESTAMP(), posts=posts+1 WHERE forum_id='{$thread["forum_id"]}'");
         $resulte = mysql_query("REPLACE INTO ttf_thread_new SET thread_id='$thread_id', user_id='{$ttf["uid"]}', last_view=UNIX_TIMESTAMP()");
         $resultf = mysql_query("UPDATE ttf_user SET post_date=UNIX_TIMESTAMP() WHERE user_id='{$ttf["uid"]}'");
      };
   };
};
header("Location: thread.php?thread_id=$thread_id");
?>