<?php
/* think tank forums 1.0-beta
 *
 * Copyright (c) 2004, 2005, 2006 Jonathan Lucas Reddinger <lucas@wingedleopard.net>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 *
 ****************************************************************************
 */
 include "common.inc.php";
 admin();
 $label = "administration » dbms tables";
 include "header.inc.php";
?>
<table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
<tr class="mediuminv"><td width="594" colspan="5"><b>
<a class="mediuminv" style="color: white;" href="<?php echo $_SERVER["PHP_SELF"]; ?>?fix=thread_date">thread date (increase only)</a> | 
<a class="mediuminv" style="color: white;" href="<?php echo $_SERVER["PHP_SELF"]; ?>?fix=thread_posts">thread posts</a> | 
<a class="mediuminv" style="color: white;" href="<?php echo $_SERVER["PHP_SELF"]; ?>?fix=forum_date">forum date</a></b></td></tr>
<tr class="mediuminv"><td>
<?php
$fix = clean($_GET["fix"]);
if ($fix == "thread_date") {
	$sql =	"SELECT ttf_thread.thread_id, ttf_thread.date, ttf_post.date ".
		"FROM ttf_thread, ttf_post ".
		"WHERE ttf_thread.thread_id=ttf_post.thread_id";
	$result = mysql_query($sql);
	while ($post = mysql_fetch_array($result)) {
		if ($post[1] < $post[2]) {
			$result_u = mysql_query("UPDATE ttf_thread SET date='{$post[2]}' WHERE thread_id='{$post[0]}' LIMIT 1");
			if ($result_u == 1) echo "success: <b>UPDATE ttf_thread SET date='$post[2]' WHERE thread_id='$post[0]' LIMIT 1</b><br />\n";
			else echo "failure: <b>UPDATE ttf_thread SET date='$post[2]' WHERE thread_id='$post[0]' LIMIT 1</b><br />\n";
			$k = 1;
		};
	};
	if ($k != 1) echo "all thread dates current.<br />\n";
} else if ($fix == "thread_posts") {
	$sql =	"SELECT ttf_thread.thread_id, ttf_thread.posts, COUNT(*) ".
		"FROM ttf_thread, ttf_post ".
		"WHERE ttf_thread.thread_id=ttf_post.thread_id ".
		"GROUP BY thread_id";
	$result = mysql_query($sql);
	while ($thread = mysql_fetch_array($result)) {
		if ($thread[1] != $thread[2]) {
			$result_u = mysql_query("UPDATE ttf_thread SET posts='{$thread[2]}' WHERE thread_id='{$thread[0]}' LIMIT 1");
			if ($result_u == 1) echo "success: fix post count on thread id=$thread[0]<br />\n";
			else  echo "failure: fix post count on thread id=$thread[0]<br />\n";
			$k = 1;
		};
	};
	if ($k != 1) echo "all thread post counts current.<br />\n";
} else if ($fix == "forum_date") {
	$sql =	"SELECT ttf_forum.forum_id, ttf_forum.date, ttf_thread.date ".
		"FROM ttf_forum, ttf_thread ".
		"WHERE ttf_forum.forum_id=ttf_thread.forum_id";
	$result = mysql_query($sql);
	while ($thread = mysql_fetch_array($result)) {
		if ($thread[1] < $thread[2]) {
			$result_u = mysql_query("UPDATE ttf_forum SET date='{$thread[2]}' WHERE forum_id='{$thread[0]}' LIMIT 1");
			if ($result_u == 1) echo "success: fix date on forum id=$thread[0]<br />\n";
			else echo "failure: fix date on forum id=$thread[0]<br />\n";
			$k = 1;
		};
	};
	if ($k != 1) echo "all forum dates current.<br />\n";
};
?>
</td></tr></table>
<?php
mysql_close();
include "footer.inc.php";
?>