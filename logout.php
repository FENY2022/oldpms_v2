<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session completely
session_destroy();

// Redirect back to the index/login page
header("Location: index.php");
exit;
?>