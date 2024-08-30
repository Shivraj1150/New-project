<?php
session_start();

// Destroy the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page or home page
header("Location: _login.php?logged_out=1");
exit();
?>