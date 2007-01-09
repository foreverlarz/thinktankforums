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
 * common.inc.php
 *
 * AUDITED BY JLR 200611250159
 *
 * this script accepts NO variables.
 *
 * sanity checks are scattered throughout the script.
 * in fact, some functions herein ARE sanity checks.
 *
 * NOTICE: TRAILING WHITESPACE AT THE END OF THIS
 * FILE MAY RENDER OTHER SCRIPTS USELESS THAT REQUIRE
 * THIS SCRIPT. MAKE SURE THIS FILES ENDS WITH "?>".
 */


// message printing //
function message($section, $header, $message, $a, $b) {
	global $ttf;
	if ($a == 1) {
		$label = $section;
		include "header.inc.php";
	};
 ?>
  <table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
   <tr class="mediuminv"><td width="594" colspan="5"><b><?php echo $header; ?></b></td></tr>
   <tr class="medium"><td><?php echo $message; ?></td></tr>
  </table>
<?php
	if ($b == 1) include "footer.inc.php";
};


// validate as admin //
function admin() {
	global $ttf;
	if ($ttf["uid"] != 1 && $ttf["uid"] != 2) {
	 message("ttf administration backend!","dead end.","please do not reload this page or attempt to exploit it. every <b>page view</b> is logged with {ip, timestamp, request, additional agent information}.",1,1);
	 die();
	};
};


// input security cleaning //
function clean($input) {	// allows html, only escapes
	if (get_magic_quotes_gpc()) {
		$input = stripslashes($input);
	};
	$output = mysql_real_escape_string(trim($input));
	return($output);
};


/* convert text blobs for output //
 *
 * used solely for two fields:
 * 	ttf_user.profile
 * 	ttf_post.body
 *
 * all other fields must be inserted using cleanall.
 * these two fields only user clean, therefore they
 * may contain unencoded html characters.
 *
 * note: the primary function is output(). all other
 * functions defined are ancillary to output(). this
 * function may be modified to use text formatting
 * utilities such as php-markdown.
 */
function add_tags($input) {	// converts some html entities to tags
	$search = array("&lt;b&gt;",  "&lt;i&gt;",  "&lt;u&gt;", "&lt;pre&gt;", "&lt;/b&gt;", "&lt;/i&gt;", "&lt;/u&gt;", "&lt;/pre&gt;");
	$replace = array("<b>",  "<i>",  "<u>", "<pre>", "</b>", "</i>", "</u>", "</pre>");
	$input = str_replace($search, $replace, $input);
	return($input);
};


//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////     THE FOLLOWING IS GPL     //////////////////////////////
//////////////////////////////Copyright (C) 2002-2005  Rickard Andersson
//////////////////////////////////////////////////////////////////////////////////////////
function handle_url_tag($url, $link = '')/////////////////////////////////////////////////
{/////////////////////////////////////////////////////////////////////////////////////////
	$full_url = str_replace(array(' ', '\'', '`'), array('%20', '', ''), $url);///////
	if (strpos($url, 'www.') === 0)///////////// If it starts with www, we add http://
		$full_url = 'http://'.$full_url;//////////////////////////////////////////
	else if (strpos($url, 'ftp.') === 0)//// Else if it starts with ftp, we add ftp://
		$full_url = 'ftp://'.$full_url;///////////////////////////////////////////
	else if (!preg_match('#^([a-z0-9]{3,6})://#', $url, $bah))// Else if it doesn't start with abcdef://, we add http://
		$full_url = 'http://'.$full_url;//////////////////////////////////////////
	// Ok, not very pretty :-)////////////////////////////////////////////////////////
	$link = ($link == '' || $link == $url) ? ((strlen($url) > 55) ? substr($url, 0 , 39).' … '.substr($url, -10) : $url) : stripslashes($link);
	return '<a href="'.$full_url.'">'.$link.'</a>';///////////////////////////////////
};////////////////////////////////////////////////////////////////////////////////////////
function linkup($input) {///////// post formatting ///////////////////////////////////////
	$input = ' '.$input;//////////////////////////////////////////////////////////////
	$input = preg_replace('#([\s\(\)])(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ie', '\'$1\'.handle_url_tag(\'$2://$3\')', $input);
	$input = preg_replace('#([\s\(\)])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ie', '\'$1\'.handle_url_tag(\'$2.$3\', \'$2.$3\')', $input);
	$input = substr($input, 1);///////////////////////////////////////////////////////
	return($input);///////////////////////////////////////////////////////////////////
};////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////     THE PRECEDING IS GPL     //////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

function output($input) {
	$output = htmlspecialchars(stripslashes($input));
	return($output);
};


function outputbody($input) {
	$output = nl2br(add_tags(linkup(htmlspecialchars(stripslashes($input)))));
	return($output);
};


// mysql error printing
function showerror() {
	if (mysql_error()) {
		message("fatal error", "mysql dbms", "error ".mysql_errno().": ".mysql_error(), 1, 1); die();
	} else {
		message("fatal error", "mysql dbms", "could not connect.", 1, 1); die();
	};
};


/* database variables
 * let $dbms_host be the hostname
 *     $dbms_user be the username
 *     $dbms_pass be the password
 *     $dbms_db   be the database
 */
require "../credentials.inc.php";


// mysql dbms connection
 if (!($dbmscnx = @mysql_pconnect($dbms_host, $dbms_user, $dbms_pass))) showerror();
 if (!mysql_select_db($dbms_db)) showerror();


// config variables
 $result = mysql_query("SELECT * FROM ttf_config");
 while ($config = mysql_fetch_array($result)) $ttf_config["{$config["name"]}"] = $config["value"];
 mysql_free_result($result);


// check banned list
 $sql = "SELECT * FROM ttf_banned GROUP BY ip";
 $result = mysql_query($sql);
 while ($ban = mysql_fetch_array($result)) {
  if ($ban["ip"] == $_SERVER["REMOTE_ADDR"]) {
   message("think tank forums","error!","holy shit! you're banned!",1,1);
   die();
  };
 };
 mysql_free_result($result);


// cookie management
 if (isset($_COOKIE["thinktank"])) {
  list($user_id, $password) = unserialize(stripslashes($_COOKIE["thinktank"]));
  $result = mysql_query("SELECT user_id, username, avatar_type, time_zone FROM ttf_user WHERE user_id='$user_id' AND password='$password'");
  if (mysql_num_rows($result) == 1) {
   $user = mysql_fetch_array($result);
   $ttf["uid"] = $user["user_id"];
   $ttf["username"] = $user["username"];
   $ttf["avatar_type"] = $user["avatar_type"];
   $ttf["time_zone"] = $user["time_zone"] + $ttf_config["server_time_zone"];
   mysql_free_result($result);
  } else { message("fatal error", "authentication", "your cookie is invalid. please <a href=\"logout.php\">logout</a> then login again.",1,1); die(); };
 };
 if (isset($ttf["uid"])) {
  $resulta = mysql_query("UPDATE ttf_user SET visit_date=UNIX_TIMESTAMP(), visit_ip='{$_SERVER["REMOTE_ADDR"]}' WHERE user_id='{$ttf["uid"]}'");
  $resultb = mysql_query("INSERT INTO ttf_visit VALUES ('{$ttf["uid"]}', '{$_SERVER["REMOTE_ADDR"]}', UNIX_TIMESTAMP())");
 };


// maintenance
 if ($ttf_config["maintenance"] && $ttf["uid"] != 1) {
  message("think tank forums!","maintenance","sorry, ttf is offline for maintenance.<br />we are most likely updating scripts and adding new features! come back soon!\n", 1, 1);
  die();
 };
?>
