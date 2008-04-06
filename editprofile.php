<?php
/* think tank forums
 *
 * editprofile.php
 */

$ttf_label = "edit your profile";
$ttf_title = $ttf_label;

require_once "include_common.php";
require_once "include_header.php";

if (empty($ttf["uid"])) {

    message($ttf_label, $error_die_text,
            "you must login before you may edit your profile.");
    die();

};

// grab user info
$sql = "SELECT * FROM ttf_user WHERE user_id='{$ttf["uid"]}'";
if (!$result = mysql_query($sql)) showerror();
$user = mysql_fetch_array($result);

$sql = "SELECT body FROM ttf_revision ".
       "WHERE ref_id='{$ttf["uid"]}' && type='profile' ".
       "ORDER BY date DESC LIMIT 1";
if (!$result = mysql_query($sql)) showerror();
list($profile_head) = mysql_fetch_array($result);

$sql = "SELECT body FROM ttf_revision ".
       "WHERE ref_id='{$ttf["uid"]}' && type='title' ".
       "ORDER BY date DESC LIMIT 1";
if (!$result = mysql_query($sql)) showerror();
list($title_head) = mysql_fetch_array($result);

$messages = array();

if (isset($_POST["edit"])) {

    // change password **********************************************

    $pass0 = $_POST["password0"];
    $pass1 = $_POST["password1"];

    if (!empty($pass0)) {

        if ($pass0 == $pass1) {

            if ($pass0 == clean($pass0)) {
              
                $encrypt = sha1(clean($pass0));

                $sql = "UPDATE ttf_user SET password='$encrypt' WHERE user_id='{$ttf["uid"]}'";

                if (!$result = mysql_query($sql)) {

                    showerror();

                } else {
                        
                    // sql update is successfull, reset cookie
                    /* can't set headers after html has been printed to the agent
                     * i can fix this later. -- jlr
                    $expire = time() + 31556926;
                    $cookie = serialize(array($user["user_id"], $encrypt));
                    setcookie("thinktank", $cookie, $expire);
                    */
                    $messages[] = "your password has been successfully changed.";

                };

            } else {

                $messages[] = "<span class=\"error\">your password contained invalid characters and was not changed.</span>";

            };

        } else {

            $messages[] = "<span class=\"error\">your password did not match and was not changed.</span>";

        };

    };

    // edit profile *************************************************

    $profile = $_POST["profile"];

    if ($profile != $profile_head) {

        $sql = "INSERT INTO ttf_revision SET ".
               "ref_id='{$ttf["uid"]}', ".
               "type='profile', ".
               "author_id='{$ttf["uid"]}', ".
               "date=UNIX_TIMESTAMP(), ".
               "ip='{$_SERVER["REMOTE_ADDR"]}', ".
               "body='".clean($profile)."'";
        if (!$result = mysql_query($sql)) showerror();

        $sql = "UPDATE ttf_user SET profile='".clean(outputbody($profile))."' WHERE user_id='{$ttf["uid"]}'";
        
        if (!$result = mysql_query($sql)) {
            
            showerror();

        } else {

            $messages[] = "your profile has been successfully changed.";

        };

    };

    // edit title ***************************************************

    $title = $_POST["title"];
        
    if ($title != $title_head) {

        $sql = "INSERT INTO ttf_revision SET ".
               "ref_id='{$ttf["uid"]}', ".
               "type='title', ".
               "author_id='{$ttf["uid"]}', ".
               "date=UNIX_TIMESTAMP(), ".
               "ip='{$_SERVER["REMOTE_ADDR"]}', ".
               "body='".clean($title)."'";
        if (!$result = mysql_query($sql)) showerror();

        $sql = "UPDATE ttf_user SET title='".clean(output($title))."' WHERE user_id='{$ttf["uid"]}'";

        if (!$result = mysql_query($sql)) {
            
            showerror();

        } else {

            $messages[] = "your title has been successfully changed.";

        };

    };

    // change time zone *********************************************

    $zone = $_POST["zone"];

    if ($zone != $user["time_zone"]) {

        $sql = "UPDATE ttf_user SET time_zone='".clean($zone)."' WHERE user_id='{$ttf["uid"]}'";

        if (!$result = mysql_query($sql)) {
    
            showerror();

        } else {

            $messages[] = "your time zone has been successfully changed.";
        
        };

    };

    // change email *************************************************

    $email = $_POST["email"];

    if ($email != $user["email"]) {

        if (validateEmail($email) == TRUE) {

            $sql = "UPDATE ttf_user SET email='$email' WHERE user_id='{$ttf["uid"]}'";

            if (!$result = mysql_query($sql)) {

                showerror();

            } else {

                $messages[] = "your email address has been successfully updated.";

            };

        } else {

            $messages[] = "<span class=\"error\">your email address is not valid.</span>";

        };

    };

    // change avatar ************************************************

    if ($_FILES["avatar"]["size"] != 0) {

        unset($ext);

        if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == IMAGETYPE_GIF) $ext = "gif";
        if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == IMAGETYPE_JPEG) $ext = "jpg";
        if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == IMAGETYPE_PNG) $ext = "png";

        if (!empty($ext)) {

            list($x, $y) = getimagesize($_FILES["avatar"]["tmp_name"]);

            if ($x == 30 && $y == 30) {

                if (!deleteAvatar()) {

                    $messages[] = "<span class=\"error\">there was an error trying to delete your old avatar.</span>";

                };

                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], "avatars/".$ttf["uid"].".".$ext)) {

                    $sql = "UPDATE ttf_user SET avatar_type='$ext' WHERE user_id='{$ttf["uid"]}'";
                        
                    if (!$result = mysql_query($sql)) {

                        showerror();

                    } else {

                        $messages[] = "your avatar has been successfully changed.";

                    };
                    
                } else {
                        
                    $messages[] = "<span class=\"error\">the avatar change was unsuccessful.</span>";
                        
                };
                
            } else {
                    
                $messages[] = "<span class=\"error\">the image uploaded is not 30x30 pixels.</span>";
                
            };
            
        } else {
                
            $messages[] = "<span class=\"error\">the image uploaded is not a gif, png, or jpeg.</span>";
            
        };

    };

    if (empty($messages)) {

        message($ttf_label, "error", "you didn't make any changes.");

    } else {

        message($ttf_label, "results", $messages);

    };

} else {

?>
            <form action="editprofile.php" method="post" enctype="multipart/form-data">
                <div class="contenttitle">
                    change your avatar <span class="small">(30px square; gif, jpg, png)</span>
                </div>
                <div class="contentbox" style="text-align: center;">
                    <input type="file" name="avatar" size="28" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="10000" />
                    <input type="hidden" name="edit" value="avatar" />
                </div>
            </form>

            <form action="editprofile.php" method="post">
                <div class="contenttitle">change your email</div>
                <div class="contentbox" style="text-align: center;">
                    <input type="text" name="email" maxlength="96" size="40" value="<?php echo output($user["email"]); ?>" />
                </div>
                <div class="contenttitle">edit your actual profile</div>
                <div class="contentbox" style="text-align: center;">
                    <textarea class="profile" cols="70" rows="7" name="profile" wrap="virtual"><?php echo output($profile_head); ?></textarea><br />
                </div>
                <div class="contenttitle">change your user title</div>
                <div class="contentbox" style="text-align: center;">
                    <input type="text" name="title" maxlength="96" size="64" value="<?php echo output($title_head); ?>" />
                </div>
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
                </div>
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
                </table>
                <div class="contenttitle">apply changes</div>
                <div class="contentbox" style="text-align: center;">
                    <input type="hidden" name="edit" value="true" />
                    <input type="submit" value="apply" />
                </div>
            </form>
<?php

};

require_once "include_footer.php";

?>
