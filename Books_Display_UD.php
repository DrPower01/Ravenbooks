<?php
// Inclure le fichier de connexion à la base de données
include('db.php');

// Obtenir la valeur sélectionnée "Livres par page" à partir des paramètres GET, par défaut est 12
$booksPerPage = isset($_GET['booksPerPage']) ? intval($_GET['booksPerPage']) : 12;

// Obtenir la page actuelle à partir des paramètres GET, par défaut est 1
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Obtenir la lettre sélectionnée (navigation par lettre)
$selectedLetter = isset($_GET['letter']) ? $_GET['letter'] : null;

// Obtenir la bibliothèque sélectionnée
$selectedBiblio = isset($_GET['biblio']) ? $_GET['biblio'] : 'all';

// Calculer le décalage pour la requête SQL
$offset = ($currentPage - 1) * $booksPerPage;

// Obtenir le type de filtre et la valeur à partir des paramètres GET
$filterValue = isset($_GET['categories']) ? $_GET['categories'] : 'all';

// Requête par défaut pour récupérer les livres avec pagination
$sql = "SELECT id, title, authors, cover_url, categories, biblio, likes, views FROM Books WHERE 1=1";

// Appliquer le filtre basé sur les catégories sélectionnées
if ($filterValue !== 'all') {
    $sql .= " AND categories = '" . $conn->real_escape_string($filterValue) . "'";
}

// Appliquer le filtre basé sur la lettre sélectionnée
if ($selectedLetter) {
    $sql .= " AND title LIKE '" . $conn->real_escape_string($selectedLetter) . "%'";
}

// Appliquer le filtre basé sur la bibliothèque sélectionnée
if ($selectedBiblio !== 'all') {
    $sql .= " AND biblio = '" . $conn->real_escape_string($selectedBiblio) . "'";
}

// Appliquer la pagination
$sql .= " ORDER BY id DESC LIMIT $offset, $booksPerPage";

$result = $conn->query($sql);

// Obtenir le nombre total de livres pour la pagination
$totalBooksQuery = "SELECT COUNT(*) as total FROM Books WHERE 1=1";
if ($filterValue !== 'all') {
    $totalBooksQuery .= " AND categories = '" . $conn->real_escape_string($filterValue) . "'";
}
if ($selectedLetter) {
    $totalBooksQuery .= " AND title LIKE '" . $conn->real_escape_string($selectedLetter) . "%'";
}
if ($selectedBiblio !== 'all') {
    $totalBooksQuery .= " AND biblio = '" . $conn->real_escape_string($selectedBiblio) . "'";
}
$totalBooksResult = $conn->query($totalBooksQuery);
$totalBooks = $totalBooksResult->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $booksPerPage);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/02a370eee2.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Bibliothèque de Livres</title>
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
        .biblio-tag, .likes-tag, .views-tag {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px;
            border-radius: 5px;
            font-size: 12px;
        }
        .likes-tag {
            top: 40px;
        }
        .views-tag {
            top: 70px;
        }
    </style>
