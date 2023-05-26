<?php
// Connexion à la base de données
$connexion = new mysqli("localhost", "root", "", "inscriptions");

// Vérifier la connexion
if ($connexion->connect_error) {
  die("Connexion échouée: " . $connexion->connect_error);
}

// Récupérer les données du formulaire
$prenom = $_POST['prenom'];
$nom = $_POST['nom'];
$email = $_POST['email'];
// Vérifier si le mot de passe respecte les critères requis
$passwords = $_POST['passwords'];
if (strlen($passwords) < 8 || !preg_match('/[0-9]/', $passwords) || !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $passwords)) {
    echo "Le mot de passe doit contenir au moins 8 caractères, un chiffre et un caractère spécial.";
    exit();
}
$dates = $_POST['dates'];
$ville = $_POST['ville'];
$codepostal = $_POST['codepostal'];
$adresse = $_POST['adresse'];

// Échapper les caractères spéciaux pour éviter les injections SQL
$prenom = mysqli_real_escape_string($connexion, $prenom);
$nom = mysqli_real_escape_string($connexion, $nom);
$email = mysqli_real_escape_string($connexion, $email);
$passwords = mysqli_real_escape_string($connexion, $passwords);
$dates = mysqli_real_escape_string($connexion, $dates);
$ville = mysqli_real_escape_string($connexion, $ville);
$codepostal = mysqli_real_escape_string($connexion, $codepostal);
$adresse = mysqli_real_escape_string($connexion, $adresse);

// Hachage du mot de passe
$password_hashed = password_hash($passwords, PASSWORD_BCRYPT);

// Insérer les données dans la table d'inscription
$sql = "INSERT INTO inscription (prenom, nom, email, passwords, dates, ville, codepostal, adresse)
        VALUES ('$prenom', '$nom', '$email', '$password_hashed', '$dates', '$ville', '$codepostal', '$adresse')";

if ($connexion->query($sql) === TRUE) {
  echo "Inscription réussie";
  header('Location: /commerce/login/login.php');
} else {
  echo "Erreur: " . $sql . "<br>" . $connexion->error;
}

// Fermer la connexion à la base de données
$connexion->close();
?>
