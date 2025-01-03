<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Book Search</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .search-bar {
            margin-top: 4rem;
            text-align: center;
        }

        .search-container {
            position: relative;
            text-align: right;
        }

        .search-container input {
            padding: 1rem;
            width: 100%;
            border: 2px solid #ff6f61;
            border-radius: 50px;
            font-size: 1rem;
        }

        .search-container .list-group {
            position: absolute;
            top: 100%;
            right: 0;
            width: 100%;
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
</body>
</html>
