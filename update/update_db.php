<?php
//update an existing ttf install to be compatible with an admintools merged install


if(!isset($_POST['submit'])) {
	echo <<<EOF
		enter root credentials for the database containing your ttf install<br>
		this script will update your tables to be compatible with the admin tools<br>
		branch of ttf. remember, ttf should be in maintence mode for this install<br>
		<table>
			<form action="update_db.php" method="post">
			<tr>
				<td>
					hostname:
				</td>
				<td>
					<input type="text" name="host">
				</td>
			</tr>
			<tr>
				<td>
					username:
				</td>
				<td>
					<input type="text" name="user">
				</td>
			</tr>
			<tr>
				<td>
					password:
				</td>
				<td>	
					<input type="text" name="pass">
				</td>
			</tr>
			<tr>
				<td>
					database:
				</td>
				<td>	
					<input type="text" name="db">
				</td>
			</tr>
			<tr>
				<td>	
					<input type="reset" name="reset" value="reset">
				</td>
				<td>	
					<input type="submit" name="submit" value="submit">
				</td>
			</tr>
			</form>
		</table>
EOF;
} else {
//needs root privs in the db, these should be queried and not cached!
$update_db = array(
	'host'	=> "{$_POST['host']}",
	'user'	=> "{$_POST['user']}",
	'pass'	=> "{$_POST['pass']}",
	'db'		=> "{$_POST['db']}");

include_once('include_update_db.php');

//require maintence mode, you don't want anybody doing anything while this runs
//unfortunately there's no good way to check that

//all output should be dumped into a table to display progress!
//maybe include hidden rows to show details of thread updates and forum updates on an individual basis

//begin status table
echo "<table>";

//update ttf_forum for position field
$sql =<<<EOS
	ALTER TABLE `ttf_forum`
	ADD `position` tinyint(4) NOT NULL
EOS;
if (!$result = mysql_query($sql)) showerror();
echo <<<EOF
	<tr>
		<td>
			position field added to ttf_forum
		</td>
		<td>
			&#10003
		</td>
	</tr>
EOF;

//add positions to forums, we'll just use forum_ids for now
$sql =<<<EOS
	SELECT `forum_id`, `name`
	FROM `ttf_forum`
EOS;
if (!$result = mysql_query($sql)) showerror();
while($forum_data = mysql_fetch_array($result)) {
	$pos_sql =<<<EOS
		UPDATE `ttf_forum`
		SET `position` = '{$forum_data['forum_id']}'
		WHERE `forum_id` = '{$forum_data['forum_id']}'
EOS;
	if (!$pos_result = mysql_query($pos_sql)) showerror();
	echo <<<EOF
	<tr>
		<td>
			&nbsp;&middot;&nbsp;forum id: {$forum_data['forum_id']} / {$forum_data['name']} - updated
		</td>
		<td>
			&#10003
		</td>
	</tr>
EOF;
}
echo <<<EOF
<tr>
	<td>
		position field populated with forum_ids
	</td>
	<td>
		&#10003
	</td>
</tr>
EOF;

//add archive field to ttf_threads
$sql =<<<EOS
	ALTER TABLE `ttf_thread`
	ADD	`archive` int(11) default NULL
EOS;
if (!$result = mysql_query($sql)) showerror();
echo <<<EOF
<tr>
	<td>
		archive field added to ttf_thread
	</td>
	<td>
		&#10003
	</td>
</tr>
EOF;

//add the revision field to threads, we can't set a default or not null yet
$sql =<<<EOS
	ALTER TABLE `ttf_thread`
	ADD	`rev` smallint(6)
EOS;
if (!$result = mysql_query($sql)) showerror();
echo <<<EOF
<tr>
	<td>
		rev field added to ttf_thread
	</td>
	<td>
		&#10003
	</td>
</tr>
EOF;

//add the thread type to ttf_revision.type
$sql =<<<EOS
	ALTER TABLE `ttf_revision`
	CHANGE `type`
	`type` ENUM( 'post', 'profile', 'title', 'thread' ) 
	NOT NULL
EOS;
if (!$result = mysql_query($sql)) showerror();
echo <<<EOF
<tr>
	<td>
		thread option added to ttf_revision.type
	</td>
	<td>
		&#10003
	</td>
</tr>
EOF;

//find all threads without revisions
$sql =<<<EOS
	SELECT `thread_id`, `title` 
	FROM `ttf_thread`
	WHERE `rev` IS NULL
EOS;
if (!$result = mysql_query($sql)) showerror();
while($thread_data = mysql_fetch_array($result)) {
	//create the revision for this thread
	//the title needs to be escaped, maybe
	$thread_data['title'] = mysql_real_escape_string($thread_data['title']);
	$rev_sql =<<<EOS
		INSERT
		INTO `ttf_revision`
		SET `ref_id`		= '{$thread_data['thread_id']}',
				`type`			= 'thread',
				`author_id`	= '{$ttf['uid']}',
				`date`			= UNIX_TIMESTAMP(),
				`ip`				= '{$_SERVER['REMOTE_ADDR']}',
				`body`			= '{$thread_data['title']}'
EOS;
	if (!$rev_result = mysql_query($rev_sql)) showerror();
	//set the revision to 0 for this thread
	$thread_sql =<<<EOS
		UPDATE `ttf_thread`
		SET `rev` = '0'
		WHERE `thread_id` = '{$thread_data['thread_id']}'
EOS;
	if (!$thread_result = mysql_query($thread_sql)) showerror();
	$i++;

	echo <<<EOF
		<tr>
			<td>
				&nbsp;&middot;&nbsp;thread id: {$thread_data['thread_id']} / {$thread_data['title']} - updated
			</td>
			<td>
				&#10003
			</td>
		</tr>
EOF;
}

//add the revision field to threads, we can't set a default or not null yet
$sql =<<<EOS
	ALTER TABLE `ttf_thread`
	CHANGE `rev`
	`rev` smallint(6) NOT NULL DEFAULT '0'
EOS;
if (!$result = mysql_query($sql)) showerror();
echo <<<EOF
	<tr>
		<td>
			updated ttf_thread.rev to proper settings.
		</td>
		<td>
			&#10003
		</td>
	</tr>
EOF;

echo "</table><br><br>";
echo "operation complete!<br>";
echo "now would be a good time to dig through your db and verify data and ".
		 "structure against _schema.sql. if it all checks out, you're good to ".
		 "begin using the new admin tools!";
}
?>