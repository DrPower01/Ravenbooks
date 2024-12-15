<?php
// Connexion à la base de données
include 'db.php';

// Récupérer l'ID du livre depuis la requête GET
$bookId = $_GET['id'] ?? 0;

// Préparer la requête SQL pour récupérer les détails du livre
$query = "SELECT * FROM Books WHERE id = :id";
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
