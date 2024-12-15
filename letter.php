<?php
// Connexion à la base de données
include 'db.php';


// Récupération de la lettre depuis la requête GET
$letter = $_GET['letter'] ?? '';
$letter = htmlspecialchars($letter);

// Préparer la requête SQL
$query = "SELECT id, title FROM Books WHERE title LIKE :letter ORDER BY title";
$stmt = $pdo->prepare($query);
$stmt->execute(['letter' => $letter . '%']);

// Afficher les résultats
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($books) {
    foreach ($books as $book) {
        $id = htmlspecialchars($book['id']);
        $title = htmlspecialchars($book['title']);
        echo "<li class='list-style-none'><a href='#' onclick=\"showBookDetails($id); return false;\">$title</a></li>";
    }
} else {
    echo "<li>Aucun livre trouvé pour la lettre '$letter'.</li>";
}
?>
