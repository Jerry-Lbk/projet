<?php
require_once '../session_config.php';
require_once '../config.php';
require_once '../database.php';
require_once '../theme_manager.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['utilisateur_id'];
$themeManager = getThemeManager();
$message = '';
$error = '';

// Récupérer les infos de l'utilisateur
$db = getDB();
$utilisateur = $db->fetchOne("SELECT * FROM utilisateurs WHERE id = ?", [$user_id]);

// Récupérer tous les thèmes
$themes = $themeManager->getAllThemes();

// Créer les thèmes par défaut s'ils n'existent pas
if (empty($themes)) {
    $themeManager->createDefaultThemes();
    $themes = $themeManager->getAllThemes();
}

// Changer le thème si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme_id'])) {
    try {
        $theme_id = (int)$_POST['theme_id'];
        $themeManager->applyThemeToUser($user_id, $theme_id);
        $message = "Thème modifié avec succès !";
        
        // Mettre à jour les données de l'utilisateur
        $utilisateur = $db->fetchOne("SELECT * FROM utilisateurs WHERE id = ?", [$user_id]);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Récupérer le thème actuel
$current_theme_id = $utilisateur['theme_id'] ?? DEFAULT_THEME_ID;
$current_theme = $themeManager->getTheme($current_theme_id);

// Récupérer les statistiques des thèmes
$stats = $themeManager->getThemeStats();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choisir un thème - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" type="image/png" href="../favicon.png">
    
    <?php if ($current_theme && !empty($current_theme['custom_css'])): ?>
        <?php if (strpos($current_theme['custom_css'], 'theme_css/') === 0): ?>
            <link rel="stylesheet" href="../<?= htmlspecialchars($current_theme['custom_css']) ?>">
        <?php else: ?>
            <style><?= $current_theme['custom_css'] ?></style>
        <?php endif; ?>
    <?php endif; ?>
    
    <style>
        .theme-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        .theme-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 3px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .theme-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
        }
        .theme-card.selected {
            border-color: var(--primary-color, #007bff);
            background: linear-gradient(135deg, rgba(0,123,255,0.1) 0%, rgba(0,123,255,0.05) 100%);
        }
        .theme-preview {
            width: 100%;
            height: 120px;
            border-radius: 12px;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }
        .theme-preview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        }
        .theme-info {
            margin: 1rem 0;
        }
        .theme-name {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--text-color, #333);
        }
        .theme-colors {
            display: flex;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }
        .color-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .theme-stats {
            font-size: 0.9rem;
            color: var(--text-muted, #666);
            margin-top: 0.5rem;
        }
        .selected-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--primary-color, #007bff);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .preview-text {
            position: relative;
            z-index: 1;
        }
        .theme-actions {
            margin-top: 2rem;
            text-align: center;
        }
        .current-theme-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .current-theme-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Choisissez votre thème</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <!-- Thème actuel -->
        <?php if ($current_theme): ?>
        <div class="current-theme-info">
            <div class="current-theme-name">Thème actuel : <?= htmlspecialchars($current_theme['name']) ?></div>
            <div>Votre portfolio utilise actuellement ce thème</div>
        </div>
        <?php endif; ?>
        
        <form method="post" id="theme-form">
            <input type="hidden" name="theme_id" id="selected-theme-id" value="<?= $current_theme_id ?>">
            
            <div class="theme-grid">
                <?php foreach ($themes as $theme): ?>
                    <?php
                    $is_selected = $theme['id'] == $current_theme_id;
                    $user_count = 0;
                    foreach ($stats as $stat) {
                        if ($stat['name'] === $theme['name']) {
                            $user_count = $stat['user_count'];
                            break;
                        }
                    }
                    ?>
                    <div class="theme-card <?= $is_selected ? 'selected' : '' ?>" 
                         onclick="selectTheme(<?= $theme['id'] ?>)"
                         data-theme-id="<?= $theme['id'] ?>">
                        
                        <?php if ($is_selected): ?>
                            <div class="selected-badge">✓ Sélectionné</div>
                        <?php endif; ?>
                        
                        <div class="theme-preview" 
                             style="background: linear-gradient(135deg, <?= htmlspecialchars($theme['primary_color']) ?> 0%, <?= htmlspecialchars($theme['secondary_color'] ?? $theme['primary_color']) ?> 100%);">
                            <div class="preview-text">Aperçu</div>
                        </div>
                        
                        <div class="theme-info">
                            <div class="theme-name"><?= htmlspecialchars($theme['name']) ?></div>
                            
                            <div class="theme-colors">
                                <div class="color-dot" style="background: <?= htmlspecialchars($theme['primary_color']) ?>" title="Couleur principale"></div>
                                <?php if ($theme['secondary_color']): ?>
                                    <div class="color-dot" style="background: <?= htmlspecialchars($theme['secondary_color']) ?>" title="Couleur secondaire"></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="theme-stats">
                                <?= $user_count ?> utilisateur<?= $user_count > 1 ? 's' : '' ?> utilise<?= $user_count > 1 ? 'nt' : '' ?> ce thème
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="theme-actions">
                <button type="submit" class="btn btn-success btn-large">💾 Enregistrer le thème</button>
                <a href="dashboard.php" class="btn btn-small">← Retour au tableau de bord</a>
            </div>
        </form>
    </div>
    
    <script>
        function selectTheme(themeId) {
            // Désélectionner toutes les cartes
            document.querySelectorAll('.theme-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Sélectionner la carte cliquée
            document.querySelector(`[data-theme-id="${themeId}"]`).classList.add('selected');
            
            // Mettre à jour le champ caché
            document.getElementById('selected-theme-id').value = themeId;
        }
        
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.theme-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>