<?php
session_start();

// 🔐 Rediriger si non connecté
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: login.php");
    exit();
}

// 📦 Connexion à la base SQLite
try {
    $db = new PDO("sqlite:../db/ma_base.db");
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
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #eaf4ff 0%, #f8f8f8 100%);
            color: #222831;
            margin: 0;
            padding: 0;
        }
        .container {
            background: #fff;
            max-width: 420px;
            margin: 48px auto 0 auto;
            padding: 32px 28px 28px 28px;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            text-align: center;
        }
        .profile-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
            margin-bottom: 18px;
            background: #fff;
            box-shadow: 0 2px 8px #eaf4ff;
        }
        h2 {
            margin-top: 0;
            font-size: 2em;
            color: #007bff;
            letter-spacing: 1px;
        }
        .btn {
            display: inline-block;
            padding: 10px 22px;
            margin: 8px 6px 0 6px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 7px;
            font-weight: 500;
            font-size: 1em;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px #eaf4ff;
            border: none;
        }
        .btn:hover {
            background: #0056b3;
            box-shadow: 0 4px 16px #cce6ff;
        }
        .btn.logout {
            background: #dc3545;
        }
        .btn.logout:hover {
            background: #a71d2a;
        }
        p {
            margin: 12px 0 18px 0;
        }
        @media (max-width: 600px) {
            .container { padding: 18px 6px; }
            .profile-img { width: 80px; height: 80px; }
            h2 { font-size: 1.3em; }
            .btn { font-size: 0.95em; padding: 8px 10px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Bienvenue, <?= htmlspecialchars($utilisateur['nom']) ?> 👋</h2>

    <?php if (!empty($utilisateur['photo'])): ?>
        <img src="/<?= htmlspecialchars($utilisateur['photo']) ?>" alt="Photo de profil" class="profile-img"><br>
    <?php endif; ?>

    <p><strong>Email :</strong> <?= htmlspecialchars($utilisateur['email']) ?></p>
    <a href="profil.php" class="btn">Modifier mon profil</a>
    <a href="portfolio.php?id=<?= $_SESSION["utilisateur_id"] ?>" class="btn">Voir mon portfolio</a>
    <a href="ajouter_projet.php" class="btn">➕ Ajouter un projet</a>
    <a href="modifier_description.php" class="btn">Modifier la description</a>
    <a href="theme.php" class="btn">Modifier le thème</a>
    <a href="logout.php" class="btn logout">Se déconnecter</a>
</div>
</body>
</html>
