<?php
/* think tank forums
 *
 * include_functions.php
 */



/* message printing
 * ~~~~~~~~~~~~~~~~
 * $label is printed in the header of the page
 * $title is printed in the title bar of the content box
 * $body is printed in the body of the content box
 */
function message($ttf_label, $title, $body) {

    global $ttf;            // pull through the $ttf array for include_header.php (SMART!)
    global $ttf_cfg;    // pull through the $ttf_cfg array (even smarter!)

    require_once "include_header.php";

    echo "<div class=\"contenttitle\">$title</div>\n";
    echo "<div class=\"contentbox\">\n";

        if (is_array($body)) {

            echo "<ul>\n";

            foreach ($body as $message) {

                echo "<li>$message</li>\n";

            };

            echo "</ul>\n";

        } else {

            echo "$body\n";

        };

    echo "</div>\n";

    require_once "include_footer.php";

};



/* format unix timestamp
 * ~~~~~~~~~~~~~~~~~~~~~
 */
function formatdate($timestamp, $format = "Y M j, g\:i a") {

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

    $absolute = strtolower(gmdate($format, $timestamp + 3600*$ttf["time_zone"]));
    return array($relative, $absolute);

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

    if ($input === NULL) {

        return '<div class="archivedpost">this post has been archived.</div>';

    };

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
                     "<span style=\"text-decoration: underline;\">", "</span>",
                     "<blockquote><p>",     "</p></blockquote>");
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



// generate random string
function generate_string($length) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($chars, rand(0, strlen($chars)-1), 1);
    };
    return $string;
};



function kill_guests() {
    global $ttf; global $ttf_label; global $ttf_msg; global $ttf_cfg;
    if (empty($ttf_label)) { $ttf_label = $ttf_cfg["forum_name"]; };
    if (!isset($ttf["uid"])) {
        message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["notloggedin"]);
        die();
    };
};



function kill_users() {
    global $ttf; global $ttf_label; global $ttf_msg; global $ttf_cfg;
    if (empty($ttf_label)) { $ttf_label = $ttf_cfg["forum_name"]; };
    if (isset($ttf["uid"])) {
        message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["loggedin"]);
        die();
    };
};



function kill_nonadmin() {
    global $ttf; global $ttf_label; global $ttf_msg; global $ttf_cfg;
    if (empty($ttf_label)) { $ttf_label = $ttf_cfg["forum_name"]; };
    if ($ttf["perm"] != 'admin') {
        message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["noperm"]);
        die();
    };
};



/* reformat text bodies
 * ~~~~~~~~~~~~~~~~~~~~
 * ttf stores raw user input in the `ttf_revision` table.
 * ttf stores fully formatted text in places like `ttf_post.body`,
 * `ttf_user.title`, and `ttf_user.profile` for quick retreival.
 *
 * suppose that you have modified how we format our text. then we
 * need to pull the raw input from `ttf_revision`, reformat it,
 * and update where the formatted versions are stored.
 */
function reformat_caches() {

    $sql = "SELECT rev_id,      ".
           "       ref_id,      ".
           "       type,        ".
           "       body         ".
           "FROM ttf_revision   ".
           "ORDER BY rev_id ASC ";
    if (!$result = mysql_query($sql)) showerror();

    while ($rev = mysql_fetch_array($result)) {

        if ($rev["type"] === "post") {

            $sql = "UPDATE `ttf_post`                                   ".
                   "SET `body`='".clean(outputbody($rev["body"]))."'    ".
                   "WHERE `post_id`='{$rev["ref_id"]}'                  ";

        } else if ($rev["type"] === "profile") {

            $sql = "UPDATE `ttf_user`                                   ".
                   "SET `profile`='".clean(outputbody($rev["body"]))."' ".
                   "WHERE `user_id`='{$rev["ref_id"]}'                  ";

        } else if ($rev["type"] === "title") {

            $sql = "UPDATE `ttf_user`                                   ".
                   "SET `title`='".clean(output($rev["body"]))."'       ".
                   "WHERE `user_id`='{$rev["ref_id"]}'                  ";

        };

        if (!$result_nested = mysql_query($sql)) {
        
            showerror();

        } else {

            echo "successfully reformatted rev_id={$rev["rev_id"]} ({$rev["type"]}={$rev["ref_id"]}).<br />\n";

        };

    };

};

?>
