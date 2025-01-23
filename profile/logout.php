<?php
// Start session
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to another page after logout
header("Location: ../connect.php"); // Change "login.php" to the page where you want to redirect after logout
exit;
?>
