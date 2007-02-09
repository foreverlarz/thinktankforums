<?php
/* think tank forums
 *
 * logout.php
 *
 * AUDITED BY JLR 200611250124
 *
 * this script accepts NO variables.
 *
 * sanity checks include:
 * 	ttf cookie is set
 *
 * NOTE: THIS IS A SILENT SCRIPT.
 */
 if (isset($_COOKIE["thinktank"])) {
    $expire = time() - 3600;
    setcookie("thinktank", "", $expire);
 };
 header("Location: index.php");
?>