<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga"; // Use a strong password
$dbname = "library";

// Pagination settings
$books_per_page = isset($_GET['books_per_page']) ? (int)$_GET['books_per_page'] : 8; // Default to 8 books per page (multiple of 4)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $books_per_page;

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get available sections for filtering
$sectionQuery = "SELECT DISTINCT Section FROM Books_IF";
$sectionResult = $conn->query($sectionQuery);

// Get selected section for filtering
$selected_section = isset($_GET['section']) ? $_GET['section'] : '';

// Get selected letter for filtering
$selected_letter = isset($_GET['letter']) ? $_GET['letter'] : '';

// Modify SQL to filter by letter and section
$where_conditions = [];
if (!empty($selected_section)) {
    $where_conditions[] = "Section LIKE '%" . $conn->real_escape_string($selected_section) . "%'";
}
if (!empty($selected_letter)) {
    $where_conditions[] = "Titre LIKE '" . $conn->real_escape_string($selected_letter) . "%'";
}

// Combine conditions
$where_sql = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Query to fetch book details with pagination and optional filters
$sql = "SELECT ID, Titre, Auteur_principal, ISBN, couverture, Section FROM Books_IF $where_sql LIMIT $books_per_page OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books List</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .book-item {
            margin-bottom: 20px;
            text-align: center;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .book-cover {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .book-item h5 {
            font-size: 18px;
            margin: 10px 0 5px;
        }

        .book-item p {
            font-size: 14px;
            color: #555;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination .btn {
            padding: 8px 20px;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
        }

        .pagination .btn:hover {
            background-color: #0056b3;
        }

        .pagination .current-page {
            font-size: 16px;
            font-weight: bold;
            margin: 0 15px;
        }

        .alphabet-nav .btn {
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Institut Francais</h3>

        <!-- Alphabet Navigation -->
        <div class="container mb-4">
            <div class="row justify-content-center alphabet-nav">
                <?php foreach (range('A', 'Z') as $letter): ?>
                    <?php 
                    $active_letter = isset($_GET['letter']) && $_GET['letter'] == $letter ? 'btn-primary' : 'btn-outline-success'; 
                    ?>
                    <div class="col-auto my-1">
                        <a href="?letter=<?= $letter ?>&section=<?= urlencode($selected_section) ?>&books_per_page=<?= $books_per_page ?>" class="btn <?= $active_letter ?>"><?= $letter ?></a>
                    </div>
                <?php endforeach; ?>
                <div class="col-auto my-1">
                    <a href="?" class="btn btn-outline-danger">All</a>
                </div>
            </div>
        </div>

        <!-- Filter by Section -->
        <form method="get" action="" class="form-inline mb-3">
            <label for="section" class="mr-2">Select Section:</label>
            <select name="section" class="form-control" onchange="this.form.submit()">
                <option value="">All</option>
                <?php while ($section = $sectionResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($section['Section']); ?>" <?php echo ($selected_section == $section['Section'] ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($section['Section']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <?php if ($result->num_rows > 0): ?>
            <div class="row book-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                    // Check if ISBN is available, else use the 'couverture' URL
                    if (!empty($row['couverture'])) {
                        $coverUrl = htmlspecialchars($row['couverture']);
                    } elseif (!empty($row['ISBN'])) {
                        // Generate Open Library cover URL using ISBN
                        $coverUrl = "https://covers.openlibrary.org/b/isbn/" . urlencode($row['ISBN']) . "-L.jpg";
                    } else {
                        // Placeholder image if no cover is available
                        $coverUrl = "https://via.placeholder.com/150x200?text=No+Cover";
                    }
                    ?>

                    <div class="col-md-3 book-item">
                        <a href="Discover/Books_details_IF.php?id=<?php echo $row['ID']; ?>">
                            <img src="https://via.placeholder.com/150x200?text=Loading..." data-src="<?php echo $coverUrl; ?>" alt="<?php echo htmlspecialchars($row['Titre']); ?>" class="book-cover lazy img-fluid">
                            <h5><?php echo htmlspecialchars($row['Titre']); ?></h5>
                            <p>By <?php echo htmlspecialchars($row['Auteur_principal'] ?? 'Unknown'); ?></p>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No books found.</p>
        <?php endif; ?>

        <!-- Pagination controls -->
        <?php 
        $sql_count = "SELECT COUNT(*) as total_books FROM Books_IF $where_sql";
        $result_count = $conn->query($sql_count);
        $total_books = $result_count->fetch_assoc()['total_books'];
        $total_pages = ceil($total_books / $books_per_page);
        ?>
        
        <form method="get" action="" id="paginationForm" class="form-inline mb-3">
            <label for="books_per_page" class="mr-2">Books per page:</label>
            <select name="books_per_page" class="form-control" onchange="this.form.submit()">
                <?php foreach ([4, 8, 12, 16, 200] as $option): ?>
                    <option value="<?php echo $option; ?>" <?php echo ($books_per_page == $option ? 'selected' : ''); ?>>
                        <?php echo $option; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <div class="pagination text-center">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo ($page - 1); ?>&books_per_page=<?php echo $books_per_page; ?>&section=<?php echo $selected_section; ?>&letter=<?php echo $selected_letter; ?>" class="btn btn-primary mr-2">Previous</a>
            <?php endif; ?>

            <span class="current-page">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo ($page + 1); ?>&books_per_page=<?php echo $books_per_page; ?>&section=<?php echo $selected_section; ?>&letter=<?php echo $selected_letter; ?>" class="btn btn-primary ml-2">Next</a>
            <?php endif; ?>
        </div>

    </div>

    <!-- Bootstrap JS (with Popper.js) -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Lazy loading of images -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const images = document.querySelectorAll('.lazy');
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.src = entry.target.getAttribute('data-src');
                        entry.target.classList.remove('lazy');
                        observer.unobserve(entry.target);
                    }
                });
            });

            images.forEach(image => {
                observer.observe(image);
            });
        });
    </script>

</body>
</html>

<?php $conn->close(); ?>
