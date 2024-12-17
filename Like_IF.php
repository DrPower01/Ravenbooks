<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "nigga"; // Use a strong password
$dbname = "library";

// Get the logged-in user's ID from the session
$logged_in_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IF</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        /* Liked Books Page Styling */
        h2 {
            text-align: center;
            color: #333;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .liked-books-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .book-card {
            width: 220px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .book-cover {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .book-info {
            padding: 15px;
        }

        .book-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .book-info p {
            font-size: 14px;
            color: #555;
        }

        .book-info strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php
        if ($logged_in_user_id) {
            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query to fetch the books liked by the user
            $sql = "SELECT b.ID, b.Titre, b.Auteur_principal, b.ISBN, b.couverture
                    FROM Books_IF b
                    JOIN likes l ON b.ID = l.book_id
                    WHERE l.user_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $logged_in_user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<div class="liked-books-container">';  // Container for the liked books
                while ($row = $result->fetch_assoc()) {
                    // Fallback if no ISBN or cover image exists
                    if (!empty($row['ISBN'])) {
                        $coverUrl = "https://covers.openlibrary.org/b/isbn/" . urlencode($row['ISBN']) . "-L.jpg";
                    } elseif (!empty($row['couverture'])) {
                        $coverUrl = htmlspecialchars($row['couverture']);
                    } else {
                        $coverUrl = "https://via.placeholder.com/150x200?text=No+Cover";
                    }

                    // Display book cover, title, and author
                    echo '<div class="book-card">';
                    echo '<img src="' . $coverUrl . '" class="book-cover" alt="Book Cover" onerror="this.src=\'https://via.placeholder.com/150x200?text=No+Cover\';">';
                    echo '<div class="book-info">';
                    echo '<h5 class="book-title">' . htmlspecialchars($row['Titre']) . '</h5>';
                    echo '<p><strong>Author:</strong> ' . htmlspecialchars($row['Auteur_principal']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';  // End of the liked books container
            } else {
                echo "<p>You haven't liked any books yet.</p>";
            }

            $stmt->close();
            $conn->close();
        } else {
            echo "<p>Please log in to view your liked books.</p>";
        }
        ?>
    </div>
</body>
</html>
