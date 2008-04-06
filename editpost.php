<?php
/* think tank forums
 *
 * editpost.php
 */

$ttf_label = "edit a post";
$ttf_title = $ttf_label;

require_once "include_common.php";

// pull through the variables
// note: we don't clean these, because we will want to
// use $body in its raw-input form
$post_id = $_REQUEST["post_id"];
$body = $_POST["body"];

// if the agent is not logged in as a valid user
if (!isset($ttf["uid"])) {

    message($ttf_label, $error_die_text,
            "you must be logged in to edit a post.");
    die();

};

// if a post_id is not specified
if (empty($post_id)) {

    message($ttf_label, $error_die_text,
            "you must specify a post to edit.");
    die();

};

// let's check some permissions (either admin or author)
if ($ttf["perm"] != "admin") {
    
    $sql = "SELECT author_id FROM ttf_post ".
           "WHERE post_id='".clean($post_id)."'";
    if (!$result = mysql_query($sql)) showerror();
    list($author_id) = mysql_fetch_array($result);

    if ($ttf["uid"] != $author_id) {
       
        message($ttf_label, $error_die_text,
                "you do not have permission to edit this post.");
        die();

    };

};


if (!empty($body)) {
    
    // let's get our current HEAD revision

    $sql = "SELECT body FROM ttf_revision ".
           "WHERE ref_id='".clean($post_id)."' && type='post' ".
           "ORDER BY date DESC LIMIT 1";
    if (!$result = mysql_query($sql)) showerror();
    list($head) = mysql_fetch_array($result);

    if (empty($head)) {

        message($ttf_label, $error_die_text,
                "serious error encountered. please contact an admin.");
        die();
    
    };

    if ($body == $head) {

        message($ttf_label, $error_die_text,
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
    
    // wow, all of that worked! let's grab the thread_id
    // and redirect the user to their edited post
    $sql = "SELECT thread_id FROM ttf_post ".
           "WHERE post_id='".clean($post_id)."'";
    if (!$result = mysql_query($sql)) showerror();
    list($thread_id) = mysql_fetch_array($result);
    
    header("Location: thread.php?thread_id=".$thread_id."#".$post_id);

} else if (!isset($_POST["body"])) {
    
    $title = "editing post_id $post_id";
    $label = $title;
    require_once "include_header.php";
    
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

    // the following html kinda needs some work,
    // but i'm lazy about that right now.. i just
    // want to get this feature working perfectly! --jlr
?>
            <form action="editpost.php" method="post">
                <div class="contenttitle">you're creating revision <?php echo $num_revs; ?> of post <?php echo $post_id; ?></div>

                <div id="editpost_textarea">
                    <textarea class="editpost" rows="20" name="body" wrap="virtual"><?php echo output($head); ?></textarea>
                </div>
                <div id="editpost_button">
                    <input class="editpost" type="submit" value="submit revision" />
                </div>
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
            </form>
<?php

} else {
   
    message($ttf_label, $error_die_text,
            "you cannot edit a post into inexistence. use the archive feature!");
    die();

};

?>
