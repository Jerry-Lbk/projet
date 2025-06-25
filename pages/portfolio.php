<?php
// Connexion à la base de données
try {
   $db = new PDO("sqlite:../db/ma_base.db");
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Portfolio</title>
    <style>
        :root {
            --primary: <?= htmlspecialchars($primary_color) ?>;
            --background: <?= ($primary_color == "#222831" ? "#222831" : ($primary_color == "#007bff" ? "#eaf4ff" : "#f8f8f8")) ?>;
            --text: <?= ($primary_color == "#222831" ? "#fff" : "#222831") ?>;
            --card: <?= ($primary_color == "#222831" ? "#393e46" : "#fff") ?>;
            --link: <?= ($primary_color == "#007bff" ? "#007bff" : ($primary_color == "#222831" ? "#00adb5" : "#222831")) ?>;
            --link-hover: <?= ($primary_color == "#007bff" ? "#0056b3" : "#007bff") ?>;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--background);
            color: var(--text);
            padding: 2em;
            margin: 0;
            min-height: 100vh;
            text-align: center;
            transition: background 0.5s, color 0.5s;
        }
        .container {
            background: var(--card);
            color: var(--text);
            padding: 32px 24px;
            max-width: 800px;
            margin: 32px auto;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            transition: background 0.5s, color 0.5s;
        }
        .profile-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary);
            margin-bottom: 18px;
            background: #fff;
        }
        h1 {
            margin-top: 0;
            font-size: 2.2em;
            color: var(--primary);
            letter-spacing: 1px;
        }
        h2 {
            margin-top: 32px;
            color: var(--primary);
            font-size: 1.3em;
            letter-spacing: 1px;
        }
        a {
            color: var(--link);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        a:hover {
            color: var(--link-hover);
            text-decoration: underline;
        }
        ul {
            padding: 0;
            margin: 0;
        }
        li {
            margin-bottom: 18px;
            background: <?= ($primary_color == "#222831" ? "#23272f" : "#f4f4f4") ?>;
            color: var(--text);
            padding: 18px 16px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            list-style: none;
            text-align: left;
        }
        .projet-titre {
            font-size: 1.1em;
            color: var(--primary);
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-size: 1em;
            margin: 10px 0;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }
        .btn:hover {
            background: var(--link-hover);
        }
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
            .profile-img {
                width: 90px;
                height: 90px;
            }
            li {
                padding: 12px 8px;
            }
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
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun projet ajouté.</p>
    <?php endif; ?>
</div>
</body>
</html>
