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
<rss version="2.0"> 
    <channel> 
        <title>{$ttf_cfg['forum_shortname']} -- latest revisions</title>
        <link>http://{$ttf_cfg['address']}/latest.php</link>
        <description>latest revisions</description>

EOF;

while ($rev = mysql_fetch_array($result)) {

    $title = outputxml("{$rev['type']} {$rev['ref_id']}, rid {$rev['rev_id']} by {$rev['username']}");
    $link = outputxml("http://{$ttf_cfg['address']}/revision.php?type={$rev['type']}&ref_id={$rev['ref_id']}");
    $description = outputxml(nl2br($rev['body']));

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
