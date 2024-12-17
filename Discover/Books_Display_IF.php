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

// Query to fetch book details with pagination
$sql = "SELECT ID, Titre, Auteur_principal, ISBN, couverture FROM Books_IF LIMIT $books_per_page OFFSET $offset";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="row book-list">';
    while ($row = $result->fetch_assoc()) {
        // Check if ISBN is available, else use the 'couverture' URL
        if (!empty($row['couverture'])) {
            // Use the URL from 'couverture' column if ISBN is not available
            $coverUrl = htmlspecialchars($row['couverture']);
        } elseif (!empty($row['ISBN'])) {
            // Generate Open Library cover URL using ISBN
            $coverUrl = "https://covers.openlibrary.org/b/isbn/" . urlencode($row['ISBN']) . "-L.jpg";
        } else {
            // Placeholder image if no cover is available
            $coverUrl = "https://via.placeholder.com/150x200?text=No+Cover";
        }

        // Link to book detail page, passing the book ID as a parameter
        echo '<div class="col-md-3 book-item">';
        echo '<a href="Books_details_IF.php?id=' . $row['ID'] . '">';
        echo '<img src="https://via.placeholder.com/150x200?text=Loading..." data-src="' . $coverUrl . '" alt="' . htmlspecialchars($row['Titre']) . '" class="book-cover lazy img-fluid">';
        echo '<h5>' . htmlspecialchars($row['Titre']) . '</h5>';
        echo '<p>By ' . htmlspecialchars($row['Auteur_principal'] ?? 'Unknown') . '</p>';
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo "<p>No books found.</p>";
}

// Pagination controls
$sql_count = "SELECT COUNT(*) as total_books FROM Books_IF";
$result_count = $conn->query($sql_count);
$total_books = $result_count->fetch_assoc()['total_books'];
$total_pages = ceil($total_books / $books_per_page);

// Pagination select for books per page
echo '<form method="get" action="" id="paginationForm" class="form-inline mb-3">';
echo '<label for="books_per_page" class="mr-2">Books per page:</label>';
echo '<select name="books_per_page" class="form-control" onchange="this.form.submit()">';
foreach ([4, 8, 12, 16,200] as $option) {
    echo '<option value="' . $option . '" ' . ($books_per_page == $option ? 'selected' : '') . '>' . $option . '</option>';
}
echo '</select>';
echo '</form>';

// Previous and Next Buttons
echo '<div class="pagination text-center">';
if ($page > 1) {
    echo '<a href="?page=' . ($page - 1) . '&books_per_page=' . $books_per_page . '" class="btn btn-primary mr-2">Previous</a>';
}

echo '<span class="current-page">Page ' . $page . ' of ' . $total_pages . '</span>';

if ($page < $total_pages) {
    echo '<a href="?page=' . ($page + 1) . '&books_per_page=' . $books_per_page . '" class="btn btn-primary ml-2">Next</a>';
}
echo '</div>';

$conn->close();
?>

<!-- Scroll Back to Top Button -->
<button id="scrollTopBtn" class="scroll-top-btn" onclick="scrollToTop()">↑</button>

<!-- Add Bootstrap 4 CSS -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Additional Custom Styling */
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

    /* Pagination Styling */
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

    /* Scroll Back to Top Button */
    .scroll-top-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: none;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 20px;
        text-align: center;
        cursor: pointer;
    }

    .scroll-top-btn:hover {
        background-color: #0056b3;
    }
</style>

<script>
// Lazy loading of images
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

// Show/Hide Scroll to Top Button
window.onscroll = function () {
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
        scrollTopBtn.style.display = "block";
    } else {
        scrollTopBtn.style.display = "none";
    }
};

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>
