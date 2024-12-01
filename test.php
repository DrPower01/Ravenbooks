<?php
// Database connection (ensure this matches your database setup)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in
    $loginMessage = "You are not logged in. Please <a href='login.php'>log in</a>.";
} else {
    // User is logged in
    $user_name = $_SESSION['user_name']; // Retrieve user's name from session
    $firstLetter = strtoupper($user_name[0]); // Extract and capitalize the first letter
    
    
}
$stmt = $conn->prepare("SELECT id, email, password, name, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $db_email, $db_password, $db_name, $db_role);
$stmt->fetch();

if (password_verify($password, $db_password)) {
    $_SESSION['user_id'] = $id;
    $_SESSION['user_name'] = $db_name;
    $_SESSION['user_email'] = $db_email;
    $_SESSION['user_role'] = $db_role;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ravenbooks</title>
    <script src="https://kit.fontawesome.com/02a370eee2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        *{
            font-family: "Times New Roman", sans-serif;
        }
        .book_scroll_container {
            display: flex;
            overflow-x: scroll;
            padding: 10px;
            gap: 15px;
        }

        .book_scroll_container::-webkit-scrollbar {
            height: 8px;
        }

        .book_scroll_container::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
        }

        .book_scroll_container::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }

        .book {
            width: 180px;
            flex-shrink: 0;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 10px;
            overflow: hidden;
        }

        .book-cover-container {
            position: relative;
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        .book-cover-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .book-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        .book-author {
            font-size: 14px;
            color: #777;
        }

        .tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 5px;
            border-radius: 5px;
            font-size: 14px;
        }

        .nav-container {
            height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .books-container {
            height: 100vh;
            overflow-y: auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #343a40;
            color: #fff;
            padding: 10px 20px;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
        }

        .dropdown-toggle::after {
            margin-left: 8px;
        }
        .search-container {
            max-width: 600px;
            margin: 50px auto;
            position: relative;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
        }

        .search-results li {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
        }

        .search-results li img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            border-radius: 0.375rem;
            object-fit: cover;
        }

        .search-results li:hover {
            background-color: #f8f9fa;
        }

        .search-results li:last-child {
            border-bottom: none;
        }

        .no-results {
            padding: 10px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-title">Ravenbooks</div>
        <div>
            <?php if (!isset($_SESSION['user_name'])): ?>
                <!-- Login Button if no session -->
                <a href="login.php" class="btn btn-primary">Login</a>
            <?php else: ?>
                <!-- Dropdown Menu if session exists -->
                <div class="dropdown">
                    <button
                        class="btn btn-primary dropdown-toggle"
                        type="button"
                        id="userMenu"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-thumbs-up like-icon"></i>Liked Books</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-heart"></i> Wishlist</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
                    </ul>
                </div>
            <?php endif; ?>
            
    </header>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Left Navigation -->
            <div class="col-md-3 col-lg-2 nav-container">
                <h4>Navigation</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-book-atlas"></i> Home</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-graduation-cap"></i>Universite de Balballa</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-house" aria-hidden="true"></i>Institut Francais</a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><i class="fa-solid fa-house" aria-hidden="true"></i>American Corn</a></li>
                </ul>
            </div>

            <!-- Right Books Display -->
            <div class="col-md-9 col-lg-10 books-container">
            <div class="container">
                <div class="search-container">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Search books by title, author, or ISBN..."
                        id="searchBox"
                    />
                    <ul class="list-group search-results" id="searchResults"></ul>
                </div>
            </div>

            <script>
                document.getElementById('searchBox').addEventListener('input', function () {
                    const query = this.value.trim();
                    const resultsContainer = document.getElementById('searchResults');

                    // Clear previous results if query is empty
                    if (query === '') {
                        resultsContainer.innerHTML = '';
                        return;
                    }

                    // Perform AJAX request
                    fetch(`search.php?query=${encodeURIComponent(query)}`)
                        .then((response) => response.json())
                        .then((data) => {
                            resultsContainer.innerHTML = '';

                            if (data.length === 0) {
                                resultsContainer.innerHTML =
                                    '<li class="list-group-item no-results">No books found</li>';
                                return;
                            }

                            // Populate results
                            data.forEach((book) => {
                                const li = document.createElement('li');
                                li.className = 'list-group-item d-flex align-items-center';
                                li.innerHTML = `
                                    <img src="${book.cover_url}" alt="${book.title}">
                                    <div>
                                        <strong>${book.title}</strong><br>
                                        <span class="text-muted">${book.authors}</span>
                                    </div>
                                `;
                                li.addEventListener('click', () => {
                                    window.location.href = `book_details.php?id=${book.id}`;
                                });
                                resultsContainer.appendChild(li);
                            });
                        })
                        .catch((error) => {
                            console.error('Error fetching data:', error);
                        });
                });
            </script><br><br>
        </div>
                <p>Most viewed:</p>
            <div class="book_scroll_container">
                            <?php
                            // Recommended books query
                            $sql1 = "SELECT Books.id, Books.title, Books.authors, Books.publisher, Books.cover_url, Books.description, Books.Faculte, COUNT(views.id) AS view_count
                                        FROM Books
                                        LEFT JOIN views ON Books.id = views.book_id
                                        GROUP BY Books.id
                                        ORDER BY view_count DESC
                                        LIMIT 10";
                            $result1 = $conn->query($sql1);

                            if ($result1->num_rows > 0) {
                                while ($row = $result1->fetch_assoc()) {
                                    echo '<div class="book">';
                                    echo '<a href="book_details.php?id=' . htmlspecialchars($row["id"]) . '" class="book-link">';
                                    echo '<div class="book-cover-container">';
                                    if (!empty($row["cover_url"])) {
                                        echo '<img src="' . htmlspecialchars($row["cover_url"]) . '" alt="Book cover" onerror="this.onerror=null; this.src=\'placeholder_icon.png\';" loading="lazy">';
                                    } else {
                                        echo '<img src="placeholder_icon.png" alt="No cover available">';
                                    }
                                    echo '<div class="tag">' . htmlspecialchars($row["Faculte"]) . '</div>';
                                    echo '</div>';
                                    echo '<div class="book-title">' . htmlspecialchars($row["title"]) . '</div>';
                                    echo '<div class="book-author">by ' . htmlspecialchars($row["authors"]) . '</div>';
                                    echo '</a>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p>No recommended books found.</p>';
                            }
                            ?>
                        </div><br><br>
                            <p>Lastest:</p>
                        <div class="book_scroll_container">
                            <?php
                            // Newest books query
                            $sql2 = "SELECT * FROM Books ORDER BY id DESC LIMIT 10";
                            $result2 = $conn->query($sql2);

                            if ($result2->num_rows > 0) {
                                while ($row = $result2->fetch_assoc()) {
                                    echo '<div class="book">';
                                    echo '<a href="book_details.php?id=' . htmlspecialchars($row["id"]) . '" class="book-link">';
                                    echo '<div class="book-cover-container">';
                                    if (!empty($row["cover_url"])) {
                                        echo '<img src="' . htmlspecialchars($row["cover_url"]) . '" alt="Book cover" onerror="this.onerror=null; this.src=\'placeholder_icon.png\';">';
                                    } else {
                                        echo '<img src="placeholder_icon.png" alt="No cover available">';
                                    }
                                    echo '<div class="tag">' . htmlspecialchars($row["Faculte"]) . '</div>';
                                    echo '</div>';
                                    echo '<div class="book-title">' . htmlspecialchars($row["title"]) . '</div>';
                                    echo '<div class="book-author">by ' . htmlspecialchars($row["authors"]) . '</div>';
                                    echo '</a>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p>No newest books found.</p>';
                            }
                            ?>
                        </div><br><br>
                        <p>Popular:</p>
                        <div class="book_scroll_container">
                            <?php
                            // Popular books query
                            $sql3 = "SELECT * FROM Books ORDER BY likes DESC LIMIT 10";
                            $result3 = $conn->query($sql3);

                            if ($result3 && $result3->num_rows > 0) {
                                while ($row = $result3->fetch_assoc()) {
                                    echo '<div class="book">';
                                    echo '<a href="book_details.php?id=' . htmlspecialchars($row["id"]) . '" class="book-link">';
                                    echo '<div class="book-cover-container">';
                                    if (!empty($row["cover_url"])) {
                                        echo '<img src="' . htmlspecialchars($row["cover_url"]) . '" alt="Book cover" onerror="this.onerror=null; this.src=\'placeholder_icon.png\';">';
                                    } else {
                                        echo '<img src="placeholder_icon.png" alt="No cover available">';
                                    }
                                    echo '<div class="tag">' . htmlspecialchars($row["Faculte"]) . '</div>';
                                    echo '</div>';
                                    echo '<div class="book-title">' . htmlspecialchars($row["title"]) . '</div>';
                                    echo '<div class="book-author">by ' . htmlspecialchars($row["authors"]) . '</div>';
                                    echo '</a>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p>No popular books found.</p>';
                            }
                            ?>
                        </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 