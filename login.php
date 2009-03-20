<?php
/* think tank forums
 *
 * login.php
 */

$ttf_title = $ttf_label = "log in";

require_once "include_common.php";

kill_users();

if (empty($_POST["username"]) || empty($_POST["password"])) {

    message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["field_empty"]);
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

    // print an error
    message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["user_banned"]);

// if a match was found (and they aren't banned)    
} else if (isset($user["user_id"])) {

    // give them a cookie        
    $expire = time() + $ttf_cfg["cookie_time"];
    $cookie = serialize(array($user["user_id"], $password));
    setcookie($ttf_cfg["cookie_name"], $cookie, $expire, $ttf_cfg["cookie_path"], $ttf_cfg["cookie_domain"], $ttf_cfg["cookie_secure"]);

    header("Location: $ttf_protocol://{$ttf_cfg["address"]}/");
    
} else {

    message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["badcredpair"]);
    
};

?>
