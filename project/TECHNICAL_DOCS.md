# 📚 Documentation Technique - CREON

## Architecture du projet

### Structure MVC simplifiée

```
CREON/
├── config.php              # Configuration centralisée
├── database.php            # Helper base de données
├── error_handler.php       # Gestionnaire d'erreurs
├── pages/                  # Contrôleurs/Vues
├── css/                    # Styles
├── images/                 # Assets statiques
├── db/                     # Base de données
└── theme_css/              # Thèmes personnalisés
```

### Flux de données

1. **Requête utilisateur** → `pages/*.php`
2. **Validation** → `config.php` (fonctions de validation)
3. **Base de données** → `database.php` (classe Database)
4. **Rendu** → Templates HTML avec CSS

## Sécurité

### Protection CSRF
- Tous les formulaires incluent un token CSRF
- Vérification côté serveur avec `verifyCSRFToken()`
- Génération de tokens avec `generateCSRFToken()`

### Validation des données
- Nettoyage avec `sanitizeInput()`
- Validation email avec `validateEmail()`
- Validation mot de passe avec `validatePassword()`
- Validation upload avec `validateUploadedFile()`

### Gestion des sessions
- Timeout automatique configurable
- Nettoyage sécurisé à la déconnexion
- Cookies sécurisés (HttpOnly, Secure en HTTPS)

## Base de données

### Tables principales

#### `utilisateurs`
```sql
id INTEGER PRIMARY KEY
nom TEXT NOT NULL
email TEXT NOT NULL UNIQUE
mot_de_passe TEXT NOT NULL
description TEXT
photo TEXT
theme_id INTEGER DEFAULT 1
```

#### `projets`
```sql
id INTEGER PRIMARY KEY
utilisateur_id INTEGER NOT NULL
titre TEXT NOT NULL
description TEXT
lien TEXT
image TEXT
```

#### `themes`
```sql
id INTEGER PRIMARY KEY
name TEXT NOT NULL
primary_color TEXT NOT NULL
custom_css TEXT
```

### Classe Database

```php
$db = getDB();
$user = $db->fetchOne("SELECT * FROM utilisateurs WHERE id = ?", [$id]);
$users = $db->fetchAll("SELECT * FROM utilisateurs");
$id = $db->insert("INSERT INTO utilisateurs (...) VALUES (...)", [...]);
$count = $db->update("UPDATE utilisateurs SET ... WHERE id = ?", [...]);
$count = $db->delete("DELETE FROM utilisateurs WHERE id = ?", [$id]);
```

## Configuration

### Variables d'environnement

```php
// config.php
define('DB_PATH', __DIR__ . '/db/ma_base.db');
define('UPLOAD_DIR', __DIR__ . '/images/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('SESSION_TIMEOUT', 3600);
define('APP_DEBUG', true);
```

### Configuration de production

```php
// config_prod.php
define('APP_DEBUG', false);
error_reporting(0);
ini_set('display_errors', 0);
```

## Upload de fichiers

### Validation
- Vérification du type MIME avec `finfo`
- Limitation de taille (5MB par défaut)
- Types autorisés : JPG, PNG, GIF, WebP
- Noms de fichiers uniques avec `uniqid()`

### Stockage
- Dossier : `images/`
- Nommage : `file_[uniqid].[extension]`
- URL : `images/filename.ext`

## Thèmes

### Structure
- Thèmes stockés en base de données
- CSS personnalisé dans `theme_css/`
- Couleurs principales configurables
- CSS inline possible

### Utilisation
```php
$theme = $db->fetchOne("SELECT * FROM themes WHERE id = ?", [$theme_id]);
if (!empty($theme['custom_css'])) {
    // Afficher le CSS personnalisé
}
```

## Gestion d'erreurs

### Niveaux d'erreur
- **Développement** : Affichage détaillé des erreurs
- **Production** : Page d'erreur générique
- **Logging** : Toutes les erreurs enregistrées

### Fonctions utilitaires
```php
logError($message, $context);
displayError($message, $type);
validateAndSanitize($data, $rules);
```

## API des fonctions

### Configuration
- `generateCSRFToken()` : Génère un token CSRF
- `verifyCSRFToken($token)` : Vérifie un token CSRF
- `sanitizeInput($data)` : Nettoie les données
- `validateEmail($email)` : Valide un email
- `validatePassword($password)` : Valide un mot de passe
- `validateUploadedFile($file)` : Valide un upload
- `generateUniqueFilename($name)` : Génère un nom unique

### Base de données
- `getDB()` : Instance de la base de données
- `Database::getInstance()` : Singleton de la classe
- `$db->fetchOne($sql, $params)` : Un enregistrement
- `$db->fetchAll($sql, $params)` : Tous les enregistrements
- `$db->insert($sql, $params)` : Insertion
- `$db->update($sql, $params)` : Mise à jour
- `$db->delete($sql, $params)` : Suppression

## Déploiement

### Prérequis
- PHP 7.4+
- Extension SQLite
- Serveur web (Apache/Nginx)
- Permissions d'écriture sur `db/` et `images/`

### Étapes
1. Cloner le dépôt
2. Configurer le serveur web
3. Exécuter `deploy.php`
4. Accéder à `pages/init_db.php`
5. Tester avec `test_application.php`

### Configuration Apache (.htaccess)
- Masquage des fichiers sensibles
- Headers de sécurité
- Compression GZIP
- Cache des fichiers statiques

## Maintenance

### Logs
- Erreurs PHP : `/tmp/php_errors.log`
- Erreurs applicatives : `error_log()`
- Logs de déploiement : `deploy.php`

### Sauvegarde
- Base de données : `db/ma_base.db`
- Images : `images/` et `uploads/`
- Configuration : `config.php`

### Monitoring
- Vérifier les logs d'erreurs
- Surveiller l'espace disque
- Tester les fonctionnalités régulièrement

## Développement

### Ajout de fonctionnalités
1. Créer la page dans `pages/`
2. Ajouter les routes dans `.htaccess` si nécessaire
3. Mettre à jour la base de données si besoin
4. Tester avec `test_application.php`

### Debugging
- Activer `APP_DEBUG` dans `config.php`
- Utiliser `error_log()` pour le logging
- Vérifier les logs Apache/PHP

### Tests
- `test_application.php` : Tests de base
- `deploy.php` : Tests de déploiement
- Tests manuels des fonctionnalités

## Sécurité en production

### Checklist
- [ ] `APP_DEBUG = false`
- [ ] `display_errors = Off`
- [ ] Permissions restrictives sur les fichiers
- [ ] HTTPS activé
- [ ] Headers de sécurité configurés
- [ ] Base de données sécurisée
- [ ] Uploads validés
- [ ] Sessions sécurisées

### Monitoring
- Surveiller les tentatives d'intrusion
- Analyser les logs d'erreurs
- Vérifier les uploads suspects
- Contrôler l'utilisation des ressources
