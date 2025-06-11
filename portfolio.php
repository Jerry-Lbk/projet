<?php
// Connexion à la base de données
try {
    $db = new PDO("sqlite:db/ma_base.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Portfolio de <?= htmlspecialchars($utilisateur['nom']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 30px;
            max-width: 800px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        h1, h2 {
            margin-top: 20px;
        }
        a {
            color: blue;
            text-decoration: none;
        }
        li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <?php if (!empty($utilisateur['photo'])): ?>
        <img src="<?= htmlspecialchars($utilisateur['photo']) ?>" alt="Photo de profil" class="profile-img">
    <?php endif; ?>

    <h1><?= htmlspecialchars($utilisateur['nom']) ?></h1>
    <p><strong>Email :</strong> <?= htmlspecialchars($utilisateur['email']) ?></p>

    <hr>

    <h2>À propos de moi</h2>
    <p><?= nl2br(htmlspecialchars($utilisateur['bio'] ?? "Aucune description.")) ?></p>

    <h2>Projets</h2>
    <?php if (count($projets) > 0): ?>
        <ul>
            <?php foreach ($projets as $projet): ?>
                <li>
                    <strong><?= htmlspecialchars($projet['titre']) ?></strong><br>
                    <?= htmlspecialchars($projet['description']) ?><br>
                    <?php if (!empty($projet['lien'])): ?>
                        <a href="<?= htmlspecialchars($projet['lien']) ?>" target="_blank">Voir le projet</a>
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
