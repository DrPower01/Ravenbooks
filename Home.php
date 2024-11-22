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
    <script src="https://kit.fontawesome.com/02a370eee2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="test1.css">
    <title>Raven Books</title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    list-style: none;
    font-family: "Times New Roman", sans-serif;
}
.nav a, .menu a {
    text-decoration: none;
}

.nav {
    width: 800px;
    background: white;
    display: flex;
}

.left {
    width: 250px;
    padding: 0 20px;
}

.left .img_holder {
    text-align: center;
    padding: 20px 0;
    margin-top: 20px;
    border: 2px solid rgb(243, 243, 243);
    border-radius: 15px;
    background-color: #f9f9f9;
    padding: 10px;
}

.left .img_holder img {
    width: 130px;
    border-radius: 30px;
}

.pb {
    padding-bottom: 20px;
}

h1 {
    text-transform: uppercase;
    font-size: 22px;
    margin-top: 18px;
}

.title {
    font-size: 15px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 2px;
    padding-bottom: 10px;
    color: rgb(155, 145, 154);
    position: relative;
    margin-top: 35px;
    margin-left: 16px;
    margin-bottom: 13px;
}

.left .icon {
    font-size: 13px;
    color: rgb(155, 145, 154);
    margin-left: 16px;
}

.left .text {
    color: rgb(32, 31, 31);
    font-size: 13px;
}

