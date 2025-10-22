<?php
/**
 * Configuration centralisée pour l'application CREON
 */

// Configuration de la base de données
define('DB_PATH', __DIR__ . '/db/ma_base.db');
define('DB_DSN', 'sqlite:' . DB_PATH);

// Configuration des dossiers
define('UPLOAD_DIR', __DIR__ . '/images/');
define('UPLOAD_URL', 'images/');
define('THEME_CSS_DIR', __DIR__ . '/theme_css/');
define('CSS_DIR', __DIR__ . '/css/');

// Configuration de sécurité
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('SESSION_TIMEOUT', 3600); // 1 heure

// Configuration de l'application
define('APP_NAME', 'CREON');
define('APP_VERSION', '1.0.0');
define('DEFAULT_THEME_ID', 1);

// Configuration de session déplacée dans session_config.php

// Configuration des erreurs (à désactiver en production)
define('APP_DEBUG', true); // Mettre à false en production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure le gestionnaire d'erreurs
require_once __DIR__ . '/error_handler.php';

// Fonction pour générer un token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fonction pour vérifier un token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Fonction pour nettoyer les données d'entrée
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Fonction pour valider un email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Fonction pour valider un mot de passe
function validatePassword($password) {
    return strlen($password) >= 6;
}

// Fonction pour valider un fichier uploadé
function validateUploadedFile($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    return in_array($mimeType, ALLOWED_IMAGE_TYPES);
}

// Fonction pour générer un nom de fichier unique
function generateUniqueFilename($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return uniqid('file_') . '.' . $extension;
}
?>
