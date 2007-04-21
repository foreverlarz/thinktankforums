<?php
/* think tank forums
 *
 * admin_user.php
 */

require_once "include_common.php";

// if an admin isn't logged in, then die()!
admin();

$label = "administration &raquo; user list";

require_once "include_header.php";


if (isset($_GET["banuser"])) {
        
    $user_id = clean($_GET["banuser"]);

    banUser($user_id);
}

else if (isset($_GET["unbanuser"])) {
        
    $user_id = clean($_GET["unbanuser"]);

    unbanUser($user_id);

} else { 

?>
            <table cellspacing="1" class="content">
                <thead>
                    <tr>
                        <th>uid</th>
                        <th>username</th>
                        <th>email</th>
                        <th>last visit</th>
                        <th>ban user</th>
                    </tr>
                </thead>
                <tbody>
<?php

$sql = "SELECT user_id, username, email, visit_date, perm ".
       "FROM ttf_user ORDER BY user_id";
if (!$result = mysql_query($sql)) showerror();

while ($user = mysql_fetch_array($result)) {

    unset($hl);

    $timeout = 60*60*24*7*2; // two weeks of seconds (s*m*h*d*w)

    if ($user["visit_date"] > (time() - $timeout)) {

        $hl = " class=\"highlight\"";

    };


?>
                    <tr>
                        <td><?php echo $user["user_id"]; ?></td>
                        <td><a href="admin_userinfo.php?user_id=<?php echo $user["user_id"]; ?>"><?php echo output($user["username"]); ?></a></td>
                        <td><a href="mailto:<?php echo $user["email"]; ?>"><?php echo $user["email"]; ?></a></td>
                        <td<?php echo $hl; ?>><?php echo formatdate($user["visit_date"]); ?></td>
                        <td><a href="admin_user.php?<?php 
                            if ($user["perm"] != "banned") {

                                echo "banuser=".$user["user_id"]."\">ban</a>";

                            } else {
                                
                                echo "unbanuser=".$user["user_id"]."\">unban</a>";

                            }

                            ?>
                        </td>
                            
                    </tr>
<?php

};

mysql_free_result($result);

?>
                </tbody>
            </table>
<?php

};

require_once "include_footer.php";

?>
