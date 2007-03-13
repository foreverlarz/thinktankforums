<?php
/* think tank forums
 *
 * login.php
 */

require "include_common.php";

if (!isset($ttf["uid"]) && isset($_POST["username"]) && isset($_POST["password"])) {
    
    $username = clean($_POST["username"]);
    $password = sha1(clean($_POST["password"]));

    $sql = "SELECT user_id, perm FROM ttf_user WHERE username='$username' AND password='$password'";
    if (!$result = mysql_query($sql)) showerror();
    $user = mysql_fetch_array($result);
    mysql_free_result($result);
    
    if (isset($user["user_id"]) && $user["perm"] == 'banned') {
        
        $sql = "INSERT INTO ttf_banned SET user_id='{$user["user_id"]}', ip='{$_SERVER["REMOTE_ADDR"]}'";
        if (!$result = mysql_query($sql)) showerror(); 

        $sql = "INSERT INTO ttf_visit SET user_id='{$ttf["uid"]}', ip='{$_SERVER["REMOTE_ADDR"]}', date=UNIX_TIMESTAMP()";
        if (!$result = mysql_query($sql)) showerror();

        message("log in","error!","holy shit! you're banned!",1,1);
        die();
    
    } else if (isset($user["user_id"])) {
        
        $expire = time() + 31556926;
        $cookie = serialize(array($user["user_id"], $password));
        setcookie("thinktank", $cookie, $expire);

        header("Location: index.php");
    
    } else {

        message("log in","fatal error","invalid username and/or password!", 1, 1);
    
    };

} else {
    
    message("log in", "fatal error", "you must be logged out and provide credentials.", 1, 1);

};

?>
