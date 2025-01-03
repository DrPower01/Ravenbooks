<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga"; // Replace with your actual password
$dbname = "library"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize a message variable
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ids"])) {
    $ids_input = $_POST["ids"]; // Input string (comma-separated IDs)

    // Convert the input string to an array of integers
    $ids_array = array_map('intval', explode(',', $ids_input));
    $ids_array = array_filter($ids_array); // Remove any invalid or empty values

    if (count($ids_array) > 0) {
        // Sanitize the IDs and create a comma-separated string
        $ids_string = implode(",", $ids_array);

        // Delete records with the given IDs
        $sql = "DELETE FROM Books WHERE id IN ($ids_string)";
        if ($conn->query($sql) === TRUE) {
            $message = "Books with IDs (" . htmlspecialchars($ids_input) . ") have been successfully deleted.";
        } else {
            $message = "Error deleting records: " . $conn->error;
        }
    } else {
        $message = "Please enter valid IDs.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression Multiples</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
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
        .message {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #e7f3fe;
            color: #31708f;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Suppression Multiples</h2>
        <form method="POST" action="Suppression_multiples.php">
            <label for="ids">Enter Book IDs (comma-separated):</label>
            <input type="text" id="ids" name="ids" placeholder="e.g., 1,2,3">
            <button type="submit">Delete Books</button>
        </form>

        <?php if (!empty($message)): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
