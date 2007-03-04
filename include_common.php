<?php
/* think tank forums
 *
 * common.inc.php
 */



/* message printing
 * ~~~~~~~~~~~~~~~~
 * $label is printed in the header of the page
 * $title is printed in the title bar of the content box
 * $body is printed in the body of the content box
 * $header should evaluate to TRUE to include the header include
 * $footer should evaluate to TRUE to include the footer include
 */
function message($label, $title, $body, $header, $footer) {

	global $ttf;	// pull through the $ttf array for header.inc.php

	if ($header) require "include_header.php";

?>
  <div class="contenttitle"><?php echo $title; ?></div>
  <div class="contentbox"><?php echo $body; ?></div>
<?php

	if ($footer) require "include_footer.php";

};



/* administrative permission validation
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * this function should be run at the beginning of any script that
 * should only be accessed by administrators. if the user accessing
 * the page does not have admin priviledges, an error will be printed
 * and the script will exit.
 */
function admin() {

	global $ttf;	// pull through the $ttf array for header.inc.php

	if ($ttf["perm"] != 'admin') {
		
		message("think tank forums", "fatal error", "you do not have permission to access this page.", 1, 1);

		die();

	};

};



/* text formatting functions
 * ~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * the following functions culminate
 * in the use of outputbody().
 *
 * outputbody() may be used to format text
 * as suitable for posts and profiles:
 *  -> htmlspecialchars(, ENT_COMPAT, 'UTF-8')
 *  -> url linking
 *	* full	(http://www.sld.tld)
 *	* named	(ttf:http://www.sld.tld)
 *		('my page':http://subdomain.sld.tld)
 *	* short	(sld.tld)
 *		(subdomain.sld.tld)
 *  -> html tag support
 *	* <b> . . . . </b>
 *	* <i> . . . . </i>
 *	* <u> . . . . </u>
 *	* <pre> . . </pre>
 *	* removes <br /> from <pre> . . </pre>
 *
 * this function is necessary to retain xhtml compliance.
 */

// chop links
function choplink($link = '') {

	$short = ((strlen($link) > 60) ? substr($link, 0 , 45).' … '.substr($link, -10) : $link);

	return ' <a href="'.$link.'">'.$short.'</a> ';

};

// <pre> and </pre> tags drop extra <br /> baggage.
function unprebr($prestrn = '') {

	$brless = str_replace(array('\s', '\n', '\r'), '', $prestrn);

	return '<pre>'.$brless.'</pre>';

};

