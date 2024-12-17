<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga"; // Please consider changing this to a more secure password
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$user_id = $_SESSION['user_id'] ?? null; // Get the user ID from session

if (!$user_id) {
    echo "You must be logged in to view your wishlist.";
    exit;
}

// Query to get books in the user's wishlist
$sql = "SELECT Books.id, Books.title, Books.cover_url, Books.authors
        FROM Books
        INNER JOIN wishlist ON Books.id = wishlist.book_id
        WHERE wishlist.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind user_id parameter
$stmt->execute();
$result = $stmt->get_result();

$wishlistBooks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $wishlistBooks[] = $row;
    }
} else {
    echo "Your wishlist is empty.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        /* Wishlist page styles */
        .wishlist-container {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .wishlist-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .wishlist-header h1 {
            font-size: 36px;
            color: #333;
        }

        .wishlist-books-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 20px;
        }

        .wishlist-books-list li {
            flex: 1 1 calc(20% - 20px);
            max-width: calc(20% - 20px);
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .wishlist-books-list li:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .wishlist-books-list img {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #ddd;
        }

        .wishlist-books-list a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-size: 16px;
            font-weight: bold;
        }

        .wishlist-books-list a:hover {
            color: #007BFF;
        }
    </style>
</head>
<body>

    <div class="wishlist-container">
       

        <?php if (!empty($wishlistBooks)): ?>
            <ul class="wishlist-books-list">
                <?php foreach ($wishlistBooks as $book): ?>
                    <li>
                        <a href="Books_details_UD.php?id=<?php echo $book['id']; ?>">
                            <img src="<?php echo htmlspecialchars($book['cover_url']); ?>" alt="Book cover">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Your wishlist is empty.</p>
        <?php endif; ?>
    </div>
</body>
</html>
