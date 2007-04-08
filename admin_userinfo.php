<?php
/* think tank forums
 *
 * admin_userinfo.php
 */

require_once "include_common.php";

// if an admin isn't logged in, then die()!
admin();

$label = "administration &raquo; user info";

require_once "include_header.php";

$user_id = clean($_GET["user_id"]);

$sql = "SELECT * FROM ttf_user WHERE user_id='$user_id'";
if (!$result = mysql_query($sql)) showerror();
$user = mysql_fetch_array($result);

if (isset($user["user_id"])) {
 
    if ($user["register_date"] == 0) {
        $date_reg = "never";
    } else {
        $date_reg = strtolower(date("M j, Y, g\:i a", $user["register_date"] + 3600*$ttf["time_zone"]));
    };

    if ($user["visit_date"] == 0) {
        $date_visit = "never";
    } else {
        $date_visit = strtolower(date("M j, Y, g\:i a", $user["visit_date"] + 3600*$ttf["time_zone"]));
    };

    if ($user["post_date"] == 0) {
        $date_post = "never";
    } else {
        $date_post = strtolower(date("M j, Y, g\:i a", $user["post_date"] + 3600*$ttf["time_zone"]));
    };

?>

            <div class="sidebox"><a href="admin_ban.php?user_id=<?php echo $user["user_id"]; ?>"><strong>BAN THIS USER</strong></a></div>

            <table cellspacing="1" class="content">
                <thead>
                    <tr>
                        <th>field</th>
                        <th>data</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>user_id</td>
                        <td><?php echo $user["user_id"]; ?></td>
                    </tr>
                    <tr>
                        <td>username</td>
                        <td><?php echo $user["username"]; ?></td>
                    </tr>
                    <tr>
                        <td>password</td>
                        <td><?php echo $user["password"]; ?></td>
                    </tr>
                    <tr>
                        <td>permissions</td>
                        <td><?php echo $user["perm"]; ?></td>
                    </tr>
                    <tr>
                        <td>email</td>
                        <td><?php echo $user["email"]; ?></td>
                    </tr>
                    <tr>
                        <td>title</td>
                        <td><?php echo $user["title"]; ?></td>
                    </tr>
                    <tr>
                        <td>avatar</td>
                        <td><?php if (isset($user["avatar_type"])) echo "<img src=\"avatars/".$user["user_id"].".".$user["avatar_type"]."\" alt=\"av\" width=\"30\" height=\"30\" />"; ?></td>
                    </tr>
                    <tr>
                        <td>avatar_type</td>
                        <td><?php echo $user["avatar_type"]; ?></td>
                    </tr>
                    <tr>
                        <td>time_zone</td>
                        <td><?php echo $user["time_zone"]; ?></td>
                    </tr>
                    <tr>
                        <td>date_reg</td>
                        <td><?php echo $date_reg; ?></td>
                    </tr>
                    <tr>
                        <td>date_visit</td>
                        <td><?php echo $date_visit; ?></td>
                    </tr>
                    <tr>
                        <td>date_post</td>
                        <td><?php echo $date_post; ?></td>
                    </tr>
                    <tr>
                        <td>register_ip</td>
                        <td><?php echo $user["register_ip"]; ?></td>
                    </tr>
                    <tr>
                        <td>visit_ip</td>
                        <td><?php echo $user["visit_ip"]; ?></td>
                    </tr>
                    <tr>
                        <td>profile</td>
                        <td class="small"><?php echo outputbody($user["profile"]); ?></td>
                    </tr>
                </tbody>
            </table>
            <table cellspacing="1" class="float" style="float: left;">
                <thead>
                    <tr>
                        <th>post ips</th>
                        <th>last used at</th>
                    </tr>
                </thead>
                <tbody>
<?php
    $sql = "SELECT ip, MAX(date) AS maxdate FROM ttf_post WHERE author_id = '$user_id' AND ip != 'NULL' GROUP BY ip ORDER BY maxdate DESC";
    if (!$result = mysql_query($sql)) showerror();
	while ($post = mysql_fetch_array($result)) {
		$date = strtolower(date("M j, Y, g\:i a", $post["maxdate"] + 3600*$ttf["time_zone"]));
?>
                    <tr>
                        <td><?php echo $post["ip"]; ?></td>
                        <td><?php echo $date; ?></td>
                    </tr>
<?php
	};
	mysql_free_result($result);
?>
                </tbody>
            </table>
            <table cellspacing="1" class="float" style="margin-left: 24px;">
                <thead>
                    <tr>
                        <th>visit ips</th>
                        <th>last used at</th>
                    </tr>
                </thead>
                <tbody>
<?php
	$sql = "SELECT ip, MAX(date) AS maxdate FROM ttf_visit WHERE user_id = '$user_id' GROUP BY ip ORDER BY maxdate DESC";
	$result = mysql_query($sql);
	while ($visit = mysql_fetch_array($result)) {
		$date = strtolower(date("M j, Y, g\:i a", $visit["maxdate"] + 3600*$ttf["time_zone"]));
?>
                    <tr>
                        <td><?php echo $visit["ip"]; ?></td>
                        <td><?php echo $date; ?></td>
                    </tr>
<?php
	};
	mysql_free_result($result);
?>
                </tbody>
            </table>
<?php

} else {

    message("user profile","fatal error","you must specify a valid user.");

};

require_once "include_footer.php";

?>
