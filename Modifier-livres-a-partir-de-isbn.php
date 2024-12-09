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

    <?php include('navbar.php'); ?>

    <div class="form-container">
        <form method="post" class="isbn-form">
            <h2>Modifier un Livre</h2>
            <label for="update_isbn">Entrez l'ISBN du livre à modifier :</label>
            <input type="text" id="update_isbn" name="update_isbn" required>
            <label for="new_title">Nouveau Titre :</label>
            <input type="text" id="new_title" name="new_title" required>
            <label for="new_authors">Nouvel Auteur :</label>
            <input type="text" id="new_authors" name="new_authors" required>
            <button type="submit" name="update_submit">Modifier le Livre</button>
        </form>
    </div>

    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form has been submitted for updating
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_submit'])) {
        $updateIsbn = $_POST['update_isbn'];
        $newTitle = $_POST['new_title'];
        $newAuthors = $_POST['new_authors'];

        // Prepare SQL query to check if the book exists
        $checkSql = "SELECT * FROM Books WHERE isbn = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $updateIsbn);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        // If the book exists, proceed to update
        if ($checkResult->num_rows > 0) {
            // Prepare SQL query to update the book
            $updateSql = "UPDATE Books SET title = ?, authors = ? WHERE isbn = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("sss", $newTitle, $newAuthors, $updateIsbn);

            if ($updateStmt->execute()) {
                echo "<div style='color: green;'>Le livre avec l'ISBN $updateIsbn a été modifié avec succès.</div>";
            } else {
                echo "<div style='color: red;'>Erreur lors de la modification du livre : " . $updateStmt->error . "</div>";
            }
            $updateStmt->close();
        } else {
            echo "<div style='color: red;'>Aucun livre trouvé avec l'ISBN $updateIsbn.</div>";
        }

        $checkStmt->close();
    }

    // Close database connection
    $conn->close();
    ?>
</body>
</html>