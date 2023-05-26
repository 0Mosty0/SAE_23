<?php
// Démarrer la session
session_start();

// Vérifier si le formulaire a été soumis
if (isset($_POST['submit'])) {
  // Connexion à la base de données
  $connexion = new mysqli("localhost", "root", "", "inscriptions");

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
      header('Location: /commerce/index.php');
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

<!doctype html>
<html lang="fr">
<head>
  <script src="https://kit.fontawesome.com/93a77cb4e9.js" crossorigin="anonymous"></script>
  <meta charset="utf-8">
  <title>Hopitaux</title>
  <link rel="stylesheet" href="style.css">

</head>
<body>
 
     <div class="connexion">
      <form>
        
       <h1>Se connecter</h1>
       <div class="social-media">
         <p><i class="fab fa-google"></i></p>
         <p><i class="fab fa-youtube"></i></p>
         <p><i class="fab fa-facebook-f"></i></p>
         <p><i class="fab fa-twitter"></i></p>
       </div>
       <p class="choose-email">ou utiliser mon adresse e-mail :</p>
      </form>   
       <div class="inputs">
      <form method="post">
        <div>
         <label for="email">Adresse e-mail :</label><br>
         <input type="email" name="email" required><br>
        </div>
        
        <div>
         <label for="password">Mot de passe :</label><br>
		     <input type="password" name="password" required><br>
        </div>
       
        <p class="inscription">Je n'ai pas de <span>compte</span>. Je m'en <span><a href="/commerce/register/register.html">crée</a></span> un.</p>

        
        <div align="center">
         <button type="submit" name="submit">Se connecter</button>
        </div>
      </form>
    </div>
   
   
</body>
</html>
