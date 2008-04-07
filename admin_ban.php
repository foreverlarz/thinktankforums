<?php
/* think tank forums
 *
 * admin_ban.php
 */

require_once "include_common.php";

admin();

$ttf_label = "administration &raquo; ban or unban a user";
$ttf_title = $ttf_label;



/* ban a user
 * ~~~~~~~~~~
 * this code allows the administration to block access to the forums
 * for a specific user, while also banning the register_ip and all 
 * visit_ip's as well.
 */
function ban_user($user_id) {
    $sql = "SELECT username, perm, register_ip, visit_ip ".
           "FROM ttf_user WHERE user_id='$user_id'";
    if (!$result = mysql_query($sql)) showerror();
    $user = mysql_fetch_array($result);
    if (!empty($user["perm"]) && $user["perm"] != "banned") {
        if (!empty($user["register_ip"])) {
            $sql = "REPLACE INTO ttf_banned ".
                   "SET user_id='$user_id', ip='{$user["register_ip"]}'";
            if (!$result = mysql_query($sql)) {
                showerror();
            } else {
                $messages[] = "register ip of " .$user["register_ip"]." is banned.";
            };
        } else {
            $messages[] = "<span class=\"error\">no register ip for this user.</span>";
        };
        if (!empty($user["visit_ip"])) {
            $sql = "REPLACE INTO ttf_banned ".
                   "SET user_id='$user_id', ip='{$user["visit_ip"]}'";
            if (!$result = mysql_query($sql)) {
                showerror();
            } else {
                $messages[] = "visit ip of ".$user["visit_ip"]." is banned.";
            };
        } else {
            $messages[] = "<span class=\"error\">no visit ip for this user.</span>";
        };
        $sql = "SELECT ip FROM ttf_revision ".
               "WHERE author_id='$user_id'  ".
               "   && ip IS NOT NULL        ".
               "GROUP BY ip                 ";
        if (!$result = mysql_query($sql)) showerror();
        while ($rev = mysql_fetch_array($result)) {
            $sql = "REPLACE INTO ttf_banned SET user_id='$user_id', ip='{$rev["ip"]}'";
            if (!$result_nested = mysql_query($sql)) {
                showerror();
            } else {
                $messages[] = "revision ip of ".$rev["ip"]." is banned.";
            };
        };
        $sql = "UPDATE ttf_user SET perm='banned' WHERE user_id='$user_id'";
        if (!$result = mysql_query($sql)) {
            showerror();
        } else {
            $messages[] = $user["username"] ." is now banned.";
        };
    } else if ($user["perm"] == 'banned') {
        $messages[] = "<span class=\"error\">this user is already banned.</span>";
    } else {
        $messages[] = "<span class=\"error\">this user is invalid.</span>";
    };
    return $messages;
};



/* unban a user
 * ~~~~~~~~~~~~
 * this code allows the administration to unblock access to the forums
 * for a specific user, while also unbanning the register_ip and all 
 * visit_ip's as well. 
 */
function unban_user($user_id) {
    $sql = "SELECT username, perm, register_ip, visit_ip ".
           "FROM ttf_user WHERE user_id='$user_id'";
    if (!$result = mysql_query($sql)) showerror();
    $user = mysql_fetch_array($result);
    if ($user["perm"] == 'banned') {
        $sql = "DELETE FROM ttf_banned WHERE user_id='$user_id' ";
        if (!$result = mysql_query($sql)) {
            showerror();
        } else {
            $messages[] = "all associated ips were removed from the banned list.";
        };
        $sql = "UPDATE ttf_user SET perm='user' WHERE user_id='$user_id'";
        if (!$result = mysql_query($sql)) {
            showerror();
        } else {
            $messages[] = $user["username"] ." is now unbanned.";
        };
    } else if ($user["perm"] != 'banned') {
        $messages[] = "<span class=\"error\">this user is not banned.</span>";
    } else {
        $messages[] = "<span class=\"error\">this user is invalid.</span>";
    };
    return $messages;
};



require_once "include_header.php";

$user_id = clean($_GET["user_id"]);

if ($_GET["action"] == "ban") {
    message($ttf_label, "results", ban_user($user_id));
} else if ($_GET["action"] == "unban") {
    message($ttf_label, "results", unban_user($user_id));
} else {
    message($ttf_label, $error_die_text, "no action specified.");
};

require_once "include_footer.php";

?>
