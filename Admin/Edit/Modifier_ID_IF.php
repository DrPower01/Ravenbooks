<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Livre</title>
    <style>
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

    <div class="form-container">
        <form method="post" class="book-form">
            <h2>Modifier un Livre</h2>
            <label for="book_id">Entrez l'ID du Livre à modifier :</label>
            <input type="text" id="book_id" name="book_id" required>

            <label for="proprietaire">Propriétaire :</label>
            <input type="text" id="proprietaire" name="proprietaire">

            <label for="localisation">Localisation :</label>
            <input type="text" id="localisation" name="localisation">

            <label for="section">Section :</label>
            <input type="text" id="section" name="section">

            <label for="statut">Statut :</label>
            <input type="text" id="statut" name="statut">

            <label for="cote">Cote :</label>
            <input type="text" id="cote" name="cote">

            <label for="titre">Titre :</label>
            <input type="text" id="titre" name="titre">

            <label for="auteur_principal">Auteur Principal :</label>
            <input type="text" id="auteur_principal" name="auteur_principal">

            <label for="editeur">Editeur :</label>
            <input type="text" id="editeur" name="editeur">

            <label for="annee_edition">Année d'édition :</label>
            <input type="text" id="annee_edition" name="annee_edition">

            <label for="code_barres">Code Barres :</label>
            <input type="text" id="code_barres" name="code_barres">

            <label for="description">Description :</label>
            <input type="text" id="description" name="description">

            <button type="submit" name="update_submit">Modifier le Livre</button>
        </form>
    </div>

    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "nigga";
    $dbname = "library";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form has been submitted for updating
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_submit'])) {
        $bookId = $_POST['book_id'];
        $proprietaire = $_POST['proprietaire'] ?? null;
        $localisation = $_POST['localisation'] ?? null;
        $section = $_POST['section'] ?? null;
        $statut = $_POST['statut'] ?? null;
        $cote = $_POST['cote'] ?? null;
        $titre = $_POST['titre'] ?? null;
        $auteurPrincipal = $_POST['auteur_principal'] ?? null;
        $editeur = $_POST['editeur'] ?? null;
        $anneeEdition = $_POST['annee_edition'] ?? null;
        $codeBarres = $_POST['code_barres'] ?? null;
        $description = $_POST['description'] ?? null;

        // Prepare SQL query to check if the book exists by ID
        $checkSql = "SELECT * FROM Books_IF WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $bookId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        // If the book exists, proceed to update
        if ($checkResult->num_rows > 0) {
            $updateFields = [];
            $updateParams = [];

            if (!empty($proprietaire)) {
                $updateFields[] = "Propriétaire = ?";
                $updateParams[] = $proprietaire;
            }
            if (!empty($localisation)) {
                $updateFields[] = "Localisation = ?";
                $updateParams[] = $localisation;
            }
            if (!empty($section)) {
                $updateFields[] = "Section = ?";
                $updateParams[] = $section;
            }
            if (!empty($statut)) {
                $updateFields[] = "Statut = ?";
                $updateParams[] = $statut;
            }
            if (!empty($cote)) {
                $updateFields[] = "Cote = ?";
                $updateParams[] = $cote;
            }
            if (!empty($titre)) {
                $updateFields[] = "Titre = ?";
                $updateParams[] = $titre;
            }
            if (!empty($auteurPrincipal)) {
                $updateFields[] = "Auteur_principal = ?";
                $updateParams[] = $auteurPrincipal;
            }
            if (!empty($editeur)) {
                $updateFields[] = "Editeur = ?";
                $updateParams[] = $editeur;
            }
            if (!empty($anneeEdition)) {
                $updateFields[] = "Annee_edition = ?";
                $updateParams[] = $anneeEdition;
            }
            if (!empty($codeBarres)) {
                $updateFields[] = "Code_barres = ?";
                $updateParams[] = $codeBarres;
            }
            if (!empty($description)) {
                $updateFields[] = "Description = ?";
                $updateParams[] = $description;
            }

            if (!empty($updateFields)) {
                $updateSql = "UPDATE Books_IF SET " . implode(", ", $updateFields) . " WHERE id = ?";
                $updateParams[] = $bookId;

                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param(str_repeat("s", count($updateParams) - 1) . "i", ...$updateParams);

                if ($updateStmt->execute()) {
                    echo "<div style='color: green;'>Le livre avec l'ID $bookId a été modifié avec succès.</div>";
                } else {
                    echo "<div style='color: red;'>Erreur lors de la modification du livre : " . $updateStmt->error . "</div>";
                }
                $updateStmt->close();
            } else {
                echo "<div style='color: red;'>Veuillez remplir au moins un champ pour effectuer une modification.</div>";
            }
        } else {
            echo "<div style='color: red;'>Aucun livre trouvé avec l'ID $bookId.</div>";
        }

        $checkStmt->close();
    }

    // Close database connection
    $conn->close();
    ?>
</body>
</html>
