<?php
/* think tank forums
 *
 * recover.php
 */

$ttf_title = $ttf_label = "recover your account";

require_once "include_common.php";   
require_once "include_header.php";

// users don't need to recover an account
kill_users();

$id_username = clean($_POST["id_username"]);
$id_email = clean($_POST["id_email"]);

// if we have chosen an account to generate a passkey for
if (!empty($id_username) || !empty($id_email)) {

    // first we better see if we can find the user record
    $sql = "SELECT user_id,     ".
           "       username,    ".
           "       email        ".
           "FROM ttf_user       ";

    if (!empty($id_username)) {
        $sql .= " WHERE username='$id_username' ";
    } else if (!empty($id_email)) {
        $sql .= " WHERE email='$id_email' ";
    };

    if (!$result = mysql_query($sql)) showerror();

    if (mysql_num_rows($result) !== 1) {

        message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["nomatchuser"]);
        die();

    };

    list($user_id, $username, $email) = mysql_fetch_array($result);

    // now that we have a matching user, do things!
    $password = generate_string(16);
    $passkey = generate_string(32);
    
    $sql = "INSERT INTO ttf_recover             ".
           "SET date=UNIX_TIMESTAMP(),          ".
           "    ip='{$_SERVER["REMOTE_ADDR"]}', ".
           "    user_id='$user_id',             ".
           "    password=SHA1('$password'),     ".
           "    passkey='$passkey'              ";
    if (!$result = mysql_query($sql)) showerror();

    $subject = "{$ttf_cfg["forum_name"]} account recovery information";
    $message = "hello,\n\nhere is your account recovery information for {$ttf_cfg["forum_name"]}:\n\n".
               "username: $username\npassword: $password\npasskey: $passkey\n\n".
               "to begin using this new password, you'll need to activate it using the passkey. ".
               "visit http://{$ttf_cfg["address"]}/activate.php\n\nthanks,\n{$ttf_cfg["bot_name"]}\n\n\n".
               "p.s. do not reply to this email address; it is not checked.";

    if (!mail($email, $subject, $message, "from: ".$ttf_cfg["bot_email"])) {

        // uh oh, the mail() function failed
        message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["cantmail"]);
        die();

    } else {

        // it worked!
        message($ttf_label, $ttf_msg["successtitl"], $ttf_msg["mailedinfo"]);
        die();

    };

};

?>
            <div class="contenttitle">recover your account</div>
            <div class="contentbox">
                <form action="recover.php" method="post">
                    which account are you claiming as yours?
                    identify it in one way below.<br /><br />
                    username:<br />
                    <input type="text" name="id_username" /><br /><br />
                    email:<br />
                    <input type="text" name="id_email" /><br /><br />
                    we will send a new password to your email address, 
                    along with a passkey to activate it.<br />
                    <input type="submit" value="submit" />
                </form>
            </div>
<?php

require_once "include_footer.php";

?>
