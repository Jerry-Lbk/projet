<?php
session_start();

// 🔐 Rediriger si non connecté
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: login.php");
    exit();
}

// 📦 Connexion à la base SQLite
try {
    $db = new PDO("sqlite:db/ma_base.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les infos de l'utilisateur
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION["utilisateur_id"]);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "❌ Erreur base de données : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f2f2f2; }
        .container { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
        .btn { display: inline-block; padding: 10px 15px; margin: 5px; background: #007BFF; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h2>Bienvenue, <?= htmlspecialchars($utilisateur['nom']) ?> 👋</h2>

    <?php if (!empty($utilisateur['photo'])): ?>
        <img src="<?= htmlspecialchars($utilisateur['photo']) ?>" alt="Photo de profil" class="profile-img"><br>
    <?php endif; ?>

    <p><strong>Email :</strong> <?= htmlspecialchars($utilisateur['email']) ?></p>
    <a href="profil.php" class="btn">Modifier mon profil</a>
    <a href="portfolio.php?id=<?= $_SESSION["utilisateur_id"] ?>" class="btn">Voir mon portfolio</a>
    <a href="ajouter_projet.php" class="btn">➕ Ajouter un projet</a>
    <a href="modifier_description.php" class="btn">Modifier la description</a>
    <a href="theme.php" class="btn">Modifier le thème</a>
    <a href="logout.php" class="btn" style="background: #dc3545;">Se déconnecter</a>
</div>
</body>
</html>
