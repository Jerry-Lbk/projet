<?php
/**
 * Test de la configuration de session
 */

echo "<h1>🧪 Test de la configuration de session</h1>";

// Test 1: Inclusion de session_config.php avant session_start()
echo "<h2>1. Test de l'inclusion de session_config.php</h2>";

try {
    require_once 'session_config.php';
    echo "✅ session_config.php chargé avec succès<br>";
} catch (Exception $e) {
    echo "❌ Erreur lors du chargement de session_config.php: " . $e->getMessage() . "<br>";
}

// Test 1.1: Inclusion de config.php
echo "<h2>1.1. Test de l'inclusion de config.php</h2>";

try {
    require_once 'config.php';
    echo "✅ config.php chargé avec succès<br>";
} catch (Exception $e) {
    echo "❌ Erreur lors du chargement de config.php: " . $e->getMessage() . "<br>";
}

// Test 2: Démarrage de session
echo "<h2>2. Test du démarrage de session</h2>";

try {
    session_start();
    echo "✅ Session démarrée avec succès<br>";
} catch (Exception $e) {
    echo "❌ Erreur lors du démarrage de session: " . $e->getMessage() . "<br>";
}

// Test 3: Vérification des paramètres de session
echo "<h2>3. Vérification des paramètres de session</h2>";

$session_params = [
    'session.cookie_httponly' => ini_get('session.cookie_httponly'),
    'session.use_only_cookies' => ini_get('session.use_only_cookies'),
    'session.cookie_secure' => ini_get('session.cookie_secure'),
];

foreach ($session_params as $param => $value) {
    $status = $value ? '✅' : '❌';
    echo "$status $param: " . ($value ? 'Activé' : 'Désactivé') . "<br>";
}

// Test 4: Test des fonctions de sécurité
echo "<h2>4. Test des fonctions de sécurité</h2>";

try {
    $token = generateCSRFToken();
    if (!empty($token)) {
        echo "✅ generateCSRFToken() fonctionne<br>";
    } else {
        echo "❌ generateCSRFToken() ne fonctionne pas<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur generateCSRFToken(): " . $e->getMessage() . "<br>";
}

try {
    $clean = sanitizeInput("Test <script>alert('xss')</script>");
    if ($clean === "Test &lt;script&gt;alert('xss')&lt;/script&gt;") {
        echo "✅ sanitizeInput() fonctionne<br>";
    } else {
        echo "❌ sanitizeInput() ne fonctionne pas<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur sanitizeInput(): " . $e->getMessage() . "<br>";
}

// Test 5: Test de la base de données
echo "<h2>5. Test de la base de données</h2>";

try {
    require_once 'database.php';
    $db = getDB();
    echo "✅ Connexion à la base de données réussie<br>";
} catch (Exception $e) {
    echo "❌ Erreur de base de données: " . $e->getMessage() . "<br>";
}

echo "<h2>🎉 Test terminé !</h2>";
echo "<p>Si tous les tests sont verts, la configuration de session est correcte.</p>";
echo "<p><a href='pages/index.php'>Accéder à l'application</a></p>";
?>
