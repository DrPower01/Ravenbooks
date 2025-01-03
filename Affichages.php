<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Display</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            padding: 0;
        }
        /* Style for the "Back to Top" button */
        #backToTop {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            padding: 10px;
            font-size: 18px;
            display: none; /* Hidden by default */
            cursor: pointer;
        }

        #backToTop:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Discover</h1>
        <!-- Books Display Content -->
        <div class="p-3">
            <?php include 'Books_Display_UD.php'; ?>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="backToTop" onclick="scrollToTop()">↑</button>

    <!-- Bootstrap JS (with Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript to Show/Hide Back to Top Button -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Show or hide the "Back to Top" button based on scroll position
            const backToTopButton = document.getElementById('backToTop');
            window.addEventListener('scroll', function () {
                if (window.scrollY > 300) {
                    backToTopButton.style.display = 'block';
                } else {
                    backToTopButton.style.display = 'none';
                }
            });
        });

        // Function to scroll the page to the top
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
</body>
</html>
