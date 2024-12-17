<?php
include('Check_Admin.php'); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('db.php');

// Function to fetch ISBN from Google Books API
function getISBN($title, $author) {
    $url = "https://www.googleapis.com/books/v1/volumes?q=intitle:" . urlencode($title) . "+inauthor:" . urlencode($author);
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if (isset($data['items'][0]['volumeInfo']['industryIdentifiers'])) {
        foreach ($data['items'][0]['volumeInfo']['industryIdentifiers'] as $identifier) {
            if ($identifier['type'] == 'ISBN_13') {
                return $identifier['identifier'];
            }
        }
    }
    return null; // Return null if ISBN is not found
}

// Get books from your database
$sql = "SELECT ID, Titre, Auteur_principal FROM Books_IF"; // Adjust table name if needed
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $isbn = getISBN($row['Titre'], $row['Auteur_principal']);
        if ($isbn) {
            // Update the ISBN in your database
            $update_sql = "UPDATE Books_IF SET ISBN = ? WHERE ID = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("si", $isbn, $row['ID']);
            $stmt->execute();
        }
    }
}

$conn->close();
?>
