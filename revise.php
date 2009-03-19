<?php
/* think tank forums
 *
 * revise.php
 */

$ttf_title = $ttf_label = "revise a post";

require_once "include_common.php";

// people must be logged in to use this script
kill_guests();


// pull through the variables
// note: we don't clean these here,
// because we will want to
// use $body in its raw-input form
$post_id = $_REQUEST["post_id"];
$body = $_POST["body"];
$archive = $_POST["archive"];
$unarchive = $_POST["unarchive"];
$rev_num = $_POST["rev_num"];


// if a post is not specified, kill agent
if (empty($post_id)) {

    message($ttf_label, $ttf_msg["fatal_error"],
            "you must specify a post to revise.");
    die();

};


// let's check some permissions (must be either admin or author)
if ($ttf["perm"] != 'admin') {

    $sql = "SELECT author_id FROM ttf_post      ".
           "WHERE post_id='".clean($post_id)."' ";
    if (!$result = mysql_query($sql)) showerror();
    list($author_id) = mysql_fetch_array($result);

    if ($ttf["uid"] != $author_id) {

        message($ttf_label, $ttf_msg["fatal_error"],
                "you do not have permission to revise this post.");
        die();

    };

};


// get the HEAD of the post and the number of revisions
$sql = "SELECT SQL_CALC_FOUND_ROWS          ".
       "       body                         ".
       "FROM ttf_revision                   ".
       "WHERE ref_id='".clean($post_id)."'  ".
       "   && type='post'                   ".
       "ORDER BY date DESC LIMIT 1          ";

if (!$result = mysql_query($sql)) showerror();
list($head) = mysql_fetch_array($result);

$sql = "SELECT FOUND_ROWS()";
if (!$result = mysql_query($sql)) showerror();
list($num_revs) = mysql_fetch_array($result);


if (empty($num_revs)) {

    message($ttf_label, $ttf_msg["fatal_error"],
            "the specified post could not be retrieved.");
    die();

};


// if the latest revision was an archival,
// pull the next latest revision
if ($head === NULL) {

    $sql = "SELECT body                         ".
           "FROM ttf_revision                   ".
           "WHERE ref_id='".clean($post_id)."'  ".
           "   && type='post'                   ".
           "ORDER BY date DESC LIMIT 1 OFFSET 1 ";

    if (!$result = mysql_query($sql)) showerror();
    list($head) = mysql_fetch_array($result);

    // if the next latest revision is still an archival,
    // something went seriously wrong. exit!
    if ($head === NULL) {

        message($ttf_label, $ttf_msg["fatal_error"],
                "serious error encountered. please contact an admin.");
        die();

    };

    $archived = TRUE;

} else {

    $archived = FALSE;

};


if (isset($_POST["post_id"])) {

    if ($rev_num != $num_revs) {

        message($ttf_label, $ttf_msg["fatal_error"],
            "while you were editing the post, someone else committed a revision. ".
            "please go back, save your changes, reload the edit page, then merge ".
            "in your changes with the current version by hand.");
        die();

    };


    if ($archive == "TRUE" && empty($unarchive) && strcmp($body, $head) === 0) {
        
        if ($archived == TRUE) {

            message($ttf_label, $ttf_msg["fatal_error"], "you cannot archive an archived post.");
            die();

        };

        // insert the new revision
        $sql = "INSERT INTO ttf_revision            ".
               "SET ref_id='".clean($post_id)."',   ".
               "    type='post',                    ".
               "    author_id='{$ttf["uid"]}',      ".
               "    date=UNIX_TIMESTAMP(),          ".
               "    ip='{$_SERVER["REMOTE_ADDR"]}', ".
               "    body=NULL                       ";
        if (!$result = mysql_query($sql)) showerror();

        // update the formatted ttf_post
        $sql = "UPDATE ttf_post                         ".
               "SET rev=rev+1,                          ".
               "    body='".clean(outputbody(NULL))."' ".
               "WHERE post_id='".clean($post_id)."'     ";
        if (!$result = mysql_query($sql)) showerror();

    } else if ((strcmp($body, $head) !== 0 || $unarchive == "TRUE") && empty($archive)) {

        if (strcmp(trim($body), "") === 0) {

            message($ttf_label, $ttf_msg["fatal_error"],
                    "a post cannot be an empty string or whitespace.");
            die();

        };

        // insert the new revision
        $sql = "INSERT INTO ttf_revision            ".
               "SET ref_id='".clean($post_id)."',   ".
               "    type='post',                    ".
               "    author_id='{$ttf["uid"]}',      ".
               "    date=UNIX_TIMESTAMP(),          ".
               "    ip='{$_SERVER["REMOTE_ADDR"]}', ".
               "    body='".clean($body)."'         ";
        if (!$result = mysql_query($sql)) showerror();

        // update the formatted ttf_post
        $sql = "UPDATE ttf_post                         ".
               "SET rev=rev+1,                          ".
               "    body='".clean(outputbody($body))."' ".
               "WHERE post_id='".clean($post_id)."'     ";
        if (!$result = mysql_query($sql)) showerror();

    } else if (empty($archive) && empty($unarchive) && strcmp($body, $head) === 0) {

        message($ttf_label, $ttf_msg["fatal_error"], "you didn't make any changes.");
        die();

    } else {

        message($ttf_label, $ttf_msg["fatal_error"], "you cannot make multiple changes at once.");
        die();

    };


    // update the user's last rev date
    $sql = "UPDATE ttf_user                 ".
           "SET rev_date=UNIX_TIMESTAMP()  ".
           "WHERE user_id={$ttf["uid"]}     ";
    if (!$result = mysql_query($sql)) showerror();


    // wow, all of that worked! let's grab the thread_id
    // and redirect the user to their edited post
    $sql = "SELECT thread_id                    ".
           "FROM ttf_post                       ".
           "WHERE post_id='".clean($post_id)."' ";
    if (!$result = mysql_query($sql)) showerror();
    list($thread_id) = mysql_fetch_array($result);


    header("Location: $ttf_protocol://{$ttf_cfg["address"]}/thread.php?thread_id=".$thread_id."#post-".$post_id);


} else if (isset($_GET["post_id"])) {


    $ttf_title = $ttf_label = "creating revision $num_revs of post $post_id";

    require_once "include_header.php";

?>
            <form action="revise.php" method="post">
                <div class="contenttitle">edit the contents of the post</div>
                <div id="editpost_textarea">
                    <textarea class="editpost" cols="72" rows="20" name="body"><?php echo output($head); ?></textarea>
                    </div>
<?php

    if ($archived === FALSE) {

        echo <<<EOF
                <div class="contenttitle">archive the post</div>
                <div id="editpost_archive">
                    <input type="checkbox" name="archive" value="TRUE" />
                    don't bother with editing; simply don't display this post.
                </div>

EOF;

    } else {

        echo <<<EOF
                <div class="contenttitle">notice</div>
                <div id="editpost_archive">
                    <input type="hidden" name="unarchive" value="TRUE" />
                    <i>this post is currently archived.</i>
                    by submitting this form, the post will be un-archived.
                </div>

EOF;

    };

    echo <<<EOF
                <div id="editpost_button">
                    <input class="editpost" type="submit" value="submit revision" />
                </div>
                <div>
                    <input type="hidden" name="post_id" value="{$post_id}" />
                    <input type="hidden" name="rev_num" value="{$num_revs}" />
                </div>
            </form>

EOF;

};

require_once "include_footer.php";

?>
