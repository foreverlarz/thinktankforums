<?php
/* think tank forums
 *
 * admin_ban.php
 */




/* ban a user
 * ~~~~~~~~~~
 * this code allows the administration to block access to the forums
 * for a specific user, while also banning the register_ip and all 
 * visit_ip's as well.
 */
function banUser($user_id) {
    admin();  // I dont think this is necessary

    $sql = "SELECT perm, username, register_ip, visit_ip FROM ttf_user WHERE user_id='$user_id'";
    if (!$result = mysql_query($sql)) showerror();
    $user = mysql_fetch_array($result);
    mysql_free_result($result);

    if (!empty($user["perm"]) && $user["perm"] != "banned") {

        if (!empty($user["register_ip"])) {
            $sql = "REPLACE INTO ttf_banned ".
                "SET user_id='$user_id', ip='{$user["register_ip"]}'";
            if (!$result = mysql_query($sql)) {
                showerror();
            } else {
                $arrMessage[] = "register ip of " .$user["register_ip"]." was banned";
            };
        } else {
            $arrMessage[] = "<span class=\"error\">no register ip for this user</span>";
        };


        if (!empty($user["visit_ip"])) {
            $sql = "REPLACE INTO ttf_banned ".
              "SET user_id='$user_id', ip='{$user["visit_ip"]}'";
            if (!$result = mysql_query($sql)) {
                showerror();
            } else {
                $arrMessage[] = "visit ip of ".$user["visit_ip"]." was banned";
            };
        } else {
            $arrMessage[] = "<span class=\"error\">no visit ip for this user</span>";
        };


        $sql = "SELECT post_id, ip FROM ttf_post WHERE author_id='$user_id' ".
           "&& ip IS NOT NULL GROUP BY ip";
        if (!$result = mysql_query($sql)) showerror();

        while ($post = mysql_fetch_array($result)) {

            $sql = "REPLACE INTO ttf_banned SET user_id='$user_id', ip='{$post["ip"]}'";
            if (!$resultx = mysql_query($sql)) {
                showerror();
            } else {
                $arrMessage[] = "post ip of ".$user["post_ip"]." was banned";
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
                $arrMessage[] = "visit ip of ".$visit["ip"]." was banned";
            };
        };
        mysql_free_result($result);


        $sql = "UPDATE ttf_user SET perm='banned' WHERE user_id='$user_id'";
        if (!$result = mysql_query($sql)) {
            showerror();
            } else {
                $arrMessage[] = $user["username"] ." -> banned!";
            };


    } elseif ($user["perm"] == "banned") {

        $arrMessage[] = "<span class=\"error\">the user_id provided is already banned</span>";

    } else {

        $arrMessage[] = "<span class=\"error\">the user_id provided is invalid</span>";

    };

    if (!empty($arrMessage)) {
    
        message("banuser", "information", $arrMessage);
    
    }; 
};



/* unban a user
 * ~~~~~~~~~~~~
 * this code allows the administration to unblock access to the forums
 * for a specific user, while also unbanning the register_ip and all 
 * visit_ip's as well. 
 */
function unbanUser($user_id) {
    admin(); // I dont think this one is necessary either - see banUser()

    $sql = "SELECT perm, username, register_ip, visit_ip FROM ttf_user WHERE user_id='$user_id'";
    if (!$result = mysql_query($sql)) showerror();
    $user = mysql_fetch_array($result);
    mysql_free_result($result);

    if ($user["perm"] == "banned") {

        $sql = "DELETE FROM ttf_banned WHERE user_id='$user_id' ";

        if (!$result = mysql_query($sql)) {
                
            showerror();

        } else {

            $arrMessage[] = $user["username"] ."'s ip's where unbanned";

        };


        $sql = "UPDATE ttf_user SET perm='user' WHERE user_id='$user_id'";

        if (!$result = mysql_query($sql)) {

            showerror();

        } else {

            $arrMessage[] = $user["username"] ." -> unbanned!";

        };


    } elseif ($user["perm"] != "banned") {

        $arrMessage[] = "<span class=\"error\">". $user["username"] ." is not currently banned!</span>";

    } else {

        $arrMessage[] = "<span class=\"error\">the user_id provided is invalid</span>";

    };

    if (!empty($arrMessage)) {
    
        message("unbanuser", "information", $arrMessage);
    
    }; 
};






require_once "include_common.php";

// if an admin isn't logged it, then die()!
admin();

$label = "administration &raquo; user ban";

require_once "include_header.php";

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

    message("user information", "fatal error", "the user_id provided is already banned.");

} else {

    message("user information", "fatal error", "the user_id provided is invalid.");

};

require_once "include_footer.php";

?>
