<?php
/* think tank forums
 *
 * login.php
 *
 * this script accepts the following variables:
 * 	$_POST["username"]	clean
 *	$_POST["password"]	clean
 *
 * sanity checks include:
 * 	user not already logged in
 * 	both username and password are specified
 * 	username/password combination valid
 * 	user not banned
 * 	includes are REQUIRED
 */
 require "common.inc.php";
 if (!isset($ttf["uid"]) && isset($_POST["username"]) && isset($_POST["password"])) {
  $username = clean($_POST["username"]);
  $password = sha1(clean($_POST["password"]));
  $result = mysql_query("SELECT user_id, perm FROM ttf_user WHERE username='$username' AND password='$password'");
  $user = mysql_fetch_array($result);
  mysql_free_result($result);
  if (isset($user["user_id"]) && $user["perm"] == 'banned') {
   $resulta = mysql_query("INSERT INTO ttf_banned VALUES ('{$user["user_id"]}', '{$_SERVER["REMOTE_ADDR"]}')");
   $resultb = mysql_query("INSERT INTO ttf_visit VALUES ('{$ttf["uid"]}', '{$_SERVER["REMOTE_ADDR"]}', UNIX_TIMESTAMP())");
   message("log in","error!","holy shit! you're banned!",1,1);
   die();
  } else if (isset($user["user_id"])) {
   $expire = time() + 31556926;
   $cookie = serialize(array($user["user_id"], $password));
   setcookie("thinktank", $cookie, $expire);
   header("Location: index.php");
  } else { message("log in","error!","invalid username and/or password!",1,1); };
 } else { message("log in","error!","you must be logged out and provide credentials.",1,1); };
?>
