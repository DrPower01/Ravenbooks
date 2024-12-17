<?php
include('Check_Admin.php'); 

$server = "localhost";
$username = "root";
$password = "nigga";
$database = "library";

// Connexion à la base de données
$co = new mysqli($server, $username, $password, $database);

// Vérification de la connexion
if ($co->connect_error) {
    die("Connexion échouée : " . $co->connect_error);
}

// Requête pour récupérer les messages
$sql = "SELECT id, name, email, phone, subject, message, submitted_at FROM contact_messages ORDER BY submitted_at DESC";
$result = $co->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages de Contact</title>
    <style>
        /* Reset and global styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100%;
            background-color: #3a6186;
            color: white;
            padding: 1rem;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #fce7e7;
            color: #3a6186;
        }

        /* Main content */
        .content {
            margin-left: 270px; /* Add margin equal to sidebar width + some spacing */
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: #333;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #3a6186;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .empty {
            text-align: center;
            color: #777;
            margin: 20px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php';?>
    <h1>Messages de Contact</h1>

    <!-- Main Content -->
    <div class="content">
        <h1>Messages de Contact</h1>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Sujet</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["id"]); ?></td>
                        <td><?php echo htmlspecialchars($row["name"]); ?></td>
                        <td><?php echo htmlspecialchars($row["email"]); ?></td>
                        <td><?php echo htmlspecialchars($row["phone"]); ?></td>
                        <td><?php echo htmlspecialchars($row["subject"]); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row["message"])); ?></td>
                        <td><?php echo htmlspecialchars($row["submitted_at"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="empty">Aucun message trouvé.</p>
        <?php endif; ?>

        <?php
        // Fermer la connexion
        $co->close();
        ?>
    </div>
</body>
</html>