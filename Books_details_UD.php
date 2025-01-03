<?php
session_start(); // Start the session

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

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Handle like action
if ($user_id > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['like_book'])) {
            // Like book
            $likeSql = "INSERT INTO likes (user_id, book_id) VALUES ($user_id, $book_id)";
            if ($conn->query($likeSql)) {
                $_SESSION['like_status'] = "You liked this book.";
            } else {
                $_SESSION['like_status'] = "You have already liked this book.";
            }
        } elseif (isset($_POST['unlike_book'])) {
            // Unlike book
            $likeSql = "DELETE FROM likes WHERE user_id = $user_id AND book_id = $book_id";
            if ($conn->query($likeSql)) {
                $_SESSION['like_status'] = "You unliked this book.";
            } else {
                $_SESSION['like_status'] = "Failed to unlike this book.";
            }
        } elseif (isset($_POST['submit_comment'])) {
            // Add comment
            $comment = $conn->real_escape_string($_POST['comment']);
            $date = date('Y-m-d H:i:s');
            $commentSql = "INSERT INTO comments (book_id, uid, date, message) VALUES ($book_id, '$user_id', '$date', '$comment')";
            $conn->query($commentSql);
        }
        // Redirect to the same page to prevent form resubmission
        header("Location: Books_details_UD.php?id=$book_id");
        exit;
    }
}

// Check if the book is already liked
$likeCheckSql = "SELECT * FROM likes WHERE user_id = $user_id AND book_id = $book_id";
$likeCheckResult = $conn->query($likeCheckSql);
$isLiked = $likeCheckResult->num_rows > 0;

// Retrieve comments
$commentsSql = "SELECT * FROM comments WHERE book_id = $book_id ORDER BY date DESC";
$commentsResult = $conn->query($commentsSql);
$comments = [];
if ($commentsResult->num_rows > 0) {
    while ($row = $commentsResult->fetch_assoc()) {
        $comments[] = $row;
    }
}

// Update likes count for all books
$sql = "
    UPDATE `Books` b
    LEFT JOIN (
        SELECT 
            book_id, 
            COALESCE((COUNT(user_id)), 0) AS total_likes
        FROM `likes`
        GROUP BY book_id
    ) l ON b.id = l.book_id
    SET b.likes = l.total_likes;
";
$conn->query($sql); // Execute the update query
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - Book Details</title>
    <style>
        /* Add CSS for Like Button */
        .like-button {
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
        .like-button:hover {
            background-color: #0056b3;
        }
        .like-status {
            color: green;
            font-size: 18px;
            margin-top: 10px;
        }
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

        /* Comments Section */
        .comments-container {
            margin-top: 40px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .comments-container h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
            text-align: center;
        }

        .comment-form {
            margin-bottom: 20px;
        }

        .comment-form textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }

        .comment-form button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            font-size: 18px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .comment-form button:hover {
            background-color: #0056b3;
        }

        .comments-list {
            list-style: none;
            padding: 0;
        }

        .comments-list li {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .comments-list .comment-date {
            font-size: 14px;
            color: #999;
        }

        .comments-list .comment-message {
            font-size: 16px;
            color: #333;
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
                <?php if (!empty($book['authors'])): ?>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($book['authors']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['publisher'])): ?>
                    <p><strong>Publisher:</strong> <?php echo htmlspecialchars($book['publisher']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['publishedDate'])): ?>
                    <p><strong>Published Date:</strong> <?php echo htmlspecialchars($book['publishedDate']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['description'])): ?>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['pageCount'])): ?>
                    <p><strong>Page Count:</strong> <?php echo htmlspecialchars($book['pageCount']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['categories'])): ?>
                    <p><strong>Categories:</strong> <?php echo htmlspecialchars($book['categories']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['language'])): ?>
                    <p><strong>Language:</strong> <?php echo htmlspecialchars($book['language']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['isbn'])): ?>
                    <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['biblio'])): ?>
                    <p><strong>Biblioteque:</strong> <?php echo htmlspecialchars($book['biblio']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['Localisation'])): ?>
                    <p><strong>Localisation:</strong> <?php echo htmlspecialchars($book['Localisation']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['views'])): ?>
                    <p><strong>Views:</strong> <?php echo htmlspecialchars($book['views']); ?></p>
                <?php endif; ?>
                <?php if (!empty($book['likes'])): ?>
                    <p><strong>Likes:</strong> <?php echo htmlspecialchars($book['likes']); ?></p>
                <?php endif; ?>

                <?php if ($user_id > 0): ?>
                    <!-- Display status message -->
                    <?php if (isset($_SESSION['like_status'])): ?>
                        <p class="like-status"><?php echo $_SESSION['like_status']; unset($_SESSION['like_status']); ?></p>
                    <?php endif; ?>

                    <!-- Like Button -->
                    <?php if ($isLiked): ?>
                        <form method="POST">
                            <button type="submit" name="unlike_book" class="like-button">Unlike this Book</button>
                        </form>
                    <?php else: ?>
                        <form method="POST">
                            <button type="submit" name="like_book" class="like-button">Like this Book</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <p><em>Log in to like this book.</em></p>
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
    <!-- Comments Section -->
    <div class="comments-container">
        <h2>Comments</h2>
        <?php if ($user_id > 0): ?>
            <form method="POST" class="comment-form">
                <textarea name="comment" rows="4" placeholder="Write your comment here..." required></textarea>
                <button type="submit" name="submit_comment">Submit Comment</button>
            </form>
        <?php else: ?>
            <p><em>Log in to post a comment.</em></p>
        <?php endif; ?>

        <ul class="comments-list">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <li>
                        <p class="comment-date"><?php echo htmlspecialchars($comment['date']); ?></p>
                        <p class="comment-message"><?php echo htmlspecialchars($comment['message']); ?></p>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </ul>
    </div>
</div>
</body>
</html>