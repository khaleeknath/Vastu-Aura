<?php
session_start();

// Unset all session variables and destroy the session
$_SESSION = [];
session_unset();
session_destroy();

// Optional safety cookie clear
setcookie("PHPSESSID", "", time() - 3600, "/");

// Dynamically determine the redirect URL
// Redirects back to the page the user just logged out from, 
// or falls back to the homepage (assuming this file is in assets/api/)
$redirect_url = '../../index.php';

if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
    $redirect_url = $_SERVER['HTTP_REFERER'];
}

// Execute the dynamic redirect
header("Location: " . $redirect_url);
exit;
?>