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
 * header.inc.php
 *
 * AUDITED BY JLR 200611250137
 *
 * the following variables are accepted:
 * 	$label		secured
 *
 * being an include script, there are no sanity checks.
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
 <head>
  <title>think tank forums <?php echo $ttf_config["version"]; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <style type="text/css">
   @import "_style.css";
  </style>
 </head>
 <body>
  <a href="/"><img id="ttf" src="images/header.gif" width="600" height="46" border="0" alt="think tank forums!" /></a>
  <div id="label"><span class="indent"><?php echo $label; ?></span></div>
  <div id="enclosure">
   <div class="menu_one"> 
<?php
  if (isset($ttf["uid"])) {
   if (isset($ttf["avatar_type"])) {
?>
    <img src="avatars/<?php echo $ttf["uid"].".".$ttf["avatar_type"]; ?>" alt="avatar!" width="30" height="30" align="absmiddle" />
<?php
   };
?>
    <b>hi, <?php echo $ttf["username"]; ?>!</b>
   </div>
   <div class="menu_two">
    · <a href="search.php">search</a><br />
    · <a href="editprofile.php">edit your profile</a><br />
    · <a href="logout.php">log out</a>
<?php	
	if ($ttf["perm"] == 'admin') {
?>
   </div>
   <div class="menu_one"><b>administrate!</b></div>
   <div class="menu_two">
    · <a href="admin_dbms.php">dbms tables</a><br />
    · <a href="admin_user.php">users</a><br />
    · <a href="http://www.wingedleopard.net/phpmyadmin/">phpmyadmin</a><br />
    · <a href="http://code.google.com/p/thinktankforums/">ttf development</a>
<?php
	};
   } else {
?>
    <b>log in to ttf!</b>
   </div>
   <div class="menu_two">
    <form action="login.php" method="post">
     <input type="text" name="username" maxlength="16" size="16" /><br />
     <input type="password" name="password" maxlength="32" size="16" /><br />
     <input type="submit" value="let's go!" />
    </form>
   </div>
   <div class="menu_one">
    <b>lack an account?</b>
   </div>
   <div class="menu_two">
    · <a href="register.php">register an account</a><br />
    · <a href="search.php">search the forums</a>
<?php	 
   };
?>
   </div>
   <!-- end header.inc.php -->
