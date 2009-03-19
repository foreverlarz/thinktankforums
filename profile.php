<?php
/* think tank forums
 *
 * profile.php
 */

$ttf_title = $ttf_label = "user profile";

require_once "include_common.php"; 
require_once "include_header.php";

$user_id = clean($_GET["user_id"]);

$sql = "SELECT user_id,         ".
       "       username,        ".
       "       avatar_type,     ".
       "       title,           ".
       "       profile,         ".
       "       register_date,   ".
       "       visit_date       ".
       "FROM ttf_user           ".
       "WHERE user_id='$user_id'";
if (!$result = mysql_query($sql)) showerror();
$user = mysql_fetch_array($result);

// if a user was not found with the given id
if (empty($user["user_id"])) {

    message($ttf_label, $ttf_msg["fatal_error"], "you must specify a valid user.");
    die();

};

$visit_date = formatdate($user["visit_date"]);
$register_date = formatdate($user["register_date"]);

$sql = "SELECT COUNT(*)         ".
       "FROM ttf_revision       ".
       "WHERE ref_id='$user_id' ".
       "   && type='profile'    ";
if (!$result = mysql_query($sql)) showerror();
list($profilerev) = mysql_fetch_array($result);

$sql = "SELECT COUNT(*)         ".
       "FROM ttf_revision       ".
       "WHERE ref_id='$user_id' ".
       "   && type='title'      ";
if (!$result = mysql_query($sql)) showerror();
list($titlerev) = mysql_fetch_array($result);

$sql = "SELECT COUNT(*)             ".
       "FROM ttf_post               ".
       "WHERE author_id='$user_id'  ";
if (!$result = mysql_query($sql)) showerror();
list($numposts) = mysql_fetch_array($result);

$sql = "SELECT COUNT(*)             ".
       "FROM ttf_thread             ".
       "WHERE author_id='$user_id'  ";
if (!$result = mysql_query($sql)) showerror();
list($numthreads) = mysql_fetch_array($result);

?>
            <div class="userbar">
                <div class="userbar_left">
<?php

if (isset($user["avatar_type"])) {

    echo "                    <img src=\"avatars/{$user["user_id"]}.{$user["avatar_type"]}\" alt=\"av\" width=\"30\" height=\"30\" />";

} else {

    echo "                    &nbsp;\n";

};

?>
                </div>
                <span class="username"><?php echo output($user["username"]); ?></span><br />
<?php
echo <<<EOF
                {$user["title"]}
            </div>
            <div class="contentbox_sm">
                {$user["profile"]}
            </div>
            <table cellspacing="1" class="content">
                <thead>
                    <tr>
                        <th colspan="2">user statistics</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="revision.php?type=title&amp;ref_id={$user["user_id"]}">title revisions</a>
                        </td>
                        <td>{$titlerev}</td>
                    </tr>
                    <tr>
                        <td>
                            <a href="revision.php?type=profile&amp;ref_id={$user["user_id"]}">profile revisions</a>
                        </td>
                        <td>{$profilerev}</td>
                    </tr>
                    <tr>
                        <td>number of posts</td>
                        <td>{$numposts}</td>
                    </tr>
                    <tr>
                        <td>number of threads</td>
                        <td>{$numthreads}</td>
                    </tr>
                    <tr>
                        <td>last visited</td>
                        <td><span title="{$visit_date[1]}">{$visit_date[0]}</span></td>
                    </tr>
                    <tr>
                        <td>account registered</td>
                        <td><span title="{$register_date[1]}">{$register_date[0]}</span></td>
                    </tr>
                </tbody>
            </table>

EOF;

$sql = "SELECT ttf_post.post_id,                        ".
       "       ttf_post.thread_id,                      ".
       "       ttf_post.date,                           ".
       "       ttf_thread.title                         ".
       "FROM ttf_post                                   ".
       "LEFT JOIN ttf_thread                            ".
       "  ON ttf_post.thread_id=ttf_thread.thread_id    ".
       "WHERE ttf_post.author_id='$user_id'             ".
       "ORDER BY date DESC LIMIT 5                      ";
if (!$result = mysql_query($sql)) showerror();
if (mysql_num_rows($result) != 0) {

    echo <<<EOF
            <table cellspacing="1" class="content">
                <thead>
                    <tr>
                        <th colspan="2">recent posts by this user</th>
                    </tr>
                </thead>
                <tbody>

EOF;

    while ($post = mysql_fetch_array($result)) {

        $date = formatdate($post["date"]);

        echo <<<EOF
                    <tr>
                        <td><a href="thread.php?thread_id={$post["thread_id"]}#post-{$post["post_id"]}">{$post["title"]}</a></td>
                        <td><span title="{$date[1]}">{$date[0]}</span></td>
                    </tr>

EOF;

    };

    echo <<<EOF
                </tbody>
            </table>

EOF;

};

require_once "include_footer.php";

?>
