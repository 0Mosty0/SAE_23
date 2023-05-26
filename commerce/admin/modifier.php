<?php
// Vérifier si l'utilisateur est connecté
session_start();

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['id'])) {
    header("Location: /commerce/login/login.php");
    exit;
}

// Vérifier les informations d'identification de l'utilisateur
if ($_SESSION['id'] !== '24') {
    header('Location: /commerce/login/login.php'); // Rediriger vers la page de connexion si les informations d'identification sont incorrectes
    exit;
}

// Vérifier si une suppression d'article a été demandée
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // Informations de connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "articles2";

    // Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier si la connexion a échoué
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Supprimer l'article de la base de données
    $deleteSql = "DELETE FROM articles WHERE article_id = $deleteId";
    if ($conn->query($deleteSql) === TRUE) {
        echo "L'article a été supprimé avec succès.";
    } else {
        echo "Erreur lors de la suppression de l'article : " . $conn->error;
    }

    // Fermer la connexion à la base de données
    $conn->close();
}

// Vérifier si une modification d'article a été demandée
if (isset($_GET['edit_id'])) {
    $editId = $_GET['edit_id'];

    // Informations de connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "articles2";

    // Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier si la connexion a échoué
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Récupérer les informations de l'article à modifier
    $editSql = "SELECT * FROM articles WHERE article_id = $editId";
    $result = $conn->query($editSql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $nom = $row['nom'];
        $description = $row['description'];
        $prix = $row['prix'];
        $stock = $row['stock'];
        $image = $row['image'];

        // Vérifier si le formulaire de modification a été soumis
        if (isset($_POST['modifier'])) {
            // Récupérer les données modifiées du formulaire
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $stock = $_POST['stock'];
            $image = $_POST['image'];

            // Préparer et exécuter la requête de mise à jour de l'article
            $updateSql = "UPDATE articles SET nom = '$nom', description = '$description', prix = '$prix', stock = '$stock', image = '$image' WHERE article_id = $editId";
            if ($conn->query($updateSql) === TRUE) {
                echo "L'article a été modifié avec succès.";
            } else {
                echo "Erreur lors de la modification de l'article : " . $conn->error;
            }
        }

        // Afficher le formulaire de modification de l'article
        echo "<h2>Modifier l'article</h2>";
        echo "<form method='post' action='{$_SERVER['PHP_SELF']}?edit_id=$editId'>";
        echo "<label for='nom'>Nom :</label>";
        echo "<input type='text' id='nom' name='nom' value='$nom' required><br>";
        echo "<label for='description'>Description :</label>";
        echo "<textarea id='description' name='description' required>$description</textarea><br>";
        echo "<label for='prix'>Prix :</label>";
        echo "<input type='number' id='prix' name='prix' step='0.01' value='$prix' required><br>";
        echo "<label for='stock'>Stock :</label>";
        echo "<input type='number' id='stock' name='stock' value='$stock' required><br>";
        echo "<label for='image'>Image :</label>";
        echo "<input type='text' id='image' name='image' value='$image' required><br>";
        echo "<input type='submit' name='modifier' value='Modifier l'article'>";
        echo "</form>";
    } else {
        echo "L'article à modifier n'a pas été trouvé.";
    }

    // Fermer la connexion à la base de données
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ajouter un article - Admin</title>
</head>
<body>
    <h1>Ajouter un nouvel article</h1>

    <?php
    // Vérifier si le formulaire a été soumis
    if (isset($_POST['ajouter'])) {
        // Récupérer les données du formulaire
        $nom = $_POST['nom'];
        $description = $_POST['description'];
        $prix = $_POST['prix'];
        $stock = $_POST['stock'];
        $image = $_POST['image'];

        // Informations de connexion à la base de données
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "articles2";

        // Connexion à la base de données
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Vérifier si la connexion a échoué
        if ($conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $conn->connect_error);
        }

        // Préparer et exécuter la requête d'insertion
        $sql = "INSERT INTO articles (nom, description, prix, stock, image)
                VALUES ('$nom', '$description', '$prix', '$stock', '$image')";

        if ($conn->query($sql) === TRUE) {
            echo "L'article a été ajouté avec succès.";
        } else {
            echo "Erreur lors de l'ajout de l'article : " . $conn->error;
        }

        // Fermer la connexion à la base de données
        $conn->close();
    }
    ?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required><br>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="prix">Prix :</label>
        <input type="number" id="prix" name="prix" step="0.01" required><br>

        <label for="stock">Stock :</label>
        <input type="number" id="stock" name="stock" required><br>

        <label for="image">Image :</label>
        <input type="text" id="image" name="image" required><br>

        <input type="submit" name="ajouter" value="Ajouter l'article">
    </form>

    <h1>Liste des articles</h1>

    <?php
    // Informations de connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "articles2";

    // Connexion à la base de données
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifier si la connexion a échoué
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Sélectionner tous les articles de la base de données
    $sql = "SELECT * FROM articles";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Afficher la liste des articles
        while ($row = $result->fetch_assoc()) {
            $articleId = $row['article_id'];
            $nom = $row['nom'];
            $description = $row['description'];
            $prix = $row['prix'];
            $stock = $row['stock'];
            $image = $row['image'];

            echo "<h3>$nom</h3>";
            echo "<p>Description: $description</p>";
            echo "<p>Prix: $prix</p>";
            echo "<p>Stock: $stock</p>";
            echo "<img src='$image' alt='$nom' /><br>";
            echo "<a href='?edit_id=$articleId'>Modifier</a> | <a href='?delete_id=$articleId'>Supprimer</a>";
            echo "<hr>";
        }
    } else {
        echo "Aucun article trouvé dans la base de données.";
    }

    // Fermer la connexion à la base de données
    $conn->close();
    ?>
</body>
</html>
