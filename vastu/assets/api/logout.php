<?php
session_start();

$_SESSION = [];
session_unset();
session_destroy();

// optional safety cookie clear
setcookie("PHPSESSID", "", time() - 3600, "/");

header("Location: /vastu/vastu/index.php");

exit;
?>