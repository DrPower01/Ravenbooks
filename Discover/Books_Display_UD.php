<?php
// Include the database connection file
include('db.php');

// Get the selected "Books Per Page" value from GET parameters, default is 12
$booksPerPage = isset($_GET['booksPerPage']) ? intval($_GET['booksPerPage']) : 12;

// Get the current page from GET parameters, default is 1
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Get the selected letter (navigation by letter)
$selectedLetter = isset($_GET['letter']) ? $_GET['letter'] : null;

// Calculate the offset for the SQL query
$offset = ($currentPage - 1) * $booksPerPage;

// Get the filter type and value from GET parameters
$filterValue = isset($_GET['categories']) ? $_GET['categories'] : 'all';

// Default query to fetch books with pagination
$sql = "SELECT id, title, authors, cover_url, categories FROM Books WHERE 1=1";

// Apply filter based on selected categories
if ($filterValue !== 'all') {
    $sql .= " AND categories = '" . $conn->real_escape_string($filterValue) . "'";
}

// Apply filter based on selected letter
if ($selectedLetter) {
    $sql .= " AND title LIKE '" . $conn->real_escape_string($selectedLetter) . "%'";
}

// Apply pagination
$sql .= " ORDER BY id DESC LIMIT $offset, $booksPerPage";

$result = $conn->query($sql);

// Get total number of books for pagination
$totalBooksQuery = "SELECT COUNT(*) as total FROM Books WHERE 1=1";
if ($filterValue !== 'all') {
    $totalBooksQuery .= " AND categories = '" . $conn->real_escape_string($filterValue) . "'";
}
if ($selectedLetter) {
    $totalBooksQuery .= " AND title LIKE '" . $conn->real_escape_string($selectedLetter) . "%'";
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
        .alphabet-nav a {
            margin: 5px;
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #007bff;
            border-radius: 5px;
            color: #007bff;
        }
        .alphabet-nav a.active, .alphabet-nav a:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3>Universite Balballa</h3>

        <!-- Alphabet Navigation -->
        <div class="alphabet-nav d-flex flex-wrap justify-content-center mb-4">
            <?php foreach (range('A', 'Z') as $letter): ?>
                <?php $activeClass = ($selectedLetter === $letter) ? 'active' : ''; ?>
                <a href="?letter=<?php echo $letter; ?>&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>" class="<?php echo $activeClass; ?>">
                    <?php echo $letter; ?>
                </a>
            <?php endforeach; ?>
            <a href="?letter=&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>" class="<?php echo is_null($selectedLetter) ? 'active' : ''; ?>">All</a>
        </div>

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

        <!-- Book Grid -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col">';
                    echo '<div class="card h-100">';
                    echo '<a href="Discover/Books_details_UD.php?id=' . htmlspecialchars($row["id"]) . '" class="text-decoration-none">';
                    echo '<div class="book-cover-container text-center">';
                    if (!empty($row["cover_url"])) {
                        echo '<img src="' . htmlspecialchars($row["cover_url"]) . '" alt="Book cover" class="card-img-top" onerror="this.onerror=null; this.src=\'placeholder_icon.png\';">';
                    } else {
                        echo '<img src="https://via.placeholder.com/150x200?text=No+Cover" class="card-img-top">';
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
                echo '<p>No books found for the selected category or letter.</p>';
            }
            ?>
        </div>

        <!-- Pagination -->
        <div class="text-center mt-4">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?php echo $currentPage - 1; ?>&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>&letter=<?php echo $selectedLetter; ?>" class="btn btn-primary">Previous</a>
            <?php endif; ?>

            <span>Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?php echo $currentPage + 1; ?>&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>&letter=<?php echo $selectedLetter; ?>" class="btn btn-primary">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Function to handle category filter change
        function filterByCategories() {
            const categories = document.getElementById('categoriesSelect').value;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('categories', categories);
            urlParams.set('page', 1);
            window.location.search = urlParams.toString();
        }
    </script>
</body>
</html>
