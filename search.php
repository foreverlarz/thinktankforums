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
                    <div>
                        <input size="32" type="text" name="string" value="<?php echo output($string); ?>" />
                        <input type="submit" value="search" />
                    </div>
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

    $search_terms = explode(" ", $string);
    $highlight_terms = array();
    foreach ($search_terms as $word) {
        $highlight_terms[] = "<span class=\"highlight\">".$word."</span>";
    };

    // print the results (if there are any)
    while ($post = mysql_fetch_array($result)) {

        $date = formatdate($post["date"]);

        $body = str_ireplace($search_terms, $highlight_terms, $post["body"]);

        echo "            <div class=\"contenttitle_sm\">\n";
        echo "                <span title=\"post id\">{$post["post_id"]}</span> in\n";
        echo "                <a class=\"link\" href=\"thread.php?thread_id=";
        echo "{$post["thread_id"]}#post-{$post["post_id"]}\">".output($post["title"])."</a> by\n";
        echo "                <a class=\"link\" href=\"profile.php?user_id={$post["author_id"]}\">";
        echo output($post["username"])."</a>\n";
        echo "                <span title=\"{$date[1]}\">{$date[0]}</span>\n";
        echo "            </div>\n";
        echo "            <div class=\"contentbox_sm\">\n";
        echo $body."\n";
        echo "            </div>\n";
    
    };
    mysql_free_result($result);

} else if (isset($_GET["string"])) {

    // if a blank string was entered
    message($ttf_label, "error", "you must enter search terms.");

};

require_once "include_footer.php";

?>
