<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un Livre</title>
    <style>
        /* Styles existants */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        nav {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        nav ul {
            display: flex;
            list-style: none;
            justify-content: center;
        }
        nav ul li {
            margin: 0 1rem;
            position: relative;
        }
        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: color 0.3s;
        }
        nav ul li a:hover {
            color: #3a6186;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            color: #3a6186;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-container label {
            font-size: 16px;
            color: #333;
        }
        .form-container input[type="text"] {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-container button {
            padding: 12px 20px;
            background-color: #3a6186;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background-color: #fce7e7;
            color: #3a6186;
        }
    </style>
</head>
<body>
    
        <h2>Universite Balballa</h2>
    <div class="form-container">
        <!-- Form for deleting a book -->
        <form method="post" class="id-form">
            <label for="delete_id">Entrez l'ID du livre à supprimer :</label>
            <input type="text" id="delete_id" name="delete_id" required>
            <button type="submit" name="delete_submit">Supprimer le livre</button>
        </form>
    </div>

    <?php
    // Connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "nigga";
    $dbname = "library";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_submit'])) {
        $deleteId = $_POST['delete_id'];
        
        // Préparer la requête SQL pour supprimer le livre
        $deleteSql = "DELETE FROM Books WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $deleteId);
        
        if ($deleteStmt->execute()) {
            if ($deleteStmt->affected_rows > 0) {
                echo "<div style='color: green;'>Le livre avec l'ID $deleteId a été supprimé de la base de données.</div>";
            } else {
                echo "<div style='color: red;'>Aucun livre trouvé avec l'ID $deleteId.</div>";
            }
        } else {
            echo "<div style='color: red;'>Erreur lors de la suppression du livre : " . $deleteStmt->error . "</div>";
        }

        $deleteStmt->close();
    }

    // Fermer la connexion à la base de données
    $conn->close();
    ?>
</body>
</html>
