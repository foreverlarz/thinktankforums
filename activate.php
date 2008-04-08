<?php
/* think tank forums
 *
 * activate.php
 */

require_once "include_common.php";   
$ttf_label = "Activate Your Password";
$ttf_title = $ttf_label;
require_once "include_header.php";

kill_users();

$passkey = clean($_POST["passkey"]);

if (!empty($passkey)) {

    // get the information from the passkey
    $sql = "SELECT user_id,         ".
           "       password         ".
           "FROM ttf_recover        ".
           "WHERE passkey='$passkey'";

    if (!$result = mysql_query($sql)) showerror();

    if (mysql_num_rows($result) !== 1) {

        message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["passkeydne"]);
        die();

    };

    list($user_id, $password) = mysql_fetch_array($result);

    // use the information to change the active password
    $sql = "UPDATE ttf_user             ".
           "SET password='$password'    ".
           "WHERE user_id='$user_id'    ";
    if (!$result = mysql_query($sql)) showerror();

    // get rid of the recovery record
    $sql = "DELETE FROM ttf_recover WHERE passkey='$passkey'";
    if (!$result = mysql_query($sql)) showerror();

    message($ttf_label, $ttf_msg["successtitl"], $ttf_msg["pwdchanged"]);
    die();

};

?>
            <div class="contenttitle">Activate Your Password</div>
            <div class="contentbox">
                <form action="activate.php" method="post">
                    Enter your passkey to activate your new password.<br /><br />
                    <input type="text" name="passkey" size="36" /><br /><br />
                    <input type="submit" value="Activate" />
                </form>
            </div>
<?php

require_once "include_footer.php";

?>
