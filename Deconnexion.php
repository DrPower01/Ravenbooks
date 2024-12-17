<?php
session_start(); // Start the session

// Destroy all session data
session_unset();
session_destroy();

// Redirect to the login page or homepage
header("Location: Home.php"); // You can change this to your homepage URL if needed
exit();
?>
