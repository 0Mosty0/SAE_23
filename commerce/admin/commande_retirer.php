<?php
// Informations de connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inscriptions";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier si la connexion a échoué
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["commande_id"])) {
    // Récupérer l'ID de la commande à valider
    $commandeId = $_POST["commande_id"];

    // Mettre à jour le statut de la commande en "Validé"
    $sql = "UPDATE reservations SET statut_reservation = 'Validé' WHERE id = '$commandeId'";

    if ($conn->query($sql) === TRUE) {
        echo "La commande a été validée avec succès.";
    } else {
        echo "Erreur lors de la validation de la commande : " . $conn->error;
    }
}

// Récupérer toutes les commandes en attente
$sql = "SELECT * FROM reservations WHERE statut_reservation = 'En attente'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des commandes en attente</title>
</head>
<body>
    <h1>Liste des commandes en attente</h1>
    <table>
        <tr>
            <th>ID de la commande</th>
            <th>ID de l'utilisateur</th>
            <th>Date de sélection</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $commandeId = $row["id"];
                $userId = $row["id_user"];
                $dateSelection = $row["date_selection"];
                $statut = $row["statut_reservation"];

                echo "<tr>";
                echo "<td>$commandeId</td>";
                echo "<td>$userId</td>";
                echo "<td>$dateSelection</td>";
                echo "<td>$statut</td>";
                echo "<td>";
                echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "'>";
                echo "<input type='hidden' name='commande_id' value='$commandeId'>";
                echo "<input type='submit' value='Valider'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucune commande en attente.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
