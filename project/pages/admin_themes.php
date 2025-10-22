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

$themeManager = getThemeManager();
$message = '';
$error = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'create':
                $name = sanitizeInput($_POST['name']);
                $primary_color = sanitizeInput($_POST['primary_color']);
                $secondary_color = sanitizeInput($_POST['secondary_color'] ?? '');
                $custom_css = $_POST['custom_css'] ?? '';
                
                if (empty($name) || empty($primary_color)) {
                    throw new Exception("Le nom et la couleur principale sont obligatoires.");
                }
                
                $theme_id = $themeManager->createTheme($name, $primary_color, $secondary_color, $custom_css);
                $message = "Thème créé avec succès !";
                break;
                
            case 'update':
                $id = (int)$_POST['theme_id'];
                $name = sanitizeInput($_POST['name']);
                $primary_color = sanitizeInput($_POST['primary_color']);
                $secondary_color = sanitizeInput($_POST['secondary_color'] ?? '');
                $custom_css = $_POST['custom_css'] ?? '';
                
                $themeManager->updateTheme($id, $name, $primary_color, $secondary_color, $custom_css);
                $message = "Thème mis à jour avec succès !";
                break;
                
            case 'delete':
                $id = (int)$_POST['theme_id'];
                $themeManager->deleteTheme($id);
                $message = "Thème supprimé avec succès !";
                break;
                
            case 'generate_css':
                $id = (int)$_POST['theme_id'];
                $theme = $themeManager->getTheme($id);
                if ($theme) {
                    $css = $themeManager->generateThemeCSS($theme);
                    $themeManager->saveThemeCSS($id, $css);
                    $message = "CSS généré et sauvegardé !";
                }
                break;
                
            case 'create_defaults':
                $themeManager->createDefaultThemes();
                $message = "Thèmes par défaut créés !";
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Récupérer les données
$themes = $themeManager->getAllThemes();
$stats = $themeManager->getThemeStats();
$editing_theme = null;

