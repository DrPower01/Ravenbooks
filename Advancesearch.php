
<?php
$servername = "localhost";
$username = "root";
$password = "nigga";
$dbname = "Ravenbooks";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM Books";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Authors</th>
                <th>Publisher</th>
                <th>Published Date</th>
                <th>Description</th>
                <th>Page Count</th>
                <th>Categories</th>
                <th>Language</th>
                <th>ISBN</th>
                <th>Biblio</th>
                <th>Localisation</th>
                <th>Cover URL</th>
                <th>Added At</th>
                <th>Views</th>
                <th>Likes</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["id"]. "</td>
                <td>" . $row["title"]. "</td>
                <td>" . $row["authors"]. "</td>
                <td>" . $row["publisher"]. "</td>
                <td>" . $row["publishedDate"]. "</td>
                <td>" . $row["description"]. "</td>
                <td>" . $row["pageCount"]. "</td>
                <td>" . $row["categories"]. "</td>
                <td>" . $row["language"]. "</td>
                <td>" . $row["isbn"]. "</td>
                <td>" . $row["biblio"]. "</td>
                <td>" . $row["Localisation"]. "</td>
                <td>" . $row["cover_url"]. "</td>
                <td>" . $row["added_at"]. "</td>
                <td>" . $row["views"]. "</td>
                <td>" . $row["likes"]. "</td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
$conn->close();
?>