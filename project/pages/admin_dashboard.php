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

$db = getDB();
$themeManager = getThemeManager();

// Récupérer les statistiques générales
$stats = [
    'users' => $db->fetchOne("SELECT COUNT(*) as count FROM utilisateurs")['count'],
    'projects' => $db->fetchOne("SELECT COUNT(*) as count FROM projets")['count'],
    'themes' => $db->fetchOne("SELECT COUNT(*) as count FROM themes")['count'],
    'portfolios' => $db->fetchOne("SELECT COUNT(*) as count FROM portfolios")['count']
];

// Récupérer les statistiques des thèmes
$themeStats = $themeManager->getThemeStats();

// Récupérer les utilisateurs récents
$recentUsers = $db->fetchAll("SELECT nom, email, created_at FROM utilisateurs ORDER BY created_at DESC LIMIT 5");

// Récupérer les projets récents
$recentProjects = $db->fetchAll("
    SELECT p.titre, u.nom as utilisateur, p.created_at 
    FROM projets p 
    JOIN utilisateurs u ON p.utilisateur_id = u.id 
    ORDER BY p.created_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" type="image/png" href="../favicon.png">
    <style>
        .admin-dashboard {
            max-width: 1200px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        .admin-card {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .admin-card h3 {
            margin-top: 0;
            color: var(--primary-color, #007bff);
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .action-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        .action-card:hover {
            border-color: var(--primary-color, #007bff);
            background: rgba(0,123,255,0.05);
            transform: translateY(-2px);
        }
        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .action-title {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .recent-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .recent-item:last-child {
            border-bottom: none;
        }
        .recent-info {
            flex: 1;
        }
        .recent-name {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        .recent-details {
            font-size: 0.9rem;
            color: #666;
        }
        .recent-date {
            font-size: 0.8rem;
            color: #999;
        }
        .theme-popularity {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0.5rem 0;
        }
        .theme-bar {
            flex: 1;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            margin: 0 1rem;
            overflow: hidden;
        }
        .theme-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container admin-dashboard">
        <h1>⚙️ Tableau de Bord Administrateur</h1>
        <p>Gérez votre application CREON depuis ce tableau de bord centralisé.</p>
        
        <!-- Statistiques générales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['users'] ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['projects'] ?></div>
                <div class="stat-label">Projets</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['themes'] ?></div>
                <div class="stat-label">Thèmes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['portfolios'] ?></div>
                <div class="stat-label">Portfolios</div>
            </div>
        </div>
        
        <!-- Actions rapides -->
        <h2>🚀 Actions Rapides</h2>
        <div class="quick-actions">
            <a href="admin_themes.php" class="action-card">
                <div class="action-icon">🎨</div>
                <div class="action-title">Gérer les Thèmes</div>
                <div>Créer, modifier et supprimer des thèmes</div>
            </a>
            <a href="theme_generator.php" class="action-card">
                <div class="action-icon">✨</div>
                <div class="action-title">Générateur de Thèmes</div>
                <div>Créer des thèmes automatiquement</div>
            </a>
            <a href="admin.php" class="action-card">
                <div class="action-icon">👥</div>
                <div class="action-title">Gérer les Utilisateurs</div>
                <div>Voir et gérer les comptes utilisateurs</div>
            </a>
            <a href="admin_projects.php" class="action-card">
                <div class="action-icon">📁</div>
                <div class="action-title">Gérer les Projets</div>
                <div>Modérer et gérer les projets</div>
            </a>
            <a href="admin_settings.php" class="action-card">
                <div class="action-icon">⚙️</div>
                <div class="action-title">Paramètres</div>
                <div>Configuration de l'application</div>
            </a>
            <a href="admin_logs.php" class="action-card">
                <div class="action-icon">📊</div>
                <div class="action-title">Logs & Rapports</div>
                <div>Consulter les logs et statistiques</div>
            </a>
        </div>
        
        <!-- Informations détaillées -->
        <div class="admin-grid">
            <!-- Utilisateurs récents -->
            <div class="admin-card">
                <h3>👥 Utilisateurs Récents</h3>
                <?php if (empty($recentUsers)): ?>
                    <p>Aucun utilisateur récent.</p>
                <?php else: ?>
                    <?php foreach ($recentUsers as $user): ?>
                        <div class="recent-item">
                            <div class="recent-info">
                                <div class="recent-name"><?= htmlspecialchars($user['nom']) ?></div>
                                <div class="recent-details"><?= htmlspecialchars($user['email']) ?></div>
                            </div>
                            <div class="recent-date">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Projets récents -->
            <div class="admin-card">
                <h3>📁 Projets Récents</h3>
                <?php if (empty($recentProjects)): ?>
                    <p>Aucun projet récent.</p>
                <?php else: ?>
                    <?php foreach ($recentProjects as $project): ?>
                        <div class="recent-item">
                            <div class="recent-info">
                                <div class="recent-name"><?= htmlspecialchars($project['titre']) ?></div>
                                <div class="recent-details">par <?= htmlspecialchars($project['utilisateur']) ?></div>
                            </div>
                            <div class="recent-date">
                                <?= date('d/m/Y', strtotime($project['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Popularité des thèmes -->
            <div class="admin-card">
                <h3>🎨 Popularité des Thèmes</h3>
                <?php if (empty($themeStats)): ?>
                    <p>Aucun thème utilisé.</p>
                <?php else: ?>
                    <?php 
                    $maxUsers = max(array_column($themeStats, 'user_count'));
                    foreach ($themeStats as $theme): 
                        $percentage = $maxUsers > 0 ? ($theme['user_count'] / $maxUsers) * 100 : 0;
                    ?>
                        <div class="theme-popularity">
                            <div style="flex: 0 0 120px;">
                                <strong><?= htmlspecialchars($theme['name']) ?></strong>
                            </div>
                            <div class="theme-bar">
                                <div class="theme-fill" style="width: <?= $percentage ?>%"></div>
                            </div>
                            <div style="flex: 0 0 40px; text-align: right;">
                                <?= $theme['user_count'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Informations système -->
            <div class="admin-card">
                <h3>💻 Informations Système</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <strong>Version PHP:</strong><br>
                        <?= PHP_VERSION ?>
                    </div>
                    <div>
                        <strong>Version App:</strong><br>
                        <?= APP_VERSION ?>
                    </div>
                    <div>
                        <strong>Base de données:</strong><br>
                        SQLite
                    </div>
                    <div>
                        <strong>Mode Debug:</strong><br>
                        <?= APP_DEBUG ? 'Activé' : 'Désactivé' ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liens de navigation -->
        <div style="text-align: center; margin: 3rem 0;">
            <a href="dashboard.php" class="btn btn-small">← Retour au tableau de bord</a>
            <a href="logout.php?token=<?= generateCSRFToken() ?>" class="btn btn-danger btn-small">Se déconnecter</a>
        </div>
    </div>
</body>
</html>
