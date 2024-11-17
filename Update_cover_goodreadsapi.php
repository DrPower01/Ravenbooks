<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch book cover from Goodreads API
function fetchBookCoverFromGoodreads($isbn) {
    // Goodreads API URL with your API key (replace YOUR_GOODREADS_API_KEY with your actual API key)
    $api_key = 'YOUR_GOODREADS_API_KEY';
    $url = "https://www.goodreads.com/book/isbn/$isbn?user_id=$api_key&key=$api_key";
    
    // Get the response from Goodreads API
    $response = file_get_contents($url);
    if ($response === FALSE) {
        return null; // In case of an error, return null
    }

    $data = simplexml_load_string($response);
    
    // Check if cover image exists
    if (isset($data->book->image_url)) {
        return (string)$data->book->image_url; // Return the cover URL
    }
    return null; // Return null if no cover is found
}

// Query to select books that need updating (i.e., where cover_url is null or empty)
$sql = "SELECT id, isbn, cover_url FROM Books WHERE cover_url IS NULL OR cover_url = ''";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Loop through each book that needs a cover update
    while ($row = $result->fetch_assoc()) {
        $book_id = $row['id'];
        $isbn = $row['isbn'];

        // Fetch the cover URL from the Goodreads API
        $cover_url = fetchBookCoverFromGoodreads($isbn);

        if ($cover_url) {
            // Update the cover_url in the database
            $updateSql = "UPDATE Books SET cover_url = ? WHERE id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("si", $cover_url, $book_id);

            if ($stmt->execute()) {
                echo "Updated cover for book ID: $book_id<br>";
            } else {
                echo "Failed to update cover for book ID: $book_id<br>";
            }

            $stmt->close();
        } else {
            echo "No cover found for ISBN: $isbn (Book ID: $book_id)<br>";
        }
    }
} else {
    echo "No books need updating.<br>";
}

$conn->close();
?>
