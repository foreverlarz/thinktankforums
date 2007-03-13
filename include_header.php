<?php
/* think tank forums
 *
 * header.inc.php
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>think tank forums <?php echo $ttf_config["version"]; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" type="text/css" href="style.css" />
        <!--[if lt IE 7]>
        <link rel="stylesheet" type="text/css" href="style_ie.css" />
        <![endif]-->
        <script type="text/javascript" src="script_confirm.js"></script>
    </head>
    <body>
        <div id="header">
            <a href="/"><img src="images/header.gif" width="600" height="46" border="0" alt="think tank forums" /></a>
        </div>
        <div id="title"><?php echo $label; ?></div>
        <div id="enclosure">
            <div class="menu_title">
<?php
if (isset($ttf["uid"])) {
    if (isset($ttf["avatar_type"])) {
?>
                <img src="avatars/<?php echo $ttf["uid"].".".$ttf["avatar_type"]; ?>" alt="av" width="30" height="30" />
<?php
    };
?>
                hi, <?php echo $ttf["username"]; ?>!
            </div>
            <div class="menu_body">
                · <a href="search.php">search</a><br />
                · <a href="editprofile.php">edit your profile</a><br />
                · <a href="logout.php">log out</a>
<?php
    if ($ttf["perm"] == 'admin') {
?>
            </div>
            <div class="menu_title">administrate</div>
            <div class="menu_body">
                · <a href="admin_user.php">manage users</a><br />
                · <a href="http://code.google.com/p/thinktankforums/">ttf development</a>
<?php
    };
} else {
?>
                log in to ttf
            </div>
            <div class="menu_body">
                <form action="login.php" method="post">
                    <input type="text" name="username" maxlength="16" size="16" /><br />
                    <input type="password" name="password" maxlength="32" size="16" /><br />
                    <input type="submit" value="let's go!" />
                </form>
            </div>
            <div class="menu_title">
                lack an account?
            </div>
            <div class="menu_body">
                · <a href="register.php">register an account</a><br />
                · <a href="search.php">search the forums</a>
<?php
};
?>
            </div>
            <!-- **** **** end header.inc.php **** **** -->
