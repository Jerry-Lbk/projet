<?php
session_start();

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['utilisateur_id'];
$db = new PDO("sqlite:../db/ma_base.db");

// Récupération de la description actuelle
$stmt = $db->prepare("SELECT description FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$current_description = isset($user['description']) && $user['description'] !== null ? $user['description'] : "";

// Traitement du formulaire
if (isset($_POST['description'])) {
    $desc = trim($_POST['description']);
    $stmt = $db->prepare("UPDATE utilisateurs SET description = ? WHERE id = ?");
    $stmt->execute([$desc, $user_id]);
    $_SESSION['success'] = "Description mise à jour !";
    header("Location: modifier_description.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la description</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f8f8f8;
            padding: 2em;
            text-align: center;
        }
        textarea {
            width: 90%;
            min-width: 250px;
            max-width: 500px;
            min-height: 100px;
            font-size: 1em;
            margin-bottom: 1em;
        }
        .success {
            color: green;
        }
        .desc-preview {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1em;
            margin: 2em auto;
            max-width: 500px;
            box-shadow: 0 2px 8px #eee;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Votre description</h1>
    <?php if (!empty($_SESSION['success'])): ?>
        <p class="success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <form method="post">
        <textarea name="description" placeholder="Décrivez-vous..."><?= htmlspecialchars($current_description) ?></textarea><br>
        <button type="submit">Enregistrer</button>
    </form>

    <h2>Prévisualisation</h2>
    <div class="desc-preview">
        <?= nl2br(htmlspecialchars($current_description)) ?>
    </div>
</body>
</html>