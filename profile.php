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
 * profile.php
 *
 * AUDITED BY JLR 200611250103
 *
 * this script accepts the following variables:
 * 	$_GET["user_id"]	clean
 *
 * sanity checks include:
 * 	valid user exists
 * 	includes are REQUIRED
 */
 require "common.inc.php"; 
 $label = "user profile";
 require "header.inc.php";	  
 $user_id = clean($_GET["user_id"]);
 $result = mysql_query("SELECT user_id, username, avatar_type, title, profile FROM ttf_user WHERE user_id='$user_id'");
 $user = mysql_fetch_array($result);
 mysql_free_result($result);
 if (isset($user["user_id"])) {
?>
   <table border="0" cellpadding="1" cellspacing="0" class="shift" width="600">
    <tr>
     <td align="left" class="smallinv" rowspan="2" valign="bottom" width="34">
<?php
 if (isset($user["avatar_type"])) {
?>
        <img src="avatars/<?php echo $user["user_id"].".".$user["avatar_type"]; ?>" alt="avatar!" width="30" height="30" class="avatar" />
<?php
 } else { echo "&nbsp;\n"; };
?>
     </td>
     <td align="left" class="mediuminv" valign="middle" width="566"><b><?php echo output($user["username"]); ?></b></td>
    </tr>
    <tr>
     <td align="left" class="smallinv" valign="middle" width="566"><?php echo output($user["title"]); ?></td>
    </tr>
   </table>
   <div class="whitebox">
<?php echo outputbody($user["profile"])."\n"; ?>
   </div>
<?php
 } else { message("user profile","error!","not a valid user!",0,0); };
 require "footer.inc.php";
?>