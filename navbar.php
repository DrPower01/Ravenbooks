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
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            background-color: #5a2d82; /* Darker shade of purple */
            color: white;
        }

        .user-info-popup .btn-primary:hover {
            background-color: #4a236b; /* Even darker shade of purple */
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

        /* Responsive Navbar */
        .hamburger {
            display: none;
            font-size: 2rem;
            cursor: pointer;
            color: white;
        }

        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                display: none;
            }

            nav ul.show {
                display: flex;
            }

            .hamburger {
                display: block;
            }

            .user-dropdown {
                margin-left: 0;
            }
        }

        /* Login Button Styling */
        .btn-login {
            background-color: #5a2d82; /* Darker shade of purple */
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #4a236b; /* Even darker shade of purple */
        }
    </style>
</head>

<!-- Navbar -->
<nav>
    <div class="hamburger" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </div>
    <ul>
        <li><a href="Home.php">Accueil</a></li>

        <!-- Admin Panel Link for Admin Role -->
        <?php if ($userRole === 'admin'): ?>
            <li><a href="Admin/Stats/Books_Overview.php">Panneau d'administration</a></li>
        <?php endif; ?>

        <!-- Other Pages -->
        <li><a href="Affichages.php">Discover</a></li>
        <li><a href="a-propos.php">À propos</a></li>
        <li><a href="Formulaire de contact.php">Contact</a></li>
    </ul>
    <div class="navbar">
        <?php if (!$isLoggedIn): ?>
            <!-- Login Button -->
            <a href="connexion.php" class="btn-login">Connexion</a>
        <?php else: ?>
            <!-- User Dropdown -->
            <div class="user-dropdown">
                <i class="fas fa-user-circle user-icon" onclick="showUserInfo()"></i>
            </div>
        <?php endif; ?>
    </div>
</nav>

<!-- User Info Popup -->
<div id="userInfoPopup" class="user-info-popup">
    <span class="close-popup" onclick="closeUserInfoPopup()">&times;</span>
    <h5>Informations de l'utilisateur</h5>
    <p><strong>Nom d'utilisateur:</strong> <?= htmlspecialchars($userName) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($userEmail) ?></p>
    <?php if ($userRole === 'admin'): ?>
        <p><Strong>Rôle: </Strong> <?= htmlspecialchars($userRole) ?></p>
    <?php endif; ?>
    <a href="Liked_general.php" class="btn btn-danger btn-primary"><i class="fas fa-heart"></i> Livres aimés</a>
    <a href="logout.php" class="btn btn-danger btn-primary"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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
