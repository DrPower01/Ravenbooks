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

            <label for="new_author">Nouvel Auteur :</label>
            <input type="text" id="new_author" name="new_author">

            <label for="new_title">Nouveau Titre :</label>
            <input type="text" id="new_title" name="new_title">

            <label for="new_description">Nouvelle Description :</label>
            <input type="text" id="new_description" name="new_description">

            <label for="new_publishedDate">Nouvelle Date de Publication :</label>
            <input type="text" id="new_publishedDate" name="new_publishedDate">

            <button type="submit" name="update_submit">Modifier le Livre</button>
        </form>
    </div>

    <?php
include('Check_Admin.php'); 

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
        $newAuthor = $_POST['new_author'] ?? null;
        $newTitle = $_POST['new_title'] ?? null;
        $newDescription = $_POST['new_description'] ?? null;
        $newPublishedDate = $_POST['new_publishedDate'] ?? null;

        // Prepare SQL query to check if the book exists by ID
        $checkSql = "SELECT * FROM Books WHERE id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $bookId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        // If the book exists, proceed to update
        if ($checkResult->num_rows > 0) {
            $updateFields = [];
            $updateParams = [];

            if (!empty($newAuthor)) {
                $updateFields[] = "authors = ?";
                $updateParams[] = $newAuthor;
            }
            if (!empty($newTitle)) {
                $updateFields[] = "title = ?";
                $updateParams[] = $newTitle;
            }
            if (!empty($newDescription)) {
                $updateFields[] = "description = ?";
                $updateParams[] = $newDescription;
            }
            if (!empty($newPublishedDate)) {
                $updateFields[] = "publishedDate = ?";
                $updateParams[] = $newPublishedDate;
            }

            if (!empty($updateFields)) {
                $updateSql = "UPDATE Books SET " . implode(", ", $updateFields) . " WHERE id = ?";
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
