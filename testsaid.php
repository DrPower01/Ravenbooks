<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque - Recherche par Lettre</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container my-4">
        <!-- Titre -->
        <h1 class="text-center mb-4">Bibliothèque - Recherche par Lettre</h1>

        <!-- Conteneur pour les lettres alphabétiques -->
        <div class="alphabet-container d-flex flex-wrap justify-content-center gap-2 mb-4">
            <?php
            foreach (range('A', 'Z') as $letter) {
                echo "<button class='btn btn-primary' onclick=\"filterBooks('$letter')\">$letter</button>";
            }
            ?>
        </div>

        <!-- Conteneur pour les lettres arabes -->
        <div class="alphabet-container d-flex flex-wrap justify-content-center gap-2 mb-4">
            <?php
            $arabic_letters = ['ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'هـ', 'و', 'ي'];
            foreach ($arabic_letters as $letter) {
                echo "<button class='btn btn-success' onclick=\"filterBooks('$letter')\">$letter</button>";
            }
            ?>
        </div>

        <!-- Conteneur pour afficher les livres -->
        <div id="book-container" class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h2 class="card-title mb-0">Liste des livres</h2>
            </div>
            <div class="card-body">
                <ul id="book-list" class="list-group">
                    <!-- Les livres filtrés apparaîtront ici -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Boîte modale pour afficher les détails d'un livre -->
    <div id="modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title" class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Auteurs :</strong> <span id="modal-authors"></span></p>
                    <p><strong>Éditeur :</strong> <span id="modal-publisher"></span></p>
                    <p><strong>Date de publication :</strong> <span id="modal-publishedDate"></span></p>
                    <p><strong>Description :</strong> <span id="modal-description"></span></p>
                    <p><strong>Nombre de pages :</strong> <span id="modal-pageCount"></span></p>
                    <p><strong>Catégories :</strong> <span id="modal-categories"></span></p>
                    <p><strong>Langue :</strong> <span id="modal-language"></span></p>
                    <p><strong>ISBN :</strong> <span id="modal-isbn"></span></p>
                    <p><strong>Rayon :</strong> <span id="modal-shelf"></span></p>
                    <p><strong>Faculté :</strong> <span id="modal-faculte"></span></p>
                </div>
                <!-- <div class="modal-footer">
                    <button class="btn btn-success">Modifier</button>
                    <button class="btn btn-danger">Supprimer</button>
                    <button class="btn btn-secondary">Cacher</button>
                </div> -->
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Fonction pour filtrer les livres par lettre
        function filterBooks(letter) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `letter.php?letter=${letter}`, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('book-list').innerHTML = xhr.responseText;
                } else {
                    console.error('Erreur de récupération des livres');
                }
            };
            xhr.send();
        }

        // Fonction pour afficher les détails d'un livre
        function showBookDetails(bookId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `letter_details.php?id=${bookId}`, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const book = JSON.parse(xhr.responseText);
                    document.getElementById('modal-title').innerText = book.title || 'Titre inconnu';
                    document.getElementById('modal-authors').innerText = book.authors || 'Auteurs inconnus';
                    document.getElementById('modal-publisher').innerText = book.publisher || 'Éditeur inconnu';
                    document.getElementById('modal-publishedDate').innerText = book.publishedDate || 'Date inconnue';
                    document.getElementById('modal-description').innerText = book.description || 'Pas de description disponible';
                    document.getElementById('modal-pageCount').innerText = book.pageCount || 'Non spécifié';
                    document.getElementById('modal-categories').innerText = book.categories || 'Non spécifié';
                    document.getElementById('modal-language').innerText = book.language || 'Langue inconnue';
                    document.getElementById('modal-isbn').innerText = book.isbn || 'Non spécifié';
                    document.getElementById('modal-shelf').innerText = book.shelf || 'Non spécifié';
                    document.getElementById('modal-faculte').innerText = book.faculte || 'Non spécifié';

                    const modal = new bootstrap.Modal(document.getElementById('modal'));
                    modal.show();
                } else {
                    console.error('Erreur de récupération des détails du livre');
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
