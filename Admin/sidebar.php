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
    .sidebar a:hover {
        background-color: #575757;
    }
    .submenu {
        padding-left: 20px; /* Indentation for submenu */
    }
    .submenu a {
        font-size: 16px;
        padding: 5px 30px; /* Submenu link padding */
    }
    .submenu a:hover {
        background-color: #575757;
    }
</style>

<!-- sidebar.php -->
<div class="sidebar">
    <h2 style="text-align: center;">Dashboard</h2>
    <a href="Redirect_Home.php">Home</a>
    <a href="Books_Overview_general.php">Books Overview</a>
    
    <!-- Submenu -->
    <a href="#">Add Books</a>
    <div class="submenu">
        <a href="Ajoute_ISBN_generale.php">Via ISBN</a>
        <a href="Ajoute_csv_generale.php">Via CSV</a>
        <a href="Ajoute_manuel.php">Manually</a>
    </div>
    
    <a href="#">Delete Books</a>
    <div class="submenu">
        <a href="Supprime_ID_general.php"> ID</a>
        <a href="Supprime_ISBN_general.php">Via ISBN</a>
    </div>
    <a href="#">Modify Books</a>
    <div class="submenu">
        <a href="Modifie_ID_general.php"> ID</a>
        <a href="Modifie_ISBN_general.php">Via ISBN</a>
    </div>
    <a href="logout.php">Logout</a>
</div>

<!-- Bootstrap JS and dependencies (optional) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
