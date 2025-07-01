<?php
session_start();

$db = new PDO("sqlite:../db/ma_base.db");

// Ajoute la colonne theme_id si elle n'existe pas déjà
try {
    $db->exec("ALTER TABLE utilisateurs ADD COLUMN theme_id INTEGER");
} catch (PDOException $e) {
    // Ignore l'erreur si la colonne existe déjà
    if (strpos($e->getMessage(), 'duplicate column name') === false) {
        throw $e;
    }
}

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["utilisateur_id"];

// Récupère tous les thèmes
$themes = $db->query("SELECT * FROM themes")->fetchAll(PDO::FETCH_ASSOC);

// Récupère les infos de l'utilisateur
$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

// Change le thème si formulaire soumis
if (isset($_POST['theme_id'])) {
    $theme_id = (int)$_POST['theme_id'];
    $db->prepare("UPDATE utilisateurs SET theme_id=? WHERE id=?")->execute([$theme_id, $user_id]);
    $_SESSION['success'] = "Thème modifié !";
    header("Location: theme.php");
    exit();
}

// Récupère le thème actuel
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
    <title>Choisir un thème</title>
    <style>
        body {
            font-family: sans-serif;
            background: <?= htmlspecialchars($primary_color) ?>;
            padding: 2em;
            text-align: center;
        }
        .theme-option {
            display: inline-block;
            margin: 1em;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 1em;
            background: #fff;
            min-width: 180px;
        }
        .current {
            border-color: #007bff;
            background: #eaf4ff;
        }
        .theme-color {
            display: block;
            width: 40px;
            height: 40px;
            margin: 0.5em auto;
            border-radius: 50%;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <h1>Choisissez votre thème</h1>
    <?php if (!empty($_SESSION['success'])) { echo "<p style='color:green'>{$_SESSION['success']}</p>"; unset($_SESSION['success']); } ?>
    <form method="post">
        <?php foreach ($themes as $theme): ?>
            <label class="theme-option<?= $theme['id'] == $current_theme_id ? ' current' : '' ?>">
                <input type="radio" name="theme_id" value="<?= $theme['id'] ?>" <?= $theme['id'] == $current_theme_id ? 'checked' : '' ?>>
                <strong><?= htmlspecialchars($theme['name']) ?></strong>
                <span class="theme-color" style="background:<?= htmlspecialchars($theme['primary_color']) ?>"></span>
                <div>Couleur principale : <?= htmlspecialchars($theme['primary_color']) ?></div>
            </label>
        <?php endforeach; ?>
        <div style="margin-top:2em;">
            <button type="submit" target="_blank">Enregistrer</button>
        </div>
    </form>
</body>
</html>