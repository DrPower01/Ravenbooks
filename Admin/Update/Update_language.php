<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch books with no language set
$sql = "SELECT id, isbn FROM Books WHERE language IS NULL OR language = ''";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookId = $row['id'];
        $isbn = $row['isbn'];

        if (empty($isbn)) {
            echo "No ISBN found for book ID: $bookId\n";
            continue;
        }

        // Fetch book language using Google Books API
        $googleApiUrl = "https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn";
        $googleApiResponse = file_get_contents($googleApiUrl);
        $googleApiData = json_decode($googleApiResponse, true);

        if (isset($googleApiData['items'][0]['volumeInfo']['language'])) {
            $language = $googleApiData['items'][0]['volumeInfo']['language'];
        } else {
            echo "No book information found for ISBN: $isbn\n";
            continue;
        }

        // Update book language in the database
        $updateSql = "UPDATE Books SET language = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $language, $bookId);

        if ($stmt->execute()) {
            echo "Book ID: $bookId updated with language: $language\n";
        } else {
            echo "Error updating book ID: $bookId\n";
        }

        $stmt->close();
    }
} else {
    echo "No books found with missing language information.\n";
}

$conn->close();
?>