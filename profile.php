<?php
/* think tank forums
 *
 * profile.php
 */

require "include_common.php"; 
$label = "user profile";
require "include_header.php";	  

$user_id = clean($_GET["user_id"]);

$sql = "SELECT user_id, username, avatar_type, title, profile ".
	"FROM ttf_user WHERE user_id='$user_id'";
if (!$result = mysql_query($sql)) showerror();
$user = mysql_fetch_array($result);
mysql_free_result($result);

// if a user was found with the given id
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
   <div class="contentbox_sm">
<?php echo outputbody($user["profile"])."\n"; ?>
   </div>
<?php
} else {

	message("user profile","fatal error","you must specify a valid user.", 0, 0);

};

require "include_footer.php";

?>
