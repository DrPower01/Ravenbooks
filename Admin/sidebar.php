<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    .sidebar {
        width: 250px;
        background-color: #333;
        color: white;
        padding-top: 20px;
        height: 100vh;
        position: fixed;
    }
    .sidebar a {
        display: block;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        font-size: 18px;
        transition: background-color 0.3s ease;
    }
    .sidebar a:hover, .sidebar a.active {
        background-color: #575757;
    }
    .submenu {
        padding-left: 20px; /* Indentation for submenu */
    }
    .submenu a {
        font-size: 16px;
        padding: 5px 30px; /* Submenu link padding */
    }
    .submenu a:hover, .submenu a.active {
        background-color: #575757;
    }
</style>

<!-- sidebar.php -->
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <h2 style="text-align: center;">Dashboard</h2>
    <a href="Redirect_Home.php" class="<?= $current_page == 'Redirect_Home.php' ? 'active' : '' ?>">Home</a>
    <a href="Books_Overview_general.php" class="<?= $current_page == 'Books_Overview_general.php' ? 'active' : '' ?>">Books Overview</a>
    
    <!-- Submenu -->
    <a href="Ajoute.php" class="<?= $current_page == 'Ajoute.php' ? 'active' : '' ?>">Add Books</a>
    
    <a href="Supprime_ID_general.php" class="<?= $current_page == 'Supprime_ID_general.php' ? 'active' : '' ?>">Delete Books</a>

    <a href="#" class="<?= $current_page == 'Modifie_ID_general.php' || $current_page == 'Modifie_ISBN_general.php' ? 'active' : '' ?>">Modify Books</a>
    <div class="submenu">
        <a href="Modifie_ID_general.php" class="<?= $current_page == 'Modifie_ID_general.php' ? 'active' : '' ?>"> ID</a>
        <a href="Modifie_ISBN_general.php" class="<?= $current_page == 'Modifie_ISBN_general.php' ? 'active' : '' ?>">Via ISBN</a>
    </div>
    <a href="messages.php" class="<?= $current_page == 'messages.php' ? 'active' : '' ?>">Contacts</a>

</div>

<!-- Bootstrap JS and dependencies (optional) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
