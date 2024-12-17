<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga"; // Replace with a secure password
$dbname = "library"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if file is uploaded
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $extension = pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION);

        // Only allow CSV files
        if ($extension === 'csv') {
            if (($handle = fopen($file, "r")) !== false) {
                // Read the CSV headers
                $headers = fgetcsv($handle);

                // Prepare SQL insert statement
                $stmt = $conn->prepare(
                    "INSERT INTO Books_IF (Propriétaire, Localisation, Section, Statut, Cote, Titre, Auteur_principal, Editeur, Annee_edition, Code_barres) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }

                // Loop through the CSV rows
                while (($data = fgetcsv($handle)) !== false) {
                    // Map CSV data to variables and handle nullable fields
                    $proprietaire = !empty($data[0]) ? $data[0] : null;
                    $localisation = !empty($data[1]) ? $data[1] : null;
                    $section = !empty($data[2]) ? $data[2] : null;
                    $statut = !empty($data[3]) ? $data[3] : null;
                    $cote = !empty($data[4]) ? $data[4] : null;
                    $titre = !empty($data[5]) ? $data[5] : null;
                    $auteur_principal = !empty($data[6]) ? $data[6] : null;
                    $editeur = !empty($data[7]) ? $data[7] : null;
                    $annee_edition = (!empty($data[8]) && is_numeric($data[8]) && strlen($data[8]) === 4) ? $data[8] : null;
                    $code_barres = !empty($data[9]) ? $data[9] : null;

                    try {
                        // Bind and execute
                        $stmt->bind_param(
                            "ssssssssss",
                            $proprietaire,
                            $localisation,
                            $section,
                            $statut,
                            $cote,
                            $titre,
                            $auteur_principal,
                            $editeur,
                            $annee_edition,
                            $code_barres
                        );

                        $stmt->execute();
                    } catch (mysqli_sql_exception $e) {
                        echo "Error inserting row: " . implode(", ", $data) . ". Message: " . $e->getMessage() . "<br>";
                        continue; // Skip the problematic row and continue
                    }
                }

                fclose($handle);
                echo "CSV file imported successfully!";
            } else {
                echo "Unable to open file.";
            }
        } else {
            echo "Invalid file type. Please upload a CSV file.";
        }
    } else {
        echo "File upload error.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload CSV Institut Francais</title>
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
        .form-container input[type="file"] {
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
    <h2>Institut Francais</h2>
    <form action="livre_IF.php" method="post" enctype="multipart/form-data">
        <label for="file">Choose CSV file:</label>
        <input type="file" name="csv_file" id="file" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>
</div>

</body>
</html>
