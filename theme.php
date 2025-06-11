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

 <!---->