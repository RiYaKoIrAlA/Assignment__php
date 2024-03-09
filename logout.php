<?php
// Start the session
session_start();

// Unset the user session variable
unset($_SESSION['user']);
unset($_SESSION['admin_logged_in']);
// Redirect to index.php or any other page
header("Location: index.php");
exit;
?>