</head>
<body>
        <!-- Navigation par alphabet -->
        <div class="alphabet-nav d-flex flex-wrap justify-content-center mb-4">
            <?php foreach (range('A', 'Z') as $letter): ?>
                <?php $activeClass = ($selectedLetter === $letter) ? 'active' : ''; ?>
                <a href="?letter=<?php echo $letter; ?>&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>&biblio=<?php echo $selectedBiblio; ?>" class="<?php echo $activeClass; ?>">
                    <?php echo $letter; ?>
                </a>
            <?php endforeach; ?>
            <a href="?letter=&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>&biblio=<?php echo $selectedBiblio; ?>" class="<?php echo is_null($selectedLetter) ? 'active' : ''; ?>">Tous</a>
        </div>

        <!-- Filtre déroulant pour les catégories -->
        <div class="filter-container">
            <label for="categoriesSelect" class="form-label">Sélectionner les catégories</label>
            <select id="categoriesSelect" class="form-select" onchange="filterByCategories()">
                <option value="all" <?php echo $filterValue === 'all' ? 'selected' : ''; ?>>Toutes les catégories</option>
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

        <!-- Filtre déroulant pour les bibliothèques -->
        <div class="filter-container">
            <label for="biblioSelect" class="form-label">Sélectionner la bibliothèque</label>
            <select id="biblioSelect" class="form-select" onchange="filterByBiblio()">
                <option value="all" <?php echo $selectedBiblio === 'all' ? 'selected' : ''; ?>>Toutes les bibliothèques</option>
                <?php
                $biblioQuery = "SELECT DISTINCT biblio FROM Books";
                $biblioResult = $conn->query($biblioQuery);
                if ($biblioResult->num_rows > 0) {
                    while ($row = $biblioResult->fetch_assoc()) {
                        $selected = ($row['biblio'] === $selectedBiblio) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($row['biblio']) . '" ' . $selected . '>' . htmlspecialchars($row['biblio']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <!-- Sélection du nombre de livres par page -->
        <div class="filter-container">
            <label for="booksPerPageSelect" class="form-label">Livres par page</label>
            <select id="booksPerPageSelect" class="form-select" onchange="changeBooksPerPage()">
                <option value="12" <?php echo $booksPerPage == 12 ? 'selected' : ''; ?>>12</option>
                <option value="24" <?php echo $booksPerPage == 24 ? 'selected' : ''; ?>>24</option>
                <option value="36" <?php echo $booksPerPage == 36 ? 'selected' : ''; ?>>36</option>
                <option value="48" <?php echo $booksPerPage == 48 ? 'selected' : ''; ?>>48</option>
            </select>
        </div>

        <!-- Grille de livres -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col">';
                    echo '<div class="card h-100 position-relative">';
                    echo '<a href="Books_details_UD.php?id=' . htmlspecialchars($row["id"]) . '" class="text-decoration-none">';
                    echo '<div class="book-cover-container text-center">';
                    if (!empty($row["cover_url"])) {
                        echo '<img src="' . htmlspecialchars($row["cover_url"]) . '" alt="Couverture du livre" class="card-img-top" onerror="this.onerror=null; this.src=\'placeholder_icon.png\';">';
                    } else {
                        echo '<img src="https://via.placeholder.com/150x200?text=Pas+de+Couverture" class="card-img-top">';
                    }
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row["title"]) . '</h5>';
                    echo '<p class="card-text">par ' . htmlspecialchars($row["authors"]) . '</p>';
                    echo '</div>';
                    echo '</a>';
                    echo '<div class="biblio-tag">' . htmlspecialchars($row["biblio"]) . '</div>';
                    echo '<div class="likes-tag"><i class="fas fa-thumbs-up"></i> ' . htmlspecialchars($row["likes"]) . '</div>';
                    echo '<div class="views-tag"><i class="fas fa-eye"></i> ' . htmlspecialchars($row["views"]) . '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Aucun livre trouvé pour la catégorie, la lettre ou la bibliothèque sélectionnée.</p>';
            }
            ?>
        </div>

        <!-- Pagination -->
        <div class="text-center mt-4">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?php echo $currentPage - 1; ?>&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>&letter=<?php echo $selectedLetter; ?>&biblio=<?php echo $selectedBiblio; ?>" class="btn btn-primary">Précédent</a>
            <?php endif; ?>

            <span>Page <?php echo $currentPage; ?> de <?php echo $totalPages; ?></span>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?php echo $currentPage + 1; ?>&booksPerPage=<?php echo $booksPerPage; ?>&categories=<?php echo $filterValue; ?>&letter=<?php echo $selectedLetter; ?>&biblio=<?php echo $selectedBiblio; ?>" class="btn btn-primary">Suivant</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Fonction pour gérer le changement de filtre de catégorie
        function filterByCategories() {
            const categories = document.getElementById('categoriesSelect').value;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('categories', categories);
            urlParams.set('page', 1);
            window.location.search = urlParams.toString();
        }

        // Fonction pour gérer le changement de filtre de bibliothèque
        function filterByBiblio() {
            const biblio = document.getElementById('biblioSelect').value;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('biblio', biblio);
            urlParams.set('page', 1);
            window.location.search = urlParams.toString();
        }

        // Fonction pour gérer le changement du nombre de livres par page
        function changeBooksPerPage() {
            const booksPerPage = document.getElementById('booksPerPageSelect').value;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('booksPerPage', booksPerPage);
            urlParams.set('page', 1);
            window.location.search = urlParams.toString();
        }
    </script>
</body>
</html>
