<?php
/* think tank forums
 *
 * newthread.php
 */

require "include_common.php";

$forum_id = clean($_POST["forum_id"]);
$title = clean($_POST["title"]);

// if the agent is logged in as a valid user
if (isset($ttf["uid"])) {

    // if both fields aren't blank
    if (!empty($forum_id) && !empty($title)) {

        // grab the name of the specified forum
        $sql = "SELECT name FROM ttf_forum WHERE forum_id='$forum_id'";
        if (!$result = mysql_query($sql)) showerror();
        list($forum_name) = mysql_fetch_array($result);
        mysql_free_result($result);

        // if a valid forum_id was supplied
        if (isset($forum_name)) {

            // insert the new thread into ttf_thread
            $sql = "INSERT INTO ttf_thread SET forum_id='$forum_id', ".
                   "author_id='{$ttf["uid"]}', date=UNIX_TIMESTAMP(), title='$title'";
            if (!$result = mysql_query($sql)) showerror();
            $thread_id = mysql_insert_id();
                
            // update the date, thread count, and post count of the forum
            $sql = "UPDATE ttf_forum SET threads=threads+1 WHERE forum_id='$forum_id'";
            if (!$result = mysql_query($sql)) showerror();

            // mark the new thread as read for the author
            $sql = "REPLACE INTO ttf_thread_new SET thread_id='$thread_id', ".
                   "user_id='{$ttf["uid"]}', last_view=UNIX_TIMESTAMP()";
            if (!$result = mysql_query($sql)) showerror();

            // redirect to the new thread
            header("Location: thread.php?thread_id=".$thread_id);
            
        } else {

            message("create a new thread", "fatal error", "you must specify a valid forum.", 1, 1);
            
        };
    
    } else {

        message("create a new thread", "fatal error", "you must enter a thread title.", 1, 1);

    };

} else {
    
    message("create a new thread", "fatal error", "you must login before you may create a new thread.", 1, 1);

};

?>
