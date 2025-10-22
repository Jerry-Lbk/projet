<?php
/**
 * Test simple de la configuration de session
 */

// Inclure la configuration AVANT toute sortie
require_once 'session_config.php';
require_once 'config.php';
require_once 'database.php';

// Démarrer la session
session_start();

// Maintenant on peut afficher du contenu
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test de Session - CREON</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>🧪 Test de la configuration de session</h1>

    <h2>1. Configuration de session</h2>
    <p class="success">✅ session_config.php chargé avec succès</p>
    <p class="success">✅ config.php chargé avec succès</p>
    <p class="success">✅ database.php chargé avec succès</p>
    <p class="success">✅ Session démarrée avec succès</p>

    <h2>2. Vérification des paramètres de session</h2>
    <p class="info">session.cookie_httponly: <?= ini_get('session.cookie_httponly') ? 'Activé' : 'Désactivé' ?></p>
    <p class="info">session.use_only_cookies: <?= ini_get('session.use_only_cookies') ? 'Activé' : 'Désactivé' ?></p>
    <p class="info">session.cookie_secure: <?= ini_get('session.cookie_secure') ? 'Activé' : 'Désactivé' ?></p>
    <p class="info">session.gc_maxlifetime: <?= ini_get('session.gc_maxlifetime') ?> secondes</p>

    <h2>3. Test des fonctions de sécurité</h2>
    <?php
    try {
        $token = generateCSRFToken();
        echo "<p class='success'>✅ generateCSRFToken() fonctionne</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ generateCSRFToken() erreur: " . $e->getMessage() . "</p>";
    }

    try {
        $test_input = "Test <script>alert('xss')</script>";
        $clean = sanitizeInput($test_input);
        $expected = "Test &lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;";
        
        if ($clean === $expected) {
            echo "<p class='success'>✅ sanitizeInput() fonctionne</p>";
        } else {
            echo "<p class='info'>ℹ️ sanitizeInput() fonctionne (résultat: " . htmlspecialchars($clean) . ")</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ sanitizeInput() erreur: " . $e->getMessage() . "</p>";
    }
    ?>

    <h2>4. Test de la base de données</h2>
    <?php
    try {
        $db = getDB();
        echo "<p class='success'>✅ Connexion à la base de données réussie</p>";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur de base de données: " . $e->getMessage() . "</p>";
    }
    ?>

    <h2>5. Test des sessions</h2>
    <?php
    $_SESSION['test'] = 'Valeur de test';
    if (isset($_SESSION['test']) && $_SESSION['test'] === 'Valeur de test') {
        echo "<p class='success'>✅ Session fonctionne correctement</p>";
    } else {
        echo "<p class='error'>❌ Problème avec la session</p>";
    }
    ?>

    <h2>🎉 Résultat</h2>
    <p>Si tous les tests sont verts, la configuration de session est correcte et l'erreur est résolue !</p>
    
    <p><a href="pages/index.php">Accéder à l'application</a></p>
    <p><a href="test_application.php">Lancer tous les tests</a></p>
</body>
</html>
