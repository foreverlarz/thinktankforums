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

$sql = "SELECT user_id, username, avatar_type, title, email, visit_date FROM ttf_user";
if (!$result = mysql_query($sql)) showerror();

while ($user = mysql_fetch_array($result)) {

    if ($user["visit_date"] == 0) {

        $date = "never visited";

    } else {

        $date = strtolower(date("M j, Y, g\:i a", $user["visit_date"] + 3600*$ttf["time_zone"]));

    };

?>
            <div class="userbar"<?php if ($i) echo ' style="margin-top: 5px;"'; ?>>
                <div class="userbar_left">
<?php
    if (isset($user["avatar_type"])) {
?>
                    <img src="avatars/<?php echo $user["user_id"].".".$user["avatar_type"]; ?>" alt="av" width="30" height="30" />
<?php
    } else {
        echo "                    &nbsp;\n";
    };
?>
                </div>
                <div class="userbar_right"><?php echo $date; ?><br />
                    <?php echo $user["email"]."\n"; ?>
                </div>
                <?php echo $user["user_id"]."\n"; ?>
                <a class="username" href="admin_userinfo.php?user_id=<?php echo $user["user_id"]; ?>"><?php echo output($user["username"]); ?></a><br />
                <?php echo output($user["title"])."\n"; ?>
            </div>
<?php

    $i = TRUE;
};

mysql_free_result($result);

require "include_footer.php";

?>