// Si on édite un thème
if (isset($_GET['edit'])) {
    $editing_theme = $themeManager->getTheme((int)$_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Thèmes - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" type="image/png" href="../favicon.png">
    <style>
        .theme-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        .theme-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .theme-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }
        .theme-preview {
            width: 100%;
            height: 100px;
            border-radius: 8px;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .theme-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .color-picker {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
        }
        .form-group textarea {
            min-height: 120px;
            font-family: 'Courier New', monospace;
        }
        .btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Gestion des Thèmes</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= count($themes) ?></div>
                <div>Thèmes disponibles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= array_sum(array_column($stats, 'user_count')) ?></div>
                <div>Utilisateurs avec thèmes</div>
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="btn-group mb-4">
            <button onclick="showCreateForm()" class="btn btn-success">➕ Créer un thème</button>
            <a href="theme_generator.php" class="btn btn-warning">✨ Générateur automatique</a>
            <form method="post" style="display: inline;">
                <input type="hidden" name="action" value="create_defaults">
                <button type="submit" class="btn btn-info">🎨 Créer thèmes par défaut</button>
            </form>
            <a href="theme.php" class="btn btn-small">🎨 Choisir un thème</a>
            <a href="dashboard.php" class="btn btn-small">← Retour</a>
        </div>
        
        <!-- Formulaire de création/édition -->
        <div id="theme-form" style="display: none;" class="theme-card">
            <h3 id="form-title">Créer un thème</h3>
            <form method="post" id="theme-form-element">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="theme_id" id="form-theme-id">
                
                <div class="form-group">
                    <label for="name">Nom du thème *</label>
                    <input type="text" name="name" id="name" required>
                </div>
                
                <div class="form-group">
                    <label for="primary_color">Couleur principale *</label>
                    <input type="color" name="primary_color" id="primary_color" class="color-picker" required>
                </div>
                
                <div class="form-group">
                    <label for="secondary_color">Couleur secondaire</label>
                    <input type="color" name="secondary_color" id="secondary_color" class="color-picker">
                </div>
                
                <div class="form-group">
                    <label for="custom_css">CSS personnalisé</label>
                    <textarea name="custom_css" id="custom_css" placeholder="/* Votre CSS personnalisé ici */"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                    <button type="button" onclick="hideForm()" class="btn btn-secondary">Annuler</button>
                </div>
            </form>
        </div>
        
        <!-- Liste des thèmes -->
        <div class="theme-grid">
            <?php foreach ($themes as $theme): ?>
                <div class="theme-card">
                    <h3><?= htmlspecialchars($theme['name']) ?></h3>
                    
                    <div class="theme-preview" style="background: linear-gradient(135deg, <?= htmlspecialchars($theme['primary_color']) ?> 0%, <?= htmlspecialchars($theme['secondary_color'] ?? $theme['primary_color']) ?> 100%);">
                        Aperçu
                    </div>
                    
                    <div class="mb-2">
                        <strong>Couleur principale:</strong> 
                        <span style="color: <?= htmlspecialchars($theme['primary_color']) ?>"><?= htmlspecialchars($theme['primary_color']) ?></span>
                    </div>
                    
                    <?php if ($theme['secondary_color']): ?>
                    <div class="mb-2">
                        <strong>Couleur secondaire:</strong> 
                        <span style="color: <?= htmlspecialchars($theme['secondary_color']) ?>"><?= htmlspecialchars($theme['secondary_color']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-2">
                        <strong>Utilisateurs:</strong> 
                        <?php
                        $user_count = 0;
                        foreach ($stats as $stat) {
                            if ($stat['name'] === $theme['name']) {
                                $user_count = $stat['user_count'];
                                break;
                            }
                        }
                        echo $user_count;
                        ?>
                    </div>
                    
                    <div class="theme-actions">
                        <button onclick="editTheme(<?= $theme['id'] ?>, '<?= htmlspecialchars($theme['name']) ?>', '<?= htmlspecialchars($theme['primary_color']) ?>', '<?= htmlspecialchars($theme['secondary_color'] ?? '') ?>', `<?= htmlspecialchars($theme['custom_css'] ?? '') ?>`)" class="btn btn-small">✏️ Modifier</button>
                        
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="action" value="generate_css">
                            <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                            <button type="submit" class="btn btn-small btn-info">🎨 Générer CSS</button>
                        </form>
                        
                        <?php if ($user_count == 0): ?>
                        <form method="post" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce thème ?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="theme_id" value="<?= $theme['id'] ?>">
                            <button type="submit" class="btn btn-small btn-danger">🗑️ Supprimer</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        function showCreateForm() {
            document.getElementById('form-title').textContent = 'Créer un thème';
            document.getElementById('form-action').value = 'create';
            document.getElementById('theme-form-element').reset();
            document.getElementById('theme-form').style.display = 'block';
        }
        
        function editTheme(id, name, primaryColor, secondaryColor, customCss) {
            document.getElementById('form-title').textContent = 'Modifier le thème';
            document.getElementById('form-action').value = 'update';
            document.getElementById('form-theme-id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('primary_color').value = primaryColor;
            document.getElementById('secondary_color').value = secondaryColor;
            document.getElementById('custom_css').value = customCss;
            document.getElementById('theme-form').style.display = 'block';
        }
        
        function hideForm() {
            document.getElementById('theme-form').style.display = 'none';
        }
        
        // Mise à jour de l'aperçu en temps réel
        document.getElementById('primary_color').addEventListener('change', updatePreview);
        document.getElementById('secondary_color').addEventListener('change', updatePreview);
        
        function updatePreview() {
            const primary = document.getElementById('primary_color').value;
            const secondary = document.getElementById('secondary_color').value || primary;
            // Vous pouvez ajouter un aperçu en temps réel ici
        }
    </script>
</body>
</html>
