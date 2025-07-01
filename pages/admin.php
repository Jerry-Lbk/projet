<?php
// filepath: c:\Users\Mister One\Desktop\projet\pages\admin.php
session_start();

// (Optionnel) Sécurité admin
// if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) { die('Accès interdit'); }

$db = new PDO("sqlite:../db/ma_base.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$message = "";

// Traitement de l'ajout de thème
if (isset($_POST['add_theme'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $custom_css = "";

    // Si un fichier CSS est uploadé
    if (isset($_FILES['css_file']) && $_FILES['css_file']['error'] == 0 && $_FILES['css_file']['size'] > 0) {
        $ext = strtolower(pathinfo($_FILES['css_file']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'css') {
            $message = "Le fichier doit être un .css";
        } else {
            $target_dir = "../theme_css/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $css_filename = uniqid("theme_") . ".css";
            $target_file = $target_dir . $css_filename;
            if (move_uploaded_file($_FILES['css_file']['tmp_name'], $target_file)) {
                $custom_css = "theme_css/" . $css_filename;
            } else {
                $message = "Erreur lors de l'upload du fichier.";
            }
        }
    } else {
        // Sinon, CSS écrit à la main
        $custom_css = trim($_POST['custom_css'] ?? "");
    }

    if ($name && $description && $custom_css && !$message) {
        $stmt = $db->prepare("INSERT INTO themes (name, primary_color, custom_css) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $custom_css]);
        $message = "Thème ajouté avec succès !";
    } elseif (!$message) {
        $message = "Veuillez remplir tous les champs et fournir un CSS (fichier ou texte).";
    }
}

// Liste des thèmes existants
$themes = $db->query("SELECT * FROM themes")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Thèmes</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; padding: 30px; }
        .container { background: #fff; padding: 20px; border-radius: 10px; max-width: 700px; margin: auto; }
        input, textarea { width: 100%; margin-bottom: 10px; padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
        button { background: #007bff; color: #fff; border: none; padding: 10px 18px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #eee; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
<div class="container">
    <h2>Ajouter un thème</h2>
    <?php if ($message): ?>
        <p style="color:green"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <label>Nom du thème :</label>
        <input type="text" name="name" required>

        <label>Description :</label>
        <input type="text" name="description" required>

        <label>Fichier CSS (optionnel) :</label>
        <input type="file" name="css_file" accept=".css">

        <label>Ou CSS personnalisé (optionnel) :</label>
        <textarea name="custom_css" rows="6" placeholder="body { background: #fff; color: #222; }"></textarea>

        <button type="submit" name="add_theme">Ajouter le thème</button>
    </form>

    <h3>Thèmes existants</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>CSS</th>
        </tr>
        <?php foreach ($themes as $theme): ?>
            <tr>
                <td><?= $theme['id'] ?></td>
                <td><?= htmlspecialchars($theme['name']) ?></td>
                <td><?= htmlspecialchars($theme['primary_color']) ?></td>
                <td>
                    <?php if (!empty($theme['custom_css']) && strpos($theme['custom_css'], 'theme_css/') === 0): ?>
                        <a href="/<?= htmlspecialchars($theme['custom_css']) ?>" target="_blank">Voir le CSS</a>
                    <?php elseif (!empty($theme['custom_css'])): ?>
                        <pre style="white-space:pre-wrap;"><?= htmlspecialchars($theme['custom_css']) ?></pre>
                    <?php else: ?>
                        <em>Aucun CSS</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>