<?php
/* think tank forums
 *
 * login.php
 */

$ttf_label = "log in";
$ttf_title = $ttf_label;

require_once "include_common.php";

// if the user isn't logged in and provided a username and password
if (isset($ttf["uid"]) || !isset($_POST["username"]) || !isset($_POST["password"])) {

    message($ttf_label, $ttf_msg["fatal_error"], "you must be logged out and provide credentials.");
    die();

};

$username = clean($_POST["username"]);
$password = sha1(clean($_POST["password"]));

$sql = "SELECT user_id, perm FROM ttf_user WHERE username='$username' AND password='$password'";
if (!$result = mysql_query($sql)) showerror();
$user = mysql_fetch_array($result);

// if a match was found and they are marked as banned    
if (isset($user["user_id"]) && $user["perm"] == 'banned') {

    // ban their current ip as well
    $sql = "INSERT INTO ttf_banned SET user_id='{$user["user_id"]}', ip='{$_SERVER["REMOTE_ADDR"]}'";
    if (!$result = mysql_query($sql)) showerror(); 

    // mark this visit
    $sql = "INSERT INTO ttf_visit SET user_id='{$ttf["uid"]}', ".
           "ip='{$_SERVER["REMOTE_ADDR"]}', date=UNIX_TIMESTAMP()";
    if (!$result = mysql_query($sql)) showerror();

    // print an error
    message($ttf_label, $ttf_msg["fatal_error"], "you are banned.");

// if a match was found (and they aren't banned)    
} else if (isset($user["user_id"])) {

    // give them a cookie        
    $expire = time() + 31556926;
    $cookie = serialize(array($user["user_id"], $password));
    setcookie("thinktank", $cookie, $expire);

    // take them back from where they came
    header("Location: ".$_SERVER["HTTP_REFERER"]);
    
} else {

    message($ttf_label, $ttf_msg["fatal_error"], "invalid username and/or password.");
    
};

?>
