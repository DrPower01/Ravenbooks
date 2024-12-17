<?php
include('Check_Admin.php'); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    die("Database connection failed: " . $e->getMessage());
}

// Query to get books with null or empty ISBN and non-null Title and Publisher
$query = "SELECT ID, Titre, Editeur FROM Books_IF 
          WHERE (ISBN IS NULL OR ISBN = '') AND Titre IS NOT NULL AND Editeur IS NOT NULL";
$stmt = $pdo->prepare($query);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Google Books API base URL
$apiBaseUrl = "https://www.googleapis.com/books/v1/volumes?q=";

foreach ($books as $book) {
    $id = $book['ID'];
    $title = urlencode($book['Titre']);  // URL-encode the title for safe API requests
    $publisher = urlencode($book['Editeur']);  // URL-encode the publisher

    // Construct API URL
    $apiUrl = $apiBaseUrl . "intitle:" . $title . "+inpublisher:" . $publisher;

    try {
        // Fetch ISBN information from Google Books API
        $response = @file_get_contents($apiUrl);
        if ($response === false) {
            echo "Failed to fetch data for book ID $id. Skipping...<br>";
            continue;
        }

        $data = json_decode($response, true);

        // Check if API returned a valid response and ISBN exists
        if ($data && isset($data['items'][0]['volumeInfo']['industryIdentifiers'])) {
            $identifiers = $data['items'][0]['volumeInfo']['industryIdentifiers'];
            $isbn = null;

            // Find ISBN-13 if available, else use any available identifier
            foreach ($identifiers as $identifier) {
                if ($identifier['type'] === 'ISBN_13') {
                    $isbn = $identifier['identifier'];
                    break;
                }
                if ($identifier['type'] === 'ISBN_10') {
                    $isbn = $identifier['identifier'];
                }
            }

            if ($isbn) {
                // Update ISBN in the database
                $updateQuery = "UPDATE Books_IF SET ISBN = :isbn WHERE ID = :id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute(['isbn' => $isbn, 'id' => $id]);

                echo "Updated book ID $id with ISBN '$isbn'.<br>";
            } else {
                echo "No valid ISBN found for book ID $id. Skipping...<br>";
            }
        } else {
            echo "No data found for book ID $id with title '{$book['Titre']}' and publisher '{$book['Editeur']}'. Skipping...<br>";
        }
    } catch (Exception $e) {
        echo "An error occurred while processing book ID $id: " . $e->getMessage() . "<br>";
    }
}
?>
