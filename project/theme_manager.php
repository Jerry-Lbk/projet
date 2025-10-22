<?php
/**
 * Gestionnaire de thèmes pour CREON
 * Gère la création, modification et application des thèmes
 */

require_once 'config.php';
require_once 'database.php';

class ThemeManager {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Récupère tous les thèmes disponibles
     */
    public function getAllThemes() {
        return $this->db->fetchAll("SELECT * FROM themes ORDER BY name");
    }
    
    /**
     * Récupère un thème par ID
     */
    public function getTheme($id) {
        return $this->db->fetchOne("SELECT * FROM themes WHERE id = ?", [$id]);
    }
    
    /**
     * Crée un nouveau thème
     */
    public function createTheme($name, $primary_color, $secondary_color = null, $custom_css = null) {
        $id = $this->db->insert(
            "INSERT INTO themes (name, primary_color, secondary_color, custom_css) VALUES (?, ?, ?, ?)",
            [$name, $primary_color, $secondary_color, $custom_css]
        );
        return $id;
    }
    
    /**
     * Met à jour un thème
     */
    public function updateTheme($id, $name, $primary_color, $secondary_color = null, $custom_css = null) {
        return $this->db->update(
            "UPDATE themes SET name = ?, primary_color = ?, secondary_color = ?, custom_css = ? WHERE id = ?",
            [$name, $primary_color, $secondary_color, $custom_css, $id]
        );
    }
    
    /**
     * Supprime un thème
     */
    public function deleteTheme($id) {
        // Vérifier qu'il n'est pas utilisé
        $users = $this->db->fetchAll("SELECT COUNT(*) as count FROM utilisateurs WHERE theme_id = ?", [$id]);
        if ($users[0]['count'] > 0) {
            throw new Exception("Ce thème est utilisé par des utilisateurs et ne peut pas être supprimé.");
        }
        
        return $this->db->delete("DELETE FROM themes WHERE id = ?", [$id]);
    }
    
    /**
     * Applique un thème à un utilisateur
     */
    public function applyThemeToUser($user_id, $theme_id) {
        return $this->db->update(
            "UPDATE utilisateurs SET theme_id = ? WHERE id = ?",
            [$theme_id, $user_id]
        );
    }
    
    /**
     * Génère le CSS d'un thème
     */
    public function generateThemeCSS($theme) {
        $css = ":root {\n";
        $css .= "  --primary-color: {$theme['primary_color']};\n";
        if (!empty($theme['secondary_color'])) {
            $css .= "  --secondary-color: {$theme['secondary_color']};\n";
        }
        $css .= "}\n\n";
        
        // CSS personnalisé
        if (!empty($theme['custom_css'])) {
            $css .= $theme['custom_css'];
        }
        
        return $css;
    }
    
    /**
     * Sauvegarde le CSS d'un thème dans un fichier
     */
    public function saveThemeCSS($theme_id, $css) {
        $filename = "theme_css/theme_{$theme_id}.css";
        $filepath = THEME_CSS_DIR . "theme_{$theme_id}.css";
        
        if (!is_dir(THEME_CSS_DIR)) {
            mkdir(THEME_CSS_DIR, 0755, true);
        }
        
        if (file_put_contents($filepath, $css)) {
            // Mettre à jour la base de données
            $this->db->update(
                "UPDATE themes SET custom_css = ? WHERE id = ?",
                [$filename, $theme_id]
            );
            return true;
        }
        
        return false;
    }
    
    /**
     * Crée des thèmes par défaut
     */
    public function createDefaultThemes() {
        $defaultThemes = [
            [
                'name' => 'Classique',
                'primary_color' => '#007bff',
                'secondary_color' => '#6c757d',
                'custom_css' => null
            ],
            [
                'name' => 'Sombre',
                'primary_color' => '#343a40',
                'secondary_color' => '#6c757d',
                'custom_css' => 'body { background: #1a1a1a; color: #fff; } .container { background: #2d2d2d; }'
            ],
            [
                'name' => 'Nature',
                'primary_color' => '#28a745',
                'secondary_color' => '#20c997',
                'custom_css' => 'body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }'
            ],
            [
                'name' => 'Élégant',
                'primary_color' => '#6f42c1',
                'secondary_color' => '#e83e8c',
                'custom_css' => 'body { background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%); }'
            ],
            [
                'name' => 'Minimaliste',
                'primary_color' => '#495057',
                'secondary_color' => '#adb5bd',
                'custom_css' => 'body { background: #f8f9fa; } .container { box-shadow: none; border: 1px solid #dee2e6; }'
            ]
        ];
        
        foreach ($defaultThemes as $theme) {
            // Vérifier si le thème existe déjà
            $existing = $this->db->fetchOne("SELECT id FROM themes WHERE name = ?", [$theme['name']]);
            if (!$existing) {
                $this->createTheme($theme['name'], $theme['primary_color'], $theme['secondary_color'], $theme['custom_css']);
            }
        }
    }
    
    /**
     * Récupère les statistiques des thèmes
     */
    public function getThemeStats() {
        $stats = $this->db->fetchAll("
            SELECT t.name, t.primary_color, COUNT(u.id) as user_count
            FROM themes t
            LEFT JOIN utilisateurs u ON t.id = u.theme_id
            GROUP BY t.id, t.name, t.primary_color
            ORDER BY user_count DESC
        ");
        
        return $stats;
    }
}

// Fonction helper pour obtenir le gestionnaire de thèmes
function getThemeManager() {
    static $manager = null;
    if ($manager === null) {
        $manager = new ThemeManager();
    }
    return $manager;
}
?>
