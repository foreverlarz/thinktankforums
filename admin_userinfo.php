<?php
/* think tank forums
 *
 * admin_userinfo.php
 */

$ttf_title = $ttf_label = "administration &raquo; user info";

require_once "include_common.php";

// this is an admin-only script--kill everyone else
kill_nonadmin();

require_once "include_header.php";

$user_id = clean($_GET["user_id"]);



// select info for the specified user
$sql = "SELECT * FROM ttf_user WHERE user_id='$user_id'";
if (!$result = mysql_query($sql)) showerror();
$user = mysql_fetch_array($result);



// if a row wasn't returned, we better die
if (empty($user["user_id"])) {

    message($ttf_label, $ttf_msg["fatal_error"], "you must specify a valid user.");
    die();

};



// format all of the dates
$register_date = formatdate($user["register_date"]);
$visit_date = formatdate($user["visit_date"]);
$post_date = formatdate($user["post_date"]);
$rev_date = formatdate($user["rev_date"]);



// count revisions, posts, threads
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
            <table cellspacing="1" class="content">
                <thead>
                    <tr>
                        <th>field</th>
                        <th>data</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>user_id</td>
                        <td><?php echo $user["user_id"]; ?></td>
                    </tr>
                    <tr>
                        <td>username</td>
                        <td><?php echo output($user["username"]); ?></td>
                    </tr>
                    <tr>
                        <td>password</td>
                        <td><?php echo output($user["password"]); ?></td>
                    </tr>
                    <tr>
                        <td>permissions</td>
                        <td><?php echo output($user["perm"]); ?></td>
                    </tr>
                    <tr>
                        <td>email</td>
                        <td><?php echo output($user["email"]); ?></td>
                    </tr>
                    <tr>
                        <td>title</td>
                        <td><?php echo output($user["title"]); ?></td>
                    </tr>
                    <tr>
                        <td>avatar</td>
                        <td><?php if (isset($user["avatar_type"])) echo "<img src=\"avatars/".$user["user_id"].".".$user["avatar_type"]."\" alt=\"avatar\" width=\"30\" height=\"30\" />"; ?></td>
                    </tr>
                    <tr>
                        <td>avatar_type</td>
                        <td><?php echo output($user["avatar_type"]); ?></td>
                    </tr>
                    <tr>
                        <td>time_zone</td>
                        <td><?php echo $user["time_zone"]; ?></td>
                    </tr>
                    <tr>
                        <td>date_reg</td>
                        <td><span title="<?php echo $register_date[1]; ?>"><?php echo $register_date[0]; ?></span></td>
                    </tr>
                    <tr>
                        <td>date_visit</td>
                        <td><span title="<?php echo $visit_date[1]; ?>"><?php echo $visit_date[0]; ?></span></td>
                    </tr>
                    <tr>
                        <td>date_post</td>
                        <td><span title="<?php echo $post_date[1]; ?>"><?php echo $post_date[0]; ?></span></td>
                    </tr>
                    <tr>
                        <td>date_revision</td>
                        <td><span title="<?php echo $rev_date[1]; ?>"><?php echo $rev_date[0]; ?></span></td>
                    </tr>
                    <tr>
                        <td>register_ip</td>
                        <td><?php echo $user["register_ip"]; ?></td>
                    </tr>
                    <tr>
                        <td>visit_ip</td>
                        <td><?php echo $user["visit_ip"]; ?></td>
                    </tr>
                    <tr>
                        <td>num_threads</td>
                        <td><?php echo $numthreads; ?></td>
                    </tr>
                    <tr>
                        <td>num_posts</td>
                        <td><?php echo $numposts; ?></td>
                    </tr>
                    <tr>
                        <td><a href="revision.php?type=title&amp;ref_id=<?php echo $user["user_id"]; ?>">title revisions</a></td>
                        <td><?php echo $titlerev; ?></td>
                    </tr>
                    <tr>
                        <td><a href="revision.php?type=profile&amp;ref_id=<?php echo $user["user_id"]; ?>">profile revisions</a></td>
                        <td><?php echo $profilerev; ?></td>
                    </tr>
                    <tr>
                        <td>profile</td>
                        <td class="small"><?php echo $user["profile"]; ?></td>
                    </tr>
                </tbody>
            </table>
            <table cellspacing="1" class="float">
                <thead>
                    <tr>
                        <th>revision ips</th>
                        <th>last used at</th>
                    </tr>
                </thead>
                <tbody>
<?php

// select all of the revision IPs (unique) with its respective highest timestamp
$sql = "SELECT ip, MAX(date) AS maxdate ".
       "FROM ttf_revision               ".
       "WHERE author_id='$user_id'      ".
       "   && ip IS NOT NULL            ".
       "GROUP BY ip                     ".
       "ORDER BY maxdate DESC           ";
if (!$result = mysql_query($sql)) showerror();

while ($rev = mysql_fetch_array($result)) {

    // format the date
    $date = formatdate($rev["maxdate"]);

?>
                    <tr>
                        <td><?php echo $rev["ip"]; ?></td>
                        <td><span title="<?php echo $date[1]; ?>"><?php echo $date[0]; ?></span></td>
                    </tr>
<?php

};

?>
                </tbody>
            </table>
<?php

require_once "include_footer.php";

?>
