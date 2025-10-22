<?php
require_once '../session_config.php';
require_once '../config.php';
require_once '../database.php';
session_start();

// Vérifier la session
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.php");
    exit();
}

$utilisateur_id = $_SESSION['utilisateur_id'];
$erreur = '';

try {
    $db = getDB();

    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérification du token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $erreur = "❌ Token de sécurité invalide.";
        } else {
            $titre = sanitizeInput($_POST['titre'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $lien = sanitizeInput($_POST['lien'] ?? '');
            $img_path = null;

            // Validation des données
            if (empty($titre) || empty($description)) {
                $erreur = "⚠️ Le titre et la description sont obligatoires.";
            } elseif (!empty($lien) && !filter_var($lien, FILTER_VALIDATE_URL)) {
                $erreur = "❌ L'URL fournie n'est pas valide.";
            } else {
                // Traitement de l'upload d'image
                if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                    if (validateUploadedFile($_FILES['img'])) {
                        $target_dir = UPLOAD_DIR;
                        if (!is_dir($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }
                        
                        $filename = generateUniqueFilename($_FILES['img']['name']);
                        $target_file = $target_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
                            $img_path = UPLOAD_URL . $filename;
                        }
                    } else {
                        $erreur = "❌ Type de fichier non autorisé ou fichier trop volumineux.";
                    }
                }

                if (empty($erreur)) {
                    // Insertion du projet
                    $db->insert("INSERT INTO projets (utilisateur_id, titre, description, lien, image) VALUES (?, ?, ?, ?, ?)",
                              [$utilisateur_id, $titre, $description, $lien, $img_path]);

                    header("Location: dashboard.php");
                    exit();
                }
            }
        }
    }
} catch (Exception $e) {
    error_log("Erreur ajout projet: " . $e->getMessage());
    $erreur = "❌ Erreur lors de l'ajout du projet. Veuillez réessayer.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un projet - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" type="image/png" href="../favicon.png">
</head>
<body>
    <div class="container fade-in">
        <h2>Ajouter un projet</h2>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="form-group">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label for="titre">Titre du projet *</label>
                <input type="text" name="titre" id="titre" required
                       value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>"
                       placeholder="Nom de votre projet">
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea name="description" id="description" required
                          placeholder="Décrivez votre projet en détail"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="lien">Lien du projet (optionnel)</label>
                <input type="url" name="lien" id="lien"
                       value="<?= htmlspecialchars($_POST['lien'] ?? '') ?>"
                       placeholder="https://exemple.com">
            </div>

            <div class="form-group">
                <label for="img">Image du projet (optionnel)</label>
                <input type="file" name="img" id="img" accept="image/*">
                <small>Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)</small>
            </div>

            <button type="submit" class="btn btn-success btn-large">
                Ajouter le projet
            </button>
        </form>

        <p class="mt-3">
            <a href="dashboard.php" class="btn btn-small">Retour au tableau de bord</a>
        </p>
    </div>
</body>
</html>
