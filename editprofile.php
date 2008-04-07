<?php
/* think tank forums
 *
 * editprofile.php
 */

$ttf_label = "edit your profile";
$ttf_title = $ttf_label;

require_once "include_common.php";

if (!isset($ttf["uid"])) {

    message($ttf_label, $error_die_text,
            "you must login before you may edit your profile.");
    die();

};

// awesome function for avatar deletion
function delete_avatar() {

    global $ttf;

    $sql = "SELECT avatar_type FROM ttf_user WHERE user_id='{$ttf["uid"]}'";
    if (!$result = mysql_query($sql)) showerror();
    list($ext) = mysql_fetch_row($result);

    if (!empty($ext)) {

        // if the user has an avatar
        
        if (!unlink("avatars/".$ttf["uid"].".".$ext)) {

            // we couldn't delete their avatar
            return FALSE;
        
        } else {
            
            $sql = "UPDATE ttf_user SET avatar_type=NULL ".
                   "WHERE user_id='{$ttf["uid"]}'";
            if (!$result = mysql_query($sql)) {

                // we couldn't reflect the deletion in the db                
                showerror();

            } else {

                // everything worked
                return TRUE;

            };

        };
   
    } else {

        // there was no avatar to delete,
        // so we are still happy people
        return TRUE;

    };

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

        // update the user's last rev date
        $sql = "UPDATE ttf_user                 ".
               "SET rev_date=UNIX_TIMESTAMP()  ".
               "WHERE user_id={$ttf["uid"]}     ";
        if (!$result = mysql_query($sql)) showerror();

        $sql = "UPDATE ttf_user SET profile='".clean(outputbody($profile))."' WHERE user_id='{$ttf["uid"]}'";
        
        if (!$result = mysql_query($sql)) {
            
            showerror();

        } else {

            $messages[] = "your profile has been successfully changed.";

        };

    };

    
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
                        
                    $expire = time() + 31556926;
                    $cookie = serialize(array($user["user_id"], $encrypt));
                    setcookie("thinktank", $cookie, $expire);

                    $messages[] = "your password has been successfully changed. your cookie has been updated.";

                };

            } else {

                $messages[] = "<span class=\"error\">your password contained invalid characters and was not changed.</span>";

            };

        } else {

            $messages[] = "<span class=\"error\">your password did not match and was not changed.</span>";

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

        // update the user's last rev date
        $sql = "UPDATE ttf_user                 ".
               "SET rev_date=UNIX_TIMESTAMP()  ".
               "WHERE user_id={$ttf["uid"]}     ";
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

        if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {

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

    // delete avatar ************************************************

    if (isset($_POST["deleteavatar"])) {

        if (delete_avatar()) {

            $messages[] = "your avatar has been successfully deleted.";

        } else {

            $messages[] = "<span class=\"error\">there was an error trying to delete your avatar.</span>";

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

                if (!delete_avatar()) {

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

    require_once "include_header.php";

?>
            <form action="editprofile.php" method="post" enctype="multipart/form-data">
                <div class="contenttitle">edit your actual profile</div>
                <div class="contentbox" style="text-align: center;">
                    <textarea class="profile" cols="70" rows="7" name="profile" wrap="virtual"><?php echo output($profile_head); ?></textarea><br />
                </div>
                <table cellspacing="1" class="content">
                    <tr>
                        <th colspan="2">change your password</th>
                    </tr>
                    <tr>
                        <td>enter your new password:</td>
                        <td><input type="password" name="password0" maxlength="32" size="16" /></td>
                    </tr>
                    <tr>
                        <td>corfirm the new password:</td>
                        <td><input type="password" name="password1" maxlength="32" size="16" /></td>
                    </tr>
                </table>
                <table cellspacing="1" class="content">
                    <tr>
                        <th colspan="2">edit things to see or use</th>
                    </tr>
                    <tr>
                        <td>user title:</td>
                        <td><input type="text" name="title" maxlength="64" size="48" value="<?php echo output($title_head); ?>" /></td>
                    </tr>
                    <tr>
                        <td>email:</td>
                        <td><input type="text" name="email" maxlength="96" size="48" value="<?php echo output($user["email"]); ?>" /></td>
                    </tr>
                    <tr>
                        <td><span class="tip" title="your new avatar must be 30px square, and it must be a jpg, gif, or png.">new avatar</span>:</td>
                        <td>
                            <input type="file" name="avatar" size="48" />
                            <input type="hidden" name="MAX_FILE_SIZE" value="64000" />
                        </td>
                    </tr>
                    <tr>
                        <td><span class="tip" title="check the box to delete your current avatar.">delete avatar</span>:</td>
                        <td><input type="checkbox" name="deleteavatar" value="true" /></td>
                    </tr>
                    <tr>
                        <td>time zone:</td>
                        <td>
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
                        </td>
                    </tr>
                </table>
                <div id="editprofile_button">
                    <input class="editprofile" type="submit" value="apply changes" />
                </div>
                <input type="hidden" name="edit" value="true" />
            </form>
<?php

};

require_once "include_footer.php";

?>
