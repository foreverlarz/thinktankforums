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
 */
 require "common.inc.php";
 admin();
 $label = "administration » user list";
 require "header.inc.php";
?>
  <table cellspacing="0" cellpadding="1" width="600" class="shift">
<?php
 if ($offset == "") $offset = 0;
 $sql = "SELECT user_id, username, avatar_type, title, email, visit_date FROM ttf_user";
 $result = mysql_query($sql);
 $inv = "inv";
 while ($user = mysql_fetch_array($result)) {
  if ($user["visit_date"] == 0) {
   $vdate = "never visited";
  } else {
   $vdate = strtolower(date("M j, Y, g\:i a", $user["visit_date"] + 3600*$ttf["time_zone"]));
  };
?>
    <tr class="small<?php echo $inv; ?>">
     <td align="left" class="small<?php echo $inv; ?>" rowspan="2" valign="middle" width="34">
<?php
	if (isset($user["avatar_type"])) {
?>
        <img src="avatars/<?php echo $user["user_id"].".".$user["avatar_type"]; ?>" alt="avatar!" width="30" height="30" class="avatar" />
<?php
	} else { echo "&nbsp;\n"; };
?>
     </td>
     <td align="left" class="medium<?php echo $inv; ?>" valign="middle" width="350">[<?php echo $user["user_id"]; ?>] <b><a <?php if ($inv == "inv") echo "style=\"color: #ffffff\""; ?> href="admin_userinfo.php?user_id=<?php echo $user["user_id"]; ?>"><?php echo $user["username"]; ?></a></b></td>
     <td align="right" class="small<?php echo $inv; ?>" valign="middle" width="216"><?php echo $user["email"]; ?>&nbsp;</td>
    </tr>
    <tr>
     <td align="left" class="small<?php echo $inv; ?>" valign="middle" width="350"><?php echo $user["title"]; ?></td>
     <td align="right" class="small<?php echo $inv; ?>" valign="middle" width="216"><?php echo $vdate; ?>&nbsp;</td>
    </tr>
<?php
  if ($inv == "inv") { $inv = ""; } else { $inv = "inv"; };
 };
 mysql_free_result($result);
?>
  </table>
<?php
 require "footer.inc.php";
?>