<!-- Form to enter ISBN -->
<div class="container">
    <form method="get" class="isbn-form">
        <label for="isbn">UD Enter ISBN:</label>
        <input type="text" id="isbn" name="isbn" required>
        <button type="submit">Search</button>
    </form>
    <style>
        /* Your existing CSS styles */
    </style>
</div>
<?php
include('Check_Admin.php'); 

// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch book data from Google Books API
function fetchBookData($isbn) {
    $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn;
    $response = file_get_contents($url);
    if ($response === FALSE) {
        echo "Error fetching book data.\n";
        return null;
    }

    $data = json_decode($response, true);

    if (isset($data['items'][0]['volumeInfo'])) {
        return $data['items'][0]['volumeInfo'];
    } else {
        echo "No book data found for ISBN: $isbn\n";
        return null;
    }
}

// Check if the form has been submitted to insert into the database
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $isbn = $_POST['isbn'];

    // Check if the book already exists in the database
    $checkSql = "SELECT * FROM Books WHERE isbn = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $isbn);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo "The book with ISBN $isbn already exists in the database.\n";
    } else {
        // Get data from form
        $title = $_POST['title'];
        $authors = $_POST['authors'];
        $publisher = $_POST['publisher'];
        $publishedDate = $_POST['publishedDate'];
        $description = $_POST['description'];
        $pageCount = $_POST['pageCount'];
        $categories = $_POST['categories'];
        $language = $_POST['language'];
        $coverUrl = $_POST['cover_url'];
        $biblio = 'Institut_Francais'; // Default value
        $localisation = $_POST['localisation'] ?? null; // Optional field
        $views = 0; // Default value
        $likes = 0; // Default value

        // Convert the published date to year only
        if ($publishedDate) {
            $publishedDate = substr($publishedDate, 0, 4); // Extract only the year (first 4 characters)
        }

        // SQL query to insert the data into the database
        $sql = "INSERT INTO Books (title, authors, publisher, publishedDate, description, pageCount, categories, language, isbn, biblio, Localisation, cover_url, views, likes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "Error preparing SQL statement: " . $conn->error . "\n";
            exit;
        }

        $stmt->bind_param("ssssssssssssii", $title, $authors, $publisher, $publishedDate, $description, $pageCount, $categories, $language, $isbn, $biblio, $localisation, $coverUrl, $views, $likes);

        if ($stmt->execute()) {
            echo "Book inserted into the database with ISBN: $isbn\n";
        } else {
            echo "Error inserting into the database: " . $stmt->error . "\n";
        }

        $stmt->close();
    }

    $checkStmt->close();
}

// Display book data if an ISBN is provided
if (isset($_GET['isbn'])) {
    $isbn = $_GET['isbn'];
    $bookData = fetchBookData($isbn);
    if ($bookData) {
        // Extract book information
        $title = $bookData['title'] ?? '';
        $authors = isset($bookData['authors']) ? implode(', ', $bookData['authors']) : '';
        $publisher = $bookData['publisher'] ?? '';
        $publishedDate = $bookData['publishedDate'] ?? '';
        $description = $bookData['description'] ?? '';
        $pageCount = $bookData['pageCount'] ?? '';
        $categories = isset($bookData['categories']) ? implode(', ', $bookData['categories']) : '';
        $language = $bookData['language'] ?? '';
        $coverUrl = $bookData['imageLinks']['thumbnail'] ?? '';

        // Display the book information and form
        echo "<div class='container'>";
        echo "<h2>Book Details</h2>";
        echo "<div class='book-details'>";
        if ($coverUrl) {
            echo "<img src='$coverUrl' alt='Book cover' class='book-cover'>";
        }
        echo "<p><strong>Title:</strong> $title</p>";
        echo "<p><strong>Authors:</strong> $authors</p>";
        echo "<p><strong>Publisher:</strong> $publisher</p>";
        echo "<p><strong>Published Date:</strong> $publishedDate</p>";
        echo "<p><strong>Description:</strong> $description</p>";
        echo "<p><strong>Page Count:</strong> $pageCount</p>";
        echo "<p><strong>Categories:</strong> $categories</p>";
        echo "<p><strong>Language:</strong> $language</p>";
        echo "</div>";

        // Display the form to submit the data to the database
        echo "<form method='post'>
                <input type='hidden' name='title' value='$title'>
                <input type='hidden' name='authors' value='$authors'>
                <input type='hidden' name='publisher' value='$publisher'>
                <input type='hidden' name='publishedDate' value='$publishedDate'>
                <input type='hidden' name='description' value='$description'>
                <input type='hidden' name='pageCount' value='$pageCount'>
                <input type='hidden' name='categories' value='$categories'>
                <input type='hidden' name='language' value='$language'>
                <input type='hidden' name='isbn' value='$isbn'>
                <input type='hidden' name='cover_url' value='$coverUrl'>
                <input type='hidden' name='localisation' value=''>
                <button type='submit' name='submit' class='submit-button'>Add to Database</button>
              </form>";
        echo "</div>";
    }
}

// Close database connection
$conn->close();
?>
