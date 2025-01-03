<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        nav {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        nav ul {
            display: flex;
            list-style: none;
            justify-content: center;
        }
        nav ul li {
            margin: 0 1rem;
            position: relative;
        }
        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: color 0.3s;
        }
        nav ul li a:hover {
            color: #3a6186;
        }
        /* Section */
        .hero {
            margin-top: 0px;
            height: calc(100vh - 50px);
            padding: auto 8%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .left {
            font-family: Caladea, 'sans-serif';
            max-width: 800px;
            margin-left: 55px;
        }

        .hero h2 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .hero p {
            font-size: 20px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #34495e;
        }

        .hero .right {
            width: 40%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero .right img {
            width: auto;
            max-width: 85%;
            height: auto;
            border-radius: 10px;
        }

        /* Section 2 */
        .hero.reverse {
            flex-direction: row-reverse;
        }

        /* Nouveau style du footer */
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
    </style>
</head>
<body>
     <!-- Navbar -->
     <?php include('navbar.php'); ?>

    <!-- Premier texte à gauche -->
    <section class="hero">
        <div class="left">
            <h2>Notre Objectif</h2>
            <p>Notre site de bibliothèque en ligne a pour objectif de faciliter la recherche et la localisation des livres. Grâce à une interface intuitive, les utilisateurs peuvent rapidement identifier l'emplacement des ouvrages disponibles, qu'ils soient dans des bibliothèques locales ou des espaces de prêt partenaires. Vous n'avez plus qu'à entrer le titre du livre pour obtenir sa disponibilité et son emplacement, tout cela en un clic.</p>
        </div>
        <div class="right">
            <img src="Raven.jpeg" alt="Image bibliothèque">
        </div>
    </section>

    <!-- Deuxième texte à droite -->
    <section class="hero reverse">
        <div class="left">
            <h2>Pourquoi Nous Avons Créé ce Site ?</h2>
            <p>Le projet a vu le jour d'une simple observation : malgré la grande quantité de livres disponibles, il était souvent difficile pour les lecteurs de localiser facilement les ouvrages qu'ils recherchaient. En combinant la puissance des bases de données avec l'accessibilité des technologies web, nous avons voulu offrir une solution moderne pour optimiser l'accès aux livres, quel que soit l'endroit où vous vous trouvez.</p>
        </div>
        <div class="right">
            <img src="cd.jpg" alt="Image équipe">
        </div>
    </section>

    <!-- Footer : Nouveau style -->
    <footer>
        <p>Ce projet a été réalisé avec passion par l'équipe <strong>RavenBooks</strong>.</p>
        <p>&copy; 2024 Online Library. All rights reserved. | <a href="#">Privacy Policy</a></p>
    </footer>
</body>
</html>
