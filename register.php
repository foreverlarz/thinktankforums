<?php
/* think tank forums
 *
 * register.php
 */

$ttf_title = $ttf_label = "register an account";

require_once "include_common.php";   
require_once "include_header.php";

// users don't need another account
kill_users();

// if the form was submitted
if ($_POST["action"] == "register") {

    if (!empty($_POST["username"])) {

        message("register an account", "fatal error",
                "you did it wrong.");

        die();

    };

    $username = clean($_POST["garbage"]);

    // if the username is 16 characters or less
    if ($username == substr($username, 0, 15)) {

        // if the username is not blank
        if (!empty($username)) {

            // if the username is clean 
            if ($username == $_POST["garbage"]) {

                $email0 = clean($_POST["email0"]);
                $email1 = clean($_POST["email1"]);

                // if the email addresses match
                if ($email0 == $email1) {

                    // if the email address isn't blank
                    if (!empty($email0)) {

                        // if the email address is clean
                        if ($email0 == $_POST["email0"]) { 


    //  <<<<<<<<<<<<<<<<<<<<<<<<<<  shift indents back in   <<<<<<<<<<<<<<<<<<<<<<


    // generate a 12-character password
                        
    $password = generate_string(12);

    // insert the new user into the ttf_user table
    $sql = "INSERT INTO ttf_user SET username='$username', password=SHA1('$password'), ".
           "email='$email0', register_date=UNIX_TIMESTAMP(), register_ip='{$_SERVER["REMOTE_ADDR"]}'";
    if (!$result = mysql_query($sql)) {

        // if unsuccessful, a user with the same username probably exists
        message("register an account", "fatal error", "no account was created. perhaps an ".
                "account already exists with a matching username or e-mail address.");

    } else {

        // if successful, send the email with the login information
        $subject = "{$ttf_cfg["forum_name"]} account information";
        $message =<<<EOF
hi--

here is your account information for {$ttf_cfg["forum_name"]}:

username: {$username}
password: {$password}

log in at {$ttf_protocol}://{$ttf_cfg["address"]}/

thanks,
{$ttf_cfg["bot_name"]}
EOF;

        if (!mail($email0, $subject, $message, "from: ".$ttf_cfg["bot_email"])) {

            // uh oh, the mail() function failed
            message("register an account","fatal error", "sorry, no account was created.");

        } else {

            // it worked!
            message("register an account", "success", "we have e-mailed your password to you.");
            
        };

    };


    //  >>>>>>>>>>>>>>>>>>>>>>>>>>  shift indents back out  >>>>>>>>>>>>>>>>>>>>>>


                        } else {

                            message("register an account",
                                    "fatal error",
                                    "your e-mail address contained ".
                                    "invalid characters. no account ".
                                    "was created.");

                        };

                    } else {

                        message("register an account", "fatal error",
                                "your e-mail address cannot be null. ".
                                "no account was created.");

                    };

                } else {

                    message("register an account", "fatal error",
                            "your e-mail address did not match. ".
                            "no account was created.");

                };

            } else {

                message("register an account", "fatal error",
                        "your username contained invalid characters. ".
                        "no account was created.");

            };

        } else {

            message("register an account", "fatal error",
                    "your username cannot be null. no account was created.");

        };

    } else {

        message("register an account", "fatal error",
                "your username was longer than 16 characters. no account was created.");
    };

} else {

    echo <<<EOF
            <form action="register.php" method="post">
                <table cellspacing="1" class="content">
                    <tr>
                        <th colspan="2">we'll e-mail you a password</th>
                    </tr>
                    <tr class="hide">
                        <td>leave empty:</td>
                        <td><input type="text" name="username" maxlength="16" size="16" /></td>
                    </tr>
                    <tr>
                        <td>username:</td>
                        <td><input type="text" name="garbage" maxlength="16" size="16" /></td>
                    </tr>
                    <tr>
                        <td>e-mail:</td>
                        <td><input type="text" name="email0" maxlength="64" size="32" /></td>
                    </tr>
                    <tr>
                        <td>confirm e-mail:</td>
                        <td><input type="text" name="email1" maxlength="64" size="32" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input type="submit" value="register" />
                            <input type="hidden" name="action" value="register" />
                        </td>
                    </tr>
                </table>
            </form>

EOF;

};



require_once "include_footer.php";

