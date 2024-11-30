<?php
session_start();  // Start the session

// Destroy the session to log the user out
session_destroy();

// Redirect to the login page after logging out
header("Location: admin_login.php");
exit();
?>
