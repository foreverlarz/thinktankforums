<?php
/* think tank forums 1.0
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


// message printing

 function message($label, $header, $message, $a, $b) {
	global $ttf;			// i think this is made a global here to pass to header.inc.php
	if ($a == 1) {
		require "header.inc.php";
	};
?>
  <table border="0" cellpadding="2" cellspacing="1" width="600" class="shift">
   <tr class="mediuminv"><td width="594" colspan="5"><b><?php echo $header; ?></b></td></tr>
   <tr class="medium"><td><?php echo $message; ?></td></tr>
  </table>
<?php
	if ($b == 1) require "footer.inc.php";
 };


// validate as admin

 function admin() {
	global $ttf;
	if ($ttf["perm"] != 'admin') {
	 message("ttf administration backend!","dead end.","please do not reload this page or attempt to exploit it. every <b>page view</b> is logged with {ip, timestamp, request, additional agent information}.",1,1);
	 die();
	};
 };

//chop links
function choplink($link = '') {
	$link = str_replace(' ', '%20', $link);
	$short = ((strlen($link) > 60) ? substr($link, 0 , 45).'[…]'.substr($link, -10) : $link);
	return ' <a href="'.$link.'">'.$short.'</a> ';
};
//<pre> and </pre> tags drop extra <br /> baggage.
function unprebr($prestrn = '') {
	$brless = str_replace(array('\s', '\n', '\r'), '', $prestrn);
	return '<pre>'.$brless.'</pre>';
};

// format text output for posts and profiles
function outputbody($input)
{
	$input = htmlspecialchars($input, ENT_COMPAT, 'UTF-8');
	
	//long paste
	//ex1: http://www.thinktankforums.com == <a href="http://www.wingedleopard.net">http://www.wingedleopard.net</a>
	$input = preg_replace('@(^|\s)(http(s)?|(s)?ftp):\/\/(([\w\/.\-\=\~\?\&]*)?[^\s\,\.\)\!\?\:\'\}\]$])@ie', 'choplink(\'$2$3$4://$5\')', $input);
	
	//name paste
	//ex1: wlw:http://www.wingedleopard.net == <a href="http://www.wingedleopard.net">wlw</a> 
	//ex2: 'winged leopard':http://www.wingedleopard.net == <a href="http://www.wingedleopard.net">winged leopard</a> 
	$input = preg_replace('@(((\'([\w\s]+)\')?(\w+)?:))(http(s)?|(s)?ftp):\/\/(([\w\/.\-\=\~\?\&]*)?[^\s\,\.\)\!\?\:\'\}\]$])@i', '<a href="$6$7$8://$9">$4$5</a>', $input);

	//quick link
	//ex: ttf.com
 	$input = preg_replace('@(^|\s)(\w+)\.(com|net|org|edu|gov|mil)($|\s)@i', ' <a href="http://$2.$3">$2.$3</a> ', $input);
	
	//quick subdomain
	//ex: www.ttf.com
 	$input = preg_replace('@(^|\s)(\w+)\.(\w+)\.(com|net|org|edu|gov|mil)($|\s)@i', ' <a href="http://$2.$3.$4">$2.$3.$4</a> ', $input);

	// converts some html entities to tags
	$search = array("&lt;b&gt;",  "&lt;i&gt;",  "&lt;u&gt;", "&lt;/b&gt;", "&lt;/i&gt;", "&lt;/u&gt;");
	$replace = array("<b>",  "<i>",  "<u>", "</b>", "</i>", "</u>");
	$input = str_replace($search, $replace, $input);
	if (preg_match('@&lt;pre&gt;([\w\s\r\n]+)&lt;/pre&gt;@i', $input)) {
		$input = preg_replace('@&lt;pre&gt;([\w\s\r\n]+)&lt;/pre&gt;@ie', 'unprebr(\'$1\')', $input);
	} elseif (preg_match('@<pre>([\w\s\r\n]+)</pre>@i', $input)) {
		return $input;
	} else {
		$input = nl2br($input);
	};
	return $input;
};


/* output text for non-post and non-profile uses
 *
 * this must be used to make the data xhtml compliant.
 * for example, "&" is not compliant, you must use "&amp;",
 * and this function converts that for you!
 */

function output($input)
{
	$output = htmlspecialchars($input, ENT_COMPAT, 'UTF-8');
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


// input security cleaning

 function clean($input) {	// allows html, only escapes
	if (get_magic_quotes_gpc()) {
		$input = stripslashes($input);
	};
	$output = mysql_real_escape_string(trim($input));
	return($output);
 };


// config variables

 $result = mysql_query("SELECT * FROM ttf_config");
 while ($config = mysql_fetch_array($result)) $ttf_config["{$config["name"]}"] = $config["value"];


// check banned list

 $sql = "SELECT * FROM ttf_banned GROUP BY ip";
 $result = mysql_query($sql);
 while ($ban = mysql_fetch_array($result)) {
  if ($ban["ip"] == $_SERVER["REMOTE_ADDR"]) {
   message("think tank forums","error!","holy shit! you're banned!",1,1);
   die();
  };
 };


// cookie management

 if (isset($_COOKIE["thinktank"])) {
  list($user_id, $password) = unserialize(stripslashes($_COOKIE["thinktank"]));
  $result = mysql_query("SELECT user_id, username, perm, avatar_type, time_zone FROM ttf_user WHERE user_id='$user_id' AND password='$password'");
  if (mysql_num_rows($result) == 1) {
   $user = mysql_fetch_array($result);
   $ttf["uid"] = $user["user_id"];
   $ttf["username"] = $user["username"];
   $ttf["perm"] = $user["perm"];
   $ttf["avatar_type"] = $user["avatar_type"];
   $ttf["time_zone"] = $user["time_zone"] + $ttf_config["server_time_zone"];
  } else { message("fatal error", "authentication", "your cookie is invalid. please <a href=\"logout.php\">logout</a> then login again.",1,1); die(); };
 };
 if (isset($ttf["uid"])) {
  $resulta = mysql_query("UPDATE ttf_user SET visit_date=UNIX_TIMESTAMP(), visit_ip='{$_SERVER["REMOTE_ADDR"]}' WHERE user_id='{$ttf["uid"]}'");
  $resultb = mysql_query("INSERT INTO ttf_visit VALUES ('{$ttf["uid"]}', '{$_SERVER["REMOTE_ADDR"]}', UNIX_TIMESTAMP())");
 };


// maintenance

 if ($ttf_config["maintenance"] && $ttf["perm"] != 'admin') {
  message("think tank forums!","maintenance","sorry, ttf is offline for maintenance.<br />we are most likely updating scripts and adding new features! come back soon!\n", 1, 1);
  die();
 };
?>
