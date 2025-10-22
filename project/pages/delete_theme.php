<?php
// filepath: c:\Users\Mister One\Desktop\projet\pages\delete_theme.php
session_start();

$db = new PDO("sqlite:../db/ma_base.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("DELETE FROM themes WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['message'] = "Thème supprimé avec succès.";
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression du thème.";
    }
}

header("Location: admin.php");
exit;