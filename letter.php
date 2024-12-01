<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'library';
$username = 'root';
$password = 'nigga';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

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
