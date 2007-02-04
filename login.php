<?php
/* think tank forums 1.0-beta
 *
 * Copyright (c) 2004, 2005, 2006 Jonathan Lucas Reddinger <lucas@wingedleopard.net>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 *
 ****************************************************************************
 *
 * login.php
 *
 * AUDITED BY JLR 200611250126
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
  $result = mysql_query("SELECT user_id, banned FROM ttf_user WHERE username='$username' AND password='$password'");
  $user = mysql_fetch_array($result);
  mysql_free_result($result);
  if (isset($user["user_id"]) && $user["banned"] == 'yes') {
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