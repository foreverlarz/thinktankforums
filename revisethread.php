<?php
/* think tank forums
 *
 * revisethread.php
 */

$ttf_title = $ttf_label = "revise a thread";

require_once "include_common.php";

// only admins can revise threads
kill_nonadmin();

// pull through the variables
// we only need to clean $thread_id because it's the only one included in the query
$thread_id = clean($_REQUEST["thread_id"]);
$title = clean($_POST["title"]);
$rev_num = clean($_POST["rev_num"]);

// if a thread is not specified, kill agent
if (empty($thread_id)) {

    message($ttf_label, $ttf_msg["fatal_error"],
            "you must specify a thread to revise.");
    die();

};

// get the HEAD of the thread and the number of revisions
$sql = "SELECT SQL_CALC_FOUND_ROWS          ".
       "       body                         ".
       "FROM ttf_revision                   ".
       "WHERE ref_id='$thread_id'           ".
       "   && type='thread'                 ".
       "ORDER BY date DESC LIMIT 1          ";

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

	// rename a thread, uses revisions
		$thread_id = clean($_POST['thread_id']);
		$title = clean($_POST['title']);
		
		//get current thread info
		$sql=<<<EOS
			SELECT `forum_id`, `rev`
			FROM `ttf_thread`
			WHERE `thread_id` = '{$thread_id}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
		$thread_data = mysql_fetch_array($result);
		
		//add updated info into a new revision
		$sql =<<<EOS
			INSERT INTO ttf_revision
			SET ref_id = '{$thread_id}',
					type = 'thread',
					author_id = '{$ttf["uid"]}',
					date = UNIX_TIMESTAMP(),
					ip = '{$_SERVER["REMOTE_ADDR"]}',
					body = '{$title}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
 
		// update ttf_thread
		$sql =<<<EOS
			UPDATE ttf_thread
			SET rev = rev+1,
					title = '{$title}'
			WHERE thread_id='{$thread_id}'
EOS;
		if (!$result = mysql_query($sql)) showerror();

		header("Location: $ttf_protocol://{$ttf_cfg["address"]}/thread.php?thread_id=".$thread_id);
		
} else if (isset($_GET["thread_id"])) {


    $ttf_title = $ttf_label = "creating revision $num_revs of thread $thread_id";

    require_once "include_header.php";

?>
            <form action="revisethread.php" method="post">
                <div class="contenttitle">edit the title of the thread</div>
                <div id="editpost_textarea">
                    <textarea class="editpost" cols="72" rows="20" name="title"><?php echo output($head); ?></textarea>
                    </div>

                <div id="editpost_button">
                    <input class="editpost" type="submit" value="submit revision" />
                </div>
                <div>
                    <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>" />
                    <input type="hidden" name="rev_num" value="<?php echo $num_revs; ?>" />
                </div>
            </form>
<?php

};

require_once "include_footer.php";

?>