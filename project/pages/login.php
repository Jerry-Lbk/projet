<?php
require_once '../session_config.php';
require_once '../config.php';
require_once '../database.php';
session_start();

$erreur = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $erreur = "❌ Token de sécurité invalide.";
    } else {
        $email = sanitizeInput($_POST["email"] ?? '');
        $mot_de_passe = $_POST["mot_de_passe"] ?? '';

        if (empty($email) || empty($mot_de_passe)) {
            $erreur = "⚠️ Tous les champs sont obligatoires.";
        } elseif (!validateEmail($email)) {
            $erreur = "❌ Adresse email invalide.";
        } else {
            try {
                $db = getDB();
                $utilisateur = $db->fetchOne("SELECT * FROM utilisateurs WHERE email = ?", [$email]);

                if ($utilisateur && password_verify($mot_de_passe, $utilisateur["mot_de_passe"])) {
                    $_SESSION["utilisateur_id"] = $utilisateur["id"];
                    $_SESSION["nom"] = $utilisateur["nom"];
                    $_SESSION["email"] = $utilisateur["email"];
                    $_SESSION["last_activity"] = time();
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $erreur = "❌ Email ou mot de passe incorrect.";
                }
            } catch (Exception $e) {
                error_log("Erreur lors de la connexion: " . $e->getMessage());
                $erreur = "❌ Erreur lors de la connexion. Veuillez réessayer.";
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
    <title>Connexion - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" type="image/png" href="../favicon.png">
</head>
<body>
    <div class="container fade-in">
        <h2>Connexion</h2>
        
        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="form-group">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label for="email">Adresse email *</label>
                <input type="email" name="email" id="email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="votre@email.com">
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe *</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" required
                       placeholder="Votre mot de passe">
            </div>
            
            <button type="submit" class="btn btn-large">
                Se connecter
            </button>
        </form>
        
        <p class="mt-3">
            Vous n'avez pas de compte ? 
            <a href="register.php" class="btn btn-small">S'inscrire</a>
        </p>
    </div>
</body>
</html>