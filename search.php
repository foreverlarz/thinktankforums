<?php
/* think tank forums
 *
 * search.php
 */

require_once "include_common.php";

$ttf_title = $ttf_label = "search posts";

require_once "include_header.php";

$string = $_GET["string"];       

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
           "MATCH(ttf_post.body) AGAINST ('".clean($string)."') AS score ".
           "FROM ttf_post ".
           "LEFT JOIN ttf_thread ON ttf_post.thread_id=ttf_thread.thread_id ".
           "LEFT JOIN ttf_user ON ttf_post.author_id=ttf_user.user_id ".
           "WHERE MATCH(ttf_post.body) AGAINST ('".clean($string)."') ".
           "ORDER BY score DESC";
    if (!$result = mysql_query($sql)) showerror();

    // if no results are returned...
    if (mysql_num_rows($result) == 0) {

        message($ttf_label, "search results", "no results returned.<br /><br />".
                "either the keyword you entered is <i>very</i> common or non-existent.");

    };

    // print the results (if there are any)
    while ($post = mysql_fetch_array($result)) {

        // shorten the selection
        // NOTE: THIS IS NOT UTF-8 COMPATIBLE. WE NEED TO USE MULTI-BYTE SAFE
        //       STRING FUNCTIONS HERE. --jlr
        $body = $post["body"];
        $first = strpos($body, $string);
        $last = strrpos($body, $string);
        $pad = 256; // chars
        $len = strlen($body);
        $start = max(0, $first - $pad);
        $stop = min($len, $last + $pad);
        $body = substr($body, $start, $stop - $start);
        $print = "";
        if ($start > 0) $print .= "&hellip; ";
        $print .= str_ireplace($string, "<span class=\"highlight\">$string</span>", $body);
        if ($stop < $len) $print .= " &hellip;";

        echo "            <div class=\"contenttitle_sm\">\n";
        echo "                [{$post["post_id"]}] in\n";
        echo "                <a class=\"link\" href=\"thread.php?thread_id=";
        echo "{$post["thread_id"]}#{$post["post_id"]}\">".output($post["title"])."</a> by\n";
        echo "                <a class=\"link\" href=\"profile.php?user_id={$post["author_id"]}\">";
        echo output($post["username"])."</a> on\n";
        echo "                ".formatdate($post["date"])."\n";
        echo "            </div>\n";
        echo "            <div class=\"contentbox_sm\">\n";
        echo $print."\n";
        echo "            </div>\n";
    
    };
    mysql_free_result($result);

} else if (isset($_GET["string"])) {

    // if a blank string was entered
    message($ttf_label, "error", "you must enter search terms.");

};

require_once "include_footer.php";

?>
