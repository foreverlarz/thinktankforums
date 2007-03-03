<?php
/* think tank forums
 *
 * profile.php
 *
 * this script accepts the following variables:
 * 	$_GET["user_id"]	clean
 *
 * sanity checks include:
 * 	valid user exists
 * 	includes are REQUIRED
 */
 require "common.inc.php"; 
 $label = "user profile";
 require "header.inc.php";	  
 $user_id = clean($_GET["user_id"]);
 $result = mysql_query("SELECT user_id, username, avatar_type, title, profile FROM ttf_user WHERE user_id='$user_id'");
 $user = mysql_fetch_array($result);
 mysql_free_result($result);
 if (isset($user["user_id"])) {
?>
   <div class="userbar">
    <div class="userbar_left">
<?php
		if (isset($user["avatar_type"])) {
?>
     <img src="avatars/<?php echo $user["user_id"].".".$user["avatar_type"]; ?>" alt="av" width="30" height="30" />
<?php
		} else {
			echo "&nbsp;\n";
		};
?>
    </div>
    <span class="username"><?php echo output($user["username"]); ?></span><br />
    <?php echo output($user["title"])."\n"; ?>
   </div>
   <div class="contentbox">
<?php echo outputbody($user["profile"])."\n"; ?>
   </div>
<?php
 } else { message("user profile","error!","not a valid user!",0,0); };
 require "footer.inc.php";
?>
