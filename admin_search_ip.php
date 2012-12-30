<?php
/* think tank forums
 *
 * admin_search_ip.php
 */

$ttf_title = $ttf_label = "administration &raquo; search &raquo; ip address";

require_once "include_common.php";

kill_nonadmin();



if (isset($_GET["ip_address"])) {

    $ip_address = clean($_GET["ip_address"]);

    $ttf_title = $ttf_label = "administration &raquo; search &raquo; ip address &raquo; $ip_address";

    require_once "include_header.php";

    //// search the revision table

    echo <<<EOF
            <table cellspacing="1" class="content">
                <tr>
                    <th colspan="6">matches in the revision table</th>
                </tr>
                <tr>
                    <th><span title="count for this username" class="pro-tip">c</span></th>
                    <th>username</th>
                    <th>min rev_id</th>
                    <th>max rev_id</th>
                    <th>min date</th>
                    <th>max date</th>
                </tr>

EOF;

    $sql = <<<EOF
SELECT COUNT(ttf_revision.author_id) AS cnt, ttf_revision.author_id AS uid, ttf_user.username AS un, min(ttf_revision.rev_id) AS rev0, max(ttf_revision.rev_id) AS rev1, min(ttf_revision.date) AS dat0, max(ttf_revision.date) AS dat1
FROM ttf_revision
LEFT JOIN ttf_user ON ttf_user.user_id=ttf_revision.author_id
WHERE ttf_revision.ip = '{$ip_address}'
GROUP BY ttf_revision.author_id
ORDER BY cnt DESC, dat1 DESC
EOF;

    if (!$result = mysql_query($sql)) showerror();

    while($row = mysql_fetch_array($result)) {

    $dat0 = formatdate($row["dat0"]);
    $dat1 = formatdate($row["dat1"]);

echo <<<EOF
                    <tr>
                        <td>{$row["cnt"]}</td>
                        <td><a href="profile.php?user_id={$row["uid"]}">{$row["un"]}</a></td>
                        <td>{$row["rev0"]}</td>
                        <td>{$row["rev1"]}</td>
                        <td>{$dat0[1]}</td>
                        <td>{$dat1[1]}</td>
                    </tr>

EOF;

    };

    if (mysql_num_rows($result) == 0) {

        echo <<<EOF
                    <tr>
                        <td colspan="6"><em>no matches.</em></td>
                    </tr>

EOF;

    };

    echo <<<EOF
            </table>

EOF;



    //// search the user table

    echo <<<EOF
            <table cellspacing="1" class="content">
                <tr>
                    <th colspan="3">matches in the user table</th>
                </tr>
                <tr>
                    <th>username</th>
                    <th><span title="this user registered with this ip" class="pro-tip">registered</span></th>
                    <th><span title="this user last visited with this ip" class="pro-tip">last visit</span></th>
                </tr>

EOF;

    $sql = <<<EOF
SELECT user_id AS uid, username AS un,
       CASE register_ip
        WHEN '{$ip_address}' THEN register_date ELSE ''
        END as reg,
       CASE visit_ip
        WHEN '{$ip_address}' THEN visit_date ELSE ''
        END as vis
FROM ttf_user
WHERE register_ip = '{$ip_address}'
   OR visit_ip = '{$ip_address}'
ORDER BY username
EOF;

    if (!$result = mysql_query($sql)) showerror();

    while($row = mysql_fetch_array($result)) {

        $reg = !empty($row["reg"]) ? formatdate($row["reg"]) : array('','');
        $vis = !empty($row["vis"]) ? formatdate($row["vis"]) : array('','');

echo <<<EOF
                    <tr>
                        <td><a href="profile.php?user_id={$row["uid"]}">{$row["un"]}</a></td>
                        <td>{$reg[1]}</td>
                        <td>{$vis[1]}</td>
                    </tr>

EOF;

    };

    if (mysql_num_rows($result) == 0) {

        echo <<<EOF
                    <tr>
                        <td colspan="6"><em>no matches.</em></td>
                    </tr>

EOF;

    };

    echo <<<EOF
            </table>

EOF;



    //// search the recovery table

    echo <<<EOF
            <table cellspacing="1" class="content">
                <tr>
                    <th colspan="2">matches in the recover table</th>
                </tr>
                <tr>
                    <th>username</th>
                    <th>date</th>
                </tr>

EOF;

    $sql = <<<EOF
SELECT ttf_recover.user_id AS uid, ttf_recover.date AS dat, ttf_user.username AS un
FROM ttf_recover
LEFT JOIN ttf_user ON ttf_user.user_id=ttf_recover.user_id
WHERE ttf_recover.ip = '{$ip_address}'
ORDER BY recover_id
EOF;

    if (!$result = mysql_query($sql)) showerror();

    while($row = mysql_fetch_array($result)) {

    $date = formatdate($row["dat"]);

echo <<<EOF
                    <tr>
                        <td><a href="profile.php?user_id={$row["uid"]}">{$row["un"]}</a></td>
                        <td>{$date[1]}</td>
                    </tr>

EOF;

    };

    if (mysql_num_rows($result) == 0) {

        echo <<<EOF
                    <tr>
                        <td colspan="6"><em>no matches.</em></td>
                    </tr>

EOF;

    };

    echo <<<EOF
            </table>

EOF;



    //// search the banned table

    echo <<<EOF
            <table cellspacing="1" class="content">
                <tr>
                    <th colspan="1">matches in the banned table</th>
                </tr>
                <tr>
                    <th>username</th>
                </tr>

EOF;

    $sql = <<<EOF
SELECT ttf_banned.user_id, ttf_user.username
FROM ttf_banned
LEFT JOIN ttf_user ON ttf_user.user_id=ttf_banned.user_id
WHERE ttf_banned.ip = '{$ip_address}'
ORDER BY username
EOF;

    if (!$result = mysql_query($sql)) showerror();

    while($row = mysql_fetch_array($result)) {

echo <<<EOF
                    <tr>
                        <td><a href="profile.php?user_id={$row["user_id"]}">{$row["username"]}</a></td>
                    </tr>

EOF;

    };

    if (mysql_num_rows($result) == 0) {

        echo <<<EOF
                    <tr>
                        <td colspan="1"><em>no matches.</em></td>
                    </tr>

EOF;

    };

    echo <<<EOF
            </table>

EOF;



    require_once "include_footer.php";
    die();

};

require_once "include_header.php";

echo <<<EOF
            <div class="contenttitle">search the database for an ip address</div>
            <div class="contentbox" style="text-align: center;">
                <form action="admin_search_ip.php" method="get">
                    <div>
                        <input size="16" type="text" name="ip_address" />
                        <input type="submit" value="search" />
                    </div>
                </form>
            </div>

EOF;

require_once "include_footer.php";

