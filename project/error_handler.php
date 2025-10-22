<?php
/**
 * Gestionnaire d'erreurs global pour l'application CREON
 */

// Définir le gestionnaire d'erreurs personnalisé
set_error_handler(function($severity, $message, $file, $line) {
    // Ne pas exécuter le gestionnaire d'erreurs interne de PHP
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $errorMessage = "Erreur PHP [$severity]: $message dans $file à la ligne $line";
    error_log($errorMessage);
    
    // En mode développement, afficher l'erreur
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "<strong>Erreur PHP:</strong> $message<br>";
        echo "<strong>Fichier:</strong> $file<br>";
        echo "<strong>Ligne:</strong> $line";
        echo "</div>";
    }
    
    return true;
});

// Définir le gestionnaire d'exceptions
set_exception_handler(function($exception) {
    $errorMessage = "Exception non capturée: " . $exception->getMessage() . " dans " . $exception->getFile() . " à la ligne " . $exception->getLine();
    error_log($errorMessage);
    
    // En mode développement, afficher l'exception
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "<strong>Exception:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>Fichier:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Ligne:</strong> " . $exception->getLine() . "<br>";
        echo "<strong>Trace:</strong><br><pre>" . $exception->getTraceAsString() . "</pre>";
        echo "</div>";
    } else {
        // En production, afficher une page d'erreur générique
        http_response_code(500);
        echo "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Erreur - " . APP_NAME . "</title>
            <link rel='stylesheet' href='css/main.css'>
        </head>
        <body>
            <div class='container'>
                <h2>Une erreur s'est produite</h2>
                <p>Nous nous excusons pour la gêne occasionnée. Veuillez réessayer plus tard.</p>
                <a href='index.php' class='btn'>Retour à l'accueil</a>
            </div>
        </body>
        </html>";
    }
});

// Fonction pour logger les erreurs personnalisées
function logError($message, $context = []) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message;
    if (!empty($context)) {
        $logMessage .= " - Contexte: " . json_encode($context);
    }
    error_log($logMessage);
}

// Fonction pour afficher les messages d'erreur de manière sécurisée
function displayError($message, $type = 'danger') {
    $alertClass = 'alert-' . $type;
    return "<div class='alert $alertClass'>" . htmlspecialchars($message) . "</div>";
}

// Fonction pour valider et nettoyer les données d'entrée
function validateAndSanitize($data, $rules = []) {
    $errors = [];
    $sanitized = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? '';
        
        // Nettoyage de base
        $sanitized[$field] = sanitizeInput($value);
        
        // Validation selon les règles
        if (isset($rule['required']) && $rule['required'] && empty($sanitized[$field])) {
            $errors[$field] = "Le champ $field est obligatoire.";
        }
        
        if (isset($rule['email']) && $rule['email'] && !empty($sanitized[$field]) && !validateEmail($sanitized[$field])) {
            $errors[$field] = "L'adresse email n'est pas valide.";
        }
        
        if (isset($rule['min_length']) && strlen($sanitized[$field]) < $rule['min_length']) {
            $errors[$field] = "Le champ $field doit contenir au moins {$rule['min_length']} caractères.";
        }
        
        if (isset($rule['max_length']) && strlen($sanitized[$field]) > $rule['max_length']) {
            $errors[$field] = "Le champ $field ne peut pas dépasser {$rule['max_length']} caractères.";
        }
        
        if (isset($rule['url']) && !empty($sanitized[$field]) && !filter_var($sanitized[$field], FILTER_VALIDATE_URL)) {
            $errors[$field] = "L'URL fournie n'est pas valide.";
        }
    }
    
    return ['data' => $sanitized, 'errors' => $errors];
}
?>
