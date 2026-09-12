<?php
session_start();

$_SESSION = [];
session_unset();
session_destroy();

// optional safety cookie clear
setcookie("PHPSESSID", "", time() - 3600, "/");

header("Location: ../../index.php");
exit;
?>