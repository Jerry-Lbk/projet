<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.php");
    exit();
}

$utilisateur_id = $_SESSION['utilisateur_id'];

try {
    // Connexion à la base
    $db = new PDO("sqlite:db/ma_base.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $lien = trim($_POST['lien'] ?? '');

        if (!empty($titre) && !empty($description)) {
            // Insertion dans la table projets
            $stmt = $db->prepare("INSERT INTO projets (utilisateur_id, titre, description, lien)
                                  VALUES (:uid, :titre, :desc, :lien)");
            $stmt->execute([
                ':uid' => $utilisateur_id,
                ':titre' => $titre,
                ':desc' => $description,
                ':lien' => $lien
            ]);

            header("Location: dashboard.php");
            exit();
        } else {
            $erreur = "Tous les champs obligatoires doivent être remplis.";
        }
    }
} catch (PDOException $e) {
    $erreur = "Erreur de base de données : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un projet</title>
</head>
<body>
    <h1>Ajouter un projet</h1>

    <?php if (!empty($erreur)): ?>
        <p style="color:red;"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Titre :</label><br>
        <input type="text" name="titre" required><br><br>

        <label>Description :</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Lien du projet:</label><br>
        <input type="url" name="lien"><br><br>

        <label>photo du projet</label>
        <input type="file" name="img" id="" accept="jpg">
        <button type="submit">Ajouter</button>
    </form>

    <p><a href="dashboard.php">Retour au tableau de bord</a></p>
</body>
</html>
