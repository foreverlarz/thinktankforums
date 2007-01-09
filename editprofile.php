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
 *
 * editprofile.php rev0002
 *
 * AUDITED BY JLR 200611250210
 *
 * this script accepts the following variables:
 * 	$_POST["edit"]			--
 *	$_POST["password0"]		clean
 *	$_POST["password1"]		clean
 *	$_FILES["avatar"]["size"]	--
 *	$_FILES["avatar"]["tmp_name"]	bin2hex
 *	$_POST["profile"]		clean
 *	$_POST["title"]			clean
 *	$_POST["zone"]			clean
 *	$_POST["email0"]		clean
 *	$_POST["email1"]		clean
 *
 * sanity checks include:
 * 	passwords match
 * 	passwords are not blank
 * 	passwords do not contain invalid characters
 *	avatar is not 0 bytes
 *	avatar is gif||jpg||png
 *	avatar is 30px by 30px
 * 	includes are REQUIRED
 */
 require "common.inc.php";	  
 $label = "edit your profile";
 require "header.inc.php";
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
      if ($result == 1) message("edit your profile","success!","your password has been successfully changed.",0,0);
      else message("edit your profile","error!","the password change was unsuccessful. error unknown!",0,0);
     } else { message("edit your profile","error!","your password contained invalid characters and was not changed.",0,0); };
    } else { message("edit your profile","error!","you password cannot be null and was not changed.",0,0); };
   } else { message("edit your profile","error!","your password did not match and was not changed.",0,0); };
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
        message("edit your profile","success!","your avatar has been changed.",0,0);
       } else { message("edit your profile","error!","the avatar change was unsuccessful. error inserting avatar!",0,0); };
      } else { message("edit your profile","error!","the avatar change was unsuccessful. attack?",0,0); };
     } else { message("edit your profile","error!","image uploaded is not 30px sq.",0,0); };
    } else { message("edit your profile","error!","image uploaded is not a gif, png, or jpeg.",0,0); };
   } else { message("edit your profile","error!","no image specified.",0,0); };
  } else if ($edit == "profile") {		//////// EDIT PROFILE ////////
   $profile = clean($_POST["profile"]);
   $result = mysql_query("UPDATE ttf_user SET profile='$profile' WHERE user_id='{$ttf["uid"]}'");
   if ($result == 1) message("edit your profile","success!","your profile was edited.",0,0);
   else message("edit your profile","error!","your profile edit was unsuccessful. error unknown!",0,0);
  } else if ($edit == "title") {		//////// EDIT USER TITLE ////////
   $title = clean($_POST["title"]);
   $result = mysql_query("UPDATE ttf_user SET title='$title' WHERE user_id='{$ttf["uid"]}'");
   if ($result == 1) message("edit your profile","success!","your title was edited.",0,0);
   else message("edit your profile","error!","your title edit was unsuccessful. error unknown!",0,0);
  } else if ($edit == "zone") {			//////// EDIT TIME ZONE ////////
   $zone = clean($_POST["zone"]);
   $result = mysql_query("UPDATE ttf_user SET time_zone='$zone' WHERE user_id='{$ttf["uid"]}'");
   if ($result == 1) message("edit your profile","success!","your time zone was changed.",0,0);
   else message("edit your profile","error!","your time zone change was unsuccessful. error unknown!",0,0);
  } else if ($edit == "email") {		//////// EDIT E-MAIL ////////
   $email0 = $_POST["email0"];
   $email1 = $_POST["email1"];
   if ($email0 == $email1) {
    if ($email0 != "") {
     if ($email0 == clean($email0)) {
	// do something....
     } else { message("edit your profile","error!","your e-mail contained invalid characters and was not changed.",0,0); };
    } else { message("edit your profile","error!","your e-mail cannot be null and was not changed.",0,0); };
   } else { message("edit your profile","error!","your e-mail did not match and was not changed.",0,0); };
  } else {
   $result = mysql_query("SELECT * FROM ttf_user WHERE user_id='{$ttf["uid"]}' LIMIT 1");
   $user = mysql_fetch_array($result);
   mysql_free_result($result);
?>
  <form action="editprofile.php" method="post"><table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv"><td width="594"><b>edit your actual profile</b></td></tr>
    <tr class="medium">
     <td align="center" width="594">
      <textarea class="profile" cols="70" rows="7" name="profile" wrap="virtual"><?php echo output($user["profile"]); ?></textarea>
     </td>
    </tr>
    <tr class="medium">
     <td align="center" width="594">
      <input type="hidden" name="edit" value="profile" />
      <input type="submit" value="edit!" />
     </td>
    </tr>
   </table></form>
  <form action="editprofile.php" method="post"><table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv"><td width="594" colspan="2"><b>change your user title</b></td></tr>
    <tr class="medium">
     <td align="center"><input type="text" name="title" maxlength="96" size="64" value="<?php echo output($user["title"]); ?>" /></td>
     <td align="center">
      <input type="hidden" name="edit" value="title" />
      <input type="submit" value="change!" />
     </td>
    </tr>
   </table></form>
  <form action="editprofile.php" method="post"><table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv"><td width="594" colspan="3"><b>change your time zone</b></td></tr>
    <tr class="medium">
     <td align="center">localize:</td>
     <td align="center"><select name="zone">
<!-- /////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////     THE FOLLOWING IS GPL     //////////////////////////////
//////////////////////////////Copyright (C) 2002-2005  Rickard Andersson
////////////////////////////////////////////////////////////////////////////////////// -->
	<option value="-12"<?php if($user["time_zone"]==-12) echo " selected=\"selected\""; ?>>-12</option>
	<option value="-11"<?php if($user["time_zone"]==-11) echo " selected=\"selected\""; ?>>-11</option>
	<option value="-10"<?php if($user["time_zone"]==-10) echo " selected=\"selected\""; ?>>-10</option>
	<option value="-9.5"<?php if($user["time_zone"]==-9.5) echo " selected=\"selected\""; ?>>-09.5</option>
	<option value="-9"<?php if($user["time_zone"]==-9) echo " selected=\"selected\""; ?>>-09</option>
	<option value="-8.5"<?php if($user["time_zone"]==-8.5) echo " selected=\"selected\""; ?>>-08.5</option>
	<option value="-8"<?php if($user["time_zone"]==-8) echo " selected=\"selected\""; ?>>-08 PST</option>
	<option value="-7"<?php if($user["time_zone"]==-7) echo " selected=\"selected\""; ?>>-07 MST</option>
	<option value="-6"<?php if($user["time_zone"]==-6) echo " selected=\"selected\""; ?>>-06 CST</option>
	<option value="-5"<?php if($user["time_zone"]==-5) echo " selected=\"selected\""; ?>>-05 EST</option>
	<option value="-4"<?php if($user["time_zone"]==-4) echo " selected=\"selected\""; ?>>-04 AST</option>
	<option value="-3.5"<?php if($user["time_zone"]==-3.5) echo " selected=\"selected\""; ?>>-03.5</option>
	<option value="-3"<?php if($user["time_zone"]==-3) echo " selected=\"selected\""; ?>>-03 ADT</option>
	<option value="-2"<?php if($user["time_zone"]==-2) echo " selected=\"selected\""; ?>>-02</option>
	<option value="-1"<?php if($user["time_zone"]==-1) echo " selected=\"selected\""; ?>>-01</option>
	<option value="0"<?php if($user["time_zone"]==0) echo " selected=\"selected\""; ?>>00 GMT</option>
	<option value="1"<?php if($user["time_zone"]==1) echo " selected=\"selected\""; ?>>+01 CET</option>
	<option value="2"<?php if($user["time_zone"]==2) echo " selected=\"selected\""; ?>>+02</option>
	<option value="3"<?php if($user["time_zone"]==3) echo " selected=\"selected\""; ?>>+03</option>
	<option value="3.5"<?php if($user["time_zone"]==3.5) echo " selected=\"selected\""; ?>>+03.5</option>
	<option value="4"<?php if($user["time_zone"]==4) echo " selected=\"selected\""; ?>>+04</option>
	<option value="4.5"<?php if($user["time_zone"]==4.5) echo " selected=\"selected\""; ?>>+04.5</option>
	<option value="5"<?php if($user["time_zone"]==5) echo " selected=\"selected\""; ?>>+05</option>
	<option value="5.5"<?php if($user["time_zone"]==5.5) echo " selected=\"selected\""; ?>>+05.5</option>
	<option value="6"<?php if($user["time_zone"]==6) echo " selected=\"selected\""; ?>>+06</option>
	<option value="6.5"<?php if($user["time_zone"]==6.5) echo " selected=\"selected\""; ?>>+06.5</option>
	<option value="7"<?php if($user["time_zone"]==7) echo " selected=\"selected\""; ?>>+07</option>
	<option value="8"<?php if($user["time_zone"]==8) echo " selected=\"selected\""; ?>>+08</option>
	<option value="9"<?php if($user["time_zone"]==9) echo " selected=\"selected\""; ?>>+09</option>
	<option value="9.5"<?php if($user["time_zone"]==9.5) echo " selected=\"selected\""; ?>>+09.5</option>
	<option value="10"<?php if($user["time_zone"]==10) echo " selected=\"selected\""; ?>>+10</option>
	<option value="10.5"<?php if($user["time_zone"]==10.5) echo " selected=\"selected\""; ?>>+10.5</option>
	<option value="11"<?php if($user["time_zone"]==11) echo " selected=\"selected\""; ?>>+11</option>
	<option value="11.5"<?php if($user["time_zone"]==11.5) echo " selected=\"selected\""; ?>>+11.5</option>
	<option value="12"<?php if($user["time_zone"]==12) echo " selected=\"selected\""; ?>>+12</option>
	<option value="13"<?php if($user["time_zone"]==13) echo " selected=\"selected\""; ?>>+13</option>
	<option value="14"<?php if($user["time_zone"]==14) echo " selected=\"selected\""; ?>>+14</option>
<!-- /////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////     THE PRECEDING IS GPL     //////////////////////////////
////////////////////////////////////////////////////////////////////////////////////// -->
      </select></td>
     <td align="center">
      <input type="hidden" name="edit" value="zone" />
      <input type="submit" value="change!" />
     </td>
    </tr>
   </table></form>
  <form action="editprofile.php" method="post" enctype="multipart/form-data"><table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv"><td width="594" colspan="3"><b>change your avatar</b> (30px square; gif, jpg, png)</td></tr>
    <tr class="medium">
     <td align="center">choose:</td>
     <td align="center"><input type="file" name="avatar" size="28" /></td>
     <td align="center">
      <input type="hidden" name="MAX_FILE_SIZE" value="10000" />
      <input type="hidden" name="edit" value="avatar" />
      <input type="submit" value="upload!" />
     </td>
    </tr>
   </table></form>
  <form action="editprofile.php" method="post"><table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv"><td width="594" colspan="5"><b>change your password</b></td></tr>
    <tr class="medium">
     <td align="center">type:</td>
     <td align="center"><input type="password" name="password0" maxlength="32" size="16" /></td>
     <td align="center">again:</td>
     <td align="center"><input type="password" name="password1" maxlength="32" size="16" /></td>
     <td align="center">
      <input type="hidden" name="edit" value="password" />
      <input type="submit" value="change!" />
     </td>
    </tr>
   </table></form>
<!--  <br />
  <form action="editprofile.php" method="post">
   <table border="0" cellpadding="2" cellspacing="1" width="600">
    <tr class="mediuminv"><td width="594" colspan="5"><b>change your e-mail address</b> (must be valid.)</td></tr>
    <tr class="medium">
     <td align="center">type:</td>
     <td align="center"><input type="text" name="email0" maxlength="128" size="25" /></td>
     <td align="center">again:</td>
     <td align="center"><input type="text" name="email1" maxlength="128" size="25" /></td>
     <td align="center">
      <input type="hidden" name="edit" value="email" />
      <input type="submit" value="change!" />
     </td>
    </tr>
   </table>
   </form>-->
<?php
  };
 } else { message("edit your profile","error!","you must login before you may edit your profile.",0,0); };
 mysql_close();
 require "footer.inc.php";
?>
