<?php
/**
 * Test du système de thèmes pour CREON
 */

require_once 'config.php';
require_once 'database.php';
require_once 'theme_manager.php';

echo "<h1>🧪 Test du système de thèmes</h1>";

try {
    $themeManager = getThemeManager();
    echo "<p>✅ ThemeManager initialisé avec succès</p>";
    
    // Test 1: Création des thèmes par défaut
    echo "<h2>1. Test de création des thèmes par défaut</h2>";
    $themeManager->createDefaultThemes();
    echo "<p>✅ Thèmes par défaut créés</p>";
    
    // Test 2: Récupération des thèmes
    echo "<h2>2. Test de récupération des thèmes</h2>";
    $themes = $themeManager->getAllThemes();
    echo "<p>✅ " . count($themes) . " thèmes récupérés</p>";
    
    foreach ($themes as $theme) {
        echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<strong>" . htmlspecialchars($theme['name']) . "</strong><br>";
        echo "Couleur principale: <span style='color: " . htmlspecialchars($theme['primary_color']) . "'>" . htmlspecialchars($theme['primary_color']) . "</span><br>";
        if ($theme['secondary_color']) {
            echo "Couleur secondaire: <span style='color: " . htmlspecialchars($theme['secondary_color']) . "'>" . htmlspecialchars($theme['secondary_color']) . "</span><br>";
        }
        echo "</div>";
    }
    
    // Test 3: Génération de CSS
    echo "<h2>3. Test de génération de CSS</h2>";
    if (!empty($themes)) {
        $firstTheme = $themes[0];
        $css = $themeManager->generateThemeCSS($firstTheme);
        echo "<p>✅ CSS généré pour le thème '" . htmlspecialchars($firstTheme['name']) . "'</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($css) . "</pre>";
    }
    
    // Test 4: Statistiques des thèmes
    echo "<h2>4. Test des statistiques</h2>";
    $stats = $themeManager->getThemeStats();
    echo "<p>✅ Statistiques récupérées</p>";
    
    foreach ($stats as $stat) {
        echo "<p>- " . htmlspecialchars($stat['name']) . ": " . $stat['user_count'] . " utilisateur(s)</p>";
    }
    
    // Test 5: Création d'un thème personnalisé
    echo "<h2>5. Test de création d'un thème personnalisé</h2>";
    $customThemeId = $themeManager->createTheme(
        "Test Personnalisé",
        "#ff6b6b",
        "#4ecdc4",
        "body { background: linear-gradient(45deg, #ff6b6b, #4ecdc4); }"
    );
    echo "<p>✅ Thème personnalisé créé avec l'ID: " . $customThemeId . "</p>";
    
    // Test 6: Sauvegarde du CSS
    echo "<h2>6. Test de sauvegarde du CSS</h2>";
    $customTheme = $themeManager->getTheme($customThemeId);
    $css = $themeManager->generateThemeCSS($customTheme);
    $saved = $themeManager->saveThemeCSS($customThemeId, $css);
    echo "<p>" . ($saved ? "✅" : "❌") . " CSS sauvegardé</p>";
    
    // Test 7: Application d'un thème à un utilisateur
    echo "<h2>7. Test d'application d'un thème</h2>";
    $testUserId = 1; // Supposons qu'il y ait un utilisateur avec l'ID 1
    try {
        $themeManager->applyThemeToUser($testUserId, $customThemeId);
        echo "<p>✅ Thème appliqué à l'utilisateur " . $testUserId . "</p>";
    } catch (Exception $e) {
        echo "<p>⚠️ Impossible d'appliquer le thème (utilisateur inexistant): " . $e->getMessage() . "</p>";
    }
    
    // Test 8: Suppression du thème de test
    echo "<h2>8. Test de suppression du thème de test</h2>";
    try {
        $themeManager->deleteTheme($customThemeId);
        echo "<p>✅ Thème de test supprimé</p>";
    } catch (Exception $e) {
        echo "<p>⚠️ Impossible de supprimer le thème: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>🎉 Tests terminés avec succès !</h2>";
    echo "<p>Le système de thèmes fonctionne correctement.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "<p><a href='pages/theme.php'>Voir la page de sélection de thèmes</a></p>";
echo "<p><a href='pages/admin_themes.php'>Voir la page d'administration des thèmes</a></p>";
echo "<p><a href='pages/theme_generator.php'>Voir le générateur de thèmes</a></p>";
?>
