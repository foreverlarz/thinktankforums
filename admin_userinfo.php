<?php
/* think tank forums
 *
 * admin_userinfo.php
 */
 require "common.inc.php";
 die("broken");
 admin();
 $label = "administration » user info";
 require "header.inc.php";
 $user_id = clean($_GET["user_id"]);
 $result = mysql_query("SELECT * FROM ttf_user WHERE user_id='$user_id'");
 $user = mysql_fetch_array($result);
 mysql_free_result($result);
 if (isset($user["user_id"])) {
  if ($user["register_date"] == 0) {
   $date_reg = "never";
  } else {
   $date_reg = strtolower(date("M j, Y, g\:i a", $user["register_date"] + 3600*$ttf["time_zone"]));
  };
  if ($user["visit_date"] == 0) {
   $date_visit = "never";
  } else {
   $date_visit = strtolower(date("M j, Y, g\:i a", $user["visit_date"] + 3600*$ttf["time_zone"]));
  };
  if ($user["post_date"] == 0) {
   $date_post = "never";
  } else {
   $date_post = strtolower(date("M j, Y, g\:i a", $user["post_date"] + 3600*$ttf["time_zone"]));
  };
?>
   <div class="sidebox"><a href="admin_ban.php?user_id=<?php echo $user["user_id"]; ?>"><b>BAN THIS USER</b></a></div>
   <table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv"><td><b>field</b></td><td><b>data</b></td>
    <tr class="small"><td><b>user_id</b></td><td><?php echo $user["user_id"]; ?></td></tr>
    <tr class="small"><td><b>username</b></td><td><?php echo $user["username"]; ?></td></tr>
    <tr class="small"><td><b>password</b></td><td><?php echo $user["password"]; ?></td></tr>
    <tr class="small"><td><b>email</b></td><td><?php echo $user["email"]; ?></td></tr>
    <tr class="small"><td><b>title</b></td><td><?php echo $user["title"]; ?></td></tr>
    <tr class="small"><td><b>avatar</b></td><td><?php if (isset($user["avatar_type"])) echo "<img src=\"avatars/".$user["user_id"].".".$user["avatar_type"]."\" alt=\"avatar!\" width=\"30\" height=\"30\" class=\"avatar\" />"; ?></td></tr>
    <tr class="small"><td><b>avatar_type</b></td><td><?php echo $user["avatar_type"]; ?></td></tr>
    <tr class="small"><td><b>time_zone</b></td><td><?php echo $user["time_zone"]; ?></td></tr>
    <tr class="small"><td><b>register_date</b></td><td><?php echo $date_reg; ?></td></tr>
    <tr class="small"><td><b>visit_date</b></td><td><?php echo $date_visit; ?></td></tr>
    <tr class="small"><td><b>post_date</b></td><td><?php echo $date_post; ?></td></tr>
    <tr class="small"><td><b>register_ip</b></td><td><?php echo $user["register_ip"]; ?></td></tr>
    <tr class="small"><td><b>visit_ip</b></td><td><?php echo $user["visit_ip"]; ?></td></tr>
    <tr class="small"><td><b>profile</b></td><td><?php echo $user["profile"]; ?></td></tr>
    <tr class="small"><td><b>banned</b></td><td><?php echo $user["banned"]; ?></td></tr>
   </table>
   <table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv"><td><b>technical implications of banning a user</b></td>
    <tr class="small"><td>
     post ips are ips that are associated with a post made by this user.<br />
     visit ips are recorded whenever this user loads a ttf page or avatar.<br /><br />
     clicking the "ban this user" link will:
     <ul>
      <li>change `banned` to 'yes'.</li>
      <li>change `title` to 'non-user'.</li>
      <li>copy all post ips rows into the banned ips table.</li>
      <li>copy all visit ips rows into the banned ips table.</li>
      <li>copy the register_ip into the banned ips table.</li>
      <li>copy the visit_ip into the banned ips table.</li>
     </ul>
     remember:
     <b><ul>
      <li>bans take effect immediately!</li>
      <li>user title information is LOST!</li>
      <li>there is currently no "un-ban" feature for ttf!</li>
     </ul></b>
    </td></tr>
   </table>
   <table border="0" cellpadding="2" cellspacing="1" class="shift">
    <tr class="mediuminv"><td><b>post ips</b></td><td><b>last used at</b></td>
<?php
	$sql = "SELECT ip, MAX(date) AS maxdate FROM ttf_post WHERE author_id = '$user_id' AND ip != 'NULL' GROUP BY ip ORDER BY maxdate DESC";
	$result = mysql_query($sql);
	while ($post = mysql_fetch_array($result)) {
		$date = strtolower(date("M j, Y, g\:i a", $post["maxdate"] + 3600*$ttf["time_zone"]));
?>
    <tr class="small"><td><?php echo $post["ip"]; ?></td><td><?php echo $date; ?></td></tr>
<?php
	};
	mysql_free_result($result);
?>
    <tr class="mediuminv"><td><b>visit ips</b></td><td><b>last used at</b></td>
<?php
	$sql = "SELECT ip, MAX(date) AS maxdate FROM ttf_visit WHERE user_id = '$user_id' GROUP BY ip ORDER BY maxdate DESC";
	$result = mysql_query($sql);
	while ($visit = mysql_fetch_array($result)) {
		$date = strtolower(date("M j, Y, g\:i a", $visit["maxdate"] + 3600*$ttf["time_zone"]));
?>
    <tr class="small"><td><?php echo $visit["ip"]; ?></td><td><?php echo $date; ?></td></tr>
<?php
	};
	mysql_free_result($result);
?>
   </table>
<?php
 } else { message("user profile","error!","not a valid user!",0,0); };
 require "footer.inc.php";
?>