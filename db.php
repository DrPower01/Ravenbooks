<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your actual password
$dbname = "library";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Connection successful
echo "Connected successfully";
?>
