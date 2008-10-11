<?php
/* think tank forums
 *
 * editpost.php
 */

$ttf_title = $ttf_label = "edit a post";

require_once "include_common.php";

// people must be logged in to use this script
kill_guests();


// pull through the variables
// note: we don't clean these here,
// because we will want to
// use $body in its raw-input form
$post_id = $_REQUEST["post_id"];
$body = $_POST["body"];
$rev_num = $_POST["rev_num"];



// if a post is not specified, kill agent
if (empty($post_id)) {

    message($ttf_label, $ttf_msg["fatal_error"],
            "you must specify a post to edit.");
    die();

};



// let's check some permissions (must be either admin or author)
if ($ttf["perm"] != 'admin') {

    $sql = "SELECT author_id FROM ttf_post ".
           "WHERE post_id='".clean($post_id)."'";
    if (!$result = mysql_query($sql)) showerror();
    list($author_id) = mysql_fetch_array($result);

    if ($ttf["uid"] != $author_id) {

        message($ttf_label, $ttf_msg["fatal_error"],
                "you do not have permission to edit this post.");
        die();

    };

};



// get the $head of the post
// and get the number of revisions
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

if (!empty($body)) {

    if (empty($head) || empty($num_revs)) {

        message($ttf_label, $ttf_msg["fatal_error"],
                "serious error encountered. please contact an admin.");
        die();

    };

    if ($rev_num != $num_revs) {

        message($ttf_label, $ttf_msg["fatal_error"],
            "while you were editing the post, someone else committed a revision. ".
            "please go back, save your changes, reload the edit page, then merge ".
            "in your changes with the current version by hand.");
        die();

    };

    if (strcmp($body, $head) === 0) {

        message($ttf_label, $ttf_msg["fatal_error"],
                "you didn't make any changes.");
        die();

    };

    $sql = "INSERT INTO ttf_revision SET ".
           "ref_id='".clean($post_id)."', ".
           "type='post', ".
           "author_id='{$ttf["uid"]}', ".
           "date=UNIX_TIMESTAMP(), ".
           "ip='{$_SERVER["REMOTE_ADDR"]}', ".
           "body='".clean($body)."'";
    if (!$result = mysql_query($sql)) showerror();



    // update the formatted ttf_post
    $sql = "UPDATE ttf_post SET rev=rev+1, ".
           "body='".clean(outputbody($body))."' WHERE post_id='".clean($post_id)."'";
    if (!$result = mysql_query($sql)) showerror();



    // update the user's last rev date
    $sql = "UPDATE ttf_user                 ".
           "SET rev_date=UNIX_TIMESTAMP()  ".
           "WHERE user_id={$ttf["uid"]}     ";
    if (!$result = mysql_query($sql)) showerror();



    // wow, all of that worked! let's grab the thread_id
    // and redirect the user to their edited post
    $sql = "SELECT thread_id FROM ttf_post ".
           "WHERE post_id='".clean($post_id)."'";
    if (!$result = mysql_query($sql)) showerror();
    list($thread_id) = mysql_fetch_array($result);

    header("Location: thread.php?thread_id=".$thread_id."#".$post_id);

} else if (!isset($_POST["body"])) {

    $title = $label = "editing post $post_id";

    require_once "include_header.php";

?>
            <form action="editpost.php" method="post">
                <div class="contenttitle">you're creating revision <?php echo $num_revs; ?> of post <?php echo $post_id; ?></div>
                <div id="editpost_textarea">
                    <textarea class="editpost" cols="12" rows="20" name="body"><?php echo output($head); ?></textarea>
                </div>
                <div id="editpost_button">
                    <input class="editpost" type="submit" value="submit revision" />
                </div>
                <div>
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
                    <input type="hidden" name="rev_num" value="<?php echo $num_revs; ?>" />
                </div>
            </form>
<?php

} else {
   
    message($ttf_label, $ttf_msg["fatal_error"],
            "you cannot edit a post into inexistence. use the archive feature!");
    die();

};

require_once "include_footer.php";

?>
