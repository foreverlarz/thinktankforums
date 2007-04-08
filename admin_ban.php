<?php
/* think tank forums
 *
 * admin_ban.php
 */

require "include_common.php";

// if an admin isn't logged it, then die()!
admin();

$label = "administration &raquo; user ban";

require "include_header.php";

$user_id = clean($_GET["user_id"]);

$sql = "SELECT perm, register_ip, visit_ip FROM ttf_user WHERE user_id='$user_id'";
if (!$result = mysql_query($sql)) showerror();
$user = mysql_fetch_array($result);
mysql_free_result($result);

if (!empty($user["perm"]) && $user["perm"] != "banned") {

?>
            <table cellspacing="1" class="content">
                <thead>
                    <tr>
                        <th>field</th>
                        <th>action</th>
                        <th>data</th>
                        <th>info</th>
                    </tr>
                </thead>
                <tbody>
<?php

    if (!empty($user["register_ip"])) {
        $sql = "REPLACE INTO ttf_banned ".
               "SET user_id='$user_id', ip='{$user["register_ip"]}'";
        if (!$result = mysql_query($sql)) {
            showerror();
        } else {
            echo "                    <tr><td>banned_ip</td><td>+=</td>";
            echo "<td>{$user["register_ip"]}</td><td>register_ip</td></tr>\n";
        };
    } else {
        echo "                    <tr><td colspan=\"4\"><em>no register_ip for this user</em></td></tr>\n";
    };


    if (!empty($user["visit_ip"])) {
        $sql = "REPLACE INTO ttf_banned ".
               "SET user_id='$user_id', ip='{$user["visit_ip"]}'";
        if (!$result = mysql_query($sql)) {
            showerror();
        } else {
            echo "                    <tr><td>banned_ip</td><td>+=</td>";
            echo "<td>{$user["visit_ip"]}</td><td>visit_ip</td></tr>\n";
        };
    } else {
        echo "                    <tr><td colspan=\"4\"><em>no visit_ip for this user</em></td></tr>\n";
    };


    $sql = "SELECT post_id, ip FROM ttf_post WHERE author_id='$user_id' ".
           "                              && ip IS NOT NULL GROUP BY ip";
    if (!$result = mysql_query($sql)) showerror();

    while ($post = mysql_fetch_array($result)) {

        $sql = "REPLACE INTO ttf_banned SET user_id='$user_id', ip='{$post["ip"]}'";
        if (!$resultx = mysql_query($sql)) {
            showerror();
        } else {
            echo "                    <tr><td>banned_ip</td><td>+=</td>";
            echo "<td>{$post["ip"]}</td><td>post_id={$post["post_id"]}</td></tr>\n";
        };
	};
	mysql_free_result($result);


    $sql = "SELECT date, ip FROM ttf_visit ".
           "WHERE user_id='$user_id' && ip<>'' GROUP BY ip";
    if (!$result = mysql_query($sql)) showerror();

    while ($visit = mysql_fetch_array($result)) {

        $sql = "REPLACE INTO ttf_banned SET user_id='$user_id', ip='{$visit["ip"]}'";
        if (!$resultx = mysql_query($sql)) {
            showerror();
        } else {
            echo "                    <tr><td>banned_ip</td><td>+=</td>";
            echo "<td>{$visit["ip"]}</td><td>visit_date={$visit["date"]}</td></tr>\n";
        };
	};
	mysql_free_result($result);


    $sql = "UPDATE ttf_user SET perm='banned' WHERE user_id='$user_id'";
    if (!$result = mysql_query($sql)) {
        showerror();
    } else {
        echo "                    <tr><td>perm</td><td>-></td><td>banned</td><td>&nbsp;</td></tr>\n";
    };

?>
                </tbody>
            </table>
<?php

} else if ($user["perm"] == "banned") {

    message("user information", "fatal error", "the user_id provided is already banned.", 0, 0);

} else {

    message("user information", "fatal error", "the user_id provided is invalid.", 0, 0);

};

require "include_footer.php";

?>
