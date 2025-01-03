<?php
$servername = "localhost";
$username = "root";
$password = "nigga";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isbn = $_POST["isbn"];
    $biblio = $_POST["biblio"];
    $oldest_id = $_POST["oldest_id"];

    // Delete duplicates except the oldest record
    $delete_sql = "DELETE FROM Books WHERE isbn = '$isbn' AND biblio = '$biblio' AND id != $oldest_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "Duplicate books deleted successfully.";
    } else {
        echo "Error deleting records: " . $conn->error;
    }
}

$conn->close();
?>
