<?php
/* think tank forums
 *
 * index.php
 */

require "include_common.php";
$label = $ttf_config["index_title"];
require "include_header.php";

?>
   <table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv">
     <td width="40"><b>&nbsp;</b></td>
     <td width="430"><b>forum</b></td>
     <td width="65"><b>threads</b></td>
     <td width="65"><b>posts</b></td>
    </tr>
<?php

$sql = "SELECT ttf_forum.*, ttf_forum_new.last_view FROM ttf_forum ".
	"LEFT JOIN ttf_forum_new ON ttf_forum_new.forum_id=ttf_forum.forum_id ".
	"AND ttf_forum_new.user_id='{$ttf["uid"]}'";
if (!$result = mysql_query($sql)) showerror();

// let's calculate total numbers of threads and posts
$tot_threads = 0;
$tot_posts = 0;

while ($forum = mysql_fetch_array($result)) {

	// reset $code from last time
	unset($code);

	// if user is logged in and hasn't read the forum since the last post
	if ($forum["last_view"] < $forum["date"] && isset($ttf["uid"])) {

		// make $code an arrow
		$code = "<img src=\"images/arrow.gif\" width=\"11\" height=\"11\" alt=\"new posts!\" /> ";
	
	} else {

		// or make it a space
		$code = "&nbsp;";
	
	};

	// add the forum's count to the total
	$tot_threads += $forum["threads"];
	$tot_posts += $forum["posts"];
	
	// ** should forum name and description be run through output() ? --jlr **

?>
    <tr class="medium">
     <td align="center"><?php echo $code; ?></td>
     <td>
      <a href="forum.php?forum_id=<?php echo $forum["forum_id"]; ?>"><?php echo $forum["name"]; ?></a><br />
      <span class="small">&nbsp;&nbsp;· <?php echo $forum["description"]; ?></span>
     </td>
     <td><?php echo $forum["threads"]; ?></td>
     <td><?php echo $forum["posts"]; ?></td>
    </tr>
<?php

};
mysql_free_result($result);

// let's find out if anyone is online
$sql = "SELECT user_id, username, perm FROM ttf_user ".
	"WHERE visit_date > UNIX_TIMESTAMP()-{$ttf_config["online_timeout"]} ".
	"ORDER BY username";
if (!$result = mysql_query($sql)) showerror();

// initialize $code and $i
$code = ""; $i = 0;

while ($user = mysql_fetch_array($result)) {

	// if there has been a previous printed, use a comma
	if ($i > 0) $code .= ", ";

	// if the user is an admin, make the name bold
	if ($user["perm"] == 'admin') {
		
		$code .= "<a href=\"profile.php?user_id={$user["user_id"]}\"><b>".output($user["username"])."</b></a>";
	
	} else {
		
		$code .= "<a href=\"profile.php?user_id={$user["user_id"]}\">".output($user["username"])."</a>";
	
	};

	// we printed a user
	$i = 1;

};

// if no users were printed, say so
if ($i == 0) $code = "noone is online.";

?>
    <tr class="mediuminv">
     <td><b>&nbsp;</b></td>
     <td><b>online persons</b></td>
     <td align="center" colspan="2"><b>totals</b></td>
    </tr>
    <tr class="medium">
     <td>&nbsp;</td>
     <td class="small"><?php echo $code; ?></td>
     <td><?php echo $tot_threads; ?></td>
     <td><?php echo $tot_posts; ?></td>
    </tr>
   </table>
<?php
 require "include_footer.php";
?>
