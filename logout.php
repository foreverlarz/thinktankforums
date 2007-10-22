<?php
/* think tank forums
 *
 * logout.php
 */

if (isset($_COOKIE["thinktank"])) {

    $expire = time() - 3600;

    setcookie("thinktank", "", $expire);

};

header("Location: ".$_SERVER["HTTP_REFERER"]);

?>
