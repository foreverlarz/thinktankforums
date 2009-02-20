<?php

require_once "include_common.php";
kill_nonadmin();

//why did you come here?
$action = clean($_REQUEST['action']);
switch($action) {
	case "create":
 		$forum_name = clean($_POST['forum_name']);
		$forum_desc = clean($_POST['forum_desc']);
		create_forum($forum_name, $forum_desc);
		header( "Location: http://".$ttf_cfg['address']."/admin_manageforum.php" ) ;
		break;
	case "moveup":
 		$forum_id = clean($_GET['forum_id']);
		$direction = "up";
		swap_position($forum_id, $direction); 
		header( "Location: http://".$ttf_cfg['address']."/admin_manageforum.php" ) ;
		break;
	case "movedown":
		$forum_id = clean($_GET['forum_id']);
		$direction = "down";
		swap_position($forum_id, $direction); 
		header( "Location: http://".$ttf_cfg['address']."/admin_manageforum.php" ) ;
		break;
	case "edit":
		$forum_id = clean($_POST['forum_id']);
		$forum_name = clean($_POST['forum_name']);
		$forum_desc = clean($_POST['forum_desc']);
		edit_forum($forum_id, $forum_name, $forum_desc);
		header( "Location: http://".$ttf_cfg['address']."/admin_manageforum.php" ) ;
		break;
	case "delete":
		$forum_id = clean($_GET['forum_id']);
		remove_forum($forum_id);
		header( "Location: http://".$ttf_cfg['address']."/admin_manageforum.php" ) ;
		break;
	default:
		//what are you doing here?
		header("Location: http://".$ttf_cfg['address']."/admin_manageforum.php");
		break;
}
//functions supporting the above options
function create_forum($forum_name, $forum_desc) {
	$position = lowest_avail_pos();
	$sql = <<<EOS
		INSERT INTO `ttf_forum`
		(`forum_id`, `name`, `description`, `date`, `position`) 
		VALUES
		(NULL,'{$forum_name}', '{$forum_desc}', UNIX_TIMESTAMP(), '{$position}')
EOS;
	if (!$result = mysql_query($sql)) showerror();
}

function swap_position($forum_id, $direction) {
	$cur_forum['forum_id'] = clean($forum_id);
	//pull 'position' and 'forum_id' into $cur_forum array
	$sql =<<<EOS
		SELECT `forum_id`, `position`
		FROM `ttf_forum`
		WHERE `forum_id` = '{$cur_forum['forum_id']}'
EOS;
	if (!$result = mysql_query($sql)) showerror();
	$cur_forum = mysql_fetch_array($result, MYSQL_ASSOC);
	//finding the forum_id and position directly above/below $cur_forum['position']
	if($direction == "up"){
		$sql =<<<EOS
			SELECT `forum_id`, `position`
			FROM `ttf_forum`
			WHERE `position` =
				(
				SELECT MAX(`position`)
				FROM `ttf_forum`
				WHERE `position` < '{$cur_forum['position']}'
				)
EOS;
	} else {
		$sql =<<<EOS
		SELECT `forum_id`, `position`
		FROM `ttf_forum`
		WHERE `position` =
			(
			SELECT MIN(`position`)
			FROM `ttf_forum`
			WHERE `position` > '{$cur_forum['position']}'
			)
EOS;
	}
	if (!$result = mysql_query($sql)) showerror();
	//pull the position and forum_id of the other forum into an array
	$other_forum = mysql_fetch_array($result, MYSQL_ASSOC);
	//set the position of the current forum first
	$sql =<<<EOS
		UPDATE `ttf_forum`
		SET `position` = '{$other_forum['position']}'
		WHERE `forum_id` = '{$cur_forum['forum_id']}'
EOS;
	if (!$result = mysql_query($sql)) showerror();
	//set the position of the other forum next
	$sql =<<<EOS
		UPDATE `ttf_forum`
		SET `position` = '{$cur_forum['position']}'
		WHERE `forum_id` = '{$other_forum['forum_id']}'
EOS;
	if (!$result = mysql_query($sql)) showerror();
}

function edit_forum($forum_id, $forum_name, $forum_desc) {
	$sql = <<<EOS
		UPDATE `ttf_forum`
		SET `name` = '{$forum_name}',
		`description` = '{$forum_desc}'
		WHERE `forum_id` = '{$forum_id}'
EOS;
	if (!$result = mysql_query($sql)) showerror();
}

function remove_forum($forum_id) {
	//check to make sure that there's no posts or threads within the forum
	$sql =<<<EOS
		SELECT `threads`, `posts`
		FROM `ttf_forum`
		WHERE `forum_id` = '{$forum_id}'
EOS;
	if (!$result = mysql_query($sql)) showerror();
	list($threads, $posts) = mysql_fetch_array($result);
	if($threads != 0 || $posts != 0) {
		//if there are then we stop here
		return("no go, a forum may not be deleted until it is empty of posts and threads");
	} else {
		//otherwise, delete the forum
		$sql =<<<EOS
			DELETE 
			FROM `ttf_forum`
			WHERE `forum_id` = '{$forum_id}'
EOS;
		if (!$result = mysql_query($sql)) showerror();
		return("forum id: {$forum_id} was successfully deleted");
	}
}

function lowest_avail_pos() {
	//finds the lowest available position in the ttf_forum table
	$sql =<<<EOS
		SELECT MAX(`position`) FROM `ttf_forum`
EOS;
	if (!$result = mysql_query($sql)) showerror();
	$array = mysql_fetch_array($result);
	return $array[0] + 1;
}
?>