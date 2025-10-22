<?php
require_once '../session_config.php';
require_once '../config.php';
require_once '../database.php';
session_start();

// Vérifier la session et le timeout
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: login.php");
    exit();
}

// Vérifier le timeout de session
if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}

// Mettre à jour l'activité
$_SESSION["last_activity"] = time();

try {
    $db = getDB();
    $utilisateur = $db->fetchOne("SELECT * FROM utilisateurs WHERE id = ?", [$_SESSION["utilisateur_id"]]);
    
    if (!$utilisateur) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur dashboard: " . $e->getMessage());
    die("❌ Erreur lors du chargement du tableau de bord.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" type="image/png" href="../favicon.png">
</head>
<body>
    <div class="container fade-in">
        <h2>Bienvenue, <?= htmlspecialchars($utilisateur['nom']) ?> 👋</h2>

        <?php if (!empty($utilisateur['photo'])): ?>
            <img src="../<?= htmlspecialchars($utilisateur['photo']) ?>" alt="Photo de profil" class="profile-img">
        <?php endif; ?>

        <p><strong>Email :</strong> <?= htmlspecialchars($utilisateur['email']) ?></p>
        
        <div class="mt-4">
            <a href="profil.php" class="btn">Modifier mon profil</a>
            <a href="portfolio.php?id=<?= $_SESSION["utilisateur_id"] ?>" class="btn">Voir mon portfolio</a>
            <a href="ajouter_projet.php" class="btn">➕ Ajouter un projet</a>
            <a href="modifier_description.php" class="btn">Modifier la description</a>
            <a href="theme.php" class="btn">🎨 Choisir un thème</a>
            <a href="admin_dashboard.php" class="btn btn-info">⚙️ Administration</a>
            <a href="logout.php?token=<?= generateCSRFToken() ?>" class="btn btn-danger">Se déconnecter</a>
        </div>
    </div>
</body>
</html>
