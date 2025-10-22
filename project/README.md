[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/7hzke9so)
[![Open in Visual Studio Code](https://classroom.github.com/assets/open-in-vscode-2e0aaae1b6195c2367325f4f02e2d04e9abb55f0b24a779b69b11b9e10269abc.svg)](https://classroom.github.com/online_ide?assignment_repo_id=19828164&assignment_repo_type=AssignmentRepo)
# 🎓 CREON - Plateforme de Portfolios

Bienvenue sur **CREON**, une plateforme web moderne permettant aux utilisateurs de créer et partager leurs portfolios professionnels de manière simple et élégante.

---

## 📌 Objectif du projet

CREON est une application web développée en PHP qui permet aux utilisateurs de :
- Créer un compte et gérer leur profil
- Ajouter des projets avec descriptions, images et liens
- Personnaliser l'apparence de leur portfolio avec des thèmes
- Partager leur portfolio avec un lien unique
- Découvrir les portfolios d'autres utilisateurs

---

## 🛠️ Technologies utilisées

- **Langage principal** : `PHP 7.4+`
- **Base de données** : `SQLite`
- **Frontend** : `HTML5`, `CSS3`, `JavaScript`
- **Sécurité** : Protection CSRF, validation des données, hachage des mots de passe
- **Architecture** : MVC simplifié avec helpers personnalisés
- **Outils** : `Git`, `GitHub`, `PDO`

---

## 🚀 Étapes pour lancer le projet

### Prérequis
- PHP 7.4 ou supérieur
- Serveur web (Apache/Nginx) ou serveur de développement PHP
- Extension SQLite activée

### Installation

1. **Cloner le dépôt** :
```bash
git clone https://github.com/criagi-upc/projet-final-etudiant.git
cd projet-final-etudiant
```

2. **Configurer le serveur web** :
   - Placer le projet dans le répertoire web de votre serveur
   - S'assurer que PHP a les permissions d'écriture sur le dossier `db/` et `images/`

3. **Initialiser la base de données** :
```bash
# Accéder à l'URL d'initialisation
http://votre-domaine/pages/init_db.php
```

4. **Lancer l'application** :
```bash
# Avec le serveur de développement PHP
php -S localhost:8000

# Ou accéder via votre serveur web
http://votre-domaine/pages/index.php
```

### Configuration

- Modifier `config.php` pour ajuster les paramètres de l'application
- Changer `APP_DEBUG` à `false` en production
- Configurer les chemins selon votre environnement

---

## 📁 Structure du projet

```
📦 CREON
  ┣ 📂 pages/                  # Pages PHP de l'application
  ┃  ├ 📄 index.php           # Page d'accueil
  ┃  ├ 📄 login.php           # Connexion
  ┃  ├ 📄 register.php        # Inscription
  ┃  ├ 📄 dashboard.php       # Tableau de bord
  ┃  ├ 📄 portfolio.php       # Affichage des portfolios
  ┃  ├ 📄 ajouter_projet.php  # Ajout de projets
  ┃  └ 📄 init_db.php         # Initialisation BDD
  ┣ 📂 css/                    # Feuilles de style
  ┃  └ 📄 main.css            # Styles consolidés
  ┣ 📂 images/                 # Images uploadées
  ┣ 📂 db/                     # Base de données SQLite
  ┃  └ 📄 ma_base.db          # Fichier de base de données
  ┣ 📂 theme_css/              # Thèmes personnalisés
  ┣ 📄 config.php              # Configuration centralisée
  ┣ 📄 database.php            # Helper base de données
  ┣ 📄 error_handler.php       # Gestionnaire d'erreurs
  ┣ 📄 README.md               # Documentation
  ┗ 📄 .gitignore              # Fichier gitignore
```

---

## ✨ Fonctionnalités

### 🔐 Sécurité
- **Protection CSRF** : Tokens de sécurité sur tous les formulaires
- **Validation des données** : Nettoyage et validation de toutes les entrées
- **Hachage des mots de passe** : Utilisation de `password_hash()`
- **Gestion des sessions** : Timeout automatique et nettoyage sécurisé
- **Upload sécurisé** : Validation des types de fichiers et tailles

### 🎨 Interface utilisateur
- **Design responsive** : Compatible mobile et desktop
- **Thèmes personnalisables** : Système de thèmes modulaire
- **Animations CSS** : Transitions fluides et effets hover
- **Interface moderne** : Design clean et professionnel

### 🛠️ Architecture
- **Configuration centralisée** : Fichier `config.php` pour tous les paramètres
- **Helper base de données** : Classe `Database` pour simplifier les requêtes
- **Gestion d'erreurs** : Système de logging et affichage d'erreurs
- **Code réutilisable** : Fonctions utilitaires partagées

### 📱 Fonctionnalités utilisateur
- **Inscription/Connexion** : Système d'authentification complet
- **Gestion de profil** : Photo, description, informations personnelles
- **Portfolio personnalisé** : Ajout de projets avec images et liens
- **Thèmes visuels** : Personnalisation de l'apparence
- **Partage public** : Liens uniques pour chaque portfolio

---

## 🔧 Améliorations apportées

### Corrections de bugs
- ✅ Suppression du caractère orphelin dans `register.php`
- ✅ Correction des incohérences de chemins de fichiers
- ✅ Amélioration de la gestion des erreurs

### Sécurité renforcée
- ✅ Protection CSRF sur tous les formulaires
- ✅ Validation stricte des uploads de fichiers
- ✅ Nettoyage des données d'entrée
- ✅ Gestion sécurisée des sessions

### Code optimisé
- ✅ Configuration centralisée dans `config.php`
- ✅ Helper base de données réutilisable
- ✅ CSS consolidé et responsive
- ✅ Gestion d'erreurs globale

### Interface améliorée
- ✅ Design moderne et responsive
- ✅ Animations et transitions fluides
- ✅ Messages d'erreur utilisateur-friendly
- ✅ Navigation intuitive

---

## 🎥 Démonstration

Lien vers la démonstration vidéo :
👉 [https://youtu.be/votre-video-demo](https://youtu.be/votre-video-demo)

---

## 🔁 Vous avez déjà commencé votre projet ailleurs ?

Si vous avez déjà un projet sur GitHub (dans votre compte personnel), vous n'avez pas besoin de le recommencer.
Vous pouvez continuer à travailler dessus et simplement pousser le même code vers le dépôt de CRIAGI.

Pas de panique ! Voici comment transférer votre code existant dans ce dépôt :

### ✅ Étapes à suivre (une seule fois)

1. 📥 **Acceptez l'invitation GitHub Classroom**
   Exemple :
   `https://classroom.github.com/a/xxxxxxxx`
   → Un dépôt sera automatiquement créé pour vous dans l'organisation de CRIAGI (ex: `https://github.com/criagi-upc/projet-final-etudiant.git`)

2. 🔗 **Copiez le lien du dépôt Classroom généré**

   * Cliquez sur le bouton **"Code"** dans GitHub
   * Copiez l'URL HTTPS du dépôt créé (ex: `https://github.com/criagi-upc/projet-final-etudiant.git`)

3. 🧠 **Dans votre projet existant (sur votre machine)**
   Ouvrez un terminal et placez-vous dans le dossier :

   ```bash
   cd mon-projet
   ```

4. ➕ **Ajoutez le dépôt de CRIAGI comme destination secondaire (remote)**

   ```bash
   git remote add criagi https://github.com/criagi-upc/projet-final-etudiant.git
   ```

---

### 🚀 Pour soumettre votre projet

À chaque fois que vous souhaitez soumettre votre travail à l'université :

```bash
git push criagi main
```

Et pour continuer à sauvegarder sur votre dépôt personnel habituel :

```bash
git push origin main
```

---

### ⚠️ Une autre étape à suivre (une seule fois) — Cette étape est optionnelle mais récommandée

5. **Créez un remote "both" pour tout pousser d'un coup**

Cette étape permet de **pousser automatiquement votre code vers votre dépôt personnel *et* le dépôt CRIAGI** en une seule commande.

Dans votre terminal, toujours dans le dossier du projet :

```bash
git remote add both https://github.com/votre-utilisateur/mon-projet.git
git remote set-url --add both https://github.com/criagi-upc/projet-final-etudiant.git
```

✅ Vous pouvez maintenant soumettre votre travail aux **deux dépôts en même temps** avec :

```bash
git push both main
```

---

### Résumé des commandes possibles

| Commande               | Effet                                                   |
| ---------------------- | ------------------------------------------------------- |
| `git push origin main` | 🔐 Sauvegarde dans votre dépôt personnel                |
| `git push criagi main` | 🎓 Soumission officielle à CRIAGI                       |
| `git push both main`   | ✅ Soumet dans les **deux dépôts** en une seule commande |

---

### Conditions 

Pour que votre projet soit pris en compte, **merci de suivre scrupuleusement toutes les étapes décrites dans ce README**.

* Assurez-vous d'avoir accepté l'invitation GitHub Classroom avant de commencer.
* Copiez et ajoutez correctement le dépôt CRIAGI comme remote secondaire (`criagi` ou `both`).
* Poussez votre code dans le dépôt CRIAGI **avant la date limite**.
* Vérifiez que vos dernières modifications sont bien visibles sur GitHub.
* Tout dépôt non soumis conformément à ces consignes ne sera pas pris en compte.

En cas de difficulté, contactez votre la COMMISSION **avant la deadline**.

---

## 💡 Comprendre Git et GitHub

Cette vidéo vous explique les bases de Git et GitHub : création de dépôt, commits, push/pull, branches, etc.  
Utile pour bien démarrer avec le versioning collaboratif.

👉 [Regarder la vidéo sur YouTube](https://www.youtube.com/watch?v=V6Zo68uQPqE)

---
## 📄 Licence

Projet académique – Usage Strictement Pédagogique.
© 2025 – Université Protestante au Congo - CRIAGI
