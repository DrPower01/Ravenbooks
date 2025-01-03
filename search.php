<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Get the search query from the request
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (!empty($query)) {
    // SQL query to search books by title, author, or ISBN
    $sql = $conn->prepare("SELECT id, title, authors, cover_url FROM Books WHERE title LIKE ? OR authors LIKE ? OR ISBN LIKE ?");
    $searchTerm = "%" . $query . "%";
    $sql->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);

    if ($sql->execute()) {
        $result = $sql->get_result();
        $books = [];

        while ($row = $result->fetch_assoc()) {
            $books[] = [
                "id" => $row["id"],
                "title" => $row["title"],
                "authors" => $row["authors"],
                "cover_url" => $row["cover_url"] ?? "placeholder_icon.png"
            ];
        }

        // Return the search results as JSON
        echo json_encode($books);
    } else {
        echo json_encode(["error" => "Error executing query: " . $sql->error]);
    }

    $sql->close();
} else {
    echo json_encode([]);
}

$conn->close();
?>
