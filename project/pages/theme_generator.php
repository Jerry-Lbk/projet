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

// Traitement du formulaire de génération
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = sanitizeInput($_POST['name']);
        $primary_color = sanitizeInput($_POST['primary_color']);
        $style = $_POST['style'] ?? 'modern';
        $mood = $_POST['mood'] ?? 'professional';
        
        if (empty($name) || empty($primary_color)) {
            throw new Exception("Le nom et la couleur principale sont obligatoires.");
        }
        
        // Générer la couleur secondaire basée sur la couleur principale
        $secondary_color = generateSecondaryColor($primary_color, $mood);
        
        // Générer le CSS personnalisé
        $custom_css = generateCustomCSS($primary_color, $secondary_color, $style, $mood);
        
        // Créer le thème
        $theme_id = $themeManager->createTheme($name, $primary_color, $secondary_color, $custom_css);
        
        // Générer et sauvegarder le CSS
        $theme = $themeManager->getTheme($theme_id);
        $css = $themeManager->generateThemeCSS($theme);
        $themeManager->saveThemeCSS($theme_id, $css);
        
        $message = "Thème généré avec succès !";
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

/**
 * Génère une couleur secondaire basée sur la couleur principale et l'humeur
 */
function generateSecondaryColor($primary, $mood) {
    // Convertir hex en RGB
    $hex = ltrim($primary, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Générer la couleur secondaire selon l'humeur
    switch ($mood) {
        case 'warm':
            // Couleur chaude (orange/rouge)
            $r2 = min(255, $r + 50);
            $g2 = max(0, $g - 30);
            $b2 = max(0, $b - 50);
            break;
        case 'cool':
            // Couleur froide (bleu/vert)
            $r2 = max(0, $r - 50);
            $g2 = min(255, $g + 30);
            $b2 = min(255, $b + 50);
            break;
        case 'neutral':
            // Couleur neutre (gris)
            $avg = ($r + $g + $b) / 3;
            $r2 = $avg;
            $g2 = $avg;
            $b2 = $avg;
            break;
        case 'vibrant':
            // Couleur vibrante (complémentaire)
            $r2 = 255 - $r;
            $g2 = 255 - $g;
            $b2 = 255 - $b;
            break;
        default: // professional
            // Couleur professionnelle (variation subtile)
            $r2 = max(0, $r - 20);
            $g2 = max(0, $g - 20);
            $b2 = max(0, $b - 20);
    }
    
    return sprintf("#%02x%02x%02x", $r2, $g2, $b2);
}

/**
 * Génère le CSS personnalisé selon le style et l'humeur
 */
function generateCustomCSS($primary, $secondary, $style, $mood) {
    $css = "";
    
    // Variables CSS
    $css .= ":root {\n";
    $css .= "  --primary-color: {$primary};\n";
    $css .= "  --secondary-color: {$secondary};\n";
    $css .= "}\n\n";
    
    // Styles selon le type
    switch ($style) {
        case 'gradient':
            $css .= "body {\n";
            $css .= "  background: linear-gradient(135deg, {$primary} 0%, {$secondary} 100%);\n";
            $css .= "  min-height: 100vh;\n";
            $css .= "}\n\n";
            $css .= ".container {\n";
            $css .= "  background: rgba(255, 255, 255, 0.95);\n";
            $css .= "  backdrop-filter: blur(10px);\n";
            $css .= "}\n\n";
            break;
            
        case 'minimal':
            $css .= "body {\n";
            $css .= "  background: #f8f9fa;\n";
            $css .= "}\n\n";
            $css .= ".container {\n";
            $css .= "  box-shadow: none;\n";
            $css .= "  border: 1px solid #dee2e6;\n";
            $css .= "}\n\n";
            break;
            
        case 'dark':
            $css .= "body {\n";
            $css .= "  background: #1a1a1a;\n";
            $css .= "  color: #fff;\n";
            $css .= "}\n\n";
            $css .= ".container {\n";
            $css .= "  background: #2d2d2d;\n";
            $css .= "  color: #fff;\n";
            $css .= "}\n\n";
            break;
            
        case 'glass':
            $css .= "body {\n";
            $css .= "  background: linear-gradient(135deg, {$primary}20 0%, {$secondary}20 100%);\n";
            $css .= "}\n\n";
            $css .= ".container {\n";
            $css .= "  background: rgba(255, 255, 255, 0.1);\n";
            $css .= "  backdrop-filter: blur(20px);\n";
            $css .= "  border: 1px solid rgba(255, 255, 255, 0.2);\n";
            $css .= "}\n\n";
            break;
            
        default: // modern
            $css .= "body {\n";
            $css .= "  background: linear-gradient(45deg, {$primary}10 0%, {$secondary}10 100%);\n";
            $css .= "}\n\n";
    }
    
    // Ajouts selon l'humeur
    switch ($mood) {
        case 'warm':
            $css .= ".btn {\n";
            $css .= "  background: linear-gradient(45deg, {$primary}, {$secondary});\n";
            $css .= "  border: none;\n";
            $css .= "}\n\n";
            break;
            
        case 'cool':
            $css .= ".btn {\n";
            $css .= "  background: linear-gradient(135deg, {$primary}, {$secondary});\n";
            $css .= "  border: none;\n";
            $css .= "}\n\n";
            break;
            
        case 'vibrant':
            $css .= ".btn {\n";
            $css .= "  background: {$primary};\n";
            $css .= "  box-shadow: 0 4px 15px {$primary}40;\n";
            $css .= "}\n\n";
            $css .= ".btn:hover {\n";
            $css .= "  transform: translateY(-2px);\n";
            $css .= "  box-shadow: 0 6px 20px {$primary}60;\n";
            $css .= "}\n\n";
            break;
    }
    
    return $css;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de Thèmes - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" type="image/png" href="../favicon.png">
    <style>
        .generator-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .preview-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
        }
        .preview-box {
            width: 200px;
            height: 120px;
            margin: 1rem auto;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
        }
        .color-input {
            width: 60px;
            height: 40px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .style-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .style-option {
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .style-option:hover {
            border-color: var(--primary-color, #007bff);
            background: rgba(0,123,255,0.05);
        }
        .style-option.selected {
            border-color: var(--primary-color, #007bff);
            background: rgba(0,123,255,0.1);
        }
        .mood-options {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 1rem 0;
        }
        .mood-option {
            padding: 0.75rem 1.5rem;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .mood-option:hover {
            border-color: var(--primary-color, #007bff);
            background: rgba(0,123,255,0.05);
        }
        .mood-option.selected {
            border-color: var(--primary-color, #007bff);
            background: var(--primary-color, #007bff);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container generator-container">
        <h1>🎨 Générateur de Thèmes</h1>
        <p>Créez un thème personnalisé en quelques clics !</p>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="post" id="theme-generator-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Nom du thème *</label>
                    <input type="text" name="name" id="name" required placeholder="Mon thème personnalisé">
                </div>
                
                <div class="form-group">
                    <label for="primary_color">Couleur principale *</label>
                    <input type="color" name="primary_color" id="primary_color" class="color-input" value="#007bff" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Style du thème</label>
                <div class="style-options">
                    <div class="style-option selected" data-style="modern">
                        <div>🎨</div>
                        <div>Moderne</div>
                    </div>
                    <div class="style-option" data-style="gradient">
                        <div>🌈</div>
                        <div>Dégradé</div>
                    </div>
                    <div class="style-option" data-style="minimal">
                        <div>⚪</div>
                        <div>Minimal</div>
                    </div>
                    <div class="style-option" data-style="dark">
                        <div>🌙</div>
                        <div>Sombre</div>
                    </div>
                    <div class="style-option" data-style="glass">
                        <div>🔮</div>
                        <div>Verre</div>
                    </div>
                </div>
                <input type="hidden" name="style" id="selected-style" value="modern">
            </div>
            
            <div class="form-group">
                <label>Ambiance</label>
                <div class="mood-options">
                    <div class="mood-option selected" data-mood="professional">Professionnel</div>
                    <div class="mood-option" data-mood="warm">Chaleureux</div>
                    <div class="mood-option" data-mood="cool">Frais</div>
                    <div class="mood-option" data-mood="vibrant">Vibrant</div>
                    <div class="mood-option" data-mood="neutral">Neutre</div>
                </div>
                <input type="hidden" name="mood" id="selected-mood" value="professional">
            </div>
            
            <div class="preview-section">
                <h3>Aperçu du thème</h3>
                <div class="preview-box" id="theme-preview">
                    Aperçu
                </div>
                <p>Le thème sera généré automatiquement selon vos choix</p>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-success btn-large">✨ Générer le thème</button>
                <a href="theme.php" class="btn btn-small">← Retour aux thèmes</a>
            </div>
        </form>
    </div>
    
    <script>
        // Gestion des styles
        document.querySelectorAll('.style-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.style-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('selected-style').value = this.dataset.style;
                updatePreview();
            });
        });
        
        // Gestion des ambiances
        document.querySelectorAll('.mood-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.mood-option').forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('selected-mood').value = this.dataset.mood;
                updatePreview();
            });
        });
        
        // Mise à jour de l'aperçu
        function updatePreview() {
            const primaryColor = document.getElementById('primary_color').value;
            const style = document.getElementById('selected-style').value;
            const mood = document.getElementById('selected-mood').value;
            
            // Générer la couleur secondaire (simplifié)
            let secondaryColor = primaryColor;
            if (mood === 'warm') {
                // Logique simplifiée pour la couleur chaude
                secondaryColor = adjustColor(primaryColor, 30, -20, -30);
            } else if (mood === 'cool') {
                secondaryColor = adjustColor(primaryColor, -30, 20, 30);
            }
            
            const preview = document.getElementById('theme-preview');
            
            switch (style) {
                case 'gradient':
                    preview.style.background = `linear-gradient(135deg, ${primaryColor} 0%, ${secondaryColor} 100%)`;
                    break;
                case 'minimal':
                    preview.style.background = '#f8f9fa';
                    preview.style.color = primaryColor;
                    break;
                case 'dark':
                    preview.style.background = '#1a1a1a';
                    preview.style.color = primaryColor;
                    break;
                case 'glass':
                    preview.style.background = `linear-gradient(135deg, ${primaryColor}20 0%, ${secondaryColor}20 100%)`;
                    preview.style.backdropFilter = 'blur(20px)';
                    break;
                default: // modern
                    preview.style.background = `linear-gradient(45deg, ${primaryColor}10 0%, ${secondaryColor}10 100%)`;
            }
        }
        
        // Fonction pour ajuster une couleur (simplifiée)
        function adjustColor(hex, r, g, b) {
            const num = parseInt(hex.replace('#', ''), 16);
            const newR = Math.max(0, Math.min(255, ((num >> 16) & 255) + r));
            const newG = Math.max(0, Math.min(255, ((num >> 8) & 255) + g));
            const newB = Math.max(0, Math.min(255, (num & 255) + b));
            return `#${newR.toString(16).padStart(2, '0')}${newG.toString(16).padStart(2, '0')}${newB.toString(16).padStart(2, '0')}`;
        }
        
        // Écouter les changements de couleur
        document.getElementById('primary_color').addEventListener('change', updatePreview);
        
        // Initialiser l'aperçu
        updatePreview();
    </script>
</body>
</html>
