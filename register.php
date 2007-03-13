<?php
/* think tank forums
 *
 * register.php
 */

require "include_common.php";   
$label = "register an account";
require "include_header.php";

// if the agent isn't already logged in
if (!isset($ttf["uid"])) {

    // if the form was submitted
    if ($_POST["action"] == "register") {

        $username = clean($_POST["username"]);

        // if the username is 16 characters or less
        if ($username == substr($username, 0, 15)) {

            // if the username is not blank
            if (!empty($username)) {

                // if the username is clean 
                if ($username == $_POST["username"]) {

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
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for ($i = 0; $i < 12; $i++) {
            $password .= substr($chars, rand(0, 61), 1);
        };

        // clean and segment the provided username, clean the provided email
        $username = substr(clean($username), 0, 15);
        $email = clean($email0);
        
        // insert the new user into the ttf_user table
        $sql = "INSERT INTO ttf_user SET username='$username', password=SHA1('$password'), ".
               "email='$email', register_date=UNIX_TIMESTAMP(), register_ip='{$_SERVER["REMOTE_ADDR"]}'";
        if (!$result = mysql_query($sql)) {

            // if unsuccessful, a user with the same username probably exists
            message("register an account", "fatal error", "no account was created. perhaps an ".
                    "account already exists with a matching username or password.", 0, 0);

        } else {

            // if successful, send the email with the login information
            $subject = "think tank forums account information";
            $fromadd = "violet@thinktankforums.com";
            $message = "hi--\n\nhere is your account information for think tank forums:\n\n".
                       "username: $username\npassword: $password\n\n".
                       "log in at http://www.thinktankforums.com/\n\nthanks,\nviolet";

            if (!mail($email, $subject, $message, "from: ".$fromadd)) {

                // uh oh, the mail() function failed
                message("register an account","fatal error", "sorry, no account was created.", 1, 1);

            } else {

                // it worked!
                message("register an account", "success", "we have e-mailed your password to you.", 0, 0);
            
            };

        };


        //  >>>>>>>>>>>>>>>>>>>>>>>>>>  shift indents back out  >>>>>>>>>>>>>>>>>>>>>>


                            } else {

                                message("register an account",
                                        "fatal error",
                                        "your e-mail address contained ".
                                        "invalid characters. no account ".
                                        "was created.", 0, 0);

                            };

                        } else {

                            message("register an account", "fatal error",
                                    "your e-mail address cannot be null. ".
                                    "no account was created.", 0, 0);

                        };

                    } else {

                        message("register an account", "fatal error",
                                "your e-mail address did not match. ".
                                "no account was created.", 0, 0);

                    };

                } else {

                    message("register an account", "fatal error",
                            "your username contained invalid characters. ".
                            "no account was created.", 0, 0);

                };

            } else {

                message("register an account", "fatal error",
                        "your username cannot be null. no account was created.", 0, 0);

            };
        
        } else {
            
            message("register an account", "fatal error",
                    "your username was longer than 16 characters. no account was created.", 0, 0);
        };
    
    } else {

?>
            <form action="register.php" method="post">
                <table cellspacing="1">
                    <tr>
                        <th colspan="2">punch it in -- we'll e-mail you a password</th>
                    </tr>
                    <tr>
                        <td>username:</td>
                        <td><input type="text" name="username" maxlength="16" size="16" /></td>
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
<?php

    };

} else {

    message("register an account", "fatal error", "you already have an account!", 0, 0);

};

require "include_footer.php";

?>
