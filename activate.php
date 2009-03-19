<?php
/* think tank forums
 *
 * activate.php
 */

$ttf_title = $ttf_label = "activate your password";

require_once "include_common.php";   

// people with accounts don't need to activate
kill_users(); 

require_once "include_header.php";

$passkey = clean($_POST["passkey"]);



// if we were given a passkey...
if (!empty($passkey)) {

    // select the information from the passkey
    $sql = "SELECT user_id,         ".
           "       password         ".
           "FROM ttf_recover        ".
           "WHERE passkey='$passkey'";

    if (!$result = mysql_query($sql)) showerror();



    // if there isn't exactly one match, we have serious problems. ABORT!
    if (mysql_num_rows($result) !== 1) {

        message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["passkeydne"]);
        die();

    };



    // otherwise, grab the infos
    list($user_id, $password) = mysql_fetch_array($result);



    // use the information to change the active password
    $sql = "UPDATE ttf_user             ".
           "SET password='$password'    ".
           "WHERE user_id='$user_id'    ";
    if (!$result = mysql_query($sql)) showerror();



    // get rid of the recovery record
    $sql = "DELETE FROM ttf_recover WHERE passkey='$passkey'";
    if (!$result = mysql_query($sql)) showerror();



    // tell the user it worked, kill the agent
    message($ttf_label, $ttf_msg["successtitl"], $ttf_msg["pwdchanged"]);
    die();

};

echo <<<EOF
            <div class="contenttitle">activate your password</div>
            <div class="contentbox">
                <form action="activate.php" method="post">
                    <div>
                        enter your passkey to activate your new password.<br /><br />
                        <input type="text" name="passkey" size="36" /><br /><br />
                        <input type="submit" value="activate" />
                    </div>
                </form>
            </div>

EOF;

require_once "include_footer.php";

?>
