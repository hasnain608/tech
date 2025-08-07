<?php
session_start();

// Clear all session variables
session_unset();
session_destroy();

// Start a new session to pass logout message
session_start();
$_SESSION['logout_message'] = "You have been logged out successfully.";

// Optional: prevent caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Redirect to login
header("Location: index.php");
exit;
?>
