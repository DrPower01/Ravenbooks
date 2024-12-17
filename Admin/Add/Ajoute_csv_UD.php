
<?php
include('Check_Admin.php'); 

// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga"; // Ensure this is securely handled in production
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

                // Prepare SQL insert statement with added_at field
                $stmt = $conn->prepare(
                    "INSERT INTO Books (id, title, author, publisher, publishedDate, description, pageCount, categories, language, isbn, shelf, library, cover_image, likes, views, added_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
                );

                // Loop through the CSV rows
                while (($data = fgetcsv($handle)) !== false) {
                    // Map CSV data to variables
                    $id = $data[0];
                    $title = $data[1];
                    $authors = $data[2];
                    $publisher = $data[3];
                    $year = $data[4];
                    $description = $data[5] ?? null;
                    $page_count = $data[6];
                    $category = $data[7] ?? null;
                    $language = $data[8];
                    $isbn = $data[9];
                    $shelf = $data[10];
                    $library = $data[11];
                    $cover_url = $data[12] ?? null;
                    $likes = $data[13];
                    $views = $data[14];

                    // Bind and execute
                    $stmt->bind_param(
                        "isssssiissssiii",
                        $id,
                        $title,
                        $authors,
                        $publisher,
                        $year,
                        $description,
                        $page_count,
                        $category,
                        $language,
                        $isbn,
                        $shelf,
                        $library,
                        $cover_url,
                        $likes,
                        $views
                    );
                    $stmt->execute();
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
    <title>Upload CSV Universite Balballa</title>
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
    <h2>Universite Balballa</h2>
    <form action="Ajouter-livres-par-csv.php" method="post" enctype="multipart/form-data">
        <label for="file">Choose CSV file:</label>
        <input type="file" name="csv_file" id="file" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>
</div>

</body>
</html>
