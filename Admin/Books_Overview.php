<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('Check_Admin.php'); 
include('sidebar.php'); 
// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga"; // Replace with your actual password
$dbname = "library"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch data from database
function fetchData($conn, $sql) {
    $result = $conn->query($sql);
    $data = [];

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

// Query to get the count of NULL fields for each column
$sql_null_counts = "
    SELECT 
        SUM(title IS NULL) AS title_null_count,
        SUM(authors IS NULL) AS authors_null_count,
        SUM(publisher IS NULL) AS publisher_null_count,
        SUM(publishedDate IS NULL) AS publishedDate_null_count,
        SUM(description IS NULL) AS description_null_count,
        SUM(pageCount IS NULL) AS pageCount_null_count,
        SUM(categories IS NULL) AS categories_null_count,
        SUM(language IS NULL) AS language_null_count,
        SUM(isbn IS NULL) AS isbn_null_count,
        SUM(Localisation IS NULL) AS Localisation_null_count,
        SUM(cover_url IS NULL) AS cover_url_null_count
    FROM Books
";

$null_counts_data = fetchData($conn, $sql_null_counts);

$fields = [];
$null_counts = [];

if (!empty($null_counts_data)) {
    foreach ($null_counts_data[0] as $field => $null_count) {
        $field_name = ucfirst(str_replace('_null_count', '', $field));
        $fields[] = $field_name;
        $null_counts[] = $null_count;
    }
}

// Query to get the most viewed books
$sql_most_viewed = "
    SELECT title, views
    FROM Books
    ORDER BY views DESC
    LIMIT 10
";

$most_viewed_data = fetchData($conn, $sql_most_viewed);

$most_viewed_titles = array_column($most_viewed_data, 'title');
$most_viewed_counts = array_column($most_viewed_data, 'views');

// Query to get the most liked books
$sql_most_liked = "
    SELECT title, likes
    FROM Books
    ORDER BY likes DESC
    LIMIT 10
";

$most_liked_data = fetchData($conn, $sql_most_liked);

$most_liked_titles = array_column($most_liked_data, 'title');
$most_liked_counts = array_column($most_liked_data, 'likes');

// Query to get the number of books added per day
$sql_books_per_day = "
    SELECT DATE(added_at) as date, COUNT(*) as count
    FROM Books
    GROUP BY added_at
    ORDER BY added_at DESC
";

$books_per_day_data = fetchData($conn, $sql_books_per_day);

$books_per_day_dates = array_column($books_per_day_data, 'date');
$books_per_day_counts = array_column($books_per_day_data, 'count');

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Books Overview - Stats</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
        }
        .sidebar {
            width: 250px;
            /* Adjust the width of the sidebar as needed */
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 250px; /* Adjust this margin to match the sidebar width */
        }
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .chart-item {
            width: 48%;
            margin-bottom: 20px;
        }
        .scrollable {
            overflow-x: auto;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <?php include('sidebar.php'); ?>
    </div>
    <div class="main-content">
        <h1>Books Overview - Stats</h1>
        <div class="chart-container">
            <div class="chart-item">
                <h2>Null Fields Stats</h2>
                <canvas id="nullFieldsChart" width="200" height="200"></canvas>
            </div>
            <div class="chart-item">
                <h2>Most Viewed Books</h2>
                <canvas id="mostViewedChart" width="200" height="200"></canvas>
            </div>
            <div class="chart-item">
                <h2>Most Liked Books</h2>
                <canvas id="mostLikedChart" width="200" height="200"></canvas>
            </div>
            <div class="chart-item scrollable">
                <h2>Books Added Per Day</h2>
                <canvas id="booksPerDayChart" width="400" height="200"></canvas>
            </div>
        </div>

        <div id="chartModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <canvas id="modalChart" width="800" height="400"></canvas>
            </div>
        </div>
    </div>

    <script>
        var ctxNullFields = document.getElementById('nullFieldsChart').getContext('2d');
        var nullFieldsChart = new Chart(ctxNullFields, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($fields); ?>,
                datasets: [{
                    label: 'Null Count',
                    data: <?php echo json_encode($null_counts); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(199, 199, 199, 0.2)',
                        'rgba(83, 102, 255, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        var ctxMostViewed = document.getElementById('mostViewedChart').getContext('2d');
        var mostViewedChart = new Chart(ctxMostViewed, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($most_viewed_titles); ?>,
                datasets: [{
                    label: 'Views',
                    data: <?php echo json_encode($most_viewed_counts); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        var ctxMostLiked = document.getElementById('mostLikedChart').getContext('2d');
        var mostLikedChart = new Chart(ctxMostLiked, {
            type: 'radar',
            data: {
                labels: <?php echo json_encode($most_liked_titles); ?>,
                datasets: [{
                    label: 'Likes',
                    data: <?php echo json_encode($most_liked_counts); ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    r: {
                        beginAtZero: true
                    }
                }
            }
        });

        var ctxBooksPerDay = document.getElementById('booksPerDayChart').getContext('2d');
        var booksPerDayChart = new Chart(ctxBooksPerDay, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($books_per_day_dates); ?>,
                datasets: [{
                    label: 'Books Added',
                    data: <?php echo json_encode($books_per_day_counts); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day'
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Modal handling
        var modal = document.getElementById("chartModal");
        var modalChartCtx = document.getElementById("modalChart").getContext('2d');
        var modalChart;

        function showModal(chart) {
            if (modalChart) {
                modalChart.destroy();
            }
            modalChart = new Chart(modalChartCtx, chart.config);
            modal.style.display = "block";
        }

        document.querySelectorAll('.chart-item canvas').forEach(function(canvas) {
            canvas.addEventListener('click', function() {
                var chart = Chart.getChart(canvas.id);
                showModal(chart);
            });
        });

        var span = document.getElementsByClassName("close")[0];
        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
