<?php
// Informations de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "articles2";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier si la connexion a échoué
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Requête SQL pour récupérer les informations des articles
$sql = "SELECT * FROM articles";

// Exécuter la requête SQL
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock des articles</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Stock des articles</h1>

    <table>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Prix</th>
            <th>Stock</th>
            <th>Image</th>
        </tr>

        <?php
        // Vérifier si des enregistrements ont été trouvés
        if ($result->num_rows > 0) {
            // Afficher les informations des articles
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['nom'] . "</td>";
                echo "<td>" . $row['description'] . "</td>";
                echo "<td>" . $row['prix'] . "</td>";
                echo "<td>" . $row['stock'] . "</td>";
                echo "<td><img src='" . $row['image'] . "' alt='Image de l'article' width='100'></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucun article trouvé.</td></tr>";
        }
        ?>

    </table>

</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
