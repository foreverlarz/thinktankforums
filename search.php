<?php
/* think tank forums
 *
 * search.php
 */

require "include_common.php";
$label = "search ttf posts";
require "include_header.php";

$string = clean($_GET["string"]);       

?>
            <div class="contenttitle">punch in a keyword</div>
            <div class="contentbox" style="text-align: center;">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="get">
                    <input size="32" type="text" name="string" value="<?php echo output($string); ?>" />
                    <input type="submit" value="search" />
                </form>
            </div>
<?php

if (!empty($string)) {

    $sql = "SELECT ttf_post.thread_id, ttf_post.post_id, ttf_post.author_id, ".
           "ttf_post.date, ttf_post.body, ttf_thread.title, ttf_user.username, ".
           "MATCH(ttf_post.body) AGAINST ('$string') AS score ".
           "FROM ttf_post ".
           "LEFT JOIN ttf_thread ON ttf_post.thread_id=ttf_thread.thread_id ".
           "LEFT JOIN ttf_user ON ttf_post.author_id=ttf_user.user_id ".
           "WHERE MATCH(ttf_post.body) AGAINST ('$string') ".
           "ORDER BY score DESC";
    if (!$result = mysql_query($sql)) showerror();

    // if no results are returned...
    if (mysql_num_rows($result) == 0) {

        message("search ttf posts", "search results", "no results returned.<br /><br />".
                "either the keyword you entered is <i>very</i> common or non-existent.", 0, 0);

    };

    // print the results (if there are any)
    while ($post = mysql_fetch_array($result)) {

        // format the date
        $date = strtolower(date("M j, g\:i a", $post["date"] + 3600*$ttf["time_zone"]));

        // highlight the keyword
        // ** if part of a url is replaces, it busts the link. needs fixed. --jlr **
        $body = str_ireplace($string, "<span class=\"highlight\">$string</span>", outputbody($post["body"]));

?>
            <div class="contenttitle_sm">
                <?php echo "[".$post["post_id"]."] in <a style=\"color: white\" href=\"thread.php?thread_id=".$post["thread_id"]."#".$post["post_id"]."\">".output($post["title"])."</a> by <a style=\"color: white\" href=\"profile.php?user_id=".$post["author_id"]."\">".output($post["username"])."</a> on $date\n"; ?>
            </div>
            <div class="contentbox_sm">
<?php echo $body."\n"; ?>
            </div>
<?php
    
    };
    mysql_free_result($result);

} else if (isset($_GET["string"])) {

    // if a blank string was entered
    message("search ttf posts", "fatal error", "you must enter search terms.", 0, 0);

};

require "include_footer.php";

?>
