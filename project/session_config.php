<?php
/**
 * Configuration de session pour CREON
 * Ce fichier doit être inclus AVANT session_start() dans tous les fichiers
 * 
 * Note: Les paramètres de session sont configurés dans .htaccess
 * pour éviter les erreurs "headers already sent"
 */

// Vérification que la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_ACTIVE) {
    // Session déjà active, rien à faire
    return;
}

// Configuration de la session via .htaccess (recommandé)
// Les paramètres suivants sont définis dans .htaccess :
// - session.cookie_httponly
// - session.use_only_cookies  
// - session.cookie_secure
// - session.gc_maxlifetime
?>
