<?php
include('Check_Admin.php'); 

// Database connection
$host = 'localhost'; 
$dbname = 'library'; 
$username = 'root'; 
$password = 'nigga'; 
$dsn = "mysql:host=$host;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Query to get books with null or empty 'Auteur_principal' and non-null 'Titre'
$query = "SELECT ID, Titre FROM Books_IF 
          WHERE (Auteur_principal IS NULL OR Auteur_principal = '') AND Titre IS NOT NULL";
$stmt = $pdo->prepare($query);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// OpenLibrary API base URL
$apiBaseUrl = "https://openlibrary.org/search.json?title=";

foreach ($books as $book) {
    $id = $book['ID'];
    $title = urlencode($book['Titre']); // URL-encode the title for safe API requests

    // Fetch author information from OpenLibrary API
    $apiUrl = $apiBaseUrl . $title;
    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);

    if ($data && isset($data['docs'][0]['author_name'][0])) {
        $author = $data['docs'][0]['author_name'][0]; // Get the first author from the response

        // Update 'Auteur_principal' in the database
        $updateQuery = "UPDATE Books_IF SET Auteur_principal = :author WHERE ID = :id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute(['author' => $author, 'id' => $id]);

        echo "Updated book ID $id with author '$author'.<br>";
    } else {
        echo "No author found for book ID $id with title '{$book['Titre']}'. Skipping...<br>";
    }
}
?>
