<?php
/* think tank forums
 *
 * recover.php
 */

require_once "include_common.php";   
$ttf_label = "recover an account";
$ttf_title = $ttf_label;
require_once "include_header.php";

// if the agent isn't already logged in
if (isset($ttf["uid"])) {

    message($ttf_label, $error_die_text, "your account is working.");
    die();

};

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

        message($ttf_label, $error_die_text, "we couldn't find a matching user.");
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

    $subject = "think tank forums account recovery info";
    $fromadd = "violet@thinktankforums.com";
    $message = "hi--\n\nhere is your account recovery information for think tank forums:\n\n".
               "username: $username\npassword: $password\npasskey: $passkey\n\n".
               "to begin using this new password on ttf, ".
               "you'll need to activate it using the passkey.".
               "visit http://www.thinktankforums.com/activate.php\n\nthanks,\nviolet\n\n\n".
               "p.s. do not reply to this email address; it is not checked.";

    if (!mail($email, $subject, $message, "from: ".$fromadd)) {

        // uh oh, the mail() function failed
        message($ttf_label, $error_die_text, "sorry, we couldn't mail your password and passkey.");
        die();

    } else {

        // it worked!
        message($ttf_label, "success", "please check your email to complete this process.");
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
                    send a new password to your email address, along with a
                    passkey to activate it. tough luck if you gave us a fake
                    email address.<br />
                    <input type="submit" value="let's do this" />
                </form>
            </div>
<?php

require_once "include_footer.php";

?>
