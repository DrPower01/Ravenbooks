<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <style>
        /* Your existing styles */
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
        <!-- Form for deleting a book -->
        <form method="post" class="isbn-form">
            <label for="delete_isbn">Enter ISBN to Delete:</label>
            <input type="text" id="delete_isbn" name="delete_isbn" required>
            <button type="submit" name="delete_submit">Delete Book</button>
        </form>
    </div>

    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the form has been submitted for deletion
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_submit'])) {
        $deleteIsbn = $_POST['delete_isbn'];
        
        // Prepare SQL query to delete the book
        $deleteSql = "DELETE FROM Books WHERE isbn = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("s", $deleteIsbn);
        
        if ($deleteStmt->execute()) {
            if ($deleteStmt->affected_rows > 0) {
                echo "<div style='color: green;'>Book with ISBN $deleteIsbn has been deleted from the database.</div>";
            } else {
                echo "<div style='color: red;'>No book found with ISBN $deleteIsbn.</div>";
            }
        } else {
            echo "<div style='color: red;'>Error deleting book: " . $deleteStmt->error . "</div>";
        }

        $deleteStmt->close();
    }

    // Close database connection
    $conn->close();
    ?>
</body>
</html>