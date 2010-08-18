<?php
/* think tank forums
 *
 * rss.php
 */

require_once "include_common.php";

$sql = "SELECT `ttf_revision`.*, `ttf_user`.`username`          ".
       "FROM `ttf_revision`                                     ".
       "LEFT JOIN `ttf_user`                                    ".
       "  ON `ttf_revision`.`author_id`=`ttf_user`.`user_id`    ".
       "ORDER BY `date` DESC, `rev_id` DESC                     ".
       "LIMIT 100                                               ";

if (!$result = mysql_query($sql)) showerror();

header("Content-Type: application/xml; charset=utf-8");

    echo <<<EOF
<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel> 
        <title>{$ttf_cfg['forum_shortname']} -- latest revisions</title>
        <link>http://{$ttf_cfg['address']}/latest.php</link>
        <description>latest revisions</description>
        <atom:link href="$ttf_protocol://{$ttf_cfg["address"]}/rss.php" rel="self" type="application/rss+xml" />

EOF;

while ($rev = mysql_fetch_array($result)) {

    if ($rev['type'] === 'post') {
        $sql = "SELECT thread_id FROM ttf_post WHERE post_id='{$rev['ref_id']}'";
        if (!$result_a = mysql_query($sql)) showerror();
        list($thread_id) = mysql_fetch_row($result_a);
        $bonus = "<a href=\"http://{$ttf_cfg['address']}/thread.php?thread_id=$thread_id#post-{$rev['ref_id']}\">view this post in the context of the thread</a>";
    } else if ($rev['type'] === 'thread') {
        $bonus = "<a href=\"http://{$ttf_cfg['address']}/thread.php?thread_id={$rev['ref_id']}\">view this thread</a>";
    } else if ($rev['type'] === 'profile') {
        $bonus = "<a href=\"http://{$ttf_cfg['address']}/profile.php?user_id={$rev['ref_id']}\">view this user profile</a>";
    } else if ($rev['type'] === 'title') {
        $bonus = "<a href=\"http://{$ttf_cfg['address']}/profile.php?user_id={$rev['ref_id']}\">view this user title in the context of the profile</a>";
    };

    $title = outputxml("{$rev['type']} {$rev['ref_id']}, rid {$rev['rev_id']} by {$rev['username']}");
    $link = outputxml("http://{$ttf_cfg['address']}/revision.php?type={$rev['type']}&ref_id={$rev['ref_id']}");
    $description = outputxml(nl2br($rev['body']."\n\n<hr />".$bonus));



    echo <<<EOF
        <item>
            <title>{$title}</title>
            <link>{$link}</link>
            <guid isPermaLink="false">{$ttf_cfg['address']}-{$rev['rev_id']}</guid>
            <description>{$description}</description>
        </item>

EOF;

};

?>
    </channel>
</rss>
