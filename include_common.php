<?php
/* think tank forums
 *
 * common.inc.php
 *
 * this script ***MUST*** be run at the beginning of
 * every request to the ttf installation. ALWAYS!
 */



// start timing the execution
$time_start = microtime(TRUE);



// include widely-used functions
require "include_functions.php";



/* database variables
 * ~~~~~~~~~~~~~~~~~~
 *
 * let $dbms_host be the hostname
 *     $dbms_user be the username
 *     $dbms_pass be the password
 *     $dbms_db   be the database
 */
require "include_credentials.php";



// messages are fun
$ttf_msg["fatal_error"] = "fatal error";
$ttf_msg["maint_title"] = "maintenance mode";
$ttf_msg["successtitl"] = "success";
$ttf_msg["resultstitl"] = "results";
$ttf_msg["ip_banned"]   = "sorry, but your IP is banned.";
$ttf_msg["user_banned"] = "sorry, but your user account is banned.";
$ttf_msg["cookie_inv"]  = "sorry, but your cookie is invalid. please try logging out and logging in again.";
$ttf_msg["maint_body"]  = "sorry, but the forum is offline for maintenance. we are most likely ".
                          "updating scripts and adding new features. please come back soon.";
$ttf_msg["field_empty"] = "sorry, but you left a field empty.";
$ttf_msg["notloggedin"] = "sorry, but you must be logged in to do this.";
$ttf_msg["thread_dne"]  = "sorry, but the thread you specified does not exist.";
$ttf_msg["noitemspec"]  = "sorry, but you must specify an item to view.";
$ttf_msg["loggedin"]    = "sorry, but you can't do this if you are logged in. your account is working fine!";
$ttf_msg["nomatchuser"] = "sorry, but we couldn't find a matching user.";
$ttf_msg["mailedinfo"]  = "we have emailed you the account information. please check your email.";
$ttf_msg["btnpost"]     = "click to post";
$ttf_msg["badcredpair"] = "sorry, but the username and password pair that you provided does not match any user record.";
$ttf_msg["passkeydne"]  = "sorry, but the passkey that you specified does not exist.";
$ttf_msg["pwdchanged"]  = "your password has been changed successfully.";
$ttf_msg["noperm"]      = "sorry, but you don't have the proper permission.";
$ttf_msg["noactnspec"]  = "sorry, but you must specify an action.";



// make php use utf-8
mb_internal_encoding('UTF-8');



// remove magic quotes (from php.net docs)
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
        $value = is_array($value) ?
            array_map('stripslashes_deep', $value) :
            stripslashes($value);
        return $value;
    };
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
};



// mysql dbms connection
if (!($dbms_cnx = @mysql_pconnect($dbms_host, $dbms_user, $dbms_pass))) showerror();
if (!mysql_select_db($dbms_db)) showerror();
if (!mysql_query("SET NAMES 'utf8'")) showerror();



// forum configuration variables
$sql = "SELECT * FROM ttf_config";
if (!$result = mysql_query($sql)) showerror();
while ($cfg = mysql_fetch_array($result)) {
    $ttf_cfg["{$cfg["name"]}"] = $cfg["value"];
};



// some messages have to be defined down here
$ttf_msg["cantmail"]    = "sorry, but we couldn't email the information. please contact the forum".
                          "administrator, {$ttf_cfg["admin_name"]}, at {$ttf_cfg["admin_email"]}";



// kill agent if banned
$sql = "SELECT * FROM ttf_banned GROUP BY ip";
if (!$result = mysql_query($sql)) showerror();
while ($ban = mysql_fetch_array($result)) {
    if ($ban["ip"] == $_SERVER["REMOTE_ADDR"]) {
        message($ttf_cfg["forum_name"], $ttf_msg["fatal_error"], $ttf_msg["ip_banned"]);
        die();
    };
};



// cookie management
if (isset($_COOKIE["{$ttf_cfg["cookie_name"]}"])) {

    // pull the data out of the cookie
    list($uid, $pwd) = unserialize(stripslashes($_COOKIE["{$ttf_cfg["cookie_name"]}"]));

    // select the data from ttf_user associated with this user
    $sql = "SELECT user_id,                     ".
           "       username,                    ".
           "       perm,                        ".
           "       avatar_type,                 ".
           "       time_zone                    ".
           "FROM ttf_user                       ".
           "WHERE user_id='".clean($uid)."'     ".
           "   && password='".clean($pwd)."'    ";
    if (!$result = mysql_query($sql)) showerror();

    // if we could find a user matching the specified user_id and password...
    if (mysql_num_rows($result) === 1) {

        // put the user data into the $ttf array
        list($ttf["uid"],
             $ttf["username"],
             $ttf["perm"],
             $ttf["avatar_type"],
             $ttf["time_zone"]) = mysql_fetch_array($result);

    } else {

        // or print an error and exit
        message($ttf_cfg["forum_name"], $ttf_msg["fatal_error"], $ttf_msg["cookie_inv"]);
        die();

    };

};



// kill if in maintenance mode and not an admin
if ($ttf_cfg["maintenance"] && $ttf["perm"] != 'admin') {

    message($ttf_msg["fatal_error"], $ttf_msg["maint_title"], $ttf_msg["maint_body"]);
    die();

};



// update the user's visit_date and visit_ip
if (isset($ttf["uid"])) {

    $sql = "UPDATE ttf_user                             ".
           "SET visit_date=UNIX_TIMESTAMP(),            ".
           "    visit_ip='{$_SERVER["REMOTE_ADDR"]}'    ".
           "WHERE user_id='{$ttf["uid"]}'               ";
    if (!$result = mysql_query($sql)) showerror();

};

?>
