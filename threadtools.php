<?php
require_once "include_common.php";
kill_nonadmin();

//why did you come here?
$action = clean($_REQUEST['action']);
switch($action) {
	case "rename":
	// rename a thread, uses revisions
		$thread_id = clean($_GET['thread_id']);
		$new_name = clean($_GET['name']);
		
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
					body = '{$new_name}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
        
		// update ttf_thread
		$sql =<<<EOS
			UPDATE ttf_thread
			SET rev = rev+1,
					title = '{$new_name}'
			WHERE thread_id='{$thread_id}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
		header("Location: $ttf_protocol://".$ttf_cfg['address']."/admin_managethread.php?forum_id=".$thread_data['forum_id']);
		break;
	case "archive":
	//archive given thread and all posts associated with it
		$thread_id = clean($_GET['thread_id']);
		
		//what forum do we need to go back to?
		$sql =<<<EOS
			SELECT `forum_id`
			FROM `ttf_thread`
			WHERE `thread_id` = '{$thread_id}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
		$thread_data = mysql_fetch_array($result);
/* 	we don't need to archive posts since the thread won't be visible, this also simplifies restoration!	
		//find all posts associated with current thread that aren't already archived
		$thread_sql =<<<EOS
			SELECT `post_id`, `rev`
			FROM `ttf_post`
			WHERE `thread_id` = '{$thread_id}'
				 && `body` IS NOT NULL
EOS;
		// heredoc's won't run functions so just fill the body now
		$archived_body = clean(outputbody(NULL));
		if (!$thread_result = mysql_query($thread_sql)) showerror();
		while($cur_post = mysql_fetch_array($thread_result)){
				// for each one, archive the post
				// insert the new revision
        $sql =<<<EOS
					INSERT INTO ttf_revision
          SET ref_id = '{$cur_post['post_id']}',
              type = 'post',
              author_id = '{$ttf["uid"]}',
              date = UNIX_TIMESTAMP(),
              ip = '{$_SERVER["REMOTE_ADDR"]}',
              body = NULL
EOS;

        if (!$result = mysql_query($sql)) showerror();
        
				// update the formatted ttf_post
        $sql =<<<EOS
					UPDATE ttf_post
          SET rev = rev+1,
              body = '{$archived_body}'
          WHERE post_id='{$cur_post['post_id']}'
EOS;
				if (!$result = mysql_query($sql)) showerror();
		} */
		
		// now that all posts are archived, archive the thread
		
/* 		// create new revision - is this necessary, seems like an `archived` flag would be faster and cleaner
//however we lose accounting on threads, this should be admin only and trackable by posts but who knows?
		$sql =<<<EOS
			INSERT INTO `ttf_revision`
			SET ref_id = '{$thread_id}',
          type = 'thread',
          author_id = '{$ttf["uid"]}',
          date = UNIX_TIMESTAMP(),
          ip = '{$_SERVER["REMOTE_ADDR"]}',
          body = NULL
EOS;
		if (!$result = mysql_query($sql)) showerror(); */
		
		//add the date to `archive` designating the thread archived
		$sql =<<<EOS
			UPDATE `ttf_thread`
			SET `archive` = UNIX_TIMESTAMP()
			WHERE `thread_id` = '{$thread_id}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
		
		//go back to the management page
		header("Location: $ttf_protocol://".$ttf_cfg['address']."/admin_managethread.php?forum_id=".$thread_data['forum_id']);
		break;
	case "move":
		//move the thread to another forum
		$thread_id = clean($_GET['thread_id']);
		$forum_id = clean($_GET['forum_id']);
		$sql =<<<EOS
			UPDATE `ttf_thread`
			SET `forum_id` = '{$forum_id}'
			WHERE `thread_id` = '{$thread_id}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
		header("Location: $ttf_protocol://".$ttf_cfg['address']."/admin_managethread.php?forum_id=".$forum_id);
		break;
	case "restore":
	//restore the thread from the archives
		$thread_id = clean($_GET['thread_id']);
		
		//what forum do we need to go back to?
		$sql =<<<EOS
			SELECT `forum_id`
			FROM `ttf_thread`
			WHERE `thread_id` = '{$thread_id}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
		$thread_data = mysql_fetch_array($result);
		
		//remove the archive date
		$sql =<<<EOS
			UPDATE `ttf_thread`
			SET `archive` = NULL
			WHERE `thread_id` = '{$thread_id}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
		header("Location: $ttf_protocol://".$ttf_cfg['address']."/admin_managethread.php?forum_id=".$thread_data['forum_id']);
		break;
	default:
		//what are you doing here?
		header("Location: $ttf_protocol://".$ttf_cfg['address']."/admin_managethread.php");
		break;
}

?>