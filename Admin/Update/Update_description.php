<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga"; // Change this to your actual password
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch books with no description set
$sql = "SELECT id, isbn FROM Books WHERE description IS NULL OR description = ''";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookId = $row['id'];
        $isbn = $row['isbn'];

        if (empty($isbn)) {
            echo "No ISBN found for book ID: $bookId\n";
            continue;
        }

        // Fetch book information using Google Books API
        $googleApiUrl = "https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn";
        $googleApiResponse = file_get_contents($googleApiUrl);
        $googleApiData = json_decode($googleApiResponse, true);

        if (isset($googleApiData['items'][0]['volumeInfo'])) {
            $volumeInfo = $googleApiData['items'][0]['volumeInfo'];
            $description = isset($volumeInfo['description']) ? $volumeInfo['description'] : null;
        } else {
            echo "No book information found for ISBN: $isbn\n";
            continue;
        }

        // Update book description in the database
        if ($description) {
            $updateSql = "UPDATE Books SET description = ? WHERE id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("si", $description, $bookId);

            if ($stmt->execute()) {
                echo "Book ID: $bookId updated with description.\n";
            } else {
                echo "Error updating book ID: $bookId\n";
            }

            $stmt->close();
        } else {
            echo "No description found for ISBN: $isbn\n";
        }
    }
} else {
    echo "No books found with missing description information.\n";
}

$conn->close();
?>