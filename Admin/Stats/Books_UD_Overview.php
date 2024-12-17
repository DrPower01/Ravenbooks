<?php
include('Check_Admin.php'); 

// Database connection
$host = 'localhost';
$dbname = 'library';
$username = 'root';
$password = 'nigga'; // Change this password as needed for security
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Missing data counts for Books table
$missingQueries = [
    'missingTitle' => "SELECT COUNT(*) FROM Books WHERE title IS NULL OR title = ''",
    'missingAuthors' => "SELECT COUNT(*) FROM Books WHERE authors IS NULL OR authors = ''",
    'missingPublisher' => "SELECT COUNT(*) FROM Books WHERE publisher IS NULL OR publisher = ''",
    'missingPublishedDate' => "SELECT COUNT(*) FROM Books WHERE publishedDate IS NULL OR publishedDate = ''",
    'missingDescription' => "SELECT COUNT(*) FROM Books WHERE description IS NULL OR description = ''",
    'missingPageCount' => "SELECT COUNT(*) FROM Books WHERE pageCount IS NULL OR pageCount = 0",
    'missingCategories' => "SELECT COUNT(*) FROM Books WHERE categories IS NULL OR categories = ''",
    'missingLanguage' => "SELECT COUNT(*) FROM Books WHERE language IS NULL OR language = ''",
    'missingISBN' => "SELECT COUNT(*) FROM Books WHERE isbn IS NULL OR isbn = ''",
    'missingShelf' => "SELECT COUNT(*) FROM Books WHERE Shelf IS NULL OR Shelf = ''",
    'missingLocalisation' => "SELECT COUNT(*) FROM Books WHERE Localisation IS NULL OR Localisation = ''",
    'missingCoverURL' => "SELECT COUNT(*) FROM Books WHERE cover_url IS NULL OR cover_url = ''",
];

$data = [];
foreach ($missingQueries as $key => $query) {
    $data[$key] = $pdo->query($query)->fetchColumn();
}

// Books by section
$sectionsQuery = "SELECT Shelf, COUNT(*) AS count FROM Books GROUP BY Shelf";
$sections = $pdo->query($sectionsQuery)->fetchAll(PDO::FETCH_ASSOC);

$labels = array_column($sections, 'Shelf');
$dataSection = array_column($sections, 'count');

// Books added in the last 30 days
$booksAddedQuery = "
    SELECT DATE(added_at) AS add_date, COUNT(*) AS book_count
    FROM Books
    WHERE added_at >= CURDATE() - INTERVAL 30 DAY
    GROUP BY add_date
    ORDER BY add_date ASC
";
$booksAdded = $pdo->query($booksAddedQuery)->fetchAll(PDO::FETCH_ASSOC);

$bookData = [
    'dates' => array_column($booksAdded, 'add_date'),
    'counts' => array_column($booksAdded, 'book_count'),
];

// Top 10 most viewed books
$topBooksQuery = "
    SELECT title, views
    FROM Books
    ORDER BY views DESC 
    LIMIT 10
";
$topBooks = $pdo->query($topBooksQuery)->fetchAll(PDO::FETCH_ASSOC);

