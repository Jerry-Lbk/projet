<?php
/**
 * Démonstration du système de thèmes CREON
 */

require_once 'config.php';
require_once 'database.php';
require_once 'theme_manager.php';

// Démarrer la session pour la démonstration
session_start();

// Créer un utilisateur de démonstration si nécessaire
$db = getDB();
$themeManager = getThemeManager();

// Vérifier si l'utilisateur de démo existe
$demoUser = $db->fetchOne("SELECT * FROM utilisateurs WHERE email = 'demo@creon.com'");

if (!$demoUser) {
    // Créer un utilisateur de démonstration
    $db->insert(
        "INSERT INTO utilisateurs (nom, email, mot_de_passe, photo, description, theme_id) VALUES (?, ?, ?, ?, ?, ?)",
        ['Utilisateur Démo', 'demo@creon.com', password_hash('demo123', PASSWORD_DEFAULT), 'images/user_demo.jpg', 'Compte de démonstration pour tester les thèmes', 1]
    );
    $demoUser = $db->fetchOne("SELECT * FROM utilisateurs WHERE email = 'demo@creon.com'");
}

// Se connecter en tant qu'utilisateur de démo
$_SESSION['utilisateur_id'] = $demoUser['id'];

// Récupérer les thèmes
$themes = $themeManager->getAllThemes();
$stats = $themeManager->getThemeStats();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Démonstration des Thèmes - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/png" href="favicon.png">
    <style>
        .demo-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .demo-section {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .theme-showcase {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        .theme-demo {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .theme-demo:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }
        .theme-header {
            padding: 1.5rem;
            color: white;
            text-align: center;
            position: relative;
        }
        .theme-content {
            padding: 1.5rem;
            background: #fff;
        }
        .theme-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .theme-colors {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin: 1rem 0;
        }
        .color-dot {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .theme-stats {
            font-size: 0.9rem;
            color: #666;
            text-align: center;
        }
        .demo-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            margin: 2rem 0;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .feature-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .feature-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            border-left: 4px solid var(--primary-color, #007bff);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color, #007bff);
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <h1>🎨 Démonstration du Système de Thèmes CREON</h1>
        <p>Découvrez toutes les fonctionnalités du nouveau système de thèmes !</p>
        
        <!-- Statistiques globales -->
        <div class="demo-section">
            <h2>📊 Statistiques du Système</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= count($themes) ?></div>
                    <div>Thèmes disponibles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= array_sum(array_column($stats, 'user_count')) ?></div>
                    <div>Utilisateurs avec thèmes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">5</div>
                    <div>Styles disponibles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">5</div>
                    <div>Ambiances disponibles</div>
                </div>
            </div>
        </div>
        
        <!-- Fonctionnalités principales -->
        <div class="demo-section">
            <h2>✨ Fonctionnalités Principales</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <div class="feature-title">Gestion des Thèmes</div>
                    <div>Création, modification et suppression des thèmes</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✨</div>
                    <div class="feature-title">Générateur Automatique</div>
                    <div>Création de thèmes en quelques clics</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👁️</div>
                    <div class="feature-title">Aperçu Temps Réel</div>
                    <div>Prévisualisation instantanée des thèmes</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <div class="feature-title">Statistiques</div>
                    <div>Suivi de l'utilisation des thèmes</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <div class="feature-title">Sélection Intuitive</div>
                    <div>Interface moderne et facile à utiliser</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔧</div>
                    <div class="feature-title">CSS Personnalisé</div>
                    <div>Support du CSS personnalisé avancé</div>
                </div>
            </div>
        </div>
        
        <!-- Aperçu des thèmes -->
        <div class="demo-section">
            <h2>🎨 Aperçu des Thèmes Disponibles</h2>
            <div class="theme-showcase">
                <?php foreach ($themes as $theme): ?>
                    <?php
                    $user_count = 0;
                    foreach ($stats as $stat) {
                        if ($stat['name'] === $theme['name']) {
                            $user_count = $stat['user_count'];
                            break;
                        }
                    }
                    ?>
                    <div class="theme-demo">
                        <div class="theme-header" style="background: linear-gradient(135deg, <?= htmlspecialchars($theme['primary_color']) ?> 0%, <?= htmlspecialchars($theme['secondary_color'] ?? $theme['primary_color']) ?> 100%);">
                            <div class="theme-name"><?= htmlspecialchars($theme['name']) ?></div>
                            <div class="theme-colors">
                                <div class="color-dot" style="background: <?= htmlspecialchars($theme['primary_color']) ?>" title="Couleur principale"></div>
                                <?php if ($theme['secondary_color']): ?>
                                    <div class="color-dot" style="background: <?= htmlspecialchars($theme['secondary_color']) ?>" title="Couleur secondaire"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="theme-content">
                            <div class="theme-stats">
                                <strong><?= $user_count ?></strong> utilisateur<?= $user_count > 1 ? 's' : '' ?> utilise<?= $user_count > 1 ? 'nt' : '' ?> ce thème
                            </div>
                            <?php if (!empty($theme['custom_css'])): ?>
                                <div style="margin-top: 1rem; font-size: 0.8rem; color: #28a745;">
                                    ✓ CSS personnalisé
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Actions de démonstration -->
        <div class="demo-section">
            <h2>🚀 Actions de Démonstration</h2>
            <div class="demo-actions">
                <a href="pages/theme.php" class="btn btn-success btn-large">🎨 Choisir un Thème</a>
                <a href="pages/admin_themes.php" class="btn btn-info btn-large">⚙️ Gérer les Thèmes</a>
                <a href="pages/theme_generator.php" class="btn btn-warning btn-large">✨ Générateur Automatique</a>
                <a href="pages/dashboard.php" class="btn btn-small">📊 Tableau de Bord</a>
                <a href="test_themes.php" class="btn btn-small">🧪 Tests du Système</a>
            </div>
        </div>
        
        <!-- Instructions d'utilisation -->
        <div class="demo-section">
            <h2>📖 Comment Utiliser le Système de Thèmes</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <div>
                    <h3>👤 Pour les Utilisateurs</h3>
                    <ol>
                        <li>Allez dans <strong>"Choisir un thème"</strong></li>
                        <li>Prévisualisez les thèmes disponibles</li>
                        <li>Cliquez sur le thème souhaité</li>
                        <li>Confirmez votre sélection</li>
                        <li>Votre portfolio utilise maintenant le nouveau thème !</li>
                    </ol>
                </div>
                <div>
                    <h3>⚙️ Pour les Administrateurs</h3>
                    <ol>
                        <li>Accédez à <strong>"Gérer les thèmes"</strong></li>
                        <li>Créez de nouveaux thèmes manuellement</li>
                        <li>Ou utilisez le <strong>"Générateur automatique"</strong></li>
                        <li>Modifiez les thèmes existants</li>
                        <li>Supprimez les thèmes non utilisés</li>
                    </ol>
                </div>
                <div>
                    <h3>✨ Générateur Automatique</h3>
                    <ol>
                        <li>Choisissez un nom pour votre thème</li>
                        <li>Sélectionnez une couleur principale</li>
                        <li>Choisissez un style (Moderne, Dégradé, etc.)</li>
                        <li>Sélectionnez une ambiance (Professionnel, etc.)</li>
                        <li>Le thème est généré automatiquement !</li>
                    </ol>
                </div>
            </div>
        </div>
        
        <!-- Informations techniques -->
        <div class="demo-section">
            <h2>🔧 Informations Techniques</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <div>
                    <h4>Base de Données</h4>
                    <ul>
                        <li>Table <code>themes</code> étendue</li>
                        <li>Colonnes : name, primary_color, secondary_color, custom_css</li>
                        <li>Timestamps : created_at, updated_at</li>
                        <li>Contraintes : nom unique</li>
                    </ul>
                </div>
                <div>
                    <h4>Architecture</h4>
                    <ul>
                        <li>Classe <code>ThemeManager</code></li>
                        <li>Génération de CSS dynamique</li>
                        <li>Sauvegarde des fichiers CSS</li>
                        <li>Gestion des erreurs</li>
                    </ul>
                </div>
                <div>
                    <h4>Interface</h4>
                    <ul>
                        <li>Design responsive</li>
                        <li>Animations CSS</li>
                        <li>Aperçu temps réel</li>
                        <li>Navigation intuitive</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin: 3rem 0; padding: 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px;">
            <h2>🎉 Système de Thèmes CREON</h2>
            <p style="font-size: 1.2rem; margin: 1rem 0;">Une solution complète et moderne pour la personnalisation des portfolios</p>
            <div style="margin-top: 2rem;">
                <a href="pages/theme.php" class="btn" style="background: white; color: #667eea; margin: 0 0.5rem;">Commencer</a>
                <a href="pages/admin_themes.php" class="btn" style="background: rgba(255,255,255,0.2); color: white; margin: 0 0.5rem;">Administrer</a>
            </div>
        </div>
    </div>
</body>
</html>
