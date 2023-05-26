<?php
// Connexion à la base de données (remplacez les valeurs par celles de votre configuration)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inscriptions";
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si l'utilisateur est connecté à la session
session_start();
if (!isset($_SESSION['id'])) {
    echo "Vous devez vous connecter pour accéder à votre panier.";
    exit();
}

// Récupérer l'ID de la session utilisateur
$id_session = $_SESSION['id'];

// Vérifier si une modification de quantité a été soumise
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_article = $_POST['id_article'];
    $quantite = $_POST['quantite'];

    // Vérifier si la quantité est valide
    if ($quantite < 1) {
        echo "La quantité doit être supérieure ou égale à 1.";
    } else {
        // Mettre à jour la quantité dans la base de données
        $updateSql = "UPDATE panier SET quantite = $quantite WHERE id_session = '$id_session' AND id_article = $id_article";
        if ($conn->query($updateSql) === TRUE) {
            echo "La quantité a été mise à jour avec succès.";
        } else {
            echo "Une erreur s'est produite lors de la mise à jour de la quantité : " . $conn->error;
        }
    }
}

// Vérifier si une suppression d'article a été demandée
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // Supprimer l'article du panier dans la base de données
    $deleteSql = "DELETE FROM panier WHERE id_session = '$id_session' AND id_article = $deleteId";
    if ($conn->query($deleteSql) === TRUE) {
        echo "L'article a été supprimé du panier avec succès.";
    } else {
        echo "Une erreur s'est produite lors de la suppression de l'article : " . $conn->error;
    }
}

// Sélectionner tous les articles du panier de l'utilisateur connecté
$sql = "SELECT * FROM panier WHERE id_session = '$id_session'";
$result = $conn->query($sql);

// Vérifier si le panier est vide
if ($result->num_rows === 0) {
    echo "  Votre panier est vide.";
    exit();
}

// Afficher les articles du panier
$totalPrice = 0;
while ($row = $result->fetch_assoc()) {
    $id_article = $row['id_article'];
    $nom_article = $row['nom_article'];
    $quantite = $row['quantite'];
    $prix = $row['prix'];
    $description = $row['description'];
    $image_article = $row['image_article'];

    // Calculer le prix total pour chaque article
    $articlePrice = $prix * $quantite;
    $totalPrice += $articlePrice;

    // Afficher les informations de l'article
    echo "<h3>$nom_article</h3>";
    echo "<p>Quantité: $quantite</p>";
    echo "<p>Prix: $prix €</p>";
    echo "<p>Description: $description</p>";
    echo "<img src='$image_article' alt='$nom_article' />";
    echo "<hr>";

    // Formulaire pour modifier la quantité de l'article
    echo "<form method='post' action=''>";
    echo "<input type='hidden' name='id_article' value='$id_article' />";
    echo "<label>Quantité: </label>";
    echo "<input type='number' name='quantite' value='$quantite' min='1' />";
    echo "<input type='submit' value='Modifier' />";
    echo "</form>";

    // Bouton pour supprimer l'article
    echo "<a href='?delete_id=$id_article'>Supprimer</a>";
}

// Afficher le prix total des articles
echo "<h2>Prix total: $totalPrice €</h2>";

// Fermer la connexion à la base de données
$conn->close();
?>