$bookTitles = array_column($topBooks, 'title');
$bookViews = array_column($topBooks, 'views');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Information Overview</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        
        .main-content {
            margin: 20px auto;
            padding: 20px;
            max-width: 1200px;
        }
        .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .chart-container > div {
            width: 45%;
            margin: 10px;
        }
        canvas {
            max-width: 100%;
            height: auto;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .overview-box {
            margin: 10px 0;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 8px;
            font-size: 1.2em;
        }
        .overview-box ul {
            list-style-type: none;
            padding: 0;
        }
        .overview-box ul li {
            margin: 8px 0;
            font-size: 1em;
        }
        .overview-box .highlight {
            font-weight: bold;
            color: #ff6347;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h2>Books Information Overview</h2>
        
        <!-- Missing Data Overview Section -->
        <div class="overview-box">
            <h3>Missing Data Overview</h3>
            <ul>
                <li>Missing Titles: <?php echo $data['missingTitle']; ?></li>
                <li>Missing Authors: <?php echo $data['missingAuthors']; ?></li>
                <li>Missing Publishers: <?php echo $data['missingPublisher']; ?></li>
                <li>Missing Published Dates: <?php echo $data['missingPublishedDate']; ?></li>
                <li>Missing Descriptions: <?php echo $data['missingDescription']; ?></li>
                <li>Missing Page Counts: <?php echo $data['missingPageCount']; ?></li>
                <li>Missing Categories: <?php echo $data['missingCategories']; ?></li>
                <li>Missing Languages: <?php echo $data['missingLanguage']; ?></li>
                <li>Missing ISBNs: <?php echo $data['missingISBN']; ?></li>
                <li>Missing Shelves: <?php echo $data['missingShelf']; ?></li>
                <li>Missing Localisations: <?php echo $data['missingLocalisation']; ?></li>
                <li class="highlight">Missing Covers: <?php echo $data['missingCoverURL']; ?></li>
            </ul>
        </div>

        <div class="chart-container">
            <div>
                <h3>Missing Data Overview</h3>
                <canvas id="missingDataChart"></canvas>
            </div>
            <div>
                <h3>Books by Shelf</h3>
                <canvas id="sectionChart"></canvas>
            </div>
        </div>
        <h3>Books Added by Day (Last 30 Days)</h3>
        <canvas id="dailyBooksChart"></canvas>
        <h3>Top 10 Most Viewed Books</h3>
        <canvas id="topBooksChart"></canvas>
    </div>

    <script>
        // Data for charts
        const data = <?php echo json_encode($data); ?>;
        const sectionData = <?php echo json_encode(['labels' => $labels, 'data' => $dataSection]); ?>;
        const bookData = <?php echo json_encode($bookData); ?>;
        const topBooksData = <?php echo json_encode(['titles' => $bookTitles, 'views' => $bookViews]); ?>;

        // Missing Data Chart
        new Chart(document.getElementById('missingDataChart'), {
            type: 'bar',
            data: {
                labels: [
                    'Missing Titles', 'Missing Authors', 'Missing Publishers', 
                    'Missing Published Dates', 'Missing Descriptions', 
                    'Missing Page Counts', 'Missing Categories', 
                    'Missing Languages', 'Missing ISBNs', 
                    'Missing Shelves', 'Missing Localisations', 
                    'Missing Covers'
                ],
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: ['#ff9999', '#ffcc99', '#ffff99', '#99ccff', '#99ff99', '#ff6347', '#ffb3e6', '#c2c2f0', '#ffb347', '#ff6666', '#80ff80', '#ff8c1a']
                }]
            }
        });

        // Books by Section Chart
        new Chart(document.getElementById('sectionChart'), {
            type: 'pie',
            data: {
                labels: sectionData.labels,
                datasets: [{
                    data: sectionData.data,
                    backgroundColor: ['#99ccff', '#6699ff', '#3366cc', '#66ffcc', '#99ff66', '#ffcc99', '#ff9999']
                }]
            }
        });

        // Daily Books Added Chart
        new Chart(document.getElementById('dailyBooksChart'), {
            type: 'line',
            data: {
                labels: bookData.dates,
                datasets: [{
                    label: 'Books Added',
                    data: bookData.counts,
                    borderColor: '#669900',
                    fill: false
                }]
            }
        });

        // Top 10 Most Viewed Books Chart
        new Chart(document.getElementById('topBooksChart'), {
            type: 'bar',
            data: {
                labels: topBooksData.titles,
                datasets: [{
                    label: 'Views',
                    data: topBooksData.views,
                    backgroundColor: '#99ccff'
                }]
            }
        });
    </script>
</body>
</html>
