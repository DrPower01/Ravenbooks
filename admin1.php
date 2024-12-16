<?php
include 'db.php';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Missing data counts
$missingQueries = [
    'missingTitre' => "SELECT COUNT(*) FROM Books_IF WHERE Titre IS NULL OR Titre = ''",
    'missingAuteur' => "SELECT COUNT(*) FROM Books_IF WHERE Auteur_principal IS NULL OR Auteur_principal = ''",
    'missingEditeur' => "SELECT COUNT(*) FROM Books_IF WHERE Editeur IS NULL OR Editeur = ''",
    'missingAnnee' => "SELECT COUNT(*) FROM Books_IF WHERE Annee_edition IS NULL OR Annee_edition = ''",
    'missingISBN' => "SELECT COUNT(*) FROM Books_IF WHERE ISBN IS NULL OR ISBN = '' OR ISBN = '0'",
    'missingCover' => "SELECT COUNT(*) FROM Books_IF WHERE couverture IS NULL OR couverture = ''"  // Missing covers
];

$data = [];
foreach ($missingQueries as $key => $query) {
    $data[$key] = $pdo->query($query)->fetchColumn();
}

// Books by section
$sectionsQuery = "SELECT Section, COUNT(*) AS count FROM Books_IF GROUP BY Section";
$sections = $pdo->query($sectionsQuery)->fetchAll(PDO::FETCH_ASSOC);

$labels = array_column($sections, 'Section');
$dataSection = array_column($sections, 'count');

// Books added in the last 30 days
$booksAddedQuery = "
    SELECT DATE(added_at) AS add_date, COUNT(*) AS book_count
    FROM Books_IF
    WHERE added_at >= CURDATE() - INTERVAL 30 DAY
    GROUP BY add_date
    ORDER BY add_date ASC
";
$booksAdded = $pdo->query($booksAddedQuery)->fetchAll(PDO::FETCH_ASSOC);

$bookData = [
    'dates' => array_column($booksAdded, 'add_date'),
    'counts' => array_column($booksAdded, 'book_count')
];

// Top 10 most viewed books
$topBooksQuery = "
    SELECT Titre, Vue 
    FROM Books_IF 
    ORDER BY Vue DESC 
    LIMIT 10
";
$topBooks = $pdo->query($topBooksQuery)->fetchAll(PDO::FETCH_ASSOC);

$bookTitles = array_column($topBooks, 'Titre');
$bookVue = array_column($topBooks, 'Vue');
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
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
        .chart-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .chart-container > canvas {
            max-width: 45%;
        }
        h2 {
            color: #333;
        }
        h3 {
            color: #555;
        }

        .overview-box {
            margin: 10px 0;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 8px;
            font-size: 1.2em;
        }

        .overview-box h4 {
            margin: 0;
            font-size: 1.5em;
            color: #333;
        }
        .overview-box .cover-count {
            font-weight: bold;
            color: #ff6347;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2>Books Information Overview</h2>
        
        <!-- Missing Data Overview Section -->
        <div class="overview-box">
            <h4>Missing Data Overview</h4>
            <ul>
                <li>Missing Titles: <?php echo $data['missingTitre']; ?></li>
                <li>Missing Authors: <?php echo $data['missingAuteur']; ?></li>
                <li>Missing Publishers: <?php echo $data['missingEditeur']; ?></li>
                <li>Missing Year of Edition: <?php echo $data['missingAnnee']; ?></li>
                <li>Missing ISBN: <?php echo $data['missingISBN']; ?></li>
                <li><span class="cover-count">Missing Covers: <?php echo $data['missingCover']; ?></span></li> <!-- Added Missing Covers Count -->
            </ul>
        </div>

        <div class="chart-container">
            <div>
                <h3>Missing Data Overview</h3>
                <canvas id="booksChart"></canvas>
            </div>
            <div>
                <h3>Books by Section</h3>
                <canvas id="sectionChart"></canvas>
            </div>
        </div>
        <h3>Books Added by Day (Last 30 Days)</h3>
        <canvas id="dailyBooksChart"></canvas>
        <h3>Top 10 Most Viewed Books</h3>
        <canvas id="topBooksChart"></canvas>
    </div>

    <script>
        const data = <?php echo json_encode($data); ?>;
        const sectionData = <?php echo json_encode(['labels' => $labels, 'data' => $dataSection]); ?>;
        const bookData = <?php echo json_encode($bookData); ?>;
        const topBooksData = <?php echo json_encode(['titles' => $bookTitles, 'Vue' => $bookVue]); ?>;

        new Chart(document.getElementById('booksChart'), {
            type: 'bar',
            data: {
                labels: ['Missing Title', 'Missing Author', 'Missing Publisher', 'Missing Year', 'Missing ISBN', 'Missing Cover'],
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: ['#ff9999', '#ffcc99', '#ffff99', '#99ccff', '#99ff99', '#ff6347'],
                    borderColor: '#333',
                }]
            }
        });

        new Chart(document.getElementById('sectionChart'), {
            type: 'pie',
            data: {
                labels: sectionData.labels,
                datasets: [{
                    data: sectionData.data,
                    backgroundColor: ['#99ccff', '#6699ff', '#3366cc', '#66ffcc', '#99ff66'],
                }]
            }
        });

        new Chart(document.getElementById('dailyBooksChart'), {
            type: 'line',
            data: {
                labels: bookData.dates,
                datasets: [{
                    data: bookData.counts,
                    borderColor: '#669900',
                    fill: false
                }]
            }
        });

        new Chart(document.getElementById('topBooksChart'), {
            type: 'bar',
            data: {
                labels: topBooksData.titles,
                datasets: [{
                    data: topBooksData.Vue,
                    backgroundColor: ['#99ccff', '#6699ff', '#3366cc', '#66ffcc', '#99ff66'],
                }]
            }
        });
    </script>
</body>
</html>
