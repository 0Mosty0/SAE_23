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

// Vérifier si l'utilisateur est connecté à la session
session_start();
if (!isset($_SESSION['id'])) {
    echo "Vous devez vous connecter pour accéder à votre panier.";
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer l'ID du créneau horaire sélectionné
    $creneauId = $_POST["creneau"];

    // Vérifier si le créneau horaire est valide
    $sql = "SELECT * FROM planning WHERE id = '$creneauId' AND disponible = 1";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Le créneau horaire existe et est disponible, donc il peut être réservé

        // Récupérer la date de sélection du créneau horaire
        $sql = "SELECT date_debut FROM planning WHERE id = '$creneauId'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $date_selectionnee = $row["date_debut"];

        // Récupérer les autres données du formulaire
        $user_id = $_SESSION['id'];
        $status = "En attente"; // Statut de la réservation

        // Préparer et exécuter la requête d'insertion
        $sql = "INSERT INTO reservations (id_user, date_reservation, date_selection, statut_reservation) 
                VALUES ('$user_id',  NOW(), '$date_selectionnee', '$status')";

        if ($conn->query($sql) === TRUE) {
            echo "La réservation a été effectuée avec succès.";

            // Mettre à jour la disponibilité du créneau horaire
            $updateSql = "UPDATE planning SET disponible = 0 WHERE id = '$creneauId'";
            $conn->query($updateSql);
        } else {
            echo "Erreur lors de la réservation : " . $conn->error;
        }
    } else {
        echo "Le créneau horaire sélectionné n'est pas valide ou n'est plus disponible.";
    }
}

// Récupérer les créneaux horaires disponibles
$sql = "SELECT * FROM planning WHERE disponible = 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sélection de créneau horaire</title>
</head>
<body>
    <h1>Sélection de créneau horaire</h1>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <h3>Créneaux horaires disponibles :</h3>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $creneauId = $row["id"];
                $dateDebut = $row["date_debut"];
                $dateFin = $row["date_fin"];

                echo "<input type='radio' name='creneau' value='$creneauId'>";
                echo "$dateDebut - $dateFin<br>";
            }
        } else {
            echo "Aucun créneau horaire disponible.";
        }
        ?>
        <br>
        <input type="submit" value="Réserver">
    </form>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
