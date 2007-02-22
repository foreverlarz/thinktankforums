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
   <table border="0" cellpadding="1" cellspacing="0" class="shift" width="600">
    <tr>
     <td align="left" class="smallinv" rowspan="2" valign="bottom" width="34">
<?php
 if (isset($user["avatar_type"])) {
?>
        <img src="avatars/<?php echo $user["user_id"].".".$user["avatar_type"]; ?>" alt="avatar!" width="30" height="30" class="avatar" />
<?php
 } else { echo "&nbsp;\n"; };
?>
     </td>
     <td align="left" class="mediuminv" valign="middle" width="566"><b><?php echo output($user["username"]); ?></b></td>
    </tr>
    <tr>
     <td align="left" class="smallinv" valign="middle" width="566"><?php echo output($user["title"]); ?></td>
    </tr>
   </table>
   <div class="whitebox">
<?php echo outputbody($user["profile"])."\n"; ?>
   </div>
<?php
 } else { message("user profile","error!","not a valid user!",0,0); };
 require "footer.inc.php";
?>