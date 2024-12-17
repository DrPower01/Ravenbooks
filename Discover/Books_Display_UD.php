<?php
// Include the database connection file
include('db.php');

// Get the selected "Books Per Page" value from GET parameters, default is 12
$booksPerPage = isset($_GET['booksPerPage']) ? intval($_GET['booksPerPage']) : 12;

// Get the current page from GET parameters, default is 1
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($currentPage - 1) * $booksPerPage;

// Get the filter type and value from GET parameters
$filterValue = isset($_GET['categories']) ? $_GET['categories'] : 'all';

// Default query to fetch books with pagination
$sql = "SELECT id, title, authors, cover_url, categories FROM Books";

// Apply filter based on selected categories
if ($filterValue !== 'all') {
    $sql .= " WHERE categories = '" . $conn->real_escape_string($filterValue) . "'";
}

// Apply pagination
$sql .= " ORDER BY id DESC LIMIT $offset, $booksPerPage";

$result = $conn->query($sql);

// Get total number of books for pagination
$totalBooksQuery = "SELECT COUNT(*) as total FROM Books";
if ($filterValue !== 'all') {
    $totalBooksQuery .= " WHERE categories = '" . $conn->real_escape_string($filterValue) . "'";
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
        .filter-container {
            margin-bottom: 20px;
        }
        .filter-container label {
            margin-right: 15px;
        }
        .filter-container select {
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3>Book Library</h3>

        <!-- Filter Dropdown -->
        <div class="filter-container">
            <label for="categoriesSelect" class="form-label">Select categories</label>
            <select id="categoriesSelect" class="form-select" onchange="filterByCategories()">
                <option value="all" <?php echo $filterValue === 'all' ? 'selected' : ''; ?>>All categories</option>
                <?php
                $categoriesQuery = "SELECT DISTINCT categories FROM Books";
                $categoriesResult = $conn->query($categoriesQuery);
                if ($categoriesResult->num_rows > 0) {
                    while ($row = $categoriesResult->fetch_assoc()) {
                        $selected = ($row['categories'] === $filterValue) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($row['categories']) . '" ' . $selected . '>' . htmlspecialchars($row['categories']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <!-- Books Per Page and Pagination Section -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <label for="booksPerPageSelect" class="form-label">Books Per Page</label>
                <select id="booksPerPageSelect" class="form-select" onchange="updateBooksPerPage()">
                    <option value="6" <?php if ($booksPerPage == 6) echo 'selected'; ?>>6</option>
                    <option value="12" <?php if ($booksPerPage == 12) echo 'selected'; ?>>12</option>
                    <option value="24" <?php if ($booksPerPage == 24) echo 'selected'; ?>>24</option>
                    <option value="48" <?php if ($booksPerPage == 48) echo 'selected'; ?>>48</option>
                </select>
            </div>

            
        </div>

        <!-- Book Grid -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php
            if ($result->num_rows > 0) {
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
                echo '<p>No books found for the selected category.</p>';
            }
            ?>
        </div>
        <div class="col-md-6 text-end">
                <!-- Pagination -->
                <div class="pagination" style="display: flex; align-items: center; gap: 10px;">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?>&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>" class="btn btn-primary" style="margin-right: 10px;">Previous</a>
                    <?php endif; ?>

                    <span style="margin: 0 10px;">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?php echo $currentPage + 1; ?>&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>" class="btn btn-primary" style="margin-left: 10px;">Next</a>
                    <?php endif; ?>
                </div>

            </div>
    </div>

    <script>
        // Function to handle category filter change
        function filterByCategories() {
            const categories = document.getElementById('categoriesSelect').value;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('categories', categories);
            urlParams.set('page', 1); // Reset to page 1 when category changes
            window.location.search = urlParams.toString();
        }

        function updateBooksPerPage() {
            const booksPerPage = document.getElementById('booksPerPageSelect').value;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('booksPerPage', booksPerPage);
            urlParams.set('page', 1); // Reset to page 1 when books per page changes
            window.location.search = urlParams.toString();
        }
    </script>
</body>
</html>
