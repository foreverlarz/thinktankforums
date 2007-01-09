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
 * search.php rev0001
 *
 * AUDITED BY JLR 200611250113
 *
 * this script accepts the following variables:
 * 	$_GET["string"]		clean
 *
 * sanity checks include:
 * 	search string not blank
 *	includes are REQUIRED
 */
 require "common.inc.php";
 $string = clean($_GET["string"]);		
 $label = "search ttf posts";
 require "header.inc.php";
?>
  <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
   <table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
    <tr class="mediuminv"><td width="594"><b>punch in a keyword</b></td></tr>
    <tr class="medium">
     <td align="center" width="594">
      <input size="32" type="text" name="string" value="<?php echo output($string); ?>" />
      <input type="submit" value="search" />
     </td>
    </tr>
   </table>
  </form>
<?php
 if ($string != "") {
  $sql = "SELECT ttf_post.thread_id, ttf_post.post_id, ttf_post.author_id, 
          ttf_post.date, ttf_post.body, ttf_thread.title, ttf_user.username, 
          MATCH(ttf_post.body) AGAINST ('$string') AS score 
          FROM ttf_post 
          LEFT JOIN ttf_thread ON ttf_post.thread_id=ttf_thread.thread_id 
          LEFT JOIN ttf_user ON ttf_post.author_id=ttf_user.user_id 
          WHERE MATCH(ttf_post.body) AGAINST ('$string') 
          ORDER BY score DESC";
  $result = mysql_query($sql);
  if (mysql_num_rows($result) == 0) {
?>
  <table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
   <tr class="mediuminv"><td width="594" colspan="5"><b>search results</b></td></tr>
   <tr class="medium"><td>no results returned.<br /><br />either the keyword you entered is <i>very</i> common or non-existent.</td></tr>
  </table>
<?php
  };
  while ($post = mysql_fetch_array($result)) {
   $date = strtolower(date("M j, g\:i a", $post["date"] + 3600*$ttf["time_zone"]));
?>
  <table border="0" cellpadding="5" cellspacing="0" width="600" class="shift">
   <tr>
    <td align="left" class="smallinv">
     <?php echo "[".$post["post_id"]."] in <a style=\"color: #ffffff\" href=\"thread.php?thread_id=".$post["thread_id"]."#".$post["post_id"]."\">".output($post["title"])."</a> by <a style=\"color: #ffffff\" href=\"profile.php?user_id=".$post["author_id"]."\">".output($post["username"])."</a> on $date"; ?>
    </td>
   </tr>
   <tr>
    <td class="small">
     <?php echo outputbody($post["body"]); ?>
    </td>
   </tr>
  </table>
<?php
  };
  mysql_free_result($result);
 } else { message("search ttf posts","error!","you must enter search terms.",0,0); };
 mysql_close();
 require "footer.inc.php";
?>
