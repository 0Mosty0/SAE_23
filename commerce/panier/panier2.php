<?php
// Démarrer la session
session_start();


if (!isset($_SESSION['id'])) {
    echo "Vous devez vous connecter pour ajouter un article au panier";
    header("Location: /commerce/login/login.php");
    exit();
  }



// Connexion à la base de données article
$conn = new mysqli('localhost', 'root', '', 'article2');

// Vérifier la connexion
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


// Récupérer les informations de l'article
$id_article = $_POST['id_article'];
$sql = "SELECT * FROM articles WHERE article_id = $id_article";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $article_name = $row['nom'];
  $article_price = $row['prix'];
  $article_description = $row['description'];
  $image = $row['image'];
}

// Récupérer la quantité depuis le formulaire HTML
$nombre_article = $_POST['nombre_article'];

// Calculer le prix total
$prix_total = $article_price * $nombre_article;

// 2ème connexion à la base inscription
$conn = new mysqli('localhost', 'root', '', 'inscriptions');

// Vérifier la connexion
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


// Récupérer l'ID utilisateur de la session
$user_id = $_SESSION['id'];

// Récupérer la date actuelle
$date_ajout = date('Y-m-d H:i:s');

// Vérifier si l'article existe déjà dans le panier
$sql = "SELECT * FROM panier WHERE id_session = $user_id AND id_article = $id_article";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // L'article existe déjà dans le panier, mettre à jour la quantité et le prix total
  $row = $result->fetch_assoc();
  $quantite_existant = $row['quantite'];
  $prix_existant = $row['prix'];
  $quantite_nouvelle = $quantite_existant + $nombre_article;
  $prix_nouveau = $prix_existant + $prix_total;
  $sql = "UPDATE panier SET quantite = $quantite_nouvelle, prix = $prix_nouveau WHERE id_session = $user_id AND id_article = $id_article";
} else {
  // L'article n'existe pas dans le panier, l'ajouter
  $sql = "INSERT INTO panier (id_session, id_article, nom_article, prix, description, quantite, date_ajout, image_article) VALUES ('$user_id', '$id_article', '$article_name', '$prix_total', '$article_description', '$nombre_article', '$date_ajout', '$image')";
}

if ($conn->query($sql) === TRUE) {
  echo "Article ajouté au panier avec succès";
} else {
  echo "Erreur lors de l'ajout de l'article au panier: " . $conn->error;
}

// Fermer la connexion à la base de données
$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Panier</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Fonction pour mettre à jour le panier via une requête AJAX
    function updatePanier(article_id, quantite) {
      $.ajax({
        url: "update_panier.php",
        method: "POST",
        data: {
          article_id: article_id,
          quantite: quantite
        },
        success: function(response) {
          // Mettre à jour l'affichage du panier
          $("#panier").html(response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(textStatus, errorThrown);
        }
      });
    }

    // Fonction pour ajouter un article au panier
    function ajouterAuPanier(article_id) {
      var quantite = parseInt($("#quantite-" + article_id).val());
      if (!isNaN(quantite) && quantite > 0) {
        updatePanier(article_id, quantite);
      }
    }

    // Fonction pour supprimer un article du panier
    function supprimerDuPanier(article_id) {
      updatePanier(article_id, 0);
    }
  </script>
</head>
<body>
  <h1>Mon panier</h1>

  <?php

  // Vérifier si l'utilisateur est connecté
  if (!isset($_SESSION['id'])) {
    echo "Vous devez vous connecter pour accéder au panier.";
    header("Location: /commerce/login/login.php");
    exit();
  }

  // Connexion à la base de données
  $conn = new mysqli('localhost', 'root', '', 'inscriptions');

  // Vérifier la connexion
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Récupérer l'ID utilisateur de la session
  $user_id = $_SESSION['id'];

  // Récupérer les articles du panier pour cet utilisateur
  $sql = "SELECT * FROM panier WHERE id_session = '$user_id'";
  $result = $conn->query($sql);

  // Si le panier est vide
  if ($result->num_rows == 0) {
    echo "Votre panier est vide.";
  } else {
    // Afficher les articles
    while($row = $result->fetch_assoc()) {
      echo "<div>";
      echo "<h2>" . $row['nom_article'] . "</h2>";
      echo "<p>Prix unitaire : " . $row['prix'] / $row['quantite'] . "</p>";
      echo "<form method='post' action='update_panier.php'>";
      echo "<input type='hidden' name='article_id' value='" . $row['id_article'] . "'/>";
      echo "<p>Quantité : ";
      echo "<button type='submit' name='action' value='remove'>-</button>";
      echo "<input type='number' name='quantite' min='1' value='" . $row['quantite'] . "'/>";
      echo "<button type='submit' name='action' value='add'>+</button>";
      echo "</p>";
      echo "<p>Prix total : " . $row['prix'] . "</p>";
      echo "<p>Description : " . $row['description'] . "</p>";
      echo "<img src='" . $row['image_article'] . "' alt='" . $row['nom_article'] . "' />";
      echo "</form>";
      echo "</div>";
    }

    // Afficher le total de la commande
    $sql = "SELECT SUM(prix) AS total FROM panier WHERE id_session = '$user_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $total = $row['total'];
    echo "<p>Total de la commande : " . $total . "</p>";
  }

  // Fermer la connexion à la base de données
  $conn->close();
  ?>

</body>
</html>


