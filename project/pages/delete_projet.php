<?php
// filepath: c:\Users\Mister One\Desktop\projet\pages\delete_projet.php
session_start();

$db = new PDO("sqlite:../db/ma_base.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("DELETE FROM projets WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['message'] = "Projet supprimé avec succès.";
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression du projet.";
    }
}

header("Location: dashboard.php");
exit;
