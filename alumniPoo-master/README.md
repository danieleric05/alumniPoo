# Alumni CNDA - Plateforme de Gestion des Profils

Une application web moderne et sécurisée pour gérer les profils des alumni du CNDA. Les alumni peuvent créer des comptes, mettre à jour leurs informations personnelles, enregistrer leur expérience professionnelle et gérer leurs coordonnées de contact.

## 🚀 Caractéristiques

- ✅ Authentification sécurisée (hachage de mots de passe avec bcrypt)
- ✅ Gestion complète des profils alumni
- ✅ Historique d'expérience professionnelle
- ✅ Gestion des informations de contact
- ✅ Validation des données côté serveur
- ✅ Système de logging et gestion d'erreurs
- ✅ Support multi-environnements (dev/prod)
- ✅ Interface web responsive et moderne

## 📋 Pré-requis

- PHP >= 7.4
- MySQL >= 5.7
- Composer
- Apache avec mod_rewrite activé (ou utiliser le serveur PHP natif)

## 🔧 Installation

### 1. Cloner le projet

```bash
git clone <repository-url>
cd alumniPoo-master
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer la base de données

Créez une base de données MySQL nommée `cnda`:

```sql
CREATE DATABASE cnda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Configurer les variables d'environnement

Copiez le fichier `.env.example` en `.env` et adaptez les paramètres si nécessaire:

```bash
cp .env.example .env
```

Modifiez le fichier `.env` avec vos paramètres MySQL:

```env
DB_TYPE=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=cnda
DB_USER=root
DB_PASSWORD=votre_mot_de_passe
APP_ENV=production
APP_DEBUG=false
```

### 5. Charger les données de test

```bash
php src/Formulair/DataFixtures/LoadFixtures.php
```

Cela créera:
- Les tables de la base de données
- Les données de référence (pays, villes, divisions, types de contact)
- 3 utilisateurs de test

**Utilisateurs de test disponibles:**

| Login | Mot de passe | Rôle |
|-------|-------------|------|
| admin | admin123 | Administrateur |
| john_doe | password123 | Utilisateur |
| jane_smith | password123 | Utilisateur |

## 🚀 Lancer l'application

### Avec le serveur PHP natif

```bash
composer run dev-server
```

L'application sera accessible sur: `http://localhost:8000`

### Avec Apache

1. Configurez un VirtualHost pointant vers le dossier `web/`
2. Assurez-vous que `mod_rewrite` est activé
3. Le fichier `.htaccess` gère automatiquement le routage

Exemple de VirtualHost:

```apache
<VirtualHost *:80>
    ServerName alumni.local
    DocumentRoot /chemin/vers/alumniPoo-master/web

    <Directory /chemin/vers/alumniPoo-master/web>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 📁 Structure du projet

```
alumniPoo-master/
├── core/                          # Système core
│   ├── bootstrap.php              # Initialisation de l'application
│   ├── Environment.php            # Gestion des variables d'environnement
│   ├── Database.php               # Gestionnaire de base de données
│   ├── Router.php                 # Système de routage
│   ├── Validator.php              # Validation des données
│   └── Logger.php                 # Logging
├── src/Formulair/
│   ├── Controller/                # Contrôleurs
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   └── AlumniController.php
│   ├── Service/                   # Services métier
│   │   ├── AuthService.php
│   │   └── AlumniService.php
│   ├── Middleware/                # Middleware
│   │   └── AuthMiddleware.php
│   ├── Model/                     # Modèles de données
│   └── DataFixtures/              # Données de test
├── web/
│   ├── index.php                  # Point d'entrée principal
│   ├── .htaccess                  # Configuration Apache
│   └── app.php                    # (hérité, non utilisé)
└── src/views/                     # Vues HTML
    ├── layouts/
    │   └── base.php               # Layout principal
    ├── auth/
    │   ├── login.php
    │   └── register.php
    └── alumni/
        ├── dashboard.php
        ├── edit_profile.php
        ├── work_experience.php
        ├── add_work_experience.php
        ├── contact_info.php
        └── add_contact_info.php