// format text output for posts and profiles
function outputbody($input) {

	// convert all special characters to their html equivalent
	$input = htmlspecialchars($input, ENT_COMPAT, 'UTF-8');

	// full url
	$input = preg_replace('@(^|\s)(http(s)?|(s)?ftp):\/\/(([\w\/.\-\=\~\?\&\,\.]*)?[^\s\,\.\)\!\?\:\'\}\]$]+)@ie', 'choplink(\'$2://$5\')', $input);

	// named url
	$input = preg_replace('@(((\'([\w\s.?\!?\@?\:?\-?]+)\')?([\w.?\!?\@?\:?\-?]+)?:))(http(s)?|(s)?ftp(s)?):\/\/(([\w\/.\-\=\~\?\&]*)?[^\s\,\.\)\!\?\:\'\}\]$]+)@i', '<a href="$6://$10">$4$5</a>', $input);

	// short url
 	$input = preg_replace('@(^|\s)(\w+)\.(com|net|org|edu|gov)($|\s)@i', ' <a href="http://$2.$3">$2.$3</a> ', $input);
 	$input = preg_replace('@(^|\s)(\w+)\.(\w+)\.(com|net|org|edu|gov)($|\s)@i', ' <a href="http://$2.$3.$4">$2.$3.$4</a> ', $input);

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
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * this must be used to make the data xhtml compliant.
 * for example, "&" is not compliant, you must use "&amp;",
 * and this function converts that for you!
 */
function output($input) {

	$output = htmlspecialchars($input, ENT_COMPAT, 'UTF-8');

	return $output;

};



/* mysql error printing
 * ~~~~~~~~~~~~~~~~~~~~
 * this function should be used with any call to a mysql function.
 * 
 * example use:
 * 	if (!$result = mysql_query($sql)) showerror();
 *
 * remember that this function will exit the script if mysql
 * encounters an error. this is extremely necessary when issuing
 * a series of queries where a latter query depends on data from
 * a former query.
 */
function showerror() {

	if (mysql_error()) {

		message("think tank forums", "fatal error", "mysql error ".mysql_errno().": ".mysql_error(), 1, 1);

		die();
		
	} else {
		
		message("think tank forums", "fatal error", "could not connect to the mysql dbms.", 1, 1);

		die();

	};

};



/* database variables
 * ~~~~~~~~~~~~~~~~~~
 *
 * let $dbms_host be the hostname
 *     $dbms_user be the username
 *     $dbms_pass be the password
 *     $dbms_db   be the database
 */
require "../credentials.inc.php";



/* mysql dbms connection
 * ~~~~~~~~~~~~~~~~~~~~~
 * the following code creates a persistent connection to the dbms
 * (database management system) using the variables defined above.
 */
if (!($dbms_cnx = @mysql_pconnect($dbms_host, $dbms_user, $dbms_pass))) showerror();
if (!mysql_select_db($dbms_db)) showerror();



/* input security cleaning
 * ~~~~~~~~~~~~~~~~~~~~~~~
 * this function should be used on every variable used in a script
 * coming from $_REQUEST, $_GET, and $_POST. it is extremely
 * important to use it on data used in mysql queries.
 *
 * it also strips magic quote slashes and uses the proper function.
 */
function clean($input) {

	if (get_magic_quotes_gpc()) {

		$input = stripslashes($input);

	};

	$output = mysql_real_escape_string(trim($input));

	return($output);

};



/* forum configuration variables
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * this code pulls in some variables that are used throughout
 * the ttf code.
 */
$sql = "SELECT * FROM ttf_config";
if (!$result = mysql_query($sql)) showerror();
while ($config = mysql_fetch_array($result)) {
	$ttf_config["{$config["name"]}"] = $config["value"];
};



/* check banned list
 * ~~~~~~~~~~~~~~~~~
 * this code checks the current agent against a blacklist of ips.
 * if a match is found, an error is printed and the script exits.
 */
$sql = "SELECT * FROM ttf_banned GROUP BY ip";
if (!$result = mysql_query($sql)) showerror();
while ($ban = mysql_fetch_array($result)) {

	if ($ban["ip"] == $_SERVER["REMOTE_ADDR"]) {

		message("think tank forums","fatal error","your ip is banned.", 1, 1);

		die();

	};

};



/* cookie management
 * ~~~~~~~~~~~~~~~~~
 *
 */
if (isset($_COOKIE["thinktank"])) {

	// pull the data out of the cookie
	list($user_id, $password) = unserialize(stripslashes($_COOKIE["thinktank"]));

	// select the data from ttf_user associated with this user
	$sql =	"SELECT user_id, username, perm, avatar_type, time_zone ".
		"FROM ttf_user WHERE user_id='$user_id' AND password='$password'";
	if (!$result = mysql_query($sql)) showerror();

	// if we could find a user matching the specified user_id and password...
	if (mysql_num_rows($result) == 1) {

		// put the user data into the $ttf array
		$user = mysql_fetch_array($result);
		$ttf["uid"] = $user["user_id"];
		$ttf["username"] = $user["username"];
		$ttf["perm"] = $user["perm"];
		$ttf["avatar_type"] = $user["avatar_type"];
		$ttf["time_zone"] = $user["time_zone"] + $ttf_config["server_time_zone"];

	} else {

		// or print an error and exit
		message("fatal error", "authentication","your cookie is invalid. ".
			"please <a href=\"logout.php\">logout</a> then login again.",1,1);

		die();

	};

};



/* user statistics
 * ~~~~~~~~~~~~~~~
 * this code updates the visit_date field if the agent is a valid user.
 *
 * it also logs the user_id, date, and ip into the ttf_visit table.
 * this is quite possibly the most ridiculous thing that ttf does. it
 * maintains an excessively table.
 */
if (isset($ttf["uid"])) {

	$sql =	"UPDATE ttf_user SET visit_date=UNIX_TIMESTAMP(), ".
		"visit_ip='{$_SERVER["REMOTE_ADDR"]}' WHERE user_id='{$ttf["uid"]}'";
	if (!$result = mysql_query($sql)) showerror();

	$sql =	"INSERT INTO ttf_visit SET user_id='{$ttf["uid"]}', ".
		"ip='{$_SERVER["REMOTE_ADDR"]}', date=UNIX_TIMESTAMP()";
	if (!$result = mysql_query($sql)) showerror();

};



/* maintenance mode
 * ~~~~~~~~~~~~~~~~
 * this code allows the administration to block access to the forums
 * while critical changes are being made. note that administrators
 * are still allowed access to the forums. be careful!
 */
if ($ttf_config["maintenance"] && $ttf["perm"] != 'admin') {

	message("think tank forums","maintenance",
		"sorry, ttf is offline for maintenance.<br />we are most likely ".
		"updating scripts and adding new features. come back soon!\n", 1, 1);

	die();

};

?>
