<?php
// Démarrer la session
session_start();

// Vérifier si le formulaire a été soumis
if (isset($_POST['submit'])) {
  // Connexion à la base de données
  $connexion = new mysqli("localhost", "root", "", "inscription");

  // Vérifier la connexion
  if ($connexion->connect_error) {
    die("Connexion échouée: " . $connexion->connect_error);
  }

  // Récupérer les données du formulaire
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Échapper les caractères spéciaux pour éviter les injections SQL
  $email = mysqli_real_escape_string($connexion, $email);

  // Rechercher l'utilisateur dans la base de données
  $sql = "SELECT * FROM inscription WHERE email = '$email'";
  $resultat = $connexion->query($sql);

  if ($resultat->num_rows == 1) {
    // L'utilisateur existe, vérifier le mot de passe
    $row = $resultat->fetch_assoc();
    if (password_verify($password, $row['passwords'])) {
      // Le mot de passe est correct, connecter l'utilisateur
      $_SESSION['email'] = $email;
      $_SESSION['nb_tentatives'] = 0;
      $_SESSION['id'] = $row['id']; // enregistrer l'ID de l'utilisateur dans la session
      header('Location: /commerce/index.html');
      exit();
    } else {
      // Le mot de passe est incorrect
      if (isset($_SESSION['nb_tentatives'])) {
        $_SESSION['nb_tentatives']++;
      } else {
        $_SESSION['nb_tentatives'] = 1;
      }
      if ($_SESSION['nb_tentatives'] >= 5) {
        echo "Trop de tentatives de connexion, veuillez réessayer dans 5 minutes.";
        exit();
      } else {
        echo "Le mot de passe ou le login incorrect. Tentative " . $_SESSION['nb_tentatives'] . "/5.";
      }
    }
  } else {
    // L'utilisateur n'existe pas
    if (isset($_SESSION['nb_tentatives'])) {
      $_SESSION['nb_tentatives']++;
    } else {
      $_SESSION['nb_tentatives'] = 1;
    }
    if ($_SESSION['nb_tentatives'] >= 5) {
      echo "Trop de tentatives de connexion, veuillez réessayer dans 5 minutes.";
      exit();
    } else {
      echo "Le mot de passe ou le login incorrect. Tentative " . $_SESSION['nb_tentatives'] . "/5.";
    }
  }

  // Fermer la connexion à la base de données
  $connexion->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
</head>
<body>
  <h1>Connexion</h1>
  <form method="post">
    <div>
      <label for="email">Email :</label>
      <input type="email" name="email" required>
    </div>
    <div>
      <label for="password">Mot de passe :</label>
      <input type="password" name="password" required>
    </div>
    <div>
      <button type="submit" name="submit">Se connecter</button>
    </div>
  </form>
</body>
</html>
