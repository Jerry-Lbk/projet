<?php
try {
    $db = new PDO("sqlite:../db/ma_base.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->query("
        SELECT p.*, u.nom, u.photo, u.description
        FROM portfolios p
        JOIN utilisateurs u ON p.utilisateur_id = u.id
        ORDER BY p.date_creation DESC
    ");

    $portfolios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Portfolios créés</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .portfolio-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }
        .portfolio-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 15px;
            width: calc(33.333% - 20px);
        }
        .portfolio-card img {
            max-width: 100%;
            border-radius: 50%;
        }
        .portfolio-card h3 {
            margin-top: 10px;
        }
        .portfolio-card p {
            margin: 5px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Portfolios générés</h1>

    <div class="portfolio-list">
        <?php foreach ($portfolios as $p): ?>
            <div class="portfolio-card">
                <img src="../<?= htmlspecialchars($p['photo']) ?>" alt="Photo de profil" class="profile-img">
                <div class="info">
                    <h3><?= htmlspecialchars($p['titre']) ?></h3>
                    <p><strong>Par :</strong> <?= htmlspecialchars($p['nom']) ?></p>
                    <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($p['description'])) ?></p>
                    <p><strong>Date :</strong> <?= date("d/m/Y", strtotime($p['date_creation'])) ?></p>
                    <a href="<?= htmlspecialchars($p['url']) ?>" target="_blank" class="btn">Voir</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <p><a href="index.php">Retour</a></p>
</body>
</html>
