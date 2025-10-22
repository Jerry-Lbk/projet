<?php
require_once '../session_config.php';
require_once '../config.php';
session_start();

// Vérifier le token CSRF si fourni
if (isset($_GET['token']) && verifyCSRFToken($_GET['token'])) {
    // Nettoyer toutes les variables de session
    $_SESSION = array();
    
    // Détruire le cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Détruire la session
    session_destroy();
    
    // Rediriger vers la page d'accueil avec un message
    header("Location: index.php?logout=1");
    exit();
} else {
    // Rediriger vers le dashboard si pas de token valide
    header("Location: dashboard.php");
    exit();
}
?>