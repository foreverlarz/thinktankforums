<?php
/* think tank forums
 *
 * editprofile.php
 */

$ttf_title = $ttf_label = "edit your profile";

require_once "include_common.php";

// people must be logged in to use this script
kill_guests();



// awesome function for avatar deletion
function delete_avatar() {

    global $ttf;

    $sql = "SELECT avatar_type FROM ttf_user WHERE user_id='{$ttf["uid"]}'";
    if (!$result = mysql_query($sql)) showerror();
    list($ext) = mysql_fetch_row($result);

    // if the user has an avatar
    if (!empty($ext)) {

        // delete the avatar
        if (!unlink("avatars/".$ttf["uid"].".".$ext)) {

            // it wasn't deleted
            return FALSE;

        } else {

            // if successful, remove it from the user row
            $sql = "UPDATE ttf_user SET avatar_type=NULL    ".
                   "WHERE user_id='{$ttf["uid"]}'           ";
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



// select user info
$sql = "SELECT * FROM ttf_user WHERE user_id='{$ttf["uid"]}'";
if (!$result = mysql_query($sql)) showerror();
$user = mysql_fetch_array($result);



// select the current profile
$sql = "SELECT body FROM ttf_revision                   ".
       "WHERE ref_id='{$ttf["uid"]}' && type='profile'  ".
       "ORDER BY date DESC LIMIT 1                      ";
if (!$result = mysql_query($sql)) showerror();
list($profile_head) = mysql_fetch_array($result);



// select the current user title
$sql = "SELECT body FROM ttf_revision                   ".
       "WHERE ref_id='{$ttf["uid"]}' && type='title'    ".
       "ORDER BY date DESC LIMIT 1                      ";
if (!$result = mysql_query($sql)) showerror();
list($title_head) = mysql_fetch_array($result);



// create an empty array of messages
$messages = array();



if (isset($_POST["edit"])) {

    // edit profile *************************************************

    $profile = $_POST["profile"];

    if (strcmp($profile, $profile_head) != 0) {

        // insert the profile as a new revision
        $sql = "INSERT INTO ttf_revision SET    ".
               "ref_id='{$ttf["uid"]}',         ".
               "type='profile',                 ".
               "author_id='{$ttf["uid"]}',      ".
               "date=UNIX_TIMESTAMP(),          ".
               "ip='{$_SERVER["REMOTE_ADDR"]}', ".
               "body='".clean($profile)."'      ";
        if (!$result = mysql_query($sql)) showerror();

        // update the user's last rev date
        $sql = "UPDATE ttf_user                 ".
               "SET rev_date=UNIX_TIMESTAMP()   ".
               "WHERE user_id={$ttf["uid"]}     ";
        if (!$result = mysql_query($sql)) showerror();

        // update the user's profile with a formatted version
        $sql = "UPDATE ttf_user                                 ".
               "SET profile='".clean(outputbody($profile))."'   ".
               "WHERE user_id='{$ttf["uid"]}'                   ";

        if (!$result = mysql_query($sql)) {

            showerror();

        } else {

            $messages[] = "your profile has been successfully changed.";

        };

    };

    // change password **********************************************

    $pass0 = $_POST["password0"];
    $pass1 = $_POST["password1"];

    if (!empty($pass0) || !empty($pass1)) {

        if (strcmp($pass0, $pass1) == 0) {

            if (strcmp($pass0, clean($pass0)) == 0) {

                $encrypt = sha1(clean($pass0));     // this line should be reconsidered. --jlr

                $sql = "UPDATE ttf_user SET password='$encrypt' WHERE user_id='{$ttf["uid"]}'";

                if (!$result = mysql_query($sql)) {

                    showerror();

                } else {

                    $expire = time() + $ttf_cfg["cookie_time"];
                    $cookie = serialize(array($user["user_id"], $encrypt));
                    setcookie($ttf_cfg["cookie_name"].'-pair', $cookie, $expire, $ttf_cfg["cookie_path"], $ttf_cfg["cookie_domain"], $ttf_cfg["cookie_secure"]);
                    setcookie($ttf_cfg["cookie_name"].'-user', 'TRUE',  $expire, $ttf_cfg["cookie_path"], $ttf_cfg["cookie_domain"], FALSE);

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

    if (strcmp($title, $title_head) != 0) {

        // insert the profile as a new revision
        $sql = "INSERT INTO ttf_revision SET    ".
               "ref_id='{$ttf["uid"]}',         ".
               "type='title',                   ".
               "author_id='{$ttf["uid"]}',      ".
               "date=UNIX_TIMESTAMP(),          ".
               "ip='{$_SERVER["REMOTE_ADDR"]}', ".
               "body='".clean($title)."'        ";
        if (!$result = mysql_query($sql)) showerror();

        // update the user's last rev date
        $sql = "UPDATE ttf_user                 ".
               "SET rev_date=UNIX_TIMESTAMP()   ".
               "WHERE user_id={$ttf["uid"]}     ";
        if (!$result = mysql_query($sql)) showerror();

        // update the user's title with a formatted version
        $sql = "UPDATE ttf_user                         ".
               "SET title='".clean(output($title))."'   ".
               "WHERE user_id='{$ttf["uid"]}'           ";

        if (!$result = mysql_query($sql)) {

            showerror();

        } else {

            $messages[] = "your title has been successfully changed.";

        };

    };

    // change time zone *********************************************

    $zone = $_POST["zone"];

    if (strcmp($zone, $user["time_zone"]) != 0) {

        $sql = "UPDATE ttf_user                     ".
               "SET time_zone='".clean($zone)."'    ".
               "WHERE user_id='{$ttf["uid"]}'       ";

        if (!$result = mysql_query($sql)) {

            showerror();

        } else {

            $messages[] = "your time zone has been successfully changed.";

        };

    };

    // change dst scheme ********************************************

    $dst = $_POST["dst"];

    if (strcmp($dst, $user["dst_scheme"]) != 0) {

        $sql_dst = empty($dst) ? 'NULL' : "'".clean($dst)."'";

        $sql = "UPDATE ttf_user                 ".
               "SET dst_scheme=$sql_dst         ".
               "WHERE user_id='{$ttf["uid"]}'   ";

        if (!$result = mysql_query($sql)) {

            showerror();

        } else {

            $messages[] = "your dst scheme has been successfully changed.";

        };

    };

    // change email *************************************************

    $email = $_POST["email"];

    if (strcmp($email, $user["email"]) != 0) {

        if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,24})$/i", $email)) {

            $sql = "UPDATE ttf_user                 ".
                   "SET email='".clean($email)."'   ".
                   "WHERE user_id='{$ttf["uid"]}'   ";

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

        if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == IMAGETYPE_GIF ) $ext = "gif";
        if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == IMAGETYPE_JPEG) $ext = "jpg";
        if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == IMAGETYPE_PNG ) $ext = "png";

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
                    <textarea class="profile" cols="70" rows="7" name="profile"><?php echo output($profile_head); ?></textarea><br />
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
                        <td>confirm the new password:</td>
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
                                <option value="-12"<?php if($user["time_zone"]==-12) echo " selected=\"selected\""; ?>>utc-12</option>
                                <option value="-11"<?php if($user["time_zone"]==-11) echo " selected=\"selected\""; ?>>utc-11</option>
                                <option value="-10"<?php if($user["time_zone"]==-10) echo " selected=\"selected\""; ?>>utc-10</option>
                                <option value="-9.5"<?php if($user["time_zone"]==-9.5) echo " selected=\"selected\""; ?>>utc-09:30</option>
                                <option value="-9"<?php if($user["time_zone"]==-9) echo " selected=\"selected\""; ?>>utc-09</option>
                                <option value="-8"<?php if($user["time_zone"]==-8) echo " selected=\"selected\""; ?>>utc-08</option>
                                <option value="-7"<?php if($user["time_zone"]==-7) echo " selected=\"selected\""; ?>>utc-07</option>
                                <option value="-6"<?php if($user["time_zone"]==-6) echo " selected=\"selected\""; ?>>utc-06</option>
                                <option value="-5"<?php if($user["time_zone"]==-5) echo " selected=\"selected\""; ?>>utc-05</option>
                                <option value="-4"<?php if($user["time_zone"]==-4) echo " selected=\"selected\""; ?>>utc-04</option>
                                <option value="-3.5"<?php if($user["time_zone"]==-3.5) echo " selected=\"selected\""; ?>>utc-03:30</option>
                                <option value="-3"<?php if($user["time_zone"]==-3) echo " selected=\"selected\""; ?>>utc-03</option>
                                <option value="-2"<?php if($user["time_zone"]==-2) echo " selected=\"selected\""; ?>>utc-02</option>
                                <option value="-1"<?php if($user["time_zone"]==-1) echo " selected=\"selected\""; ?>>utc-01</option>
                                <option value="0"<?php if($user["time_zone"]==0) echo " selected=\"selected\""; ?>>utc</option>
                                <option value="1"<?php if($user["time_zone"]==1) echo " selected=\"selected\""; ?>>utc+01</option>
                                <option value="2"<?php if($user["time_zone"]==2) echo " selected=\"selected\""; ?>>utc+02</option>
                                <option value="3"<?php if($user["time_zone"]==3) echo " selected=\"selected\""; ?>>utc+03</option>
                                <option value="3.5"<?php if($user["time_zone"]==3.5) echo " selected=\"selected\""; ?>>utc+03:30</option>
                                <option value="4"<?php if($user["time_zone"]==4) echo " selected=\"selected\""; ?>>utc+04</option>
                                <option value="4.5"<?php if($user["time_zone"]==4.5) echo " selected=\"selected\""; ?>>utc+04:30</option>
                                <option value="5"<?php if($user["time_zone"]==5) echo " selected=\"selected\""; ?>>utc+05</option>
                                <option value="5.5"<?php if($user["time_zone"]==5.5) echo " selected=\"selected\""; ?>>utc+05:30</option>
                                <option value="5.75"<?php if($user["time_zone"]==5.75) echo " selected=\"selected\""; ?>>utc+05:45</option>
                                <option value="6"<?php if($user["time_zone"]==6) echo " selected=\"selected\""; ?>>utc+06</option>
                                <option value="6.5"<?php if($user["time_zone"]==6.5) echo " selected=\"selected\""; ?>>utc+06:30</option>
                                <option value="7"<?php if($user["time_zone"]==7) echo " selected=\"selected\""; ?>>utc+07</option>
                                <option value="8"<?php if($user["time_zone"]==8) echo " selected=\"selected\""; ?>>utc+08</option>
                                <option value="8.75"<?php if($user["time_zone"]==8.75) echo " selected=\"selected\""; ?>>utc+08:45</option>
                                <option value="9"<?php if($user["time_zone"]==9) echo " selected=\"selected\""; ?>>utc+09</option>
                                <option value="9.5"<?php if($user["time_zone"]==9.5) echo " selected=\"selected\""; ?>>utc+09:30</option>
                                <option value="10"<?php if($user["time_zone"]==10) echo " selected=\"selected\""; ?>>utc+10</option>
                                <option value="10.5"<?php if($user["time_zone"]==10.5) echo " selected=\"selected\""; ?>>utc+10:30</option>
                                <option value="11"<?php if($user["time_zone"]==11) echo " selected=\"selected\""; ?>>utc+11</option>
                                <option value="11.5"<?php if($user["time_zone"]==11.5) echo " selected=\"selected\""; ?>>utc+11:30</option>
                                <option value="12"<?php if($user["time_zone"]==12) echo " selected=\"selected\""; ?>>utc+12</option>
                                <option value="12.75"<?php if($user["time_zone"]==12.75) echo " selected=\"selected\""; ?>>utc+12:45</option>
                                <option value="13"<?php if($user["time_zone"]==13) echo " selected=\"selected\""; ?>>utc+13</option>
                                <option value="14"<?php if($user["time_zone"]==14) echo " selected=\"selected\""; ?>>utc+14</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>dst scheme:</td>
                        <td>
                            <select name="dst">
                                <option value=""<?php if($user["dst_scheme"]=="") echo " selected=\"selected\""; ?>>none</option>
                                <option value="eu"<?php if($user["dst_scheme"]=="eu") echo " selected=\"selected\""; ?>>europe</option>
                                <option value="na_akdt"<?php if($user["dst_scheme"]=="na_akdt") echo " selected=\"selected\""; ?>>na akdt</option>
                                <option value="na_pdt"<?php if($user["dst_scheme"]=="na_pdt") echo " selected=\"selected\""; ?>>na pdt</option>
                                <option value="na_mdt"<?php if($user["dst_scheme"]=="na_mdt") echo " selected=\"selected\""; ?>>na mdt</option>
                                <option value="na_cdt"<?php if($user["dst_scheme"]=="na_cdt") echo " selected=\"selected\""; ?>>na cdt</option>
                                <option value="na_edt"<?php if($user["dst_scheme"]=="na_edt") echo " selected=\"selected\""; ?>>na edt</option>
                                <option value="na_adt"<?php if($user["dst_scheme"]=="na_adt") echo " selected=\"selected\""; ?>>na adt</option>
                                <option value="na_mx3"<?php if($user["dst_scheme"]=="na_mx3") echo " selected=\"selected\""; ?>>na mx3</option>
                                <option value="na_mx2"<?php if($user["dst_scheme"]=="na_mx2") echo " selected=\"selected\""; ?>>na mx2</option>
                                <option value="na_mx1"<?php if($user["dst_scheme"]=="na_mx1") echo " selected=\"selected\""; ?>>na mx1</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <div id="editprofile_button">
                    <input class="editprofile" type="submit" value="apply changes" />
                </div>
                <div><input type="hidden" name="edit" value="true" /></div>
            </form>
<?php

};

require_once "include_footer.php";

