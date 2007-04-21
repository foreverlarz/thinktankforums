<?php
/* think tank forums
 *
 * editpost.php
 */

require_once "include_common.php";

$post_id = clean($_REQUEST["post_id"]);
//$body  = clean($_POST["body"]); don't do this! it jerx the werx
$body = $_POST["body"];

// if the agent is logged in as a valid user
if (isset($ttf["uid"])) {

    // if a post_id is specified
    if (!empty($post_id)) {

        // let's check some permissions (either admin or author)
        if ($ttf["perm"] != "admin") {

            $sql = "SELECT author_id FROM ttf_post ".
                   "WHERE post_id='$post_id'";
            if (!$result = mysql_query($sql)) showerror();
            list($author_id) = mysql_fetch_array($result);
            mysql_free_result($result);

            if ($ttf["uid"] != $author_id) {

                message("edit a post", "fatal error",
                        "you do not have permission to edit this post.");
                die();

            };

        };

        if (!empty($body)) {

            // let's build our current HEAD revision
            // if this post already has revisions
            list($head, $lastrev) = buildHead($post_id, 'post');

            // if it doesn't have revisions for some
            // weird reason, make one and set it as HEAD
            if (empty($head)) {
                /* this shouldn't ever really happen ~*~*~*~
                $sql = "SELECT * FROM ttf_post WHERE post_id='$post_id'";
                if (!$result = mysql_query($sql)) showerror();
                $post = mysql_fetch_array($result);
                mysql_free_result($result);

                $sql = "INSERT INTO ttf_revision SET ".
                       "ref_id='{$post["post_id"]}' ".
                       "type='post' ".
                       "author_id='{$post["author_id"]}' ".
                       "num='0' ".
                       "date='{$post["date"]}' ".
                       "ip='{$post["ip"]}' ".
                       "body='{$post["body"]}'";
                if (!$result = mysql_query($sql)) showerror();

                $head = $post["body"];
                 */

                message("edit a post", "fatal error",
                        "serious error encountered. please contact an admin.");

            };

            // so now we have the current HEAD as $head;
            // we need to diff our new $body against $head
            // and insert a new ttf_revision and ttf_post
            //$diff = clean(serialize(diff($head, $body)));
            $diff = clean(serialize(diff($head, $body)));
            $newrev = $lastrev + 1;

            $sql = "INSERT INTO ttf_revision SET ".
                   "ref_id='$post_id', ".
                   "type='post', ".
                   "author_id='{$ttf["uid"]}', ".
                   "num='$newrev', ".
                   "date=UNIX_TIMESTAMP(), ".
                   "ip='{$_SERVER["REMOTE_ADDR"]}', ".
                   "body='$diff'";
            if (!$result = mysql_query($sql)) showerror();

            $sql = "UPDATE ttf_post SET ".
                   "rev=rev+1, ".
                   //"date=UNIX_TIMESTAMP(), ".             this is a terrible idea!
                   //"ip='{$_SERVER["REMOTE_ADDR"]}', ".    this is stupid, ips should just be
                   //                                       stored with the ttf_revision data
                   "body='$body' ".
                   "WHERE post_id='$post_id'";
            if (!$result = mysql_query($sql)) showerror();

            // wow, all of that worked! let's grab the thread_id
            // if we need it and redirect the agent to the thread
            $sql = "SELECT thread_id FROM ttf_post ".
                   "WHERE post_id='$post_id'";
            if (!$result = mysql_query($sql)) showerror();
            list($thread_id) = mysql_fetch_array($result);

            header("Location: thread.php?thread_id=".
                   $thread_id."#".$post_id);

            /*
            message("edit a post", "successful",
                "here's the raw diff: ".serialize(diff($head, $body)).
                "\n\nand here's the cleaned diff: ".$diff);
             */

        } else if (!isset($_POST["body"])) {

            require_once "include_header.php";

            // they need to see a HEAD rev build from scratch
            // because ttf_post is formatted, so make it
            list($head, $lastrev) = buildHead($post_id, 'post');

?>
            <form action="editpost.php" method="post">
            <div class="contenttitle">you're editing <?php echo "post_id $post_id, rev $lastrev"; ?></div>
                <div class="contentbox" style="text-align: center;">
                    <textarea class="profile" cols="70" rows="15" name="body" wrap="virtual"><?php print $head; ?></textarea><br />
                </div>
                <div class="contenttitle">apply changes</div>
                <div class="contentbox" style="text-align: center;">
                        <input type="submit" value="apply" />
                        <input type="hidden" name="edit" value="bulk" />
                </div>
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
            </form>
<?php

        } else {

            message("edit a post", "fatal error",
                    "you cannot edit a post into inexistence. use the archive link!");

        };

    } else {

        message("edit a post", "fatal error",
                "you must specify a post to edit.");

    };

} else {

    message("edit a post", "fatal error",
            "you must be logged in to edit a post.");

};

?>
