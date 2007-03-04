<?php
/* think tank forums
 *
 * admin_ban.php
 */
 require "include_common.php";
 admin();
 $label = "administration » user ban";
 require "include_header.php";
 $user_id = clean($_GET["user_id"]);
?>
  <table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
   <tr class="mediuminv"><td width="594" colspan="5"><b>game over for `user_id`='<?php echo $user_id; ?>'</b></td></tr>
   <tr class="medium"><td>
<?php
 $i=0;
 $result = mysql_query("SELECT register_ip, visit_ip FROM ttf_user WHERE user_id='$user_id'");
 $user = mysql_fetch_array($result);
 mysql_free_result($result);
 if (isset($user["register_ip"]) && $user["register_ip"] != "") {
	$resulta = mysql_query("UPDATE ttf_user SET perm='banned' WHERE user_id='$user_id'");
        if ($resulta == 1) echo "user.perm->'banned'<br />\n";
	$resultb = mysql_query("REPLACE INTO ttf_banned VALUES ('$user_id', '{$user["register_ip"]}')");
        if ($resultb == 1) echo "banned+={$user["register_ip"]}R<br />\n";
	$resultc = mysql_query("REPLACE INTO ttf_banned VALUES ('$user_id', '{$user["visit_ip"]}')");
        if ($resultc == 1) echo "banned+={$user["visit_ip"]}V<br />\n";
	$sql = "SELECT ip FROM ttf_post WHERE author_id='$user_id' AND ip != 'NULL' GROUP BY ip";
	$resultd = mysql_query($sql);
	while ($post = mysql_fetch_array($resultd)) {
	 $resultm[i] = mysql_query("REPLACE INTO ttf_banned VALUES ('$user_id', '{$post["ip"]}')");
         if ($resultm[i] == 1) echo "banned+={$post["ip"]}p<br />\n";
	 mysql_free_result($resultm[i]);
	 $i++;
	};
	mysql_free_result($resultd);
	$sql = "SELECT ip FROM ttf_visit WHERE user_id='$user_id' GROUP BY ip";
	$resulte = mysql_query($sql);
	while ($visit = mysql_fetch_array($resulte)) {
	 $resultm[i] = mysql_query("REPLACE INTO ttf_banned VALUES ('$user_id', '{$visit["ip"]}')");
         if ($resultm[i] == 1) echo "banned+={$post["ip"]}v<br />\n";
	 mysql_free_result($resultm[i]);
	 $i++;
	};
	mysql_free_result($resulte);
?>
    <b>DONE.</b>
   </td></tr>
  </table>
<?php
 } else { message("user information","error!","the `user_id` you provided was invalid!",0,0); };
 require "include_footer.php";
?>
