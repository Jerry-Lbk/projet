<?php
try {
    $db = new PDO("sqlite:../db/ma_base.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Créer la table projets si elle n'existe pas
    $db->exec("
        CREATE TABLE IF NOT EXISTS projets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            utilisateur_id INTEGER NOT NULL,
            titre TEXT NOT NULL,
            description TEXT,
            lien TEXT,
            
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
        );
    ");

    echo "✅ Table 'projets' prête.<br>";

    // Vérifier s’il y a déjà des projets
    $check = $db->query("SELECT COUNT(*) FROM projets");
    $nb = $check->fetchColumn();

    if ($nb == 0) {
        // Ajouter un projet test
        $stmt = $db->prepare("
            INSERT INTO projets (utilisateur_id, titre, description, lien)
            VALUES (:uid, :titre, :desc, :lien)
        ");
        $stmt->execute([
            ':uid' => 1,
            ':titre' => 'Mon premier site',
            ':desc' => 'Un petit site vitrine fait en HTML/CSS.',
            ':lien' => 'https://monsite.test'
        ]);

        echo "✅ Projet test ajouté pour l’utilisateur ID 1.<br>";
    } else {
        echo "ℹ️ La table contient déjà des projets, rien à ajouter.<br>";
    }

    // Ajoute la colonne description si elle n'existe pas déjà
    try {
        $db->exec("ALTER TABLE utilisateurs ADD COLUMN description TEXT");
        echo "✅ Colonne 'description' ajoutée à la table 'utilisateurs'.<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'duplicate column name') !== false) {
            echo "ℹ️ La colonne 'description' existe déjà.<br>";
        } else {
            throw $e;
        }
    }

    // Créer la table themes si elle n'existe pas
    $db->exec("
        CREATE TABLE IF NOT EXISTS themes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            primary_color TEXT NOT NULL
        );
    ");
    echo "✅ Table 'themes' prête.<br>";

    // Ajouter des thèmes par défaut si la table est vide
    $nbThemes = $db->query("SELECT COUNT(*) FROM themes")->fetchColumn();
    if ($nbThemes == 0) {
        $db->exec("
            INSERT INTO themes (name, primary_color) VALUES
            ('Clair', '#ffffff'),
            ('Sombre', '#222831'),
            ('Bleu', '#007bff')
        ");
        echo "✅ Thèmes par défaut ajoutés.<br>";
    } else {
        echo "ℹ️ La table 'themes' contient déjà des données, rien à ajouter.<br>";
    }

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
