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
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            color: #222831;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            background: #fff;
            color: #222831;
            max-width: 700px;
            margin: 30px auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #eee;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 15px;
            background: #fff;
        }
        h1 {
            margin-top: 0;
            color: #007bff;
        }
        h2 {
            margin-top: 30px;
            color: #393e46;
            font-size: 1.2em;
        }
        a.btn {
            display: inline-block;
            background: #007bff;
            color: #fff;
            padding: 8px 18px;
            border-radius: 5px;
            text-decoration: none;
            margin: 8px 0;
            transition: background 0.2s;
        }
        a.btn:hover {
            background: #0056b3;
        }
        ul {
            padding: 0;
            margin: 0;
        }
        li {
            list-style: none;
            background: #f4f4f4;
            margin-bottom: 15px;
            padding: 14px;
            border-radius: 8px;
            text-align: left;
        }
        .projet-titre {
            font-weight: bold;
            color: #007bff;
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
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun projet ajouté.</p>
    <?php endif; ?>
</div>
</body>
</html>
