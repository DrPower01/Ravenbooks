<!-- Form to enter ISBN -->
<div class="container">
    <form method="get" class="isbn-form">
        <label for="isbn">Enter ISBN:</label>
        <input type="text" id="isbn" name="isbn" required>
        <button type="submit">Search</button>
    </form>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Times New Roman", sans-serif;
    }

    .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        background-color: #f7f7f7;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
        text-align: center;
    }

    .book-details {
        background: #ffffff;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .book-cover {
        display: block;
        margin: 0 auto 20px;
        max-width: 200px;
        border-radius: 10px;
    }

    .book-details p {
        font-size: 18px;
        color: #444;
        margin-bottom: 10px;
    }

    strong {
        color: #333;
    }

    .isbn-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 30px;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .isbn-form label {
        font-size: 18px;
        margin-bottom: 5px;
        color: #333;
    }

    .isbn-form input {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    .isbn-form button,
    .submit-button {
        padding: 10px 20px;
        background-color: #ff6f61;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .isbn-form button:hover,
    .submit-button:hover {
        background-color: #555;
    }

    .isbn-form input:focus,
    .submit-button:focus {
        outline: none;
        border-color: #333;
    }

    </style>
</div>
<?php
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

        // Convert the published date to year only
        if ($publishedDate) {
            $publishedDate = substr($publishedDate, 0, 4); // Extract only the year (first 4 characters)
        }

        // SQL query to insert the data into the database
        $sql = "INSERT INTO Books (title, authors, publisher, publishedDate, description, pageCount, categories, language, isbn, cover_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "Error preparing SQL statement: " . $conn->error . "\n";
            exit;
        }

        $stmt->bind_param("ssssssssss", $title, $authors, $publisher, $publishedDate, $description, $pageCount, $categories, $language, $isbn, $coverUrl);

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
                <input type='hidden' name='coverUrl' value='$cover_url'>
                <button type='submit' name='submit' class='submit-button'>Add to Database</button>
              </form>";
        echo "</div>";
    }
}

// Close database connection
$conn->close();
?>

