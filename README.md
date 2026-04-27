# EduConnect-RDC

[![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)](https://mysql.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Une plateforme collaborative pour l'éducation en République Démocratique du Congo (RDC). EduConnect-RDC est un réseau social académique et professionnel qui connecte les étudiants, enseignants et professionnels de la RDC pour favoriser l'échange de connaissances, les opportunités d'emploi et la collaboration académique.

## 🎯 Objectifs

- **Connexion Académique** : Relier les étudiants et enseignants de toute la RDC
- **Échange de Connaissances** : Partage d'informations, ressources et expériences
- **Opportunités Professionnelles** : Mise en relation pour stages et emplois
- **Communauté Locale** : Promotion de l'excellence académique congolaise

## ✨ Fonctionnalités Principales

### 👤 Gestion des Utilisateurs
- Inscription et connexion sécurisées
- Profils détaillés (université, domaine d'études, compétences)
- Système de followers/following
- Photos de profil

### 📝 Publications et Interactions
- Création de publications textuelles
- Système de likes et commentaires
- Partage de contenu académique
- Fil d'actualité personnalisé

### 💬 Messagerie Privée
- Messagerie instantanée entre utilisateurs
- Historique des conversations
- Notifications en temps réel

### 🔍 Recherche et Découverte
- Recherche d'utilisateurs par nom, université, compétences
- Recherche de publications par contenu
- Suggestions de connexions

### 🔔 Notifications
- Notifications pour likes, commentaires, messages
- Centre de notifications centralisé

### 👑 Panel d'Administration
- Gestion des utilisateurs
- Modération des publications
- Statistiques de la plateforme

### 🎨 Interface Utilisateur
- Design Windows 11 Dark Mode
- Interface responsive et moderne
- Expérience utilisateur fluide

## 🏗️ Architecture Technique

### Pattern MVC
L'application suit une architecture MVC (Modèle-Vue-Contrôleur) personnalisée :

```
app/
├── controllers/     # Logique métier et traitement des requêtes
├── models/          # Interaction avec la base de données
└── views/           # Templates et présentation
```

### Sécurité
- **Authentification** : Bcrypt pour le hachage des mots de passe
- **Protection CSRF** : Tokens anti-CSRF sur tous les formulaires
- **Validation XSS** : Échappement des données utilisateur
- **Injection SQL** : Requêtes préparées PDO
- **Upload Sécurisé** : Validation des types et tailles de fichiers

## 📋 Prérequis

- **PHP 7.4+** avec extensions PDO et mbstring
- **MySQL 5.7+**
- **Serveur Web** (Apache/Nginx recommandé)
- **XAMPP/WAMP** pour développement local

## 🚀 Installation

### 1. Clonage du Projet
```bash
cd C:\xampp\htdocs
git clone https://github.com/votre-repo/EduConnect-RDC.git
# ou téléchargez et extrayez l'archive
```

### 2. Configuration de la Base de Données
```bash
# Démarrez MySQL via XAMPP Control Panel
# Créez la base de données via phpMyAdmin ou MySQL Workbench
mysql -u root -p
CREATE DATABASE educonnect_rdc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### 3. Import du Schéma
```bash
cd EduConnect-RDC
mysql -u root -p educonnect_rdc < database.sql
```

### 4. Configuration de l'Application
Modifiez `config/config.php` selon vos besoins :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'educonnect_rdc');
define('DB_USER', 'root');
define('DB_PASS', 'votre_mot_de_passe');
```

### 5. Permissions des Dossiers
Assurez-vous que les dossiers suivants sont accessibles en écriture :
```bash
chmod 755 storage/
chmod 755 public/uploads/
```

### 6. Accès à l'Application
- URL : `http://localhost/EduConnect-RDC/public/`
- Compte admin par défaut : ` `admin123'/admin@educonnect.rdc` /
## 📖 Utilisation

### Inscription
1. Accédez à la page d'inscription
2. Remplissez vos informations académiques
3. Ajoutez vos compétences et centres d'intérêt

### Création de Publications
1. Cliquez sur "Nouvelle Publication"
2. Rédigez votre contenu
3. Ajoutez des hashtags pour la visibilité

### Messagerie
1. Recherchez un utilisateur
2. Cliquez sur "Envoyer un message"
3. Commencez la conversation

### Recherche
- Utilisez la barre de recherche pour trouver des utilisateurs ou publications
- Filtrez par université, domaine d'études ou compétences

## 🔌 API Endpoints

### Authentification
```
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
```

### Utilisateurs
```
GET    /api/users/:id
PUT    /api/users/:id
GET    /api/users/:id/followers
POST   /api/users/:id/follow
DELETE /api/users/:id/unfollow
```

### Publications
```
GET    /api/posts
POST   /api/posts
GET    /api/posts/:id
PUT    /api/posts/:id
DELETE /api/posts/:id
POST   /api/posts/:id/like
POST   /api/posts/:id/comment
```

### Messagerie
```
GET    /api/messages
POST   /api/messages
GET    /api/messages/:conversation_id
POST   /api/messages/:conversation_id
```

### Notifications
```
GET    /api/notifications
PUT    /api/notifications/:id/read
```

## 🛡️ Sécurité

### Mesures Implémentées
- **Hachage des mots de passe** : bcrypt avec coût factor 10
- **Protection CSRF** : Tokens uniques par session
- **Validation des entrées** : Filtrage et sanitisation
- **Limitation de débit** : 100 requêtes/heure par IP
- **Sessions sécurisées** : HttpOnly, SameSite=Strict

### Bonnes Pratiques
- Changez les mots de passe par défaut
- Utilisez HTTPS en production
- Mettez à jour régulièrement PHP et MySQL
- Surveillez les logs de sécurité

## 🎨 Personnalisation

### Thème
Le design Windows 11 Dark Mode peut être personnalisé via `public/assets/css/style.css` :
```css
:root {
  --color-bg: #1e1e1e;
  --color-accent: #0078d4;
  --color-text: #ffffff;
}
```

### Fonctionnalités
Ajoutez de nouvelles fonctionnalités en étendant les contrôleurs dans `app/controllers/`.

## 🧪 Tests

### Tests Fonctionnels
- Inscription/connexion utilisateur
- Création et interaction avec les publications
- Envoi de messages privés
- Recherche d'utilisateurs

### Tests de Sécurité
- Tentatives d'injection SQL
- Validation CSRF
- Upload de fichiers malveillants

## 📊 Structure de la Base de Données

### Tables Principales
- `users` : Informations des utilisateurs
- `posts` : Publications et contenu
- `messages` : Messagerie privée
- `notifications` : Centre de notifications
- `followers` : Relations sociales
- `skills` : Compétences des utilisateurs

### Relations
```
users (1) ──── (N) posts
users (1) ──── (N) messages
users (N) ──── (N) followers
users (1) ──── (N) skills
```

## 🤝 Contribution

1. Fork le projet
2. Créez une branche feature (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Pushez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📝 Licence

Distribué sous licence MIT. Voir `LICENSE` pour plus d'informations.

## 👥 Équipe de Développement

- **Développeur Principal** : [Votre Nom]
- **Designer UI/UX** : [Nom du Designer]
- **Testeur QA** : [Nom du Testeur]

## 📞 Support

Pour toute question ou problème :
- Email : support@educonnect-rdc.cd
- Issues GitHub : [Lien vers les issues]

## 🚀 Roadmap

### Version 2.0
- [ ] Application mobile native
- [ ] Intégration de l'IA pour recommandations
- [ ] Système de notation des publications
- [ ] Événements et groupes d'étude

### Version 1.5
- [ ] Chat en temps réel avec WebSockets
- [ ] Système de badges et récompenses
- [ ] Export des données utilisateur
- [ ] Mode hors ligne

---

**EduConnect-RDC** - Connectant l'excellence académique de la RDC 🌟
