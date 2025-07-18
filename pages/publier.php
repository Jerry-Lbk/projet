<?php

session_start();

// Si l'utilisateur n'est pas connecté, on le redirige ou on bloque l'accès
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../index.php'); // ou afficher un message
    exit();
}

// Connexion à la base de données
try {
    $db = new PDO("sqlite:../db/ma_base.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}



$id =  $_SESSION['utilisateur_id'];

// Récupérer les infos de l'utilisateur
$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    echo "Utilisateur introuvable.";
    exit();
}

// Vérifier si le portfolio est déjà publié
$check = $db->prepare("SELECT COUNT(*) FROM portfolios WHERE utilisateur_id = ?");
$check->execute([$id]);
$alreadyPublished = $check->fetchColumn() > 0;

if ($alreadyPublished) {
    echo "Portfolio déjà publié.";
    exit();
}

// Publier le portfolio
$stmt = $db->prepare("INSERT INTO portfolios (utilisateur_id, titre, url) VALUES (?, ?, ?)");
$stmt->execute([
    $id,
    "Portfolio de {$utilisateur['nom']}",
    "portfolio.php?id=$id",
]);

echo "✅ Portfolio publié avec succès.";
?>
