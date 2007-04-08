<?php
/* think tank forums
 *
 * admin_user.php
 */

require "include_common.php";

// if an admin isn't logged in, then die()!
admin();

$label = "administration &raquo; user list";

require "include_header.php";

?>
            <table cellspacing="1" class="content">
                <thead>
                    <tr>
                        <th>uid</th>
                        <th>username</th>
                        <th>email</th>
                        <th>last visit</th>
                    </tr>
                </thead>
                <tbody>
<?php

$sql = "SELECT user_id, username, email, visit_date ".
       "FROM ttf_user ORDER BY user_id";
if (!$result = mysql_query($sql)) showerror();

while ($user = mysql_fetch_array($result)) {

    if ($user["visit_date"] == 0) {

        $date = "never visited";

    } else {

        $date = strtolower(date("M j, Y, g\:i a", $user["visit_date"] + 3600*$ttf["time_zone"]));

    };

?>
                    <tr>
                        <td><?php echo $user["user_id"]; ?></td>
                        <td><a href="admin_userinfo.php?user_id=<?php echo $user["user_id"]; ?>"><?php echo output($user["username"]); ?></a></td>
                        <td><a href="mailto:<?php echo $user["email"]; ?>"><?php echo $user["email"]; ?></a></td>
                        <td><?php echo $date; ?></td>
                    </tr>
<?php

};

mysql_free_result($result);

?>
                </tbody>
            </table>
<?php

require "include_footer.php";

?>
