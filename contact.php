<?php
$server = "localhost";
$names = "root";
$password = "";
$base = "contacts";

// Connexion à la base de données
$co = new mysqli($server, $names, $password, $base);

// Vérification de la connexion
if ($co->connect_error) {
    die("Connexion échouée : " . $co->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier que les champs obligatoires sont définis
    if (isset($_POST["name"], $_POST["email"], $_POST["phone"], $_POST["subject"], $_POST["message"])) {
        $name = htmlspecialchars(trim($_POST["name"])); // Nettoyage de l'entrée
        $email = htmlspecialchars(trim($_POST["email"]));
        $phone = htmlspecialchars(trim($_POST["phone"]));
        $subject = htmlspecialchars(trim($_POST["subject"]));
        $message = htmlspecialchars(trim($_POST["message"]));

        // Validation supplémentaire (email valide)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Adresse email invalide.";
            exit;
        }

        // Préparer une requête SQL pour éviter les injections SQL
        $sql = $co->prepare("INSERT INTO contact (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $sql->bind_param("sssss", $name, $email, $phone, $subject, $message);

        if ($sql->execute()) {
            echo "Merci pour votre message !";
        } else {
            echo "Erreur lors de l'enregistrement : " . $sql->error;
        }

        $sql->close();
    } else {
        echo "Veuillez remplir tous les champs obligatoires.";
    }
}

// Fermer la connexion
$co->close();
?>
