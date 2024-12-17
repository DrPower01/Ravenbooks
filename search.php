<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";  // Replace with your DB server
$username = "root";         // Replace with your DB username
$password = "nigga";             // Replace with your DB password
$dbname = "library";        // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare SQL to search both tables: Books and Books_IF
$sql = "SELECT id, title, authors, cover_url FROM Books WHERE title LIKE ? OR authors LIKE ? 
        UNION 
        SELECT id, Titre, Auteur_principal, couverture FROM Books_IF WHERE Titre LIKE ? OR Auteur_principal LIKE ?";

$stmt = $conn->prepare($sql);
$searchTerm = "%" . $query . "%";
$stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);

$stmt->execute();
$result = $stmt->get_result();

// Fetch results and prepare response
$books = [];

while ($row = $result->fetch_assoc()) {
    $books[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'authors' => $row['authors'],
        'cover_url' => $row['cover_url']
    ];
}

// Close connection
$stmt->close();
$conn->close();

// Return results as JSON
header('Content-Type: application/json');
echo json_encode($books);
?>
