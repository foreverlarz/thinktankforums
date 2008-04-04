<?php
/* think tank forums
 *
 * common.inc.php
 *
 * this script ***MUST*** be ran at the beginning of
 * every request to the ttf installation. ALWAYS!
 */



/* make php use utf-8
 * ~~~~~~~~~~~~~~~~~~
 */
mb_internal_encoding('UTF-8');



/* start timing the execution
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~
 */
$time_start = microtime(TRUE);



/* remove magic quotes
 * ~~~~~~~~~~~~~~~~~~~
 * if i had a tally of the number of hours i spent
 * debugging problems that were caused by magic quotes,
 * i'm certain it'd be on the order of 100. before, we
 * used clean() to strip magic slashes and add mysql
 * slashes. but often we want pure user variables
 * without any slashes.
 *
 * so from now on, we will REMOVE ALL MAGIC SLASHES
 * at the beginning of every request and never think
 * twice about what magic quotes are. except horrible.
 *
 *      signed,
 *      jlr
 */
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
        $value = is_array($value) ?
            array_map('stripslashes_deep', $value) :
            stripslashes($value);
        return $value;
    };
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
};



/* message printing
 * ~~~~~~~~~~~~~~~~
 * $label is printed in the header of the page
 * $title is printed in the title bar of the content box
 * $body is printed in the body of the content box
 */
function message($label, $title, $body) {

    global $ttf;    // pull through the $ttf array for include_header.php (SMART!)

    require_once "include_header.php";

    echo "<div class=\"contenttitle\">$title</div>\n";
    echo "<div class=\"contentbox\">\n";

        if (is_array($body)) {

            echo "<ul>\n";

            foreach ($body as $message) {

                echo "<li>$message</li>\n";

            }

            echo "</ul>\n";

        } else {

            echo "$body\n";

        }

    echo "</div>\n";

    require_once "include_footer.php";

};



/* format unix timestamp
 * ~~~~~~~~~~~~~~~~~~~~~
 */
function formatdate($timestamp, $format = "M j, Y, g\:i a") {

    global $ttf;    // pull through to get the user's time zone

    $longago = time() - $timestamp;
    $minute  = 60;
    $hour    = 60       * $minute;
    $day     = 24       * $hour;
    $week    = 7        * $day;
    $month   = 4        * $week;
    $year    = 365.2422 * $day;

    if ($timestamp == 0) {

        $relative = "never";

    } else if ($longago == 0) {

        $relative = "now";

    } else if ($longago < $minute) {

        $relative = floor($longago);
        if ($relative != 1) $relative .= " seconds ago";
        else $relative .= " second ago";

    } else if ($longago < $hour) {

        $relative = floor($longago / $minute);
        if ($relative != 1) $relative .= " minutes ago";
        else $relative .= " minute ago";

    } else if ($longago < $day) {

        $relative = floor($longago / $hour);
        if ($relative != 1) $relative .= " hours ago";
        else $relative .= " hour ago";

    } else if ($longago < $week) {

        $relative = floor($longago / $day);
        if ($relative != 1) $relative .= " days ago";
        else $relative .= " day ago";

    } else if ($longago < $month) {

        $relative = floor($longago / $week);
        if ($relative != 1) $relative .= " weeks ago";
        else $relative .= " week ago";

    } else if ($longago < $year) {

        $relative = floor($longago / $month);
        if ($relative != 1) $relative .= " months ago";
        else $relative .= " month ago";

    } else {

        $relative = floor($longago / $year);
        if ($relative != 1) $relative .= " years ago";
        else $relative .= " year ago";

        $date = strtolower(gmdate($format, $timestamp + 3600*$ttf["time_zone"]));

    };

    // need to go through all the other scripts and change how they call formatdate();
    // $absolute = strtolower(gmdate($format, $timestamp + 3600*$ttf["time_zone"]));
    // return array($relative, $absolute);

    return $relative;

};



/* administrative permission validation
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * this function should be run at the beginning of any script that
 * should only be accessed by administrators. if the user accessing
 * the page does not have admin priviledges, an error will be printed
 * and the script will exit.
 */
