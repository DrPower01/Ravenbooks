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

// Get the book ID from the URL
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($book_id > 0) {
    // Query to get current book details
    $sql = "SELECT * FROM Books WHERE id = $book_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();

        // Increment the views count by 1
        $updateViewsSql = "UPDATE Books SET views = views + 1 WHERE id = $book_id";
        $conn->query($updateViewsSql); // Execute the update query
    } else {
        echo "Book not found.";
        exit;
    }

    // Query to get the next 5 books
    $nextBooksSql = "SELECT * FROM Books WHERE id > $book_id ORDER BY id ASC LIMIT 5";
    $nextBooksResult = $conn->query($nextBooksSql);
    $nextBooks = [];
    if ($nextBooksResult->num_rows > 0) {
        while ($row = $nextBooksResult->fetch_assoc()) {
            $nextBooks[] = $row;
        }
    }
} else {
    echo "Invalid book ID.";
    exit;
}

session_start(); // Start the session 
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Handle wishlist action
if ($user_id > 0) {
    if (isset($_POST['add_to_wishlist'])) {
        // Add book to wishlist
        $wishlistSql = "INSERT INTO wishlist (user_id, book_id) VALUES ($user_id, $book_id)";
        if ($conn->query($wishlistSql)) {
            $wishlistStatus = "Book added to your wishlist.";
        } else {
            $wishlistStatus = "This book is already in your wishlist.";
        }
    } elseif (isset($_POST['remove_from_wishlist'])) {
        // Remove book from wishlist
        $wishlistSql = "DELETE FROM wishlist WHERE user_id = $user_id AND book_id = $book_id";
        if ($conn->query($wishlistSql)) {
            $wishlistStatus = "Book removed from your wishlist.";
        } else {
            $wishlistStatus = "Failed to remove book from wishlist.";
        }
    }
}

// Check if the book is already in the wishlist
$wishlistCheckSql = "SELECT * FROM wishlist WHERE user_id = $user_id AND book_id = $book_id";
$wishlistCheckResult = $conn->query($wishlistCheckSql);
$isInWishlist = $wishlistCheckResult->num_rows > 0;
?>

<?php session_start(); // Start the session 
    $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - Book Details</title>
    <style>
        /* Add CSS for Wishlist Button */
        .wishlist-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            font-size: 18px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .wishlist-button:hover {
            background-color: #0056b3;
        }
        .wishlist-status {
            color: green;
            font-size: 18px;
            margin-top: 10px;
        }
    </style>
    <style>
        /* General Body Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Main Content */
        .main-content {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Book Detail Container */
        .book-detail-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .book-detail-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .book-detail-header h1 {
            font-size: 36px;
            color: #333;
            margin: 0;
        }

        .book-detail-body {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 20px;
        }

        .book-detail-image img {
            width: 100%;
            max-width: 300px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .book-detail-info {
            flex: 1;
            max-width: 600px;
            padding: 20px;
        }

        .book-detail-info p {
            font-size: 18px;
            color: #555;
            margin-bottom: 10px;
        }

        .book-detail-info strong {
            color: #333;
        }

        .back-button {
            text-align: center;
            margin-top: 30px;
        }

        .back-button .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-button .btn:hover {
            background-color: #45a049;
        }

        /* Next Books Section */
        .next-books-container {
            margin-top: 40px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .next-books-container h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
            text-align: center;
        }

        .next-books-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            list-style: none;
            padding: 0;
        }

        .next-books-list li {
            flex: 1 1 calc(20% - 20px); /* Adjust width for responsive layout */
            max-width: calc(20% - 20px);
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .next-books-list li:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .next-books-list img {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #ddd;
        }

        .next-books-list a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-size: 16px;
            font-weight: bold;
        }

        .next-books-list a:hover {
            color: #007BFF;
        }
    </style>
</head>

    

<body>
<?php include('navbar.php'); ?>

<div class="main-content">
    <!-- Book Detail Section -->
    <div class="book-detail-container">
        <div class="book-detail-header">
            <h1><?php echo htmlspecialchars($book['title']); ?></h1>
        </div>
        <div class="book-detail-body">
            <div class="book-detail-image">
                <img src="<?php echo htmlspecialchars($book['cover_url']); ?>" alt="Book cover">
            </div>
            <div class="book-detail-info">
                <p><strong>Author:</strong> <?php echo htmlspecialchars($book['authors']); ?></p>
                <p><strong>Publisher:</strong> <?php echo htmlspecialchars($book['publisher']); ?></p>
                <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
                <p><strong>Localisation:</strong> <?php echo htmlspecialchars($book['Localisation']); ?></p>

                <?php if ($user_id > 0): ?>
                    <!-- Display status message -->
                    <?php if (isset($wishlistStatus)): ?>
                        <p class="wishlist-status"><?php echo $wishlistStatus; ?></p>
                    <?php endif; ?>

                    <!-- Wishlist Button -->
                    <?php if ($isInWishlist): ?>
                        <form method="POST">
                            <button type="submit" name="remove_from_wishlist" class="wishlist-button">Remove from Wishlist</button>
                        </form>
                    <?php else: ?>
                        <form method="POST">
                            <button type="submit" name="add_to_wishlist" class="wishlist-button">Add to Wishlist</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <p><em>Log in to manage your wishlist.</em></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Next Books Section -->
    <div class="next-books-container">
        <h2>Next Books</h2>
        <?php if (!empty($nextBooks)): ?>
            <ul class="next-books-list">
                <?php foreach ($nextBooks as $nextBook): ?>
                    <li>
                        <a href="Books_details_UD.php?id=<?php echo $nextBook['id']; ?>">
                            <img src="<?php echo htmlspecialchars($nextBook['cover_url']); ?>" alt="Book cover">
                            <?php echo htmlspecialchars($nextBook['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No more books available.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>