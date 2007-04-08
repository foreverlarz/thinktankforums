<?php
/* think tank forums
 *
 * editprofile.php
 */
 require_once "include_common.php";	  
 $label = "edit your profile";
 $title = $label;
 require_once "include_header.php";
 if (isset($ttf["uid"])) {
  $edit = clean($_POST["edit"]);
  if ($edit == "password") {			//////// EDIT PASSWORD ////////
   $pass0 = $_POST["password0"];
   $pass1 = $_POST["password1"];
   if ($pass0 == $pass1) {
    if ($pass0 != "") {
     if ($pass0 == clean($pass0)) {
      $encrypt = sha1(clean($pass0));
      $result = mysql_query("UPDATE ttf_user SET password='$encrypt' WHERE user_id='{$ttf["uid"]}'");
      if ($result == 1) message("edit your profile","success!","your password has been successfully changed.");
      else message("edit your profile","error!","the password change was unsuccessful. error unknown!");
     } else { message("edit your profile","error!","your password contained invalid characters and was not changed."); };
    } else { message("edit your profile","error!","you password cannot be null and was not changed."); };
   } else { message("edit your profile","error!","your password did not match and was not changed."); };
  } else if ($edit == "avatar") {		//////// EDIT AVATAR ////////
   if ($_FILES["avatar"]["size"] != 0) {
    if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == 1) $ext = "gif";
    if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == 2) $ext = "jpg";
    if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == 3) $ext = "png";
    if ($ext == "gif" || $ext == "jpg" || $ext == "png") {
     list($x, $y) = getimagesize($_FILES["avatar"]["tmp_name"]);
     if ($x == 30 && $y == 30) {
      if (move_uploaded_file($_FILES["avatar"]["tmp_name"], "avatars/".$ttf["uid"].".".$ext)) {
       if ($resulta = mysql_query("UPDATE ttf_user SET avatar_type='$ext' WHERE user_id='{$ttf["uid"]}'")) {
        message("edit your profile","success!","your avatar has been changed.");
       } else { message("edit your profile","error!","the avatar change was unsuccessful. error inserting avatar!"); };
      } else { message("edit your profile","error!","the avatar change was unsuccessful. attack?"); };
     } else { message("edit your profile","error!","image uploaded is not 30px sq."); };
    } else { message("edit your profile","error!","image uploaded is not a gif, png, or jpeg."); };
   } else { message("edit your profile","error!","no image specified."); };
  } else if ($edit == "profile") {		//////// EDIT PROFILE ////////
   $profile = clean($_POST["profile"]);
   $result = mysql_query("UPDATE ttf_user SET profile='$profile' WHERE user_id='{$ttf["uid"]}'");
   if ($result == 1) message("edit your profile","success!","your profile was edited.");
   else message("edit your profile","error!","your profile edit was unsuccessful. error unknown!");
  } else if ($edit == "title") {		//////// EDIT USER TITLE ////////
   $title = clean($_POST["title"]);
   $result = mysql_query("UPDATE ttf_user SET title='$title' WHERE user_id='{$ttf["uid"]}'");
   if ($result == 1) message("edit your profile","success!","your title was edited.");
   else message("edit your profile","error!","your title edit was unsuccessful. error unknown!");
  } else if ($edit == "zone") {			//////// EDIT TIME ZONE ////////
   $zone = clean($_POST["zone"]);
   $result = mysql_query("UPDATE ttf_user SET time_zone='$zone' WHERE user_id='{$ttf["uid"]}'");
   if ($result == 1) message("edit your profile","success!","your time zone was changed.");
   else message("edit your profile","error!","your time zone change was unsuccessful. error unknown!");
  } else if ($edit == "email") {		//////// EDIT E-MAIL ////////
   $email0 = $_POST["email0"];
   $email1 = $_POST["email1"];
   if ($email0 == $email1) {
    if ($email0 != "") {
     if ($email0 == clean($email0)) {
	// do something....
     } else { message("edit your profile","error!","your e-mail contained invalid characters and was not changed."); };
    } else { message("edit your profile","error!","your e-mail cannot be null and was not changed."); };
   } else { message("edit your profile","error!","your e-mail did not match and was not changed."); };
  } else {
   $result = mysql_query("SELECT * FROM ttf_user WHERE user_id='{$ttf["uid"]}' LIMIT 1");
   $user = mysql_fetch_array($result);
   mysql_free_result($result);
?>

            <form action="editprofile.php" method="post">
                <div class="contenttitle">edit your actual profile</div>
                <div class="contentbox" style="text-align: center;">
                    <textarea class="profile" cols="70" rows="7" name="profile" wrap="virtual"><?php echo output($user["profile"]); ?></textarea><br />
                    <input type="hidden" name="edit" value="profile" />
                    <input type="submit" value="update" />
                </div>
            </form>

            <form action="editprofile.php" method="post">
                <div class="contenttitle">change your user title</div>
                <div class="contentbox" style="text-align: center;">
                    <input type="text" name="title" maxlength="96" size="64" value="<?php echo output($user["title"]); ?>" />
                    <input type="submit" value="change" />
                    <input type="hidden" name="edit" value="title" />
                </div>
            </form>

            <form action="editprofile.php" method="post">
                <div class="contenttitle">change your time zone</div>
                <div class="contentbox" style="text-align: center;">
                    <select name="zone">
                        <option value="-12"<?php if($user["time_zone"]==-12) echo " selected=\"selected\""; ?>>UTC-12</option>
                        <option value="-11"<?php if($user["time_zone"]==-11) echo " selected=\"selected\""; ?>>UTC-11</option>
                        <option value="-10"<?php if($user["time_zone"]==-10) echo " selected=\"selected\""; ?>>UTC-10</option>
                        <option value="-9.5"<?php if($user["time_zone"]==-9.5) echo " selected=\"selected\""; ?>>UTC-09:30</option>
                        <option value="-9"<?php if($user["time_zone"]==-9) echo " selected=\"selected\""; ?>>UTC-09</option>
                        <option value="-8"<?php if($user["time_zone"]==-8) echo " selected=\"selected\""; ?>>UTC-08</option>
                        <option value="-7"<?php if($user["time_zone"]==-7) echo " selected=\"selected\""; ?>>UTC-07</option>
                        <option value="-6"<?php if($user["time_zone"]==-6) echo " selected=\"selected\""; ?>>UTC-06</option>
                        <option value="-5"<?php if($user["time_zone"]==-5) echo " selected=\"selected\""; ?>>UTC-05</option>
                        <option value="-4"<?php if($user["time_zone"]==-4) echo " selected=\"selected\""; ?>>UTC-04</option>
                        <option value="-3.5"<?php if($user["time_zone"]==-3.5) echo " selected=\"selected\""; ?>>UTC-03:30</option>
                        <option value="-3"<?php if($user["time_zone"]==-3) echo " selected=\"selected\""; ?>>UTC-03</option>
                        <option value="-2"<?php if($user["time_zone"]==-2) echo " selected=\"selected\""; ?>>UTC-02</option>
                        <option value="-1"<?php if($user["time_zone"]==-1) echo " selected=\"selected\""; ?>>UTC-01</option>
                        <option value="0"<?php if($user["time_zone"]==0) echo " selected=\"selected\""; ?>>UTC</option>
                        <option value="1"<?php if($user["time_zone"]==1) echo " selected=\"selected\""; ?>>UTC+01</option>
                        <option value="2"<?php if($user["time_zone"]==2) echo " selected=\"selected\""; ?>>UTC+02</option>
                        <option value="3"<?php if($user["time_zone"]==3) echo " selected=\"selected\""; ?>>UTC+03</option>
                        <option value="3.5"<?php if($user["time_zone"]==3.5) echo " selected=\"selected\""; ?>>UTC+03:30</option>
                        <option value="4"<?php if($user["time_zone"]==4) echo " selected=\"selected\""; ?>>UTC+04</option>
                        <option value="4.5"<?php if($user["time_zone"]==4.5) echo " selected=\"selected\""; ?>>UTC+04:30</option>
                        <option value="5"<?php if($user["time_zone"]==5) echo " selected=\"selected\""; ?>>UTC+05</option>
                        <option value="5.5"<?php if($user["time_zone"]==5.5) echo " selected=\"selected\""; ?>>UTC+05:30</option>
                        <option value="5.75"<?php if($user["time_zone"]==5.75) echo " selected=\"selected\""; ?>>UTC+05:45</option>
                        <option value="6"<?php if($user["time_zone"]==6) echo " selected=\"selected\""; ?>>UTC+06</option>
                        <option value="6.5"<?php if($user["time_zone"]==6.5) echo " selected=\"selected\""; ?>>UTC+06:30</option>
                        <option value="7"<?php if($user["time_zone"]==7) echo " selected=\"selected\""; ?>>UTC+07</option>
                        <option value="8"<?php if($user["time_zone"]==8) echo " selected=\"selected\""; ?>>UTC+08</option>
                        <option value="8.75"<?php if($user["time_zone"]==8.75) echo " selected=\"selected\""; ?>>UTC+08:45</option>
                        <option value="9"<?php if($user["time_zone"]==9) echo " selected=\"selected\""; ?>>UTC+09</option>
                        <option value="9.5"<?php if($user["time_zone"]==9.5) echo " selected=\"selected\""; ?>>UTC+09:30</option>
                        <option value="10"<?php if($user["time_zone"]==10) echo " selected=\"selected\""; ?>>UTC+10</option>
                        <option value="10.5"<?php if($user["time_zone"]==10.5) echo " selected=\"selected\""; ?>>UTC+10:30</option>
                        <option value="11"<?php if($user["time_zone"]==11) echo " selected=\"selected\""; ?>>UTC+11</option>
                        <option value="11.5"<?php if($user["time_zone"]==11.5) echo " selected=\"selected\""; ?>>UTC+11:30</option>
                        <option value="12"<?php if($user["time_zone"]==12) echo " selected=\"selected\""; ?>>UTC+12</option>
                        <option value="12.75"<?php if($user["time_zone"]==12.75) echo " selected=\"selected\""; ?>>UTC+12:45</option>
                        <option value="13"<?php if($user["time_zone"]==13) echo " selected=\"selected\""; ?>>UTC+13</option>
                        <option value="14"<?php if($user["time_zone"]==14) echo " selected=\"selected\""; ?>>UTC+14</option>
                    </select>
                    <input type="submit" value="change" />
                    <input type="hidden" name="edit" value="zone" />
                </div>
            </form>

            <form action="editprofile.php" method="post" enctype="multipart/form-data">
                <div class="contenttitle">
                    change your avatar <span class="small">(30px square; gif, jpg, png)</span>
                </div>
                <div class="contentbox" style="text-align: center;">
                    <input type="file" name="avatar" size="28" />
                    <input type="submit" value="upload" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="10000" />
                    <input type="hidden" name="edit" value="avatar" />
                </div>
            </form>

            <form action="editprofile.php" method="post">
                <table cellspacing="1" class="content">
                    <tr>
                        <th colspan="2">change your password</th>
                    </tr>
                    <tr>
                        <td>type once:</td>
                        <td><input type="password" name="password0" maxlength="32" size="16" /></td>
                    </tr>
                    <tr>
                        <td>and again:</td>
                        <td><input type="password" name="password1" maxlength="32" size="16" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input type="submit" value="change" />
                            <input type="hidden" name="edit" value="password" />
                        </td>
                    </tr>
                </table>
            </form>

<?php
  };
 } else { message("edit your profile","error!","you must login before you may edit your profile."); };
 require_once "include_footer.php";
?>
