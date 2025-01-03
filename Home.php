<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "nigga";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Récupérer les livres les plus vus
$mostViewedBooks = $conn->query("SELECT * FROM Books WHERE cover_url IS NOT NULL ORDER BY views DESC LIMIT 10");

// Récupérer les livres les plus aimés
$mostLikedBooks = $conn->query("SELECT * FROM Books WHERE cover_url IS NOT NULL ORDER BY likes DESC LIMIT 10");

// Récupérer les livres pour le diaporama
$slideshowBooks = $conn->query("SELECT * FROM Books WHERE cover_url IS NOT NULL ORDER BY added_at DESC LIMIT 5");

?>
<!DOCTYPE html>
<html lang="fr">
<?php include('navbar.php'); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RavenBooks</title>
    <style>
        body{
            margin: 0;
        }

        .slideshow-container {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;
            border-radius: 10px;
        }

        .slideshow-container img {
            width: 190px; /* Ajuster la largeur pour rendre les images de la même taille que les livres défilants */
            height: auto;
            border-radius: 10px;
        }

        .slideshow-container .mySlides {
            display: none;
            position: relative;
            text-align: center; /* Centrer les images */
        }

        .slideshow-container .mySlides .blur-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            filter: blur(10px);
            z-index: -1;
        }

        .scrollable-books-container {
            margin: 2rem 0;
        }

        .scrollable-books {
            display: flex;
            overflow-x: auto;
            padding: 1rem;
            gap: 1rem;
        }

        .scrollable-books .book {
            flex: 0 0 auto;
            width: 150px;
            text-align: center;
        }

        .scrollable-books .book img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .scrollable-books .book-title {
            margin-top: 0.5rem;
            font-size: 1rem;
            color: #333;
        }

        .scrollable-books .arrow {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            font-size: 2rem;
            color: #333;
            text-decoration: none;
        }
        footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 1.5rem 0;
            margin-top: 3rem;
        }

        footer a {
            color: #ff6f61;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Style for search bar */
        .search-bar {
            margin: 2rem 0;
            text-align: center;
        }

        .search-container {
            position: relative;
            text-align: right;
        }

        .search-container input {
            padding: 1rem;
            width: 100%;
            max-width: 600px; /* Limit the maximum width */
            border: 2px solid #ff6f61;
            border-radius: 10px;
            font-size: 1rem;
            margin: 0 auto; /* Center the input */
        }

        .search-container .list-group {
            position: absolute;
            top: 100%;
            right: 0;
            width: 100%;
            max-width: 600px; /* Match the input's max-width */
            margin-top: 1rem;
            max-height: 300px; /* Limit the height */
            overflow-y: auto; /* Enable vertical scrolling */
            border-radius: 10px;
            z-index: 1000; /* Ensure it appears above other elements */
            background: white; /* Ensure background is white */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Add shadow for better visibility */
        }

        .list-group-item {
            cursor: pointer;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            display: flex;
            align-items: center;
        }

        .list-group-item img {
            width: 50px;
            height: 75px;
            margin-right: 10px;
        }

        .list-group-item:hover {
            background-color: #ff6f61;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <section class="search-bar">
            <div class="container">
                <div class="row justify-content-end">
                    <div class="col-lg-6">
                        <div class="search-container">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search books by title, author, or ISBN..."
                                id="searchBox"
                            />
                            <ul class="list-group search-results" id="searchResults"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </header>

    <!-- Diaporama -->
    <section class="slideshow-container">
        <?php while ($book = $slideshowBooks->fetch_assoc()): ?>
            <div class="mySlides">
                <div class="blur-background" style="background-image: url('<?php echo $book['cover_url']; ?>');"></div>
                <a href="Books_details_UD.php?id=<?php echo $book['id']; ?>">
                    <img src="<?php echo $book['cover_url']; ?>" alt="<?php echo $book['title']; ?>">
                </a>
            </div>
        <?php endwhile; ?>
    </section>
    <h1>Explorez des Connaissances Infinies</h1>
        <p>Votre porte d'entrée vers un monde de livres, d'histoires et d'apprentissage.</p>
        <a href="Affichages.php" class="btn-primary">Découvrez Maintenant</a>
    <!-- Livres les plus vus -->
    <section class="scrollable-books-container">
        <h2>Livres les Plus Vus</h2>
        <div class="scrollable-books">
            <?php while ($book = $mostViewedBooks->fetch_assoc()): ?>
                <div class="book">
                    <a href="Books_Display_UD.php?id=<?php echo $book['id']; ?>">
                        <img src="<?php echo $book['cover_url']; ?>" alt="<?php echo $book['title']; ?>">
                    </a>
                    <div class="book-title"><?php echo $book['title']; ?></div>
                </div>
            <?php endwhile; ?>
            <a href="Books_Display_UD.php" class="arrow">&rarr;</a>
        </div>
    </section>

    <!-- Livres les plus aimés -->
    <section class="scrollable-books-container">
        <h2>Livres les Plus Aimés</h2>
        <div class="scrollable-books">
            <?php while ($book = $mostLikedBooks->fetch_assoc()): ?>
                <div class="book">
                    <a href="Books_Display_UD.php?id=<?php echo $book['id']; ?>">
                        <img src="<?php echo $book['cover_url']; ?>" alt="<?php echo $book['title']; ?>">
                    </a>
                    <div class="book-title"><?php echo $book['title']; ?></div>
                </div>
            <?php endwhile; ?>
            <a href="Books_Display_UD.php" class="arrow">&rarr;</a>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Bibliothèque en Ligne. Tous droits réservés. | <a href="#">Politique de Confidentialité</a></p>
    </footer>

    <script>
        // JavaScript pour le diaporama
        let slideIndex = 0;
        showSlides();

        function showSlides() {
            let slides = document.getElementsByClassName("mySlides");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) { slideIndex = 1 }
            slides[slideIndex - 1].style.display = "block";
            setTimeout(showSlides, 4000); // Changer d'image toutes les 3 secondes
        }

        // JavaScript pour la recherche en temps réel
        document.getElementById('searchBox').addEventListener('input', function () {
            const query = this.value.trim();
            const resultsContainer = document.getElementById('searchResults');

            // Clear previous results if query is empty
            if (query === '') {
                resultsContainer.innerHTML = '';
                return;
            }

            // Perform AJAX request
            fetch(`search.php?query=${encodeURIComponent(query)}`)
                .then((response) => response.json())
                .then((data) => {
                    resultsContainer.innerHTML = '';

                    if (data.length === 0) {
                        resultsContainer.innerHTML =
                            '<li class="list-group-item no-results">No books found</li>';
                        return;
                    }

                    // Populate results
                    data.forEach((book) => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex align-items-center';
                        li.innerHTML = `
                            <img src="${book.cover_url}" alt="${book.title}">
                            <div>
                                <strong>${book.title}</strong><br>
                                <span class="text-muted">${book.authors}</span>
                            </div>
                        `;
                        li.addEventListener('click', () => {
                            window.location.href = `Booksdetail.php?id=${book.id}`;
                        });
                        resultsContainer.appendChild(li);
                    });
                })
                .catch((error) => {
                    console.error('Error fetching data:', error);
                });
        });
    </script>
</body>
</html>
