<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Database connection
include 'db.php';

// Initialize error and success messages
$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Verify email address using Hunter.io API
    if (empty($errors)) {
        $api_key = 'YOUR_HUNTERIO_API_KEY';
        $url = "https://api.hunter.io/v2/email-verifier?email=$email&api_key=$api_key";

        $response = file_get_contents($url);
        $result = json_decode($response, true);

        if (!$result['data']['result'] || $result['data']['result'] == 'undeliverable') {
            $errors[] = "Email address is not valid.";
        }
    }

    // Check if email or username already exists
    if (empty($errors)) {
        $check_sql = "SELECT * FROM `user` WHERE `email` = ? OR `username` = ?";
        $stmt = $mysqli->prepare($check_sql);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Username or email already exists.";
        }
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO `user` (`username`, `email`, `password`, `role`) VALUES (?, ?, ?, 'user')";
        $stmt = $mysqli->prepare($insert_sql);
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $success = "Registration successful. You can now <a href='login.php'>log in</a>.";
        } else {
            $errors[] = "Error occurred while registering. Please try again.";
        }
    }
}
?>
