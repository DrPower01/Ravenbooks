<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'books_if') {
        // Insert into Books_IF table
        $query = "INSERT INTO Books_IF (Propriétaire, Localisation, Section, Statut, Cote, Titre, Auteur_principal, Editeur, Annee_edition, Code_barres, ISBN) 
                  VALUES (:proprietaire, :localisation, :section, :statut, :cote, :titre, :auteur_principal, :editeur, :annee_edition, :code_barres, :isbn)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':proprietaire' => $_POST['proprietaire'],
            ':localisation' => $_POST['localisation'],
            ':section' => $_POST['section'],
            ':statut' => $_POST['statut'],
            ':cote' => $_POST['cote'],
            ':titre' => $_POST['titre'],
            ':auteur_principal' => $_POST['auteur_principal'],
            ':editeur' => $_POST['editeur'],
            ':annee_edition' => $_POST['annee_edition'],
            ':code_barres' => $_POST['code_barres'],
            ':isbn' => $_POST['isbn']
        ]);
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'books') {
        // Insert into Books table
        $query = "INSERT INTO Books (title, authors, publisher, publishedDate, description, pageCount, categories, language, isbn, shelf, localisation) 
                  VALUES (:title, :authors, :publisher, :publishedDate, :description, :pageCount, :categories, :language, :isbn, :shelf, :localisation)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':title' => $_POST['title'],
            ':authors' => $_POST['authors'],
            ':publisher' => $_POST['publisher'],
            ':publishedDate' => $_POST['publishedDate'],
            ':description' => $_POST['description'],
            ':pageCount' => $_POST['pageCount'],
            ':categories' => $_POST['categories'],
            ':language' => $_POST['language'],
            ':isbn' => $_POST['isbn'],
            ':shelf' => $_POST['shelf'],
            ':localisation' => $_POST['localisation']
        ]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="books-if-tab" data-bs-toggle="tab" data-bs-target="#books-if" type="button" role="tab">Books_IF</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="books-tab" data-bs-toggle="tab" data-bs-target="#books" type="button" role="tab">Books</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <!-- Books_IF Form -->
        <div class="tab-pane fade show active" id="books-if" role="tabpanel">
            <form method="POST" class="mt-3">
                <input type="hidden" name="form_type" value="books_if">
                <div class="mb-3">
                    <label for="proprietaire" class="form-label">Propriétaire</label>
                    <input type="text" name="proprietaire" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="localisation" class="form-label">Localisation</label>
                    <input type="text" name="localisation" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="section" class="form-label">Section</label>
                    <input type="text" name="section" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="statut" class="form-label">Statut</label>
                    <input type="text" name="statut" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="cote" class="form-label">Cote</label>
                    <input type="text" name="cote" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="auteur_principal" class="form-label">Auteur Principal</label>
                    <input type="text" name="auteur_principal" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="editeur" class="form-label">Editeur</label>
                    <input type="text" name="editeur" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="annee_edition" class="form-label">Année Edition</label>
                    <input type="text" name="annee_edition" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="code_barres" class="form-label">Code Barres</label>
                    <input type="text" name="code_barres" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <!-- Books Form -->
        <div class="tab-pane fade" id="books" role="tabpanel">
            <form method="POST" class="mt-3">
                <input type="hidden" name="form_type" value="books">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="authors" class="form-label">Authors</label>
                    <input type="text" name="authors" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="publisher" class="form-label">Publisher</label>
                    <input type="text" name="publisher" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="publishedDate" class="form-label">Published Date</label>
                    <input type="text" name="publishedDate" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="pageCount" class="form-label">Page Count</label>
                    <input type="number" name="pageCount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="categories" class="form-label">Categories</label>
                    <input type="text" name="categories" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="language" class="form-label">Language</label>
                    <input type="text" name="language" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="shelf" class="form-label">Shelf</label>
                    <input type="text" name="shelf" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="localisation" class="form-label">Localisation</label>
                    <input type="text" name="localisation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
