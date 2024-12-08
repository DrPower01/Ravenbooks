<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'library';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer l'ID du livre depuis la requête GET
$bookId = $_GET['id'] ?? 0;

// Préparer la requête SQL pour récupérer les détails du livre
$query = "SELECT * FROM books WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $bookId]);

// Envoyer les détails au format JSON
$book = $stmt->fetch(PDO::FETCH_ASSOC);
if ($book) {
    echo json_encode($book);
} else {
    echo json_encode(['error' => 'Livre introuvable']);
}
?>
