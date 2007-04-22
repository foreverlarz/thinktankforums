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

    // grab user info
    $sql = "SELECT * FROM ttf_user WHERE user_id='{$ttf["uid"]}' LIMIT 1";
    if (!$result = mysql_query($sql)) showerror();
    $user = mysql_fetch_array($result);
    mysql_free_result($result);

    $arrMessages = array();

    $edit = clean($_POST["edit"]);
    
    if ($edit == "bulk") {          

        $pass0 = $_POST["password0"];   //////// EDIT PASSWORD ////////
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
                        $arrMessages[] = "your password has been successfully changed.";

                    };

                } else {

                    $arrMessages[] = "<span class=\"error\">your password contained invalid characters and was not changed.</span>";

                };

            } else {

                $arrMessages[] = "<span class=\"error\">your password did not match and was not changed</span>";

            };

        };

        $profile = $_POST["profile"];    //////// EDIT USER PROFILE ////////

        list($profile_head, ) = buildHead($ttf["uid"], 'profile');
        
        if ($profile != $profile_head) {

            // if it doesn't have revisions (user has
            // never set a profile), make this one the base
            if (empty($profile_head)) {

                $sql = "INSERT INTO ttf_revision SET ".
                       "ref_id='{$ttf["uid"]}', ".
                       "type='profile', ".
                       "author_id='{$ttf["uid"]}', ".
                       "date=UNIX_TIMESTAMP(), ".
                       "ip='{$_SERVER["REMOTE_ADDR"]}', ".
                       "body='".clean($profile)."'";

            } else {

                $profile_diff = clean(serialize(diff($profile_head, $profile)));

                $sql = "INSERT INTO ttf_revision SET ".
                       "ref_id='{$ttf["uid"]}', ".
                       "type='profile', ".
                       "author_id='{$ttf["uid"]}', ".
                       "date=UNIX_TIMESTAMP(), ".
                       "ip='{$_SERVER["REMOTE_ADDR"]}', ".
                       "body='$profile_diff'";

            };

            if (!$result = mysql_query($sql)) showerror();

            // this should be ran through outputbody() in the future!
            $sql = "UPDATE ttf_user SET profile='".clean($profile)."' WHERE user_id='{$ttf["uid"]}'";
        
            if (!$result = mysql_query($sql)) {
            
                showerror();

            } else {

                $arrMessages[] = "your profile has been successfully changed.";
            
            };

        };

        //$title = clean($_POST["title"]);    //////// EDIT USER TITLE ////////
        $title = $_POST["title"];
        
        list($title_head, ) = buildHead($ttf["uid"], 'title');
        
        if ($title != $title_head) {

            // if it doesn't have revisions (user has
            // never set a profile), make this one the base
            if (empty($title_head)) {

                $sql = "INSERT INTO ttf_revision SET ".
                       "ref_id='{$ttf["uid"]}', ".
                       "type='title', ".
                       "author_id='{$ttf["uid"]}', ".
                       "date=UNIX_TIMESTAMP(), ".
                       "ip='{$_SERVER["REMOTE_ADDR"]}', ".
                       "body='".clean($title)."'";

            } else {

                $title_diff = clean(serialize(diff($title_head, $title)));

                $sql = "INSERT INTO ttf_revision SET ".
                       "ref_id='{$ttf["uid"]}', ".
                       "type='title', ".
                       "author_id='{$ttf["uid"]}', ".
                       "date=UNIX_TIMESTAMP(), ".
                       "ip='{$_SERVER["REMOTE_ADDR"]}', ".
                       "body='$title_diff'";

            };

            if (!$result = mysql_query($sql)) showerror();
                
            $sql = "UPDATE ttf_user SET title='".clean($title)."' WHERE user_id='{$ttf["uid"]}'";

            if (!$result = mysql_query($sql)) {
            
                showerror();

            } else {

                $arrMessages[] = "your title has been successfully changed.";
        
            };

        };

        $zone = clean($_POST["zone"]);      //////// EDIT TIME-ZONE ////////

        if ($zone != $user["time_zone"]) {
        
            $sql = "UPDATE ttf_user SET time_zone='$zone' WHERE user_id='{$ttf["uid"]}'";

            if (!$result = mysql_query($sql)) {
            
                showerror();

            } else {

                $arrMessages[] = "your time zone has been successfully changed.";
        
            };

        };

        $email = $_POST["email"];           //////// EDIT EMAIL ////////

        if ($email != $user["email"]) {

            if (validateEmail($email) == true) {

                $sql = "UPDATE ttf_user SET email='$email' WHERE user_id='{$ttf["uid"]}'";

                if (!$result = mysql_query($sql)) {
            
                    showerror();

                } else {
                    
                    $arrMessages[] = "your email address has been successfully updated.";
                                    
                };
            
            } else {
                
                $arrMessages[] = "<span class=\"error\">your e-mail is not vailid.</span>";

           };

        };



    } else if ($edit == "avatar") {     //////// EDIT AVATAR ////////
        
        if ($_FILES["avatar"]["size"] != 0) {
            
            if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == 1) $ext = "gif";
            if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == 2) $ext = "jpg";
            if (exif_imagetype($_FILES["avatar"]["tmp_name"]) == 3) $ext = "png";
            
            if ($ext == "gif" || $ext == "jpg" || $ext == "png") {
                
                list($x, $y) = getimagesize($_FILES["avatar"]["tmp_name"]);
                
                if ($x == 30 && $y == 30) {
                    
                    if (!deleteAvatar()) {
                        
                        $arrMessages[] = "<span class=\"error\">there was an error trying to delete your old avatar.</span>";
                    
                    }; 


                    // Upload new avatar

                    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], "avatars/".$ttf["uid"].".".$ext)) {

                        $sql = "UPDATE ttf_user SET avatar_type='$ext' WHERE user_id='{$ttf["uid"]}'";
                        
                        if (!$result = mysql_query($sql)) {

                            showerror();

                        } else {

                            $arrMessages[] = "your avatar has been successfully changed.";

                        };
                    
                    } else {
                        
                        $arrMessages[] = "<span class=\"error\">the avatar change was unsuccessful.</span>";
                        
                    };
                
                } else {
                    
                    $arrMessages[] = "<span class=\"error\">image uploaded is not 30x30 pixels.</span>";
                
                };
            
            } else {
                
                $arrMessages[] = "<span class=\"error\">image uploaded is not a gif, png, or jpeg.</span>";
            
            };
        
        } else {
            
            $arrMessages[] = "<span class=\"error\">no image specified.</span>";
        
        };
    
    
    } else {

?>
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
                <div class="contenttitle">change your email</div>
                <div class="contentbox" style="text-align: center;">
                    <input type="text" name="email" maxlength="96" size="40" value="<?php echo output($user["email"]); ?>" />
                </div>
                <div class="contenttitle">edit your actual profile</div>
                <div class="contentbox" style="text-align: center;">
                    <textarea class="profile" cols="70" rows="7" name="profile" wrap="virtual"><?php echo output($user["profile"]); ?></textarea><br />
                </div>
                <div class="contenttitle">change your user title</div>
                <div class="contentbox" style="text-align: center;">
                    <input type="text" name="title" maxlength="96" size="64" value="<?php echo output($user["title"]); ?>" />
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
                        <input type="submit" value="apply" />
                        <input type="hidden" name="edit" value="bulk" />
                </div>

            </form>
<?php
    
    };

} else {
    
    message("edit your profile", "fatal error", "you must login before you may edit your profile.");

};

if (!empty($arrMessages)) {
    
    message("edit your profile", "information", $arrMessages);
    
} 

if (empty($arrMessages) && !empty($edit)) {

    message("edit your profile", "information", "you didnt make any changes!");

}  

require_once "include_footer.php";

?>

