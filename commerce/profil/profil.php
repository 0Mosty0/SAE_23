<?php
session_start();

// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inscriptions";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: /commerce/login/login.php");
    exit;
}

// Obtenir les informations personnelles de l'utilisateur
$stmt = $conn->prepare("SELECT prenom, nom, email, dates, ville, codepostal, adresse FROM inscription WHERE id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $prenom = $row['prenom'];
    $nom = $row['nom'];
    $email = $row['email'];
    $dates = $row['dates'];
    $ville = $row['ville'];
    $codepostal = $row['codepostal'];
    $adresse = $row['adresse'];
} else {
    echo "Erreur : Impossible de récupérer les informations de l'utilisateur.";
    exit;
}

// Déconnexion de la session
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /commerce/login/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mon Compte</title>
</head>
<body>
    <h1>Informations Personnelles</h1>
    <p><strong>Prénom:</strong> <?php echo $prenom; ?></p>
    <p><strong>Nom:</strong> <?php echo $nom; ?></p>
    <p><strong>Email:</strong> <?php echo $email; ?></p>
    <p><strong>Date:</strong> <?php echo $dates; ?></p>
    <p><strong>Ville:</strong> <?php echo $ville; ?></p>
    <p><strong>Code Postal:</strong> <?php echo $codepostal; ?></p>
    <p><strong>Adresse:</strong> <?php echo $adresse; ?></p>

    <form method="post" action="">
        <input type="submit" name="logout" value="Déconnexion">
    </form>
</body>
</html>
