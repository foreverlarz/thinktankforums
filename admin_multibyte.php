<?php
/* think tank forums
 *
 * admin_multibyte.php
 */

require_once "include_common.php";

// create the header label
$label = " all ttf posts with multi-byte characters";

// let's output a page to the user
require_once "include_header.php";

// select the posts in this thread
$sql = "SELECT ttf_post.post_id, ttf_post.author_id, ttf_post.date, ".
       "ttf_post.body, ttf_user.username, ttf_user.title, ttf_user.avatar_type ".
       "FROM ttf_post, ttf_user ".
       "WHERE ttf_post.author_id = ttf_user.user_id ".
       "      && LENGTH(body) != CHAR_LENGTH(body) ".
       "ORDER BY date ASC";
if (!$result = mysql_query($sql)) showerror();

// for each post...
while ($post = mysql_fetch_array($result)) {

    // format the date
    $date = strtolower(date("g\:i a, j M y", $post["date"] + 3600*$ttf["time_zone"]));
?>

            <a name="<?php echo $post["post_id"]; ?>"></a>
            <div class="userbar">
                <div class="userbar_left">
<?php
    if (isset($post["avatar_type"])) {
?>
                    <img src="avatars/<?php echo $post["author_id"].".".$post["avatar_type"]; ?>" alt="av" width="30" height="30" />
<?php
    } else {
        echo "                    &nbsp;\n";
    };
?>
                </div>
                <div class="userbar_right"><?php echo $date; ?><?php
    if ($ttf["perm"] == 'admin' || $ttf["uid"] == $post["author_id"]) {
?><br />
                    <a class="link" href="archivepost.php?post_id=<?php echo $post["post_id"]; ?>" onclick="return confirmaction()">archive</a>
<?php
    };
?>
                </div>
                <a class="username" href="profile.php?user_id=<?php echo $post["author_id"]; ?>"><?php echo output($post["username"]); ?></a><br />
                <?php echo output($post["title"])."\n"; ?>
            </div>
            <div class="contentbox_sm">
<?php echo outputbody($post["body"])."\n"; ?>
            </div>
<?php

};

mysql_free_result($result);

require_once "include_footer.php";

?>