.menu .li_wrap {
    display: flex;
    align-items: center;
    width: 100%;
    margin-bottom: 15px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.menu .li_wrap:hover .icon {
    background-color: rgb(255, 88, 46);
    color: white;
}

.menu .li_wrap:hover .text {
    font-weight: bold;
    color: rgb(27, 25, 25);
}

.menu .li_wrap .icon {
    width: 30px;
    height: 30px;
    background: rgb(243, 243, 243);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.menu .li_wrap .text {
    width: calc(100% - 50px);
    word-break: break-word;
}

hr {
    width: 120px;
    margin-left: 16px;
}

.right {
    width: calc(100% - 250px);
}

.search-container {
    width: 100%;    
    max-width: 800px;
    margin-left: 40px;
    padding: 20px;
}

h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 33px;
    font-weight: normal;
}

.search-bar {
    display: flex;
    align-items: center;
    background-color: #fff;
    padding: 7px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin: 0 auto;
    margin-bottom: 20px;
}

.category-select {
    width: 150px;
    padding: 10px;
    border: none;
    border-radius: 4px;
    margin-right: 10px;
    font-size: 14px;
    background-color: #ffffff;
    color: #555;
}

.search-input {
    flex-grow: 2;
    padding: 10px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
}

.search-button {
    padding: 10px 20px;
    border: none;
    background-color: #333;
    color: white;
    font-size: 14px;
    border-radius: 4px;
    margin-left: 10px;
    cursor: pointer;
}

.search-button:active {
    background-color: rgb(236, 236, 236);
    color: black;
}

.right_inner {
    margin-left: 60px;
    padding: 20px;
}

p {
    margin-bottom: 2%;
    color: #525151;
    font-size: 24px;
    margin-left: 0%;
}

.book_grid {
    display: grid;
    grid-template-columns: repeat(20, 1fr); /* 6 books horizontally */
    grid-template-rows: repeat(1, 1fr); /* 2 rows vertically */
    gap: 20px; /* Space between books */
    padding: 20px;
}

.book {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.book img {
    width: 100%;
    height: auto;
    max-width: 180px; /* Adjust to fit 6 books in a row */
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
    transition: box-shadow 0.3s ease; /* Smooth transition for hover effect */
}
.book:hover img {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}


.book-title {
    font-size: 18px; /* Larger title font */
    font-weight: bold;
    color: #333;
    margin-top: 15px;
}

.book-author {
    font-size: 16px; /* Larger author font */
    color: #666;
    margin-top: 5px;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
}

.pagination button {
    background-color: #333;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    margin: 0 10px;
    cursor: pointer;
}

.pagination button:hover {
    background-color: #555;
}

.pagination .page-number {
    font-size: 18px;
    margin: 0 10px;
}
.cat {
    margin-left: 40px;
    padding: 20px;
}

.cat .nm {
    color: #525151;
    font-size: 24px;
}
/* Media Query for Mobile Screens */
@media (max-width: 768px) {
    .book_grid {
        grid-template-columns: repeat(3, 1fr); /* 3 books per row */
        grid-template-rows: repeat(4, 1fr); /* 4 rows */
    }

    .book img {
        max-width: 150px; /* Adjust image size */
    }

    .right {
        width: 100%;
        margin-left: 0;
    }

    .nav {
        flex-direction: column; /* Stack the left and right sections */
    }

    .left {
        width: 100%; /* Take full width on mobile */
    }

    .search-container {
        max-width: 100%; /* Full width search container */
        margin-left: 0;
    }

    h2 {
        font-size: 24px;
    }
    .nav {
    width: 100%;
    background: white;
    display: flex;
    flex-direction: column;
    padding: 10px;
}

.left {
    width: 100%;
    padding: 0 20px;
}

.left .img_holder {
    text-align: center;
    padding: 20px 0;
    margin-top: 20px;
    border: 2px solid rgb(243, 243, 243);
    border-radius: 15px;
    background-color: #f9f9f9;
    padding: 10px;
}

.left .img_holder img {
    width: 130px;
    border-radius: 30px;
}

h1 {
    text-transform: uppercase;
    font-size: 22px;
    margin-top: 18px;
}

/* Menu styles */
.menu {
    display: flex;
    flex-direction: column;
}

.menu .li_wrap {
    display: flex;
    align-items: center;
    width: 100%;
    margin-bottom: 15px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.menu .li_wrap .icon {
    width: 30px;
    height: 30px;
    background: rgb(243, 243, 243);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.menu .li_wrap .text {
    width: calc(100% - 50px);
    word-break: break-word;
}

    .nav {
        flex-direction: column;
        padding: 0;
    }

    .left {
        width: 100%;
        padding: 10px;
    }

    .left .img_holder {
        text-align: center;
        padding: 10px;
    }

    h1 {
        font-size: 18px;
    }

    /* Adjust menu items to stack */
    .menu .li_wrap {
        flex-direction: column;
        align-items: flex-start;
    }

    /* Adjust the icon and text alignment */
    .menu .li_wrap .icon {
        margin-bottom: 8px;
        margin-right: 0;
    }

    /* Adjust text size for mobile */
    .menu .li_wrap .text {
        font-size: 14px;
    }

    .menu .li_wrap:hover .icon {
        background-color: rgb(255, 88, 46);
        color: white;
    }

    .menu .li_wrap:hover .text {
        font-weight: bold;
        color: rgb(27, 25, 25);
    }
    
    .search-container {
        margin-left: 0;
        padding: 10px;
    }

    .search-bar {
        flex-direction: column;
    }

    .category-select,
    .search-input {
        width: 100%;
        margin-bottom: 10px;
    }

    .search-button {
        width: 100%;
        margin-top: 10px;
    }
    
    .right {
        width: 100%;
        padding: 10px;
    }
    
    .book_grid {
        grid-template-columns: repeat(2, 1fr); /* Adjust grid for smaller screens */
    }
}
.book-cover-container {
    position: relative;
    display: inline-block;
}

.book-cover-container img {
    display: block;
    width: 100%; /* Adjust as needed */
    height: auto;
}

.tag {
    position: absolute;
    top: 10px; /* Adjust the position as needed */
    left: 10px; /* Adjust the position as needed */
    background-color: rgba(0, 102, 204, 0.8); /* Semi-transparent background */
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 14px;
    opacity: 0; /* Initially hidden */
    transition: opacity 0.3s ease-in-out; /* Smooth transition for showing */
}
.book-cover-container:hover .tag {
    opacity: 1; /* Show the tag when the book cover is hovered */
}

@media (max-width: 480px) {
    .book_grid {
        grid-template-columns: 1fr; /* 1 book per row */
    }

    .book img {
        max-width: 120px; /* Smaller image size for mobile */
    }

    h2 {
        font-size: 20px;
    }
}
* {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Times New Roman", sans-serif;
        }
        .nav {
            width: 100%;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .nav .left {
            display: flex;
            align-items: center;
        }
        .nav .left h1 {
            font-size: 24px;
            color: #333;
            margin-left: 10px;
        }
        .nav .menu {
            display: flex;
            align-items: center;
        }
        .nav .menu a {
            text-decoration: none;
            color: #333;
            margin: 0 10px;
            font-size: 16px;
            font-weight: bold;
        }
        .nav .menu a:hover {
            color: #007BFF;
        }
        .user-info-container {
            display: flex;
            align-items: center;
            position: relative;
        }
        .user-info {
            width: 40px;
            height: 40px;
            background-color: #007BFF;
            color: white;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            margin-right: 10px;
            transition: transform 0.3s;
        }
        .user-info:hover {
            transform: scale(1.1);
        }
        .logout {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: #FF4136;
            color: white;
            font-size: 14px;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .user-info-container:hover .logout {
            display: block;
        }
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                align-items: flex-start;
            }
            .nav .menu {
                margin-top: 10px;
                flex-wrap: wrap;
            }
            .nav .menu a {
                margin: 5px 0;
            }
        }
        .user-info .dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    overflow: hidden;
    z-index: 1000;
}

.user-info:hover .dropdown-menu {
    display: block;
}

.dropdown-menu a {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.3s ease;
}

.dropdown-menu a i {
    margin-right: 8px;
}

.dropdown-menu a:hover {
    background: #f4f4f4;
    color: #007bff;
}
.book_scroll_container {
        display: flex; /* Arrange the books in a horizontal row */
        overflow-x: scroll; /* Enable horizontal scrolling */
        padding: 10px;
        gap: 15px; /* Add some space between the books */
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

    /* Individual Book Item */
    .book {
        width: 180px; /* Set a fixed width for each book */
        flex-shrink: 0; /* Prevent the books from shrinking */
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
        height: 250px; /* Set a height for the book cover */
        overflow: hidden;
    }

    .book-cover-container img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ensures the image covers the area */
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
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <div class="nav">
        <h1>Raven Books</h1>
        <div class="user-info-container">
            <div class="user-info">
                <?php echo $firstLetter; ?>
                <div class="dropdown-menu">
                        <a href="#"><i class="fas fa-user"></i> Profile</a>
                        <a href="#"><i class="fas fa-heart"></i> Wishlist</a>
                        <a href="#"><i class="fas fa-user-friends"></i> Following</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
            </div>
            <?php if(isset($loginMessage)) { echo $loginMessage; } ?>
        </div>
    </div>

    <div class="wrapper">
        <div class="nav">
            <div class="left">
                <div class="header">
                    
                </div>
                <div class="menu_wrap pb">
                    <div class="title">
                        <p>MENU</p>
                    </div>
                    <div class="menu">
                        <ul>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-book-atlas"></i></div>
                                    <div class="text">Discover</div>
                                </div>
                            </li>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-graduation-cap"></i></div>
                                    <a href="UnivB.php"><div class="text">Universite de Balballa</div></a>
                                </div>
                            </li>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-house" aria-hidden="true"></i></div>
                                    <a href="IF.php"><div class="text">Institut Francais</div></a>
                                </div>
                            </li>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-house" aria-hidden="true"></i></div>
                                    <a href="AC.php"><div class="text">American Corn</div></a>
                                </div>
                            </li>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-list" aria-hidden="true"></i></div>
                                    <div class="text">Category</div>
                                </div>
                            </li>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-bookmark" aria-hidden="true"></i></div>
                                    <div class="text">My Library</div>
                                </div>
                            </li>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i></div>
                                    <div class="text">Download</div>
                                </div>
                            </li>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-file-lines"></i></i></div>
                                    <div class="text">About us</div>
                                </div>
                            </li><br>
                            
                            <br><br>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-arrow-right-to-bracket"></i></div>
                                    <div class="text">Log in</div>
                                </div>
                            </li>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i></div>
                                    <div class="text">Log out</div>
                                </div>
                            </li>
                            <li>
                                <div class="li_wrap">
                                    <div class="icon"><i class="fa-solid fa-user-tie"></i></div>
                                    <div class="text">Admin</div>
                                </div>
                            </li>
                        </ul><br>
                       
                    </div>
                    <div class="img_holder">
                        <img src="ab5.webp" alt="picture">
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="background">
                    <div class="search-container">
                        <h2>Discover</h2>
                        <a href="AdvanceBooksADD.php">add</a>
                        <div class="search-bar">
                            <select class="category-select">
                                <option value="all-categories">All Categories</option>
                            </select>
                            <input type="text" class="search-input" placeholder="Find the book you love...">
                            <button class="search-button">Search</button>
                        </div>
                    </div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>
