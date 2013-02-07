<?php
/* think tank forums
 *
 * admin_privacy.php
 */

$length_wrap = 32;

$ttf_title = $ttf_label = "administration &raquo; change revision privacy";

require_once "include_common.php";

// this is an admin-only script--kill everyone else
kill_nonadmin();



if (isset($_POST["batch"])) {

    $batch = explode(' ', clean($_POST["batch"]));
    
    if (count($batch) == 0) {

        message($ttf_label, $ttf_msg["fatal_error"], $ttf_msg["field_empty"]);
        die();

    };

    foreach($batch as $rev_id) {

        if (!is_numeric($rev_id)) {

            message($ttf_label, $ttf_msg["fatal_error"], "all of the id numbers must be numeric.");
            die();

        };
    };

    $ttf_title = $ttf_label = "changing the privacy of a revision batch";

    require_once "include_header.php";

    $comma_list = implode(', ', $batch);

    $sql = <<<EOF
SELECT ttf_revision.*, ttf_user.username
FROM ttf_revision, ttf_user
WHERE ttf_revision.author_id = ttf_user.user_id
   && rev_id IN ($comma_list)
EOF;

    if (!$result = mysql_query($sql)) showerror();

    echo <<<EOF
            <div class="content_title">alter your batch</div>
            <div class="content_body">
                choose new permissions as you wish. if you don't change the privacy of a revision, then it will be excluded from the batch. include all information to justify this change.
            </div>
            <form action="admin_privacy.php" method="post">
                <table cellspacing="1" class="content">
                    <tr>
                        <th>rev</th>
                        <th>date</th>
                        <th>user</th>
                        <th>ip</th>
                        <th>body</th>
                        <th>cmnt</th>
                        <th>priv</th>
                    </tr>

EOF;

    while ($row = mysql_fetch_array($result)) {

        $rev = "<a href=\"revision.php?type={$row["type"]}&ref_id={$row["ref_id"]}#rev-{$row["rev_id"]}\">{$row["rev_id"]}</a>";
        $date = formatdate($row["date"]);
        $user = "<a href=\"admin_userinfo.php?user_id={$row["author_id"]}\">{$row["username"]}</a>";
        $ip = ($row["ip"] == null) ? "<em>null</em>" : $row["ip"];
        $priv = ($row["privacy"] == null) ? "null" : $row["privacy"];
        if ($row["body"] === null) {
            $body = "<em>null</em>";
        } else {
            $body = output(str_replace("\n", ' \n ', $row["body"]));
            if (strlen($body) > $length_wrap) {
                $body = wordwrap($body, $length_wrap);
                $body = substr($body, 0, strpos($body, "\n"));
            };
        };
        if ($row["comment"] == null) {
            $comment = "<em>null</em>";
        } else {
            $comment = output(str_replace("\n", ' \n ', $row["comment"]));
            if (strlen($comment) > $length_wrap) {
                $comment = wordwrap($comment, $length_wrap);
                $comment = substr($comment, 0, strpos($comment, "\n"));
            };
        };

        $priv_null = ($row["privacy"] == null) ? 'selected="true" value="nochg"' : 'value="null"';
        $priv_admin = ($row["privacy"] == 'admin') ? 'selected="true" value="nochg"' : 'value="admin"';
        $priv_user = ($row["privacy"] == 'user') ? 'selected="true" value="nochg"' : 'value="user"';

        echo <<<EOF
                    <tr class="small">
                        <td>{$rev}</td>
                        <td>{$date[1]}</td>
                        <td>{$user}</td>
                        <td>{$ip}</td>
                        <td>{$body}</td>
                        <td>{$comment}</td>
                        <td>
                            <select class="small" name="privacy[{$row["rev_id"]}]">
                                <option {$priv_null}>none</option>
                                <option {$priv_admin}>admin</option>
                                <option {$priv_user}>user</option>
                            </select>
                        </td>
                    </tr>

EOF;

    };

echo <<<EOF
                </table>
                <div class="content_title">comment on the change</div>
                <div class="content_body">
                    <textarea class="large" cols="72" rows="64" name="comment" wrap="virtual"></textarea>
                </div>
                <div>
                    <input class="submit-large" type="submit" value="submit" />
                </div>
            </form>

EOF;

    require_once "include_footer.php";

    die();

} else if (isset($_POST["comment"])) {

    $message = '';
    $sql = '';

    foreach ($_POST["privacy"] as $rev_id => $privacy) {

        if ($privacy == 'nochg') {

            $message .= "$rev_id unchanged."

        } else {

            $sql .= " ... ";

        };

    };

    echo "\n\n";
    
    echo $_POST["comment"];

/*
    $sql = <<<EOF
SELECT ttf_revision.*, ttf_user.username
FROM ttf_revision, ttf_user
WHERE ttf_revision.author_id = ttf_user.user_id
   && rev_id IN ($comma_list)
EOF;

    if (!$result = mysql_query($sql)) showerror();
*/

    die();

};

require_once "include_header.php";

echo <<<EOF
            <form action="admin_privacy.php" method="post">
                <div class="content_title">enter revision ids separated by spaces</div>
                <div class="content_body">
                    <textarea class="large" cols="72" rows="4" name="batch" wrap="virtual"></textarea>
                </div>
                <div>
                    <input class="submit-large" type="submit" value="submit" />
                </div>
            </form>

EOF;

require_once "include_footer.php";

