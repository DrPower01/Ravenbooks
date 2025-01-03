<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "nigga";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to find duplicate books by ISBN and biblio
$sql = "SELECT isbn, biblio, title, authors, COUNT(*) as count 
    FROM Books 
    WHERE isbn IS NOT NULL AND isbn != ''
    GROUP BY isbn, biblio 
    HAVING count > 1
    ORDER BY count DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h1>Duplicate Books by ISBN and Library (Biblio)</h1>";
    echo "<style>
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid black;
            }
            th, td {
                padding: 15px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
          </style>";
    echo "<table>
            <tr>
                <th>ISBN</th>
                <th>Biblio</th>
                <th>Title</th>
                <th>Authors</th>
                <th>Count</th>
                <th>Action</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        $isbn = $row["isbn"];
        $biblio = $row["biblio"];
        $ids_sql = "SELECT id FROM Books WHERE isbn = '$isbn' AND biblio = '$biblio' ORDER BY id ASC";
        $ids_result = $conn->query($ids_sql);
        $ids = [];
        while ($id_row = $ids_result->fetch_assoc()) {
            $ids[] = $id_row["id"];
        }
        $ids_list = implode(", ", $ids); // IDs of duplicates
        echo "<tr>
                <td>" . $row["isbn"] . "</td>
                <td>" . $row["biblio"] . "</td>
                <td>" . $row["title"] . "</td>
                <td>" . $row["authors"] . "</td>
                <td>" . $row["count"] . "</td>
                <td><button onclick=\"showModal('$isbn', '$biblio', '$ids_list')\">Show</button></td>
              </tr>";
    }
    echo "</table>";
    echo "<div id='myModal' class='modal'>
            <div class='modal-content'>
                <span class='close'>&times;</span>
                <p id='modalContent'></p>
            </div>
          </div>";
    echo "<script>
            function showModal(isbn, biblio, ids) {
                document.getElementById('modalContent').innerText = 
                    'ISBN: ' + isbn + '\\nBiblio: ' + biblio + '\\nDuplicate IDs: ' + ids;
                document.getElementById('myModal').style.display = 'block';
            }
            var modal = document.getElementById('myModal');
            var span = document.getElementsByClassName('close')[0];
            span.onclick = function() {
                modal.style.display = 'none';
            }
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
          </script>";
} else {
    echo "No duplicate books found.";
}

$conn->close();
?>
