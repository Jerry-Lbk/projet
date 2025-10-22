<?php
require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../favicon.png">
    <title>Accueil - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
    <div class="home-container fade-in">
        <h1>Bienvenue sur <?= APP_NAME ?></h1>
        <p>Créez et partagez votre portfolio professionnel en quelques clics</p>
        
        <div class="visit-section">
            <a href="liste.php">
                <img src="../favicon.png" alt="Visiter les portfolios" />
            </a>
            <h5>Découvrir les portfolios</h5>
        </div>

        <div class="visit-section">
            <a href="register.php">
                <img src="../creer.png" alt="Créer votre portfolio" />
            </a>
            <h5>Créer votre portfolio</h5>
        </div>
        
        <div class="mt-4">
            <p>Déjà membre ? <a href="login.php" class="btn btn-small">Se connecter</a></p>
        </div>
    </div>
</body>
</html>