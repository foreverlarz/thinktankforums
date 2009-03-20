<?php
/* think tank forums
 *
 * include_config.php
 */

$ttf_dbms = array(
    'host' => 'localhost',
    'user' => 'ttf',
    'pass' => '',
    'db'   => 'ttf'
);

$ttf_cfg = array(
    'forum_name'        => 'think tank forums',
    'forum_shortname'   => 'ttf',
    'address'           => 'www.thinktankforums.com',
    'version'           => '',
    'maintenance'       => FALSE,
    'index_title'       => 'welcome',
    'online_timeout'    => 300,
    'forum_display'     => 15,

    'admin_name'        => 'lucas reddinger',
    'admin_email'       => 'trashbin@ttfproject.com',
    'bot_name'          => 'violet',
    'bot_email'         => 'violet@thinktankforums.com',

    'cookie_name'       => 'thinktank',
    'cookie_time'       => 31556926,
    'cookie_path'       => '/',
    'cookie_domain'     => 'www.thinktankforums.com',
    'cookie_secure'     => FALSE
);

$ttf_msg = array(
    "fatal_error"       => "fatal error",
    "maint_title"       => "maintenance mode",
    "successtitl"       => "success",
    "resultstitl"       => "results",
    "ip_banned"         => "sorry, but your IP is banned.",
    "user_banned"       => "sorry, but your user account is banned.",
    "cookie_inv"        => "sorry, but your cookie is invalid. please try logging out and logging in again.",
    "maint_body"        => "sorry, but the forum is offline for maintenance. we are most likely ".
                           "updating scripts and adding new features. please come back soon.",
    "field_empty"       => "sorry, but you left a field empty.",
    "notloggedin"       => "sorry, but you must be logged in to do this.",
    "thread_dne"        => "sorry, but the thread you specified does not exist.",
    "noitemspec"        => "sorry, but you must specify an item to view.",
    "loggedin"          => "sorry, but you can't do this if you are logged in. your account is working fine!",
    "nomatchuser"       => "sorry, but we couldn't find a matching user.",
    "mailedinfo"        => "we have emailed you the account information. please check your email.",
    "btnpost"           => "click to post",
    "badcredpair"       => "sorry, but the username and password pair that you provided does not match any user record.",
    "passkeydne"        => "sorry, but the passkey that you specified does not exist.",
    "pwdchanged"        => "your password has been changed successfully.",
    "noperm"            => "sorry, but you don't have the proper permission.",
    "noactnspec"        => "sorry, but you must specify an action.",
    "cantmail"          => "sorry, but we couldn't email the information. please contact the forum".
                           "administrator, {$ttf_cfg["admin_name"]}, at {$ttf_cfg["admin_email"]}"
);

?>
