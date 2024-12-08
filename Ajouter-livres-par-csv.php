<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga";
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
                    "INSERT INTO books (id, title, author, publisher, publishedDate, description, pageCount, categories , language, isbn, shelf, library,  cover_image, likes, views) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
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
