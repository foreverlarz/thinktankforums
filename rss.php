<?php
/* think tank forums
 *
 * rss.php
 */

require_once "include_common.php";

$channel_titl = $ttf_cfg['forum_shortname']." -- latest revisions";
$channel_link = "http://".$ttf_cfg['address']."/rss.php";

$sql = "SELECT `ttf_revision`.*, `ttf_user`.`username`          ".
       "FROM `ttf_revision`                                     ".
       "LEFT JOIN `ttf_user`                                    ".
       "  ON `ttf_revision`.`author_id`=`ttf_user`.`user_id`    ".
       "ORDER BY `date` DESC                                    ".
       "LIMIT 20                                                ";

header("Content-Type: application/xml; charset=utf-8");

?>
<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0"> 
    <channel> 
        <title><?php echo $channel_titl; ?></title>
        <link><?php echo $channel_link; ?></link>
<?php

if (!$result = mysql_query($sql)) showerror();

while ($rev = mysql_fetch_array($result)) {

    switch ($rev["type"]) {
    case 'post':
        $title = "post ".$rev["ref_id"];
        $type = "post";
        break;
    case 'profile':
        $title = "user profile ".$rev["ref_id"];
        $type = "profile";
        break;
    case 'title':
        $title = "user title ".$rev["ref_id"];
        $type = "title";
        break;
    };

    $title .=  ", rid {$rev["rev_id"]} by ".output($rev["username"]);

    echo <<<EOF
        <item>
            <title>{$title}</title>
            <link>http://{$ttf_cfg['address']}/revision.php?type={$type}&amp;ref_id={$rev['ref_id']}</link>
            <description>{$rev["body"]}</description>
        </item>

EOF;

};

?>
    </channel>
</rss>
