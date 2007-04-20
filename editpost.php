<?php
/* think tank forums
 *
 * editpost.php
 */

require_once "include_common.php";

$post_id = clean($_REQUEST["forum_id"]);
$body    = clean($_GET["body"]);

// if the agent is logged in as a valid user
if (isset($ttf["uid"])) {

    // if a post_id is specified
    if (!empty($post_id))) {

        // let's check some permissions (either admin or author)
        if ($ttf["perm"] != "admin") {

            $sql = "SELECT author_id FROM ttf_post ".
                   "WHERE post_id='$post_id'";
            if (!$result = mysql_query($sql)) showerror();
            list($author_id) = mysql_fetch_array($result);
            mysql_free_result($result);

            if ($ttf["uid"] != $author_id) {

                message("edit a post", "fatal error",
                        "you do not have permission to edit this post.");
                die();

            };

        };

        if (!empty($body)) {


            // update the damn thing! ************************************************


        } else if (!isset($_POST["body"])) {


            // print the form with the current post content. *************************


        } else {

            message("edit a post", "fatal error",
                    "you cannot edit a post into inexistence. use the archive link!");

        };

    } else {

        message("edit a post", "fatal error",
                "you must specify a post to edit.");

    };

} else {

    message("edit a post", "fatal error",
            "you must be logged in to edit a post.");

};

?>