function admin() {

    global $ttf;    // pull through the $ttf array for include_header.php (SMART!)

    if ($ttf["perm"] != 'admin') {
        
        message("think tank forums", "fatal error", "you do not have permission to access this page.");

        die();

    };

};



/* text formatting
 * ~~~~~~~~~~~~~~~
 * outputbody() may be used to format text
 * as suitable for posts and profiles:
 *  -> htmlspecialchars(, ENT_COMPAT, 'UTF-8')
 *  -> url linking
 *     * full  (http://www.sld.tld)
 *     * named (ttf:http://www.sld.tld)
 *             ('my page':http://subdomain.sld.tld)
 *     * short (sld.tld)
 *             (subdomain.sld.tld)
 *  -> html tag support
 *     * <b>
 *     * <i>
 *     * <u>
 *     * <pre>
 *     * <blockquote>
 *
 * this function is necessary to retain xhtml compliance.
 */
function outputbody($input) {

    // convert all special characters to their html equivalent
    $input = htmlspecialchars($input, ENT_COMPAT, 'UTF-8');

    // chop links
    if (!function_exists('choplink')) {
        function choplink($link = '') {

            $short = ((strlen($link) > 60) ? substr($link, 0 , 45).' &hellip; '.substr($link, -10) : $link);

            return ' <a href="'.$link.'">'.$short.'</a> ';

        };

    };

    // full url
    $input = preg_replace('@(^|\s)(http(s)?|(s)?ftp):\/\/(([\w\/.\-\=\~\?\&\,\.]*)?[^\s\,\.\)\!\?\:\'\}\]$]+)@ie', 'choplink(\'$2://$5\')', $input);

    // named url
    $input = preg_replace('@(((\'([\w\s.?\!?\@?\:?\-?]+)\')?([\w.?\!?\@?\:?\-?]+)?:))(http(s)?|(s)?ftp(s)?):\/\/(([\w\/.\-\=\~\?\&]*)?[^\s\,\.\)\!\?\:\'\}\]$]+)@i', '<a href="$6://$10">$4$5</a>', $input);

    // short url
    $input = preg_replace('@(^|\s)(\w+)\.(com|net|org|edu|gov)($|\s)@i', ' <a href="http://$2.$3">$2.$3</a> ', $input);
    $input = preg_replace('@(^|\s)(\w+)\.(\w+)\.(com|net|org|edu|gov)($|\s)@i', ' <a href="http://$2.$3.$4">$2.$3.$4</a> ', $input);

    // converts some html entities to tags
    $search =  array("&lt;b&gt;",            "&lt;/b&gt;",
                     "&lt;i&gt;",            "&lt;/i&gt;",
                     "&lt;u&gt;",            "&lt;/u&gt;",
                     "&lt;blockquote&gt;",   "&lt;/blockquote&gt;");
    $replace = array("<b>",                 "</b>",
                     "<i>",                 "</i>",
                     "<u>",                 "</u>",
                     "<blockquote>",        "</blockquote>");
    $input = str_replace($search, $replace, $input);

    if (strpos($input, '&lt;pre&gt;') !== FALSE) {
        $open_split = explode('&lt;pre&gt;', $input);
        foreach ($open_split as $var) {
            $close_split = explode('&lt;/pre&gt;', $var);
            foreach ($close_split as $doubly) {
                $alternate[] = $doubly;
            };
        };
        $print = '';
        $br = 1;
        foreach ($alternate as $finally) {
            if ($br % 2) {
                $print .= nl2br($finally);
            } else {
                $print .= '<pre>'.$finally.'</pre>';
            };
            $br++;
        };
    } else {
        $print = nl2br($input);
    };

    return $print;

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
 *  if (!$result = mysql_query($sql)) showerror();
 *
 * remember that this function will exit the script if mysql
 * encounters an error. this is extremely necessary when issuing
 * a series of queries where a latter query depends on data from
 * a former query.
 */
function showerror() {

    if (mysql_error()) {

        message("think tank forums", "fatal error", "mysql error ".mysql_errno().": ".mysql_error());

        die();
        
    } else {
        
        message("think tank forums", "fatal error", "could not connect to the mysql dbms.");

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
require "include_credentials.php";



/* mysql dbms connection
 * ~~~~~~~~~~~~~~~~~~~~~
 * the following code creates a persistent connection to the dbms
 * (database management system) using the variables defined above.
 */
if (!($dbms_cnx = @mysql_pconnect($dbms_host, $dbms_user, $dbms_pass))) showerror();
if (!mysql_select_db($dbms_db)) showerror();
if (!mysql_query("SET NAMES 'utf8'")) showerror();



/* input security cleaning
 * ~~~~~~~~~~~~~~~~~~~~~~~
 * this function should be used on every variable used in a script
 * coming from $_REQUEST, $_GET, and $_POST. it is extremely
 * important to use it on data used in mysql queries.
 */
function clean($input) {

    // do we want to trim?
    // $output = mysql_real_escape_string(trim($input));
    $output = mysql_real_escape_string($input);

    return($output);

};



/* email validation
 * ~~~~~~~~~~~~~~~~
 * this function should be used on every email address in a script
 * coming from $_REQUEST, $_GET, and $_POST.
 */
function validateEmail($email) {

    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {

        return false;

    } else {

        return true;
    };
}



/* delete a user's avatar
 * ~~~~~~~~~~~~~~~~~~~~~~
 * use this function to delete the current user's avatar.
 */
function deleteAvatar() {

    global $ttf;

    $sql = "SELECT avatar_type FROM ttf_user WHERE user_id='{$ttf["uid"]}'";
    if (!$result = mysql_query($sql)) showerror();
    list($ext) = mysql_fetch_row($result);

    if (!empty($ext)) {

        // if the user has an avatar
        
        if (!unlink("avatars/".$ttf["uid"].".".$ext)) {

            // we couldn't delete their avatar
            return FALSE;
        
        } else {
            
            $sql = "UPDATE ttf_user SET avatar_type=NULL ".
                   "WHERE user_id='{$ttf["uid"]}'";
            if (!$result = mysql_query($sql)) {

                // we couldn't reflect the deletion in the db                
                showerror();

            } else {

                // everything worked
                return TRUE;

            };

        };
   
    } else {

        // there was no avatar to delete,
        // so we are still happy people
        return TRUE;

    };

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

        message("think tank forums","fatal error","your ip is banned.");

        die();

    };

};



/* cookie management
 * ~~~~~~~~~~~~~~~~~
 * grab the thinktank cookie and get the corresponding user info,
 * then shove it all into the $ttf array.
 */
if (isset($_COOKIE["thinktank"])) {

    // pull the data out of the cookie
    list($user_id, $password) = unserialize(stripslashes($_COOKIE["thinktank"]));

    $user_id = clean($user_id);
    $password = clean($password);

    // select the data from ttf_user associated with this user
    $sql = "SELECT user_id, username, perm, avatar_type, time_zone ".
           "FROM ttf_user WHERE user_id='$user_id' AND password='$password'";
    if (!$result = mysql_query($sql)) showerror();

    // if we could find a user matching the specified user_id and password...
    if (mysql_num_rows($result) == 1) {

        // put the user data into the $ttf array
        list($ttf["uid"],
             $ttf["username"],
             $ttf["perm"],
             $ttf["avatar_type"],
             $ttf["time_zone"]) = mysql_fetch_array($result);

    } else {

        // or print an error and exit
        message("fatal error", "authentication","your cookie is invalid. ".
                "please <a href=\"logout.php\">logout</a> then login again.");

        die();

    };

};



/* user statistics
 * ~~~~~~~~~~~~~~~
 * this code updates the visit_date field if the agent is a valid user.
 *
 * it also logs the user_id, date, and ip into the ttf_visit table.
 * this is quite possibly the most ridiculous thing that ttf does. it
 * maintains an excessively large table.
 */
if (isset($ttf["uid"])) {

    $sql = "UPDATE ttf_user SET visit_date=UNIX_TIMESTAMP(), ".
           "visit_ip='{$_SERVER["REMOTE_ADDR"]}' WHERE user_id='{$ttf["uid"]}'";
    if (!$result = mysql_query($sql)) showerror();

    $sql = "INSERT INTO ttf_visit SET user_id='{$ttf["uid"]}', ".
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
            "updating scripts and adding new features. come back soon!\n");

    die();

};


?>
