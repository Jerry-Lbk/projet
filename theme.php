<?php
session_start();
$db = new PDO('sqlite:./ma_base.db'); // adapte le chemin si besoin

// Récupère tous les thèmes
$themes = $db->query("SELECT * FROM themes")->fetchAll(PDO::FETCH_ASSOC);

// Change le thème si formulaire soumis
if (isset($_POST['theme_id'])) {
    $theme_id = (int)$_POST['theme_id'];
    // ici, tu dois adapter pour l'utilisateur courant, on suppose id=1
    $db->prepare("UPDATE users SET theme_id=? WHERE id=?")->execute([$theme_id, 1]);
    $_SESSION['success'] = "Thème modifié !";
    header("Location: theme.php");
    exit();
}

// Récupère le thème actuel
$user = $db->query("SELECT * FROM users WHERE id=1")->fetch(PDO::FETCH_ASSOC);
$current_theme_id = $user ? $user['theme_id'] : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choisir un thème</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f8f8f8;
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
            <button type="submit">Enregistrer</button>
        </div>
    </form>
</body>
</html>