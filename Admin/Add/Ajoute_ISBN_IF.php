<!-- Form to enter ISBN -->
<div class="container">
    <form method="get" class="isbn-form">
        <label for="isbn">IF Enter ISBN:</label>
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
$password = "nigga"; // Ensure sensitive data like this is stored securely.
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
    $checkSql = "SELECT * FROM Books_IF WHERE ISBN = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $isbn);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo "The book with ISBN $isbn already exists in the database.\n";
    } else {
        // Get data from form
        $titre = $_POST['title'];
        $auteur_principal = $_POST['authors'];
        $editeur = $_POST['publisher'];
        $annee_edition = $_POST['publishedDate'];
        $couverture = $_POST['coverUrl'];

        // Convert publishedDate to year only
        if ($annee_edition) {
            $annee_edition = substr($annee_edition, 0, 4); // Extract only the year
        }

        // Additional static values for fields not provided
        $proprietaire = "Library Admin"; // Example value
        $localisation = "Main Library"; // Example value
        $section = "General"; // Example value
        $statut = "Available"; // Example value
        $cote = "N/A"; // Example value
        $code_barres = "N/A"; // Example value
        $added_at = date("Y-m-d H:i:s"); // Current timestamp
        $vue = 0; // Default view count

        // SQL query to insert data into Books_IF table
        $sql = "INSERT INTO Books_IF (Propriétaire, Localisation, Section, Statut, Cote, Titre, Auteur_principal, Editeur, Annee_edition, Code_barres, ISBN, couverture, added_at, Vue)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "Error preparing SQL statement: " . $conn->error . "\n";
            exit;
        }

        $stmt->bind_param("sssssssssssssi", $proprietaire, $localisation, $section, $statut, $cote, $titre, $auteur_principal, $editeur, $annee_edition, $code_barres, $isbn, $couverture, $added_at, $vue);

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
        echo "</div>";

        // Display the form to submit the data to the database
        echo "<form method='post'>
                <input type='hidden' name='title' value='$title'>
                <input type='hidden' name='authors' value='$authors'>
                <input type='hidden' name='publisher' value='$publisher'>
                <input type='hidden' name='publishedDate' value='$publishedDate'>
                <input type='hidden' name='coverUrl' value='$coverUrl'>
                <input type='hidden' name='isbn' value='$isbn'>
                <button type='submit' name='submit' class='submit-button'>Add to Database</button>
              </form>";
        echo "</div>";
    }
}

// Close database connection
$conn->close();
?>
