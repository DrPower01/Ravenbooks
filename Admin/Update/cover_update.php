<?php
include('Check_Admin.php'); 

// Database connection
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include dirname(__DIR__) .'db.php';
// Query to get Books_IF with missing covers
$sql = "SELECT ID, ISBN FROM Books_IF WHERE couverture IS NULL OR couverture = ''";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['ID'];
        $isbn = $row['ISBN'];

        // Check if ISBN is available
        if (!empty($isbn)) {
            // Fetch cover URL from Google Books API
            $coverUrl = getBookCoverFromGoogle($isbn);

            // Check if cover URL is found
            if ($coverUrl) {
                // Update the couverture field in the database
                $updateSql = "UPDATE Books_IF SET couverture = ? WHERE ID = ?";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("si", $coverUrl, $id);
                if ($stmt->execute()) {
                    echo "Updated cover for book ID $id<br>";
                } else {
                    echo "Error updating book ID $id: " . $conn->error . "<br>";
                }
            } else {
                echo "No valid cover found for ISBN: $isbn<br>";
            }
        } else {
            echo "No ISBN found for book ID $id<br>";
        }
    }
} else {
    echo "No Books_IF with missing covers found.<br>";
}

$conn->close();

// Function to fetch book cover from Google Books API
function getBookCoverFromGoogle($isbn) {
    if (empty($isbn)) {
        return null;
    }

    $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn";
    
    // Use cURL to fetch the data from Google Books API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 seconds timeout
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data['items']) && !empty($data['items'])) {
            $book = $data['items'][0];
            if (isset($book['volumeInfo']['imageLinks']['thumbnail'])) {
                return $book['volumeInfo']['imageLinks']['thumbnail']; // Return the thumbnail image URL
            }
        }
    }

    return null; // Return null if no cover found
}
?>
