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
            <label for="isbn">Entrez l'ISBN du Livre à modifier :</label>
            <input type="text" id="isbn" name="isbn" required>

            <label for="new_proprietaire">Propriétaire :</label>
            <input type="text" id="new_proprietaire" name="new_proprietaire">

            <label for="new_localisation">Localisation :</label>
            <input type="text" id="new_localisation" name="new_localisation">

            <label for="new_section">Section :</label>
            <input type="text" id="new_section" name="new_section">

            <label for="new_statut">Statut :</label>
            <input type="text" id="new_statut" name="new_statut">

            <label for="new_cote">Cote :</label>
            <input type="text" id="new_cote" name="new_cote">

            <label for="new_titre">Titre :</label>
            <input type="text" id="new_titre" name="new_titre">

            <label for="new_auteur_principal">Auteur Principal :</label>
            <input type="text" id="new_auteur_principal" name="new_auteur_principal">

            <label for="new_editeur">Editeur :</label>
            <input type="text" id="new_editeur" name="new_editeur">

            <label for="new_annee_edition">Année d'Edition :</label>
            <input type="text" id="new_annee_edition" name="new_annee_edition">

            <label for="new_code_barres">Code Barres :</label>
            <input type="text" id="new_code_barres" name="new_code_barres">

            <label for="new_description">Description :</label>
            <input type="text" id="new_description" name="new_description">

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
        $isbn = $_POST['isbn'];
        $newProprietaire = $_POST['new_proprietaire'] ?? null;
        $newLocalisation = $_POST['new_localisation'] ?? null;
        $newSection = $_POST['new_section'] ?? null;
        $newStatut = $_POST['new_statut'] ?? null;
        $newCote = $_POST['new_cote'] ?? null;
        $newTitre = $_POST['new_titre'] ?? null;
        $newAuteurPrincipal = $_POST['new_auteur_principal'] ?? null;
        $newEditeur = $_POST['new_editeur'] ?? null;
        $newAnneeEdition = $_POST['new_annee_edition'] ?? null;
        $newCodeBarres = $_POST['new_code_barres'] ?? null;
        $newDescription = $_POST['new_description'] ?? null;

        // Prepare SQL query to check if the book exists by ISBN
        $checkSql = "SELECT * FROM Books_IF WHERE isbn = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $isbn); // Bind ISBN as a string
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        // If the book exists, proceed to update
        if ($checkResult->num_rows > 0) {
            $updateFields = [];
            $updateParams = [];

            if (!empty($newProprietaire)) {
                $updateFields[] = "Propriétaire = ?";
                $updateParams[] = $newProprietaire;
            }
            if (!empty($newLocalisation)) {
                $updateFields[] = "Localisation = ?";
                $updateParams[] = $newLocalisation;
            }
            if (!empty($newSection)) {
                $updateFields[] = "Section = ?";
                $updateParams[] = $newSection;
            }
            if (!empty($newStatut)) {
                $updateFields[] = "Statut = ?";
                $updateParams[] = $newStatut;
            }
            if (!empty($newCote)) {
                $updateFields[] = "Cote = ?";
                $updateParams[] = $newCote;
            }
            if (!empty($newTitre)) {
                $updateFields[] = "Titre = ?";
                $updateParams[] = $newTitre;
            }
            if (!empty($newAuteurPrincipal)) {
                $updateFields[] = "Auteur_principal = ?";
                $updateParams[] = $newAuteurPrincipal;
            }
            if (!empty($newEditeur)) {
                $updateFields[] = "Editeur = ?";
                $updateParams[] = $newEditeur;
            }
            if (!empty($newAnneeEdition)) {
                $updateFields[] = "Annee_edition = ?";
                $updateParams[] = $newAnneeEdition;
            }
            if (!empty($newCodeBarres)) {
                $updateFields[] = "Code_barres = ?";
                $updateParams[] = $newCodeBarres;
            }
            if (!empty($newDescription)) {
                $updateFields[] = "Description = ?";
                $updateParams[] = $newDescription;
            }

            if (!empty($updateFields)) {
                $updateSql = "UPDATE Books_IF SET " . implode(", ", $updateFields) . " WHERE isbn = ?";
                $updateParams[] = $isbn;

                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param(str_repeat("s", count($updateParams) - 1) . "s", ...$updateParams);

                if ($updateStmt->execute()) {
                    echo "<div style='color: green;'>Le livre avec l'ISBN $isbn a été modifié avec succès.</div>";
                } else {
                    echo "<div style='color: red;'>Erreur lors de la modification du livre : " . $updateStmt->error . "</div>";
                }
                $updateStmt->close();
            } else {
                echo "<div style='color: red;'>Veuillez remplir au moins un champ pour effectuer une modification.</div>";
            }
        } else {
            echo "<div style='color: red;'>Aucun livre trouvé avec l'ISBN $isbn.</div>";
        }

        $checkStmt->close();
    }

    // Close database connection
    $conn->close();
    ?>
</body>
</html>
