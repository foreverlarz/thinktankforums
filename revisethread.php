<?php
/* think tank forums
 *
 * revisethread.php
 */

$ttf_title = $ttf_label = "revise a thread";

require_once "include_common.php";

// people must be logged in to use this script
kill_guests();


// pull through the variables
// note: we don't clean these here,
// because we will want to
// use $body in its raw-input form
$thread_id = $_REQUEST["thread_id"];
$body = $_POST["body"];
$rev_num = $_POST["rev_num"];


// if a post is not specified, kill agent
if (empty($thread_id)) {

    message($ttf_label, $ttf_msg["fatal_error"],
            "you must specify a thread to revise.");
    die();

};


// let's check some permissions (must be either admin or author)
if ($ttf["perm"] != 'admin') {

    $sql = "SELECT author_id FROM ttf_thread        ".
           "WHERE thread_id='".clean($thread_id)."' ";
    if (!$result = mysql_query($sql)) showerror();
    list($author_id) = mysql_fetch_array($result);

    if ($ttf["uid"] != $author_id) {

        message($ttf_label, $ttf_msg["fatal_error"],
                "you do not have permission to revise this thread.");
        die();

    };

};


// get the HEAD of the thread and the number of revisions
$sql = "SELECT SQL_CALC_FOUND_ROWS              ".
       "       body                             ".
       "FROM ttf_revision                       ".
       "WHERE ref_id='".clean($thread_id)."'    ".
       "   && type='thread'                     ".
       "ORDER BY date DESC LIMIT 1              ";

if (!$result = mysql_query($sql)) showerror();
list($head) = mysql_fetch_array($result);

$sql = "SELECT FOUND_ROWS()";
if (!$result = mysql_query($sql)) showerror();
list($num_revs) = mysql_fetch_array($result);


if (empty($num_revs)) {

    message($ttf_label, $ttf_msg["fatal_error"],
            "the specified thread could not be retrieved.");
    die();

};


if (isset($_POST["thread_id"])) {

    if ($rev_num != $num_revs) {

        message($ttf_label, $ttf_msg["fatal_error"],
            "while you were editing the thread, someone else committed a revision. ".
            "please go back, save your changes, reload the edit page, then merge ".
            "in your changes with the current version by hand.");
        die();

    };


    if (strcmp($body, $head) !== 0) {

        if (strcmp(trim($body), "") === 0) {

            message($ttf_label, $ttf_msg["fatal_error"],
                    "a thread title cannot be an empty string or whitespace.");
            die();

        };

        // insert the new revision
        $sql = "INSERT INTO ttf_revision            ".
               "SET ref_id='".clean($thread_id)."', ".
               "    type='thread',                  ".
               "    author_id='{$ttf["uid"]}',      ".
               "    date=UNIX_TIMESTAMP(),          ".
               "    ip='{$_SERVER["REMOTE_ADDR"]}', ".
               "    body='".clean($body)."'         ";
        if (!$result = mysql_query($sql)) showerror();

        // update the formatted ttf_thread
        $sql = "UPDATE ttf_thread                       ".
               "SET rev=rev+1,                          ".
               "    body='".clean(output($body))."'     ".
               "WHERE thread_id='".clean($thread_id)."' ";
        if (!$result = mysql_query($sql)) showerror();

    } else {

        message($ttf_label, $ttf_msg["fatal_error"], "you didn't make any changes.");
        die();

    };


    // update the user's last rev date
    $sql = "UPDATE ttf_user                 ".
           "SET rev_date=UNIX_TIMESTAMP()  ".
           "WHERE user_id={$ttf["uid"]}     ";
    if (!$result = mysql_query($sql)) showerror();


    // wow, all of that worked!
    // let's redirect the user to their edited thread
    header("Location: $ttf_protocol://{$ttf_cfg["address"]}/thread.php?thread_id=".$thread_id);


} else if (isset($_GET["thread_id"])) {


    $ttf_title = $ttf_label = "creating revision $num_revs of thread $thread_id";

    require_once "include_header.php";

?>
            <form action="revisethread.php" method="post">
                <div class="contenttitle">edit the title of the thread</div>
                <div id="editthread_textarea">
                    <textarea class="editthread" cols="72" rows="20" name="body"><?php echo output($head); ?></textarea>
                    </div>
<?php

    echo <<<EOF
                <div id="editthread_button">
                    <input class="editthread" type="submit" value="submit revision" />
                </div>
                <div>
                    <input type="hidden" name="thread_id" value="{$thread_id}" />
                    <input type="hidden" name="rev_num" value="{$num_revs}" />
                </div>
            </form>

EOF;

};

require_once "include_footer.php";

?>
