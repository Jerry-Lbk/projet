<?php
// Connexion à la base de données
try {
   $db = new PDO("sqlite:../db/ma_base.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}

// Script de correction à exécuter une fois
$stmt = $db->prepare("UPDATE utilisateurs SET photo = REPLACE(photo, '../images/', 'images/') WHERE photo LIKE '../images/%'");
$stmt->execute();

// Vérifier si un ID est fourni dans l’URL
if (!isset($_GET['id'])) {
    echo "Aucun portfolio sélectionné.";
    exit();
}

$id = intval($_GET['id']);

// Récupérer les infos de l'utilisateur
$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    echo "Utilisateur introuvable.";
    exit();
}

// Récupérer les projets de l'utilisateur
$stmtProjets = $db->prepare("SELECT * FROM projets WHERE utilisateur_id = :id");
$stmtProjets->bindParam(':id', $id);
$stmtProjets->execute();
$projets = $stmtProjets->fetchAll(PDO::FETCH_ASSOC);

// Récupère tous les thèmes
$themes = $db->query("SELECT * FROM themes")->fetchAll(PDO::FETCH_ASSOC);

// Récupère le thème actuel de l'utilisateur affiché
$current_theme_id = isset($utilisateur['theme_id']) ? $utilisateur['theme_id'] : null;

// Récupère la couleur principale du thème sélectionné
$primary_color = "#f8f8f8"; // couleur par défaut
if ($current_theme_id) {
    foreach ($themes as $theme) {
        if ($theme['id'] == $current_theme_id) {
            $primary_color = $theme['primary_color'];
            break;
        }
    }
}

// Récupère le thème de l'utilisateur, ou le thème par défaut (id=1) si non défini
$theme_id = !empty($utilisateur['theme_id']) ? $utilisateur['theme_id'] : 1;
$stmt = $db->prepare("SELECT * FROM themes WHERE id = ?");
$stmt->execute([$theme_id]);
$theme = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$theme) {
    // Valeurs de secours si jamais la table themes est vide
    $theme = [
        'primary_color' => '#007bff'
    ];
}

// Ajout du thème par défaut si aucun thème n'existe
$db->exec("INSERT OR IGNORE INTO themes (id, name, primary_color) VALUES (1, 'Clair', '#007bff')");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Portfolio</title>
    
    
    <?php if (!empty($theme['custom_css'])): ?>
        <?php if (strpos($theme['custom_css'], 'theme_css/') === 0): ?>
            <link rel="stylesheet" href="/<?= htmlspecialchars($theme['custom_css']) ?>">
        <?php else: ?>
            <style><?= $theme['custom_css'] ?></style>
        <?php endif; ?>
    <?php endif; ?>
</head>
<body>
<div class="container">
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
        <ul>
            <?php foreach ($projets as $projet): ?>
                <li>
                    <span class="projet-titre"><?= htmlspecialchars($projet['titre']) ?></span><br>
                    <?= htmlspecialchars($projet['description']) ?><br>
                    <?php if (!empty($projet['lien'])): ?>
                        <a href="<?= htmlspecialchars($projet['lien']) ?>" target="_blank" class="btn">Voir le projet</a>
                    <?php endif; ?>
                    <?php if (!empty($projet['image'])): ?>
    <img src="/<?= htmlspecialchars($projet['image']) ?>" alt="Image du projet" style="max-width:200px;">
<?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun projet ajouté.</p>
    <?php endif; ?>
</div>
</body>
</html>
