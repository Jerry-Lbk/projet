<?php
require_once '../session_config.php';
require_once '../config.php';
require_once '../database.php';
session_start();

$erreur = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $erreur = "❌ Token de sécurité invalide.";
    } else {
        $nom = sanitizeInput($_POST['nom'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';
        $photo = null;

        // Validation des données
        if (empty($nom) || empty($email) || empty($mot_de_passe)) {
            $erreur = "⚠️ Tous les champs sont obligatoires.";
        } elseif (!validateEmail($email)) {
            $erreur = "❌ Adresse email invalide.";
        } elseif (!validatePassword($mot_de_passe)) {
            $erreur = "❌ Le mot de passe doit contenir au moins 6 caractères.";
        } else {
            try {
                $db = getDB();
                
                // Vérifier si l'email existe déjà
                $existingUser = $db->fetchOne("SELECT id FROM utilisateurs WHERE email = ?", [$email]);
                if ($existingUser) {
                    $erreur = "❌ Email déjà utilisé.";
                } else {
                    // Traitement de l'upload de photo
                    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                        if (validateUploadedFile($_FILES['photo'])) {
                            $target_dir = UPLOAD_DIR;
                            if (!is_dir($target_dir)) {
                                mkdir($target_dir, 0777, true);
                            }
                            
                            $filename = generateUniqueFilename($_FILES['photo']['name']);
                            $target_file = $target_dir . $filename;
                            
                            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                                $photo = UPLOAD_URL . $filename;
                            }
                        } else {
                            $erreur = "❌ Type de fichier non autorisé ou fichier trop volumineux.";
                        }
                    }
                    
                    if (empty($erreur)) {
                        // Hachage du mot de passe
                        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                        
                        // Insertion de l'utilisateur
                        $db->insert("INSERT INTO utilisateurs (nom, email, mot_de_passe, photo, theme_id) VALUES (?, ?, ?, ?, ?)", 
                                  [$nom, $email, $hash, $photo, DEFAULT_THEME_ID]);
                        
                        header("Location: login.php");
                        exit;
                    }
                }
            } catch (Exception $e) {
                error_log("Erreur lors de l'inscription: " . $e->getMessage());
                $erreur = "❌ Erreur lors de l'inscription. Veuillez réessayer.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" type="image/png" href="../favicon.png">
</head>
<body>
    <div class="container fade-in">
        <h2>Créer un compte</h2>
        
        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="form-group">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label for="nom">Nom complet *</label>
                <input type="text" name="nom" id="nom" required 
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                       placeholder="Votre nom complet">
            </div>
            
            <div class="form-group">
                <label for="email">Adresse email *</label>
                <input type="email" name="email" id="email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="votre@email.com">
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe *</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" required
                       placeholder="Minimum 6 caractères">
            </div>
            
            <div class="form-group">
                <label for="photo">Photo de profil (optionnel)</label>
                <input type="file" name="photo" id="photo" accept="image/*">
                <small>Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)</small>
            </div>
            
            <button type="submit" class="btn btn-success btn-large">
                Créer mon compte
            </button>
        </form>
        
        <p class="mt-3">
            Déjà inscrit ? <a href="login.php" class="btn btn-small">Se connecter</a>
        </p>
    </div>
</body>
</html>