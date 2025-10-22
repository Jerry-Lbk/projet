<?php
/**
 * Page d'administration - Redirection vers le nouveau tableau de bord
 * Cette page redirige vers admin_dashboard.php pour une meilleure organisation
 */

require_once '../session_config.php';
require_once '../config.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.php");
    exit();
}

// Rediriger vers le nouveau tableau de bord d'administration
header("Location: admin_dashboard.php");
exit();
?>