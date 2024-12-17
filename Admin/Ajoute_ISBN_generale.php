<?php include('navbar.php'); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Tabs with Include</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
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
        padding: 10px;
        text-decoration: none;
        font-size: 18px;
        transition: background-color 0.3s ease;
    }

    .sidebar a:hover {
        background-color: #575757;
    }

    /* Adding a left margin to the main content to prevent it from being hidden behind the sidebar */
    .main-content {
        margin-left: 260px; /* Sidebar width + some padding */
        padding: 20px;
    }

    .dropdown-menu {
        background-color: #333; /* To match the sidebar */
    }

    .dropdown-item {
        color: white;
    }

    .dropdown-item:hover {
        background-color: #575757; /* Hover effect for dropdown items */
    }
</style>
<body>
<?php include 'sidebar.php';?>

<div class="main-content">
    <h1 class="text-center">Ajouter par ISBN</h1>
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="CSV-UD-tab" data-bs-toggle="tab" data-bs-target="#CSV-UD" type="button" role="tab" aria-controls="CSV-UD">Universite de Balballa</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="CSV_IF-tab" data-bs-toggle="tab" data-bs-target="#CSV_IF" type="button" role="tab" aria-controls="CSV_IF">Institut Francais</button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="myTabContent">
        <!-- Tab 1 Content -->
        <div class="tab-pane fade p-3" id="CSV-UD" role="tabpanel" aria-labelledby="CSV-UD-tab">
            <?php include 'Add/Ajoute_ISBN_UD.php'; ?>
        </div>
        <!-- Tab 2 Content -->
        <div class="tab-pane fade p-3" id="CSV_IF" role="tabpanel" aria-labelledby="CSV_IF-tab">
            <?php require 'Add/Ajoute_ISBN_IF.php'; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<!-- JavaScript to Save Active Tab State -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the active tab from localStorage
        const activeTab = localStorage.getItem('activeTab');
        
        if (activeTab) {
            // Trigger click on the stored tab
            const tabButton = document.querySelector(`[data-bs-target="${activeTab}"]`);
            if (tabButton) tabButton.click();
        }

        // Store the active tab in localStorage on tab change
        const tabButtons = document.querySelectorAll('#myTab button[data-bs-toggle="tab"]');
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', function (event) {
                localStorage.setItem('activeTab', event.target.getAttribute('data-bs-target'));
            });
        });
    });
</script>