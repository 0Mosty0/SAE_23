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
if (isset($_POST['reserver'])) {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $articles = $_POST['articles'];
    $dateSelectionnee = $_POST['date_selectionnee'];

    // Préparer et exécuter la requête d'insertion pour enregistrer les informations utilisateur
    $sql = "INSERT INTO utilisateurs (nom, email) VALUES ('$nom', '$email')";

    if ($conn->query($sql) === TRUE) {
        // Récupérer l'ID de l'utilisateur nouvellement inséré
        $utilisateurId = $conn->insert_id;

        // Parcourir les articles sélectionnés et les enregistrer dans la table panier avec l'ID de l'utilisateur
        foreach ($articles as $articleId) {
            // Préparer et exécuter la requête d'insertion pour enregistrer les articles dans le panier
            $sql = "INSERT INTO panier (utilisateur_id, article_id, date_selectionnee) VALUES ('$utilisateurId', '$articleId', '$dateSelectionnee')";

            if ($conn->query($sql) !== TRUE) {
                echo "Erreur lors de l'ajout de l'article dans le panier : " . $conn->error;
            }
        }

        echo "La réservation a été enregistrée avec succès.";
    } else {
        echo "Erreur lors de l'enregistrement des informations utilisateur : " . $conn->error;
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réservation</title>
</head>
<body>
    <h1>Réservation</h1>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br>

        <label for="articles">Articles :</label><br>
        <input type="checkbox" id="article1" name="articles[]" value="1">
        <label for="article1">Article 1</label><br>
        <input type="checkbox" id="article2" name="articles[]" value="2">
        <label for="article2">Article 2</label><br>
        <input type="checkbox" id="article3" name="articles[]" value="3">
        <label for="article3">Article 3</label><br>

        <label for="date_selectionnee">Date sélectionnée :</label>
        <input type="date" id="date_selectionnee" name="date_selectionnee" required><br>

        <input type="submit" name="reserver" value="Réserver">
    </form>
</body>
</html>
