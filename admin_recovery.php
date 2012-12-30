<?php
/* think tank forums
 *
 * admin_recovery.php
 */

$ttf_title = $ttf_label = "administration &raquo; password recovery";

require_once "include_common.php";

// this is an admin-only script--kill everyone else
kill_nonadmin();



if (isset($_GET["recover_id"]) && empty($_GET["action"])) {

    $recover_id = clean($_GET["recover_id"]);

    $sql = "DELETE FROM ttf_recover WHERE recover_id='$recover_id'";

    if (!$result = mysql_query($sql)) {

        showerror();

    } else {

        set_msg("<strong>success:</strong> the recovery entry has been removed.", "green");

        header("Location: $ttf_protocol://{$ttf_cfg["address"]}/admin_recovery.php");

    };

    die();

} else if (isset($_GET["ip_address"]) && empty($_GET["action"])) {

    $ip_address = clean($_GET["ip_address"]);

    $ttf_title = $ttf_label = "ban $ip_address and clean up";

    require_once "include_header.php";

    echo <<<EOF
                <div class="contentbox-orange">
                    if you continue with this action,
                    <ul>
                        <li>all recovery entries associated with this ip address will be removed.</li>
                        <li>the ip address {$ip_address} will be banned.</li>
                    </ul><br />
                    <a href="admin_recovery.php?action=clean&ip_address={$ip_address}">continue</a>
                </div>

EOF;

    require_once "include_footer.php";

    die();

} else if (isset($_GET["ip_address"]) && $_GET["action"] == "clean") {

    set_msg("<strong>error:</strong> this action has not yet been implemented.", "red");

    header("Location: $ttf_protocol://{$ttf_cfg["address"]}/admin_recovery.php");

    die();

};



require_once "include_header.php";

$sql = <<<EOF
SELECT ttf_recover.recover_id,
       ttf_recover.date,
       ttf_recover.ip,
       ttf_recover.user_id AS r_uid,
       ttf_user.user_id AS u_uid,
       ttf_user.username
FROM ttf_recover
LEFT JOIN ttf_user ON ttf_user.user_id=ttf_recover.user_id
ORDER BY date
EOF;

if (!$result = mysql_query($sql)) showerror();

echo <<<EOF
            <table cellspacing="1" class="content">
                <tr>
                    <th>id</th>
                    <th>date</th>
                    <th>user</th>
                    <th>ip</th>
                    <th>actn</th>
                </tr>

EOF;

while($row = mysql_fetch_array($result)) {

    $date = formatdate($row["date"]);
    $user = (!empty($row["u_uid"])) ?
            "<a href=\"admin_userinfo.php?user_id={$row["u_uid"]}\">{$row["username"]}</a>" :
            "<em>{$row["r_uid"]}</em>";

    echo <<<EOF
                    <tr>
                        <td>{$row["recover_id"]}</td>
                        <td>{$date[1]}</td>
                        <td>{$user}</td>
                        <td><a href="admin_search_ip.php?ip_address={$row["ip"]}">{$row["ip"]}</a></td>
                        <td><a href="admin_recovery.php?recover_id={$row["recover_id"]}">del</a>, <a href="admin_recovery.php?ip_address={$row["ip"]}">ban</a></td>
                    </tr>

EOF;

};

if(mysql_num_rows($result) === 0) {

    echo <<<EOF
                    <tr>
                        <td colspan="5"><em>no recovery table entries exist.</em></td>
                    </tr>

EOF;

};

echo "            </table>";

require_once "include_footer.php";

