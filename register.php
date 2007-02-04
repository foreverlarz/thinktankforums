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
 * register.php
 *
 * AUDITED BY JLR 200611250153
 *
 * the following variables are accepted:
 * 	$_POST["username"]	clean
 * 	$_POST["email0"]	clean
 *	$_POST["email1"]	clean
 *	$_POST["action"]	--
 *
 * sanity checks include:
 * 	email addresses match
 * 	email addresses are not blank
 * 	email addresses do not contain invalid characters
 * 	username length is 16 characters or less
 *	username is not blank
 *	username does not contain invalid characters
 *	no user is logged in
 *	includes are REQUIRED
 */
 require "common.inc.php";	
 $label = "register an account";
 require "header.inc.php";
 if (!isset($ttf["uid"])) {
  if ($_POST["action"] == "register") {
   $username = $_POST["username"];
   if ($username == substr($username, 0, 15)) {
    if ($username != "") {
     if ($username == clean($username)) {
      $email0 = $_POST["email0"];  
      $email1 = $_POST["email1"];
      if ($email0 == $email1) {
       if ($email0 != "") {
        if ($email0 == clean($email0)) { 
         $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
         for ($i=0;$i<12;$i++) { $password .= substr($chars, rand(0, 61), 1); };
	 $username = substr(clean($username), 0, 15);
         $email = clean($email0);
         $message = "hi--\n\nhere is your account information for think tank forums:\n\nusername: $username\n".
	 "password: $password\n\nlog in at http://www.thinktankforums.com/\n\nthanks,\nviolet";
         if (!mysql_query("INSERT INTO ttf_user SET username='$username', password=SHA1('$password'), email='$email', register_date=UNIX_TIMESTAMP(), register_ip='{$_SERVER["REMOTE_ADDR"]}'")) {
          message("register an account","error!","no account was created. perhaps an account already exists with a matching username or password.",0,0);
         } else {
          if (!mail($email, "think tank forums account information", $message, "from: violet@thinktankforums.com")) {
           message("register an account","error!","unknown error. no account was created.",0,0);
          } else {
           message("register an account","success!","we have e-mailed your password to you. login within 7 days or the account will be deleted.",0,0);
          };
         };
        } else { message("register an account","error!","your e-mail address contained invalid characters. no account was created.",0,0); };
       } else { message("register an account","error!","your e-mail address cannot be null. no account was created.",0,0); };
      } else { message("register an account","error!","your e-mail address did not match. no account was created.",0,0); };
     } else { message("register an account","error!","your username contained invalid characters. no account was created.",0,0); };
    } else { message("register an account","error!","your username cannot be null. no account was created.",0,0); };
   } else { message("register an account","error!","your username was longer than 16 characters. no account was created.",0,0); };
  } else {
?>
  <form action="register.php" method="post">
   <table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv"><td width="594" colspan="5"><b>punch it in</b> -- we'll e-mail you a password</td></tr>
    <tr class="medium">
     <td align="left">username:</td>
     <td align="left"><input type="text" name="username" maxlength="16" size="16" /></td>
    </tr>
    <tr class="medium">
     <td align="left">e-mail:</td>
     <td align="left"><input type="text" name="email0" maxlength="64" size="32" /></td>
    </tr>
    <tr class="medium">
     <td align="left">confirm e-mail:</td>
     <td align="left"><input type="text" name="email1" maxlength="64" size="32" /></td>
    </tr>
    <tr class="medium">
     <td align="left">&nbsp;</td>
     <td align="left">
      <input type="hidden" name="action" value="register" />
      <input type="submit" value="register!" />
     </td>
    </tr>
   </table>
  </form>
<?php
  };
 } else { message("register an account","error!","you already have an account!",0,0); };
 require "footer.inc.php";
?>