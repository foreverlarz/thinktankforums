<?php
/* think tank forums
 *
 * include_header.php
 */

header('Content-Type: text/html; charset=utf-8');

if (empty($ttf_title)) {
    $ttf_htmltitle = $ttf_cfg["forum_name"];
} else {
    $ttf_htmltitle = $ttf_cfg["forum_name"]." &raquo; ".$ttf_title;
};

echo <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>{$ttf_htmltitle}</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
    </head>
    <body>
        <h1><a href="./">{$ttf_cfg["forum_name"]}</a></h1>
        <h2>{$ttf_label}</h2>
        <div id="enclosure">
            <div class="menu_title">

EOF;

if (isset($ttf["uid"])) {

    if (isset($ttf["avatar_type"])) {
        echo '                <img src="avatars/'.$ttf["uid"].'.'.$ttf["avatar_type"].'" alt="your avatar" width="30" height="30" class="avatar" />'."\n";
    };

    echo <<<EOF
                hi, {$ttf["username"]}!
            </div>
            <div class="menu_body">
                &middot; <a href="search.php">search</a><br />
                &middot; <a href="editprofile.php">edit your profile</a><br />

EOF;

    if ($ttf["perm"] == 'admin') {
        echo '                &middot; <a href="admin_userlist.php">user list</a><br />'."\n";
    };

    echo '                &middot; <a href="logout.php">log out</a>'."\n";

} else {

    $force_https = $ttf_cfg["cookie_secure"] ? 'https://'.$ttf_cfg["address"].'/' : '';

    echo <<<EOF
                log in to {$ttf_cfg["forum_shortname"]}
            </div>
            <div class="menu_body">
                <form action="{$force_https}login.php" method="post">
                    <div>
                        <input type="text" name="username" maxlength="16" size="16" /><br />
                        <input type="password" name="password" maxlength="32" size="16" /><br />
                        <input type="submit" value="let's go!" />
                    </div>
                </form>
            </div>
            <div class="menu_title">
                can't log in?
            </div>
            <div class="menu_body">
                &middot; <a href="register.php">register an account</a><br />
                &middot; <a href="recover.php">recover your account</a><br />
                &middot; <a href="search.php">search the forums</a>

EOF;

};

?>
            </div>
