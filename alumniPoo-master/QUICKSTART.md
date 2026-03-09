# Quick Start - Alumni CNDA

Bienvenue! Voici comment démarrer rapidement avec l'application Alumni CNDA.

## ✅ L'application est maintenant fonctionnelle et production-ready!

Voici ce qui a été fait:

### 🏗️ Architecture complète

✅ **Système de configuration**
- Gestion des variables d'environnement (.env)
- Support multi-environnements (dev/prod)
- Configurations sécurisées

✅ **Système de routage**
- Router REST-friendly
- Gestion des paramètres d'URL
- Logging des routes

✅ **Authentification & Sessions**
- Système d'authentification complet
- Hashage sécurisé des mots de passe (bcrypt)
- Gestion des sessions PHP

✅ **Couche métier (Services)**
- AuthService (authentification)
- AlumniService (gestion des profils)
- Séparation des responsabilités

✅ **Contrôleurs**
- BaseController avec méthodes communes
- AuthController (login/register)
- AlumniController (profils, expérience, contacts)

✅ **Validation des données**
- Validator avec 11 règles de validation
- Validation côté serveur
- Gestion des erreurs

✅ **Logging et monitoring**
- Logger complète
- Logs en fichier
- Support du debug mode

✅ **Interface web responsive**
- 8 pages complètes (formulaires, profils, liste d'expérience)
- Design moderne avec gradient
- Responsive design

### 📦 Fichiers créés

**Core système:**
- `core/Environment.php` - Gestion des configs
- `core/Database.php` - Connexion DB
- `core/Router.php` - Routage d'URLs
- `core/Validator.php` - Validation des données
- `core/Logger.php` - Logging

**Contrôleurs:**
- `src/Formulair/Controller/BaseController.php`
- `src/Formulair/Controller/AuthController.php`
- `src/Formulair/Controller/AlumniController.php`

**Services:**
- `src/Formulair/Service/AuthService.php`
- `src/Formulair/Service/AlumniService.php`

**Middleware:**
- `src/Formulair/Middleware/AuthMiddleware.php`

**Vues (8 pages):**
- `src/views/layouts/base.php` - Layout principal
- `src/views/auth/login.php` - Connexion
- `src/views/auth/register.php` - Inscription
- `src/views/alumni/dashboard.php` - Accueil
- `src/views/alumni/edit_profile.php` - Édition profil
- `src/views/alumni/work_experience.php` - Liste expériences
- `src/views/alumni/add_work_experience.php` - Ajouter expérience
- `src/views/alumni/contact_info.php` - Liste contacts
- `src/views/alumni/add_contact_info.php` - Ajouter contact

**Configuration & Data:**
- `web/index.php` - Point d'entrée principal
- `web/.htaccess` - Configuration Apache
- `src/Formulair/DataFixtures/LoadFixtures.php` - Données de test
- `.env` - Configuration
- `.env.example` - Template de configuration

**Documentation:**
- `README.md` - Documentation générale
- `CLAUDE.md` - Guide pour développeurs
- `INSTALL.md` - Guide d'installation
- `test-setup.php` - Script de vérification

## 🚀 Démarrer maintenant

### 1. Préparation (1ère fois seulement)

Créer la base de données MySQL:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE cnda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 2. Charger les données de test

```bash
php src/Formulair/DataFixtures/LoadFixtures.php
```

Vous verrez:
```
✓ Tous les fixtures ont été chargés avec succès
```

### 3. Lancer le serveur

```bash
composer run dev-server
```

Vous verrez:
```
[DATE TIME] Development Server running at http://localhost:8000
```

### 4. Accéder à l'application

Ouvrez votre navigateur et allez à:
```
http://localhost:8000/login
```

### 5. Se connecter

Utilisateurs de test disponibles:

| Login | Mot de passe | Rôle |
|-------|-------------|------|
| admin | admin123 | Administrateur |
| john_doe | password123 | Utilisateur |
| jane_smith | password123 | Utilisateur |

## 📋 Routes disponibles

### Sans authentification
- `GET  /login` - Page de connexion
- `POST /login` - Traiter la connexion
- `GET  /register` - Page d'inscription
- `POST /register` - Créer un compte
- `GET  /logout` - Se déconnecter

### Avec authentification requise
- `GET  /` - Redirection vers dashboard
- `GET  /dashboard` - Tableau de bord
- `GET  /profile` - Voir/Éditer profil
- `POST /profile` - Sauvegarder le profil
- `GET  /work-experience` - Liste des expériences
- `GET  /work-experience/add` - Formulaire d'ajout
- `POST /work-experience/add` - Ajouter une expérience
- `GET  /contact-info` - Liste des contacts
- `GET  /contact-info/add` - Formulaire d'ajout
- `POST /contact-info/add` - Ajouter un contact

## 🛠️ Pour développer

### Ajouter une nouvelle page

1. Créer le contrôleur:
```php
public function myPage(): string {
    $data = [];
    return $this->view('my_page', $data);
}
```

2. Ajouter la route dans `web/index.php`:
```php
$router->get('/my-page', fn($params) => $controller->myPage());
```

3. Créer la vue en `src/views/my_page.php`:
```php
<?php
$title = 'Mon titre';
$content = '<h1>Ma page</h1>';
include __DIR__ . '/layouts/base.php';
?>
```

### Ajouter une validation

Ajouter une règle dans `core/Validator.php`:
```php
'custom' => $this->validateCustom($field, $value),
```

Puis implémenter:
```php
private function validateCustom(string $field, mixed $value): void {
    // Implementation
}
```

### Interroger la base de données

Utiliser RedBean Facade dans les services:
```php
use RedBeanPHP\Facade as R;

// Create
$user = R::dispense('users');
$user->sLogin = 'john';
R::store($user);

// Read
$user = R::load('users', 123);

// Find
$users = R::find('users', 'FirstName LIKE ?', ['John%']);

// Update
$user->FirstName = 'Jane';
R::store($user);

// Delete
R::trash($user);
```

## 📊 Structure des fichiers

```
alumniPoo-master/
├── core/                    # Système core
├── src/
│   └── Formulair/
│       ├── Controller/      # Contrôleurs HTTP
│       ├── Service/         # Logique métier
│       ├── Model/           # Modèles de données
│       ├── Middleware/      # Authentification
│       └── DataFixtures/    # Données de test
├── web/                     # Point d'entrée public
│   ├── index.php           # Routeur
│   └── .htaccess           # Config Apache
├── src/views/              # Vues HTML
├── logs/                    # Logs d'application
├── composer.json           # Dépendances PHP
├── .env                    # Configuration (à adapter)
└── Documentation/
    ├── README.md           # Vue d'ensemble
    ├── INSTALL.md          # Installation complète
    ├── CLAUDE.md           # Pour développeurs
    └── QUICKSTART.md       # Ce fichier
```

## 🔒 Sécurité

✅ Mots de passe hachés avec bcrypt
✅ Escaping HTML pour prévenir XSS
✅ Validation stricte des entrées
✅ Sessions PHP sécurisées
✅ Paramètres de requête via RedBean

## 🐛 Troubleshooting

### Erreur: "Cannot connect to database"
```bash
# Vérifier que MySQL est actif
mysql -u root -p

# Vérifier les credentials dans .env
```

### Erreur 404 sur les routes
```bash
# Vérifier Apache mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Logs vides?
Vérifier que le dossier `logs/` est accessible:
```bash
chmod 755 logs
```

## 📚 Documentation complète

Pour plus de détails, consultez:
- **README.md** - Présentation générale et mise à jour
- **INSTALL.md** - Installation en production
- **CLAUDE.md** - Architecture et développement
- **Logs** - Consultez `logs/app.log`

## 🎯 Prochaines étapes

1. **Personnaliser l'interface** - Modifier les couleurs dans `src/views/layouts/base.php`
2. **Ajouter des champs** - Ajouter des propriétés aux modèles
3. **Ajouter des validations** - Renforcer la validation des données
4. **Déployer en production** - Suivre INSTALL.md
5. **Ajouter des tests** - Utiliser PHPUnit

## 💡 Tips

- Utilisez `APP_DEBUG=true` dans `.env` pour plus de détails
- Vérifiez `logs/app.log` pour les erreurs
- Utilisez `composer run dev-server` pour développer
- RedBean crée automatiquement les tables

## 📞 Besoin d'aide?

1. Consultez les logs: `logs/app.log`
2. Lancez le test de setup: `php test-setup.php`
3. Lisez la documentation: README.md, INSTALL.md, CLAUDE.md

---

**Bienvenue dans Alumni CNDA!** 🎓
