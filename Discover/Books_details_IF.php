<?php include('navbar.php'); ?>
<style>
    /* Container for the book detail page */
    .book-detail-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        margin: 30px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background-color: #f9f9f9;
    }

    /* Book cover image styling */
    .book-cover {
        flex: 1 1 200px; /* Reduce the flex basis to make it slightly smaller */
        max-width: 200px; /* Reduce the maximum width of the cover image */
        margin-right: 10px; /* Reduce space between cover and information */
    }

    .book-cover img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Book info styling */
    .book-info {
        flex: 2 1 500px; /* Ensure information takes up more space */
        max-width: 600px;
        padding-left: 15px; /* Reduce left padding to bring content closer */
    }

    .book-info h1 {
        font-size: 28px; /* Slightly smaller title font */
        margin-bottom: 15px;
        color: #333;
    }

    .book-info p {
        font-size: 16px; /* Reduce font size for better proximity */
        margin-bottom: 8px; /* Less space between lines */
        color: #555;
    }

    .book-info strong {
        color: #000;
    }

    /* Comment section styling */
    textarea {
        width: 100%;
        height: 100px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    button {
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #218838;
    }

    hr {
        margin-top: 20px;
        border: 1px solid #ccc;
    }

    p {
        padding: 10px;
        background-color: white;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    strong {
        color: #007bff;
    }
</style>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga"; // Use a strong password
$dbname = "library";

// Get the logged-in user's ID from the session
$logged_in_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Get the book ID from the URL parameter
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($book_id > 0) {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch the book details
    $sql = "SELECT * FROM Books_IF WHERE ID = $book_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Fallback if no ISBN or cover image exists
        if (!empty($row['ISBN'])) {
            $coverUrl = "https://covers.openlibrary.org/b/isbn/" . urlencode($row['ISBN']) . "-L.jpg";
        } elseif (!empty($row['couverture'])) {
            $coverUrl = htmlspecialchars($row['couverture']);
        } else {
            $coverUrl = "https://via.placeholder.com/150x200?text=No+Cover";
        }

        // Display book information with cover image on the left
        echo '<div class="book-detail-container">';
        echo '<div class="book-cover">';
        echo '<img src="' . $coverUrl . '" alt="Book Cover" class="img-fluid">';
        echo '</div>';
        echo '<div class="book-info">';
        echo '<h1>' . htmlspecialchars($row['Titre']) . '</h1>';
        echo '<p><strong>Author:</strong> ' . htmlspecialchars($row['Auteur_principal'] ?? 'Unknown') . '</p>';
        echo '<p><strong>Publisher:</strong> ' . htmlspecialchars($row['Editeur'] ?? 'Unknown') . '</p>';
        echo '<p><strong>Year of Edition:</strong> ' . htmlspecialchars($row['Annee_edition'] ?? 'Unknown') . '</p>';
        echo '<p><strong>ISBN:</strong> ' . htmlspecialchars($row['ISBN'] ?? 'Not available') . '</p>';
        echo '<p><strong>Section:</strong> ' . htmlspecialchars($row['Section'] ?? 'Not specified') . '</p>';
        echo '<p><strong>Status:</strong> ' . htmlspecialchars($row['Statut'] ?? 'Not available') . '</p>';
        echo '<p><strong>Shelf Code (Cote):</strong> ' . htmlspecialchars($row['Cote'] ?? 'Not available') . '</p>';
        echo '</div>';
        echo '</div>';

        // LIKE / UNLIKE Button Logic
        if ($logged_in_user_id) {
            // Check if the user has already liked this book
            $sql_like_check = "SELECT * FROM likes WHERE user_id = $logged_in_user_id AND book_id = $book_id";
            $like_check_result = $conn->query($sql_like_check);

            if ($like_check_result->num_rows > 0) {
                // User has liked the book, show "Unlike" button
                echo '<form method="POST">';
                echo '<input type="hidden" name="action" value="unlike">';
                echo '<input type="hidden" name="book_id" value="' . $book_id . '">';
                echo '<button type="submit">Unlike</button>';
                echo '</form>';
            } else {
                // User has not liked the book, show "Like" button
                echo '<form method="POST">';
                echo '<input type="hidden" name="action" value="like">';
                echo '<input type="hidden" name="book_id" value="' . $book_id . '">';
                echo '<button type="submit">Like</button>';
                echo '</form>';
            }
        } else {
            echo "<p>Please log in to like this book.</p>";
        }

        

        // Comment Section
        echo '<h2>Leave a Comment</h2>';
        if ($logged_in_user_id) {
            echo '<form action="" method="POST">';
            echo '<textarea name="message" rows="4" cols="50" placeholder="Write your comment here..."></textarea><br>';
            echo '<input type="hidden" name="book_id" value="' . $book_id . '">';  // Hidden book ID
            echo '<input type="hidden" name="uid" value="' . $logged_in_user_id . '">';  // Logged-in user ID
            echo '<input type="hidden" name="date" value="' . date('Y-m-d H:i:s') . '">';  // Current date and time
            echo '<button type="submit" name="submit">Post Comment</button>';
            echo '</form>';
        } else {
            echo "<p>Please log in to leave a comment.</p>";
        }

        // Handle comment submission
        if (isset($_POST['submit']) && $logged_in_user_id) {
            $message = $_POST['message'];  // Get comment message
            $uid = $_POST['uid'];  // Get user ID
            $date = $_POST['date'];  // Get current date
            $book_id = $_POST['book_id'];  // Get book ID

            // Insert comment into database
            $sql_insert = "INSERT INTO comments (book_id, uid, date, message) VALUES ('$book_id', '$uid', '$date', '$message')";
            if ($conn->query($sql_insert) === TRUE) {
                echo "<p>Comment posted successfully!</p>";
            } else {
                echo "<p>Error: " . $conn->error . "</p>";
            }
        }

        // Display existing comments for this book
        echo '<h3>Comments:</h3>';
        $sql_comments = "SELECT * FROM comments WHERE book_id = $book_id ORDER BY date DESC";
        $result_comments = $conn->query($sql_comments);
        
        if ($result_comments->num_rows > 0) {
            while ($comment = $result_comments->fetch_assoc()) {
                echo '<p><strong>' . htmlspecialchars($comment['uid']) . '</strong> (' . htmlspecialchars($comment['date']) . ')<br>' . htmlspecialchars($comment['message']) . '</p><hr>';
            }
        } else {
            echo "<p>No comments yet.</p>";
        }
    } else {
        echo "<p>Book not found.</p>";
    }

    // Increment the 'Vue' column for the book
    if ($book_id > 0) {
        $updateVueSql = "UPDATE Books_IF SET Vue = Vue + 1 WHERE ID = $book_id";
        if (!$conn->query($updateVueSql)) {
            echo "<p>Error updating views: " . $conn->error . "</p>";
        }
    }
    // Handle like/unlike actions
    if (isset($_POST['action']) && $logged_in_user_id) {
        $action = $_POST['action'];
        $book_id = $_POST['book_id'];

        if ($action == 'like') {
            // Insert like into the database
            $sql_like = "INSERT INTO likes (user_id, book_id) VALUES ($logged_in_user_id, $book_id)";
            if ($conn->query($sql_like) === TRUE) {
                echo "<p>You liked this book!</p>";
            } else {
                echo "<p>Error: " . $conn->error . "</p>";
            }
        } elseif ($action == 'unlike') {
            // Remove like from the database
            $sql_unlike = "DELETE FROM likes WHERE user_id = $logged_in_user_id AND book_id = $book_id";
            if ($conn->query($sql_unlike) === TRUE) {
                echo "<p>You unliked this book!</p>";
            } else {
                echo "<p>Error: " . $conn->error . "</p>";
            }
        }
    }
    $conn->close();
} else {
    echo "<p>Invalid book ID.</p>";
}
?>


