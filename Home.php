<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


?>
<!DOCTYPE html>
<html lang="en">
<?php include('navbar.php'); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RavenBooks</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
            background: linear-gradient(to bottom, #830CFA, #d9e8ff);
        }

        header {
            background: linear-gradient(to right, #830CFA, #7286F7);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }

        header h1 {
            font-size: 3.8rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        header p {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            display: inline-block;
            text-decoration: none;
            background: #ff6f61;
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 30px;
            font-size: 1.1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #ff4a36;
            transform: translateY(-2px);
        }

        .search-bar {
            margin-top: 4rem;
            text-align: center;
        }

        .search-bar input {
            padding: 1rem;
            width: 70%;
            max-width: 500px;
            border: 2px solid #ff6f61;
            border-radius: 50px;
            font-size: 1rem;
        }

        .search-bar button {
            padding: 1rem 2rem;
            background: #ff6f61;
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            margin-left: -50px;
        }

        .search-bar button:hover {
            background: #bb1264;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 4rem auto;
            max-width: 1200px;
            padding: 0 2rem;
        }

        .feature-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            text-align: center;
            transition: transform 0.4s;
        }

        .feature-box:hover {
            transform: translateY(-15px);
        }

        .feature-box img {
            width: 100px;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .feature-box h3 {
            margin-bottom: 1rem;
            color: #3a6186;
            font-size: 1.5rem;
        }

        .feature-box p {
            color: #666;
            line-height: 1.5;
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
        .search-bar {
            margin-top: 4rem;
            text-align: center;
        }

        .search-bar input {
            padding: 1rem;
            width: 70%;
            max-width: 500px;
            border: 2px solid #ff6f61;
            border-radius: 50px;
            font-size: 1rem;
        }

        .search-bar button {
            padding: 1rem 2rem;
            background: #ff6f61;
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            cursor: pointer;
            margin-left: -50px;
        }

        .search-bar button:hover {
            background: #bb1264;
        }

        /* Search container styles for the input box and results */
        .search-container {
            margin-top: 4rem;
            text-align: center;
        }

        .search-container input {
            padding: 1rem;
            width: 70%;
            max-width: 500px;
            border: 2px solid #ff6f61;
            border-radius: 50px;
            font-size: 1rem;
        }

        .search-container .list-group {
            margin-top: 1rem;
            width: 70%;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            max-height: 300px; /* Limit the height */
            overflow-y: auto; /* Enable vertical scrolling */
            border-radius: 10px;
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
        <h1>Explore Infinite Knowledge</h1>
        <p>Your gateway to a world of books, stories, and learning.</p>
        <a href="Affichages.php" class="btn-primary">Discover Now</a>
        
    </header>

    <section class="search-bar">
    <div class="container">
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
</section>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script>
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

    <section class="features" id="features">
        <div class="feature-box">
            <img src="https://via.placeholder.com/100" alt="Books">
            <h3>Extensive Library</h3>
            <p>Discover thousands of books across genres and languages.</p>
        </div>
        <div class="feature-box">
            <img src="https://via.placeholder.com/100" alt="Access">
            <h3>Seamless Access</h3>
            <p>Read your favorite books anytime, anywhere, on any device.</p>
        </div>
        <div class="feature-box">
            <img src="https://via.placeholder.com/100" alt="Community">
            <h3>Engage with Community</h3>
            <p>Join discussions, write reviews, and connect with book lovers.</p>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Online Library. All rights reserved. | <a href="#">Privacy Policy</a></p>
    </footer>
</body>
</html>
