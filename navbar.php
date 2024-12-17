<?php
// Include database connection
include('db.php');
session_start();


// Check if the user is logged in
$isLoggedIn = false;
$userRole = 'guest';
$userName = ''; // Initialize user name

if (isset($_SESSION['user_id'])) {
    $isLoggedIn = true;
    $userId = $_SESSION['user_id'];

    // Query the database to get the user's role and name
    $query = "SELECT role, username, email FROM user WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userRole = $row['role'];
        $userName = $row['username']; // Fetch user name
        $userEmail = $row['email'];
    }

    $stmt->close();
}
?>

<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Navbar Styling */
        nav {
            background: linear-gradient(to right, #B572F7, #830CFA); /* Gradient using the provided colors */
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
            padding: 0;
            margin: 0;
        }

        nav ul li {
            margin: 0 1rem;
            position: relative;
        }

        nav ul li a {
            text-decoration: none;
            color: white; /* White text for better contrast against the colorful background */
            font-weight: bold;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        nav ul li a:hover {
            color: #f8f9fa; /* Light color on hover for contrast */
            transform: scale(1.05);
        }

        /* User Dropdown */
        .user-dropdown {
            position: relative;
            display: inline-block;
            margin-left: auto;
        }

        .user-icon {
            font-size: 2rem;
            cursor: pointer;
            color: white;
            margin-right: 20px;
            transition: color 0.3s ease;
        }

        .user-icon:hover {
            color: #ff6f61;
        }

        /* User Info Popup Styling */
        .user-info-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 300px;
            padding: 20px;
            z-index: 2000;
            display: none;
        }

        .user-info-popup.active {
            display: block;
        }

        .user-info-popup h5 {
            font-size: 1.5rem;
            color: #007bff;
        }

        .user-info-popup p {
            margin: 8px 0;
            color: #555;
        }

        .user-info-popup .btn {
            width: 100%;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .user-info-popup .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .user-info-popup .btn-primary:hover {
            background-color: #0056b3;
        }

        .user-info-popup .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .user-info-popup .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Close button for the popup */
        .close-popup {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            cursor: pointer;
            color: #888;
        }

        .close-popup:hover {
            color: #333;
        }
    </style>
</head>

<!-- Navbar -->
<nav>
    
    <ul>
        <li><a href="Home.php">Home</a></li>


        <!-- Admin Panel Link for Admin Role -->
        <?php if ($userRole === 'admin'): ?>
            <li><a href="Admin/Books_Overview_general.php">Admin</a></li>
        <?php endif; ?>

        <!-- Other Pages -->
        <li><a href="Affichages.php">Discover</a></li>
        <li><a href="a-propos.php">About</a></li>
        <li><a href="Formulaire de contact.php">Contact</a></li>

        <!-- User Login/Dropdown -->
        <li>
            <div class="navbar">
                <?php if (!$isLoggedIn): ?>
                    <!-- Login Button -->
                    <a href="connexion.php" class="btn-login">Login</a>
                <?php else: ?>
                    <!-- User Dropdown -->
                    <div class="user-dropdown">
                        <i class="fas fa-user-circle user-icon" onclick="showUserInfo()"></i>
                    </div>
                <?php endif; ?>
            </div>
        </li>
    </ul>
</nav>

<!-- User Info Popup -->
<div id="userInfoPopup" class="user-info-popup">
    <span class="close-popup" onclick="closeUserInfoPopup()">&times;</span>
    <h5>User Information</h5>
    <p><strong>Username:</strong> <?= htmlspecialchars($userName) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($userEmail) ?></p>
    <?php if ($userRole === 'admin'): ?>
        <p><Strong>Role: </Strong> <?= htmlspecialchars($userRole) ?></p>
    <?php endif; ?>
    <a href="liked.php" class="btn btn-danger btn-primary"><i class="fas fa-heart"></i> Liked Books</a>
    <a href="Deconnexion.php" class="btn btn-danger btn-primary"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<script>
    // Toggle the menu visibility on mobile
    function toggleMenu() {
        const nav = document.querySelector('nav ul');
        nav.classList.toggle('show');
    }

    // Show the user info popup
    function showUserInfo() {
        document.getElementById('userInfoPopup').classList.add('active');
    }

    // Close the user info popup
    function closeUserInfoPopup() {
        document.getElementById('userInfoPopup').classList.remove('active');
    }
</script>
