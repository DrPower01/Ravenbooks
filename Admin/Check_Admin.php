<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has an admin role
if (isset($_SESSION['user_id'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "nigga"; // Use a strong password
    $dbname = "library";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get user ID from session
    $user_id = $_SESSION['user_id'];

    // Query to check if the user is an admin
    $sql = "SELECT role FROM user WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['role'] !== 'admin') {
            // Redirect the user to another page (e.g., home page or an error page)
            header("Location: no_access.php");
            exit();
        }
    } else {
        // Redirect the user if no user data is found
        header("Location: no_access.php");
        exit();
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect if not logged in
    header("Location: login.php");
    exit();
}
?>
