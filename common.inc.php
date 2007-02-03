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
 *
 */


// message printing

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


// validate as admin

 function admin() {
	global $ttf;
	if ($ttf["uid"] != 1 && $ttf["uid"] != 2) {
	 message("ttf administration backend!","dead end.","please do not reload this page or attempt to exploit it. every <b>page view</b> is logged with {ip, timestamp, request, additional agent information}.",1,1);
	 die();
	};
 };


// input security cleaning

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

function ttf_format($input)
{
	$input = str_replace("\n","\n ", $input);
	
	//for a direct copy-paste style insert. 
	$match_array[0] = '/([\s\(])(http(s)|(s)ftp)://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(\s\))?$)/ime'; //i modifier set since some people link upper/lower case && m
	$clean_array[0] = '/<a href="$1" target="_blank">$2</a>';
	
	//for a [url=http://www.domain.com]domain[/url]
	$match_array[1] = '/\[url=(.*?)\](.*?)[\/url]/i';
	$clean_array[1] = '/<a href="$1" target="_blank">$2</a>/';
	
	//for a [url]domain.com[/url]
	$match_array[2] = '/\[url\](.*?)[\/url]/i';
	$clean_array[2] = '/<a href="http://.$1" target="_blank">$1</a>/';
	
	//for bold, italic, underline, and preformatted text
	$match_array[3] = '/"&lt;b&gt;",  "&lt;i&gt;",  "&lt;u&gt;", "&lt;pre&gt;", "&lt;/b&gt;", "&lt;/i&gt;", "&lt;/u&gt;", "&lt;/pre&gt;"/';
	$clean_array[3] = '/"<b>",  "<i>",  "<u>", "<pre>", "</b>", "</i>", "</u>", "</pre>"/';
	
	$input = preg_replace($match_array, $clean_array, $input);
	$input = str_replace("\n","\n ", $input);
	return $input;
};

function output($input)
{
	$output = htmlspecialchars(stripslashes($input));
	return $output;
};

function outputbody($input)
{
	$output = nl2br(ttf_format(htmlspecialchars(stripslashes($input))));
	return $output;
};

// mysql error printing

 function showerror() {
	if (mysql_error()) {
		message("fatal error", "mysql dbms", "error ".mysql_errno().": ".mysql_error(), 1, 1); die();
	} else {
		message("fatal error", "mysql dbms", "could not connect.", 1, 1); die();
	};
 };


/* database variables //
 *
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
