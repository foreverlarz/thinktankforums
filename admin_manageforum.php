<?php
require_once "include_common.php";
$ttf_title = $ttf_label = "administration &raquo; manage forums";
require_once "include_header.php";
kill_nonadmin();

echo <<<EOF

	<table cellspacing="1" class="content">
		<colgroup>
			<col id="forum" />
			<col id="threads" />
			<col id="posts" />
		</colgroup>
		<thead>
			<tr>
				<th>forum</th>
				<th>threads</th>
				<th>posts</th>
			</tr>
		</thead>
		<tbody>

EOF;
		//find the first and last forum
		$sql =<<<EOS
			SELECT MAX(`position`), MIN(`position`)
			FROM `ttf_forum`
EOS;
		if (!$result = mysql_query($sql)) showerror();
		list($last_forum, $first_forum) = mysql_fetch_array($result);
		//query all info on forums
		$sql =<<<EOS
			SELECT *
			FROM `ttf_forum`
			ORDER BY `position`
EOS;
		if (!$result = mysql_query($sql)) showerror();
		while($cur_forum = mysql_fetch_array($result)) {
			extract($cur_forum);
			echo <<<EOF

			<tr>
				<td>
					<span class="icon">
EOF;
			// move forum up
			echo ($position != $first_forum) ? "<a href=\"forumtools.php?action=moveup&forum_id={$forum_id}\" title=\"move up one spot\"><img src=\"img/arrow_up.png\"></a>" : "&nbsp;&nbsp;" ;
			//move forum down
			echo ($position != $last_forum) ? "<a href=\"forumtools.php?action=movedown&forum_id={$forum_id}\" title=\"move down one spot\"><img src=\"img/arrow_down.png\"></a>" : "&nbsp;&nbsp;" ;
			//edit forum
			echo "<a href=\"admin_manageforum.php?action=edit&forum_id={$forum_id}\" title=\"edit\"><img src=\"img/pencil.png\"></a>";
			//if there's no threads or posts, delete forum
			echo ($threads == 0 && $posts == 0) ? "<a href=\"forumtools.php?action=delete&forum_id={$forum_id}\" title=\"delete\"><img src=\"img/delete.png\"></a></span>" : "&nbsp;&nbsp;" ;
			echo <<<EOF
			{$name}
					<span class="small"><br>&nbsp;&nbsp;&middot;&nbsp;{$description}</span>
				</td>
				<td>{$threads}</td>
				<td>{$posts}</td>
			</tr>			
EOF;
		};
		
echo <<<EOF
		</tbody>
		</table>
		<form action="forumtools.php" method="post">
		<table cellspacing="1" class="content">
		<thead>
			<tr><th colspan=2>create/modify forum</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>name:</td>
				<td><input type="text" name="forum_name" maxlength="40" size="40" 
EOF;
// if we're editing a forum we're going to fill in some values
if ($_GET['action'] == "edit") {
	$forum_id = clean($_GET['forum_id']);
	$sql = <<<EOS
		SELECT `name`, `description`
		FROM `ttf_forum`
		WHERE `forum_id` = '{$forum_id}'
EOS;
	if (!$result = mysql_query($sql)) showerror();
	$forum = mysql_fetch_array($result);
	extract($forum);
	echo <<<EOF
			value="{$name}"/></td>
			</tr>
			<tr>
				<td>description:</td>
				<td><textarea class="forum_desc" name="forum_desc" rows="3" cols="10">{$description}
EOF;
} else {
	echo <<<EOF
			/></td>
			</tr>
			<tr>
				<td valign="top">description:</td>
				<td><textarea class="forum_desc" name="forum_desc" rows="3" cols="10">
EOF;
}
echo <<<EOF
</textarea>
				</td>
			</tr>
		</tbody>
		</table>
		<div id="editforum_button">
			<input class="editpost" type="submit" value="apply changes" />
		</div>
		<input type="hidden" name="action" value="
EOF;
		echo (empty($_GET['action'])) ? "create" : $_GET['action'] ;
		echo "\"/>";
		echo (empty($_GET['forum_id'])) ? "" : "<input type=\"hidden\" name=\"forum_id\" value=\"{$_GET[forum_id]}\" />" ;
		echo "</form>";
require_once "include_footer.php";
?>