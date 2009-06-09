<?php
/* think tank forums
 *
 * newtest.php
 */

$ttf_title = $ttf_label = "suggest a new reverse turing test";

require_once "include_common.php";

// guests cannot create tests
kill_guests();

// don't clean $question and $answer, we might output them!
$question = $_POST["question"];
$answer = $_POST["answer"];

// if any field is empty, silently and patiently let them fill them! :D
if (trim($question) == "" || trim($answer) == "") {

    require_once "include_header.php";

?>
            <form action="newtest.php" method="post">
                <div class="contenttitle">question:</div>
                <div id="newtest_question">
                    <input class="newtest_question" type="text" name="question" value="<?php echo output($question); ?>" />
                </div>
                <div class="contenttitle">answer:</div>
                <div id="newtest_answer">
                    <input class="newtest_answer" type="text" name="answer" value="<?php echo output($answer); ?>" />
                </div>
                <div id="newtest_button">
                    <input class="newtest_button" type="submit" value="submit test pair" />
                </div>
            </form>
<?php

    require_once "include_footer.php";
    die();

};

$body = strtolower(trim($question)."\n\n".trim($answer));

// insert the pair
$sql = "INSERT INTO ttf_test                    ".
       "SET author_id={$ttf["uid"]},            ".
       "    date=UNIX_TIMESTAMP(),              ".
       "    body='".clean(output($body))."' ";
if (!$result = mysql_query($sql)) showerror();
$test_id = mysql_insert_id();

// insert the post as a base revision
$sql = "INSERT INTO ttf_revision            ".
       "SET ref_id=$test_id,                ".
       "    type='test',                    ".
       "    author_id={$ttf["uid"]},        ". 
       "    date=UNIX_TIMESTAMP(),          ".
       "    ip='{$_SERVER["REMOTE_ADDR"]}', ".
       "    body='".clean($body)."'         ";
if (!$result = mysql_query($sql)) showerror();

// update the user's last rev date
$sql = "UPDATE ttf_user                 ".
       "SET rev_date=UNIX_TIMESTAMP()  ".
       "WHERE user_id={$ttf["uid"]}     ";
if (!$result = mysql_query($sql)) showerror();

// redirect to the new thread
header("Location: $ttf_protocol://{$ttf_cfg["address"]}/test.php?test_id=$test_id");

?>
