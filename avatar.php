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
 * avatar.php rev0001
 *
 * *****************************       <---<---<---<---<---<---<---<---
 * THIS SCRIPT IS DEPRECIATED! *    <---<---<---<---<---<---<---<---
 * *****************************      <---<---<---<---<---<---<---<---
 *
 * AUDITED BY JLR 200611250144
 *
 * this script accepts the following variables:
 * 	$_GET["user_id"]	clean
 *
 * sanity checks include:
 * 	avatar type specified
 * 	includes are REQUIRED
 *
 * !!!WARNING!!!
 * THIS SCRIPT WILL NOT WORK IF COMMON.INC.PHP
 * HAS TOO MUCH TRAILING WHITESPACE. THIS
 * WHITESPACE WILL BE PRINTED TO THE AGENT
 * BEFORE THE CONTENT-TYPE HEADER IS SENT.
 */
 require "common.inc.php";
 $user_id = clean($_GET["user_id"]);
 $sql = "SELECT avatar, avatar_type FROM ttf_user WHERE user_id = '$user_id'";
 $result = mysql_query($sql);
 $user = mysql_fetch_array($result);
 if (isset($user["avatar_type"])) {
  header("Content-type: image/".$user["avatar_type"]);
  echo $user["avatar"];
 } else {
  $text = "X!";
  $thm = imagecreatetruecolor(30, 30);
  $clr = imagecolorallocate($thm, 255, 255, 255);
  imagestring($thm, 5, 7, 7, $text, $clr);
  header("Content-type: image/gif");
  imagegif($thm);
  imagedestroy($thm);
 };
?>
