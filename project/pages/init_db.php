<?php
require_once __DIR__ . '/../config.php';

// Créer le répertoire de base de données s'il n'existe pas
$dbDir = dirname(DB_PATH);
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

try {
    $db = new PDO(DB_DSN);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Table utilisateurs
    $db->exec("
        CREATE TABLE IF NOT EXISTS utilisateurs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            mot_de_passe TEXT NOT NULL,
            description TEXT,
            photo TEXT,
            theme_id INTEGER DEFAULT 1,
            FOREIGN KEY(theme_id) REFERENCES themes(id)
        );
    ");

    // Table projets avec colonne image
    $db->exec("
        CREATE TABLE IF NOT EXISTS projets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            utilisateur_id INTEGER NOT NULL,
            titre TEXT NOT NULL,
            description TEXT,
            lien TEXT,
            image TEXT,
            
            FOREIGN KEY(utilisateur_id) REFERENCES utilisateurs(id)
        );
    ");

    // Table themes avec colonnes étendues
    $db->exec("
        CREATE TABLE IF NOT EXISTS themes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            primary_color TEXT NOT NULL,
            secondary_color TEXT,
            custom_css TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Ajouter les colonnes manquantes si elles n'existent pas
    try {
        $db->exec("ALTER TABLE themes ADD COLUMN secondary_color TEXT");
    } catch (PDOException $e) {
        // Ignore si la colonne existe déjà
    }
    
    try {
        $db->exec("ALTER TABLE themes ADD COLUMN custom_css TEXT");
    } catch (PDOException $e) {
        // Ignore si la colonne existe déjà
    }
    
    try {
        $db->exec("ALTER TABLE themes ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    } catch (PDOException $e) {
        // Ignore si la colonne existe déjà
    }
    
    try {
        $db->exec("ALTER TABLE themes ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    } catch (PDOException $e) {
        // Ignore si la colonne existe déjà
    }

    // Créer les thèmes par défaut via le ThemeManager
    require_once __DIR__ . '/../theme_manager.php';
    $themeManager = getThemeManager();
    $themeManager->createDefaultThemes();

    // Table portfolios (portfolios générés automatiquement)
$db->exec("
    CREATE TABLE IF NOT EXISTS portfolios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        utilisateur_id INTEGER NOT NULL,
        titre TEXT NOT NULL,
        url TEXT NOT NULL,
        date_creation TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(utilisateur_id) REFERENCES utilisateurs(id)
    );
");


    echo "Base de données initialisée avec succès.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
