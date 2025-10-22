<?php
require_once '../session_config.php';
require_once '../config.php';
require_once '../database.php';
session_start();

// Si l'utilisateur n'est pas connecté, on le redirige
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../index.php');
    exit();
}

try {
    $db = getDB();
} catch (Exception $e) {
    error_log("Erreur portfolio: " . $e->getMessage());
    die("❌ Erreur lors du chargement du portfolio.");
}
// Vérifier si un ID est fourni dans l'URL
if (!isset($_GET['id'])) {
    die("❌ Aucun portfolio sélectionné.");
}

$id = intval($_GET['id']);

// Récupérer les infos de l'utilisateur
$utilisateur = $db->fetchOne("SELECT * FROM utilisateurs WHERE id = ?", [$id]);

if (!$utilisateur) {
    die("❌ Utilisateur introuvable.");
}

// Récupérer les projets de l'utilisateur
$projets = $db->fetchAll("SELECT * FROM projets WHERE utilisateur_id = ?", [$id]);


// Récupère le thème de l'utilisateur, ou le thème par défaut si non défini
$theme_id = !empty($utilisateur['theme_id']) ? $utilisateur['theme_id'] : DEFAULT_THEME_ID;
$theme = $db->fetchOne("SELECT * FROM themes WHERE id = ?", [$theme_id]);

if (!$theme) {
    // Valeurs de secours si jamais la table themes est vide
    $theme = [
        'primary_color' => '#007bff',
        'custom_css' => null
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - <?= htmlspecialchars($utilisateur['nom']) ?></title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" type="image/png" href="../favicon.png">
    
    <?php if (!empty($theme['custom_css'])): ?>
        <?php if (strpos($theme['custom_css'], 'theme_css/') === 0): ?>
            <link rel="stylesheet" href="../<?= htmlspecialchars($theme['custom_css']) ?>">
        <?php else: ?>
            <style>
                <?= $theme['custom_css'] ?>
            </style>
        <?php endif; ?>
    <?php endif; ?>
</head>
<body>
    <div class="container fade-in">
        <?php if (!empty($utilisateur['photo'])): ?>
            <img src="../<?= htmlspecialchars($utilisateur['photo']) ?>" alt="Photo de profil" class="profile-img">
        <?php endif; ?>

        <h1><?= htmlspecialchars($utilisateur['nom']) ?></h1>
        <p><strong>Email :</strong> <?= htmlspecialchars($utilisateur['email']) ?></p>

        <hr>

        <h2>À propos de moi</h2>
        <p><?= nl2br(htmlspecialchars($utilisateur['description'] ?? "Aucune description.")) ?></p>

        <h2>Projets</h2>
        <?php if (count($projets) > 0): ?>
            <div class="projects-grid">
                <?php foreach ($projets as $projet): ?>
                    <div class="project-card">
                        <?php if (!empty($projet['image'])): ?>
                            <img src="../<?= htmlspecialchars($projet['image']) ?>" alt="Image du projet" class="project-image">
                        <?php endif; ?>
                        <div class="project-content">
                            <h3 class="project-title"><?= htmlspecialchars($projet['titre']) ?></h3>
                            <p class="project-description"><?= htmlspecialchars($projet['description']) ?></p>
                            <?php if (!empty($projet['lien'])): ?>
                                <a href="<?= htmlspecialchars($projet['lien']) ?>" target="_blank" class="btn">Voir le projet</a>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $utilisateur['id']): ?>
                                <a href="delete_projet.php?id=<?= $projet['id'] ?>&token=<?= generateCSRFToken() ?>"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');" 
                                   class="btn btn-danger btn-small">Supprimer</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun projet ajouté.</p>
        <?php endif; ?>
        
        <div class="mt-4">
            <?php if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $utilisateur['id']): ?>
                <a href="publier.php" class="btn btn-success">Publier mon portfolio</a>
                <a href="dashboard.php" class="btn btn-small">Retour au tableau de bord</a>
            <?php else: ?>
                <a href="liste.php" class="btn">Retour à la liste</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>