```

## 🔐 Architecture de sécurité

- **Authentification**: Mots de passe hachés avec bcrypt
- **Sessions**: Stockage sécurisé des identifiants utilisateur
- **Validation**: Validation stricte de toutes les entrées utilisateur
- **Protection XSS**: Échappement systématique du contenu HTML
- **Protection CSRF**: Les sessions PHP offrent une protection de base
- **Gestion d'erreurs**: Les erreurs sensibles ne sont jamais exposées aux utilisateurs

## 🌐 Routes disponibles

### Authentification

| Méthode | Route | Description |
|---------|-------|-------------|
| GET/POST | `/login` | Page de connexion |
| GET/POST | `/register` | Inscription d'un nouvel utilisateur |
| GET | `/logout` | Déconnexion |

### Tableau de bord

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/dashboard` | Tableau de bord principal |
| GET/POST | `/profile` | Modifier le profil utilisateur |

### Expérience professionnelle

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/work-experience` | Liste des expériences |
| GET/POST | `/work-experience/add` | Ajouter une expérience |

### Informations de contact

| Méthode | Route | Description |
|---------|-------|-------------|
| GET | `/contact-info` | Liste des contacts |
| GET/POST | `/contact-info/add` | Ajouter une information |

## 🧪 Tests

Pour exécuter les tests unitaires:

```bash
composer test
```

## 📝 Fichiers de configuration

### `.env`

Fichier de configuration de l'application. À dupliquer et adapter pour chaque environnement.

### `composer.json`

Configuration du projet Composer, dépendances et scripts.

### `.htaccess`

Configuration Apache pour le routage d'URL.

## 📊 Modèles de données

### Users
Profils d'alumni avec informations personnelles.

### UserContactInfo
Informations de contact (email, téléphone, LinkedIn, etc.).

### UserWorkExperience
Historique d'expérience professionnelle.

### ContactInfoType
Catalogue des types de contact.

### JobDivision
Hiérarchie des divisions professionnelles.

### Cities
Catalogue des villes avec référence aux pays.

### Country
Catalogue des pays.

### TblEvents
Événements alumni.

## 🐛 Dépannage

### Erreur de connexion à la base de données

Vérifiez:
1. Que MySQL est en cours d'exécution
2. Les paramètres de connexion dans `.env`
3. Que la base de données `cnda` existe

### Pages 404

Assurez-vous que:
1. Apache `mod_rewrite` est activé
2. Le `.htaccess` est présent dans le dossier `web/`
3. Vous utilisez le bon domaine/port

### Erreurs de session

Vérifiez que le dossier `/tmp` est accessible et que `session.save_path` est configuré correctement en PHP.

## 📈 Développement

### Ajouter une nouvelle page

1. Créer un contrôleur dans `src/Formulair/Controller/`
2. Créer une vue dans `src/views/`
3. Ajouter les routes dans `web/index.php`
4. Créer un service si nécessaire

### Exemple:

```php
// Contrôleur
public function myPage(): string {
    return $this->view('my_page', ['data' => $data]);
}

// Route
$router->get('/my-page', fn($params) => $controller->myPage());

// Vue (src/views/my_page.php)
<?php
$title = 'Ma page';
$content = 'Contenu...';
include __DIR__ . '/layouts/base.php';
?>
```

## 📄 Licence

MIT License - Voir le fichier LICENSE pour plus de détails.

## 👥 Support

Pour toute question ou problème, consultez la documentation ou ouvrez une issue sur le dépôt du projet.

## 🔄 Mise à jour

Pour mettre à jour les dépendances:

```bash
composer update
```

Pour réinitialiser la base de données:

```bash
# Supprimer les données (optionnel)
# puis recharger les fixtures
php src/Formulair/DataFixtures/LoadFixtures.php
```
