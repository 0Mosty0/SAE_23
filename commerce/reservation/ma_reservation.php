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


session_start();
// Vérifier si la personne est connectée (exemple : utilisateur avec ID 1)
$userId = $_SESSION['id']; // Remplacer par l'ID de la personne connectée

// Récupérer les réservations de la personne connectée
$sql = "SELECT * FROM reservations WHERE id_user = '$userId'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h1>Réservations de la personne connectée</h1>";
    echo "<table>";
    echo "<tr>";
    echo "<th>ID de la commande</th>";
    echo "<th>Date de réservation</th>";
    echo "<th>Date de sélection</th>";
    echo "<th>Statut</th>";
    echo "</tr>";

    while ($row = $result->fetch_assoc()) {
        $commandeId = $row["id"];
        $dateReservation = $row["date_reservation"];
        $dateSelection = $row["date_selection"];
        $statut = $row["statut_reservation"];

        echo "<tr>";
        echo "<td>$commandeId</td>";
        echo "<td>$dateReservation</td>";
        echo "<td>$dateSelection</td>";
        echo "<td>$statut</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Aucune réservation pour la personne connectée.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
