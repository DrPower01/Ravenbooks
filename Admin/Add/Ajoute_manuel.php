<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    .sidebar {
        width: 250px;
        background-color: #333;
        color: white;
        padding-top: 20px;
        height: 100vh;
        position: fixed;
    }

    .sidebar a {
        display: block;
        color: white;
        padding: 10px;
        text-decoration: none;
        font-size: 18px;
        transition: background-color 0.3s ease;
    }

    .sidebar a:hover {
        background-color: #575757;
    }

    /* Adding a left margin to the main content to prevent it from being hidden behind the sidebar */
    .main-content {
        margin-left: 260px; /* Sidebar width + some padding */
        padding: 20px;
    }

    .dropdown-menu {
        background-color: #333; /* To match the sidebar */
    }

    .dropdown-item {
        color: white;
    }

    .dropdown-item:hover {
        background-color: #575757; /* Hover effect for dropdown items */
    }
</style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="container mt-5">
            <form method="POST" class="mt-3">
                <input type="hidden" name="form_type" value="books">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="authors" class="form-label">Authors</label>
                    <input type="text" name="authors" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="publisher" class="form-label">Publisher</label>
                    <input type="text" name="publisher" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="publishedDate" class="form-label">Published Date</label>
                    <input type="text" name="publishedDate" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="pageCount" class="form-label">Page Count</label>
                    <input type="number" name="pageCount" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="categories" class="form-label">Categories</label>
                    <input type="text" name="categories" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="language" class="form-label">Language</label>
                    <input type="text" name="language" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="shelf" class="form-label">Shelf</label>
                    <input type="text" name="shelf" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="localisation" class="form-label">Localisation</label>
                    <input type="text" name="localisation" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="cover_url" class="form-label">Cover URL</label>
                    <input type="text" name="cover_url" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
include('Check_Admin.php'); 

// Database connection
$host = 'localhost';
$dbname = 'library';
$username = 'root';
$password = 'nigga';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'books') {
        // Insert into Books table
        $query = "INSERT INTO Books (title, authors, publisher, publishedDate, description, pageCount, categories, language, isbn, shelf, localisation, cover_url) 
                  VALUES (:title, :authors, :publisher, :publishedDate, :description, :pageCount, :categories, :language, :isbn, :shelf, :localisation, :cover_url)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':title' => $_POST['title'] ?? null,
            ':authors' => $_POST['authors'] ?? null,
            ':publisher' => $_POST['publisher'] ?? null,
            ':publishedDate' => $_POST['publishedDate'] ?? null,
            ':description' => $_POST['description'] ?? null,
            ':pageCount' => $_POST['pageCount'] ?? null,
            ':categories' => $_POST['categories'] ?? null,
            ':language' => $_POST['language'] ?? null,
            ':isbn' => $_POST['isbn'] ?? null,
            ':shelf' => $_POST['shelf'] ?? null,
            ':localisation' => $_POST['localisation'] ?? null,
            ':cover_url' => $_POST['cover_url'] ?? null
        ]);
    }
}
?>
