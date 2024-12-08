<?php
// Include the database connection file
include 'db.php';

// Get the selected "Books Per Page" value from GET parameters, default is 12
$booksPerPage = isset($_GET['booksPerPage']) ? intval($_GET['booksPerPage']) : 12;

// Get the current page from GET parameters, default is 1
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($currentPage - 1) * $booksPerPage;

// Default query to fetch books with pagination
$locationFilter = isset($_GET['location']) ? $_GET['location'] : '';
$sql = "SELECT id, title, authors, cover_url, Faculte FROM Books";

if ($locationFilter) {
    $sql .= " WHERE Faculte = '" . $conn->real_escape_string($locationFilter) . "'";
}
$sql .= " ORDER BY id DESC LIMIT $offset, $booksPerPage";

$result = $conn->query($sql);

// Get total number of books for pagination
$totalBooksQuery = "SELECT COUNT(*) as total FROM Books";
if ($locationFilter) {
    $totalBooksQuery .= " WHERE Faculte = '" . $conn->real_escape_string($locationFilter) . "'";
}
$totalBooksResult = $conn->query($totalBooksQuery);
$totalBooks = $totalBooksResult->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $booksPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/02a370eee2.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Book Library</title>

    <style>
        /* Apply the background gradient to match the main page */
        body {
            background: linear-gradient(to bottom, #f0f4f8, #ffffff);
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* Container styling */
        .book-cover-container {
            position: relative;
        }

        .book-cover-container img {
            max-width: 100%;
            height: auto;
            transition: transform 0.3s ease;
            border-radius: 10px;
        }

        .book-cover-container img:hover {
            transform: scale(1.05);
        }
        
        /* Tag styling */
        .tag {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 12px;
            padding: 5px 10px;
            background-color: #ff4a36;
            color: white;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        }

        /* Pagination and Buttons */
        .pagination a {
            margin: 0 5px;
            border-radius: 20px;
            padding: 8px 15px;
            background-color: #ff4a36;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .pagination a:hover {
            background-color: #e63c29;
        }

        .pagination span {
            margin: 0 10px;
        }

        .card-body h5 {
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
        }

        .card-body p {
            font-size: 0.9rem;
            color: #555;
        }

        /* Layout for Books Per Page and Pagination */
        .row.mb-4.d-flex {
            justify-content: space-between;
            align-items: center;
        }

        .form-label {
            font-size: 1rem;
            font-weight: 600;
        }

        /* Card styling */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        /* Back to Top Button */
        #backToTopBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #ff4a36; /* Same color as tags and pagination */
            color: white;
            border: none;
            border-radius: 50%;
            padding: 15px;
            font-size: 16px;
            cursor: pointer;
            display: none; /* Initially hidden */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        #backToTopBtn:hover {
            background-color: #e63c29; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3>Newest Releases</h3>

        <!-- Location Select -->
        <div class="mb-4">
            <label for="locationSelect" class="form-label">Select Location</label>
            <select id="locationSelect" class="form-select" onchange="filterByLocation()">
                <option value="">All Locations</option>
                <?php
                $locationQuery = "SELECT DISTINCT Faculte FROM Books";
                $locationResult = $conn->query($locationQuery);
                if ($locationResult->num_rows > 0) {
                    while ($row = $locationResult->fetch_assoc()) {
                        $selected = ($row['Faculte'] == $locationFilter) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($row['Faculte']) . '" ' . $selected . '>' . htmlspecialchars($row['Faculte']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <!-- Books Per Page and Pagination in a row -->
        <div class="row mb-4 d-flex align-items-center">
            <div class="col-md-6 d-flex justify-content-start">
                <!-- Books Per Page Select -->
                <div>
                    <label for="booksPerPageSelect" class="form-label">Books Per Page</label>
                    <select id="booksPerPageSelect" class="form-select" onchange="updateBooksPerPage()">
                        <option value="6" <?php if ($booksPerPage == 6) echo 'selected'; ?>>6</option>
                        <option value="12" <?php if ($booksPerPage == 12) echo 'selected'; ?>>12</option>
                        <option value="24" <?php if ($booksPerPage == 24) echo 'selected'; ?>>24</option>
                        <option value="48" <?php if ($booksPerPage == 48) echo 'selected'; ?>>48</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6 d-flex justify-content-end">
                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?>&booksPerPage=<?php echo $booksPerPage; ?>" class="btn btn-primary">Previous</a>
                    <?php endif; ?>
                    <span class="mx-2">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?php echo $currentPage + 1; ?>&booksPerPage=<?php echo $booksPerPage; ?>" class="btn btn-primary">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Book Grid -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php
            if ($result->num_rows > 0) {
                // Output data of each book
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col">';
                    echo '<div class="card h-100">';
                    echo '<a href="Booksdetail.php?id=' . htmlspecialchars($row["id"]) . '" class="text-decoration-none">';
                    echo '<div class="book-cover-container text-center">';
                    
                    if (!empty($row["cover_url"])) {
                        echo '<img src="' . htmlspecialchars($row["cover_url"]) . '" alt="Book cover" class="card-img-top" onerror="this.onerror=null; this.src=\'placeholder_icon.png\';">';
                    } else {
                        echo '<img src="placeholder_icon.png" alt="No cover available" class="card-img-top">';
                    }
                    
                    echo '<div class="tag">' . htmlspecialchars($row["Faculte"]) . '</div>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row["title"]) . '</h5>';
                    echo '<p class="card-text">by ' . htmlspecialchars($row["authors"]) . '</p>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No books found.</p>';
            }
            $conn->close();
            ?>
        </div>

    </div>
    <button id="backToTopBtn" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i> <!-- Font Awesome Arrow Icon -->
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function updateBooksPerPage() {
        const booksPerPage = document.getElementById('booksPerPageSelect').value;
        const location = new URLSearchParams(window.location.search).get('location') || '';
        const url = window.location.pathname + '?booksPerPage=' + booksPerPage + (location ? '&location=' + location : '');
        window.location.href = url;
    }

    function filterByLocation() {
        var location = document.getElementById('locationSelect').value;
        var url = window.location.pathname + (location ? '?location=' + location : '');
        window.location.href = url;
    }
    </script>
    <script>
        // Show the button when the user scrolls down
        window.onscroll = function() {
            var button = document.getElementById("backToTopBtn");
            if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                button.style.display = "block";
            } else {
                button.style.display = "none";
            }
        };

        // Function to scroll to the top smoothly
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth' // Smooth scroll
            });
        }
    </script>
</body>
</html>
