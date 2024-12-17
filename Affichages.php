<?php include('navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Tabs with Include</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Discover</h1>
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1">Universite de Balballa</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2">Institut Francais</button>
            </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content" id="myTabContent">
            <!-- Tab 1 Content -->
            <div class="tab-pane fade p-3" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                <?php include 'Discover/Books_Display_UD.php'; ?>
            </div>
            <!-- Tab 2 Content -->
            <div class="tab-pane fade p-3" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                <?php include 'Discover/Books_Display_IF.php'; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (with Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>
