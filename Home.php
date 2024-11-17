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

// Query to get books
$sql = "SELECT id, title, authors, cover_url,Faculte FROM Books ORDER BY id DESC"; // Adjust field names as per your database structure
$result = $conn->query($sql);
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
    grid-template-columns: repeat(6, 1fr); /* 6 books horizontally */
    grid-template-rows: repeat(2, 1fr); /* 2 rows vertically */
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

    </style>
</head>
<body>
    <div class="wrapper">
        <div class="nav">
            <div class="left">
                <div class="header">
                    <h1>Raven Books</h1>
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
                            <hr>
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
                        <hr>
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
                        <div class="search-bar">
                            <select class="category-select">
                                <option value="all-categories">All Categories</option>
                                <!-- More categories can be added -->
                            </select>
                            <input type="text" class="search-input" placeholder="Find the book you love...">
                            <button class="search-button">Search</button>
                        </div>
                    </div>
                </div>
                <div class="search-container">
                    <p>Newest Release:</p>
                    <!-- Pagination -->
                    <div class="pagination">
                        <button class="prev">Previous</button>
                        <span class="page-number">1</span>
                        <button class="next">Next</button>
                    </div>

                    <div class="book_grid">
                        <?php
                        if ($result->num_rows > 0) {
                            // Output data of each book
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="book">';
                                echo '<a href="book_details.php?id=' . htmlspecialchars($row["id"]) . '" class="book-link">';
                                
                                // Wrapper for the image and tag
                                echo '<div class="book-cover-container">';
                                
                                // Check if cover URL is empty or null
                                if (!empty($row["cover_url"])) {
                                    echo '<img src="' . htmlspecialchars($row["cover_url"]) . '" alt="Book cover" onerror="this.onerror=null; this.src=\'placeholder_icon.png\';">';
                                } else {
                                    // Display a placeholder icon if no cover URL is provided
                                    echo '<img src="placeholder_icon.png" alt="No cover available">';
                                }
                                
                                // Add the tag element with the 'Faculte' field
                                echo '<div class="tag">' . htmlspecialchars($row["Faculte"]) . '</div>';
                                
                                echo '</div>'; // Close the wrapper div
                                
                                echo '<div class="book-title">' . htmlspecialchars($row["title"]) . '</div>';
                                echo '<div class="book-author">by ' . htmlspecialchars($row["authors"]) . '</div>';
                                echo '</a>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No books found.</p>';
                        }
                        $conn->close();
                        ?>
                    </div>




                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
            let currentPage = 1;
        const booksPerPage = 12; // 6 books per row * 2 rows = 12 books per page

        const books = document.querySelectorAll('.book');
        const totalBooks = books.length; // Get the total number of books

        // Calculate total pages dynamically
        const totalPages = Math.ceil(totalBooks / booksPerPage);

        // Function to show the books for the current page
        function showPage(page) {
            const startIndex = (page - 1) * booksPerPage;
            const endIndex = startIndex + booksPerPage;
            
            books.forEach((book, index) => {
                if (index >= startIndex && index < endIndex) {
                    book.style.display = 'block';
                } else {
                    book.style.display = 'none';
                }
            });

            // Update page number
            document.querySelector('.page-number').textContent = page;
        }

        // Pagination event listeners
        document.querySelector('.next').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });

        document.querySelector('.prev').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        // Initial load
        showPage(currentPage);

    </script>
</body>
</html>
