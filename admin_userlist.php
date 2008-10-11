<?php
/* think tank forums
 *
 * admin_userlist.php
 */

$ttf_title = $ttf_label = "administration &raquo; user list";

require_once "include_common.php";

// this is an admin-only script--kill everyone else
kill_nonadmin();

require_once "include_header.php";

?>
            <table cellspacing="1" class="content">
                <thead>
                    <tr>
                        <th>uid</th>
                        <th>username</th>
                        <th>email</th>
                        <th>last visit</th>
                        <th>action</th>
                    </tr>
                </thead>
                <tbody>
<?php

$sql = "SELECT user_id,     ".
       "       username,    ".
       "       email,       ".
       "       visit_date,  ".
       "       perm         ".
       "FROM ttf_user       ".
       "ORDER BY user_id    ";
if (!$result = mysql_query($sql)) showerror();

while ($user = mysql_fetch_array($result)) {

    // highlight visitors within the last two weeks
    unset($visithl);
    $timeout = 60*60*24*7*2; // two weeks of seconds (s*m*h*d*w)
    if ($user["visit_date"] > (time() - $timeout)) {
        $visithl = " class=\"highlight\"";
    };

    // highlight admins and lowlight banned users
    unset($userhl);
    if ($user["perm"] == 'admin') {
        $userhl = " class=\"highlight\"";
    } else if ($user["perm"] == 'banned') {
        $userhl = " class=\"lowlight\"";
    };

    // format the visit date
    $date = formatdate($user["visit_date"]);

    // make actions to show or use
    unset($actions);
    if ($user["perm"] == 'user') {
        $actions .= "<a href=\"admin_userban.php?action=ban&amp;user_id=".$user["user_id"]."\">ban</a>";
    } else if ($user["perm"] == 'banned') {
        $actions .= "<a href=\"admin_userban.php?action=unban&amp;user_id=".$user["user_id"]."\">unban</a>";
    };

?>
                    <tr>
                        <td><?php echo $user["user_id"]; ?></td>
                        <td<?php echo $userhl; ?>><a href="admin_userinfo.php?user_id=<?php echo $user["user_id"]; ?>"><?php echo output($user["username"]); ?></a></td>
                        <td><a href="mailto:<?php echo $user["email"]; ?>"><?php echo $user["email"]; ?></a></td>
                        <td<?php echo $visithl; ?>><span title="<?php echo $date[1]; ?>"><?php echo $date[0]; ?></span></td>
                        <td><?php echo $actions; ?></td>
                    </tr>
<?php

};

?>
                </tbody>
            </table>
<?php

require_once "include_footer.php";

?>
