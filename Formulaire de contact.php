<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Contact</title>
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

        .form-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .form-container h1 {
            text-align: center;
            color: #3a6186;
            margin-bottom: 20px;
        }

        .form-container label {
            font-size: 16px;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="tel"],
        .form-container textarea,
        .form-container select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            margin-bottom: 20px;
            box-sizing: border-box;
            transition: border 0.3s, box-shadow 0.3s;
        }

        .form-container input:focus,
        .form-container textarea:focus,
        .form-container select:focus {
            border-color: #3a6186;
            box-shadow: 0 0 5px rgba(58, 97, 134, 0.5);
        }

        .form-container textarea {
            resize: vertical;
        }

        .form-container .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .form-container button {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .form-container button[type="submit"] {
            background-color: #3a6186;
            color: white;
        }

        .form-container button[type="reset"] {
            background-color: #ddd;
            color: #333;
        }

        .form-container button:hover {
            transform: scale(1.05);
        }

        .form-container button[type="submit"]:hover {
            background-color: #89253e;
        }

        .form-container button[type="reset"]:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="form-container">
    <h1>Contactez-nous</h1>
    <form action="contact.php" method="POST">
        <label for="name">Nom complet :</label>
        <input type="text" id="name" name="name" placeholder="Entrez votre nom complet" required>

        <label for="email">Adresse email :</label>
        <input type="email" id="email" name="email" placeholder="Entrez votre adresse email" required>

        <label for="phone">Téléphone :</label>
        <input type="tel" id="phone" name="phone" placeholder="Entrez votre numéro de téléphone" required>

        <label for="subject">Sujet :</label>
        <select id="subject" name="subject" required>
            <option value="" disabled selected>Choisissez un sujet</option>
            <option value="support">Support Technique</option>
            <option value="feedback">Retour d'expérience</option>
            <option value="inquiry">Renseignements</option>
            <option value="other">Autre</option>
        </select>

        <label for="message">Message :</label>
        <textarea id="message" name="message" rows="5" placeholder="Écrivez votre message ici..." required></textarea>

        <div class="button-group">
            <button type="submit">Envoyer</button>
            <button type="reset">Annuler</button>
        </div>
    </form>
</div>
</body>
</html>
