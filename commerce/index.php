<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    // Rediriger vers la page de connexion ou afficher un message d'erreur
    header("Location: /commerce/login/login.php");
    exit;
}

// Informations de connexion à la base de données des articles
$articlesServername = "localhost";
$articlesUsername = "root";
$articlesPassword = "";
$articlesDbname = "articles2";

// Informations de connexion à la base de données des utilisateurs
$usersServername = "localhost";
$usersUsername = "root";
$usersPassword = "";
$usersDbname = "inscriptions";

// Connexion à la base de données des articles
$articlesConn = new mysqli($articlesServername, $articlesUsername, $articlesPassword, $articlesDbname);

// Vérifier si la connexion à la base de données des articles a échoué
if ($articlesConn->connect_error) {
    die("La connexion à la base de données des articles a échoué : " . $articlesConn->connect_error);
}

// Connexion à la base de données des utilisateurs
$usersConn = new mysqli($usersServername, $usersUsername, $usersPassword, $usersDbname);

// Vérifier si la connexion à la base de données des utilisateurs a échoué
if ($usersConn->connect_error) {
    die("La connexion à la base de données des utilisateurs a échoué : " . $usersConn->connect_error);
}

// Récupérer l'ID de l'utilisateur connecté depuis la session
$userId = $_SESSION['id'];

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Récupérer les données du formulaire
    $articleId = $_POST['article_id'];
    $quantity = $_POST['quantity'];

    // Vérifier si l'article existe dans la base de données des articles
    $articleQuery = "SELECT * FROM articles WHERE article_id = $articleId";
    $articleResult = $articlesConn->query($articleQuery);

    if ($articleResult->num_rows > 0) {
        $articleData = $articleResult->fetch_assoc();
        $articleName = $articleData['nom'];
        $articlePrice = $articleData['prix'];

        // Vérifier si l'article est déjà présent dans le panier de l'utilisateur
        $cartQuery = "SELECT * FROM panier WHERE id_session = '$userId' AND id_article = $articleId";
        $cartResult = $usersConn->query($cartQuery);

        if ($cartResult->num_rows > 0) {
            // Mettre à jour la quantité dans le panier
            $updateQuery = "UPDATE panier SET quantite = quantite + $quantity WHERE id_session = '$userId' AND id_article = $articleId";
            $usersConn->query($updateQuery);
        } else {
            // Ajouter l'article dans le panier de l'utilisateur
            $insertQuery = "INSERT INTO panier (id_session, id_article, nom_article, quantite, prix, description, date_ajout, image_article) VALUES ('$userId', $articleId, '$articleName', $quantity, $articlePrice, '', NOW(), '')";
            $usersConn->query($insertQuery);
        }
    }
}

// Récupérer tous les articles disponibles
$articlesQuery = "SELECT * FROM articles";
$articlesResult = $articlesConn->query($articlesQuery);

// Fermer les connexions à la base de données
$articlesConn->close();
$usersConn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://kit.fontawesome.com/93a77cb4e9.js" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <title>Adidas Shop</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="#">Femmes</a></li>
            <li><a href="#">Hommes</a></li>
            <li><a href="#">Enfants</a></li>
            <li><a href="#">Accessoires</a></li>
        </ul>
    </nav>
    <div class="logo">
        <img src="images/logo.png" alt="Adidas">
    </div>
    <div class="search">
        <input type="text" placeholder="Rechercher...">
        <button type="submit">OK</button>
    </div>
    <div class="cart">
        <a href="/commerce/panier/panier.php">
            <img src="" alt="Panier">
            <span>Panier (0)</span>
        </a>
    </div>
</header>
<header class="header">
            <div class="header__logo">
              <img src="logo.png" alt="Logo de la boutique">
            </div>
            <nav class="header__nav">
              <ul>
                <li><a href="#">Accueil</a></li>
                <li><a href="stock/stock.php">Produits</a></li>
                <li><a href="#">Commander</a></li>
                <li><a href="reservation/ma_reservation.php">Mes Réservations</a></li>
				<li><a href="panier/panier.php">Panier</a></li>
				<li><a href="profil/profil.php">Compte</a></li>
                <?php if ($_SESSION['id'] == '24') { ?>
                <li><a href="admin/admin.php">Admin</a></li>
            <?php } ?>
              </ul>
            </nav>
</header>
<main>
    <section class="products">
        <h2>Nouveautés</h2>
        <div class="product-items">
            <?php
            // Afficher tous les articles disponibles
            if ($articlesResult->num_rows > 0) {
                while ($row = $articlesResult->fetch_assoc()) {
                    $articleId = $row['article_id'];
                    $articleName = $row['nom'];
                    $articlePrice = $row['prix'];
                    $articleImage = $row['image'];
                    $articleStock = $row['stock'];
                    ?>
                    <div class="product-item">
                        <img src="<?php echo $articleImage; ?>" alt="<?php echo $articleName; ?>">
                        <h3><?php echo $articleName; ?></h3>
                        <p class="price"><?php echo $articlePrice; ?> €</p>
                        <?php if ($articleStock > 0) { ?>
                            <form method="POST" action="">
                                <input type="hidden" name="article_id" value="<?php echo $articleId; ?>">
                                <label for="quantity">Quantité :</label>
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $articleStock; ?>">
                                <button type="submit" name="add_to_cart">Ajouter au panier</button>
                            </form>
                        <?php } else { ?>
                            <p>Stock épuisé</p>
                        <?php } ?>
                    </div>
                    <?php
                }
            } else {
                echo "Aucun article disponible.";
            }
            ?>
        </div>
    </section>
</main>
<footer>
    <div class="footer-logo">
        <img src="images/logo.png" alt="Adidas">
    </div>
    <p>&copy; 2023 Boutique de Vélos. Tous droits réservés.</p>
</footer>
</body>
</html>
