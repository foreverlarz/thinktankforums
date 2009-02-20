<?php
require_once "include_common.php";
$ttf_title = $ttf_label = "administration &raquo; manage threads";
require_once "include_header.php";
kill_nonadmin();
if(isset($_GET['forum_id'])) $forum_id = clean($_GET['forum_id']);
//form to discover what forum they want to look at
	echo <<<EOF
		select forum: 
		<form action="admin_managethread.php" method="get">
			<select name="forum_id" onchange="this.form.submit();">
				<option value=""> </option>
EOF;
	//query info on all forums
	$sql =<<<EOS
		SELECT `forum_id`, `name`
		FROM `ttf_forum`
		ORDER BY `position`			
EOS;
	if (!$result = mysql_query($sql)) showerror();
	//display them as options
	while($cur_forum = mysql_fetch_array($result)) {
		echo "<option value=\"{$cur_forum['forum_id']}\" ";
		//maintain selected state after choosing a forum
		echo ($_GET['forum_id'] == $cur_forum['forum_id']) ? "selected" : "";
		echo ">{$cur_forum['name']}</option>";
		}
	echo <<<EOF
			</select>
		</form>
EOF;

//the threads are displayed here
	echo <<<EOF
	<table cellspacing="1" class="content">
		<colgroup>
			<col id="forum" />
			<col id="posts" />
		</colgroup>
		<thead>
			<tr>
				<th>thread</th>
				<th>posts</th>
			</tr>
		</thead>
		<tbody>
		
EOF;
	if (empty($offset)) $offset = 0;
	$sql =<<<EOS
		SELECT *
		FROM `ttf_thread`
		WHERE `forum_id` = '{$forum_id}'
		LIMIT {$offset}, {$ttf_cfg["forum_display"]}
EOS;
	if (!$result = mysql_query($sql)) showerror();
	while($cur_thread = mysql_fetch_array($result)) {
		echo <<<EOF
		<tr>
			<td>
				<span class="icon">
					<a href="threadtools.php?action=sticky&thread_id={$cur_thread['thread_id']}" title="stickify!">
						<img src="img/star.png">
					</a>
					<a onclick="javascript:displayRow(document.getElementById('move{$cur_thread['thread_id']}'))" title="move to another forum">
						&#10021;
					</a>
					<a onclick="javascript:displayRow(document.getElementById('edit{$cur_thread['thread_id']}'))" title="rename">
						<img src="img/pencil.png">
					</a>
EOF;
					if(isset($cur_thread['archive'])){
						echo <<<EOF
						<a href="threadtools.php?action=restore&thread_id={$cur_thread['thread_id']}" title="restore">
							<img src="img/accept.png">
						</a>
EOF;
					} else {
						echo <<<EOF
						<a href="threadtools.php?action=archive&thread_id={$cur_thread['thread_id']}" title="archive">
							<img src="img/delete.png">
						</a>
EOF;
					}
					echo "&nbsp;&nbsp;";
EOF;
		if (isset($cur_thread['archive'])) {
			//set color to red if thread is archived
			echo <<<EOF
				<span style="color: #ff0000" title="thread id: {$cur_thread['thread_id']}">
					{$cur_thread['title']}
				</span>
EOF;
		} elseif ($cur_thread['sticky'] == "true") {
			//set color to green if thread is sticky
			echo <<<EOF
				<span style="color: #55AE3A" title="thread id: {$cur_thread['thread_id']}">
					{$cur_thread['title']}
				</span>
EOF;
		} else {
			//nothing special about this thread
			echo <<<EOF
				<span title="thread id: {$cur_thread['thread_id']}">
					{$cur_thread['title']}
				</span>
EOF;
			//if there are any revisions
			echo ($cur_thread['rev'] > 0) ? "<i><a href=\"revision.php?ref_id={$cur_thread['thread_id']}&type=thread\">{$cur_thread['rev']} rev</a></i>" : "";
		}
		echo <<<EOF
			</td>
			<td>
				{$cur_thread['posts']}
			</td>
		</tr>
		</span>
EOF;
		//rename form
	echo <<<EOF
		<tr id="edit{$cur_thread['thread_id']}" style="display:none">
			<td>
				<form action="threadtools.php" method="get">
				name:<input type="text" name="name">
				<input type="hidden" name="action" value="rename">
				<input type="hidden" name="thread_id" value="{$cur_thread['thread_id']}">
				<input type="hidden" name="forum_id" value="{$cur_thread['forum_id']}">
				<input type="submit" value="submit">
				</form>
			</td>
			<td></td>
		</tr>
EOF;

//move form
	echo <<<EOF
		<tr id="move{$cur_thread['thread_id']}" style="display:none">
		<td>
		<form action="threadtools.php" method="get">
			<input type="hidden" name="action" value="move">
			<input type="hidden" name="thread_id" value="{$cur_thread['thread_id']}">
			<select name="forum_id" onchange="this.form.submit();">
				<option value=""> </option>
EOF;
	//query info on all forums
	$move_sql =<<<EOS
		SELECT `forum_id`, `name`
		FROM `ttf_forum`
		ORDER BY `position`			
EOS;
	if (!$move_result = mysql_query($move_sql)) showerror();
	//display them as options
	while($cur_forum = mysql_fetch_array($move_result)) {
		echo "<option value=\"{$cur_forum['forum_id']}\">{$cur_forum['name']}</option>";
		}
	echo <<<EOF
			</select>
		</form>
		</td>
		<td></td>
		</tr>
EOF;
	}
	echo <<<EOF
		</tbody>
		</table>
EOF;
/* if ($numrows > ($ttf_cfg["forum_display"] + $offset)) {
        
    $next = $offset + $ttf_cfg["forum_display"];
    $left = min($numrows - $offset - $ttf_cfg["forum_display"], $ttf_cfg["forum_display"]);

?>
            <div class="sidebox">
                <strong><a href="forum.php?forum_id=<?php echo $forum_id; ?>&amp;offset=<?php echo $next; ?>">next <?php echo $left; ?> threads</a></strong><br /><span class="small"><?php echo $numrows; ?> total</span>
            </div>
<?php

};
EOF; */
require_once "include_footer.php";
?>