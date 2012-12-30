<?php
/* think tank forums
 *
 * privacy.php
 */

$ttf_title = $ttf_label = "administration &raquo; change revision privacy";

require_once "include_common.php";

// this is an admin-only script--kill everyone else
kill_nonadmin();

$rev_id = clean($_REQUEST["rev_id"]);

// if a revision is not specified, kill agent
if (empty($rev_id)) {

    message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["noitemspec"]);
    die();

};



if (isset($_POST["rev_id"])) {


    header("Location: $ttf_protocol://{$ttf_cfg["address"]}/admin_privacy.php?rev_id=".$rev_id);


} else if (isset($_GET["rev_id"])) {

    $ttf_title = $ttf_label = "changing privacy of revision $rev_id";

    require_once "include_header.php";

    $sql = <<<EOF
SELECT ttf_revision.*, ttf_user.username
FROM ttf_revision, ttf_user
WHERE ttf_revision.author_id = ttf_user.user_id
   && rev_id='{$rev_id}'
EOF;

    if (!$result = mysql_query($sql)) showerror();

    $rev = mysql_fetch_array($result);

    $username = output($rev["username"]);
    $date = formatdate($rev["date"]);
    $privacy = $rev["privacy"] === NULL ? '<em>NULL</em>' : $rev["privacy"];

echo <<<EOF
            <table cellspacing="1" class="content">
                <thead>
                    <tr>
                        <th colspan="2">current information for revision {$rev_id}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>author</td>
                        <td><a href="profile.php?user_id={$rev["author_id"]}">{$username}</a></td>
                    </tr>
                    <tr>
                        <td>date</td>
                        <td>{$date[1]}</td>
                    </tr>
                    <tr>
                        <td>ip address</td>
                        <td><a href="admin_search_ip.php?ip_address={$rev["ip"]}">{$rev["ip"]}</a></td>
                    </tr>
                    <tr>
                        <td>privacy</td>
                        <td>{$privacy}</td>
                    </tr>
                </tbody>
            </table>
            <div class="contenttitle">change privacy for revision {$rev_id}</div>
            <form action="admin_privacy.php" method="post">
                <div class="contentbox">
                    <input type="radio" name="privacy" value="null"> no privacy<br />
                    <input type="radio" name="privacy" value="admin"> admin only
                </div>
                <div class="contenttitle">comment on the privacy change</div>
                <div id="editpost_textarea">
                    <textarea class="editpost" cols="72" rows="6" name="comment"></textarea>
                </div>
                <div id="editpost_button">
                    <input class="editpost" type="submit" value="commit" />
                </div>
                <div>
                    <input type="hidden" name="rev_id" value="{$rev_id}" />
                </div>
            </form>

EOF;

};

require_once "include_footer.php";
