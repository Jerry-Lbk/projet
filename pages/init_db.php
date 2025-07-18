<?php
try {
    $db = new PDO("sqlite:../db/ma_base.db");
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

    // Table themes avec custom_css
    $db->exec("
        CREATE TABLE IF NOT EXISTS themes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            primary_color TEXT NOT NULL
        );
    ");

    // Quelques thèmes par défaut
    $db->exec("
        INSERT OR IGNORE INTO themes (id, name, primary_color) VALUES
        (1, 'Clair', '#007bff'),
        (2, 'Sombre', '#222831'),
        (3, 'Bleu', '#00adb5')
    ");

    // Ajout de la colonne custom_css à la table themes
    $db->exec("ALTER TABLE themes ADD COLUMN custom_css TEXT");

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
